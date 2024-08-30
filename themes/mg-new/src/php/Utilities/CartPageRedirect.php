<?php
/**
 * Cart page redirect
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

/**
 * CartPageRedirect
 *
 * @author Jason Witt
 */
class CartPageRedirect {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Hooks.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public static function redirect() {
		if ( ! empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
			return;
		}

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
			return;
		}

		if ( is_cart() ) {
			wp_safe_redirect( trailingslashit( '/shop' ) . '?open-cart=true' );
			exit;
		}
	}
}
