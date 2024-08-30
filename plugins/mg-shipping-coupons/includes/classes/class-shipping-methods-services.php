<?php
/**
 * Shipping Methods Services.
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

if ( ! class_exists( __NAMESPACE__ . '\\Shipping_Methods_Services' ) ) {

	/**
	 * Shipping Methods Services
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Shipping_Methods_Services {

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {}

		/**
		 * Get Shipping Method Settings.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get() {
			$shipping_methods_settings = new Shipping_Methods_Settings();
			$methods_services          = $shipping_methods_settings->get();
			$enabled_services          = array();

			// Bail if empty.
			if ( empty( $methods_services ) ) {
				return;
			}

			foreach ( $methods_services as $service_id => $service_value ) {
				$services = isset( $service_value['services'] ) ? $service_value['services'] : '';

				if ( $services ) {
					foreach ( $services as $key => $value ) {
						$enabled = isset( $value['enabled'] ) ? $value['enabled'] : '';
						if ( $enabled ) {
							$enabled_services[ $service_id ][ $key ] = $value;
						}
					}
				} else {
					$service_value['enabled']                       = 1;
					$enabled_services[ $service_id ][ $service_id ] = $service_value;
				}
			}

			return $enabled_services;
		}
	}
}
