<?php

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;

class StashWooProduct extends TwigFunction {
	public function action( $args ) {
		global $stashed_product, $product;

		$stashed_product = $product;

		return;
	}
}
