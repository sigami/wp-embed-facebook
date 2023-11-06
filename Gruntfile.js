/**
 * Created by Miguel Sirvent on 6/15/16.
 */
'use strict';
module.exports = function (grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    sass: {
      main: {
        options: {
          style: 'compressed'
        },
        files: {
          'templates/lightbox/css/lightbox.css': 'templates/lightbox/css/lightbox.scss',
          'templates/custom-embeds/styles.css': 'templates/custom-embeds/styles.scss',
        }
      }
    },
    uglify: {
      main: {
        files: {
          'inc/js/fb.min.js': ['inc/js/fb.js'],
          'templates/lightbox/js/lightbox.min.js': ['templates/lightbox/js/lightbox.js']
        },
        options: {
          sourceMap: false
        }
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-uglify');

  //grunt
  grunt.registerTask('default', ['sass', 'uglify']);

  // ---------------------------------
  //       Deployment only.
  // ---------------------------------

  grunt.loadTasks("../grunt-helpers/");
};
