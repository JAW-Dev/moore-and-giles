<?php
/**
 *
 * Plugin Name: MG Specialty Coupons
 * Description: Create coupons for special events
 * Version:     1.0.0
 * Author:      Objectiv
 * Author URI:  https://objectiv.co/
 * License:     GPL-2.0
 * Text Domain: mg-specialty-coupons
 * Domain Path: /languages
 *
 * @package    MG_Specialty_Coupons
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    GPL-2.0
 * @version    1.0.0
 */

namespace MGSpecialtyCoupons;

use MGSpecialtyCoupons\Includes\Classes as Classes;
use MGSpecialtyCoupons\Includes\Classes\Admin\Fields as Fields;
use MGSpecialtyCoupons\Includes\Classes\Shipping as Shipping;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

/**
 * Autoloader
 */
if ( file_exists( trailingslashit( plugin_dir_path( __FILE__ ) ) . trailingslashit( 'vendor' ) . 'autoload.php' ) ) {
	require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . trailingslashit( 'vendor' ) . 'autoload.php';
}

/**
 * Constants
 */
if ( ! defined( 'MG_SPECIALTY_COUPONS_PLUGIN_VERSION' ) ) {
	define( 'MG_SPECIALTY_COUPONS_PLUGIN_VERSION', '1.0.0.' );
}

if ( ! defined( 'MG_SPECIALTY_COUPONS_PLUGIN_DIR_URL' ) ) {
	define( 'MG_SPECIALTY_COUPONS_PLUGIN_DIR_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
}

if ( ! defined( 'MG_SPECIALTY_COUPONS_PLUGIN_DIR_PATH' ) ) {
	define( 'MG_SPECIALTY_COUPONS_PLUGIN_DIR_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

if ( ! defined( 'MG_SPECIALTY_COUPONS_PLUGIN_PRFIX' ) ) {
	define( 'MG_SPECIALTY_COUPONS_PLUGIN_PRFIX', 'mg-specialty-coupons' );
}

if ( ! defined( 'MG_SPECIALTY_COUPONS_ERROR_MESSAGE' ) ) {
	define( 'MG_SPECIALTY_COUPONS_ERROR_MESSAGE',  __( 'Sorry, this coupon is not applicable to your cart contents.', 'mg-specialty-coupons' ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\MGSpecialtyCoupons' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class MGSpecialtyCoupons {

		/**
		 * Singleton instance of plugin.
		 *
		 * @var   static
		 * @since 1.0.0
		 */
		protected static $single_instance = null;

		/**
		 * Creates or returns an instance of this class.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return static
		 */
		public static function get_instance() {
			if ( null === self::$single_instance ) {
				self::$single_instance = new self();
			}

			return self::$single_instance;
		}

		/**
		 * Init
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function init() {

			// Load translated strings for plugin.
			load_plugin_textdomain( 'mg-specialty-coupons', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			// Include Classes.
			$this->include_classes();

			// Enqueue Admin Scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

			// Enqueue Scripts.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Include Classes.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function include_classes() {
			if ( class_exists( 'MGSpecialtyCoupons\Includes\Classes\Main' ) ) {
				new Classes\Main();
				new Shipping\Ajax();
			}
		}

		/**
		 * Enqueue Admin Scripts.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function enqueue_admin_scripts() {
			$file   = 'dist/scripts/admin.js';
			$screen = get_current_screen();

			if ( 'shop_coupon' === $screen->id ) {
				wp_enqueue_script( 'mg-specialty-coupons', MG_SPECIALTY_COUPONS_PLUGIN_DIR_URL . $file, array( 'jquery' ), MG_SPECIALTY_COUPONS_PLUGIN_VERSION, true );
			}
		}

		/**
		 * Enqueue Scripts.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function enqueue_scripts() {
			$file = 'dist/scripts/index.js';

			wp_enqueue_script( 'mg-specialty-coupons', MG_SPECIALTY_COUPONS_PLUGIN_DIR_URL . $file, array( 'jquery' ), MG_SPECIALTY_COUPONS_PLUGIN_VERSION, true );

			wp_localize_script(
				'mg-specialty-coupons',
				'mgShippingCoupons',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				)
			);
		}
	}
}

/**
 * Return an instance of the plugin class.
 *
 * @author Jason Witt
 * @since  1.0.0
 *
 * @return MGSpecialtyCoupons instance of plugin class.
 *
 * @SuppressWarnings(PHPMD)
 */
function mg_specialty_coupons() {
	return MGSpecialtyCoupons::get_instance();
}
add_action( 'plugins_loaded', array( mg_specialty_coupons(), 'init' ) );
