<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__, 2));
define('PUBLIC_PATH', BASE_PATH . '/public');
define('SRC_PATH', BASE_PATH . '/src');
define('TEMPLATES_PATH', SRC_PATH . '/templates');
define('PAGES_PATH', TEMPLATES_PATH . '/pages');

$supportedLanguages = require SRC_PATH . '/config/languages.php';
$routes = require SRC_PATH . '/config/routes.php';
require_once SRC_PATH . '/services/content-api.php';

function renderSitePage(string $slug, string $language): void
{
    global $routes;

    if (!isset($routes[$slug])) {
        http_response_code(404);
        echo '404';
        return;
    }

    $pageTemplateName = $routes[$slug];
    $currentLanguage = $language;
    $currentSlug = $slug;
    $dictionaryContent = fetchDictionaryContent($language);
    $dictionary = normalizeDictionaryMap($dictionaryContent);
    $feedbackForm = fetchFeedbackFormContent($language);
    $footerContent = fetchFooterContent($language);
    $pageTemplate = PAGES_PATH . '/' . $pageTemplateName . '.php';
    $pageContent = fetchPageContentBySlug($slug, $language);
    $seoContent = fetchSeoContentBySlug($slug, $language);
    $pageHomePayload = $slug === 'home' && is_array($pageContent) ? $pageContent : [];

    $pagePresentationBySlug = [
        'home' => [
            'backgroundImg' => '/assets/img/home/home-header.jpg',
            'endOfPageBackgroundImg' => '/img/home/home-bottom-bg.png',
            'titleSource' => static function () use ($pageHomePayload): string {
                return isset($pageHomePayload['title']) && is_string($pageHomePayload['title'])
                    ? trim($pageHomePayload['title'])
                    : '';
            },
            'subtitleSource' => static function () use ($pageHomePayload): string {
                if (!is_array($pageHomePayload)) {
                    return '';
                }

                foreach (['subtitle', 'sub_title', 'subTitle'] as $subtitleKey) {
                    if (isset($pageHomePayload[$subtitleKey]) && is_string($pageHomePayload[$subtitleKey])) {
                        return trim($pageHomePayload[$subtitleKey]);
                    }
                }

                return '';
            },
        ],
    ];

    $pagePresentation = $pagePresentationBySlug[$slug] ?? [];
    $pageTitleResolver = $pagePresentation['titleSource'] ?? null;
    $pageTitle = is_callable($pageTitleResolver)
        ? (string) $pageTitleResolver()
        : (
            is_array($pageContent) && isset($pageContent['title']) && is_string($pageContent['title'])
                ? trim($pageContent['title'])
                : (
                    is_array($pageContent) && isset($pageContent['name']) && is_string($pageContent['name'])
                        ? trim($pageContent['name'])
                        : ''
                )
        );
    $pageSubtitleResolver = $pagePresentation['subtitleSource'] ?? null;
    $pageSubtitle = is_callable($pageSubtitleResolver)
        ? (string) $pageSubtitleResolver()
        : (
            is_array($pageContent)
                ? (
                    isset($pageContent['subtitle']) && is_string($pageContent['subtitle'])
                        ? trim($pageContent['subtitle'])
                        : (
                            isset($pageContent['sub_title']) && is_string($pageContent['sub_title'])
                                ? trim($pageContent['sub_title'])
                                : (
                                    isset($pageContent['subTitle']) && is_string($pageContent['subTitle'])
                                        ? trim($pageContent['subTitle'])
                                        : ''
                                )
                        )
                )
                : ''
        );
    $backgroundImg = isset($pagePresentation['backgroundImg']) && is_string($pagePresentation['backgroundImg'])
        ? trim($pagePresentation['backgroundImg'])
        : '';
    $endOfPageBackgroundImg = isset($pagePresentation['endOfPageBackgroundImg']) && is_string($pagePresentation['endOfPageBackgroundImg'])
        ? trim($pagePresentation['endOfPageBackgroundImg'])
        : '';
    $seoTitle = is_array($seoContent) && isset($seoContent['title']) && is_string($seoContent['title'])
        ? trim($seoContent['title'])
        : '';
    $seoDescription = is_array($seoContent) && isset($seoContent['description']) && is_string($seoContent['description'])
        ? trim($seoContent['description'])
        : '';
    $seoImage = is_array($seoContent) && isset($seoContent['image']) && is_string($seoContent['image'])
        ? trim($seoContent['image'])
        : '';
    $seoType = 'website';
    $seoSiteName = 'Bioprotection';

    require TEMPLATES_PATH . '/layout.php';
}

function redirectToLanguageHome(string $language): void
{
    header('Location: /' . $language . '/', true, 301);
    exit;
}

function renderNotFound(): void
{
    http_response_code(404);
    echo '404';
}

function resolveRouteFromRequestUri(string $requestUri): ?array
{
    global $supportedLanguages;
    global $routes;

    $path = trim(parse_url($requestUri, PHP_URL_PATH) ?? '', '/');
    $query = parse_url($requestUri, PHP_URL_QUERY) ?? '';
    parse_str($query, $queryParams);

    if ($path === '') {
        return null;
    }

    $segments = array_values(array_filter(explode('/', $path), static fn ($segment) => $segment !== ''));
    $segmentsCount = count($segments);

    if ($segmentsCount < 1 || $segmentsCount > 2) {
        return null;
    }

    $language = $segments[0];
    $queryLanguage = isset($queryParams['locale']) && is_string($queryParams['locale'])
        ? trim($queryParams['locale'])
        : '';
    if ($queryLanguage !== '' && in_array($queryLanguage, $supportedLanguages, true)) {
        $language = $queryLanguage;
    }

    if (!in_array($language, $supportedLanguages, true)) {
        return null;
    }

    $slug = $segments[1] ?? 'home';

    if (!isset($routes[$slug])) {
        return null;
    }

    return [
        'language' => $language,
        'slug' => $slug,
    ];
}

function handleSiteRequest(): void
{
    $route = resolveRouteFromRequestUri($_SERVER['REQUEST_URI'] ?? '/');

    if ($route === null) {
        $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '', '/');

        if ($path === '') {
            redirectToLanguageHome('en');
        }

        renderNotFound();
        return;
    }

    renderSitePage($route['slug'], $route['language']);
}
