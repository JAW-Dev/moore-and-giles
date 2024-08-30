<?php

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;
use \Objectiv\Site\Factories\TwigFunctionFactory;

class SvgUrlTwigFunction extends TwigFunction {
	public function action( $args ) {
		if ( empty( $args ) || $args === null ) {
			return '';
		}

		$source   = '';
		$response = wp_remote_get( $args, array( 'sslverify' => false ) );

		if ( ! is_wp_error( $response ) ) {
			$source = $response['body'];
		} else {
			return '';
		}

		return $source;
	}
}
