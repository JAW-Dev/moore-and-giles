<?php

class MG_SampleRequests_Admin {

	public function __construct() {
		if ( ! current_user_can('shopp_orders') ) return;

		// Admin AJAX
		add_action('wp_ajax_update_backordered', array($this, 'update_backordered') );
		add_action('wp_ajax_update_picked', array($this, 'update_picked') );

		// Scripts
		add_action( 'admin_enqueue_scripts', array($this, 'admin_scripts') );

		// Add Picked / Backorder Columns
		add_filter('manage_toplevel_page_shopp-orders_columns', array($this, 'add_cols') );
		add_action('shopp_manage_order_cb_column', array($this, 'picked_data'), 10, 4);
		add_action('shopp_manage_order_backordered_column_data', array($this, 'backordered_data'), 10, 3);

		// Admin menu
		add_action('admin_menu', array($this, 'handle_admin_menu'), 100);

		// Set shipped by user
		add_action('shopp_shipped_order_event', array($this, 'set_shipped_by_user'), 100, 1);

		// Send customer shipped and note event notices
		add_filter('shopp_shipped_order_event_emails', array($this, 'send_additional_event_notices'), 100, 2 );
		add_filter('shopp_note_order_event_emails', array($this, 'send_additional_event_notices'), 100, 2 );

		// Unfulfilled Order Reminders
		add_action( 'do_unfulfilled_reminders', array($this, 'do_reminders') );

		// Reports
		add_action('shopp_init', array($this, 'load_report_classes') );
		add_filter('shopp_reports', array($this, 'add_reports') );
	}

	function update_backordered() {
		$order = (int)$_REQUEST['purchase'];
		$purchased = $_REQUEST['purchased'];
		$backordered_status = $_REQUEST['backordered_status'];
		$all_items_backordered = Shopp::str_true( $_REQUEST['all_items_backordered'] );
		$Purchase = shopp_order($order);

		// crude safety check
		if ( ! in_array($backordered_status, array('backordered','unbackordered')) ) {
			die(0);
		}

		if ( $all_items_backordered ) {
			// Backordered Status
			$Purchase->status = 5;
		}

		$Purchase->save();

		shopp_set_meta($purchased, 'purchased', 'backordered', $backordered_status);

		echo 1;
		exit();
	}

	function update_picked() {
		$purchased = (int) $_REQUEST['purchased'];
		$status = $_REQUEST['picked_status'];
		$order = (int)$_REQUEST['purchase'];
		$finished = Shopp::str_true( $_REQUEST['finished_picking'] );

		if ( $status == "picked" ) {
			shopp_set_meta($purchased, 'purchased', 'picked', get_current_user_id() );
			shopp_set_meta($purchased, 'purchased', 'backordered', 'unbackordered' ); // unbackorder it
		} else {
			shopp_rmv_meta($purchased, 'purchased', 'picked');
		}

		$Purchase = shopp_order($order);

		if ( $finished ) {
			// Ready to Ship
			$Purchase->status = 2;
		} else {
			// In Progress
			$Purchase->status = 1;
		}

		$Purchase->save();

		echo 1;
		exit();
	}

	function admin_scripts() {
		if ( $_GET['page'] == "shopp-orders" ) {
			wp_enqueue_style( 'sample-requests-admin', plugins_url('/css/sample-requests-admin.css', MG_SR_FILE) );
			wp_enqueue_script( 'sample-requests-admin-js', plugins_url('/js/sample-requests-admin.js', MG_SR_FILE), array('jquery'), '1.1.0' );
		}
	}

	function add_cols($headers) {
		if ( ! isset($_GET['id']) ) return $headers;

		$headers = array('cb' => '<input type="checkbox" />Picked') + (array)$headers;
		$headers = (array)$headers + array('backordered' => 'Backordered');

		return $headers;
	}

	function picked_data($result, $Product, $Item, $Purchase) {
		$disabled = false;
		$picked = shopp_meta($Item->id, 'purchased', 'picked');
		$picked = (! empty($picked));
		if ( $picked && $Purchase->shipped ) $disabled = true; // don't show pick boxes if shipped

		?>
		<th scope='row' class='<?php if ( ! $disabled ): ?>check-column<?php endif; ?>' style="padding: 11px 0 0 3px; vertical-align:top;"><input type="checkbox" class="picked-check" value="<?php echo $Item->id; ?>" data-purchase="<?php echo (int)$_GET['id']; ?>" <?php if($picked) echo "checked"; ?> <?php if ($disabled) echo 'disabled'; ?>></th>
		<?php
	}

	function backordered_data($column, $Product, $Item) {
		$Purchase = shopp_order( (int)$_REQUEST['id'] );

		$picked = shopp_meta($Item->id, 'purchased', 'picked');

		$backordered = shopp_meta($Item->id, 'purchased', 'backordered');
		$backordered = ($backordered == 'backordered');

		if ( ! empty($picked) && ! $backordered) return; // if picked, don't allow it to be backordered
		?>
		<input type="checkbox" class="backordered-check" value="<?php echo $Item->id; ?>" data-purchase="<?php echo (int)$_GET['id']; ?>" <?php if($backordered) echo "checked"; ?>>
		<?php
	}

	function handle_admin_menu() {
		add_submenu_page( "shopp-orders", "Unfulfilled Orders", "Unfulfilled Orders", "shopp_orders", "unfulfilled-orders", array($this, 'unfulfilled_orders_admin') );
	}

	function unfulfilled_orders_admin() {
		include(MG_SR_PATH . '/unfulfilled-orders-admin.php');
	}

	function set_shipped_by_user ( $e ) {
		$user = wp_get_current_user();

		shopp_set_meta($e->order, 'purchase', 'shipped_by', $user->ID, 'event');
	}

	function send_additional_event_notices ( $messages, $Event ) {

		$Purchase = $Event->order();

		if ( isset($Purchase->data['Recipient Email']) ) {

			$recipient_emails = str_replace(' ', '', $Purchase->data['Recipient Email']);

			if ( strpos($recipient_emails, ',') !== false ) {
				$recipient_emails = explode(',', $recipient_emails);
			}

			foreach( (array)$recipient_emails as $email ) {
				$messages[$email] = $messages['customer'];

				$messages[$email][0] = $Purchase->shipname;
				$messages[$email][1] = $email;
			}
		}

		return $messages;
	}

	function do_reminders() {
		if ( stripos(get_site_url(), 'www.mooreandgiles.com') === false ) return; // never run on dev environments

		global $wpdb;

		$html_list = "";
		$html_backordered_list = "";
		$html_pending_list = "";

		// Orders at least 7 days old that are still in progress
		// Status 1 is In Progress
		$orders = $wpdb->get_results("SELECT DISTINCT purchase, o.created FROM {$wpdb->prefix}shopp_purchased pd LEFT JOIN {$wpdb->prefix}shopp_purchase o ON o.id = pd.purchase WHERE DATE_SUB(CURDATE(),INTERVAL 7 DAY) >= o.created AND pd.id NOT IN (SELECT parent FROM {$wpdb->prefix}shopp_meta WHERE context='purchased' AND name='backordered' AND value='backordered') AND o.status = 1");

		if(count($orders) == 0) return;

		foreach($orders as $order) {
			$html_list .= "<a href='http://www.mooreandgiles.com/leather/wp-admin/admin.php?page=shopp-orders&id={$order->purchase}'>Order $order->purchase</a><br />";
		}


		// Orders at least 7 days old, with backordered items
		// Status 1 is In Progress
		$orders = $wpdb->get_results("SELECT DISTINCT purchase, o.created FROM {$wpdb->prefix}shopp_purchased pd LEFT JOIN {$wpdb->prefix}shopp_purchase o ON o.id = pd.purchase WHERE DATE_SUB(CURDATE(),INTERVAL 7 DAY) >= o.created AND pd.id in (SELECT parent FROM {$wpdb->prefix}shopp_meta WHERE context='purchased' AND name='backordered' AND value='backordered') AND o.status = 1");

		if(count($orders) == 0) return;

		foreach($orders as $order) {
			$html_backordered_list .= "<a href='http://www.mooreandgiles.com/leather/wp-admin/admin.php?page=shopp-orders&id={$order->purchase}'>Order $order->purchase</a><br />";
		}


		// Orders at least 7 days old that haven't been started
		// Status 0 is Pending
		$orders = $wpdb->get_results("SELECT DISTINCT purchase, o.created FROM {$wpdb->prefix}shopp_purchased pd LEFT JOIN {$wpdb->prefix}shopp_purchase o ON o.id = pd.purchase WHERE DATE_SUB(CURDATE(),INTERVAL 3 DAY) >= o.created AND pd.id NOT IN (SELECT parent FROM {$wpdb->prefix}shopp_meta WHERE context='purchased' AND name='backordered' AND value='backordered') AND o.status = 0");

		if(count($orders) == 0) return;

		foreach($orders as $order) {
			$html_pending_list .= "<a href='http://www.mooreandgiles.com/leather/wp-admin/admin.php?page=shopp-orders&id={$order->purchase}'>Order $order->purchase</a><br />";
		}


		$this->send_reminders("samples@mooreandgiles.com", $html_list, $html_backordered_list, $html_pending_list);
	}

	function send_reminders($email, $html_list, $html_backordered_list, $html_pending_list)
	{
		global $wpdb;

		// Send the e-mail notification
		$addressee = "";
		$address = "$email";

		$email = array();
		$email['from'] = '"'.get_bloginfo("name").'"';
		$email['from'] .= ' <samples@mooreandgiles.com>';
		$email['to'] = '"'.html_entity_decode($addressee,ENT_QUOTES).'" <'.$address.'>';
		$email['subject'] = __("Unfulfilled Orders Report",'Shopp');

		$email['message'] = "
		<table style='border: none'>
			<tr>
				<td>
					<p>Greetings,<p>
					<p><strong>The following pending orders were placed 7 days ago or more:</strong></p>
					<p style='margin-bottom:30px;'>$html_list</p>

					<p><strong>The following pending orders have were placed 7 days ago or more and have backordered items:</strong></p>
					<p style='margin-bottom:30px;'>$html_backordered_list</p>

					<p><strong>The following pending orders have were placed 3 days ago or more and haven't been started:</strong></p>
					<p style='margin-bottom:30px;'>$html_pending_list</p>
				</td>
			</tr>
		</table>";

		$template = dirname(__FILE__) . "/template/unfulfilled_reminder.php";
		$GLOBALS['u_reminder_email_data'] = $email;
		Shopp::email($template,$email);
		unset($GLOBALS['u_reminder_email_data']);
	}

	function humanTiming ($time) {
		$time = time() - $time; // to get the time since that moment

		$tokens = array (
			31536000 => 'year',
			2592000 => 'month',
			604800 => 'week',
			86400 => 'day',
			3600 => 'hour',
			60 => 'minute',
			1 => 'second');

		foreach ($tokens as $unit => $text) {
			if ($time < $unit) continue;
			$numberOfUnits = floor($time / $unit);
			return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
		}
	}

	function load_report_classes() {
		require( 'reports/sampling-report.php' );
		require( 'reports/shipping-report.php' );
		require( 'reports/grouped-products-report.php' );
		require( 'reports/osr-grouped-products-report.php' );
		require( 'reports/rep-sampling-report.php' );
		require( 'reports/rep-product-report.php' );
		require( 'reports/division-report.php');
	}

	function add_reports($reports) {
	    global $wpdb;

		$reports['sampling'] = array( 'class' => 'SamplingReport', 'name' => __('Sampling','Shopp'), 'label' => __('Sampling','Shopp') );
		$reports['rep_sampling'] = array( 'class' => 'RepSamplingReport', 'name' => __('Sampling by Representative','Shopp'), 'label' => __('Sampling by Representative','Shopp') );
		$reports['shipping_methods'] = array( 'class' => 'ShippingMethodsReport', 'name' => __('Shipping Methods','Shopp'), 'label' => __('Shipping Methods','Shopp') );
		$reports['top_products'] = array( 'class' => 'TopProductsReport', 'name' => __('Top Grouped Products','Shopp'), 'label' => __('Top Grouped Products','Shopp') );
		$reports['osr_top_products'] = array( 'class' => 'OSRTopProductsReport', 'name' => __('Top Grouped Products (OSR)','Shopp'), 'label' => __('Top Grouped Products (OSR)','Shopp') );
		$reports['osr_division'] = array( 'class' => 'MG_DivisionReport', 'name' => __('Division Sample Requests (OSR)','Shopp'), 'label' => __('Division Sample Requests (OSR)','Shopp') );

		if ( ! empty($_GET['rep_customer_id']) ) {
		    $rep_user_id = $wpdb->get_var( $wpdb->prepare("SELECT wpuser FROM {$wpdb->prefix}shopp_customer WHERE id = %d", $_GET['rep_customer_id']) );
			$rep_username = $wpdb->get_var( $wpdb->prepare("SELECT display_name FROM {$wpdb->users} WHERE ID = %d", $rep_user_id) );

			$reports['rep_products'] = array( 'class' => 'MG_RepProductsReport', 'name' => __('Products by Rep - ' . $rep_username,'Shopp'), 'label' => __('Products by Rep - ' . $rep_username,'Shopp') );
        }

        // Disable some defaults
        unset($reports['discounts']);
		unset($reports['paytype']);
		unset($reports['customers']);
		unset($reports['tax']);

		return $reports;
	}
}
