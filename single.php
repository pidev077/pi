<?php
/**
 * Template for displaying single blog posts.
 * @package pi
 */

get_header();
?>

<main id="primary" class="site-main post-single">

<?php while (have_posts()) : the_post();

    $post_id   = get_the_ID();
    $tags      = get_the_tags();
    $cats      = get_the_category();

    $author_name   = get_the_author();
    $author_url    = get_author_posts_url( get_the_author_meta('ID') );
    $reviewer_name = get_field('reviewer_name');

    // Process content: add heading IDs then extract TOC
    $raw_content = get_the_content();
    $content     = pi_add_heading_ids(apply_filters('the_content', $raw_content));
    $toc         = pi_extract_toc($content);

    // Reading time
    $word_count = str_word_count(wp_strip_all_tags($raw_content));
    $read_time  = max(1, ceil($word_count / 200));

    // Blog page URL for breadcrumb
    $blog_url = get_permalink(get_option('page_for_posts')) ?: home_url('/blog/');

    // Related posts — prefer same category, fallback to latest
    $cat_ids = array_column($cats, 'term_id');
    $related = get_posts([
        'posts_per_page' => 4,
        'post__not_in'   => [$post_id],
        'category__in'   => $cat_ids ?: [0],
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
    if (count($related) < 4) {
        $related = get_posts([
            'posts_per_page' => 4,
            'post__not_in'   => [$post_id],
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);
    }

    // Sidebar latest posts
    $sidebar_latest = get_posts([
        'posts_per_page' => 5,
        'post__not_in'   => [$post_id],
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);

    // Sidebar related tags — all tags from posts in the same root category
    $root_cat_ids = [];
    foreach ($cats as $cat) {
        if ($cat->parent == 0) {
            $root_cat_ids[] = $cat->term_id;
        } else {
            $ancestors = get_ancestors($cat->term_id, 'category');
            $root_cat_ids[] = end($ancestors);
        }
    }
    $root_cat_ids  = array_unique($root_cat_ids);
    $sidebar_tags  = [];
    if (!empty($root_cat_ids)) {
        $root_post_ids = get_posts([
            'posts_per_page' => -1,
            'category__in'   => $root_cat_ids,
            'fields'         => 'ids',
        ]);
        if (!empty($root_post_ids)) {
            $sidebar_tags = wp_get_object_terms($root_post_ids, 'post_tag', [
                'orderby' => 'count',
                'order'   => 'DESC',
            ]);
        }
    }

?>

<!-- ── Post Header ──────────────────────────────────────────────── -->
<div class="post-single__header">
    <!-- Breadcrumb -->
    <div class="single-breadcrumb"> 
        <div class="container">         
            <nav class="post-breadcrumb" aria-label="<?php esc_attr_e('Đường dẫn', 'pi'); ?>">
                <a href="<?= esc_url(home_url('/')) ?>"><?php esc_html_e('Trang Chủ', 'pi'); ?></a>
                <span class="post-breadcrumb__sep" aria-hidden="true">
                    <svg width="6" height="10" viewBox="0 0 6 10" fill="none"><path d="M1 1l4 4-4 4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <a class="post-breadcrumb__item--blog" href="<?= esc_url($blog_url) ?>"><?php esc_html_e('Blog', 'pi'); ?></a>
                <span class="post-breadcrumb__ellipsis" aria-hidden="true">...</span>
                <span class="post-breadcrumb__sep" aria-hidden="true">
                    <svg width="6" height="10" viewBox="0 0 6 10" fill="none"><path d="M1 1l4 4-4 4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <span aria-current="page"><?= esc_html(get_the_title()) ?></span>
            </nav>
        </div>
    </div>
    <div class="post-info">
        <div class="container">

            <!-- Tag pills -->
            <?php $pills = !empty($tags) ? $tags : $cats; ?>
            <?php if (!empty($pills)): ?>
            <div class="post-single__pills">
                <?php foreach (array_slice($pills, 0, 4) as $pill):
                    $pill_url = !empty($tags)
                        ? get_tag_link($pill->term_id)
                        : get_category_link($pill->term_id);
                ?>
                <a href="<?= esc_url($pill_url) ?>" class="post-single__pill">
                    <?= esc_html($pill->name) ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Title -->
            <h1 class="post-single__title"><?php the_title(); ?></h1>

            <!-- Meta: author · date · reading time -->
            <div class="post-single__meta">
                <span class="post-single__meta-author">
                    <?php esc_html_e('Bởi', 'pi'); ?> <a href="<?= esc_url($author_url) ?>" class="post-single__meta-author-link"><strong><?= esc_html($author_name) ?></strong></a>
                </span>
                <span class="post-single__meta-sep" aria-hidden="true">•</span>
                <time class="post-single__meta-date" datetime="<?= get_the_date('c') ?>">
                    <?= pi_get_localized_date() ?>
                </time>
                <span class="post-single__meta-sep" aria-hidden="true">•</span>
                <span class="post-single__meta-read">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M12 7v5l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <?= $read_time ?> <?php esc_html_e('phút đọc', 'pi'); ?>
                </span>
            </div>

            <!-- Excerpt -->
            <?php $excerpt = get_the_excerpt(); if ($excerpt): ?>
            <p class="post-single__excerpt"><?= esc_html($excerpt) ?></p>
            <?php endif; ?>

            <!-- Medical reviewer badge -->
            <?php if ($reviewer_name): ?>
            <div class="post-single__reviewer">
                <div class="post-single__reviewer-info">
                    <span class="post-single__reviewer-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
<path d="M9 12.0005L11 14.0005L15 10.0005M5.00046 7.20046C5.00046 6.61698 5.23225 6.05741 5.64483 5.64483C6.05741 5.23225 6.61698 5.00046 7.20046 5.00046H8.20046C8.78136 5.00013 9.33855 4.77006 9.75046 4.36046L10.4505 3.66046C10.6549 3.45486 10.898 3.2917 11.1657 3.18037C11.4334 3.06903 11.7205 3.01172 12.0105 3.01172C12.3004 3.01172 12.5875 3.06903 12.8552 3.18037C13.1229 3.2917 13.366 3.45486 13.5705 3.66046L14.2705 4.36046C14.6825 4.77046 15.2405 5.00046 15.8205 5.00046H16.8205C17.4039 5.00046 17.9635 5.23225 18.3761 5.64483C18.7887 6.05741 19.0205 6.61698 19.0205 7.20046V8.20046C19.0205 8.78046 19.2505 9.33846 19.6605 9.75046L20.3605 10.4505C20.5661 10.6549 20.7292 10.898 20.8406 11.1657C20.9519 11.4334 21.0092 11.7205 21.0092 12.0105C21.0092 12.3004 20.9519 12.5875 20.8406 12.8552C20.7292 13.1229 20.5661 13.366 20.3605 13.5705L19.6605 14.2705C19.2509 14.6824 19.0208 15.2396 19.0205 15.8205V16.8205C19.0205 17.4039 18.7887 17.9635 18.3761 18.3761C17.9635 18.7887 17.4039 19.0205 16.8205 19.0205H15.8205C15.2396 19.0208 14.6824 19.2509 14.2705 19.6605L13.5705 20.3605C13.366 20.5661 13.1229 20.7292 12.8552 20.8406C12.5875 20.9519 12.3004 21.0092 12.0105 21.0092C11.7205 21.0092 11.4334 20.9519 11.1657 20.8406C10.898 20.7292 10.6549 20.5661 10.4505 20.3605L9.75046 19.6605C9.33855 19.2509 8.78136 19.0208 8.20046 19.0205H7.20046C6.61698 19.0205 6.05741 18.7887 5.64483 18.3761C5.23225 17.9635 5.00046 17.4039 5.00046 16.8205V15.8205C5.00013 15.2396 4.77006 14.6824 4.36046 14.2705L3.66046 13.5705C3.45486 13.366 3.2917 13.1229 3.18037 12.8552C3.06903 12.5875 3.01172 12.3004 3.01172 12.0105C3.01172 11.7205 3.06903 11.4334 3.18037 11.1657C3.2917 10.898 3.45486 10.6549 3.66046 10.4505L4.36046 9.75046C4.77006 9.33855 5.00013 8.78136 5.00046 8.20046V7.20046Z" stroke="#07B9F0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                        <?php esc_html_e('Được kiểm duyệt chuyên môn bởi', 'pi'); ?>
                    </span>
                    <strong class="post-single__reviewer-name"><?= esc_html($reviewer_name) ?></strong>
                </div>
            </div>
            <?php endif; ?>

        </div><!-- /.container -->
    </div>
    <!-- Featured image -->
    <?php if (has_post_thumbnail()): ?>
    <div class="post-single__featured-image">
            <?php the_post_thumbnail('full', ['loading' => 'eager', 'class' => 'post-single__featured-img']); ?>
    </div>
    <?php endif; ?>
</div><!-- /.post-single__header -->

<!-- ── Post Body: Sidebar + Content ───────────────────────────────── -->
<div class="post-single__body">
    <div class="container post-single__body-inner">

        <!-- Left sidebar -->
        <aside class="post-sidebar" aria-label="<?php esc_attr_e('Nội dung phụ', 'pi'); ?>">
            <div class="post-sidebar__sticky">

                <!-- Table of Contents -->
                <?php if (!empty($toc)): ?>
                <div class="post-toc js-post-toc">
                    <h4 class="post-toc__title">
                        <span class="post-toc__title-text"><?php esc_html_e('Mục Lục Bài Viết', 'pi'); ?></span>
                        <button type="button" class="post-toc__toggle js-toc-toggle" aria-label="<?php esc_attr_e('Mục lục', 'pi'); ?>">
                            <svg class="post-toc__icon--plus" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5V19M5 12H19" stroke="#978B7B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <svg class="post-toc__icon--minus" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M5 12H19" stroke="#978B7B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </h4>
                    <ol class="post-toc__list">
                        <?php foreach ($toc as $item): ?>
                        <li class="post-toc__item post-toc__item--h<?= $item['level'] ?>">
                            <a href="#<?= esc_attr($item['id']) ?>" class="post-toc__link">
                                <?= esc_html($item['text']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
                <?php endif; ?>

                <!-- Latest posts -->
                <?php if (!empty($sidebar_latest)): ?>
                <div class="post-sidebar__section">
                    <h4 class="post-sidebar__section-title"><?php esc_html_e('Bài Viết Mới Nhất', 'pi'); ?></h4>
                    <ul class="post-sidebar__list">
                        <?php foreach ($sidebar_latest as $i => $lp): ?>
                        <li>
                            <a href="<?= esc_url(get_permalink($lp->ID)) ?>" class="post-sidebar__post-link">
                                <span class="post-sidebar__post-num"><?= $i + 1 ?></span>
                                <?php if (has_post_thumbnail($lp->ID)): ?>
                                <div class="post-sidebar__post-thumb">
                                    <?= get_the_post_thumbnail($lp->ID, [64, 64]) ?>
                                </div>
                                <?php endif; ?>
                                <span class="post-sidebar__post-title"><?= esc_html(get_the_title($lp->ID)) ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Related tags / categories -->
                <?php if (!empty($sidebar_tags)): ?>
                <div class="post-sidebar__section">
                    <h4 class="post-sidebar__section-title"><?php esc_html_e('Chủ Đề Liên Quan', 'pi'); ?></h4>
                    <div class="post-sidebar__cat-tags">
                        <?php foreach ($sidebar_tags as $t): ?>
                        <a href="<?= esc_url(get_tag_link($t->term_id)) ?>" class="post-sidebar__cat-tag post-sidebar__cat-tag--tag">
                            <?= esc_html($t->name) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div><!-- /.post-sidebar__sticky -->
        </aside>

        <!-- Right: Gutenberg content -->
        <div class="post-content js-post-content">
            <div class="post-content__body">
                <?= $content ?>
            </div>

            <!-- Footer: tags + share -->
            <div class="post-content__footer">

                <div class="post-share">
                    <div class="post-share__label">
                        <span class="post-share__label-cursive"><?php esc_html_e('Chia sẻ', 'pi'); ?></span>
                        <span class="post-share__label-bold"><?php esc_html_e('BÀI VIẾT NÀY', 'pi'); ?></span>
                    </div>
                    <div class="post-share__icons">
                        <!-- Copy link -->
                        <button type="button" class="post-share__icon js-copy-link" aria-label="<?php esc_attr_e('Sao chép liên kết', 'pi'); ?>" data-url="<?= esc_url(get_permalink()) ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                            </svg>
                        </button>
                        <!-- Gmail -->
                        <a href="https://mail.google.com/mail/?view=cm&su=<?= urlencode(get_the_title()) ?>&body=<?= urlencode(get_permalink()) ?>" target="_blank" rel="noopener noreferrer" class="post-share__icon" aria-label="Gmail">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20.283 10.356h-8.327v3.451h4.792c-.446 2.193-2.313 3.453-4.792 3.453a5.27 5.27 0 0 1-5.279-5.28 5.27 5.27 0 0 1 5.279-5.279c1.259 0 2.397.447 3.29 1.178l2.6-2.599c-1.584-1.381-3.615-2.233-5.89-2.233a8.908 8.908 0 0 0-8.934 8.934 8.907 8.907 0 0 0 8.934 8.934c4.467 0 8.529-3.249 8.529-8.934 0-.528-.081-1.097-.202-1.625z"/>
                            </svg>
                        </a>
                        <!-- Twitter/X -->
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode(get_permalink()) ?>&text=<?= urlencode(get_the_title()) ?>" target="_blank" rel="noopener noreferrer" class="post-share__icon" aria-label="X (Twitter)">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                        <!-- Facebook -->
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(get_permalink()) ?>" target="_blank" rel="noopener noreferrer" class="post-share__icon" aria-label="Facebook">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div><!-- /.post-content -->

    </div><!-- /.post-single__body-inner -->
</div><!-- /.post-single__body -->

<!-- ── Related Posts ───────────────────────────────────────────────── -->
<?php if (!empty($related)): ?>
<section class="post-related">
    <div class="container">
        <div class="post-related__heading">
            <span class="post-related__label"><?php esc_html_e('Bài Viết Liên Quan', 'pi'); ?></span>
            <h2 class="post-related__title"><?php esc_html_e('Bạn Có Thể Muốn Đọc Thêm', 'pi'); ?></h2>
        </div>
        <div class="post-related__header">
            <div class="post-related__nav">
                <button class="post-related-prev blog-swiper-btn" aria-label="<?php esc_attr_e('Bài trước', 'pi'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M8.57143 6C8.57143 6.6095 7.96932 7.51964 7.35982 8.28357C6.57618 9.26929 5.63975 10.1293 4.56614 10.7856C3.76114 11.2777 2.78529 11.75 2 11.75M2 11.75C2.78529 11.75 3.76196 12.2223 4.56614 12.7144C5.63975 13.3715 6.57618 14.2315 7.35982 15.2156C7.96932 15.9804 8.57143 16.8921 8.57143 17.5M2 11.75H21.7143" stroke="#27211C" stroke-width="1.5"/>
                    </svg>
                </button>
                <button class="post-related-next blog-swiper-btn" aria-label="<?php esc_attr_e('Bài tiếp', 'pi'); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M15.1429 6C15.1429 6.6095 15.745 7.51964 16.3545 8.28357C17.1381 9.26929 18.0745 10.1293 19.1481 10.7856C19.9531 11.2777 20.929 11.75 21.7143 11.75M21.7143 11.75C20.929 11.75 19.9523 12.2223 19.1481 12.7144C18.0745 13.3715 17.1381 14.2315 16.3545 15.2156C15.745 15.9804 15.1429 16.8921 15.1429 17.5M21.7143 11.75H2" stroke="#27211C" stroke-width="1.5"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div class="container post-related__swiper-wrap">
        <div class="swiper js-related-swiper">
            <div class="swiper-wrapper">
                <?php foreach ($related as $rp): ?>
                <div class="swiper-slide">
                    <?php pi_blog_card($rp, false, false); ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ── Footer blog synced pattern ────────────────────────────────── -->
<?php
$footer_blog = get_page_by_path('footer-blog', OBJECT, 'wp_block');
if ($footer_blog) {
    echo do_blocks($footer_blog->post_content);
}
?>

<!-- ── Global CTA / form block ────────────────────────────────────── -->
<?php
$form_pattern = get_field('form_block_code', 'option');
if ($form_pattern) {
    echo do_blocks($form_pattern);
}
?>

<?php endwhile; ?>

</main><!-- /#primary -->

<?php get_footer(); ?>
