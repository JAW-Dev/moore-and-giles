<?php
/**
* Module Name:       WooCommerce - Hide Removed from Cart Notice from Single Product Page
* Module URI:
* Description:       Hide notice after product is added to the cart.
* Version:           1.0.0
* Author:            Objectiv
* Author URI:        https://objectiv.co
*/

namespace MG_Core\modules\WooCommerce;

use MG_Core\Modules\Base;

class HideRemovedFromCartNotice extends Base {
	/***
	 * Register module
	 */
	public function __construct() {
		$this->set_id( 'woocommerce_hide_removed_from_cart_notice' );
		$this->set_name( 'Hide Removed from Cart Notice from Single Product Page' );
		$this->set_description( 'Hide notice after product is removed from the cart.' );
		$this->set_author( 'Clifton Griffin' );
		$this->set_author_uri( 'https://objectiv.co' );
		$this->set_version( '1.0.0' );
	}

	/**
	 * Kicks off the module.
	 */
	public function run() {
		add_action( 'wp', array( $this, 'remove_notices' ) );
	}

	function remove_notices() {
		remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices', 10 );
	}
}