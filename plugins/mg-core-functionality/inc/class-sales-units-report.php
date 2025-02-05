<?php

class MG_SalesReport extends ShoppReportFramework implements ShoppReport {

	var $periods = true;

	function setup () {
		$this->setchart(array(
			'yaxis' => array('tickFormatter' => 'asMoney')
		));
		$this->chartseries( Shopp::__('Total'), array('column' => 'total') );
	}

	function query () {
		extract($this->options, EXTR_SKIP);

		$where = array();

		$where[] = "o.created BETWEEN '" . sDB::mkdatetime($starts) . "' AND '" . sDB::mkdatetime($ends) . "'";
		$where[] = "o.txnstatus IN ('authed', 'captured', 'CHARGED')";

		$where = join(" AND ",$where);

		$id = $this->timecolumn('o.created');
		$orders_table = ShoppDatabaseObject::tablename('purchase');
		$purchased_table = ShoppDatabaseObject::tablename('purchased');

		$query = "SELECT CONCAT($id) AS id,
							UNIX_TIMESTAMP(o.created) AS period,
							COUNT(DISTINCT o.id) AS orders,
							SUM(o.subtotal) AS subtotal,
							SUM(o.tax) AS tax,
							SUM(o.freight) AS shipping,
							SUM(o.discount) AS discounts,
							SUM(o.total) AS total,
							AVG(o.total) AS orderavg,
							SUM( (SELECT SUM(p.quantity) FROM $purchased_table AS p WHERE o.id = p.purchase) ) AS items,
							(SELECT AVG(p.unitprice) FROM $purchased_table AS p WHERE o.id = p.purchase) AS itemavg
					FROM $orders_table AS o
					WHERE $where
					GROUP BY CONCAT($id)";

		return $query;

	}

	function columns () {
		return array(
			'period'	   => Shopp::__('Period'),
			'orders'	   => Shopp::__('Orders'),
			'items'	       => Shopp::__('Items'),
			'subtotal'	   => Shopp::__('Subtotal'),
			'tax'	       => Shopp::__('Tax'),
			'shipping'	   => Shopp::__('Shipping'),
			'discounts'	   => Shopp::__('Discounts'),
			'total'	       => Shopp::__('Total'),
			'orderavg'	   => Shopp::__('Average Order'),
			'itemavg'	   => Shopp::__('Average Items'),
			'itemcountavg' => Shopp::__('Average No. of Items')
		);
	}

	function scores () {
		return array(
			Shopp::__('Total') => money( isset($this->totals->total) ? $this->totals->total : 0 ),
			Shopp::__('Orders') => intval( isset($this->totals->orders) ? $this->totals->orders : 0 ),
			Shopp::__('Average Order') => money( isset($this->totals->total) && isset($this->totals->orders) ? $this->totals->total/$this->totals->orders : 0)
		);
	}

	static function orders ($data) {
		return intval( isset($data->orders) ? $data->orders : 0);
	}

	static function items ($data) {
		return intval( isset($data->items) ? $data->items : 0);
	}

	static function subtotal ($data) {
		return money( isset($data->subtotal) ? $data->subtotal : 0 );
	}

	static function tax ($data) {
		return money( isset($data->tax) ? $data->tax : 0 );
	}

	static function shipping ($data) {
		return money( isset($data->shipping) ? $data->shipping : 0 );
	}

	static function discounts ($data) {
		return money( isset($data->discounts) ? $data->discounts : 0 );
	}

	static function total ($data) {
		return money( isset($data->total) ? $data->total : 0 );
	}

	static function orderavg ($data) {
		return money( isset($data->orderavg) ? $data->orderavg : 0 );
	}

	static function itemavg ($data) {
		return money( isset($data->itemavg) ? $data->itemavg : 0 );
	}

	function itemcountavg( $data ) {
		if ( $data->items < $data->orders ) return 0;
		if ( ! isset($data->items) || ! isset($data->orders) ) return 0;
		if ( ! is_numeric($data->items) || ! is_numeric($data->orders) ) return 0;

		return round( $data->items / $data->orders, 2 );
	}

}
