<?php
/*
Plugin Name: MG Spec Sheet Generator
Plugin URI: http://cgd.io
Description:  Generate spec sheet PDF on the fly.
Version: 1.0.0
Author: CGD Inc.
Author URI: http://cgd.io

------------------------------------------------------------------------
Copyright 2009-2011 Clif Griffin Development Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

require_once( dirname(__FILE__) . '/vendor/autoload.php' );
use Knp\Snappy\Pdf;

class MG_SpecSheet {
	function __construct() {
		add_action('wp', array($this, 'catch_request') );
		add_action('shopp_loaded', array($this, 'shopp_loaded') );
	}

	function catch_request() {
		if ( ! isset($_REQUEST['download_spec_sheet']) ) return;

		$product_id = intval($_REQUEST['download_spec_sheet']);

		$this->create_pdf($product_id);
		exit();
	}

	function shopp_loaded() {
		add_filter('shopp_themeapi_product_pdfvariations', array($this, 'variations'), 100, 3 );
    }

	function create_pdf( $product_id ) {
		shopp('storefront','product',"id={$product_id}&load=true");
		ShoppProduct()->load_data();
		ob_start();
		?>
		<?php while ( shopp( 'product.specs' ) ) :
            $spec_name = shopp( 'product.get-spec', 'name' );
            if ( $spec_name == "Features" ) {
                $features = shopp( 'product.get-spec', 'content' );
                $features = explode( PHP_EOL, $features);
                $temp = array();

                foreach( $features as $feature ) {
                    $temp[] = "<li>{$feature}</li>";
                }

                $features = join( PHP_EOL, $temp );
                continue;
			}
			?>
			<?php if ( $spec_name != 'Testing' ) : ?>
				<div class="spec">
					<h4><?php echo $spec_name; ?></h4>
                    <?php if ( shopp('product.get-spec', 'name') == "Avg. Hide Size" ): ?>
                        <?php
                        $matches = [];
                        $square_feet = shopp( 'product.get-spec', 'content' );
                        $square_meters = '';

                        if ( preg_match('@(\d+)(?: - )?(?: – )?(?: &#8211; )?(\d+)?@', $square_feet, $matches ) && count($matches) > 1 ) {
                            if ( ! empty( $matches[1] ) && is_numeric( $matches[1] ) ) {
                                $square_meters = round($matches[1] / 10.764, 2);
                            }
                            if ( ! empty( $matches[2] ) && is_numeric( $matches[2] ) ) {
                                $matches[2] = round($matches[2] / 10.764, 2);
                                $square_meters = "$square_meters &#8211; {$matches[2]} sq m";
                            }
                        }
                        echo wpautop( $square_feet . '<br />' . $square_meters  ); ?>
                    <?php else: ?>
					    <p><?php shopp( 'product.spec', 'content' ); ?></p>
                    <?php endif; ?>
				</div>
			<?php endif; ?>
		<?php endwhile; ?>
		<?php while ( shopp( 'product.specs' ) ) :
            $spec_name = shopp( 'product.get-spec', 'name' );
			?>
			<?php if ( $spec_name === 'Testing' ) : ?>
				<div class="spec">
					<h4><?php echo $spec_name; ?></h4>
					<p><?php shopp('product.spec', 'content'); ?></p>
				</div>
			<?php endif; ?>
		<?php endwhile; ?>

        <?php
        $specs = ob_get_clean();

		// set up hide type image for the pdf render
		$hide_types = wp_get_object_terms( $product_id, "shopp_hide_type" );
		$hide_image = '';
		$hide_type_name = '';
		$hide_type = '';

		if ( ! empty( $hide_types ) && ! is_wp_error( $hide_types ) ) {
            foreach( $hide_types as $ht ) {

                if ( $ht->name ) {
                    $hide_type_name = $ht->name;
                }

                $image_url = get_term_meta( $ht->term_id, 'hide_type_image', true );

                $hide_image = '<img width="130" class="hide-image" src="' . $image_url . '"/>';
                break;
            }
		}

        if ( ! empty($hide_image) && ! empty($hide_type_name) ) {
		    $hide_type = '<div class="hide-type">
		           ' . $hide_image . '
                    <p>' . $hide_type_name . '</p>
			    </div>';
        }

		$print_html = '
        <!DOCTYPE html>
		<html>
			<head>
				<style>
                    * {
                      box-sizing: border-box;
                    }

					body {
						padding:0;
						font-family: Open Sans, Arial, sans-serif;
						font-weight: normal;
						font-size: 12px;
						margin: 0;

						background-size: 100%;

						width: 100%;
						height: 100%;
					}

					h1  {
						margin-top: 0;
						font-size: 24px;
						font-weight: 600;
					}

					h2 {
					    // font-weight: lighter;
					}

		            h3 {
		                font-size: 14px;
		                font-weight: 600;
		            }

					h4 {
						font-size: 12px;
						margin-bottom: 0;
					}

					h5 {
						font-size: 14px;
						color: #999;
					}

					p {
						font-size: 12px;
						line-height: 1.6em;
					}


                    div.info-column {
                        margin: 0;
                        float: left;
                        width: 30%;
                        height: 100%;
						padding: 20px;
						color: #4d4d4f;
                    }

                    .info-column .logo {
                        margin-bottom: 30px;
                    }

                    .info-column ul {
                        padding-left: 20px;
                        margin-bottom: 30px;
                    }

                    .info-column li {
                        font-size: 12px;
                    }

                    .divider {
                        width: 60px;
                        height: 3px;
                        background: #ecb21f;
                        margin: 30px 0;
                    }

                    .hide-type {
                        position: absolute;
						text-align: center;

						bottom: 40px;
						left: 0;

						width: 25%;

						color: #fff;
						font-weight: lighter;
						font-size: 12px;
                    }

					div.content-container {
						float: left;
						padding: 20px;
						width: 70%;
						font-size: 12px;

						height: 75%;

						background: #fff;
						color: #666;
					}

					div.content-container form {
					    text-align: center;
					}

					div.content-container input {
					    display: none;
					}

					div.content-container .image-variant img {
					    border-radius: 50%;
					    width: 100%;
					    height: auto;
					    margin-bottom: 10px;
					}

					div.content-container .image-variants {
					    width: 100%;
					}

					div.content-container .image-variant {
					    float: left;
					    width: 124px;
					    padding-right: 8px;
					    margin-right: 16px;
					    margin-bottom: 16px;
					    font-size: 10px;
                        text-align: center;
					}

					div.content-container .many-image-variant {
					    float: left;
					    width: 9%;
					    padding-right: 8px;
					    margin-right: 16px;
					    margin-bottom: 16px;
					    font-size: 10px;
                        text-align: center;
					}

					div.content-container .many-image-variant img {
					    border-radius: 50%;
					    width: 100%;
					    height: auto;
					    margin-bottom: 10px;
					}

					div.content-container .lower {
					    display: block;
					    clear:both;
					    float: left;
					    width: 100%;
					}

					div.content-container .lower h2 {
						margin-top: 0;
					}

					.specs .spec:nth-child(odd) {
					    clear: both;
					}

					div.spec {
					    float: left;
					    width: 40%;
					    margin-right: 16px;
					}

					div.test-spec {
						width: 450px !important;
						float: left;
						margin-right: 16px;
					}

					div .spec h4,
					div .test-spec h4 {
						margin-top: 0;
						margin-bottom: 4px;
						color: #ecb21f;
					}

					div .spec p,
					div .test-spec p {
						margin-top: 0;
					}

					.clear {
					    clear:both;
					}
				</style>
			</head>
			<body>
			    <div class="info-column">
			        <img class="logo" src="' . get_stylesheet_directory_uri() . '/assets/images/logo_boxed_1x.png" />

			        <h1>' . shopp('product','get-name') . '</h1>

			        <p>' . shopp('product.get-description') . '</p>

			        <div class="divider"></div>

			        <h3>Features</h3>

			        <ul>' . $features . '</ul>
			        <div class="lower">
                        <div class="specs">' . $specs . '</div>

                        <div class="clear"></div>
					</div>
                </div>

				<div class="content-container">
					' . shopp( 'product.get-pdfvariations', 'mode=single&label=true&defaults=' . __( 'Select an option', 'Shopp') . '&before_menu=&after_menu=' ) . '

					<div class="clear"></div>
				</div>
			</body>
		 </html>';
        if ( isset($_GET['debug']) ) {
	        echo $print_html;
	        die;
        }
		stream_context_set_default(array('ssl' => array('verify_peer' => false)));
		$snappy = new Pdf('/usr/local/bin/wkhtmltopdf.sh');
        $snappy->setOption('orientation', 'Landscape');
        $snappy->setOption('encoding', 'UTF-8');
		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename="' . shopp('product','get-name') . '-specs.pdf"');
		echo $snappy->getOutputFromHtml($print_html);
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
			'lazyload'  => true,
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
			$num_variations = count($prices);
			$variant_class = 'image-variant';

			if ( $num_variations > 42 ) {
				$variant_class = 'many-image-variant';
			}

			foreach ($prices as $pricing) {

				$currently = Shopp::str_true($pricing->sale)?$pricing->promoprice:$pricing->price;
				$disabled = Shopp::str_true($pricing->inventory) && $pricing->stock == 0 ? ' disabled="disabled"' : '';

				$image_id = $wpdb->get_var( $wpdb->prepare("SELECT parent FROM {$wpdb->prefix}shopp_meta as nm WHERE context = 'image' AND type = 'meta' AND name = 'price' AND value = %s", $pricing->id ));

				$image_src = ShoppStorefrontThemeAPI::image($result, array('id' => $image_id, 'width' => '60', 'quality' => '60', 'height' => '60', 'fit' => 'crop','property' => 'src'), $O);
				$src = $image_src;

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

					//error_log($pricing->label . ' ' . $image_id . ' ' . $pricing->id);

					$string .= "<div class='$variant_class'>";

					$string .= '<img src="' . $src . '" />';
					$string .=  $pricing->label;

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

	function get_variation_sort_options() {
		return apply_filters('shopp_variation_sort_options', array(
			'label'  => 'Name',
			'color' => 'Color',
			'times_sampled' => 'Popularity'
		) );
	}

	function get_variation_sort_direction() {
		return apply_filters('shopp_variation_sort_direction', array(
			'label'  => 'ASC',
			'color' => 'ASC',
			'times_sampled' => 'DESC'
		) );
	}
}

$MG_SpecSheet = new MG_SpecSheet();
