function cmyk(state = [], action) {
	switch (action.type) {
		case "CHANGE_CMYK":
			return action.cmyk;
		default:
			return state;
	}
}

export default cmyk;
