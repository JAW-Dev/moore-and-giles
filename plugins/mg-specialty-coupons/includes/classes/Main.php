<?php
/**
 * Main.
 *
 * @package    MG_Specialty_Coupons
 * @subpackage MG_Specialty_Coupons/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    GPL-2.0
 * @since      1.0.0
 */

namespace MGSpecialtyCoupons\Includes\Classes;

use MGSpecialtyCoupons\Includes\Classes\Admin\Fields as Fields;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Main' ) ) {

	/**
	 * Main.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Main {

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
		 * Hooks
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function hooks() {
			add_action( 'woocommerce_coupon_loaded', array( $this, 'set_coupon_meta_data' ) );
			add_action( 'current_screen', array( $this, 'init_product_fields' ) );
			add_action( 'init', array( $this, 'init_coupons' ) );
		}

		/**
		 * Init Admin
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function init_product_fields() {
			$screen = get_current_screen();

			// Bail if not on shop coupon page.
			if ( 'shop_coupon' !== $screen->id ) {
				return;
			}

			// Black Friday.
			if ( class_exists( 'MGSpecialtyCoupons\Includes\Classes\Admin\Fields\SpecialtyTypes' ) ) {
				new Fields\SpecialtyTypes();
			}

			// Gift Wrapping.
			if ( class_exists( 'MGSpecialtyCoupons\Includes\Classes\Admin\Fields\Gift_Wrapping\CouponPanels' ) ) {
				new Fields\Gift_Wrapping\CouponPanels();
			}

			if ( class_exists( 'MGSpecialtyCoupons\Includes\Classes\Admin\Fields\Gift_Wrapping\CouponTabs' ) ) {
				new Fields\Gift_Wrapping\CouponTabs();
			}

			if ( class_exists( 'MGSpecialtyCoupons\Includes\Classes\Admin\Fields\Gift_Wrapping\Code' ) ) {
				new Fields\Gift_Wrapping\Code();
			}

			if ( class_exists( 'MGSpecialtyCoupons\Includes\Classes\Admin\Fields\Gift_Wrapping\Boxes' ) ) {
				new Fields\Gift_Wrapping\Boxes();
			}

			// Shipping.
			if ( class_exists( 'MGSpecialtyCoupons\Includes\Classes\Admin\Fields\Shipping\CouponPanels' ) ) {
				new Fields\Shipping\CouponPanels();
			}

			if ( class_exists( 'MGSpecialtyCoupons\Includes\Classes\Admin\Fields\Shipping\CouponTabs' ) ) {
				new Fields\Shipping\CouponTabs();
			}

			if ( class_exists( 'MGSpecialtyCoupons\Includes\Classes\Admin\Fields\Shipping\Code' ) ) {
				new Fields\Shipping\Code();
			}

			if ( class_exists( 'MGSpecialtyCoupons\Includes\Classes\Admin\Fields\Shipping\Methods' ) ) {
				new Fields\Shipping\Methods();
			}

			if ( class_exists( 'MGSpecialtyCoupons\Includes\Classes\Admin\Fields\Shipping\Services' ) ) {
				new Fields\Shipping\Services();
			}
		}

		/**
		 * Init
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function init_coupons() {
			// Black Friday.
			if ( class_exists( 'MGSpecialtyCoupons\Includes\Classes\BlackFirday\Coupon' ) ) {
				new BlackFirday\Coupon();
			}

			// Personalization.
			if ( class_exists( 'MGSpecialtyCoupons\Includes\Classes\Personalization\Coupon' ) ) {
				new Personalization\Coupon();
			}

			// Gift Wrapping.
			if ( class_exists( 'MGSpecialtyCoupons\Includes\Classes\Gift_Wrapping\Coupon' ) ) {
				new Gift_Wrapping\Coupon();
			}

			// Shipping.
			if ( class_exists( 'MGSpecialtyCoupons\Includes\Classes\Shipping\Coupon' ) ) {
				// new Shipping\Coupon();
			}

			if ( class_exists( 'MGSpecialtyCoupons\Includes\Classes\Shipping\Ajax' ) ) {
				// new Shipping\Ajax();
			}
		}

		/**
		 * Set Coupon Meta Data
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param WC_Coupon $coupon The coupon object.
		 *
		 * @return void
		 */
		public function set_coupon_meta_data( $coupon ) {
			$coupon_type         = get_post_meta( $coupon->get_id(), 'coupon_type', true );
			$coupon->coupon_type = $coupon_type;
		}
	}
}
