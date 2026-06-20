<?php
/**
 * Template part: Page Hero / Title Bar
 *
 * ACF fields (per page):
 *   page_hero_enable      — true/false toggle
 *   page_hero_style       — 'style-1' (centered) | 'style-2' (split: text left / image right)
 *   page_hero_supertitle  — small text above title
 *   page_hero_title       — main heading (falls back to page title)
 *   page_hero_description — paragraph below title
 *   page_hero_cta         — link button
 *   page_hero_bg_image    — background / side image URL
 *
 * Optional args (passed via get_template_part $args):
 *   supertitle   — overrides page_hero_supertitle
 *   title        — overrides page_hero_title
 *   description  — overrides page_hero_description
 *   cta          — overrides page_hero_cta
 *   bg_image     — overrides page_hero_bg_image
 *   style        — overrides page_hero_style
 *   extra        — extra HTML injected after description (before CTA)
 *   modifier     — extra CSS class on <section>
 */

$args = $args ?? [];

// When called without args, respect the per-page ACF toggle.
// When called with args, always render (caller takes responsibility).
if ( empty( $args ) && ! get_field( 'page_hero_enable' ) ) {
	return;
}

$supertitle   = $args['supertitle']  ?? get_field( 'page_hero_supertitle' );
$title        = $args['title']       ?? ( get_field( 'page_hero_title' ) ?: get_the_title() );
$description  = $args['description'] ?? get_field( 'page_hero_description' );
$cta          = $args['cta']         ?? get_field( 'page_hero_cta' );
$bg_image     = $args['bg_image']    ?? get_field( 'page_hero_bg_image' );
$style_choice = $args['style']       ?? ( get_field( 'page_hero_style' ) ?: 'style-1' );
$extra        = $args['extra']       ?? '';
$modifier     = $args['modifier']    ?? '';

// ── Shared inner content (reused in both styles) ─────────────────────────────
$inner_content = function() use ( $supertitle, $title, $description, $cta, $extra ) { ?>
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

	<?php if ( $extra ) : ?>
		<?= $extra ?>
	<?php endif; ?>

	<?php if ( $cta ) : ?>
		<a href="<?= esc_url( $cta['url'] ) ?>"
		   class="page-hero__cta"
		   <?= ! empty( $cta['target'] ) ? 'target="' . esc_attr( $cta['target'] ) . '"' : '' ?>>
			<?= esc_html( $cta['title'] ) ?>
			<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/>
			</svg>
		</a>
	<?php endif;
};

$bg_image    = $bg_image ?: get_template_directory_uri() . '/assets/images/bg-titlebar.webp';
$style      = $bg_image ? ' style="background-image:url(' . esc_url( $bg_image ) . ');"' : '';
$has_bg     = $bg_image ? ' page-hero--has-bg' : '';
$style_class = $style_choice === 'style-2' ? ' page-hero--style-2' : '';
$mod_class   = $modifier ? ' ' . sanitize_html_class( $modifier ) : '';
?>
<section class="page-hero<?= $has_bg ?><?= $style_class ?><?= $mod_class ?>"<?= $style ?>>
	<div class="page-hero__inner">
		<div class="container">
			<?php $inner_content(); ?>
		</div>
	</div>
</section>

<?php pi_page_breadcrumb(); ?>
