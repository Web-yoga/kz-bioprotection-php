<?php

declare(strict_types=1);

$bottomMenu = [];
if (
	isset($footerContent)
	&& is_array($footerContent)
	&& isset($footerContent['bottomMenu'])
	&& is_array($footerContent['bottomMenu'])
) {
	$bottomMenu = $footerContent['bottomMenu'];
}

$contactsHtml = '';
if (isset($footerContent) && is_array($footerContent) && isset($footerContent['contacts']) && is_string($footerContent['contacts'])) {
	$contactsHtml = trim($footerContent['contacts']);
}

?>
<footer class="site-footer">
	<div class="site-footer__inner container mx-auto px-4">
		<nav class="site-footer__menu" aria-label="Footer menu">
			<ul class="site-footer__menu-list">
				<?php foreach ($bottomMenu as $item): ?>
					<?php
					if (!is_array($item)) {
						continue;
					}
					$link = isset($item['link']) && is_string($item['link']) ? trim($item['link']) : '';
					$name = isset($item['name']) && is_string($item['name']) ? trim($item['name']) : '';
					if ($link === '' || $name === '') {
						continue;
					}
					?>
					<li class="site-footer__menu-item">
						<a class="site-footer__menu-link" href="<?= htmlspecialchars($link, ENT_QUOTES, 'UTF-8'); ?>">
							<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
		<div class="site-footer__contacts">
			<?= $contactsHtml; ?>
		</div>
	</div>
</footer>
