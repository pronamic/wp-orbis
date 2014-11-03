module.exports = function( grunt ) {
	// Project configuration
	grunt.initConfig( {
		// Package
		pkg: grunt.file.readJSON( 'package.json' ),

		dirs: {
			ignore: [ 'build', 'node_modules', 'vendor' ].join( ',' ) 
		},

		// PHP Code Sniffer
		phpcs: {
			application: {
				dir: [ '.' ],
			},
			options: {
				standard: 'phpcs.ruleset.xml',
				extensions: 'php',
				ignore: '<%= dirs.ignore %>'
			}
		},

		// PHPLint
		phplint: {
			options: {
				phpArgs: {
					'-lf': null
				}
			},
			all: [ '**/*.php' ]
		},

		// PHP Mess Detector
		phpmd: {
			application: {
				dir: '.'
			},
			options: {
				exclude: '<%= dirs.ignore %>',
				reportFormat: 'text',
				rulesets: 'phpmd.ruleset.xml'
			}
		},
		
		// Check WordPress version
		checkwpversion: {
			options: {
				readme: 'readme.txt',
				plugin: 'orbis.php',
			},
			check: {
				version1: 'plugin',
				version2: 'readme',
				compare: '=='
			},
			check2: {
				version1: 'plugin',
				version2: '<%= pkg.version %>',
				compare: '=='
			}
		},
		
		// MakePOT
		makepot: {
			target: {
				options: {
					cwd: '',
					domainPath: 'languages',
					type: 'wp-plugin'
				}
			}
		},
		
		// Copy
		copy: {
			main: {
				files: [
					{ // AngularJS
						expand: true,
						cwd: 'bower_components/angular',
						src: [ 'angular-csp.css', 'angular.js', 'angular.min.js', 'angular.min.js.map' ],
						dest: 'assets/angular'
					},
					{ // AngularJS jQuery UI Datepicker
						expand: true,
						cwd: 'bower_components/angular-ui-date/src',
						src: [ 'date.js' ],
						dest: 'assets/angular-ui-date'
					},
					{ // AngularJS ui-select
						expand: true,
						cwd: 'bower_components/angular-ui-select/dist',
						src: [ 'select.css', 'select.js' ],
						dest: 'assets/angular-ui-select'
					},
					{ // Select2
						expand: true,
						cwd: 'bower_components/select2',
						src: [ 'select2.js', 'select2.css', 'select2-bootstrap.css', 'select2-spinner.gif', 'select2.png', 'select2x2.png' ],
						dest: 'assets/select2'
					},
				]
			},

			deploy: {
				src: [
					'**',
					'!bower.json',
					'!composer.json',
					'!Gruntfile.js',
					'!package.json',
					'!phpcs.ruleset.xml',
					'!phpmd.ruleset.xml',
					'!bower_components/**',
					'!node_modules/**',
					'!wp-svn/**',
				],
				dest: 'deploy',
				expand: true
			},
		},

		// Clean
		clean: {
			deploy: {
				src: [ 'deploy' ]
			},
		},

		// WordPress deploy
		rt_wp_deploy: {
			app: {
				options: {
					svnUrl: 'http://plugins.svn.wordpress.org/orbis/',
					svnDir: 'wp-svn',
					svnUsername: 'pronamic',
					deployDir: 'deploy',
					version: '<%= pkg.version %>',
				}
			}
		},
	} );

	grunt.loadNpmTasks( 'grunt-phpcs' );
	grunt.loadNpmTasks( 'grunt-phplint' );
	grunt.loadNpmTasks( 'grunt-phpmd' );
	grunt.loadNpmTasks( 'grunt-checkwpversion' );
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-concat' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-rt-wp-deploy' );

	// Default task(s).
	grunt.registerTask( 'default', [ 'phplint', 'phpmd', 'checkwpversion', 'copy' ] );
	grunt.registerTask( 'pot', [ 'makepot' ] );
	
	grunt.registerTask( 'deploy', [
		'default',
		'clean:deploy',
		'copy:deploy'
	] );

   	grunt.registerTask( 'wp-deploy', [
   		'deploy',
   		'rt_wp_deploy'
   	] );
};
