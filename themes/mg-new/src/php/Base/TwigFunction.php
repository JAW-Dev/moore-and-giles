<?php

namespace Objectiv\Site\Base;

abstract class TwigFunction {
	var $function_name;

	public function __construct( $function_name ) {
		$this->function_name = $function_name;
	}

	abstract public function action($args);

	public function get_function_name() {
		return $this->function_name;
	}
}