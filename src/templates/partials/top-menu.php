<?php

declare(strict_types=1);

global $supportedLanguages;
$dictionary = isset($dictionary) && is_array($dictionary) ? $dictionary : [];

$lang = isset($currentLanguage) ? (string) $currentLanguage : 'en';
$slug = isset($currentSlug) ? (string) $currentSlug : 'home';

$languageLabels = [
	'ru' => 'RU',
	'en' => 'EN',
	'kz' => 'KZ',
];

$buildLanguageUrl = static function (string $targetLanguage, string $currentSlug): string {
	$basePath = $currentSlug === 'home'
		? '/' . $targetLanguage . '/'
		: '/' . $targetLanguage . '/' . $currentSlug;

	return $basePath;
};

$homeUrl = '/' . $lang . '/';
$oilCleaningUrl = '/' . $lang . '/oil-cleaning';
$wastewaterTreatmentUrl = '/' . $lang . '/wastewater-treatment';
$currentPageUrl = $slug === 'home'
	? '/' . $lang . '/'
	: '/' . $lang . '/' . $slug;
$contactUrl = $currentPageUrl . '#contact';
$newsUrl = $currentPageUrl . '#news';
?>
<nav
	class="site-top-menu flex h-[100px] flex-row flex-nowrap items-center justify-end gap-6"
	aria-label="Primary navigation">
	<ul class="site-top-menu__list m-0 flex list-none flex-row flex-nowrap items-center gap-6 p-0">
		<li class="site-top-menu__item">
			<a class="site-top-menu__link" href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8'); ?>">
				<?= $dictionary['home']; ?>
			</a>
		</li>
		<li class="site-top-menu__item site-top-menu__item--dropdown" data-dropdown>
			<button
				class="site-top-menu__trigger"
				type="button"
				aria-haspopup="true"
				aria-expanded="false"
				aria-controls="site-top-menu-solutions-dropdown"
				data-dropdown-trigger>
				<?= $dictionary['solutions']; ?>
			</button>
			<ul
				id="site-top-menu-solutions-dropdown"
				class="site-top-menu__dropdown"
				hidden
				data-dropdown-menu>
				<li class="site-top-menu__dropdown-item">
					<a class="site-top-menu__dropdown-link" href="<?= htmlspecialchars($oilCleaningUrl, ENT_QUOTES, 'UTF-8'); ?>">
						<?= $dictionary['menuSoilWaterCleanup']; ?>
					</a>
				</li>
				<li class="site-top-menu__dropdown-item">
					<a class="site-top-menu__dropdown-link" href="<?= htmlspecialchars($wastewaterTreatmentUrl, ENT_QUOTES, 'UTF-8'); ?>">
						<?= $dictionary['menuWastewaterTreatment']; ?>
					</a>
				</li>
			</ul>
		</li>
		<li class="site-top-menu__item">
			<a class="site-top-menu__link" href="<?= htmlspecialchars($contactUrl, ENT_QUOTES, 'UTF-8'); ?>">
				<?= $dictionary['contact']; ?>
			</a>
		</li>
		<li class="site-top-menu__item">
			<a class="site-top-menu__link" href="<?= htmlspecialchars($newsUrl, ENT_QUOTES, 'UTF-8'); ?>">
				<?= $dictionary['newsEvents']; ?>
			</a>
		</li>
	</ul>

	<select
		id="language-switcher"
		name="language"
		onchange="window.location.href=this.value"
		class="site-top-menu__language"
		aria-label="Language">
		<?php foreach ($supportedLanguages as $languageCode): ?>
			<?php
			$isSelected = $languageCode === $lang;
			$targetUrl = $buildLanguageUrl($languageCode, $slug);
			$languageLabel = $languageLabels[$languageCode] ?? strtoupper($languageCode);
			?>
			<option value="<?= htmlspecialchars($targetUrl, ENT_QUOTES, 'UTF-8'); ?>" <?= $isSelected ? 'selected' : ''; ?>>
				<?= htmlspecialchars($languageLabel, ENT_QUOTES, 'UTF-8'); ?>
			</option>
		<?php endforeach; ?>
	</select>
</nav>