<?php

declare(strict_types=1);

$pageOilPayload = isset($pageContent) && is_array($pageContent) ? $pageContent : [];
$topText = isset($pageOilPayload['topText']) ? trim((string) $pageOilPayload['topText']) : '';
$benefits = $pageOilPayload['benefits'] ?? [];
$aboutTheTechnology = $pageOilPayload['aboutTheTechnology'];
$howDoesItWork = $pageOilPayload['howDoesItWork'];
$stagesOfCleanupAndBioremediation = $pageOilPayload['stagesOfCleanupAndBioremediation'];
$stagesOfCleanupAndBioremediationImageUrl = UPLOADS_BASE_URL . ltrim($pageOilPayload['stagesOfCleanupAndBioremediationImage']['path'], '/');
$diagramSoilAndGroundwaterImageUrl = UPLOADS_BASE_URL . ltrim($pageOilPayload['diagramSoilAndGroundwaterImage']['path'], '/');
$diagramSoilAndGroundwaterImageMobileUrl = UPLOADS_BASE_URL . ltrim($pageOilPayload['diagramSoilAndGroundwaterImageMobile']['path'], '/');
$certificationsAccreditationsRaw = isset($pageOilPayload['certificationsAccreditations']) && is_array($pageOilPayload['certificationsAccreditations'])
	? $pageOilPayload['certificationsAccreditations']
	: [];
$certificationsAccreditations = array_values(array_filter(
	$certificationsAccreditationsRaw,
	static fn($item): bool => isset($item['path']) && trim((string) $item['path']) !== ''
));
$caseStudyRaw = isset($pageOilPayload['caseStudy']) && is_array($pageOilPayload['caseStudy'])
	? $pageOilPayload['caseStudy']
	: [];
$caseStudy = array_values(array_filter(
	$caseStudyRaw,
	static fn($item): bool => isset($item['path']) && trim((string) $item['path']) !== ''
));
?>
<?php if ($topText !== ''): ?>
	<section class="home-top-text">
		<div class="home-top-text__content"><?= $topText; ?></div>
	</section>
<?php endif; ?>
<section class="key-benefits key-benefits--oil-cleaning">
	<h2 class="section-title"><?= $dictionary['keyBenefits']; ?></h2>
	<div class="key-benefits__layout">
		<div class="key-benefits__media img-shadow">
			<div class="key-benefits__media-bg" aria-hidden="true"></div>
		</div>
		<ul class="key-benefits__list">
			<?php foreach ($benefits as $benefit): ?>
				<?php
				$benefitText = $benefit['text'];
				$benefitIconUrl = UPLOADS_BASE_URL . ltrim($benefit['icon']['path'], '/');
				?>
				<li class="key-benefits__item key-benefits__item--oil-cleaning">
					<img
						class="key-benefits__icon key-benefits__icon--oil-cleaning"
						src="<?= htmlspecialchars($benefitIconUrl, ENT_QUOTES, 'UTF-8'); ?>"
						alt=""
						decoding="async" />
					<span class="key-benefits__text"><?= $benefitText; ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
<section class="oil-content-section">
	<h2 class="section-title"><?= $dictionary['aboutTheTechnology']; ?></h2>
	<div class="oil-content-section__body oil-content-section__body--justified"><?= $aboutTheTechnology; ?></div>
</section>
<section class="oil-content-section">
	<h2 class="section-title"><?= $dictionary['howDoesItWork']; ?></h2>
	<div class="oil-content-section__body oil-content-section__body--justified"><?= $howDoesItWork; ?></div>
	<div class="oil-how-gallery">
		<div class="oil-how-gallery__item img-shadow">
			<img
				class="oil-how-gallery__img"
				src="/img/oil-cleaning/oil-how_1.jpg"
				alt=""
				decoding="async" />
		</div>
		<div class="oil-how-gallery__item img-shadow">
			<img
				class="oil-how-gallery__img"
				src="/img/oil-cleaning/oil-how_2.jpg"
				alt=""
				decoding="async" />
		</div>
		<div class="oil-how-gallery__item img-shadow">
			<img
				class="oil-how-gallery__img"
				src="/img/oil-cleaning/oil-how_3.jpg"
				alt=""
				decoding="async" />
		</div>
	</div>
</section>
<section class="oil-content-section oil-stages">
	<h2 class="section-title"><?= $dictionary['stagesOfCleanupAndBioremediation']; ?></h2>
	<div class="oil-stages__image-wrap">
		<img
			class="oil-stages__image"
			src="<?= htmlspecialchars($stagesOfCleanupAndBioremediationImageUrl, ENT_QUOTES, 'UTF-8'); ?>"
			alt=""
			decoding="async" />
	</div>
	<div class="oil-stages__mobile-list">
		<?php foreach ($stagesOfCleanupAndBioremediation as $stage): ?>
			<article class="oil-stage-card">
				<div class="oil-stage-card__number"><?= htmlspecialchars((string) $stage['number'], ENT_QUOTES, 'UTF-8'); ?></div>
				<div class="oil-stage-card__content">
					<h3 class="oil-stage-card__title"><?= htmlspecialchars((string) $stage['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
					<div class="oil-stage-card__text"><?= $stage['text']; ?></div>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</section>
<section class="oil-content-section oil-diagram">
	<h2 class="section-title"><?= $dictionary['diagramSoilAndGroundwater']; ?></h2>
	<div class="oil-diagram__image-wrap">
		<picture>
			<source media="(max-width: 767px)" srcset="<?= htmlspecialchars($diagramSoilAndGroundwaterImageMobileUrl, ENT_QUOTES, 'UTF-8'); ?>" />
			<img
				class="oil-diagram__image"
				src="<?= htmlspecialchars($diagramSoilAndGroundwaterImageUrl, ENT_QUOTES, 'UTF-8'); ?>"
				alt=""
				decoding="async" />
		</picture>
	</div>
</section>
<section class="oil-content-section">
	<div class="oil-before-after img-shadow" data-before-after style="--before-after-position: 50;">
		<img
			class="oil-before-after__image"
			src="/img/oil-cleaning/oil-after.png"
			alt=""
			decoding="async" />
		<div class="oil-before-after__after-layer">
			<img
				class="oil-before-after__image"
				src="/img/oil-cleaning/oil-before.png"
				alt=""
				decoding="async" />
		</div>
		<div class="oil-before-after__divider" aria-hidden="true"></div>
		<div class="oil-before-after__handle" aria-hidden="true">
			<span class="oil-before-after__handle-line"></span>
			<span class="oil-before-after__handle-line"></span>
		</div>
		<input
			class="oil-before-after__range"
			type="range"
			min="0"
			max="100"
			value="50"
			step="1"
			aria-label="Image comparison slider" />
	</div>
</section>
<?php if ($certificationsAccreditations !== []): ?>
	<section class="oil-content-section oil-certifications">
		<h2 class="section-title"><?= $dictionary['certificationsAccreditations']; ?></h2>
		<div class="oil-certifications__slider swiper" data-oil-certifications-slider>
			<div class="swiper-wrapper">
				<?php foreach ($certificationsAccreditations as $certification): ?>
					<?php $certificationImageUrl = UPLOADS_BASE_URL . ltrim((string) $certification['path'], '/'); ?>
					<div class="swiper-slide">
						<div class="oil-certifications__slide img-shadow">
							<img
								class="oil-certifications__image"
								src="<?= htmlspecialchars($certificationImageUrl, ENT_QUOTES, 'UTF-8'); ?>"
								alt=""
								decoding="async" />
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="oil-certifications__pagination" data-oil-certifications-pagination></div>
		</div>
	</section>
<?php endif; ?>
<?php if ($caseStudy !== []): ?>
	<section class="oil-content-section oil-case-study">
		<h2 class="section-title"><?= $dictionary['caseStudy']; ?></h2>
		<div class="oil-case-study__grid">
			<?php foreach ($caseStudy as $caseStudyItem): ?>
				<?php $caseStudyImageUrl = UPLOADS_BASE_URL . ltrim((string) $caseStudyItem['path'], '/'); ?>
				<div class="oil-case-study__item img-shadow">
					<img
						class="oil-case-study__image"
						src="<?= htmlspecialchars($caseStudyImageUrl, ENT_QUOTES, 'UTF-8'); ?>"
						alt=""
						decoding="async" />
				</div>
			<?php endforeach; ?>
		</div>
	</section>
<?php endif; ?>
<?php
require TEMPLATES_PATH . '/partials/contact-form.php';
$articlesJson = fetchArticlesCollection((string) ($currentLanguage ?? 'en'));
?>
<section id="news" class="news-events" style="margin-top: var(--section-spacing); margin-bottom: var(--section-spacing);">
	<h2 class="section-title"><?= $dictionary['newsEvents']; ?></h2>
	<?php require TEMPLATES_PATH . '/partials/news-list.php'; ?>
</section>