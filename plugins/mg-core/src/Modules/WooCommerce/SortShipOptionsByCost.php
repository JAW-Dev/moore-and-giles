<?php
/**
* Module Name:       WooCommerce - Sort Ship Options By Cost
* Module URI:        https://www.xadapter.com/woocommerce-sort-shipping-options-methods-services-by-shipment-cost/
* Description:       Sort WooCommerce shipping options from lowest cost to highest cost.
* Version:           1.0.0
* Author:            Objectiv
* Author URI:        https://objectiv.co
*/

namespace MG_Core\modules\WooCommerce;

use MG_Core\Modules\Base;

class SortShipOptionsByCost extends Base {
	/***
	 * Register module
	 */
	public function __construct() {
		$this->set_id( 'woocommerce_sort_ship_options_by_cost' );
		$this->set_name( 'WooCommerce - Sort Ship Options By Cost' );
		$this->set_description( 'Sort WooCommerce shipping options from lowest cost to highest cost.' );
		$this->set_author( 'Clifton Griffin' );
		$this->set_author_uri( 'https://objectiv.co' );
		$this->set_version( '1.0.0' );
	}

	/**
	 * Kicks off the module.
	 */
	public function run() {
		add_filter( 'woocommerce_package_rates' , array($this, 'sort_shipping_services_by_cost'), 10, 2 );
	}

	/**
	 * @param $rates
	 * @param $package
	 *
	 * @return mixed
	 */
	function sort_shipping_services_by_cost( $rates, $package ) {
		if ( ! $rates )  return $rates;

		$rate_cost = array();
		foreach( $rates as $rate ) {
			$rate_cost[] = $rate->cost;
		}

		// using rate_cost, sort rates.
		array_multisort( $rate_cost, $rates );

		return $rates;
	}
}