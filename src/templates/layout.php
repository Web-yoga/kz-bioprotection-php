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
?>
<!doctype html>
<html lang="<?= htmlspecialchars($currentLanguage, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Skeleton</title>
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
<?php renderPartial('header', ['currentLanguage' => $currentLanguage, 'currentSlug' => $currentSlug]); ?>
<div class="content-frame">
    <div class="content-frame__bleed content-frame__bleed--left" aria-hidden="true"></div>
    <div class="content-frame__main">
        <?php require $pageTemplate; ?>
        <?php renderPartial('footer'); ?>
    </div>
    <div class="content-frame__bleed content-frame__bleed--right" aria-hidden="true"></div>
</div>
<?php if (!$isViteDevMode): ?>
<?php foreach ($viteAssets['js'] as $jsPath): ?>
    <script src="<?= htmlspecialchars($jsPath, ENT_QUOTES, 'UTF-8'); ?>" defer></script>
<?php endforeach; ?>
<?php endif; ?>
</body>
</html>
