export class QuickviewPagination {
	/**
     * Move the quickview pagination
	 *
	 * @author Jason Witt
	 *
	 * @return void
     */
	public moveElements() {
		jQuery( document ).ajaxComplete( () => {
			jQuery( '#jckqv #jckqv_images' ).on( {
				init: function( event, slick ) {
					const wrapper = jQuery('.jckqv_slider');
					const element = jQuery('<div class="jckqv_slider__pagi"></div>');
					const prev = jQuery('.jckqv-images__arr--prev');
					const next = jQuery('.jckqv-images__arr--next');
					const dots =  jQuery('.slick-dots');

					wrapper.append(element);

					prev.appendTo(element);
					dots.appendTo(element);
					next.appendTo(element);
				}
			} );
		});
	}
}
