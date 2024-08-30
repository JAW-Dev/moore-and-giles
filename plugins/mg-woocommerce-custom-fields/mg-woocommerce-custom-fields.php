<?php
/**
 *
 * Plugin Name: MG WooCommerce Custom Fields
 * Description: Custom fields for WooCommerce
 * Version:     1.0.0
 * Author:      Objectiv
 * Author URI:  https://objectiv.co/
 * License:     GPLv2
 * Text Domain: moore-and-giles
 * Domain Path: /languages
 *
 * @package    MG_WooCommerce_Custom_Fields
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    GNU General Public License v2 or later
 * @version    1.0.0
 */

namespace MG_WooCommerce_Custom_Fields;

use MG_WooCommerce_Custom_Fields\Includes\Classes as Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

/**
 * Autoloader
 */
include dirname( __FILE__ ) . '/vendor/autoload.php';

if ( ! class_exists( __NAMESPACE__ . '\\MG_WooCommerce_Custom_Fields' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class MG_WooCommerce_Custom_Fields {

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
				'version'         => '1.0.0',
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

			// Include Classes.
			$this->include_classes();

			// Enqueue Styles.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );

			// Enqueue Scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
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
			new Classes\Serialized_Assembly_Items_Sku();
			new Classes\Processing();
			new Classes\Personalization_Toggle();
			new Classes\Personalization_Tooltip();
			new Classes\Custom_Fields_Meta();
			new Classes\Shipping();
			new Classes\TipsTooltips();
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
			wp_enqueue_style( 'product_addons_stylesheet', $this->plugin_args['plugin_dir_url'] . 'src/index.css', array(), $this->plugin_args['version'] );
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
			global $current_screen;

			if ( $current_screen->post_type == 'product' ) {
				wp_enqueue_script( 'product_addons_scripts', $this->plugin_args['plugin_dir_url'] . 'src/index.js', array( 'jquery' ), $this->plugin_args['version'], true );
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
 * @return MG_WooCommerce_Custom_Fields instance of plugin class.
 *
 * @SuppressWarnings(PHPMD)
 */
function mg_woocommerce_custom_fields() {
	return MG_WooCommerce_Custom_Fields::get_instance();
}
add_action( 'plugins_loaded', array( mg_woocommerce_custom_fields(), 'init' ) );

/**
 * Activateion Hook
 */
register_activation_hook( __FILE__, array( mg_woocommerce_custom_fields(), '_activate' ) );

/**
 * Deactivation Hook
 */
register_deactivation_hook( __FILE__, array( mg_woocommerce_custom_fields(), '_deactivate' ) );
