<?php
/*Register Custom Post Type*/
function adforestAPI_custom_post_type() {

	$labels = array(
		'name'                  => _x( 'App Pages', 'Post Type General Name', 'adforest-rest-api' ),
		'singular_name'         => _x( 'App Page', 'Post Type Singular Name', 'adforest-rest-api' ),
		'menu_name'             => __( 'App Pages', 'adforest-rest-api' ),
		'name_admin_bar'        => __( 'App Page', 'adforest-rest-api' ),
		'archives'              => __( 'Item Archives', 'adforest-rest-api' ),
		'attributes'            => __( 'Item Attributes', 'adforest-rest-api' ),
		'parent_item_colon'     => __( 'Parent Item:', 'adforest-rest-api' ),
		'all_items'             => __( 'All Items', 'adforest-rest-api' ),
		'add_new_item'          => __( 'Add New Item', 'adforest-rest-api' ),
		'add_new'               => __( 'Add New', 'adforest-rest-api' ),
		'new_item'              => __( 'New Item', 'adforest-rest-api' ),
		'edit_item'             => __( 'Edit Item', 'adforest-rest-api' ),
		'update_item'           => __( 'Update Item', 'adforest-rest-api' ),
		'view_item'             => __( 'View Item', 'adforest-rest-api' ),
		'view_items'            => __( 'View Items', 'adforest-rest-api' ),
		'search_items'          => __( 'Search Item', 'adforest-rest-api' ),
		'not_found'             => __( 'Not found', 'adforest-rest-api' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'adforest-rest-api' ),
		'featured_image'        => __( 'Featured Image', 'adforest-rest-api' ),
		'set_featured_image'    => __( 'Set featured image', 'adforest-rest-api' ),
		'remove_featured_image' => __( 'Remove featured image', 'adforest-rest-api' ),
		'use_featured_image'    => __( 'Use as featured image', 'adforest-rest-api' ),
		'insert_into_item'      => __( 'Insert into item', 'adforest-rest-api' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'adforest-rest-api' ),
		'items_list'            => __( 'Items list', 'adforest-rest-api' ),
		'items_list_navigation' => __( 'Items list navigation', 'adforest-rest-api' ),
		'filter_items_list'     => __( 'Filter items list', 'adforest-rest-api' ),
	);
	$args = array(
		'label'                 => __( 'App Page', 'adforest-rest-api' ),
		'description'           => __( 'App Page is design to set the app layouts', 'adforest-rest-api' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', ),
		'hierarchical'          => true,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => false,		
		'exclude_from_search'   => true,
		'publicly_queryable'    => false,
		'capability_type'       => 'page',
	);
	register_post_type( 'app_page', $args );

}
add_action( 'init', 'adforestAPI_custom_post_type', 0 );
