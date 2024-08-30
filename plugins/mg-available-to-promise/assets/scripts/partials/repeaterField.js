/* global jQuery */

const setup = (fields, i) => {
	jQuery.each(fields, function () { // eslint-disable-line
		const sibling = jQuery(this).prev('tr');
		if (sibling) {
			jQuery(sibling)
				.find('.repeatable-field-add')
				.css('display', 'none');
			jQuery(sibling)
				.find('.repeatable-field-remove')
				.css('display', 'inline');
		}
		if (i === 1) {
			jQuery(this)
				.find('.repeatable-field-remove')
				.css('display', 'none');
		}
	});
};

const addField = (wrapper, i) => {
	const markup = `
	<tr id="field_group_${i}" data-index="${i}">
		<td>
			<input type="text" class="datepicker" name="_mg_atp_settings[${i}][from_date]" value="" placeholder="From">
		</td>
		<td>
			<input type="text" class="datepicker" name="_mg_atp_settings[${i}][to_date]" value="" placeholder="To">
		</td>
		<td class="buttons">
			<button class="remove repeatable-field-remove"></button>
			<button class="add repeatable-field-add"></button>
		</td>
	</tr>`;
	jQuery(markup).appendTo(wrapper);
};

const updateFields = wrapper => {
	const newFields = jQuery(wrapper).find('tr');
	jQuery.each(newFields, function (index, element) { // eslint-disable-line
		jQuery(this).attr('data-index', index);
	});
};

const repeaterField = () => {
	const wrapper = jQuery('#repeatable-fields-list');
	const fields = jQuery(wrapper).find('tr');
	let i = fields.size();

	setup(fields, i);

	jQuery(document.body).on('click', '.repeatable-field-add', function () { // eslint-disable-line
		addField(wrapper, i);
		i++; // eslint-disable-line
		jQuery(this).parent().find('.repeatable-field-add').css('display', 'none'); // prettier-ignore
		jQuery(this).parent().find('.repeatable-field-remove').css('display', 'inline'); // prettier-ignore
		jQuery('.datepicker').datepicker();
		return false;
	});

	jQuery(document.body).on('click', '.repeatable-field-remove', function () { // eslint-disable-line
		i--; // eslint-disable-line
		updateFields(wrapper);

		const parent = jQuery(this).parent().parent(); // prettier-ignore
		const sibling = parent.prev('tr');
		const siblingIndex = parseInt(jQuery(sibling).attr('data-index'), 10);
		const siblingCheck = sibling && siblingIndex === i - 1;

		parent.remove();

		if (siblingCheck) {
			jQuery(sibling).find('.repeatable-field-add').css('display', 'inline'); // prettier-ignore
		}

		if (i === 1) {
			jQuery(wrapper).find('tr').find('.repeatable-field-remove').css('display', 'none'); // prettier-ignore
			jQuery(wrapper).find('tr').find('.repeatable-field-add').css('display', 'inline'); // prettier-ignore
		}

		return false;
	});
};

export default repeaterField;
