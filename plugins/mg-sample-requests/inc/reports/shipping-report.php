<?php

class ShippingMethodsReport extends ShoppReportFramework implements ShoppReport {

	function setup () {
		$this->setchart(array(
			'series' => array('bars' => array('show' => true,'lineWidth'=>0,'fill'=>true,'barWidth' => 0.75),'points'=>array('show'=>false),'lines'=>array('show'=>false)),
			'xaxis' => array('show' => false),
			//'yaxis' => array('tickFormatter' => 'asMoney')
		));
	}

	function query () {
		global $wpdb;
		extract($this->options, EXTR_SKIP);

		$where = array();

		$where[] = "p.created BETWEEN '" . sDB::mkdatetime($starts) . "' AND '" . sDB::mkdatetime($ends) . "'";
		if ( isset($_GET['internal']) && $_GET['internal'] == "true" ) $where[] = "m.value = 1";
		
		$where = join(" AND ",$where);

		$orderd = 'desc';
		if ( in_array( $order, array('asc','desc') ) ) $orderd = strtolower($order);

		$ordercols = 'orders';
		switch ($orderby) {
			case 'shipoption': $ordercols = 'shipoption'; break;
			case 'orders': $ordercols = 'orders'; break;
			case 'items': $ordercols = 'items'; break;
		}
		$ordercols = "$ordercols $orderd";
		
		$id = $this->timecolumn('p.created');
		
		$meta_table = ShoppDatabaseObject::tablename('meta');
		
		$query = "SELECT CONCAT($id) as id, shipoption, count(*) as orders, SUM( (SELECT count(*) FROM {$wpdb->prefix}shopp_purchased pd WHERE pd.purchase = p.id) ) as items 
					FROM `{$wpdb->prefix}shopp_purchase` p 
					LEFT JOIN $meta_table AS m ON m.parent = p.id AND m.context = 'purchase' AND m.name = 'internal_sample_request' AND m.type = 'meta'
					WHERE shipoption <> '' AND $where 					
					GROUP BY shipoption
					ORDER BY $ordercols";
		
		return $query;

	}

	function chartseries ( $label, array $options = array() ) {
		if ( ! $this->Chart ) $this->initchart();

		extract($options);

		$this->Chart->series($record->shipoption, array( 'color' => '#1C63A8', 'data'=> array( array($index, $record->orders) ) ));
	}

	function filters () {
		ShoppReportFramework::rangefilter();
		ShoppReportFramework::filterbutton();
	}

	function columns () {
		return array(
			'shipoption'=>__('Shipping Method','Shopp'),
			'orders'=>__('Orders','Shopp'),
			'items'=>__('Articles','Shopp')
		);
	}

	function sortcolumns () {
		return array(
			'shipoption'=>'shipoption',
			'orders'=>'orders',
			'items'=>'items'
		);
	}

	static function shipoption ($data) { return trim($data->shipoption); }

	static function orders ($data) { return intval($data->orders); }

	static function items ($data) { return intval($data->items); }

}