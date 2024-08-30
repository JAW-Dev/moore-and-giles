<?php
/**
 * Process Credit Card Save
 *
 * @package    MG_Merchant_E_Solutions
 * @subpackage MG_Merchant_E_Solutions/Includes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Merchant_E_Solutions\Includes\Classes\Order;

use MG_Merchant_E_Solutions\Includes\Classes as Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\ProcessCCSave' ) ) {

	/**
	 * Process Credit Card Save
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class ProcessCCSave extends Classes\Gateway {

		/**
		 * ID.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var int
		 */
		protected static $static_id;

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			parent::__construct();
			$this->hooks();
			self::$static_id = $this->id;
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
			add_action( 'woo_mes_' . $this->id . '_credit_card_save_transaction', array( $this, 'run' ), 10, 4 );
		}

		/**
		 * Run.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param Object   $tran         The transaction.
		 * @param array    $order_data   The order data array.
		 * @param array    $post_data  The post values array.
		 * @param WC_Order $order        The order object.
		 *
		 * @return void
		 */
		public static function run( $tran, $order_data, $post_data, $order ) {

			// Set the resquest fields.
			$tran->setAvsRequest( $order_data['billing_address_1'], $order_data['billing_potalcode'] );
			$tran->setRequestField( 'cvv2', isset( $post_data['card_cvc'] ) ? $post_data['card_cvc'] : '' );
			$tran->setRequestField( 'invoice_number', method_exists( $order, 'get_id' ) && $order->get_id() ? $order->get_id() : '' );
			$tran->setRequestField( 'store_card', 'y' );

			// Set the transaction data.
			$tran->setTransactionData( $order_data['card_number'], $order_data['card_expiry'], $order_data['order_total'] );

			// Execute and Proccess.
			$tran->execute();

			if ( $tran->isApproved() ) {
				do_action( 'woo_mes_' . self::$static_id . '_save_card', $tran, $post_data );
			}
		}
	}
}
