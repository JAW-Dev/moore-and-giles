import React from "react";
import loading from "../images/loading.svg";
import styled, { keyframes } from "styled-components";

const Loading = () => {
	return (
		<Container>
			<Icon src={loading} />
			<p>Loading...</p>
		</Container>
	);
};

const rotate360 = keyframes`
	from {
		transform: rotate(0deg);
	}

	to {
		transform: rotate(360deg);
	}
`;

const Container = styled.div`
	text-align: center;

	p {
		color: #9e9e9e;
		font-size: 14px;
		text-transform: uppercase;
	}
`;

const Icon = styled.img`
	animation: ${rotate360} 2s linear infinite;
	height: 50px;
	transform-origin: 50% 50%;
	width: 50px;
`;

export default Loading;
