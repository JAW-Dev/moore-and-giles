<?php
/**
 * Cart Button Fragment
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

/**
 * CartButtonFragment
 *
 * @author Jason Witt
 */
class CartButtonFragment {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Refresh the car button after cart is updated.
	 *
	 * @author Jason Witt
	 *
	 * @param array $fragments The cart fragments.
	 *
	 * @return array
	 */
	public static function refresh( $fragments ) {
		$fragments['div.cart-button']  = '<div class="cart-button"></div>';
		$fragments['div.cart-message'] = '<div class="cart-message"></div>';

		if ( WC()->cart->get_cart_contents_count() > 0 ) {
			$fragments['div.cart-button'] = '<div class="cart-button"><a href="' . wc_get_checkout_url() . '" class="button button__orange cart-button sidebar-cart__button">Proceed to checkout</a></div>';
		}

		$cart_message = ( function_exists( 'get_field' ) ) ? obj_get_acf_field( 'woo_custom_cart_message', 'option' ) : '';

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			$has_personalization = isset( $cart_item['product_addons']['addons']['addon-personalization'] ) ? true : false;
			if ( $has_personalization ) {
				if ( $cart_message && $has_personalization ) {
					$fragments['div.cart-message'] = '<div class="cart-message">' . $cart_message . '</div>';
				}
				break;
			}
		}

		return $fragments;
	}
}
