let mix  = require('laravel-mix');
let path = 'public/assets-2.0.0/';

mix
  .autoload({
    jquery: ['$', 'jQuery']
  })
  .js('resources/assets/js/vendor.js', path + 'js/vendor.js')
  .babel('resources/assets/js/webtrees.js', path + 'js/webtrees.js')
  .sourceMaps()
  .styles([
    'node_modules/bootstrap/dist/css/bootstrap.min.css',
    'node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css',
    'node_modules/@fortawesome/fontawesome-free-webfonts/css/fa-regular.css',
    'node_modules/@fortawesome/fontawesome-free-webfonts/css/fa-solid.css',
    'node_modules/@fortawesome/fontawesome-free-webfonts/css/fontawesome.css',
    'node_modules/font-awesome-rtl/font-awesome-rtl.css',
    'node_modules/select2/dist/css/select2.min.css',
    'node_modules/typeahead.js-bootstrap4-css/typeaheadjs.css'
  ], path + 'css/vendor.css')
  .styles([
    'resources/assets/css/bootstrap-rtl.min.css',
    'node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css',
    'node_modules/font-awesome/css/font-awesome.css',
    'node_modules/@fortawesome/fontawesome-free-webfonts/css/fa-regular.css',
    'node_modules/@fortawesome/fontawesome-free-webfonts/css/fa-solid.css',
    'node_modules/@fortawesome/fontawesome-free-webfonts/css/fontawesome.css',
    'node_modules/font-awesome-rtl/font-awesome-rtl.css',
    'node_modules/select2/dist/css/select2.min.css',
    'node_modules/typeahead.js-bootstrap4-css/typeaheadjs.css'
  ], path + 'css/vendor-rtl.css')
  .copy('node_modules/@fortawesome/fontawesome-free-webfonts/webfonts/*', path + 'webfonts/')
  .copy('node_modules//dejavu-fonts-ttf/ttf/DejaVuSans.ttf', 'resources/fonts/');
