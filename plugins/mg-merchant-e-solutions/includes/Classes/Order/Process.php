<?php
/**
 * Process.
 *
 * @package    MG_Merchant_E_Solutions
 * @subpackage MG_Merchant_E_Solutions/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Merchant_E_Solutions\Includes\Classes\Order;

use MG_Merchant_E_Solutions\Includes\Classes as Classes;
use MG_Merchant_E_Solutions\Includes\Classes\Order as Order;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Process' ) ) {

	/**
	 * Process
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Process extends Classes\Gateway {

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
		}

		/**
		 * Process Order.
		 *
		 * @author Jason Witt
		 * @since  1.1.0
		 *
		 * @param \WC_Order $order The order object.
		 * @param array     $type  The type of processing array.
		 * @param array     $data  The data for processing the order.
		 *
		 * @return array
		 */
		public function order( $order, $type, $data ) {
			$payment_token       = isset( $data['post']['payment_token'] ) ? \WC_Payment_Tokens::get( $data['post']['payment_token'] ) : '';
			$payment_token_data  = $payment_token ? $payment_token->get_data() : '';
			$payment_token_last4 = $payment_token && isset( $payment_token_data['last4'] ) ? $payment_token_data['last4'] : null;
			$user_token          = Classes\UserToken::get( $this->id, $payment_token_last4 );
			$token               = method_exists( $user_token, 'get_token' ) && $user_token->get_token() ? $user_token->get_token() : '';


			// Set transaction type.
			if ( 'sale' === $this->tran_type && ! $type['pre-order'] ) {
				$tran = new \TpgSale( $this->profile_id, $this->profile_key );
			} else { // auth or preorder
				$tran = new \TpgPreAuth( $this->profile_id, $this->profile_key );
			}

			// Set the API endpoint URL.
			$tran->setHost( $this->api_endpoint );

			// Run the Credit Card Transactions.
			if ( 'existing-card' === $type['type'] ) {
				do_action( 'woo_mes_' . $this->id . '_credit_card_token_transaction', $tran, $data['order'], $token );
			} elseif ( 'existing-save-new-card' === $type['type'] || 'save-new-card' === $type['type'] ) {
				do_action( 'woo_mes_' . $this->id . '_credit_card_save_transaction', $tran, $data['order'], $data['post'], $order );
			} else {
				do_action( 'woo_mes_' . $this->id . '_credit_card_transaction', $tran, $data['order'], $data['post'], $order, $type['pre-order'] );
			}

			// Bail if transaction isn't approved.
			if ( false === $tran->isApproved() ) {
				$error_message = ! empty( $tran->getResponseField( 'auth_response_text' ) ) ? 'Transaction declined: ' . $tran->getResponseField( 'auth_response_text' ) : '';

				$order->add_order_note( $error_message );

				wc_add_notice( $error_message . '. You may also try a different credit card or payment method.', 'error' );
				return array(
					'result'   => 'fail',
					'redirect' => '',
				);
			}

			// Update post meta.
			$this->update_meta( $type, $order, $user_token, $token, $tran, $data['post'] );

			// Complete the order.
			$order_complete = new Order\Order();

			return $order_complete->complete( $order, $tran );
		}

		/**
		 * Update Meta.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $type         The type of transaction.
		 * @param object $order       The order object.
		 * @param object $user_token  The user token object.
		 * @param string $token       The transaction token.
		 * @param object $tran        The transaction object.
		 * @param array  $post_data   The post data array.
		 *
		 * @return void
		 */
		public function update_meta( $type, $order, $user_token, $token, $tran, $post_data ) {
			$card_type  = '';
			$last4      = '';
			$month      = '';
			$year       = '';
			$card_token = '';
			$preorder   = $type['pre-order'] ? 'holding' : '';
			$tran_type  = $tran->get_trans_type();

			if ( 'existing-card' === $type['type'] ) {
				$card_type  = $user_token->get_card_type();
				$last4      = $user_token->get_last4();
				$month      = $user_token->get_expiry_month();
				$year       = $user_token->get_expiry_year();
				$card_token = $token;
			} else {
				$card_type  = $post_data['credit_card_type'] ? $post_data['credit_card_type'] : '';
				$last4      = substr( $post_data['card_number'], -4 );
				$month      = $post_data['card_expiry_month'];
				$year       = $post_data['card_expiry_year'];
				$card_token = $tran->getResponseField( 'card_id' );

				if ( empty( $card_type) ) {
					$detector = new \CardDetect\Detector();
					$card_type = strtolower( $detector->detect( $post_data['card_number'] ) );
				}
			}

			$cardholder = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();

			if ( 'P' === $tran_type ) {
				$order->update_meta_data( '_mes_pre_authorization', 'holding' );
			}

			if ( $cardholder ) {
				$order->update_meta_data( '_mes_cardholder', $cardholder );
			}

			if ( $card_type ) {
				$order->update_meta_data( '_mes_card_type', $card_type );
			}

			if ( $last4 ) {
				$order->update_meta_data( '_mes_card_number', '************' . $last4 );
			}

			if ( $month && $year ) {
				$order->update_meta_data( '_mes_expiry_date', $month . '/' . $year );
			}

			if ( $month ) {
				$order->update_meta_data( '_mes_expiry_month', $month );
			}

			if ( $year ) {
				$order->update_meta_data( '_mes_expiry_year', $year );
			}

			if ( $card_token ) {
				$order->update_meta_data( '_mes_card_token', $card_token );
			}

			if ( $preorder ) {
				$order->update_meta_data( '_mes_pre_order_status', $preorder );
			}
		}
	}
}
