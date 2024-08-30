<?php
/**
 * Process Refund
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

if ( ! class_exists( __NAMESPACE__ . '\\ProcessRefund' ) ) {

	/**
	 * Process Refund
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class ProcessRefund extends Classes\Gateway {

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
			add_action( 'woo_mes_' . $this->id . '_refund_transaction', array( $this, 'run' ), 10, 2 );
		}

		/**
		 * Run.
		 *
		 * @author Jason Witt
		 * @since  1.1.0
		 *
		 * @param int   $order_id Order ID.
		 * @param float $amount   Refund amount.
		 *
		 * @return boolean
		 */
		public function run( $order_id, $amount = null ) {
			$order      = wc_get_order( $order_id );
			$trans_id   = $order->get_transaction_id();
			$user_token = Classes\UserToken::get( $this->id );
			$token      = method_exists( $user_token, 'get_token' ) && $user_token->get_token() ? $user_token->get_token() : '';
			$tran       = new \TpgRefund( $this->profile_id, $this->profile_key, $trans_id );

			$tran->setStoredData( $token, $amount );
			$tran->execute();

			if ( '000' === $tran->getResponseField( 'error_code' ) ) {
				update_post_meta( $order_id, 'transaction_refunded', true );
				update_post_meta( $order_id, 'refund_amount', $amount );
				update_post_meta( $order_id, 'refund_trans_id', $tran->getResponseField( 'transaction_id' ) );

				if ( $order->get_total() === $amount ) {
					/* translators: The refund amount */
					$message = sprintf( esc_html__( 'The Order has been completely refunded a total of %1$s.', 'moore-and-giles' ), wc_price( $amount ) );
					if ( ! $order->has_status( 'refunded' ) ) {
						$order->update_status( 'refunded', $message );
					} else {
						$order->add_order_note( $message );
					}
				} else {
					/* translators: The refund amount */
					$message = sprintf( esc_html__( 'The Order has been refunded a total of %1$s.', 'moore-and-giles' ), wc_price( $amount ) );
					$order->add_order_note( $message );
				}

				return true;
			} else {
				return false;
			}
		}
	}
}
