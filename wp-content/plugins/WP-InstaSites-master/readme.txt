=== Kigo Wordpress Plugin ===
Contributors: Kigo
Tags: vacation rental, lodging, online booking, crm
Requires at least: 3.5
Tested up to: 3.5.1
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin is intended for use by Kigo customers to display property and booking tools on their WP-hosted sites on any platform.

== Description ==

This plugin is intended for use by Kigo customers to display property and booking tools on their WP-hosted sites on any platform.

For assistance, please contact support@kigo.net

http://kigo.net

== Installation ==

1. Copy the bookt-api folder to /wp-content/plugins
2. Go to Dashboard and click on "Kigo settings" menu button which now appears in the dashboard navigation
3. Fill in your Kigo API key and save.
4. Go to Kigo settings > Data Sync and click on "Create Default Pages"

- Apache mod_rewrite must be enabled.  Use command 'a2enmod rewrite' and then restart apache for this change to take effect.
- .htaccess file must contain the following:
	# BEGIN WordPress
	<IfModule mod_rewrite.c>
	RewriteEngine On
	RewriteBase /
	RewriteRule ^index\.php$ - [L]
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule . /index.php [L]
	</IfModule>
	# END WordPress
- We recommend using "Day and Name" for permalink settings at first.  Other modes may require different rewrite rules in .htaccess (Wordpress Requirement)

== Frequently Asked Questions ==

= Where do I get a Kigo API Key? =

You must be an active Kigo client.  Existing clients may contact support@kigo.net for assistance.  Prospective clients should contact sales@kigo.net to learn more about our products and services.

= Where can I get Kigo-approved themes? =

Kigo's premium themes provide the best integration experience with the Kigo API and Kigo wesites plugin.  These help simplify the setup process and make it easy to control layouts and styles.

If you are an Kigo client and we are hosting our site for you, then you will already have access to our premium themes.  If you are hosting this in your own environment please contact support@kigo.net for information about using our premium themes.

== Screenshots ==

1. This screenshot show a finished working site powered by our plugin.  Availability search, and property finders all load dynamically.

== Changelog ==

= 0.1 =
* Initial Public Release Version.

== Upgrade Notice ==

= 0.1 =
This is the original version.  No need to upgrade at this time. 