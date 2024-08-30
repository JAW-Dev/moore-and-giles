<?php
/**
 * Bundle Product Rpice
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

/**
 * BundleProductPrice
 *
 * @author Jason Witt
 */
class BundleProductPrice {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * New Tag.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public static function render() {
		global $product;

		if ( $product->is_type( 'bundle' ) ) {
			?>
				<span class="price">
					<span class="woocommerce-Price-amount amount">
						<?php echo wc_price( $product->get_price() ); ?>
					</span>
				</span>
			<?php
		}
	}
}
