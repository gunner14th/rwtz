var gulp = require('gulp'),
    sass = require('gulp-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    notify = require('gulp-notify'),
    sourcemaps = require('gulp-sourcemaps'),
    watch = require('glob-watcher'),
    cleanCSS = require('gulp-clean-css'),
    concat = require('gulp-concat'),
    rename = require('gulp-rename'),
    uglify = require('gulp-uglify');

var autoprefixerOptions = {
    browsers: ['last 2 versions', '> 5%', 'Firefox ESR']
};

var vendor_js = [
    'node_modules/slick-carousel/slick/slick.js',
    // 'node_modules/chart.js/dist/Chart.js',
    'node_modules/select2/dist/js/select2.js',
    'node_modules/sweetalert2/dist/sweetalert2.js',
    // 'lib/jquery.pwdMeter.js',
    // 'lib/popperjs/popper.min.js',
    // 'lib/popperjs/tippy-bundle.umd.min.js',
    // 'node_modules/lightbox2/dist/js/lightbox.js'
];

var vendor_styles = [
    'node_modules/slick-carousel/slick/slick.css',
    // 'node_modules/chart.js/dist/Chart.css',
    'node_modules/select2/dist/css/select2.css',
    'node_modules/sweetalert2/dist/sweetalert2.css',
    // 'node_modules/lightbox2/dist/css/lightbox.css'
];

// JS
gulp.task('script', async function() {
    gulp.src('./assets/js/**/*.js')
        .pipe(concat('scripts.js'))
        .pipe(gulp.dest('./dist/js'))
        .pipe(rename('scripts.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./dist/js'))
});

gulp.task('lib-script',async function() {
    gulp.src(vendor_js)
        .pipe(concat('libs-scripts.js'))
        .pipe(gulp.dest('./dist/js'))
        .pipe(rename('libs-scripts.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./dist/js'));
});

gulp.task('lib-style',async function() {
    gulp.src(vendor_styles)
        .pipe(concat('libs-styles.css'))
        .pipe(gulp.dest('./dist/css'));
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
        // .pipe(cleanCSS())
        .pipe(sourcemaps.write("."))
        .pipe(gulp.dest('./dist/css'))
});


gulp.task('watch', gulp.series('scss', 'script',  (done) => {
    gulp.watch("./assets/scss/**/*.scss", gulp.series('scss'));
    gulp.watch("./assets/js/**/*.js", gulp.series('script'));
    done();
}));

