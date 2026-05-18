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
				'name'               => 'Services',
				'singular_name'      => 'Service',
				'add_new'            => 'Add New',
				'add_new_item'       => 'Add New Service',
				'edit_item'          => 'Edit Service',
				'new_item'           => 'New Service',
				'view_item'          => 'View Service',
				'search_items'       => 'Search Services',
				'not_found'          => 'No services found',
				'not_found_in_trash' => 'No services found in Trash',
				'menu_name'          => 'Services',
			),
			'description'        => 'Manage clinic services',
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array('slug' => 'service', 'with_front' => false),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 20,
			'menu_icon'          => 'dashicons-heart',
			'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'),
			'show_in_rest'       => true,
			'rest_base'          => 'services',
		));
	}

	add_action('init', 'pi_register_service_post_type', 0);
}

if (!function_exists('pi_create_custom_taxonomy')) {
	function pi_create_custom_taxonomy()
	{

		

		register_taxonomy('team-location', array('teams'), array(
			'labels' => array(
				'name'              => 'Locations',
				'singular_name'     => 'location',
				'search_items'      => 'Search Locations',
				'all_items'         => 'All Locations',
				'parent_item'       => 'Parent Location',
				'parent_item_colon' => 'Parent Location:',
				'edit_item'         => 'Edit Location',
				'update_item'       => 'Update Location',
				'add_new_item'      => 'Add New Location',
				'new_item_name'     => 'New Location Name',
				'menu_name'         => 'Locations',
			),
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => false,
			'show_in_rest'      => true,
		));
	}

	add_action('init', 'pi_create_custom_taxonomy', 0);
}