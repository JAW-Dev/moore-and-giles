module.exports = (options, settings) => {
	if (options.process.html) {
		settings.module.rules.push({
			test: /\.html$/,
			use: [
				{
					loader: 'html-loader',
					options: { minimize: options.minimize.html }
				}
			]
		});
	}
};
