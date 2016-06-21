/**
 * Created by Miguel Sirvent on 6/15/16.
 */
'use strict';
module.exports = function (grunt) {
    var slug = ['wp-embed-facebook'];
    grunt.initConfig({
        variables: grunt.file.readJSON('variables.json'),
        package_json: grunt.file.readJSON('package.json'),
        slug: slug,
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
            dist: {
                files: {
                    '<%= slug %>/lib/js/fb.min.js': ['<%= slug %>/lib/js/fb.js'],
                    '<%= slug %>/lib/js/wpembedfb.min.js': ['<%= slug %>/lib/js/wpembedfb.js'],
                    '<%= slug %>/lib/lightbox2/js/lightbox.js': ['<%= slug %>/lib/lightbox2/js/lightbox.min.js']
                },
                options: {
                    sourceMap: false
                }
            }
        },
        makepot: {
            target: {
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
            pot: {
                files: '<%= slug %>/**/*.php',
                tasks: ['makepot']
            },
            test_dir: {
                files: ['<%= slug %>/**', '!*'],
                tasks: ['copy:test_dir']
            }
        },
        copy: {
            test_dir: {
                dest: '<%= variables.test_dir %>',
                src: '<%= slug %>/**'
            },
            svn_trunk: {
                dest: 'svn/trunk/',
                src: ['<%= slug %>/**','!<%= slug %>/readme.txt']
            },
            svn_tag: {
                dest: 'svn/tags/' + '<%= package_json.version %>' + '/',
                src: '<%= slug %>/**'
            }
        },
        clean: {
            options: {force: true},
            test_dir: [
                '<%= variables.test_dir %>'
            ],
            svn_trunk: [
                'svn/trunk'
            ],
            svn_tag: [
                'svn/tags/<%= package_json.version %>/'
            ]
        },

        commit_msg: (grunt.option('commit') || 'Update v<%= package_json.version %>'),
        shell: {
            git: {//add and commit
                command: 'git add --all && git commit -m "<%= commit_msg %>" && git push'
            },
            svn: {//add and commit
                command: 'cd svn && svn add --force * --auto-props --parents --depth infinity -q && svn ci -m "<%= commit_msg %>"'
            },
            svn_clean: {//revert all, update and remove files not added to svn
                command: "cd svn && svn revert -R . && svn up && svn status | grep ^? | awk '{print $2}' | xargs rm -r"
            },
            svn_start: {//get svn repository
                command: 'svn co https://plugins.svn.wordpress.org/<%= slug %>/ svn'
            },
            svn_tag: {//copy trunk to new tag
                command: 'cd svn && svn cp trunk tags/<%= package_json.version %>'
            }
        },
        version: {
            options: {
                release: (grunt.option('type') || 'patch')
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
        },
        confirm: {
            deploy: {
                options: {
                    question: '100% sure?',
                    input: '_key:y'
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.loadNpmTasks('grunt-wp-i18n');

    grunt.loadNpmTasks('grunt-confirm');
    grunt.loadNpmTasks('grunt-shell');
    grunt.loadNpmTasks('grunt-version');


    //grunt
    grunt.registerTask('default', [
        'sass',
        'uglify',
        'makepot'
    ]);

    //
    grunt.registerTask('dev', [
        'default',
        'clean:test_dir',
        'copy:test_dir',
        'watch'
    ]);

    // Deployment only.
    // ---------------------------------

    // grunt dev-update --commit="Future things"
    grunt.registerTask('dev-update', [
        'default',
        'shell:svn_clean',
        'clean:svn_trunk',
        'copy:svn_trunk',
        'shell:git',
        'shell:svn'
    ]);

    // major . minor . patch . prerelease
    //grunt bump --type=major|minor|patch|prerelease(default=patch) --commit="Awesome things"
    grunt.registerTask('bump', [
        'default',
        'confirm',
        'version',
        'shell:svn_clean',
        'clean:svn_trunk',
        'clean:svn_tag',
        'copy:svn_trunk',
        'shell:svn_tag',
        'shell:git',
        'shell:svn'
    ]);


};
