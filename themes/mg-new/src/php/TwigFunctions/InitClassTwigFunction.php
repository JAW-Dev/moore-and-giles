<?php
namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;
use Objectiv\Site\Components\FurnitureSpecs;

class InitClassTwigFunction extends TwigFunction {

	/**
	 * Init Class
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function action( $classname, $namespace = '', $method = '', $static = false ) {
		if ( ! empty( $namespace ) ) {
			$call_class = $namespace . '\\' . $classname;

			$this->maybe_call_method( $call_class, $method, $static );
		} else {
			$this->maybe_call_method( $classname, $method, $static );
		}
	}

	/**
	 * Call Method
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function maybe_call_method( $classname, $method, $static ) {
		if ( ! empty( $method ) ) {
			$call_method = "{$method}";

			if ( $static ) {
				$classname::$call_method();
			} else {
				$class = new $classname();
				$class->$call_method();
			}
		} else {
			new $classname;
		}
	}
}
