<?php
/**
 * Ordercategorylist.
 *
 * @package    Mg_Admin
 * @subpackage Mg_Admin/Includes/Classes/AdminPages
 * @author     Objectiv
 * @copyright  Copyright (c) 2020, Objectiv
 * @license    GNU General Public License v2 or later
 * @since      1.0.0
 */

namespace MgAdmin\Includes\Classes\AdminPages;

use MgAdmin\Includes\Classes\AdminTabels\OrderCategoryList as OrderCategoryTable;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

/**
 * Ordercategorylist.
 *
 * @author Objectiv
 * @since  1.0.0
 */
class OrderCategoryList {

	/**
	 * Initialize the class
	 *
	 * @author Objectiv
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
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 99 );
		add_filter( 'set-screen-option', array( $this, 'table_set_option' ), 10, 3 );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'styles' ) );
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
		$hook = add_submenu_page(
			'wc-admin&path=/analytics/overview',
			__( 'Category Orders Report', 'mg_admin' ),
			__( 'Category Orders Report', 'mg_admin' ),
			'view_woocommerce_reports',
			'order-caregories-table',
			array( $this, 'render' ),
			4
		);

		add_action( "load-$hook", array( $this, 'table_options' ) );
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
	 * Table Options
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function table_options() {
		$option = 'per_page';
		$args   = array(
			'label'   => 'Posts Per Page',
			'default' => 20,
			'option'  => 'posts_per_page',
		);
		add_screen_option( $option, $args );
		$this->table = new OrderCategoryTable();
	}

	/**
	 * Render
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function render() {
		$this->table->prepare_items();
		?>
		<div class="wrap">
			<h2><?php echo esc_html__( 'Category Orders Report', 'mg_admin' ); ?></h2>
			<?php $this->table->views(); ?>
			<form method="get">
				<input type="hidden" name="page" value="<?php echo esc_html( $_REQUEST['page'] ); // phpcs:ignore ?>" />
				<?php
				$this->table->search_box( __( 'Search', 'mg_admin' ), 'search_id' );
				?>
			</form>
			<?php $this->table->display(); ?>
		</div>
		<?php
	}

	/**
	 * Scripts
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param string $hook The admin page hook.
	 *
	 * @return void
	 */
	public function scripts( $hook ) {
		if ( $hook !== 'analytics_page_order-caregories-table' ) {
			return;
		}

		$file      = 'src/js/categories-table.js';
		$file_path = MGADMIN_DIR_PATH . $file;
		$file_url  = MGADMIN_DIR_URL . $file;
		$version   = file_exists( $file_path ) ? filemtime( $file_path ) : '1.0.0';

		wp_enqueue_script( 'jquery-ui-datepicker' );

		wp_register_script( 'mg-admin-categories-table', $file_url, array( 'jquery', 'jquery-ui-datepicker' ), $version, true );
		wp_enqueue_script( 'mg-admin-categories-table' );
	}

	/**
	 * Styles
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param string $hook The admin page hook.
	 *
	 * @return void
	 */
	public function styles( $hook ) {
		if ( $hook !== 'analytics_page_order-caregories-table' ) {
			return;
		}

		$file      = 'src/css/categories-table.css';
		$file_path = MGADMIN_DIR_PATH . $file;
		$file_url  = MGADMIN_DIR_URL . $file;
		$version   = file_exists( $file_path ) ? filemtime( $file_path ) : '1.0.0';

		wp_enqueue_style( 'jquery-ui', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css', array(), $version, 'all' );

		wp_register_style( 'mg-admin-categories-table', $file_url, array( 'jquery-ui' ), $version, 'all' );
		wp_enqueue_style( 'mg-admin-categories-table' );
	}
}
