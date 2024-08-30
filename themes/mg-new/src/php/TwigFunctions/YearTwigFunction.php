<?php
namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;

class YearTwigFunction extends TwigFunction {

	/**
	 * Copyright Year
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function action( $args = '' ) {
		echo date( 'Y' );
	}
}
