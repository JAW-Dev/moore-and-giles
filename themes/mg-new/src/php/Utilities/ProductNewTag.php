<?php
/**
 * Single Product New Tag
 *
 * The markup for the Single Product New tag.
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

/**
 * ProductNewTag
 *
 * @author Jason Witt
 */
class ProductNewTag {

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
	public static function render() {
		global $product;

		// Woocommerce not available? return false.
		if ( ! function_exists( 'wc_get_product' ) ) {
			return false;
		}

		$text       = ( function_exists( 'get_field' ) ) ? obj_get_acf_field( 'woo_product_summary_new_tag_label', 'option' ) : __( 'New', 'moore-and-giles' );
		$product    = wc_get_product( $product );
		$product_id = $product->get_id();
		$terms      = get_the_terms( $product_id, 'product_tag' );

		if ( ! empty( $terms ) && is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				if ( 'new-arrivals' === $term->slug ) {
					printf( '<div class="new-product-tag">%1$s</div>', $text );
				}
			}
		}

	}
}
