const path = require('path');
const SRC_DIR = path.join(__dirname, '/resources/js');
const DIST_DIR = path.join(__dirname, '/public/js');
const webpack = require('webpack');
module.exports = {
    entry: `${SRC_DIR}/index.js`,
    output: {
        path: DIST_DIR,
        filename: 'app.js',
    },
    resolve: {
        extensions: ['.js', '.jsx', '.json', '.css'],
        alias: {
            '_variables.sass': `${SRC_DIR}/_variables.sass`,
        },
    },
    module : {
        rules : [
            {
                test: /\.css$/,
                loader: 'style-loader!css-loader'
            },
            {
                test: /\.png$/,
                loader: 'url-loader?limit=100000&minetype=image/png'
            },
            {
                test: /\.jpg/,
                loader: 'file-loader'
            },
            {
                test : /\.jsx?/,
                include : SRC_DIR,
                loader : 'babel-loader?presets[]=react,presets[]=env,presets[]=stage-0'
            },
            {
                test: /\.(scss|sass)$/i,
                include: [
                    path.resolve(__dirname, 'node_modules'),
                    path.resolve(__dirname, 'resources/sass')
                ],
                loaders: ["style-loader", "css-loader", "sass-loader"]
            },
            {
                test: /\.svg$/,
                use: [
                    {
                        loader: "babel-loader?presets[]=react,presets[]=env,presets[]=stage-0"
                    },
                    {
                        loader: "react-svg-loader",
                        options: {
                            jsx: true // true outputs JSX tags
                        }
                    }
                ]
            }
        ]
    },
    plugins: [
        new webpack.DefinePlugin({
            'process.env.NODE_ENV': JSON.stringify('development')
        })
    ]
};