import React from "react";
import ReactDOM from "react-dom";
import thunkMiddleware from "redux-thunk";
import { createLogger } from "redux-logger";
import { Provider } from "react-redux";
import { createStore, applyMiddleware } from "redux";
import AppContainer from "./containers/AppContainer.js";
import rootReducer from "./reducers";
import { fetchData } from "./actions";

const loggerMiddleware = createLogger();

const defaultState = {
	hex: "#7DC0D1",
	rgb: "rgb(125, 192, 209)",
	cmyk: "40,8,0,18",
	grain: "Soho",
	finish: "Satin Sheen"
};

const store = createStore(
	rootReducer,
	defaultState,
	// window.__REDUX_DEVTOOLS_EXTENSION__ &&
	// 	window.__REDUX_DEVTOOLS_EXTENSION__(),
	applyMiddleware(thunkMiddleware, loggerMiddleware)
);

store.dispatch(fetchData());

ReactDOM.render(
	<Provider store={store}>
		<AppContainer />
	</Provider>,
	document.getElementById("root")
);
