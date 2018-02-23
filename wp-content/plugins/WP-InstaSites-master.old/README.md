Kigo websites
===================================
The Kigo plugin provides direct integration into the Kigo application.  Kigo is cloud based vacation rental software which powers vacation rental property managers around the world.

Features
- Responsive themes
- Automatic synchronization of availabliy
- Set of widgets for featured properties, specials, property details, availability calendars, property finders, developments, etc...
- Support for vacation rentals, hotels, resorts and B&B
- Multi-language support
- Booking website
- SEO Friendly
- Channel manager (FlipKey, TripAdvisor, Homeaway, VRBO, etc...)
- Social Media & Lead Management
- Owner's Extranet
- Unlimited Email & Phone Support
- Comprehensive Report Suite

- Powered by Kigo (http://kigo.net)
- Requires an *API key* from Kigo. Go to http://kigo.net/vacation-rental-software-contact to request a key.


============================
Installation Considerations
============================

Apache config
-------------

   • Apache `mod_rewrite` must be enabled.  Use command `a2enmod rewrite` and then restart apache for this change to take effect.

Wordpress config
----------------

This plugin requires multiuser version of WP. To enable μWP after installation of WP do the following:

   • go edit `wp-config.php` file to add the `define('WP_ALLOW_MULTISITE', true);` line. This will enable the option ‘Network’ under the ‘Tools’ menu in your administration area;

   • create a new folder named `blogs.dir` inside your `wp-content` folder;

   • navigate to **Tools⇒Network** and follow the instructions on the screen. Add following to your `wp-config.php` file:

```
define('MULTISITE', true);
define('SUBDOMAIN_INSTALL', false);
define('DOMAIN_CURRENT_SITE', 'localhost');
define('PATH_CURRENT_SITE', '/');
define('SITE_ID_CURRENT_SITE', 1);
define('BLOG_ID_CURRENT_SITE', 1);
```

   • and the following to `.htaccess`:

```
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]

# add a trailing slash to /wp-admin
RewriteRule ^([_0-9a-zA-Z-]+/)?wp-admin$ $1wp-admin/ [R=301,L]

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(wp-(content|admin|includes).*) $2 [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(.*\.php)$ $2 [L]
RewriteRule . index.php [L]
```

   • We recommend using “Day and Name” for permalink settings at first.  Other modes may require different rewrite rules in `.htaccess` (Wordpress Requirement.)

