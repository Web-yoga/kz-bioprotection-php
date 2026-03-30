<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/utils/i18n.php';

global $supportedLanguages;

$lang = isset($currentLanguage) ? (string) $currentLanguage : 'en';
$slug = isset($currentSlug) ? (string) $currentSlug : 'home';

$languageLabels = [
    'ru' => 'RU',
    'en' => 'EN',
    'kz' => 'KZ',
];

$buildLanguageUrl = static function (string $targetLanguage, string $currentSlug): string {
    $basePath = $currentSlug === 'home'
        ? '/' . $targetLanguage . '/'
        : '/' . $targetLanguage . '/' . $currentSlug;

    return $basePath . '?locale=' . rawurlencode($targetLanguage);
};

$homeUrl = '/' . $lang . '/';
$solutionsUrl = '/' . $lang . '/oil-cleaning';
$contactUrl = '/' . $lang . '/#contact';
$newsUrl = '/' . $lang . '/#news';
?>
<nav
    class="site-top-menu flex h-[100px] flex-row flex-nowrap items-center justify-end gap-6"
    aria-label="Primary navigation"
>
    <ul class="site-top-menu__list m-0 flex list-none flex-row flex-nowrap items-center gap-6 p-0">
        <li class="site-top-menu__item">
            <a class="site-top-menu__link" href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8'); ?>">
                <?= htmlspecialchars(i18n('menu.home', $lang), ENT_QUOTES, 'UTF-8'); ?>
            </a>
        </li>
        <li class="site-top-menu__item">
            <a class="site-top-menu__link" href="<?= htmlspecialchars($solutionsUrl, ENT_QUOTES, 'UTF-8'); ?>">
                <?= htmlspecialchars(i18n('menu.solutions', $lang), ENT_QUOTES, 'UTF-8'); ?>
            </a>
        </li>
        <li class="site-top-menu__item">
            <a class="site-top-menu__link" href="<?= htmlspecialchars($contactUrl, ENT_QUOTES, 'UTF-8'); ?>">
                <?= htmlspecialchars(i18n('menu.contact', $lang), ENT_QUOTES, 'UTF-8'); ?>
            </a>
        </li>
        <li class="site-top-menu__item">
            <a class="site-top-menu__link" href="<?= htmlspecialchars($newsUrl, ENT_QUOTES, 'UTF-8'); ?>">
                <?= htmlspecialchars(i18n('menu.news_events', $lang), ENT_QUOTES, 'UTF-8'); ?>
            </a>
        </li>
    </ul>

    <select
        id="language-switcher"
        name="language"
        onchange="window.location.href=this.value"
        class="site-top-menu__language"
        aria-label="Language"
    >
        <?php foreach ($supportedLanguages as $languageCode): ?>
            <?php
            $isSelected = $languageCode === $lang;
            $targetUrl = $buildLanguageUrl($languageCode, $slug);
            $languageLabel = $languageLabels[$languageCode] ?? strtoupper($languageCode);
            ?>
            <option value="<?= htmlspecialchars($targetUrl, ENT_QUOTES, 'UTF-8'); ?>" <?= $isSelected ? 'selected' : ''; ?>>
                <?= htmlspecialchars($languageLabel, ENT_QUOTES, 'UTF-8'); ?>
            </option>
        <?php endforeach; ?>
    </select>
</nav>
