/* global __basedir */

// Import Modules.
const path = require('path');

const devMode = process.env.NODE_ENV !== 'production';
const destinationPath = `${__basedir}/src`;

module.exports = {
	mode: devMode ? 'development' : 'production',
	target: 'web',
	context: path.resolve(__basedir, 'assets'),
	output: {
		path: path.resolve(__dirname, destinationPath),
		filename: '[name].js',
		sourceMapFilename: '[file].map'
	},
	destinationPath,
	watch: !!devMode,
	watchOptions: {
		poll: true,
		ignored: /node_modules/
	},
	externals: {
		jquery: 'jQuery',
	},
	resolve: {
		extensions: ['.ts', '.tsx', '.js']
	},
	images: {
		outputPath: './'
	},
	fonts: {
		outputPath: './'
	},
	sourcemaps: {
		js: true,
		css: true
	},
	minimize: {
		js: !devMode,
		css: !devMode
	},
	CleanWebpackPlugin: {
		cleanStaleWebpackAssets: false
	},
	MiniCssExtractPlugin: {
		filename: '[name].css'
	},
	FixStyleOnlyEntriesPlugin: {
		silent: true
	},
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
							{ minifyStyles: false },
							{ moveElemsAttrsToGroup: true },
							{ moveGroupAttrsToElems: true },
							{ removeComments: true },
							{ removeAttrs: false },
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
							{ removeXMLProcInst: false },
							{ transformsWithOnePath: true },
							{ addAttributesToSVGElement: false },
							{ addClassesToSVGElement: false },
							{ cleanupListOfValues: false },
							{ removeDimensions: true },
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
	BundleAnalyzerPlugin: {
		analyzerMode: 'static'
	}
};
