import { bindActionCreators } from "redux";
import { connect } from "react-redux";
import * as actions from "../actions";
import App from "../components/App";

function mapStateToProps(state) {
	return {
		state
	};
}

function mapDispatchToProps(dispatch) {
	return bindActionCreators(actions, dispatch);
}

const AppContainer = connect(mapStateToProps, mapDispatchToProps)(App);

export default AppContainer;
