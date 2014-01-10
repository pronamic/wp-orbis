=== Orbis ===
Contributors: pronamic, remcotolsma, kjtolsma
Tags: orbis, intranet
Requires at least: 3.0
Tested up to: 3.8
Stable tag: 1.0.3

Orbis is a powerful, extendable plugin to boost up your business. Project Management, Customer Relation Management & More...

== Description ==

This plugin transforms your WordPress environment into a fully operating 
business tool. Orbis has some nice basic Project Management, Customer Relation 
Management & Intranet functionalities. Make Orbis even more powerfull with some 
cool additional plugins.

This plugin is built with idea to make business tools extendable. Every 
bussiness is different. We believe in the power of WordPress and although 
WordPress may not be your first idea to serve as a framework for such a tool, 
we feel it has shown many advantages, particularly with extendability and 
control. 

Orbis comes with a front-end theme with the very orginal name, Orbis. This 
theme is based on Bootstrap and gives you all the front-end functionality you 
need. Easely extend this theme with child themes. We’ll try to release more
 themes.

[Download the Orbis theme for free.](https://github.com/pronamic/wt-orbis/releases)

= Benefits at a glance =

*	Easily extendable with plugins
*	Fully translatable
*	The Orbis Framework is available for FREE
*	Various high quality themes
*	Fully manageable
*	Use you own hosting environment
*	Built on WordPress

= Core Features =

*	Manage Projects - Add projects and connect them to companies
*	Manage Persons - Add persons and connect them to companies
*	Manage Companies - Add companies
*	Pages - Use the standard WordPress functionality to add pages
*	Posts - Use posts for your intranet
*	Comments - Comment on Pages, Projects, Persons & Companies

= Extend =

The power of Orbis lays in the extendability and flexibility. Use plugins to 
add the functionality you need.

*	[Orbis Tasks](http://www.orbiswp.com/plugins/) - Add tasks to Persons and connect them to Projects
*	[Orbis Keychains](http://www.orbiswp.com/plugins/) - Alle your passwords im a save place
*	[Orbis Timesheets](http://www.orbiswp.com/plugins/) - Submit your time en connect them to Projects.

= Get involved =

Want to contribute? Checkout the source code on the Orbis GitHub Repository.


== Functions ==

*	General
	*	orbis_format_seconds(  $seconds, $format = 'HH:MM' )

*	Projects
	*	orbis_project_get_the_time( $format = 'HH:MM' )
	*	orbis_project_the_time( $format = 'HH:MM' )

*	Flot
	*	orbis_flot( $id, $data, $options )


== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your 
WordPress installation and then activate the Plugin from Plugins page.


== Screenshots ==

1.	Home
2.	News
3.	Person
4.	Persons
5.	Project
6.	Projects
7.	WordPress


== Changelog ==

= 1.0.3 =
*	Added custom column 'e_mail' to the custom companies table.

= 1.0.2 =
*	Added custom Posts 2 Posts labels.

= 1.0.1 =
*	Added project finished field.

= 1.0.0 =
*	Initial release.


== Developers ==

*	php ~/wp/svn/i18n-tools/makepot.php wp-plugin ~/wp/git/orbis ~/wp/git/orbis/languages/orbis.pot


== Links ==

*	[WP-Admin Icons](http://wordpress.org/extend/plugins/wp-admin-icons/)
*	[Pronamic](http://pronamic.eu/)
*	[Remco Tolsma](http://remcotolsma.nl/)
*	[Markdown's Syntax Documentation][markdown syntax]

[markdown syntax]: http://daringfireball.net/projects/markdown/syntax
            "Markdown is what the parser uses to process much of the readme file"