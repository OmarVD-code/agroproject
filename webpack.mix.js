const mix = require('laravel-mix');

mix.js('resources/js/app.js', 'public/js')
   .vue({ version: 2 })
   .sass('resources/sass/app.scss', 'public/css')
   .browserSync({
      proxy: 'http://127.0.0.1:8000',
      files: [
        'resources/views/**/*.blade.php',
        'resources/js/**/*.vue',
        'resources/js/**/*.js',
        'public/js/**/*.js',
        'public/css/**/*.css',
      ],
      open: false,
      notify: false,
   });
