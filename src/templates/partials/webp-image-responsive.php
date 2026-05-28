<?php

declare(strict_types=1);

/**
 * Renders a picture that uses:
 * - optional mobile files with "-m" suffix on mobile viewport;
 * - .webp sources when browser supports WebP;
 * - original extension as fallback when WebP is not supported.
 * Set $useMobileVariant = false to always use non-mobile file names.
 */

$imagePath = isset($imagePath) && is_string($imagePath) ? trim($imagePath) : '';
$alt = isset($alt) && is_string($alt) ? trim($alt) : '';
$imgClass = isset($imgClass) && is_string($imgClass) ? trim($imgClass) : '';
$useMobileVariant = !isset($useMobileVariant) || (bool) $useMobileVariant;

if ($imagePath === '') {
	return;
}

$pathInfo = pathinfo($imagePath);
$directory = isset($pathInfo['dirname']) && $pathInfo['dirname'] !== '.'
	? (string) $pathInfo['dirname']
	: '';
$filename = isset($pathInfo['filename']) ? (string) $pathInfo['filename'] : '';
$extension = isset($pathInfo['extension']) ? strtolower((string) $pathInfo['extension']) : '';

if ($filename === '' || $extension === '') {
	return;
}

$pathPrefix = $directory !== '' ? rtrim($directory, '/') . '/' : '';
$desktopFallbackPath = $pathPrefix . $filename . '.' . $extension;
$mobileFallbackPath = $pathPrefix . $filename . '-m.' . $extension;
$desktopWebpPath = $pathPrefix . $filename . '.webp';
$mobileWebpPath = $pathPrefix . $filename . '-m.webp';

$imgClassAttribute = $imgClass !== ''
	? ' class="' . htmlspecialchars($imgClass, ENT_QUOTES, 'UTF-8') . '"'
	: '';
?>
<picture>
	<?php if ($useMobileVariant): ?>
		<source
			media="(max-width: 767px)"
			srcset="<?= htmlspecialchars($mobileWebpPath, ENT_QUOTES, 'UTF-8'); ?>"
			type="image/webp">
		<source
			media="(max-width: 767px)"
			srcset="<?= htmlspecialchars($mobileFallbackPath, ENT_QUOTES, 'UTF-8'); ?>">
	<?php endif; ?>
	<source
		srcset="<?= htmlspecialchars($desktopWebpPath, ENT_QUOTES, 'UTF-8'); ?>"
		type="image/webp">
	<img<?= $imgClassAttribute; ?>
		src="<?= htmlspecialchars($desktopFallbackPath, ENT_QUOTES, 'UTF-8'); ?>"
		alt="<?= htmlspecialchars($alt, ENT_QUOTES, 'UTF-8'); ?>">
</picture>