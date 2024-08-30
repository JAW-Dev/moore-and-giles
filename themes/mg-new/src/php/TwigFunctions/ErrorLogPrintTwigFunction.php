<?php

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;

class ErrorLogPrintTwigFunction extends TwigFunction {
	/**
	 * Error Log
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function action( $args ) {
		if ( ! isset( $args ) ) {
			return;
		}
		error_log( print_r( $args, true ) ); // phpcs:ignore
	}
}
