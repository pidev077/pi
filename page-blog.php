<?php
/**
 * Template Name: Blog
 * @package pi
 */

get_header();

// Set up page post context so page-hero.php can read ACF fields via get_field()
if ( have_posts() ) {
    the_post();
}

$categories = get_categories([
    'hide_empty' => true,
    'orderby'    => 'term_id',
    'order'      => 'ASC',
    'exclude'    => get_option('default_category'),
]);
usort($categories, function($a, $b) {
    $oa = (int)(get_field('cat_sort_order', 'category_' . $a->term_id) ?: 10);
    $ob = (int)(get_field('cat_sort_order', 'category_' . $b->term_id) ?: 10);
    return $oa - $ob;
});
?>

<main id="primary" class="site-main blog-page">

    <!-- ── Page Hero — handled by the shared template part (ACF-driven) ── -->
    <?php get_template_part( 'template-parts/page-hero' ); ?>

    <!-- ── Category sticky nav ──────────────────────────────────────── -->
    <nav class="blog-cat-nav" id="blog-cat-nav" aria-label="<?php esc_attr_e('Danh mục bài viết', 'pi'); ?>">
        <div class="container">
            <button class="blog-cat-nav__trigger" aria-expanded="false" aria-controls="blog-cat-nav-list">
                <span class="blog-cat-nav__trigger-label"><?php esc_html_e('CHỦ ĐỀ:', 'pi'); ?> <strong><?php esc_html_e('Tất Cả', 'pi'); ?></strong></span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <ul class="blog-cat-nav__list" id="blog-cat-nav-list" role="list">

                <!-- "Tất Cả" tab (no number) -->
                <li class="blog-cat-nav__item">
                    <a href="#tat-ca" class="blog-cat-nav__link is-active" data-target="tat-ca">
                        <span class="blog-cat-nav__name"><?php esc_html_e('Tất Cả', 'pi'); ?></span>
                    </a>
                </li>

                <?php
                $nav_index = 0;
                foreach ($categories as $cat):
                    $nav_index++;
                ?>
                <li class="blog-cat-nav__item">
                    <a href="#cat-<?= esc_attr($cat->slug) ?>"
                       class="blog-cat-nav__link"
                       data-target="cat-<?= esc_attr($cat->slug) ?>">
                        <span class="blog-cat-nav__num"><?= str_pad($nav_index, 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="blog-cat-nav__name"><?= esc_html($cat->name) ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </nav>

    <!-- ── "Tất Cả" — 5 latest posts grid ──────────────────────────── -->
    <?php
    $all_posts = get_posts([
        'posts_per_page' => 5,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
    if (!empty($all_posts)):
    ?>
    <section class="blog-cat-section blog-all-section" id="tat-ca">
        <div class="container">
            <div class="blog-all-grid">
                <?php foreach ($all_posts as $i => $post_item):
                    $is_featured = ($i === 2);
                ?>
                <div class="blog-all-grid__item<?= $is_featured ? ' blog-all-grid__item--featured' : '' ?>">
                    <?php pi_blog_card($post_item, false, $is_featured); ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ── Category carousel sections ──────────────────────────────── -->
    <?php
    $cat_index = 0;
    foreach ($categories as $cat):
        $posts = get_posts([
            'category'       => $cat->term_id,
            'posts_per_page' => 10,
            'post_status'    => 'publish',
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);
        if (empty($posts)) continue;
        $cat_index++;
    ?>
    <section class="blog-cat-section" id="cat-<?= esc_attr($cat->slug) ?>">
        <div class="container">
            <div class="blog-cat-section__header">
                <div class="blog-cat-section__heading">
                    <span class="blog-cat-section__num"><?= str_pad($cat_index, 2, '0', STR_PAD_LEFT) ?></span>
                    <h2 class="blog-cat-section__title"><?= esc_html($cat->name) ?></h2>
                </div>
                <div class="blog-cat-section__actions">
                    <a href="<?= esc_url(get_category_link($cat->term_id)) ?>" class="blog-cat-section__more">
                        <?php esc_html_e('Xem tất cả', 'pi'); ?>
                    </a>
                </div>
            </div>
            <div class="blog-cat-section__nav">
                <button class="blog-swiper-btn blog-swiper-prev" aria-label="<?php esc_attr_e('Bài trước', 'pi'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
<path d="M8.57143 6C8.57143 6.6095 7.96932 7.51964 7.35982 8.28357C6.57618 9.26929 5.63975 10.1293 4.56614 10.7856C3.76114 11.2777 2.78529 11.75 2 11.75M2 11.75C2.78529 11.75 3.76196 12.2223 4.56614 12.7144C5.63975 13.3715 6.57618 14.2315 7.35982 15.2156C7.96932 15.9804 8.57143 16.8921 8.57143 17.5M2 11.75H21.7143" stroke="#27211C" stroke-width="1.5"/>
</svg>
                </button>
                <button class="blog-swiper-btn blog-swiper-next" aria-label="<?php esc_attr_e('Bài tiếp', 'pi'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
<path d="M15.1429 6C15.1429 6.6095 15.745 7.51964 16.3545 8.28357C17.1381 9.26929 18.0745 10.1293 19.1481 10.7856C19.9531 11.2777 20.929 11.75 21.7143 11.75M21.7143 11.75C20.929 11.75 19.9523 12.2223 19.1481 12.7144C18.0745 13.3715 17.1381 14.2315 16.3545 15.2156C15.745 15.9804 15.1429 16.8921 15.1429 17.5M21.7143 11.75H2" stroke="#27211C" stroke-width="1.5"/>
</svg>
                </button>
            </div>
        </div>

        <div class="container blog-swiper-wrap">
            <div class="swiper blog-swiper js-blog-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($posts as $post_item): ?>
                    <div class="swiper-slide">
                        <?php pi_blog_card($post_item, false, false); ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endforeach; ?>

    <?php if ( get_the_content() ) : ?>
    <div class="blog-page-content">
        <?php the_content(); ?>
    </div>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
