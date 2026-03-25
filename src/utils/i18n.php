<?php

declare(strict_types=1);

/**
 * Simple i18n helper for server-rendered PHP templates.
 *
 * Usage: i18n('menu.home', 'en')
 */
function i18n(string $key, string $lang = 'en'): string
{
    static $cache = [];

    $allowed = ['ru', 'en', 'kz'];
    $lang = in_array($lang, $allowed, true) ? $lang : 'en';

    if (!isset($cache[$lang])) {
        $langJsonPath = dirname(__DIR__, 2) . '/lang/' . $lang . '.json';
        $raw = is_file($langJsonPath) ? file_get_contents($langJsonPath) : false;

        $decoded = [];
        if (is_string($raw) && $raw !== '') {
            $json = json_decode($raw, true);
            if (is_array($json)) {
                $decoded = $json;
            }
        }

        $cache[$lang] = $decoded;
    }

    $segments = explode('.', $key);
    $current = $cache[$lang];

    foreach ($segments as $segment) {
        if (!is_array($current) || !array_key_exists($segment, $current)) {
            return $key;
        }
        $current = $current[$segment];
    }

    return is_string($current) ? $current : $key;
}

