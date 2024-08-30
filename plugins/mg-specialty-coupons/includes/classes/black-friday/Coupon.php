<?php
/**
 * BlackFriday Coupon.
 *
 * @package    MG_Specialty_Coupons
 * @subpackage MG_Specialty_Coupons/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    GPL-2.0
 * @since      1.0.0
 */

namespace MGSpecialtyCoupons\Includes\Classes\BlackFirday;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Coupon' ) ) {

	/**
	 * BlackFriday Coupon.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Coupon {

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
		 * Hooks
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function hooks() {
			add_action( 'woocommerce_coupon_get_discount_amount', array( $this, 'apply_discount' ), 10, 5 );
		}

		/**
		 * Apply Discount
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param int       $discount           The discount amount.
		 * @param int       $discounting_amount Amount the coupon is being applied to.
		 * @param array     $cart_item          The cart item object.
		 * @param boolean   $single             True if discounting a single qty item, false if its the line.
		 * @param WC_Coupon $coupon             The coupon object.
		 *
		 * @return int
		 */
		public function apply_discount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {

			if ( empty( $discount ) ) {
				$discount = 0;
			}

			if ( ! empty( $coupon ) && 'black-friday' === $coupon->coupon_type ) {

				if ( ! empty( $discounting_amount ) && $discounting_amount > 0 ) {
					$discount        = $discounting_amount;
					$personalization = isset( $cart_item['product_addons']['addons']['addon-personalization']['addon_price'] ) ? $cart_item['product_addons']['addons']['addon-personalization']['addon_price'] : 0;
					$gift_wrapping   = isset( $cart_item['product_addons']['addons']['addon-gift-wrapping']['addon_price'] ) ? $cart_item['product_addons']['addons']['addon-gift-wrapping']['addon_price'] : 0;

					if ( $personalization || $gift_wrapping ) {

						if ( $personalization && ! $gift_wrapping ) {
							$discount = $discount - $personalization;
						}

						if ( $gift_wrapping && ! $personalization ) {
							$discount = $discount - $gift_wrapping;
						}

						if ( $personalization && $gift_wrapping ) {
							$discount = ( $discount - $gift_wrapping ) - $personalization;
						}
					}
				}

				return $discount;
			}

			return $discount;
		}
	}
}
