module.exports = function(grunt){

	require("matchdep").filterDev("grunt-*").forEach(grunt.loadNpmTasks);

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
		concat: {
			options: { separator: '\n\n' },
			js : {
				src: ['src/js/components/*.js', 'src/js/shared.js'],
				dest: 'build/build.js'
			},
			css : {
				src: ['src/css/shared.scss', 'src/css/components/*'],
				dest: 'build/build.css'
			}
		},
        uglify: {
		    build: {
		        files: {
		            'build/build.js': ['build/build.js']
		        }
		    }
		},
		postcss: {
			options: {
				map: false,
				processors: [
					require('autoprefixer')({browsers: ['last 3 version']})
				]
			},
			dist: {
				src: 'build/build.css'
			}
		},
		cssmin: {
		    build: {
		        src: 'build/build.css',
		        dest: 'build/build.css'
		    }
		},
		sass: {
			options: {
		    	sourcemap: 'none'
			},
		    build: {
		        files: {
		            'build/build.css': 'build/build.css'
		        }
		    }
		},
		watch: {
		    js: {
		        files: ['src/js/shared.js', 'src/js/components/*.js'],
		        tasks: ['buildjs']
		    },
		    css: {
		        files: ['src/css/shared.scss', 'src/css/components/*'],
		        tasks: ['buildcss']
		    }
		}
    });

	grunt.registerTask('default', ['buildall', 'watch']);
	grunt.registerTask('buildall', ['buildcss', 'buildjs']);
	
	grunt.registerTask('buildcss', ['concat:css', 'sass', 'postcss', 'cssmin']);
	grunt.registerTask('buildjs', ['concat:js', 'uglify:build']);

};