<?php

declare(strict_types=1);

$topPictureItems = [];

if (isset($plantProtectionTopPictureUrls) && is_array($plantProtectionTopPictureUrls)) {
	$topPictureItems = array_values(
		array_filter(
			$plantProtectionTopPictureUrls,
			static fn($url): bool => is_string($url) && trim($url) !== ''
		)
	);
} else {
	$topPictures = isset($topPictures) && is_array($topPictures) ? $topPictures : [];

	foreach ($topPictures as $row) {
		if (!is_array($row)) {
			continue;
		}
		$path = '';
		if (isset($row['path']) && is_string($row['path'])) {
			$path = trim($row['path']);
		} elseif (isset($row['image']) && is_array($row['image']) && isset($row['image']['path']) && is_string($row['image']['path'])) {
			$path = trim($row['image']['path']);
		}
		if ($path === '') {
			continue;
		}
		$topPictureItems[] = UPLOADS_BASE_URL . ltrim($path, '/');
	}
}

if ($topPictureItems === []) {
	return;
}
?>
<div class="plant-protection-top-pictures">
	<ul class="plant-protection-top-pictures__grid">
		<?php foreach ($topPictureItems as $imageUrl): ?>
			<li class="plant-protection-top-pictures__item">
				<img
					class="plant-protection-top-pictures__img"
					src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8'); ?>"
					alt=""
					decoding="async"
					loading="lazy" />
			</li>
		<?php endforeach; ?>
	</ul>
</div>
