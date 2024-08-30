<?php
/**
 * Process Credit Card
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

if ( ! class_exists( __NAMESPACE__ . '\\ProcessCC' ) ) {

	/**
	 * Process Credit Card
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class ProcessCC extends Classes\Gateway {

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
			add_action( 'woo_mes_' . $this->id . '_credit_card_transaction', array( $this, 'run' ), 10, 5 );
		}

		/**
		 * Run.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param Object   $tran         The transaction.
		 * @param array    $order_data   The order data array.
		 * @param array    $post_values  The post values array.
		 * @param WC_Order $order        The order object.
		 * @param boolean  $preorder     If is a pre-order.
		 *
		 * @return void
		 */
		public function run( $tran, $order_data, $post_values, $order, $preorder ) {

			// Set the resquest fields.
			$tran->setAvsRequest( $order_data['billing_address_1'], $order_data['billing_potalcode'] );
			$tran->setRequestField( 'cvv2', isset( $post_values['card_cvc'] ) ? $post_values['card_cvc'] : '' );
			$tran->setRequestField( 'invoice_number', method_exists( $order, 'get_id' ) && $order->get_id() ? $order->get_id() : '' );

			if ( $preorder ) {
				$tran->setRequestField( 'store_card', 'y' );
			}

			// Set the transaction data.
			$tran->setTransactionData( $order_data['card_number'], $order_data['card_expiry'], $order_data['order_total'] );

			// Execute and Proccess.
			$tran->execute();
		}
	}
}
