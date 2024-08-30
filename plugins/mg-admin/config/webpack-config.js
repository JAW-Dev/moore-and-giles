/* global __basedir */

// Import Modules.
const path = require('path');

module.exports = {
	entry: {
		'js/categories-table': [path.resolve(__basedir, 'assets/js/categories-table.js')],
		'css/categories-table': [path.resolve(__basedir, 'assets/css/categories-table.scss')],
	},
	process: {
		js: true,
		css: true,
		images: true,
		fonts: true,
		typescript: false,
		tailwind: false
	},
	plugins: {
		CleanWebpackPlugin: true,
		MiniCssExtractPlugin: true,
		FixStyleOnlyEntriesPlugin: true,
		ImageminPlugin: false,
		BundleAnalyzerPlugin: false
	}
};
