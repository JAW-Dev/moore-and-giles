<?php
/**
 * Shipping Methods.
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

if ( ! class_exists( __NAMESPACE__ . '\\Shipping_Methods' ) ) {

	/**
	 * Shipping Methods
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Shipping_Methods {

		/**
		 * Shipping Methods.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var array
		 */
		protected $shipping_methods;

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
		public function get() {
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

			$this->shipping_methods = $methods;

			return $methods;
		}
	}
}
