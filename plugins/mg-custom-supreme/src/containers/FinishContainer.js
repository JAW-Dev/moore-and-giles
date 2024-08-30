import { bindActionCreators } from "redux";
import { connect } from "react-redux";
import * as actions from "../actions";
import Finish from "../components/steps/Finish";

function mapStateToProps(state) {
	return state;
}

function mapDispatchToProps(dispatch) {
	return bindActionCreators(actions, dispatch);
}

const FinishContainer = connect(mapStateToProps, mapDispatchToProps)(Finish);

export default FinishContainer;
