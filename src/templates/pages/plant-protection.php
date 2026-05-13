<?php

declare(strict_types=1);

$dictionary = isset($dictionary) && is_array($dictionary) ? $dictionary : [];

$pagePlantProtectionPayload = isset($pageContent) && is_array($pageContent) ? $pageContent : [];
$aboutUsHtml = isset($pagePlantProtectionPayload['aboutUs']) && is_string($pagePlantProtectionPayload['aboutUs'])
	? trim($pagePlantProtectionPayload['aboutUs'])
	: '';
$topBoldTextHtml = isset($pagePlantProtectionPayload['topBoldText']) && is_string($pagePlantProtectionPayload['topBoldText'])
	? trim($pagePlantProtectionPayload['topBoldText'])
	: '';
$topPictureTextHtml = isset($pagePlantProtectionPayload['topPictureText']) && is_string($pagePlantProtectionPayload['topPictureText'])
	? trim($pagePlantProtectionPayload['topPictureText'])
	: '';
$topPictures = isset($pagePlantProtectionPayload['topPictures']) && is_array($pagePlantProtectionPayload['topPictures'])
	? $pagePlantProtectionPayload['topPictures']
	: [];

$plantProtectionTopPictureUrls = [];
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
	$plantProtectionTopPictureUrls[] = UPLOADS_BASE_URL . ltrim($path, '/');
}

$hasRenderableTopPictures = $plantProtectionTopPictureUrls !== [];
$diagramGrowthPictureUrls = array_slice($plantProtectionTopPictureUrls, 0, 4);

$hasFollowContent = $topBoldTextHtml !== '' || $topPictureTextHtml !== '' || $hasRenderableTopPictures;
$hasAboutSection = $aboutUsHtml !== '';
?>
<?php if ($hasAboutSection): ?>
	<section class="oil-content-section">
		<h2 class="section-title"><?= $dictionary['aboutUs']; ?></h2>
		<div class="oil-content-section__body oil-content-section__body--justified"><?= $aboutUsHtml; ?></div>
	</section>
<?php endif; ?>
<?php if ($hasFollowContent): ?>
	<div class="plant-protection-follow">
		<?php if ($topBoldTextHtml !== ''): ?>
			<div class="plant-protection-follow__lead"><?= $topBoldTextHtml; ?></div>
		<?php endif; ?>
		<?php if ($topPictureTextHtml !== ''): ?>
			<div><?= $topPictureTextHtml; ?></div>
		<?php endif; ?>
		<?php require TEMPLATES_PATH . '/partials/plant-protection/plant-protection-top-pictures.php'; ?>
		<?php if ($diagramGrowthPictureUrls !== []): ?>
			<?php require TEMPLATES_PATH . '/partials/plant-protection/plant-protection-growth-diagram.php'; ?>
		<?php endif; ?>
	</div>
<?php endif; ?>
