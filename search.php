<?php

/**
 * The template for displaying search results pages
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 * @package pi
 */

get_header();
?>
<main id="primary" class="site-main">
	<div class="pi-form-search">
		<h5><?php esc_html_e('Search', 'pi'); ?></h5>
		<form role="search" method="get" class="search-form" action="<?php echo home_url('/'); ?>">
			<input type="search" class="search-field" placeholder="<?php echo esc_attr_x('Search …', 'placeholder') ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x('Search for:', 'label') ?>" />
			<input type="submit" />
		</form>
	</div>

	<div class="post-loop-container-search">
		<?php if (have_posts()) : ?>
			<?php
			/* Start the Loop */
			echo '<h3 class=”title-total-result-search”>' . count($posts) . ' ' . esc_html__('results for', 'pi') . ' “' . esc_html(get_search_query()) . '”</h3>';
			global $post;
			$index = 1;
			$duration = $index % 2 == 0 ? 400 : 800;

			while (have_posts()) :
				the_post();
				$postType = str_replace("-", " ", get_post_type($post->ID));
				if ($postType == 'post') {
					$postType = __('NEWS & ARTICLES', 'pi');
				}
			?>
				<article <?php post_class('post-loop-item post-item-temp-defaults post-indexs-' . $index) ?> data-aos="fade-up" data-aos-duration="<?= $index == 1 ? 400 : $duration; ?>">
					<div class="item-inner">
						<h6 class="post-type"><?php echo $postType; ?></h6>
						<h5 class="post-title">
							<a href="<?php the_permalink() ?>"><?php the_title() ?></a>
						</h5>
						<div class="post-desc">
							<?php
							$excerpt = get_the_excerpt();
							echo $excerpt;
							?>
						</div>
					</div>
				</article>
			<?php
			endwhile;

			pi_the_posts_navigation();

			wp_reset_postdata(); // Reset the global post object
		else :
			?>
			<section class="no-results not-found">
				<header class="page-header">
					<h3 class="page-titles"><?php esc_html_e('Nothing Found', 'pi'); ?></h3>
				</header><!-- .page-header -->

				<div class="page-content">
					<p><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'pi'); ?></p>

				</div><!-- .page-content -->
			</section><!-- .no-results -->
		<?php
		endif;
		
		?>
	</div>
</main><!-- #main -->
<?php
get_footer();
