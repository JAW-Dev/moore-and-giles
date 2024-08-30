<?php
/**
 * Hidden Fields
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

if ( ! class_exists( __NAMESPACE__ . '\\HiddenFields' ) ) {

	/**
	 * Hidden Fields
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class HiddenFields {

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
			add_filter( 'objectiv_addon_hidden_fields', array( $this, 'custom_hidden_fields' ), 10, 4 );
		}

		/**
		 * Custom hidden fields.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $html          The HTML markup for the hidden fields.
		 * @param array  $field_args    The field arguments.
		 * @param array  $addon_args {
		 *      The arguments.
		 *
		 *      @type string $addon_id    The addon ID.
		 *      @type string $addon_slug  The addon slug.
		 *      @type string $addon_label The addon lable.
		 *      @type string $addon_price The addon price.
		 *      @type string $addon_sku   The addon sku.
		 *      @type string $product_id  The product ID.
		 *      @type string $product_key The product Key.
		 * }
		 * @param string $cart_item_key The art item key.
		 *
		 * @return string
		 */
		public static function custom_hidden_fields( $html, $field_args, $addon_args, $cart_item_key ) {
			self::custom_sku_field( $html, $field_args, $addon_args, $cart_item_key );
			self::custom_price_field( $html, $field_args, $addon_args, $cart_item_key );
			return $html;
		}

		/**
		 * Custom Sku Field.
		 *
		 * @author Jason Witt
		 * @since  1.1.0
		 *
		 * @param string $html          The HTML markup for the hidden fields.
		 * @param array  $field_args    The field arguments.
		 * @param array  $addon_args {
		 *      The arguments.
		 *
		 *      @type string $addon_id    The addon ID.
		 *      @type string $addon_slug  The addon slug.
		 *      @type string $addon_label The addon lable.
		 *      @type string $addon_price The addon price.
		 *      @type string $addon_sku   The addon sku.
		 *      @type string $product_id  The product ID.
		 *      @type string $product_key The product Key.
		 * }
		 * @param string $cart_item_key The art item key.
		 *
		 * @return string
		 */
		public static function custom_sku_field( $html, $field_args, $addon_args, $cart_item_key ) {
			$addon = isset( $addon_args['addon'] ) ? $addon_args['addon'] : array();

			// Bail if addon is empty and return the default HTML.
			if ( empty( $addon ) ) {
				return $html;
			}

			$variables = array(
				'addon_id'        => isset( $addon['product'] ) ? $addon['product'] : '',
				'cart_product_id' => isset( $addon_args['cart_product_id'] ) ? $addon_args['cart_product_id'] : '',
			);

			// Get the base product's sku.
			$base_sku = isset( $addon['sku'] ) ? $addon['sku'] : '';
			$sku      = '';

			// If the cart item ID matched the item ID in the addon.
			if ( $addon_args['cart_product_id'] == $variables['cart_product_id'] ) { // phpcs:ignore

				GetGiftWrapping::data(
					$variables,
					function( $boxes ) use ( $base_sku, $variables ) {

						// Loop though the box types.
						foreach ( $boxes as $box ) {
							if ( $box['cart_product_id'] === $variables['cart_product_id'] ) {
								if ( 'large' === $box['entry_size'] || $box['is_product_included'] || $box['is_category_included'] ) {

									// Apply the field filter.
									add_filter(
										'objectiv_addon_hidden_addon_sku',
										function( $html, $field_args, $addon_args, $cart_item_key ) use ( $box ) {
											$sku = ( isset( $box['entry_sku'] ) && $box['entry_sku'] ) ? $box['entry_sku'] : $base_sku;
											return '<input id="addon_sku-' . $cart_item_key . '" name="' . $field_args['addon_sku'] . '" value="' . $sku . '" type="hidden">';
										},
										10,
										4
									);
								}
							}
						}
					}
				);
			}
		}

		/**
		 * Custom Price Field.
		 *
		 * @author Jason Witt
		 * @since  1.1.0
		 *
		 * @param string $html          The HTML markup for the hidden fields.
		 * @param array  $field_args    The field arguments.
		 * @param array  $addon_args {
		 *      The arguments.
		 *
		 *      @type string $addon_id    The addon ID.
		 *      @type string $addon_slug  The addon slug.
		 *      @type string $addon_label The addon lable.
		 *      @type string $addon_price The addon price.
		 *      @type string $addon_sku   The addon sku.
		 *      @type string $product_id  The product ID.
		 *      @type string $product_key The product Key.
		 * }
		 * @param string $cart_item_key The art item key.
		 *
		 * @return string
		 */
		public static function custom_price_field( $html, $field_args, $addon_args, $cart_item_key ) {
			$addon = isset( $addon_args['addon'] ) ? $addon_args['addon'] : array();

			// Bail if addon is empty and return the default HTML.
			if ( empty( $addon ) ) {
				return $html;
			}

			$variables = array(
				'addon_id'        => isset( $addon['product'] ) ? $addon['product'] : '',
				'cart_product_id' => isset( $addon_args['cart_product_id'] ) ? $addon_args['cart_product_id'] : '',
			);

			// Get the base product's sku.
			$base_sku = isset( $addon['price'] ) ? $addon['price'] : '';

			// If the cart item ID matched the item ID in the addon.
			if ( $addon_args['cart_product_id'] == $variables['cart_product_id'] ) { // phpcs:ignore

				GetGiftWrapping::data(
					$variables,
					function( $boxes ) use ( $base_sku, $variables ) {

						// Loop though the box types.
						foreach ( $boxes as $box ) {

							// Apply the field filter.
							add_filter(
								'objectiv_addon_hidden_addon_price',
								function( $html, $field_args, $addon_args, $cart_item_key ) use ( $box ) {
									$price = ( isset( $box['entry_price'] ) && $box['entry_price'] ) ? $box['entry_price'] : $base_sku;
									return '<input id="addon_price-' . $cart_item_key . '" name="' . $field_args['addon_price'] . '" value="' . $price . '" type="hidden">';
								},
								10,
								4
							);
						}
					}
				);
			}
		}
	}
}
