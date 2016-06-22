/**
 * Created by Miguel Sirvent on 6/15/16.
 */
'use strict';
module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        vars: grunt.file.readJSON('variables.json'),
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
        copy: {
            main: {
                dest: '<%= variables.test_dir %>',
                expand: true,
                //nonull: true,
                cwd: '<%= pkg.name %>/',
                src: '**'
            }
        },
        clean: {
            options: {force: true},
            main: [
                '<%= variables.test_dir %>'
            ]
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
                        'X-Poedit-WPHeader': '<%= pkg.name %>/.php'
                    },
                    type: 'wp-plugin',
                    updateTimestamp: false,
                    updatePoFiles: true
                }
            }
        },
        watch: {
            css: {
                files: '<%= pkg.name %>/**/*.sass',
                tasks: ['sass']
            },
            js: {
                files: '<%= pkg.name %>/**/*.js',
                tasks: ['uglify']
            },
            pot: {
                files: '<%= pkg.name %>/**/*.php',
                tasks: ['makepot']
            },
            test_dir: {
                files: ['<%= pkg.name %>/**', '!*'],
                tasks: ['copy']
            }
        },
    });

    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-wp-i18n');


    //grunt
    grunt.registerTask('default', ['sass', 'uglify', 'makepot']);

    //grunt dev
    grunt.registerTask('dev', ['default', 'clean', 'copy', 'watch']);

    // ---------------------------------
    //       Deployment only.
    // ---------------------------------

    grunt.loadTasks("../grunt-helpers");


};
