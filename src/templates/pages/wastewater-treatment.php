<?php

declare(strict_types=1);

$pageWastewaterPayload = isset($pageContent) && is_array($pageContent) ? $pageContent : [];
$topText = isset($pageWastewaterPayload['topText']) ? trim((string) $pageWastewaterPayload['topText']) : '';
?>
<?php if ($topText !== ''): ?>
	<section class="home-top-text">
		<div class="home-top-text__content"><?= $topText; ?></div>
	</section>
<?php endif; ?>
<?php
require TEMPLATES_PATH . '/partials/slider.php';
require TEMPLATES_PATH . '/partials/contact-form.php';
$articlesJson = fetchArticlesCollection((string) ($currentLanguage ?? 'en'));
?>
<section id="news" class="news-events" style="margin-top: var(--section-spacing); margin-bottom: var(--section-spacing);">
	<h2 class="section-title"><?= $dictionary['newsEvents']; ?></h2>
	<?php require TEMPLATES_PATH . '/partials/news-list.php'; ?>
</section>
<?php
$pageWastewaterJson = json_encode(
	$pageWastewaterPayload,
	JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
);
?>
<section class="home-pagehome-data">
	<pre class="home-pagehome-data__pre"><?= htmlspecialchars((string) $pageWastewaterJson, ENT_QUOTES, 'UTF-8'); ?></pre>
</section>
