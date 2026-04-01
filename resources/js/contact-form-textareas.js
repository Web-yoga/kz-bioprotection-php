const CONTACT_FORM_TEXTAREA_SELECTOR = "textarea.contact-form-section__field--grow";
const CONTACT_FORM_FILE_INPUT_SELECTOR = "input.contact-form-section__file-input";
const CONTACT_FORM_STATUS_SELECTOR = ".contact-form-section__status";
const CONTACT_FORM_SUBMIT_SELECTOR = ".contact-form-section__submit";
const CONTACT_FORM_MODAL_SELECTOR = "[data-contact-form-modal]";
const CONTACT_FORM_MODAL_CLOSE_SELECTOR = "[data-contact-form-modal-close]";
const CONTACT_FORM_MODAL_TITLE_SELECTOR = ".contact-form-modal__title";
const STATUS_SUCCESS_CLASS = "contact-form-section__status--success";
const STATUS_ERROR_CLASS = "contact-form-section__status--error";
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

function getOrCreateStatusElement(form) {
	const section = form.closest(".contact-form-section");
	if (!section) {
		return null;
	}

	let statusEl = section.querySelector(CONTACT_FORM_STATUS_SELECTOR);
	if (!statusEl) {
		statusEl = document.createElement("p");
		statusEl.className = "contact-form-section__status";
		statusEl.hidden = true;
		form.parentNode?.insertBefore(statusEl, form);
	}

	statusEl.setAttribute("role", "status");
	statusEl.setAttribute("aria-live", "polite");
	return statusEl;
}

function showContactFormStatus(form, type, message) {
	const statusEl = getOrCreateStatusElement(form);
	if (!statusEl || !message) {
		return;
	}

	statusEl.hidden = false;
	statusEl.classList.remove(STATUS_SUCCESS_CLASS, STATUS_ERROR_CLASS);
	statusEl.classList.add(
		type === "success" ? STATUS_SUCCESS_CLASS : STATUS_ERROR_CLASS,
	);
	statusEl.textContent = message;
}

function hideContactFormStatus(form) {
	const statusEl = getOrCreateStatusElement(form);
	if (!statusEl) {
		return;
	}

	statusEl.hidden = true;
	statusEl.classList.remove(STATUS_SUCCESS_CLASS, STATUS_ERROR_CLASS);
	statusEl.textContent = "";
}

function openContactFormSuccessModal(form, message) {
	const section = form.closest(".contact-form-section");
	if (!section) {
		return;
	}

	const modal = section.querySelector(CONTACT_FORM_MODAL_SELECTOR);
	if (!modal) {
		return;
	}

	const modalTitle = modal.querySelector(CONTACT_FORM_MODAL_TITLE_SELECTOR);
	const fallbackTitle = modalTitle ? (modalTitle.textContent ?? "").trim() : "";
	const resolvedMessage = (message || fallbackTitle || "Form submitted successfully").trim();
	if (modalTitle) {
		modalTitle.textContent = resolvedMessage;
	}

	modal.hidden = false;
	document.body.style.overflow = "hidden";
}

function closeContactFormSuccessModal(modal) {
	modal.hidden = true;
	document.body.style.overflow = "";
}

function initContactFormModal(form) {
	const section = form.closest(".contact-form-section");
	if (!section) {
		return;
	}

	const modal = section.querySelector(CONTACT_FORM_MODAL_SELECTOR);
	if (!modal) {
		return;
	}

	modal
		.querySelectorAll(CONTACT_FORM_MODAL_CLOSE_SELECTOR)
		.forEach((closeTrigger) => {
			closeTrigger.addEventListener("click", () => {
				closeContactFormSuccessModal(modal);
			});
		});

	document.addEventListener("keydown", (event) => {
		if (event.key === "Escape" && !modal.hidden) {
			closeContactFormSuccessModal(modal);
		}
	});
}

function updateFileInputLabel(form, fileInput) {
	const buttonLabel = form.querySelector(`label[for="${fileInput.id}"]`);
	if (!buttonLabel) {
		return;
	}

	if (!buttonLabel.dataset.defaultLabelText) {
		buttonLabel.dataset.defaultLabelText = buttonLabel.textContent ?? "";
	}

	const selectedFile = fileInput.files && fileInput.files[0];
	buttonLabel.textContent =
		selectedFile && selectedFile.name
			? selectedFile.name
			: buttonLabel.dataset.defaultLabelText;
}

async function submitContactFormWithAjax(form) {
	const submitButton = form.querySelector(CONTACT_FORM_SUBMIT_SELECTOR);
	const defaultSubmitText = submitButton ? submitButton.textContent ?? "" : "";
	const sendingMessage = form.dataset.sendingMessage ?? "";
	const successMessage = form.dataset.successMessage ?? "";
	const errorMessage = form.dataset.errorMessage ?? "";

	if (submitButton) {
		submitButton.disabled = true;
		if (sendingMessage) {
			submitButton.textContent = sendingMessage;
		}
	}

	let isSuccess = false;
	try {
		const formData = new FormData(form);
		const response = await fetch(window.location.pathname, {
			method: "POST",
			body: formData,
			headers: {
				"X-Requested-With": "XMLHttpRequest",
				Accept: "application/json",
			},
		});

		let payload = null;
		try {
			payload = await response.json();
		} catch {
			payload = null;
		}

		isSuccess = response.ok && payload && payload.success === true;
		if (isSuccess) {
			hideContactFormStatus(form);
			openContactFormSuccessModal(form, successMessage);
			form.reset();
			const submittedAtField = form.querySelector('input[name="submitted_at"]');
			if (submittedAtField) {
				submittedAtField.value = String(Math.floor(Date.now() / 1000));
			}

			form.querySelectorAll(CONTACT_FORM_TEXTAREA_SELECTOR).forEach((textarea) => {
				syncTextareaRows(textarea);
			});
			form.querySelectorAll(CONTACT_FORM_FILE_INPUT_SELECTOR).forEach((fileInput) => {
				updateFileInputLabel(form, fileInput);
			});
		} else {
			showContactFormStatus(form, "error", errorMessage || "Error");
		}
	} catch {
		showContactFormStatus(form, "error", errorMessage || "Error");
	} finally {
		if (submitButton) {
			submitButton.disabled = false;
			submitButton.textContent = defaultSubmitText;
		}
	}
}

function initContactForm() {
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

	const fileInputs = form.querySelectorAll(CONTACT_FORM_FILE_INPUT_SELECTOR);
	fileInputs.forEach((fileInput) => {
		updateFileInputLabel(form, fileInput);
		fileInput.addEventListener("change", () => {
			updateFileInputLabel(form, fileInput);
		});
	});

	initContactFormModal(form);

	form.addEventListener("submit", async (event) => {
		event.preventDefault();
		await submitContactFormWithAjax(form);
	});
}

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", initContactForm, {
		once: true,
	});
} else {
	initContactForm();
}
