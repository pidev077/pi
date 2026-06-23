<?php
/**
 * Card dùng chung — tái dùng markup/CSS của block "List Service Category" (pi-blocks).
 * Dùng cho taxonomy-service_category.php cho cả 2 loại post: service_group và service.
 *
 * Args:
 *   post_id — ID bài viết (service_group hoặc service), bắt buộc
 *   index   — vị trí trong danh sách, dùng để xen kẽ layout (is-reversed)
 */

$args    = $args ?? [];
$post_id = $args['post_id'] ?? null;
if ( ! $post_id ) {
	return;
}
$index = $args['index'] ?? 0;

$title = get_the_title( $post_id );
$url   = get_permalink( $post_id );
$desc  = get_field( 'service_hero_desc', $post_id ) ?: get_the_excerpt( $post_id );

$image_url = '';
$hero_img  = get_field( 'service_hero_image', $post_id );
if ( $hero_img && is_array( $hero_img ) ) {
	$image_url = $hero_img['sizes']['large'] ?? $hero_img['url'] ?? '';
}
if ( ! $image_url ) {
	$image_url = get_the_post_thumbnail_url( $post_id, 'large' );
}

// Nếu là service_group → gợi ý "Dịch Vụ Bao Gồm" dựa trên danh mục được liên kết.
$list_items = [];
if ( get_post_type( $post_id ) === 'service_group' ) {
	$linked_term_id = get_field( 'sg_linked_category', $post_id );
	$linked_term    = $linked_term_id ? get_term( $linked_term_id, 'service_category' ) : null;

	if ( $linked_term && ! is_wp_error( $linked_term ) ) {
		$children = get_terms( [
			'taxonomy'   => 'service_category',
			'parent'     => $linked_term->term_id,
			'hide_empty' => false,
			'orderby'    => 'term_order',
			'order'      => 'ASC',
		] );
		if ( ! is_wp_error( $children ) && ! empty( $children ) ) {
			foreach ( $children as $child ) {
				$list_items[] = $child->name;
			}
		} else {
			$services = get_posts( [
				'post_type'   => 'service',
				'post_status' => 'publish',
				'numberposts' => 6,
				'tax_query'   => [ [
					'taxonomy' => 'service_category',
					'field'    => 'term_id',
					'terms'    => $linked_term->term_id,
				] ],
			] );
			foreach ( $services as $svc ) {
				$list_items[] = get_the_title( $svc->ID );
			}
		}
	}
}

$card_classes = 'service-category-card' . ( $index % 2 !== 0 ? ' is-reversed' : '' );
$cta_title    = str_ireplace( [ 'Phẫu Thuật ', 'Điều Trị ' ], '', $title );
?>
<div class="<?= esc_attr( $card_classes ) ?>">
	<div class="service-category-card__inner">

		<div class="service-category-card__content">
			<h2 class="service-category-card__title"><?= esc_html( $title ) ?></h2>

			<?php if ( $desc ) : ?>
				<p class="service-category-card__desc"><?= esc_html( $desc ) ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $list_items ) ) : ?>
				<div class="service-category-card__services">
					<span class="service-category-card__services-label"><?php esc_html_e( 'Dịch Vụ Bao Gồm', 'pi' ); ?></span>
					<ul class="service-category-card__list">
						<?php foreach ( $list_items as $item ) : ?>
							<li><?= esc_html( $item ) ?></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<a href="<?= esc_url( $url ) ?>" class="service-category-card__cta">
				<?php printf( esc_html__( 'Xem %s', 'pi' ), esc_html( $cta_title ) ); ?>
				<svg width="14" height="14" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<line x1="5" y1="19" x2="19" y2="5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
					<polyline points="9 5 19 5 19 15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
			</a>
		</div>

		<div class="service-category-card__media">
			<div class="service-category-card__image-wrap">
				<?php if ( $image_url ) : ?>
					<img src="<?= esc_url( $image_url ) ?>" alt="<?= esc_attr( $title ) ?>" loading="lazy">
				<?php endif; ?>
			</div>
		</div>

	</div>
</div>
