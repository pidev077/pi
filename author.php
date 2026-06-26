<?php
/**
 * Author archive template
 * @package pi
 */

get_header();

$author       = get_queried_object();
$author_id    = $author->ID;
$author_name  = $author->display_name;
$author_bio   = get_the_author_meta( 'description', $author_id );
$post_count   = (int) count_user_posts( $author_id, 'post' );
$current_sort = get_query_var( 'sort', 'newest' );

// ACF custom user field: job title
$job_title = function_exists( 'get_field' )
    ? get_field( 'author_job_title', 'user_' . $author_id )
    : '';

// Dynamically compute top root categories written by this author
$author_post_ids = get_posts( [
    'author'         => $author_id,
    'posts_per_page' => -1,
    'fields'         => 'ids',
    'post_status'    => 'publish',
] );
$author_main_cats = [];
if ( ! empty( $author_post_ids ) ) {
    $cat_counts = [];
    foreach ( $author_post_ids as $pid ) {
        foreach ( get_the_category( $pid ) as $cat ) {
            $root = $cat->parent == 0 ? $cat->name : get_category( get_ancestors( $cat->term_id, 'category' )[0] ?? $cat->term_id )->name;
            $cat_counts[ $root ] = ( $cat_counts[ $root ] ?? 0 ) + 1;
        }
    }
    arsort( $cat_counts );
    $author_main_cats = array_keys( array_slice( $cat_counts, 0, 3, true ) );
}

$blog_page_id   = get_option( 'page_for_posts' );
$blog_page_url  = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/blog/' );
$blog_page_name = $blog_page_id ? get_the_title( $blog_page_id ) : 'DD MEDI Blog';

$newest_url = esc_url( remove_query_arg( 'sort' ) );
$oldest_url = esc_url( add_query_arg( 'sort', 'oldest' ) );
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
                <a href="<?= esc_url( $blog_page_url ) ?>"><?= esc_html( $blog_page_name ) ?></a>
                <span class="post-breadcrumb__sep" aria-hidden="true">
                    <svg width="6" height="10" viewBox="0 0 6 10" fill="none" aria-hidden="true">
                        <path d="M1 1L5 5L1 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span aria-current="page"><?= esc_html( $author_name ) ?></span>
            </nav>
        </div>
    </div>

    <!-- ── Author Hero (2-column) ────────────────────────────────────────── -->
    <section class="archive-hero">
        <div class="container">
            <div class="archive-hero__inner">

                <!-- Left: identity -->
                <div class="archive-hero__content">
                    <span class="archive-hero__label"><?= esc_html__( 'Tác Giả Blog', 'pi' ) ?></span>
                    <h1 class="archive-hero__title"><?= esc_html( $author_name ) ?></h1>
                    <?php if ( $author_bio ) : ?>
                        <p class="archive-hero__desc"><?= wp_kses_post( $author_bio ) ?></p>
                    <?php endif; ?>
                </div>

                <!-- Right: meta panel -->
                <div class="archive-hero__meta">

                    <!-- Job title -->
                    <?php if ( $job_title ) : ?>
                    <div class="archive-hero__meta-item">
                        <div class="archive-hero__meta-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        <div class="archive-hero__meta-body">
                            <span class="archive-hero__meta-text"><?= esc_html( $job_title ) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Main topics -->
                    <?php if ( ! empty( $author_main_cats ) ) : ?>
                    <div class="archive-hero__meta-item">
                        <div class="archive-hero__meta-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                        </div>
                        <div class="archive-hero__meta-body">
                            <span class="archive-hero__meta-label"><?= esc_html__( 'Chủ Đề Chính:', 'pi' ) ?></span>
                            <span class="archive-hero__meta-text"><?= esc_html( implode( ' và ', $author_main_cats ) ) ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Post count -->
                    <div class="archive-hero__meta-item">
                        <div class="archive-hero__meta-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                            </svg>
                        </div>
                        <div class="archive-hero__meta-body">
                            <span class="archive-hero__meta-text"><?= $post_count ?> <?= esc_html__( 'Bài Viết', 'pi' ) ?></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- ── Posts Section ─────────────────────────────────────────────────── -->
    <section class="archive-posts">
        <div class="container">

            <!-- Header row -->
            <div class="archive-posts__header">
                <div class="archive-posts__heading">
                    <h2 class="archive-posts__title">
                        <?= esc_html__( 'Tất cả bài viết của', 'pi' ) ?>
                        <span><?= esc_html( $author_name ) ?></span>
                    </h2>
                    <?php
                    global $wp_query;
                    $total        = (int) $wp_query->found_posts;
                    $per_page     = (int) $wp_query->get( 'posts_per_page' );
                    $current_page = max( 1, (int) get_query_var( 'paged' ) );
                    $from         = ( $current_page - 1 ) * $per_page + 1;
                    $to           = min( $current_page * $per_page, $total );
                    if ( $total > 0 ) :
                    ?>
                        <p class="archive-posts__subtitle">
                            <?php printf(
                                esc_html__( 'Đang hiển thị %1$d trong %2$d bài viết', 'pi' ),
                                $total <= $per_page ? $total : $to - $from + 1,
                                $total
                            ); ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Sort dropdown -->
                <div class="archive-posts__sort">
                    <select aria-label="<?= esc_attr__( 'Sắp xếp bài viết', 'pi' ) ?>"
                            onchange="location.href=this.value">
                        <option value="<?= $newest_url ?>"
                            <?= $current_sort !== 'oldest' ? 'selected' : '' ?>>
                            <?= esc_html__( 'Mới Nhất', 'pi' ) ?>
                        </option>
                        <option value="<?= $oldest_url ?>"
                            <?= $current_sort === 'oldest' ? 'selected' : '' ?>>
                            <?= esc_html__( 'Cũ Nhất', 'pi' ) ?>
                        </option>
                    </select>
                </div>
            </div>

            <?php if ( have_posts() ) : ?>

                <!-- Posts grid -->
                <div class="row g-4 archive-posts__grid">
                    <?php while ( have_posts() ) : the_post(); ?>
                        <div class="col-md-6 col-lg-4">
                            <?php pi_blog_card( get_post(), true ); ?>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <div class="nav-filter-wrap">
                    <?php pi_the_posts_navigation( [
                        'prev_text' => pi_svg_icon( 'arrow_prev' ) . __( 'Trước', 'pi' ),
                        'next_text' => __( 'Tiếp', 'pi' ) . pi_svg_icon( 'arrow_next' ),
                    ] ); ?>
                </div>

            <?php else : ?>
                <?php get_template_part( 'template-parts/content', 'none' ); ?>
            <?php endif; ?>

        </div>
    </section>
    <?php pi_render_footer_blog_block(); ?>
</main>

<?php get_footer(); ?>
