export class Serialize {
	/**
     * Serialize From Data.
	 * 
	 * @author Jason Witt
	 * 
	 * @param string form The form.
     */
	static serializeForm(form: HTMLFormElement): string {
		let serialized = [];
		// Loop through each field in the form
		for (let i = 0; i < form.elements.length; i++) {
			let field = (form.elements[i] as HTMLFormElement );
			// Don't serialize fields without a name, submits, buttons, file and reset inputs, and disabled fields
			if (!field.name || field.disabled || field.type === 'file' || field.type === 'reset' || field.type === 'submit' || field.type === 'button') {
				continue;
			}
			// Convert field data to a query string
			if ((field.type !== 'checkbox' && field.type !== 'radio') || field.checked) {
				serialized.push(encodeURIComponent(field.name) + "=" + encodeURIComponent(field.value));
			}
		}
		return serialized.join('&');
	}
}
