function hex(state = [], action) {
	switch (action.type) {
		case "CHANGE_HEX":
			return action.hex;
		default:
			return state;
	}
}

export default hex;
