<?php
/**
 * Template cho trang dịch vụ đơn lẻ (service).
 * Banner split-layout → nội dung Gutenberg.
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		get_template_part( 'template-parts/service-hero' );
		?>
		<main id="primary" class="site-main single-service">

			<div class="entry-content">
				<?php the_content(); ?>
			</div>

		</main>
		<?php
	endwhile;
endif;

get_footer();
