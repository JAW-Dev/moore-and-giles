<?php
/**
 * Rates.
 *
 * @package    MG_Shipping_Coupons
 * @subpackage MG_Shipping_Coupons/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Shipping_Coupons\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Rates' ) ) {

	/**
	 * Rates
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Rates {

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
			add_action( 'woocommerce_checkout_init', array( $this, 'get' ) );
		}

		/**
		 * Get.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get() {
			$packages = \WC()->shipping->get_shipping_methods();
			$rates    = array();

			// Bail if packages is an empty array.
			if ( empty( $packages ) ) {
				return array();
			}

			foreach ( $packages as $package ) {
				$package_rates = $package->rates;

				if ( is_array( $package_rates ) && ! empty( $package_rates ) ) {
					foreach ( $package_rates as $package_rate ) {
						$rate_array = array(
							'id'          => $package_rate->get_id(),
							'method_id'   => $package_rate->get_method_id(),
							'instance_id' => $package_rate->get_instance_id(),
							'label'       => $package_rate->get_label(),
							'cost'        => $package_rate->get_cost(),
							'taxes'       => $package_rate->get_taxes(),
						);
						array_push( $rates, $rate_array );
					}
				}
			}
			return $rates;
		}
	}
}
