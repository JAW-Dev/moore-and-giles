<?php
/**
 * Module Name:       SearchWP - Index WooCommerce variations
 * Module URI:
 * Description:       Index WooCommerce variations
 * Version:           1.0.0
 * Author:            Objectiv
 * Author URI:        https://objectiv.co
 */

namespace MG_Core\modules\SearchWP;

use MG_Core\Modules\Base;

class WooCommerce extends Base {
	/***
	 * Register module
	 */
	public function __construct() {
		$this->set_id( 'searchwp_index_product_variations' );
		$this->set_name( 'SearchWP - Index WooCommerce variations' );
		$this->set_description( 'Index WooCommerce variations' );
		$this->set_author( 'Clifton Griffin' );
		$this->set_author_uri( 'https://objectiv.co' );
		$this->set_version( '1.0.0' );
	}

	/**
	 * Kicks off the module.
	 */
	public function run() {
		/**
		 * Index WooCommerce Product Variations
		 */

		add_filter( 'searchwp_indexed_post_types', array( $this, 'my_searchwp_indexed_post_types' ) );
		add_action( 'save_post', array( $this, 'my_swp_purge_product_variations' ), 10, 2 );
	}

	// Add product variations to the list of post types to index
	function my_searchwp_indexed_post_types( $post_types ) {
		if ( ! in_array('product_variation', $post_types ) ) {
			$post_types = array_merge( $post_types, array( 'product_variation' ) );
		}
		return $post_types;
	}

	// Update the product variations in the index when the parent product gets updated
	function my_swp_purge_product_variations( $post_id, $post ){
		if ( 'product' === $post->post_type ) {
			$args = array(
				'post_type'   => 'product_variation',
				'post_parent' => $post_id,
			);
			$variations = get_children( $args, ARRAY_A );
			foreach( $variations as $id => $variation ) {
				if ( class_exists( 'SWP' ) ) {
					SWP()->purge_post( $id, true );
				}
			}
			if ( class_exists( 'SWP' ) ) {
				SWP()->trigger_index();
			}
		}
	}
}