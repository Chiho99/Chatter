module.exports = {
    entry: __dirname + "assets/js/jquery-3.1.1.js", //ビルドするファイル
    output: {
      path: __dirname +'assets/js', //ビルドしたファイルを吐き出す場所
      filename: 'bundle.js' //ビルドした後のファイル名
    },
      module: {
      loaders: [
              //loader
              {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/,
                query://loaderに渡したいクエリパラメータを指定します
                 {
                   presets: ['es2015','stage-0']
                 }
               }
        ]
    },

    entry: __dirname + "assets/js/jquery-migrate-1.4.1.js", //ビルドするファイル
    output: {
      path: __dirname +'assets/js', //ビルドしたファイルを吐き出す場所
      filename: 'bundle.js' //ビルドした後のファイル名
    },
      module: {
      loaders: [
              //loader
              {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/,
                query://loaderに渡したいクエリパラメータを指定します
                 {
                   presets: ['es2015','stage-0']
                 }
               }
        ]
    },

    entry: __dirname + "assets/js/bootstrap.js", //ビルドするファイル
    output: {
      path: __dirname +'assets/js', //ビルドしたファイルを吐き出す場所
      filename: 'bundle.js' //ビルドした後のファイル名
    },
      module: {
      loaders: [
              //loader
              {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/,
                query://loaderに渡したいクエリパラメータを指定します
                 {
                   presets: ['es2015','stage-0']
                 }
               }
        ]
    },

    entry: __dirname + "assets/js/app.js", //ビルドするファイル
    output: {
      path: __dirname +'assets/js', //ビルドしたファイルを吐き出す場所
      filename: 'bundle.js' //ビルドした後のファイル名
    },
      module: {
      loaders: [
              //loader
              {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/,
                query://loaderに渡したいクエリパラメータを指定します
                 {
                   presets: ['es2015','stage-0']
                 }
               }
        ]
    }
  };