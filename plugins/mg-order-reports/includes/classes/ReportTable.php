<?php
/**
 * ReportTable.
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

/**
 * WP_List_Table
 */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if ( ! class_exists( __NAMESPACE__ . '\\ReportTable' ) ) {

	/**
	 * ReportTable.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class ReportTable extends \WP_List_Table {

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {
			if ( function_exists( 'get_current_screen' ) ) {
				$this->screen = get_current_screen();
			}
			parent::__construct(
				array(
					'singular' => __( 'Report Orders', 'mg_order_reports' ),
					'plural'   => __( 'Report Orders', 'mg_order_reports' ),
					'ajax'     => false,
				)
			);
		}

		/**
		 * Display Table Nav
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $which The setion of the table (top, bottom).
		 *
		 * @return void
		 */
		public function display_tablenav( $which ) {
			?>
			<div class="tablenav <?php echo esc_attr( $which ); ?>">
				<?php
				if ( 'top' === $which ) {
					$this->filter_by_date( $which );
				}

				if ( 'bottom' === $which ) {
					echo '<button id="download-report" class="button" data-nonce="' . esc_attr( MG_ORDER_REPORTS_PLUGIN_REPORT_NONCE ) . '">Download Report</button>';
				}

				$this->pagination( $which );
				?>
				<br class="clear" />
			</div>
			<?php
		}

		/**
		 * Extra Table Nav
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $which The setion of the table (top, bottom).
		 *
		 * @return void
		 */
		public function extra_tablenav( $which ) {
			global $wp_meta_boxes;

			$views = $this->get_views();

			if ( empty( $views ) ) {
				return;
			}

			$this->views();
		}

		/**
		 * Filter by date
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $which The setion of the table (top, bottom).
		 *
		 * @return void
		 */
		public function filter_by_date( $which ) {
			$from_date  = isset( $_GET['date_from'] ) && ! empty( $_GET['date_from'] ) ? sanitize_text_field( wp_unslash( date( 'F j, Y', strtotime( $_GET['date_from'] ) ) ) ) : date( 'F j, Y', strtotime( 'first day of this month', ) ); // phpcs:ignore
			$to_date    = isset( $_GET['date_to'] ) && ! empty( $_GET['date_to'] ) ? sanitize_text_field( wp_unslash( date( 'F j, Y', strtotime( $_GET['date_to'] ) ) ) ) : date( 'F j, Y', strtotime( 'now' ) ); // phpcs:ignore
			$query_string = sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) );// phpcs:ignore

			?>
			<form class="order-dates tablenav-pages" method="get">
				<?php
				foreach ( $_GET as $key => $value ) { // phpcs:ignore
					echo '<input type="hidden" name="' . esc_attr( htmlspecialchars( $key ) ) . '" value="' . esc_attr( htmlspecialchars( $value ) ) . '" />';
				}
				?>
				<input type="text" name="date_from" placeholder="Date From" value="<?php echo esc_attr( $from_date ); ?>" />
				<input type="text" name="date_to" placeholder="Date To" value="<?php echo esc_attr( $to_date ); ?>" />
				<button id="top" class="button">Filter</button>
			</form>
			<?php
		}

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function prepare_items() {
			$search_key = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : ''; // phpcs:ignore
			$columns    = $this->get_columns();
			$hidden     = $this->get_hidden_columns();
			$sortable   = $this->get_sortable_columns();
			$data       = $this->table_data();

			if ( $search_key ) {
				$data = $this->filter_table_data( $data, $search_key );
			}

			usort( $data, array( &$this, 'sort_data' ) );

			$per_page     = $this->get_items_per_page( 'orders_per_page', 25 );
			$current_page = $this->get_pagenum();
			$total_items  = count( $data );

			$this->set_pagination_args(
				array(
					'total_items' => $total_items,
					'per_page'    => $per_page,
				)
			);

			$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

			$this->_column_headers = $this->get_column_info();

			$this->items = $data;
		}

		/**
		 * Filter Table Data
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array  $table_data The table data array.
		 * @param string $search_key The search term.
		 *
		 * @return array
		 */
		public function filter_table_data( $table_data, $search_key ) {
			$filtered_table_data = array_values(
				array_filter(
					$table_data,
					function( $row ) use ( $search_key ) {
						foreach ( $row as $row_val ) {
							if ( stripos( $row_val, $search_key ) !== false ) {
								return true;
							}
						}
					}
				)
			);
			return $filtered_table_data;
		}

		/**
		 * Get Views
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get_views() {
			global $wp;
			$current_url  = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '';
			$query_string = sanitize_text_field( wp_unslash( $_SERVER['QUERY_STRING'] ) );// phpcs:ignore

			parse_str( $query_string, $params );
			unset( $params['addon'] );

			$new_url = $current_url . '?' . http_build_query( $params );
			$links   = array(
				'all'             => sprintf( __( '<a href="%s">All</a>', 'mg_order_reports' ), $new_url ), // phpcs:ignore
				'personalization' => sprintf( __( '<a href="%s">Personalization</a>', 'mg_order_reports' ), $new_url . '&addon=personalization' ), // phpcs:ignore
				'gift_wrapping'   => sprintf( __( '<a href="%s">Gift Wrapping</a>', 'mg_order_reports' ), $new_url . '&addon=gift-wrapping' ), // phpcs:ignore
			);
			return $links;
		}

		/**
		 * Get Columns
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get_columns() {
			return array(
				'order_id'        => __( 'Order', 'mg_order_reports' ),
				'first_name'      => __( 'First Name', 'mg_order_reports' ),
				'last_name'       => __( 'Last Name', 'mg_order_reports' ),
				'personalization' => __( 'Personalization', 'mg_order_reports' ),
				'gift_wrapping'   => __( 'Gift Wrapping', 'mg_order_reports' ),
				'email'           => __( 'Email', 'mg_order_reports' ),
				'date'            => __( 'Date', 'mg_order_reports' ),
			);
		}

		/**
		 * Get Hidden Columns
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get_hidden_columns() {
			return array();
		}

		/**
		 * Sortable Columns
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return array
		 */
		public function get_sortable_columns() {
			return array(
				'order_id' => array( 'order_id', false ),
				'date'     => array( 'date', false ),
			);
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

				if ( ! empty( $order ) ) {
					$order_id        = $order->get_id();
					$order_data      = $order->get_data();
					$order_number    = $order->get_order_number();
					$order_url       = method_exists( $order, 'get_edit_order_url' ) ? '<a href="' . esc_url( $order->get_edit_order_url() ) . '">' . "$order_number ($order_id)" . '</a>' : $order_id;
					$billing_email   = ! empty( $order_data['billing']['email'] ) ? $order_data['billing']['email'] : '';
					$user            = ! empty( $order_data['billing']['email'] ) ? get_user_by( 'email', $billing_email ) : '';
					$user_url        = ! empty( $user ) ? get_edit_user_link( $user->ID ) : '';
					$order_items     = $order->get_items();
					$personalization = $this->has_addon( $order_items, 'Personalization' ) ? $this->addon_indicator() : '';
					$gift_wrapping   = $this->has_addon( $order_items, 'Gift Wrapping' ) ? $this->addon_indicator() : '';

					$data[] = array(
						'order_id'        => $order_url,
						'first_name'      => ! empty( $order_data['billing']['first_name'] ) ? $order_data['billing']['first_name'] : '',
						'last_name'       => ! empty( $order_data['billing']['last_name'] ) ? $order_data['billing']['last_name'] : '',
						'personalization' => $personalization,
						'gift_wrapping'   => $gift_wrapping,
						'email'           => '<a href="' . esc_url( $user_url ) . '">' . esc_html( $billing_email ) . '</a>',
						'date'            => $order_data['date_created']->date( 'Y/m/d' ),
					);
				}
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
			$from_date  = isset( $_GET['date_from'] ) && ! empty( $_GET['date_from'] ) ? sanitize_text_field( wp_unslash( date( 'Y-m-d', strtotime( $_GET['date_from'] ) ) ) ) : date( 'Y-m-d', strtotime( 'first day of this month', ) ); // phpcs:ignore
			$to_date    = isset( $_GET['date_to'] ) && ! empty( $_GET['date_to'] ) ? sanitize_text_field( wp_unslash( date( 'Y-m-d', strtotime( $_GET['date_to'] ) ) ) ) : date( 'Y-m-d', strtotime( 'now' ) ); // phpcs:ignore
			$date_range = $from_date . '...' . $to_date;

			$args = array(
				'limit'        => -1,
				'date_created' => $date_range,
				'type'         => wc_get_order_types( 'sales-reports' ),
			);

			$query  = new \WC_Order_Query( $args );
			$orders = $query->get_orders();

			$addon = isset( $_GET['addon'] ) ? sanitize_text_field( wp_unslash( $_GET['addon'] ) ) : ''; // phpcs:ignore

			if ( $addon ) {
				foreach ( $orders as $key => $value ) {
					$order_items = $value->get_items();
					if ( 'personalization' === $addon ) {
						$has_personalization = $this->has_addon( $order_items, 'Personalization' ) ? $this->addon_indicator() : '';

						if ( ! $has_personalization ) {
							unset( $orders[ $key ] );
						}
					}

					if ( 'gift-wrapping' === $addon ) {
						$has_gift_wrapping = $this->has_addon( $order_items, 'Gift Wrapping' ) ? $this->addon_indicator() : '';

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
		 * Addon Indicator
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return string
		 */
		public function addon_indicator() {
			return '<span class="indicator"></span><span class="indicator__text">true</span>';
		}

		/**
		 * Column Default
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array  $item        The column items.
		 * @param string $column_name The column name.
		 *
		 * @return mixed
		 */
		public function column_default( $item, $column_name ) {
			switch ( $column_name ) {
				case 'order_id':
				case 'first_name':
				case 'last_name':
				case 'personalization':
				case 'gift_wrapping':
				case 'email':
				case 'date':
					return $item[ $column_name ];
				default:
					return print_r( $item, true ) ; // phpcs:ignore
			}
		}

		/**
		 * Sort Data
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $a Item A.
		 * @param string $b Item B.
		 *
		 * @return array
		 */
		public function sort_data( $a, $b ) {
			// Set defaults.
			$orderby = 'date';
			$order   = 'asc';

			// If orderby is set, use this as the sort column.
			if ( ! empty( $_GET['orderby'] ) ) { // phpcs:ignore
				$orderby = $_GET['orderby']; // phpcs:ignore
			}

			// If order is set use this as the order.
			if ( ! empty( $_GET['order'] ) ) { // phpcs:ignore
				$order = $_GET['order']; // phpcs:ignore
			}

			$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

			if ( 'desc' === $order ) {
				return $result;
			}

			return -$result;
		}
	}
}
