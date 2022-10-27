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
const rtlcss = require( 'gulp-rtlcss' );

// Clear cache.
gulp.task( 'clearCache', ( done ) => {
	return cache.clearAll( done );
} );

/******************************************* Build styles *******************************************/
gulp.task( 'mincss', () => {
	return '';
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
];

// Copy folder to releases.
gulp.task( 'copyReleases', () => {
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
		//'mincss',
		'cleanReleases',
		'copyReleases',
		'updateReadme',
		'zipReleases',
		( done ) => {
			done();
		}
	)
);

