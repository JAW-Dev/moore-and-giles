<?php
/**
 * Personalization Coupon
 *
 * @package    MGSpecialtyCoupons
 * @subpackage MGSpecialtyCoupons/Includes/Classes/Personalization
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MGSpecialtyCoupons\Includes\Classes\Personalization;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Coupon' ) ) {

	/**
	 * Personalization Coupon
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
		 * Hooks.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function hooks() {
			add_action( 'woocommerce_coupon_is_valid', array( $this, 'is_valid' ), 10, 2 );
			add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'apply_discount' ), 10, 5 );
		}

		/**
		 * Is Coupon Valid
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param boolean $valid  If the coupon is valid.
		 * @param object  $coupon The coupon object.
		 *
		 * @throws Exception If coupon not valid.
		 *
		 * @return boolean
		 */
		public function is_valid( $valid, $coupon ) {
			$has_addon = array();

			// Bail if coupon type is not personalization.
			if ( 'personalization' === $coupon->coupon_type ) {

				foreach ( \WC()->cart->get_cart_contents() as $cart_item ) {
					$product_addons = isset( $cart_item['product_addons'] ) ? $cart_item['product_addons'] : array();

					// Bail if product addons is empty.
					if ( empty( $product_addons ) ) {
						continue;
					}

					foreach ( $product_addons as $product_addon ) {
						$gift_wrapping = isset( $product_addon['addon-personalization'] ) ? $product_addon['addon-personalization'] : array();

						if ( ! empty( $gift_wrapping ) ) {
							$has_addon[] = 'personalization';
						}
					}
				}

				if ( ! in_array( 'personalization', $has_addon, true ) ) {
					$valid = false;
				}

				if ( ! $valid ) {
					throw new \Exception( MG_SPECIALTY_COUPONS_ERROR_MESSAGE, 100 );
				}
			}

			return $valid;
		}

		/**
		 * Apply Discount.
		 *
		 * @author Jason Witt
		 * @since  1.1.0
		 *
		 * @param int     $discount           The discount amount.
		 * @param int     $discounting_amount Amount the coupon is being applied to.
		 * @param object  $cart_item          The cart item object.
		 * @param boolean $single             True if discounting a single qty item, false if its the line.
		 * @param object  $coupon             The coupon object.
		 *
		 * @return int
		 */
		public function apply_discount( $discount, $discounting_amount, $cart_item, $single, $coupon ) {
			if ( 'personalization' === $coupon->coupon_type ) {

				$coupon_data = $coupon->get_data();
				$discount    = 0;
				$addon       = isset( $cart_item['product_addons']['addons']['addon-personalization'] ) ? $cart_item['product_addons']['addons']['addon-personalization'] : array();

				if ( ! empty( $addon ) ) {
					$price = $coupon_data['amount'] > 0 ? $coupon_data['amount'] : $addon['addon_price'];

					if ( 'percent' === $coupon->get_discount_type() ) {
						$price = round( $addon['addon_price'] * ( $coupon_data['amount'] / 100 ), wc_get_rounding_precision() );
					}

					$discount += $price;
				}

				return $discount;
			}

			return $discount;
		}
	}
}
