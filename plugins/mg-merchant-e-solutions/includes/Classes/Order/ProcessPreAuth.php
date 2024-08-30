<?php
/**
 * Process Authorize
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

if ( ! class_exists( __NAMESPACE__ . '\\ProcessPreAuth' ) ) {

	/**
	 * Process Authorize
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class ProcessPreAuth extends Classes\Gateway {

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
			add_action( 'woo_mes_' . $this->id . '_pre_auth_transaction', array( $this, 'run' ) );
		}

		/**
		 * Run.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param WC_Order $order The order object.
		 *
		 * @return void
		 */
		public function run( $order ) {
			$order_id       = $order->get_id();
			$transaction_id = get_post_meta( $order_id, '_mes_transaction_id', true );
			$total          = $order->get_total();
			$tran           = new \TpgSettle( $this->profile_id, $this->profile_key, $transaction_id, $total );

			$tran->setHost( $this->api_endpoint );
			$tran->execute();

			if ( $tran->getResponseField( 'transaction_id' ) === $transaction_id ) {
				if ( $tran->isApproved() ) {
					$order->payment_complete( $tran->getResponseField( 'transaction_id' ) );

					$pre_authorization = metadata_exists( 'post', $order_id, '_mes_pre_authorization' );
					if ( $pre_authorization ) {
						update_post_meta( $order_id, '_mes_pre_authorization', 'completed' );
					}

					$pre_order_status = metadata_exists( 'post', $order_id, '_mes_pre_order_status' );
					if ( $pre_order_status ) {
						update_post_meta( $order_id, '_mes_pre_order_status', 'completed' );
					}

					$order->add_order_note(
						sprintf(
							// translators: Transaction authorization code, and transaction ID.
							__( 'MeS Pre-order Payment <strong>Complete</strong> (Transaction ID: %1$s)', 'woocommerce' ),
							$tran->getResponseField( 'transaction_id' )
						)
					);
				} else {
					// Add order note.
					$order->add_order_note(
						sprintf(
							// translators: Transaction authorization code, and transaction ID.
							__( 'MeS Pre-order Payment <strong>Failed</strong> (Transaction ID: %1$s)', 'woocommerce' ),
							$tran->getResponseField( 'transaction_id' )
						)
					);
				}
				return;
			}
		}
	}
}
