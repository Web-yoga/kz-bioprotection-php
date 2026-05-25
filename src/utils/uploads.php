<?php

declare(strict_types=1);

const UPLOADS_GENERATED_SUBDIR = 'webp';

/**
 * Relative upload path from a CMS path field, e.g. `2024/photo.png`.
 *
 * @param mixed $pathField e.g. `['path' => 'folder/file.jpg']`
 */
function uploadsRelativePathFromPathField(mixed $pathField): string
{
	if (!is_array($pathField)) {
		return '';
	}

	$path = isset($pathField['path']) ? trim((string) $pathField['path']) : '';

	return $path !== '' ? ltrim($path, '/') : '';
}

/**
 * Public URL for a CMS upload referenced by a node with a `path` key (same shape as image/file fields from the API).
 *
 * @param mixed $pathField e.g. `['path' => 'folder/file.jpg']`
 */
function uploadsPublicUrlFromPathField(mixed $pathField): string
{
	$path = uploadsRelativePathFromPathField($pathField);
	if ($path === '') {
		return '';
	}

	return UPLOADS_BASE_URL . $path;
}

/**
 * Public URL for an original upload relative path.
 */
function uploadsPublicUrlFromRelativePath(string $relativePath): string
{
	$relativePath = ltrim(trim($relativePath), '/');
	if ($relativePath === '') {
		return '';
	}

	return UPLOADS_BASE_URL . $relativePath;
}

/**
 * Local filesystem directory for API uploads (used to detect generated variants).
 */
function uploadsFilesystemBaseDir(): string
{
	if (!defined('BASE_PATH')) {
		return '';
	}

	return BASE_PATH . '/api/storage/uploads';
}

/**
 * Relative path of a cron-generated variant (webp/jpg) for an original upload path.
 */
function uploadsGeneratedVariantRelativePath(string $originRelativePath, string $extension): string
{
	$originRelativePath = ltrim(trim($originRelativePath), '/');
	if ($originRelativePath === '') {
		return '';
	}

	$extension = strtolower(ltrim(trim($extension), '.'));
	if ($extension === '') {
		return '';
	}

	$pathInfo = pathinfo($originRelativePath);
	$directory = isset($pathInfo['dirname']) && $pathInfo['dirname'] !== '.' ? (string) $pathInfo['dirname'] : '';
	$filename = isset($pathInfo['filename']) ? (string) $pathInfo['filename'] : '';
	if ($filename === '') {
		return '';
	}

	$prefix = $directory !== '' ? rtrim($directory, '/') . '/' : '';

	return UPLOADS_GENERATED_SUBDIR . '/' . $prefix . $filename . '.' . $extension;
}

function uploadsGeneratedVariantFilesystemPath(string $originRelativePath, string $extension): string
{
	$variantRelativePath = uploadsGeneratedVariantRelativePath($originRelativePath, $extension);
	if ($variantRelativePath === '') {
		return '';
	}

	$uploadsDir = uploadsFilesystemBaseDir();
	if ($uploadsDir === '') {
		return '';
	}

	return rtrim($uploadsDir, '/\\') . '/' . $variantRelativePath;
}

function uploadsGeneratedVariantExists(string $originRelativePath, string $extension): bool
{
	$filesystemPath = uploadsGeneratedVariantFilesystemPath($originRelativePath, $extension);

	return $filesystemPath !== '' && is_file($filesystemPath);
}

/**
 * Resolved display URLs for cron-generated webp/jpg variants with fallback to the original upload.
 *
 * @return array{
 *     originalUrl: string,
 *     webpUrl: string,
 *     jpgUrl: string,
 *     hasWebp: bool,
 *     hasJpg: bool,
 *     usePicture: bool,
 *     imgUrl: string
 * }
 */
function uploadsResolveGeneratedImageUrls(string $originRelativePath): array
{
	$empty = [
		'originalUrl' => '',
		'webpUrl' => '',
		'jpgUrl' => '',
		'hasWebp' => false,
		'hasJpg' => false,
		'usePicture' => false,
		'imgUrl' => '',
	];

	$originRelativePath = ltrim(trim($originRelativePath), '/');
	if ($originRelativePath === '') {
		return $empty;
	}

	$originalUrl = uploadsPublicUrlFromRelativePath($originRelativePath);
	$webpRelativePath = uploadsGeneratedVariantRelativePath($originRelativePath, 'webp');
	$jpgRelativePath = uploadsGeneratedVariantRelativePath($originRelativePath, 'jpg');
	$webpUrl = $webpRelativePath !== '' ? UPLOADS_BASE_URL . $webpRelativePath : '';
	$jpgUrl = $jpgRelativePath !== '' ? UPLOADS_BASE_URL . $jpgRelativePath : '';

	$hasWebp = uploadsGeneratedVariantExists($originRelativePath, 'webp');
	$hasJpg = uploadsGeneratedVariantExists($originRelativePath, 'jpg');
	$usePicture = $hasWebp;
	$imgUrl = $hasJpg ? $jpgUrl : $originalUrl;

	return [
		'originalUrl' => $originalUrl,
		'webpUrl' => $webpUrl,
		'jpgUrl' => $jpgUrl,
		'hasWebp' => $hasWebp,
		'hasJpg' => $hasJpg,
		'usePicture' => $usePicture,
		'imgUrl' => $imgUrl,
	];
}
