<?php

class SamplingReport extends ShoppReportFramework implements ShoppReport {

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

		$where[] = "created BETWEEN '" . sDB::mkdatetime($starts) . "' AND '" . sDB::mkdatetime($ends) . "'";

		$where = join(" AND ",$where);

		$orderd = 'desc';
		if ( in_array( $order, array('asc','desc') ) ) $orderd = strtolower($order);

		$ordercols = 'sorders';
		switch ($orderby) {
			case 'porders': $ordercols = 'porders'; break;
			case 'sorders': $ordercols = 'sorders'; break;
			case 'agent': $ordercols = 'agent'; break;
			case 'items': $ordercols = 'items'; break;
		}
		$ordercols = "$ordercols $orderd";

		$id = $this->timecolumn('created');

		$where_picked_orders = str_ireplace('created', 'pm.created', $where);

		$query = "SELECT u.ID as id, display_name as agent, COALESCE(d1.sorders, 0) as sorders, porders, items
					FROM {$wpdb->users} u
					LEFT JOIN ( SELECT COUNT(*) as sorders, m.value FROM `wp_3_shopp_meta` m WHERE context = 'purchase' AND name = 'shipped_by' AND $where GROUP BY m.value ) as d1 ON d1.value = u.ID
					LEFT JOIN ( SELECT COUNT(DISTINCT ppd.purchase) as porders, pm.value FROM wp_3_shopp_purchased ppd LEFT JOIN wp_3_shopp_meta pm ON pm.parent = ppd.id LEFT JOIN wp_users u ON u.ID = pm.value WHERE pm.name = 'picked' AND $where_picked_orders GROUP BY pm.value ) as d2 ON d2.value = u.ID
					LEFT JOIN ( SELECT COUNT(m.value) as items, m.value FROM wp_3_shopp_meta m WHERE m.name = 'picked' AND $where GROUP BY m.value ) as d3 ON d3.value = u.ID
					LEFT JOIN {$wpdb->usermeta} um ON um.user_id = u.ID
					WHERE porders > 0 OR sorders > 0 OR items > 0
					GROUP BY u.ID ORDER BY $ordercols";
					//WHERE meta_key = 'wp_3_capabilities' AND meta_value LIKE '%shopp-csr%'

		//error_log($query);
		return $query;

	}

	function chartseries ( $label, array $options = array() ) {
		if ( ! $this->Chart ) $this->initchart();

		extract($options);

		$this->Chart->series($record->agent, array( 'color' => '#1C63A8', 'data'=> array( array($index, $record->items) ) ));
	}

	function filters () {
		ShoppReportFramework::rangefilter();
		ShoppReportFramework::filterbutton();
	}

	function columns () {
		return array(
			'agent'=>__('Agent','Shopp'),
			'porders'=>__('Picked Orders','Shopp'),
			'sorders'=>__('Shipped Orders','Shopp'),
			'items'=>__('Articles','Shopp')
		);
	}

	function sortcolumns () {
		return array(
			'agent'=>'agent',
			'porders'=>'porders',
			'sorders'=>'sorders',
			'items'=>'items'
		);
	}

	static function agent ($data) { return trim($data->agent); }

	static function porders ($data) { return intval($data->porders); }

	static function sorders ($data) { return intval($data->sorders); }

	static function items ($data) { return intval($data->items); }

}
