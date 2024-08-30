<?php
/**
 * Get Gift Wrapping
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

if ( ! class_exists( __NAMESPACE__ . '\\GetGiftWrapping' ) ) {

	/**
	 * Get Gift Wrapping
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class GetGiftWrapping {

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {}

		/**
		 * Get Gift Wrapping.
		 *
		 * Get the values of the Gift Wrapping ACF fields.
		 *
		 * @author Jason Witt
		 * @since  1.1.0
		 *
		 * @param array  $args {
		 *      The arguments.
		 *
		 *      @type string $addon_id        The addon ID.
		 *      @type string $addon_slug      The addon slug.
		 *      @type string $addon_label     The addon label.
		 *      @type string $tooltip         The tooltip text.
		 *      @type string $cart_product_id The cart item product ID.
		 *      @type array $arguments = array(
		 *          @type array  $addon_args        The addon arguments.
		 *          @type string $hidden_fields     The hidden fields markup.
		 *          @type string $additional_fields The additional fields markup.
		 *          @type array  $cart_item         The cart item array.
		 *      )
		 * }
		 * @param string $callback The callback.
		 *
		 * @return string
		 */
		public static function data( $args, $callback ) {
			$entries          = ( function_exists( 'get_field' ) && isset( $args['addon_id'] ) ) ? get_field( 'mg_box_sizes', 'option' ) : array();
			$parent_terms     = ( function_exists( 'get_the_terms' ) && isset( $args['cart_product_id'] ) ) ? get_the_terms( $args['cart_product_id'], 'product_cat' ) : array(); // Get product_cat for cart item else empty array.
			$term_ids         = ( function_exists( 'wp_list_pluck' ) ) && isset( $parent_terms['term_id'] ) ? wp_list_pluck( $parent_terms, 'term_id' ) : array(); // Get term ID array else empty array.
			$boxes            = array();
			$product_id       = isset( $args['cart_product_id'] ) ? $args['cart_product_id'] : '';
			$product_wrapping = function_exists( 'get_field' ) && $product_id ? get_field( 'mg_product_enable_gift_wrapping', $product_id ) : false;

			if ( ! $product_wrapping ) {
				return;
			}

			$wrapping_size = function_exists( 'get_field' ) && $product_wrapping ? get_field( 'mg_gift_box_size', $product_id ) : 'large';

			foreach ( $entries as $entry ) {

				$entry_size = isset( $entry['size'] ) ? $entry['size'] : '';

				// If the size isn't the same continue to next iteration.
				if ( $entry_size !== $wrapping_size ) {
					continue;
				}

				$entry_sku   = isset( $entry['sku'] ) ? $entry['sku'] : '';
				$entry_price = isset( $entry['price'] ) ? $entry['price'] : '';

				$included_products   = isset( $entry['included_products'] ) ? $entry['included_products'] : array();
				$is_product_included = FieldContainerMarkup::is_in_array( $included_products, array( $args['cart_product_id'] ) );

				$included_categories  = isset( $entry['included_categories'] ) ? $entry['included_categories'] : array();
				$is_category_included = FieldContainerMarkup::is_in_array( $included_categories, $term_ids );

				$boxes[] = array(
					'entry_sku'            => $entry_sku,
					'entry_price'          => $entry_price,
					'entry_size'           => $entry_size,
					'cart_product_id'      => $args['cart_product_id'],
					'is_product_included'  => $is_product_included,
					'is_category_included' => $is_category_included,
				);
			}

			$boxes = apply_filters( 'mg_gift_wrapping_boxes', $boxes );

			return call_user_func( $callback, $boxes );
		}
	}
}
