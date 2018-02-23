var gulp = require('gulp'),
	uglify = require('gulp-uglify'),
	rename = require('gulp-rename');

// define tasks here
gulp.task('default', function(){
  // run tasks here
  // set up watch handlers here
});

gulp.task('pickadate', function () {
	var src = './js/pickadate/source/';
  	gulp.src([
  		src+'picker.date.js',
  		src+'picker.js',
  		src+'legacy.js'
  	])
    .pipe(uglify())
    .pipe(rename({
    	suffix: '.min'
    }))
    .pipe(gulp.dest('./js/pickadate/source'));
});