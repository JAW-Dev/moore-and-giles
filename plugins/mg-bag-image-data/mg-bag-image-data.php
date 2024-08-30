<?php
/*
Plugin Name: Shopp Bage Image Data
Description:  Associate pricelines with product images.
Version: 1.1
Author: Clif Griffin Development Inc.
Author URI: http://cgd.io
*/

class ShoppImageData {
	public function __construct() {
		add_action( 'shopp_product_saved', array($this, 'handle_save') );
		add_action( 'shopp_loaded', array($this, 'shopp_loaded') );

		// Coverimage Override
		add_filter('shopp_themeapi_product_coverimage', array($this, 'coverimage'), 20, 3);
	}

	function shopp_loaded () {
		add_filter('shopp_order_item_image_id', array($this, 'admin_item_image_id'), 100, 2 );
		add_filter('shopp_themeapi_cartitem_coverimage', array($this, 'new_cartitem_coverimage'), 100, 3);
		add_action( 'admin_head', array($this, 'admin_head') );
	}

	function admin_head() {
		add_meta_box(
	        'shopp_image_data',
	        'Image Data',
	        array($this, 'inner'),
	        Product::$posttype,
			'advanced',
			'core'
	    );
	}

    // Actually render content of meta box
	function inner($Product) {
	    ?>
		<table class="widefat fixed" cellspacing="0">
			<?php $featured_image = shopp_product_meta(ShoppProduct()->id, 'featured-image'); ?>
			<?php foreach ((array)$Product->images as $i => $Image): ?>
				<?php
				$current_prices = (array)shopp_meta($Image->id, 'image', 'price');
				?>
				<tr>
					<td class="column-image">
						<img src="?siid=<?php echo $Image->id; ?>&amp;<?php echo $Image->resizing(96,0,1); ?>" width="96" height="96" /><br/>
						<?php echo $Image->title; ?> <br />

						<input type="hidden" name="shopp_image_data[<?php echo $Image->id; ?>][featured]" value="off" />
						<input type="checkbox" class="shopp-image-data-cover-check" name="shopp_image_data[<?php echo $Image->id; ?>][featured]" value="on" <?php if( $featured_image == $Image->id ) echo 'checked="checked"'; ?> /> Cover image.
					</td>
					<td class="column-price">
						<select name="shopp_image_data[<?php echo $Image->id; ?>][price][]" size="10" multiple>
							<?php echo $this->get_variant_options($Product, $current_prices); ?>
						</select>
					</td>
				</tr>
			<?php endforeach; ?>

		</table>

		<style>
		td.column-image {
			width: 20%;
		}
		td.column-price {
			width: 20%;
		}
		td.column-color {
			width: 60%;
		}
		ul.color-checkboxes {
			margin: 0;
		}
		ul.color-checkboxes li {
			list-style: none;
			float: left;
			width: 80px;
		}
		</style>
		<script type="text/javascript">
		jQuery(document).ready(function() {
			jQuery(document).on('change', '.shopp-image-data-cover-check', function() {
				if ( jQuery(this).is(':checked') ) {
					jQuery(".shopp-image-data-cover-check").not(this).prop('checked', false);
				}
			});
		});
		</script>
	<?php
	}

	function get_variant_options($Product, $current_prices) {
		$result = array();

		$result[] = "<option value=''>None</option>";

		foreach($Product->prices as $p) {
			if ( $Product->variants == 'on' && $p->context == "product" ) continue;
			if ( $p->context != "variation" ) continue;

			$selected = '';
			if ( in_array($p->id, $current_prices) ) {
				$selected = "selected='selected'";
			}

			$result[] = "<option value='{$p->id}' $selected>{$p->label}</option>";
		}

		return join('\n', $result);
	}

	function handle_save($Product) {
		global $wpdb;

		if ( isset($_REQUEST['shopp_image_data']) ) {

			$shopp_image_data = $_REQUEST['shopp_image_data'];

			foreach($shopp_image_data as $image => $data) {
				if ( ! empty($data['price']) && is_array($data['price']) ) {
					$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}shopp_meta WHERE context = 'image' AND type = 'meta' AND name = 'price' AND value = %s", $data['price']) );
					shopp_set_meta($image, 'image', 'price', $data['price']);
				}

				if ( count($data['color']) > 0 ) {
					shopp_set_meta($image, 'image', 'colors', join(',', $data['color']) );
				}

				if ( $data['featured'] == 'on' ) {
					shopp_rmv_product_meta( ShoppProduct()->id, 'featured-image' );
					shopp_set_meta(ShoppProduct()->id, 'product', 'featured-image', $image );
				}
			}
		}

		// Remove deleted images
		if ( ! empty($_POST['deleteImages']) ) {
			$deletes = array();
			if (strpos($_POST['deleteImages'],",") !== false) $deletes = explode(',',$_POST['deleteImages']);
			else $deletes = array($_POST['deleteImages']);

			foreach ($deletes as $image) {
				sDB::query($wpdb->prepare("DELETE FROM {$wpdb->prefix}shopp_meta WHERE context='image' AND type='meta' AND name='price' AND parent = %d", $image));
			}
		}
	}

	function variations ($result, $options, $O) {
		global $wpdb;

		$string = "";

		if (!isset($options['mode'])) {
			return current($O->prices);
		}

		if ( shopp_setting_enabled('inventory') && $O->outofstock ) return false; // Completely out of stock, hide menus
		if (!isset($options['taxes'])) $options['taxes'] = null;

		$defaults = array(
			'defaults' => '',
			'disabled' => 'show',
			'pricetags' => 'show',
			'before_menu' => '',
			'after_menu' => '',
			'format' => '%l (%p)',
			'label' => 'on',
			'mode' => 'multiple',
			'taxes' => null,
			'required' => __('You must select the options for this item before you can add it to your shopping cart.','Shopp')
			);
		$options = array_merge($defaults,$options);
		extract($options);

		$taxes = isset($taxes) ? Shopp::str_true($taxes) : null;
		$collection_class = ShoppCollection() && isset(ShoppCollection()->slug) ? 'category-' . ShoppCollection()->slug : '';


		if ( 'single' == $mode ) {
			if ( ! empty($before_menu) ) $string .= $before_menu . "\n";

			$string .= "<div class='image-variants'>"; // products[' . (int)$O->id . '][price]

			$count = 1;
			$class = "first";

			$possible_sort_options = $this->get_variation_sort_options();
			$possible_sort_directions = $this->get_variation_sort_direction();

			$orderby = 'label';
			$orderby_direction = "ASC";

			if ( isset( $_GET['variation_sort'] ) && isset( $possible_sort_options[$_GET['variation_sort']] ) ) {
				$orderby = $_GET['variation_sort'];
				$orderby_direction = $possible_sort_directions[$orderby];
			}

			$orderby = "ORDER BY {$orderby} {$orderby_direction}";

			$query = "SELECT pr.*, m1.parent as image_id, m2.sortorder as color, pop.count as times_sampled
						FROM wp_3_shopp_price pr
						LEFT JOIN wp_3_shopp_meta m1 ON m1.value = pr.id AND m1.context = 'image' AND m1.type = 'meta' AND m1.name = 'price'
						LEFT JOIN wp_3_shopp_meta m2 ON m1.parent = m2.id AND m2.context = 'product' AND m2.type = 'image' AND m2.name = 'original'
						LEFT JOIN (SELECT count(*) as count, price FROM `wp_3_shopp_purchased` WHERE price > 0 GROUP BY price ORDER BY count DESC) as pop ON pop.price = pr.id
						WHERE pr.product = %d AND pr.context = 'variation' {$orderby}";

			$prices = $wpdb->get_results( $wpdb->prepare($query, $O->id) );

			foreach ($prices as $pricing) {

				$currently = Shopp::str_true($pricing->sale)?$pricing->promoprice:$pricing->price;
				$disabled = Shopp::str_true($pricing->inventory) && $pricing->stock == 0 ? ' disabled="disabled"' : '';

				$currently = self::_taxed((float)$currently, $O, $pricing->tax, $taxes);

				$discount = $pricing->price == 0 ? 0 : 100-round($pricing->promoprice*100/$pricing->price);
				$_ = new StdClass();
				if ($pricing->type != 'Donation')
					$_->p = money($currently);
				$_->l = $pricing->label;
				$_->i = Shopp::str_true($pricing->inventory);
				if ($_->i) $_->s = $pricing->stock;
				$_->u = $pricing->sku;
				$_->tax = Shopp::str_true($pricing->tax);
				$_->t = $pricing->type;
				if ($pricing->promoprice != $pricing->price)
					$_->r = money($pricing->price);
				if ($discount > 0)
					$_->d = $discount;

				if ( 'N/A' != $pricing->type ) {
					global $wpdb;

					$image_id = $wpdb->get_var( $wpdb->prepare("SELECT parent FROM {$wpdb->prefix}shopp_meta as nm WHERE context = 'image' AND type = 'meta' AND name = 'price' AND value = %s", $pricing->id ));
					//error_log($pricing->label . ' ' . $image_id . ' ' . $pricing->id);

					$string .= "<div class='image-variant image-variant-$pricing->id one-fourth $class'>";
						$string .= '<form action="' . shopp( 'cart.get-url' ) . '" method="post" class="shopp product validate validation-alerts">';
							$string .= '<img rel="image-variants" href="' . ShoppStorefrontThemeAPI::image($result, array('id' => $image_id, 'width' => '960', 'quality' => '60', 'height' => '960', 'fit' => 'crop','property' => 'src'), $O) . '" class="lazy colorbox-pinterest" title="' . shopp('product','get-name') . ' ' . $pricing->label . ' Leather" src="' . get_bloginfo('stylesheet_directory') . '/assets/images/grey.png" data-original="' . ShoppStorefrontThemeAPI::image($result, array('id' => $image_id, 'width' => '180', 'quality' => '60', 'height' => '180', 'fit' => 'crop','property' => 'src'), $O) . '" width="180" height="180" />';
							$string .=  $pricing->label;
							$string .= '<input type="hidden" name="products[' . (int)$O->id . '][prices][]" value="' . $pricing->id . '"' . $disabled . ' />';
							$string .= shopp( 'product.get-addtocart','ajax=html&class=addtocart single-variant-button&value=Add to Basket' );
						$string .= '</form>';
					$string .= "</div>";

					$class = "";
					if($count % 4 == 0) $class = "first";
					$count++;
				}
			}

			$string .= '</div>';
			if (!empty($options['after_menu'])) $string .= $options['after_menu']."\n";

		} else {
			if (!isset($O->options)) return;

			$menuoptions = $O->options;
			if (!empty($O->options['v'])) $menuoptions = $O->options['v'];

			$baseop = shopp_setting('base_operations');
			$precision = $baseop['currency']['format']['precision'];

			$pricekeys = array();
			foreach ($O->pricekey as $key => $pricing) {
				$discount = 100-round($pricing->promoprice*100/$pricing->price);
				$_ = new StdClass();
				if ($pricing->type != 'Donation')
					$_->p = (float)apply_filters('shopp_product_variant_price', (Shopp::str_true($pricing->sale) ? $pricing->promoprice : $pricing->price) );
				$_->i = Shopp::str_true($pricing->inventory);
				$_->s = $_->i ? (int)$pricing->stock : false;
				$_->u = $pricing->sku;
				$_->tax = Shopp::str_true($pricing->tax);
				$_->t = $pricing->type;
				if ($pricing->promoprice != $pricing->price)
					$_->r = $pricing->price;
				if ($discount > 0)
					$_->d = $discount;
				$pricekeys[$key] = $_;
			}

			// Output a JSON object for JS manipulation
			if ( 'json' == $options['mode'] ) return json_encode($pricekeys);

			$jsoptions = array('prices'=> $pricekeys,'format' => $format);
			if ( 'hide' == $options['disabled'] ) $jsoptions['disabled'] = false;
			if ( 'hide' == $options['pricetags'] ) $jsoptions['pricetags'] = false;
			if ( ! empty($taxrate) ) $jsoptions['taxrate'] = Shopp::taxrate($O);

			ob_start();
?><?php if (!empty($options['defaults'])): ?>
$s.opdef = true;
<?php endif; ?>
<?php if (!empty($options['required'])): ?>
$s.opreq = "<?php echo $options['required']; ?>";
<?php endif; ?>
new ProductOptionsMenus(<?php printf("'select%s.product%d.options'",$collection_class,$O->id); ?>,<?php echo json_encode($jsoptions); ?>);
<?php

			$script = ob_get_contents();
			ob_end_clean();

			add_storefrontjs($script);

			foreach ( $menuoptions as $id => $menu ) {
				if ( ! empty($before_menu) ) $string .= $before_menu . "\n";
				if ( Shopp::str_true($label) ) $string .= '<label for="options-' . esc_attr($menu['id']) . '">' . esc_html($menu['name']) . '</label> '."\n";
				$string .= '<select name="products[' . (int)$O->id . '][options][]" class="' . esc_attr($collection_class) . ' product' . (int)$O->id . ' options" id="options-' . esc_attr($menu['id']) . '">';
				if ( ! empty($defaults) ) $string .= '<option value="">' . esc_html($options['defaults']) . '</option>' . "\n";
				foreach ( $menu['options'] as $key => $option )
					$string .= '<option value="' . esc_attr($option['id']) . '">' . esc_html($option['name']) . '</option>'."\n";

				$string .= '</select>';
				if ( ! empty($after_menu) ) $string .= $after_menu . "\n";
			}
		}

		return $string;
	}

	function admin_item_image_id($result, $Item, $Product) {
		global $wpdb;
		$meta_table = $wpdb->prefix . 'shopp_meta';
		$image_id = $wpdb->get_var( $wpdb->prepare("SELECT parent FROM $meta_table as nm WHERE context = 'image' AND type = 'meta' AND name = 'price' AND value = %s", $Item->priceline ) );

		if ( $image_id > 0 ) return $image_id;

		return $result;
	}

	function new_cartitem_coverimage($result, $options, $O) {
		global $wpdb;

		$meta_table = $wpdb->prefix . 'shopp_meta';

		$image_id = $wpdb->get_var( $wpdb->prepare("SELECT parent FROM $meta_table as nm WHERE context = 'image' AND type = 'meta' AND name = 'price' AND value = %s", $O->priceline ));

		unset($options['index']);
		$options['id'] = $image_id;

		$new_image = ShoppStorefrontThemeAPI::image( $result, $options, shopp_product($O->product) );

		if ( ! empty($new_image) ) return $new_image;

		return $result;
	}

	function coverimage($result, $options, $Product) {
		if ( shopp_product_has_meta($Product->id, 'featured-image') ) {
			$Product->load_data( array('images') );

			$featured_image = shopp_product_meta($Product->id, 'featured-image');
			$options['id'] = $featured_image;

			return ShoppStorefrontThemeAPI::image($result, $options, $Product);
		}

		return $result;
	}
}

$ShoppImageData = new ShoppImageData();
