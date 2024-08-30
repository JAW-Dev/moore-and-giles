<?php

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;

class SetWooProductTwigFunction extends TwigFunction {
	public function action($passedProduct) {
		global $product;

		if ( is_woocommerce() ) {
			$product = wc_get_product( $passedProduct->ID );
		}

		return;
	}
}