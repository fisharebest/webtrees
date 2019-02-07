let mix  = require("laravel-mix");

mix
    .autoload({
        jquery: ["$", "jQuery"],
    })
    .js("resources/js/vendor.js", "public/js/vendor.min.js")
    .babel("resources/js/webtrees.js", "public/js/webtrees.min.js")
    .sourceMaps()
    .copy("node_modules//dejavu-fonts-ttf/ttf/DejaVuSans.ttf", "resources/fonts/")
    .styles("resources/css/administration.css", "public/css/administration.min.css")
    .styles([
        "node_modules/bootstrap/dist/css/bootstrap.min.css",
        "node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css",
        "node_modules/@fortawesome/fontawesome-free/css/regular.css",
        "node_modules/@fortawesome/fontawesome-free/css/solid.css",
        "node_modules/@fortawesome/fontawesome-free/css/fontawesome.css",
        "node_modules/font-awesome-rtl/font-awesome-rtl.css",
        "node_modules/select2/dist/css/select2.min.css",
        "node_modules/typeahead.js-bootstrap4-css/typeaheadjs.css",
        "node_modules/leaflet/dist/leaflet.css",
        "node_modules/beautifymarker/leaflet-beautify-marker-icon.css",
        "node_modules/leaflet-geosearch/dist/style.css",
        "node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css",
        "node_modules/leaflet.markercluster/dist/MarkerCluster.css",
    ], "public/css/vendor.min.css")
    .styles([
        "resources/css/bootstrap-rtl.min.css",
        "node_modules/datatables.net-bs4/css/dataTables.bootstrap4.css",
        "node_modules/font-awesome/css/font-awesome.css",
        "node_modules/@fortawesome/fontawesome-free/css/regular.css",
        "node_modules/@fortawesome/fontawesome-free/css/solid.css",
        "node_modules/@fortawesome/fontawesome-free/css/fontawesome.css",
        "node_modules/font-awesome-rtl/font-awesome-rtl.css",
        "node_modules/select2/dist/css/select2.min.css",
        "node_modules/typeahead.js-bootstrap4-css/typeaheadjs.css",
        "node_modules/leaflet/dist/leaflet.css",
        "node_modules/beautifymarker/leaflet-beautify-marker-icon.css",
        "node_modules/leaflet-geosearch/dist/style.css",
        "node_modules/leaflet.markercluster/dist/MarkerCluster.Default.css",
        "node_modules/leaflet.markercluster/dist/MarkerCluster.css",
    ], "public/css/vendor-rtl.min.css")
    .copy("node_modules/@fortawesome/fontawesome-free/webfonts/*", "public/webfonts/")
    .copy("node_modules/leaflet/dist/images/*", "public/css/images/");
