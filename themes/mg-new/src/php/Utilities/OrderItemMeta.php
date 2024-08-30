<?php
/**
 * Order Item Meta
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

/**
 * OrderItemMeta
 *
 * @author Jason Witt
 */
class OrderItemMeta {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Backorder Release Date
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public static function backorder_release_date( $item_id, $cart_item, $order_id ) {

		if ( ! empty( $cart_item ) ) {
			$product_id   = ! empty( $cart_item['product_id'] ) ? $cart_item['product_id'] : '';
			$variation_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : '';
			$product_id   = ! empty( $variation_id ) ? $variation_id : $product_id;
			$product      = ! empty( $product_id ) ? wc_get_product( $product_id ) : array();
			$release_date = '';

			if ( ! empty( $product ) ) {
				$product_data = $product->get_data();

				if ( ! empty( $product_data ) && $product_data['backorders'] ) {
					$release_date = get_post_meta( $product_id, 'release_date', true );

					if ( $release_date !== 'Array' ) {
						wc_update_order_item_meta( $item_id, '_mg_backorder_release_date', $release_date );
					}
				}
			}
		}
	}
}
