<?php
/**
 *
 * Plugin Name: MG Product Addons - Gift Wrapping
 * Description: Add gift wrapping addon to the products and cart extending the core Product Addons core plugin.
 * Version:     1.0.0
 * Author:      Objectiv
 * Author URI:  https://objectiv.co/
 * License:     GPLv2
 * Text Domain: moore-and-giles
 * Domain Path: /languages
 *
 * @package    MG_Product_Addons_Gift_Wrapping
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    GNU General Public License v2 or later
 * @version    1.0.0
 */

namespace MG_Product_Addons_Gift_Wrapping;

use MG_Product_Addons_Gift_Wrapping\Includes\Classes as Classes;


if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

// ==============================================
// Autoloader
// ==============================================
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . trailingslashit( 'vendor' ) . 'autoload.php';

if ( ! defined( 'MGGWA_FIELD_ID_CODE' ) ) {
	define( 'MGGWA_FIELD_ID_CODE', 'objectiv_gift_wrapping_coupons_code' );
}

if ( ! defined( 'MGGWA_FIELD_ID_ENABLE' ) ) {
	define( 'MGGWA_FIELD_ID_ENABLE', 'objectiv_gift_wrapping_coupons_enable' );
}

if ( ! defined( 'MGGWA_FIELD_ID_BOX' ) ) {
	define( 'MGGWA_FIELD_ID_BOX', 'objectiv_gift_wrapping_coupons_box' );
}

if ( ! class_exists( __NAMESPACE__ . '\\MG_Product_Addons_Gift_Wrapping' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class MG_Product_Addons_Gift_Wrapping {

		/**
		 * Plugin Arguments.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var array
		 */
		protected $plugin_args;

		/**
		 * PLugin Version.
		 *
		 * @author Jason Witt
		 * @since  1.1.0
		 *
		 * @var string
		 */
		protected $plugin_version = '1.1.0';

		/**
		 * Addons Field.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var array
		 */
		protected $addons_field;

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
				'version'         => $this->plugin_version,
				'plugin_dir_url'  => trailingslashit( plugin_dir_url( __FILE__ ) ),
				'plugin_dir_path' => trailingslashit( plugin_dir_path( __FILE__ ) ),
				'field_id_code'   => MGGWA_FIELD_ID_CODE,
				'field_id_box'    => MGGWA_FIELD_ID_BOX,
				'field_id_enable' => MGGWA_FIELD_ID_ENABLE,
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

			// Load translated strings for plugin.
			load_plugin_textdomain( 'moore-and-giles', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			// Get Addons.
			$this->plugin_args['addons'] = ( function_exists( 'get_field' ) ) ? get_field( 'woo_mg_products_product_addons', 'option' ) : null;

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
		 * @since  1.1.0
		 *
		 * @return void
		 */
		public function include_classes() {
			new Classes\AddonFieldContainer();
			new Classes\HiddenFields();

			// Keep this in case they change their minds again.
			// new Classes\GiftBox(); Removed gift box order line item.
		}

		/**
		 * Enqueue Styles.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function enqueue_scripts() {
			if ( ! is_page( 'checkout' ) ) {
				wp_enqueue_script( 'product_addons_scripts_gift_wrapping', $this->plugin_args['plugin_dir_url'] . 'src/frontend.js', array( 'jquery', 'product_addons_scripts', 'objectiv-theme' ), $this->plugin_args['version'], true );
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
			if ( ! is_page( 'checkout' ) ) {
				wp_enqueue_script( 'product_addons_admin_scripts_gift_wrapping', $this->plugin_args['plugin_dir_url'] . 'src/backend.js', array( 'jquery' ), $this->plugin_args['version'], true );
			}
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
	}
}

/**
 * Return an instance of the plugin class.
 *
 * @author Jason Witt
 * @since  1.0.0
 *
 * @return MG_Product_Addons_Gift_Wrapping instance of plugin class.
 *
 * @SuppressWarnings(PHPMD)
 */
function mg_product_addons_gift_wrapping() {
	return MG_Product_Addons_Gift_Wrapping::get_instance();
}
add_action( 'plugins_loaded', array( mg_product_addons_gift_wrapping(), 'init' ) );

// ==============================================
// Activation
// ==============================================
register_activation_hook( __FILE__, array( mg_product_addons_gift_wrapping(), '_activate' ) );
