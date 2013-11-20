=== Orbis ===
Contributors: pronamic, remcotolsma 
Tags: orbis, intranet
Requires at least: 3.0
Tested up to: 3.7.1
Stable tag: 1.0.0

This plugin creates an intranet environment in WordPress.

== Description ==



== Functions ==

*	General
	*	orbis_format_seconds(  $seconds, $format = 'H:m' )

*	Projects
	*	orbis_project_get_the_time( $format = 'H:m' )
	*	orbis_project_the_time( $format = 'H:m' )

*	Flot
	*	orbis_flot( $id, $data, $options )


== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your 
WordPress installation and then activate the Plugin from Plugins page.


== Screenshots ==

1.	Orbis


== Changelog ==

= 0.2.4 =
*	Added support for wildcard domains in subscription / licenses API

= 0.2.3 =
*	Added the keychain URL, username and e-mail fields to an password request comment

= 0.2.2 =
*	Added company post type
*	Added subscription post type
*	Improved the keychain visibile till date functionality

= 0.2.1 = 
*	Added own capabilities for the keychain posts

= 0.2 =
*	Added keychain manager support

= 0.1 =
*	Initial release


== Links ==

*	[WP-Admin Icons](http://wordpress.org/extend/plugins/wp-admin-icons/)
*	[Pronamic](http://pronamic.eu/)
*	[Remco Tolsma](http://remcotolsma.nl/)
*	[Markdown's Syntax Documentation][markdown syntax]

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"