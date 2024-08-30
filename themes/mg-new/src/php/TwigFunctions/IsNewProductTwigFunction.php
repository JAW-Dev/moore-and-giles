<?php

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;

class IsNewProductTwigFunction extends TwigFunction {
	public function action( $product ) {

		// Bail if wc_get_product doesn't exists or if product is empty.
		if ( ! function_exists( 'wc_get_product' ) || empty( $product ) ) {
			return false;
		}

		$new_product = false;

		try {
			$product = wc_get_product( $product );

			if ( has_term( 'new-items', 'product_cat', $product->get_id() ) ) {
				$new_product = true;
			}
		} catch ( \Exception $e ) {
			// Some issue? return false;
			return false;
		}

		// Otherwise return true or false
		return $new_product;
	}
}
