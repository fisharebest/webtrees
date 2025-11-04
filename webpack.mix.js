// https://laravel-mix.com
const mix = require('laravel-mix');

// https://github.com/postcss/postcss-import
const postcssImport = require('postcss-import')();

// https://github.com/postcss/autoprefixer
const postcssAutoprefixer = require('autoprefixer')();

// https://github.com/elchininet/postcss-rtlcss
const postcssRTLCSS = require('postcss-rtlcss')({safeBothPrefix: true});

// https://github.com/bezoerb/postcss-image-inliner
const postcssImageInliner = require('postcss-image-inliner')({
  assetPaths: ['resources/css'],
  maxFileSize: 0
});

mix
  .autoload({
    jquery: ['$', 'jQuery']
  })
  .setPublicPath('./public')
  .sourceMaps(false)
  .js('resources/js/vendor.js', 'public/js/vendor.min.js')
  .babel(['resources/js/webtrees.js', 'resources/js/statistics.js', 'resources/js/treeview.js'], 'public/js/webtrees.min.js')
  .copy('node_modules/leaflet/dist/images/*', 'public/css/images/')
  .copy('node_modules/dejavu-fonts-ttf/ttf/DejaVuSans.ttf', 'resources/fonts/')
  .options({
    processCssUrls: false,
    postCss: [
      postcssImport,
      postcssRTLCSS,
      postcssAutoprefixer,
      postcssImageInliner
    ]
  })
  .postCss('resources/css/administration.css', 'public/css/administration.min.css')
  .postCss('resources/css/clouds.css', 'public/css/clouds.min.css')
  .postCss('resources/css/colors.css', 'public/css/colors.min.css')
  .postCss('resources/css/fab.css', 'public/css/fab.min.css')
  .postCss('resources/css/minimal.css', 'public/css/minimal.min.css')
  .postCss('resources/css/webtrees.css', 'public/css/webtrees.min.css')
  .postCss('resources/css/xenea.css', 'public/css/xenea.min.css')
  .postCss('resources/css/vendor.css', 'public/css/vendor.min.css');
