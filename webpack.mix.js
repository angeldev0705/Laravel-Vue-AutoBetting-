const path = require('path')
const mix = require('laravel-mix')

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

// this plugin is required to automatically load Vuetify components
const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin')
const SWPrecacheWebpackPlugin = require('sw-precache-webpack-plugin')

const _module = {
  rules: [
    {
      test: /\.(wav|mp3|webm)$/i,
      use: [
        {
          loader: 'file-loader',
          options: {
            // it's important to specify [path], so it's passed to outputPath() function
            name: '[path][name].[ext]',
            // generate custom output path depending on package ID
            outputPath: (path) => {
              let outputPath = ''

              const matches = path.match(/^packages\/([a-z0-9-]+)\/.+\/([a-z0-9-]+\.[a-z0-9]{3,4})$/i)

              if (matches !== null && matches.length === 3) {
                outputPath = `audio/${matches[1]}/${matches[2]}`
              } else {
                outputPath = `${path.replace('resources/', '')}`
              }

              return outputPath
            }
          }
        }
      ]
    }
  ]
}

const plugins = [
  new VuetifyLoaderPlugin(),
  new SWPrecacheWebpackPlugin({
    cacheId: 'stake-pwa',
    filename: 'service-worker.js',
    minify: true,
    stripPrefix: 'public/',
    handleFetch: true,
    staticFileGlobs: [
      'public/**/*.{css,eot,svg,ttf,woff,woff2}',
      'public/js/{offline,pages}.js',
      'public/lang/*.json'
    ],
    navigateFallback: '/offline',
    runtimeCaching: [
      {
        urlPattern: /^https:\/\/fonts\.(?:googleapis|gstatic)\.com\//,
        handler: 'networkFirst'
      },
      {
        urlPattern: /\.(?:png|jpg|jpeg|svg)$/,
        handler: 'networkFirst',
        options: {
          cacheName: 'images',
          expiration: {
            maxEntries: 50
          }
        }
      },
      {
        urlPattern: /\/js\/[^.]+\.js$/,
        handler: 'networkFirst',
        options: {
          cacheName: 'js'
        }
      }
    ]
  })
]

const resolve = {
  alias: {
    '~': path.resolve(__dirname, 'resources/js'),
    packages: path.resolve(__dirname, 'packages')
  },
  extensions: ['.js', '.json', '.vue']
}

const optimization = {
  splitChunks: {
    cacheGroups: {
      // games: {
      //   test: module => {
      //     const match = module.identifier().match(/\\packages\\[a-z-]+\\resources\\/)
      //     return !!match
      //   },
      //   name: (module, chunks, cacheGroupKey) => {
      //     const match = module.identifier().match(/\\packages\\([a-z-]+)\\resources\\/)
      //     const allChunksNames = chunks.map((item) => item.name).join('~')
      //     return `js/games/${match[1]}/${allChunksNames}`
      //   }
      // },
      sharedGamesAssets: {
        test: module => {
          const match = module.identifier().includes('\\components\\Games\\') || module.identifier().includes('\\mixins\\Game\\')
          return !!match
        },
        name: module => {
          return 'js/games/common'
        }
      }
    }
  }
}


mix.webpackConfig({ module: _module, plugins, resolve, optimization })

mix
  .js('resources/js/app.js', 'public/js')
  .sourceMaps()

if (mix.inProduction()) {
  mix.version()
}
