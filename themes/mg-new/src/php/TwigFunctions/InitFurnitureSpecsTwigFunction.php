<?php
namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;
use Objectiv\Site\Components\LeatherTemplate\FurnitureSpecs;

class InitFurnitureSpecsTwigFunction extends TwigFunction {

	/**
	 * Blog Name
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @return void
	 */
	public function action( $args = '' ) {
		FurnitureSpecs::render();
	}
}
