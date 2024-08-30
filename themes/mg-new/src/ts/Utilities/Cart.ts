// Import Core Modules
import { Tooltips } from "./Tooltips";

export class Cart {
	public init(): void {
		jQuery( document.body ).on( 'updated_wc_div', function() {
			jQuery( '#mg_cart_wrap' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			} );
		} );

		jQuery(document.body).on('wc_fragments_refreshed wc_fragments_loaded', function() {
			jQuery( '#mg_cart_wrap' ).unblock();
		});

		jQuery(document.body).on('updated_wc_div wc_fragments_refreshed wc_fragments_loaded removed_from_cart', function() {
			Tooltips.init();
		});
	}

	/**
	 * Toggle the sidebar cart.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	sidebarCartToggle(): void {
		let menuBtn: JQuery = jQuery(".cart.menu-item");
		let container: JQuery = jQuery(".sidebar-cart");

		let openSidebarCart: () => void = () => {
			jQuery(container).addClass("opened");
			jQuery("body").css("overflow", "hidden");
		};

		if ( jQuery( container ).hasClass("opened") ) {
			jQuery("body").css("overflow", "hidden");
		} else {
			jQuery("body").css("overflow", "auto");
		}

		let closeSidebarCart: () => void = () => {
			jQuery(container).removeClass("opened");
			jQuery("body").css("overflow", "auto");
		};

		menuBtn.on("click", ( e ) => {
			e.preventDefault();
			openSidebarCart();
		} );

		jQuery( document.body ).on( 'click', '.sidebar-cart__close', function() {
			closeSidebarCart();
		} );
	}

	updateCartOnQtyChange(): void {
		jQuery(document).on(
			"change",
			".sidebar-cart__container input.qty",
			function() {
				jQuery("button[name=update_cart]")
					.prop("disabled", false)
					.trigger("click");
			}
		);
	}
}
