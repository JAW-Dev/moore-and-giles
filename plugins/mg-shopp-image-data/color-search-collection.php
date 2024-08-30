<?php
class ColorSearch extends SmartCollection {

	public static $slugs = array('color-search');

	public function smart ( array $options = array() ) {
		add_filter('shopp_product_loader', array($this, 'product_loader_object'), 10, 2);

		global $wpdb;
		if ( ! isset($options['c']) ) $options['c'] = "Red";

		$color = ucfirst( $options['c'] );

		$this->name = "$color Leather";

		$meta_table = $wpdb->prefix . 'shopp_meta';
		// $index => "INNER JOIN $index AS search ON search.product=p.ID"
		$joins = array();
		$joins[ $meta_table ] = "LEFT JOIN $meta_table m ON m.parent = p.ID";

		$where = array();
		$where[] = "p.post_status = 'publish'";
		$where[] = "m.context = 'product'";
		$where[] = "m.type = 'image'";
		$where[] = "m.id IN (SELECT parent FROM $meta_table WHERE context = 'image' AND type = 'meta' AND name = 'colors' AND FIND_IN_SET('$color', value) )";

		$columns = array();
		$columns[] = "m.id as image_id";
		$columns[] = "(SELECT value FROM $meta_table as nm WHERE context = 'image' AND type = 'meta' AND name = 'price' AND nm.parent = m.id) as stockid";

		$this->loading = array();
		$this->loading['joins'] = $joins;
		$this->loading['where'] = $where;
		$this->loading['columns'] = implode(', ', $columns);
		//$this->loading['debug'] = true;
	}

	function product_loader_object ( $Object, $record ) {
		if ( ! isset($record->image_id) ) return $Object;

		$Object->load_data( array('images','prices') );
		$Object->meta['color_coverimage'] = $record->image_id;
		$Object->meta['image_variant'] = $record->stockid;

		return $Object;
	}
}