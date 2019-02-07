// https://laravel-mix.com
const mix = require("laravel-mix");

// https://github.com/postcss/autoprefixer
const autoprefixer = require("autoprefixer")();

// https://github.com/jakob101/postcss-inline-rtl
const postcss_rtl = require("postcss-rtl")();

// https://github.com/bezoerb/postcss-image-inliner
const postcss_image_inliner = require("postcss-image-inliner")({
    assetPaths: ["resources/css"],
});

// https://github.com/postcss/postcss
const postCssPlugins = [
    autoprefixer,
    postcss_image_inliner,
    postcss_rtl,
];

mix
    .autoload({
        jquery: ["$", "jQuery"],
    })
    .sourceMaps()
    .js("resources/js/vendor.js", "public/js/vendor.min.js")
    .babel("resources/js/webtrees.js", "public/js/webtrees.min.js")
    .copy("node_modules/@fortawesome/fontawesome-free/webfonts/*", "public/webfonts/")
    .copy("node_modules/leaflet/dist/images/*", "public/css/images/")
    .copy("node_modules/dejavu-fonts-ttf/ttf/DejaVuSans.ttf", "resources/fonts/")
    .styles(["resources/css/common.css", "resources/css/clouds.css"], "resources/css/clouds.temp.css")
    .styles(["resources/css/common.css", "resources/css/clouds.css", "resources/css/colors.css"], "resources/css/colors.temp.css")
    .styles(["resources/css/common.css", "resources/css/fab.css"], "resources/css/fab.temp.css")
    .styles(["resources/css/common.css", "resources/css/minimal.css"], "resources/css/minimal.temp.css")
    .styles(["resources/css/common.css", "resources/css/webtrees.css"], "resources/css/webtrees.temp.css")
    .styles(["resources/css/common.css", "resources/css/xenea.css"], "resources/css/xenea.temp.css")
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
    ], "public/css/vendor.temp.css")
    .postCss("resources/css/administration.css", "public/css/administration.min.css", postCssPlugins)
    .postCss("resources/css/clouds.temp.css", "public/css/clouds.min.css", postCssPlugins)
    .postCss("resources/css/colors.temp.css", "public/css/colors.min.css", postCssPlugins)
    .postCss("resources/css/fab.temp.css", "public/css/fab.min.css", postCssPlugins)
    .postCss("resources/css/minimal.temp.css", "public/css/minimal.min.css", postCssPlugins)
    .postCss("resources/css/webtrees.temp.css", "public/css/webtrees.min.css", postCssPlugins)
    .postCss("resources/css/xenea.temp.css", "public/css/xenea.min.css", postCssPlugins)
    .postCss("public/css/vendor.temp.css", "public/css/vendor.min.css", postCssPlugins)
;
