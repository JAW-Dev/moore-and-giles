<?php

class MG_VariationCategories {
	public function __construct() {
		add_action('cmb2_init', array($this, 'metaboxes') );
	}

	function metaboxes() {
		$prefix = '_mg_vc_';

		if ( ! isset($_GET['id']) ) return;

		$product = shopp_product( $_GET['id'] );

		$variant_options = array();

		foreach($product->prices as $price) {
			if ( $price->context != 'variation' ) continue;

			$variant_options[$price->id] = $price->label;
		}

		$category_box = new_cmb2_box( array(
			'id'            => $prefix . 'metabox',
			'title'         => __( 'Variation Categories', 'cmb2' ),
			'object_types'  => array( 'shopp_product', ), // Post type
		) );

		$group_field_id = $category_box->add_field( array(
			'id'          => $prefix . 'categories',
			'type'        => 'group',
			'description' => __( 'Variation Category', 'cmb2' ),
			'options'     => array(
				'group_title'   => __( 'Category {#}', 'cmb2' ), // {#} gets replaced by row number
				'add_button'    => __( 'Add Another Category', 'cmb2' ),
				'remove_button' => __( 'Remove Category', 'cmb2' ),
				'sortable'      => true, // beta
				'repeatable'    => true,
			),
		) );

		$category_box->add_group_field( $group_field_id, array(
			'name'       => __( 'Name', 'cmb2' ),
			'desc'       => __( 'Name of variation category', 'cmb2' ),
			'id'         => 'name',
			'type'       => 'text',
		) );

		$category_box->add_group_field( $group_field_id, array(
			'name'       => __( 'Image', 'cmb2' ),
			'desc'       => __( 'Image for variation category', 'cmb2' ),
			'id'         => 'image',
			'type'       => 'file',
		) );

		$category_box->add_group_field( $group_field_id, array(
			'name'       => __( 'Description', 'cmb2' ),
			'desc'       => __( 'Description for variation category', 'cmb2' ),
			'id'         => 'description',
			'type'       => 'textarea',
		) );

		$category_box->add_group_field( $group_field_id, array(
			'name'    => __( 'Variations', 'cmb2' ),
			'desc'    => __( 'The variations in this category', 'cmb2' ),
			'id'      => 'variations',
			'type'    => 'multicheck',
			//'multiple' => true, // Store values in individual rows
			'options' => $variant_options,
			'inline'  => true, // Toggles display to inline
		) );
	}
}
