<?php
/**
 * Add the Color as the last name to simple products
 *
 *
 * @author Eldon Yoder
 */

namespace Objectiv\Site\Utilities;

/**
 * ColorLastName
 *
 * @author Eldon Yoder
 */
class ColorLastName {

	/**
	 * Initialize the class
	 *
	 * @author Eldon Yoder
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * The color output.
	 *
	 * @author Eldon Yoder
	 *
	 * @return string
	 */
	public static function render() {
		global $product;

		if ( $product->is_type( 'simple' ) ) {
			$color = $product->get_attribute( 'pa_color' );

			if ( ! empty( $color ) ) {
				echo "<div class='single-color-name'>$color</div>";
			}
		}
	}
}
