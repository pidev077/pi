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

	AOS.init({
		once: true,
	});

	const hamberger = document.getElementById("btn-toggle-menu-mobile");
	hamberger.addEventListener("click", function (e) {
		e.stopPropagation();
		const isOpen = document.body.classList.toggle("mobile-opened");
		if (isOpen) {
			lenis.stop();
		} else {
			lenis.start();
		}
	});
});

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
