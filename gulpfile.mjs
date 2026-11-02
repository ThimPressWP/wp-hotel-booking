import gulp from 'gulp';
import zip from 'gulp-zip';
import { deleteSync } from 'del';
import * as dartSass from 'sass';
import gulpSass from 'gulp-sass';
import cleanCSS from 'gulp-clean-css';
import rename from 'gulp-rename';
import fs from 'node:fs';

const name = 'wp-hotel-booking';
const sass = gulpSass( dartSass );

/******************************************* Build styles *******************************************/
const srcFrontendScssFiles = [
	'includes/elementor/src/scss/frontend-el-style.scss',
];

gulp.task( 'build_frontend_css', () => {
	return gulp
		.src( srcFrontendScssFiles )
		.pipe( sass( { silenceDeprecations: [ 'import' ] } ).on( 'error', sass.logError ) )
		.pipe( cleanCSS() )
		.pipe( gulp.dest( 'includes/elementor/src/css/frontend' ) );
} );

const srcDefaultScssFiles = [
	'assets/scss/**/*.scss',
];

gulp.task( 'build_default_frontend_css', () => {
	return gulp
		.src( srcDefaultScssFiles )
		.pipe( sass.sync().on( 'error', sass.logError ) )
		.pipe( cleanCSS() )
		.pipe( gulp.dest( 'assets/css' ) );
} );

gulp.task( 'watch_scss', () => {
	gulp.watch( srcDefaultScssFiles, gulp.series( 'build_default_frontend_css' ) );
} );

gulp.task( 'mincss', () => {
	return gulp
		.src( [
			'assets/css/**/*.css',
			'!assets/css/**/*.min.css',
			'!assets/css/theme-default.css',
			'!assets/css/check-out.css',
		] )
		.pipe( rename( { suffix: '.min' } ) )
		.pipe( cleanCSS() )
		.pipe( gulp.dest( 'assets/css' ) );
} );

/******************************************* Release *******************************************/

// Clean folder to releases.
gulp.task( 'cleanReleases', ( done ) => {
	deleteSync( [ './releases' ] );
	done();
} );

const releasesFiles = [
	'./**',
	'!node_modules/**',
	'!assets/src/**',
	'!assets/dist/js/**/*.map',
	'!assets/dist/js/**/*.LICENSE.txt',
	'!webpack.config.js',
	'!tsconfig.json',
	'!phpcs.xml',
	'!phpunit.xml',
	'!.eslintrc.js',
	'!.eslintignore',
	'!.gitignore',
	'!.gitattributes',
	'!.travis.yml',
	'!composer.json',
	'!composer.lock',
	'!gulpfile.mjs',
	'!package-lock.json',
	'!package.json',
	'!tests/**',
	'!README.md',
	'!releases/**',
	// Exclude AI assistant files/folders from the release package.
	'!CLAUDE.md',
	'!AGENTS.md',
	'!GEMINI.md',
	'!agent',
	'!agent/**',
	'!.agent',
	'!.agent/**',
	'!.claude',
	'!.claude/**',
	'!.codex',
	'!.codex/**',
	'!.cursor',
	'!.cursor/**',
	'!.github/copilot-instructions.md',
	'!.aider*',
	'!.windsurf',
	'!.windsurf/**',
];

// Copy folder to releases (vendor is included: run composer with --no-dev beforehand).
gulp.task( 'copyReleases', () => {
	return gulp.src( releasesFiles, { encoding: false } ).pipe( gulp.dest( `./releases/${ name }/` ) );
} );

const getCurrentVer = () => {
	const current = fs.readFileSync( `${ name }.php`, { encoding: 'utf8' } ).match( /Version:\s*(.*)/ );
	return current ? current[ 1 ].trim() : null;
};

// Update Stable tag in the released readme.txt.
gulp.task( 'updateReadme', ( done ) => {
	const readmePath = `./releases/${ name }/readme.txt`;
	const readme = fs.readFileSync( readmePath, { encoding: 'utf8' } );
	fs.writeFileSync( readmePath, readme.replace( /Stable tag: (.*)/g, 'Stable tag: ' + getCurrentVer() ) );
	done();
} );

// Create file zip.
gulp.task( 'zipReleases', () => {
	return gulp
		.src( `./releases/${ name }/**`, { base: './releases/', encoding: false } )
		.pipe( zip( `${ name }.${ getCurrentVer() }.zip` ) )
		.pipe( gulp.dest( './releases' ) );
} );

gulp.task(
	'release',
	gulp.series(
		'mincss',
		'cleanReleases',
		'copyReleases',
		'updateReadme',
		'zipReleases'
	)
);
