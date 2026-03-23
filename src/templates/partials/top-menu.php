<?php

declare(strict_types=1);

global $supportedLanguages;

$lang = isset($currentLanguage) ? (string) $currentLanguage : 'en';
$slug = isset($currentSlug) ? (string) $currentSlug : 'home';

$languageLabels = [
    'ru' => 'RU',
    'en' => 'EN',
    'kz' => 'KZ',
];

$buildLanguageUrl = static function (string $targetLanguage, string $currentSlug): string {
    if ($currentSlug === 'home') {
        return '/' . $targetLanguage . '/';
    }

    return '/' . $targetLanguage . '/' . $currentSlug;
};
?>
<nav>
    <label for="language-switcher">Language:</label>
    <select
        id="language-switcher"
        name="language"
        onchange="window.location.href=this.value"
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
