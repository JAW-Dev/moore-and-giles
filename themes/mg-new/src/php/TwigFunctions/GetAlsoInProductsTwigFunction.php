<?php
/**
 * TODO: This class isn't currently used. We should remove it if we continue to not use it.
 */

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;


class GetAlsoInProductsTwigFunction extends TwigFunction {
	/**
	 * Get Also In Products
	 *
	 * @param $product_id
	 *
	 * @return array
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 */
	public function action( $product_id ) {
		$product  = wc_get_product( $product_id );
		$colors   = $product->get_attribute( 'pa_color' );

		$products = wc_get_products(
			[
				'posts_per_page' => 9,
				'orderby'        => 'total_sales',
				'tax_query'      => [ // phpcs:ignore
					'taxonomy' => 'pa_color',
					'field'    => 'name',
					'terms'    => $colors,
					'operator' => 'IN',
				],
			]
		);

		$product_ids = [];

		foreach ( $products as $product ) {
			$product_ids[] = $product->get_id();
		}

		return $product_ids;
	}
}
