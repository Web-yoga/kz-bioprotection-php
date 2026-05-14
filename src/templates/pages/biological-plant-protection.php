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

$hasRenderableTopPictures = true;

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
		<?php require TEMPLATES_PATH . '/partials/biological-plant-protection/biological-plant-protection-top-pictures.php'; ?>
	</div>
<?php endif; ?>
