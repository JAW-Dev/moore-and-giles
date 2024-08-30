<?php
/**
 * Has Addon
 *
 * @package    MG_Product_Addons_Gift_Wrapping
 * @subpackage MG_Product_Addons_Gift_Wrapping/Includes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Product_Addons_Gift_Wrapping\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( 'HasAddon' ) ) {

	/**
	 * Has Addon
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class HasAddon {

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
		 * Personalization.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $cart_item The cart item.
		 *
		 * @return boolean
		 */
		public function personalization( $cart_item ) {
			$product_addons = isset( $cart_item['product_addons'] ) ? $cart_item['product_addons'] : array();

			// Bail if product addons is empty.
			if ( empty( $product_addons ) ) {
				return false;
			}

			foreach ( $product_addons as $product_addon ) {
				$personalization = isset( $product_addon['addon-gift-wrapping'] ) ? $product_addon['addon-gift-wrapping'] : false;
				return $personalization;
			}
			return false;
		}
	}
}
