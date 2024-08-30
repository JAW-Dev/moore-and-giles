function rgb(state = [], action) {
	switch (action.type) {
		case "CHANGE_RGB":
			return action.rgb;
		default:
			return state;
	}
}

export default rgb;
