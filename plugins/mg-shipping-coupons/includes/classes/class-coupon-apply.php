<?php
/**
 * Coupon Apply
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

if ( ! class_exists( 'Coupon_Apply' ) ) {

	/**
	 * Coupon Apply
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Coupon_Apply {

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
		 * Apply.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $coupon_code The coupon code array.
		 *
		 * @return void
		 */
		public function apply( $coupon_code ) {
			$coupon          = new \WC_Coupon( $coupon_code );
			$coupon_settings = new Coupon_Settings();
			$coupon_id       = $coupon->get_id();
			$settings        = $coupon_settings->get( $coupon_id );

			if ( isset( $settings['code'] ) && $settings['code'] === $coupon_code ) {
				$coupon_type     = isset( $settings['service'] ) ? $settings['method'] . ':' . $settings['service'] : '';
				$shipping_method = isset( WC()->session->get( 'chosen_shipping_methods' )[0] ) ? WC()->session->get( 'chosen_shipping_methods' )[0] : '';
				$rates_class     = new Rates();
				$rates           = $rates_class->get();

				if ( ! empty( $rates ) && $settings['enabled'] ) {


					if ( ! WC()->session->has_session() ) {
						WC()->session->set_customer_session_cookie( true );
					}

					if ( $shipping_method !== $coupon_type ) {
						WC()->session->set( 'chosen_shipping_methods', array( $coupon_type ) );
					}

					add_filter(
						'cfw_apply_coupon_response',
						function( $response ) use ( $coupon_type, $shipping_method ) {
							if ( $shipping_method !== $coupon_type && $coupon_type ) {
								WC()->session->set( 'chosen_shipping_methods', array( '0' => $coupon_type ) );
							}
							return $response;
						}
					);
				}
			}
		}
	}
}
