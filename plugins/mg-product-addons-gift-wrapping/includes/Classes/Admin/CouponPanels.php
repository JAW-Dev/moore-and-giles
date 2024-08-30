<?php
/**
 * Coupon Panel.
 *
 * @package    MG_Product_Addons_Gift_Wrapping
 * @subpackage MG_Product_Addons_Gift_Wrapping/Inlcudes/Classes
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_Product_Addons_Gift_Wrapping\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\CouponPanels' ) ) {

	/**
	 * Coupon Panels
	 *
	 * @author Jason Witt
	 * @since  1.1.0
	 */
	class CouponPanels {

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
		 *     @type string $version         The plugin version.
		 *     @type string $plugin_dir_url  The plugin directory URL.
		 *     @type string $plugin_dir_path The plugin Directory Path.
		 *     @type string $field_id_code   The code field ID.
		 *     @type string $field_id_box    The box field ID.
		 *     @type string $field_id_enable The box enable ID.
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
			<div id="objectv_gift_wrapping_types" class="panel woocommerce_options_panel objectv_gift_wrapping_types_panel">
				<div class="options_group">
					<?php
					$enable = new Enable( $this->args );
					$enable->render();

					$enable = new Boxes( $this->args );
					$enable->render();

					$enable = new Code( $this->args );
					$enable->render();
					?>
				</div>
			</div>
			<?php
		}
	}
}
