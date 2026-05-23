/**
 * Mega Menu — desktop panel open/close + services interactive columns.
 *
 * Desktop (≥ 992 px):
 *   Click .mega-toggle → opens fixed mega panel
 *   Click .mega-menu__close / backdrop / Escape → closes
 *
 * Mobile (< 992 px):
 *   Handled by initMobileSubmenus() in index.js via .dropdown-toggle / .open class.
 *   The .mega-menu panel is hidden on mobile via CSS; .mega-mobile-nav is shown instead.
 */

const LG = 992;

export default {
    init() {
        const header = document.getElementById('header');
        if (!header) return;

        const toggles = Array.from(document.querySelectorAll('.mega-toggle'));
        if (!toggles.length) return;

        // Shared backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'mega-backdrop';
        document.body.appendChild(backdrop);

        // Keep --mega-top CSS variable in sync with actual header bottom edge
        const updateTop = () => {
            const rect = header.getBoundingClientRect();
            document.documentElement.style.setProperty(
                '--mega-top',
                Math.max(rect.bottom, 0) + 'px'
            );
        };
        updateTop();
        window.addEventListener('scroll', updateTop, { passive: true });
        window.addEventListener('resize', updateTop, { passive: true });

        // ── Open / close helpers ──────────────────────────────────────

        const openPanel = (toggle) => {
            if (window.innerWidth < LG) return; // mobile handled by index.js

            const panel = getPanel(toggle);
            if (!panel) return;

            closeAll();

            toggle.setAttribute('aria-expanded', 'true');
            panel.removeAttribute('hidden');
            // Next frame so CSS transition fires after display kicks in
            requestAnimationFrame(() => panel.classList.add('is-open'));
            backdrop.classList.add('is-visible');

            if (window.lenis) window.lenis.stop();
        };

        const closePanel = (toggle) => {
            const panel = getPanel(toggle);
            if (!panel) return;

            toggle.setAttribute('aria-expanded', 'false');
            panel.classList.remove('is-open');
            panel.addEventListener(
                'transitionend',
                () => panel.setAttribute('hidden', ''),
                { once: true }
            );
        };

        const closeAll = () => {
            toggles.forEach((t) => {
                if (t.getAttribute('aria-expanded') === 'true') closePanel(t);
            });
            backdrop.classList.remove('is-visible');
            if (window.lenis) window.lenis.start();
        };

        const getPanel = (toggle) =>
            document.getElementById(toggle.getAttribute('aria-controls'));

        // ── Event bindings ────────────────────────────────────────────

        toggles.forEach((toggle) => {
            toggle.addEventListener('click', (e) => {
                if (window.innerWidth < LG) return;
                e.stopPropagation();

                const isOpen = toggle.getAttribute('aria-expanded') === 'true';
                isOpen ? closeAll() : openPanel(toggle);
            });
        });

        document.querySelectorAll('.mega-menu__close').forEach((btn) => {
            btn.addEventListener('click', closeAll);
        });

        backdrop.addEventListener('click', closeAll);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeAll();
        });

        // Close when clicking a regular (non-mega) nav link
        document.querySelectorAll('.primary-menu .nav-item:not(.has-mega-menu) .nav-link').forEach((link) => {
            link.addEventListener('click', closeAll);
        });

        // ── Services: hover category → swap sub-group ─────────────────

        document.querySelectorAll('.mega-menu--services').forEach((panel) => {
            const catItems = panel.querySelectorAll('.mega-menu__cat-item');
            const groups   = panel.querySelectorAll('.mega-menu__service-group');

            catItems.forEach((catItem) => {
                catItem.addEventListener('mouseenter', () => {
                    const targetId = catItem.dataset.target;

                    catItems.forEach((ci) => ci.classList.remove('is-active'));
                    groups.forEach((g) => g.classList.remove('is-active'));

                    catItem.classList.add('is-active');
                    const target = document.getElementById(targetId);
                    if (target) target.classList.add('is-active');
                });

                // Allow click on category link (pointer-events: none on <a> → click on <li>)
                catItem.addEventListener('click', (e) => {
                    const a = catItem.querySelector('a');
                    if (a && e.target !== a) {
                        window.location.href = a.href;
                    }
                });
            });
        });
    },
};
