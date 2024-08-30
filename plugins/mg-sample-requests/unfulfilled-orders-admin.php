<div class="wrap">
<h2>Unfulfilled Orders</h2>
<?php
global $wpdb, $MG_SampleRequests;
//$orders = $wpdb->get_results("SELECT id, created FROM {$wpdb->prefix}shopp_purchase WHERE id in (SELECT parent FROM {$wpdb->prefix}shopp_meta WHERE context='purchase' AND name='items_sent' AND value='incomplete')");

$orders = $wpdb->get_results("SELECT DISTINCT purchase, o.created FROM {$wpdb->prefix}shopp_purchased pd LEFT JOIN {$wpdb->prefix}shopp_purchase o ON o.id = pd.purchase WHERE pd.id in (SELECT parent FROM {$wpdb->prefix}shopp_meta WHERE context='purchased' AND name='backordered' AND value='backordered')");
?>
<table class="widefat">
<thead>
	<tr>
		<th>Order</th>
		<th>Time Since Order</th>
	</tr>
</thead>
<tfoot>
	<tr>
		<th>Order</th>
		<th>Time Since Order</th>
	</tr>
</tfoot>
<?php if(count($orders) == 0): ?>
	<tr>
		<td>No unfulfilled orders found.</td>
		<td></td>
	</tr>
<?php endif; ?>
<?php foreach($orders as $entry) : ?>
	<?php
	$date = strtotime($entry->created);
	?>
	<tr>
		<td><a href="<?php echo admin_url("admin.php?page=shopp-orders&id={$entry->purchase}"); ?>" target="_blank"><?php echo $entry->purchase; ?></a></td>
		<td><?php echo MG_SampleRequests_Admin::humanTiming($date); ?></td>
	</tr>
<?php endforeach; ?>
</table>