<?php
/**
 * Coupon Tabs.
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

if ( ! class_exists( __NAMESPACE__ . '\\Coupon_Tabs' ) ) {

	/**
	 * Coupon Tabs
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Coupon_Tabs {

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
			add_filter( 'woocommerce_coupon_data_tabs', array( $this, 'shipping_tab' ), 20, 1 );
		}

		/**
		 * Shipping Tab.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $tabs The coupon settings tabs.
		 *
		 * @return array
		 */
		public function shipping_tab( $tabs ) {
			$tabs['objectv_shipping_types'] = array(
				'label'  => __( 'Shipping', 'moore-and-giles' ),
				'target' => 'objectv_shipping_types',
				'class'  => 'objectv_shipping_types',
			);

			return $tabs;
		}
	}
}
