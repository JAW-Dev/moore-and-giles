import React from "react";
import styled from "styled-components";
import ProgressItem from "./ProgressItem";
import { Button } from "./styles";

class Progress extends React.Component {
	state = {
		buttonVisible: true
	};

	render() {
		const { progress } = this.props.data;
		return (
			<Container>
				<div className="wrap">
					<Title>{progress.title}</Title>
					<Description
						dangerouslySetInnerHTML={{
							__html: progress.description
						}}
					/>
					<div>
						<StepsTitle>Review Your Options</StepsTitle>
						<Steps>
							<ProgressItem
								color
								complete={this.props.progress.color}
								title="Color"
								selection={this.props.hex}
							/>
							<ProgressItem
								complete={this.props.progress.grain}
								title="Grain"
								selection={this.props.grain}
							/>
							<ProgressItem
								complete={this.props.progress.finish}
								title="Finish"
								selection={this.props.finish}
							/>
						</Steps>
					</div>
					<div>
						<NextButton
							visible={this.state.buttonVisible}
							onClick={this.proceed}
						>
							Next
						</NextButton>
					</div>
				</div>
			</Container>
		);
	}

	proceed = () => {
		jQuery(".cs-form").slideDown();
		this.setState({
			buttonVisible: false
		});
	};
}

const Container = styled.div`
	padding: 70px 0;
	text-align: center;

	.wrap {
		max-width: 870px;

		@media (max-width: 768px) {
			padding: 0 30px;
		}
	}
`;

const Title = styled.h2`
	color: #f0b300;
	font-family: "proxima-nova", sans-serif;
	font-size: 51px;
	letter-spacing: -3px;
	line-height: 60px;
	margin-bottom: 30px;
`;

const Description = styled.p`
	color: #9b9b9b;
	font-size: 15px;
	line-height: 24px;
	margin-bottom: 30px;
`;

const StepsTitle = Description.extend`
	letter-spacing: 3px;
	text-transform: uppercase;
`;

const Steps = styled.div`
	display: flex;
	justify-content: space-between;
	margin-bottom: 60px;

	@media (max-width: 414px) {
		flex-direction: column;
	}
`;

const NextButton = Button.extend`
	background: #ebb136;
	border-color: #ebb136;
	color: #fff;
	opacity: ${props => (props.visible ? 1 : 0)};
	padding-left: 30px;
	padding-right: 30px;
	transition: opacity 0.3s ease-in-out;
	width: auto;

	&:hover {
		background: #ddd;
		border: 1px solid #ddd;
		color: #666;
	}
`;

export default Progress;
