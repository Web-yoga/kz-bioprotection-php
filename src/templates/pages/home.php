<?php

declare(strict_types=1);

$pageHomePayload = isset($pageContent) && is_array($pageContent) ? $pageContent : [];
$topText = isset($pageHomePayload['topText']) ? trim((string) $pageHomePayload['topText']) : '';
?>
<?php if ($topText !== ''): ?>
	<section class="home-top-text">
		<div class="home-top-text__content"><?= $topText; ?></div>
	</section>
<?php endif; ?>
<?php
require TEMPLATES_PATH . '/partials/slider.php';
require TEMPLATES_PATH . '/partials/contact-form.php';
