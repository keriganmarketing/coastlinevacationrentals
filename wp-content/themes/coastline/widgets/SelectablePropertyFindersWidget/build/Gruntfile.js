module.exports = function (grunt) {

    //Init config
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        jshint: {
            files: {
                src: ['../js/dev/**/*.js']
            },
        },
        uglify: {
            options: {
                banner: '/* ct-<%= pkg.name %> by j~: <%=grunt.template.today("dd-mm-yyyy")%>*/\n'
            },
            build: {
                src: ['../js/dev/**/*.js'],
                dest: '../js/main.min.js'
            }

        },
        concat: {
            options: {
                banner: '/* ct-<%= pkg.name %> by j~: <%=grunt.template.today("dd-mm-yyyy")%>*/\n',
            },
            dist: {
                src: ['../js/dev/**/*.js'],
                dest: '../js/main.js'
            }
        },
        sass: {
            dist: {
                options: {
                    style: 'compressed'
                },
                files: {
                    '../css/main.css': '../scss/main.scss',
                    '../css/admin.css': '../scss/admin.scss',
                }
            }
        },
        autoprefixer: {
            options: {
                browsers: ['last 2 version', 'ie 9'],
                banner: '/* ct-<%= pkg.name %> by j~: <%=grunt.template.today("dd-mm-yyyy")%>*/\n'
            },
            main: {
                expand: true,
                flatten: true,
                src: '../css/main.css',
                dest: '../css'

            }
        },
        cssmin: {
            options: {
                banner: '/* ct-<%= pkg.name %> by j~: <%=grunt.template.today("dd-mm-yyyy")%>*/\n',
                shorthandCompacting: false,
                roundingPrecision: -1
            },
            target: {
                files: {
                    '../css/main.min.css': ['../css/main.css']
                }
            }
        },
        watch: {
            scritps: {
                files: ['../js/dev/**/*.js'],
                tasks: ['concat', 'uglify'],
                options: {
                    spawn: false,
                    livereload: true, /*  <script src="//localhost:35729/livereload.js"></script>  */
                }
            },
            sass: {
                files: ['../scss/**/*.scss'],
                tasks: ['sass', 'autoprefixer', 'cssmin'],
                options: {
                    spawn: false,
                    livereload: true,
                }
            },
            css: {
                files: ['../css/main.css'],
                tasks: ['cssmin'],
                options: {
                    spawn: false,
                    livereload: true,
                }
            },
            html: {
                files: ['../**/*.php'],
                options: {
                    livereload: true,
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-autoprefixer');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-jshint');



    grunt.registerTask('default', ['sass', 'autoprefixer', 'concat', 'cssmin', 'uglify', 'watch']);
}
