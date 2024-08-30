<?php
/**
 * Process Void
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

if ( ! class_exists( __NAMESPACE__ . '\\ProcessVoid' ) ) {

	/**
	 * Process Void
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class ProcessVoid extends Classes\Gateway {

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
			add_action( 'woo_mes_' . $this->id . '_void_transaction', array( $this, 'run' ), 10, 2 );
		}

		/**
		 * Run.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param int   $order_id Order ID.
		 * @param float $amount   Refund amount.
		 *
		 * @return boolean
		 */
		public function run( $order_id, $amount = null ) {
			$order    = wc_get_order( $order_id );
			$trans_id = get_post_meta( $order_id, '_mes_transaction_id', true );
			$tran     = '';

			if ( $amount !== $order->get_total() ) {
				return;
			}

			$trans_id = get_post_meta( $order_id, '_mes_transaction_id', true );
			$card_id  = get_post_meta( $order_id, '_mes_token_card_number', true );
			$tran     = new \TpgVoid( $this->profile_id, $this->profile_key, $trans_id );

			$tran->setStoredData( $card_id, $amount );
			$tran->execute();

			if ( '000' === $tran->getResponseField( 'error_code' ) ) {
				$order->update_meta_data( 'transaction_voided', true );
				$order->update_meta_data( 'void_amount', $amount );
				$order->update_meta_data( 'void_trans_id', $tran->getResponseField( 'transaction_id' ) );
				$order->update_meta_data( '_mes_pre_authorization', 'canceled' );
				add_filter(
					'woocommerce_order_fully_refunded_status',
					function( $order_status, $order_id ) use ( $amount ) {
						$order = wc_get_order( $order_id );

						/* translators: The refunt amount. */
						$message = sprintf( esc_html__( 'The order has been voided in the amount of %1$s', 'moore-and-giles' ), wc_price( $amount ) );
						$order->add_order_note( $message );

						return 'cancelled';
					},
					10,
					2
				);

				return true;
			} else {
				return false;
			}
		}
	}
}
