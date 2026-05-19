<?php

declare(strict_types=1);

/** @var array<string, mixed>|null $plantProtectionBeneficialMicroflora */
$plantProtectionBeneficialMicroflora = is_array($plantProtectionBeneficialMicroflora ?? null)
	? $plantProtectionBeneficialMicroflora
	: [];

$displacement = $plantProtectionBeneficialMicroflora['displacement'] ?? '';
$competition = $plantProtectionBeneficialMicroflora['competition'] ?? '';
$suppression = $plantProtectionBeneficialMicroflora['suppression'] ?? '';
$firstDay = $plantProtectionBeneficialMicroflora['firstDay'] ?? '';
$tenthDay = $plantProtectionBeneficialMicroflora['tenthDay'] ?? '';
$trichodermaHarzianum = $plantProtectionBeneficialMicroflora['trichodermaHarzianum'] ?? '';
$fusariumOxysporum = $plantProtectionBeneficialMicroflora['fusariumOxysporum'] ?? '';
?>
<div class="beneficial-microflora-scheme" role="presentation">
	<div class="beneficial-microflora-scheme__col beneficial-microflora-scheme__col--labels">
		<div class="beneficial-microflora-scheme__slot-grid">
			<div class="beneficial-microflora-scheme__label-box"><?= $displacement; ?></div>
			<div class="beneficial-microflora-scheme__label-box"><?= $competition; ?></div>
			<div class="beneficial-microflora-scheme__label-box"><?= $suppression; ?></div>
		</div>
	</div>
	<div class="beneficial-microflora-scheme__col beneficial-microflora-scheme__col--track">
		<div class="beneficial-microflora-scheme__slot-grid">
			<div class="beneficial-microflora-scheme__track-row">
				<span class="diagram-dot beneficial-microflora-scheme__track-dot" aria-hidden="true"></span>
				<span class="beneficial-microflora-scheme__track-seg" aria-hidden="true"></span>
			</div>
			<div class="beneficial-microflora-scheme__track-row">
				<span class="diagram-dot beneficial-microflora-scheme__track-dot" aria-hidden="true"></span>
				<span class="beneficial-microflora-scheme__track-seg" aria-hidden="true"></span>
			</div>
			<div class="beneficial-microflora-scheme__track-row">
				<span class="diagram-dot beneficial-microflora-scheme__track-dot" aria-hidden="true"></span>
				<span class="beneficial-microflora-scheme__track-seg" aria-hidden="true"></span>
			</div>
		</div>
	</div>
	<div class="beneficial-microflora-scheme__col beneficial-microflora-scheme__col--bridge" aria-hidden="true">
		<div class="beneficial-microflora-scheme__bridge-top">
			<div class="beneficial-microflora-scheme__bridge-corner beneficial-microflora-scheme__bridge-corner--top">
				<span class="beneficial-microflora-scheme__bridge-arm beneficial-microflora-scheme__bridge-arm--top-vertical"></span>
				<span class="beneficial-microflora-scheme__bridge-arm beneficial-microflora-scheme__bridge-arm--top-horizontal"></span>
			</div>
		</div>
		<div class="beneficial-microflora-scheme__bridge-mid">
			<span class="beneficial-microflora-scheme__bridge-mid-line"></span>
		</div>
		<div class="beneficial-microflora-scheme__bridge-bottom">
			<div class="beneficial-microflora-scheme__bridge-corner beneficial-microflora-scheme__bridge-corner--bottom">
				<span class="beneficial-microflora-scheme__bridge-arm beneficial-microflora-scheme__bridge-arm--bottom-vertical"></span>
				<span class="beneficial-microflora-scheme__bridge-arm beneficial-microflora-scheme__bridge-arm--bottom-horizontal"></span>
			</div>
		</div>
	</div>
	<div class="beneficial-microflora-scheme__col beneficial-microflora-scheme__col--day">
		<p class="beneficial-microflora-scheme__day-title"><?= $firstDay; ?></p>
		<div class="beneficial-microflora-scheme__day-figure">
			<?php
			$webpPath = '/img/plant-protection/first_day.webp';
			$fallbackPath = '/img/plant-protection/first_day.jpg';
			$alt = '';
			require TEMPLATES_PATH . '/partials/webp-image.php';
			?>
		</div>
		<div class="beneficial-microflora-scheme__day-captions">
			<span class="beneficial-microflora-scheme__day-caption beneficial-microflora-scheme__day-caption--left"><?= $trichodermaHarzianum; ?></span>
			<span class="beneficial-microflora-scheme__day-caption beneficial-microflora-scheme__day-caption--right"><?= $fusariumOxysporum; ?></span>
		</div>
	</div>
	<div class="beneficial-microflora-scheme__col beneficial-microflora-scheme__col--arrows" aria-hidden="true">
		<div class="beneficial-microflora-scheme__arrow-row beneficial-microflora-scheme__arrow-row--top">
			<span class="beneficial-microflora-scheme__arrow"></span>
		</div>
		<div class="beneficial-microflora-scheme__arrow-row beneficial-microflora-scheme__arrow-row--middle">
			<span class="beneficial-microflora-scheme__arrow"></span>
		</div>
	</div>
	<div class="beneficial-microflora-scheme__col beneficial-microflora-scheme__col--day">
		<p class="beneficial-microflora-scheme__day-title"><?= $tenthDay; ?></p>
		<div class="beneficial-microflora-scheme__day-figure">
			<?php
			$webpPath = '/img/plant-protection/tenth_day.webp';
			$fallbackPath = '/img/plant-protection/tenth_day.jpg';
			$alt = '';
			require TEMPLATES_PATH . '/partials/webp-image.php';
			?>
		</div>
		<div class="beneficial-microflora-scheme__day-captions">
			<span class="beneficial-microflora-scheme__day-caption beneficial-microflora-scheme__day-caption--left"><?= $trichodermaHarzianum; ?></span>
			<span class="beneficial-microflora-scheme__day-caption beneficial-microflora-scheme__day-caption--right"><?= $fusariumOxysporum; ?></span>
		</div>
	</div>
</div>
