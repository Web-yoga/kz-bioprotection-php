<?php

declare(strict_types=1);

$backgroundImg = isset($backgroundImg) && is_string($backgroundImg) ? trim($backgroundImg) : '';
$wrapperClass = isset($wrapperClass) && is_string($wrapperClass) ? trim($wrapperClass) : '';

if ($backgroundImg === '' || $wrapperClass === '') {
	return;
}

$backgroundStyle = buildResponsiveBackgroundStyle($backgroundImg);

if ($backgroundStyle === '') {
	?>
	<div class="<?= htmlspecialchars($wrapperClass, ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true">
		<img
			class="<?= htmlspecialchars($wrapperClass, ENT_QUOTES, 'UTF-8'); ?>__image"
			src="<?= htmlspecialchars($backgroundImg, ENT_QUOTES, 'UTF-8'); ?>"
			alt="">
	</div>
	<?php
	return;
}
?>
<div
	class="<?= htmlspecialchars($wrapperClass, ENT_QUOTES, 'UTF-8'); ?> responsive-bg"
	style="<?= $backgroundStyle; ?>"
	aria-hidden="true"></div>
