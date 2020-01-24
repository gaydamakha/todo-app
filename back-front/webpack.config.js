const webpack = require('webpack');
const path = require('path');
const ManifestPlugin = require('webpack-manifest-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const isDevelopment = process.env.NODE_ENV === 'development';

module.exports = {
    entry: {
        app: ['./assets/js/app.js', './assets/css/app.scss'],
    },
    mode: 'development',
    output: {
        path: path.resolve(__dirname, 'public/build/'),
        publicPath: "/build/",
        filename: '[name].js'
    },
    resolve: {
        extensions: ['.scss']
    },
    module: {
        rules: [
            {
                // test: /\.module\.s([ac])ss$/,
                test: /\.module\.(css|sass|scss)$/,
                loader: [
                    isDevelopment ? 'style-loader' : MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            modules: true,
                            sourceMap: isDevelopment
                        }
                    },
                    {
                        loader: 'sass-loader',
                            options: {
                                sourceMap: isDevelopment
                            }
                    }
                ],
                exclude: /node_modules/,
            },
            {
                // test: /\.s([ac])ss$/,
                test: /\.(css|sass|scss)$/,
                // exclude: /\.module.(s([ac])ss)$/,
                exclude: /\.module\.(css|sass|scss)$/,
                loader: [
                    isDevelopment ? 'style-loader' : MiniCssExtractPlugin.loader,
                    'css-loader',
                    {
                        loader: 'sass-loader',
                        options: {
                              sourceMap: isDevelopment
                        }
                    }
                ]
            },
            {
                test: /.(ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
                use: [{
                    loader: 'file-loader',
                    options: {
                        name: '[name].[ext]',
                        outputPath: '../fonts/',
                        publicPath: '../static/fonts'
                    }
                }]
            },
        ],
    },
    plugins: [
        new ManifestPlugin(),
        new MiniCssExtractPlugin({
            filename: isDevelopment ? '[name].css' : '[name].[hash].css',
            chunkFilename: isDevelopment ? '[id].css' : '[id].[hash].css'
        }),
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery",
            jquery: 'jquery',
            // 'window.jQuery': 'jquery'
        })
    ]
};