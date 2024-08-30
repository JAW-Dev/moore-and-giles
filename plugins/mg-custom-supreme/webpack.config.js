const path = require("path");
const webpack = require("webpack");
const CopyWebpackPlugin = require("copy-webpack-plugin");
const CleanWebpackPlugin = require("clean-webpack-plugin");
const UglifyJsPlugin = require("uglifyjs-webpack-plugin");
const BabelWebpackPlugin = require("babel-minify-webpack-plugin");

const webpackConfig = {
	entry: "./src/index.js",
	output: {
		path: path.resolve(__dirname, "dist"),
		filename: "bundle.js"
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loaders: ["babel-loader"]
			},
			{
				test: /\.css$/,
				use: [
					{ loader: "style-loader" },
					{
						loader: "css-loader",
						options: {
							modules: true
						}
					},
					{
						loader: "postcss-loader"
					}
				]
			},
			{
				test: /\.(png|jpg|gif|svg)$/,
				use: [
					{
						loader: "url-loader"
					}
				]
			}
		]
	},
	plugins: [require("autoprefixer")]
};

if (process.env.NODE_ENV === "production") {
	webpackConfig.plugins.push(new BabelWebpackPlugin());
}

module.exports = webpackConfig;
