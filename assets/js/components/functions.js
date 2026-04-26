import Swiper from "swiper";
import {
	Navigation,
	Pagination,
	EffectFade,
	Autoplay,
	EffectCube,
	EffectCoverflow,
	EffectFlip,
} from "swiper/modules";
export default {
	init() {
		flipCaseRelatedCarousel();
	},
};
function flipCaseRelatedCarousel() {
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
