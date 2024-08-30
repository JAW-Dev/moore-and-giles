<?php

class MG_GiftWrapping extends WordPress_SimpleSettings {
    var $prefix = "_mg_gw_";

	public function __construct() {
	    parent::__construct();

		add_action('shopp_cart_request', array($this, 'handle_gift_wrapping'), 100); // 100 happens after cart items are added
        add_action('shopp_cart_updated', array($this, 'handle_cart_updates'), 11 );
		add_action('admin_menu', array($this, 'setup_menu_page'), 100 );
		add_action('admin_enqueue_scripts', array($this, 'scripts') );
		add_filter('shopp_themeapi_product_giftwrapping', array($this, 'gift_wrapping_output'), 10, 3);

		// Exclude Gift Tag From Search Results
		add_action('shopp_product_saved', array($this, 'prevent_search_index'), 1000, 1);
	}

	function handle_gift_wrapping() {
		if ( ! isset($_REQUEST['cart']) || $_REQUEST['cart'] != "add" ) return;

		$products = ! empty($_REQUEST['products']) && is_array($_REQUEST['products']) ? $_REQUEST['products'] : false;

		if ( ! empty($_REQUEST['add-gift-wrapping']) ) {
			if ( $products !== false && count($products) == 1 ) {
				$added_product_id = key($products);
				$Product = shopp_product($added_product_id);

				// Look up Gift Wrapping Product
				$GiftProduct = shopp_product('Gift Wrapping', 'name');

				// Configure Gift Wrapping Option
				if ( $_REQUEST['add-gift-wrapping'] == "small" ) {
					$Variant = shopp_product_variant( array('product' => $GiftProduct->id, 'option' => array('Type' => 'Small Products') ) );
				} else {
					$Variant = shopp_product_variant( array('product' => $GiftProduct->id, 'option' => array('Type' => 'Large Products') ) );
				}

				// Find cart item ID
				$cart_item_id = false;
				foreach ( ShoppOrder()->Cart as $id => $Item ) {
					if ( $Item->product == $added_product_id ) {
						$cart_item_id = $id;
					}
				}

				// Add product to cart
				shopp_add_cart_product($GiftProduct->id, $products[$added_product_id]['quantity'], $Variant->id, array('For' => $Product->name, '_linked_cart_item_id' => $cart_item_id ) );
			}
		}

		if ( $products !== false && count($products) == 1 ) {
			$added_product_id = key($products);
            $product = $products[$added_product_id];

            if ( $product['product'] == "10454" && ! empty($product['data']['_cart_item_id']) ) {
                foreach ( ShoppOrder()->Cart as $id => $Item ) {
                    if ( $product['data']['_cart_item_id'] == $id ) {
	                    $Item->data['_has_gift_tag'] = true;
                    }
                }
            }
        }
	}

	function handle_cart_updates( $Cart ) {
		$command = 'update'; // Default command
		$commands = array('add', 'empty', 'update', 'remove');

		if ( isset($_REQUEST['empty']) )
			$_REQUEST['cart'] = 'empty';

		$request = isset($_REQUEST['cart']) ? strtolower($_REQUEST['cart']) : false;

		if ( in_array( $request, $commands) )
			$command = $request;

		$allowed = array(
			'quantity' => 1,
			'product'  => false,
			'products' => array(),
			'item'     => false,
			'items'    => array(),
			'remove'   => array(),
		);

		$request = array_intersect_key($_REQUEST, $allowed); // Filter for allowed arguments

        if ( "update" == $command ) {
            if ( count($request['items']) > 0 ) {
	            foreach( $request['items'] as $id => $item ) {
		            foreach ( $Cart as $item_id => $CartItem ) {
			            if ( isset($CartItem->data['_linked_cart_item_id']) && $CartItem->data['_linked_cart_item_id'] == $id ) {
				            $Cart->setitem($item_id, $item['quantity'] );
			            }
		            }
	            }
            }

	        if ( count($request['remove']) > 0 ) {
		        foreach ( $request['remove'] as $id => $item ) {
			        foreach ( $Cart as $item_id => $CartItem ) {
				        if ( isset( $CartItem->data['_linked_cart_item_id'] ) && $CartItem->data['_linked_cart_item_id'] == $id ) {
					        $Cart->rmvitem( $item_id );
				        }
			        }
		        }
	        }
        }
    }

	function setup_menu_page() {
		add_submenu_page( "shopp-products", "Gift Wrapping", "Gift Wrapping", "manage_options", "mg-gift-wrapping", array($this, "admin_page") );
	}

	function scripts() {
		if ( isset($_GET['page']) && $_GET['page'] == "mg-gift-wrapping" ) {
			wp_register_script( 'select2', MG_CF_URL . '/js/select2/dist/js/select2.min.js', array(), '4.0.3' );
			wp_enqueue_script( 'select2' );

			wp_register_style( 'select2-css', MG_CF_URL . '/js/select2/dist/css/select2.min.css', false, '4.0.3' );
			wp_enqueue_style( 'select2-css' );

			wp_register_script( 'mg-gw-admin', MG_CF_URL . '/js/gift-wrap.js', array( 'jquery', 'select2' ), '1.0.0' );
			wp_enqueue_script( 'mg-gw-admin' );
		}
	}

	function admin_page() {
	    global $wpdb;

	    $excluded_products = $this->get_setting('excluded_products');
		$small_products = $this->get_setting('small_products');
	    $products = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'shopp_product' AND post_title <> '' ORDER BY post_title");

		$excluded_categories = $this->get_setting('excluded_categories');
		$small_categories = $this->get_setting('small_categories');
	    $categories = get_terms('shopp_category');
		?>
		<div class="wrap">
			<h2>Customer Merge</h2>
            <p>Exclusions do not get gift wrapping option.</p>
            <p>All products that are not excluded and do not match small criteria get large boxes.</p>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
                <?php $this->the_nonce(); ?>

                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row" valign="top">Excluded Products</th>
                        <td>
                            <label>
                                <select class="mg-gw-select2" type="text" name="<?php echo $this->get_field_name('excluded_products'); ?>[]" multiple="multiple">
                                    <?php foreach( $products as $prod ): $selected = ''; ?>
                                        <?php if ( in_array($prod->ID, $excluded_products) ) $selected = "selected='selected'"; ?>
                                        <option value="<?php echo $prod->ID; ?>" <?php echo $selected; ?>><?php echo $prod->post_title; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" valign="top">Excluded Categories</th>
                        <td>
                            <label>
                                <select class="mg-gw-select2" type="text" name="<?php echo $this->get_field_name('excluded_categories'); ?>[]" multiple="multiple">
				                    <?php foreach( $categories as $cat ): $selected = ''; ?>
					                    <?php if ( in_array($cat->term_id, $excluded_categories) ) $selected = "selected='selected'"; ?>
                                        <option value="<?php echo $cat->term_id; ?>" <?php echo $selected; ?>><?php echo $cat->name; ?></option>
				                    <?php endforeach; ?>
                                </select>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" valign="top">Small Box Categories</th>
                        <td>
                            <label>
                                <select class="mg-gw-select2" type="text" name="<?php echo $this->get_field_name('small_categories'); ?>[]" multiple="multiple">
				                    <?php foreach( $categories as $cat ): $selected = ''; ?>
					                    <?php if ( in_array($cat->term_id, $small_categories) ) $selected = "selected='selected'"; ?>
                                        <option value="<?php echo $cat->term_id; ?>" <?php echo $selected; ?>><?php echo $cat->name; ?></option>
				                    <?php endforeach; ?>
                                </select>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" valign="top">Small Box Products</th>
                        <td>
                            <label>
                                <select class="mg-gw-select2" type="text" name="<?php echo $this->get_field_name('small_products'); ?>[]" multiple="multiple">
				                    <?php foreach( $products as $prod ): $selected = ''; ?>
					                    <?php if ( in_array($prod->ID, $small_products) ) $selected = "selected='selected'"; ?>
                                        <option value="<?php echo $prod->ID; ?>" <?php echo $selected; ?>><?php echo $prod->post_title; ?></option>
				                    <?php endforeach; ?>
                                </select>
                            </label>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <?php submit_button(); ?>
            </form>
		<?php
	}

	function get_gift_wrapping_size( $Product ) {
		// Figure out wrapping options
		$excluded_products = $this->get_setting('excluded_products');
		$excluded_categories = $this->get_setting('excluded_categories');

		$small_categories = $this->get_setting('small_categories');
		$small_products = $this->get_setting('small_products');

		/**
		 * Exclusion Tests
		 */
		// Product ID
		if ( in_array($Product->id, $excluded_products) ) {
			return false;
		}

		// Categories
		foreach( $excluded_categories as $excluded_category ) {
			if ( shopp($Product, 'in-category', "id={$excluded_category}") ) {
				return false;
			}
		}

		/**
		 * Small Box Tests
		 */
		$small_box = false;

		// Product ID
		if ( in_array($Product->id, $small_products) ) {
			$small_box = true;
		}

		// Categories
		foreach( $small_categories as $small_category ) {
			if ( shopp($Product, 'in-category', "id={$small_category}") ) {
				$small_box = true;
				break;
			}
		}

		return true === $small_box ? 'small' : 'large';
	}

	function gift_wrapping_output($result, $options, $Product) {
		$defaults = array(
			'wrap'  => false,
            'title' => false,
		);

		$options = array_merge($defaults, $options);
		$wrap = Shopp::str_true( $options['wrap'] );

		// Figure out wrapping options
		$excluded_products = $this->get_setting('excluded_products');
		$excluded_categories = $this->get_setting('excluded_categories');

		$small_categories = $this->get_setting('small_categories');
		$small_products = $this->get_setting('small_products');

		// Default Box Size
		$gift_wrapping = "large";
		$gift_wrapping_price = "20";

		/**
		 * Exclusion Tests
		 */
		// Product ID
		if ( in_array($Product->id, $excluded_products) ) {
			return false;
        }

		// Categories
        foreach( $excluded_categories as $excluded_category ) {
            if ( shopp($Product, 'in-category', "id={$excluded_category}") ) {
                return false;
            }
        }

		/**
		 * Small Box Tests
		 */
        $small_box = false;

		// Product ID
		if ( in_array($Product->id, $small_products) ) {
		    $small_box = true;
        }

		// Categories
		foreach( $small_categories as $small_category ) {
			if ( shopp($Product, 'in-category', "id={$small_category}") ) {
				$small_box = true;
				break;
			}
		}

		if ( $small_box === true ) {
			$gift_wrapping = "small";
			$gift_wrapping_price = "10";
        }

		/**
		 * Markup
		 */
        if ( $wrap ) {
	        $result = '<div class="addons">';
        }

        if ( Shopp::str_true( $options['title'] ) ) {
            $result .= '<h4>Add-ons</h4>';
        }

        if ( $wrap ) $result .= "<ul>";

        $img = get_stylesheet_directory_uri() . '/assets/images/gift-wrap-img.jpg';

        if ( ! empty( $img ) ) {
            $result .= '<span class="add-on-wrap">';
        }

        $result .= '<li class="gift-wrapping-addon"><label><input type="checkbox" name="add-gift-wrapping" value="' . $gift_wrapping . '" />Add Gift Wrapping (+' . ShoppCore::money($gift_wrapping_price) . ')</label></li> ';

        if ( ! empty( $img ) ) {
            $result .= '<a href="#gift-wrap-info-modal" class="gift-wrap-more-link">More information.</a></span>';

            $result .= '<div id="gift-wrap-info-modal" class="mfp-hide white-popup-block">
                <div class="personalization-modal-content">
                <img src="' . $img . '" alt="Gift Wrap" />
                <br />
                <a class="popup-modal-dismiss" href="#">Close</a>
                </div>
            </div>';
            
        }

		if ( $wrap ) $result .= "</ul></div>";

	    return $result;
    }

	function prevent_search_index($Product) {
		if ( $Product->name == "Leather Gift Tag" ) {
			global $wpdb, $MG_CoreFunctionality;
			$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}shopp_index WHERE product = %d", $Product->id) );
			//$MG_CoreFunctionality->remove_filters_for_anonymous_class( 'shopp_product_saved', 'ShoppAdminWarehouse', 'index', 99 );
		}
	}
}
