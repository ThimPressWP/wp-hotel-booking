const gulp = require( 'gulp' );
const cache = require( 'gulp-cache' );
const lineec = require( 'gulp-line-ending-corrector' );
const rename = require( 'gulp-rename' );
const sass = require( 'gulp-sass' )( require( 'sass' ) );
const replace = require( 'gulp-replace' );
const zip = require( 'gulp-vinyl-zip' );
const plumber = require( 'gulp-plumber' );
const uglifycss = require( 'gulp-uglifycss' );
const del = require( 'del' );
const readFile = require( 'read-file' );
const postcss = require( 'gulp-postcss' );
const css_minify = require( 'postcss-minify' );
const rtlcss = require( 'gulp-rtlcss' );

// Clear cache.
gulp.task( 'clearCache', ( done ) => {
	return cache.clearAll( done );
} );

/******************************************* Build styles *******************************************/
const srcFrontendScssFiles = [
	'includes/elementor/src/scss/frontend-el-style.scss',
];
gulp.task( 'build_frontend_css', () => {
	return gulp
		.src( srcFrontendScssFiles )
		.pipe( sass({
			silenceDeprecations: ['legacy-js-api', 'import']
		}).on( 'error', sass.logError ) )
		.pipe( postcss( [ css_minify() ] ) )
		.pipe( lineec() )
		.pipe( gulp.dest( 'includes/elementor/src/css/frontend' ) );
} );


const srcDefaultScssFiles = [
	'assets/scss/**/*.scss',
];
gulp.task( 'build_default_frontend_css', () => {
	return gulp
		.src( srcDefaultScssFiles )
		.pipe( sass.sync().on( 'error', sass.logError ) )
		.on( 'error', sass.logError )
		.pipe( postcss( [ css_minify() ] ) )
		.pipe( lineec() )
		.pipe( gulp.dest( 'assets/css' ) );
} );

gulp.task( 'watch_scss', () => {
    gulp.watch( srcDefaultScssFiles, gulp.series( 'build_default_frontend_css' ) );
});

gulp.task( 'mincss', () => {
	return gulp.
		src( [ 'assets/css/**/*.css', '!assets/css/**/*.min.css', '!assets/css/theme-default.css', '!assets/css/check-out.css' ] )
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( uglifycss() )
		.pipe( gulp.dest( 'assets/css' ) );
} );

/******************************************* Release *******************************************/

// Clean folder to releases.
gulp.task( 'cleanReleases', () => {
	return del( './releases/**' );
} );

const releasesFiles = [
	'./**',
	'!vendor/**',
	'!node_modules/**',
	'!assets/src/**',
	'!webpack.config.js',
	'!tsconfig.json',
	'!phpcs.xml',
	'!.eslintrc.js',
	'!.eslintignore',
	'!composer.json',
	'!composer.lock',
	'!Gulpfile.js',
	'!package-lock.json',
	'!package.json',
	'!tests/**',
	'!phpunit.xml',
	'!README.md',
	'!build-release.js',
];

// Copy folder to releases.
gulp.task( 'copyReleases', () => {
	gulp.src( [ 'vendor/autoload.php' ] ).pipe( gulp.dest( './releases/wp-hotel-booking/vendor' ) );
	gulp.src( [ 'vendor/composer/**' ] ).pipe( gulp.dest( './releases/wp-hotel-booking/vendor/composer' ) );
	return gulp.src( releasesFiles ).pipe( gulp.dest( './releases/wp-hotel-booking/' ) );
} );

// Update file Readme
let currentVer = null;

const getCurrentVer = function( force ) {
	if ( currentVer === null || force === true ) {
		const current = readFile.sync( 'wp-hotel-booking.php', { encoding: 'utf8' } ).match( /Version:\s*(.*)/ );
		currentVer = current ? current[ 1 ] : null;
	}

	return currentVer;
};

gulp.task( 'updateReadme', () => {
	return gulp.src( [ 'readme.txt' ] )
		.pipe( replace( /Stable tag: (.*)/g, 'Stable tag: ' + getCurrentVer( true ) ) )
		.pipe( gulp.dest( './releases/wp-hotel-booking/', { overwrite: true } ) );
} );

// Create file zip.
gulp.task( 'zipReleases', () => {
	const version = getCurrentVer();

	return gulp
		.src( './releases/wp-hotel-booking/**', { base: './releases/' } )
		.pipe( zip.dest( './releases/wp-hotel-booking.' + version + '.zip' ) );
} );

gulp.task(
	'release',
	gulp.series(
		'clearCache',
		'mincss',
		'cleanReleases',
		'copyReleases',
		'updateReadme',
		'zipReleases',
		( done ) => {
			done();
		}
	)
);
