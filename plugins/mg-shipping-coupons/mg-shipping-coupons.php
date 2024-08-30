<?php
/**
 *
 * Plugin Name: MG Shipping Coupons
 * Description: Create Coupons for Shipping Methods
 * Version:     1.0.0
 * Author:      Objectiv
 * Author URI:  https://objectiv.co/
 * License:     GPLv2
 * Text Domain: moore-and-giles
 * Domain Path: /languages
 *
 * @package    MG_Shipping_Coupons
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    GNU General Public License v2 or later
 * @version    1.0.0
 */

namespace MG_Shipping_Coupons;

use MG_Shipping_Coupons\Includes\Classes as Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

/**
 * Autoloader
 */
include dirname( __FILE__ ) . '/vendor/autoload.php';

if ( ! defined( 'MGSC_FIELD_ID_CODE' ) ) {
	define( 'MGSC_FIELD_ID_CODE', 'objectiv_shipping_coupons_code' );
}

if ( ! defined( 'MGSC_FIELD_ID_ENABLE' ) ) {
	define( 'MGSC_FIELD_ID_ENABLE', 'objectiv_shipping_coupons_enable' );
}

if ( ! defined( 'MGSC_FIELD_ID_METHOD' ) ) {
	define( 'MGSC_FIELD_ID_METHOD', 'objectiv_shipping_coupons_method' );
}

if ( ! defined( 'MGSC_FIELD_ID_SERVICE' ) ) {
	define( 'MGSC_FIELD_ID_SERVICE', 'objectiv_shipping_coupons_service' );
}

if ( ! class_exists( __NAMESPACE__ . '\\MG_Shipping_Coupons' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class MG_Shipping_Coupons {

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
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			$this->plugin_args = array(
				'version'          => '1.0.0',
				'plugin_dir_url'   => trailingslashit( plugin_dir_url( __FILE__ ) ),
				'plugin_dir_path'  => trailingslashit( plugin_dir_path( __FILE__ ) ),
				'field_id_code'    => MGSC_FIELD_ID_CODE,
				'field_id_enable'  => MGSC_FIELD_ID_ENABLE,
				'field_id_method'  => MGSC_FIELD_ID_METHOD,
				'field_id_service' => MGSC_FIELD_ID_SERVICE,
			);
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

			// Bail if Woocommerce is not active.
			if ( ! class_exists( 'WooCommerce' ) ) {
				return;
			}

			// Load translated strings for plugin.
			load_plugin_textdomain( 'moore-and-giles', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			// Call Classes.
			add_action( 'woocommerce_init', array( $this, 'include_classes' ) );

			// Enqueue Styles.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );

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
			new Classes\Template_Tags( $this->plugin_args['plugin_dir_path'] . 'includes/functions' );
			new Classes\Coupon();
			new Classes\Shipping_Ajax();
			new Classes\Coupon_Tabs();
			new Classes\Coupon_Panels( $this->plugin_args );
			new Classes\Enable( $this->plugin_args );
			new Classes\Methods( $this->plugin_args );
			new Classes\Services( $this->plugin_args );
			new Classes\Code( $this->plugin_args );
		}

		/**
		 * Enqueue Styles.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function enqueue_styles() {
			wp_enqueue_style( 'product_addons_stylesheet', $this->plugin_args['plugin_dir_url'] . 'src/frontend.css', array(), $this->plugin_args['version'] );
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
			wp_enqueue_script( 'shipping_coupon_admin_scripts', $this->plugin_args['plugin_dir_url'] . 'src/backend.js', array( 'jquery' ), $this->plugin_args['version'], true );
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
			wp_enqueue_script( 'shipping_coupon_scripts', $this->plugin_args['plugin_dir_url'] . 'src/frontend.js', array( 'jquery' ), $this->plugin_args['version'], true );
			wp_localize_script(
				'shipping_coupon_scripts',
				'mgShippingCoupons',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
				)
			);
		}

		/**
		 * Activate the plugin.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function _activate() {
			flush_rewrite_rules();
		}

		/**
		 * Decativate the plugin.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function _deactivate() {}
	}
}

/**
 * Return an instance of the plugin class.
 *
 * @author Jason Witt
 * @since  1.0.0
 *
 * @return MG_Shipping_Coupons instance of plugin class.
 *
 * @SuppressWarnings(PHPMD)
 */
function mg_shipping_coupons() {
	return MG_Shipping_Coupons::get_instance();
}
add_action( 'plugins_loaded', array( mg_shipping_coupons(), 'init' ) );

/**
 * Activateion Hook
 */
register_activation_hook( __FILE__, array( mg_shipping_coupons(), '_activate' ) );

/**
 * Deactivation Hook
 */
register_deactivation_hook( __FILE__, array( mg_shipping_coupons(), '_deactivate' ) );
