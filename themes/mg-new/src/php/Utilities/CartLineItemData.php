<?php
/**
 * Cart Line Item Data
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

/**
 * Cart Line Ite mData
 *
 * @author Eldon Yoder
 */
class CartLineItemData {

	/**
	 * Initialize the class
	 *
	 * @author Eldon Yoder
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Last Chance.
	 *
	 * @author Jason Witt
	 *
	 * @param array $cart_item_data The cart array.
	 * @param int   $product_id    The product ID.
	 * @param int   $variation_id  The variation ID.
	 *
	 * @return array
	 */
	public static function last_chance( $cart_item_data, $product_id, $variation_id ) {
		$has_term = has_term( 'Last Chance', 'product_cat', $product_id );

		if ( $has_term ) {
			$cart_item_data['is_last_chance'] = 'Last Chance';
		}

		return $cart_item_data;
	}

	/**
	 * SHow Last Chance in cart
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param array $item_data The item data.
	 * @param array $cart_item The cart item.
	 *
	 * @return array
	 */
	public static function show_last_chance_in_cart( $item_data, $cart_item ) {
		if ( ! empty( $cart_item['is_last_chance'] ) ) {
			// Add Final Sale disclaimer
			if ( ! defined( 'CFW_CART_FINAL_SALE_DISCLAIMER_ADDED' ) ) {
				add_action( 'cfw_checkout_cart_summary', function() {
					echo '<small>**Final Sale items are not eligible for return.</small>';
				}, 75 );

				define( 'CFW_CART_FINAL_SALE_DISCLAIMER_ADDED', true );
			}

			$item_data[] = array(
				'key'     => $cart_item['is_last_chance'],
				'value'   => wc_clean( __( 'Final Sale**', 'moore-and-giles' ) ),
				'display' => '',
			);
		}

		return $item_data;
	}

	/**
	 * SHow Last Chance in cart (thank you)
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param array $html The item data.
	 * @param \WC_Order_Item $item The cart item.
	 *
	 * @return array
	 */
	public static function show_last_chance_in_cart_thank_you( $html, $item ) {
		/** @var \WC_Product $product */
		$product = $item->get_product();

		if ( ! is_object( $product ) ) {
			return $html;
		}

		if ( $product->get_type() === 'variation' ) {
			$product_id = $product->get_parent_id();
		} else {
			$product_id = $product->get_id();
		}

		if ( has_term( 'Last Chance', 'product_cat', $product_id ) ) {
			if ( ! defined( 'CFW_THANKYOU_FINAL_SALE_DISCLAIMER_ADDED' ) ) {
				add_action( 'cfw_thank_you_cart_summary', function() {
					echo '<small>**Final Sale items are not eligible for return.</small>';
				}, 45 );

				define( 'CFW_THANKYOU_FINAL_SALE_DISCLAIMER_ADDED', true );
			}

			$html = $html . "Final Sale**";
		}

		return $html;
	}
}
