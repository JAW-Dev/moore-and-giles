import React from "react";
import styled from "styled-components";
import {
	StepTitle,
	MiniDivider,
	StepButtons,
	StepDescription,
	Button
} from "../styles";
import caret from "../../images/rightCaret.png";

class Grain extends React.Component {
	render() {
		const { grain } = this.props.data;

		const buttons = grain.grains.map((item, key) => (
			<Button
				selected={this.isSelected(item.name)}
				onClick={() => this.handleChange(item.name)}
				key={key}
			>
				{item.name}
			</Button>
		));

		const descriptions = grain.grains.map((item, key) => {
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

		const images = grain.grains.map((item, key) => {
			return (
				<GrainImage
					selected={this.isSelected(item.name)}
					src={item.image}
					key={key}
				/>
			);
		});
		return (
			<div>
				<ContentContainer className="wrap">
					<ContentArea content>
						<StepTitle>
							Step 2 <span>{grain.title}</span>
						</StepTitle>
						<MiniDivider />
						<p
							dangerouslySetInnerHTML={{
								__html: grain.description
							}}
						/>
						<StepButtons>
							<p>Select Your Grain</p>
							<div>{buttons}</div>
						</StepButtons>
						<StepDescription align="right">
							{descriptions}
							<img src={caret} />
						</StepDescription>
					</ContentArea>
					<ContentArea>
						<GrainImages>{images}</GrainImages>
					</ContentArea>
				</ContentContainer>
			</div>
		);
	}

	isSelected = grain => {
		if (this.props.grain === grain) return true;
	};

	handleChange = grain => {
		this.props.changeGrain(grain);
		this.props.updateProgress("grain");

		jQuery("#cs-form-review-grain .cs-form-selection").html(grain);
		jQuery("#input_20_12").val(grain);
	};
}

const ContentContainer = styled.div`
	align-items: center;
	background: #fff;
	display: flex;
	flex-direction: row;
	justify-content: space-between;
	min-height: 674px;
	padding: 70px;

	@media (max-width: 768px) {
		padding: 60px 30px;
	}

	@media (max-width: 414px) {
		flex-direction: column;
	}
`;

const ContentArea = styled.div`
	width: 48%;

	@media (max-width: 414px) {
		margin-bottom: ${props => (props.content ? "30px" : "")};
		width: 100%;
	}
`;

const GrainImages = styled.div`
	align-items: center;
	display: flex;
	transition: all 0.2s ease-in-out;
`;

const GrainImage = styled.img`
	left: ${props => (props.selected ? "30px" : 0)};
	order: ${props => (props.selected ? 1 : 2)};
	height: ${props => (props.selected ? "534px" : "456px")};
	width: ${props => (props.selected ? "258px" : "218px")};
	position: relative;
	transition: all 0.3s ease-in-out;
	z-index: ${props => (props.selected ? 50 : 1)};

	@media (max-width: 414px) {
		left: 0;
	}
`;

export default Grain;
