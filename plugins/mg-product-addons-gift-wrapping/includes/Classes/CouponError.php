<?php
/**
 * Coupon Error
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

if ( ! class_exists( 'CouponError' ) ) {

	/**
	 * Coupon Error
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class CouponError {

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
		 * Message.
		 *
		 * @author Jason Witt
		 * @since  1.1.0
		 *
		 * @param string $message  The error message.
		 * @param int    $err_code The error code.
		 * @param object $coupon   The coupon object.
		 *
		 * @return string
		 */
		public function message( $message, $err_code, $coupon ) {
			$coupon_settings = new CouponSettings();
			$coupon_id       = $coupon->get_id();
			$settings        = $coupon_settings->get( $coupon_id );
			$coupon_code     = $coupon->get_code();
			$has             = new HasAddon();
			$has_addon       = false;

			if ( isset( $settings['code'] ) && $settings['code'] === $coupon_code ) {

				foreach ( \WC()->cart->get_cart_contents() as $cart_item ) {
					$addon = $has->personalization( $cart_item );

					if ( $addon ) {
						$has_addon = true;
						break;
					}
				}

				if ( ! $has_addon ) {
					$message = __( 'Sorry, this coupon is not applicable to your cart contents.', 'woocommerce' );
				}
			}

			return $message;
		}
	}
}
