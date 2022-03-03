const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

// mix.js('resources/js/app.js', 'public/js')
//     .sass('resources/sass/app.scss', 'public/css')

/**
 * 개발, 배포 환경 설정
 */
 if (mix.inProduction()) {
    /**
     * Production - 배포
     * 
     * 브라우저가 오래된 코드 사본을 제공하는 대신에
     * 새로운 자산을 강제로 로드하도록합니다. (no cache - 해시 고유화)
     * 
     * ex) <script src="{{ mix('js/app.js') }}"></script> 와 같은 mix 파일들 처리
     */
    // mix.version(); // Version all compiled assets.
 
 } else {
    /**
     * Develoment - 개발
     * 
     * 코드 저장시 새로고침을 적용합니다.
     * php artisan serve & (백그라운드 실행 후)
     * npm run watch 로 사용
     * 
     */
    mix.browserSync({
       proxy: 'http://127.0.0.1:8000'
    });
 }
 