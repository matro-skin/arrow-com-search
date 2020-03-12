const { VueLoaderPlugin } = require("vue-loader");
// const Dotenv = require('dotenv-webpack');

module.exports = {
	entry: [
		'./src/js/app.js',
	],
	output: {
		filename: './js/app.js'
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader'
				}
			},
			{
				test: /\.vue$/,
				loader: 'vue-loader'
			}
		]
	},
	resolve: {
		alias: {
			'vue$': 'vue/dist/vue.esm.js'
		},
		extensions: ['*', '.js', '.vue', '.json']
	},
	plugins: [
		new VueLoaderPlugin(),
		// new Dotenv()
	],
	target: "web"
};
