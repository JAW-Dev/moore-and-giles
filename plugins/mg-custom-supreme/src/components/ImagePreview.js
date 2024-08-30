import React from "react";
import styled from "styled-components";
import picker from "../images/picker.png";

class ImagePreview extends React.Component {
	state = {
		imageUrl: this.props.images[0].image,
		thumbs: this.props.images
		// imageUrl:
		// 	"https://staging.mooreandgiles.com/leather/wp-content/uploads/sites/3/2017/09/20170906-7781MB_500x500.jpg",
		// thumbs: [
		// 	{
		// 		image_id: 10006,
		// 		image:
		// 			"https://staging.mooreandgiles.com/leather/wp-content/uploads/sites/3/2017/09/20170906-7781MB_500x500.jpg"
		// 	},
		// 	{
		// 		image_id: 10005,
		// 		image:
		// 			"https://staging.mooreandgiles.com/leather/wp-content/uploads/sites/3/2017/09/20170906-7773MB_500x500.jpg"
		// 	},
		// 	{
		// 		image_id: 10004,
		// 		image:
		// 			"https://staging.mooreandgiles.com/leather/wp-content/uploads/sites/3/2017/09/20170906-7768MB_500x500.jpg"
		// 	}
		// ]
	};

	componentDidMount() {
		this.updateCanvas();
	}

	componentDidUpdate() {
		this.updateCanvas();
	}

	render() {
		const thumbs = this.state.thumbs.map((thumb, key) => {
			return (
				<PreviewThumb
					key={key}
					src={thumb.image}
					onClick={() => this.updatePreview(thumb.image)}
					selected={
						this.state.imageUrl === thumb.image ? true : false
					}
				/>
			);
		});

		return (
			<div>
				<CanvasContainer>
					<div ref="canvasContainer">
						<canvas
							onClick={this.getColorCode}
							ref="canvas"
							height={400}
							width={500}
						/>
					</div>
				</CanvasContainer>
				<PreviewThumbs>
					{thumbs}
					<UploadButton>
						Upload Your Own
						<input
							style={{ display: "none" }}
							type="file"
							onChange={this.handleImageUpload}
						/>
					</UploadButton>
				</PreviewThumbs>
			</div>
		);
	}

	updateCanvas = () => {
		const canvas = this.refs.canvas;
		const canvasContainer = this.refs.canvasContainer;
		const height = canvasContainer.offsetHeight;
		const width = canvasContainer.offsetWidth;

		const ctx = canvas.getContext("2d");
		var image = new Image(width, height);
		image.src = this.state.imageUrl;
		image.onload = function() {
			ctx.drawImage(image, 0, 0, width, height);
		};
	};

	getColorCode = e => {
		const ctx = this.refs.canvas.getContext("2d");
		const data = ctx.getImageData(
			e.nativeEvent.offsetX,
			e.nativeEvent.offsetY,
			1,
			1
		).data;
		const rgb = { r: data[0], g: data[1], b: data[2], a: 1 };
		this.props.onChange(rgb);
	};

	updatePreview(thumb) {
		this.setState({
			imageUrl: thumb
		});
	}

	handleImageUpload = e => {
		e.preventDefault();
		let reader = new FileReader();
		let file = e.target.files[0];
		const thumbs = this.state.thumbs;

		reader.onloadend = () => {
			const newThumb = { image: reader.result };
			thumbs.push(newThumb);
			this.setState({
				imageUrl: reader.result,
				thumbs: thumbs
			});
		};

		reader.readAsDataURL(file);
	};
}

const CanvasContainer = styled.div`cursor: url(${picker}) 15 15, auto;`;

const PreviewThumbs = styled.div`
	display: flex;
	flex-direction: row;
	flex-wrap: wrap;
`;

const PreviewThumb = styled.img`
	border: 2px solid ${props => (props.selected ? "#f0b300" : "transparent")};
	cursor: pointer;
	height: 45px;
	width: 45px;
	margin-bottom: 0.5em;
	margin-right: 0.5em;
`;

const UploadButton = styled.label`
	background: none;
	border-radius: 50px;
	border: 1px solid #9b9b9b;
	color: #565656;
	font-size: 10px;
	height: 45px;
	letter-spacing: 3px;
	line-height: 2.3;
	outline: none;
	padding: 10px 20px;
	text-transform: uppercase;
	transition: all 0.3s ease-in-out;

	&:hover {
		background: #f0b300;
		border-color: #f0b300;
		color: #fff;
	}
`;

export default ImagePreview;
