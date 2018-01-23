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
    'node_modules/datatables.net-responsive-bs4/css/responsive.bootstrap4.css',
    'node_modules/@fortawesome/fontawesome-free/css/regular.css',
    'node_modules/@fortawesome/fontawesome-free/css/solid.css',
    'node_modules/@fortawesome/fontawesome-free/css/fontawesome.css',
    'node_modules/font-awesome-rtl/font-awesome-rtl.css',
    'node_modules/select2/dist/css/select2.min.css',
    'node_modules/typeahead.js-bootstrap4-css/typeaheadjs.css',
    'node_modules/leaflet/dist/leaflet.css',
    'node_modules/beautifymarker/leaflet-beautify-marker-icon.css',
    'node_modules/leaflet-geosearch/dist/style.css',
    'node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css',
    'node_modules/leaflet.markercluster/dist/MarkerCluster.css',
  ], path + 'css/vendor.css')
  .styles([
    'resources/assets/css/bootstrap-rtl.min.css',
    'node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css',
    'node_modules/datatables.net-responsive-bs4/css/responsive.bootstrap4.css',
    'node_modules/font-awesome/css/font-awesome.css',
    'node_modules/@fortawesome/fontawesome-free/css/regular.css',
    'node_modules/@fortawesome/fontawesome-free/css/solid.css',
    'node_modules/@fortawesome/fontawesome-free/css/fontawesome.css',
    'node_modules/font-awesome-rtl/font-awesome-rtl.css',
    'node_modules/select2/dist/css/select2.min.css',
    'node_modules/typeahead.js-bootstrap4-css/typeaheadjs.css',
    'node_modules/leaflet/dist/leaflet.css',
    'node_modules/beautifymarker/leaflet-beautify-marker-icon.css',
    'node_modules/leaflet-geosearch/dist/style.css',
    'node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css',
    'node_modules/leaflet.markercluster/dist/MarkerCluster.css',
  ], path + 'css/vendor-rtl.css')
  .copy('node_modules/@fortawesome/fontawesome-free/webfonts/*', path + 'webfonts/')
  .copy('node_modules//dejavu-fonts-ttf/ttf/DejaVuSans.ttf', 'resources/fonts/')
  .copy('node_modules/leaflet/dist/images/*', path + 'css/images/');
