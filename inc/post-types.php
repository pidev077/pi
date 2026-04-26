<?php

/**
 * Use this file to register any custom post types you wish to create.
 */
if (!function_exists('flip_create_custom_post_type')) {
	// Register Custom Post Type
	function flip_create_custom_post_type()
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

	add_action('init', 'flip_create_custom_post_type', 0);
}

if (!function_exists('flip_create_custom_taxonomy')) {
	function flip_create_custom_taxonomy()
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

	add_action('init', 'flip_create_custom_taxonomy', 0);
}