import Lenis from "lenis";
import Header from "./components/header";
import Functions from "./components/functions";
import AOS from "aos";

// global lenis instance
let lenis;
let rafId;

document.addEventListener("DOMContentLoaded", async () => {
	initLenis();
	window.lenis = lenis;

	Header.init();
	Functions.init();
	initLangSwitcher();
	initMobileSubmenus();

	AOS.init({
		once: true,
	});

	const hamberger = document.getElementById("btn-toggle-menu-mobile");
	if (hamberger) {
		hamberger.addEventListener("click", function (e) {
			e.stopPropagation();
			const isOpen = document.body.classList.toggle("mobile-opened");
			if (isOpen) {
				lenis.stop();
			} else {
				lenis.start();
				// Close any open submenus when closing mobile menu
				document.querySelectorAll(".nav-item.dropdown.open").forEach((el) => {
					el.classList.remove("open");
					const toggle = el.querySelector(".dropdown-toggle");
					if (toggle) toggle.setAttribute("aria-expanded", "false");
				});
			}
		});
	}
});

// Mobile submenu: click dropdown-toggle to expand/collapse inline
function initMobileSubmenus() {
	const LG_BREAKPOINT = 992;
	const dropdownItems = document.querySelectorAll(
		".header .nav-item.dropdown"
	);

	dropdownItems.forEach((item) => {
		const toggle = item.querySelector(".dropdown-toggle");
		if (!toggle) return;

		toggle.addEventListener("click", (e) => {
			if (window.innerWidth >= LG_BREAKPOINT) return; // desktop: CSS hover handles it
			e.preventDefault();
			e.stopPropagation();

			const isOpen = item.classList.toggle("open");
			toggle.setAttribute("aria-expanded", String(isOpen));

			// Close sibling submenus
			dropdownItems.forEach((sibling) => {
				if (sibling !== item) {
					sibling.classList.remove("open");
					const sibToggle = sibling.querySelector(".dropdown-toggle");
					if (sibToggle) sibToggle.setAttribute("aria-expanded", "false");
				}
			});
		});
	});
}

function initLangSwitcher() {
	const toggle = document.querySelector(".lang-switcher__toggle");
	const dropdown = document.querySelector(".lang-switcher__dropdown");

	if (!toggle || !dropdown) return;

	toggle.addEventListener("click", (e) => {
		e.stopPropagation();
		const isOpen = dropdown.classList.toggle("show");
		toggle.setAttribute("aria-expanded", isOpen);
	});

	document.addEventListener("click", () => {
		dropdown.classList.remove("show");
		toggle.setAttribute("aria-expanded", "false");
	});

	dropdown.addEventListener("click", (e) => e.stopPropagation());
}

/* ========== LENIS ========== */
function initLenis() {
	if (rafId) cancelAnimationFrame(rafId);
	if (lenis) lenis.destroy();

	lenis = new Lenis({
		duration: 1.2,
		lerp: 0.1,
	});

	function raf(time) {
		lenis.raf(time);
		rafId = requestAnimationFrame(raf);
	}
	rafId = requestAnimationFrame(raf);
}
