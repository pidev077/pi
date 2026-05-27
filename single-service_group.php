<?php
/**
 * Template cho trang nhóm dịch vụ (service_group).
 * Layout do Gutenberg quyết định — danh sách dịch vụ tự động nối sau nội dung.
 */

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		get_template_part( 'template-parts/service-hero' );
		?>
		<main id="primary" class="site-main service-group">

			<div class="entry-content">
				<?php the_content(); ?>
			</div>

			<?php get_template_part( 'template-parts/content', 'service_group' ); ?>

		</main>
		<?php
	endwhile;
endif;

get_footer();
