<?php

/**
 * Helpers
 */

function dump($data)
{
	print "<pre style=' background: rgba(0, 0, 0, 0.1); margin-bottom: 1.618em; padding: 1.618em; overflow: auto; max-width: 100%; '>==========================\n";
	if (is_array($data)) {
		print_r($data);
	} elseif (is_object($data)) {
		var_dump($data);
	} else {
		var_dump($data);
	}
	print "===========================</pre>";
}


if (!function_exists('pi_svg_icon')) {

	/**
	 * @param $icon
	 *
	 * @return mixed|string
	 */
	function pi_svg_icon($icon)
	{
		$icons = require(__DIR__ . '/svg.php');
		return isset($icons[$icon]) ? $icons[$icon] : '';
	}
}

if (!function_exists('pi_the_posts_navigation')) {
	function pi_the_posts_navigation($args = array(), $base = false, $query = false)
	{
		$args = wp_parse_args($args, array(
			'prev_text' => __('Older posts'),
			'next_text' => __('Newer posts'),
			'screen_reader_text' => __('Posts navigation'),
			'aria_label' => __('Posts'),
			'class' => 'posts-navigation',
		));

		$wp_query = $query ? $query : $GLOBALS['wp_query'];

		// Don't print empty markup if there's only one page.
		if ($wp_query->max_num_pages < 2) {
			return;
		}
		$paged = get_query_var('paged') ? intval(get_query_var('paged')) : 1;
		$pagenum_link = html_entity_decode(get_pagenum_link());
		if ($base) {
			$orig_req_uri = $_SERVER['REQUEST_URI'];
			$_SERVER['REQUEST_URI'] = $base;
			$pagenum_link = get_pagenum_link($paged - 1);
			$_SERVER['REQUEST_URI'] = $orig_req_uri;
		}

		$query_args = array();
		$url_parts = explode('?', $pagenum_link);
		if (isset($url_parts[1])) {
			wp_parse_str($url_parts[1], $query_args);
		}

		$pagenum_link = remove_query_arg(array_keys($query_args), $pagenum_link);
		$pagenum_link = trailingslashit($pagenum_link) . '%_%';
		$format = $GLOBALS['wp_rewrite']->using_index_permalinks() && !strpos($pagenum_link, 'index.php') ? 'index.php/' : '';
		$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit('page/%#%', 'paged') : '?paged=%#%';

		// Set up paginated links.
		$links = paginate_links(array(
			'base' => $pagenum_link,
			'format' => $format,
			'total' => $wp_query->max_num_pages,
			'current' => $paged,
			'mid_size' => 1,
			// 'add_args'  => array_map('urlencode', $query_args),
			'prev_text' => $args['prev_text'],
			'next_text' => $args['next_text'],
		));

		if ($links): ?>
			<nav class="navigation paging-navigation">
				<span class="screen-reader-text"><?= $args['screen_reader_text']; ?></span>
				<?php echo '<div class="pagination loop-pagination">' . $links . '</div><!-- .pagination -->' ?>
			</nav><!-- .navigation -->
			<?php
		endif;
	}
}

if (!function_exists('__get_field')) {
	function __get_field($selector, $post_id = false, $format_value = true)
	{
		if (function_exists('__get_field')) {
			return get_field($selector, $post_id, $format_value);
		}

		return false;
	}
}
if (!function_exists('__get_fields')) {
	function __get_fields($post_id = false, $format_value = true)
	{
		if (function_exists('__get_fields')) {
			return get_fields($post_id, $format_value);
		}

		return [];
	}
}

if (!function_exists('pi_blog_card')) {
	/**
	 * Render a blog card.
	 *
	 * @param WP_Post $post_obj
	 * @param bool    $show_cats  true = show category pills with ACF colours
	 * @param bool    $is_featured  true = tall centre card variant
	 */
	function pi_blog_card($post_obj, $show_cats = false, $is_featured = false) {
		$tags        = get_the_tags($post_obj->ID);
		$cats        = get_the_category($post_obj->ID);
		$first_tag   = !empty($tags) ? $tags[0] : null;
		$author_name = get_the_author_meta('display_name', $post_obj->post_author);
		$author_url  = get_author_posts_url($post_obj->post_author);
		$excerpt     = wp_trim_words(get_the_excerpt($post_obj->ID), 18, '...');
		$img_size    = $is_featured ? 'large' : 'medium_large';
		$classes     = 'blog-card' . ($is_featured ? ' blog-card--featured' : '');
		?>
		<article class="<?= $classes ?>">
			<a href="<?= esc_url(get_permalink($post_obj->ID)) ?>" class="blog-card__image-link" tabindex="-1" aria-hidden="true">
				<div class="blog-card__image">
					<?php if (has_post_thumbnail($post_obj->ID)): ?>
						<?= get_the_post_thumbnail($post_obj->ID, $img_size) ?>
					<?php else: ?>
						<div class="blog-card__image-placeholder">
							<svg width="48" height="48" viewBox="0 0 24 24" fill="none" aria-hidden="true">
								<rect x="3" y="3" width="18" height="18" rx="3" stroke="currentColor" stroke-width="1" opacity="0.3"/>
								<circle cx="8.5" cy="8.5" r="1.5" stroke="currentColor" stroke-width="1" opacity="0.3"/>
								<path d="M3 15l5-5 4 4 3-3 5 5" stroke="currentColor" stroke-width="1" opacity="0.3" stroke-linecap="round"/>
							</svg>
						</div>
					<?php endif; ?>
				</div>
			</a>
			<div class="blog-card__body">
				<div class="blog-card__tags">
					<?php if (!empty($tags)):
						foreach (array_slice($tags, 0, 2) as $tag): ?>
							<a href="<?= esc_url(get_tag_link($tag->term_id)) ?>" class="blog-card__tag"><?= esc_html($tag->name) ?></a>
						<?php endforeach;
					endif; ?>
				</div>

				<h3 class="blog-card__title">
					<a href="<?= esc_url(get_permalink($post_obj->ID)) ?>"><?= esc_html(get_the_title($post_obj->ID)) ?></a>
				</h3>

				<?php if ($excerpt): ?>
				<p class="blog-card__excerpt"><?= esc_html($excerpt) ?></p>
				<?php endif; ?>

				<div class="blog-card__meta">
					<span class="blog-card__author">
						<span class="blog-card__by" aria-hidden="true"><?= esc_html__('Bởi', 'pi') ?></span>
						<a href="<?= esc_url($author_url) ?>" class="blog-card__author-link"><?= esc_html($author_name) ?></a>
					</span>
					<span class="blog-card__sep" aria-hidden="true">•</span>
					<span class="blog-card__date">
						<?= get_the_date('j \t\h\á\n\g n, Y', $post_obj->ID) ?>
					</span>
				</div>
			</div>
		</article>
		<?php
	}
}

if ( ! function_exists( 'pi_add_heading_ids' ) ) {
	function pi_add_heading_ids( $content ) {
		$counts = [];
		return preg_replace_callback(
			'/<h([23])([^>]*)>(.*?)<\/h\1>/is',
			function ( $m ) use ( &$counts ) {
				$level = $m[1];
				$attrs = $m[2];
				$inner = $m[3];
				if ( preg_match( '/\bid=["\']/', $attrs ) ) return $m[0];
				$slug          = sanitize_title( wp_strip_all_tags( $inner ) );
				$counts[$slug] = isset( $counts[$slug] ) ? $counts[$slug] + 1 : 0;
				$id            = $counts[$slug] > 0 ? $slug . '-' . $counts[$slug] : $slug;
				return "<h{$level}{$attrs} id=\"{$id}\">{$inner}</h{$level}>";
			},
			$content
		);
	}
}

if ( ! function_exists( 'pi_extract_toc' ) ) {
	function pi_extract_toc( $content ) {
		$items = [];
		if ( preg_match_all( '/<h([23])[^>]*\bid=["\']([^"\']+)["\'][^>]*>(.*?)<\/h\1>/is', $content, $matches, PREG_SET_ORDER ) ) {
			foreach ( $matches as $m ) {
				$items[] = [
					'level' => (int) $m[1],
					'id'    => $m[2],
					'text'  => wp_strip_all_tags( $m[3] ),
				];
			}
		}
		return $items;
	}
}
