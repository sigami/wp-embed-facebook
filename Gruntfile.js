/**
 * Created by Miguel Sirvent on 6/15/16.
 */
'use strict';
module.exports = function (grunt) {
    var reload = false;
    if( !(typeof grunt.option('reload') === "undefined") ){
        reload = true;
    }
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        vars: grunt.file.readJSON('variables.json'),
        watch: {
            sass: {
                options: {
                    livereload: reload
                },
                files: '<%= pkg.name %>/**/*.sass',
                tasks: ['sass']
            },
            uglify: {
                options: {
                    livereload: reload
                },
                files: '<%= pkg.name %>/**/*.js',
                tasks: ['uglify']
            },
            makepot: {
                options: {
                    livereload: reload
                },
               files: '<%= pkg.name %>/**/*.php',
               tasks: ['makepot']
            }
        },
        sass: {
            main: {
                options: {
                    style: 'compressed'
                },
                files: {
                    '<%= pkg.name %>/lib/lightbox2/css/lightbox.css': '<%= pkg.name %>/lib/lightbox2/css/lightbox.sass',
                    '<%= pkg.name %>/templates/classic/classic.css': '<%= pkg.name %>/templates/classic/classic.sass',
                    '<%= pkg.name %>/templates/default/default.css': '<%= pkg.name %>/templates/default/default.sass'
                }
            }
        },
        uglify: {
            main: {
                files: {
                    '<%= pkg.name %>/lib/js/fb.min.js': ['<%= pkg.name %>/lib/js/fb.js'],
                    '<%= pkg.name %>/lib/js/wpembedfb.min.js': ['<%= pkg.name %>/lib/js/wpembedfb.js'],
                    '<%= pkg.name %>/lib/lightbox2/js/lightbox.min.js': ['<%= pkg.name %>/lib/lightbox2/js/lightbox.js']
                },
                options: {
                    sourceMap: false
                }
            }
        },
        makepot: {
            main: {
                options: {
                    cwd: '<%= pkg.name %>/',
                    domainPath: 'lang',
                    exclude: ['node_modules', '.sass-cache', 'svn'],
                    mainFile: '<%= pkg.name %>.php',
                    potFilename: '<%= pkg.name %>.pot',
                    potHeaders: {
                        poedit: true,
                        'x-poedit-keywordslist': true,
                        'last-translator': '<%= pkg.author %>',
                        'language-team': '<%= pkg.author %>',
                        'X-Poedit-Basepath': '..',
                        'X-Poedit-SearchPathExcluded-0': '*.js',
                        'X-Poedit-WPHeader': '<%= pkg.name %>.php'
                    },
                    type: 'wp-plugin',
                    updateTimestamp: false,
                    updatePoFiles: true
                }
            }
        },
        sync: {
            test_dir: {
                files: [{
                    cwd: '<%= pkg.name %>/',
                    src: '**',
                    dest: '<%= vars.test_dir %>'
                }],
                // pretend: true, // Don't do any IO. Before you run the task with `updateAndDelete` PLEASE MAKE SURE it doesn't remove too much.
                verbose: true // Display log messages when copying files
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-wp-i18n');
    grunt.loadNpmTasks('grunt-sync');


    //grunt
    grunt.registerTask('default', ['sass', 'uglify', 'makepot']);

    //grunt dev
    grunt.registerTask('dev', function () {
        grunt.config.set('watch', {
            options: {
                livereload: reload
            },
            sass: {
                files: '<%= pkg.name %>/**/*.sass',
                tasks: ['sass','sync']
            },
            all_files: {
                files: ['<%= pkg.name %>/**'],
                tasks: ['sync']
            }
        });
        grunt.task.run(['default', 'sync', 'watch']);
    });

    // ---------------------------------
    //       Deployment only.
    // ---------------------------------

    grunt.loadTasks("../grunt-helpers");


};
