const resolve = require('path').resolve;
const merge = require('webpack-merge');

module.exports = (env = {}) => {
	const {common, OUTPUT_DIR} = require('./common.js')(env);
	const DIST_DIR = resolve(OUTPUT_DIR, 'prod');
	return merge(common, {
		mode: 'production',
		devtool: 'source-map',
		output: {
			path: DIST_DIR,
			publicPath: '/build/prod/',
			filename: '[name].[contenthash:8].js',
			chunkFilename: '[name].[contenthash:8].js'
		}
	})
};
