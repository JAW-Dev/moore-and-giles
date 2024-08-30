<?php

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;

class UnStashWooProduct extends TwigFunction {
	public function action( $args ) {
		global $stashed_product, $product;

		if ( ! empty( $stashed_product ) ) {
			$product = $stashed_product;
		}

		return;
	}
}
