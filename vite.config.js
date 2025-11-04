import { defineConfig } from 'vite';
import autoprefixer from 'autoprefixer';
import postcssImport from 'postcss-import';
import postcssRTLCSS from 'postcss-rtlcss';
import postcssImageInliner from 'postcss-image-inliner';

export default defineConfig({
  css: {
    postcss: {
      plugins: [
        autoprefixer(),
        postcssImport(),
        postcssRTLCSS({safeBothPrefix: true}),
        postcssImageInliner({ assetPaths: ['resources/css'], maxFileSize: 0 }),
      ],
    },
  },

  publicDir: false, // Suppress the warning about public and assets directories being the same.

  build: {
    assetsDir: '',
    chunkSizeWarningLimit: 1024, // Suppress the warning about chunks larger than 500K.
    emptyOutDir: false,
    outDir: 'public',
    rollupOptions: {
      input: {
        'css/administration': 'resources/css/administration.css',
        'css/clouds': 'resources/css/clouds.css',
        'css/colors': 'resources/css/colors.css',
        'css/fab': 'resources/css/fab.css',
        'css/minimal': 'resources/css/minimal.css',
        'css/webtrees': 'resources/css/webtrees.css',
        'css/xenea': 'resources/css/xenea.css',
        'css/vendor': 'resources/css/vendor.css',
        'js/vendor': 'resources/js/vendor.js',
        'js/webtrees': 'resources/js/webtrees.js',
      },
      output: {
        assetFileNames: '[name].min.[ext]',
        entryFileNames: '[name].min.js',
      },
    }
  },
});
