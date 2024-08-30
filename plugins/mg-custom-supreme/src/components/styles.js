import React from "react";
import styled, { css } from "styled-components";

export const StepTitle = styled.h3`
	color: #f0b300;
	font-family: "proxima-nova", sans-serif;
	font-size: 25px;
	letter-spacing: -1px;
	line-height: 29px;
	margin: 0;
	text-transform: uppercase;

	span {
		color: #565656;
		display: block;
		font-size: 35px;
		letter-spacing: -1px;
		line-height: 41px;
		text-transform: none;
	}
`;

export const MiniDivider = styled.div`
	box-sizing: border-box;
	height: 3px;
	width: 40px;
	border: 2px solid #f0b300;
	margin: 30px 0;
`;

export const StepButtons = styled.div`
	p {
		color: #9b9b9b;
		font-size: 12px;
		letter-spacing: 2px;
		line-height: 15px;
		text-transform: uppercase;
	}

	div {
		display: flex;
		justify-content: space-between;
	}
`;

export const StepDescription = styled.div`
	align-items: flex-start;
	display: flex;
	margin-top: 60px;
	text-align: ${props => props.align};

	p {
		color: #9b9b9b;
		font-size: 12px;
		line-height: 15px;
		margin: 0 13px;
	}

	img {
		position: relative;
		top: 2px;
	}
`;

export const Button = styled.button`
	background: ${props => (props.selected ? "#f0b300" : "#fff")};
	border: 1px solid ${props => (props.selected ? "#f0b300" : "#979797")};
	color: ${props => (props.selected ? "#fff" : "#565656")};
	font-size: 10px;
	letter-spacing: 4px;
	line-height: 12px;
	max-width: ${props => (props.small ? "165px" : "auto")};
	outline: none;
	transition: all 0.3s ease-in-out;
	width: 48%;

	&:hover {
		background: #f0b300;
		border-color: #f0b300;
		color: #fff;
	}
`;
