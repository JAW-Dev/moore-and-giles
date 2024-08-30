const datepickerInit = () => {
	jQuery(() => {
		jQuery('input[name="date_from"], input[name="date_to"]').datepicker();
	});
}

export default datepickerInit;
