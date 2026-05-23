import Swiper from "swiper";
import {
	Navigation,
	Pagination,
	EffectFade,
	Autoplay,
	EffectCube,
	EffectCoverflow,
	EffectPi,
} from "swiper/modules";
export default {
	init() {
		piCaseRelatedCarousel();
		initScrollTop();
	},
};
function initScrollTop() {
	const btn = document.getElementById('scroll-top');
	if (!btn) return;

	window.addEventListener('scroll', () => {
		btn.classList.toggle('is-visible', window.scrollY > 300);
	}, { passive: true });

	btn.addEventListener('click', () => {
		if (window.lenis) {
			window.lenis.scrollTo(0, { duration: 1.2 });
		} else {
			window.scrollTo({ top: 0, behavior: 'smooth' });
		}
	});
}

function piCaseRelatedCarousel() {
	const el = document.querySelector(".case-related__carousel");

	if (!el) return;

	const swiper = new Swiper(el, {
		modules: [Pagination, Autoplay, EffectFade, Navigation],
		slidesPerView: 2,
		spaceBetween: 20,
		loop: false,
		keyboard: true,
		slideToClickedSlide: false,
		grabCursor: true,
		parallax: true,
		folowFinger: false,
		navigation: {
			nextEl: ".swiper-button-next",
			prevEl: ".swiper-button-prev",
		},
		breakpoints: {
			320: {
				slidesPerView: 1.2,
				spaceBetween: 10,
			},
			768: {
				slidesPerView: 2,
				spaceBetween: 15,
			},
			1024: {
				slidesPerView: 1.8,
				spaceBetween: 20,
			},
		},
	});
}
