module.exports = function( grunt ) {
	'use strict';

	// Load all grunt tasks matching the `grunt-*` pattern
	require( 'load-grunt-tasks' )( grunt );

	// Show elapsed time
	require( 'time-grunt' )( grunt );

	// Project configuration
	grunt.initConfig(
		{
			package 		   : grunt.file.readJSON( 'package.json' ),

			replace 		   : {
				readme_md     : {
					src 	     : [ 'README.md' ],
					overwrite    : true,
					replacements : [
						{
							from : /\*\*Stable tag:\*\* (.*)/,
							to   : "**Stable tag:** <%= package.version %>  "
						}
					]
				},
				bootstrap_php : {
					src 		 : [ 'bootstrap.php' ],
					overwrite 	 : true,
					replacements : [
						{
							from : /Version:(\s*)(.*)/,
							to   : "Version:$1<%= package.version %>"
						},
						{
							from : /define\( __NAMESPACE__ \. '\\DWS_WP_FRAMEWORK_WOOCOMMERCE_VERSION', '(.*)' \);/,
							to   : "define( __NAMESPACE__ . '\\DWS_WP_FRAMEWORK_WOOCOMMERCE_VERSION', '<%= package.version %>' );"
						}
					]
				}
			}
		}
	);

	grunt.registerTask( 'version_number', [ 'replace:readme_md', 'replace:bootstrap_php' ] );
}
