<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );?>
<?php
// Register Custom Post Type
function xolo_hotspot_cpt_function() {

	$labels = array(
		'name'                  => _x( 'Hotspot', 'Post Type General Name', 'hotspot' ),
		'singular_name'         => _x( 'Hotspot', 'Post Type Singular Name', 'hotspot' ),
		'menu_name'             => __( 'Hotspot', 'hotspot' ),
		'name_admin_bar'        => __( 'Hotspot', 'hotspot' ),
		'archives'              => __( 'Item Archives', 'hotspot' ),
		'parent_item_colon'     => __( 'Parent Item:', 'hotspot' ),
		'all_items'             => __( 'All Items', 'hotspot' ),
		'add_new_item'          => __( 'Add New Item', 'hotspot' ),
		'add_new'               => __( 'Add New', 'hotspot' ),
		'new_item'              => __( 'New Item', 'hotspot' ),
		'edit_item'             => __( 'Edit Item', 'hotspot' ),
		'update_item'           => __( 'Update Item', 'hotspot' ),
		'view_item'             => __( 'View Item', 'hotspot' ),
		'search_items'          => __( 'Search Item', 'hotspot' ),
		'not_found'             => __( 'Not found', 'hotspot' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'hotspot' ),
		'featured_image'        => __( 'Featured Image', 'hotspot' ),
		'set_featured_image'    => __( 'Set featured image', 'hotspot' ),
		'remove_featured_image' => __( 'Remove featured image', 'hotspot' ),
		'use_featured_image'    => __( 'Use as featured image', 'hotspot' ),
		'insert_into_item'      => __( 'Insert into item', 'hotspot' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'hotspot' ),
		'items_list'            => __( 'Items list', 'hotspot' ),
		'items_list_navigation' => __( 'Items list navigation', 'hotspot' ),
		'filter_items_list'     => __( 'Filter items list', 'hotspot' ),
	);
	$args = array(
		'label'                 => __( 'Hotspot', 'hotspot' ),
		'labels'                => $labels,
		'supports'              => array( 'title' ),
		'hierarchical'          => false,
		'public'                => false,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-location-alt',
		'show_in_admin_bar'     => false,
		'show_in_nav_menus'     => false,
		'can_export'            => false,
		'has_archive'           => false,		
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		
	);
	register_post_type( 'points_pin', $args );

}
add_action( 'init', 'xolo_hotspot_cpt_function', 0 );

//Add admin inline style
function xolo_hotspot_admin_css() {
	global $post_type;
	$post_types = array(
		'points_pin'
	);
	if(in_array($post_type, $post_types))
		echo '<style type="text/css">#post-preview, #view-post-btn,#message.notice-success a{display: none;}</style>';
}
add_action( 'admin_head-post-new.php', 'xolo_hotspot_admin_css' );
add_action( 'admin_head-post.php', 'xolo_hotspot_admin_css' );

//Add row to admin column
add_filter( 'page_row_actions', 'xolo_hotspot_row_actions', 10, 2 );
add_filter( 'post_row_actions', 'xolo_hotspot_row_actions', 10, 2 );
function xolo_hotspot_row_actions( $actions, $post ) {
	if($post->post_type == 'points_pin'){
	    unset( $actions['inline hide-if-no-js'] );
	    unset( $actions['view'] );
	}
    return $actions;
}

//Add new column
function xolo_hotspot_cpt_admin_columns( $columns ) {
	$columns = array(
		'cb' 			=> '<input type="checkbox" />',
		'title' 		=> __( 'Title','hotspot' ),
		'shortcode' 	=> __( 'Shortcode','hotspot' ),
		'date' 			=> __( 'Date','hotspot' ),
	);
	return $columns;
}
add_filter( 'manage_edit-points_pin_columns', 'xolo_hotspot_cpt_admin_columns' ) ;

//Add content to colum
function xolo_hotspot_manage_points_pin_columns( $column, $post_id ) {
	global $post;
	switch( $column ) {
		case 'shortcode' :
			echo '[xolo_hotspot id="'.$post->ID.'"]';
			break;
		default :
			break;
	}
}
add_action( 'manage_points_pin_posts_custom_column', 'xolo_hotspot_manage_points_pin_columns', 10, 2 );