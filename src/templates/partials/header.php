<?php

declare(strict_types=1);

$lang = isset($currentLanguage) ? (string) $currentLanguage : 'en';
$homeUrl = '/' . $lang . '/';
?>
<div class="site-header__row">
	<div class="site-header__logo">
		<a class="site-header__logo-link" href="<?= htmlspecialchars($homeUrl, ENT_QUOTES, 'UTF-8'); ?>">
			<img
				class="site-header__logo-img"
				src="/img/home-logo.svg"
				alt="Home" />
		</a>
	</div>
	<div class="site-header__decor" aria-hidden="true"></div>

	<div class="site-header__top-menu">
		<?php renderPartial('top-menu', ['currentLanguage' => $currentLanguage ?? 'en', 'currentSlug' => $currentSlug ?? 'home', 'dictionary' => $dictionary]); ?>
	</div>
</div>