import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';

const Blog = {
    nav: null,
    links: [],
    sections: [],

    init() {
        // Blog listing page
        if (document.querySelector('.blog-page')) {
            this.nav = document.getElementById('blog-cat-nav');
            this.links = this.nav ? Array.from(this.nav.querySelectorAll('.blog-cat-nav__link')) : [];
            this.sections = Array.from(document.querySelectorAll('.blog-cat-section'));
            this.setHeaderOffset();
            this.initSwipers();
            this.initCatNav();
            this.initPinObserver();
            this.initMobileTrigger();
        }

        // Single post page
        this.initToc();
        this.initRelatedSwiper();
        this.initCopyLink();
        this.initMobileToc();
    },

    setHeaderOffset() {
        const header = document.getElementById('header');
        if (header && this.nav) {
            const update = () => {
                document.documentElement.style.setProperty('--header-h', header.offsetHeight + 'px');
                this.sections.forEach((s) => {
                    s.style.scrollMarginTop = (this.nav.offsetHeight + 8) + 'px';
                });
            };
            update();
            window.addEventListener('resize', update, { passive: true });
        }
    },

    initSwipers() {
        document.querySelectorAll('.js-blog-swiper').forEach((el) => {
            const section = el.closest('.blog-cat-section');
            new Swiper(el, {
                modules: [Navigation],
                slidesPerView: 1.15,
                spaceBetween: 16,
                navigation: {
                    nextEl: section ? section.querySelector('.blog-swiper-next') : null,
                    prevEl: section ? section.querySelector('.blog-swiper-prev') : null,
                },
                breakpoints: {
                    576: {
                        slidesPerView: 1.6,
                        spaceBetween: 20,
                    },
                    768: {
                        slidesPerView: 2.2,
                        spaceBetween: 20,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 24,
                    },
                    1280: {
                        slidesPerView: 3,
                        spaceBetween: 28,
                    },
                },
            });
        });
    },

    initCatNav() {
        const { nav, links } = this;
        if (!nav || !links.length) return;

        links.forEach((link) => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.getElementById(link.dataset.target);
                if (!target) return;

                links.forEach((l) => l.classList.remove('is-active'));
                link.classList.add('is-active');
                this.scrollNavToLink(link);

                const navH = nav.offsetHeight;
                if (window.lenis) {
                    window.lenis.scrollTo(target, { offset: -(navH + 16) });
                } else {
                    const top = target.getBoundingClientRect().top + window.scrollY - navH - 16;
                    window.scrollTo({ top, behavior: 'smooth' });
                }
            });
        });

        if (!this.sections.length) return;

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    const id = entry.target.id;
                    const link = nav.querySelector(`[data-target="${id}"]`);
                    if (!link) return;
                    links.forEach((l) => l.classList.remove('is-active'));
                    link.classList.add('is-active');
                    this.scrollNavToLink(link);
                    const trigger = nav.querySelector('.blog-cat-nav__trigger');
                    if (trigger) this.updateMobileLabel(trigger, link);
                });
            },
            { rootMargin: '-25% 0px -75% 0px', threshold: 0 }
        );

        this.sections.forEach((s) => observer.observe(s));
    },

    scrollNavToLink(link) {
        link.scrollIntoView({ block: 'nearest', inline: 'center' });
    },

    initMobileTrigger() {
        const { nav, links } = this;
        if (!nav) return;
        const trigger = nav.querySelector('.blog-cat-nav__trigger');
        const list = nav.querySelector('.blog-cat-nav__list');
        if (!trigger || !list) return;

        trigger.addEventListener('click', () => {
            const open = list.classList.toggle('is-open');
            trigger.setAttribute('aria-expanded', open);
        });

        links.forEach((link) => {
            link.addEventListener('click', () => {
                list.classList.remove('is-open');
                trigger.setAttribute('aria-expanded', 'false');
                this.updateMobileLabel(trigger, link);
            });
        });
    },

    updateMobileLabel(trigger, activeLink) {
        const name = activeLink.querySelector('.blog-cat-nav__name');
        if (!name) return;
        const strong = trigger.querySelector('strong');
        if (strong) strong.textContent = name.textContent;
    },

    initPinObserver() {
        const { nav } = this;
        if (!nav) return;
        const hero = document.querySelector('.blog-hero');
        if (!hero) return;

        const observer = new IntersectionObserver(
            ([entry]) => nav.classList.toggle('is-pinned', !entry.isIntersecting),
            { threshold: 0 }
        );
        observer.observe(hero);
    },

    // ── Single post: Table of Contents ──────────────────────────────────────
    initToc() {
        const toc     = document.querySelector('.js-post-toc');
        const content = document.querySelector('.js-post-content');
        if (!toc || !content) return;

        const headings = Array.from(content.querySelectorAll('h2[id], h3[id]'));
        const tocLinks = Array.from(toc.querySelectorAll('.post-toc__link'));
        if (!headings.length || !tocLinks.length) return;

        // Smooth-scroll on TOC link click
        tocLinks.forEach((link) => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const id     = link.getAttribute('href').slice(1);
                const target = document.getElementById(id);
                if (!target) return;

                const offset = -(document.getElementById('header')?.offsetHeight || 80) - 24;
                if (window.lenis) {
                    window.lenis.scrollTo(target, { offset });
                } else {
                    const top = target.getBoundingClientRect().top + window.scrollY + offset;
                    window.scrollTo({ top, behavior: 'smooth' });
                }
            });
        });

        // Highlight active TOC entry on scroll
        const activate = (id) => {
            tocLinks.forEach((l) => l.classList.remove('is-active'));
            const active = toc.querySelector(`.post-toc__link[href="#${CSS.escape(id)}"]`);
            if (active) active.classList.add('is-active');
        };

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) activate(entry.target.id);
                });
            },
            { rootMargin: '-10% 0px -82% 0px', threshold: 0 }
        );

        headings.forEach((h) => observer.observe(h));
    },

    // ── Single post: Mobile floating TOC ────────────────────────────────────
    initMobileToc() {
        const mq = window.matchMedia('(max-width: 991px)');
        if (!mq.matches) return;

        const toc = document.querySelector('.js-post-toc');
        const btn = document.querySelector('.js-toc-toggle');
        if (!toc || !btn) return;

        btn.addEventListener('click', () => {
            toc.classList.toggle('is-expanded');
        });

        // Close when clicking a TOC link on mobile
        toc.querySelectorAll('.post-toc__link').forEach((link) => {
            link.addEventListener('click', () => {
                toc.classList.remove('is-expanded');
            });
        });
    },

    // ── Single post: Copy link ───────────────────────────────────────────────
    initCopyLink() {
        document.querySelectorAll('.js-copy-link').forEach((btn) => {
            btn.addEventListener('click', () => {
                const url = btn.dataset.url || window.location.href;
                navigator.clipboard.writeText(url).then(() => {
                    btn.classList.add('is-copied');
                    setTimeout(() => btn.classList.remove('is-copied'), 2000);
                });
            });
        });
    },

    // ── Single post: Related posts swiper ───────────────────────────────────
    initRelatedSwiper() {
        const el = document.querySelector('.js-related-swiper');
        if (!el) return;

        const section = el.closest('.post-related');
        new Swiper(el, {
            modules: [Navigation],
            slidesPerView: 1.2,
            spaceBetween: 16,
            navigation: {
                nextEl: section ? section.querySelector('.post-related-next') : null,
                prevEl: section ? section.querySelector('.post-related-prev') : null,
            },
            breakpoints: {
                576: { slidesPerView: 1.6, spaceBetween: 20 },
                768: { slidesPerView: 2.2, spaceBetween: 20 },
                1024: { slidesPerView: 3,   spaceBetween: 24 },
            },
        });
    },
};

export default Blog;
