<?php
/**
 *
 * Plugin Name: MG Product Addons
 * Description: Add core plugin for adding custom addons to your products and cart.
 * Version:     1.0.0
 * Author:      Objectiv
 * Author URI:  https://objectiv.co/
 * License:     GPLv2
 * Text Domain: moore-and-giles
 * Domain Path: /languages
 *
 * @package    MG_Product_Addons
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    GNU General Public License v2 or later
 * @version    1.0.0
 */

namespace MG_Product_Addons;

use MG_Product_Addons\Includes\Classes as Classes;


if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

// ==============================================
// Autoloader
// ==============================================
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . trailingslashit( 'vendor' ) . 'autoload.php';

if ( ! class_exists( 'MG_Product_Addons' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class MG_Product_Addons {

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
			new Classes\CreateOptionsPage();
			new Classes\CreateFieldGroup();
			new Classes\ProductAddons( $this->plugin_args );
			new Classes\Cart();
			new Classes\AddCartItemPrice();
			new Classes\GetItemData();
			new Classes\OrderLineItem();
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
				wp_enqueue_script( 'product_addons_scripts', $this->plugin_args['plugin_dir_url'] . 'src/index.js', array( 'jquery', 'objectiv-theme' ), $this->plugin_args['version'], true );
				wp_localize_script(
					'product_addons_scripts',
					'productAddonsData',
					array(
						'adminAjax' => admin_url( 'admin-ajax.php' ),
					)
				);
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
 * @return MG_Product_Addons instance of plugin class.
 *
 * @SuppressWarnings(PHPMD)
 */
function mg_product_addons() {
	return MG_Product_Addons::get_instance();
}
add_action( 'plugins_loaded', array( mg_product_addons(), 'init' ) );

// ==============================================
// Activation
// ==============================================
register_activation_hook( __FILE__, array( mg_product_addons(), '_activate' ) );

// ==============================================
// Deactivation
// ==============================================
register_deactivation_hook( __FILE__, array( mg_product_addons(), '_deactivate' ) );
