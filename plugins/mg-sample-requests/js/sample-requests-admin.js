jQuery(window).load(function() {
	// Set our initial load state, yo!
	jQuery("tbody .check-column :checkbox").each( function(index, element) {
		jQuery(this).attr('initial_checked_state', jQuery(this).prop('checked') );
	});

	// Handle bulk check ourselves
	jQuery( document.body ).on( 'click.wp-toggle-checkboxes', 'thead .check-column :checkbox, tfoot .check-column :checkbox', function( event ) {
		var $this = jQuery(this),
			$table = $this.closest( 'table' ),
			controlChecked = $this.prop('checked'),
			toggle = event.shiftKey || $this.data('wp-toggle');

		$table.children( 'tbody' ).filter(':visible')
			.children().children('.check-column').find(':checkbox')
			.prop('checked', function() {
				if ( jQuery(this).is(':hidden,:disabled') ) {
					return false;
				}

				if ( toggle ) {
					return ! jQuery(this).prop( 'checked' );
				} else if ( controlChecked ) {
					return true;
				}

				return false;
			});

		// Trigger changed if it really changed
		$table.children( 'tbody' ).filter(':visible').children().children('.check-column').find(':checkbox').each( function( index, element ) {
			if ( ( jQuery(this).attr('initial_checked_state') == "true" && ! jQuery(this).is(':checked') ) ||
			     ( jQuery(this).attr('initial_checked_state') == "false" && jQuery(this).is(':checked') )  ||
			     ! jQuery(this).is('[initial_checked_state]') ) {
				jQuery(this).removeAttr('initial_checked_state');
				jQuery(this).trigger('change');
			}
		});
	});

	jQuery("input.backordered-check").change(function(){
		var element  = jQuery(this);
		var status = 'unbackordered';
		var item = jQuery(this).val();
        var backordered_count = jQuery("input.backordered-check:checked").length;
        var item_count = jQuery("input.picked-check").length;
        var order_is_backordered = 'no';

        if ( backordered_count == item_count ) {
            order_is_backordered = 'yes';
		}

		if ( jQuery(this).is(':checked') ) {
			status = 'backordered';
		} else {
			status = 'unbackordered';
		}

		element.prop('disabled','disabled');

		var data = {
			action: 'update_backordered',
            purchase: jQuery(this).data('purchase'),
			purchased: item,
			backordered_status: status,
			all_items_backordered: order_is_backordered
		};

		// We can also pass the url value separately from ajaxurl for front end AJAX implementations
		jQuery.post(ajaxurl, data, function(response) {
			element.removeProp('disabled');

			if ( response != 1 ) {
				alert('There was an error updating that item.');
			}
		});
	});


	jQuery("input.picked-check").change(function() {
		var element  = jQuery(this);
		var item = jQuery(this).val();
		var status = 'picked';
		var backordered_count = jQuery("input.backordered-check:checked").length;
		var item_count = jQuery("input.picked-check").length;
		var item_picked_count = jQuery('input.picked-check:checked').length;
		var finished = 'yes';

		if ( (item_count - backordered_count) > item_picked_count) {
			finished = 'no';
		}

		if ( jQuery(this).is(':checked') ) {
			status = 'picked';
		} else {
			status = 'unpicked';
		}

		element.prop('disabled','disabled');

		var data = {
			action: 'update_picked',
			purchase: jQuery(this).data('purchase'),
			purchased: item,
			picked_status: status,
			finished_picking: finished
		};

		// We can also pass the url value separately from ajaxurl for front end AJAX implementations
		jQuery.post(ajaxurl, data, function(response) {
			element.removeProp('disabled');

			if ( response != 1 ) {
				alert('There was an error updating that item.');
			}
		});
	});
});
