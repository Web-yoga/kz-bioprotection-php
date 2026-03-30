const CONTACT_FORM_TEXTAREA_SELECTOR = "textarea.contact-form-section__field--grow";
const MAX_ROWS = 40;

/**
 * Fit textarea height by increasing `rows` until content is not clipped.
 */
function syncTextareaRows(textarea) {
	textarea.rows = 1;
	while (
		textarea.scrollHeight > textarea.clientHeight &&
		textarea.rows < MAX_ROWS
	) {
		textarea.rows += 1;
	}
}

function initContactFormTextareaRows() {
	const form = document.querySelector(".contact-form-section__form");
	if (!form) {
		return;
	}

	const textareas = form.querySelectorAll(CONTACT_FORM_TEXTAREA_SELECTOR);
	textareas.forEach((textarea) => {
		syncTextareaRows(textarea);
		textarea.addEventListener("input", () => {
			syncTextareaRows(textarea);
		});
	});

	window.addEventListener(
		"resize",
		() => {
			textareas.forEach((el) => {
				syncTextareaRows(el);
			});
		},
		{ passive: true },
	);
}

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", initContactFormTextareaRows, {
		once: true,
	});
} else {
	initContactFormTextareaRows();
}
