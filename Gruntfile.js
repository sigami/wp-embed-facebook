/**
 * Created by Miguel Sirvent on 6/15/16.
 */
'use strict';
module.exports = function (grunt) {
    let reload = false;
    if (!(typeof grunt.option('reload') === "undefined")) {
        reload = true;
    }
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
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
                    '<%= pkg.name %>/inc/wef-lightbox/css/lightbox.css': '<%= pkg.name %>/inc/wef-lightbox/css/lightbox.sass',
                    '<%= pkg.name %>/templates/custom_embeds/styles.css': '<%= pkg.name %>/templates/custom_embeds/styles.scss',
                }
            }
        },
        uglify: {
            main: {
                files: {
                    '<%= pkg.name %>/inc/js/fb.min.js': ['<%= pkg.name %>/inc/js/fb.js'],
                    '<%= pkg.name %>/inc/wef-lightbox/js/lightbox.min.js': ['<%= pkg.name %>/inc/wef-lightbox/js/lightbox.js']
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
        }
    });

    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-wp-i18n');

    //grunt
    grunt.registerTask('default', ['sass', 'uglify', 'makepot']);

    // ---------------------------------
    //       Deployment only.
    // ---------------------------------

    grunt.loadTasks("../grunt-helpers/");
};
