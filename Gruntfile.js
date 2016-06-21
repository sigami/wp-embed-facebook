/**
 * Created by Miguel Sirvent on 6/15/16.
 */
'use strict';
module.exports = function (grunt) {
    grunt.initConfig({
        slug: 'wp-embed-facebook',
        variables: grunt.file.readJSON('variables.json'),
        package_json: grunt.file.readJSON('package.json'),
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
                tasks: ['copy:test_dir']
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
    grunt.registerTask('default', [
        'sass',
        'uglify',
        'makepot'
    ]);

    //grunt dev
    grunt.registerTask('dev', [
        'default',
        'clean:main',
        'copy:main',
        'watch'
    ]);

    // ---------------------------------
    //       Deployment only.
    // ---------------------------------

    grunt.loadNpmTasks('grunt-confirm');
    grunt.loadNpmTasks('grunt-exec');
    grunt.loadNpmTasks('grunt-version');


    var commands = {
        //add and commit
        git: 'git add --all && git commit -m "<%= commit_msg %>" && git push',
        //add and commit
        svn: 'cd svn && svn add --force * --auto-props --parents --depth infinity -q && svn ci -m "<%= commit_msg %>"',
        //remove files not added to svn
        svn_clean: "cd svn && svn revert -R . && svn up && svn status | grep ^? | awk '{print $2}' | xargs rm -r",
        //remove un-present files in svn
        svn_clean2: "cd svn && svn revert -R . && svn up && svn status | grep ^! | awk '{print $2}' | xargs svn rm",
        //start svn repository
        svn_start: 'svn co https://plugins.svn.wordpress.org/<%= slug %>/ svn'
    };

    grunt.config.set('exec', commands);

    // grunt dev-update --commit="Future things"
    grunt.registerTask('dev-push', function () {
        grunt.config.set('commit_msg', (grunt.option('commit') || 'Dev Update v<%= package_json.version %>'));
        grunt.config.set('clean', {
            dev: ['svn/trunk/*','!svn/trunk/readme.txt']
        });
        grunt.config.set('copy', {
            trunk: {
                dest: 'svn/trunk/',
                expand: true,
                //nonull: true,
                cwd: '<%= slug %>/',
                src: ['**', '!readme.txt']
            }
        });
        grunt.config.set('confirm', {
            "Development Update": {
                options: {
                    question: 'Development update continue? v<%= package_json.version %>\nCommit msg: <%= commit_msg %>\n',
                    input: '_key:y'
                }
            }
        });
        grunt.task.run([
            'default',
            'exec:svn_clean',
            'confirm',
            'clean',
            'copy'
        ]);
        grunt.task.run(['exec:git','exec:svn']);
    });

    //grunt bump --type=major|minor|patch|prerelease(default=patch) --commit="Awesome things"
    grunt.registerTask('bump', function () {
        var semver = require('semver'),
            newVersion = semver.inc(grunt.config.get('package_json.version'), (grunt.option('type') || 'patch'));

        grunt.config.set('commit_msg', (grunt.option('commit') || 'Update v' + newVersion));

        grunt.config.set('clean', {
            bump: ['svn/trunk/', 'svn/tags/' + newVersion + '/']
        });
        grunt.config.set('version', {
            options: {
                release: newVersion
            },
            plugin_file: {
                options: {
                    prefix: '[^\\-]tag:\\s*'
                },
                src: ['<%= slug %>/readme.txt']
            },
            stable_tag: {
                options: {
                    prefix: '[^\\-]Version:\\s*'
                },
                src: ['<%= slug %>/<%= slug %>.php']
            },
            package_json: {
                src: ['package.json']
            }
        });
        grunt.config.set('copy', {
            trunk: {
                dest: 'svn/trunk/',
                expand: true,
                //nonull: true,
                cwd: '<%= slug %>/',
                src: '**'
            },
            tag: {
                dest: 'svn/tags/' + newVersion + '/',
                expand: true,
                cwd: '<%= slug %>/',
                src: '**'
            }
        });
        grunt.config.set('confirm', {
            "New version deployment": {
                options: {
                    question: 'Update from v<%= package_json.version %> to v'+newVersion+'\nCommit msg: <%= commit_msg %>\n',
                    input: '_key:y'
                }
            }
        });
        grunt.task.run([
            'default',
            'exec:svn_clean',
            'confirm',
            'clean',
            'version',
            'copy'
        ]);
        grunt.task.run(['exec:git','exec:svn']);
    });


};
