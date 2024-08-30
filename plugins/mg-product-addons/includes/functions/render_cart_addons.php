<?php
/**
 * Render the addon fields in the cart for each procuct.
 *
 * Load: true
 *
 * @package    MG_Product_Addons
 * @subpackage MG_Product_Addons/Includes/Core
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    GNU General Public License v2 or later
 * @version    1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! function_exists( 'objectiv_render_cart_addons' ) ) {
	/**
	 * Example Function.
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 *
	 * @param int   $cart_item_product_id The cart item product id.
	 * @param array $cart_item            The cart item array.
	 *
	 * @return sring
	 */
	function objectiv_render_cart_addons( $cart_item_product_id, $cart_item ) {
		$addons           = array();
		$addons['addons'] = ( function_exists( 'get_field' ) ) ? get_field( 'woo_mg_products_product_addons', 'option' ) : null;
		$render_fields    = new MG_Product_Addons\Includes\Classes\CartAddons( $addons, $cart_item_product_id, $cart_item );
		return $render_fields->render_fields();
	}
}
