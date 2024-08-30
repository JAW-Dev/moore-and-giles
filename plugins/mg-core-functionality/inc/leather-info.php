<?php
class MG_LeatherInformation {
	public function __construct() {
		add_action('init', array($this, 'leather_info_cpt') );
		add_action('cmb2_init', array($this, 'metaboxes') );
	}

	function leather_info_cpt() {
		$labels = array(
			 'name' => _x( 'Leather Information', 'leather_information' ),
			 'singular_name' => _x( 'Leather Information', 'leather_information' ),
			 'add_new' => _x( 'Add New', 'leather_information' ),
			 'add_new_item' => _x( 'Add New Leather Information', 'leather_information' ),
			 'edit_item' => _x( 'Edit Leather Information', 'leather_information' ),
			 'new_item' => _x( 'New Leather Information', 'leather_information' ),
			 'view_item' => _x( 'View Leather Information', 'leather_information' ),
			 'search_items' => _x( 'Search Leather Information', 'leather_information' ),
			 'not_found' => _x( 'No leather information found', 'leather_information' ),
			 'not_found_in_trash' => _x( 'No leather information found in Trash', 'leather_information' ),
			 'parent_item_colon' => _x( 'Parent Leather Information:', 'leather_information' ),
			 'menu_name' => _x( 'Leather Information', 'leather_information' ),
		 );

		 $args = array(
			 'labels' => $labels,
			 'hierarchical' => false,

			 'supports' => array( 'title', 'editor' ),

			 'public' => false,
			 'show_ui' => true,
			 'show_in_menu' => true,
			 'menu_position' => 60,

			 'show_in_nav_menus' => false,
			 'publicly_queryable' => false,
			 'exclude_from_search' => true,
			 'has_archive' => false,
			 'query_var' => true,
			 'can_export' => true,
			 'rewrite' => false,
			 'capability_type' => 'post'
		 );

		 register_post_type( 'leather_information', $args );
	}

	function metaboxes() {
		$prefix = '_mg_li_';

		$monogram_images = new_cmb2_box( array(
			'id'            => $prefix . 'metabox',
			'title'         => __( 'Monogram Images', 'cmb2' ),
			'object_types'  => array( 'leather_information', ), // Post type
		) );

		$group_field_id = $monogram_images->add_field( array(
			'id'          => $prefix . 'monogram_info',
			'type'        => 'group',
			'description' => __( 'Monogram Images', 'cmb2' ),
			'options'     => array(
				'group_title'   => __( 'Monogram Image {#}', 'cmb2' ), // {#} gets replaced by row number
				'add_button'    => __( 'Add Another Monogram Image', 'cmb2' ),
				'remove_button' => __( 'Remove Monogram Image', 'cmb2' ),
				'repeatable'    => true,
			),
		) );

		$monogram_images->add_group_field( $group_field_id, array(
			'name'       => __( 'Specific Leather Name', 'cmb2' ),
			'desc'       => __( 'Name of leather color (e.g., Brompton Brown)', 'cmb2' ),
			'id'         => 'name',
			'type'       => 'text',
		) );

		$monogram_images->add_group_field( $group_field_id, array(
			'name'       => __( 'Image', 'cmb2' ),
			'desc'       => __( 'Upload an image or enter a URL.', 'cmb2' ),
			'id'         => 'image',
			'type'       => 'file',
		) );
	}
}
