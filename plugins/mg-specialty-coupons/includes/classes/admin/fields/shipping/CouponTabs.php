<?php
/**
 * Coupon Tabs.
 *
 * @package    MGSpecialtyCoupons
 * @subpackage MGSpecialtyCoupons/Inlcudes/Classes/Admin/Fields/Shipping
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MGSpecialtyCoupons\Includes\Classes\Admin\Fields\Shipping;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\CouponTabs' ) ) {

	/**
	 * Coupon Tabs
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class CouponTabs {

		/**
		 * Args.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var array
		 */
		protected $args;

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
			add_filter( 'woocommerce_coupon_data_tabs', array( $this, 'gift_wrapping_tab' ), 20, 1 );
		}

		/**
		 * Gift Wrapping Tab.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $tabs The coupon settings tabs.
		 *
		 * @return array
		 */
		public function gift_wrapping_tab( $tabs ) {
			$tabs['objectv_shipping_types'] = array(
				'label'  => __( 'Shipping', 'moore-and-giles' ),
				'target' => 'objectv_shipping_types',
				'class'  => 'objectv_shipping_types',
			);

			return $tabs;
		}
	}
}
