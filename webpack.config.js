// webpack.config.js
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');  // bundled with webpack
const CopyPlugin = require('copy-webpack-plugin');

// CSS entry points
const cssEntries = [
  'administration',
  'clouds',
  'colors',
  'fab',
  'minimal',
  'webtrees',
  'xenea',
  'vendor',
];

const cssEntryPoints = Object.fromEntries(
  cssEntries.map(name => [`css/${name}.min`, path.resolve(__dirname, `resources/css/${name}.css`)])
);

module.exports = (env, argv) => ({
    devtool: false,

    entry: {
      'js/vendor.min': path.resolve(__dirname, 'resources/js/vendor.js'),
      'js/webtrees.min': [
        path.resolve(__dirname, 'resources/js/webtrees.js'),
        path.resolve(__dirname, 'resources/js/statistics.js'),
        path.resolve(__dirname, 'resources/js/treeview.js'),
      ],
      ...cssEntryPoints,
    },

    output: {
      path: path.resolve(__dirname, 'public'),
      filename: '[name].js',
      clean: false,
    },

    module: {
      rules: [
        {
          test: /\.js$/,
          exclude: /node_modules/,
          use: {
            loader: 'babel-loader',
            options: {
              presets: ['@babel/preset-env'],
            },
          },
        },
        {
          // jquery-colorbox uses jQuery/$ as free variables without importing
          test: /jquery-colorbox/,
          use: {
            loader: 'imports-loader',
            options: {
              imports: ['default jquery jQuery', 'default jquery $'],
            },
          },
        },
        {
          test: /\.css$/,
          use: [
            MiniCssExtractPlugin.loader,
            {
              loader: 'css-loader',
              options: {
                url: false,
                importLoaders: 1,
              },
            },
            {
              loader: 'postcss-loader',
              options: {
                postcssOptions: {
                  plugins: [
                    'postcss-import',
                    ['postcss-rtlcss', { safeBothPrefix: true }],
                    'autoprefixer',
                    ['postcss-image-inliner', { assetPaths: ['resources/css'], maxFileSize: 0 }],
                  ],
                },
              },
            },
          ],
        },
      ],
    },

    plugins: [
      new MiniCssExtractPlugin({
        filename: '[name].css',
      }),
      new CopyPlugin({
        patterns: [
          { from: 'node_modules/leaflet/dist/images/*', to: 'css/images/[name][ext]' },
          { from: 'node_modules/dejavu-fonts-ttf/ttf/DejaVuSans.ttf', to: path.resolve(__dirname, 'resources/fonts/DejaVuSans.ttf') },
        ],
      }),
      {
        // Remove empty JS files generated from CSS-only entries
        apply(compiler) {
          compiler.hooks.afterEmit.tap('RemoveEmptyJsPlugin', () => {
            const fs = require('fs');
            cssEntries.forEach(name => {
              const jsFile = path.resolve(__dirname, `public/css/${name}.min.js`);
              if (fs.existsSync(jsFile)) {
                fs.unlinkSync(jsFile);
              }
            });
          });
        },
      },
    ],

    optimization: {
      minimizer: [
        new TerserPlugin(),
        new CssMinimizerPlugin(),
      ],
    },

    resolve: {
      alias: {
        // jQuery 4's exports map resolves to different files for import vs require,
        // which causes issues with CJS packages receiving a namespace object instead
        // of the jQuery function. Pin to the CJS build for universal compatibility.
        jquery: path.resolve(__dirname, 'node_modules/jquery/dist/jquery.js'),
      },
    },

    performance: {
      hints: false,
    },
});
