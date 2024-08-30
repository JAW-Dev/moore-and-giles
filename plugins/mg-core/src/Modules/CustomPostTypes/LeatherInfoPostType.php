<?php
/**
 * Module Name:       Leather Info Post Type
 * Module URI:
 * Description:       Adds the "Leather Info Post Type"
 * Version:           1.0.0
 * Author:            Objectiv
 * Author URI:        https://objectiv.co
 */

namespace MG_Core\modules\CustomPostTypes;

use MG_Core\Modules\Base;

class LeatherInfoPostType extends Base {
	/***
	 * Register module
	 */
	public function __construct() {
		$this->set_id( 'mg_leather_info_custom_post_type' );
		$this->set_name( 'Leather Info Post Type' );
		$this->set_description( 'Adds the Leather Info Post Type' );
		$this->set_author( 'Eldon Yoder' );
		$this->set_author_uri( 'https://objectiv.co' );
		$this->set_version( '1.0.0' );
	}

	/**
	 * Kicks off the module.
	 */
	public function run() {
		add_filter( 'init', array( $this, 'leather_info_cpt' ) );
	}

	function leather_info_cpt() {
		$labels = array(
			'name'               => _x( 'Leather Information', 'leather_information' ),
			'singular_name'      => _x( 'Leather Information', 'leather_information' ),
			'add_new'            => _x( 'Add New', 'leather_information' ),
			'add_new_item'       => _x( 'Add New Leather Information', 'leather_information' ),
			'edit_item'          => _x( 'Edit Leather Information', 'leather_information' ),
			'new_item'           => _x( 'New Leather Information', 'leather_information' ),
			'view_item'          => _x( 'View Leather Information', 'leather_information' ),
			'search_items'       => _x( 'Search Leather Information', 'leather_information' ),
			'not_found'          => _x( 'No leather information found', 'leather_information' ),
			'not_found_in_trash' => _x( 'No leather information found in Trash', 'leather_information' ),
			'parent_item_colon'  => _x( 'Parent Leather Information:', 'leather_information' ),
			'menu_name'          => _x( 'Leather Info', 'leather_information' ),
		);

		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 60,
			'show_in_nav_menus'   => false,
			'menu_icon'           => 'dashicons-admin-generic',
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => false,
			'capability_type'     => 'post',
		);

		register_post_type( 'leather_information', $args );
	}
}
