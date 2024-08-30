<?php
/**
 * Get the color varations data
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

/**
 * GetColorVariationData
 *
 * @author Jason Witt
 */
class GetColorVariationData {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * The color varation data.
	 *
	 * @author Jason Witt
	 *
	 * @return string
	 */
	public static function data() {
		$terms = get_terms( array( 'taxomony' => 'pa_color-family' ) );

		foreach ( $terms as $term ) {
			$color = get_term_meta( $term->term_id, 'product_attribute_color', true );
			$image = get_term_meta( $term->term_id, 'product_attribute_image', true );

			if ( $color ) {
				$term->background_color = $color;
			} else if ( $image ) {
				$url = isset( wp_get_attachment_image_src( $image )[0] ) ? wp_get_attachment_image_src( $image )[0] : '';

				$term->background_image = $url;
			}
		}
		return $terms;
	}
}
