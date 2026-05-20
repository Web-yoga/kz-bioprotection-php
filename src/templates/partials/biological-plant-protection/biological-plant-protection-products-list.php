<?php

declare(strict_types=1);

/**
 * Product cards grid for biological plant protection pages.
 *
 * @var list<array<string, mixed>> $data
 */
$data = isset($data) && is_array($data) ? $data : [];

$validProductRows = [];
foreach ($data as $productRow) {
	$productRow = is_array($productRow) ? $productRow : [];
	$imageUrl = uploadsPublicUrlFromPathField($productRow['image'] ?? null);
	$cardTitle = trim((string) ($productRow['title'] ?? ''));
	$subtitle = trim((string) ($productRow['subtitle'] ?? ''));
	$italics = trim((string) ($productRow['italics'] ?? ''));
	$description = trim((string) ($productRow['description'] ?? ''));
	$hasCopy = $subtitle !== '' || $italics !== '' || $description !== '';
	if ($imageUrl === '' && $cardTitle === '' && !$hasCopy) {
		continue;
	}
	$validProductRows[] = $productRow;
}

$bppVisibleCount = count($validProductRows);
// Desktop: one row shows all items when there are 3 or 4; cap at 4 columns so 5+ wrap (4 + rest).
$bppDesktopCols = $bppVisibleCount === 0 ? 1 : min($bppVisibleCount, 4);
$listStyle = '--bpp-desktop-cols: ' . (string) $bppDesktopCols . ';';
?>
<div class="biological-plant-protection-products-list" style="<?= htmlspecialchars($listStyle, ENT_QUOTES, 'UTF-8'); ?>">
	<?php foreach ($validProductRows as $productRow): ?>
		<?php
		$imageUrl = uploadsPublicUrlFromPathField($productRow['image'] ?? null);
		$cardTitle = trim((string) ($productRow['title'] ?? ''));
		$subtitle = trim((string) ($productRow['subtitle'] ?? ''));
		$italics = trim((string) ($productRow['italics'] ?? ''));
		$description = trim((string) ($productRow['description'] ?? ''));
		$hasCopy = $subtitle !== '' || $italics !== '' || $description !== '';
		$mediaStyle = $imageUrl !== ''
			? 'background-image:url(\'' . htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8') . '\');'
			: '';
		?>
		<article class="biological-plant-protection-products-list__card">
			<div
				class="biological-plant-protection-products-list__media<?= $imageUrl === '' ? ' biological-plant-protection-products-list__media--empty' : ''; ?>"
				<?php if ($mediaStyle !== ''): ?>
					style="<?= $mediaStyle; ?>"
				<?php endif; ?>
				aria-hidden="true"></div>
			<?php if ($cardTitle !== ''): ?>
				<h3 class="biological-plant-protection-products-list__title"><?= $productRow['title']; ?></h3>
			<?php endif; ?>
			<?php if ($hasCopy): ?>
				<div class="biological-plant-protection-products-list__copy">
					<?php if ($subtitle !== ''): ?>
						<p class="biological-plant-protection-products-list__subtitle"><?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8'); ?></p>
					<?php endif; ?>
					<?php if ($italics !== ''): ?>
						<p class="biological-plant-protection-products-list__italics"><?= htmlspecialchars($italics, ENT_QUOTES, 'UTF-8'); ?></p>
					<?php endif; ?>
					<?php if ($description !== ''): ?>
						<p class="biological-plant-protection-products-list__description"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</article>
	<?php endforeach; ?>
</div>
