<?php

/**
 * Use this file to register any custom post types you wish to create.
 */
if (!function_exists('pi_create_custom_post_type')) {
	// Register Custom Post Type
	function pi_create_custom_post_type()
	{	

		register_post_type('teams', array(
			'labels' => [
				'name'               => 'Teams ',
				'singular_name'      => 'Teams',
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New Team',
				'edit_item'          => 'Edit Team',
				'new_item'           => 'New Team',
				'view_item'          => 'View Team',
				'search_items'       => 'Search Teams',
				'not_found'          => 'No case teams found',
				'not_found_in_trash' => 'No case teams found in Trash',
				'menu_name'          => 'Teams',
			],
			'description'        => 'Manage different teams',
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 21,
			'menu_icon'          => 'dashicons-groups',
			'supports'           => ['title', 'editor', 'thumbnail', 'revisions', 'custom-fields'],
			'show_in_rest'       => true,
		));
	}

	add_action('init', 'pi_create_custom_post_type', 0);
}

if (!function_exists('pi_register_service_post_type')) {
	function pi_register_service_post_type()
	{
		register_post_type('service', array(
			'labels' => array(
				'name'               => 'Dịch Vụ',
				'singular_name'      => 'Dịch Vụ',
				'add_new'            => 'Thêm Mới',
				'add_new_item'       => 'Thêm Dịch Vụ Mới',
				'edit_item'          => 'Sửa Dịch Vụ',
				'new_item'           => 'Dịch Vụ Mới',
				'view_item'          => 'Xem Dịch Vụ',
				'search_items'       => 'Tìm Dịch Vụ',
				'not_found'          => 'Không tìm thấy dịch vụ',
				'not_found_in_trash' => 'Không có dịch vụ trong thùng rác',
				'menu_name'          => 'Dịch Vụ',
			),
			'description'        => 'Quản lý dịch vụ thẩm mỹ',
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array('slug' => 'dich-vu', 'with_front' => false),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 20,
			'menu_icon'          => 'dashicons-heart',
			'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'),
			'show_in_rest'       => true,
			'rest_base'          => 'services',
			'taxonomies'         => array('service_category'),
		));
	}

	add_action('init', 'pi_register_service_post_type', 0);
}

if (!function_exists('pi_register_service_taxonomy')) {
	function pi_register_service_taxonomy()
	{
		// Taxonomy phân cấp cho dịch vụ (hỗ trợ cấp 1 và cấp 2)
		// Ví dụ cấp 1: "Phẫu Thuật Thẩm Mỹ Khuôn Mặt"
		// Ví dụ cấp 2: "Nâng Mũi" (con của Khuôn Mặt)
		// Post thực tế: "Nâng Mũi S-Line", "Nâng Mũi L-Line" gán vào term cấp 2
		register_taxonomy('service_category', array('service'), array(
			'labels' => array(
				'name'              => 'Danh Mục Dịch Vụ',
				'singular_name'     => 'Danh Mục',
				'search_items'      => 'Tìm Danh Mục',
				'all_items'         => 'Tất Cả Danh Mục',
				'parent_item'       => 'Danh Mục Cha',
				'parent_item_colon' => 'Danh Mục Cha:',
				'edit_item'         => 'Sửa Danh Mục',
				'update_item'       => 'Cập Nhật Danh Mục',
				'add_new_item'      => 'Thêm Danh Mục Mới',
				'new_item_name'     => 'Tên Danh Mục Mới',
				'menu_name'         => 'Danh Mục',
			),
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			// URL phẳng tại root (giống service_group) — không có prefix "danh-muc-dich-vu".
			// Xem filter 'term_link' và hook template_redirect bên dưới.
			'rewrite'           => false,
			'show_in_rest'      => true,
		));
	}

	add_action('init', 'pi_register_service_taxonomy', 0);
}

// Hiển thị đúng permalink phẳng cho service_category (vd: /tham-my-voc-dang/)
add_filter('term_link', function ($url, $term, $taxonomy) {
	if ($taxonomy === 'service_category') {
		return home_url(user_trailingslashit($term->slug));
	}
	return $url;
}, 10, 3);

if (!function_exists('pi_register_service_group_post_type')) {
	function pi_register_service_group_post_type()
	{
		register_post_type('service_group', array(
			'labels' => array(
				'name'               => 'Nhóm Dịch Vụ',
				'singular_name'      => 'Nhóm Dịch Vụ',
				'add_new'            => 'Thêm Mới',
				'add_new_item'       => 'Thêm Nhóm Dịch Vụ Mới',
				'edit_item'          => 'Sửa Nhóm Dịch Vụ',
				'new_item'           => 'Nhóm Dịch Vụ Mới',
				'view_item'          => 'Xem Nhóm Dịch Vụ',
				'search_items'       => 'Tìm Nhóm Dịch Vụ',
				'not_found'          => 'Không tìm thấy nhóm dịch vụ',
				'not_found_in_trash' => 'Không có nhóm dịch vụ trong thùng rác',
				'menu_name'          => 'Nhóm Dịch Vụ',
			),
			'description'        => 'Trang landing cho từng nhóm dịch vụ lớn — layout tự do bằng Gutenberg',
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => true,
			'menu_position'      => 21,
			'menu_icon'          => 'dashicons-category',
			'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields', 'page-attributes'),
			'show_in_rest'       => true, // bắt buộc để Gutenberg hoạt động
			'rest_base'          => 'service-groups',
		));
	}

	add_action('init', 'pi_register_service_group_post_type', 0);
}

// Hiển thị đúng permalink trong admin cho service_group
add_filter('post_type_link', function ($url, $post) {
	if (is_object($post) && $post->post_type === 'service_group' && !empty($post->post_name)) {
		return home_url(user_trailingslashit($post->post_name));
	}
	return $url;
}, 10, 2);

// Phục vụ service_group tại root URL mà không xung đột với pages
add_action('template_redirect', function () {
	if (!is_404()) return;

	$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
	if (empty($path) || strpos($path, '/') !== false) return;

	$posts = get_posts([
		'post_type'      => 'service_group',
		'name'           => sanitize_title($path),
		'posts_per_page' => 1,
		'post_status'    => 'publish',
	]);

	if (!empty($posts)) {
		global $wp_query;
		$wp_query->is_404      = false;
		$wp_query->is_single   = true;
		$wp_query->is_singular = true;
		$wp_query->post_count  = 1;
		$wp_query->found_posts = 1;
		$wp_query->post        = $posts[0];
		$wp_query->posts       = $posts;
		$GLOBALS['post']       = $posts[0];
		setup_postdata($posts[0]);

		status_header(200);

		$template = locate_template(['single-service_group.php', 'single.php']);
		if ($template) {
			include $template;
			exit;
		}
		return;
	}

	// Không khớp service_group → thử khớp danh mục dịch vụ (service_category) tại root URL.
	$term = get_term_by('slug', sanitize_title($path), 'service_category');
	if (!$term || is_wp_error($term)) return;

	global $wp_query;
	$wp_query->is_404         = false;
	$wp_query->is_archive     = true;
	$wp_query->is_tax         = true;
	$wp_query->queried_object = $term;
	$wp_query->queried_object_id = $term->term_id;

	status_header(200);

	$template = locate_template(['taxonomy-service_category.php', 'taxonomy.php']);
	if ($template) {
		include $template;
		exit;
	}
}, 5);

