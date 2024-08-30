<?php
/**
 * Settings
 *
 * @package    MGSpecialtyCoupons
 * @subpackage MGSpecialtyCoupons/Includes/Classes/Shipping
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MGSpecialtyCoupons\Includes\Classes\Shipping;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Settings' ) ) {

	/**
	 * Settings
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Settings {

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
		 * Get Saved Meta.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $key       The meta data key.
		 * @param int    $coupon_id The coupon ID.
		 *
		 * @return string
		 */
		public function get_saved_meta( $key, $coupon_id ) {
			return metadata_exists( 'post', $coupon_id, $key ) && isset( get_post_meta( $coupon_id, $key )[0] ) ? get_post_meta( $coupon_id, $key )[0] : '';
		}

		/**
		 * Get Coupon Settings.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param int $coupon_id The coupon ID.
		 *
		 * @return array
		 */
		public function get( $coupon_id ) {
			$options = array();

			$options['code']   = get_post_meta( 'objectiv_shipping_coupons_code', $coupon_id, true );
			$options['method'] = get_post_meta( 'objectiv_shipping_coupons_method', $coupon_id, true );
			$options['service'] = get_post_meta( 'objectiv_shipping_coupons_service_' . $options['method'], $coupon_id, true );

			error_log( 'code: ' . print_r(get_post_meta( 'objectiv_shipping_coupons_code', $coupon_id, true ), true ) ); // phpcs:ignore
			error_log( 'method: ' . print_r(get_post_meta( 'objectiv_shipping_coupons_method', $coupon_id, true ), true ) ); // phpcs:ignore
			error_log( 'service: ' . print_r(get_post_meta( 'objectiv_shipping_coupons_service_' . $options['method'], $coupon_id, true ), true ) ); // phpcs:ignore

			return $options;
		}
	}
}
