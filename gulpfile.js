var gulp = require('gulp');
var sass = require('gulp-sass');
var rename = require('gulp-rename');

var scssFolder = 'assets/src/scss';
var destCssFolder = 'assets/css';

//sass
gulp.task('sass', function () {
    gulp.src([scssFolder + '/admin.scss'])
        .pipe(sass({outputStyle: 'compressed'}))
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(destCssFolder));

    gulp.src([scssFolder + '/theme-*.scss'])
        .pipe(sass({includePaths: ['assets/src/scss'], outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(destCssFolder));

});

// Default task
gulp.task('default', function () {
    gulp.start('sass');
});