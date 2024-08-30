<?php
/**
 * Get Item Data
 *
 * Display the addon data on the cart and checkout.
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

if ( ! class_exists( __NAMESPACE__ . '\\GetItemData' ) ) {

	/**
	 * Get Item Data
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class GetItemData {

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
			add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 20, 2 );
		}

		/**
		 * Render in cart.
		 *
		 * Sets the addon data to be displayed in the cart and checkout.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $item_data The item data to display.
		 * @param array $cart_item The cart item array.
		 *
		 * @return array
		 */
		public function get_item_data( $item_data, $cart_item ) {
			$cart = WC()->cart->get_cart_contents();

			foreach ( $cart as $cart_key => $cart_value ) {

				if ( $cart_key == $cart_item['key'] ) { // phpcs:ignore
					$addons = isset( $cart_item['product_addons']['addons'] ) ? $cart_item['product_addons']['addons'] : false; // Get the addons else false.

					// If $addons is not an empty array.
					if ( $addons ) {

						// Loop through the addons.
						foreach ( $addons as $addon ) {
							$addon_slug  = isset( $addon['addon_slug'] ) ? $addon['addon_slug'] : '';  // Get $addon['addon_slug'] if is set or empty string.
							$addon_name  = isset( $addon['addon_name'] ) ? $addon['addon_name'] : '';  // Get $addon['addon_name'] if is set else empty string.
							$addon_value = isset( $addon['addon_sku'] ) ? $addon['addon_sku'] : '';    // Get $addon['addon_sku'] if is set else empty string.

							// If addon valie is set.
							if ( $addon_value ) {

								// Add to the item data array.
								$item_data[] = array(
									'name'    => $addon_name,
									'value'   => wc_clean( $addon_value ),
									'default' => '',
								);
							}
						}
					}
				}
			}
			return $item_data;
		}
	}
}
