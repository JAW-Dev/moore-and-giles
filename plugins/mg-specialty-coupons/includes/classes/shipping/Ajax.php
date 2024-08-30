<?php
/**
 * Ajax
 *
 * @package    MGSpecialtyCoupons
 * @subpackage MGSpecialtyCoupons/Includes/Classes/Shipping
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MGSpecialtyCoupons\Includes\Classes\Shipping;

use MGSpecialtyCoupons\Includes\Classes as Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Ajax' ) ) {

	/**
	 * Ajax
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Ajax {

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
			add_action( 'wp_ajax_check_shipping', array( $this, 'check_shipping' ) );
			add_action( 'wp_ajax_nopriv_check_shipping', array( $this, 'check_shipping' ) );
		}

		/**
		 * Check Shipping.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function check_shipping() {
			$post = Classes\Post::request();

			// Bail if thee is no post request.
			if ( empty( $post ) ) {
				wp_die();
			}

			$shipping_method = WC()->session->get( 'chosen_shipping_methods' );
			$coupon_code     = !empty( $post['coupon_code'] ) ? strtolower(  $post['coupon_code'] ) : '';
			$coupon          = new \WC_Coupon( $coupon_code );
			$coupon_id       = $coupon->get_id();

			$settings = array();
			$settings['method'] = get_post_meta( $coupon_id, 'objectiv_shipping_coupons_method', true );
			$settings['service'] = get_post_meta( $coupon_id, 'objectiv_shipping_coupons_service_' . $settings['method'], $coupon_id, true );

			$method      = !empty( $settings['method'] ) ? $settings['method'] : '';
			$service     = !empty( $settings['service'] ) ? $settings['service'] : '';
			$coupon_type = $method . ':' . $service;

			echo esc_html( $coupon_type );

			wp_die();
		}
	}
}
