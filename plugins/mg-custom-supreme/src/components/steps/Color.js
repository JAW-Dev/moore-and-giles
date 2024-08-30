import React from "react";
import ReactDOM from "react-dom";
import ColorPicker from "coloreact";
import ImagePreview from "../ImagePreview";
import styled from "styled-components";
import { StepTitle, MiniDivider, media } from "../styles";
import { has, pick, startsWith } from "lodash";
import Color from "color";
import dividerimage from "../../images/dividerimage.png";

class ColorStep extends React.Component {
	state = {
		imageSelected: true,
		colorSelected: false
	};

	render() {
		const { color } = this.props.data;
		let output = (
			<ImagePreview
				images={color.moodImages}
				onChange={this.handleColorChange}
			/>
		);
		if (this.state.colorSelected) {
			output = (
				<div>
					<ColorPicker
						style={{
							position: "relative",
							height: "400px",
							width: "100%"
						}}
						color={this.props.hex}
						onChange={this.handleColorChange}
					/>
					<HexInput
						type="text"
						defaultValue={this.props.hex}
						onChange={this.updateColorByHex}
					/>
				</div>
			);
		}
		return (
			<div>
				<DividerImage />
				<Container color={this.props.hex}>
					<ModalContainer className="wrap">
						<Modal>
							<ContentContainer>
								<ContentArea>
									<StepTitle>
										Step 1 <span>{color.title}</span>
									</StepTitle>
									<MiniDivider />
									<p
										dangerouslySetInnerHTML={{
											__html: color.description
										}}
									/>
									<GroupedButton>
										<GroupButton
											selected={
												this.state.imageSelected
													? true
													: false
											}
											onClick={this.handleImageUpload}
										>
											Use Image
										</GroupButton>
										<GroupButton
											selected={
												this.state.colorSelected
													? true
													: false
											}
											onClick={this.handleColorPicker}
										>
											Color Picker
										</GroupButton>
									</GroupedButton>
								</ContentArea>
								<ContentArea picker>{output}</ContentArea>
							</ContentContainer>
						</Modal>
						<ColorBarContainer className="wrap">
							<ColorBar>
								<span>HEX: {this.props.hex}</span>
								<span>{this.props.rgb}</span>
								<span>CMYK: {this.props.cmyk}</span>
							</ColorBar>
						</ColorBarContainer>
					</ModalContainer>
				</Container>
			</div>
		);
	}

	handleImageUpload = () => {
		this.setState({
			imageSelected: true,
			colorSelected: false
		});
	};

	handleColorPicker = () => {
		this.setState({
			imageSelected: false,
			colorSelected: true
		});
	};

	handleColorChange = color => {
		let rgb = color;
		if (_.has(color, "rgb")) {
			rgb = { ...color.rgb };
		}

		if (rgb) {
			const rgbObj = _.pick(rgb, ["r", "g", "b"]);
			const colorObj = Color(rgbObj);
			const rgbString = colorObj.string();
			const hexString = colorObj.hex();
			const cmykString = `${colorObj
				.cmyk()
				.round()
				.array()}`;

			this.props.changeRgb(rgbString);
			this.props.changeHex(hexString);
			this.props.changeCmyk(cmykString);
			this.props.updateProgress("color");

			this.updateMarkup(hexString, rgbString, cmykString);
		}
	};

	updateColorByHex = e => {
		let hex = e.target.value;
		if (!_.startsWith(hex, "#")) {
			hex = `#${e.target.value}`;
		}

		const color = Color(hex);
		if (color) {
			const rgbString = color.rgb().string();
			const cmykString = `${color
				.cmyk()
				.round()
				.array()}`;

			this.props.changeRgb(rgbString);
			this.props.changeHex(hex);
			this.props.changeCmyk(cmykString);
			this.props.updateProgress("color");

			this.updateMarkup(hex, rgbString, cmykString);
		}
	};

	updateMarkup = (hex, rgb, cmyk) => {
		jQuery(".cs-swatch").css("background", hex);
		jQuery("#input_20_6").val(hex);
		jQuery("#input_20_7").val(rgb);
		jQuery("#input_20_11").val(cmyk);
	};
}

const DividerImage = styled.div`
	background-image: url(${dividerimage});
	background-size: cover;
	background-position: center;
	background-repeat: no-repeat;
	min-height: 245px;
	width: 100%;
`;

const Container = styled.section`
	background: ${props => props.color};
	padding: 95px 0;
	transition: background 0.2s ease-in-out;
`;

const ModalContainer = styled.div`
	box-shadow: 9px 11px 15px 0 rgba(0, 0, 0, 0.17);

	@media (max-width: 768px) {
		box-shadow: none;
	}
`;

const Modal = styled.div`@media (max-width: 768px) {padding: 0 30px;}`;

const ContentContainer = styled.div`
	align-items: center;
	background: #fff;
	display: flex;
	flex-direction: row;
	justify-content: space-between;
	padding: 70px;

	@media (max-width: 768px) {
		flex-direction: column;
		padding: 30px;
	}
`;

const ContentBar = styled.div`
	background: #d8d8d8;
	padding: 13px 0;
	text-align: center;
`;

const ContentArea = styled.div`
	width: 48%;

	@media (max-width: 768px) {
		margin-top: ${props => (props.picker ? "30px" : "")};
		width: 100%;
	}
`;

const GroupedButton = styled.div`
	align-items: center;
	display: flex;
	flex-direction: row;

	button:first-child {
		border-top-left-radius: 50px;
		border-bottom-left-radius: 50px;
		border-right: none;
	}

	button:last-child {
		border-top-right-radius: 50px;
		border-bottom-right-radius: 50px;
		border-left: none;
	}
`;

const GroupButton = styled.button`
	background: ${props => (props.selected ? "#f0b300" : "#fff")};
	border: 1px solid ${props => (props.selected ? "#f0b300" : "#9b9b9b")};
	color: ${props => (props.selected ? "#fff" : "#4a4a4a")};
	font-size: 10px;
	letter-spacing: 4px;
	line-height: 12px;
	outline: none;
	padding: 15px 30px;
	transition: all 0.3s ease-in-out;

	&:hover {
		background: #f0b300;
		border-color: #f0b300;
		color: #fff;
	}
`;

const ColorBarContainer = styled.div`
	@media (max-width: 768px) {
		padding: 0 30px;
	}
`;

const ColorBar = styled.div`
	background: #d8d8d8;
	color: #9b9b9b;
	font-size: 10px;
	letter-spacing: 4px;
	padding: 13px 0;
	text-align: center;
	text-transform: uppercase;

	span {
		margin: 0 30px;

		@media (max-width: 414px) {
			display: block;
			margin-bottom: 1em;

			&:last-child {
				margin-bottom: 0;
			}
		}
	}
`;

const HexInput = styled.input`
	background: none;
	border: none;
	border-bottom: 1px solid #000;
	box-shadow: none;
	color: #000;
	font-size: 14px;
	max-width: 85px;

	&:focus {
		border: none;
		border-bottom: 1px solid #000;
	}
`;

export default ColorStep;
