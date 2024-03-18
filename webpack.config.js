const path = require( 'path' );
const entries = require( 'webpack-glob-entries' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config.js' );
const { BundleAnalyzerPlugin } = require( 'webpack-bundle-analyzer' );
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );

const isProduction = process.env.NODE_ENV === 'production';

module.exports = {
	...defaultConfig,
	entry: {
		'frontend/hotel-booking': './assets/js/frontend/hotel-booking.js',
		'frontend/hotel-booking-v2': './assets/js/frontend/hotel-booking-v2.js',
		'frontend/filter-by': './assets/js/frontend/filter-by.js',
		'frontend/sort-by': './assets/js/frontend/sort-by.js',
	},
	output: {
		filename: '[name]' + ( isProduction ? '.min.js' : '.js' ),
		path: path.resolve( __dirname, './assets/dist/js' ),
	},
	plugins: [
		process.env.WP_BUNDLE_ANALYZER && new BundleAnalyzerPlugin(),

		// WP_NO_EXTERNALS global variable controls whether scripts' assets get
		// generated, and the default externals set.
		! process.env.WP_NO_EXTERNALS && new DependencyExtractionWebpackPlugin(),
	].filter( Boolean ),
};
