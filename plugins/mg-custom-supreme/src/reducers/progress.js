function progress(state = {}, action) {
	switch (action.type) {
		case "UPDATE_PROGRESS":
			return { ...state, [action.progress]: true };
		default:
			return state;
	}
}

export default progress;
