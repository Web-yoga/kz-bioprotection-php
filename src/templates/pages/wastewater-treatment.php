<?php

declare(strict_types=1);

echo basename(__FILE__, '.php') . PHP_EOL;
require TEMPLATES_PATH . '/partials/slider.php';
require TEMPLATES_PATH . '/partials/contact-form.php';
$articlesJson = fetchArticlesCollection((string) ($currentLanguage ?? 'en'));
?>
<section id="news" class="news-events" style="margin-top: var(--section-spacing);">
	<h2 class="section-title"><?= $dictionary['newsEvents']; ?></h2>
	<?php require TEMPLATES_PATH . '/partials/news-list.php'; ?>
</section>
