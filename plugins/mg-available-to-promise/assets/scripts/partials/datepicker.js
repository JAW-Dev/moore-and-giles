/* global jQuery, document */

const datepicker = () => {
	jQuery(document).ready(() => {
		jQuery('.datepicker').datepicker();
	});
};

export default datepicker;
