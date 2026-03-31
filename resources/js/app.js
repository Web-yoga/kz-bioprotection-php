import "../css/app.css";
import "swiper/css";
import "swiper/css/pagination";
import { Swiper } from "swiper";
import { Pagination } from "swiper/modules";
import { animate } from "animejs";

window.Swiper = Swiper;
window.anime = animate;

const dropdownItems = Array.from(document.querySelectorAll("[data-dropdown]"));

if (dropdownItems.length > 0) {
	const closeDropdown = (dropdownItem) => {
		const trigger = dropdownItem.querySelector("[data-dropdown-trigger]");
		const menu = dropdownItem.querySelector("[data-dropdown-menu]");

		if (!trigger || !menu) {
			return;
		}

		dropdownItem.classList.remove("is-open");
		trigger.setAttribute("aria-expanded", "false");
		menu.setAttribute("hidden", "");
	};

	const openDropdown = (dropdownItem) => {
		const trigger = dropdownItem.querySelector("[data-dropdown-trigger]");
		const menu = dropdownItem.querySelector("[data-dropdown-menu]");

		if (!trigger || !menu) {
			return;
		}

		menu.removeAttribute("hidden");
		dropdownItem.classList.add("is-open");
		trigger.setAttribute("aria-expanded", "true");
	};

	const closeAllDropdowns = (exceptItem = null) => {
		dropdownItems.forEach((dropdownItem) => {
			if (exceptItem !== null && dropdownItem === exceptItem) {
				return;
			}
			closeDropdown(dropdownItem);
		});
	};

	dropdownItems.forEach((dropdownItem) => {
		const trigger = dropdownItem.querySelector("[data-dropdown-trigger]");
		if (!trigger) {
			return;
		}

		trigger.addEventListener("click", () => {
			const isOpen = dropdownItem.classList.contains("is-open");
			if (isOpen) {
				closeDropdown(dropdownItem);
				return;
			}

			closeAllDropdowns(dropdownItem);
			openDropdown(dropdownItem);
		});
	});

	document.addEventListener("click", (event) => {
		const target = event.target;
		if (!(target instanceof Node)) {
			return;
		}

		const clickedInsideAnyDropdown = dropdownItems.some((dropdownItem) =>
			dropdownItem.contains(target),
		);

		if (!clickedInsideAnyDropdown) {
			closeAllDropdowns();
		}
	});

	document.addEventListener("keydown", (event) => {
		if (event.key !== "Escape") {
			return;
		}
		closeAllDropdowns();
	});
}

const beforeAfterBlocks = Array.from(document.querySelectorAll("[data-before-after]"));

beforeAfterBlocks.forEach((block) => {
	const range = block.querySelector(".oil-before-after__range");
	if (!(range instanceof HTMLInputElement)) {
		return;
	}

	const syncPosition = () => {
		const nextPosition = Number.parseFloat(range.value);
		if (Number.isNaN(nextPosition)) {
			return;
		}

		const boundedPosition = Math.min(100, Math.max(0, nextPosition));
		block.style.setProperty("--before-after-position", String(boundedPosition));
	};

	range.addEventListener("input", syncPosition);
	range.addEventListener("change", syncPosition);
	range.addEventListener("focus", () => {
		block.classList.add("is-focused");
	});
	range.addEventListener("blur", () => {
		block.classList.remove("is-focused");
	});

	syncPosition();
});

const oilCertificationsSliders = Array.from(
	document.querySelectorAll("[data-oil-certifications-slider]"),
);

oilCertificationsSliders.forEach((sliderElement) => {
	const paginationElement = sliderElement.querySelector(
		"[data-oil-certifications-pagination]",
	);

	new Swiper(sliderElement, {
		modules: [Pagination],
		slidesPerView: "auto",
		spaceBetween: 12,
		speed: 450,
		grabCursor: true,
		pagination: {
			el: paginationElement,
			clickable: true,
		},
	});
});
