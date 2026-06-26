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
				document.querySelectorAll(".cf7-custom-select.is-open").forEach((other) => {
					if (other === dd) return;
					other.classList.remove("is-open");
					other.querySelector(".cf7-custom-select__panel").style.display = "none";
				});
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

			// Ẩn native select — giữ 1x1px (không phải 0x0) để trình duyệt vẫn
			// focus/định vị được khi cần, tránh submit bị HTML5 validation chặn
			// âm thầm (field 0x0 không hiện được bong bóng cảnh báo "required").
			select.style.cssText =
				"position:absolute;opacity:0;width:1px;height:1px;pointer-events:none;overflow:hidden;clip-path:inset(50%);";

			// Ẩn wrap nhưng vẫn giữ 1px để không đè kích thước select xuống 0
			const wrap = select.closest(".wpcf7-form-control-wrap");
			if (wrap) wrap.style.cssText = "height:1px;overflow:hidden;display:block;";

			// Insert dropdown SAU wrap
			const anchor = wrap || select;
			anchor.insertAdjacentElement("afterend", dd);
		};

		const buildDateTimePicker = (zone) => {
			const input = zone.querySelector("input.consult-datetime-value");
			if (!input || input.dataset.cf7DtBound) return;
			input.dataset.cf7DtBound = "1";

			const SLOTS = ["09:00 - 11:00", "11:00 - 13:00", "13:00 - 15:00", "15:00 - 17:00", "17:00 - 19:00", "19:00 - 21:00"];
			const WEEKDAYS = ["T2", "T3", "T4", "T5", "T6", "T7", "CN"];
			const placeholder = zone.closest("[data-placeholder]")?.dataset.placeholder || "Chọn thời gian tư vấn mong muốn";
			const pad = (n) => String(n).padStart(2, "0");

			const today = new Date();
			today.setHours(0, 0, 0, 0);

			let view = new Date(today.getFullYear(), today.getMonth(), 1);
			let selected = null; // { y, m, d }
			let slot = null;

			const dd = document.createElement("div");
			dd.className = "cf7-custom-select consult-dt-picker";

			const trigger = document.createElement("button");
			trigger.type = "button";
			trigger.className = "cf7-custom-select__trigger";
			trigger.innerHTML = `<span class="cf7-custom-select__label">${placeholder}</span>`;

			const panel = document.createElement("div");
			panel.className = "cf7-custom-select__panel";
			panel.style.display = "none";

			const renderDays = () => {
				const y = view.getFullYear();
				const m = view.getMonth();
				const firstWeekday = (new Date(y, m, 1).getDay() + 6) % 7;
				const totalDays = new Date(y, m + 1, 0).getDate();
				const prevTotalDays = new Date(y, m, 0).getDate();

				const cells = [];
				for (let i = 0; i < firstWeekday; i++) {
					cells.push({ d: prevTotalDays - firstWeekday + i + 1, inMonth: false });
				}
				for (let d = 1; d <= totalDays; d++) cells.push({ d, inMonth: true });
				let next = 1;
				while (cells.length < 42) cells.push({ d: next++, inMonth: false });

				return cells
					.map(({ d, inMonth }) => {
						const cellDate = inMonth ? new Date(y, m, d) : null;
						const isPast = inMonth && cellDate < today;
						const isToday = inMonth && cellDate.getTime() === today.getTime();
						const isSelected = inMonth && selected && selected.y === y && selected.m === m && selected.d === d;
						const disabled = !inMonth || isPast;
						const cls = [
							"consult-dt-picker__day",
							!inMonth && "is-muted",
							isPast && "is-disabled",
							isToday && "is-today",
							isSelected && "is-selected",
						].filter(Boolean).join(" ");
						return `<button type="button" class="${cls}" ${disabled ? "disabled" : ""} data-y="${y}" data-m="${m}" data-d="${d}">${d}</button>`;
					})
					.join("");
			};

			const render = () => {
				const y = view.getFullYear();
				const m = view.getMonth();
				panel.innerHTML = `
					<div class="consult-dt-picker__cal-head">
						<div class="consult-dt-picker__cal-title">CHỌN NGÀY</div>
						<div class="consult-dt-picker__nav-row">
							<button type="button" class="consult-dt-picker__nav" data-nav="year-1">&laquo;</button>
							<button type="button" class="consult-dt-picker__nav" data-nav="month-1">&lsaquo;</button>
							<span class="consult-dt-picker__title">Tháng ${m + 1}, ${y}</span>
							<button type="button" class="consult-dt-picker__nav" data-nav="month1">&rsaquo;</button>
							<button type="button" class="consult-dt-picker__nav" data-nav="year1">&raquo;</button>
						</div>
					</div>
					<div class="consult-dt-picker__weekdays">${WEEKDAYS.map((w) => `<span>${w}</span>`).join("")}</div>
					<div class="consult-dt-picker__days">${renderDays()}</div>
					<div class="consult-dt-picker__slots">
						<div class="consult-dt-picker__slots-label">CHỌN KHUNG GIỜ ƯU TIÊN</div>
						<div class="consult-dt-picker__slots-grid">
							${SLOTS.map((s) => `<button type="button" class="consult-dt-picker__slot${slot === s ? " is-active" : ""}" data-slot="${s}">${s}</button>`).join("")}
						</div>
					</div>
					<div class="consult-dt-picker__footer">
						<button type="button" class="consult-dt-picker__reset">Đặt lại</button>
						<button type="button" class="consult-dt-picker__apply" ${selected && slot ? "" : "disabled"}>Xác nhận</button>
					</div>
				`;
			};

			const applyValue = () => {
				if (!selected || !slot) return;
				const formatted = `${pad(selected.d)}/${pad(selected.m + 1)}/${selected.y} - ${slot}`;
				input.value = formatted;
				input.dispatchEvent(new Event("input", { bubbles: true }));
				input.dispatchEvent(new Event("change", { bubbles: true }));
				trigger.querySelector(".cf7-custom-select__label").textContent = `${pad(selected.d)}/${pad(selected.m + 1)}/${selected.y}, ${slot}`;
				dd.classList.add("has-value");
				close();
			};

			const resetState = () => {
				selected = null;
				slot = null;
				view = new Date(today.getFullYear(), today.getMonth(), 1);
				input.value = "";
				input.dispatchEvent(new Event("input", { bubbles: true }));
				trigger.querySelector(".cf7-custom-select__label").textContent = placeholder;
				dd.classList.remove("has-value", "is-open");
				render();
			};

			panel.addEventListener("click", (e) => {
				const navBtn = e.target.closest("[data-nav]");
				if (navBtn) {
					const nav = navBtn.dataset.nav;
					if (nav === "year-1") view.setFullYear(view.getFullYear() - 1);
					if (nav === "year1") view.setFullYear(view.getFullYear() + 1);
					if (nav === "month-1") view.setMonth(view.getMonth() - 1);
					if (nav === "month1") view.setMonth(view.getMonth() + 1);
					render();
					return;
				}
				const dayBtn = e.target.closest(".consult-dt-picker__day");
				if (dayBtn && !dayBtn.disabled) {
					selected = { y: +dayBtn.dataset.y, m: +dayBtn.dataset.m, d: +dayBtn.dataset.d };
					render();
					return;
				}
				const slotBtn = e.target.closest(".consult-dt-picker__slot");
				if (slotBtn) {
					slot = slot === slotBtn.dataset.slot ? null : slotBtn.dataset.slot;
					render();
					return;
				}
				if (e.target.closest(".consult-dt-picker__apply")) { applyValue(); return; }
				if (e.target.closest(".consult-dt-picker__reset")) { resetState(); return; }
			});

			dd.appendChild(trigger);
			dd.appendChild(panel);

			const open = () => {
				document.querySelectorAll(".cf7-custom-select.is-open").forEach((other) => {
					if (other === dd) return;
					other.classList.remove("is-open");
					other.querySelector(".cf7-custom-select__panel").style.display = "none";
				});
				render();
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
			document.addEventListener("wpcf7mailsent", () => setTimeout(resetState, 100));

			// Ẩn input thật, chỉ giữ giá trị cho CF7 validate/submit — giữ 1x1px
			// (không phải 0x0) để tránh submit bị HTML5 validation chặn âm thầm.
			input.style.cssText =
				"position:absolute;opacity:0;width:1px;height:1px;pointer-events:none;overflow:hidden;clip-path:inset(50%);";
			const wrap = input.closest(".wpcf7-form-control-wrap");
			if (wrap) wrap.style.cssText = "height:1px;overflow:hidden;display:block;";

			zone.appendChild(dd);
		};

		const initAll = () => {
			document.querySelectorAll(".wpcf7 select").forEach(buildDropdown);
			document.querySelectorAll(".consult-datetime-picker").forEach(buildDateTimePicker);
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

		// ── Fallback: nếu AJAX của CF7 bị treo (mạng lỗi, server không phản
		// hồi...), input submit có thể bị kẹt ở trạng thái disabled vĩnh viễn
		// (CSS .cf7-submit:has(:disabled) khoá pointer-events trên cả vùng nút).
		// Tự gỡ disabled sau 15s nếu CF7 chưa tự xử lý xong.
		document.querySelectorAll(".wpcf7-form").forEach((form) => {
			const submit = form.querySelector("input[type='submit']");
			if (!submit) return;
			form.addEventListener("submit", () => {
				setTimeout(() => {
					if (submit.disabled) submit.disabled = false;
				}, 15000);
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
					const placeholder =
						sel?.options[0]?.text ||
						sel?.closest("[data-placeholder]")?.dataset.placeholder ||
						"Vui lòng chọn";
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
