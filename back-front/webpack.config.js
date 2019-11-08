const path = require("path");
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
                test: /\.module\.s(a|c)ss$/,
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
                ]
            },
            {
                test: /\.s(a|c)ss$/,
                exclude: /\.module.(s(a|c)ss)$/,
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
            }
        ],
    },
    plugins: [
        new ManifestPlugin(),
        new MiniCssExtractPlugin({
            filename: isDevelopment ? '[name].css' : '[name].[hash].css',
            chunkFilename: isDevelopment ? '[id].css' : '[id].[hash].css'
        })
    ]
};