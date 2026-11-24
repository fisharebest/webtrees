const fs = require('fs');
const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const CopyPlugin = require('copy-webpack-plugin');

const cssEntries = ['administration', 'clouds', 'colors', 'fab', 'minimal', 'webtrees', 'xenea', 'vendor'];

module.exports = {
  devtool: false,

  entry: {
    'js/vendor.min': path.resolve(__dirname, 'resources/js/vendor.js'),
    'js/webtrees.min': [
      path.resolve(__dirname, 'resources/js/webtrees.js'),
      path.resolve(__dirname, 'resources/js/statistics.js'),
      path.resolve(__dirname, 'resources/js/treeview.js')
    ],
    ...Object.fromEntries(cssEntries.map(name => [`css/${name}.min`, path.resolve(__dirname, `resources/css/${name}.css`)]))
  },

  output: {
    path: path.resolve(__dirname, 'public'),
    filename: '[name].js',
    clean: false
  },

  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: { presets: ['@babel/preset-env'] }
        }
      },
      {
        test: /jquery-colorbox/,
        use: {
          loader: 'imports-loader',
          options: { imports: ['default jquery jQuery', 'default jquery $'] }
        }
      },
      {
        test: /\.css$/,
        use: [
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: { url: false, importLoaders: 1 }
          },
          {
            loader: 'postcss-loader',
            options: {
              postcssOptions: {
                plugins: [
                  'postcss-import',
                  ['postcss-rtlcss', { safeBothPrefix: true }],
                  'autoprefixer',
                  ['postcss-image-inliner', { assetPaths: ['resources/css'], maxFileSize: 0 }]
                ]
              }
            }
          }
        ]
      }
    ]
  },

  plugins: [
    new webpack.ProvidePlugin({ $: 'jquery', jQuery: 'jquery' }),
    new MiniCssExtractPlugin({ filename: '[name].css' }),
    new CopyPlugin({
      patterns: [
        { from: 'node_modules/leaflet/dist/images/*', to: 'css/images/[name][ext]' },
        { from: 'node_modules/dejavu-fonts-ttf/ttf/DejaVuSans.ttf', to: path.resolve(__dirname, 'resources/fonts/DejaVuSans.ttf') }
      ]
    }),
    {
      apply (compiler) {
        // CSS-only entries do not need webpack's empty companion JavaScript files.
        compiler.hooks.afterEmit.tap('RemoveEmptyJsPlugin', () => {
          for (const name of cssEntries) {
            fs.rmSync(path.resolve(__dirname, `public/css/${name}.min.js`), { force: true });
          }
        });
      }
    }
  ],

  optimization: {
    minimizer: ['...', new CssMinimizerPlugin()]
  },

  performance: {
    hints: false
  }
};
