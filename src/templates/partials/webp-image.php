<?php

declare(strict_types=1);

$webpPath = isset($webpPath) && is_string($webpPath) ? trim($webpPath) : '';
$fallbackPath = isset($fallbackPath) && is_string($fallbackPath) ? trim($fallbackPath) : '';
$alt = isset($alt) && is_string($alt) ? trim($alt) : '';

if ($webpPath === '' || $fallbackPath === '') {
	return;
}
?>
<picture>
	<source
		srcset="<?= htmlspecialchars($webpPath, ENT_QUOTES, 'UTF-8'); ?>"
		type="image/webp">
	<img
		src="<?= htmlspecialchars($fallbackPath, ENT_QUOTES, 'UTF-8'); ?>"
		alt="<?= htmlspecialchars($alt, ENT_QUOTES, 'UTF-8'); ?>">
</picture>
