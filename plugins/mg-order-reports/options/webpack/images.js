module.exports = (options, settings) => {
	if (options.process.images) {
		settings.module.rules.push({
			test: /\.(gif|png|jpe?g|webp|svg)$/i,
			use: [
				{
					loader: 'file-loader',
					options: {
						name: '[name].[ext]',
						outputPath: options.images.outputPath,
						publicPath: options.images.publicPath
					}
				}
			]
		});
	}
};
