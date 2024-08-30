<?php
/**
 * Get the Variant Color Label
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

/**
 * VariantColorLabel
 *
 * @author Jason Witt
 */
class VariantColorLabel {

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
	 * @return string
	 */
	public static function get_label() {
		$product_id = isset( $_REQUEST['product_id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['product_id'] ) ) : false;

		if ( $product_id ) {
			$ids          = explode( ':', $product_id );
			$product_id   = isset( $ids[0] ) ? $ids[0] : false;
			$variation_id = isset( $ids[1] ) ? $ids[1] : false;

			if ( class_exists( 'WC_Product_Variable' ) ) {
				$product   = new \WC_Product_Variable( $product_id );
				$variation = wc_get_product( $variation_id );
				$attrs     = method_exists( $variation, 'get_variation_attributes' ) ? $variation->get_variation_attributes() : '';

				if ( $attrs ) {
					foreach ( $attrs as $taxonomy => $term_slug ) {
						$term_obj  = ! is_array( $term_slug ) ? get_term_by( 'slug', $term_slug, 'pa_color' ) : '';
						$term_name = ! empty( $term_obj ) ? $term_obj->name : '';

						if ( $term_name ) {
							echo wp_kses_post( '<h2 class="variant-color">' . $term_name . '</h2>' );
						}
					}
				}
			}
		}
		return '';
	}
}
