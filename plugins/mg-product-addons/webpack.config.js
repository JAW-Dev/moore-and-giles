const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');

module.exports = env => {
  return {
    externals: {
      jquery: 'jQuery'
    },
    // Source Maps.
    devtool: 'source-map',
    // Entry.
    entry: {
      'index': [
        './assets/js/index.js',
        './assets/css/index.scss',
      ],
    },
    // Output.
    output: {
      path: path.resolve(__dirname, 'src'),
      filename: '[name].js',
    },
    // Optimization.
    optimization: {
      minimizer: [
        new UglifyJsPlugin({
          include: /\.jsx?$/,
        }),
        new OptimizeCssAssetsPlugin(),
      ]
    },
    // Modules
    module: {
      rules: [
        // Babel Loader.
        {
          test: /\.jsx?$/,
          exclude: /node_modules/,
          loader: 'babel-loader',
        },
        // CSS/SCSS.
        {
          test: /\.(sa|sc|c)ss$/,
          use: [
            MiniCssExtractPlugin.loader,
            { loader: 'css-loader', options: { sourceMap: true } },
            { loader: 'postcss-loader', options: { sourceMap: true } },
            { loader: 'sass-loader', options: { sourceMap: true } },
          ],
        },
        // Images.
        {
          test: /\.(gif|png|jpe?g|webp)$/i,
          use: [
            {
              loader: 'file-loader',
              options: {
                name: '[name].[ext]',
                outputPath: 'images/',
                publicPath: 'images/',
              }
            },
            {
              loader: 'image-webpack-loader',
              options: {
                mozjpeg: { progressive: true, quality: 75 },
                optipng: { optimizationLevel: 3 },
                pngquant: { quality: '65-90', speed: 4 },
                gifsicle: { interlaced: false },
              },
            },
          ],
        },
        // SVG.
        {
          test: /\.(svg)$/i,
          use: [
            {
              loader: 'file-loader',
              options: {
                name: '[name].[ext]',
                outputPath: 'images/icons',
                publicPath: 'images/icons',
              }
            }
          ],
        },
        // Fonts.
      {
				test: /\.woff2?$|\.ttf$|\.eot$/,
				use: [
					{
						loader: 'file-loader',
						options: {
							name: '[name].[ext]',
							outputPath: 'fonts/',
              publicPath: 'fonts/'
						}
					}
				]
			}
      ],
    },
    // Plugins.
    plugins: [
      new MiniCssExtractPlugin({
        filename: '[name].css',
        chunkFilename: '[id].css',
      }),
    ],
  };
};
