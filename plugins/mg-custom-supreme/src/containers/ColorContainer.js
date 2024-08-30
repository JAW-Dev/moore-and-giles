import { bindActionCreators } from "redux";
import { connect } from "react-redux";
import * as actions from "../actions";
import ColorStep from "../components/steps/Color";

function mapStateToProps(state) {
	return state;
}

function mapDispatchToProps(dispatch) {
	return bindActionCreators(actions, dispatch);
}

const ColorContainer = connect(mapStateToProps, mapDispatchToProps)(ColorStep);

export default ColorContainer;
