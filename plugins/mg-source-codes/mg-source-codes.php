<?php
/**
 *
 * Plugin Name: MG Source Codes
 * Description: Catalog Source Codes
 * Version:     1.0.0
 * Author:      Objectiv
 * Author URI:  https://objectiv.co/
 * License:     GPLv2
 * Text Domain: moore-adn-giles
 * Domain Path: /languages
 *
 * @package    MG_Source_Codes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    GNU General Public License v2 or later
 * @version    1.0.0
 */

namespace MG_Source_Codes;

use MG_Source_Codes\Includes\Classes as Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

/**
 * Autoloader
 */
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . trailingslashit( 'vendor' ) . 'autoload.php';

if ( ! class_exists( __NAMESPACE__ . '\\MG_Source_Codes' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class MG_Source_Codes {

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
				'options'         => get_option( 'mg_source_codes' ),
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
			new Classes\Settings( $this->plugin_args );
			new Classes\Source_Codes( $this->plugin_args );
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
 * @return MG_Source_Codes instance of plugin class.
 *
 * @SuppressWarnings(PHPMD)
 */
function mg_source_codes() {
	return MG_Source_Codes::get_instance();
}
add_action( 'plugins_loaded', array( mg_source_codes(), 'init' ) );

/**
 * Activateion Hook
 */
register_activation_hook( __FILE__, array( mg_source_codes(), '_activate' ) );
