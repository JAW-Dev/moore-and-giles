<?php
/**
 * Add Cart Item Data
 *
 * Set the Price of the Addon.
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

if ( ! class_exists( __NAMESPACE__ . '\\AddCartItemPrice' ) ) {

	/**
	 * Add Cart Item Data
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class AddCartItemPrice {

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
			add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 100, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 100, 2 );
		}

		/**
		 * Add to Total.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $cart_item The cart item.
		 *
		 * @return array
		 */
		public function add_cart_item( $cart_item ) {
			$addons = isset( $cart_item['product_addons']['addons'] ) && ! empty( $cart_item['product_addons']['addons'] ) ? $cart_item['product_addons']['addons'] : array(); // Get the cart item product_addons addons if is set else empty array.
			$price  = (float) $cart_item['data']->get_price( 'edit' );

			// If the addons is not an empty array.
			if ( ! empty( $addons ) ) {

				// Loop through the addons.
				foreach ( $addons as $addon ) {
					if ( $addon['addon_price'] > 0 ) {
						$price += (float) $addon['addon_price'];
					}
				}

				$cart_item['data']->set_price( $price );
			}

			return $cart_item;
		}

		/**
		 * Get cart from session.
		 *
		 * @param array $cart_item The cart Items.
		 * @param array $values    The set values.
		 *
		 * @return array
		 */
		public function get_cart_item_from_session( $cart_item, $values ) {
			if ( ! empty( $values['product_addons'] ) ) {
				$cart_item['product_addons'] = $values['product_addons'];
				$cart_item                   = self::add_cart_item( $cart_item );
			}

			return $cart_item;
		}
	}
}
