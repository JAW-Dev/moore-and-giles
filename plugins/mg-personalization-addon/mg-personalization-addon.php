<?php
/**
 *
 * Plugin Name: MG Product Addons - Personalization
 * Description: Add Personalization field to products extending the core Product Addons core plugin.
 * Version:     1.0.0
 * Author:      Objectiv
 * Author URI:  https://objectiv.co/
 * License:     GPLv2
 * Text Domain: moore-and-giles
 * Domain Path: /languages
 *
 * @package    MG_Personalization_Addon
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    GNU General Public License v2 or later
 * @version    1.0.0
 */

namespace MG_Personalization_Addon;

use MG_Personalization_Addon\Includes\Classes as Classes;


if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

// ==============================================
// Autoloader
// ==============================================
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . trailingslashit( 'vendor' ) . 'autoload.php';

if ( ! defined( 'MGPA_FIELD_ID_CODE' ) ) {
	define( 'MGPA_FIELD_ID_CODE', 'objectiv_personalization_coupons_code' );
}

if ( ! defined( 'MGPA_FIELD_ID_ENABLE' ) ) {
	define( 'MGPA_FIELD_ID_ENABLE', 'objectiv_personalization_coupons_enable' );
}

if ( ! class_exists( 'MG_Personalization_Addon' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class MG_Personalization_Addon {

		/**
		 * Plugin Arguments.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var array
		 */
		public $plugin_args;

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
		 * @param boolean $kill If to kill the functionality.
		 *
		 * @return void
		 */
		public function __construct( $kill = false ) {
			$this->plugin_args = array(
				'version'                  => $this->plugin_version,
				'plugin_dir_url'           => trailingslashit( plugin_dir_url( __FILE__ ) ),
				'plugin_dir_path'          => trailingslashit( plugin_dir_path( __FILE__ ) ),
				'personalization_slug'     => 'addon-personalization',
				'personalization_title'    => __( 'Personalization', 'moore-and-giles' ),
				'personalization_price'    => get_option( 'options_woo_personalization_price' ) ? get_option( 'options_personalization_price' ) : 12,
				'personalization_label'    => get_option( 'options_woo_personalization_title' ) ? get_option( 'options_woo_personalization_title' ) : '',
				'personalization_sublabel' => get_option( 'options_woo_personalization_label' ) ? get_option( 'options_woo_personalization_label' ) : '',
				'personalization_tooltip'  => get_option( 'options_woo_help_personalization_tooltip' ) ? get_option( 'options_woo_help_personalization_tooltip' ) : '',
				'field_id_code'            => MGPA_FIELD_ID_CODE,
				'field_id_enable'          => MGPA_FIELD_ID_ENABLE,
			);

			if ( $kill ) {
				return;
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
		public function init() {

			// Load translated strings for plugin.
			load_plugin_textdomain( 'moore-and-giles', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

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
			new Classes\DisplayFields( $this->plugin_args );
			new Classes\AddCartItemData( $this->plugin_args );
			new Classes\GetAddon( $this->plugin_args );
			new Classes\GetItemData( $this->plugin_args );
			new Classes\OrderLineItem( $this->plugin_args );
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
				wp_enqueue_script( 'product_addons_scripts_personalization', $this->plugin_args['plugin_dir_url'] . 'src/index.js', array( 'jquery', 'product_addons_scripts', 'objectiv-theme' ), $this->plugin_args['version'], true );
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
		 * Deactivate the plugin.
		 * Uninstall routines should be in uninstall.php.
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
 * @return MG_Personalization_Addon instance of plugin class.
 *
 * @SuppressWarnings(PHPMD)
 */
function mg_personalization_addon() {
	return MG_Personalization_Addon::get_instance();
}
add_action( 'plugins_loaded', array( mg_personalization_addon(), 'init' ) );

// ==============================================
// Activation
// ==============================================
register_activation_hook( __FILE__, array( mg_personalization_addon(), '_activate' ) );

// ==============================================
// Deactivation
// ==============================================
register_deactivation_hook( __FILE__, array( mg_personalization_addon(), '_deactivate' ) );
