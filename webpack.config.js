const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const IgnoreEmitPlugin = require('ignore-emit-webpack-plugin');
const webpack = require('webpack');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
    watch: true,
    watchOptions: {
        ignored: /node_modules/,
    },
    mode: 'production',
    entry: {
        admin: './src/js/admin.js',
        login: './src/js/login.js',
        public: './src/js/public.js',
        settings: './src/js/settings.js',
        cc: './src/js/cc.js',
        admin_styles: './src/scss/admin.scss',
        login_styles: './src/scss/login.scss',
        public_styles: './src/scss/public.scss',
        settings_styles: './src/scss/settings.scss',
    },
    output: {
        filename: 'js/[name].min.js',
        path: __dirname + '/dist',
    },
    module: {
        rules: [
            {
                test: /\.s[ac]ss$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            modules: false,
                        },
                    },
                    { loader: 'sass-loader' },
                ],
            },
            {
                test: /\.(png|jpe?g|gif|svg)$/i,
                type: 'asset/resource',
                generator: {
                    filename: 'img/[hash][ext][query]', 
                },
            },
        ],
    },
    optimization: {
        minimize: true,
        minimizer: [
            new TerserPlugin({
                extractComments: false,
                terserOptions: {
                    format: {
                        comments: /@license|@preserve|^!/i, 
                    },
                },
            }),
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({ filename: 'css/[name].min.css' }),
        new IgnoreEmitPlugin([
            'public_styles.min.js',
            'admin_styles.min.js',
            'login_styles.min.js',
            'settings_styles.min.js',
        ]),
        new webpack.BannerPlugin({
            banner: 
`/*!
 * WordPress Development Environment (WPDE)
 * https://ruciloss.github.io
 * Author Ruciloss
 * Released under the MIT License
 */`,
        }),
    ],
};
