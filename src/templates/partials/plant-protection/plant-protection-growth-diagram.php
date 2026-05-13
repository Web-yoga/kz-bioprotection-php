<?php

declare(strict_types=1);

$diagramGrowthPictureUrls = isset($diagramGrowthPictureUrls) && is_array($diagramGrowthPictureUrls)
	? array_values(
		array_filter(
			$diagramGrowthPictureUrls,
			static fn($url): bool => is_string($url) && trim($url) !== ''
		)
	)
	: [];

if ($diagramGrowthPictureUrls === []) {
	return;
}

$diagramGrowthStepCount = count($diagramGrowthPictureUrls);
$diagramGrowthLastIndex = $diagramGrowthStepCount - 1;
$diagramNAttr = htmlspecialchars((string) $diagramGrowthStepCount, ENT_QUOTES, 'UTF-8');
?>
<div class="plant-protection-growth-diagram">
	<div class="diagram-growth" style="--diagram-n: <?= $diagramNAttr; ?>">
		<?php foreach ($diagramGrowthPictureUrls as $stepIndex => $imageUrl): ?>
			<div class="diagram-growth__step">
				<div class="diagram-growth__figure">
					<img
						src="<?= htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8'); ?>"
						alt=""
						decoding="async"
						loading="lazy" />
				</div>
				<div class="diagram-growth__timeline">
					<span
						class="diagram-growth__seg diagram-growth__seg--left<?= $stepIndex === 0 ? ' diagram-growth__seg--blank' : ''; ?>"
						aria-hidden="true"></span>
					<span
						class="diagram-growth__seg diagram-growth__seg--right<?= $stepIndex === $diagramGrowthLastIndex ? ' diagram-growth__seg--blank' : ''; ?>"
						aria-hidden="true"></span>
					<span class="diagram-dot diagram-growth__dot" aria-hidden="true"></span>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
