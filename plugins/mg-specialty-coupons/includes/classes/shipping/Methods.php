<?php
/**
 * Methods
 *
 * @package    MGSpecialtyCoupons
 * @subpackage MGSpecialtyCoupons/Includes/Classes/Shipping
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MGSpecialtyCoupons\Includes\Classes\Shipping;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Methods' ) ) {

	/**
	 * Methods
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Methods {

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
		 * Get Shipping Methods.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get_methods() {
			$shipping_methods = WC()->shipping->load_shipping_methods();
			$methods          = array();

			// Bail if empty.
			if ( empty( $shipping_methods ) ) {
				return;
			}

			foreach ( $shipping_methods as $shipping_method ) {
				$id    = property_exists( $shipping_method, 'id' ) ? $shipping_method->id : '';
				$title = property_exists( $shipping_method, 'method_title' ) ? $shipping_method->method_title : '';

				// Continue if there is no id set.
				if ( ! $id || ! $title ) {
					continue;
				}

				$methods[ $id ] = $title;
			}

			return $methods;
		}

		/**
		 * Get Shipping Method Settings.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get_settings() {
			$methods = $this->get_methods();

			// Bail if empty.
			if ( empty( $methods ) ) {
				return;
			}

			$loop_total      = apply_filters( 'objectiv_shipping_methods_loop_total', 20 );
			$method_settings = array();

			for ( $i = 0; $i < $loop_total; $i++ ) {

				foreach ( $methods as $type_id => $type_title ) {
					$settings = get_option( 'woocommerce_' . $type_id . '_' . $i . '_settings' );

					// Bail if empty.
					if ( ! is_array( $settings ) || empty( $settings ) ) {
						continue;
					}

					$title = isset( $settings['title'] ) ? $settings['title'] : '';

					// Continue if the settings title doesn't match.
					if ( ! $title || ! $type_title ) {
						continue;
					}

					$settings['instance'] = $i;

					$method_settings[ $type_id ] = $settings;
				}
			}

			return $method_settings;
		}

		/**
		 * Get Shipping Method Services.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get_services() {
			$methods_services = $this->get_settings();
			$enabled_services = array();

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
