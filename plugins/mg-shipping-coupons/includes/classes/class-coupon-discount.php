<?php
/**
 * Coupon Discount
 *
 * @package    MG_Shipping_Coupons
 * @subpackage MG_Shipping_Coupons/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Shipping_Coupons\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( 'Coupon_Discount' ) ) {

	/**
	 * Coupon Discount
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Coupon_Discount {

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
		 * @since  1.0.0
		 *
		 * @param int    $discount  The discount amount.
		 * @param object $cart_item The cart item object.
		 * @param object $coupon    The coupon object.
		 *
		 * @return int
		 */
		public function get( $discount, $cart_item, $coupon ) {
			$coupon_settings = new Coupon_Settings();
			$coupon_id       = $coupon->get_id();
			$settings        = $coupon_settings->get( $coupon_id );
			$coupon_code     = $coupon->get_code();
			$discount_amount = $coupon->get_amount();

			if ( isset( $settings['code'] ) && $settings['code'] === $coupon_code ) {
				$coupon_type = isset( $settings['service'] ) ? $settings['method'] . ':' . $settings['service'] : '';
				$cart        = \WC()->cart->get_cart();
				$cart_count  = count( $cart );
				$rates_class = new Rates();
				$rates       = $rates_class->get();

				if ( ! empty( $rates ) && isset( $settings['enabled'] ) && $settings['enabled'] ) {
					foreach ( $rates as $rate ) {
						if ( $coupon_type === $rate['id'] ) {
							if ( 'percent' === $coupon->get_discount_type() ) {
								$discount_amount = 0;

								add_filter(
									'woocommerce_package_rates',
									function( $rates, $packages ) use ( $coupon_type, $discount_amount, $cart_count ) {
										foreach ( $rates as $rate ) {
											if ( $rate->get_id() === $coupon_type ) {
												$cost          = $rate->get_cost();
												$adjusted_cost = round( $cost * ( $discount_amount / 100 ), wc_get_rounding_precision() ) / $cart_count;
												$rate->set_cost( $adjusted_cost );
											}
										}
										return $rates;
									},
									10,
									2
								);

								// Only apply coupon once.
								foreach ( $cart as $key => $value ) {
									if ( isset( $value['has_applied_discount'] ) ) {
										return $discount_amount;
									} else {
										WC()->cart->cart_contents[ $key ]['has_applied_discount'] = true;
									}
								}
								WC()->cart->set_session();
							}
						}
					}
					return $discount_amount;
				}
			}
			return 0;
		}
	}
}
