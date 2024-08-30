<?php

class MG_ProductBundles {
	public function __construct() {
		// Add UI
		add_action( 'cmb2_init', array( $this, 'metaboxes' ) );

		// Handle Purchase
		add_action( 'shopp_authed_order_event', array( $this, 'post_purchase_handle_bundles' ) );
	}

	function metaboxes() {
		global $wpdb;

		$prefix = '_mg_pb_';

		if ( ! isset( $_GET['id'] ) ) {
			return;
		}

		$product = shopp_product( $_GET['id'] );

		// Product Dropdown Options
		$products = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_type = 'shopp_product' ORDER BY post_title ASC" );

		$product_dropdown     = array();
		$product_dropdown[''] = 'Select product';

		foreach ( $products as $product ) {
			$product = shopp_product( $product->ID );

			$variations = $wpdb->get_results( $query = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}shopp_price WHERE product = %d AND context = 'variation' AND type = 'Shipped'", $product->id ) );

			if ( ! empty( $variations ) ) {
				foreach ( $variations as $var ) {
					$sku = $var->sku;
					if ( ! empty( $sku ) ) {
						$product_dropdown[ "{$var->sku}" ] = $product->name . ' - ' . $var->label;
					}
				}
			} else {
				$sku = shopp( $product, 'get-sku' );
				if ( ! empty( $sku ) ) {
					$product_dropdown[ $sku ] = $product->name;
				}
			}
		}

		$bundle_box = new_cmb2_box(
			array(
				'id'           => $prefix . 'metabox',
				'title'        => __( 'Product Bundle', 'cmb2' ),
				'object_types' => array( 'shopp_product' ), // Post type
			)
		);

		$bundle_box->add_field(
			array(
				'name' => 'Apply discount across bundle items.',
				'desc' => 'Only turn on if price of bundle is less than price of bundled products.',
				'id'   => $prefix . 'apply_discount',
				'type' => 'checkbox',
			)
		);

		$bundle_group_field_id = $bundle_box->add_field(
			array(
				'id'          => $prefix . 'product_bundles',
				'type'        => 'group',
				'description' => __( 'Product Bundle', 'cmb2' ),
				'options'     => array(
					'group_title'   => __( 'Product Bundle {#}', 'cmb2' ), // {#} gets replaced by row number
					'add_button'    => __( 'Add Another Product Bundle', 'cmb2' ),
					'remove_button' => __( 'Remove Product Bundle', 'cmb2' ),
					'repeatable'    => true,
				),
			)
		);

		$bundle_box->add_group_field(
			$bundle_group_field_id, array(
				'name' => 'Enable Product Bundle',
				'desc' => 'Turn this product into a bundle. After purchase, product listed below will be added to the cart in replace of bundle product.',
				'id'   => 'enable_product_bundle',
				'type' => 'checkbox',
			)
		);

		$bundle_box->add_group_field(
			$bundle_group_field_id, array(
				'name' => 'Product Bundle SKU',
				'desc' => 'If bundle is a variation, enter the SKU for the variation here.',
				'id'   => 'product_bundle_sku',
				'type' => 'text',
			)
		);

		$bundle_box->add_group_field(
			$bundle_group_field_id, array(
				'name'       => __( 'Product', 'cmb2' ),
				'desc'       => __( 'The bundled products.', 'cmb2' ),
				'id'         => 'sku',
				'type'       => 'select',
				'repeatable' => true,
				'options'    => $product_dropdown,
			)
		);
	}

	function post_purchase_handle_bundles() {
		global $MG_CoreFunctionality, $wpdb;
		$Order = ShoppPurchase();

		ShoppPurchase()->load_purchased();
		$bundle = 0;

		foreach ( ShoppPurchase()->purchased as $index => $pd ) {
			$already_processed_addons = false;

			if ( empty( $pd->product ) ) {
				error_log( 'Product Bundles: No product ID, bailing.' );
				continue;
			}

			// Get list of product bundles
			$product_bundles = get_post_meta( $pd->product, '_mg_pb_product_bundles' );
			$apply_discount  = get_post_meta( $pd->product, '_mg_pb_apply_discount' );

			if ( empty( $product_bundles ) ) {
				error_log( 'Product Bundles: No bundle, bailing.' );
				continue;
			}

			// Get it out of the array
			$product_bundles = $product_bundles[0];

			foreach ( $product_bundles as $product_bundle ) {
				// Are product bundles enabled?
				$enabled = $product_bundle['enable_product_bundle'];

				if ( empty( $enabled ) ) {
					error_log( 'Product Bundles: Bundle not enabled.' );
					continue;
				}

				// Does the product bundle have a particular SKU we are looking for?
				$bundle_sku = $product_bundle['product_bundle_sku'];

				// If bundle has SKU and doesn't match this item, bail
				if ( ! empty( $bundle_sku ) && strtolower( $bundle_sku ) != strtolower( $pd->sku ) ) {
					error_log( 'Product Bundles: SKU requirement not matched.' );
					continue;
				}

				// WE HAVE A BUNDLE!
				$bundled_products = $product_bundle['sku'];
				$bundle++;

				// Get total of bundled products
				if ( ! empty( $bundled_products ) ) {
					// If apply discounts enabled, try to apply them
					if ( ! empty( $apply_discount ) ) {
						$b_total = 0.00;

						foreach ( $bundled_products as $bp ) {
							if ( empty( $bp ) ) {
								error_log( 'Product Bundles: Bundle item has no SKU.' );
							};

							// Look up price of sku
							$price = $wpdb->get_var( $wpdb->prepare( "SELECT price FROM {$wpdb->prefix}shopp_price WHERE sku = %s LIMIT 1", $bp ) );

							if ( ! empty( $price ) ) {
								$b_total = $b_total + ( $price * $pd->quantity );
							}
						}

						// Get a fraction to reduce price as we add bundled items to the purchase
						if ( $pd->total > $b_total ) {
							error_log( 'Product Bundles: Item total was OVER bundled product total, so we have an error. Purchased Total: ' . $pd->total . ' Bundle Total: ' . $b_total );
							//continue; TODO: This check is probably not necessary since not all bundles have discounts.
							$reduction_factor = 1;
						} else {
							error_log( 'Product Bundles: Item total was under bundled product total. Purchased Total: ' . $pd->total . ' Bundle Total: ' . $b_total );
							$reduction_factor = $pd->total / $b_total;
						}
					} else {
						error_log( 'Product Bundles: Apply discount disabled.' );
						$reduction_factor = 1;
					}

					// Loop through products again, adding them to the cart but with a reduced price
					foreach ( $bundled_products as $bundle_index => $bp ) {
						$price_line = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}shopp_price WHERE (context = 'product' OR context = 'variation') AND type <> 'N/A' AND sku = %s LIMIT 1", $bp ) );
						//error_log('Product Bundles: Main Priceline - ' . print_r($price_line, true) );

						$price_line = $price_line[0];
						$Product    = shopp_product( $price_line->product );

						if ( empty( $price_line ) ) {
							error_log( 'Product Bundles: Price line look up failed.' );
							continue;
						};

						$new_data = $pd->data;

						if ( ! shopp( $Product, 'has-addons' ) && isset( $new_data['Personalization Initials'] ) ) {
							unset( $new_data['Personalization Initials'] );
						}

						if ( $price_line->label != 'Price & Delivery' ) {
							$name = $Product->name . ' - ' . $price_line->label;
						} else {
							$name = $Product->name;
						}

						// If the first product in the bundle, pull in the monogramming add-on
						$excluded_addons = array();
						if ( $bundle_index == 0 && ! empty( $pd->addons ) ) {
							$addons = $pd->addons->named;

							foreach ( $addons as $addon_name => $addon ) {
								if ( $addon_name == 'Add Personalization' ) {
									$excluded_addons[] = $addon;
									unset( $pd->addons->named[ $addon_name ] );
									unset( $pd->addons->meta[ $addon->id ] );
								}
							}
						}

						$MG_CoreFunctionality->shopp_add_order_line(
							$Order->id, array(
								'type'      => $price_line->type,
								'product'   => $price_line->product,
								'price'     => $price_line->id,
								'name'      => "Bundle $bundle: " . $name,
								'quantity'  => $pd->quantity,
								'unitprice' => round( $price_line->price * $reduction_factor, 2 ),
								'unittax'   => round( ( ( $price_line->price * $reduction_factor ) / $pd->total ) * $pd->tax, 2 ),
								'shipping'  => 0.00,
								'total'     => round( $price_line->price * $reduction_factor * $pd->quantity, 2 ),
								'sku'       => $price_line->sku,
								'data'      => $new_data,
								'addons'    => $excluded_addons,
							)
						);

						if ( ! empty( $pd->addons ) && ! $already_processed_addons ) {
							$addons = $pd->addons->meta;

							foreach ( $addons as $addon ) {
								$addon_price_line = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}shopp_price WHERE (context = 'product' OR context = 'variation') AND type <> 'N/A' AND sku = %s LIMIT 1", $addon->value->sku ) );
								//error_log('Product Bundles: Addon Priceline - ' . print_r($addon_price_line, true) );

								if ( empty( $addon_price_line ) ) {
									$MG_CoreFunctionality->shopp_add_order_line(
										$Order->id, array(
											'type'      => $addon,
											'product'   => 0,
											'price'     => $addon->value->price,
											'name'      => "Bundle $bundle: " . $addon->name,
											'quantity'  => $pd->quantity,
											'unitprice' => $addon->value->unitprice,
											'unittax'   => 0,
											'shipping'  => 0.00,
											'total'     => $addon->value->unitprice,
											'sku'       => $addon->value->sku,
										)
									);
								} else {
									$price_line    = $addon_price_line[0];
									$addon_product = shopp_product( $price_line->product );

									if ( $price_line->label != 'Price & Delivery' ) {
										$name = $addon_product->name . ' - ' . $price_line->label;
									} else {
										$name = $Product->label . ' Add-on - ' . $addon->name;
									}

									$MG_CoreFunctionality->shopp_add_order_line(
										$Order->id, array(
											'type'      => $price_line->type,
											'product'   => $price_line->product,
											'price'     => $price_line->id,
											'name'      => "Bundle $bundle: " . $name,
											'quantity'  => $pd->quantity,
											'unitprice' => $addon->value->unitprice,
											'unittax'   => 0,
											'shipping'  => 0.00,
											'total'     => $addon->value->unitprice * $pd->quantity,
											'sku'       => $price_line->sku,
										)
									);
								}
							}

							$already_processed_addons = true;
						}
					}
				}

				// Clean up original product from purchase items
				$MG_CoreFunctionality->shopp_rmv_order_line( $Order->id, $index );
			}
		}

		// Correct order of items
		ShoppPurchase()->load_purchased();
	}
}
