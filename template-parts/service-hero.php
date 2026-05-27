<?php
/**
 * Service Hero — banner split-layout for service & service_group.
 * Text at bottom-left, full-height image on the right, breadcrumb below.
 *
 * ACF fields:
 *   service_hero_title — main heading (falls back to post title)
 *   service_hero_desc  — short description
 *   service_hero_cta   — CTA button link
 *   service_hero_image — right-side image
 */

$title      = get_field( 'service_hero_title' ) ?: get_the_title();
$desc       = get_field( 'service_hero_desc' );
$cta        = get_field( 'service_hero_cta' );
$image      = get_field( 'service_hero_image' );
?>
<section class="service-hero">

	<?php if ( $image ) : ?>
		<figure class="service-hero__image-wrap" aria-hidden="true">
			<img src="<?= esc_url( $image['url'] ) ?>"
			     alt="<?= esc_attr( $image['alt'] ) ?>"
			     loading="eager" />
		</figure>
	<?php endif; ?>

	<div class="service-hero__body">
		<div class="container">
			<div class="service-hero__content">

				<h1 class="service-hero__title"><?= wp_kses_post( $title ) ?></h1>

				<?php if ( $desc ) : ?>
					<p class="service-hero__desc"><?= wp_kses_post( $desc ) ?></p>
				<?php endif; ?>

				<?php if ( $cta ) : ?>
					<a href="<?= esc_url( $cta['url'] ) ?>"
					   class="service-hero__cta"
					   <?= ! empty( $cta['target'] ) ? 'target="' . esc_attr( $cta['target'] ) . '"' : '' ?>>
						<?= esc_html( $cta['title'] ) ?>
						<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
							<line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/>
						</svg>
					</a>
				<?php endif; ?>

			</div>
		</div>
	</div>

</section>

<?php pi_page_breadcrumb(); ?>
