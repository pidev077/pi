<?php
/**
 * Template part: Page Hero / Title Bar
 *
 * ACF fields (per page):
 *   page_hero_enable      — true/false toggle
 *   page_hero_supertitle  — small text above title
 *   page_hero_title       — main heading (falls back to page title)
 *   page_hero_description — paragraph below title
 *   page_hero_bg_image    — background image URL
 */

if ( ! get_field( 'page_hero_enable' ) ) {
	return;
}

$supertitle  = get_field( 'page_hero_supertitle' );
$title       = get_field( 'page_hero_title' ) ?: get_the_title();
$description = get_field( 'page_hero_description' );
$bg_image    = get_field( 'page_hero_bg_image' );

$style  = $bg_image ? ' style="background-image:url(' . esc_url( $bg_image ) . ');"' : '';
$has_bg = $bg_image ? ' page-hero--has-bg' : '';
?>
<section class="page-hero<?= $has_bg ?>"<?= $style ?>>
	<div class="page-hero__inner">
		<div class="container">

			<div class="page-hero__label">
				<?php if ( $supertitle ) : ?>
					<p class="page-hero__supertitle"><?= esc_html( $supertitle ) ?></p>
				<?php endif; ?>
				<span class="page-hero__divider" aria-hidden="true">
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
						<path d="M8 0L10.1607 5.83927L16 8L10.1607 10.1607L8 16L5.83927 10.1607L0 8L5.83927 5.83927L8 0Z" fill="#B9AE9E"/>
					</svg>
				</span>
			</div>

			<?php if ( $title ) : ?>
				<h1 class="page-hero__title"><?= wp_kses_post( $title ) ?></h1>
			<?php endif; ?>

			<?php if ( $description ) : ?>
				<p class="page-hero__desc"><?= wp_kses_post( $description ) ?></p>
			<?php endif; ?>

		</div>
	</div>
</section>
<?php pi_page_breadcrumb(); ?>
