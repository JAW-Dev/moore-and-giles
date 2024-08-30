// Import Packages
const TerserPlugin = require('terser-webpack-plugin');

module.exports = (options, coreConfig, settings) => {
	if (options.process.js) {
		settings.module.rules.push({
			test: /\.(jsx?|es6)$/,
			exclude: /node_modules/,
			use: {
				loader: 'babel-loader'
			}
		});

		if (coreConfig.minimize.js) {
			settings.optimization.minimizer.push(
				// https://github.com/webpack-contrib/terser-webpack-plugin
				new TerserPlugin({
					sourceMap: coreConfig.sourcemaps.js,
					cache: true,
					parallel: true
				})
			);
		}
	}
};
