import React from "react";
import styled from "styled-components";
import {
	StepTitle,
	MiniDivider,
	StepButtons,
	StepDescription,
	Button
} from "../styles";
import caret from "../../images/leftCaret.png";
import grain from "../../reducers/grain";

class Finish extends React.Component {
	render() {
		const { finish } = this.props.data;

		const image = finish.finishes.map((item, key) => {
			return (
				<ImagePreview
					image={this.isSelected(item.name) && item.image}
					key={key}
				/>
			);
		});

		const buttons = finish.finishes.map((item, key) => {
			return (
				<FinishButton
					selected={this.isSelected(item.name)}
					onClick={() => this.handleChange(item.name)}
					key={key}
				>
					{item.name}
				</FinishButton>
			);
		});

		const descriptions = finish.finishes.map((item, key) => {
			if (this.isSelected(item.name)) {
				return (
					<p
						key={key}
						dangerouslySetInnerHTML={{
							__html: item.description
						}}
					/>
				);
			}
		});

		return (
			<div>
				<Container>
					{image}
					<div className="wrap">
						<Content>
							<StepTitle>
								Step 3 <span>{finish.title}</span>
							</StepTitle>
							<MiniDivider />
							<p
								dangerouslySetInnerHTML={{
									__html: finish.description
								}}
							/>
							<StepButtons>
								<p>Select Your Finish</p>
								<div>{buttons}</div>
							</StepButtons>
							<StepDescription align="left">
								<img src={caret} />
								{descriptions}
							</StepDescription>
						</Content>
					</div>
				</Container>
			</div>
		);
	}

	isSelected = finish => {
		if (this.props.finish === finish) return true;
	};

	handleChange = finish => {
		this.props.changeFinish(finish);
		this.props.updateProgress("finish");

		jQuery("#cs-form-review-finish .cs-form-selection").html(finish);
		jQuery("#input_20_8").val(finish);
	};
}

const Container = styled.div`
	background-color: #f3f3f3;
	min-height: 578px;
	position: relative;

	@media (max-width: 414px) {
		display: flex;
		flex-direction: column;
	}
`;

const ImagePreview = styled.div`
	background-image: url(${props => props.image});
	background-position: center;
	background-size: cover;
	background-repeat: no-repeat;
	position: absolute;
	left: 0;
	height: 100%;
	transition: all 0.2s ease-in-out;
	width: 50%;

	@media (max-width: 768px) {
		width: 40%;
	}

	@media (max-width: 414px) {
		height: 300px;
		order: 2;
		position: relative;
		width: 100%;
	}
`;

const Content = styled.div`
	padding: 80px 0 80px 55%;

	@media (max-width: 768px) {
		padding-left: 45%;
		padding-right: 30px;
	}

	@media (max-width: 414px) {
		order: 1;
		padding: 30px;
	}
`;

const FinishButton = Button.extend`
	background: ${props => (props.selected ? "#f0b300" : "#f3f3f3")};
`;

export default Finish;
