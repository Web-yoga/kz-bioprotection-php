<?php

declare(strict_types=1);

/**
 * Picture partial for cron-generated upload variants (api/storage/uploads/webp/…).
 *
 * Expects one of:
 * - $originalPath — relative path, e.g. `2024/photo.png`
 * - $pathField — CMS field with `path` key
 * - $generatedImageResolved — pre-resolved array from uploadsResolveGeneratedImageUrls()
 *
 * Optional: $alt, $imgClass, $imgWidth, $imgHeight, $imgDecoding, $imgLoading ("lazy" or "eager")
 */

$imgClass = isset($imgClass) && is_string($imgClass) ? trim($imgClass) : '';
$imgWidth = isset($imgWidth) ? (int) $imgWidth : 0;
$imgHeight = isset($imgHeight) ? (int) $imgHeight : 0;
$imgDecoding = isset($imgDecoding) && is_string($imgDecoding) ? trim($imgDecoding) : '';
$imgLoading = isset($imgLoading) && is_string($imgLoading) ? trim($imgLoading) : '';

if ($imgLoading !== 'lazy' && $imgLoading !== 'eager') {
	$imgLoading = '';
}

$imgAttrParts = [];
if ($imgClass !== '') {
	$imgAttrParts[] = 'class="' . htmlspecialchars($imgClass, ENT_QUOTES, 'UTF-8') . '"';
}
if ($imgWidth > 0) {
	$imgAttrParts[] = 'width="' . $imgWidth . '"';
}
if ($imgHeight > 0) {
	$imgAttrParts[] = 'height="' . $imgHeight . '"';
}
if ($imgDecoding !== '') {
	$imgAttrParts[] = 'decoding="' . htmlspecialchars($imgDecoding, ENT_QUOTES, 'UTF-8') . '"';
}
if ($imgLoading !== '') {
	$imgAttrParts[] = 'loading="' . htmlspecialchars($imgLoading, ENT_QUOTES, 'UTF-8') . '"';
}
$imgAttrString = $imgAttrParts !== [] ? ' ' . implode(' ', $imgAttrParts) : '';

$originalPath = isset($originalPath) && is_string($originalPath)
	? ltrim(trim($originalPath), '/')
	: uploadsRelativePathFromPathField($pathField ?? null);

$alt = isset($alt) && is_string($alt) ? trim($alt) : '';

$resolved = isset($generatedImageResolved) && is_array($generatedImageResolved)
	? $generatedImageResolved
	: uploadsResolveGeneratedImageUrls($originalPath);

$originalUrl = trim((string) ($resolved['originalUrl'] ?? ''));
$webpUrl = trim((string) ($resolved['webpUrl'] ?? ''));
$imgUrl = trim((string) ($resolved['imgUrl'] ?? ''));
$usePicture = !empty($resolved['usePicture']);

if ($originalUrl === '') {
	unset($generatedImageResolved, $resolved, $originalPath, $originalUrl, $webpUrl, $imgUrl, $usePicture);

	return;
}

if ($usePicture && $webpUrl !== '') {
	?>
	<picture>
		<source
			srcset="<?= htmlspecialchars($webpUrl, ENT_QUOTES, 'UTF-8'); ?>"
			type="image/webp">
		<img<?= $imgAttrString; ?>
			src="<?= htmlspecialchars($imgUrl, ENT_QUOTES, 'UTF-8'); ?>"
			alt="<?= htmlspecialchars($alt, ENT_QUOTES, 'UTF-8'); ?>">
	</picture>
	<?php
	unset($generatedImageResolved, $resolved, $originalPath, $originalUrl, $webpUrl, $imgUrl, $usePicture);

	return;
}
?>
<img<?= $imgAttrString; ?>
	src="<?= htmlspecialchars($originalUrl, ENT_QUOTES, 'UTF-8'); ?>"
	alt="<?= htmlspecialchars($alt, ENT_QUOTES, 'UTF-8'); ?>">
<?php
unset($generatedImageResolved, $resolved, $originalPath, $originalUrl, $webpUrl, $imgUrl, $usePicture);
