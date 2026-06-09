<?php
/**
 * Category archive template
 * @package pi
 */

get_header();

$current_cat  = get_queried_object();
$cat_name     = $current_cat->name;
$cat_desc     = term_description( $current_cat->term_id, 'category' );
$current_sort = get_query_var( 'sort', 'newest' );
$parent_cat   = $current_cat->parent ? get_category( $current_cat->parent ) : null;

$blog_page_id   = get_option( 'page_for_posts' );
$blog_page_url  = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/blog/' );
$blog_page_name = $blog_page_id ? get_the_title( $blog_page_id ) : 'DD CLINIC Blog';

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
                <?php if ( $parent_cat ) : ?>
                    <span class="post-breadcrumb__sep" aria-hidden="true">
                        <svg width="6" height="10" viewBox="0 0 6 10" fill="none" aria-hidden="true">
                            <path d="M1 1L5 5L1 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <a href="<?= esc_url( get_category_link( $parent_cat->term_id ) ) ?>"><?= esc_html( $parent_cat->name ) ?></a>
                <?php endif; ?>
                <span class="post-breadcrumb__sep" aria-hidden="true">
                    <svg width="6" height="10" viewBox="0 0 6 10" fill="none" aria-hidden="true">
                        <path d="M1 1L5 5L1 9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span aria-current="page"><?= esc_html( $cat_name ) ?></span>
            </nav>
        </div>
    </div>

    <!-- ── Archive Hero (centered) ───────────────────────────────────────── -->
    <section class="archive-hero archive-hero--centered">
        <div class="container">
            <h1 class="archive-hero__title">
                <?= esc_html( $cat_name ) ?>
            </h1>
            <?php if ( $cat_desc ) : ?>
                <div class="archive-hero__desc"><?= wp_kses_post( $cat_desc ) ?></div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ── Posts Section ─────────────────────────────────────────────────── -->
    <section class="archive-posts">
        <div class="container">

            <!-- Header row -->
            <div class="archive-posts__header">
                <div class="archive-posts__heading">
                    <h2 class="archive-posts__title">
                        <?= esc_html( $cat_name ) ?>
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
                                esc_html__( 'Đang hiển thị %1$d-%2$d trong %3$d bài viết', 'pi' ),
                                $from, $to, $total
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
    <?php
    $footer_blog = get_page_by_path('footer-blog', OBJECT, 'wp_block');
    if ($footer_blog) {
        echo do_blocks($footer_blog->post_content);
    }
    ?>
</main>

<?php get_footer(); ?>
