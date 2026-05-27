<?php

declare(strict_types=1);

/**
 * Inline style with CSS variables for .responsive-bg.
 * Naming format: name.ext / name.webp / name-m.ext / name-m.webp.
 * Set $useMobileVariant = false to use only non-mobile names on all screens.
 */
function buildResponsiveBackgroundStyle(string $sourcePath, bool $useMobileVariant = true): string
{
	$sourcePath = trim($sourcePath);
	if ($sourcePath === '') {
		return '';
	}

	$pathInfo = pathinfo($sourcePath);
	$directory = isset($pathInfo['dirname']) && $pathInfo['dirname'] !== '.'
		? (string) $pathInfo['dirname']
		: '';
	$filename = isset($pathInfo['filename']) ? (string) $pathInfo['filename'] : '';
	$fallbackExtension = isset($pathInfo['extension']) ? strtolower((string) $pathInfo['extension']) : '';

	if ($filename === '' || $fallbackExtension === '') {
		return '';
	}

	$normalizedFilename = preg_replace('/-m$/', '', $filename);
	if (!is_string($normalizedFilename) || $normalizedFilename === '') {
		return '';
	}

	$prefix = $directory !== '' ? rtrim($directory, '/') . '/' : '';
	$desktopFallback = $prefix . $normalizedFilename . '.' . $fallbackExtension;
	$desktopWebp = $prefix . $normalizedFilename . '.webp';
	$mobileFallback = $useMobileVariant
		? $prefix . $normalizedFilename . '-m.' . $fallbackExtension
		: $desktopFallback;
	$mobileWebp = $useMobileVariant
		? $prefix . $normalizedFilename . '-m.webp'
		: $desktopWebp;

	return '--responsive-bg-mobile-fallback: url(\''
		. htmlspecialchars($mobileFallback, ENT_QUOTES, 'UTF-8')
		. '\');--responsive-bg-desktop-fallback: url(\''
		. htmlspecialchars($desktopFallback, ENT_QUOTES, 'UTF-8')
		. '\');--responsive-bg-mobile-webp: url(\''
		. htmlspecialchars($mobileWebp, ENT_QUOTES, 'UTF-8')
		. '\');--responsive-bg-desktop-webp: url(\''
		. htmlspecialchars($desktopWebp, ENT_QUOTES, 'UTF-8')
		. '\');';
}
