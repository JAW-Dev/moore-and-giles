/* global document */

const toggleTab = (tabID, type) => {
	const couponType = document.getElementById('coupon_type');
	const couponTypeValue = couponType.options[couponType.selectedIndex].value;
	const tab = document.querySelector(tabID);

	// Hide tab by default
	tab.style.display = 'none';

	// If coupon type is set to gift-wrapping show the tab.
	if (couponTypeValue === type) {
		tab.style.display = 'block';
	}

	// Watch for changes on the coupon type select
	couponType.addEventListener('input', e => {
		const selectValue = e.target.value;

		// If coupon type is selected as gift-wrapping show the tab.
		if (selectValue === type) {
			tab.style.display = 'block';
		} else {
			tab.style.display = 'none';
		}
	});
};

export default toggleTab;
