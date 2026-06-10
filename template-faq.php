<?php
/**
 * Template Name: Trang FAQ
 * @package pi
 */

get_header();

$faq_label   = get_field( 'legal_label' )        ?: 'CÂU HỎI THƯỜNG GẶP';
$faq_desc    = get_field( 'legal_description' )  ?: '';

// Parse blocks to build sidebar navigation
$blocks    = parse_blocks( get_the_content() );
$faq_groups = [];
foreach ( $blocks as $block ) {
    if ( $block['blockName'] === 'pi-blocks/block-faq' ) {
        $attrs = $block['attrs'];
        $letter = $attrs['groupLetter'] ?? '';
        $title  = $attrs['groupTitle']  ?? '';
        if ( $letter && $title ) {
            $faq_groups[] = [
                'letter' => $letter,
                'title'  => $title,
                'id'     => 'faq-group-' . strtolower( sanitize_title( $letter ) ),
            ];
        }
    }
}

$raw_content = get_the_content();
$content     = apply_filters( 'the_content', $raw_content );
?>

<main id="primary" class="site-main">

    <!-- ── Breadcrumb bar ────────────────────────────────────────────────── -->
    <div class="archive-breadcrumb-bar">
        <div class="container">
            <nav class="post-breadcrumb" aria-label="breadcrumb">
                <a href="<?= esc_url( home_url( '/' ) ) ?>"><?= esc_html__( 'Trang Chủ', 'pi' ) ?></a>
                <span class="post-breadcrumb__sep" aria-hidden="true">
                    <svg width="6" height="10" viewBox="0 0 6 10" fill="none" aria-hidden="true">
                        <path d="M1 1L5 5L1 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span aria-current="page"><?= esc_html( get_the_title() ) ?></span>
            </nav>
        </div>
    </div>

    <!-- ── Hero ─────────────────────────────────────────────────────────── -->
    <section class="legal-hero">
        <div class="container">
            <?php if ( $faq_label ) : ?>
                <span class="legal-hero__label"><?= esc_html( $faq_label ) ?></span>
            <?php endif; ?>
            <h1 class="legal-hero__title"><?= esc_html( get_the_title() ) ?></h1>
            <?php if ( $faq_desc ) : ?>
                <p class="legal-hero__desc"><?= esc_html( $faq_desc ) ?></p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ── Body: Sidebar + FAQ Content ──────────────────────────────────── -->
    <div class="legal-body-wrap">
        <div class="container legal-body-inner">

            <!-- Sidebar navigation -->
            <?php if ( ! empty( $faq_groups ) ) : ?>
            <aside class="legal-sidebar faq-sidebar" aria-label="Chủ đề câu hỏi">
                <div class="legal-sidebar__sticky">
                    <h2 class="faq-sidebar__title"><?= esc_html__( 'Chủ Đề Câu Hỏi', 'pi' ) ?></h2>
                    <nav class="faq-sidebar__nav">
                        <ol class="faq-sidebar__list">
                            <?php foreach ( $faq_groups as $group ) : ?>
                            <li class="faq-sidebar__item">
                                <a href="#<?= esc_attr( $group['id'] ) ?>"
                                   class="faq-sidebar__link js-faq-nav-link"
                                   data-target="<?= esc_attr( $group['id'] ) ?>">
                                    <span class="faq-sidebar__letter"><?= esc_html( $group['letter'] ) ?>.</span>
                                    <span class="faq-sidebar__label"><?= esc_html( $group['title'] ) ?></span>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                </div>
            </aside>
            <?php endif; ?>

            <!-- FAQ blocks output -->
            <div class="legal-content faq-content">
                <?= $content ?>
            </div>

        </div>
    </div>

    <?php
    $footer_cta = get_page_by_path( 'footer-blog', OBJECT, 'wp_block' );
    if ( $footer_cta ) {
        echo do_blocks( $footer_cta->post_content );
    }
    ?>

</main>

<script>
(function () {
    // ── Group toggle ──────────────────────────────────────────────────────
    document.querySelectorAll('.js-faq-group-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var group   = this.closest('.block-faq');
            var body    = group.querySelector('.faq-group__body');
            var isOpen  = group.classList.toggle('is-open');
            this.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            if (isOpen) {
                body.removeAttribute('hidden');
            } else {
                body.setAttribute('hidden', '');
            }
        });
    });

    // ── Sidebar active on scroll ──────────────────────────────────────────
    var navLinks = document.querySelectorAll('.js-faq-nav-link');
    if (!navLinks.length) return;

    var setActive = function (id) {
        navLinks.forEach(function (a) {
            a.classList.toggle('is-active', a.dataset.target === id);
        });
    };

    var groups = [];
    navLinks.forEach(function (a) {
        var el = document.getElementById(a.dataset.target);
        if (el) groups.push({ el: el, id: a.dataset.target });
    });

    if (groups.length) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    setActive(entry.target.id);
                }
            });
        }, { rootMargin: '0px 0px -60% 0px', threshold: 0 });

        groups.forEach(function (g) { io.observe(g.el); });
        setActive(groups[0].id);
    }

    // ── Smooth scroll for sidebar links ──────────────────────────────────
    navLinks.forEach(function (a) {
        a.addEventListener('click', function (e) {
            e.preventDefault();
            var target = document.getElementById(this.dataset.target);
            if (target) {
                var offset = 100;
                var top = target.getBoundingClientRect().top + window.scrollY - offset;
                window.scrollTo({ top: top, behavior: 'smooth' });
            }
        });
    });
})();
</script>

<?php get_footer(); ?>
