<?php
/**
 * Coupon
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

if ( ! class_exists( 'Coupon' ) ) {

	/**
	 * Coupon
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
			add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'apply_discount' ), 10, 5 );
			add_action( 'woocommerce_applied_coupon', array( $this, 'apply' ) );
			add_filter( 'woocommerce_coupon_message', array( $this, 'error_message' ), 10, 3 );
		}

		/**
		 * Apply.
		 *
		 * @author Jason Witt
		 * @since  1.1.0
		 *
		 * @param array $coupon_code The coupon code array.
		 *
		 * @return mixed
		 */
		public function apply( $coupon_code ) {
			$coupon = new CouponApply();
			return $coupon->apply( $coupon_code );
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
			$coupon_discount = new CouponDiscount();
			return $coupon_discount->get( $discount, $cart_item, $coupon );
		}

		/**
		 * Error Message.
		 *
		 * @author Jason Witt
		 * @since  1.1.0
		 *
		 * @param string $message  The error message.
		 * @param int    $err_code The error code.
		 * @param object $coupon   The coupon object.
		 *
		 * @return string
		 */
		public function error_message( $message, $err_code, $coupon ) {
			$error = new CouponError();
			return $error->message( $message, $err_code, $coupon );
		}
	}
}
