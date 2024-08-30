<?php
/**
 * Coupon Panels.
 *
 * @package    MG_Shipping_Coupons
 * @subpackage MG_Shipping_Coupons/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Shipping_Coupons\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Coupon_Panels' ) ) {

	/**
	 * Coupon Panels
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Coupon_Panels {

		/**
		 * Args.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @var array
		 */
		protected $args;

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param array $args {
		 *     Array the plugin arguments.
		 *
		 *     @type string $version          The plugin version.
		 *     @type string $plugin_dir_url   The plugin directory URL.
		 *     @type string $plugin_dir_path  The plugin Directory Path.
		 *     @type string $field_id_code    The code field ID.
		 *     @type string $field_id_enable  The enable field ID.
		 *     @type string $field_id_method  The method field ID.
		 *     @type string $field_id_service The service field ID.
		 * }
		 *
		 * @return void
		 */
		public function __construct( $args = array() ) {
			$this->args = $args;
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
			add_action( 'woocommerce_coupon_data_panels', array( $this, 'panel' ), 10, 0 );
		}

		/**
		 * Panel.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function panel() {
			?>
			<div id="objectv_shipping_types" class="panel woocommerce_options_panel objective_shipping_types_panel">
				<div class="options_group">
					<?php
					$enable = new Enable( $this->args );
					$enable->render();

					$methods = new Methods( $this->args );
					$methods->render();

					$services = new Services( $this->args );
					$services->render();

					$code = new Code( $this->args );
					$code->render();
					?>
				</div>
			</div>
			<?php
		}
	}
}
