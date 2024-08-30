<?php
/**
 * Coupon Panel.
 *
 * @package    MGSpecialtyCoupons
 * @subpackage MGSpecialtyCoupons/Inlcudes/Classes/Admin/Fields/Shipping
 * @author     Objectiv
 * @copyright  Copyright (c) 2018, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MGSpecialtyCoupons\Includes\Classes\Admin\Fields\Shipping;

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
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 *
		 * @return void
		 */
		public function __construct() {
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
					$methods = new Methods();
					$methods->render();

					$services = new Services();
					$services->render();

					$code = new Code();
					$code->render();
					?>
				</div>
			</div>
			<?php
		}
	}
}
