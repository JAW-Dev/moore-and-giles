<?php
/**
 * Coupon Discount
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

if ( ! class_exists( 'CouponDiscount' ) ) {

	/**
	 * Coupon Discount
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class CouponDiscount {

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
		 * Apply Discount.
		 *
		 * @author Jason Witt
		 * @since  1.1.0
		 *
		 * @param int    $discount  The discount amount.
		 * @param object $cart_item The cart item object.
		 * @param object $coupon    The coupon object.
		 *
		 * @return int
		 */
		public function get( $discount, $cart_item, $coupon ) {
			$coupon_settings = new CouponSettings();
			$coupon_id       = $coupon->get_id();
			$settings        = $coupon_settings->get( $coupon_id );
			$coupon_code     = $coupon->get_code();
			$coupon_amount   = $coupon->get_amount();
			$has             = new HasAddon();
			$addon           = false;
			$price           = 0;

			if ( isset( $settings['code'] ) && $settings['code'] === $coupon_code ) {
				$addon = $has->personalization( $cart_item );

				if ( ! $addon ) {
					return $discount = 0;
				}

				if ( $settings['box'] === $addon['addon_sku'] ) {
					$price += $addon['addon_price'];
				} elseif ( 'all' === $settings['box'] ) {
					$price += $addon['addon_price'];
				}

				if ( isset( $settings['enabled'] ) && $settings['enabled'] && $addon ) {
					if ( 'percent' === $coupon->get_discount_type() ) {
						$discount = round( $price * ( $coupon_amount / 100 ), wc_get_rounding_precision() );
					}
				}
				return $discount;
			}
			return 0;
		}
	}
}
