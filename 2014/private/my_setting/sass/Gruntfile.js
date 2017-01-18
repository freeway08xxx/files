

module.exports = function(grunt) {
    var pkg, taskName;
    pkg = grunt.file.readJSON('package.json');

    grunt.initConfig({
        pkg: pkg,
       ß dir: {
            bin:'src',//作業フォルダ
            release:'dist',//納品ファイル
            js: '**',
            css: '**',
            img:'**',
            sass:'src/sass'
        },

        //CSSドキュメント生成
        //clean: ['src/sass'],
        styleguide: {
            dist: {
                name: 'Style Guide',
                description: 'スタイルガイド',
                version: '1.0',
                options: {
                    name: 'SearchSuite Style Guide',
                    framework: {
                        name: 'styledocco'
                    }
                },
                files: {
                    'styleguide': 'src/sass/style_guide.scss'
                }
            }
        },
        clean: ['styleguide'],

        // ファイルを監視する
        watch: {
            // sass: {
            //     files: ['<%= dir.sass %>/*.scss'],
            //     //tasks: ['compass','csscomb']
            //     tasks: ['compass'],
            //     tasks: 'styleguide'
            // }
        },
    });


    // pakage.jsonに記載されているパッケージを自動読み込み
    for(taskName in pkg.devDependencies) {
        if(taskName.substring(0, 6) == 'grunt-') {
            grunt.loadNpmTasks(taskName);
        }
    }

    // sassをコンパイルするgruntコマンド
    grunt.registerTask('default', ['watch']);

    // CSSのドキュメントを作るためのgruntコマンド
   grunt.registerTask('cssDoc', ['clean','styleguide']);


    grunt.registerTask('eatwarnings', function() {
        grunt.warn = grunt.fail.warn = function(warning) {
            grunt.log.error(warning);
        };
    });
};
