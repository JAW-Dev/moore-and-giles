jQuery(document).ready(function () {

	jQuery("form#checkout").bind('shopp_validate', function (e) {

		if (jQuery(":input.error").length > 0) {
			jQuery(e.target).data('error', new Array('We found errors with your submission. Please ensure all required fields have been filled in before submitting.'));

			jQuery('html,body').animate({
				scrollTop: jQuery(':input.error').first().offset().top - 200
			}, 400);

			return false;
		}

		jQuery(":input.article-selector").each(function () {
			if (jQuery(this).val() === '') {
				jQuery(e.target).data('error', new Array('We found errors with your submission. Please ensure each article row has a selection, or remove that row before submitting.'));
				return false;
			}
		});

		jQuery(":input.custom-article-name").each(function () {
			if (jQuery(this).val() === '') {
				jQuery(e.target).data('error', new Array('We found errors with your submission. Please ensure each article row has a selection, or remove that row before submitting.'));
				return false;
			}
		});

		if (jQuery(".row").length == 1 && jQuery(".custom-row").length == 1 && jQuery(".box-row").length == 1 && jQuery(".custom-order-row").length == 1) {
			jQuery(e.target).data('error', new Array('You must specify at least one article before proceeding.'));
		}
	});

	// Manage Postal Code Requirement
	jQuery("#shipping-country").change(function () {
		var countries_without_zip_codes = ["AO", "AG", "AW", "BS", "BZ", "BJ", "BW", "BF", "BI", "CM", "CF", "KM", "CG", "CD", "CK", "CI", "DJ", "DM", "GQ", "ER", "FJ", "TF", "GM", "GH", "GD", "GN", "GY", "HK", "IE", "JM", "KE", "KI", "MO", "MW", "ML", "MR", "MU", "MS", "NR", "AN", "NU", "KP", "PA", "QA", "RW", "KN", "LC", "ST", "SA", "SC", "SL", "SB", "SO", "ZA", "SR", "SY", "TZ", "TL", "TK", "TO", "TT", "TV", "UG", "AE", "VU", "YE", "ZW"];
		var country = jQuery(this).val();

		if (countries_without_zip_codes.indexOf(country) > -1) {
			jQuery("#shipping-postcode").removeClass("required");
		} else {
			jQuery("#shipping-postcode").addClass("required");
		}

	});

	// Validate Address
	jQuery("#validate_shipping_address").click(function (e) {
		jQuery(".verify-address .verify-address-message").slideUp();
		jQuery("#sample_requests .continue-button").addClass("loading");
		jQuery("#sample_requests .continue-button .primary-button").html("Verifying...");

		e.preventDefault();

		var data = {
			action: 'lob_verify_address',
			address: jQuery("#shipping-address").val(),
			xaddress: jQuery("#shipping-xaddress").val(),
			city: jQuery("#shipping-city").val(),
			state: jQuery("[name='shipping[state]']").not(':disabled').val(),
			zip: jQuery("#shipping-postcode").val(),
			country: jQuery("#shipping-country").val()
		};

		jQuery.getJSON(SRHelper.ajaxurl, data, function (response) {
			if (response.hasOwnProperty('deliverability') && response.deliverability !== 'deliverable') {
				jQuery(".verify-address .message").text('Address not found.');
				jQuery(".verify-address .verify-address-message").slideDown();
				jQuery(".verify-address .exception-message, .verify-address .message").show();
			} else {
				if (response.hasOwnProperty('components')) {
					jQuery("#shipping-address").val(response.primary_line);
					jQuery("#shipping-xaddress").val(response.secondary_line);
					jQuery("#shipping-city").val(response.components.city);

					if (response.components.state.length) {
						jQuery("#shipping-state, #shipping-state-menu").val(response.components.state);
					}

					jQuery("#shipping-postcode").val(response.components.zip_code);

					jQuery(".verify-address .exception-message, .verify-address .message").hide();
					jQuery(".verify-address").slideUp();
					jQuery(".locked-area").removeClass('locked-area');
				} else {
					jQuery(".verify-address .message").text('There was an error verifying your address. Please try again.');
					jQuery(".verify-address .verify-address-message").slideDown();
					jQuery(".verify-address .exception-message, .verify-address .message").show();
				}
			}

			jQuery("#sample_requests .continue-button").removeClass("loading");
			jQuery("#sample_requests .continue-button .primary-button").html("Verify Your Address to Continue...");
		});
	});

	// Skip Address Validate
	jQuery("#force_address_validate").click(function (e) {
		e.preventDefault();
		jQuery(".verify-address .exception-message, .verify-address .message").hide();
		jQuery(".verify-address").slideUp();
		jQuery(".locked-area").removeClass('locked-area');
	});

	// Re-enforce Address Validate
	jQuery("#shipping-address, #shipping-xaddress, #shipping-city, #shipping-state, #shipping-state-menu, #shipping-postcode, #shipping-country").bind('keyup change', function () {
		jQuery(".verify-address").slideDown();
		jQuery(".verify-address .exception-message, .verify-address .message").show();
		jQuery(".lockable-area").addClass('locked-area');
	});

	var article_selector_data = {
		placeholder: "Select an Article",
		width: '100%',
		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
			url: SRHelper.ajaxurl,
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					q: params.term, // search term
					page_limit: 100,
					action: 'article_search'
				};
			},
			processResults: function (data, page) {
				// parse the results into the format expected by Select2.
				// since we are using custom formatting functions we do not need to
				// alter the remote JSON data
				return {
					results: data
				};
			},
			cache: true
		},
		escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
		minimumInputLength: 3,
		templateResult: articleFormatResult,
		templateSelection: articleFormatSelection
	};

	jQuery(".article-selector").each(function () {
		jQuery(this).select2(article_selector_data);

		var default_value = jQuery(this).data('default-value');
		if ('' !== default_value) {
			var default_label = jQuery(this).data('default-label');
			var $option = jQuery("<option selected></option>").val(default_value).text(default_label);

			jQuery(this).append($option).trigger('change');
		}
	});

	var sample_selector_data = {
		placeholder: "Select Multiple Articles",
		width: '100%',
		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
			url: SRHelper.ajaxurl,
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					q: params.term, // search term
					page_limit: 100,
					action: 'article_search'
				};
			},
			processResults: function (data, page) {
				// parse the results into the format expected by Select2.
				// since we are using custom formatting functions we do not need to
				// alter the remote JSON data
				return {
					results: data
				};
			},
			cache: true
		},
		escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
		minimumInputLength: 3,
		templateResult: articleFormatResult,
		templateSelection: articleFormatSelection,
		multiple: true,
		closeOnSelect: false
	};

	// Start multi articles select
	var setTargetRow = 1;

	// Setup the select2 field
	jQuery(".sample-selector").select2(sample_selector_data);

	// Function that populates the article rows
	function populateArticleSelect(sampleData) {
		var targetRow = jQuery('.sample-request-form .row[data-row=' + setTargetRow + ']');

		if (targetRow.length === 1) {
			var optionText = sampleData.title + ' - ' + sampleData.label;
			var newOption = jQuery('<option selected="selected">' + optionText + '</option>').val(sampleData.id);

			targetRow.find('.article-selector').append(newOption).trigger('change');

			var addedOption = targetRow.find('.article-selector');

			jQuery(addedOption.children(':selected')).attr('title', sampleData.title);

			setTargetRow++;
		}
	}

	// Added multi article section to article row.
	jQuery(".sample-selector").on('select2:select', function (e) {
		var sampleData = e.params.data;
		var targetRow = jQuery('.sample-request-form .row[data-row=' + setTargetRow + ']');

		// If article row doesn't exist, add it
		if (targetRow.length === 0) {
			var new_row = SRHelper.row_html;

			new_row = new_row.replace(/row_number/g, setTargetRow);
			jQuery(new_row).insertAfter("div.row:last");

			jQuery("div.row:last").find(".article-selector").select2(article_selector_data);
		}

		// Populate the article row Select.
		populateArticleSelect(sampleData);
	});

	// When closing multi articles dropdown clear the selections
	jQuery(".sample-selector").on('select2:close', function (e) {
		jQuery(this).select2('val', '');
	});
	// End multi articles select

	jQuery("a.add-row").click(function (e) {
		var new_row = SRHelper.row_html;
		var row_count = jQuery("div.row:last").data('row');
		row_count = parseInt(row_count);

		if (row_count > 0) {
			row_count = row_count + 1;
		} else {
			row_count = 1;
		}

		new_row = new_row.replace(/row_number/g, row_count);
		jQuery(new_row).insertAfter("div.row:last");

		jQuery("div.row:last").find(".article-selector").select2(article_selector_data);

		e.preventDefault();
	});

	jQuery("a.add-custom-row").click(function (e) {
		jQuery(".custom-row.headings").show();

		var new_row = SRHelper.custom_row_html;
		var row_count = jQuery("div.custom-row:last").data('row');
		row_count = parseInt(row_count);

		if (row_count > 0) {
			row_count = row_count + 1;
		} else {
			row_count = 1;
		}

		new_row = new_row.replace(/row_number/g, row_count);
		jQuery(new_row).insertAfter("div.custom-row:last");

		e.preventDefault();
	});

	jQuery("a.add-custom-order-row").click(function (e) {
		jQuery(".custom-order-row.headings").show();

		var new_row = SRHelper.custom_order_row_html;
		var row_count = jQuery("div.custom-order-row:last").data('row');
		row_count = parseInt(row_count);

		if (row_count > 0) {
			row_count = row_count + 1;
		} else {
			row_count = 1;
		}

		new_row = new_row.replace(/row_number/g, row_count);
		jQuery(new_row).insertAfter("div.custom-order-row:last");

		//jQuery("div.custom-order-row:last").find(".article-selector").select2(article_selector_data);

		e.preventDefault();
	});

	jQuery("a.add-custom-box").click(function (e) {
		jQuery(".box-row.headings").show();

		var new_row = SRHelper.box_row_html;
		var row_count = jQuery("div.box-row:last").data('row');
		row_count = parseInt(row_count);

		if (row_count > 0) {
			row_count = row_count + 1;
		} else {
			row_count = 1;
		}

		new_row = new_row.replace(/row_number/g, row_count);
		jQuery(new_row).insertAfter("div.box-row:last");

		e.preventDefault();
	});

	jQuery(document).on('click', 'a.remove-row', function () {
		jQuery(this).parents("div.row").remove();
	});

	jQuery(document).on('click', 'a.remove-custom-row', function () {
		jQuery(this).parents("div.custom-row").remove();
	});

	jQuery(document).on('click', 'a.remove-custom-order-row', function () {
		jQuery(this).parents("div.custom-order-row").remove();
	});

	jQuery(document).on('click', 'a.remove-box-row', function () {
		jQuery(this).parents("div.box-row").remove();
	});

	jQuery(document).on('change', "input[type=radio].size-selector", function () {
		if (!jQuery(this).is(':checked')) return;

		var size_other = jQuery(this).parents("div.row, div.custom-row").find("input.size-other");

		if (jQuery(this).hasClass('other-selector')) {
			size_other.prop('disabled', false);
		} else {
			size_other.prop('disabled', true);
		}
	});

	jQuery("input.order-data-shipping-method").change(function () {
		if (jQuery(this).val() == "Overnight") {
			jQuery("#shipping_number_container").show();
		} else {
			jQuery("#shipping_number_container").hide();
		}
	});

	jQuery("#order-data-_selected_customer_address").select2({
		placeholder: "Select a Customer",
		minimumInputLength: 2,
		width: '100%',
		ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
			url: SRHelper.ajaxurl,
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					q: params.term, // search term
					page_limit: 100,
					action: 'customer_search'
				};
			},
			processResults: function (data, page) { // parse the results into the format expected by Select2.

				//data.unshift({id: 'new', text: '-- New Customer --'});
				return { results: data };
			},
			cache: true,
		},
	}).change(function (e) {
		disableShippingFields();

		var data = {
			action: 'change_customer',
			customer: this.value,
			recipient: jQuery(".order-data-intended-recipient:checked").val()
		};

		jQuery.getJSON(SRHelper.ajaxurl, data, function (response) {

			jQuery("#order-data-shipping-phone").val(response.phone);
			jQuery("#order-data-recipient-email").val(response.email);
			jQuery("#order-data-recipient-company").val(response.company);
			jQuery("#shipping-name").val(response.shippingname);
			jQuery("#shipping-address").val(response.shippingaddress);
			jQuery("#shipping-xaddress").val(response.shippingxaddress);
			jQuery("#shipping-city").val(response.shippingcity);
			jQuery("#shipping-country").val(response.shippingcountry).trigger('change');
			jQuery("#shipping-state, #shipping-state-menu").val(response.shippingstate);
			jQuery("#shipping-postcode").val(response.shippingpostcode);

			enableShippingFields();
		});
	});

	var xhr_request = null;

	jQuery("input.order-data-intended-recipient").change(function () {

		disableShippingFields();

		if (xhr_request !== null) xhr_request.abort();

		var data = null;

		if (jQuery(this).val() == "Yourself") {
			jQuery(".selected-customer-address-container").hide();

			data = {
				action: 'change_customer',
				customer: 'agent',
				recipient: jQuery(this).val()
			};

			xhr_request = jQuery.getJSON(SRHelper.ajaxurl, data, function (response) {
				xhr_request = null;

				jQuery("#order-data-shipping-phone").val(response.phone);
				jQuery("#order-data-recipient-email").val(response.email);
				jQuery("#order-data-recipient-company").val(response.company);
				jQuery("#shipping-name").val(response.shippingname);
				jQuery("#shipping-address").val(response.shippingaddress);
				jQuery("#shipping-xaddress").val(response.shippingxaddress);
				jQuery("#shipping-city").val(response.shippingcity);
				jQuery("#shipping-country").val(response.shippingcountry).trigger('change');
				jQuery("#shipping-state, #shipping-state-menu").val(response.shippingstate);
				jQuery("#shipping-postcode").val(response.shippingpostcode);

				enableShippingFields();
			});

		} else if (jQuery(this).val() == "Existing Customer") {
			jQuery(".selected-customer-address-container").show();

			data = {
				action: 'change_customer',
				customer: jQuery("#order-data-_selected_customer_address").val(),
				recipient: jQuery(this).val()
			};

			xhr_request = jQuery.getJSON(SRHelper.ajaxurl, data, function (response) {
				xhr_request = null;

				jQuery("#order-data-shipping-phone").val(response.phone);
				jQuery("#order-data-recipient-email").val(response.email);
				jQuery("#order-data-recipient-company").val(response.company);
				jQuery("#shipping-name").val(response.shippingname);
				jQuery("#shipping-address").val(response.shippingaddress);
				jQuery("#shipping-xaddress").val(response.shippingxaddress);
				jQuery("#shipping-city").val(response.shippingcity);
				jQuery("#shipping-country").val(response.shippingcountry).trigger('change');
				jQuery("#shipping-state, #shipping-state-menu").val(response.shippingstate);
				jQuery("#shipping-postcode").val(response.shippingpostcode);

				enableShippingFields();
			});
		} else {
			jQuery(".selected-customer-address-container").hide();

			data = {
				action: 'change_customer',
				customer: 'new',
				recipient: jQuery(this).val()
			};

			xhr_request = jQuery.getJSON(SRHelper.ajaxurl, data, function (response) {
				xhr_request = null;

				jQuery("#order-data-shipping-phone").val('');
				jQuery("#order-data-recipient-email").val('');
				jQuery("#order-data-recipient-company").val('');
				jQuery("#shipping-name").val('');
				jQuery("#shipping-address").val('');
				jQuery("#shipping-xaddress").val('');
				jQuery("#shipping-city").val('');
				jQuery("#shipping-state, #shipping-state-menu").val('');
				jQuery("#shipping-postcode").val('');
				jQuery("#shipping-country").val('');

				enableShippingFields();
			});
		}
	}).filter(':checked').trigger('change');
});

function disableShippingFields() {
	jQuery("#order-data-shipping-phone").prop('disabled', true);
	jQuery("#order-data-recipient-email").prop('disabled', true);
	jQuery("#order-data-recipient-company").prop('disabled', true);
	jQuery("#shipping-name").prop('disabled', true);
	jQuery("#shipping-address").prop('disabled', true);
	jQuery("#shipping-xaddress").prop('disabled', true);
	jQuery("#shipping-city").prop('disabled', true);
	jQuery("#shipping-country").prop('disabled', true);
	jQuery("#shipping-state, #shipping-state-menu").prop('disabled', true);
	jQuery("#shipping-postcode").prop('disabled', true);
}

function enableShippingFields() {
	jQuery("#order-data-shipping-phone").prop('disabled', false);
	jQuery("#order-data-recipient-email").prop('disabled', false);
	jQuery("#order-data-recipient-company").prop('disabled', false);
	jQuery("#shipping-name").prop('disabled', false);
	jQuery("#shipping-address").prop('disabled', false);
	jQuery("#shipping-xaddress").prop('disabled', false);
	jQuery("#shipping-city").prop('disabled', false);
	jQuery("#shipping-country").prop('disabled', false);
	jQuery("#shipping-state, #shipping-state-menu").prop('disabled', false);
	jQuery("#shipping-postcode").prop('disabled', false);
}

function articleFormatResult(article) {
	if (typeof (article.title) == "undefined") return "";

	var markup = "<div class='article-result' style='background: url(" + article.thumb + "); background-size: cover;'><tr>";
	markup += "<div class='article-info'>";
	markup += "<p class='article-title'>" + article.title + "</p>";
	markup += "<p class='article-label'>" + article.label + "</p>";
	markup += "</div>";
	markup += "</div>";
	return markup;
}

function articleFormatSelection(article) {
	if (typeof (article.title) == "undefined" || article.title === '') return article.text;

	return article.title + ' - ' + article.label;
}
