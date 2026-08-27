module.exports = function( grunt ) {
    // Project configuration.
    grunt.initConfig( {
        pkg: grunt.file.readJSON( 'package.json' ),
        uglify: {
            options: {
                sourceMap: true
            },
            build: {
                src: [ 'js/caleran.js', 'js/jquery.hammer.js' ],
                dest: 'build/js/caleran.min.js'
            }
        },
        sass: {
            dist: {
                options: {
                    style: 'compressed'
                },
                files: {
                    'build/css/caleran.min.css': 'css/caleran.scss'
                }
            }
        },
        watch: {
            scripts: {
                files: [ 'js/caleran.js' ],
                tasks: [ 'uglify', 'jsObfuscate' ]
            },
            styles: {
                files: [ 'css/caleran.scss' ],
                tasks: 'sass'
            },
            docs: {
                files: [ 'readme.md', 'docs/includes/template.html' ],
                tasks: 'markdown'
            },
            test: {
                files: [ 'tests/caleran.test.js' ],
                tasks: 'jasmine'
            }
        },
        markdown: {
            all: {
                files: [ {
                    expand: true,
                    src: 'readme.md',
                    dest: 'docs/',
                    ext: '.html'
                } ],
                options: {
                    template: 'docs/includes/template.html',
                    autoTemplate: true,
                    autoTemplateFormat: 'html'
                }
            }
        },
        jsObfuscate: {
            default: {
                files: {
                    'build/js/caleran.obf.js': 'build/js/caleran.min.js'
                }
            }
        },
        compress: {
            main: {
                options: {
                    archive: 'output/caleran.zip'
                },
                files: [ {
                    src: [ 'css/**' ],
                    dest: '/',
                }, {
                    src: [ 'build/**' ],
                    dest: '/'
                }, {
                    src: [ 'js/**' ],
                    dest: '/'
                }, {
                    src: [ 'docs/**' ],
                    dest: '/'
                }, {
                    src: [ 'gruntfile.js', '.gitignore', '.jshintrc', 'package.json', 'readme.md', 'CHANGELOG' ],
                    dest: '/'
                }, ]
            },
            screenshots: {
                options: {
                    archive: 'output/screenshots.zip'
                },
                files: [ {
                    expand: true,
                    cwd: 'toolbox/screenshots/',
                    src: [ '*.png', '!inline.png', '!thumbnail.png' ],
                    dest: '/'
                } ]
            },
        },
        copy: {
            main: {
                expand: true,
                cwd: 'toolbox',
                src: ['inline.png','thumbnail.png'],
                dest: 'output/',
            },
        },
        browserSync: {
            dev: {
                bsFiles: {
                    src : [
                        'build/css/*.min.css',
                        'build/js/*.min.js'
                    ]
                },
                options: {
                    watchTask: true,
                    server: {
                        baseDir: "./"
                    },
                    startPath: "docs/single-test.html"
                }
            },
            docs: {
                bsFiles: {
                    src : [
                        'docs/**/*'
                    ]
                },
                options: {
                    watchTask: true,
                    server: {
                        baseDir: "./"
                    },
                    startPath: "docs/readme.html"
                }
            },
            test: {
              bsFiles: {
                    src: [
                        'tests/caleran.test.js',
                        'tests/output/caleran.html'
                    ]
                },
                options: {
                    watchTask: true,
                    online: false,
                    server: {
                        baseDir: "./"
                    },
                    startPath: "tests/output/caleran.html"
                }
            }
        },
        jasmine: {
            src: [ 'js/caleran.js' ],
            options: {
                vendor: [ 'build/js/jquery.min.js', 'build/js/moment.min.js', 'js/jquery.hammer.js'],
                specs: 'tests/caleran.test.js',
                keepRunner: true,
                outfile: 'tests/output/caleran.html',
                styles: [ 'build/css/caleran.min.css', 'http://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' ]
            }
        }
    } );
    grunt.loadNpmTasks( 'grunt-markdown' );
    grunt.loadNpmTasks( 'grunt-contrib-uglify' );
    grunt.loadNpmTasks( 'grunt-contrib-sass' );
    grunt.loadNpmTasks( 'grunt-contrib-watch' );
    grunt.loadNpmTasks( 'js-obfuscator' );
    grunt.loadNpmTasks( 'grunt-contrib-compress' );
    grunt.loadNpmTasks( 'grunt-contrib-copy' );
    grunt.loadNpmTasks( 'grunt-browser-sync' );
    grunt.loadNpmTasks( 'grunt-contrib-jasmine' );
    grunt.registerTask( 'min', [ 'uglify', 'jsObfuscate', 'sass', 'markdown', 'compress', 'copy' ] );
    grunt.registerTask( 'default', [ 'uglify', 'jsObfuscate', 'sass', 'markdown', 'compress', 'copy', 'jasmine' ] );
    grunt.registerTask( 'watcher', [ 'browserSync:dev', 'watch' ] );
    grunt.registerTask( 'watchdocs', [ 'browserSync:docs', 'watch' ] );
    grunt.registerTask( 'test', ['browserSync:test', 'watch:test'] );
};
