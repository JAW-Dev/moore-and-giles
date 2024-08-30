export class CategoryPagination {

	/**
	 * Add classes to the prev and next pagination links.
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public clases(): void {
		jQuery(document).on('facetwp-loaded', function() {
			jQuery('.page-numbers .prev').parent().addClass('previous-link');
			jQuery('.page-numbers .next').parent().addClass('next-link');
		})
	}
}
