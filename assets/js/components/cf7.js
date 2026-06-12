export default {
	init() {
		const buildDropdown = (select) => {
			if (select.dataset.cf7SdBound) return;
			select.dataset.cf7SdBound = "1";

			const opts = [...select.options];
			const placeholder =
				opts.find((o) => o.value === "")?.text ||
				select.closest("[data-placeholder]")?.dataset.placeholder ||
				"Vui lòng chọn";

			const dd = document.createElement("div");
			dd.className = "cf7-custom-select";

			const trigger = document.createElement("button");
			trigger.type = "button";
			trigger.className = "cf7-custom-select__trigger";
			trigger.innerHTML = `<span class="cf7-custom-select__label">${placeholder}</span>`;

			const panel = document.createElement("div");
			panel.className = "cf7-custom-select__panel";
			panel.style.display = "none"; // JS kiểm soát, không phụ thuộc CSS

			opts
				.filter((o) => o.value !== "")
				.forEach((opt) => {
					const lbl = document.createElement("label");
					lbl.className = "cf7-custom-select__item";

					const radio = document.createElement("input");
					radio.type = "radio";
					radio.name = `${select.name}--rd`;
					radio.value = opt.value;

					const txt = document.createElement("span");
					txt.textContent = opt.text;

					lbl.appendChild(radio);
					lbl.appendChild(txt);
					panel.appendChild(lbl);

					radio.addEventListener("change", () => {
						select.value = opt.value;
						select.dispatchEvent(new Event("change", { bubbles: true }));
						trigger.querySelector(".cf7-custom-select__label").textContent = opt.text;
						dd.classList.add("has-value");
						close();
						panel.querySelectorAll(".cf7-custom-select__item").forEach((el) => {
							el.classList.toggle("is-checked", !!el.querySelector("input")?.checked);
						});
					});
				});

			dd.appendChild(trigger);
			dd.appendChild(panel);

			const open = () => {
				panel.style.display = "block";
				dd.classList.add("is-open");
			};
			const close = () => {
				panel.style.display = "none";
				dd.classList.remove("is-open");
			};

			trigger.addEventListener("click", (e) => {
				e.stopPropagation();
				panel.style.display === "block" ? close() : open();
			});

			document.addEventListener("click", close);
			dd.addEventListener("click", (e) => e.stopPropagation());
			document.addEventListener("keydown", (e) => {
				if (e.key === "Escape") close();
			});

			// Ẩn native select
			select.style.cssText =
				"position:absolute;opacity:0;width:0;height:0;pointer-events:none;overflow:hidden;";

			// Ẩn wrap (chỉ chứa select ẩn, không cần chiếm không gian)
			const wrap = select.closest(".wpcf7-form-control-wrap");
			if (wrap) wrap.style.cssText = "height:0;overflow:hidden;display:block;";

			// Insert dropdown SAU wrap
			const anchor = wrap || select;
			anchor.insertAdjacentElement("afterend", dd);
		};

		const initAll = () => {
			document.querySelectorAll(".wpcf7 select").forEach(buildDropdown);
		};

		initAll();
		setTimeout(initAll, 600);

		// ── Submit button: click vùng nền vàng ngoài input cũng trigger submit ──
		document.querySelectorAll(".consult-col--right .cf7-submit").forEach((p) => {
			const input = p.querySelector("input[type='submit']");
			if (!input) return;
			p.addEventListener("click", (e) => {
				if (e.target !== input) input.click();
			});
		});

		// ── Upload zone ───────────────────────────────────────────────────────
		document.querySelectorAll(".consult-upload-zone").forEach((zone) => {
			const input = zone.querySelector("input[type='file']");
			if (!input) return;

			// Click anywhere on zone → open file dialog
			zone.addEventListener("click", () => input.click());

			// Show file name after selection
			input.addEventListener("change", () => {
				const ui = zone.querySelector(".consult-upload-zone__title");
				if (!ui) return;
				if (input.files.length) {
					ui.textContent = Array.from(input.files).map((f) => f.name).join(", ");
				}
			});
		});

		// ── Character counter ──────────────────────────────────────────────────
		document.querySelectorAll(".cf7-has-counter").forEach((field) => {
			const textarea = field.querySelector("textarea");
			const counter  = field.querySelector(".cf7-char-counter");
			if (!textarea || !counter) return;
			const max = parseInt(counter.dataset.max || "1000", 10);
			textarea.setAttribute("maxlength", max);
			const update = () => { counter.textContent = `${textarea.value.length} / ${max}`; };
			textarea.addEventListener("input", update);
			update();
		});

		document.addEventListener("wpcf7mailsent", () => {
			setTimeout(() => {
				document.querySelectorAll(".cf7-custom-select").forEach((dd) => {
					const wrap = dd.previousElementSibling?.closest?.(".wpcf7-form-control-wrap");
					const sel = wrap?.querySelector("select");
					const placeholder = sel?.options[0]?.text || "Vui lòng chọn";
					dd.querySelector(".cf7-custom-select__label").textContent = placeholder;
					dd.classList.remove("has-value", "is-open");
					dd.querySelectorAll(".cf7-custom-select__item").forEach((el) => {
						el.classList.remove("is-checked");
						const r = el.querySelector("input[type='radio']");
						if (r) r.checked = false;
					});
					dd.querySelector(".cf7-custom-select__panel").style.display = "none";
				});

				// Reset upload zones
				document.querySelectorAll(".consult-upload-zone").forEach((zone) => {
					const ui = zone.querySelector(".consult-upload-zone__title");
					if (ui) ui.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg> Kéo thả hoặc nhấp để tải lên`;
				});

				// Reset char counters
				document.querySelectorAll(".cf7-char-counter").forEach((counter) => {
					const max = parseInt(counter.dataset.max || "1000", 10);
					counter.textContent = `0 / ${max}`;
				});
			}, 100);
		});
	},
};
