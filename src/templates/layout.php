<?php

declare(strict_types=1);

function renderPartial(string $name, array $data = []): void
{
	extract($data, EXTR_SKIP);
	require TEMPLATES_PATH . '/partials/' . $name . '.php';
}

function getViteAssets(string $entry = 'resources/js/app.js'): array
{
	$manifestPath = PUBLIC_PATH . '/assets/.vite/manifest.json';

	if (!is_file($manifestPath)) {
		return [
			'css' => ['/assets/css/app.min.css'],
			'js' => ['/assets/js/app.min.js'],
		];
	}

	$manifestRaw = file_get_contents($manifestPath);
	if ($manifestRaw === false) {
		return [
			'css' => ['/assets/css/app.min.css'],
			'js' => ['/assets/js/app.min.js'],
		];
	}

	$manifest = json_decode($manifestRaw, true);
	if (!is_array($manifest) || !isset($manifest[$entry])) {
		return [
			'css' => ['/assets/css/app.min.css'],
			'js' => ['/assets/js/app.min.js'],
		];
	}

	$entryData = $manifest[$entry];
	$cssFiles = [];
	$jsFiles = [];

	if (!empty($entryData['css']) && is_array($entryData['css'])) {
		foreach ($entryData['css'] as $cssFile) {
			$cssFiles[] = '/assets/' . ltrim((string) $cssFile, '/');
		}
	}

	if (!empty($entryData['file'])) {
		$jsFiles[] = '/assets/' . ltrim((string) $entryData['file'], '/');
	}

	if ($cssFiles === []) {
		$cssFiles[] = '/assets/css/app.min.css';
	}

	if ($jsFiles === []) {
		$jsFiles[] = '/assets/js/app.min.js';
	}

	return [
		'css' => $cssFiles,
		'js' => $jsFiles,
	];
}

/**
 * Resolved JS URL for a Vite build entry, or empty string if missing from manifest (no fallback to main bundle).
 */
function getViteEntryJsUrl(string $entry): string
{
	$manifestPath = PUBLIC_PATH . '/assets/.vite/manifest.json';
	if (!is_file($manifestPath)) {
		return '';
	}

	$manifestRaw = file_get_contents($manifestPath);
	if ($manifestRaw === false) {
		return '';
	}

	$manifest = json_decode($manifestRaw, true);
	if (!is_array($manifest) || !isset($manifest[$entry]['file'])) {
		return '';
	}

	return '/assets/' . ltrim((string) $manifest[$entry]['file'], '/');
}

function getViteDevServerUrl(): ?string
{
	$viteUrl = getenv('VITE_DEV_SERVER_URL');
	if ($viteUrl === false || trim($viteUrl) === '') {
		$viteUrl = 'http://localhost:5173';
	}

	$parts = parse_url($viteUrl);
	if (!is_array($parts) || empty($parts['host'])) {
		return null;
	}

	$host = (string) $parts['host'];
	$scheme = isset($parts['scheme']) ? (string) $parts['scheme'] : 'http';
	$port = isset($parts['port']) ? (int) $parts['port'] : ($scheme === 'https' ? 443 : 5173);

	$connection = @fsockopen($host, $port, $errno, $errstr, 0.2);
	if (!is_resource($connection)) {
		return null;
	}

	fclose($connection);
	return rtrim((string) $viteUrl, '/');
}

$viteDevServerUrl = getViteDevServerUrl();
$isViteDevMode = $viteDevServerUrl !== null;
$viteAssets = $isViteDevMode ? ['css' => [], 'js' => []] : getViteAssets();
$resolvedPageTitle = isset($pageTitle) && is_string($pageTitle) && trim($pageTitle) !== ''
	? trim($pageTitle)
	: ucfirst(str_replace('-', ' ', (string) ($currentSlug ?? '')));
$resolvedPageSubtitle = isset($pageSubtitle) && is_string($pageSubtitle) ? trim($pageSubtitle) : '';
$resolvedPageTitleBackgroundImg = isset($backgroundImg) && is_string($backgroundImg) ? trim($backgroundImg) : '';
$resolvedEndOfPageBackgroundImg = isset($endOfPageBackgroundImg) && is_string($endOfPageBackgroundImg)
	? trim($endOfPageBackgroundImg)
	: '';
$resolvedMiddleOfPageBackgroundImg = isset($middleOfPageBackgroundImg) && is_string($middleOfPageBackgroundImg)
	? trim($middleOfPageBackgroundImg)
	: '';
$resolvedSeoTitle = isset($seoTitle) && is_string($seoTitle) && trim($seoTitle) !== ''
	? trim($seoTitle)
	: $resolvedPageTitle;
$resolvedSeoDescription = isset($seoDescription) && is_string($seoDescription)
	? trim($seoDescription)
	: '';
$resolvedSeoType = isset($seoType) && is_string($seoType) && trim($seoType) !== ''
	? trim($seoType)
	: 'website';
$resolvedSeoSiteName = isset($seoSiteName) && is_string($seoSiteName) && trim($seoSiteName) !== ''
	? trim($seoSiteName)
	: 'Bioprotection';
$seoImagePath = isset($seoImage) && is_string($seoImage) ? trim($seoImage) : '';
$currentScheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$currentHost = isset($_SERVER['HTTP_HOST']) ? trim((string) $_SERVER['HTTP_HOST']) : '';
$baseSiteUrl = $currentHost !== '' ? $currentScheme . '://' . $currentHost : '';
$pageRelativeUrl = '/' . trim((string) ($currentLanguage ?? 'en'), '/')
	. (($currentSlug ?? 'home') === 'home' ? '/' : '/' . trim((string) $currentSlug, '/'));
$canonicalUrl = $baseSiteUrl !== '' ? $baseSiteUrl . $pageRelativeUrl : $pageRelativeUrl;
$seoImageUrl = '';
if ($seoImagePath !== '') {
	$seoImageUrl = preg_match('#^https?://#i', $seoImagePath) === 1
		? $seoImagePath
		: ($baseSiteUrl !== '' ? $baseSiteUrl . '/' . ltrim($seoImagePath, '/') : $seoImagePath);
}
$ogLocaleMap = [
	'ru' => 'ru_RU',
	'en' => 'en_US',
	'kz' => 'kk_KZ',
];
$resolvedOgLocale = $ogLocaleMap[(string) ($currentLanguage ?? 'en')] ?? 'en_US';
?>
<!doctype html>
<html lang="<?= htmlspecialchars($currentLanguage, ENT_QUOTES, 'UTF-8'); ?>">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= htmlspecialchars($resolvedSeoTitle !== '' ? $resolvedSeoTitle : 'PHP Skeleton', ENT_QUOTES, 'UTF-8'); ?></title>
	<?php if ($resolvedSeoDescription !== ''): ?>
		<meta name="description" content="<?= htmlspecialchars($resolvedSeoDescription, ENT_QUOTES, 'UTF-8'); ?>">
	<?php endif; ?>
	<link rel="canonical" href="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
	<meta property="og:type" content="<?= htmlspecialchars($resolvedSeoType, ENT_QUOTES, 'UTF-8'); ?>">
	<meta property="og:site_name" content="<?= htmlspecialchars($resolvedSeoSiteName, ENT_QUOTES, 'UTF-8'); ?>">
	<meta property="og:title" content="<?= htmlspecialchars($resolvedSeoTitle, ENT_QUOTES, 'UTF-8'); ?>">
	<?php if ($resolvedSeoDescription !== ''): ?>
		<meta property="og:description" content="<?= htmlspecialchars($resolvedSeoDescription, ENT_QUOTES, 'UTF-8'); ?>">
	<?php endif; ?>
	<meta property="og:url" content="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
	<meta property="og:locale" content="<?= htmlspecialchars($resolvedOgLocale, ENT_QUOTES, 'UTF-8'); ?>">
	<?php if ($seoImageUrl !== ''): ?>
		<meta property="og:image" content="<?= htmlspecialchars($seoImageUrl, ENT_QUOTES, 'UTF-8'); ?>">
	<?php endif; ?>
	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="<?= htmlspecialchars($resolvedSeoTitle, ENT_QUOTES, 'UTF-8'); ?>">
	<?php if ($resolvedSeoDescription !== ''): ?>
		<meta name="twitter:description" content="<?= htmlspecialchars($resolvedSeoDescription, ENT_QUOTES, 'UTF-8'); ?>">
	<?php endif; ?>
	<?php if ($seoImageUrl !== ''): ?>
		<meta name="twitter:image" content="<?= htmlspecialchars($seoImageUrl, ENT_QUOTES, 'UTF-8'); ?>">
	<?php endif; ?>
	<?php if ($isViteDevMode): ?>
		<script type="module" src="<?= htmlspecialchars($viteDevServerUrl, ENT_QUOTES, 'UTF-8'); ?>/@vite/client"></script>
		<script type="module" src="<?= htmlspecialchars($viteDevServerUrl, ENT_QUOTES, 'UTF-8'); ?>/resources/js/app.js"></script>
	<?php else: ?>
		<?php foreach ($viteAssets['css'] as $cssPath): ?>
			<link rel="stylesheet" href="<?= htmlspecialchars($cssPath, ENT_QUOTES, 'UTF-8'); ?>">
		<?php endforeach; ?>
	<?php endif; ?>
</head>

<body>
	<header class="site-header">
		<div class="content-frame">
			<div class="content-frame__bleed content-frame__bleed--left" aria-hidden="true"></div>
			<div class="content-frame__main container mx-auto px-4">
				<?php renderPartial('header', ['currentLanguage' => $currentLanguage, 'currentSlug' => $currentSlug, 'dictionary' => $dictionary]); ?>
			</div>
			<div class="content-frame__bleed content-frame__bleed--right" aria-hidden="true"></div>
		</div>
	</header>
	<?php renderPartial('page-title', ['title' => $resolvedPageTitle, 'subtitle' => $resolvedPageSubtitle, 'backgroundImg' => $resolvedPageTitleBackgroundImg]); ?>
	<div class="end-of-page-zone">
		<?php if ($resolvedMiddleOfPageBackgroundImg !== ''): ?>
			<div class="page-middle-bg" aria-hidden="true">
				<img
					class="page-middle-bg__image"
					src="<?= htmlspecialchars($resolvedMiddleOfPageBackgroundImg, ENT_QUOTES, 'UTF-8'); ?>"
					alt="">
			</div>
		<?php endif; ?>
		<?php if ($resolvedEndOfPageBackgroundImg !== ''): ?>
			<div class="page-bottom-bg" aria-hidden="true">
				<img
					class="page-bottom-bg__image"
					src="<?= htmlspecialchars($resolvedEndOfPageBackgroundImg, ENT_QUOTES, 'UTF-8'); ?>"
					alt="">
			</div>
		<?php endif; ?>
		<div class="content-frame">
			<div class="content-frame__bleed content-frame__bleed--left" aria-hidden="true"></div>
			<div class="content-frame__main container mx-auto px-4">
				<?php require $pageTemplate; ?>
			</div>
			<div class="content-frame__bleed content-frame__bleed--right" aria-hidden="true"></div>
		</div>
		<?php renderPartial('footer', ['footerContent' => $footerContent, 'currentLanguage' => $currentLanguage, 'dictionary' => $dictionary]); ?>
	</div>
	<?php if (!$isViteDevMode): ?>
		<?php foreach ($viteAssets['js'] as $jsPath): ?>
			<script type="module" src="<?= htmlspecialchars($jsPath, ENT_QUOTES, 'UTF-8'); ?>"></script>
		<?php endforeach; ?>
	<?php endif; ?>
</body>

</html>