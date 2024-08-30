<?php
/**
 * Module Name:       WooCommerce - Fix ionic show single variations bug with FacetWP
 * Module URI:
 * Description:       Fix ionic show single variations bug with FacetWP
 * Version:           1.0.0
 * Author:            Objectiv
 * Author URI:        https://objectiv.co
 */

namespace MG_Core\modules\WooCommerce;

use MG_Core\Modules\Base;

class FixIonicSingleVariationsBug extends Base {
	/***
	 * Register module
	 */
	public function __construct() {
		$this->set_id( __CLASS__ );
		$this->set_name( 'WooCommerce - Fix ionic show single variations bug with FacetWP' );
		$this->set_description( 'Fix ionic show single variations bug with FacetWP' );
		$this->set_author( 'Clifton Griffin' );
		$this->set_author_uri( 'https://objectiv.co' );
		$this->set_version( '1.0.0' );
	}

	/**
	 * Kicks off the module.
	 */
	public function run() {
		if ( stripos( $_SERVER['REQUEST_URI'], 'fwp' ) !== false ) {
			add_action( 'init', array($this, 'remove_filter'), 100 );
		}
	}

	function remove_filter() {
		remove_filter( 'posts_clauses', array( 'Iconic_WSSV_Menu_Order', 'order_by_menu_order_post_clauses' ) );
	}
}