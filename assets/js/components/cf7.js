export default {
	init() {
		const PLACEHOLDER_COLOR = "#D5CCBE";
		const TEXT_COLOR = "#27211C";

		const syncColor = (select) => {
			select.style.color = select.value === "" ? PLACEHOLDER_COLOR : TEXT_COLOR;
		};

		const attachSelects = () => {
			document.querySelectorAll(".wpcf7 select").forEach((sel) => {
				syncColor(sel);
				if (!sel.dataset.cf7ColorBound) {
					sel.dataset.cf7ColorBound = "1";
					sel.addEventListener("change", () => syncColor(sel));
				}
			});
		};

		attachSelects();

		// Re-run after CF7 resets the form on successful submit
		document.addEventListener("wpcf7mailsent", () => setTimeout(attachSelects, 50));
	},
};
