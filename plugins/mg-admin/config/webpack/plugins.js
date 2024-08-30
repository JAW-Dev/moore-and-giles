// Import Packages.
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const ImageminPlugin = require('imagemin-webpack');
const { BundleAnalyzerPlugin } = require('webpack-bundle-analyzer');

module.exports = (options, coreConfig, settings) => {
	// https://github.com/johnagan/clean-webpack-plugin
	if (options.plugins.CleanWebpackPlugin) {
		settings.plugins.push(new CleanWebpackPlugin(coreConfig.CleanWebpackPlugin));
	}

	// https://github.com/webpack-contrib/mini-css-extract-plugin
	if (options.plugins.MiniCssExtractPlugin) {
		settings.plugins.push(new MiniCssExtractPlugin(coreConfig.MiniCssExtractPlugin));
	}

	// https://github.com/fqborges/webpack-fix-style-only-entries
	if (options.plugins.FixStyleOnlyEntriesPlugin) {
		settings.plugins.push(new FixStyleOnlyEntriesPlugin(coreConfig.FixStyleOnlyEntriesPlugin));
	}

	// https://github.com/itgalaxy/imagemin-webpack
	if (options.plugins.ImageminPlugin) {
		settings.plugins.push(new ImageminPlugin(coreConfig.ImageminPlugin));
	}

	// https://github.com/webpack-contrib/webpack-bundle-analyzer
	if (options.plugins.BundleAnalyzerPlugin) {
		settings.plugins.push(new BundleAnalyzerPlugin(coreConfig.BundleAnalyzerPlugin));
	}
};
