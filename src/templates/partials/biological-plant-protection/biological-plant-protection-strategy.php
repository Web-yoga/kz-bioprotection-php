<?php

declare(strict_types=1);

$plantProtectionStrategy = $plantProtectionStrategy ?? null;

/** @var list<array{title: string, cellText: list<string>}> $plantProtectionStrategy */

$strategyColumnIconUrls = [
	'/img/icons/icon_strategy_preventive.svg',
	'/img/icons/icon_strategy_calendar.svg',
	'/img/icons/icon_strategy_chemical.svg',
	'/img/icons/icon_strategy_outcome.svg',
];

$strategyRowIconUrls = [
	'/img/icons/icon_strategy_biofungicid.svg',
	'/img/icons/icon_strategy_bioinsecticide.svg',
];
?>
<div class="plant-protection-strategy-table">
	<div class="plant-protection-strategy-table__labels">
		<div class="plant-protection-strategy-table__corner" aria-hidden="true"></div>
		<div class="plant-protection-strategy-table__row-head plant-protection-strategy-table__row-head--0">
			<div class="plant-protection-strategy-table__icon-slot">
				<div class="plant-protection-strategy-table__icon-badge">
					<img
						src="<?= htmlspecialchars($strategyRowIconUrls[0], ENT_QUOTES, 'UTF-8'); ?>"
						alt=""
						width="97"
						height="97"
						decoding="async"
						loading="lazy" />
				</div>
			</div>
			<p class="plant-protection-strategy-table__row-title"><?= $plantProtectionStrategy[0]['title']; ?></p>
		</div>
		<div class="plant-protection-strategy-table__row-head plant-protection-strategy-table__row-head--1">
			<div class="plant-protection-strategy-table__icon-slot">
				<div class="plant-protection-strategy-table__icon-badge">
					<img
						src="<?= htmlspecialchars($strategyRowIconUrls[1], ENT_QUOTES, 'UTF-8'); ?>"
						alt=""
						width="97"
						height="97"
						decoding="async"
						loading="lazy" />
				</div>
			</div>
			<p class="plant-protection-strategy-table__row-title"><?= $plantProtectionStrategy[1]['title']; ?></p>
		</div>
	</div>
	<div class="plant-protection-strategy-table__data">
		<div class="plant-protection-strategy-table__col-headers" role="presentation">
			<?php foreach ($strategyColumnIconUrls as $columnIconUrl): ?>
				<div class="plant-protection-strategy-table__col-head">
					<div class="plant-protection-strategy-table__icon-slot">
						<div class="plant-protection-strategy-table__icon-badge">
							<img
								src="<?= htmlspecialchars($columnIconUrl, ENT_QUOTES, 'UTF-8'); ?>"
								alt=""
								width="97"
								height="97"
								decoding="async"
								loading="lazy" />
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<div class="plant-protection-strategy-table__panel-row plant-protection-strategy-table__panel-row--light">
			<?php foreach ($plantProtectionStrategy[0]['cellText'] as $cellIndex => $cellHtml): ?>
				<div class="plant-protection-strategy-table__cell">
					<div class="plant-protection-strategy-table__cell-col-icon" aria-hidden="true">
						<div class="plant-protection-strategy-table__icon-slot">
							<div class="plant-protection-strategy-table__icon-badge">
								<img
									src="<?= htmlspecialchars($strategyColumnIconUrls[$cellIndex], ENT_QUOTES, 'UTF-8'); ?>"
									alt=""
									width="97"
									height="97"
									decoding="async"
									loading="lazy" />
							</div>
						</div>
					</div>
					<div class="plant-protection-strategy-table__cell-text"><?= $cellHtml; ?></div>
					<span class="diagram-dot plant-protection-strategy-table__cell-node" aria-hidden="true"></span>
				</div>
			<?php endforeach; ?>
			<div class="plant-protection-strategy-table__connector" aria-hidden="true"></div>
		</div>
		<div class="plant-protection-strategy-table__panel-row plant-protection-strategy-table__panel-row--dark">
			<?php foreach ($plantProtectionStrategy[1]['cellText'] as $cellIndex => $cellHtml): ?>
				<div class="plant-protection-strategy-table__cell">
					<div class="plant-protection-strategy-table__cell-col-icon" aria-hidden="true">
						<div class="plant-protection-strategy-table__icon-slot">
							<div class="plant-protection-strategy-table__icon-badge">
								<img
									src="<?= htmlspecialchars($strategyColumnIconUrls[$cellIndex], ENT_QUOTES, 'UTF-8'); ?>"
									alt=""
									width="97"
									height="97"
									decoding="async"
									loading="lazy" />
							</div>
						</div>
					</div>
					<div class="plant-protection-strategy-table__cell-text"><?= $cellHtml; ?></div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
