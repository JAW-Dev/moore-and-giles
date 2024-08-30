<?php
require_once('Iglobal.php');

class IgWC
{
	protected $store;
	protected $key;
	/* @var Iglobal $ig */
	protected $ig;
	protected $test_orders;
	protected $usingPromoCode = false;
	protected $promocodeAmount = 0;

	function __construct($store, $key)
	{
		$this->store = $store;
		$this->key = $key;
		$this->ig = new Iglobal($store, $key);
		$this->test_orders = get_option('iglobal_import_tests');
	}

	public $statusArray = array(
		"IGLOBAL_FRAUD_REVIEW" => "validating",
		"IGLOBAL_ORDER_ON_HOLD" => "on-hold",
		"IGLOBAL_ORDER_CANCELLED" => "cancelled",
		"IGLOBAL_ORDER_IN_PROCESS" => "validated"
	);

	public function zonos_import_orders()
	{
		$startDate = date('Ymd', time() - (7 * 24 * 60 * 60));
		$orders = $this->ig->zonos_orderNumbers(null, $startDate);
		if (property_exists($orders, 'orders')) {
			foreach ($orders->orders as $order_id) {
				$this->zonos_import_order($order_id->id);
			}
		}
	}

	public function zonos_get_product_terms($product, $term) {
		$terms =get_the_terms($product->get_id(), $term);
		if ( is_wp_error( $terms ) || empty( $terms ))
			return null;
		$tags = array();
		foreach ( $terms as $term ) {
			$tags[] = $term->name;
		}
		return join(", ", $tags);
	}

	public function convertToInches($value){
		if($value == null){
			return null;
		}

		$dimensionUnit = get_option('woocommerce_dimension_unit');
		$units = array(
			'm'=> 0.0254,
			'yd' => 0.0277778,
			'mm' => 25.4,
			'cm' => 2.54,
			'in' => 1
		);

		return ($value * $units[$dimensionUnit]);
	}

	public function zonos_create_temp_cart($wcGeneralCart, $returnURL, $user)
	{
		$wcCart = $wcGeneralCart->get_cart();
		$wcCoupons = $wcGeneralCart->coupon_discount_amounts;

		$items = array();
		$weights = array(
			"lbs" => "LB",
			"oz" => "OZ",
			"g" => "G",
			"kg" => "KG"
		);

		foreach ($wcCart as $item => $values) {
			$id = $values['product_id'];
			$variation_id = $values['variation_id'];
			$product = wc_get_product($id);
			$image = $product->get_image();
			$doc = new DOMDocument();
			$doc->loadHTML($image);
			$imageTags = $doc->getElementsByTagName('img');
			foreach ($imageTags as $tag) {
				$image = $tag->getAttribute('src');
			}
			$price = ($values['line_subtotal'] / $values['quantity']);
			//$price = $product->get_price();
			$customization = "";

			$sku = get_post_meta($id, "_sku", true);
			$length = get_post_meta($id, "_length", true);
			$width = get_post_meta($id, "_width", true);
			$height = get_post_meta($id, "_height", true);
			$weight = get_post_meta($id, "_weight", true);

			if ($variation_id != 0) {
				$_product = new WC_Product_Variation($variation_id);
				$variation_data = $_product->get_variation_attributes();
				$customization = wc_get_formatted_variation($variation_data, true);
				//$price = $_product->get_price();
				$sku = $_product->get_sku();
				if($length === "" || ($length != $_product->get_length() && $_product->get_length() !== ""))
					$length = $_product->get_length();
				if($width === "" || ($width != $_product->get_width() && $_product->get_width() !== ""))
					$width = $_product->get_width();
				if($height === "" || ($height != $_product->get_height() && $_product->get_height() !== ""))
					$height = $_product->get_height();
				if($weight === "" || ($weight != $_product->get_weight() && $_product->get_weight() !== ""))
					$weight = $_product->get_weight();
			}

			$length = $this->convertToInches($length);
			$width = $this->convertToInches($width);
			$height = $this->convertToInches($height);

			$discount_price = $values['data'];
			if ($discount_price && $discount_price->get_price()) {
				//$price = $discount_price->get_price();
			}

			$item = array(
				"description" => $product->get_title(),
				"productId" => $id,
				"sku" => $sku,
				"unitPrice" => $price,
				"quantity" => $values['quantity'],
				"length" => $length,
				"width" => $width,
				"height" => $height,
				"weight" => $weight,
				"weightUnits" => $weights[get_option('woocommerce_weight_unit')],
				"itemURL" => get_permalink($values['product_id']),
				"imageURL" => $image,
				"itemCustomization" => $variation_id,
				"itemCategory" => $this->zonos_get_product_terms($product, 'product_cat'),
				"itemBrand" => $this->zonos_get_product_terms($product, 'product_tag')
			);
			if($item['length']) {
				$item['length'] = preg_replace('/[^0-9.]/','', $item['length']);
			}
			if($item['width']) {
				$item['width'] = preg_replace('/[^0-9.]/','', $item['width']);
			}
			if($item['height']) {
				$item['height'] = preg_replace('/[^0-9.]/','', $item['height']);
			}
			if($item['weight']) {
				$item['weight'] = preg_replace('/[^0-9.]/','', $item['weight']);
			}

			$addons = '';

			if (isset($values['addons'])) {
				$addons = " : ";
				$descAddOn = "";
				$first = true;
				foreach ($values['addons'] as $addon) {
					if (!$first) {
						$addons = $addons . "||";
					} else {
						$first = false;
					}
					$descAddOn = $descAddOn . "<li><b>" . $addon['name'] . ":</b> " . $addon['value'] . "</li>";
					$addons = $addons . $addon['name'] . "::" . $addon['value'] . "::" . $addon['price'];
				}
				$item['description'] = $item['description'] . $addons;
				$item['itemDescriptionDetailed'] = $addons;
			}

			if ( isset( $values['product_addons']['addons'] ) ) {
				$desc_add_on = '';
				$first       = true;

				foreach ( $values['product_addons']['addons'] as $addon ) {

					if ( ! empty( $addons ) && $first ) {
						$addons = $addons . ' || ';
					} elseif ( ! $first ) {
						$addons = $addons . ' ||';
					} else {
						$addons = ' : ';
						$first = false;
					}

					$personalization_addons = '';

					if ( $addon['addon_slug'] === 'addon-personalization' ) {
						$desc_add_on           .= '<li><b>' . $addon['addon_label'] . ':</b> ' . $addon['addon_value'] . '</li>';
						$personalization_addons = $addon['addon_label'] . '::' . $addon['addon_value'];
					}

					$gift_wrapping_addons = '';

					if ( $addon['addon_slug'] === 'gift-wrapping' ) {
						$desc_add_on         .= '<li><b>' . $addon['addon_label'] . '</b></li>';
						$gift_wrapping_addons = ' ' . $addon['addon_label'];
					}

					$addons = $addons . $personalization_addons . $gift_wrapping_addons;
				}

				$item['description']             = $item['description'] . $addons;
				$item['itemDescriptionDetailed'] = $addons;
			}

			// Just comment out incase the client wants to go back.
			// if ( isset( $values['addons'] ) || isset( $values['product_addons']['addons'] ) ) {
			// 	$item['description'] .= '';
			// }

			array_push( $items, $item );

		}

		foreach ($wcCoupons as $discountDescription => $discountAmount) {
			$discountAmount = "-" . $discountAmount;
			$discountLineItem = array(
				"description" => $discountDescription,
				"quantity" => "1",
				"unitPrice" => $discountAmount
			);

			array_push($items, $discountLineItem);
		}

		$data_array = array(
			'items'                       => $items,
			'externalConfirmationPageURL' => $returnURL,
			'misc1'                       => $user,
		);

		$tempId = $this->ig->zonos_createTempCart( $data_array )->tempCartUUID;
		return $tempId;
	}

	public function zonos_import_order($order_id)
	{
		$order = $this->ig->zonos_orderDetails($order_id);
		if (property_exists($order, 'error')) {
			echo $order->error;
		}

		// Disable Order creation.
		// else if (property_exists($order, 'order')) {
		// 	if (!$order->order->merchantOrderId) {
		// 		if (!$order->order->testOrder || $this->test_orders) {
		// 			$this->zonos_create_WC_order($order);
		// 		}
		// 	} else {
		// 		try{
		// 				$wcOrder = new WC_Order($order->order->merchantOrderId);
		// 				$newStatus = $this->statusArray[$order->order->orderStatus];
		// 				// update the status if the current status is one that we can progress from (fraud or hold) to (hold, cancelled or validated)
		// 				if ($newStatus && $wcOrder->has_status(array('validating', 'on-hold')) && !$wcOrder->has_status($newStatus)) {
		// 					$wcOrder->update_status($newStatus);
		// 					if ($order->order->orderStatus == 'IGLOBAL_ORDER_IN_PROCESS') {
		// 						$wcOrder->update_status(get_option('iglobal_default_status'));
		// 					}
		// 				}
		// 		} catch(Exception $e){ }
		// 	}
		// }
		return $order;
	}

	function zonos_compare_WC_items($order, $wcOrder)
	{
		$products = array();

		//getting items from both groups to filter out extra items
		$ig_items = array();
		$wc_items = array();

		foreach ($wcOrder->get_items() as $wcOrder_id => $item) {
			//removing items so we only use our order as the source of truth and ignore the cart.
			$wcOrder->remove_item($wcOrder_id);
		}


		//getting discounts from cart
		$discounts = array();
		foreach ($wcOrder->get_items('coupon') as $item_id => $coupon) {
			//deleting any coupons we find in cart because we manually add them later because
			//  - if we import the order later (due to fail or other issue) the coupon doesn't exist anymore anyway.
			$discounts[$coupon['name']] = $coupon['discount_amount'];
			$wcOrder->remove_item( $item_id );
		}


		if(isset($order->order->promoCode)){
			$discounts[$order->order->promoCode->code] = $order->order->promoCode->amount;
		}

		//get items from our cart
		foreach ($order->order->items as $item) {
			$variationsArray = array();
			if($item->unitPrice < 0) {//discounts
				if(!array_key_exists($item->description, $discounts)) {
					$wcOrder->add_coupon($item->description, $item->unitPrice * -1);
					$discounts[$item->description] = $item->unitPrice * -1;
				}
				continue;
			}
			else if (isset($item->itemCustomization) && $item->itemCustomization != 0) {//variable items
				$product = new WC_Product_Variation($item->itemCustomization);
				$_product = new WC_Product_Variable($item->productId);
				$variations = $_product->get_available_variations();
				foreach ($variations as $variation) {
					if ($variation['variation_id'] == $item->itemCustomization) {
						$variationsArray['variation'] = $variation['attributes'];
					}
				}
			} else {//other
				if(!$item->productId) {
					continue;
				}
				$product = new WC_Product($item->productId);
			}

			//foreach item we got from the cart, if its product id matches our product id then add it to ig_items array. For keeping track of the line item id
			foreach ($wc_items as $wcItemId=>$wcItem){
				if($wcItem->id == $item->productId){
					$ig_items[$wcItemId] = $item;
				}
			}

			if($this->zonos_is_woo_version_2()){
				if(isset($product->variation_id)){
					if (array_key_exists($product->variation_id, $products)) {
						continue;
					}
				}else{
					if (array_key_exists($product->get_id(), $products)) {
						continue;
					}
				}
			}else{
				//check if the product exists in our carts product array. if its not in there then we add it
				if (array_key_exists($product->get_parent_id(), $products)) {
					continue;
				}
			}

			// product missing from the order
			$wcOrder->add_product(
				$product,
				$item->quantity,
				$variationsArray
			);

		}

		//adding any discounts we have on the order.
		if(count($discounts) > 0){
			foreach($discounts as $code => $amount) {
				$wcOrder->add_coupon($code, -1 * abs($amount));
				$this->promocodeAmount = $amount;
				$this->usingPromoCode = true;
			}
		}
	}
	protected function zonos_get_id($obj) {
		if(method_exists($obj, 'get_id')) {
			return $obj->get_id();
		} else {
			return $obj->id;
		}
	}
	function zonos_create_WC_order($order)
	{
		try {
			$checkout = new WC_Checkout();
			//create a temp cart here?
			$orderId = $checkout->create_order(Array('payment_method' => ''));
			$wcOrder = new WC_Order($orderId);
			$this->zonos_compare_WC_items($order, $wcOrder);
			$this->ig->zonos_updateMerchantOrderId($order->order->orderId, $this->zonos_get_id($wcOrder));
			$order->order->merchantOrderId = $wcOrder->get_order_number();
			$confirmation_email = !get_option('iglobal_email_receipt');
			$wcOrder->add_order_note("iGlobal Order: " . $order->order->orderId);
			foreach ($order->order->notes as $note) {
				if ($note->customerNote) {
					$wcOrder->add_order_note("Customer Notes: " . $note->note);
				}
			}

			// Set Billing Address
			$billingFirst = explode(' ', $order->order->billingName)[0];
			$billingLast = explode(' ', $order->order->billingName)[1];
			$wcOrder->set_address(
				array(
					'first_name' => $billingFirst,
					'last_name' => $billingLast,
					'company' => $order->order->company,
					'email' => $order->order->billingEmail,
					'phone' => $order->order->billingPhone,
					'address_1' => $order->order->billingAddress1,
					'address_2' => $order->order->billingAddress2,
					'city' => $order->order->billingCity,
					'state' => $order->order->billingState,
					'postcode' => $order->order->billingZip,
					'country' => $order->order->billingCountryCode
				),
				'billing'
			);
			$shippingFirst = explode(' ', $order->order->name)[0];
			$shippingLast = explode(' ', $order->order->name)[1];
			// Set Shipping Address
			$wcOrder->set_address(
				array(
					'first_name' => $shippingFirst,
					'last_name' => $shippingLast,
					'company' => $order->order->company,
					'email' => $order->order->email,
					'phone' => $order->order->phone,
					'address_1' => $order->order->address1,
					'address_2' => $order->order->address2,
					'city' => $order->order->city,
					'state' => $order->order->state,
					'postcode' => $order->order->zip,
					'country' => $order->order->countryCode
				),
				'shipping'
			);
			// Set Shipping Total
			$item_id = wc_add_order_item($this->zonos_get_id($wcOrder), array(
				'order_item_name' => $order->order->customerSelectedShippingName,
				'order_item_type' => 'shipping'
			));

			wc_add_order_item_meta($item_id, 'method_id', 'other');
			wc_add_order_item_meta($item_id, 'cost', wc_format_decimal($order->order->shippingTotal));

			// Save shipping taxes - Since 2.2
			$taxes = array_map('wc_format_decimal', Array());
			wc_add_order_item_meta($item_id, 'taxes', $taxes);

			// Update total
			$wcOrder->set_shipping_total(wc_format_decimal($order->order->shippingTotal));//, 'shipping');

			if($this->usingPromoCode){
				$wcOrder->set_discount_total($order->order->promoCode->amount);
			}

			// Attempt to set Duties and Taxes
			if (isset($order->order->dutyTaxesTotal) && $order->order->dutyTaxesTotal > 0) {
				$item_id = wc_add_order_item($this->zonos_get_id($wcOrder), array(
					'order_item_name' => "Duties & Taxes",
					'order_item_type' => 'shipping'
				));

				wc_add_order_item_meta($item_id, 'method_id', 'other');
				wc_add_order_item_meta($item_id, 'cost', wc_format_decimal($order->order->dutyTaxesTotal));

				$taxes = array_map('wc_format_decimal', Array());
				wc_add_order_item_meta($item_id, 'taxes', $taxes);

				$wcOrder->set_shipping_total($wcOrder->get_shipping_total() + wc_format_decimal($order->order->dutyTaxesTotal));//, 'shipping');
			}

			// Calculate total cost
			if($this->zonos_is_woo_version_2()){
				$wcOrder->calculate_totals($and_taxes = false);
			}else{
				$this->zonos_calculate_totals($wcOrder, $and_taxes = false);
			}
			// Set Payment Method
			if ($order->order->paymentProcessing) {
				update_post_meta($this->zonos_get_id($wcOrder), '_payment_method', "iglobal_payment");
				update_post_meta($this->zonos_get_id($wcOrder), '_payment_method_title', "iGlobal accepted payment");
			}

			if ($confirmation_email) {
				WC()->mailer()->get_emails()['WC_Email_New_Order']->trigger($this->zonos_get_id($wcOrder));
			}
			// Send order from woocommerce if set
			if ($order->order->testOrder) {
				$wcOrder->update_status('wc-test');
			}
			$wcOrder->update_status($this->statusArray[$order->order->orderStatus]);
			if ($order->order->orderStatus == 'IGLOBAL_ORDER_IN_PROCESS') {
				$wcOrder->update_status(get_option('iglobal_default_status'));
			}

		} catch (Exception $e) {
			// Delete order and send error to iGlobal
			wp_delete_post($this->zonos_get_id($wcOrder), true);
			$message = "
		<--------- Error ----------->\n
		" . $e->getMessage() . "\n
		<--------- Order ----------->\n
		" . print_r($order, true) . "\n
		";
			wp_mail("failedimport@zonos.com", "Order Failed: #" . $order->order->orderId, $message);

		}
	}

	function zonos_calculate_totals($wcOrder, $and_taxes = true ) {
		//tweaking of the function in abstract-wc-order.php - woocommerce changed how they handled calculate_totals() and it broke shipping.
		$cart_subtotal      = 0;
		$cart_total         = 0;
		$fee_total          = 0;
		$cart_subtotal_tax  = 0;
		$cart_total_tax     = 0;
		$shipping_total     = $wcOrder->get_shipping_total();

		// Sum line item costs.
		foreach ( $wcOrder->get_items() as $item ) {
			$cart_subtotal += $item->get_subtotal();
			$cart_total    += $item->get_total();
		}

		// Sum fee costs.
		foreach ( $wcOrder->get_fees() as $item ) {
			$amount = $item->get_amount();

			if ( 0 > $amount ) {
				$item->set_total( $amount );
				$max_discount = round( $cart_total + $fee_total + $shipping_total, wc_get_price_decimals() ) * -1;

				if ( $item->get_total() < $max_discount ) {
					$item->set_total( $max_discount );
				}
			}

			$fee_total += $item->get_total();
		}

		// Calculate taxes for items, shipping, discounts.
		if ( $and_taxes ) {
			$wcOrder->calculate_taxes();
		}

		// Sum taxes.
		foreach ( $wcOrder->get_items() as $item ) {
			$cart_subtotal_tax += $item->get_subtotal_tax();
			$cart_total_tax    += $item->get_total_tax();
		}

		$totalDiscount = ($cart_subtotal - $cart_total);
		if($this->usingPromoCode){
			$totalDiscount = $this->promocodeAmount;
		}

		$wcOrder->set_discount_total( $totalDiscount );
		$wcOrder->set_discount_tax( $cart_subtotal_tax - $cart_total_tax );
		$wcOrder->set_total( round( $cart_total + $fee_total + $wcOrder->get_shipping_total() + $wcOrder->get_cart_tax() + $wcOrder->get_shipping_tax() - $totalDiscount, wc_get_price_decimals() ) );// - $totalDiscount
		$wcOrder->save();

		return $wcOrder->get_total();
	}
	function zonos_is_woo_version_2() {
		// If get_plugins() isn't available, require it
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		// Create the plugins folder and file variables
		$plugin_folder = get_plugins( '/' . 'woocommerce' );
		$plugin_file = 'woocommerce.php';

		// If the plugin version number is set, return it
		if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
			$version = explode(".", $plugin_folder[$plugin_file]['Version']);
			if($version[0] == "2"){
				return true;
			}
			return false;

		} else {
			// Otherwise return null
			return false;
		}
	}
}

