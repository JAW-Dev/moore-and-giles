function finish(state = [], action) {
	switch (action.type) {
		case "CHANGE_FINISH":
			return action.finish;
		default:
			return state;
	}
}

export default finish;
