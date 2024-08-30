<?php
/**
 * Shipping Methods Settings.
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

if ( ! class_exists( __NAMESPACE__ . '\\Shipping_Methods_Settings' ) ) {

	/**
	 * Shipping Methods Settings
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Shipping_Methods_Settings {

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
			$shipping_methods = new Shipping_Methods();
			$methods          = $shipping_methods->get();

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
	}
}
