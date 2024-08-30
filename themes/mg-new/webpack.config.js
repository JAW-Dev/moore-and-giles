let MiniCssExtractPlugin = require('mini-css-extract-plugin');
let path = require('path');
let TsClassMetaGeneratorPlugin = require('ts-class-meta-generator');
let jsonImporter = require('node-sass-json-importer');

module.exports = {
    externals: {
        jquery: 'jQuery',
		'block-ui': 'block-ui'
    },
	context: __dirname,
	entry: {
		site: ['./site-entry.ts']
	},
	output: {
		filename: './js/[name].js'
	},
	resolve: {
		extensions: ['.webpack.js', '.web.js', '.ts', '.tsx', '.js']
	},
	stats: {
		entrypoints: false,
		children: false
	},
	devtool: 'source-map',
	module: {
		rules: [
			{
				test: /\.ts$/,
				loader: 'ts-loader',
				options: {
					configFile: 'tsconfig.json'
				}
			},
			{
				test: /\.(scss|css)$/,
				include: path.resolve('./src/scss'),
				use: [
					MiniCssExtractPlugin.loader,
					{
						loader: 'css-loader',
						options: {
							sourceMap: true,
							minimize: false
						}
					},
					{
						loader: 'postcss-loader',
						options: {
							sourceMap: true
						}
					},
					{
						loader: 'sass-loader',
						options: {
							sourceMap: true,
							importer: jsonImporter,
						}
					}
				]
			},
			{
				test: /\.woff2?$|\.ttf$|\.eot$|\.png$|\.jpg$|\.gif$|\.svg$/,
				use: [
					{
						loader: 'file-loader',
						options: {
							name: '[path][name].[ext]',
							publicPath: ''
						}
					}
				]
			}
		]
	},
	mode: 'development',
	plugins: [
		new MiniCssExtractPlugin({
			// Options similar to the same options in webpackOptions.output
			// both options are optional
			filename: '../style.css'
		}),
		new TsClassMetaGeneratorPlugin({
			siteName: 'MooreAndGiles',
			ignoreFolders: ["Interfaces"]
		})
	]
};
