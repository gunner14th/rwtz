const gulp = require('gulp'),
    sass = require('gulp-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    sourcemaps = require('gulp-sourcemaps'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    uglify = require('gulp-uglify');

const autoprefixerOptions = {
    browsers: ['last 2 versions', '> 5%', 'Firefox ESR']
};

// JS Front
gulp.task('script', async function() {
    gulp.src('./assets/js/**/*.js')
        .pipe(concat('scripts.js'))
        .pipe(gulp.dest('./dist/js'))
        .pipe(rename('scripts.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./dist/js'))
});

// JS Admin
gulp.task('admin-script', async function() {
    gulp.src('./assets/admin-js/**/*.js')
        .pipe(concat('admin-scripts.js'))
        .pipe(gulp.dest('./dist/admin-js'))
        .pipe(rename('admin-scripts.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./dist/admin-js'))
});

// CSS
gulp.task('scss',async function () {
    return gulp.src('./assets/scss/style.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer(autoprefixerOptions))
        .pipe(rename('main.css'))
        .pipe(gulp.dest('./dist/css'))
        .pipe(sourcemaps.init())
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(autoprefixer({
            browsers: ['>1%', 'last 5 versions'],
            cascade: false
        }))
        .pipe(rename('main.min.css'))
        .pipe(sourcemaps.write("."))
        .pipe(gulp.dest('./dist/css'))
});


gulp.task('watch', gulp.series('scss', 'script', 'admin-script',  (done) => {
    gulp.watch("./assets/scss/**/*.scss", gulp.series('scss'));
    gulp.watch("./assets/js/**/*.js", gulp.series('script'));
    gulp.watch("./assets/admin-js/**/*.js", gulp.series('admin-script'));
    done();
}));

