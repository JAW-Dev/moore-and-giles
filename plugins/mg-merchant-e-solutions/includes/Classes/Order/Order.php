<?php
/**
 * Payments.
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

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Order' ) ) {

	/**
	 * Order
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Order extends Classes\Gateway {

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
		 * Process.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param int   $order_id The order ID.
		 * @param array $data     The data for processing the order.
		 *
		 * @return void
		 */
		public function run_transaction( $order_id, $data ) {

			// Bail if order_id is not set.
			if ( ! isset( $order_id ) ) {
				return;
			}

			$order   = wc_get_order( $order_id );
			$process = new Process();

			return $process->order( $order, $this->payment_type( $data['post'], $order_id ), $data );
		}

		/**
		 * Payment Type.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $post_data The post values.
		 * @param int   $order_id  The order ID.
		 *
		 * @return array
		 */
		public function payment_type( $post_data, $order_id ) {
			$type = array(
				'type'      => 'default',
				'pre-order' => false,
			);

			if ( ! $post_data['payment_token'] && 'true' === $post_data['save_to_account'] ) {
				// Process new card and save to account is checked.
				$type['type'] = 'save-new-card';
			} elseif ( 'new' === $post_data['payment_token'] && 'true' === $post_data['save_to_account'] ) {
				// Process new card and save to account is checked if already has saved card.
				$type['type'] = 'existing-save-new-card';
			} elseif ( 'new' !== $post_data['payment_token'] && $post_data['payment_token'] && ! $post_data['save_to_account'] ) {
				// Process using existing saved Card.
				$type['type'] = 'existing-card';
			} elseif ( 'new' === $post_data['payment_token'] && ! $post_data['save_to_account'] ) {
				// Has saved card using new card but not saving it.
				$type['type'] = 'default';
			}

			if ( class_exists( '\\WC_Pre_Orders_Order' ) && \WC_Pre_Orders_Order::order_contains_pre_order( $order_id ) ) {
				$type['pre-order'] = true;
			}

			return apply_filters( 'woo_mes_proccesing_type', $type );
		}

		/**
		 * Complete.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param WC_Order $order     The order object.
		 * @param object   $tran      The Transaction.
		 *
		 * @return array
		 */
		public function complete( $order, $tran) {
			$order_saved = $this->save( $order, $tran );
			$array       = array();

			if ( $order_saved ) {
				// If not using Credit Card payment.
				if ( 'ph' === $this->mode ) {
					$array = array(
						'result'   => 'success',
						'redirect' => $order->get_checkout_payment_url( true ),
					);
				} else {
					$array = array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order ),
					);
				}
			}
			return $array;
		}

		/**
		 * Save.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param WC_Order $order The order object.
		 * @param object   $tran  The transaction object.
		 *
		 * @return boolean
		 */
		public function save( $order, $tran ) {
			$order_id = $order->get_id();

			if ( '000' === $tran->getResponseField( 'error_code' ) ) {

				if ( class_exists( '\\WC_Pre_Orders_Order' ) && \WC_Pre_Orders_Order::order_contains_pre_order( $order_id ) ) {
					$order->update_meta_data( '_mes_transaction_id', $tran->getResponseField( 'transaction_id' ) );
					\WC_Pre_Orders_Order::mark_order_as_pre_ordered( $order );
				} else {
					// Payment complete.
					$order->payment_complete( $tran->getResponseField( 'transaction_id' ) );
					$order->update_meta_data( '_mes_transaction_id', $tran->getResponseField( 'transaction_id' ) );

					// Add order note.
					$order->add_order_note(
						sprintf(
							// translators: Transaction authorization code, and transaction ID.
							__( 'MeS payment approved (Transaction ID: %1$s, Auth Code: %2$s)', 'woocommerce' ),
							$tran->getResponseField( 'transaction_id' ),
							$tran->getResponseField( 'auth_code' )
						)
					);

					// Add auth code to order meta.
					$order->update_meta_data( '_mes_authorization_code', $tran->getResponseField( 'auth_code' ) );

					// Save the meta data change to the DB.
					$order->save();

					$tran_type = $tran->get_trans_type();
					if ( 'P' === $tran_type ) {
						$order->update_status( 'on-hold' );
					}
				}

				\WC()->cart->empty_cart();

				return true;
			}

			return false;
		}
	}
}
