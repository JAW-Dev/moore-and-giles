<?php
/**
 * Shipping Ajax.
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

if ( ! class_exists( __NAMESPACE__ . '\\Shipping_Ajax' ) ) {

	/**
	 * Shipping Ajax
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Shipping_Ajax {

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
			$post = Post::request();

			// Bail if thee is no post request.
			if ( empty( $post ) ) {
				wp_die();
			}

			$shipping_method = WC()->session->get( 'chosen_shipping_methods' );
			$coupon_code     = isset( $post['coupon_code'] ) ? strtolower( str_replace( array( ' ', '_' ), '-', $post['coupon_code'] ) ) : '';
			$coupon          = new \WC_Coupon( $coupon_code );
			$coupon_settings = new Coupon_Settings();
			$coupon_id       = $coupon->get_id();
			$settings        = $coupon_settings->get( $coupon_id );

			if ( isset( $settings['enabled'] ) && $settings['enabled'] ) {
				$method      = isset( $settings['method'] ) ? $settings['method'] : '';
				$service     = isset( $settings['service'] ) ? $settings['service'] : '';
				$coupon_type = $method . ':' . $service;
				error_log( ': ' . print_r( $shipping_method[0], true ) ); // phpcs:ignore
				echo esc_html( $shipping_method[0] );

				wp_die();
			}

			echo false;
			error_log( ': ' . print_r( 'died', true ) ); // phpcs:ignore
			wp_die();
		}
	}
}
