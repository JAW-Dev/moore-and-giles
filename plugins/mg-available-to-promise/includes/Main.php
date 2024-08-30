<?php

namespace MG_ATP;

use MG_ATP\Managers\SettingsManager;
use MG_ATP\TransitCalculators\FedEx;
use MG_ATP\TransitCalculators\UPS;
use Objectiv\BoosterSeat\Base\Singleton;

/**
 * Class Admin
 *
 * @link objectiv.co
 * @since 1.0.0
 * @package MG_ATP
 * @author Clifton Griffin <clif@objectiv.co>
 */

class Main extends Singleton {
	public $carriers = array();

	public $settings_manager;

	public function run() {
		$this->load();

		add_filter( 'mg_get_delivery_dates_order_details', array( $this, 'get_order_details' ) );
		add_filter( 'mg_atp_pickup_date', array( $this, 'adjust_pickup_date_cart_addons' ) );
	}

	function load() {
		$this->settings_manager  = new SettingsManager();
		$this->carriers['ups']   = new UPS();
		$this->carriers['fedex'] = new FedEx();
	}

	function get_order_details( $details ) {
		$details['from_state']    = 'VA';
		$details['from_city']     = 'Forest';
		$details['from_postcode'] = '24551';
		$details['from_country']  = 'US';

		if ( class_exists( '\\Shopp' ) ) {
			/** @var \ShoppOrder $order */
			$order = \ShoppOrder();

			$details['to_state']    = $order->Shipping->state;
			$details['to_city']     = $order->Shipping->city;
			$details['to_postcode'] = $order->Shipping->postcode;
			$details['to_country']  = $order->Shipping->country;

			// Calculate weight / items
			$package_count = 0;
			$total_weight  = 0;

			foreach ( $order->Cart as $id => $Item ) {
				$cubic_inches = ( ( convert_unit( $Item->height, 'in' ) ) * convert_unit( $Item->length, 'in' ) * convert_unit( $Item->width, 'in' ) );
				$weight       = $cubic_inches / 166;

				$weight = $Item->weight < $weight ? $weight : $Item->weight;

				$total_weight += $weight;
				$package_count++;
			}

			$details['total_weight']  = $total_weight;
			$details['package_count'] = $package_count;
			$details['cart_subtotal'] = $order->Cart->total( 'order' );
		}

		if ( class_exists( 'WooCommerce' ) ) {
			$cart     = \WC()->cart;
			$customer = \WC()->customer;

			$details['to_state']      = $customer->get_shipping_state();
			$details['to_city']       = $customer->get_shipping_city();
			$details['to_postcode']   = $customer->get_shipping_postcode();
			$details['to_country']    = $customer->get_shipping_country();
			$details['total_weight']  = $cart->get_cart_contents_weight();
			$details['package_count'] = $cart->get_cart_contents_count();
			$details['cart_subtotal'] = $cart->get_subtotal();
		}

		return $details;
	}

	/**
	 * @param \DateTime $date
	 *
	 * @return mixed
	 */
	function adjust_pickup_date_cart_addons( $date ) {
		if ( class_exists( '\\Shopp' ) ) {
			$order = ShoppOrder();

			$has_addon = false;

			foreach ( $order->Cart as $item ) {
				if ( ! empty( $item->data['Personalization Initials'] ) ) {
					$has_addon = true;
				} elseif ( ! empty( $item->data['For'] ) ) {
					$has_addon = true;
				}
			}

			if ( $has_addon ) {
				$date->modify( '+2 days' );
			}
		}

		if ( class_exists( 'WooCommerce' ) ) {
			$cart      = \WC()->cart->get_cart();
			$has_addon = false;

			foreach ( $cart as $cart_item ) {
				$addons = isset( $cart_item['product_addons']['addons'] ) ? $cart_item['product_addons']['addons']: array();
				if ( ! empty( $addons ) ) {
					if ( isset( $addons['addon-personalization'] ) && ! empty( $addons['addon-personalization'] ) ) {
						$has_addon = true;
					}
				}
			}

			if ( $has_addon ) {
				$date->modify( '+2 days' );
			}
		}

		return $date;
	}

	/**
	 * @return array
	 */
	public function get_carriers() {
		return $this->carriers;
	}

	/**
	 * @return SettingsManager
	 */
	public function get_settings_manager() {
		return $this->settings_manager;
	}
}
