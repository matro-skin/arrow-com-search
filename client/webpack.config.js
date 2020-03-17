const { VueLoaderPlugin } = require("vue-loader");
var CleanObsoleteChunks = require('webpack-clean-obsolete-chunks');

module.exports = {
	entry: [
		'./src/js/app.js',
	],
	output: {
		filename: './js/partsSearch.js'
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
		new CleanObsoleteChunks({
			verbose: true, // Write logs to console
			deep: true // Clean obsolete chunks of webpack child compilations
		})
	],
	target: "web"
};
