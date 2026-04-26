<?php

add_action('wp_enqueue_scripts', function () {
	wp_enqueue_style('theme-styles', get_template_directory_uri() . '/dist/css/style.css', array(), uniqid());
	wp_enqueue_script('theme-scripts', get_template_directory_uri() . '/dist/js/main.bundle.js', array('jquery'), uniqid(), true);

	wp_localize_script('app-scripts', 'php_data', [
		'admin_logged' => in_array('administrator', wp_get_current_user()->roles) ? 'yes' : 'no',
		'ajax_url' => admin_url('admin-ajax.php'),
		'site_url' => site_url(),
		'rest_url' => get_rest_url(),
	]);

	wp_localize_script('theme-scripts', 'themeData', [
		'templateUrl' => get_template_directory_uri()
	]);
});

if (!function_exists('flip_load_fonts')) {
	/**
	 * Load custom font family
	 */
	function flip_load_fonts()
	{
		wp_enqueue_style('primary-font', get_template_directory_uri() . '/assets/fonts/stylesheet.css', [], FLIP_WP_TOOLKIT_VER);
	}
}

add_action('admin_enqueue_scripts', 'flip_load_fonts');
