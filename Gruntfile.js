module.exports = function(grunt) {

  require('load-grunt-tasks')(grunt);

  // Project configuration.
  grunt.initConfig({
	pkg: grunt.file.readJSON('package.json'),

	// # Internationalization

	// Add text domain
	addtextdomain: {
		options: {
            textdomain: '<%= pkg.name %>',    // Project text domain.
            updateDomains: [ 'wc-mnm-checkboxes' ]  // List of text domains to replace.
        },
		target: {
			files: {
				src: ['*.php', '**/*.php', '**/**/*.php', '!node_modules/**', '!deploy/**']
			}
		}
	},

	// Generate .pot file
	makepot: {
		target: {
			options: {
				domainPath: '/languages', // Where to save the POT file.
				exclude: ['deploy'], // List of files or directories to ignore.
				mainFile: 'wc-mnm-checkboxes.php', // Main project file.
				potFilename: 'wc-mnm-checkboxes.pot', // Name of the POT file.
				type: 'wp-plugin', // Type of project (wp-plugin or wp-theme).
				potHeaders: {
                    'Report-Msgid-Bugs-To': 'https://github.com/helgatheviking/wc-mnm-checkboxes/issues/'
                } 
			}
		}
	},


	// bump version numbers (replace with version in package.json)
	replace: {
		Version: {
			src: [
				'readme.txt',
				'<%= pkg.name %>.php'
			],
			overwrite: true,
			replacements: [
				{
					from: /Stable tag:.*$/m,
					to: "Stable tag: <%= pkg.version %>"
				},
				{
					from: /Version:.*$/m,
					to: "Version: <%= pkg.version %>"
				},
				{
					from: /public \$version = \'.*.'/m,
					to: "public $version = '<%= pkg.version %>'"
				},
				{
					from: /public \$version      = \'.*.'/m,
					to: "public $version      = '<%= pkg.version %>'"
				}
			]
		}
	}

});

};
