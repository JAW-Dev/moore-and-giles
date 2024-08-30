import { combineReducers } from "redux";
import hex from "./hex";
import rgb from "./rgb";
import cmyk from "./cmyk";
import grain from "./grain";
import finish from "./finish";
import progress from "./progress";
import data from "./data";

const rootReducer = combineReducers({
	data,
	hex,
	rgb,
	cmyk,
	grain,
	finish,
	progress
});

export default rootReducer;
