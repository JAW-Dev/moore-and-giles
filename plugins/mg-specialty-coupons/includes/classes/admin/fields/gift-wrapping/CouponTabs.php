<?php
/**
 * Coupon Tabs.
 *
 * @package    MGSpecialtyCoupons
 * @subpackage MGSpecialtyCoupons/Inlcudes/Classes/Admin/Fields/Gift_Wrapping
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MGSpecialtyCoupons\Includes\Classes\Admin\Fields\Gift_Wrapping;

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
		 * @param array $args {
		 *     Array the plugin arguments.
		 *
		 *     @type string $version         The plugin version.
		 *     @type string $plugin_dir_url  The plugin directory URL.
		 *     @type string $plugin_dir_path The plugin Directory Path.
		 * }
		 *
		 * @return void
		 */
		public function __construct( $args = array() ) {
			$this->args = $args;
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
			$tabs['objectv_gift_wrapping_types'] = array(
				'label'  => __( 'Gift Wrapping', 'moore-and-giles' ),
				'target' => 'objectv_gift_wrapping_types',
				'class'  => 'objectv_gift_wrapping_types',
			);

			return $tabs;
		}
	}
}
