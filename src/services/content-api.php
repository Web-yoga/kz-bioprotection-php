<?php

declare(strict_types=1);

const CONTENT_API_BASE_URL = 'https://api.bioprotection.kz/api/content/';

function buildContentApiUrl(string $resourceType, string $resourceName, string $language): string
{
    $normalizedType = $resourceType === 'items' ? 'items' : 'item';
    $normalizedName = trim($resourceName);
    $normalizedLanguage = trim($language) !== '' ? trim($language) : 'en';

    return rtrim(CONTENT_API_BASE_URL, '/') . '/'
        . rawurlencode($normalizedType) . '/'
        . rawurlencode($normalizedName)
        . '?lang=' . rawurlencode($normalizedLanguage);
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

function fetchDictionaryContent(string $language): ?array
{
    return fetchContentApiEntity('item', 'dictionary', $language);
}

function fetchFeedbackFormContent(string $language): ?array
{
    return fetchContentApiEntity('item', 'feedbackForm', $language);
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
