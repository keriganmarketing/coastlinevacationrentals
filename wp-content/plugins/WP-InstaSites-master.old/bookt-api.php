<?php
/*
Plugin Name: Kigo Sites
Plugin URI: http://kigo.net
Description: This plugin is intended for use by Kigo customers to display property and booking tools on their WP-hosted sites on any platform.
Version: 1.0.20161104a
Author: Kigo.net
Author URI: http://kigo.net
License: GPL2
*/

define( 'KIGO_PLUGIN_VERSION', '1.0.20161104a' ); // KEEP THIS IN SYNC WITH PLUGIN METADATA ABOVE !!!


/*  Copyright 2014 Kigo.net (email : support@kigo.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if(
	!is_multisite() ||
	( ( $blog = get_blog_details() ) && $blog->deleted != '1' && $blog->archived != '1' ) // Only load the plugin if the blog is active on the site (network)
) {
	require_once('mustache.php-2.1.0/src/Mustache/Autoloader.php');
	Mustache_Autoloader::register();
	require_once( dirname( __FILE__ ) . '/includes/class-kigo-mustache.php' );
	include_once(dirname( __FILE__ ).'/timthumb-config.php');
	include_once(dirname( __FILE__ ).'/functions.php');
	include_once(dirname( __FILE__ ).'/admin.php');
	include_once(dirname( __FILE__ ).'/widgets.php');
	include_once(dirname( __FILE__ ).'/sync.php');
	include_once(dirname( __FILE__ ).'/google-xml-sitemap.php');
	include_once(dirname( __FILE__ ).'/cdn-linker/wp-cdn-linker.php');
	include_once(dirname( __FILE__ ).'/create-site.php');
	include_once(dirname( __FILE__ ).'/shortcodes.php');
	include_once(dirname( __FILE__ ).'/cloudfront.php');
	include_once(dirname( __FILE__ ).'/cron.php');
	require_once( dirname( __FILE__ ) . '/sso/class-kigo-single-sign-on.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-kigo-cron.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-loggly-logs.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-kigo-admin-bar-menu.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-kigo-i18n.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-kigo-setups.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-kigo-ssl-config.php' );
	//require_once( dirname( __FILE__ ) . '/custom-post-types.php' );
	require_once('bapi-php/bapi.php');
	require_once('init.php');

	// Plugin version hooks
	register_activation_hook( __FILE__, 'kigo_plugin_activation' );
	register_deactivation_hook( __FILE__, 'kigo_plugin_deactivation' );
	add_action( 'plugins_loaded', 'kigo_plugin_detect_update' );

	wp_register_script( 'typeahead', get_relative(plugins_url( '/js/typeahead.min.js', __FILE__)), array('jquery-min') );
	wp_register_script( 'flexslider', get_relative(plugins_url( '/js/jquery.flexslider-min.js', __FILE__)), array('jquery-min') );
	wp_register_script( 'multiselect', get_relative(plugins_url( '/js/jquery.multiselect.min.js', __FILE__)), array('jquery-min') );

	// Single-sign-on hooks
	add_action( 'wp_ajax_'.Kigo_Single_Sign_On::ACTION_CREATE_TOKEN, array( 'Kigo_Single_Sign_On', 'create_token' ) ); // during tests, in case we have a cookie
	add_action( 'wp_ajax_nopriv_'.Kigo_Single_Sign_On::ACTION_CREATE_TOKEN, array( 'Kigo_Single_Sign_On', 'create_token' ) );
	add_action( 'wp_ajax_'.Kigo_Single_Sign_On::ACTION_LOGIN, array( 'Kigo_Single_Sign_On', 'login' ) ); // for logged-in users
	add_action( 'wp_ajax_nopriv_'.Kigo_Single_Sign_On::ACTION_LOGIN, array( 'Kigo_Single_Sign_On', 'login' ) ); // for NON-logged-in users

	if(!defined('KIGO_SOLUTION_SYNC')) define('KIGO_SOLUTION_SYNC',3600);
	if(!defined('KIGO_SEO_SYNC')) define('KIGO_SEO_SYNC',600);
	if(!defined('KIGO_PROPERTY_SYNC')) define('KIGO_PROPERTY_SYNC',3600);


	// Cron sync
	if( defined( 'KIGO_CRON_SECRET' ) ) {

		// This endpoint is called by a cron job every X minutes to do every site sync.
		// http://<network_root>/wp-admin/admin-ajax.php?action=kigo_network_cron
		add_action( 'wp_ajax_nopriv_'.Kigo_Network_Cron::ACTION_CRON, array( 'Kigo_Network_Cron', 'do_sync' ) );
		add_action( 'wp_ajax_'.Kigo_Network_Cron::ACTION_CRON, array( 'Kigo_Network_Cron', 'do_sync' ) );
		
		// Endpoint called on each websites by the execution of do_sync
		// http://<website>.<network_root>/wp-admin/admin-ajax.php?action=kigo_site_cron
		// Will perform a full sync if called with &forced_full_sync=1
		add_action( 'wp_ajax_nopriv_'.Kigo_Network_Cron::ACTION_SITE_CRON, array( 'Kigo_Network_Cron', 'do_site_sync' ) );
		add_action( 'wp_ajax_'.Kigo_Network_Cron::ACTION_SITE_CRON, array( 'Kigo_Network_Cron', 'do_site_sync' ) );

		// Used by the UI to display the last cron execution
		add_action( 'wp_ajax_'.Kigo_Site_Cron::ACTION_GET_LAST_CRON_EXEC, array( 'Kigo_Site_Cron', 'get_interval_last_update_prop' ) );
	}

	// Specific endpoint to update translation files requires KIGO_PRIVATE_GOOGLE_TRANSLATE_KEY in wp-config 
	add_action( 'wp_ajax_' . kigo_I18n::ACTION_UPDATE_TRANSLATION_FILES, array( 'kigo_I18n', 'update_default_translations' ) ); // for logged-in users
	add_action( 'wp_ajax_nopriv_' . kigo_I18n::ACTION_UPDATE_TRANSLATION_FILES, array( 'kigo_I18n', 'update_default_translations' ) ); // for NON-logged-in users
	
	// Save custom translation endpoint
	add_action( 'wp_ajax_'.kigo_I18n::ACTION_SAVE_CUSTOM_TRANSLATION, array( 'kigo_I18n', 'save_custom_translations' ) );

	// Ajax endpoint to restore a page to it's default content 
	add_action( 'wp_ajax_restore_default_content', 'restore_default_content_callback' );


	add_action( 'init', 'disable_newrelic', 1 ); // Hook to disable logging in new relic in some specific cases.
	add_action('init','urlHandler_emailtrackingimage',1);	// handler for email images
	add_filter('home_url','home_url_cdn',1,2);
	add_filter('wp_head','add_server_name_meta',1);
	add_filter('redirect_canonical','bapi_redirect_fix',10,2);
	add_filter('language_attributes','bapi_language_attributes',10);	// ensure output of proper language
	add_filter('upload_mimes', 'custom_upload_mimes');
	add_filter('get_sample_permalink_html', 'perm', '',4); //Remove Edit Button for non superusers on BAPI pages
	add_filter('page_row_actions', 'remove_quickedit_for_nonsuperusers', 10, 2 );
	add_action('admin_menu', 'remove_pageattributes_meta_box' );
	add_action('template_redirect', 'do_ossdl_off_ob_start',10);
	add_action('wp_enqueue_scripts', 'enqueue_and_register_my_scripts_in_head',1);//scripts that load in the head of the site
	add_action('admin_enqueue_scripts', 'enqueue_and_register_my_scripts_in_head',1 );//scripts that load in the admin pages (same as above)
	add_action( 'admin_enqueue_scripts', 'enqueue_and_register_admin_scritps', 1 );//scripts that load in the admin pages ONLY
	add_action( 'admin_enqueue_scripts', 'load_featured_properties_js', 1 );
	add_action( 'admin_enqueue_scripts', 'load_bootstrap_3_css', 1 );		
	add_action('wp_head','loadscriptjquery',10);//lets load this at the end of wp-head so the wp_enqueue runs first
	add_action('wp_footer','getconfig',1);
	add_action('wp_head','bapi_getmeta',1);
	add_action('wp_head','display_global_header',10);
	add_action('wp_head','display_gw_verification',10);
	//add_action('wp_head','bapi_no_follow',1);
	add_action('init','bapi_create_site',1);  //Hook to add new sites
	add_action('init','bapi_setup_default_pages',5);
	add_action('init','bapi_crawl_sitemap_pages',5);
	add_action('init','urlHandler_bapidefaultpages',1);
	add_action('init','urlHandler_securepages',1);  //Hook to force redirect to secure pages
	add_action('init','bapi_wp_site_options',1);  //Preload Site Data to help reduce DB usage
	add_action('init','bapi_sync_coredata',2); 	// syncing BAPI core data
	add_action('init','bapi_sync_entity',3);	// syncing BAPI entities (such as properties, developments, etc...)
	add_action('init','urlHandler_bapitextdata',4);	// handler for /bapi.textdata.js FIXME make sure there are no usage any more to this
	add_action('init', 'customScriptHandler', 4); //Handler for scripts
	add_action('init','urlHandler_bapitemplates',4);	// handler for /bapi.templates.js
	add_action('init','urlHandler_bapiconfig',4);	// handler for /bapi.config.js
	add_action('init','urlHandler_sitelist',4);	// handler for /sitelist (possible warmup list)
	add_action('init','urlHandler_timthumb',1);	// handler for /img.php 
	add_action('wp_footer','urlHandler_bapi_ui_min',1);	// handler for /bapi.ui.min.js
	add_action('init','urlHandler_bapi_js_combined',1);	// handler for /bapi.ui.min.js
	add_action('init','disable_kses_content',20);
	add_action('template_redirect', 'google_sitemap'); // sitemap handler
	add_action('wp_login','bapi_reset_first_look');
	add_action('after_setup_theme','bapi_login_handler',1);  //Hook to do single sign-on
	add_action('template_redirect','relative_url',10);

	// create custom plugin settings menu
	add_action('admin_menu', 'bapi_create_menu');
	add_action( 'admin_bar_menu', array( 'Kigo_App_Admin_Bar_Menu', 'register_admin_bar_node' ), 999 ); // Only new app clients have this extra toolbar to link to the app
	add_action('update_option_update_action', 'bapi_option_update', 10, 2);
	add_action('update_option_property_category_name', 'bapi_option_category', 10, 2);

	add_action( 'widgets_init', create_function( '', 'register_widget( "BAPI_Header" );' ) );
	add_action( 'widgets_init', create_function( '', 'register_widget( "BAPI_Footer" );' ) );
	add_action( 'widgets_init', create_function( '', 'register_widget( "BAPI_HP_Slideshow" );' ) );
	add_action( 'widgets_init', create_function( '', 'register_widget( "BAPI_HP_Logo" );' ) );
	add_action( 'widgets_init', create_function( '', 'register_widget( "BAPI_HP_LogoWithTagline" );' ) );
	add_action( 'widgets_init', create_function( '', 'register_widget( "BAPI_HP_Search" );' ) );
	add_action( 'widgets_init', create_function( '', 'register_widget( "BAPI_Search" );' ) );
	add_action( 'widgets_init', create_function( '', 'register_widget( "BAPI_Featured_Properties" );' ) );
	add_action( 'widgets_init', create_function( '', 'register_widget( "BAPI_Inquiry_Form" );' ) );
	add_action( 'widgets_init', create_function( '', 'register_widget( "BAPI_Similar_Properties" );' ) );
	add_action( 'widgets_init', create_function( '', 'register_widget( "BAPI_Property_Finders" );' ) );
	add_action( 'widgets_init', create_function( '', 'register_widget( "BAPI_Specials_Widget" );' ) );
	add_action( 'widgets_init', create_function( '', 'register_widget( "BAPI_Weather_Widget" );' ) );
	add_action( 'widgets_init', create_function( '', 'register_widget( "BAPI_DetailOverview_Widget" );' ) );
	add_action( 'widgets_init', create_function( '', 'register_widget( "BAPI_Developments_Widget" );' ) );
	add_action( 'widgets_init', create_function( '', 'register_widget( "BAPI_SiteSelector" );' ) );
	//To hide update notifications to non-admin users
	add_action( 'admin_head', 'hide_update_notice_to_non_admin_users', 1 );

	add_filter( 'page_row_actions', 'kigo_disable_quick_edit', 10, 2 );
	add_filter( 'login_headertitle', 'newapp_login_headertitle' ); // Filter to display the correct brand in title attribute of login page

	
	require_once('JShrink/Minifier.php');
}
?>
