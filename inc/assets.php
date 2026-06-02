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

if (!function_exists('pi_load_fonts')) {
	/**
	 * Load custom font family
	 */
	function pi_load_fonts()
	{
		wp_enqueue_style('primary-font', 'https://fonts.googleapis.com/css2?family=Google+Sans:wght@200;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap', [], null);
	}
}

add_action('admin_enqueue_scripts', 'pi_load_fonts');

// Load theme CSS inside CF7 admin editor so the form preview matches frontend
add_action('admin_enqueue_scripts', function ( $hook ) {
	if ( strpos( $hook, 'wpcf7' ) === false ) {
		return;
	}

	wp_enqueue_style(
		'pi-cf7-admin-styles',
		get_template_directory_uri() . '/dist/css/style.css',
		[],
		null
	);
});

add_action('enqueue_block_editor_assets', function () {
	wp_enqueue_style(
		'pi-editor-fonts',
		'https://fonts.googleapis.com/css2?family=Google+Sans:wght@200;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600;1,700&display=swap',
		[],
		null
	);

	wp_add_inline_style('pi-editor-fonts', '
		.font-google-sans { font-family: "Google Sans", sans-serif; }
		.font-playfair-display { font-family: "Playfair Display", serif; }
	');
});
