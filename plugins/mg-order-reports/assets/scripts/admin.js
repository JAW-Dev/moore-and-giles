/* global jQuery, document, ajaxurl */

import jsonexport from 'jsonexport';

jQuery(() => {
	jQuery('input[name="date_from"], input[name="date_to"]').datepicker();
	const button = jQuery('#download-report');

	const parameters = {};
	const searchString = location.search.substr(1);
	const pairs = searchString.split('&');
	let parts;
	for (let i = 0; i < pairs.length; i++) {
		parts = pairs[i].split('=');
		const name = parts[0];
		const data = decodeURI(parts[1]);
		parameters[name] = data;
	}

	jQuery(button).on('click', e => {
		e.preventDefault();
		const nonce = jQuery(button).attr('data-nonce');

		jQuery.ajax({
			type: 'post',
			dataType: 'json',
			url: ajaxurl,
			data: {
				action: 'build_report_csv',
				nonce,
				'date_from': ('date_from' in parameters) ? parameters.date_from : '', // prettier-ignore
				'date_to': ('date_to' in parameters) ? parameters.date_to : '', // prettier-ignore
				'addon': ('addon' in parameters) ? parameters.addon: '' // prettier-ignore
			},
			success(response) {
				if (response) {
					const data = JSON.parse(response);

					const options = { rename: ['Order ID', 'First Name', 'Last Name', 'Personalization', 'Gift Wrapping', 'Email', 'Date'] };

					jsonexport(data, options, (err, csv) => {
						if (err) {
							return console.log(err); // eslint-disable-line
						}

						const today = new Date();
						const date = `${today.getFullYear()}-${today.getMonth() + 1}-${today.getDate()}`;

						const hiddenElement = document.createElement('a');
						hiddenElement.href = `data:text/csv;charset=utf-8,${encodeURI(csv)}`;
						hiddenElement.target = '_blank';
						hiddenElement.download = `order-report-${date}.csv`;
						hiddenElement.click();
					});
				}
			},
			error(response) {
				console.log('Error', response); // eslint-disable-line
			}
		});
	});
});
