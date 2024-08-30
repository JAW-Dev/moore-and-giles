<?php
/**
* Module Name:       WooCommerce - Fix zoom image for Additional Variation Images AJAX call
* Module URI:
* Description:       Fix zoom image for Additional Variation Images AJAX call
* Version:           1.0.0
* Author:            Objectiv
* Author URI:        https://objectiv.co
*/

namespace MG_Core\modules\WooCommerce;

use MG_Core\Modules\Base;

class AddDataSrcToAdditionalVariationImages extends Base {
	/***
	 * Register module
	 */
	public function __construct() {
		$this->set_id( 'woocommerce_add_data_src_to_additional_variation_images' );
		$this->set_name( 'WooCommerce - Fix zoom image for Additional Variation Images AJAX call' );
		$this->set_description( 'Fix zoom image for Additional Variation Images AJAX call' );
		$this->set_author( 'Clifton Griffin' );
		$this->set_author_uri( 'https://objectiv.co' );
		$this->set_version( '1.0.0' );
	}

	/**
	 * Kicks off the module.
	 */
	public function run() {
		add_filter( 'wp_get_attachment_image_attributes', array($this, 'switch_image_attribute'), 10, 2 );
	}

	function switch_image_attribute( $attributes, $attachment ) {
		if ( empty($attributes['data-src']) ) {
			$attributes['data-src'] = wp_get_attachment_image_src( $attachment->ID, 'full' )[0];
		}
		return $attributes;
	}
}