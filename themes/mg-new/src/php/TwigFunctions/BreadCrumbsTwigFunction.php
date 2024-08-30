<?php

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;

class BreadCrumbsTwigFunction extends TwigFunction {
	public function action( $args ) {
		if ( function_exists( 'yoast_breadcrumb' ) ) {
			echo yoast_breadcrumb( $args );
		}
	}
}
