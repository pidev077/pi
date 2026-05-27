<?php
/**
 * Auto-list dịch vụ cho trang nhóm dịch vụ.
 *
 * Đọc ACF field "sg_linked_category" từ post hiện tại.
 * - Nếu danh mục được chọn có danh mục con → nhóm theo từng danh mục con.
 * - Nếu không có con → hiển thị thẳng các dịch vụ thuộc danh mục đó.
 */

$linked_cat_id = get_field( 'sg_linked_category' );
if ( ! $linked_cat_id ) {
	return;
}

$child_terms = get_terms( [
	'taxonomy'   => 'service_category',
	'parent'     => $linked_cat_id,
	'hide_empty' => false,
	'orderby'    => 'term_order',
	'order'      => 'ASC',
] );

$has_children = ! empty( $child_terms ) && ! is_wp_error( $child_terms );
?>

<section class="service-group__listing">
	<div class="container">

		<?php if ( $has_children ) :
			// Có danh mục con → nhóm Nâng Mũi, Cắt Mí ...
			foreach ( $child_terms as $child_term ) :
				$services = new WP_Query( [
					'post_type'      => 'service',
					'posts_per_page' => -1,
					'tax_query'      => [ [
						'taxonomy' => 'service_category',
						'field'    => 'term_id',
						'terms'    => $child_term->term_id,
					] ],
				] );

				if ( ! $services->have_posts() ) {
					continue;
				}
				?>
				<div class="service-group__sub-category">
					<h2 class="sub-category__title">
						<a href="<?php echo esc_url( get_term_link( $child_term ) ); ?>">
							<?php echo esc_html( $child_term->name ); ?>
						</a>
					</h2>

					<?php if ( $child_term->description ) : ?>
						<p class="sub-category__desc"><?php echo esc_html( $child_term->description ); ?></p>
					<?php endif; ?>

					<div class="service-group__grid">
						<?php while ( $services->have_posts() ) : $services->the_post(); ?>
							<?php get_template_part( 'template-parts/service-card' ); ?>
						<?php endwhile; ?>
					</div>
				</div>
				<?php
				wp_reset_postdata();
			endforeach;

		else :
			// Không có con → list thẳng dịch vụ thuộc danh mục
			$services = new WP_Query( [
				'post_type'      => 'service',
				'posts_per_page' => -1,
				'tax_query'      => [ [
					'taxonomy' => 'service_category',
					'field'    => 'term_id',
					'terms'    => $linked_cat_id,
				] ],
			] );

			if ( $services->have_posts() ) : ?>
				<div class="service-group__grid">
					<?php while ( $services->have_posts() ) : $services->the_post(); ?>
						<?php get_template_part( 'template-parts/service-card' ); ?>
					<?php endwhile; ?>
				</div>
				<?php
				wp_reset_postdata();
			endif;
		endif;
		?>

	</div>
</section>
