<?php

declare(strict_types=1);

$pageTitle = isset($title) ? (string) $title : '';
$pageSubtitle = isset($subtitle) ? (string) $subtitle : '';
$pageTitleBackgroundImg = isset($backgroundImg) ? trim((string) $backgroundImg) : '';
$backgroundStyle = $pageTitleBackgroundImg !== ''
	? "background-image: url('" . htmlspecialchars($pageTitleBackgroundImg, ENT_QUOTES, 'UTF-8') . "');"
	: '';
$pageTitlePlainText = trim(str_replace('&nbsp;', ' ', strip_tags($pageTitle)));
$pageSubtitlePlainText = trim(str_replace('&nbsp;', ' ', strip_tags($pageSubtitle)));
?>
<section class="page-title-background-gradient">
	<div class="page-title rounded-16" <?= $backgroundStyle !== '' ? ' style="' . $backgroundStyle . '"' : ''; ?>>
		<div class="page-title__overlay">
			<div class="page-title__inner container mx-auto px-4">
				<div class="page-title__content">
					<?php if ($pageTitlePlainText !== ''): ?>
						<h1 class="page-title__text"><?= $pageTitle; ?></h1>
					<?php endif; ?>
					<?php if ($pageSubtitlePlainText !== ''): ?>
						<div class="page-title__subtitle"><?= $pageSubtitle; ?></div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>