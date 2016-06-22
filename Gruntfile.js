/**
 * Created by Miguel Sirvent on 6/15/16.
 */
'use strict';
module.exports = function (grunt) {
    grunt.initConfig({
        slug: 'wp-embed-facebook',
        package_json: grunt.file.readJSON('package.json'),
        variables: grunt.file.readJSON('variables.json'),
        sass: {
            main: {
                options: {
                    style: 'compressed'
                },
                files: {
                    '<%= slug %>/lib/lightbox2/css/lightbox.css': '<%= slug %>/lib/lightbox2/css/lightbox.sass',
                    '<%= slug %>/templates/classic/classic.css': '<%= slug %>/templates/classic/classic.sass',
                    '<%= slug %>/templates/default/default.css': '<%= slug %>/templates/default/default.sass'
                }
            }
        },
        uglify: {
            main: {
                files: {
                    '<%= slug %>/lib/js/fb.min.js': ['<%= slug %>/lib/js/fb.js'],
                    '<%= slug %>/lib/js/wpembedfb.min.js': ['<%= slug %>/lib/js/wpembedfb.js'],
                    '<%= slug %>/lib/lightbox2/js/lightbox.min.js': ['<%= slug %>/lib/lightbox2/js/lightbox.js']
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
                cwd: '<%= slug %>/',
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
                    cwd: '<%= slug %>/',
                    domainPath: 'lang',
                    exclude: ['node_modules', '.sass-cache', 'svn'],
                    mainFile: '<%= slug %>.php',
                    potFilename: '<%= slug %>.pot',
                    potHeaders: {
                        poedit: true,
                        'x-poedit-keywordslist': true,
                        'last-translator': '<%= package_json.author %>',
                        'language-team': '<%= package_json.author %>',
                        'X-Poedit-Basepath': '..',
                        'X-Poedit-SearchPathExcluded-0': '*.js',
                        'X-Poedit-WPHeader': '<%= slug %>/.php'
                    },
                    type: 'wp-plugin',
                    updateTimestamp: true,
                    updatePoFiles: true
                }
            }
        },
        watch: {
            css: {
                files: '<%= slug %>/**/*.sass',
                tasks: ['sass']
            },
            js: {
                files: '<%= slug %>/**/*.js',
                tasks: ['uglify']
            },
            pot: {
                files: '<%= slug %>/**/*.php',
                tasks: ['makepot']
            },
            test_dir: {
                files: ['<%= slug %>/**', '!*'],
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
