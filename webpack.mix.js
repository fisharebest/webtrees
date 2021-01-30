// https://laravel-mix.com
const mix = require("laravel-mix");

// https://github.com/postcss/postcss-import
const postcss_import = require("postcss-import")();

// https://github.com/postcss/autoprefixer
const postcss_autoprefixer = require("autoprefixer")();

// https://github.com/jakob101/postcss-inline-rtl
const postcss_rtl = require("@mjhenkes/postcss-rtl")();

// https://github.com/bezoerb/postcss-image-inliner
const postcss_image_inliner = require("postcss-image-inliner")({
    assetPaths: ["resources/css"],
    maxFileSize: 0,
});

// https://github.com/postcss/postcss-custom-properties
// Enable CSS variables in IE
const postcss_custom_properties = require("postcss-custom-properties")();

mix
    .autoload({
        jquery: ["$", "jQuery"],
    })
    .setPublicPath('./public')
    .sourceMaps(false)
    .js("resources/js/vendor.js", "public/js/vendor.min.js")
    .babel(["resources/js/webtrees.js", "resources/js/statistics.js", "resources/js/treeview.js"], "public/js/webtrees.min.js")
    .copy("node_modules/leaflet/dist/images/*", "public/css/images/")
    .copy("node_modules/dejavu-fonts-ttf/ttf/DejaVuSans.ttf", "resources/fonts/")
    .options({
            processCssUrls: false,
            postCss: [
                postcss_import,
                postcss_rtl,
                postcss_autoprefixer,
                postcss_image_inliner,
                postcss_custom_properties,
            ]
    })
    .postCss("resources/css/administration.css", "public/css/administration.min.css")
    .postCss("resources/css/clouds.css", "public/css/clouds.min.css")
    .postCss("resources/css/colors.css", "public/css/colors.min.css")
    .postCss("resources/css/colors/aquamarine.css", "public/css/colors/aquamarine.min.css")
    .postCss("resources/css/colors/ash.css", "public/css/colors/ash.min.css")
    .postCss("resources/css/colors/belgianchocolate.css", "public/css/colors/belgianchocolate.min.css")
    .postCss("resources/css/colors/bluelagoon.css", "public/css/colors/bluelagoon.min.css")
    .postCss("resources/css/colors/bluemarine.css", "public/css/colors/bluemarine.min.css")
    .postCss("resources/css/colors/coffeeandcream.css", "public/css/colors/coffeeandcream.min.css")
    .postCss("resources/css/colors/coldday.css", "public/css/colors/coldday.min.css")
    .postCss("resources/css/colors/greenbeam.css", "public/css/colors/greenbeam.min.css")
    .postCss("resources/css/colors/mediterranio.css", "public/css/colors/mediterranio.min.css")
    .postCss("resources/css/colors/mercury.css", "public/css/colors/mercury.min.css")
    .postCss("resources/css/colors/nocturnal.css", "public/css/colors/nocturnal.min.css")
    .postCss("resources/css/colors/olivia.css", "public/css/colors/olivia.min.css")
    .postCss("resources/css/colors/pinkplastic.css", "public/css/colors/pinkplastic.min.css")
    .postCss("resources/css/colors/sage.css", "public/css/colors/sage.min.css")
    .postCss("resources/css/colors/shinytomato.css", "public/css/colors/shinytomato.min.css")
    .postCss("resources/css/colors/tealtop.css", "public/css/colors/tealtop.min.css")
    .postCss("resources/css/fab.css", "public/css/fab.min.css")
    .postCss("resources/css/minimal.css", "public/css/minimal.min.css")
    .postCss("resources/css/webtrees.css", "public/css/webtrees.min.css")
    .postCss("resources/css/xenea.css", "public/css/xenea.min.css")
    .postCss("resources/css/vendor.css", "public/css/vendor.min.css");
