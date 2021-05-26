const paths = require('./paths')

const webpack = require('webpack')
const { merge } = require('webpack-merge')
const common = require('./webpack.common.js')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')

module.exports = merge(common, {
  // Set the mode to development or production
  mode: 'development',

  // Control how source maps are generated
  devtool: false,

  // Spin up a server for quick development
  devServer: {
    historyApiFallback: true,
    contentBase: paths.build,
    open: true,
    compress: true,
    hot: true,
    port: 8080,
  },

  plugins: [

    new MiniCssExtractPlugin({
      filename: `[name].css`
    }),
    // Only update what has changed on hot reload
    new webpack.HotModuleReplacementPlugin(),
    new webpack.SourceMapDevToolPlugin({
          filename: 'sourcemaps/[file].map'
      }),
  ],
})
