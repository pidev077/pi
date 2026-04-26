export default {
	init() {
		const header = document.querySelector("header");
		if (!header) return;

		let lastScrollPosition = window.pageYOffset;
		const SCROLL_DELTA = 5;

		window.addEventListener("scroll", () => {
			let currentScrollPosition = Math.max(0, window.pageYOffset);

			if (currentScrollPosition <= 0) {
				header.classList.remove("hide");
				document.body.classList.add("scroll-up");
				lastScrollPosition = 0;
				return;
			}

			if (
				Math.abs(currentScrollPosition - lastScrollPosition) > SCROLL_DELTA
			) {
				if (currentScrollPosition > lastScrollPosition) {
					header.classList.add("hide");
					document.body.classList.remove("scroll-up");
				} else {
					header.classList.remove("hide");
					document.body.classList.add("scroll-up");
				}
				lastScrollPosition = currentScrollPosition;
			}

			if (currentScrollPosition > 100) {
				header.classList.add("scrolled");
			} else {
				header.classList.remove("scrolled");
			}
		});
	},
};
