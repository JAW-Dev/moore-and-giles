import { bindActionCreators } from "redux";
import { connect } from "react-redux";
import * as actions from "../actions";
import Progress from "../components/Progress";

function mapStateToProps(state) {
	return state;
}

function mapDispatchToProps(dispatch) {
	return bindActionCreators(actions, dispatch);
}

const ProgressContainer = connect(mapStateToProps, mapDispatchToProps)(
	Progress
);

export default ProgressContainer;
