import fetch from "isomorphic-fetch";
import base64 from "base-64";

export const requestData = data => {
	return {
		type: "REQUEST_DATA",
		data
	};
};

export const receiveData = json => {
	return {
		type: "RECEIVE_DATA",
		json
	};
};

export const fetchData = () => {
	return function(dispatch) {
		dispatch(requestData());
		let headers = new Headers();
		headers.append(
			"Authorization",
			"Basic " + base64.encode("guest" + ":" + "access")
		);

		return fetch("/wp-json/mg/v1/supreme", {
			method: "GET",
			headers: headers
		})
			.then(
				response => response.json(),
				error => console.log("An error occured.", error)
			)
			.then(json => dispatch(receiveData(json)));
	};
};

export const changeHex = hex => {
	return {
		type: "CHANGE_HEX",
		hex
	};
};

export const changeRgb = rgb => {
	return {
		type: "CHANGE_RGB",
		rgb
	};
};

export const changeCmyk = cmyk => {
	return {
		type: "CHANGE_CMYK",
		cmyk
	};
};

export const changeGrain = grain => {
	return {
		type: "CHANGE_GRAIN",
		grain
	};
};

export const changeFinish = finish => {
	return {
		type: "CHANGE_FINISH",
		finish
	};
};

export const updateProgress = progress => {
	return {
		type: "UPDATE_PROGRESS",
		progress
	};
};
