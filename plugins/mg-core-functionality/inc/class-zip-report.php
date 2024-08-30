<?php
class MG_LocationsZipReport extends ShoppReportFramework implements ShoppReport {

	function setup () {
		$this->Chart = false;
	}

	public function chart () {

	}

	function query () {
		$this->options = array_merge(array( // Define default URL query parameters
			'orderby' => 'orders',
			'order' => 'desc'
		), $this->options);
		extract($this->options, EXTR_SKIP);

		$state_filter = empty($_GET['state']) ? false : $_GET['state'];

		$where = array();

		$where[] = "o.created BETWEEN '" . sDB::mkdatetime($starts) . "' AND '" . sDB::mkdatetime($ends) . "'";
		$where[] = "o.txnstatus IN ('authed', 'captured', 'CHARGED')";

		if ( false !== $state_filter ) {
			$where[] = "COALESCE(NULLIF(o.state, ''),NULLIF(o.shipstate, '')) = '$state_filter'";
		}

		$where = join(" AND ",$where);

		if ( ! in_array( $order, array('asc', 'desc') ) ) $order = 'desc';
		if ( ! in_array( strtolower($orderby), array('orders', 'items', 'grossed') ) ) $orderby = 'orders';
		$ordercols = "$orderby $order";

		$orders_table = ShoppDatabaseObject::tablename('purchase');
		$purchased_table = ShoppDatabaseObject::tablename('purchased');

		$query = "SELECT CONCAT( COALESCE(NULLIF(o.postcode, ''),NULLIF(o.shippostcode, '')) ) AS id,
							COALESCE(NULLIF(o.postcode, ''),NULLIF(o.shippostcode, '')) AS zip,
							COUNT(DISTINCT o.id) AS orders,
							SUM( (SELECT SUM(p.quantity) FROM $purchased_table AS p WHERE o.id = p.purchase) ) AS items,
							SUM(o.subtotal) AS grossed
					FROM $orders_table AS o
					WHERE $where AND COALESCE(NULLIF(o.postcode, ''),NULLIF(o.shippostcode, '')) <> ''
					GROUP BY zip ORDER BY $ordercols";

		return $query;
	}

	function filters () {
		ShoppReportFramework::rangefilter();
		ShoppReportFramework::filterbutton();
	}

	function columns () {
		return array(
			'zip'   => Shopp::__('State'),
			'orders'	=> Shopp::__('Orders'),
			'items'	    => Shopp::__('Items'),
			'grossed'	=> Shopp::__('Grossed')
		);
	}

	function sortcolumns () {
		return array(
			'zip'   => Shopp::__('State'),
			'orders'	=> Shopp::__('Orders'),
			'items'	    => Shopp::__('Items'),
			'grossed'	=> Shopp::__('Grossed')
		);
	}

	static function zip ($data) {
		return $data->zip;
	}

	static function orders ($data) { return intval($data->orders); }

	static function items ($data) { return intval($data->items); }

	static function grossed ($data) { return money($data->grossed); }

}