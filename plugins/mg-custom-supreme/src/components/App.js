import React from "react";
import Loading from "./Loading";
import ColorContainer from "../containers/ColorContainer";
import GrainContainer from "../containers/GrainContainer";
import FinishContainer from "../containers/FinishContainer";
import ProgressContainer from "../containers/ProgressContainer";
import grain from "../reducers/grain";
import finish from "../reducers/finish";
import axios from "axios";
import data from "../reducers/data";

class App extends React.Component {
	componentWillMount() {
		jQuery(".cs-swatch").css("background", this.props.state.hex);
		jQuery("#cs-form-review-grain .cs-form-selection").html(
			this.props.state.grain
		);
		jQuery("#cs-form-review-finish .cs-form-selection").html(
			this.props.state.finish
		);
		jQuery("#input_20_6").val(this.props.state.hex);
		jQuery("#input_20_7").val(this.props.state.rgb);
		jQuery("#input_20_11").val(this.props.state.cmyk);
		jQuery("#input_20_12").val(this.props.state.grain);
		jQuery("#input_20_8").val(this.props.state.finish);
	}

	render() {
		if (!this.props.state.data.isFetching) {
			return (
				<div>
					<ColorContainer />
					<GrainContainer />
					<FinishContainer />
					<ProgressContainer />
				</div>
			);
		}
		return <Loading />;
	}
}

export default App;
