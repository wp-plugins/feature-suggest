<?php

// this function creates the 'Suggestion' Custom Post Type
function fs_create_cpt() {
	global $textdomain;
	register_post_type( 'suggestion',
		array(
			'labels' => array(
			'name' => __( 'Suggestions', $textdomain ),
			'singular_name' => __( 'Suggestion', $textdomain ),
			'add_new' => __( 'Add New', $textdomain ),
			'add_new_item' => __( 'Add New Suggestion', $textdomain ),
			'edit' => __( 'Edit', $textdomain ),
			'edit_item' => __( 'Edit Suggestion', $textdomain ),
			'new_item' => __( 'New Suggestion', $textdomain ),
			'view' => __( 'View Suggestion', $textdomain ),
			'view_item' => __( 'View Suggestion', $textdomain ),
			'search_items' => __( 'Search Suggestions', $textdomain ),
			'not_found' => __( 'No Suggestions found', $textdomain ),
			'not_found_in_trash' => __( 'No Suggestions found in Trash', $textdomain ),
			'parent' => __( 'Parent Suggestion', $textdomain ),
		),
		'hierarchical' => true,
		'public' => true,
		'rewrite' => array( 'slug' => 'suggestion', 'with_front' => false ),
		'supports' => array ( 'title','editor','thumbnail' ),
		)
	);
	flush_rewrite_rules( false );
}

?>