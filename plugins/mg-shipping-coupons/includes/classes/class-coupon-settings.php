<?php
/**
 * Coupon Settings
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

if ( ! class_exists( 'Coupon_Settings' ) ) {

	/**
	 * Coupon Settings
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Coupon_Settings {

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
		 * @param array $args The plugin arguments.
		 * @param int   $coupon The coupon ID.
		 *
		 * @return array
		 */
		public function get( $coupon_id ) {
			$options = array();

			if ( self::get_saved_meta( MGSC_FIELD_ID_CODE, $coupon_id ) ) {
				$options['code'] = self::get_saved_meta( MGSC_FIELD_ID_CODE, $coupon_id );
			}

			if ( self::get_saved_meta( MGSC_FIELD_ID_ENABLE, $coupon_id ) ) {
				$options['enabled'] = self::get_saved_meta( MGSC_FIELD_ID_ENABLE, $coupon_id );
			}

			if ( self::get_saved_meta( MGSC_FIELD_ID_METHOD, $coupon_id ) ) {
				$options['method'] = self::get_saved_meta( MGSC_FIELD_ID_METHOD, $coupon_id );
			}

			if ( isset( $options['method'] ) && self::get_saved_meta( MGSC_FIELD_ID_SERVICE . '_' . $options['method'], $coupon_id ) ) {
				$options['service'] = self::get_saved_meta( MGSC_FIELD_ID_SERVICE . '_' . $options['method'], $coupon_id );
			}

			return $options;
		}
	}
}
