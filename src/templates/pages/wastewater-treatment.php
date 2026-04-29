<?php

declare(strict_types=1);

$dictionary = isset($dictionary) && is_array($dictionary) ? $dictionary : [];

$pageWastewaterPayload = isset($pageContent) && is_array($pageContent) ? $pageContent : [];
$topText = isset($pageWastewaterPayload['topText']) ? trim((string) $pageWastewaterPayload['topText']) : '';
$benefits = $pageWastewaterPayload['benefits'] ?? [];
$deployment = $pageWastewaterPayload['deployment'] ?? [];
$wastewaterTreatmentStagesImageUrl = UPLOADS_BASE_URL . ltrim($pageWastewaterPayload['wastewaterTreatmentStagesImage']['path'], '/');
$wastewaterTreatmentStagesImageMobileUrl = UPLOADS_BASE_URL . ltrim($pageWastewaterPayload['wastewaterTreatmentStagesImageMobile']['path'], '/');
$parametersOfWastewater = $pageWastewaterPayload['parametersOfWastewater'] ?? [];
$requirementsForPurifiedWater = $pageWastewaterPayload['requirementsForPurifiedWater'] ?? [];
$certificationsAccreditationsRaw = isset($pageWastewaterPayload['certificationsAccreditations']) && is_array($pageWastewaterPayload['certificationsAccreditations'])
	? $pageWastewaterPayload['certificationsAccreditations']
	: [];
$certificationsAccreditations = array_values(array_filter(
	$certificationsAccreditationsRaw,
	static fn($item): bool => isset($item['path']) && trim((string) $item['path']) !== ''
));
$caseStudyRaw = isset($pageWastewaterPayload['caseStudy']) && is_array($pageWastewaterPayload['caseStudy'])
	? $pageWastewaterPayload['caseStudy']
	: [];
$caseStudy = array_values(array_filter(
	$caseStudyRaw,
	static fn($item): bool => isset($item['path']) && trim((string) $item['path']) !== ''
));
$caseStudyTextRaw = isset($pageWastewaterPayload['caseStudyText']) && is_array($pageWastewaterPayload['caseStudyText'])
	? $pageWastewaterPayload['caseStudyText']
	: [];
$caseStudyText = array_values(array_filter(
	$caseStudyTextRaw,
	static fn($item): bool => trim((string) $item) !== ''
));
?>
<?php if ($topText !== ''): ?>
	<section class="home-top-text">
		<div class="home-top-text__content"><?= $topText; ?></div>
	</section>
<?php endif; ?>
<section class="key-benefits key-benefits--wastewater-treatment">
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
				<li class="key-benefits__item key-benefits__item--wastewater-treatment">
					<img
						class="key-benefits__icon key-benefits__icon--wastewater-treatment"
						src="<?= htmlspecialchars($benefitIconUrl, ENT_QUOTES, 'UTF-8'); ?>"
						alt=""
						decoding="async" />
					<span class="key-benefits__text"><?= $benefitText; ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
<section class="wastewater-deployment">
	<h2 class="section-title"><?= $dictionary['deployment']; ?></h2>
	<div class="wastewater-deployment__list">
		<?php foreach ($deployment as $deploymentItem): ?>
			<article class="oil-stage-card">
				<div class="oil-stage-card__number"><?= htmlspecialchars((string) $deploymentItem['number'], ENT_QUOTES, 'UTF-8'); ?></div>
				<div class="oil-stage-card__content">
					<h3 class="oil-stage-card__title"><?= htmlspecialchars((string) $deploymentItem['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
					<div class="oil-stage-card__text"><?= $deploymentItem['text']; ?></div>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</section>
<section class="oil-content-section oil-diagram wastewater-stages">
	<h2 class="section-title"><?= $dictionary['wastewaterStages']; ?></h2>
	<div class="oil-diagram__image-wrap">
		<picture>
			<source media="(max-width: 767px)" srcset="<?= htmlspecialchars($wastewaterTreatmentStagesImageMobileUrl, ENT_QUOTES, 'UTF-8'); ?>" />
			<img
				class="oil-diagram__image"
				src="<?= htmlspecialchars($wastewaterTreatmentStagesImageUrl, ENT_QUOTES, 'UTF-8'); ?>"
				alt=""
				decoding="async" />
		</picture>
	</div>
</section>
<section class="wastewater-parameters">
	<h2 class="section-title"><?= $dictionary['parametersOfWastewater']; ?></h2>
	<div class="wastewater-parameters__table">
		<div class="wastewater-parameters__row wastewater-parameters__row--head">
			<div class="wastewater-parameters__cell wastewater-parameters__cell--parameter"><?= $dictionary['parameters']; ?></div>
			<div class="wastewater-parameters__cell wastewater-parameters__cell--value"><?= $dictionary['value']; ?></div>
		</div>
		<?php foreach ($parametersOfWastewater as $parameterItem): ?>
			<div class="wastewater-parameters__row">
				<div class="wastewater-parameters__cell wastewater-parameters__cell--parameter">
					<?= htmlspecialchars((string) $parameterItem['parameter'], ENT_QUOTES, 'UTF-8'); ?>
				</div>
				<div class="wastewater-parameters__cell wastewater-parameters__cell--value">
					<?= htmlspecialchars((string) $parameterItem['value'], ENT_QUOTES, 'UTF-8'); ?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</section>
<section class="wastewater-parameters">
	<h2 class="section-title"><?= $dictionary['requirementsForPurifiedWater']; ?></h2>
	<div class="wastewater-parameters__table">
		<div class="wastewater-parameters__row wastewater-parameters__row--head">
			<div class="wastewater-parameters__cell wastewater-parameters__cell--parameter"><?= $dictionary['parameters']; ?></div>
			<div class="wastewater-parameters__cell wastewater-parameters__cell--value"><?= $dictionary['value']; ?></div>
		</div>
		<?php foreach ($requirementsForPurifiedWater as $requirementItem): ?>
			<div class="wastewater-parameters__row">
				<div class="wastewater-parameters__cell wastewater-parameters__cell--parameter">
					<?= htmlspecialchars((string) $requirementItem['parameter'], ENT_QUOTES, 'UTF-8'); ?>
				</div>
				<div class="wastewater-parameters__cell wastewater-parameters__cell--value">
					<?= htmlspecialchars((string) $requirementItem['value'], ENT_QUOTES, 'UTF-8'); ?>
				</div>
			</div>
		<?php endforeach; ?>
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
	<section class="oil-content-section wastewater-case-study">
		<h2 class="section-title"><?= $dictionary['caseStudy']; ?></h2>
		<div class="wastewater-case-study__slider swiper" data-oil-certifications-slider>
			<div class="swiper-wrapper">
				<?php foreach ($caseStudy as $caseStudyItem): ?>
					<?php $caseStudyImageUrl = UPLOADS_BASE_URL . ltrim((string) $caseStudyItem['path'], '/'); ?>
					<div class="swiper-slide">
						<div class="wastewater-case-study__slide">
							<img
								class="wastewater-case-study__image"
								src="<?= htmlspecialchars($caseStudyImageUrl, ENT_QUOTES, 'UTF-8'); ?>"
								alt=""
								decoding="async" />
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="wastewater-case-study__pagination" data-oil-certifications-pagination></div>
		</div>
		<?php if ($caseStudyText !== []): ?>
			<div class="wastewater-case-study__text-list">
				<?php foreach ($caseStudyText as $caseStudyTextItem): ?>
					<div class="wastewater-case-study__text"><?= $caseStudyTextItem; ?></div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</section>
<?php endif; ?>
<?php
require TEMPLATES_PATH . '/partials/contact-form.php';
$articlesJson = fetchArticlesCollectionApi((string) ($currentLanguage ?? 'en'));
$newsItems = is_array($articlesJson) ? $articlesJson : [];
$hasNewsItems = $newsItems !== [];
?>
<div id="news"></div>
<?php if ($hasNewsItems): ?>
	<section class="news-events" style="margin-top: var(--section-spacing); margin-bottom: var(--section-spacing);">
		<h2 class="section-title"><?= $dictionary['newsEvents']; ?></h2>
		<?php require TEMPLATES_PATH . '/partials/news-list.php'; ?>
	</section>
<?php endif; ?>
<?php
