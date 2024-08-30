var gulp = require("gulp");
var postcss = require("gulp-postcss");

gulp.task("css", function() {
	return gulp
		.src("./assets/*.css")
		.pipe(postcss())
		.pipe(gulp.dest("./server/dist"));
});

gulp.task("watch", function() {
	gulp.watch("./assets/*.css", ["css"]);
});

gulp.task("default", ["css", "watch"]);
