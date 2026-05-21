<?php

declare(strict_types=1);

/**
 * Builds a responsive asset path from a base image path, e.g.
 * /img/foo/bar.png → /img/foo/bar-1280.webp
 */
function buildResponsiveImagePath(string $sourcePath, string $sizeSuffix, string $extension): string
{
	$sourcePath = trim($sourcePath);
	if ($sourcePath === '') {
		return '';
	}

	$pathInfo = pathinfo($sourcePath);
	$directory = isset($pathInfo['dirname']) && $pathInfo['dirname'] !== '.' ? (string) $pathInfo['dirname'] : '';
	$filename = isset($pathInfo['filename']) ? (string) $pathInfo['filename'] : '';
	if ($filename === '') {
		return '';
	}

	$normalizedFilename = preg_replace('/-(1280|1920)$/', '', $filename);
	if (!is_string($normalizedFilename) || $normalizedFilename === '') {
		return '';
	}

	$prefix = $directory !== '' ? rtrim($directory, '/') . '/' : '';

	return $prefix . $normalizedFilename . '-' . $sizeSuffix . '.' . $extension;
}

/**
 * Inline style with CSS variables for .responsive-bg (webp + 1280/1920 breakpoints in app.css).
 */
function buildResponsiveBackgroundStyle(string $sourcePath): string
{
	$sourcePath = trim($sourcePath);
	if ($sourcePath === '') {
		return '';
	}

	$pathInfo = pathinfo($sourcePath);
	$fallbackExtension = isset($pathInfo['extension']) ? strtolower((string) $pathInfo['extension']) : '';
	if ($fallbackExtension === '') {
		return '';
	}

	$mobileFallback = buildResponsiveImagePath($sourcePath, '1280', $fallbackExtension);
	$desktopFallback = buildResponsiveImagePath($sourcePath, '1920', $fallbackExtension);
	$mobileWebp = buildResponsiveImagePath($sourcePath, '1280', 'webp');
	$desktopWebp = buildResponsiveImagePath($sourcePath, '1920', 'webp');

	if ($mobileFallback === '' || $desktopFallback === '' || $mobileWebp === '' || $desktopWebp === '') {
		return '';
	}

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
