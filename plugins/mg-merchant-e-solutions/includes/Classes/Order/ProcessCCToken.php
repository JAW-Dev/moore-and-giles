<?php
/**
 * Process Credit Card Token
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

if ( ! class_exists( __NAMESPACE__ . '\\ProcessCCToken' ) ) {

	/**
	 * Process Credit Card Token
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class ProcessCCToken extends Classes\Gateway {

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
			add_action( 'woo_mes_' . $this->id . '_credit_card_token_transaction', array( $this, 'run' ), 10, 3 );
		}

		/**
		 * Run.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param Object $tran         The transaction.
		 * @param array  $order_data   The order data array.
		 * @param string $token        The CC token.
		 *
		 * @return void
		 */
		public static function run( $tran, $order_data, $token ) {

			// Set the request fields.
			$tran->setRequestField( 'card_id', $token );
			$tran->setRequestField( 'card_exp_date', $order_data['card_expiry'] );
			$tran->setRequestField( 'transaction_amount', $order_data['order_total'] );
			$tran->setRequestField( 'cardholder_street_address', $order_data['billing_address_1'] );
			$tran->setRequestField( 'cardholder_zip', $order_data['billing_potalcode'] );
			$tran->setRequestField( 'invoice_number', $order_data['invoice_number'] );
			$tran->setRequestField( 'merchant_initiated', 'y' );

			// Execute and Proccess.
			$tran->execute();
		}
	}
}
