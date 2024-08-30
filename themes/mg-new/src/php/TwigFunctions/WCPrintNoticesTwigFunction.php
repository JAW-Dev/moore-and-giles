<?php

namespace Objectiv\Site\TwigFunctions;

use \Objectiv\Site\Base\TwigFunction;

class WCPrintNoticesTwigFunction extends TwigFunction {
	public function action( $return = false ) {
        echo wc_print_notices( $return );
	}
}
