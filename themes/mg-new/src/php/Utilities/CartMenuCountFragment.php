<?php
/**
 * Cart Menu Count Fragment
 * 
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

/**
 * CartMenuCountFragment
 *
 * @author Jason Witt
 */
class CartMenuCountFragment {

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
		$fragments['div.menu-item__cart-count-container'] = '<div class="menu-item__cart-count-container"></div>';
	
		if ( WC()->cart->get_cart_contents_count() > 0 ) {
			$fragments['div.menu-item__cart-count-container'] = '<div class="menu-item__cart-count-container"><div class="menu-item__cart-count">' . WC()->cart->get_cart_contents_count() . '</div></div>';
		}
		
		return $fragments;
	}
}
