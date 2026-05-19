<?php

declare(strict_types=1);

/** @var list<array{title: string, text: string}> $keyBenefitsItems */
/** @var bool $keyBenefitsUseDash */
/** @var string $keyBenefitsImageBase path without extension, e.g. /img/plant-protection/key_biofungicides */

$keyBenefitsFigureJpg = $keyBenefitsImageBase . '.jpg';
$keyBenefitsFigureWebp = $keyBenefitsImageBase . '.webp';
$keyBenefitsFigureStyle = '--plant-protection-key-benefits-bg-jpg: url(\''
	. htmlspecialchars($keyBenefitsFigureJpg, ENT_QUOTES, 'UTF-8')
	. '\');--plant-protection-key-benefits-bg-webp: url(\''
	. htmlspecialchars($keyBenefitsFigureWebp, ENT_QUOTES, 'UTF-8')
	. '\');';
?>
<div class="plant-protection-key-benefits__layout">
	<ul class="plant-protection-key-benefits__list">
		<?php foreach ($keyBenefitsItems as $keyBenefitItem): ?>
			<li class="plant-protection-key-benefits__item">
				<p class="plant-protection-key-benefits__line">
					<strong class="plant-protection-key-benefits__title"><?= $keyBenefitItem['title']; ?></strong><?php if ($keyBenefitsUseDash): ?> - <?php endif; ?><span class="plant-protection-key-benefits__text"><?= $keyBenefitItem['text']; ?></span>
				</p>
			</li>
		<?php endforeach; ?>
	</ul>
	<div
		class="plant-protection-key-benefits__figure plant-protection-key-benefits__figure--with-bg"
		style="<?= $keyBenefitsFigureStyle; ?>"
		aria-hidden="true"></div>
</div>
