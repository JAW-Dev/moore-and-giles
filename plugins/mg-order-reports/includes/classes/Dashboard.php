<?php
/**
 * Dashboard.
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

if ( ! class_exists( __NAMESPACE__ . '\\Dashboard' ) ) {

	/**
	 * Dashboard.
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Dashboard {

		/**
		 * Table.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var object
		 */
		protected $table;

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
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_filter( 'set-screen-option', array( $this, 'table_set_option' ), 10, 3 );
		}

		/**
		 * Admin Menu
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function admin_menu() {
			$hook = add_menu_page(
				__( 'Order Reports', 'mg_order_reports' ),
				__( 'Order Reports', 'mg_order_reports' ),
				'view_woocommerce_reports',
				'mg-order-reports',
				array( $this, 'init' ),
				'dashicons-clipboard'
			);

			add_action( "load-$hook", array( $this, 'add_options' ) );
		}

		/**
		 * Table set option
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param boolean $status Whether to save or skip saving the screen option value. Default false.
		 * @param string  $option The option name.
		 * @param int     $value  The number of rows to use.
		 *
		 * @return int
		 */
		public function table_set_option( $status, $option, $value ) {
			return $value;
		}

		/**
		 * Add Options
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function add_options() {
			$option = 'per_page';
			$args   = array(
				'label'   => 'Orders Per Page',
				'default' => 10,
				'option'  => 'orders_per_page',
			);
			add_screen_option( $option, $args );
			$this->table = new ReportTable();
		}

		/**
		 * Init
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function init() {
			$this->table->prepare_items();
			?>
			<div class="wrap">
				<h2><?php echo esc_html__( 'Order Reports', 'mg_order_reports' ); ?></h2>
				<?php $this->table->views(); ?>
				<form method="get">
					<input type="hidden" name="page" value="<?php echo esc_html( $_REQUEST['page'] ); // phpcs:ignore ?>" />
					<?php
					$this->table->search_box( __( 'Search', 'mg_order_reports' ), 'search_id' );
					?>
				</form>
				<?php $this->table->display(); ?>
			</div>
			<?php
		}
	}
}
