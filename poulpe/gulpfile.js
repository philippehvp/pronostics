var gulp = require('gulp');
var ts = require('gulp-typescript');
var concat = require('gulp-concat');
var sass = require('gulp-sass');
var livereload = require('gulp-livereload');
var sourcemaps = require('gulp-sourcemaps');

/* Configuration */

var jsSource =	[
					'./assets/scripts/*.ts'
				];
var jsTarget = "app.js";

var htmlSource =	[
						'./assets/scripts/templates/*.html'
					];

var phpSource =	[
						'./assets/php/*.php'
				];

var cssSource =	[
					 './assets/styles/partials/_variables.scss'
					,'./assets/styles/partials/_navbar.scss'
					,'./assets/styles/main.scss'
				];
var cssTarget = 'main.css';

var imgSource =	[
					'./assets/images/*.png'
				];

var distTarget = "./dist/";

/* Concat TypeScript files */
gulp.task('js', function() {
	return	gulp.src(jsSource)
			.pipe(sourcemaps.init())
			.pipe(ts({
				noImplicitAny: false,
				out: jsTarget
			}))
			.pipe(concat(jsTarget))
			.pipe(sourcemaps.write())
			.pipe(gulp.dest('./dist/'))
			.pipe(livereload());
});

/* Copy template files */
gulp.task('html', function() {
	return	gulp.src(htmlSource)
			.pipe(gulp.dest('./dist/'))
			.pipe(livereload());
});

/* Copy PHP files */
gulp.task('php', function() {
	return	gulp.src(phpSource)
			.pipe(gulp.dest('./dist/'))
			.pipe(livereload());
});

/* Concat SASS files */
gulp.task('sass', function () {
	return	gulp.src(cssSource)
			.pipe(sass())
			.pipe(concat(cssTarget))
    		.pipe(gulp.dest('./dist/'))
    		.pipe(livereload());
});

gulp.task('watch', function() {
	livereload.listen();

	gulp.watch(jsSource, ['js']);
	gulp.watch(htmlSource, ['html']);
	gulp.watch(phpSource, ['php']);
	gulp.watch(cssSource, ['sass']);
	gulp.watch('index.php', function() {
		livereload.reload('./index.php');
	});
});