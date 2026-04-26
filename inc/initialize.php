<?php
/**
 * Use this file for initialization of the theme.
 */
add_action('after_setup_theme', function () {
	add_theme_support('automatic-feed-links');
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('align-wide');
	add_theme_support('custom-line-height');
	add_theme_support('html5', [
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	]);

	add_theme_support('custom-logo');
	add_theme_support('wp-block-styles');
	add_theme_support('editor-styles');
	add_editor_style('build/editor.css');
});

function flip_add_slug_to_body_class($classes)
{
	if (is_singular()) {
		global $post;
		$classes[] = 'page-' . sanitize_html_class($post->post_name);
	}
	return $classes;
}
add_filter('body_class', 'flip_add_slug_to_body_class');



add_filter('rest_authentication_errors', function ($result) {
	if (!empty($result)) {
		return $result;
	}

	if (!is_user_logged_in()) {
		$request_uri = $_SERVER['REQUEST_URI'] ?? '';

		if (strpos($request_uri, '/wp-json/wp/v2/users') !== false) {
			return new WP_Error(
				'rest_forbidden',
				__('User endpoint is restricted for non-logged-in users.', 'flip'),
				array('status' => 401)
			);
		}
	}

	return $result;
});