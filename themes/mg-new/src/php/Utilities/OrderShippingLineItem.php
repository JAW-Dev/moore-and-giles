<?php
/**
 * Order Line Item Meta
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

/**
 * ProductNewTag
 *
 * @author Jason Witt
 */
class OrderShippingLineItem {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Set the line item meta data.
	 *
	 * @author Jason Witt
	 *
	 * @param object $item          The item in the order object.
	 * @param int    $cart_item_key The cart item key.
	 * @param array  $values        The cart item values.
	 * @param object $order         The order object.
	 *
	 * @return void
	 */
	public static function set( $item, $cart_item_key, $values, $order ) {
		self::shipping_method( $item, $cart_item_key, $values, $order );
		self::shipping_address( $item, $cart_item_key, $values, $order );
	}

	/**
	 * Shipping Address
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param object $item          The item in the order object.
	 * @param int    $cart_item_key The cart item key.
	 * @param array  $values        The cart item values.
	 * @param object $order         The order object.
	 *
	 * @return void
	 */
	public static function shipping_address( $item, $cart_item_key, $values, $order ) {
		$shipping1 = $order->get_shipping_address_1();
		$item->update_meta_data( '_mg_shipping_address_1', $shipping1 );

		$shipping2 = $order->get_shipping_address_2();
		$item->update_meta_data( '_mg_shipping_address_2', $shipping2 );

		$city = $order->get_shipping_city();
		$item->update_meta_data( '_mg_shipping_city', $city );

		$state = $order->get_shipping_state();
		$item->update_meta_data( '_mg_shipping_state', $state );

		$postcode = $order->get_shipping_postcode();
		$item->update_meta_data( '_mg_shipping_postcode', $postcode );

		$country = $order->get_shipping_country();
		$item->update_meta_data( '_mg_shipping_country', $country );
	}

	/**
	 * Shipping Method
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param object $item          The item in the order object.
	 * @param int    $cart_item_key The cart item key.
	 * @param array  $values        The cart item values.
	 * @param object $order         The order object.
	 *
	 * @return void
	 */
	public static function shipping_method( $item, $cart_item_key, $values, $order ) {
		$rates     = self::rates( $cart_item_key );
		$item_name = $item->get_name() ? $item->get_name() : '';

		// Bail if rates is empty or no item name.
		if ( empty( $rates ) || ! $item_name ) {
			return;
		}

		foreach ( $rates as $rate ) {
			$label = isset( $rate['label'] ) ? $rate['label'] : '';

			if ( $label ) {
				$item->update_meta_data( '_mg_shipping_method', $label );
			}
		}
	}

	/**
	 * Rates.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public static function rates( $cart_item_key ) {
		$array = array();

		foreach ( WC()->shipping->get_packages() as $key => $value ) {

			$key          = isset( $value['contents'][ $cart_item_key ]['key'] ) ? $value['contents'][ $cart_item_key ]['key'] : '';
			$product_id   = isset( $value['contents'][ $cart_item_key ]['product_id'] ) ? $value['contents'][ $cart_item_key ]['product_id'] : '';
			$variation_id = isset( $value['contents'][ $cart_item_key ]['variation_id'] ) ? $value['contents'][ $cart_item_key ]['variation_id'] : '';

			if ( ! isset( $key ) || $cart_item_key !== $key ) {
				continue;
			}

			// Bail if rates is not set.
			if ( ! isset( $value['rates'] ) ) {
				return array();
			}

			$post = ! empty( $_POST ) ? $_POST : ''; // phpcs:ignore
			$shipping_methods = ! empty( $post['shipping_method'] ) ? $post['shipping_method'] : '';

			foreach ( $value['rates'] as $rate_id => $rate ) {
				if ( in_array( $rate->get_id(), $shipping_methods, true ) ) {
					$label = $rate->get_label() ? $rate->get_label() : '';
					$meta  = $rate->get_meta_data() ? $rate->get_meta_data() : array();
					$item  = ! empty( $meta ) && isset( $meta['Items'] ) ? trim( substr( $meta['Items'], 0, strpos( $meta['Items'], '&' ) ) ) : '';

					$array[] = array(
						'label'        => $label,
						'item'         => $item,
						'product_id'   => $product_id,
						'variation_id' => $variation_id,
					);
				} else {
					continue;
				}
			}
		}
		return $array;
	}
}
