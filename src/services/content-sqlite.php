<?php

declare(strict_types=1);

const COCKPIT_CONTENT_SQLITE_PATH = BASE_PATH . '/api/storage/data/content.sqlite';

function fetchDictionaryContent(string $language): ?array
{
	$resolvedLanguage = resolveContentApiLanguage($language);
	$selectedLanguage = $resolvedLanguage !== '' ? $resolvedLanguage : 'en';
	$sqlitePath = COCKPIT_CONTENT_SQLITE_PATH;

	if (!is_file($sqlitePath)) {
		logDictionaryFetchInfo($selectedLanguage, 'sqlite_missing', null);
		return null;
	}

	if (!class_exists('PDO') || !in_array('sqlite', PDO::getAvailableDrivers(), true)) {
		logDictionaryFetchInfo($selectedLanguage, 'sqlite_driver_unavailable', null);
		return null;
	}

	try {
		$pdo = new PDO('sqlite:' . $sqlitePath);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$query = 'SELECT document FROM singletons WHERE json_extract(document, \'$._model\') = :model LIMIT 1';
		$statement = $pdo->prepare($query);
		$statement->execute(['model' => 'dictionary']);
		$document = $statement->fetchColumn();

		if (!is_string($document) || trim($document) === '') {
			logDictionaryFetchInfo($selectedLanguage, 'dictionary_document_not_found', null);
			return null;
		}

		$decoded = json_decode($document, true);
		if (!is_array($decoded)) {
			logDictionaryFetchInfo($selectedLanguage, 'dictionary_document_invalid_json', null);
			return null;
		}

		$dictionary = extractLocalizedDictionary($decoded, $selectedLanguage);
		logDictionaryFetchInfo($selectedLanguage, 'sqlite_ok', $dictionary);
		return $dictionary;
	} catch (Throwable $exception) {
		logDictionaryFetchInfo($selectedLanguage, 'sqlite_error: ' . $exception->getMessage(), null);
		return null;
	}
}

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

function logDictionaryFetchInfo(string $language, string $status, ?array $payload): void
{
	$logPath = STORAGE_PATH . '/logs/php-info.log';
	$logEntry = [
		'timestamp' => date('c'),
		'language' => $language,
		'status' => $status,
		'payload' => $payload,
	];
	$line = json_encode($logEntry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

	if (!is_string($line)) {
		return;
	}

	@file_put_contents($logPath, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
}
