<?php

declare(strict_types=1);

$articles = (array) ($articlesJson ?? []);
$currentLanguageCode = (string) ($currentLanguage ?? 'en');
$fallbackImagePath = '/img/home/home-soil-btn.jpg';
$newsCardLinkLabel = 'Open news';
?>

<section
	class="news-list"
	aria-label="News list announcements"
	data-content-api-base="<?= htmlspecialchars(CONTENT_API_BASE_URL, ENT_QUOTES, 'UTF-8'); ?>"
	data-uploads-base="<?= htmlspecialchars(UPLOADS_BASE_URL, ENT_QUOTES, 'UTF-8'); ?>"
	data-current-language="<?= htmlspecialchars($currentLanguageCode, ENT_QUOTES, 'UTF-8'); ?>">
	<div class="news-list__grid">
		<?php foreach ($articles as $item): ?>
			<?php
			$item = (array) $item;
			$itemSlug = trim((string) ($item['slug'] ?? ''));
			$announcement = (array) ($item['announcement'] ?? []);
			$itemImagePath = trim((string) (($announcement['image']['path'] ?? '')));
			$itemImage = $itemImagePath !== '' ? UPLOADS_BASE_URL . $itemImagePath : $fallbackImagePath;
			$itemTitle = trim((string) ($item['title'] ?? ''));
			$itemText = trim((string) ($announcement['text'] ?? ''));
			$modalImagePath = trim((string) (($item['images'][0]['image']['path'] ?? '')));
			$modalImage = $modalImagePath !== '' ? UPLOADS_BASE_URL . $modalImagePath : $itemImage;
			$modalTitle = trim((string) ($item['title'] ?? ''));
			$modalContent = (string) ($item['content'] ?? '');
			?>
			<article class="news-list__card">
				<button
					type="button"
					class="news-list__figure js-news-list-open"
					data-modal-slug="<?= htmlspecialchars($itemSlug, ENT_QUOTES, 'UTF-8'); ?>"
					data-modal-image="<?= htmlspecialchars($modalImage, ENT_QUOTES, 'UTF-8'); ?>"
					data-modal-title="<?= htmlspecialchars($modalTitle, ENT_QUOTES, 'UTF-8'); ?>"
					aria-label="<?= htmlspecialchars($newsCardLinkLabel, ENT_QUOTES, 'UTF-8'); ?>">
					<img
						class="news-list__img"
						src="<?= htmlspecialchars($itemImage, ENT_QUOTES, 'UTF-8'); ?>"
						alt="<?= htmlspecialchars($itemTitle, ENT_QUOTES, 'UTF-8'); ?>"
						width="800"
						height="600"
						decoding="async" />
				</button>
				<div class="news-list__copy">
					<?php if ($itemTitle !== ''): ?>
						<button
							type="button"
							class="news-list__title news-list__title-button js-news-list-open"
							data-modal-slug="<?= htmlspecialchars($itemSlug, ENT_QUOTES, 'UTF-8'); ?>"
							data-modal-image="<?= htmlspecialchars($modalImage, ENT_QUOTES, 'UTF-8'); ?>"
							data-modal-title="<?= htmlspecialchars($modalTitle, ENT_QUOTES, 'UTF-8'); ?>"
							aria-label="<?= htmlspecialchars($newsCardLinkLabel, ENT_QUOTES, 'UTF-8'); ?>">
							<?= htmlspecialchars($itemTitle, ENT_QUOTES, 'UTF-8'); ?>
						</button>
					<?php endif; ?>
					<?php if ($itemText !== ''): ?>
						<div class="news-list__text"><?= $itemText; ?></div>
					<?php endif; ?>
				</div>
				<div class="js-news-list-item-content" hidden><?= $modalContent; ?></div>
			</article>
		<?php endforeach; ?>
	</div>
</section>

<div class="news-modal" id="news-modal" hidden>
	<div class="news-modal__backdrop js-news-modal-close" aria-hidden="true"></div>
	<div class="news-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="news-modal-title">
		<button class="news-modal__close js-news-modal-close" type="button" aria-label="Close">×</button>
		<div class="news-modal__content">
			<img class="news-modal__image" src="" alt="" />
			<h3 class="news-modal__title" id="news-modal-title"></h3>
			<div class="news-modal__body"></div>
		</div>
	</div>
</div>
<script>
	(() => {
		const listRoot = document.querySelector('.news-list');
		const modal = document.getElementById('news-modal');
		if (!modal || !listRoot) {
			return;
		}
		if (modal.parentElement !== document.body) {
			document.body.appendChild(modal);
		}
		const apiBase = (listRoot.dataset.contentApiBase || '').replace(/\/+$/, '');
		const uploadsBase = (listRoot.dataset.uploadsBase || '').replace(/\/+$/, '');
		const currentLanguage = listRoot.dataset.currentLanguage || 'en';

		const modalImage = modal.querySelector('.news-modal__image');
		const modalTitle = modal.querySelector('.news-modal__title');
		const modalBody = modal.querySelector('.news-modal__body');

		const closeModal = () => {
			modal.hidden = true;
			document.body.style.overflow = '';
		};

		const openModal = (payload) => {
			modalImage.src = payload.image || '';
			modalImage.alt = payload.title || '';
			modalTitle.textContent = payload.title || '';
			modalBody.innerHTML = payload.content || '';
			modal.hidden = false;
			document.body.style.overflow = 'hidden';
		};

		const normalizeArticles = (response) => {
			if (Array.isArray(response)) {
				return response;
			}
			if (!response || typeof response !== 'object') {
				return [];
			}
			for (const key of ['entries', 'items', 'data', 'results']) {
				if (Array.isArray(response[key])) {
					return response[key];
				}
			}
			return [];
		};

		const normalizeSlug = (slug) =>
			String(slug || '')
			.trim()
			.toLowerCase()
			.replace(/^\/+|\/+$/g, '');
		const buildUploadUrl = (path) =>
			`${uploadsBase}/${String(path || '').trim().replace(/^\/+/, '')}`;

		const loadNewsBySlug = async (slug) => {
			const requestUrl = `${apiBase}/items/articles?locale=${encodeURIComponent(currentLanguage)}`;
			const response = await fetch(requestUrl, {
				credentials: 'omit'
			});
			const payload = await response.json();
			const articles = normalizeArticles(payload);
			const targetSlug = normalizeSlug(slug);
			const item = articles.find((entry) => normalizeSlug(entry?.slug) === targetSlug) || {};
			const announcement = item.announcement || {};
			const announcementImagePath = announcement?.image?.path || '';
			const modalImagePath = item?.images?.[0]?.image?.path || '';
			return {
				image: modalImagePath ?
					buildUploadUrl(modalImagePath) : announcementImagePath ?
					buildUploadUrl(announcementImagePath) : '',
				title: String(item.title || '').trim(),
				content: String(item.content || ''),
			};
		};

		document.addEventListener('click', async (event) => {
			const openTrigger = event.target.closest('.js-news-list-open');
			if (openTrigger) {
				const card = openTrigger.closest('.news-list__card');
				const figureTrigger = card ? card.querySelector('.news-list__figure.js-news-list-open') : null;
				event.preventDefault();
				const slug = openTrigger.dataset.modalSlug || (figureTrigger ? figureTrigger.dataset.modalSlug || '' : '');
				const contentNode = card.querySelector('.js-news-list-item-content');
				let newsData = {
					image: openTrigger.dataset.modalImage || (figureTrigger ? figureTrigger.dataset.modalImage || '' : ''),
					title: openTrigger.dataset.modalTitle || (figureTrigger ? figureTrigger.dataset.modalTitle || '' : ''),
					content: contentNode ? contentNode.innerHTML : '',
				};

				if (apiBase !== '' && slug !== '') {
					try {
						const remoteData = await loadNewsBySlug(slug);
						if (remoteData.content !== '' || remoteData.title !== '' || remoteData.image !== '') {
							newsData = remoteData;
						}
					} catch (error) {
						// Keep local fallback content when API request fails.
					}
				}

				openModal(newsData);
				return;
			}

			if (event.target.closest('.js-news-modal-close')) {
				closeModal();
			}
		});

		document.addEventListener('keydown', (event) => {
			if (event.key === 'Escape' && !modal.hidden) {
				closeModal();
			}
		});
	})();
</script>