<?php
/**
 * Coupon Settings
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

if ( ! class_exists( 'CouponSettings' ) ) {

	/**
	 * Coupon Settings
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class CouponSettings {

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

			if ( self::get_saved_meta( MGGWA_FIELD_ID_ENABLE, $coupon_id ) ) {
				$options['enabled'] = self::get_saved_meta( MGGWA_FIELD_ID_ENABLE, $coupon_id );
			}

			if ( self::get_saved_meta( MGGWA_FIELD_ID_CODE, $coupon_id ) ) {
				$options['code'] = self::get_saved_meta( MGGWA_FIELD_ID_CODE, $coupon_id );
			}

			if ( self::get_saved_meta( MGGWA_FIELD_ID_BOX, $coupon_id ) ) {
				$options['box'] = self::get_saved_meta( MGGWA_FIELD_ID_BOX, $coupon_id );
			}

			return $options;
		}
	}
}
