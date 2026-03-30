<?php

declare(strict_types=1);

$feedbackFormPayload = isset($feedbackForm) && is_array($feedbackForm) ? $feedbackForm : [];
$contactFormFields = isset($feedbackFormPayload['contactFormFields']) && is_array($feedbackFormPayload['contactFormFields'])
	? $feedbackFormPayload['contactFormFields']
	: [];
$contactTextHtml = isset($feedbackFormPayload['contactText']) && is_string($feedbackFormPayload['contactText'])
	? $feedbackFormPayload['contactText']
	: '';
$technicalBtnText = isset($feedbackFormPayload['technicalBtnText']) && is_string($feedbackFormPayload['technicalBtnText'])
	? trim($feedbackFormPayload['technicalBtnText'])
	: '';
$contactBtnText = isset($feedbackFormPayload['contactBtnText']) && is_string($feedbackFormPayload['contactBtnText'])
	? trim($feedbackFormPayload['contactBtnText'])
	: '';
?>
<section class="contact-form-section">
	<h2 class="section-title"><?= $dictionary['requestQuote']; ?></h2>
	<form
		class="contact-form-section__form"
		method="post"
		action="#"
		enctype="multipart/form-data">
		<div class="contact-form-section__hero-sm">
			<div class="contact-form-section__hero-sm-frame img-shadow">
				<div class="contact-form-section__hero-sm-square">
					<img
						class="contact-form-section__hero-img"
						src="/img/form-bg.jpg"
						alt=""
						width="1200"
						height="1200"
						decoding="async" />
					<?php if ($contactTextHtml !== ''): ?>
						<div class="contact-form-section__hero-sm-strip">
							<div class="contact-form-section__hero-sm-strip-shade" aria-hidden="true"></div>
							<div class="contact-form-section__hero-sm-strip-content">
								<div class="contact-form-section__hero-caption"><?= $contactTextHtml; ?></div>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="contact-form-section__desktop">
			<div class="contact-form-section__desktop-bg" aria-hidden="true"></div>
			<?php if ($contactTextHtml !== ''): ?>
				<div class="contact-form-section__desktop-strip">
					<div class="contact-form-section__desktop-strip-shade" aria-hidden="true"></div>
					<div class="contact-form-section__desktop-strip-content">
						<div class="contact-form-section__desktop-caption"><?= $contactTextHtml; ?></div>
					</div>
				</div>
			<?php endif; ?>
			<div class="contact-form-section__desktop-inner">
				<div class="contact-form-section__form-column">
					<div class="contact-form-section__form-panel">
						<?php
						foreach ($contactFormFields as $fieldRow):
							if (!is_array($fieldRow)) {
								continue;
							}
							$fieldId = isset($fieldRow['id']) && is_string($fieldRow['id']) ? trim($fieldRow['id']) : '';
							$fieldLabel = isset($fieldRow['name']) && is_string($fieldRow['name']) ? $fieldRow['name'] : '';
							if ($fieldId === '') {
								continue;
							}
							$safeId = preg_replace('/[^a-z0-9_-]/i', '', $fieldId);
							$inputId = 'contact-field-' . ($safeId !== '' ? $safeId : md5($fieldId));
						?>
							<?php if ($fieldId === 'technical'): ?>
								<div class="contact-form-section__row">
									<span class="contact-form-section__label" id="<?= htmlspecialchars($inputId, ENT_QUOTES, 'UTF-8'); ?>-legend">
										<?= htmlspecialchars($fieldLabel, ENT_QUOTES, 'UTF-8'); ?>
									</span>
									<input
										class="contact-form-section__file-input"
										type="file"
										id="<?= htmlspecialchars($inputId, ENT_QUOTES, 'UTF-8'); ?>"
										name="<?= htmlspecialchars($fieldId, ENT_QUOTES, 'UTF-8'); ?>"
										aria-labelledby="<?= htmlspecialchars($inputId, ENT_QUOTES, 'UTF-8'); ?>-legend" />
									<label
										class="contact-form-section__field contact-form-section__file-label"
										for="<?= htmlspecialchars($inputId, ENT_QUOTES, 'UTF-8'); ?>">
										<?= htmlspecialchars($technicalBtnText, ENT_QUOTES, 'UTF-8'); ?>
									</label>
								</div>
							<?php else: ?>
								<div class="contact-form-section__row">
									<label class="contact-form-section__label" for="<?= htmlspecialchars($inputId, ENT_QUOTES, 'UTF-8'); ?>">
										<?= htmlspecialchars($fieldLabel, ENT_QUOTES, 'UTF-8'); ?>
									</label>
									<textarea
										class="contact-form-section__field contact-form-section__field--grow"
										id="<?= htmlspecialchars($inputId, ENT_QUOTES, 'UTF-8'); ?>"
										name="<?= htmlspecialchars($fieldId, ENT_QUOTES, 'UTF-8'); ?>"
										rows="1"></textarea>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
					<button class="home-solutions__cta contact-form-section__submit" type="submit">
						<?= htmlspecialchars($contactBtnText, ENT_QUOTES, 'UTF-8'); ?>
					</button>
				</div>
			</div>
		</div>
	</form>
</section>
<?php
$contactFormTextareasEntry = 'resources/js/contact-form-textareas.js';
if (!empty($isViteDevMode) && isset($viteDevServerUrl) && is_string($viteDevServerUrl) && $viteDevServerUrl !== '') {
	$contactFormTextareasSrc = rtrim($viteDevServerUrl, '/') . '/' . $contactFormTextareasEntry;
} else {
	$contactFormTextareasSrc = getViteEntryJsUrl($contactFormTextareasEntry);
}
?>
<?php if ($contactFormTextareasSrc !== ''): ?>
	<script type="module" src="<?= htmlspecialchars($contactFormTextareasSrc, ENT_QUOTES, 'UTF-8'); ?>"></script>
<?php endif; ?>