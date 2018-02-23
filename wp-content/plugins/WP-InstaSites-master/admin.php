<?php

function bapi_create_menu() {

	$parentSlug = 'site_settings_general';

	//create new top-level menu
	add_menu_page('Kigo sites Plugin Settings', 'Kigo', 'administrator', $parentSlug, '', plugins_url('/img/icon_kigo.ico', __FILE__) );

	add_submenu_page( $parentSlug, 'General',                    'General',                    'administrator', $parentSlug,                function(){ require('setup-general.php'); }      );
	add_submenu_page( $parentSlug, 'Property & Search Settings', 'Property & Search Settings', 'administrator', 'site_settings_propsearch', function(){ require('setup-sitesettings.php'); } );
	add_submenu_page( $parentSlug, 'Slideshow',                  'Slideshow',                  'administrator', 'site_settings_slideshow',  function(){ require('setup-slideshow.php'); }    );
	add_submenu_page( $parentSlug, 'Take me Live',               'Take me Live',               'administrator', 'site_settings_golive',     function(){ require('setup-golive.php'); }       );
	add_submenu_page( $parentSlug, 'Data Sync',                  'Data Sync',                  'administrator', 'site_settings_sync',       function(){ require('setup-sync.php'); }         );
	add_submenu_page( $parentSlug, 'Initial Setup',              'Initial Setup',              'administrator', 'site_settings_initial',    function(){ require('setup-initial.php'); }      );
	add_submenu_page( $parentSlug, 'Advanced Options',           'Advanced',                   'administrator', 'site_settings_advanced',   function(){ require('setup-advanced.php'); }     );
	add_submenu_page( $parentSlug, 'Translations',				 'Translations',			   'administrator', 'site_settings_translations',	array( 'Kigo_Setups', 'translation_gui' )     );
	add_submenu_page( $parentSlug, 'SSL Config',				 'SSL Config',				   'administrator', 'site_settings_sslconfig',		array( 'Kigo_Setups', 'ssl_config' )    );

	// call register settings function
	// (Why is this done here? because the options should only be init'ed if we are inside a site-specific admin. Not when we are in the Network admin panel, nor in the /wp-admin/user/ panel.)
	add_action( 'admin_init', 'bapi_options_init' );
}

function bapi_options_init(){
	// register the core settings
	register_setting('bapi_options','api_key');
	register_setting('bapi_options','bapi_language');
	register_setting('bapi_options','bapi_basueurl');
	register_setting('bapi_options','bapi_secureurl');	
	register_setting('bapi_options','bapi_solutiondata');
	register_setting('bapi_options','bapi_solutiondata_lastmod');	
	register_setting('bapi_options','bapi_site_cdn_domain'); 
	register_setting('bapi_options','bapi_cloudfronturl');
	register_setting('bapi_options','bapi_cloudfrontid'); 
	register_setting('bapi_options','bapi_global_header'); 
	register_setting('bapi_options','bapi_sitesettings');
	register_setting('bapi_options','bapi_google_webmaster_htmltag');
	
	// register the slideshow settings
	// register the settings
	register_setting('bapi_options','bapi_slideshow_image1');
	register_setting('bapi_options','bapi_slideshow_image2');
	register_setting('bapi_options','bapi_slideshow_image3');
	register_setting('bapi_options','bapi_slideshow_image4');
	register_setting('bapi_options','bapi_slideshow_image5');
	register_setting('bapi_options','bapi_slideshow_image6');
	register_setting('bapi_options','bapi_slideshow_caption1');
	register_setting('bapi_options','bapi_slideshow_caption2');
	register_setting('bapi_options','bapi_slideshow_caption3');
	register_setting('bapi_options','bapi_slideshow_caption4');
	register_setting('bapi_options','bapi_slideshow_caption5');
	register_setting('bapi_options','bapi_slideshow_caption6');	
	
	// doc template specific settings
	register_setting('bapi_options','bapi_rental_policy');
	register_setting('bapi_options','bapi_rental_policy_lastmod');
	register_setting('bapi_options','bapi_privacy_policy');
	register_setting('bapi_options','bapi_privacy_policy_lastmod');
	register_setting('bapi_options','bapi_terms_of_use');
	register_setting('bapi_options','bapi_terms_of_use_lastmod');
	register_setting('bapi_options','bapi_safe_harbor');
	register_setting('bapi_options','bapi_safe_harbor_lastmod');	
}

function bapi_notify_blog_public(){
	global $bapi_all_options;
	if($bapi_all_options['blog_public']==0){
		echo '<div class="error"><p>Your site is currently hidden to search engines. <a href="/wp-admin/options-reading.php">CLICK HERE</a> to enable <em>Search Engine Visibility</em> and fix this problem.</p></div>';
	}
}
add_action('admin_notices','bapi_notify_blog_public');

//Mantis Ticket 5408 compatible permalinks
function bapi_notify_incompatible_permalinks(){
	$currentPermalinkStructure = get_option('permalink_structure');
	if($currentPermalinkStructure != "/%year%/%monthnum%/%day%/%postname%/" && $currentPermalinkStructure != "/%year%/%monthnum%/%postname%/" && $currentPermalinkStructure != "/%postname%/" ){
		echo '<div id="incompatiblepermalink" class="error"><p>The Permalink settings for your site are not compatible with the Kigo Plugin. Please <a href="/wp-admin/options-permalink.php">CLICK HERE</a> and select \'Day and name\', \'Month and name\', or \'Post name\'.</p></div>';
	}
}
add_action('admin_notices','bapi_notify_incompatible_permalinks');
//Mantis  Ticket: 5859 Display error notice if site config in KigoSite is mis-matched with KigoApp

function site_config_error(){
	global $bapi_all_options;
	bapi_wp_site_options();
	
	$bapi_solutiondata = json_decode(wp_unslash($bapi_all_options['bapi_solutiondata']),true);  
	//values saved in our bapi options array our old values	

	//print_r($bapi_solutiondata); exit();
	$bapi_unique_prefx = $bapi_solutiondata['UniquePrefix'];  //Unique prefix DOES NOT need to match to domain!!!
	
	$primaryUrl = 'http://'.$bapi_solutiondata['PrimaryURL'];
	$secureUrl = $bapi_solutiondata['SecureURL'];
	$plugUrl = plugins_url();
	$setUpErr =  array();
	//update fields

	$bapi_cdn_domain = $bapi_all_options['bapi_site_cdn_domain'];
	$bapi_secure_url = $bapi_all_options['bapi_secureurl'];
	$contains = strpos($bapi_secure_url, "lodgingcloud.com");
	$contains2 = strpos($bapi_secure_url, "imbookingsecure.com");


	if($bapi_unique_prefx != array_shift(explode(".",$_SERVER['HTTP_HOST']))){ //throw error - ""
		echo '<div id="mis-match-config" class="error"><p>Kigo site domain prefix (<em>"' . array_shift(explode(".",$_SERVER['HTTP_HOST'])) . '"</em>) is mis-matched with Kigo app (<em>"' . $bapi_unique_prefx . '"</em>). Please contact <a href="mailto:support@kigo.net?subject=Kigo site%20Error%20Report%20for%20'.$bapi_cdn_domain.'&amp;body=Kigo%20site%20domain%20prefix%20%27' . array_shift(explode(".",$_SERVER['HTTP_HOST'])) . '%27%20is%20mis-matched%20with%20Kigo%20app%20%27' . $bapi_unique_prefx .'%27">support@kigo.net</a> and provide this error message for expedited assistance.</p></div>';
	}
	if($bapi_cdn_domain != $primaryUrl){
		echo '<div id="mis-match-config" class="error"><p>Kigo site URL configuration does not match the Kigo app settings.  Please <a href="'.menu_page_url( 'site_settings_initial', false ).'">CLICK HERE</a> to change your Site URL to <em>"'.$primaryUrl.'"</em> or contact <a href="mailto:support@kigo.net?subject=Kigo%20site%20Error%20Report%20for%20'.$bapi_cdn_domain.'&amp;body=Kigo%20site%20URL%20configuration%20does%20not%20match%20the%20Kigo%20app%20settings.%20Change%20your%20Site%20URL%20to%20%27'.$primaryUrl.'%27%20or%20contact%20support%20to%20continue%20using%20%27'.$bapi_cdn_domain.'%27">support@kigo.net</a> to continue using <em>"'.$bapi_cdn_domain.'"</em>.</p></div>';
		
	}
	if($bapi_secure_url == $bapi_solutiondata['PrimaryURL']){
			
	}elseif(empty($bapi_secure_url)){
		
	}elseif($contains){
			
	}elseif($contains2){
			
	}
	else{
		echo '<div id="mis-match-config" class="error"><p>Kigo site plugin (<em>"'.$bapi_all_options['bapi_secureurl'].'"</em>) configuration is mis-matched with Kigo app (<em>"'.$secureUrl.'"</em>). Please <a href="'.menu_page_url( 'site_settings_initial', false ).'">CLICK HERE</a> to correct the Secure Site URL. Secure Site URL must be set to <em>"'.$secureUrl.'"</em>, <em>"'.$bapi_solutiondata['PrimaryURL'].'"</em> or left blank.</p></div>';

	}


}
//this function Display error notice if site config in KigoSite is mis-matched
add_action('admin_notices','site_config_error');


function kigo_login_logo() { ?>
    <style type="text/css">
        body.login div#login h1 a {
            background-image: url(<?php echo ( get_relative( plugins_url( 'img/logo_kigo.png', __FILE__ )))?>);
			background-size:auto auto;
            width:auto;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'kigo_login_logo', 11 );


function bapi_update_incompatible_permalinks_error_notice($oldvalue, $_newvalue){
	if($_newvalue == "/%year%/%monthnum%/%day%/%postname%/" || $_newvalue == "/%year%/%monthnum%/%postname%/" || $_newvalue == "/%postname%/" ){
		?>
		<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready(function() {
			if(jQuery('#incompatiblepermalink').length > 0){
				jQuery('#incompatiblepermalink').remove();
			}
		});
		//]]>
		</script>
		<?php
	}
}
add_action( 'update_option_permalink_structure' , 'bapi_update_incompatible_permalinks_error_notice', 10, 2 );
