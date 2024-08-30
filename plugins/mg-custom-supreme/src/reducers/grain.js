function grain(state = [], action) {
	switch (action.type) {
		case "CHANGE_GRAIN":
			return action.grain;
		default:
			return state;
	}
}

export default grain;
