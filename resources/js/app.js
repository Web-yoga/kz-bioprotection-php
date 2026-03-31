import "../css/app.css";
import "swiper/css";
import { Swiper } from "swiper";
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
