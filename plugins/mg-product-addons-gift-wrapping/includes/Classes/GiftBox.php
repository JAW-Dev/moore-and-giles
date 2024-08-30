<?php
/**
 * Gift Box
 *
 * @package    MG_Product_Addons_Gift_Wrapping
 * @subpackage MG_Product_Addons_Gift_Wrapping/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Product_Addons_Gift_Wrapping\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\GiftBox' ) ) {

	/**
	 * Gift Box
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class GiftBox {

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
			add_action( 'woocommerce_payment_successful_result', array( $this, 'add' ), 10, 2 );
			add_filter( 'woocommerce_display_item_meta', array( $this, 'giftbox_checkout_render' ), 10, 3 );
		}

		/**
		 * Giftbox Checkout Render
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $html The html.
		 * @param object $item The item Object.
		 * @param array  $args The arguments.
		 *
		 * @return string
		 */
		public function giftbox_checkout_render( $html, $item, $args ) {
			$html = '';

			foreach ( $item->get_formatted_meta_data() as $meta_id => $meta ) {
				$meta_value  = 'Gift Wrapping Box' !== $meta->display_key ? $meta->display_value : '</strong><p>&nbsp;</p>';
				$meta_key    = 'Gift Wrapping Box' !== $meta->display_key ? $meta->display_key : str_replace( ':', '', $meta->display_key );
				$label_after = 'Gift Wrapping Box' !== $meta->display_key ? $args['label_after'] : '';
				$value       = $args['autop'] ? wp_kses_post( $meta_value ) : wp_kses_post( make_clickable( trim( $meta_value ) ) );
				$strings[]   = $args['label_before'] . wp_kses_post( $meta_key ) . $label_after . $value;
			}

			if ( $strings ) {
				$html = $args['before'] . implode( $args['separator'], $strings ) . $args['after'];
			}

			return $html;
		}

		/**
		 * Add.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $result   The order result.
		 * @param int   $order_id The oder ID.
		 *
		 * @return array
		 */
		public function add( $result, $order_id ) {
			$order = wc_get_order( $order_id );
			$args  = array(
				'post_type'     => 'product',
				'post_name__in' => array( 'gift-wrapping' ),
			);

			$posts = get_posts( $args );

			// Bail if there are no posts.
			if ( empty( $posts ) ) {
				return $result;
			}

			$gw_product_id     = $posts[0]->ID;
			$gw_product        = wc_get_product( $gw_product_id );
			$gw_variations     = method_exists( $gw_product, 'get_available_variations' ) ? $gw_product->get_available_variations() : new \stdClass();
			$gw_variation_meta = array();
			$gift_boxes        = array();
			$quantity          = array();

			// Bail if there is no gift wrapping variation.
			if ( empty( $gw_variations ) ) {
				return $result;
			}

			// Array of the product variations.
			foreach ( $gw_variations as $gw_variation ) {
				$gw_variation_meta[] = array(
					'sku' => $gw_variation['sku'],
					'ID'  => $gw_variation['variation_id'],
				);
			}

			// Loop through the products.
			foreach ( $order->get_items() as $item_key => $item_value ) {
				$product_quantity  = $item_value['quantity'];
				$gift_wrapping_sku = wc_get_order_item_meta( $item_key, 'Gift Wrapping Box', true );

				if ( $gift_wrapping_sku ) {

					// Set the combined product quantity for each product.
					if ( empty( $quantity[ $gift_wrapping_sku ] ) ) {
						$quantity[ $gift_wrapping_sku ] = $product_quantity;
					} else {
						$quantity[ $gift_wrapping_sku ] = $quantity[ $gift_wrapping_sku ] + $product_quantity;
					}

					foreach ( $gw_variation_meta as $variation_key => $variation_value ) {
						if ( isset( $variation_value['sku'] ) && $variation_value['sku'] === $gift_wrapping_sku ) {

							// Build the array for the gift boxes to be added to order.
							$gift_boxes[ $variation_value['sku'] ] = array(
								'sku'      => $variation_value['sku'],
								'product'  => wc_get_product( $variation_value['ID'] ),
								'quantity' => $product_quantity,
							);
						}
					}
				}
			}

			// Add the gift boxes to the order.
			foreach ( $gift_boxes as $gift_box ) {
				$gift_box['product']->set_price( 0 );
				$order->add_product( $gift_box['product'], $quantity[ $gift_box['sku'] ] );
			}
			return $result;
		}
	}
}
