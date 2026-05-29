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
			'show_in_rest'       => false,
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
			'rewrite'           => array('slug' => 'danh-muc-dich-vu', 'with_front' => false, 'hierarchical' => true),
			'show_in_rest'      => true,
		));
	}

	add_action('init', 'pi_register_service_taxonomy', 0);
}

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
			'rewrite'            => array('slug' => 'nhom-dich-vu', 'with_front' => false),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 21,
			'menu_icon'          => 'dashicons-category',
			'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'),
			'show_in_rest'       => true, // bắt buộc để Gutenberg hoạt động
			'rest_base'          => 'service-groups',
		));
	}

	add_action('init', 'pi_register_service_group_post_type', 0);
}

