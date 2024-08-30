<?php
/**
 * Merchant e-Solutions Gateway
 *
 * @package    MG_Merchant_E_Solutions
 * @subpackage MG_Merchant_E_Solutions/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Merchant_E_Solutions\Includes\Classes;

use MG_Merchant_E_Solutions\Includes\Classes\Admin as Admin;
use MG_Merchant_E_Solutions\Includes\Classes\Data as Data;
use MG_Merchant_E_Solutions\Includes\Classes\Order as Order;
use MG_Merchant_E_Solutions\Includes\Classes\Card as Card;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Gateway' ) ) {

	/**
	 * Merchant e-Solutions Gateway
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Gateway extends \WC_Payment_Gateway {

		/**
		 * API Endpoint.
		 *
		 * @var string
		 */
		protected $api_endpoint;

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.1.0
		 *
		 * @return void
		 */
		public function __construct() {
			// Set parent properties.
			$this->id                 = 'mg_mes';
			$this->has_fields         = true;
			$this->method_title       = __( 'Merchant e-Solutions', 'woocommerce' );
			$this->method_description = __( 'Take payments right on your website using this direct payment gateway plugin from Merchant e-Solutions! This plugin supports credit card transactions. You must have a merchant account to use this plugin.', 'woocommerce' );
			$this->form_fields        = Admin\FormFields::get( $this->id );
			$this->supports           = array(
				'products',
				'default_credit_card_form',
				'tokenization',
				'pre-orders',
				'refunds',
				'voids',
			);

			// Set merchant variables.
			$this->enabled      = $this->get_option( 'enabled' );
			$this->title        = $this->get_option( 'title' );
			$this->description  = $this->get_option( 'description' );
			$this->mode         = $this->get_option( 'mode' );
			$this->profile_id   = $this->get_option( 'profile_id' );
			$this->profile_key  = $this->get_option( 'profile_key' );
			$this->tran_type    = $this->get_option( 'tran_type' );
			$this->pre_order    = $this->get_option( 'pre_order' );
			$this->pre_auth     = $this->get_option( 'pre_auth' );
			$this->test_mode    = $this->get_option( 'test_mode' );
			$this->api_endpoint = ( 'yes' === $this->test_mode ) ? 'https://cert.merchante-solutions.com/mes-api/tridentApi' : 'https://api.merchante-solutions.com/mes-api/tridentApi';

			// Init methods.
			$this->init_settings();
			$this->hooks();
		}

		/**
		 * Hooks.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function hooks() {
			remove_filter( 'woocommerce_order-refund_data_store', array( 'WooCommerce_Custom_Orders_Table', 'order_refund_data_store' ) );
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			if ( ! $this->pre_auth ) {
				add_action( 'wc_pre_orders_process_pre_order_completion_payment_' . $this->id, array( $this, 'process_pre_order_payments' ) );
			}
			if ( ! $this->pre_auth ) {
				add_action( 'woocommerce_order_status_on-hold_to_processing', array( $this, 'process_pre_auth_payments' ), 10, 1 );
				add_action( 'woocommerce_order_status_on-hold_to_completed', array( $this, 'process_pre_auth_payments' ), 10, 1 );
			}
			add_action( 'woocommerce_order_status_refunded', array( $this, 'process_refunding' ), 10, 1 );
		}

		/**
		 * Payment Feilds
		 *
		 * @author Jason Witt
		 * @since  1.1.0
		 *
		 * @return void
		 */
		public function payment_fields() {
			PaymentFields::render( $this->get_description(), $this->test_mode, $this->mode, $this->id, $this->supports );
		}

		/**
		 * Process the payment.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param integer $order_id The order ID.
		 *
		 * @return array
		 */
		public function process_payment( $order_id ) {
			$order = new Order\Order();
			return $order->run_transaction( $order_id, $this->get_data( $order_id ) );
		}

		/**
		 * Process Preorders
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param object $order The order object.
		 *
		 * @return mixed
		 */
		public function process_pre_order_payments( $order ) {
			$order_status = get_post_meta( $order->get_id(), '_mes_pre_order_status' );

			if ( 'holding' === $order_status ) {
				return do_action( 'woo_mes_' . $this->id . '_pre_auth_transaction', $order );
			}
		}

		/**
		 * Process Preorders
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param int $order_id The order ID.
		 *
		 * @return void
		 */
		public function process_pre_auth_payments( $order_id ) {
			$order        = wc_get_order( $order_id );
			$order_status = get_post_meta( $order_id, '_mes_pre_authorization', true );

			if ( 'holding' === $order_status ) {
				do_action( 'woo_mes_' . $this->id . '_pre_auth_transaction', $order );
			}
		}

		/**
		 * Status set to Refunding
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param int $order_id Order ID.
		 *
		 * @return void
		 */
		public function process_refunding( $order_id ) {
			$pre_auth = get_post_meta( $order_id, '_mes_pre_authorization', true );
			$order    = wc_get_order( $order_id );
			$amount   = $order->get_total();

			if ( isset( $pre_auth ) && 'holding' === $pre_auth ) {
				do_action( 'woo_mes_' . $this->id . '_void_transaction', $order_id, $amount );
			} else {
				do_action( 'woo_mes_' . $this->id . '_refund_transaction', $order_id, $amount );
			}
		}

		/**
		 * Get Data.
		 *
		 * Get all the data needed for processing the order.
		 *
		 * @author Jason Witt
		 * @since  1.1.0
		 *
		 * @param int $order_id The order ID.
		 *
		 * @return array
		 */
		public function get_data( $order_id ) {

			// The post data.
			$post          = GetPost::request();
			$post_data     = new Data\PostData();
			$get_post_data = $post_data->get( $post );

			// The order data.
			$order          = wc_get_order( $order_id );
			$order_data     = new Data\OrderData();
			$get_order_data = $order_data->get( $order, $get_post_data );

			return array(
				'post'  => $get_post_data,
				'order' => $get_order_data,
			);
		}

		/**
		 * Add payment method.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function add_payment_method() {
			do_action( 'woo_mes_' . $this->id . '_add_card' );
		}
	}
}
