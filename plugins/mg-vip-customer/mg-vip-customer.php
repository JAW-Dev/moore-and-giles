<?php
/**
 *
 * Plugin Name: MG VIP Customer
 * Description: VIP Customer Features
 * Version:     1.0.0
 * Author:      Objectiv
 * Author URI:  https://objectiv.co/
 * License:     GPLv2
 * Text Domain: moore-adngiles
 * Domain Path: /languages
 *
 * @package    MG_VIP_Customer
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    GNU General Public License v2 or later
 * @version    1.0.0
 */

namespace MG_VIP_Customer;

use MG_VIP_Customer\Includes\Classes as Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

/**
 * Autoloader
 */
include dirname( __FILE__ ) . '/vendor/autoload.php';

if ( ! class_exists( __NAMESPACE__ . '\\MG_VIP_Customer' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class MG_VIP_Customer {

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
				'version'                   => '1.0.0',
				'plugin_dir_url'            => trailingslashit( plugin_dir_url( __FILE__ ) ),
				'plugin_dir_path'           => trailingslashit( plugin_dir_path( __FILE__ ) ),
				'field_id_code'             => 'objectiv_vip_coupons_code',
				'vip_enabled'          => function_exists( 'get_field' ) ? get_field( 'mg_vip_enable', 'option' ) : '',
				'vip_included_coupons' => function_exists( 'get_field' ) ? get_field( 'mg_vip_coupons', 'option' ) : '',
				'vip_members'          => function_exists( 'get_field' ) ? get_field( 'mg_vip_customer_emails', 'option' ) : '',
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
			load_plugin_textdomain( 'moore-adngiles', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

			// Include Classes.
			$this->include_classes();

			// Enqueue Styles.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
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
			new Classes\Coupon( $this->plugin_args );
			new Classes\ACF();
			new Classes\Table( $this->plugin_args );
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
 * @return MG_VIP_Customer instance of plugin class.
 *
 * @SuppressWarnings(PHPMD)
 */
function mg_vip_customer() {
	return MG_VIP_Customer::get_instance();
}
add_action( 'plugins_loaded', array( mg_vip_customer(), 'init' ) );

/**
 * Activateion Hook
 */
register_activation_hook( __FILE__, array( mg_vip_customer(), '_activate' ) );

/**
 * Deactivation Hook
 */
register_deactivation_hook( __FILE__, array( mg_vip_customer(), '_deactivate' ) );
