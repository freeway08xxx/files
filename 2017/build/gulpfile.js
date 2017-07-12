'use strict';

const path = require('path'); //node.jsのCoreモジュール
const gulp = require('gulp');
const concat = require('gulp-concat'); //jsの結合
const autoprefixer = require('autoprefixer');
const plumber = require('gulp-plumber'); //エラーで止まらせない
const eslint = require('gulp-eslint');
const webpack = require('webpack');
const notifier = require('node-notifier'); //デスクトップ通知

// アプリケーションの配置ディレクトリ
const APP_ROOT = `${path.resolve(__dirname)}/app`;


// 設定
const config = {
  dist: {
    directory: `${APP_ROOT}/ `
  },
  js: {
    files: [
      `${APP_ROOT}/js/**/*.js`
    ],
    vendor: {
      output: {
        filename: 'vendor.js'
      },
      files: [
        'node_modules/jquery/dist/jquery.min.js',
        'node_modules/lodash/dist/lodash.min.js',
      ]
    }
  },
  webpack: {
    entry: `${APP_ROOT}/js/app.js`,
    devtool: '#source-map',
    output: {
      path: `${APP_ROOT}/dist`,
      filename: 'app.js'
    },
    externals: {
      document: 'document',
      jquery: '$'
    },
    resolve: {
      root: `${APP_ROOT}/js`,
      extensions: ['', '.js']
    },
    module: {
      loaders: [
        {
          test: /\.js$/,
          loader: 'babel-loader',
          query: {
            presets: ['es2015']
          }
        }
      ]
    }
  }
};


// エラー時のnotify表示
const notify = (taskName, error) => {
  const title = `[task]${taskName} ${error.plugin}`;
  const errorMsg = `error: ${error.message}`;
  /* eslint-disable no-console */
  console.error(`${title}\n${error}`);
  notifier.notify({
    title: title,
    message: errorMsg,
    time: 3000
  });
};


// js系処理
const webpackBuild = (conf, cb) => {
  webpack(conf, (err) => {
    if (err) {
      // eslint-disable-next-line no-console
      console.error(err);
      throw err;
    }
    if (!cb.called) {
      cb.called = true;
      return cb();
    }
  });
};

gulp.task('webpack', ['lint'], (cb) => {
  const conf = config.webpack;
  webpackBuild(conf, cb);
});
gulp.task('watch-webpack', ['lint'], (cb) => {
  const conf = Object.assign(config.webpack, { watch: true });
  webpackBuild(conf, cb);
});

// jsのlint処理
gulp.task('lint', () => {
  return gulp.src(config.js.files)
    .pipe(plumber({
      errorHandler: (error) => {
        notify('lint', error);
      }
    }))
    .pipe(eslint())
    .pipe(eslint.format())
    .pipe(eslint.failOnError())
    .pipe(plumber.stop());
});

// npmで入れたフロントエンドライブラリのconcat処理
gulp.task('vendor', () => {
  return gulp.src(config.js.vendor.files)
    .pipe(plumber({
      errorHandler: (error) => {
        notify('vendor', error);
      }
    }))
    .pipe(concat(config.js.vendor.output.filename))
    .pipe(plumber.stop())
    .pipe(gulp.dest(config.dist.directory));
});

// jsビルド処理
gulp.task('build', ['vendor', 'webpack']);


gulp.task('default', ['build']);
