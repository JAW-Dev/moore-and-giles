<?php

class RepSamplingReport extends ShoppReportFramework implements ShoppReport {

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

		$ordercols = 'orders';
		switch ($orderby) {
			case 'orders': $ordercols = 'orders'; break;
			case 'articles': $ordercols = 'articles'; break;
			case 'representative': $ordercols = 'representative'; break;
		}
		$ordercols = "$ordercols $orderd";

		$where_orders = str_ireplace('created', 'p.created', $where);

		$query = "SELECT DISTINCT(u.ID) as id, c.id as customer_id, u.display_name as representative, COALESCE(p.orders, 0) as orders, COALESCE(p.articles, 0) as articles
					FROM wp_users u
					INNER JOIN wp_usermeta m ON m.user_id = u.ID
					LEFT JOIN {$wpdb->prefix}shopp_customer c ON c.wpuser = u.ID
					LEFT JOIN (SELECT p.customer, COUNT(DISTINCT p.id) as orders, COUNT(DISTINCT pd.id) as articles FROM wp_3_shopp_purchase p
								  LEFT JOIN wp_3_shopp_purchased pd ON pd.purchase = p.id
								  WHERE $where_orders
								  GROUP BY p.customer) as p ON p.customer = c.id
					WHERE m.meta_key = 'wp_3_capabilities'
					AND (m.meta_value LIKE '%sales_representative%' OR m.meta_value LIKE '%shopp-csr%')
					AND orders > 0				  
					ORDER BY $ordercols";

		//echo($query); die;
		return $query;

	}

	function chartseries ( $label, array $options = array() ) {
		if ( ! $this->Chart ) $this->initchart();

		extract($options);

		$this->Chart->series($record->representative, array( 'color' => '#1C63A8', 'data'=> array( array($index, $record->articles) ) ));
	}

	function filters () {
		ShoppReportFramework::rangefilter();
		ShoppReportFramework::filterbutton();
	}

	function columns () {
		return array(
			'representative'=>__('Representative','Shopp'),
			'orders'=>__('Orders','Shopp'),
			'articles'=>__('Articles','Shopp'),
		);
	}

	function sortcolumns () {
		return array(
			'representative'=>'representative',
			'orders'=>'orders',
			'articles'=>'articles',
		);
	}

	static function representative ($data) {
		$link = sprintf('<a href="%s">%s</a>', add_query_arg( array('report' => 'rep_products', 'rep_customer_id' => $data->customer_id) ), trim($data->representative) );
		return $link;
	}

	static function orders ($data) { return intval($data->orders); }

	static function articles ($data) { return intval($data->articles); }

}
