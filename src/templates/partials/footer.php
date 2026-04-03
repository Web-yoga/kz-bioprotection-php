<?php

declare(strict_types=1);

$lang = isset($currentLanguage) ? (string) $currentLanguage : 'en';
$homeLabel = isset($dictionary['home']) && is_string($dictionary['home']) ? $dictionary['home'] : 'Home';
$soilWaterCleanupLabel = isset($dictionary['menuSoilWaterCleanup']) && is_string($dictionary['menuSoilWaterCleanup'])
	? $dictionary['menuSoilWaterCleanup']
	: 'Soil and Water Cleanup';
$wastewaterTreatmentLabel = isset($dictionary['menuWastewaterTreatment']) && is_string($dictionary['menuWastewaterTreatment'])
	? $dictionary['menuWastewaterTreatment']
	: 'Wastewater Treatment';

$bottomMenu = [
	[
		'link' => '/' . $lang . '/',
		'name' => $homeLabel,
	],
	[
		'link' => '/' . $lang . '/oil-cleaning',
		'name' => $soilWaterCleanupLabel,
	],
	[
		'link' => '/' . $lang . '/wastewater-treatment',
		'name' => $wastewaterTreatmentLabel,
	],
];

$contactsHtml = '';
if (isset($footerContent) && is_array($footerContent) && isset($footerContent['contacts']) && is_string($footerContent['contacts'])) {
	$contactsHtml = trim($footerContent['contacts']);
}

?>
<footer class="site-footer">
	<div class="site-footer__inner container mx-auto px-4">
		<nav class="site-footer__menu w-full max-w-full md:max-w-1/3" aria-label="Footer menu">
			<ul class="site-footer__menu-list">
				<?php foreach ($bottomMenu as $item): ?>
					<?php
					if (!is_array($item)) {
						continue;
					}
					$link = isset($item['link']) && is_string($item['link']) ? trim($item['link']) : '';
					$name = isset($item['name']) && is_string($item['name']) ? trim($item['name']) : '';
					if ($link === '' || $name === '') {
						continue;
					}
					?>
					<li class="site-footer__menu-item">
						<a class="site-footer__menu-link" href="<?= htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>">
							<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
		<div class="site-footer__contacts">
			<?= $contactsHtml; ?>
		</div>
	</div>
</footer>