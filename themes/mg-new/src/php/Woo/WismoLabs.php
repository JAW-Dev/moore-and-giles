<?php
namespace Objectiv\Site\Woo;

class WismoLabs {

	public function get_tracking_url( $order = null, $tracking_item = null, $tracking_actions ) {

		if ( empty( $order ) || empty( $tracking_item ) ) {
			$formatted_item = $tracking_actions->get_formatted_tracking_item( $order->id, $tracking_item );
			$formatted_url  = $formatted_item['formatted_tracking_link'];

			if ( ! empty( $formatted_url ) ) {
				return $formatted_url;
			} else {
				return $null;
			}
		}

		$base_track_url      = 'https://mooreandgiles.wismolabs.com/mooreandgiles/tracking';
		$order_num           = $order->get_order_number();
		$tracking_number     = $tracking_item['tracking_number'];
		$tracking_carrier    = strtolower( $tracking_item['tracking_provider'] );
		$order_date          = $order->get_date_created()->date( 'Y/m/d' );
		$shipping_date       = date( 'Y/m/d', $tracking_item['date_shipped'] );
		$shipping_country    = $order->get_shipping_country();
		$shipping_first_name = $order->get_shipping_first_name();
		$destination_zip     = $order->get_shipping_postcode();
		$origin_zip          = 24551;

		if ( $tracking_carrier === 'ups' ) {
			$origin_zip = 29730;
		}

		if ( empty( $tracking_carrier ) ) {
			$tracking_carrier = 'ABF';
		}

		$full_tracking_url = add_query_arg(

			array(
				'TRK'      => $tracking_number,
				'CAR'      => $tracking_carrier,
				'ON'       => $order_num,
				'OD'       => $order_date,
				'name'     => $shipping_first_name,
				'SD'       => $shipping_date,
				'oZIP'     => $origin_zip,
				'dZIP'     => $destination_zip,
				'dCountry' => $shipping_country,

			), $base_track_url
		);

		$full_tracking_url = esc_url( $full_tracking_url );

		return $full_tracking_url;

	}
}
