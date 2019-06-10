var gulp = require('gulp');
var util = require('gulp-quintype')
var del = require('del');

var destination = "public/insurge/assets";

function compileAll() {
  return util.manifestAndWrite(destination, util.merge(
    util.sass("./resources/assets/sass/application.scss"),
    util.browserify("./resources/assets/js/application.js", "application.js"),
    util.files("./resources/assets/images/**/*")
  ));
};

gulp.task("clean", function() { return del([destination]); });
gulp.task("compile", compileAll);
gulp.task("clean-compile", ["clean"], compileAll);
gulp.task("default", ["clean-compile"]);

gulp.task('watch', ['clean-compile'], function() {
  gulp.watch("./resources/assets/**/*", ['compile']);
});
