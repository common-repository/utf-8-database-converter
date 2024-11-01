=== UTF-8 Database Converter ===
Contributors: g30rg3x
Tags: utf8, database, converter, eol
Requires at least: 2.1
Tested up to: 2.2
Stable tag: eol

Easily Converts the WordPress database from any type of character set to UTF-8 character set.

== Description ==

Since the release of WordPress 2.2 the character set has been set by default to UTF-8 and the users
that come from previously versions of WordPress, may have some problems with the old Latin1 character set
and the new default UTF-8 character set.<br>
This plugin has been designed and developed with the idea to do this complex task in a easy 1-click way,
so it will no require any type of advance knowledge about the topic to make use of this plugin.<br>

**Attention**: This plugin is no longer being maintained by the author

== Installation ==

1. [Open the file `wp-config.php` set `DB_CHARSET` to `utf8` and leave `DB_COLLATE` with nothing](http://codex.wordpress.org/Editing_wp-config.php#Database_character_set "How to edit the DB charset from wp-config.php"). (Just for WordPress 2.2.x)
1. Upload `UTF8_DB_Converter.php` to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to the sub menu called 'UTF-8 Database Converter'.
1. And just follow the instructions on the screen.

== Frequently Asked Questions ==

= Why i can't see the sub menu 'UTF-8 Database Converter'? =

If you are not the 'blog owner' or a user with level 10 (the maximum level), you cannot access this plugin.
Because this task is a little complex and exceeds the normal user daily tasks, i have decided to make only visible 
and accessibly for the user with level 10 which is normally the blog owner or the blog administrator.

= What are the System Recommendations or do i need something else? =

I make this plugin compatible with the next minimum requirements:<br>
* PHP >= 4.2<br>
* MySQL >= 4.1.2<br>
So it will no require anything different or anything special to work.

= Can i delete it after the task is done? =

Of course, once its make his job you no longer need use or have anymore this plugin in your plugin activated/stored list.

== Upgrade Notice ==

= eol =
Since this version, the plugin has been relicensed under the WTFPL free software license.

== NOTES ==

This plugin makes and a irreversible job to your database so consider seriously the task to make a complete backup of your
WordPress based site before proceed with the task.<br>
This plugin has been designed to be only compatible with WordPress versions 2.2.x and 2.1.x so running
the plugin on other minor or major versions may have unexpected behavior.