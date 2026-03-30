<?php

declare(strict_types=1);

const API_BASE_URL = 'https://api.bioprotection.kz';
const CONTENT_API_BASE_URL = API_BASE_URL . '/api/content/';
const UPLOADS_BASE_URL = API_BASE_URL . '/storage/uploads/';

function resolveContentApiLanguage(string $language = ''): string
{
	$supportedLanguages = ['ru', 'en', 'kz'];
	$normalizedLanguage = trim($language);
	if ($normalizedLanguage !== '' && in_array($normalizedLanguage, $supportedLanguages, true)) {
		return $normalizedLanguage;
	}

	$queryLanguage = isset($_GET['locale']) && is_string($_GET['locale']) ? trim($_GET['locale']) : '';
	if ($queryLanguage !== '' && in_array($queryLanguage, $supportedLanguages, true)) {
		return $queryLanguage;
	}

	$requestPath = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '', '/');
	$pathSegments = $requestPath !== '' ? explode('/', $requestPath) : [];
	$pathLanguage = isset($pathSegments[0]) ? trim((string) $pathSegments[0]) : '';
	if ($pathLanguage !== '' && in_array($pathLanguage, $supportedLanguages, true)) {
		return $pathLanguage;
	}

	return '';
}

function buildContentApiUrl(string $resourceType, string $resourceName, string $language): string
{
	$normalizedType = $resourceType === 'items' ? 'items' : 'item';
	$normalizedName = trim($resourceName);
	$resolvedLanguage = resolveContentApiLanguage($language);

	$url = rtrim(CONTENT_API_BASE_URL, '/') . '/'
		. rawurlencode($normalizedType) . '/'
		. rawurlencode($normalizedName);

	if ($resolvedLanguage !== '') {
		$url .= '?locale=' . rawurlencode($resolvedLanguage);
	}

	return $url;
}

function fetchContentApiEntity(string $resourceType, string $resourceName, string $language): ?array
{
	$url = buildContentApiUrl($resourceType, $resourceName, $language);
	static $requestCache = [];

	if (array_key_exists($url, $requestCache)) {
		return $requestCache[$url];
	}

	$response = @file_get_contents($url);

	if ($response === false && function_exists('curl_init')) {
		$curlHandle = curl_init($url);
		if ($curlHandle !== false) {
			curl_setopt_array($curlHandle, [
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CONNECTTIMEOUT => 5,
				CURLOPT_TIMEOUT => 10,
				CURLOPT_FOLLOWLOCATION => true,
			]);
			$curlResponse = curl_exec($curlHandle);
			if (is_string($curlResponse)) {
				$response = $curlResponse;
			}
			curl_close($curlHandle);
		}
	}

	if ($response === false) {
		$requestCache[$url] = null;
		return $requestCache[$url];
	}

	$decoded = json_decode($response, true);
	if (!is_array($decoded)) {
		$requestCache[$url] = null;
		return $requestCache[$url];
	}

	$requestCache[$url] = $decoded;
	return $requestCache[$url];
}

function fetchPageContentBySlug(string $slug, string $language): ?array
{
	$pageSingletonMap = [
		'home' => 'pageHome',
	];

	if (!isset($pageSingletonMap[$slug])) {
		return null;
	}

	return fetchContentApiEntity('item', $pageSingletonMap[$slug], $language);
}

function fetchArticlesRaw(string $language): ?array
{
	return fetchContentApiEntity('items', 'articles', $language);
}

function normalizePathSlug(string $slug): string
{
	$normalized = trim($slug);
	$normalized = strtolower($normalized);
	$normalized = ltrim($normalized, '/');
	$normalized = rtrim($normalized, '/');
	return $normalized;
}

function fetchArticlesCollection(string $language): array
{
	$articlesRaw = fetchArticlesRaw($language);
	return normalizeItemsCollection($articlesRaw);
}

function findArticleBySlug(array $articlesCollection, string $slug): ?array
{
	$target = normalizePathSlug($slug);
	if ($target === '') {
		return null;
	}

	foreach ($articlesCollection as $article) {
		if (!is_array($article)) {
			continue;
		}

		if (!isset($article['slug']) || !is_string($article['slug'])) {
			continue;
		}

		$itemSlug = normalizePathSlug($article['slug']);
		if ($itemSlug === $target) {
			return $article;
		}
	}

	return null;
}

function fetchArticleBySlug(string $slug, string $language): ?array
{
	$articles = fetchArticlesCollection($language);
	return findArticleBySlug($articles, $slug);
}

function fetchDictionaryContent(string $language): ?array
{
	return fetchContentApiEntity('item', 'dictionary', $language);
}

function fetchFeedbackFormContent(string $language): ?array
{
	return fetchContentApiEntity('item', 'feedbackForm', $language);
}

function fetchFooterContent(string $language): ?array
{
	return fetchContentApiEntity('item', 'footer', $language);
}

function normalizeItemsCollection(?array $collectionResponse): array
{
	if (!is_array($collectionResponse) || $collectionResponse === []) {
		return [];
	}

	if (isset($collectionResponse[0]) && is_array($collectionResponse[0])) {
		return $collectionResponse;
	}

	foreach (['entries', 'items', 'data', 'results'] as $itemsKey) {
		if (isset($collectionResponse[$itemsKey]) && is_array($collectionResponse[$itemsKey])) {
			$items = $collectionResponse[$itemsKey];
			if (isset($items[0]) && is_array($items[0])) {
				return $items;
			}
		}
	}

	return [];
}

function normalizeSeoSlug(string $slug): string
{
	$normalized = trim($slug);
	if ($normalized === '' || $normalized === 'home') {
		return '/';
	}

	$normalized = '/' . ltrim($normalized, '/');
	return rtrim($normalized, '/') ?: '/';
}

function findSeoItemBySlug(array $items, string $slug): ?array
{
	$targetSlug = normalizeSeoSlug($slug);

	foreach ($items as $item) {
		if (!is_array($item)) {
			continue;
		}

		if (!isset($item['slug']) || !is_string($item['slug'])) {
			continue;
		}

		if (normalizeSeoSlug($item['slug']) === $targetSlug) {
			return $item;
		}
	}

	return null;
}

function fetchSeoContentBySlug(string $slug, string $language): ?array
{
	$requestedLanguage = trim($language) !== '' ? trim($language) : 'en';
	$seoCollection = fetchContentApiEntity('items', 'seo', $requestedLanguage);
	$seoItems = normalizeItemsCollection($seoCollection);
	$seoItem = findSeoItemBySlug($seoItems, $slug);

	if (is_array($seoItem)) {
		return $seoItem;
	}

	if ($requestedLanguage !== 'en') {
		$fallbackCollection = fetchContentApiEntity('items', 'seo', 'en');
		$fallbackItems = normalizeItemsCollection($fallbackCollection);
		$fallbackItem = findSeoItemBySlug($fallbackItems, $slug);
		if (is_array($fallbackItem)) {
			return $fallbackItem;
		}
	}

	return null;
}

function normalizeDictionaryMap(?array $dictionaryContent): array
{
	if (!is_array($dictionaryContent) || $dictionaryContent === []) {
		return [];
	}

	$dictionaryMap = [];

	foreach ($dictionaryContent as $key => $value) {
		if (is_string($key) && str_starts_with($key, '_')) {
			continue;
		}

		if (is_string($value)) {
			$dictionaryMap[(string) $key] = $value;
			continue;
		}

		if (!is_array($value)) {
			continue;
		}

		$items = isset($value[0]) && is_array($value[0]) ? $value : [$value];
		foreach ($items as $item) {
			if (!is_array($item)) {
				continue;
			}

			$code = '';
			foreach (['code', 'key', 'slug', 'name'] as $codeField) {
				if (isset($item[$codeField]) && is_string($item[$codeField]) && trim($item[$codeField]) !== '') {
					$code = trim($item[$codeField]);
					break;
				}
			}

			$phrase = '';
			foreach (['value', 'text', 'title', 'label', 'phrase'] as $valueField) {
				if (isset($item[$valueField]) && is_string($item[$valueField]) && trim($item[$valueField]) !== '') {
					$phrase = trim($item[$valueField]);
					break;
				}
			}

			if ($code !== '' && $phrase !== '') {
				$dictionaryMap[$code] = $phrase;
			}
		}
	}

	return $dictionaryMap;
}
