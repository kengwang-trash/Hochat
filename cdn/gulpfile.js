/*!
 * gulp
 * $ npm install gulp-ruby-sass gulp-autoprefixer gulp-minify-css gulp-jshint gulp-concat gulp-uglify gulp-imagemin gulp-notify gulp-rename gulp-livereload gulp-cache del --save-dev
 */
// Load plugins
var theme = 'default';
var gulp = require('gulp'),
    replace = require('gulp-replace'),
    sass = require('gulp-ruby-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    minifycss = require('gulp-minify-css'),
    cleancss = require('gulp-clean-css'),
    jshint = require('gulp-jshint'),
    uglify = require('gulp-uglify'),
    sourcemaps = require('gulp-sourcemaps'),
    imagemin = require('gulp-imagemin'),
    rename = require('gulp-rename'),
    concat = require('gulp-concat'),
    notify = require('gulp-notify'),
    htmlclean = require('gulp-htmlclean'),
    htmlmin = require('gulp-htmlmin'),
    gulpif = require('gulp-if'),
    cache = require('gulp-cache'),
    livereload = require('gulp-livereload'),
    del = require('del');

// Styles
gulp.task('styles', function () {
    return gulp.src('theme/' + theme + '/css/*.css')
        .pipe(sourcemaps.init())
        .pipe(autoprefixer({
            browsers: ['last 10 versions', 'Firefox >= 20', 'Opera >= 36', 'ie >= 9', 'Android >= 4.0',],
            cascade: true, //是否美化格式
            remove: false //是否删除不必要的前缀
        }))
        //.pipe(rename({ suffix: '.min' }))
        .pipe(gulpif(conditionCss, cleancss({
            keepSpecialComments: '*' //保留所有特殊前缀
        })))
        .pipe(minifycss())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('theme/' + theme + '/css'))
        .pipe(notify({ message: 'Styles task complete' }));
});

// Scripts
gulp.task('scripts', function () {
    return gulp.src('theme/' + theme + '/js/*.js')
        .pipe(sourcemaps.init())
        .pipe(jshint({
            'undef': true,
            'unused': true
        }))
        .pipe(jshint.reporter('default'))
        .pipe(concat('main.js'))
        .pipe(sourcemaps.write('.'))
        .pipe(uglify())
        .pipe(gulp.dest('theme/' + theme + '/js'))
        .pipe(notify({ message: 'Scripts task complete' }));
});

//theme-make
gulp.task('make-theme', function () {
    return gulp.src('../theme/' + theme + '.theme.pre')
    .pipe(replace('<%= ','<%='))
    .pipe(replace('<%=','<?php echo $FRONT[\''))
    .pipe(replace('=%>','\']; ?>'))
    .pipe(replace('<* loop','<?php foreach('))
    .pipe(replace('<%','$FRONT[\''))
    .pipe(replace('%>','\']'))
    .pipe(replace('<* if','<?php if ('))
    .pipe(rename({extname:'.theme'}))
    .pipe(gulp.dest('../theme/')
    .pipe(notify({message: 'Theme make done!'})))
})

//html
gulp.task('html', function () {
    return gulp.src('../theme/' + theme + '.html')
        .pipe(htmlclean())
        .pipe(htmlmin({
            removeComments: true, //清除HTML注释
            collapseWhitespace: true, //压缩HTML
            minifyJS: true, //压缩页面JS
            minifyCSS: true, //压缩页面CSS
            minifyURLs: true
        }))
        .pipe(rename({ extname: '.theme.pre' }))
        .pipe(gulp.dest('../theme/'))
        //.pipe(del('../theme/' + theme + '.html'))
        .pipe(notify({ message: 'HTML task complete' }));
});

// Images
gulp.task('images', function () {
    return gulp.src('theme/' + theme + '/img/*')
        .pipe(cache(imagemin({ optimizationLevel: 3, progressive: true, interlaced: true })))
        .pipe(gulp.dest('theme/' + theme + '/img'))
        .pipe(notify({ message: 'Images task complete' }));
});

/*
// Clean
gulp.task('clean', function(cb) {
    del(['dist/assets/css', 'dist/assets/js', 'dist/assets/img'], cb)
});
*/
// Default task
gulp.task('default', gulp.series('scripts', 'images', 'styles', 'html'));

// // Watch
// gulp.task('watch', function() {
//   // Watch .scss files
//   gulp.watch('src/styles/**/*.scss', ['styles']);
//   // Watch .js files
//   gulp.watch('src/scripts/**/*.js', ['scripts']);
//   // Watch image files
//   gulp.watch('src/images/**/*', ['images']);
//   // Create LiveReload server
//   livereload.listen();
//   // Watch any files in dist/, reload on change
//   gulp.watch(['dist/**']).on('change', livereload.changed);
// });