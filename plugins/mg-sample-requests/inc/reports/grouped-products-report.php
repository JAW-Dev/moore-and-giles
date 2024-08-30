<?php

class TopProductsReport extends ShoppReportFramework implements ShoppReport {

	public function setup () {
		$this->setchart(array(
			'series' => array(
					'bars' => array(
						'show' => true,
						'lineWidth' => 0,
						'fill' => true,
						'barWidth' => 0.75
				),
				'points' => array('show' => false),
				'lines'  => array('show' => false)
			),
			'xaxis' => array('show' => false),
			'yaxis' => array('tickFormatter' => 'asMoney')
		));
	}

	public function query () {
		$this->options = array_merge(array( // Define default URL query parameters
			'orderby' => 'orders',
			'order' => 'desc'
		), $this->options);
		extract($this->options, EXTR_SKIP);

		$where = array();

		$where[] = "o.created BETWEEN '" . sDB::mkdatetime($starts) . "' AND '" . sDB::mkdatetime($ends) . "'";
		$where[] = "orders.txnstatus IN ('authed','captured')";

		$where = join(" AND ",$where);

		if ( ! in_array( $order, array('asc', 'desc') ) ) $order = 'desc';
		if ( ! in_array( $orderby, array('orders', 'sold', 'grossed') ) ) $orderby = 'orders';
		$ordercols = "$orderby $order";

		$id = "o.product";

		$purchase_table = ShoppDatabaseObject::tablename('purchase');
		$purchased_table = ShoppDatabaseObject::tablename('purchased');
		$meta_table = ShoppDatabaseObject::tablename('meta');
		$product_table = WPDatabaseObject::tablename(ShoppProduct::$table);

		$query = "SELECT CONCAT($id) AS id,
							p.post_title AS product,
							SUM(o.quantity) AS sold,
							COUNT(DISTINCT o.purchase) AS orders,
							SUM(o.total) AS grossed
					FROM $purchased_table AS o INNER JOIN $purchase_table AS orders ON orders.id=o.purchase
					JOIN $product_table AS p ON p.ID=o.product
					LEFT JOIN $meta_table AS m ON m.parent = o.purchase AND m.context = 'purchase' AND m.name = 'internal_sample_request' AND m.type = 'meta'
					WHERE $where
					GROUP BY CONCAT($id) ORDER BY $ordercols";
		
		return $query;

	}

	public function chartseries ( $label, array $options = array() ) {
		if ( ! $this->Chart ) $this->initchart();
		extract($options);

		$this->Chart->series($record->product, array( 'color' => '#1C63A8', 'data'=> array( array($index, $record->grossed) ) ));
	}

	public function filters () {
		ShoppReportFramework::rangefilter();
		ShoppReportFramework::filterbutton();
	}

	public function columns () {
		return array(
			'product' => __('Product', 'Shopp'),
			'orders'  => __('Orders', 'Shopp'),
			'sold'    => __('Items', 'Shopp'),
			'grossed' => __('Grossed', 'Shopp')
		);
	}

	public function sortcolumns () {
		return array(
			'orders'  => 'orders',
			'sold'    => 'sold',
			'grossed' => 'grossed'
		);
	}

	public static function product ( $data ) { return trim($data->product); }

	public static function orders ( $data ) { return intval($data->orders); }

	public static function sold ( $data ) { return intval($data->sold); }

	public static function grossed ( $data ) { return money($data->grossed); }

}