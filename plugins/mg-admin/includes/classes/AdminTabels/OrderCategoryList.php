<?php
/**
 * Ordercategorylist.
 *
 * @package    Mg_Admin
 * @subpackage Mg_Admin/Includes/Classes/AdminTabels
 * @author     Objectiv
 * @copyright  Copyright (c) 2020, Objectiv
 * @license    GNU General Public License v2 or later
 * @since      1.0.0
 */

namespace MgAdmin\Includes\Classes\AdminTabels;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

/**
 * Ordercategorylist.
 *
 * @author Objectiv
 * @since  1.0.0
 */
class OrderCategoryList extends \WP_List_Table {

	/**
	 * Initialize the class
	 *
	 * @author Objectiv
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => __( 'Category Orders Report', 'mg_admin' ),
				'plural'   => __( 'Category Orders Report', 'mg_admin' ),
				'ajax'     => false,
			)
		);
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

		usort( $data, array( $this, 'sort_data' ) );

		$i = 0;
		foreach ( $data as $key ) {
			if ( isset( $key['date'] ) ) {
				$data[ $i ]['date'] = date( 'F j, Y', strtotime( $key['date'] ) );
			}
			$i++;
		}

		if ( $search_key ) {
			$data = $this->filter_table_data( $data, $search_key );
		}

		$per_page     = $this->get_items_per_page( 'posts_per_page', 20 );
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
				$this->category_filter();
				$this->filter_by_date( $which );
			}

			$this->pagination( $which );
			?>
			<br class="clear" />
		</div>
		<?php
	}

	/**
	 * Category Filter
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function category_filter() {
		$orders = $this->get_the_orders();

		$categories = array();

		foreach ( $orders as $order ) {
			$order_id  = $order->get_id();
			$get_order = wc_get_order( $order_id );
			$cats      = $this->get_the_categories( $get_order );

			foreach ( $cats as $cat ) {
				if ( ! in_array( $cat->term_id, $categories, true ) ) {
					$categories[ $cat->term_id ] = $cat;
				}
			}
		}

		$categories = array_values( $categories );

		?>
		<div class="alignleft actions bulkactions">
			<select name="cat-filter" id="filter-cat">
			<option value="">Choose a Category</option>
				<?php
				foreach ( $categories as $category ) {
					?>
					<option value="<?php echo esc_attr( $category->term_id ); ?>" <?php selected( $_GET['cat-filter'], $category->term_id ); ?>><?php echo esc_html( $category->name ); ?></option>
					<?php
				}
				?>
			</select>
		</div>
				<?php
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
	 * Get Columns
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'category' => __( 'Category', 'mg_admin' ),
			'order_id' => __( 'Order ID', 'mg_admin' ),
			'order'    => __( 'order', 'mg_admin' ),
			'customer' => __( 'Customer', 'mg_admin' ),
			'total'    => __( 'Total', 'mg_admin' ),
			'status'   => __( 'Status', 'mg_admin' ),
			'date'     => __( 'Date', 'mg_admin' ),
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
			'category' => array( 'category', true ),
			'order_id' => array( 'order_id', true ),
			'order'    => array( 'order', true ),
			'customer' => array( 'customer', true ),
			'status'   => array( 'status', true ),
			'date'     => array( 'date', true ),
		);
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
			case 'category':
			case 'order_id':
			case 'order':
			case 'customer':
			case 'total':
			case 'status':
			case 'date':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ) ; // phpcs:ignore
		}
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
		$orders = $this->get_the_orders();

		foreach ( $orders as $key => $value ) {

			$order_id     = $value->get_id();
			$order_data   = $value->get_data();
			$get_order    = wc_get_order( $order_id );
			$order_number = $get_order->get_meta( '_order_number' );
			$date_created = $get_order->get_date_created();
			$order_date   = date( 'Y-m-d', strtotime( $date_created ) );
			$order_status = $get_order->get_status();
			$order_total  = $get_order->get_total();
			$get_user     = $get_order->get_user();
			$user_id      = $get_user->ID;
			$user_email   = $get_order->get_billing_email();
			$user         = $user_email;
			$categories   = $this->get_the_categories( $get_order );

			$filtered_category = sanitize_text_field( wp_unslash( $_GET['cat-filter'] ?? '' ) );

			if ( ! empty( $filtered_category ) ) {
				foreach ( $categories as $key => $value ) {
					if ( (int) $filtered_category !== $value->term_id ) {
						unset( $categories[ $key ] );
					}
				}
			}

			if ( ! empty( $user_id ) ) {
				$user = '<a href="' . add_query_arg( 'user_id', $user_id, self_admin_url( 'user-edit.php' ) ) . '">' . $user_email . '</a>';
			}

			if ( ! empty( $categories ) ) {
				foreach ( $categories as $category ) {
					$data[] = array(
						'category' => $category->name,
						'order_id' => '<a href="' . get_admin_url() . 'post.php?post=' . $order_id . '&action=edit">' . $order_id . '</a>',
						'order'    => '<a href="' . get_admin_url() . 'post.php?post=' . $order_id . '&action=edit">' . $order_number . '</a>',
						'customer' => $user,
						'status'   => $order_status,
						'total'    => $order_total,
						'date'     => $order_date,
					);
				}
			}
		}

		return $data;
	}

	/**
	 * Get the Orders
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return object
	 */
	public function get_the_orders() {
		$from_date  = isset( $_GET['date_from'] ) && ! empty( $_GET['date_from'] ) ? sanitize_text_field( wp_unslash( date( 'Y-m-d', strtotime( $_GET['date_from'] ) ) ) ) : date( 'Y-m-d', strtotime( 'first day of this month', ) ); // phpcs:ignore
		$to_date    = isset( $_GET['date_to'] ) && ! empty( $_GET['date_to'] ) ? sanitize_text_field( wp_unslash( date( 'Y-m-d', strtotime( $_GET['date_to'] ) ) ) ) : date( 'Y-m-d', strtotime( 'now' ) ); // phpcs:ignore

		$datetime = new \DateTime( $to_date );
		$datetime->modify( '-1 day' );

		$date_range = $from_date . '...' . $datetime->format( 'Y-m-d' );

		$args = array(
			'date_created' => $date_range,
			'status'       => array( 'wc-completed', 'wc-processing' ),
			'limit'        => -1,
		);

		$query = new \WC_Order_Query( $args );
		return $query->get_orders();
	}

	/**
	 * Get Categories
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param object $order The order object.
	 *
	 * @return object
	 */
	public function get_the_categories( $order ) {
		$categories = array();

		if ( ! empty( $order->get_items() ) ) {
			foreach ( $order->get_items() as $item_key => $item ) {
				$item_data    = $item->get_data();
				$product_id   = $item_data['product_id'];
				$variation_id = $item['variation_id'];

				if ( $item_variation_id ) {
					$product_id = $item_variation_id;
				}

				$categories = get_the_terms( $product_id, 'product_cat' );
			}
		}

		return $categories;
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
		$order   = 'desc';

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
