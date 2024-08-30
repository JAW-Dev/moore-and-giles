<?php
/**
 * Plugin Name: MG Merchant e-Solutions
 * Description: Merchant e-Solutions Credit Card Payment Gateway.
 * Version:     1.0.0
 * Author:      Objectiv
 * Author URI:  https://objectiv.co/
 * License:     GPLv2
 * Text Domain: moore-and-giles
 * Domain Path: /languages
 *
 * @package    MG_Merchant_E_Solutions
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    GNU General Public License v2 or later
 * @version    1.0.0
 */

namespace MG_Merchant_E_Solutions;

use MG_Merchant_E_Solutions\Includes\Classes as Classes;
use MG_Merchant_E_Solutions\Includes\Classes\Admin as Admin;
use MG_Merchant_E_Solutions\Includes\Classes\Data as Data;
use MG_Merchant_E_Solutions\Includes\Classes\Card as Card;
use MG_Merchant_E_Solutions\Includes\Classes\Order as Order;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

// ==============================================
// Autoloader
// ==============================================
require_once trailingslashit( plugin_dir_path( __FILE__ ) ) . trailingslashit( 'vendor' ) . 'autoload.php';

if ( ! class_exists( __NAMESPACE__ . '\\MG_Merchant_E_Solutions' ) ) {

	/**
	 * Name
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class MG_Merchant_E_Solutions {

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
		 * Plugin Version.
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

			// Include Classes.
			$this->include_classes();

			// Add the gateway to Woocommerce.
			add_filter( 'woocommerce_payment_gateways', array( $this, 'add_the_gateway' ) );

			// Enqueue Scripts.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Add Gateway.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $methods The Gateway methods array.
		 *
		 * @return array
		 */
		public function add_the_gateway( $methods ) {
			$methods[] = 'MG_Merchant_E_Solutions\Includes\Classes\Gateway';
			return $methods;
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
			// Include the MES SDK.
			require_once $this->plugin_args['plugin_dir_path'] . 'lib/mes-sdk.php';

			// Admin Classes.
			new Admin\FormFields();
			new Admin\Notices();

			// Data Classes.
			new Data\PostData();
			new Data\OrderData();

			// General Classes.
			new Classes\GetPost();
			new Classes\PaymentFields();
			new Classes\UserToken();
			new Classes\CustomFields();

			// Card Classes.
			new Card\AddCard();
			new Card\SaveCard();

			// Order Classes.
			new Order\Order();
			new Order\Process();
			new Order\ProcessCC();
			new Order\ProcessCCSave();
			new Order\ProcessCCToken();
			new Order\ProcessPreAuth();
			new Order\ProcessVoid();
			new Order\ProcessRefund();
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
			wp_enqueue_script( 'mg_mes_scripts', $this->plugin_args['plugin_dir_url'] . 'src/frontend/index.js', array( 'jquery' ), $this->plugin_args['version'], true );
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
 */
function mg_merchant_e_solutions() {
	return MG_Merchant_E_Solutions::get_instance();
}
add_action( 'plugins_loaded', array( mg_merchant_e_solutions(), 'init' ) );

// ==============================================
// Activation
// ==============================================
register_activation_hook( __FILE__, array( mg_merchant_e_solutions(), '_activate' ) );
