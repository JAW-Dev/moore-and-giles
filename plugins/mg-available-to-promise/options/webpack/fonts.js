module.exports = (options, settings) => {
	if (options.process.fonts) {
		settings.module.rules.push({
			test: /\.(woff2?|ttf|otf|eot)$/,
			use: [
				{
					loader: 'file-loader',
					options: {
						name: '[name].[ext]',
						outputPath: options.fonts.outputPath,
						publicPath: options.fonts.publicPath
					}
				}
			]
		});
	}
};
