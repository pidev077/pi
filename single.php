<?php
/**
 * The template for displaying all single posts
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 * @package flip
 */

get_header();

?>
<main id="primary" class="site-main">
    <div class="entry-content">
        <div class="container insight-wrapper">
            <?php while (have_posts()):
                the_post(); ?>
                <div class="insight-wrapper__thumbnail">
                    <div class="insight-wrapper__thumbnail--image">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('full'); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="insight-wrapper__content">
                    <div class="insight-wrapper__content--top">
                        <h1 class="wp-block-heading-fadein-chars"><?= get_the_title() ?></h1>

                        <div class="insight-wrapper__content--datetime">
                            <span class="post-date">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none">
                                    <path
                                        d="M19.5 3H16.5V1.5H15V3H9V1.5H7.5V3H4.5C3.675 3 3 3.675 3 4.5V19.5C3 20.325 3.675 21 4.5 21H19.5C20.325 21 21 20.325 21 19.5V4.5C21 3.675 20.325 3 19.5 3ZM19.5 19.5H4.5V9H19.5V19.5ZM19.5 7.5H4.5V4.5H7.5V6H9V4.5H15V6H16.5V4.5H19.5V7.5Z"
                                        fill="#120A00" />
                                </svg>
                                <?php echo get_the_date(); ?>
                            </span>

                            <span class="read-time">
                                <?php
                                $word_count = str_word_count(wp_strip_all_tags(get_the_content()));
                                $reading_time = ceil($word_count / 200);

                                echo $reading_time . ' min read';
                                ?>
                            </span>
                        </div>
                    </div>
                    <div class="insight-wrapper__content--body">
                        <?php the_content(); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <?php
        $author_avatar = get_field('author_avatar');
        $author_name = get_field('author_name');
        $author_bio = get_field('author_bio');
        ?>

        <div class="insight-post-meta-box">
            <div class="container insight-post-meta-box__flex">
                <div class="insight-post-meta-box__left">
                    <div class="post-meta-box__author">
                        <div class="author-avatar">
                            <?php if ($author_avatar) { ?>
                                <img src="<?= $author_avatar ?>"
                                    alt="<?php $author_name ? $author_name : the_author(); ?>" />
                            <?php } else {
                                echo get_avatar(get_the_author_meta('ID'), 80);
                            }
                            ?>s
                        </div>

                        <div class="author-info">
                            <p class="author-name">
                                Written by <?php echo $author_name ? $author_name : the_author(); ?>
                            </p>

                            <p class="author-bio">
                                <?php echo $author_bio ? $author_bio : get_the_author_meta('description'); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="insight-post-meta-box__right">
                    <div class="post-meta-box__share">
                        <p class="share-title">Share this insight</p>

                        <div class="share-icons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>"
                                target="_blank" aria-label="Share on Facebook">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                    fill="none">
                                    <path
                                        d="M6.89856 16.125H9.89856V10.1175H12.6016L12.8986 7.1325H9.89856V5.625C9.89856 5.42609 9.97758 5.23532 10.1182 5.09467C10.2589 4.95402 10.4497 4.875 10.6486 4.875H12.8986V1.875H10.6486C9.654 1.875 8.70017 2.27009 7.99691 2.97335C7.29365 3.67661 6.89856 4.63044 6.89856 5.625V7.1325H5.39856L5.10156 10.1175H6.89856V16.125Z"
                                        fill="#FFF5D2" />
                                </svg>
                            </a>

                            <a href="https://twitter.com/intent/tweet?url=<?php the_permalink(); ?>&text=<?php the_title(); ?>"
                                target="_blank" aria-label="Share on X">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                    fill="none">
                                    <path
                                        d="M10.4563 7.794L16.1522 1.125H14.8022L9.85784 6.91537L5.90684 1.125H1.35059L7.32434 9.882L1.35059 16.875H2.70059L7.92284 10.7595L12.0955 16.875H16.6517L10.4563 7.794ZM8.60796 9.9585L8.00271 9.08662L3.18659 2.14875H5.25996L9.14571 7.74788L9.75096 8.61975L14.8033 15.8985H12.73L8.60796 9.9585Z"
                                        fill="#FFF5D2" />
                                </svg>
                            </a>

                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php the_permalink(); ?>"
                                target="_blank" aria-label="Share on LinkedIn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                    fill="none">
                                    <path
                                        d="M5.20508 3.75002C5.20488 4.14784 5.04665 4.52929 4.76521 4.81046C4.48376 5.09162 4.10215 5.24947 3.70433 5.24927C3.3065 5.24907 2.92505 5.09084 2.64389 4.8094C2.36272 4.52795 2.20488 4.14634 2.20508 3.74852C2.20528 3.35069 2.3635 2.96924 2.64495 2.68808C2.92639 2.40691 3.308 2.24907 3.70583 2.24927C4.10365 2.24947 4.48511 2.40769 4.76627 2.68914C5.04743 2.97058 5.20528 3.35219 5.20508 3.75002ZM5.25008 6.36002H2.25008V15.75H5.25008V6.36002ZM9.99008 6.36002H7.00508V15.75H9.96008V10.8225C9.96008 8.07752 13.5376 7.82252 13.5376 10.8225V15.75H16.5001V9.80252C16.5001 5.17502 11.2051 5.34752 9.96008 7.62002L9.99008 6.36002Z"
                                        fill="#FFF5D2" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    <?php
                    $tags = get_the_tags();
                    if ($tags): ?>
                        <div class="post-meta-box__tags">
                            <p class="tags-title">Tags</p>

                            <div class="tags-list">
                                <?php
                                $tags = get_the_tags();
                                if ($tags):
                                    foreach ($tags as $tag):
                                        ?>
                                        <div class="tag-item">
                                            <?php echo esc_html($tag->name); ?>
                                        </div>
                                        <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php
        $form_pattern = get_field('form_block_code', 'option');
        if ($form_pattern) {
            echo do_blocks($form_pattern);
        }
        ?>

        <div class="insight-related">
            <div class="container">
                <h2>NEXT INSIGHT</h2>

                <?php
                $next_post = get_next_post();
                if (!$next_post) {
                    $args = array(
                        'posts_per_page' => 1,
                        'order' => 'ASC',
                        'orderby' => 'date',
                        'post_type' => 'post',
                        'post_status' => 'publish',
                    );
                    $first_post = get_posts($args);

                    if (!empty($first_post)) {
                        $next_post = $first_post[0];
                    }
                }
                if ($next_post) {
                    ?>
                    <article class="insight-related__article">
                        <div class="insight-related__article--thumbnail">
                            <?php if (has_post_thumbnail($next_post->ID)): ?>
                                <a href="<?php echo get_permalink($next_post->ID); ?>">
                                    <img src="<?php echo get_the_post_thumbnail_url($next_post->ID, 'full'); ?>"
                                        alt="<?php echo get_the_title($next_post->ID); ?>" />
                                </a>
                            <?php endif; ?>
                        </div>

                        <div class="insight-related__content">
                            <h3 class="insight-related__title">
                                <a href="<?php echo get_permalink($next_post->ID); ?>">
                                    <?php echo get_the_title($next_post->ID); ?>
                                </a>
                            </h3>
                            <?php
                            $excerpt = get_the_excerpt($next_post->ID);
                            if ($excerpt) {
                                ?>
                                <div class="insight-related__content--excerpt"><?= $excerpt; ?></div>
                            <?php } ?>

                            <a class="insight-related__content--cta" href="<?php echo get_permalink($next_post->ID); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"
                                    fill="none">
                                    <circle cx="5" cy="5" r="5" fill="#120A00" />
                                </svg>
                                Continue reading
                            </a>
                        </div>
                    </article>
                <?php } ?>
            </div>
        </div>
    </div>
</main><!-- #main -->
<?php
get_footer();