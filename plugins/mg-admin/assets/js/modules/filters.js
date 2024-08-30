const filters = () => {
	const catagoryFilter = document.getElementById('filter-cat');

	catagoryFilter.addEventListener('change', (e) => {
		window.location.search += `&cat-filter=${e.target.value}`;
	})
}

export default filters;
