<?php

declare(strict_types=1);

$pageTitle = isset($title) ? (string) $title : '';
$pageSubtitle = isset($subtitle) ? (string) $subtitle : '';
$pageTitleBackgroundImg = isset($backgroundImg) ? trim((string) $backgroundImg) : '';
$buildPageTitleBackgroundPath = static function (string $sourcePath, string $sizeSuffix, string $extension): string {
	if ($sourcePath === '') {
		return '';
	}

	$pathInfo = pathinfo($sourcePath);
	$directory = isset($pathInfo['dirname']) && $pathInfo['dirname'] !== '.' ? (string) $pathInfo['dirname'] : '';
	$filename = isset($pathInfo['filename']) ? (string) $pathInfo['filename'] : '';
	if ($filename === '') {
		return '';
	}

	$normalizedFilename = preg_replace('/-(1280|1920)$/', '', $filename);
	if (!is_string($normalizedFilename) || $normalizedFilename === '') {
		return '';
	}

	$prefix = $directory !== '' ? rtrim($directory, '/') . '/' : '';
	return $prefix . $normalizedFilename . '-' . $sizeSuffix . '.' . $extension;
};
$pageTitleMobileJpg = $buildPageTitleBackgroundPath($pageTitleBackgroundImg, '1280', 'jpg');
$pageTitleDesktopJpg = $buildPageTitleBackgroundPath($pageTitleBackgroundImg, '1920', 'jpg');
$pageTitleMobileWebp = $buildPageTitleBackgroundPath($pageTitleBackgroundImg, '1280', 'webp');
$pageTitleDesktopWebp = $buildPageTitleBackgroundPath($pageTitleBackgroundImg, '1920', 'webp');

$backgroundStyle = '';
if ($pageTitleMobileJpg !== '' && $pageTitleDesktopJpg !== '' && $pageTitleMobileWebp !== '' && $pageTitleDesktopWebp !== '') {
	$backgroundStyle = '--page-title-bg-mobile-jpg: url(\'' . htmlspecialchars($pageTitleMobileJpg, ENT_QUOTES, 'UTF-8') . '\');'
		. '--page-title-bg-desktop-jpg: url(\'' . htmlspecialchars($pageTitleDesktopJpg, ENT_QUOTES, 'UTF-8') . '\');'
		. '--page-title-bg-mobile-webp: url(\'' . htmlspecialchars($pageTitleMobileWebp, ENT_QUOTES, 'UTF-8') . '\');'
		. '--page-title-bg-desktop-webp: url(\'' . htmlspecialchars($pageTitleDesktopWebp, ENT_QUOTES, 'UTF-8') . '\');';
}
$pageTitlePlainText = trim(str_replace('&nbsp;', ' ', strip_tags($pageTitle)));
$pageSubtitlePlainText = trim(str_replace('&nbsp;', ' ', strip_tags($pageSubtitle)));
?>
<section class="page-title-background-gradient">
	<div class="page-title rounded-16<?= $backgroundStyle !== '' ? ' page-title--with-bg' : ''; ?>"<?= $backgroundStyle !== '' ? ' style="' . $backgroundStyle . '"' : ''; ?>>
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