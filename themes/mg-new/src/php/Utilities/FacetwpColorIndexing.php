<?php
/**
 * Have FacetWP index multiple data sources into one
 *
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

/**
 * FacetwpColorIndexing
 *
 * @author Jason Witt
 */
class FacetwpColorIndexing {

    /**
     * Initialize the class
     *
     * @author Jason Witt
     *
     * @return void
     */
    public function __construct() {}

    /**
     * New Tag.
     *
     * @author Jason Witt
	 * 
	 * @param array  $params An associative array of data to be indexed.
	 * @param object $class  The indexer class.
     *
     * @return array
     */
    public static function index( $params, $class ) {
		$name = $params['facet_name'];
		if ( 'attribute_pa_color' == $name ) {
			$params['facet_name'] = 'color';
		}
		return $params;
    }
}
