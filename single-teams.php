<?php
/**
 * Template cho trang chi tiết Teams.
 * Nội dung do Gutenberg quyết định hoàn toàn.
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/page-hero' );
		?>
		<main id="primary" class="site-main single-teams">

			<div class="entry-content">
				<?php the_content(); ?>
			</div>

		</main>
		<?php
	endwhile;
endif;

get_footer();
