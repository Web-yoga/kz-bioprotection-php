<?php

declare(strict_types=1);

$pageTitle = isset($title) ? trim((string) $title) : '';
$pageTitleBackgroundImg = isset($backgroundImg) ? trim((string) $backgroundImg) : '';
$backgroundStyle = $pageTitleBackgroundImg !== ''
	? "background-image: linear-gradient(rgba(15, 23, 42, 0.35), rgba(15, 23, 42, 0.35)), url('" . htmlspecialchars($pageTitleBackgroundImg, ENT_QUOTES, 'UTF-8') . "');"
	: '';
?>
<section class="page-title-background-gradient">
	<div class="page-title rounded-16" <?= $backgroundStyle !== '' ? ' style="' . $backgroundStyle . '"' : ''; ?>>
		<div class="page-title__inner container mx-auto px-4">
			<div class="page-title__decor" aria-hidden="true"></div>
			<?php if ($pageTitle !== ''): ?>
				<h1 class="page-title__text"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
			<?php endif; ?>
		</div>
	</div>
</section>