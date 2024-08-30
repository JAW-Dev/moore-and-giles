import React from "react";
import styled from "styled-components";
import checkmarkIncomplete from "../images/checkmarkIncomplete.svg";
import checkmarkComplete from "../images/checkmarkComplete.svg";

const ProgressItem = props => {
	let selection = props.selection;
	if (props.color) {
		selection = <Swatch color={selection} />;
	}

	let icon = <Icon src={checkmarkIncomplete} />;
	if (props.complete) {
		icon = <Icon src={checkmarkComplete} />;
	}

	return (
		<Container>
			{icon}
			<Text>
				<Title>{props.title}</Title>
				<Selection>{selection}</Selection>
			</Text>
		</Container>
	);
};

const Container = styled.div`
	flex: 1;

	@media (max-width: 414px) {
		margin-bottom: 2em;

		&:last-child {
			margin-bottom: 0;
		}
	}
`;

const Icon = styled.img`
	display: block;
	margin: 0 auto;
`;

const Text = styled.div`
	color: #9b9b9b;
	margin-top: 20px;
	text-align: center;
`;

const Title = styled.span`
	display: block;
	font-size: 14px;
	font-weight: 300;
	letter-spacing: 6px;
	text-transform: uppercase;
`;

const Selection = styled.span`
	font-size: 36px;
	letter-spacing: -1px;
	line-height: 36px;
`;

const Swatch = styled.div`
	background-color: ${props => props.color};
	height: 36px;
	margin: 0 auto;
	max-width: 200px;
	width: 100%;
`;

export default ProgressItem;
