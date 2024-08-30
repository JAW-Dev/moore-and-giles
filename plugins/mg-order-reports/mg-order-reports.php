<?php
/**
 *
 * Plugin Name: MG Order Reports
 * Description: Custom Woocmmerce order reports
 * Version:     1.0.0
 * Author:      Objectiv
 * Author URI:  https://objectiv.co/
 * License:     GPL-2.0
 * Text Domain: mg_order_reports
 * Domain Path: /languages
 *
 * @package    MG_Order_Reports
 * @author     Objectiv
 * @copyright  Copyright (c) 2019, Objectiv
 * @license    GPL-2.0
 * @version    1.0.0
 */

namespace MGOrderReports;

use MGOrderReports\Includes\Classes as Classes;

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
if ( file_exists( trailingslashit( plugin_dir_path( __FILE__ ) ) . trailingslashit( 'includes' ) . 'constants.php' ) ) {
	require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . trailingslashit( 'includes' ) . 'constants.php';
}

if ( ! class_exists( __NAMESPACE__ . '\\Plugin_Boilerplate_CLass_Name' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class MGOrderReports {

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
			load_plugin_textdomain( 'mg_order_reports', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			// Include Classes.
			$this->include_classes();
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
			new Classes\EnqueueScripts();
			new Classes\EnqueueStyles();
			new Classes\Dashboard();
			new Classes\Report();
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
 * @return MGOrderReports instance of plugin class.
 *
 * @SuppressWarnings(PHPMD)
 */
function mg_order_reports() {
	return MGOrderReports::get_instance();
}
add_action( 'plugins_loaded', array( mg_order_reports(), 'init' ) );

/**
 * Activateion Hook
 */
register_activation_hook( __FILE__, array( mg_order_reports(), '_activate' ) );

/**
 * Deactivation Hook
 */
register_deactivation_hook( __FILE__, array( mg_order_reports(), '_deactivate' ) );
