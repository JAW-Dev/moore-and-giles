<?php

/**
 * Class MG_SampleRequests_Frontend
 */
class MG_SampleRequests_Frontend {

	public function __construct() {
		/**
		 * AJAX Endpoints
		 */
		// Article search
		add_action('wp_ajax_article_search', array($this, 'article_search') );

		// Change customer
		add_action('wp_ajax_change_customer', array($this, 'change_customer') );


		// Shortcode
		add_shortcode( 'sample-requests', array($this, 'the_shortcode') );

		// Scripts
		add_action('wp', array($this, 'enqueue_scripts') );
	}

	function article_search() {
		global $wpdb;

		$q = esc_sql($_REQUEST['q'] . "%");

		$query = $wpdb->prepare("SELECT p.post_title as title, pr.label as label, pr.id, p.ID as prod_id FROM {$wpdb->prefix}posts p LEFT JOIN {$wpdb->prefix}shopp_price pr ON pr.product = p.ID  WHERE (post_title LIKE %s OR label LIKE %s) AND pr.type <> 'N/A' AND pr.context = 'variation' AND (pr.inventory = 'off' OR pr.stocked > 0) AND p.post_status = 'publish' ORDER BY title, label ASC", $q, $q);
        $query = str_replace( array($q), array( $wpdb->remove_placeholder_escape($q) ), $query); // WordPress 4.8.3 fix

		$records = sDB::query($query, 'array');

		foreach($records as $index => $r) {
			$image_id = $wpdb->get_var( $wpdb->prepare("SELECT parent FROM {$wpdb->prefix}shopp_meta WHERE context = 'image' AND type='meta' AND name = 'price' AND value = %d AND parent IN (SELECT id FROM {$wpdb->prefix}shopp_meta WHERE context = 'product' AND type = 'image') LIMIT 1", $r->id) );
			$O = shopp_product($r->prod_id);
			$records[$index]->thumb = ShoppStorefrontThemeAPI::image($result, array('id' => $image_id, 'width' => '300', 'quality' => '70', 'height' => '60', 'fit' => 'crop','property' => 'src'), $O);
			unset($O);

			$r->id = $r->id . '|' . $r->prod_id;
		}
		echo json_encode($records);
		exit();
	}

	function change_customer() {

		if ( ! current_user_can('mg_impersonate_user') ) die(0);

		get_currentuserinfo();

		$Order = ShoppOrder();
		$user = wp_get_current_user();

		if ( $_REQUEST['customer'] == "new" ) {

			$Order->data['_intended_recipient'] = $_REQUEST['recipient'];

			unset(ShoppShopping()->data->BillingAddress);
			unset(ShoppShopping()->data->ShippingAddress);
			Shopping::restore('BillingAddress', new BillingAddress );
			Shopping::restore('ShippingAddress', new ShippingAddress );

			exit();

		} else if ( $_REQUEST['customer'] == "agent" ) {

			if  ( ! isset($user->ID) ) die(0);

			$Order->data['_intended_recipient'] = $_REQUEST['recipient'];

			$Account = shopp_customer($user->ID, 'wpuser');

			// Load the billing address
			$Order->Billing->load($Account->id, 'customer');
			$clearfields = array('card', 'cardexpires', 'cardholder', 'cardtype');
			foreach ( $clearfields as $field )
				$Order->Billing->$field = '';

			// Load the shipping address
			$Order->Shipping->load($Account->id, 'customer');
			if ( empty($Order->Shipping->id) )
				$Order->Shipping->copydata($Order->Billing);

			$customer_id = $Account->id;

		} else {

			$Order->data['_intended_recipient'] = $_REQUEST['recipient'];

			$customer_id = absint($_REQUEST['customer']);
			$Account = shopp_customer($customer_id);

			$Order->Billing = new BillingAddress();
			$Order->Shipping = new ShippingAddress();

			// Load the billing address
			$Order->Billing->load($Account->id, 'customer');
			$clearfields = array('card', 'cardexpires', 'cardholder', 'cardtype');
			foreach ( $clearfields as $field )
				$Order->Billing->$field = '';

			// Load the shipping address
			$Order->Shipping->load($Account->id, 'customer');
			if ( empty($Order->Shipping->id) )
				$Order->Shipping->copydata($Order->Billing);
		}

		$Order->data['_selected_customer_address'] = $customer_id;

		$return = array();

		$return['phone'] = $Account->phone;
		$return['email'] = $Account->email;
		$return['company'] = $Account->company;
		$return['shippingname'] = empty($Order->Shipping->name) ? $Account->firstname . " " . $Account->lastname : $Order->Shipping->name;
		$return['shippingaddress'] = $Order->Shipping->address;
		$return['shippingxaddress'] = $Order->Shipping->xaddress;
		$return['shippingcity'] = $Order->Shipping->city;
		$return['shippingstate'] = $Order->Shipping->state;
		$return['shippingcountry'] = $Order->Shipping->country;
		$return['shippingpostcode'] = $Order->Shipping->postcode;

		$Order->data['Shipping Phone'] = $return['phone'];
		$Order->data['Recipient Company'] = $return['company'];
		$Order->data['Recipient Email'] = $return['email'];

		echo json_encode($return);
		exit();
	}

	function the_shortcode ( $atts ) {
		extract( shortcode_atts( array(
			'template' => 'default.php',
		), $atts ) );

		ob_start();
		global $MG_SampleRequests;

		include(MG_SR_PATH . "/templates/$template");

		return ob_get_clean();
	}

	function enqueue_scripts() {
		global $post;

		if ( ! has_shortcode($post->post_content, 'sample-requests') ) return;

		if ( ! wp_script_is('select2-4') ) {
			wp_enqueue_style( 'select2-4-css', plugins_url('/lib/select2/select2.min.css', MG_SR_FILE), false, '1.0.0' );
			wp_register_script( 'select2-4', plugins_url('/lib/select2/select2.min.js', MG_SR_FILE), array('jquery'), '1.0.0');
			wp_enqueue_script( 'select2-4');
		}

		shopp_enqueue_script('address');

		wp_enqueue_style( 'sample-requests-css', plugins_url('/css/sample-requests.css', MG_SR_FILE), false, '1.0.4' );
		wp_enqueue_script( 'sample-requests-js', plugins_url('/js/sample-requests.js', MG_SR_FILE), array('jquery','select2-4'), '1.0.9', true);

		wp_localize_script('sample-requests-js', 'SRHelper', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'row_html' => $this->get_row(), 'custom_row_html' => $this->get_custom_row(), 'box_row_html' => $this->get_box_row(), 'custom_order_row_html' => $this->get_custom_order_row() ) );
	}

	public static function row( $row = 'row_number', $article_id = '', $article_label = '', $size = "3x5", $quantity = 1 ) {
		?>
		<div class="row article-row" data-row="<?php echo $row; ?>">
			<div class="one-third first">
				<select class="article-selector" data-default-value="<?php echo $article_id; ?>" data-default-label="<?php echo $article_label; ?>" name="article_line[<?php echo $row; ?>][price_id]">
					<option></option>
				</select>
			</div>
			<div class="one-third size">
				<label><input type="radio" class="size-selector" name="article_line[<?php echo $row; ?>][size]" value="3x5" <?php if ($size == "3x5") echo "checked"; ?>> 3x5</label>
				<label><input type="radio" class="size-selector" name="article_line[<?php echo $row; ?>][size]" value="8x5" <?php if ($size == "8x5") echo "checked"; ?>> 8x5</label>
				<label><input type="radio" class="size-selector" name="article_line[<?php echo $row; ?>][size]" value="Set (3x5)" <?php if ($size == "Set (3x5)") echo "checked"; ?>> Set (3x5)</label>
				<label><input type="radio" class="size-selector" name="article_line[<?php echo $row; ?>][size]" value="Ringset (8x5)" <?php if ($size == "Ringset (8x5)") echo "checked"; ?>> Ringset (8x5)</label>
				<label><input type="radio" name="article_line[<?php echo $row; ?>][size]" class="size-selector other-selector"> Other <input type="text" class="size-other" name="article_line[<?php echo $row; ?>][size]" disabled="disabled" /></label>
			</div>
			<div class="one-sixth">
				<input type="number" class="quantity-selector" name="article_line[<?php echo $row; ?>][quantity]" value="<?php echo $quantity; ?>" />
			</div>
			<div class="one-sixth">
				<a class="remove-row"><i class="fa fa-times-circle"></i></a>
			</div>
		</div>
		<?php
	}

	public function custom_row( $row = 'row_number' ) {
		?>
		<div class="custom-row" data-row="<?php echo $row; ?>">
			<div class="one-sixth first">
				<input name="custom_article_line[<?php echo $row; ?>][name]" class="custom-article-name" type="text" />
			</div>
			<div class="one-sixth">
				<input name="custom_article_line[<?php echo $row; ?>][label]" type="text" />
			</div>
			<div class="one-third size">
				<label><input type="radio" class="size-selector" name="custom_article_line[<?php echo $row; ?>][size]" value="3x5" checked> 3x5</label>
				<label><input type="radio" class="size-selector" name="custom_article_line[<?php echo $row; ?>][size]" value="8x5"> 8x5</label>
				<label><input type="radio" class="size-selector" name="custom_article_line[<?php echo $row; ?>][size]" value="Set (3x5)"> Set (3x5)</label>
				<label><input type="radio" class="size-selector" name="custom_article_line[<?php echo $row; ?>][size]" value="Ringset (8x5)"> Ringset (8x5)</label>
				<label><input type="radio" name="custom_article_line[<?php echo $row; ?>][size]" class="size-selector other-selector"> Other <input type="text" class="size-other" name="custom_article_line[<?php echo $row; ?>][size]" disabled="disabled" /></label>
			</div>
			<div class="one-sixth">
				<input type="number" name="custom_article_line[<?php echo $row; ?>][quantity]" value="1" />
			</div>
			<div class="one-sixth">
				<a class="remove-custom-row"><i class="fa fa-times-circle"></i></a>
			</div>
		</div>
		<?php
	}

	public function box_row( $row = 'row_number' ) {
		?>
		<div class="box-row" data-row="<?php echo $row; ?>">
			<div class="one-sixth first">
				<select name="box_line[<?php echo $row; ?>][name]" class="box-name">
					<option>220 – Partial</option>
					<option>220 – Custom</option>
					<option>220 – Standard</option>
					<option>440 – Standard</option>
					<option>880 – Standard</option>
					<option>Aviation Box</option>
				</select>
			</div>
			<div class="one-sixth">
				<input type="number" name="box_line[<?php echo $row; ?>][quantity]" value="1" />
			</div>
			<div class="one-sixth">
				<a class="remove-box-row"><i class="fa fa-times-circle"></i></a>
			</div>
		</div>
		<?php
	}

	function custom_order_row( $row = 'row_number' ) {
		?>
		<div class="custom-order-row" data-row="<?php echo $row; ?>">
			<div class="one-sixth first">
				<input name="custom_order_line[<?php echo $row; ?>][pattern]" class="custom-article-pattern" type="text" />
			</div>
			<div class="one-third">
				<input name="custom_order_line[<?php echo $row; ?>][name]" class="custom-article-name" type="text" />
			</div>
			<div class="one-sixth">
				<select class="tipping-selector" name="custom_order_line[<?php echo $row; ?>][tipping]">
					<option>No Tipping</option>
					<option>Light</option>
					<option>Medium</option>
					<option>Heavy</option>
				</select>
			</div>
			<div class="one-sixth">
				<input type="number" name="custom_order_line[<?php echo $row; ?>][quantity]" value="1" />
			</div>
			<div class="one-sixth">
				<a class="remove-custom-order-row"><i class="fa fa-times-circle"></i></a>
			</div>
		</div>
		<?php
	}

	function get_row() {
		ob_start();
		$this->row();

		return ob_get_clean();
	}

	function get_custom_row() {
		ob_start();
		$this->custom_row();

		return ob_get_clean();
	}

	function get_box_row() {
		ob_start();
		$this->box_row();

		return ob_get_clean();
	}

	function get_custom_order_row() {
		ob_start();
		$this->custom_order_row();

		return ob_get_clean();
	}
}
