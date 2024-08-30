<?php

/**
 * A class to set up a custom taxonomies for hide types details
 */
class MG_HideTypes {

	public function __construct() {
		add_action('shopp_init', array($this, 'hide_type') );
		add_action('cmb2_init', array($this, 'taxonomy_register_hide_type_metabox') );
	}

  function hide_type() {
		$labels = array(
		    'name' => _x( 'Hide Types', 'hide_types' ),
		    'singular_name' => _x( 'Hide Types', 'hide_types' ),
		    'search_items' => _x( 'Search Hide Types', 'hide_types' ),
		    'popular_items' => _x( 'Popular Hide Types', 'hide_types' ),
		    'all_items' => _x( 'All Hide Types', 'hide_types' ),
		    'parent_item' => _x( 'Parent Hide Types', 'hide_types' ),
		    'parent_item_colon' => _x( 'Parent Hide Types:', 'hide_types' ),
		    'edit_item' => _x( 'Edit Hide Types', 'hide_types' ),
		    'update_item' => _x( 'Update Hide Types', 'hide_types' ),
		    'add_new_item' => _x( 'Add New Hide Types', 'hide_types' ),
		    'new_item_name' => _x( 'New Hide Types', 'hide_types' ),
		    'separate_items_with_commas' => _x( 'Separate hide types with commas', 'hide_types' ),
		    'add_or_remove_items' => _x( 'Add or remove hide types', 'hide_types' ),
		    'choose_from_most_used' => _x( 'Choose from the most used hide types', 'hide_types' ),
		    'menu_name' => _x( 'Hide Types', 'hide_types' ),
		);

		$args = array(
		    'labels' => $labels,
		    'public' => false,
		    'show_in_nav_menus' => false,
		    'show_ui' => true,
		    'show_tagcloud' => false,
		    'show_admin_column' => false,
		    'hierarchical' => true,
		    'rewrite' => false,
		    'query_var' => false
		);

		shopp_register_taxonomy( 'hide_type', $args );
  }


	function taxonomy_register_hide_type_metabox() {

		/**
		 * Add the file metabox to the shopp hide type taxonomy editor page
		 */

		$shopp_cat = new_cmb2_box( array(
		      'id'           => 'hide_type_options',
		      'object_types' => array( 'term' ),
			  'taxonomies'	=> array('shopp_hide_type'),
		  ) );

		$shopp_cat->add_field( array(
		      'name' => 'Hide Type Image',
		      'id'   => 'hide_type_image',
		      'type' => 'file',
		  ) );

	}

}
