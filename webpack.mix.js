let mix = require('laravel-mix');

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


mix.setResourceRoot('/starter-app');

mix.js('resources/assets/js/app.js', 'public/js').version()
   .sass('resources/assets/sass/app.scss', 'public/css').version()
    .then(() => {
        var fs = require('fs');
        var manifest_file = path.resolve(__dirname) + '/' + mix.config.publicPath + '/mix-manifest.json';
        var entries = require(manifest_file);

        for(var key in entries) {
            entries[key] = mix.config.resourceRoot + entries[key];
        }

        fs.writeFile(manifest_file, JSON.stringify(entries));
    });
