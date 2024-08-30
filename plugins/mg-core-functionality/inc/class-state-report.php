<?php
class MG_LocationsStateReport extends ShoppReportFramework implements ShoppReport {

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

		$country_filter = empty($_GET['country']) ? false : $_GET['country'];

		$where = array();

		$where[] = "o.created BETWEEN '" . sDB::mkdatetime($starts) . "' AND '" . sDB::mkdatetime($ends) . "'";
		$where[] = "o.txnstatus IN ('authed', 'captured', 'CHARGED')";

		if ( false !== $country_filter ) {
			$where[] = "o.country = '$country_filter'";
		}

		$where = join(" AND ",$where);

		if ( ! in_array( $order, array('asc', 'desc') ) ) $order = 'desc';
		if ( ! in_array( strtolower($orderby), array('orders', 'items', 'grossed','zstate') ) ) $orderby = 'orders';
		$ordercols = "$orderby $order";

		$orders_table = ShoppDatabaseObject::tablename('purchase');
		$purchased_table = ShoppDatabaseObject::tablename('purchased');

		$query = "SELECT CONCAT( COALESCE(NULLIF(o.state, ''),NULLIF(o.shipstate, '')) ) AS id,
							COALESCE(NULLIF(o.state, ''),NULLIF(o.shipstate, '')) AS zstate,
							COUNT(DISTINCT o.id) AS orders,
							SUM( (SELECT SUM(p.quantity) FROM $purchased_table AS p WHERE o.id = p.purchase) ) AS items,
							SUM(o.subtotal) AS grossed
					FROM $orders_table AS o
					WHERE $where AND COALESCE(NULLIF(o.state, ''),NULLIF(o.shipstate, '')) <> ''
					GROUP BY zstate ORDER BY $ordercols";

		return $query;
	}

	function filters () {
		ShoppReportFramework::rangefilter();
		ShoppReportFramework::filterbutton();
	}

	function columns () {
		return array(
			'zstate'   => Shopp::__('State'),
			'orders'	=> Shopp::__('Orders'),
			'items'	    => Shopp::__('Items'),
			'grossed'	=> Shopp::__('Grossed')
		);
	}

	function sortcolumns () {
		return array(
			'zstate'   => Shopp::__('zstate'),
			'orders'	=> Shopp::__('Orders'),
			'items'	    => Shopp::__('Items'),
			'grossed'	=> Shopp::__('Grossed')
		);
	}

	static function zstate ($data) {
		$country_filter = empty($_GET['country']) ? false : $_GET['country'];
		$country_zones = Lookup::country_zones();

		$zones = $country_zones[ $country_filter ];

		if ( isset($zones[ $data->zstate ]) ) {
			return sprintf('<a href="%s">%s</a>', add_query_arg( array('report' => 'locations_zip', 'state' => $data->zstate ) ), $zones[ $data->zstate ] );
		}

		return $data->zstate;
	}

	static function orders ($data) { return intval($data->orders); }

	static function items ($data) { return intval($data->items); }

	static function grossed ($data) { return money($data->grossed); }

}