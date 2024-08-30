<?php

class MG_SourceCodeReport extends ShoppReportFramework implements ShoppReport {

	function setup () {
		$this->setchart(array(
			'series' => array('bars' => array('show' => true,'lineWidth'=>0,'fill'=>true,'barWidth' => 0.75),'points'=>array('show'=>false),'lines'=>array('show'=>false)),
			'xaxis' => array('show' => false),
			//'yaxis' => array('tickFormatter' => 'asMoney')
		));
	}

	function query () {
		global $wpdb;

		$this->options = array_merge(array( // Define default URL query parameters
			'orderby' => 'articles',
			'order' => 'desc'
		), $this->options);
		extract($this->options, EXTR_SKIP);

		$where = array();

		$where[] = "o.created BETWEEN '" . sDB::mkdatetime($starts) . "' AND '" . sDB::mkdatetime($ends) . "'";
		$where[] = "o.txnstatus IN ('authed', 'captured', 'CHARGED')";

		$where = join(" AND ",$where);

		if ( ! in_array( $order, array('asc', 'desc') ) ) $order = 'desc';
		if ( ! in_array( strtolower($orderby), array('orders', 'articles', 'grossed','source_code') ) ) $orderby = 'articles';
		$ordercols = "$orderby $order";

		$id = "m.value";
		$orders_table = ShoppDatabaseObject::tablename('purchase');
		$purchased_table = ShoppDatabaseObject::tablename('purchased');

		$query = "SELECT CONCAT($id) AS id,
							o.country AS country,
							COUNT(DISTINCT o.id) AS orders,
							SUM( (SELECT SUM(p.quantity) FROM $purchased_table AS p WHERE o.id = p.purchase) ) AS articles,
							SUM(o.subtotal) AS grossed,		
							m.value as source_code					
					FROM $orders_table AS o
					LEFT JOIN (SELECT value, parent FROM {$wpdb->prefix}shopp_meta WHERE type = 'meta' AND context = 'purchase' AND name = 'source-code' ) as m ON m.parent = o.id
					WHERE $where AND m.value IS NOT NULL
					GROUP BY CONCAT($id) ORDER BY $ordercols";

		//echo $query; die;

		return $query;
	}

	function chartseries ( $label, array $options = array() ) {
		if ( ! $this->Chart ) $this->initchart();

		extract($options);

		$this->Chart->series($record->division, array( 'color' => '#1C63A8', 'data'=> array( array($index, $record->articles) ) ));
	}

	function filters () {
		ShoppReportFramework::rangefilter();
		ShoppReportFramework::filterbutton();
	}

	function columns () {
		return array(
			'source_code'=>__('Source Code','Shopp'),
			'orders'=>__('Orders','Shopp'),
			'articles'=>__('Articles','Shopp'),
		);
	}

	function sortcolumns () {
		return array(
			'source_code'=>'Source Code',
			'orders'=>'orders',
			'articles'=>'articles',
		);
	}

	static function source_code ($data) {
		$url = add_query_arg( array('action' => 'search', 'field' => 'source-code', 'value' => $data->source_code), admin_url('admin.php?page=order-search') );
		return sprintf('<a href="%s">%s</a>', $url, $data->source_code);
	}

	static function orders ($data) { return intval($data->orders); }

	static function articles ($data) { return intval($data->articles); }

}
