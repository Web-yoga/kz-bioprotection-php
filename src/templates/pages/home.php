<?php

declare(strict_types=1);

$pageHomePayload = isset($pageContent) && is_array($pageContent) ? $pageContent : [];
$topText = isset($pageHomePayload['topText']) ? trim((string) $pageHomePayload['topText']) : '';
$benefits = $pageHomePayload['benefits'] ?? [];
$langPath = '/' . trim((string) ($currentLanguage ?? 'en'), '/') . '/';
$oilCleaningUrl = $langPath . 'oil-cleaning';
$wastewaterTreatmentUrl = $langPath . 'wastewater-treatment';
$soilBtnDescription = trim((string) ($pageHomePayload['soilBtnDescription'] ?? ''));
$soilBtnText = trim((string) ($pageHomePayload['soilBtnText'] ?? ''));
$wastewaterBtnDescription = trim((string) ($pageHomePayload['wastewaterBtnDescription'] ?? ''));
$wastewaterBtnText = trim((string) ($pageHomePayload['wastewaterBtnText'] ?? ''));
$ourCustomers = isset($pageHomePayload['ourCustomers']) && is_array($pageHomePayload['ourCustomers'])
	? $pageHomePayload['ourCustomers']
	: [];
?>
<?php if ($topText !== ''): ?>
	<section class="home-top-text">
		<div class="home-top-text__content"><?= $topText; ?></div>
	</section>
<?php endif; ?>
<section class="key-benefits">
	<h2 class="section-title"><?= $dictionary['keyBenefits']; ?></h2>
	<div class="key-benefits__layout">
		<div class="key-benefits__media img-shadow">
			<div class="key-benefits__media-bg" aria-hidden="true"></div>
		</div>
		<ul class="key-benefits__list">
			<?php foreach ($benefits as $benefitHtml): ?>
				<li class="key-benefits__item">
					<img
						class="key-benefits__check"
						src="/img/check-mark.svg"
						alt=""
						width="21"
						height="21"
						decoding="async"
					/>
					<span class="key-benefits__text"><?= $benefitHtml; ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
<section class="home-solutions">
	<h2 class="section-title"><?= $dictionary['solutions']; ?></h2>
	<div class="home-solutions__grid">
		<div class="home-solutions__card">
			<div class="home-solutions__figure">
				<img
					class="home-solutions__img"
					src="/img/home/home-soil-btn.jpg"
					alt=""
					width="800"
					height="600"
					decoding="async"
				/>
			</div>
			<div class="home-solutions__copy">
				<h3 class="home-solutions__lead"><?= htmlspecialchars($soilBtnDescription, ENT_QUOTES, 'UTF-8'); ?></h3>
				<a class="home-solutions__cta" href="<?= htmlspecialchars($oilCleaningUrl, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($soilBtnText, ENT_QUOTES, 'UTF-8'); ?></a>
			</div>
		</div>
		<div class="home-solutions__card">
			<div class="home-solutions__figure">
				<img
					class="home-solutions__img"
					src="/img/home/home-wastewater-btn.jpg"
					alt=""
					width="800"
					height="600"
					decoding="async"
				/>
			</div>
			<div class="home-solutions__copy">
				<h3 class="home-solutions__lead"><?= htmlspecialchars($wastewaterBtnDescription, ENT_QUOTES, 'UTF-8'); ?></h3>
				<a class="home-solutions__cta" href="<?= htmlspecialchars($wastewaterTreatmentUrl, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($wastewaterBtnText, ENT_QUOTES, 'UTF-8'); ?></a>
			</div>
		</div>
	</div>
</section>
<?php require TEMPLATES_PATH . '/partials/contact-form.php'; ?>
<?php
$articlesJson = fetchArticlesCollection((string) ($currentLanguage ?? 'en'));
?>
<section id="news" class="news-events" style="margin-top: var(--section-spacing);">
	<h2 class="section-title"><?= $dictionary['newsEvents']; ?></h2>
	<?php require TEMPLATES_PATH . '/partials/news-list.php'; ?>
</section>
<?php if ($ourCustomers !== []): ?>
	<section class="our-customers" style="margin-top: var(--section-spacing);">
		<h2 class="section-title"><?= $dictionary['ourCustomers']; ?></h2>
		<div class="our-customers__slider swiper" data-our-customers-slider>
			<div class="swiper-wrapper">
				<?php foreach ($ourCustomers as $customer): ?>
				<?php
				$customer = (array) $customer;
				$customerImagePath = trim((string) ($customer['image']['path'] ?? ''));
				$customerImageUrl = $customerImagePath !== '' ? UPLOADS_BASE_URL . $customerImagePath : '';
				if ($customerImageUrl === '') {
					continue;
				}
				$customerName = trim((string) ($customer['name'] ?? ''));
				?>
					<div class="swiper-slide">
						<article class="our-customers__item">
							<div class="our-customers__image-wrap">
								<img
									class="our-customers__image"
									src="<?= htmlspecialchars($customerImageUrl, ENT_QUOTES, 'UTF-8'); ?>"
									alt="<?= htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8'); ?>"
									decoding="async" />
							</div>
							<?php if ($customerName !== ''): ?>
								<p class="our-customers__name"><?= htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8'); ?></p>
							<?php endif; ?>
						</article>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>
