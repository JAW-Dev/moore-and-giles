<?php
/**
 * Coupon.
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

if ( ! class_exists( __NAMESPACE__ . '\\Coupon' ) ) {

	/**
	 * Coupon
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Coupon extends \WC_Shipping {

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
			add_action( 'woocommerce_checkout_init', array( $this, 'get_rates' ) );
			add_action( 'woocommerce_applied_coupon', array( $this, 'apply' ), 20 );
			add_filter( 'woocommerce_coupon_get_discount_amount', array( $this, 'apply_discount' ), 1, 5 );
			add_filter( 'woocommerce_cart_shipping_method_full_label', array( $this, 'shipping_label'), 10, 2 );
		}

		/**
		 * Apply.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $coupon_code The coupon code array.
		 *
		 * @return mixed
		 */
		public function apply( $coupon_code ) {
			$coupon = new Coupon_Apply();
			$rates  = $this->get_rates();

			return $coupon->apply( $coupon_code, $rates );
		}

		/**
		 * Apply Discount.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
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
			$coupon_discount = new Coupon_Discount();
			return $coupon_discount->get( $discount, $cart_item, $coupon );
		}

		/**
		 * Get Rates.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get_rates() {
			$rates_class = new Rates();
			return $rates_class->get();
		}

		/**
		 * Shipping Label.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $label  The label markup.
		 * @param object $method The method object.
		 *
		 * @return string
		 */
		public function shipping_label( $label, $method ) {
			$label    = $method->get_label();
			$has_cost = 0 < $method->cost;

			if ( $has_cost ) {
				if ( WC()->cart->display_prices_including_tax() ) {
					$label .= ': ' . wc_price( $method->cost + $method->get_shipping_tax() );
					if ( $method->get_shipping_tax() > 0 && ! wc_prices_include_tax() ) {
						$label .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
					}
				} else {
					$label .= ': ' . wc_price( $method->cost );
					if ( $method->get_shipping_tax() > 0 && wc_prices_include_tax() ) {
						$label .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
					}
				}
			} else {
				$label .= ': Free!';
			}

			return $label;
		}
	}
}
