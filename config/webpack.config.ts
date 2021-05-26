import webpack from "webpack";
import paths from './paths';
import {CleanWebpackPlugin} from 'clean-webpack-plugin';
import HtmlWebpackPlugin from 'html-webpack-plugin';
import MiniCssExtractPlugin from 'mini-css-extract-plugin';

export default (env): Partial<webpack.Configuration> => {
    console.log(env);
    const DEV_SERVER = Boolean(env.WEBPACK_SERVE);
    const PRODUCTION =  Boolean(env.WEBPACK_BUNDLE);
    const HASH = PRODUCTION ? '.[contenthash]' : '';
    return {
        entry: [paths.src + '/index.js'],
        devtool: false,
        devServer: {
            historyApiFallback: true,
            contentBase: paths.build,
            open: true,
            compress: true,
            hot: true,
            port: 8080,
        },
        output: {
            path: paths.build,
            publicPath: './',
            filename: `js/[name]${HASH}.js`,
        },
        plugins: [
            new HtmlWebpackPlugin({
                title: 'NBSP',
                favicon: paths.src + '/images/favicon.png',
                template: paths.src + '/template.html',
                filename: 'index.html',
            }),
            new MiniCssExtractPlugin({
                filename: `styles/[name]${HASH}.css`,
                chunkFilename: '[id].css',
            }),
            PRODUCTION && new CleanWebpackPlugin(),
            DEV_SERVER && new webpack.HotModuleReplacementPlugin(),
            DEV_SERVER && new webpack.SourceMapDevToolPlugin({
                filename: 'sourcemaps/[file].map'
            }),
        ].filter(Boolean),
        module: {
            rules: [
                {test: /\.js$/, exclude: /node_modules/, use: ['babel-loader']},
                {
                    test: /\.(s?css)$/,
                    use: [
                        MiniCssExtractPlugin.loader,
                        {loader: 'css-loader'},
                        {loader: 'postcss-loader'},
                        {loader: 'sass-loader'},
                    ],
                },
                {test: /\.(?:ico|gif|png|jpg|jpeg)$/i, type: 'asset/resource'},
                {test: /\.(woff(2)?|eot|ttf|otf|svg|)$/, type: 'asset/inline'},
            ],
        },
    }
}
