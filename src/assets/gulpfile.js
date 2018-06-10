/*!
 * gulp
 * $ npm install --save-dev gulp gulp-simple-compass
 */

// Load plugins
var gulp = require('gulp'),
    compass = require('gulp-simple-compass');

// Styles
gulp.task('styles', function() {
  gulp.src('scss/*.scss')
    .pipe(compass());
});

// Watch
gulp.task('watch', function() {

  // Watch config.rb and recompile CSS on change.
  gulp.watch('config.rb', ['styles']);

  // Watch .scss files and recompile CSS on change.
  gulp.watch('scss/**/*.scss', ['styles']);

});

// Default task
gulp.task('default', function() {
    gulp.start('styles', 'watch');
});
