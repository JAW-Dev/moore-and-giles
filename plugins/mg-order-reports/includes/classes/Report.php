<?php
/**
 * Report.
 *
 * @package    MG_Order_Reports
 * @subpackage MG_Order_Reports/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2019, Objectiv
 * @license    GPL-2.0
 * @since      1.0.0
 */

namespace MGOrderReports\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Report' ) ) {

	/**
	 * Report.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Report {

		/**
		 * From date.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var string
		 */
		protected $date_from;

		/**
		 * To date.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var string
		 */
		protected $date_to;

		/**
		 * Addon.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var string
		 */
		protected $addon;

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			$this->hooks();
		}

		/**
		 * Hooks
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function hooks() {
			add_action( 'wp_ajax_build_report_csv', array( $this, 'build_report_csv' ) );
			add_action( 'wp_ajax_nopriv_build_report_csv', array( $this, 'build_report_csv' ) );
		}

		/**
		 * Build CSV
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function build_report_csv() {
			if ( MG_ORDER_REPORTS_PLUGIN_REPORT_NONCE !== $_POST['nonce'] ) {
				exit;
			}

			$this->date_from = isset( $_POST['date_from'] ) && ! empty( $_POST['date_from'] ) ? urldecode( sanitize_text_field( wp_unslash( $_POST['date_from'] ) ) ) : date( 'Y-m-d', strtotime( 'first day of this month' ) ); // phpcs:ignore
			$this->date_to   = isset( $_POST['date_to'] ) && ! empty( $_POST['date_to'] ) ? urldecode( sanitize_text_field( wp_unslash( $_POST['date_to'] ) ) ) : date( 'Y-m-d', strtotime( 'first day of this month' ) ); // phpcs:ignore
			$this->addon     = isset( $_POST['addon'] ) && ! empty( $_POST['addon'] ) ? sanitize_text_field( wp_unslash( $_POST['addon'] ) ) : ''; // phpcs:ignore

			ob_clean();

			$now  = new \DateTime();
			$data = $this->strip_tags_deep( $this->table_data() );

			echo wp_json_encode( wp_json_encode( $data ), true );
			exit;
		}

		/**
		 * Table Data
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function table_data() {
			$data   = array();
			$orders = $this->query_orders();

			/** @var \WC_Order $order */
			foreach ( $orders as $order ) {

				$order_id        = $order->get_id();
				$order_data      = $order->get_data();
				$order_number    = $order->get_order_number();
				$order_items     = $order->get_items();
				$personalization = $this->has_addon( $order_items, 'Personalization' ) ? 'true' : '';
				$gift_wrapping   = $this->has_addon( $order_items, 'Gift Wrapping' ) ? 'true' : '';

				$data[] = array(
					'order_id'        => "$order_number ($order_id)",
					'first_name'      => $order_data['billing']['first_name'],
					'last_name'       => $order_data['billing']['last_name'],
					'personalization' => $personalization,
					'gift_wrapping'   => $gift_wrapping,
					'email'           => esc_html( $order_data['billing']['email'] ),
					'date'            => $order_data['date_created']->date( 'Y/m/d' ),
				);
			}

			return $data;
		}

		/**
		 * Query Orders
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function query_orders() {
			$date_range = $this->date_from . '...' . $this->date_to;

			$args = array(
				'limit'        => -1,
				'date_created' => $date_range,
				'type'         => wc_get_order_types( 'sales-reports' ),
			);

			$query  = new \WC_Order_Query( $args );
			$orders = $query->get_orders();
			$addon  = $this->addon;

			if ( $addon ) {
				foreach ( $orders as $key => $value ) {
					$order_items = $value->get_items();
					if ( 'personalization' === $addon ) {
						$has_personalization = $this->has_addon( $order_items, 'Personalization' ) ? true : '';

						if ( ! $has_personalization ) {
							unset( $orders[ $key ] );
						}
					}

					if ( 'gift-wrapping' === $addon ) {
						$has_gift_wrapping = $this->has_addon( $order_items, 'Gift Wrapping' ) ? true : '';

						if ( ! $has_gift_wrapping ) {
							unset( $orders[ $key ] );
						}
					}
				}
			}

			return $orders;
		}

		/**
		 * Has Addon
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array  $order_items The items iin the order.
		 * @param string $type        The type of addon to check for.
		 *
		 * @return boolean
		 */
		public function has_addon( $order_items, $type ) {
			foreach ( $order_items as $order_item ) {
				$item_meta_data = $order_item->get_meta_data();

				foreach ( $item_meta_data as $item_meta ) {
					$data = $item_meta->get_data();
					$key  = $data['key'];

					if ( $type === $key ) {
						return true;
					}
				}
			}
			return false;
		}

		/**
		 * Strip Tags Deep
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $array The array to remove tags from.
		 *
		 * @return array
		 */
		public function strip_tags_deep( $array ) {
			return is_array( $array ) ? array_map( array( $this, 'strip_tags_deep' ), $array ) : wp_strip_all_tags( $array );
		}
	}
}
