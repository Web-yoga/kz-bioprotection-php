<?php

declare(strict_types=1);

$articles = (array) ($articlesJson ?? []);
$currentLanguageCode = (string) ($currentLanguage ?? 'en');
$fallbackImagePath = '/img/home/home-soil-btn.jpg';
$newsCardLinkLabel = 'Open news';
$newsListImageLoading = isset($newsListImageLoading) && is_string($newsListImageLoading)
	? trim($newsListImageLoading)
	: '';
if ($newsListImageLoading !== 'lazy' && $newsListImageLoading !== 'eager') {
	$newsListImageLoading = '';
}
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
			$announcementImagePath = uploadsRelativePathFromPathField($announcement['image'] ?? null);
			$announcementImageResolved = $announcementImagePath !== ''
				? uploadsResolveGeneratedImageUrls($announcementImagePath)
				: null;
			$hasAnnouncementUploadImage = is_array($announcementImageResolved)
				&& $announcementImageResolved['originalUrl'] !== '';
			$itemTitle = trim((string) ($item['title'] ?? ''));
			$itemText = trim((string) ($announcement['text'] ?? ''));
			$imagesList = (array) ($item['images'] ?? []);
			$firstImageSlot = $imagesList[0] ?? null;
			$modalImageNode = is_array($firstImageSlot) ? ($firstImageSlot['image'] ?? null) : null;
			$modalImagePath = uploadsRelativePathFromPathField($modalImageNode);
			if ($modalImagePath === '') {
				$modalImagePath = $announcementImagePath;
			}
			$modalImageResolved = $modalImagePath !== ''
				? uploadsResolveGeneratedImageUrls($modalImagePath)
				: null;
			$modalImage = is_array($modalImageResolved) && $modalImageResolved['imgUrl'] !== ''
				? $modalImageResolved['imgUrl']
				: (is_array($modalImageResolved) ? $modalImageResolved['originalUrl'] : '');
			if ($modalImage === '' && is_array($announcementImageResolved)) {
				$modalImage = $announcementImageResolved['imgUrl'] !== ''
					? $announcementImageResolved['imgUrl']
					: $announcementImageResolved['originalUrl'];
				$modalImageResolved = $announcementImageResolved;
			}
			if ($modalImage === '') {
				$modalImage = $fallbackImagePath;
				$modalImageOriginal = $fallbackImagePath;
				$modalImageFallback = $fallbackImagePath;
				$modalImageResolved = null;
			}
			$modalUsePicture = is_array($modalImageResolved) && $modalImageResolved['usePicture'];
			$modalImageWebp = is_array($modalImageResolved) ? $modalImageResolved['webpUrl'] : '';
			$modalImageOriginal = is_array($modalImageResolved) ? $modalImageResolved['originalUrl'] : '';
			$modalImageFallback = is_array($modalImageResolved) ? $modalImageResolved['imgUrl'] : '';
			$modalTitle = trim((string) ($item['title'] ?? ''));
			$modalContent = (string) ($item['content'] ?? '');
			$modalDataAttrs = ' data-modal-image-path="' . htmlspecialchars($modalImagePath, ENT_QUOTES, 'UTF-8') . '"'
				. ' data-modal-image="' . htmlspecialchars($modalImage, ENT_QUOTES, 'UTF-8') . '"'
				. ' data-modal-image-original="' . htmlspecialchars($modalImageOriginal, ENT_QUOTES, 'UTF-8') . '"'
				. ' data-modal-image-webp="' . htmlspecialchars($modalImageWebp, ENT_QUOTES, 'UTF-8') . '"'
				. ' data-modal-image-fallback="' . htmlspecialchars($modalImageFallback, ENT_QUOTES, 'UTF-8') . '"'
				. ' data-modal-use-picture="' . ($modalUsePicture ? '1' : '0') . '"';
			?>
			<article class="news-list__card">
				<button
					type="button"
					class="news-list__figure js-news-list-open"
					data-modal-slug="<?= htmlspecialchars($itemSlug, ENT_QUOTES, 'UTF-8'); ?>"
					<?= $modalDataAttrs; ?>
					data-modal-title="<?= htmlspecialchars($modalTitle, ENT_QUOTES, 'UTF-8'); ?>"
					aria-label="<?= htmlspecialchars($newsCardLinkLabel, ENT_QUOTES, 'UTF-8'); ?>">
					<?php if ($hasAnnouncementUploadImage): ?>
						<?php
						$generatedImageResolved = $announcementImageResolved;
						$alt = $itemTitle;
						$imgClass = 'news-list__img';
						$imgWidth = 800;
						$imgHeight = 600;
						$imgDecoding = 'async';
						$imgLoading = $newsListImageLoading;
						require TEMPLATES_PATH . '/partials/webp-image-generated.php';
						unset($generatedImageResolved, $pathField);
						?>
					<?php else: ?>
						<img
							class="news-list__img"
							src="<?= htmlspecialchars($fallbackImagePath, ENT_QUOTES, 'UTF-8'); ?>"
							alt="<?= htmlspecialchars($itemTitle, ENT_QUOTES, 'UTF-8'); ?>"
							width="800"
							height="600"
							decoding="async"<?= $newsListImageLoading !== '' ? ' loading="' . htmlspecialchars($newsListImageLoading, ENT_QUOTES, 'UTF-8') . '"' : ''; ?> />
					<?php endif; ?>
				</button>
				<div class="news-list__copy">
					<?php if ($itemTitle !== ''): ?>
						<button
							type="button"
							class="news-list__title news-list__title-button js-news-list-open"
							data-modal-slug="<?= htmlspecialchars($itemSlug, ENT_QUOTES, 'UTF-8'); ?>"
							<?= $modalDataAttrs; ?>
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
			<picture class="news-modal__picture">
				<source class="js-news-modal-source" type="image/webp" srcset="">
				<img class="news-modal__image js-news-modal-img" src="" alt="" />
			</picture>
			<h3 class="news-modal__title" id="news-modal-title"></h3>
			<div class="news-modal__body"></div>
		</div>
	</div>
</div>
<?php
ob_start();
?>
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

		const modalImageSource = modal.querySelector('.js-news-modal-source');
		const modalImage = modal.querySelector('.news-modal__image');
		const modalTitle = modal.querySelector('.news-modal__title');
		const modalBody = modal.querySelector('.news-modal__body');

		const closeModal = () => {
			modal.hidden = true;
			document.body.style.overflow = '';
		};

		const resolveGeneratedUploadImageFromPath = (relativePath) => {
			const path = String(relativePath || '').trim().replace(/^\/+/, '');
			if (path === '') {
				return {
					usePicture: false,
					webp: '',
					fallback: '',
					original: '',
					image: '',
				};
			}

			const segments = path.split('/');
			const filename = segments.pop() || '';
			const dotIndex = filename.lastIndexOf('.');
			const name = dotIndex > 0 ? filename.slice(0, dotIndex) : filename;
			const directory = segments.join('/');
			const prefix = directory !== '' ? `${directory}/` : '';
			const original = `${uploadsBase}/${path}`;
			const webp = `${uploadsBase}/webp/${prefix}${name}.webp`;
			const fallback = `${uploadsBase}/webp/${prefix}${name}.jpg`;

			return {
				usePicture: true,
				webp,
				fallback,
				original,
				image: fallback,
			};
		};

		const readModalImagePayload = (trigger) => {
			if (!trigger) {
				return {
					usePicture: false,
					webp: '',
					fallback: '',
					original: '',
					image: '',
				};
			}

			if (trigger.dataset.modalImageOriginal || trigger.dataset.modalImage) {
				return {
					usePicture: trigger.dataset.modalUsePicture === '1',
					webp: trigger.dataset.modalImageWebp || '',
					fallback: trigger.dataset.modalImageFallback || trigger.dataset.modalImage || '',
					original: trigger.dataset.modalImageOriginal || trigger.dataset.modalImage || '',
					image: trigger.dataset.modalImage || '',
				};
			}

			return resolveGeneratedUploadImageFromPath(trigger.dataset.modalImagePath || '');
		};

		const setModalImage = (payload) => {
			const usePicture = Boolean(payload.usePicture && payload.webp);
			if (modalImageSource) {
				if (usePicture) {
					modalImageSource.srcset = payload.webp;
				} else {
					modalImageSource.removeAttribute('srcset');
				}
			}
			if (modalImage) {
				modalImage.src = usePicture ?
					(payload.fallback || payload.original || payload.image || '') :
					(payload.original || payload.image || '');
				modalImage.alt = payload.title || '';
			}
		};

		const openModal = (payload) => {
			setModalImage(payload);
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
			const modalImagePath = item?.images?.[0]?.image?.path || announcementImagePath || '';
			const resolvedImage = resolveGeneratedUploadImageFromPath(modalImagePath);

			return {
				...resolvedImage,
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
				const imageTrigger = figureTrigger || openTrigger;
				let newsData = {
					...readModalImagePayload(imageTrigger),
					title: openTrigger.dataset.modalTitle || (figureTrigger ? figureTrigger.dataset.modalTitle || '' : ''),
					content: contentNode ? contentNode.innerHTML : '',
				};

				if (apiBase !== '' && slug !== '') {
					try {
						const remoteData = await loadNewsBySlug(slug);
						if (remoteData.content !== '' || remoteData.title !== '' || remoteData.image !== '' || remoteData.original !== '') {
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
<?php
enqueueFooterScript((string) ob_get_clean());
?>