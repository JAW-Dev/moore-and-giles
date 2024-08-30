<?php
/**
 * Facet Sort Options
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

/**
 * FacetSortOptions
 *
 * @author Jason Witt
 */
class FacetSortOptions {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Catalog Pre Get Posts
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 *
	 * @param object $query The query object.
	 *
	 * @return void
	 */
	public static function catalog_pre_get_posts( $query ) {
		if ( is_tax( 'product_cat' ) && ! is_admin() ) {
			$query->set( 'orderby', 'date' );
			$query->set( 'order', 'ASC' );
		}
	}

	/**
	 * Dropdown Options.
	 *
	 * @author Jason Witt
	 *
	 * @param array $options The dropdown options.
	 * @param array $params  The facet parameters.
	 *
	 * @return array
	 */
	public static function options( $options, $params ) {
		unset( $options['title_asc'] );
		unset( $options['title_desc'] );
		unset( $options['date_asc'] );

		$options['default']['label'] = __( 'Sort By', 'moore-and-giles' );

		$options['date_desc'] = array(
			'label' => __( 'Sort By', 'moore-and-giles' ),
		);

		$options['date_desc'] = array(
			'label'      => __( 'Newest', 'moore-and-giles' ),
			'query_args' => array(
				'orderby' => 'date',
				'order'   => 'DESC',
			),
		);

		$options['date_desc'] = array(
			'label'      => __( 'Best Selling', 'moore-and-giles' ),
			'query_args' => array(
				'orderby'  => 'meta_value_num',
				'meta_key' => 'total_sales',
				'order'    => 'DESC',
			),
		);

		$options['product_asc'] = array(
			'label'      => __( 'Product A-Z', 'moore-and-giles' ),
			'query_args' => array(
				'orderby' => 'title',
				'order'   => 'ASC',
			),
		);

		$options['product_desc'] = array(
			'label'      => __( 'Product Z-A', 'moore-and-giles' ),
			'query_args' => array(
				'orderby' => 'title',
				'order'   => 'DESC',
			),
		);

		$options['price'] = array(
			'label'      => __( 'Price: low to high', 'woocommerce' ),
			'query_args' => array(
				'orderby'  => 'meta_value_num',
				'meta_key' => '_price',
				'order'    => 'asc',
			),
		);

		$options['price-desc'] = array(
			'label'      => __( 'Price: high to low', 'woocommerce' ),
			'query_args' => array(
				'orderby'  => 'meta_value_num',
				'meta_key' => '_price',
				'order'    => 'desc',
			),
		);
		return $options;
	}
}
