/* global __basedir */

// Import Modules.
const path = require('path');

const devMode = process.env.NODE_ENV !== 'production';
const sourcePath = `${__basedir}/assets`;
const destinationPath = `${__basedir}/dist`;

module.exports = {
	mode: devMode ? 'development' : 'production',
	target: 'web',
	context: path.resolve(__dirname),
	entry: {
		admin: ['./../assets/scripts/admin.js', './../assets/styles/admin.scss']
	},
	output: {
		path: path.join(__dirname, './../dist'),
		filename: '[name].js',
		sourceMapFilename: '[file].map'
	},
	sourcePath,
	destinationPath,
	externals: {
		jquery: 'jQuery'
	},
	watch: !!devMode,
	watchOptions: {
		ignored: /node_modules/
	},
	process: {
		html: false,
		js: true,
		css: true,
		images: true,
		fonts: false,
		typescript: false
	},
	sourcemaps: {
		js: true,
		css: true
	},
	minimize: {
		html: true,
		js: true,
		css: true
	},
	images: {
		outputPath: './../dist/images',
		publicPath: './../images/'
	},
	fonts: {
		outputPath: './../dist/fonts',
		publicPath: './../fonts'
	},
	plugins: {
		CleanWebpackPlugin: true,
		HtmlWebpackPlugin: false,
		MiniCssExtractPlugin: true,
		CopyPlugin: false,
		FileManagerPlugin: true,
		ImageminPlugin: true,
		BundleAnalyzerPlugin: false
	},
	CleanWebpackPlugin: {},
	MiniCssExtractPlugin: {
		filename: '[name].css'
	},
	HtmlWebpackPlugin: {
		title: 'Webpack App',
		filename: '../index.html',
		template: './../src/index.html',
		inject: false
	},
	CopyPlugin: [
		{ from: `${sourcePath}/images/`, to: `${destinationPath}/images/` },
		{ from: `${sourcePath}/fonts/`, to: `${destinationPath}/fonts/` }
	], // prettier-ignore
	ImageminPlugin: {
		bail: false,
		cache: true,
		name: '[path][name].[ext]',
		imageminOptions: {
			plugins: [
				['mozjpeg', { progressive: true, quality: 75 }],
				['optipng', { optimizationLevel: 3 }],
				['gifsicle', { interlaced: false }],
				[
					'svgo',
					{
						plugins: [
							{ cleanupAttrs: true },
							{ cleanupEnableBackground: true },
							{ cleanupIDs: true },
							{ cleanupNumericValues: { floatPrecision: 3 } },
							{ collapseGroups: true },
							{ convertColors: true },
							{ convertPathData: true },
							{ convertShapeToPath: true },
							{ convertStyleToAttrs: true },
							{ convertTransform: true },
							{ inlineStyles: true },
							{ mergePaths: true },
							{ minifyStyles: true },
							{ moveElemsAttrsToGroup: true },
							{ moveGroupAttrsToElems: true },
							{ removeComments: true },
							{ removeAttrs: true },
							{ removeDesc: true },
							{ removeDoctype: true },
							{ removeEditorsNSData: true },
							{ removeElementsByAttr: true },
							{ removeEmptyAttrs: true },
							{ removeEmptyContainers: true },
							{ removeEmptyText: true },
							{ removeHiddenElems: true },
							{ removeMetadata: true },
							{ removeNonInheritableGroupAttrs: true },
							{ removeTitle: true },
							{ removeUnknownsAndDefaults: true },
							{ removeUnusedNS: true },
							{ removeUselessDefs: true },
							{ removeUselessStrokeAndFill: true },
							{ removeXMLProcInst: true },
							{ transformsWithOnePath: true },
							{ addAttributesToSVGElement: false },
							{ addClassesToSVGElement: false },
							{ cleanupListOfValues: false },
							{ removeDimensions: false },
							{ removeStyleElement: false },
							{ removeViewBox: false },
							{ removeXMLNS: false },
							{ sortAttrs: false }
						]
					}
				]
			]
		}
	},
	FileManagerPluginDev: {
		silent: true,
		onEnd: {
			mkdir: [`${destinationPath}/styles`, `${destinationPath}/scripts`],
			move: [
				{ source: `${destinationPath}/admin.css`, destination: `${destinationPath}/styles/admin.css` },
				{ source: `${destinationPath}/admin.js`, destination: `${destinationPath}/scripts/admin.js` },
				{ source: `${destinationPath}/admin.css.map`, destination: `${destinationPath}/styles/admin.css.map` },
				{ source: `${destinationPath}/admin.js.map`, destination: `${destinationPath}/scripts/admin.js.map` }
			]
		}
	},
	FileManagerPluginProduction: {
		onEnd: {
			mkdir: [`${destinationPath}/styles`, `${destinationPath}/scripts`],
			move: [
				{ source: `${destinationPath}/admin.css`, destination: `${destinationPath}/styles/admin.css` },
				{ source: `${destinationPath}/admin.js`, destination: `${destinationPath}/scripts/admin.js` }
			]
		}
	}
};
