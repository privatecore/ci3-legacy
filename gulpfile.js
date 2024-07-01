'use strict';

const gulp = require('gulp');
const plumber = require('gulp-plumber');
const cssnano = require('gulp-cssnano');
const terser = require('gulp-terser');

// default
gulp.task('css', function() {
	return gulp.src([
			'./themes/default/assets/css/**/*.css',
			'!./themes/default/assets/css/**/*.min.css',
		])
		.pipe(plumber())
		.pipe(cssnano({ zindex: false, autoprefixer: false }))
		.pipe(gulp.dest('./themes/default/assets/css'))
});

gulp.task('js', function() {
	return gulp.src([
			'./themes/default/assets/js/**/*.js',
			'!./themes/default/assets/js/**/*.min.js',
		])
		.pipe(plumber())
		.pipe(terser())
		.pipe(gulp.dest('./themes/default/assets/js'))
});

gulp.task('default', gulp.series('css', 'js'));
