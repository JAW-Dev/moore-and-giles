<?php

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;

class IsCartOpenedTwigFunction extends TwigFunction {
	/**
	 * Error Log
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return boolean
	 */
	public function action( $args = '' ) {
		$opened = isset( $_GET['open-cart'] ) ? wp_unslash( sanitize_text_field( $_GET['open-cart'] ) ) : false;
		return $opened;
	}
}
