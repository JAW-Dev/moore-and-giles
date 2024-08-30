<style>
ul#report li {
	float: left;
	margin-right: 20px;
}
</style>
<div class="wrap">
<h2>Order Data Search</h2>
<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<label>Field: <input type="text" name="field" value="<?php echo $_REQUEST['field']; ?>" /></label>&nbsp;
	<label>Field Value: <input type="text" name="value" value="<?php echo $_REQUEST['value']; ?>" /></label>
	<input type="hidden" name="action" value="search" />
	<input class="button-primary" type="submit" value="View Orders" />
	<p>Common Fields: Catalog Source Code, Source</p>
</form>
<?php if($_REQUEST['action'] == "search"): ?>
	<?php
	$field = $_REQUEST['field'];
	$value = $_REQUEST['value'];

	if(empty($field) || empty($value)) {
		echo "Empty values provided. Please try again.";
		return;
	}

	global $wpdb;
	$query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}shopp_purchase WHERE id IN (SELECT parent FROM {$wpdb->prefix}shopp_meta WHERE name = %s AND value = %s AND context = 'purchase' AND (type = 'meta' OR type = 'order-data') )", $field, $value);
	$results = DB::query($query,'array','index','id');

	$ordercount_query = $wpdb->prepare("SELECT count(*) as total,SUM(IF(txnstatus IN ('authed','captured'),total,NULL)) AS sales,AVG(IF(txnstatus IN ('authed','captured'),total,NULL)) AS avgsale FROM {$wpdb->prefix}shopp_purchase WHERE id IN (SELECT parent FROM {$wpdb->prefix}shopp_meta WHERE name = %s AND value = %s AND context = 'purchase' AND (type = 'meta' OR type = 'order-data')", $field, $value);
	//echo $ordercount_query;

	$ordercount = DB::query($ordercount_query, 'object');
	?>
	<h3>Orders where <?php echo $field; ?> is equal to <?php echo $value; ?></h3>

	<?php if (current_user_can('shopp_financials')): ?>
	<ul id="report">
		<li><strong><?php echo $ordercount->total; ?></strong> <span><?php _e('Orders','Shopp'); ?></span></li>
		<li><strong><?php echo money($ordercount->sales); ?></strong> <span><?php _e('Total Sales','Shopp'); ?></span></li>
		<li><strong><?php echo money($ordercount->avgsale); ?></strong> <span><?php _e('Average Sale','Shopp'); ?></span></li>
	</ul>
	<?php endif; ?>

	<table class="widefat">
	<thead>
		<tr>
			<th>Order</th>
			<th>Field</th>
			<th>Value</th>
			<th>Name</th>
			<th>Destination</th>
			<th>Transaction</th>
			<th>Date</th>
			<th>Total</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th>Order</th>
			<th>Field</th>
			<th>Value</th>
			<th>Name</th>
			<th>Destination</th>
			<th>Transaction</th>
			<th>Date</th>
			<th>Total</th>
		</tr>
	</tfoot>
	<?php foreach($results as $Order) : ?>
		<?php
		$url = add_query_arg('page','shopp-orders', admin_url('admin.php') );
		$viewurl = add_query_arg('id',$Order->id,$url);
		$classes = array();

		$viewurl = add_query_arg('id',$Order->id,$url);
		$customer = '' == trim($Order->firstname.$Order->lastname) ? "(".__('no contact name','Shopp').")" : ucfirst("{$Order->firstname} {$Order->lastname}");
		$customerurl = add_query_arg('customer',$Order->customer,$url);

		$txnstatus = isset($txnstatus_labels[$Order->txnstatus]) ? $txnstatus_labels[$Order->txnstatus] : $Order->txnstatus;
		$classes[] = strtolower(preg_replace('/[^\w]/','_',$Order->txnstatus));
		$gateway = $Gateways[$Order->gateway]->name;


		$addrfields = array('city','state','country');
		$format = '%3$s, %2$s &mdash; %1$s';
		if (empty($Order->shipaddress))
			$location = sprintf($format,$Order->country,$Order->state,$Order->city);
		else $location = sprintf($format,$Order->shipcountry,$Order->shipstate,$Order->shipcity);

		$location = ltrim($location,' ,');
		if (0 === strpos($location,'&mdash;'))
			$location = str_replace('&mdash; ','',$location);
		$location = str_replace(',  &mdash;',' &mdash;',$location);

		if (!$even) $classes[] = "alternate";
		do_action_ref_array('shopp_order_row_css',array(&$classes,&$Order));
		$even = !$even;
		?>
		<tr class="<?php echo join(' ',$classes); ?>">
			<td><a class='row-title' target="_blank" href='<?php echo esc_url($viewurl); ?>' title='<?php printf(__('View Order #%d','Shopp'),$Order->id); ?>'><?php printf(__('Order #%d','Shopp'),$Order->id); ?></a></td>
			<td><?php echo $field; ?></td>
			<td><?php echo $value; ?></td>
			<td class="name column-name"><a href="<?php echo esc_url($customerurl); ?>"><?php echo esc_html($customer); ?></a><?php echo !empty($Order->company)?"<br />".esc_html($Order->company):""; ?></td>
			<td class="destination column-destination<?php echo in_array('destination',$hidden)?' hidden':''; ?>"><?php echo esc_html($location); ?></td>
			<td class="txn column-txn<?php echo in_array('txn',$hidden)?' hidden':''; ?>"><?php echo $Order->txnid; ?><br /><?php echo esc_html($gateway); ?></td>
			<td class="date column-date<?php echo in_array('date',$hidden)?' hidden':''; ?>"><?php echo date("Y/m/d",mktimestamp($Order->created)); ?><br />
				<strong><?php echo $statusLabels[$Order->status]; ?></strong></td>
			<td class="total column-total<?php echo in_array('total',$hidden)?' hidden':''; ?>"><?php echo money($Order->total); ?><br /><span class="status"><?php echo $txnstatus; ?></span></td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>
