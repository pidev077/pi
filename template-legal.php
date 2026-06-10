<?php
/**
 * Template Name: Trang Chính Sách / Legal
 * @package pi
 */

get_header();

$legal_updated = get_field( 'legal_updated_date' ) ?: '';

$raw_content = get_the_content();
$content     = pi_add_heading_ids( apply_filters( 'the_content', $raw_content ) );
$toc         = pi_extract_toc( $content );
?>

<main id="primary" class="site-main">

    <?php
    $extra = $legal_updated
        ? '<p class="page-hero__updated">'
            . esc_html__( 'Cập Nhật Lần Cuối:', 'pi' )
            . ' <span>' . esc_html( $legal_updated ) . '</span>'
            . '</p>'
        : '';

    get_template_part( 'template-parts/page-hero', null, [
        'extra'    => $extra,
        'modifier' => 'page-hero--legal',
    ] );
    ?>

    <!-- ── Body: Sidebar + Content ───────────────────────────────────────── -->
    <div class="legal-body-wrap">
        <div class="container legal-body-inner">

            <!-- Sidebar (TOC) — same structure as post-sidebar -->
            <?php if ( ! empty( $toc ) ) : ?>
            <aside class="legal-sidebar" aria-label="Mục lục">
                <div class="legal-sidebar__sticky">
                    <div class="post-toc js-post-toc">
                        <h4 class="post-toc__title">
                            <span class="post-toc__title-text"><?= esc_html__( 'Mục Lục', 'pi' ) ?></span>
                            <button type="button" class="post-toc__toggle js-toc-toggle"
                                    aria-label="<?= esc_attr__( 'Mục lục', 'pi' ) ?>">
                                <svg class="post-toc__icon--plus" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 5V19M5 12H19" stroke="#978B7B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <svg class="post-toc__icon--minus" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M5 12H19" stroke="#978B7B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </h4>
                        <ol class="post-toc__list">
                            <?php foreach ( $toc as $item ) : ?>
                            <li class="post-toc__item post-toc__item--h<?= $item['level'] ?>">
                                <a href="#<?= esc_attr( $item['id'] ) ?>"
                                   class="post-toc__link js-toc-link"
                                   data-target="<?= esc_attr( $item['id'] ) ?>">
                                    <?= esc_html( $item['text'] ) ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                </div>
            </aside>
            <?php endif; ?>

            <!-- Main content -->
            <article class="legal-content entry-content js-post-content" id="legal-body">
                <?= $content ?>
            </article>

        </div>
    </div>

    <?php
    $footer_cta = get_page_by_path( 'footer-blog', OBJECT, 'wp_block' );
    if ( $footer_cta ) {
        echo do_blocks( $footer_cta->post_content );
    }
    ?>

</main>

<?php get_footer(); ?>
