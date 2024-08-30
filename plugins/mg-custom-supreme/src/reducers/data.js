function data(
	state = {
		isFetching: false,
		color: {},
		grain: {},
		finish: {},
		progress: {}
	},
	action
) {
	switch (action.type) {
		case "REQUEST_DATA":
			return Object.assign({}, state, {
				isFetching: true
			});
		case "RECEIVE_DATA":
			return Object.assign({}, state, {
				isFetching: false,
				color: {
					...action.json.color
				},
				grain: {
					...action.json.grain
				},
				finish: {
					...action.json.finish
				},
				progress: {
					...action.json.progress
				}
			});
		default:
			return state;
	}
}

export default data;
