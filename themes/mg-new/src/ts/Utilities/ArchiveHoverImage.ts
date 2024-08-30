export class ArchiveHoverImage {

	/**
	 * Add classes to the prev and next pagination links.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public init(): void {
		jQuery( document.body ).on( 'wvs-items-updated', function( event ) {
			if ( ! jQuery( document.body ).hasClass('archive') || ! jQuery( document.body ).hasClass('woocommerce-page') ) {
				return;
			}

			setTimeout( () => {
				let selected_variant = jQuery( event.target ).find( 'ul[data-attribute_name="attribute_pa_color"] li.selected' ).data('value');

				if ( ! selected_variant ) {
					selected_variant = jQuery( event.target ).find( 'li.selected' ).data('value');
				}

				let variations = jQuery( event.target ).closest( '.variations_form' ).data( 'product_variations' );

				Object.keys(variations).forEach( function( key ) {
					if ( variations[ key ].attributes.attribute_pa_color == selected_variant ) {
						jQuery( event.target ).closest( 'article' ).find( '.media-figure__image--hover').first().attr( 'src', variations[ key ].hover_image.gallery_thumbnail_src ).attr( 'srcset', '' );
					}
				} );
			}, 50 );
		} );
	}
}
