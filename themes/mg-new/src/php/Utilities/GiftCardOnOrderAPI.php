<?php
/**
 * Add the Gift Card Info to the Order API
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

/**
 * GetColorVariationData
 *
 * @author Jason Witt
 */
class GiftCardOnOrderAPI {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Add Info
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param \WP_REST_Response $response The response object.
	 *
	 * @return \WP_REST_Response
	 */
	public static function add_info( $response, $object ) {
		global $wpdb;

		$order_data     = $response->get_data();
		$cards_activity = ! empty( self::get_card_activity( $order_data, $wpdb ) ) ? self::get_card_activity( $order_data, $wpdb ) : array();
		$cards_info     = ! empty( self::get_card_info( $cards_activity, $wpdb ) ) ? self::get_card_info( $cards_activity, $wpdb ) : array();
		$total_spent    = 0;
		$data           = array();

		if ( ! empty( $cards_activity ) && $cards_activity[0]['type'] === 'issued' ) {
			return $response;
		}

		if ( ! empty( $cards_activity ) && ! empty( $cards_info ) ) {
			foreach ( $cards_activity as $card_activity ) {
				foreach ( $cards_info as $card_info ) {
					$card_id     = ! empty( $card_info['id'] ) ? $card_info['id'] : '';
					$activity_id = ! empty( $card_activity['gc_id'] ) ? $card_activity['gc_id'] : '';

					if ( ! empty( $card_id ) && ! empty( $activity_id ) ) {
						if ( $card_id === $activity_id ) {
							$data[ $card_info['code'] ] = array(
								'transaction' => $card_activity,
								'card'        => $card_info,
							);
						}
					}
				}
			}

			foreach ( $cards_activity as $card ) {
				$total_spent += ! empty( $card['amount'] ) ? $card['amount'] : 0;
			}
			$data['total'] = $total_spent;

			$order_data['gift_cards'] = $data;
		}

		$response->data = $order_data;

		return $response;
	}

	/**
	 * Get Card Info
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param array  $cards The cards activity info.
	 * @param object $wpdb  The WordPress database global object.
	 *
	 * @return array
	 */
	public static function get_card_info( $cards, $wpdb ) {
		if ( empty( $cards ) ) {
			return array();
		}

		global $wpdb;
		$cards_info     = array();
		$formatted_data = array();

		foreach ( $cards as $card ) {
			$card_id = ! empty( $card['gc_id'] ) ? $card['gc_id'] : '';

			if ( ! empty( $card_id ) ) {
				$prepare      = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_gc_cards WHERE id = %d", $card_id); // phpcs:ignore
				$cards_info[] = $wpdb->get_results( $prepare ); // phpcs:ignore
			}
		}

		if ( ! empty( $cards_info ) ) {
			foreach ( $cards_info as $card_info ) {
				$data             = ! empty( $card_info[0] ) ? (array) $card_info[0] : array();
				$formatted_data[] = $data;
			}
		}

		return $formatted_data;
	}

	/**
	 * Get Card Activity
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param array $order_data The order data.
	 * @param object $wpdb  The WordPress database global object.
	 *
	 * @return array
	 */
	public static function get_card_activity( $order_data, $wpdb ) {
		if ( empty( $order_data ) ) {
			return array();
		}

		$order_id   = ! empty( $order_data['id'] ) ? $order_data['id'] : '';
		$cards_info = array();

		if ( ! empty( $order_id ) ) {
			$prepare    = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_gc_activity WHERE object_id = %d", $order_id ); // phpcs:ignore
			$cards      = $wpdb->get_results( $prepare ); // phpcs:ignore
		}

		if ( ! empty( $cards ) ) {
			foreach ( $cards as $card ) {
				$cards_info[] = (array) $card;
			}
		}

		return $cards_info;
	}
}
