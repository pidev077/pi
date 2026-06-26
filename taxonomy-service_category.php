<?php
/**
 * Template cho taxonomy "service_category".
 *
 * - Nếu danh mục có service_group liên kết (ACF sg_linked_category) → redirect
 *   sang trang service_group đó, giữ đúng hành vi hiện tại (truy cập qua single-service_group.php).
 * - Nếu danh mục KHÔNG có service_group liên kết → render template riêng, gồm cả
 *   (cùng layout, tái dùng style của block "List Service Category" — pi-blocks):
 *   1. Các service_group được liên kết (sg_linked_category) với danh mục này hoặc
 *      bất kỳ danh mục con/cháu nào của nó (mọi cấp).
 *   2. Các bài viết "service" thuộc danh mục này, kể cả thuộc danh mục con/cháu của nó.
 */

$term = get_queried_object();

$linked_group = get_posts( [
	'post_type'   => 'service_group',
	'post_status' => 'publish',
	'numberposts' => 1,
	'meta_query'  => [ [
		'key'   => 'sg_linked_category',
		'value' => $term->term_id,
	] ],
] );

if ( $linked_group ) {
	wp_safe_redirect( get_permalink( $linked_group[0]->ID ), 301 );
	exit;
}

get_header();

get_template_part( 'template-parts/page-hero', null, [
	'supertitle'  => __( 'Dịch Vụ Tại DD MEDI', 'pi' ),
	'title'       => $term->name,
	'description' => term_description( $term->term_id, 'service_category' ),
] );

// 1) service_group liên kết với danh mục này hoặc bất kỳ danh mục con/cháu nào (mọi cấp).
$descendant_terms = get_terms( [
	'taxonomy'   => 'service_category',
	'child_of'   => $term->term_id,
	'hide_empty' => false,
] );
$descendant_terms = ! is_wp_error( $descendant_terms ) ? $descendant_terms : [];

$related_term_ids   = wp_list_pluck( $descendant_terms, 'term_id' );
$related_term_ids[] = $term->term_id;

$linked_groups = get_posts( [
	'post_type'   => 'service_group',
	'post_status' => 'publish',
	'numberposts' => -1,
	'meta_query'  => [ [
		'key'     => 'sg_linked_category',
		'value'   => $related_term_ids,
		'compare' => 'IN',
	] ],
] );

// 2) Dịch vụ thuộc danh mục này, kể cả dịch vụ thuộc danh mục con/cháu của nó.
$services = new WP_Query( [
	'post_type'      => 'service',
	'posts_per_page' => -1,
	'tax_query'      => [ [
		'taxonomy' => 'service_category',
		'field'    => 'term_id',
		'terms'    => $term->term_id,
	] ],
] );
?>

<main id="primary" class="site-main service-category-archive">

	<?php if ( $linked_groups || $services->have_posts() ) : ?>
		<section class="block-service-category-list">
			<div class="container">
				<?php
				$index = 0;
				foreach ( $linked_groups as $sg ) :
					get_template_part( 'template-parts/service-category-card', null, [
						'post_id' => $sg->ID,
						'index'   => $index,
					] );
					$index++;
				endforeach;

				while ( $services->have_posts() ) :
					$services->the_post();
					get_template_part( 'template-parts/service-category-card', null, [
						'post_id' => get_the_ID(),
						'index'   => $index,
					] );
					$index++;
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php else : ?>
		<section class="service-group__listing">
			<div class="container">
				<p><?php esc_html_e( 'Chưa có dịch vụ nào trong danh mục này.', 'pi' ); ?></p>
			</div>
		</section>
	<?php endif; ?>

	<?php pi_render_footer_blog_block(); ?>

</main>

<?php get_footer(); ?>
