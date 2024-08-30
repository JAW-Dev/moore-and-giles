jQuery(document).ready(function (e) {
	jQuery(document).on('change', '#countrySelect', () => {
		jQuery('[name="update_cart"]').trigger("click");
	});
});