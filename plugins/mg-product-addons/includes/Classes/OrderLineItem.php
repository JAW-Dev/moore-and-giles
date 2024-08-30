<?php
/**
 * Cart Addons Checkout
 *
 * @package    MG_Product_Addons
 * @subpackage MG_Product_Addons/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Product_Addons\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\OrderLineItem' ) ) {

	/**
	 * Save Cart Addons
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class OrderLineItem {

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
			add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'checkout_create_order_line_item' ), 10, 4 );
		}

		/**
		 * Save Data on Checkout.
		 *
		 * When the cart item is saved in the checkout.
		 * The addon data is saved to the itemmeta table.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param object $item          The item object.
		 * @param int    $cart_item_key The cart item key.
		 * @param array  $values        The values array.
		 * @param object $order         The order object.
		 *
		 * @return void
		 */
		public function checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
			$addons = isset( $values['product_addons']['addons'] ) && ! empty( $values['product_addons']['addons'] ) ? $values['product_addons']['addons'] : array(); // Get the values addon data else empty array.

			foreach ( $addons as $addon ) {
				$has_addon      = isset( $addon['has_addon'] ) ? $addon['has_addon'] : false;
				$addon_sku      = isset( $addon['addon_sku'] ) ? $addon['addon_sku'] : '';
				$addon_name     = isset( $addon['addon_name'] ) ? $addon['addon_name'] : '';
				$addon_sku_name = isset( $addon['addon_sku_name'] ) ? $addon['addon_sku_name'] : '';

				// If addon value and addon name is set.
				if ( $has_addon && $addon_name ) {
					// Set the addon to the order.
					$item->add_meta_data( $addon_name, str_replace( 'on', 'true', $has_addon ), true );
				}

				// If addon value and addon name is set.
				if ( $addon_sku && $addon_sku_name ) {
					// Set the addon to the order.
					$item->add_meta_data( $addon_sku_name, $addon_sku, true );
				}
			}
		}
	}
}
