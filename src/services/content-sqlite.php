<?php

declare(strict_types=1);

const COCKPIT_CONTENT_SQLITE_PATH = BASE_PATH . '/api/storage/data/content.sqlite';

/** @var array<string, true> */
const COCKPIT_SQLITE_ALLOWED_COLLECTION_TABLES = [
	'collections_articles' => true,
	'collections_seo' => true,
	'collections_product' => true,
	'collections_requests' => true,
];

/**
 * Single entry point for SQLite reads: validation, PDO lifecycle, logging, and error handling.
 *
 * @param array<string, scalar> $bindParams Bound parameters only (no table names in params).
 * @return array{success: bool, rows?: array<int, array<string, string>>, error?: string}
 */
function queryCockpitContentSqlite(string $operation, string $languageContext, string $sql, array $bindParams = []): array
{
	$operation = trim($operation);
	if ($operation === '') {
		logContentCockpitSqlite('invalid_operation', 'en', 'empty_operation', ['sql_preview' => substr($sql, 0, 80)]);
		return ['success' => false, 'error' => 'empty_operation'];
	}

	$langLog = trim($languageContext);
	if ($langLog === '' || strlen($langLog) > 16 || !preg_match('/^[a-z]{2}$/', $langLog)) {
		logContentCockpitSqlite($operation, 'en', 'invalid_language_context', ['given' => $languageContext]);
		$langLog = 'en';
	}

	$sqlTrim = trim($sql);
	if ($sqlTrim === '' || strlen($sqlTrim) > 4096) {
		logContentCockpitSqlite($operation, $langLog, 'invalid_sql', ['length' => strlen($sql)]);
		return ['success' => false, 'error' => 'invalid_sql'];
	}

	foreach ($bindParams as $paramName => $value) {
		if (!is_string($paramName) || $paramName === '' || !preg_match('/^:[a-zA-Z_][a-zA-Z0-9_]*$/', $paramName)) {
			logContentCockpitSqlite($operation, $langLog, 'invalid_bind_param_name', ['param' => $paramName]);
			return ['success' => false, 'error' => 'invalid_bind_param_name'];
		}
		if (!is_scalar($value)) {
			logContentCockpitSqlite($operation, $langLog, 'invalid_bind_param_value', ['param' => $paramName]);
			return ['success' => false, 'error' => 'invalid_bind_param_value'];
		}
	}

	$sqlitePath = COCKPIT_CONTENT_SQLITE_PATH;
	if (!is_file($sqlitePath)) {
		logContentCockpitSqlite($operation, $langLog, 'sqlite_missing', ['path' => $sqlitePath]);
		return ['success' => false, 'error' => 'sqlite_missing'];
	}

	if (!class_exists('PDO') || !in_array('sqlite', PDO::getAvailableDrivers(), true)) {
		logContentCockpitSqlite($operation, $langLog, 'sqlite_driver_unavailable', null);
		return ['success' => false, 'error' => 'sqlite_driver_unavailable'];
	}

	try {
		$pdo = new PDO('sqlite:' . $sqlitePath);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$statement = $pdo->prepare($sqlTrim);
		if ($statement === false) {
			logContentCockpitSqlite($operation, $langLog, 'prepare_failed', null);
			return ['success' => false, 'error' => 'prepare_failed'];
		}

		foreach ($bindParams as $name => $scalar) {
			$paramType = is_int($scalar) ? PDO::PARAM_INT : PDO::PARAM_STR;
			$statement->bindValue($name, $scalar, $paramType);
		}

		$statement->execute();
		$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
		if (!is_array($rows)) {
			logContentCockpitSqlite($operation, $langLog, 'fetch_invalid', null);
			return ['success' => false, 'error' => 'fetch_invalid'];
		}

		/** @var array<int, array<string, string>> $normalizedRows */
		$normalizedRows = [];
		foreach ($rows as $idx => $row) {
			if (!is_array($row)) {
				continue;
			}
			$stringRow = [];
			foreach ($row as $col => $cell) {
				if (!is_string($col)) {
					continue;
				}
				$stringRow[$col] = is_string($cell) ? $cell : (string) $cell;
			}
			$normalizedRows[] = $stringRow;
		}

		logContentCockpitSqlite($operation, $langLog, 'sqlite_ok', ['row_count' => count($normalizedRows)]);
		return ['success' => true, 'rows' => $normalizedRows];
	} catch (Throwable $exception) {
		logContentCockpitSqlite($operation, $langLog, 'sqlite_error: ' . $exception->getMessage(), null);
		return ['success' => false, 'error' => $exception->getMessage()];
	}
}

function logContentCockpitSqlite(string $operation, string $language, string $status, ?array $detail): void
{
	$logPath = STORAGE_PATH . '/logs/php-info.log';
	$logEntry = [
		'timestamp' => date('c'),
		'source' => 'cockpit_sqlite',
		'operation' => $operation,
		'language' => $language,
		'status' => $status,
		'detail' => $detail,
	];
	$line = json_encode($logEntry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

	if (!is_string($line)) {
		return;
	}

	@file_put_contents($logPath, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function cockpitLocaleFieldSuffix(string $language): string
{
	return match ($language) {
		'ru' => 'ru',
		'kz' => 'kk',
		default => 'en',
	};
}

function resolveCockpitContentLanguage(string $language): string
{
	$resolved = resolveContentApiLanguage($language);
	if ($resolved !== '' && in_array($resolved, ['ru', 'en', 'kz'], true)) {
		return $resolved;
	}

	return 'en';
}

/**
 * Merges Cockpit per-locale fields (suffixes _en, _ru, _kk) into base keys to mirror Content API locale output.
 *
 * @param array<mixed> $document
 * @return array<mixed>
 */
function mergeCockpitLocaleFields(array $document, string $language): array
{
	$langSuffix = cockpitLocaleFieldSuffix(resolveCockpitContentLanguage($language));
	$localeSuffixes = ['en', 'ru', 'kk'];

	if ($document === []) {
		return [];
	}

	if (array_is_list($document)) {
		$out = [];
		foreach ($document as $item) {
			$out[] = is_array($item) ? mergeCockpitLocaleFields($item, $language) : $item;
		}
		return $out;
	}

	$result = [];
	foreach ($document as $key => $value) {
		if (!is_string($key)) {
			$result[$key] = is_array($value) ? mergeCockpitLocaleFields($value, $language) : $value;
			continue;
		}

		if (str_starts_with($key, '_')) {
			$result[$key] = $value;
			continue;
		}

		$isLocaleKey = false;
		foreach ($localeSuffixes as $suf) {
			if (str_ends_with($key, '_' . $suf)) {
				$isLocaleKey = true;
				break;
			}
		}
		if ($isLocaleKey) {
			continue;
		}

		$result[$key] = is_array($value) ? mergeCockpitLocaleFields($value, $language) : $value;
	}

	$preferredSuffix = '_' . $langSuffix;
	foreach ($document as $key => $value) {
		if (!is_string($key) || !str_ends_with($key, $preferredSuffix)) {
			continue;
		}

		$baseKey = substr($key, 0, -strlen($preferredSuffix));
		if ($baseKey === '' || str_starts_with($baseKey, '_')) {
			continue;
		}

		if ($value === null) {
			continue;
		}

		if (is_string($value) && trim($value) === '') {
			continue;
		}

		if (is_array($value) && $value === []) {
			continue;
		}

		if (is_array($value)) {
			$result[$baseKey] = mergeCockpitLocaleFields($value, $language);
		} else {
			$result[$baseKey] = $value;
		}
	}

	if ($langSuffix !== 'en') {
		$fallbackSuffix = '_en';
		foreach ($document as $key => $value) {
			if (!is_string($key) || !str_ends_with($key, $fallbackSuffix)) {
				continue;
			}

			$baseKey = substr($key, 0, -strlen($fallbackSuffix));
			if ($baseKey === '' || str_starts_with($baseKey, '_')) {
				continue;
			}

			$missingOrEmpty = !array_key_exists($baseKey, $result);
			if (!$missingOrEmpty) {
				$current = $result[$baseKey];
				$isEmptyString = is_string($current) && trim($current) === '';
				$isEmptyArray = is_array($current) && $current === [];
				if (!($isEmptyString || $isEmptyArray || $current === null)) {
					continue;
				}
			}

			if ($value === null) {
				continue;
			}

			if (is_string($value) && trim($value) === '') {
				continue;
			}

			if (is_array($value) && $value === []) {
				continue;
			}

			if (is_array($value)) {
				$result[$baseKey] = mergeCockpitLocaleFields($value, $language);
			} else {
				$result[$baseKey] = $value;
			}
		}
	}

	return $result;
}

/**
 * @return ?array<mixed>
 */
function fetchSingletonDocumentByModel(string $model, string $language): ?array
{
	$model = trim($model);
	if ($model === '' || strlen($model) > 64 || !preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $model)) {
		logContentCockpitSqlite('fetch_singleton', resolveCockpitContentLanguage($language), 'invalid_model', ['model' => $model]);
		return null;
	}

	$lang = resolveCockpitContentLanguage($language);
	$sql = 'SELECT document FROM singletons WHERE json_extract(document, \'$._model\') = :model LIMIT 1';
	$query = queryCockpitContentSqlite('fetch_singleton:' . $model, $lang, $sql, [':model' => $model]);

	if (!$query['success'] || !isset($query['rows'][0]['document'])) {
		return null;
	}

	$document = $query['rows'][0]['document'];
	if (trim($document) === '') {
		logContentCockpitSqlite('fetch_singleton:' . $model, $lang, 'empty_document', null);
		return null;
	}

	$decoded = json_decode($document, true);
	if (!is_array($decoded)) {
		logContentCockpitSqlite('fetch_singleton:' . $model, $lang, 'invalid_json', null);
		return null;
	}

	return mergeCockpitLocaleFields($decoded, $lang);
}

/**
 * @return ?array<int, array<mixed>> Null when the SQLite query fails; empty array when there are no rows.
 */
function fetchCollectionDocuments(string $collectionTable, string $language): ?array
{
	if (!isset(COCKPIT_SQLITE_ALLOWED_COLLECTION_TABLES[$collectionTable])) {
		logContentCockpitSqlite('fetch_collection', resolveCockpitContentLanguage($language), 'collection_not_allowed', ['table' => $collectionTable]);
		return [];
	}

	$lang = resolveCockpitContentLanguage($language);
	$sql = 'SELECT document FROM `' . str_replace('`', '``', $collectionTable) . '` ORDER BY id ASC';
	$query = queryCockpitContentSqlite('fetch_collection:' . $collectionTable, $lang, $sql, []);

	if (!$query['success'] || !isset($query['rows'])) {
		return null;
	}

	$items = [];
	foreach ($query['rows'] as $row) {
		if (!isset($row['document']) || trim($row['document']) === '') {
			continue;
		}
		$decoded = json_decode($row['document'], true);
		if (!is_array($decoded)) {
			continue;
		}
		$items[] = mergeCockpitLocaleFields($decoded, $lang);
	}

	return $items;
}

function fetchDictionaryContent(string $language): ?array
{
	$lang = resolveCockpitContentLanguage($language);
	$sql = 'SELECT document FROM singletons WHERE json_extract(document, \'$._model\') = :model LIMIT 1';
	$query = queryCockpitContentSqlite('fetch_dictionary', $lang, $sql, [':model' => 'dictionary']);

	if (!$query['success'] || !isset($query['rows'][0]['document'])) {
		return null;
	}

	$document = $query['rows'][0]['document'];
	if (trim($document) === '') {
		logContentCockpitSqlite('fetch_dictionary', $lang, 'dictionary_document_not_found', null);
		return null;
	}

	$decoded = json_decode($document, true);
	if (!is_array($decoded)) {
		logContentCockpitSqlite('fetch_dictionary', $lang, 'dictionary_document_invalid_json', null);
		return null;
	}

	return extractLocalizedDictionary($decoded, $lang);
}

/**
 * @return array<mixed>
 */
function extractLocalizedDictionary(array $document, string $language): array
{
	$languageMap = [
		'en' => 'en',
		'ru' => 'ru',
		'kz' => 'kk',
	];
	$suffix = $languageMap[$language] ?? 'en';
	$suffixPattern = '_' . $suffix;
	$localized = [];

	foreach ($document as $key => $value) {
		if (!is_string($key)) {
			continue;
		}

		if (str_starts_with($key, '_')) {
			continue;
		}

		if (is_string($value) && !str_ends_with($key, '_en') && !str_ends_with($key, '_ru') && !str_ends_with($key, '_kk')) {
			$localized[$key] = $value;
		}

		if (!str_ends_with($key, $suffixPattern)) {
			continue;
		}

		$baseKey = substr($key, 0, -strlen($suffixPattern));
		if ($baseKey === false || $baseKey === '' || str_starts_with($baseKey, '_')) {
			continue;
		}

		if (is_string($value) && trim($value) !== '') {
			$localized[$baseKey] = $value;
		}
	}

	return $localized;
}

function fetchFeedbackFormContent(string $language): ?array
{
	return fetchSingletonDocumentByModel('feedbackForm', $language);
}

function fetchFooterContent(string $language): ?array
{
	return fetchSingletonDocumentByModel('footer', $language);
}

function fetchPageContentBySlug(string $slug, string $language): ?array
{
	$pageSingletonMap = [
		'home' => 'pageHome',
		'oil-cleaning' => 'pageOil',
		'wastewater-treatment' => 'pageWastewaterTreatment',
		'plant-protection' => 'pagePlantProtection',
	];

	if (!isset($pageSingletonMap[$slug])) {
		return null;
	}

	return fetchSingletonDocumentByModel($pageSingletonMap[$slug], $language);
}

/**
 * @return ?array<mixed>
 */
function fetchArticlesRaw(string $language): ?array
{
	return fetchCollectionDocuments('collections_articles', $language);
}

function fetchArticlesCollection(string $language): array
{
	$articlesRaw = fetchArticlesRaw($language);
	return normalizeItemsCollection($articlesRaw);
}

function fetchOurCustomersCollection(string $_language): array
{
	// API parity; customers on home are stored on pageHome singleton until a dedicated collection exists in SQLite.
	return [];
}

/**
 * @return ?array<mixed>
 */
function fetchArticleBySlug(string $slug, string $language): ?array
{
	$articles = fetchArticlesCollection($language);
	return findArticleBySlug($articles, $slug);
}

function fetchSeoContentBySlug(string $slug, string $language): ?array
{
	$lang = trim($language) !== '' ? resolveCockpitContentLanguage($language) : 'en';
	$seoItems = fetchCollectionDocuments('collections_seo', $lang) ?? [];
	$normalized = normalizeItemsCollection($seoItems);
	$seoItem = findSeoItemBySlug($normalized, $slug);

	if (is_array($seoItem)) {
		return $seoItem;
	}

	if ($lang !== 'en') {
		$fallbackItems = fetchCollectionDocuments('collections_seo', 'en') ?? [];
		$normalizedFallback = normalizeItemsCollection($fallbackItems);
		$fallbackItem = findSeoItemBySlug($normalizedFallback, $slug);
		if (is_array($fallbackItem)) {
			return $fallbackItem;
		}
	}

	return null;
}
