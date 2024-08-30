// Declare Global variables
declare let mgData;

export class CommentsLoadMore {
	/**
     * Comments Load More
	 *
	 * @author Jason Witt
	 *
	 * @return void
     */
    public static init(): void {
		jQuery(document).on('click', '.more-reviews-btn', function(event) {
			event.preventDefault();
			const button = jQuery(this);
			jQuery.ajax({
				url : mgData.mgProductCommentData.adminAjax,
				data : {
					'action': 'comments_load_more_response',
					'post_id': mgData.mgProductCommentData.singleProductID,
					'cpage' : mgData.mgProductCommentData.commentsCurrentPage,
					'total_pages': mgData.mgProductCommentData.totalCommentPages
				},
				type : 'POST',
				beforeSend: function () {
					button.text('Loading...');
				},
				success: function(response){
					if (response) {
						mgData.mgProductCommentData.commentsCurrentPage++
						jQuery('ol.commentlist').append(response);
						button.text('More Reviews');

						if (mgData.mgProductCommentData.commentsCurrentPage >= mgData.mgProductCommentData.totalCommentPages) {
							button.remove();
						}
					} else {
						button.remove();
					}
				}
			});
			return false;
		});
	}
}
