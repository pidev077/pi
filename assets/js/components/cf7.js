export default {
	init() {
		const buildDropdown = (select) => {
			if (select.dataset.cf7SdBound) return;
			select.dataset.cf7SdBound = "1";

			const opts = [...select.options];
			const placeholder = opts.find((o) => o.value === "")?.text || "Vui lòng chọn";

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
			}, 100);
		});
	},
};
