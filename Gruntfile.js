module.exports = function( grunt ) {
	require( 'load-grunt-tasks' )( grunt );

	var phpFiles = [
		'**/*.php',
		'!bower_components/**',
		'!deploy/**',
		'!node_modules/**',
		'!vendor/**'
	];

	// Project configuration
	grunt.initConfig( {
		// Package
		pkg: grunt.file.readJSON( 'package.json' ),

		dirs: {
			ignore: [ 'bower_components', 'deploy', 'node_modules', 'vendor' ].join( ',' ) 
		},

		// PHP Code Sniffer
		phpcs: {
			application: {
				src: phpFiles
			},
			options: {
				bin: 'vendor/bin/phpcs',
				standard: 'phpcs.ruleset.xml',
				showSniffCodes: true
			}
		},

		// PHPLint
		phplint: {
			options: {
				phpArgs: {
					'-lf': null
				}
			},
			all: phpFiles
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

		// Check textdomain errors
		checktextdomain: {
			options:{
				text_domain: '<%= pkg.name %>',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d'
				]
			},
			files: {
				src:  [
					'**/*.php',
					'!bower_components/**',
					'!deploy/**',
					'!node_modules/**',
					'!vendor/**'
				],
				expand: true
			}
		},
		
		// MakePOT
		makepot: {
			target: {
				options: {
					domainPath: 'languages',
					type: 'wp-plugin',
					updatePoFiles: true,
					exclude: [ 'bower_components/.*', 'deploy/.*', 'node_modules' ],
				}
			}
		},
		
		// Copy
		copy: {
			assets: {
				files: [
					{ // AngularJS
						expand: true,
						cwd: 'node_modules/angular',
						src: [ 'angular-csp.css', 'angular.js', 'angular.min.js', 'angular.min.js.map' ],
						dest: 'assets/angular'
					},
					{ // AngularJS jQuery UI Datepicker
						expand: true,
						cwd: 'node_modules/angular-ui-date/src',
						src: [ 'date.js' ],
						dest: 'assets/angular-ui-date'
					},
					{ // AngularJS ui-select
						expand: true,
						cwd: 'node_modules/angular-ui-select/dist',
						src: [ 'select.css', 'select.js' ],
						dest: 'assets/angular-ui-select'
					},
					{ // Select2
						expand: true,
						cwd: 'node_modules/select2/dist',
						src: [ '**' ],
						dest: 'assets/select2'
					},
				]
			},

			deploy: {
				src: [
					'**',
					'!bower.json',
					'!Gruntfile.js',
					'!package.json',
					'!phpcs.ruleset.xml',
					'!phpmd.ruleset.xml',
					'!bower_components/**',
					'!deploy/**',
					'!node_modules/**',
					'!vendor/**'
				],
				dest: 'deploy/latest',
				expand: true
			},
		},

		// Imagemin
		imagemin: {
			build: {
				files: [
					{ // Images
						expand: true,
						cwd: 'src/images/',
						src: ['**/*.{png,jpg,gif,svg}'],
						dest: 'images/'
					}
				]
			}
		},

		// Clean
		clean: {
			build: {
				src: [
					'assets'
				]
			},

			deploy: {
				src: [ 'deploy/latest' ]
			},
		},

		// Composer
		composer: {
			options : {
				cwd: 'deploy/latest',
				flags: [
					'no-dev',
					'prefer-dist'
				]
			}
		},

		// Compress
		compress: {
			deploy: {
				options: {
					archive: 'deploy/archives/<%= pkg.name %>.<%= pkg.version %>.zip'
				},
				expand: true,
				cwd: 'deploy/latest',
				src: ['**/*'],
				dest: '<%= pkg.name %>/'
			}
		},

		// Git checkout
		gitcheckout: {
			tag: {
				options: {
					branch: 'tags/<%= pkg.version %>'
				}
			},
			develop: {
				options: {
					branch: 'develop'
				}
			}
		},

		// S3
		aws_s3: {
			options: {
				region: 'eu-central-1'
			},
			deploy: {
				options: {
					bucket: 'downloads.pronamic.eu',
					differential: true
				},
				files: [
					{
						expand: true,
						cwd: 'deploy/archives/',
						src: '<%= pkg.name %>.<%= pkg.version %>.zip',
						dest: 'plugins/<%= pkg.name %>/'
					}
				]
			}
		},

		// WordPress deploy
		rt_wp_deploy: {
			app: {
				options: {
					svnUrl: 'http://plugins.svn.wordpress.org/<%= pkg.name %>/',
					svnDir: 'deploy/wp-svn',
					svnUsername: 'pronamic',
					deployDir: 'deploy/latest',
					version: '<%= pkg.version %>',
				}
			}
		},
	} );

	// Default task(s).
	grunt.registerTask( 'default', [ 'phplint', 'phpmd', 'phpcs', 'checkwpversion', 'copy:assets', 'imagemin' ] );
	grunt.registerTask( 'pot', [ 'checktextdomain', 'makepot' ] );

	grunt.registerTask( 'deploy', [
		'default',
		'clean:deploy',
		'copy:deploy',
		'composer:install',
		'compress:deploy'
	] );

	grunt.registerTask( 'wp-deploy', [
		'gitcheckout:tag',
		'deploy',
		'rt_wp_deploy',
		'gitcheckout:develop'
	] );

	grunt.registerTask( 's3-deploy', [
		'gitcheckout:tag',
		'deploy',
		'aws_s3:deploy',
		'gitcheckout:develop'
	] );

	grunt.registerTask( 'assets', [
		'clean',
		'copy'
	] );
};
