import { bindActionCreators } from "redux";
import { connect } from "react-redux";
import * as actions from "../actions";
import Grain from "../components/steps/Grain";

function mapStateToProps(state) {
	return state;
}

function mapDispatchToProps(dispatch) {
	return bindActionCreators(actions, dispatch);
}

const GrainContainer = connect(mapStateToProps, mapDispatchToProps)(Grain);

export default GrainContainer;
