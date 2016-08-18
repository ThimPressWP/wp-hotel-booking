/**
 * Command line
 * step 1: Install Nodejs & npm. 'npm install -g gulp-cli'
 * step 2: Init environment: 'sudo npm init'
 * step 3: Install gulp: 'sudo npm install gulp --save-dev'
 * step 4: Install packages: 'sudo npm install gulp-concat gulp-rename gulp-save gulp-sass gulp-sourcemaps gulp-uglify gulp-save --save-dev'
 * step 5: Start watch: 'gulp watch'
 * @type Module gulp|Module gulp
 */
var gulp = require( 'gulp' ),
        concat = require( 'gulp-concat' ),
        rename = require( 'gulp-rename' ),
        save = require( 'gulp-save' ),
        sass = require( 'gulp-sass' ),
        sourcemaps = require( 'gulp-sourcemaps' ),
        uglify = require( 'gulp-uglify' );

/**
 * Minify script
 * @param {type} param1
 * @param {type} param2
 */
gulp.task( 'scripts', function(){
    return gulp.src( [ 'assets/js/*.js', '!assets/js/*.min.*' ] )
            .pipe( uglify() )
            .pipe( rename( { suffix: '.min' } ) )
            .pipe( gulp.dest( 'assets/js/' ) );
} );

/**
 * Convert Sass to Css and minify
 */
gulp.task( 'styles', function(){
    return sass( 'assets/css/.scss' )
            .pipe( gulp.dest( 'assets/css/' ) )
            .pipe( rename( { suffix: '.min' } ) )
            .pipe( gulp.dest( 'assets/css' ) );
} );

/**
 * Gulp watch
 */
gulp.task( 'watch', function(){
    gulp.watch( 'assets/js/*!(.min).js', [ 'scripts' ] );
    
} );
