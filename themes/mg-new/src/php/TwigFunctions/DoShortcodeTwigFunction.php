<?php

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;

class DoShortcodeTwigFunction extends TwigFunction {
	public function action($shortcode) {
        echo do_shortcode( $shortcode );
	}
}
