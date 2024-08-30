<?php
/*
Plugin Name: MG Criteo
Plugin URI: http://cgd.io
Description:  Add Criteo tags to MG.
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

class MG_Criteo {
	public function __construct() {
		add_action('shopp_init', function() {
			if ( current_user_can('mg_impersonate_user') ) return;
			add_action('wp_head', array($this, 'criteo_global') );
			add_action('wp_footer', array($this, 'criteo_single') );
			add_filter('shopp_themeapi_cart_criteotag', array($this, 'cart_tag'), 10, 3);
		});
	}

	function criteo_global() {
		echo '<script type="text/javascript" src="//static.criteo.net/js/ld/ld.js" async="true"></script>' . PHP_EOL;
	}

	function criteo_single() {
		$email = '';

		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			$email = $current_user->user_email;
		}

		// FRONT PAGE
		if ( is_front_page() ): ?>
		<script type="text/javascript">
		var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";

		if ( deviceType == "d" ) {
			window.criteo_q = window.criteo_q || [];
			window.criteo_q.push(
			{ event: "setAccount", account: 23332 },
			{ event: "setEmail", email: "<?php echo $email; ?>" },
			{ event: "setSiteType", type: deviceType },
			{ event: "viewHome" }
			);
		}
		</script>
		<?php endif;

		// COLLECTION PAGE
		if ( is_shopp_collection() || ShoppCollection() !== null ) {
			ShoppCollection()->load( array('order' => 'bestselling','show' => 3) );

			if ( count(ShoppCollection()->products) > 0 ) {
				$ids = array();
				while( shopp('collection.products') ) {
					$ids[] = '"' . shopp('product.get-id') . '"';
				}
				?>
				<script type="text/javascript">
				var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
				window.criteo_q = window.criteo_q || [];
				window.criteo_q.push(
				{ event: "setAccount", account: 23332 },
				{ event: "setEmail", email: "<?php echo $email; ?>" },
				{ event: "setSiteType", type: deviceType },
				{ event: "viewList", item:[ <?php echo join(",", $ids); ?> ]}
				);
				</script>
				<?php
			}
		}

		// PRODUCT PAGE
		if ( is_shopp_product() ) {
			?>
			<script type="text/javascript">
			var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
			window.criteo_q = window.criteo_q || [];
			window.criteo_q.push(
			{ event: "setAccount", account: 23332 },
			{ event: "setEmail", email: "<?php echo $email; ?>" },
			{ event: "setSiteType", type: deviceType },
			{ event: "viewItem", item: "<?php shopp('product.id'); ?>" }
			);
			</script>
			<?php
		}

		if  ( is_shopp_thanks_page() ) {
			?>
			<script type="text/javascript">
			var deviceType = /iPad/.test(navigator.userAgent) ? "t" : /Mobile|iP(hone|od)|Android|BlackBerry|IEMobile|Silk/.test(navigator.userAgent) ? "m" : "d";
			window.criteo_q = window.criteo_q || [];
			window.criteo_q.push(
			{ event: "setAccount", account: 23332 },
			{ event: "setEmail", email: "<?php echo $email; ?>" },
			{ event: "setSiteType", type: deviceType },
			{ event: "trackTransaction", id: "<?php shopp('purchase.id'); ?>", item: [
				<?php while ( shopp( 'purchase.items' ) ): ?>
				{ id: "<?php shopp('purchase.item-product'); ?>", price: <?php echo shopp('purchase.item-unit-price','money=off'); ?>, quantity: <?php shopp('purchase.item-quantity'); ?> },
				<?php endwhile; ?>
			]}
			);
			</script>
			<?php
		}
	}

	function cart_tag($result, $options, $Cart) {
		if ( $_REQUEST['ajax'] == "true" && shopp('cart','has-items') ):
			ob_start();
			$email = '';

			if ( is_user_logged_in() ) {
				$current_user = wp_get_current_user();
				$email = $current_user->user_email;
			}
			?>
			<div id="criteo_event" style="display:none;">
			window.criteo_q.push({ event: "setAccount", account: 23332 },
			{ event: "setEmail", email: "<?php echo $email; ?>" },
			{ event: "setSiteType", type: deviceType },
			{ event: "viewBasket", item: [
				<?php while(shopp('cart','items')): ?>
				{ id: "<?php shopp('cartitem.product'); ?>", price: <?php shopp('cartitem.unitprice','money=off'); ?>, quantity: <?php shopp('cartitem.quantity'); ?> },
				<?php endwhile; ?>
			]}
			);
			</div>
			<?php
			$result = ob_get_clean();
		endif;

		return $result;
	}
}

$MG_Criteo = new MG_Criteo();
