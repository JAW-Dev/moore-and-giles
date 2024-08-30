<?php
/**
 * Custom price output.
 *
 * Remove the change from the price is it's .00
 * 
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

/**
 * CustomPrice
 *
 * @author Jason Witt
 */
class CustomPrice {

    /**
     * Initialize the class
     *
     * @author Jason Witt
     *
     * @return void
     */
    public function __construct() {}

    /**
     * The price output.
     *
     * @author Jason Witt
     *
     * @return string
     */
    public static function output( $price, $product ) {
		$price = str_replace( '.00', '', $price );
		return $price;
    }
}
