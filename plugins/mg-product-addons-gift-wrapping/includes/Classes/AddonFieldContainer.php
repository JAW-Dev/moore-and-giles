<?php
/**
 * Addon Field Container
 *
 * @package    MG_Product_Addons_Gift_Wrapping
 * @subpackage MG_Product_Addons_Gift_Wrapping/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Product_Addons_Gift_Wrapping\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\AddonFieldContainer' ) ) {

	/**
	 * Addon Field Container
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class AddonFieldContainer {

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			$this->hooks();
		}

		/**
		 * Hooks.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function hooks() {
			add_filter( 'objectiv_addon_field_container', array( $this, 'addon_field_container' ), 20, 5 );
		}

		/**
		 * Addon field container.
		 *
		 * @author Jason Witt
		 * @since  1.1.0
		 *
		 * @param string $html The markup.
		 * @param array  $arguments {
		 *     The arguments.
		 *
		 *     @type array  $addon_args        The addon arguments.
		 *     @type string $hidden_fields     The hidden fields markup.
		 *     @type string $additional_fields The additional fields markup.
		 *     @type array  $cart_item         The cart item array.
		 * }
		 *
		 * @return string
		 */
		public function addon_field_container( $html, $arguments ) {
			$addons    = $arguments['addon_args'];
			$variables = array(
				'addon_id'        => ( isset( $addons['addon']['product'] ) ) ? $addons['addon']['product'] : '',
				'addon_slug'      => ( isset( $addons['addon']['slug'] ) ) ? $addons['addon']['slug'] : false,
				'addon_label'     => ( isset( $addons['addon']['label'] ) ) ? $addons['addon']['label'] : '',
				'tooltip'         => ( function_exists( 'get_field' ) ) ? get_field( 'woo_help_gift_wrapping_tooltip', 'option' ) : '',
				'cart_product_id' => ( isset( $addons['cart_product_id'] ) ) ? $addons['cart_product_id'] : '',
				'arguments'       => ( isset( $arguments ) ) ? $arguments : array(),
				'cart_item_id'    => ( isset( $cart_item['product_id'] ) ) ? $cart_item['product_id'] : '',
			);

			$addon_product       = ( function_exists( 'wc_get_product' ) ) ? wc_get_product( $variables['addon_id'] ) : array(); // Get the product array else empty array.
			$addon_product_price = method_exists( $addon_product, 'get_price' ) ? $addon_product->get_price() : ''; // Get the product price else empty string.

			// If the addon slug equals gift-wrapping.
			if ( strpos( $variables['addon_slug'], 'gift-wrapping') !== false ) {
				GetGiftWrapping::data(
					$variables,
					function( $boxes ) use ( $variables, $addon_product_price ) {
						foreach ( $boxes as $box ) {
							$price = ( isset( $box['entry_price'] ) ) ? $box['entry_price'] : $addon_product_price; // Get the set addon price else get the default product price.
							echo FieldContainerMarkup::render( $variables, $price ); // phpcs:ignore
						}
					}
				);
			}

			// Don't return any markup.
			return '';
		}
	}
}
