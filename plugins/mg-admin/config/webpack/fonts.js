module.exports = (options, coreConfig, settings) => {
	if (options.process.fonts) {
		settings.module.rules.push({
			test: /\.(woff2?|ttf|otf|eot)$/,
			use: [
				{
					loader: 'file-loader',
					options: {
						name: '[path][name].[ext]',
						outputPath: coreConfig.fonts.outputPath
					}
				}
			]
		});
	}
};
