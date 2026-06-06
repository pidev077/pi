import Lenis from "lenis";
import Header from "./components/header";
import Functions from "./components/functions";
import MegaMenu from "./components/mega-menu";
import CF7 from "./components/cf7";
import Blog from "./components/blog";
import AOS from "aos";

// global lenis instance
let lenis;
let rafId;

document.addEventListener("DOMContentLoaded", async () => {
	initLenis();
	window.lenis = lenis;

	Header.init();
	Functions.init();
	MegaMenu.init();
	CF7.init();
	Blog.init();
	initLangSwitcher();
	initMobileDrawer();

	AOS.init({ once: true });
});

/* ═══════════════════════════════════════════════
   Mobile Drawer
   Full-screen overlay that slides in from the right.
   Replaces the old header__menu drop-down on mobile.
═══════════════════════════════════════════════ */
function initMobileDrawer() {
	const drawer    = document.getElementById("mobile-menu");
	const hamburger = document.getElementById("btn-toggle-menu-mobile");
	if (!drawer || !hamburger) return;

	const overlay  = drawer.querySelector(".mobile-menu__overlay");
	const closeBtn = drawer.querySelector(".mobile-menu__close");

	// ── Open / close ─────────────────────────────────────────────
	const openDrawer = () => {
		drawer.classList.add("is-open");
		drawer.setAttribute("aria-hidden", "false");
		document.body.classList.add("mobile-opened");
		document.body.style.overflow = "hidden";
		if (window.lenis) window.lenis.stop();
	};

	const closeDrawer = () => {
		drawer.classList.remove("is-open");
		drawer.setAttribute("aria-hidden", "true");
		document.body.classList.remove("mobile-opened");
		document.body.style.overflow = "";
		if (window.lenis) window.lenis.start();
	};

	hamburger.addEventListener("click", (e) => {
		e.stopPropagation();
		drawer.classList.contains("is-open") ? closeDrawer() : openDrawer();
	});

	closeBtn?.addEventListener("click", closeDrawer);
	overlay?.addEventListener("click", closeDrawer);

	document.addEventListener("keydown", (e) => {
		if (e.key === "Escape" && drawer.classList.contains("is-open")) {
			closeDrawer();
		}
	});

	// ── Level-1 accordion ────────────────────────────────────────
	drawer.querySelectorAll(".mob-toggle").forEach((toggle) => {
		toggle.addEventListener("click", () => {
			const item  = toggle.closest(".mob-item");
			const isOpen = item.classList.contains("is-open");

			// Close all open level-1 items
			drawer.querySelectorAll(".mob-item.is-open").forEach((el) => {
				el.classList.remove("is-open");
				el.querySelector(".mob-toggle")?.setAttribute("aria-expanded", "false");
			});

			if (!isOpen) {
				item.classList.add("is-open");
				toggle.setAttribute("aria-expanded", "true");
			}
		});
	});

	// ── Level-2 accordion ────────────────────────────────────────
	drawer.querySelectorAll(".mob-sub-toggle").forEach((toggle) => {
		toggle.addEventListener("click", () => {
			const item   = toggle.closest(".mob-sub-item");
			const isOpen = item.classList.contains("is-open");
			item.classList.toggle("is-open", !isOpen);
			toggle.setAttribute("aria-expanded", String(!isOpen));
		});
	});

	// ── Mobile language switcher ──────────────────────────────────
	const langToggle   = drawer.querySelector(".mob-lang__toggle");
	const langDropdown = drawer.querySelector(".mob-lang__dropdown");

	if (langToggle && langDropdown) {
		langToggle.addEventListener("click", () => {
			const isOpen = langDropdown.classList.toggle("is-open");
			langToggle.setAttribute("aria-expanded", String(isOpen));
		});
		drawer.addEventListener("click", (e) => {
			if (!langToggle.contains(e.target) && !langDropdown.contains(e.target)) {
				langDropdown.classList.remove("is-open");
				langToggle.setAttribute("aria-expanded", "false");
			}
		});
	}
}

/* ═══════════════════════════════════════════════
   Desktop language switcher (header)
═══════════════════════════════════════════════ */
function initLangSwitcher() {
	const toggle   = document.querySelector(".lang-switcher__toggle");
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

/* ═══════════════════════════════════════════════
   Lenis smooth scroll
═══════════════════════════════════════════════ */
function initLenis() {
	if (rafId) cancelAnimationFrame(rafId);
	if (lenis) lenis.destroy();

	lenis = new Lenis({ duration: 1.2, lerp: 0.1 });

	function raf(time) {
		lenis.raf(time);
		rafId = requestAnimationFrame(raf);
	}
	rafId = requestAnimationFrame(raf);
}
