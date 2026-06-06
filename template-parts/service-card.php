<?php
/**
 * Card hiển thị 1 dịch vụ — dùng trong service-group listing và các nơi khác.
 * Cần gọi trong vòng lặp WP_Query của post type 'service'.
 */
?>
<article class="service-card">
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>" class="service-card__thumbnail" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'medium_large' ); ?>
		</a>
	<?php endif; ?>

	<div class="service-card__body">
		<h3 class="service-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<?php if ( has_excerpt() ) : ?>
			<p class="service-card__excerpt"><?php the_excerpt(); ?></p>
		<?php endif; ?>

		<a href="<?php the_permalink(); ?>" class="service-card__cta">
			<?php esc_html_e('Xem Chi Tiết', 'pi'); ?>
		</a>
	</div>
</article>
