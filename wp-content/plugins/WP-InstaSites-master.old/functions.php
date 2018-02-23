<?php

	/* PLUGIN VERSION-RELATED FUNCTIONS */

	function kigo_plugin_activation() {

		// This plugin requires a new table
		if( !Kigo_Single_Sign_On::create_table() ) {
			wp_die('Error activating Kigo Sites plugin');
		}

		kigo_I18n::update_i18n_network_option();

		add_site_option( 'wp_plugin_kigo_sites_current_version', KIGO_PLUGIN_VERSION );
	}

	function kigo_plugin_deactivation() {

		if( !Kigo_Single_Sign_On::drop_table() ) {
			wp_die('Error deactivating Kigo Sites plugin');
		}

		delete_site_option( 'wp_plugin_kigo_sites_current_version' );
	}

	// Version checker call the function update( <version_number> ). This allow doing action on version update.
	// If the version format is changed please be ensure that the new format is compatible with the previous one and is higher when compared with strcmp()
	function kigo_plugin_detect_update() {

		$option_name = 'wp_plugin_kigo_sites_current_version';

		if( !is_string( $current_version = get_site_option( $option_name ) ) ) {

			// What if it's a WP single becoming WP MU? Options will be stored in different tables and we'll forget the plugin version?!
			// Well, doc says: "Deactivate all active plugins [before creating the network!]".
			// So problem solved.
			// source: http://codex.wordpress.org/Create_A_Network

			// For pre-existing and activated plugins that didn't have this version control, init version with 0
			add_site_option( $option_name, $current_version = '0' );
		}

		if( strcmp( $current_version, KIGO_PLUGIN_VERSION ) < 0 ) {
			if( !kigo_on_plugin_update( $current_version ) ) {
				wp_die('An error occured while the Kigo Sites plugin was being updated. Please try again.');
			}

			update_site_option( $option_name, KIGO_PLUGIN_VERSION );
		}
	}

	function kigo_on_plugin_update( $current_version ) {

		if( !kigo_I18n::update_i18n_network_option() ) {
			Loggly_logs::log( array( 'msg' => ( 'Failed to update network option translations' ), 'current_version' => $current_version ) );
		}

		if( strcmp( $current_version, '1.0.20141002' ) < 0 ) { // The auto sign on table was introduced in version 1.0.20141002 2014/10/02, every previous version should create it now!
			if( !Kigo_Single_Sign_On::create_table() ) {
				return false;
			}
		}

		return true;
	}


	/*
	 * Disable new relic if doing cron or ajax
	 */
	function disable_newrelic() {
		if(
			defined('DOING_CRON') ||
			defined('DOING_AJAX')
		) {
			if( extension_loaded( 'newrelic' ) ) {
				newrelic_ignore_transaction();
			}
		}
	}

	/* Pre-Load Site Options - Utilizes Built-in Cache Functions */

	global $bapi_all_options; 
	function bapi_wp_site_options(){
		global $bapi_all_options;
		$bapi_all_options = wp_load_alloptions();
		if(!isset($bapi_all_options['bapi_solutiondata'])){
			$bapi_all_options['bapi_solutiondata'] = '';
		}

		//$bapi_all_options['bapi_solutiondata_array'] = json_decode(wp_unslash($bapi_all_options['bapi_solutiondata']));

		if(!isset($bapi_all_options['bapi_solutiondata_lastmod'])){
			$bapi_all_options['bapi_solutiondata_lastmod'] = 0;
		}
		if(!isset($bapi_all_options['bapi_keywords_array'])){
			$bapi_all_options['bapi_keywords_array'] = '';
		}
		if(!isset($bapi_all_options['bapi_keywords_lastmod'])){
			$bapi_all_options['bapi_keywords_lastmod'] = 0;
		}
		if(!isset($bapi_all_options['bapi_language'])){
			$bapi_all_options['bapi_language'] = 'en-US';
		}
		if(!isset($bapi_all_options['bapi_baseurl'])){
			$bapi_all_options['bapi_baseurl'] = 'connect.bookt.com';
		}
		if(defined('BAPI_BASEURL')){
			$bapi_all_options['bapi_baseurl'] = BAPI_BASEURL;
		}
		if(!isset($bapi_all_options['bapi_first_look'])){
			$bapi_all_options['bapi_first_look'] = 0;
		}
		
		return $bapi_all_options;
		//print_r($bapi_all_options['bapi_solutiondata_array']); exit();
	}

	/* Rebranding functions */
	function is_newapp_website() {

		$data = getbapicontext();

		if(
			!is_array( $data ) ||
			!isset( $data[ 'App' ] ) ||
			!isset( $data[ 'App' ][ 'Data' ] )
		) {
			// By default any new website is a kigo site
			return true;
		}

		return ( false !== strpos( $data[ 'App' ][ 'Data' ], 'newapp.kigo.net' ) );
	}

	function newapp_login_headertitle( $title ) {
		return 'Kigo Websites - Powered by Kigo';
	}


	/* Ajax handler for restore_default_content request */
	function restore_default_content_callback() {
		if(
			!isset( $_POST[ 'post_name' ] ) ||
			!strlen( $_POST[ 'post_name' ] )
		) {
			kigo_ajax_json_response( false, __FUNCTION__ . '_1' );
		}
		
		if(
			!is_int( $menu_id = initmenu( "Main Navigation Menu" ) ) ||
			!is_array( $page_def = get_default_pages_def( $_POST['post_name'] ) ) ||
			!is_array( $add_page = addpage( $page_def, $menu_id ) )
		) {
			kigo_ajax_json_response( false, __FUNCTION__ . '_2', array(
				'post_name'	=>	$_POST[ 'post_name' ],
				'menu_id'	=>	$menu_id,
				'page_def'	=>	$page_def,
				'add_page'	=>	$add_page,
			) );
		}
		
		kigo_ajax_json_response( true, '', $add_page );
	}

	/**
	 * Write a Json response, and exit
	 * 
	 * @param bool $success
	 * @param string $error_code 	__FUNCTION__ . '_' . <int>
	 * @param array $result
	 */
	function kigo_ajax_json_response( $success, $error_code = '', $result = array() ) {
		header('Content-Type: application/json');
		if(
			!is_bool( $success ) ||
			!is_string( $error_code ) ||
			!is_array( $result )
		) {
			echo json_encode( array(
				'success'		=>	false,
				'error_code'	=>	__FUNCTION__ . '_1',
				'result'		=>	array(
					'success'	=>	var_export( $success, true ),
					'msg'		=>	var_export( $error_code, true ),
					'result'	=>	var_export( $result, true )
				)
			));
			exit();
		}
		
		echo json_encode( array(
			'success'		=>	$success,
			'error_code'	=>	$error_code,
			'result'		=>	$result
		));
		exit();
	}

	/* BAPI url handlers */
	function urlHandler_emailtrackingimage() {
		$url = get_relative($_SERVER['REQUEST_URI']);		
		$url = strtolower($url);
		$url = substr($url, 0, 8);
		
		if ($url == "/t/misc/") {
			//header('Content-Type: application/javascript');	
			header('Cache-Control: public');
			//$expires = round((60*10 + $lastupdatetime), 2); // expires every 10 mins
			//$expires = gmdate('D, d M Y H:i:s \G\M\T', $expires);
			//header( 'Expires: ' . $expires );		
			echo "Image Handler";
			exit();
		}
	}
	
	/**
	 * This is not used anymore but might be useful for debugging purpose
	 * @Deprecated 
	 */
	function urlHandler_bapitextdata() {
		$url = get_relative($_SERVER['REQUEST_URI']);
		if (strtolower($url) != "/bapi.textdata.js")
			return; // not our handler
		
		header('Content-Type: application/javascript');	
		header('Cache-Control: public');

		$expires = round((60*10 + $lastupdatetime), 2); // expires every 10 mins
		$expires = gmdate('D, d M Y H:i:s \G\M\T', $expires);
		header( 'Expires: ' . $expires );
		
		echo urlHandler_bapitextdata_helper();
		exit();
	}
	
	function urlHandler_bapitextdata_helper() {
		$js = json_encode( kigo_I18n::get_translations( $lang = kigo_get_site_language() ) );
		$jsn = "/*\r\n";
		$jsn .= "	BAPI TextData\r\n";
		$jsn .= "	Language: " . $lang . "\r\n";
		$jsn .= "*/\r\n\r\n";
		$jsn .= "BAPI.textdata = " . $js . ";\r\n";	
		return $jsn;
	}
	
	function urlHandler_bapiconfig() {
		$url = get_relative($_SERVER['REQUEST_URI']);
		if (strtolower($url) != "/bapi.config.js")
			return; // not our handler
		
		header('Content-Type: application/javascript');	
		header('Cache-Control: public');
		//header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
		echo urlHandler_bapiconfig_helper();
		exit();
	}
	
	function urlHandler_bapiconfig_helper() {
		$js = '';
		$js .= 'BAPI.config().searchmodes={}||BAPI.config().searchmodes'; 
		$js .= "\r\n";
		global $bapi_all_options;
		$sitesettings = $bapi_all_options['bapi_sitesettings'];
		/*do nothing if $sitesettings is null or empty*/
		if($sitesettings != null && $sitesettings != ''){
			$array = json_decode($sitesettings, TRUE);
			foreach($array as $v) {
				if (strpos($v, 'BAPI.config()') === 0) {
					$js .= stripslashes($v)."\r\n";
				}
				//print_r($v);
			}
			/* we check if the headline field its enabled. if not dont do a thing*/
			if (strpos($sitesettings,'BAPI.config().headline.enabled=true;') !== false){
				$bapi = getBAPIObj();
				$theProperty = $bapi->quicksearch("property",null,false);
				$headlinesArray = $theProperty["result"];
				if(count($headlinesArray) > 0){
					$js .= "BAPI.config().headline.values=["; 
					  foreach ( $headlinesArray as $page ){
						$js .= '{"Label":"'.str_replace('"',"&quot;",$page["obj"]).'"}';
						if(end($headlinesArray) != $page){
							$js .= ","; // not the last element
						}
					  }
					$js .= "];\r\n";
				}
			}
		}
		return $js;
	}
	
	function propertyList_array(){
		$bapi = getBAPIObj();
		$thePropertyList = $bapi->quicksearch("property",null,false);
		if($thePropertyList["status"]==1){
			return $thePropertyList["result"];
		}
		//return empty array
		return null;
	}
	
	function urlHandler_bapitemplates() {
		$url = get_relative($_SERVER['REQUEST_URI']);
		if (strtolower($url) != "/bapi.templates.js")
			return; // not our handler
		
		header('Content-Type: application/javascript');	
		header('Cache-Control: public');
		//header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
		echo urlHandler_bapitemplates_helper();
		exit();
	}

	//Server up virutal scripts
	function customScriptHandler() {
        global $bapi_all_options;

        $secureurl = '';
        if($bapi_all_options['bapi_secureurl']){
            $secureurl = $bapi_all_options['bapi_secureurl'];
        }
        $siteurl = $bapi_all_options['home'];
        if($bapi_all_options['bapi_site_cdn_domain']){
            $siteurl = $bapi_all_options['bapi_site_cdn_domain'];
	        $siteurl = 'www.coastlinevacationrentals.net';
        }
        $siteurl = str_replace("http://", "", $siteurl);

		if( '/custom-scripts.js' == get_relative($_SERVER['REQUEST_URI']) ) {

			header('Content-Type: application/javascript');	
			header('Cache-Control: public');
			
			ob_start(); ?>

			preload_image = new Image(66,66); 
			preload_image.src="<?= get_relative(plugins_url("/img/loading.gif", __FILE__)) ?>"; 
			BAPI.UI.loading.setLoadingImgUrl('<?= get_relative(plugins_url("/img/loading.gif", __FILE__)) ?>');
			BAPI.site.url =  '<?= $siteurl ?>';
			<?php if ($secureurl!='') { ?>
			BAPI.site.secureurl = '<?= $secureurl ?>';
			<?php } ?>
			BAPI.init();
			BAPI.UI.WPIS_PATH = '<?php echo get_relative( plugins_url( '/', __FILE__ ) ); ?>';
			BAPI.UI.jsroot = '<?= plugins_url("/", __FILE__) ?>';
			BAPI.defaultOptions.logpageviews = false;
			var plugin_url = '<?php echo get_relative( plugins_url( '/', __FILE__ ) ); ?>';
			$(document).ready(function () {

				jQuery(window).trigger("createPickers");

				BAPI.UI.init(); 

				BAPI.UI.createCurrencySelectorWidget('.currencyselector');
				$( '.siteselector .flag' ).each(function(i) {
					var theClassnames = $(this).attr('class');
					theClassnames = theClassnames.substring(0, theClassnames.length - 3);
					$(this).addClass(theClassnames);
				});
			});

			<?php 

			$output = ob_get_clean();

			echo $output;

			exit();
		}
	}
	
	// Used to create the combined file
	function urlHandler_bapitemplates_helper() {		 
		$c = BAPISync::getTemplates();
		$j2 = rawurlencode($c);
		$js = "";
		$js .= "var t = '" . $j2 . "';\r\n";	
		$js .= "t = decodeURIComponent(t);\r\n";
		$js .= "BAPI.templates.set(t);\r\n";	
		return $js;
	}
	
	function urlHandler_sitelist() {
		$url = get_relative($_SERVER['REQUEST_URI']);
		if (strtolower($url) != "/bapi.sitelist.js")
			return; // not our handler
		
		header('Content-Type: application/javascript');	
		header('Cache-Control: public');
		$blog_list = get_blog_list( 0, 'all' );
		$i=0;
		echo '{';
		foreach ($blog_list AS $blog) {
			if ($i>0) echo ', ';
			echo $blog['domain'].$blog['path'];
			$i++;
		}
		echo '}';
		exit();
	}
	
	function urlHandler_timthumb() {
		$url = $_SERVER['REQUEST_URI'];		
		$url = strtolower($url);
		$url = substr($url, 0, 8);
		
		if ($url == "/img.php" || $url == "/img.svc") {
			include('thumbs/timthumb.php');
			exit();
		}
	}
	
	function urlHandler_bapi_ui_min() {
		$url = $_SERVER['REQUEST_URI'];		
		$url = strtolower($url);
		if ($url == "/bapi.ui.min.js") {
			header('Content-Type: application/javascript');	
			header('Cache-Control: public');
			$js = file_get_contents('bapi/bapi.ui.js', true);
			$minifiedCode = \JShrink\Minifier::minify($js);
			echo $minifiedCode;
			exit();
		}
	}
	
	function urlHandler_bapi_js_combined() {
		$url = $_SERVER['REQUEST_URI'];		
		$url = strtolower($url);
		$url = substr($url, 0, 21);
		global $bapi_all_options;
		if($url == "/bapi.combined.min.js" && $bapi_all_options['api_key']){
			$apiKey = $bapi_all_options['api_key'];
			$language = getbapilanguage();			
			
			$secureurl = '';
			if($bapi_all_options['bapi_secureurl']){
				$secureurl = $bapi_all_options['bapi_secureurl'];
			}
			$siteurl = $bapi_all_options['home'];
			if($bapi_all_options['bapi_site_cdn_domain']){
				$siteurl = $bapi_all_options['bapi_site_cdn_domain'];
			}
			
			$siteurl = str_replace("http://", "", $siteurl);
			$sitesettings = $bapi_all_options['bapi_sitesettings'];
		
			header('Content-Type: application/javascript');	
			header('Cache-Control: public');
			//$js = urlHandler_bapi_js_combined_helper();
			if( // In debug mode, do not minify or use cache for the combined JS file
				defined('KIGO_DEBUG') &&
				true === KIGO_DEBUG
			) {
				$js = urlHandler_bapi_js_combined_helper();
				echo $js;
				exit();
			}
			
			//$cacheKey value is now managed in cachekey.php
			require_once('cachekey.php');
			$cacheKey = BAPI_COMBINED_CACHE_KEY; 
			
			if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				//$cacheFile = sys_get_temp_dir().'\\'.$jsh.'.js';
				$cacheFile = sys_get_temp_dir().'\\combined-'.$apiKey.'-'.$cacheKey.'.js';
			} else {
				//$cacheFile = sys_get_temp_dir().'/'.$jsh.'.js';
				$cacheFile = sys_get_temp_dir().'/combined-'.$apiKey.'-'.$cacheKey.'.js';
			}
			$maxcache = 14400; //use this to set max age of js cache file
			if(empty($bapi_all_options['bapi_sitesettings_lastmod'])){
				//Backward compatibility: The 'bapi_sitesettings_lastmod' option is new.  For users who have not yet saved since publishing, this will set the option to an initial value that will work well.
				update_option('bapi_sitesettings_lastmod', time());
				$bapi_all_options = bapi_wp_site_options();
			}
			$settingsmod = time() - $bapi_all_options['bapi_sitesettings_lastmod'];
			if(file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $maxcache && (time() - filemtime($cacheFile)) < $settingsmod){
				include($cacheFile);
			}
			else{
				$js = urlHandler_bapi_js_combined_helper();
				$fp = fopen($cacheFile, 'w');
				$minifiedCode = \JShrink\Minifier::minify($js);
				fwrite($fp, $minifiedCode);
				fclose($fp);
				echo $minifiedCode;
			}
			exit();
		}
	}
	
	function urlHandler_bapi_js_combined_helper() {
		global $bapi_all_options;
		$sitesettings = $bapi_all_options['bapi_sitesettings'];
		$js = '';
		$js .= file_get_contents('bapi/bapi.ui.js', true);
		$js .= urlHandler_bapitextdata_helper();
		$js .= urlHandler_bapitemplates_helper();
		if (!empty($sitesettings) && $sitesettings!='') {
			$js .= urlHandler_bapiconfig_helper();
		}
		$js .= 'var js_generated_on = \''.date(c).'\';';
		return $js;
	}

	/* Converted a url to a physical file path */
	function get_local($url) {
		$urlParts = parse_url($url);
		return realpath($_SERVER['DOCUMENT_ROOT']) . $urlParts['path'];				
	}
	
	function get_relative($url) {
		$urlParts = parse_url($url);
		return $urlParts['path'];		
	}
	
	function get_adminurl($url) {
		$url = get_relative( plugins_url($url, __FILE__) );
		$siteurl = parse_url(site_url());
		$str = $siteurl['path']."/wp-content/plugins";
		return str_replace($str,"",$url);	
	}	
	
	 /**
	 * Retrieve the plugin folder server path.
	 * 
	 * * IMPORTANT: This function has to be in the root folder of the plugin in order to return the correct value. 
	 * 
	 * @param string $file_path		Optional. Extra path (relative to the plugin folder) appended to the end of the PATH. Default empty string.
	 *
	 * @return string
	 */
	function get_kigo_plugin_path( $file_path = '' ) {
		if( !is_string( $file_path ) ) {
			return '';
		}
		return  plugin_dir_path( __FILE__ ) . $file_path;
	}
	
	/**
	 * Retrieve the plugin folder URL.
	 * 
	 * * IMPORTANT: This function has to be in the root folder of the plugin in order to return the correct value. 
	 * 
	 * @param string $file_path		Optional. Extra path (relative to the plugin folder) appended to the end of the URL. Default empty string.
	 *
	 * @return string
	 */
	function get_kigo_plugin_url( $file_path = '' ) {
		if( !is_string( $file_path ) ) {
			return '';
		}
		return plugins_url( $file_path, __FILE__ );
	}
	
	/* BAPI Helpers */	
	function getbapiurl() {
		global $bapi_all_options;
		$bapi_baseurl = 'connect.bookt.com';
		//Check if there is a globally defined baseurl constant.  This should be set in wp-config.php like so: define('BAPI_BASEURL', 'connect.bookt.com');
		if(defined(BAPI_BASEURL)){ 
			$bapi_baseurl = BAPI_BASEURL;
		}
		if($bapi_all_options['bapi_baseurl']){
			$bapi_baseurl = $bapi_all_options['bapi_baseurl'];
		}
		if(empty($bapi_baseurl) || $bapi_baseurl=='connect.bookt.com'){
			$bapi_baseurl = 'd2kqqk9digjl80.cloudfront.net';  
			//$bapi_baseurl = 'connect.bookt.com';
		}
		if (stripos($bapi_baseurl, "localhost", 0) === 0) {			
			return "http://" . $bapi_baseurl;
		}
		return "https://" . $bapi_baseurl;
	}

	function getbapilanguage() {
		global $bapi_all_options;
		$language = $bapi_all_options['bapi_language'];	
		if(empty($language)) {
			$language = "en-US";
		}
		return $language;	
	}
	
	function kigo_get_site_language() { 
		if(
			!is_array( $solution_data = BAPISync::getSolutionData() ) ||
			!is_array( $solution_data[ 'Site' ] ) ||
			!is_string( $solution_data[ 'Site' ][ 'Language' ] )
		) { 
			Loggly_logs::log( array( 'msg' => 'Unable to retrieve site language from solution data.', 'blog_id' => get_current_blog_id() ) );
			return 'en-US';
		}
		return $solution_data[ 'Site' ][ 'Language' ];
	}
	
	function bapi_language_attributes($doctype) {
		return 'lang="'.getbapilanguage().'" dir='.(is_rtl() ? 'rtl' : 'ltr');
	}

	function getbapijsurl($apiKey) {
		return getbapiurl() . "/js/bapi.min.js?apikey=" . $apiKey;
	}
	
	function getbapiapikey() {
		global $bapi_all_options;
		return $bapi_all_options['api_key'];	
	}
	
	function getbapisolutiondata() {
		$wrapper = array();
		$wrapper['site'] = getbapicontext();
		$wrapper['textdata'] = kigo_I18n::get_translations( kigo_get_site_language() );
		return $wrapper;
	}	

	static $BAPI_ALL_OPTIONS__BAPI_SOLUTIONDATA_DECODED = null;
	function getbapicontext() {	
		global $bapi_all_options, $BAPI_ALL_OPTIONS__BAPI_SOLUTIONDATA_DECODED;

		// cache the JSON decoding, as this is called several times in 1 execution
		if(!is_array($BAPI_ALL_OPTIONS__BAPI_SOLUTIONDATA_DECODED)) {
			if(empty($bapi_all_options['bapi_solutiondata'])) $bapi_all_options = bapi_wp_site_options();
			$BAPI_ALL_OPTIONS__BAPI_SOLUTIONDATA_DECODED = json_decode( wp_unslash($bapi_all_options['bapi_solutiondata']), true );
		}

		return $BAPI_ALL_OPTIONS__BAPI_SOLUTIONDATA_DECODED;
	}
	
	/**
	 * @deprecated
	 */
	function getbapitextdata() {
		return kigo_I18n::get_translations( kigo_get_site_language() );
	}	
	
	/* Page Helpers */
	function getPageKeyForEntity($entity, $pkid) {
		return $entity . ':' . $pkid;
	}	
	
	function getPageForEntity($entity, $pkid, $parentid) {
		$pagekey = getPageKeyForEntity($entity, $pkid); 
		$args = array('meta_key' => 'bapikey', 'meta_value' => $pagekey, 'child_of' => $parentid);
		return get_pages($args);		
	}
	
	function enqueue_and_register_my_scripts_in_head(){
		wp_register_script( 'jquery-min', '//cdnjs.cloudflare.com/ajax/libs/jquery/1.9.1/jquery.min.js',false,'1.9.1' );
		wp_enqueue_script( 'jquery-min' );
		
		wp_register_script( 'jquery-migrate-min', '//cdnjs.cloudflare.com/ajax/libs/jquery-migrate/1.2.1/jquery-migrate.min.js',array( 'jquery-min'),'1.2.1' );
		wp_enqueue_script( 'jquery-migrate-min' );
		
		wp_register_script( 'jquery-ui-min', '//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js',array( 'jquery-min'),'1.10.3' );
		wp_enqueue_script( 'jquery-ui-min' );

		wp_register_script( 'jquery-ui-i18n-min', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/i18n/jquery-ui-i18n.min.js',array( 'jquery-min'),'1.10.3' );
		wp_enqueue_script( 'jquery-ui-i18n-min' );
                
        wp_register_style( 'kigo-plugin-main', get_relative(plugins_url('/css/style.css', __FILE__)) );
		wp_enqueue_style( 'kigo-plugin-main' );
		
		wp_register_style( 'jquery-ui', '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/smoothness/jquery-ui.min.css', array(), '1.10.3' );
		wp_enqueue_style( 'jquery-ui' );
	}

	function enqueue_footer() {
		wp_enqueue_script( 'unveil', get_relative(plugins_url( '/js/jquery.unveil.js', __FILE__)), array('jquery-min') );
		wp_enqueue_script( 'cookie', get_relative(plugins_url( '/js/jquery.cookie.js', __FILE__)), array('jquery-min') );
		wp_enqueue_script( 'scripts', get_relative(plugins_url( '/js/scripts.js', __FILE__)), array('unveil', 'cookie', 'flexslider') );
	}
	add_action('wp_footer', 'enqueue_footer');

	function enqueue_and_register_admin_scritps() {
		wp_register_script( 'kigo-plugin-admin-js', get_relative( plugins_url( '/js/admin.js', __FILE__) ), array( 'jquery-min'), false, true );
		wp_enqueue_script( 'kigo-plugin-admin-js' );
	}
	
	/* Load conditional script */
	function loadscriptjquery(){	
	?>  
		<!--[if lt IE 8]>
		<script type="text/javascript" src="<?= get_relative(plugins_url('/js/pickadate/source/legacy.min.js', __FILE__)) ?>" ></script>
		<![endif]-->
		<!--[if gte IE 8]>
		<script type="text/javascript" src="<?= get_relative(plugins_url('/js/pickadate/source/picker.min.js', __FILE__)) ?>" ></script>
		<![endif]-->
		<!--[if !IE]> -->
		<script type="text/javascript" src="<?= get_relative(plugins_url('/js/pickadate/source/picker.min.js', __FILE__)) ?>" ></script>
		<script type="text/javascript" src="<?= get_relative(plugins_url('/js/pickadate/source/picker.date.min.js', __FILE__)) ?>" ></script>
		<!-- <![endif]-->     
	<?php	
	}
	
	/* Common include files needed for BAPI */
	function getconfig() {
		global $bapi_all_options;
		if($bapi_all_options['api_key']){
			$apiKey = $bapi_all_options['api_key'];
			$language = getbapilanguage();			
			
			$secureurl = '';
			if($bapi_all_options['bapi_secureurl']){
				$secureurl = $bapi_all_options['bapi_secureurl'];
			}
			$siteurl = $bapi_all_options['home'];
			if($bapi_all_options['bapi_site_cdn_domain']){
				$siteurl = $bapi_all_options['bapi_site_cdn_domain'];
			}
			
			$siteurl = str_replace("http://", "", $siteurl);
			$sitesettings = $bapi_all_options['bapi_sitesettings'];

			ob_start();
			?>

<script type="text/javascript">
	<?php /* This was moved to a url handler bc we weren't able to use wp_add_inline_scripts yet */ ?>
	preload_image = new Image(66,66); 
	preload_image.src="<?= get_relative(plugins_url("/img/loading.gif", __FILE__)) ?>"; 
	BAPI.UI.loading.setLoadingImgUrl('<?= get_relative(plugins_url("/img/loading.gif", __FILE__)) ?>');
	BAPI.site.url =  '<?= $siteurl ?>';
	<?php if ($secureurl!='') { ?>
	BAPI.site.secureurl = '<?= $secureurl ?>';
	<?php } ?>
	BAPI.init();
	BAPI.UI.WPIS_PATH = '<?php echo get_relative( plugins_url( '/', __FILE__ ) ); ?>';
	BAPI.UI.jsroot = '<?= plugins_url("/", __FILE__) ?>';
	BAPI.defaultOptions.logpageviews = false;
	var plugin_url = '<?php echo get_relative( plugins_url( '/', __FILE__ ) ); ?>';
	$(document).ready(function () {
		jQuery(window).trigger("createPickers");

		BAPI.UI.init(); 

		BAPI.UI.createCurrencySelectorWidget('.currencyselector');
		$( '.siteselector .flag' ).each(function(i) {
			var theClassnames = $(this).attr('class');
			theClassnames = theClassnames.substring(0, theClassnames.length - 3);
			$(this).addClass(theClassnames);
		});
	});
</script>

			<?php
			$custom = ob_get_clean();

			$deps = is_admin() ? array('scripts') : array(); 
			wp_enqueue_script( 'bapijs', getbapijsurl($apiKey), array('scripts') );
			//wp_add_inline_script( 'bapijs', 'jQuery(document).ready(function(){ $(window).trigger("createPickers") });' );
			wp_enqueue_script( 'bapi-combined', '/bapi.combined.min.js?ver='.md5(urlHandler_bapi_js_combined_helper()), array('bapijs', 'typeahead', 'google-maps') );
			wp_enqueue_script( 'custom-scripts', '/custom-scripts.js', array('bapi-combined') );
			//wp_add_inline_script( 'bapi-combined', $custom);
			?>
			
			<?php			
		}
	}


	/* Slideshow */
	function bapi_get_slideshow($mode='raw'){ 

		function get_media($url) {
			global $wpdb;

			$file = basename($url); 
			$ext = pathinfo($file, PATHINFO_EXTENSION);

			$img_name = basename($file, ".".$ext); // $file is set to "home"

			$query = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_title LIKE '{$img_name}' AND post_type LIKE 'attachment'";

			$thumb_id = $wpdb->get_var($query);
			if( !is_null($thumb_id) ) {
			    $attachment = wp_get_attachment_image_src($thumb_id, 'large');
			    $img_url = $attachment[0];

			    return $img_url;
			}
		}

		$slideshow = [];
		for($i=1; $i<=6; $i++) {
			$url = get_option('bapi_slideshow_image'.$i, false); 

			if($url) {
				$slideshow[] = array(
					'url'		=> $url, //get_media($url),
					'caption'	=> get_option('bapi_slideshow_caption'.$i),
					'thumb'		=> $url ? plugins_url('thumbs/timthumb.php?src='.urlencode($url).'&h=80', __FILE__) : ''
				);
			}
		}


		if($mode=='raw'){
			return $slideshow;
		}
		if($mode=='json'){
			$json = json_encode($slideshow);
			?>
			<script>
				var slides_json = '<?= $json ?>';
			</script>	
			<?php
			return true;
		}
		if($mode=='divs'){
			foreach($slideshow as $sl){
				?>
				<div>
					<a href=""><img src="<?= $sl['url'] ?>" title="<?= $sl['caption'] ?>" /></a>
				</div>
				<?php
			}
			return true;
		}
	}
	
	/* CDN Support */
	function home_url_cdn( $path = '', $scheme = null ) {
		return get_home_url_cdn( null, $path, $scheme );
	}

	function get_home_url_cdn( $blog_id = null, $path = '', $scheme = null ) {	
		$cdn_url = get_option('home');
		if(get_option('bapi_site_cdn_domain')&&!(current_user_can('manage_options')||is_super_admin())){
			$cdn_url = get_option('bapi_site_cdn_domain');
		}
		$home_url = str_replace(get_option('home'),$cdn_url,$path);
		//echo $home_url; 
		
		return $home_url;
	}
	
	function add_server_name_meta(){
		$sn = gethostname();
		echo '<meta name="SERVERNAME" content="'.$sn.'" />'."\n";
	}
	
	function bapi_redirect_fix($redirect_url, $requested_url) {
		$cdn_domain = parse_url(get_option('bapi_site_cdn_domain'));
		$redirect = parse_url($redirect_url);
		if($redirect['scheme']!='https') {
			$redirect_url = $redirect['scheme'].'://'.$cdn_domain['host'];
			$redirect_url .= $redirect['path'];
			if ( !empty($redirect['query']) ) {
				$redirect_url .= '?' . $redirect['query'];
			}
			return $redirect_url; 
		}
		return $redirect_url;
	}
	
	function bapi_getmeta(){
		$pid = get_the_ID();
		
		$metak = esc_attr( get_post_meta( $pid,'bapi_meta_keywords', true ) );
		$metad = esc_attr( get_post_meta( $pid,'bapi_meta_description', true ) );
		
		$lastu = (int) get_post_meta($pid,'bapi_last_update',true);
		$lastu = date('r',$lastu);
		
		?>
		<meta name="LASTMOD" content="<?= $lastu ?>" />
		<meta name="KEYWORDS" content="<?= $metak ?>" />
		<meta name="DESCRIPTION" content="<?= $metad ?>" />
		<meta name="CATEGORY" content="travel" />

		<?php
	}
	
	function bapi_add_entity_meta(){
		global $entityUpdateURL;
		?><meta name="ENTITYURL" content="<?= $entityUpdateURL ?>" /><?= "\n" ?><?php
	}
	function bapi_add_context_meta(){
		global $getContextURL;
		?><meta name="CONTEXTURL" content="<?= $getContextURL ?>" /><?= "\n" ?><?php
	}

	/**
	 * @Deprecated
	 */
	function bapi_add_textdata_meta(){
		global $textDataURL;
		?><meta name="TEXTDATAURL" content="<?= $textDataURL ?>" /><?= "\n" ?><?php
	}
	function bapi_add_seo_meta(){
		global $seoDataURL;
		?><meta name="SEOURL" content="<?= $seoDataURL ?>" /><?= "\n" ?><?php
	}

	function getBAPIObj() {
		global $bapi_instance;
		global $bapi_all_options;

		$baseurl = isset($bapi_all_options['bapi_baseurl']) && strlen($bapi_all_options['bapi_baseurl']) ? $bapi_all_options['bapi_baseurl'] : 'connect.bookt.com';
		$baseurl = (strpos($baseurl, 'localhost') === 0 || (strpos($baseurl, 'localdomain') >= 0) ? 'http://' : 'https://').$baseurl;

		if(
			!isset( $bapi_instance ) ||
			!is_a( $bapi_instance, 'BAPI' ) ||
			$bapi_instance->getApikey() !== $bapi_all_options['api_key'] ||
		    $bapi_instance->getBaseURL() !== $baseurl
		) {
			$bapi_instance = new BAPI($bapi_all_options['api_key'], $baseurl);
		}

		return $bapi_instance;
	}
	
	function disable_kses_content() {
		if(is_admin()||is_super_admin()){
			remove_filter('content_save_pre', 'wp_filter_post_kses');
		}
	}
	
	function custom_upload_mimes ( $existing_mimes=array() ) {
		// add the file extension to the array
		$existing_mimes['ico'] = 'image/x-icon';
		// call the modified list of extensions
		return $existing_mimes;
	}
	
	function display_global_header(){
		global $bapi_all_options;
		echo $bapi_all_options['bapi_global_header'];
	}
	
	function perm($return) {
		if (
			!is_super_admin() &&
			isset($_GET['post']) && strlen($_GET['post']) &&
			is_array($metaArray = get_post_meta($_GET['post'])) &&
			(
				array_key_exists('bapi_page_id', $metaArray) ||
				array_key_exists('bapikey', $metaArray) ||
				array_key_exists('bapi_last_update', $metaArray)
			)
		) {
			// the user is not super admin AND our custom fields exist (it's a BAPI page)
			//     hence we remove the permalink editing possibilities
			$return = preg_replace( '/<span id="edit-slug-buttons">(.*?)<\/span>/i', '', $return);
			$return = preg_replace_callback(
				'/<span id="editable-post-name([^"]*)">(.*?)<\/span>/i',
				function($mtch) {
					return ($mtch[1] === '-full') ? '' : $mtch[2];
				}, $return);
		}
		return $return;
	}

	// This function is called by templates (this could be replaced by a shortcode allowing client to decide wheater or not to display SSL)
	function getSSL(){
		global $wp_query;
		$postid = $wp_query->post->ID;
		$thePostMeta = get_post_meta($postid, 'bapi_page_id', true);

		if(
			!defined('BYPSASS_SSL_VERIFY') || constant('BYPSASS_SSL_VERIFY') != true && 
			'bapi_makepayment' === $thePostMeta ||
			'bapi_makebooking' === $thePostMeta
		) { 
			$ssl_config = new Kigo_Ssl_Config();
			echo $ssl_config->get_ssl_seal();
		}
	}
	
	function bapi_setup_default_pages() {
		global $bapi_all_options;
		$url = get_relative($_SERVER['REQUEST_URI']);
		//echo $url; exit();
		if (strtolower($url) == "/bapi.init")
			return;
		if(!(strpos($_SERVER['REQUEST_URI'],'wp-admin')===false)||!(strpos($_SERVER['REQUEST_URI'],'wp-login')===false)){
			return;
		}
		$menuname = "Main Navigation Menu";
		$menu_id = initmenu($menuname);
		$menu = wp_get_nav_menu_items($menu_id);
		//print_r($menu);
		if(count($menu) == 0){
			//Initialize menu and pages
			if($bapi_all_options['bapi_first_look']==1){
				wp_die('<h3>Site Configuration Incomplete</h3>Please <a href="/wp-login.php?redirect_to='.urlencode(get_site_url()).'">sign-in to the dashboard</a> to complete setup','Site Configuration Incomplete');
			}
			$path = '/bapi.init?mode=initial-setup';
			$url = get_site_url().$path;
			//$server_output = file_get_contents($url);
			header("Cache-Control: no-cache, must-revalidate");
			header("HTTP/1.1 307 Temporary Redirect");
			header("Location: $url");
			exit();
		}
	}
	
	function bapi_reset_first_look(){
		update_option( 'bapi_first_look', 0 );
	}
	
	function bapi_login_handler(){
		header('Access-Control-Allow-Origin: *');
		$url = get_relative($_SERVER['REQUEST_URI']);
		//if (strtolower($url) != "/bapi.login"){
		if (strpos($_SERVER['REQUEST_URI'],'bapi.login')===false){
			return;
		}
		header("Cache-Control: no-cache, must-revalidate");
		
		$username = $_REQUEST['username'];
		$password = $_REQUEST['password'];
		$redir = $_REQUEST['redir'];
		
		$creds = array();
		$creds['user_login'] = $username;
		$creds['user_password'] = $password;
		$creds['remember'] = true;
		$user = wp_signon( $creds, false );
		if ( is_wp_error($user) )
			wp_die($user->get_error_message());
			
		header("HTTP/1.1 307 Temporary Redirect");
		header("Location: $redir");
		exit();
	}
	
	function bapi_no_follow(){
		//Amazon CloudFront
		if($_SERVER['HTTP_USER_AGENT']!="Amazon CloudFront"){
			?>
            <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
            <?php
		}
	}
	
	
  function relative_url() {
    // Don't do anything if:
    // - In feed
    // - In sitemap by WordPress SEO plugin
    if ( is_feed() || get_query_var( 'sitemap' ) )
      return;
    $filters = array(
      'post_link',       // Normal post link
      'post_type_link',  // Custom post type link
      'page_link',       // Page link
      '_page_link',       // Page link?
      'attachment_link', // Attachment link
      'get_shortlink',   // Shortlink
      'post_type_archive_link',    // Post type archive link
      'get_pagenum_link',          // Paginated link
      'get_comments_pagenum_link', // Paginated comment link
      'term_link',   // Term link, including category, tag
      'search_link', // Search link
      'day_link',   // Date archive link
      'month_link',
      'year_link',

      // site location
      'option_siteurl',
      'blog_option_siteurl',
      'option_home',
      'admin_url',
      'home_url',
      'includes_url',
      'site_url',
      'site_option_siteurl',
      'network_home_url',
      'network_site_url',

      // debug only filters
      'get_the_author_url',
      'get_comment_link',
      'wp_get_attachment_image_src',
      'wp_get_attachment_thumb_url',
      'wp_get_attachment_url',
      'wp_login_url',
      'wp_logout_url',
      'wp_lostpassword_url',
      'get_stylesheet_uri',
      // 'get_stylesheet_directory_uri',
      // 'plugins_url',
      // 'plugin_dir_url',
      // 'stylesheet_directory_uri',
      // 'get_template_directory_uri',
      // 'template_directory_uri',
      'get_locale_stylesheet_uri',
      'script_loader_src', // plugin scripts url
      'style_loader_src', // plugin styles url
      'get_theme_root_uri'
      // 'home_url'
    );

    foreach ( $filters as $filter ) {
      add_filter( $filter, 'bapi_make_link' );
    }
    home_url($path = '', $scheme = null);
  }
  
  
function bapi_make_link( $link ) {
	global $bapi_all_options; 
	$filters = array(
      'post_link',       // Normal post link
      'post_type_link',  // Custom post type link
      'page_link',       // Page link
      '_page_link',       // Page link?
      'attachment_link', // Attachment link
      'get_shortlink',   // Shortlink
      'post_type_archive_link',    // Post type archive link
      'get_pagenum_link',          // Paginated link
      'get_comments_pagenum_link', // Paginated comment link
      'term_link',   // Term link, including category, tag
      'search_link', // Search link
      'day_link',   // Date archive link
      'month_link',
      'year_link',

      // site location
      'option_siteurl',
      'blog_option_siteurl',
      'option_home',
      'admin_url',
      'home_url',
      'includes_url',
      'site_url',
      'site_option_siteurl',
      'network_home_url',
      'network_site_url',

      // debug only filters
      'get_the_author_url',
      'get_comment_link',
      'wp_get_attachment_image_src',
      'wp_get_attachment_thumb_url',
      'wp_get_attachment_url',
      'wp_login_url',
      'wp_logout_url',
      'wp_lostpassword_url',
      'get_stylesheet_uri',
      // 'get_stylesheet_directory_uri',
      // 'plugins_url',
      // 'plugin_dir_url',
      // 'stylesheet_directory_uri',
      // 'get_template_directory_uri',
      // 'template_directory_uri',
      'get_locale_stylesheet_uri',
      'script_loader_src', // plugin scripts url
      'style_loader_src', // plugin styles url
      'get_theme_root_uri'
      // 'home_url'
    );
	
	$cdn_url = 'test.com';
	//$home_url = str_replace($bapi_all_options['home'],$cdn_url,$path);
	if($bapi_all_options['bapi_site_cdn_domain']&&!(current_user_can('manage_options')||is_super_admin())){
		$cdn_url = $bapi_all_options['bapi_site_cdn_domain'];
	}
		
	foreach ( $filters as $filter ) {
	  remove_filter( $filter, 'bapi_make_link' );
	}
	$cdn = rtrim($cdn_url,'/');
	return preg_replace( '|https?://[^/]+(/?.*)|i', $cdn, $link );
	foreach ( $filters as $filter ) {
	  add_filter( $filter, 'bapi_make_link' );
	}
}

function display_gw_verification(){
	global $bapi_all_options;
	if(strlen($bapi_all_options['bapi_google_webmaster_htmltag'])>1){
		?><meta name="google-site-verification" content="<?= esc_attr($bapi_all_options['bapi_google_webmaster_htmltag']) ?>" />
<?php
	}
}

// Used by themes to retrieve the textdata
function getTextDataArray(){
	return kigo_I18n::get_translations( kigo_get_site_language() );
}
	/**
	* Remove quick edit link in the list of all pages for non super users.
	*
	* @param	array		$actions		The page row actions
	* @param	object		$page_object			The page being listed
	* @return	array
	*/
	function remove_quickedit_for_nonsuperusers( $actions, $page_object ) {
		/* if the user is not super admin */
		if (!is_super_admin()) {
			/* we get the page ID */
			$thePageID = $page_object->ID;
			/* we get the meta data array for this post */
			$metaArray = get_post_meta($thePageID);
				/* we check if our custom fields exists */
				if(!empty($metaArray) && array_key_exists('bapi_page_id', $metaArray) || array_key_exists('bapikey', $metaArray) || array_key_exists('bapi_last_update', $metaArray)){
					/* this is not a super admin and the page is a BAPI page we remove quick edit*/
					unset ( $actions ['inline hide-if-no-js'] );
				}
		}
		return $actions;
	}
	
	/**
	* Remove page attributes meta box.
	*
	* @uses		remove_meta_box()
	*/
	function remove_pageattributes_meta_box() { 
		/* if the post var is set this var show when editing post and pages like this /wp-admin/post.php?post=2468&action=edit */
		if(isset($_GET['post']) && $_GET['post'] != '' && $_GET['action'] == 'edit'){
		/* its set we get the post ID */
		$thePostID = $_GET['post'];
		/* we get the meta data array for this post */
		$metaArray = get_post_meta($thePostID); 
			/* we check if our custom fields exists */
			if(!empty($metaArray) && array_key_exists('bapi_page_id', $metaArray) || array_key_exists('bapikey', $metaArray) || array_key_exists('bapi_last_update', $metaArray)){
				if (!is_super_admin()) {
					/* this is not a super admin and the page is a BAPI page we remove the metabox*/
					remove_meta_box( 'pageparentdiv', 'page', 'normal' );
				}
				/* lets add a metabox with a message as to why there is no page Attributes metabox */
				if(!array_key_exists('bapi_page_id', $metaArray)){
					add_meta_box( 'pageattributesmessage_meta_box_id', 'Type: Data-Driven', 'create_DataDriventDetailPagesmessage_meta_box', 'page', 'side', 'high' );

					if (!is_super_admin()) {
						/* this is not a super admin and the page is a BAPI page we remove the metabox*/
						remove_meta_box( 'pageparentdiv', 'page', 'normal' );
					}
					/* lets add a metabox with a message as to why there is no page Attributes metabox */
					if(!array_key_exists('bapi_page_id', $metaArray)){
						add_meta_box( 'pageattributesmessage_meta_box_id', 'Type: Data-Driven', 'create_DataDriventDetailPagesmessage_meta_box', 'page', 'side', 'high' );
						if (!is_super_admin()) {
							remove_post_type_support('page', 'editor');
							remove_post_type_support('page', 'title');
						}
					}else{
						add_meta_box( 'pageattributesmessage_meta_box_id', 'Type: BAPI-Initialized', 'create_BAPIInitializedPagesmessage_meta_box', 'page', 'side', 'high' );
					}
				}else{
					add_meta_box( 'pageattributesmessage_meta_box_id', 'Type: Static', 'create_StaticPagesmessage_meta_box', 'page', 'side', 'high' );
				}
			}
		}
	}
	
	function create_DataDriventDetailPagesmessage_meta_box()
	{
		echo '<div class="updated inline"><p>This page is synchronized with Kigo. All editing has been disabled.</p> <a href="//supportdocs.imbookingsecure.com/missing_attributes_on_shared_pages" target="_blank">Learn More</a></div>';
	}
	function create_BAPIInitializedPagesmessage_meta_box()
	{
		// Retrieve the current post_name
		if(
			!is_a( $post = get_post(), 'WP_Post' ) ||
			!is_string( $post_name = $post->post_name )
		) {
			$post_name = '';
		}
		echo '<div class="updated inline">
				<p>This page is synchronized with Kigo. You may only edit the page content. All other editing functions have been disabled.</p>
				<a href="//supportdocs.imbookingsecure.com/missing_attributes_on_shared_pages" target="_blank">Learn More</a>
			</div>
			<div>
				<button id="restore-default-content-button" class="button button-primary button-large" data-post-name="' . $post_name . '">Restore default content</button>
				<span id="restore-default-content-spiner" class="spinner"></span>';
		$bapi_page_id = get_post_custom_values( 'bapi_page_id' );
		if(get_page_template_slug( $post->ID ) != 'page-templates/contact-page.php' && $bapi_page_id[0] == "bapi_contact"){
				echo '<p><button id="use-new-version-button" class="button button-primary button-large">Use New Template</button></p>';
		}
		echo '</div>';
			
	}
	function create_StaticPagesmessage_meta_box()
	{
		echo '<div class="updated inline"><p>This page is a WordPress Page. All editing its enabled.</p> <a href="//supportdocs.imbookingsecure.com/missing_attributes_on_shared_pages" target="_blank">Learn More</a></div>';
	}
	
/* Custom Instasite Dashboard */

function bapi_welcome_panel() {
/*
 Hide the defaul welcome message and put the custom KigoSite block.
*/	
?>	
<script type="text/javascript">
/* Hide default welcome message */
jQuery(document).ready( function($) 
{
	$('#welcome-panel .welcome-panel-content').hide();
	$('#welcome-panel .welcome-panel-close').hide();
	$('#welcome-panel.custom .welcome-panel-close').show();
	
});
</script>
<div id="welcome-panel" class="welcome-panel custom">
		<?php wp_nonce_field( 'welcome-panel-nonce', 'welcomepanelnonce', false ); ?>
        <div class="btn-close">
		<a class="welcome-panel-close" href="<?php echo esc_url( admin_url( '?welcome=0' ) ); ?>"><?php _e( 'Close' ); ?></a>
		</div>
        <div class="welcome-panel-content-custom">
        <h1><?php _e('Welcome to your Kigo site!'); ?></h1>
        <p class="about-description"><?php _e( 'If this is your first time here, take the tour, choose theme, etc.' ); ?><br />			<?php _e( 'If you are tired of seeing this message simply close at the top right.' ); ?></p>
        </div>
</div>
<?php
}
add_action( 'welcome_panel', 'bapi_welcome_panel' );

function bapi_dashboard_custom_footer() {
		return; // Do not display the footer fo the newapp users
	}
add_filter( 'admin_footer_text', 'bapi_dashboard_custom_footer' );

function hide_dashboard_metabox() {
/* Put off the wordpress dashboard default metabox*/	
   $hide = array(
      0 => 'dashboard_recent_comments',
      1 => 'dashboard_incoming_links',
      2 => 'dashboard_activity',
      3 => 'dashboard_quick_press',
      4 => 'dashboard_primary',
      5 => 'dashboard_secondary',
	  6 => 'dashboard_recent_drafts',
	  7 => 'dashboard_right_now',
   );
   return $hide;
}
add_filter('get_user_option_metaboxhidden_dashboard', 'hide_dashboard_metabox', 1);

function bapi_register_dashboard_metabox() {
/* Add the custom KigoSites Metaboxes */	
	global $wp_meta_boxes;	
	  add_meta_box('bapi-gs', 'Getting Started', 'register_started_box', 'dashboard', 'normal', 'high');
	  add_meta_box('bapi-action', 'Advanced Actions', 'register_action_box', 'dashboard', 'normal', 'high');
	  add_meta_box('bapi-tips', 'Tips', 'register_tips_box', 'dashboard', 'side', 'high');
	  wp_enqueue_style( 'custom-dashboard', plugins_url('css/custom-dashboard.css', __FILE__) );
	}
add_action('wp_dashboard_setup', 'bapi_register_dashboard_metabox',2);

function register_started_box() {	
/* Getting Started Metabox */
	$items = array(
				array( 'url' => admin_url( "themes.php" ),
					  'icon' => "welcome-icon dashicons-images-alt2",
					  'name' => "Choose your theme"
					),
				array( 'url' => admin_url( "themes.php?page=theme_options#tabs-1" ),
					  'icon' => 'welcome-icon dashicons-admin-appearance',
					  'name' => 'Change your theme style',
					),
				array( 'url' => menu_page_url( "site_settings_slideshow", false ),
					  'icon' => "welcome-icon dashicons-format-gallery",
					  'name' => "Add a slideshow"
					),
				array( 'url' => admin_url( "nav-menus.php" ),
					  'icon' => "welcome-icon dashicons-menu",
					  'name' => "Manage your menu"
					),
				array( 'url' => admin_url( "post-new.php?post_type=page" ),
					  'icon' => "welcome-icon dashicons-welcome-add-page",
					  'name' => "Add a page"
					)
			 );
	// Display the container
	echo '<div class="welcome-panel rss-widget custom">';
   echo '<ul>';
   for($i = 0; $i < count($items) ; $i++ ){		
				echo '<li>';
				echo '<a href="' . $items[$i]['url'] . '" class="' . $items[$i]['icon'] . '">';
				echo $items[$i]['name'];
				echo '</a>';
				echo '</li>';
	}
	echo '<li><a class="button button-primary button-hero" href="'.home_url( '/' ).'" target="_blank">View your site</a></li>';
	echo '</ul></div>';
}
function register_instaapp_box() {
/* Instaapp Options Metabox */
	$items = array(
			   array( 'url' => "https://newapp.kigo.net/marketing/properties/",
                      'icon' => "welcome-icon dashicons-screenoptions",
                      'name' => "Manage Properties"
                    ),
               array( 'url' => "https://newapp.kigo.net/marketing/propertyfinders/",
                      'icon' => 'welcome-icon dashicons-search',
                      'name' => 'Set up Property Finders',
                    ),
               array( 'url' => "https://newapp.kigo.net/marketing/attractions/",
                      'icon' => "welcome-icon dashicons-location-alt",
                      'name' => "Set up Attractions"
                    ),
				array( 'url' => "https://newapp.kigo.net/booking/mgr/setup/specials/",
                      'icon' => "welcome-icon dashicons-awards",
                      'name' => "Add Specials for your visitors"
                    ),
				array( 'url' => "https://newapp.kigo.net/marketing/optionalservices/",
                      'icon' => "welcome-icon dashicons-plus",
                      'name' => "See Optional Services"
                    )
             );
	// Display the container
	echo '<div class="welcome-panel rss-widget custom">';
echo '<ul>';
   for($i = 0; $i < count($items) ; $i++ ){
				echo '<li>';
				echo '<a href="'.$items[$i]['url'].'" class="'.$items[$i]['icon'].'" target="_blank">';
				echo $items[$i]['name'];
				echo '</a>';
				echo '</li>';
	}
		echo '<li><a class="button button-primary button-hero" href="https://newapp.kigo.net/" target="_blank">Go To Kigo App</a></li>';
	echo '</ul></div>';
}
function register_action_box() {
/* Advanced Options Metabox */
	$items = array(
				array( 'url' => admin_url( "options-general.php?page=mr_social_sharing" ),
                      'icon' => "welcome-icon dashicons-facebook-alt",
                      'name' => "Set up Social Media"
                    ),
               array( 'url' => admin_url( "options-general.php?page=googlelanguagetranslator-menu-options" ),
                      'icon' => 'welcome-icon dashicons-translation',
                      'name' => 'Add Google Translate',
                    ),
                array( 'url' => menu_page_url( "site_settings_propsearch", false ),
                      'icon' => "welcome-icon dashicons-admin-generic",
                      'name' => "Property Search Settings"
                    ),
				array( 'url' => admin_url( "themes.php?page=theme_options#tabs-3" ),
                      'icon' => "welcome-icon dashicons-art",
                      'name' => "Add Custom CSS"
                    ),
				array( 'url' => menu_page_url( "site_settings_advanced", false ),
                      'icon' => "welcome-icon dashicons-welcome-write-blog",
                      'name' => "Add Custom Scripts"
                    ),
				array( 'url' => admin_url( "themes.php?page=theme_options#tabs-2" ),
                      'icon' => "welcome-icon dashicons-format-image",
                      'name' => "Change Logo Size or Add a Favicon"
                    ),
               array( 'url' => menu_page_url( "site_settings_golive", false ),
	                  'icon' => "welcome-icon dashicons-admin-site",
	                  'name' => "Take Me Live"
	                )
             );
	// Display the container
	echo '<div class="welcome-panel rss-widget custom">';
echo '<ul>';
   for($i = 0; $i < count($items) ; $i++ ){
				echo '<li>';
				echo '<a href="' . $items[$i]['url'] . '" class="' . $items[$i]['icon'] . '">';
				echo $items[$i]['name'];
				echo '</a>';
				echo '</li>';
	}
	echo '</ul></div>';
?>
<?php
}
function register_tips_box() {
/* Tips Metabox */
	$items = array( array( url => "https://codex.wordpress.org/WordPress_Widgets",
                      icon => "welcome-icon dashicons-editor-help",
                      name => "What are widgets"
                    ),
               array( url => "//supportdocs.imbookingsecure.com/managing_seo_keywords",
                      icon => 'welcome-icon dashicons-analytics',
                      name => 'How to Manage SEO',
                    ),
			   array( url => "//supportdocs.imbookingsecure.com/featured_properties_widget",
                      icon => "welcome-icon dashicons-admin-post",
                      name => "Change your Featured Properties settings"
                    ),
			   array( url => 'http://help.kigo.net/servlet/fileField?id=0BEi00000004IqP',
                      icon => "welcome-icon dashicons-sos",
                      name => "Wordpress + Kigo App FAQ" 
                    ),
			   array( url => 'http://help.kigo.net/servlet/fileField?id=0BEi00000004IFY',
                      icon => "welcome-icon dashicons-sos",
                      name => "Website guide "
                    ),
               array( url => ( '//supportdocs.imbookingsecure.com'),
                      icon => "welcome-icon welcome-view-site",
                      name => "Visit Support"
                    )
             );
	// Display the container
	echo '<div class="welcome-panel rss-widget custom">';
echo '<ul>';
   echo '<li><div class="welcome-icon dashicons-welcome-learn-more">How to create a <a href="http://codex.wordpress.org/Writing_Posts" target="_blank">Blog</a> or <a href="http://codex.wordpress.org/Pages" target="_blank">Page</a></div></li>';
   for($i = 0; $i < count($items) ; $i++ ){
				echo '<li>';
				echo '<a href="'.$items[$i]['url'].'" class="'.$items[$i]['icon'].'" target="_blank">';
				echo $items[$i]['name'];
				echo '</a>';
				echo '</li>';
	}
	echo '</ul></div>';
}
?>
<?php
//add meta box to  wp backend
function myplugin_add_meta_box() {
	foreach ( array( 'post', 'page' ) as $screen ) {
		add_meta_box(
			'myplugin_sectionid',
			__( 'SEO Attributes &nbsp;&nbsp;&nbsp;<a href="'.menu_page_url( 'site_settings_advanced', false ).'">Google Adwords Code</a>', 'myplugin_textdomain' ),
			'myplugin_meta_box_callback',
			$screen
		);
	}
}
//adds the information inside the mata seo meta box
add_action( 'add_meta_boxes', 'myplugin_add_meta_box' );
function myplugin_meta_box_callback( $metaId ) {
	wp_nonce_field( 'insta_seo_metabox ', 'myplugin_meta_box_nonce' );
	 $pageId = get_the_ID();
	 $url = get_permalink($pageId);
	 $newUrlray = split('http://localhost', $url);
	 $relpermalink = $newUrlray[1];
	 $meta_words = get_post_custom($pageId, '', true);
	 $post = get_post($pageId);
	 if(empty($meta_words['bapi_meta_title'][0])){
		$meta_words['bapi_meta_title'][0] = $post->post_title;
	 }
	 $keyword_meta = get_post_meta($pid,'bapi_meta_keywords',true);
	?>
	<!--  creats the live snippet preview box -->
	<script>
		jQuery(document).ready(function($) {
   			$("#Descript_prev").text("<?php echo addslashes($meta_words['bapi_meta_description'][0]);?>");
   			$("#seoTitle").text("<?php echo addslashes($meta_words['bapi_meta_title'][0]); ?>");
		$("#bapi_meta_description").keyup(function(){
			var prevDesc = $("#bapi_meta_description").val();
			var desc_length = prevDesc.length;
			var totLeft = 156 - desc_length;
			$("#Descript_prev").text(prevDesc).css({"width":"100%"});

			if(prevDesc == ""){
				$("#Descript_prev").text("New Description");
			}
			var charColor = $("#descrip_lenght").text(totLeft).css({"color":"green"});
			if(totLeft <= 0){
				$("#descrip_lenght").text(totLeft).css("color", "red");
			}
		});
		$("#bapi_meta_title").keyup(function(){
			var prevTitle = $("#bapi_meta_title").val();
			var title_length = prevTitle.length;
			var charleft = 70 - title_length;
			$("#seoTitle").text(prevTitle);
			var color = $("#Title_lenght").text(charleft).css("color", "green");
			if(charleft <= 0){
				$("#Title_lenght").text(charleft).css("color", "red");
			}
			if(prevTitle == ""){
				$("#seoTitle").text("New SEO Title");
			}
		});
	});
	</script>
	<!-- meta box fields -->
	<table style="max-width: 95%;">
	<tr>
	<td class="left" style="width:30%;">Snippet Preview: </td>
	<td><u><span style="color:#0000CF;" id="seoTitle"></span></u></td>
	</tr>
	<tr>
		<td></td>
		<td style="color: #006621;"><?php  echo $cdn_url = get_option('bapi_site_cdn_domain').$relpermalink;?></td>
	</tr>
	<tr>
		<td></td>
		<td style="padding-bottom: 40px;max-width: 300px;color: #808080;" ><div style="word-wrap: break-word;width:100%;" id="Descript_prev"></div></td>
	</tr>
	<tr >
		<td><label for="bapi_meta_keywords">Keywords:</label></td>
		<td><input  style="width:100%;" id="bapi_meta_keywords" class="input" type="text" name="bapi_meta_keywords" value="<?php echo esc_attr($meta_words['bapi_meta_keywords'][0]);?>"></td>
	</tr>
	<tr >
		<td><label for="bapi_meta_title">SEO Title:</label></td>
		<td><input style="width:100%;"id="bapi_meta_title" class="input" type="text" name="bapi_meta_title" value="<?php echo esc_attr($meta_words['bapi_meta_title'][0]); ?>" >
			<br />Title display in search engines is limited to 70 chars, <span id="Title_lenght"></span> chars left.
		</td>
	</tr>
	<tr>
		<td><label for="bapi_meta_description">Meta Description: </label></td>
		<td><textarea style="width:100%;" name="bapi_meta_description" id="bapi_meta_description" rows="5" cols="30" value="testing"><?php echo $meta_words['bapi_meta_description'][0];?></textarea>
			<br > The meta description will be limited  to 156 chars. <span id="descrip_lenght"></span> chars left.
		</td>
	</tr>
	</table>
<?php
}
//this function is triggered when save or update
 function save_seo_meta( $postid ) { 
	$bapisync = new BAPISync();
	$bapisync->init();
	$perma = get_permalink();
	$permaPath = parse_url($perma);
	$relativePerma = get_relative($perma);
	$pageID = get_post_meta(get_the_ID(),'bapi_page_id');
	if($relativePerma=='/' && $pageID[0]!='bapi_home'){
		return;
	}
	$seo = $bapisync->getSEOFromUrl($relativePerma);
	$meta_words = get_post_custom($post->ID, '', true);
	$myPageId = $seo['ID'];
	$myType = $seo['entity'];
	$myPkId = $seo['pkid']; 
	$post_to_api = (!$myType || !$myPkId) ? false : true;
	if($myType === null){$myType = 0;}
	if($myPageId === null){$myPageId = 0;}
	if($myPkId === null){$myPkId = 0;}
	$apiKey = getbapiapikey();
 	$bapi = getBAPIObj();
	if (!$bapi->isvalid()) { return; }
	$keywor = sanitize_text_field( $_POST[ 'bapi_meta_keywords' ]);
	$metle = sanitize_text_field( $_POST[ 'bapi_meta_title' ]);
	$meta_desc = sanitize_text_field( $_POST[ 'bapi_meta_description' ]);
	// save old value if keyword empty or null
	If($metle === null || empty($metle)){
		$metle = $meta_words['bapi_meta_title'][0];
	}
	If($meta_desc === null || empty($meta_desc)){
		$meta_desc = $meta_words['bapi_meta_description'][0];
	}
	if($keywor === null || empty($keywor)){
		$keywor = $meta_words['bapi_meta_keywords'][0];
	}
	//saves to wordpress database
	if(isset($_POST['bapi_meta_keywords'])){
		if($_POST['bapi_meta_keywords'] !== $meta_words['bapi_meta_keywords'][0]){
		}
		update_post_meta( $postid, 'bapi_meta_keywords', sanitize_text_field( $_POST[ 'bapi_meta_keywords' ]) );
	}
	if(isset($_POST['bapi_meta_title']) && $_POST['bapi_meta_title'] !== $meta_words['bapi_meta_title'][0]){
		update_post_meta( $postid, 'bapi_meta_title', sanitize_text_field( $_POST[ 'bapi_meta_title' ]) );
	}
	if(isset($_POST['bapi_meta_description']) && $_POST['bapi_meta_description'] !== $meta_words['bapi_meta_description'][0]){
		update_post_meta( $postid, 'bapi_meta_description', sanitize_text_field( $_POST[ 'bapi_meta_description' ]) );
	}

	if(!$post_to_api) { return; }
	
	// entety: tyoe and language  needs to be 
	//print_r($jsonObj);exit();
	$keywordTypes = array(
		'property',
		'development',
		'specials',
		'special',
		'fee',
		'tax',
		'marketarea',
		'poi'
	);
	
	if( isset($keywor) && isset($metle) && isset($meta_desc) && isset($myPageId) && $myPkId > 0 && isset($relativePerma) && in_array($myType, $keywordTypes) ) {
		$metaArr = array(
			'MetaKeywords'	=> $keywor,
			'PageTitle'		=> $metle,
			'MetaDescrip'	=> $meta_desc, 
			'ID'			=> $myPageId,
			'pkid'			=> $myPkId, 
			'Keyword'		=> $relativePerma, 
			'entity'		=> $myType
		);
		$jsify = json_encode($metaArr);
		$jsonObj = 'data='.(string)$jsify;

		$bapi->save($jsonObj,$apiKey);
		update_option( 'bapi_keywords_lastmod', 0 );
	}

	bapi_sync_coredata();
	
}

/* Since quick_edit_custom_box action hook is fired for **custom columns** only, we simply eliminate QuickEdit link here.
 * http://phpxref.ftwr.co.uk/wordpress/nav.html?wp-admin/includes/list-table-posts.php.source.html#l972
 */
function kigo_disable_quick_edit( $actions ) {
	if( !is_super_admin() ) {
		unset( $actions['inline hide-if-no-js'] );
	}
	return $actions;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	add_action( 'wp_insert_post',  'save_seo_meta');
}

// To hide update notifications to non-admin users
function hide_update_notice_to_non_admin_users()
{
   if(!is_super_admin() )
    {
       remove_action( 'admin_notices', 'update_nag', 3 );
	   remove_action( 'network_admin_notices', 'update_nag', 3 );
    }
}

// Site Logo upload 
function site_logo_scripts() {
wp_register_script('logo-upload', plugin_dir_url(__FILE__).'/js/site-logo.js', array('jquery','media-upload','thickbox'));
wp_enqueue_script('logo-upload');
}
 
function site_logo_styles() {
wp_enqueue_style('thickbox');
}
 
if (isset($_GET['page']) && $_GET['page'] == 'site_settings_initial') {
add_action('admin_print_scripts', 'site_logo_scripts');
add_action('admin_print_styles', 'site_logo_styles');
}

// To Turn off  auto-scrolling
add_action( 'admin_init', 'turn_off_autoscroll' );
function turn_off_autoscroll() {
	wp_deregister_script('editor-expand');
}

add_filter( 'tiny_mce_before_init', 'unset_autoresize_on_autoscroll' );
function unset_autoresize_on_autoscroll( $init ) {
	unset( $init['wp_autoresize_on'] );
	return $init;
}


//load specific JS file for the featured properties widget
function load_featured_properties_js($hook){
	if('widgets.php' != $hook){
		return;
	}
	wp_enqueue_script('featured-properties-js', get_relative(plugins_url('/js/featured.properties.js', __FILE__)) );
}
//load specific JS file for the featured properties widget
function load_bootstrap_3_css($hook){
	if('widgets.php' != $hook){
		return;
	}
	wp_enqueue_style('bootstrap-dd-css', get_relative(plugins_url('/css/bootstrap-dd.css', __FILE__))  );
}

//Bypass SSL Verification?
if(defined('BYPSASS_SSL_VERIFY') && constant('BYPSASS_SSL_VERIFY') == true) { 
	add_filter( 'https_ssl_verify', '__return_false' );
	add_filter( 'https_local_ssl_verify', '__return_false' );
}


//Add property custom post type 
function create_property_posttype() {
	register_post_type( 'property',
		array(
			'labels' => array(
				'name' => __( 'Properties' ),
				'singular_name' => __( 'Property' )
			),
			'public' => true,
			'has_archive' => true,
			'rewrite' => array('slug' => 'properties'),
		)
	);
}
add_action( 'init', 'create_property_posttype' );


//Start session
function start_session() {
	if (session_id() == "") { session_start(); }
}
add_action('init', 'start_session');


//Bypass SSL Verification?
if(defined('BYPSASS_SSL_VERIFY') && constant('BYPSASS_SSL_VERIFY') == true) { 
	add_filter( 'https_ssl_verify', '__return_false' );
	add_filter( 'https_local_ssl_verify', '__return_false' );
}


function add_kigo_vars() { 
	global $bapi_all_options;
	?>
	<script>
		var kigo_plugin_url = "<?php echo plugin_dir_url( __FILE__ ); ?>";
		var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
		var apikey = "<?php echo $bapi_all_options['api_key']; ?>";
		var bapiurl = "<?php echo $bapi_all_options['bapi_baseurl']; ?>";
		var translations = <?php echo json_encode(getbapitextdata()); ?>;
		var currency = "<?php echo getbapicontext()['DefaultCurrency']; ?>";
	</script>

<?php 
	$_SESSION['paypal_sandbox'] = defined('PAYPAL_SANDBOX') && constant('PAYPAL_SANDBOX') ? true : false;
}
add_action('wp_head', 'add_kigo_vars');


function set_session_callback() {
	if (session_id() == "") 
		session_start();

	if(isset($_POST['session'])) {
		foreach($_POST['session'] as $key => $value) {
			$_SESSION[$key] = $value;
		}
	}
}
add_action( 'wp_ajax_set_session', 'set_session_callback' );
add_action( 'wp_ajax_nopriv_set_session', 'set_session_callback' );


//Load PayPal scripts on makepayment
function makepayment_scripts() { 
	$context = array(
		'makebooking',
		'makepayment'
	);

	if(defined('PAYPAL_IN_CONTEXT') && constant('PAYPAL_IN_CONTEXT') && is_page($context)) {
?>

	<script>
	  window.paypalCheckoutReady = function () {
	    paypal.checkout.setup('Your-Merchant-ID', {
	        environment: "<?php echo defined('PAYPAL_SANDBOX') && constant('PAYPAL_SANDBOX') ? 'sandbox' : 'production'; ?>",
	        container: 'paypalForm'
	      });
	  };
	</script>

	<script src="//www.paypalobjects.com/api/checkout.js" async></script>

<?php
	} 
}
//add_action('wp_footer', 'makepayment_scripts');

//Add custom confirmation page template
function custom_page_template( $page_template )
{
	$templates = array(
		'paypal-confirmation'	=> '/page-paypal-confirmation.php',
		'paypal-cancelled'		=> '/page-paypal-cancelled.php'
	);
    
    foreach($templates as $key => $template) {
    	if ( is_page( $key ) ) {
        	$page_template = dirname( __FILE__ ) . $template;
    	}
    }

    return $page_template;
}
add_filter( 'page_template', 'custom_page_template' );


//Add pages if they don't exist
function my_page_template_redirect()
{

	$slug = $GLOBALS['wp_the_query']->query_vars['name'];


	$pages = array(
		'paypal-confirmation' => array(
			'post_title'    => 'PayPal Confirmation',
	        'post_content'  => '',
	        'post_status'   => 'publish',
	        'post_author'   => 1,
	        'post_type'     => 'page',
	        'post_name'     => 'PayPal Confirmation'
		),
		'paypal-cancelled' => array(
			'post_title'    => 'PayPal Cancelled',
	        'post_content'  => '',
	        'post_status'   => 'publish',
	        'post_author'   => 1,
	        'post_type'     => 'page',
	        'post_name'     => 'PayPal Cancelled'
		)
	);



	if($pages[$slug] && !is_page($slug)) {
		//echo "page does not exist<br />";
		$post = wp_insert_post( $pages[$slug] );
		//echo "page created: ".$post."<br />";
		//echo "url: ".get_permalink($post);
		wp_redirect( get_permalink($post) );
		exit();
	}

}
add_action( 'template_redirect', 'my_page_template_redirect' );


//Register PayPal file(s)
function register_paypal() {
	$url = get_relative($_SERVER['REQUEST_URI']);

	
	if( !strpos($url, 'paypal/expresscheckout') ) { 
		return;
	} else { 
		include( plugin_dir_path(__FILE__).'includes/paypal/expresscheckout.php' );
	}

}
//add_action( 'init', 'register_paypal' );


/*
** SEO Page titles
*/
function seo_titles($data){
	if( $title = get_post_meta( get_the_ID() )['bapi_meta_title'][0] ) {
		$data = $title;
	}

	return $data;
}
add_filter('wp_title','seo_titles');


/*
** Custom Scripts in Footer
*/
	function custom_scripts_markup($object)
	{
	    wp_nonce_field(basename(__FILE__), "meta-box-nonce");

	    ?>
	        <div>

	            <label for="footer-scripts">
	            	<?php _e( sprintf('This will be displayed verbatim inside of %s just before the %s tag.', '<code>&lt;script&gt;&lt;/script&gt;</code>', '<code>&lt;/body&gt;</code>') ); ?><br />
	            	<?php _e( sprintf('%sWarning:%s Errors in this code may cause issues rendering this page.', '<b style="color:#c00">', '</b>') ); ?></label>
	            <br />
	            <textarea name="footer-scripts" rows="4" cols="25" style="width:100%"><?php echo get_post_meta($object->ID, "footer-scripts", true); ?></textarea>
	        </div>
	    <?php  
	}


	function save_custom_scripts_meta_box($post_id, $post, $update)
	{
	    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
	        return $post_id;

	    if(!current_user_can("edit_post", $post_id))
	        return $post_id;

	    if(defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
	        return $post_id;

	    $slug = "page";
	    if($slug != $post->post_type)
	        return $post_id;

	    $meta_box_text_value = "";

	    if(isset($_POST["footer-scripts"]))
	    {
	        $meta_box_text_value = $_POST["footer-scripts"];
	    }   
	    update_post_meta($post_id, "footer-scripts", $meta_box_text_value);
	}

	add_action("save_post", "save_custom_scripts_meta_box", 10, 3);


	function add_custom_scripts_meta_box()
	{
	    add_meta_box("custom-scripts-meta-box", "Custom JavaScript", "custom_scripts_markup", "page", "advanced", "high", null);
	}
	add_action("do_meta_boxes", "add_custom_scripts_meta_box");


	function add_custom_scripts() {
	    echo '<script>'.get_post_meta( get_the_ID(), 'footer-scripts', true ).'</script>';
	}
	add_action( 'wp_footer', 'add_custom_scripts' );




add_action('wp_head','get_ajaxurl');
function get_ajaxurl() {
?>
<script type="text/javascript">
	var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	var locale = navigator.language || navigator.userLanguage;
</script>
<?php
}

//Get Translations to use in JS
add_action( 'wp_ajax_get_translations', 'kigo_get_translations' );
add_action( 'wp_ajax_nopriv_get_translations', 'kigo_get_translations' );
function kigo_get_translations() {
    echo json_encode( kigo_I18n::get_translations() );
    exit;
}


//Get Post data
add_action( 'wp_ajax_get_post_data', 'kigo_get_post_data' );
add_action( 'wp_ajax_nopriv_get_post_data', 'kigo_get_post_data' );
function kigo_get_post_data() {
	$id = isset($_POST['id']) ? $_POST['id'] : false;
	if(!$id) return;

	$key = isset($_POST['key']) ? $_POST['key'] : false;
	$single = isset($_POST['single']) ? $_POST['single'] : false;

    $meta = get_post_meta($id, $key, $single);

    echo $meta;

    exit;
}


//Enqueue map stuff
add_action( 'wp_enqueue_scripts', 'register_map_scripts' );
function register_map_scripts() {
	wp_register_script( 'google-maps', '//maps.googleapis.com/maps/api/js?v=3.5&sensor=false&key=AIzaSyAY7wxlnkMG6czYy9K-wM4OWXs0YFpFzEE' );
	wp_register_script( 'markermanager', plugins_url( '/js/jmapping/vendor/markermanager.js' , __FILE__ ), array() );
	wp_register_script( 'styled-markers', plugins_url( '/js/jmapping/vendor/StyledMarker.js' , __FILE__ ), array() );
	wp_register_script( 'metadata', plugins_url( '/js/jmapping/vendor/jquery.metadata.js' , __FILE__ ), array('jquery-min') );
	wp_register_script( 'jmapping', plugins_url( '/js/jmapping/jquery.jmapping.min.js' , __FILE__ ), array('jquery-min') );
}

function enqueue_map_scripts() {
	wp_enqueue_script('google-maps');
	wp_enqueue_script('markermanager');
	wp_enqueue_script('style-markers');
	wp_enqueue_script('metadata');
	wp_enqueue_script('jmapping');
}


function port_site_settings() {
	if( !get_option('bapi_settings_ported') && $settings = get_option('bapi_sitesettings') ) {
		$settings = json_decode( $settings, true );

		//test($settings);
		
		$raw = []; 
		$missing = [];
		foreach($settings as $key => $value) {
			if( strpos($value, '=true') && substr_count($value, "=") == 1 ) {
				$raw[$key] = true;
			} else if( strpos($value, '=false') && substr_count($value, "=") == 1 ) {
				$raw[$key] = false;
			} else {
				$raw[$key] = $value;
			}

		}

		//test($raw,1);


		if($maxbeds = $settings['maxbedsearch']) {
			$raw['maxbedsearch'] = $maxbeds;
		}


		if($map = $settings['mapviewType']) {
			$map = explode('=', $map);
			$raw['mapviewType'] = preg_replace("/[^a-zA-Z0-9]+/", "", $map[1]);
		}


		if($results = $settings['defaultsearchresultview']) {
			$results = explode('=', $results); 
			$raw['defaultsearchresultview'] = preg_replace("/[^a-zA-Z0-9-]+/", "", $results[1]);
		}


		if($sort = $settings['searchsort']) {
			$sort = explode('=', $sort);
			$raw['searchsort'] = preg_replace("/[^a-zA-Z0-9]+/", "", $sort[1]);
		}


		if($deflos = $settings['deflos']) {

			$deflos = explode('=', $deflos);
			if(count($deflos) > 1) {
				$los = array(
					'defaultval' 	=> $deflos[1],
					'minval'		=> $deflos[2],
					'value'			=> intval($deflos[2])
				); 
				$raw['deflos'] = $los['value'];
			} else {
				$raw['deflos'] = $deflos[0];
			}
			
		}


		if($numberproppage = $settings['numberproppage']) {
			$numberproppage = explode('=', $numberproppage);
			$raw['numberproppage'] = preg_replace("/[^a-zA-Z0-9]+/", "", $numberproppage[1]);
		}


		if($checkinoutmode = $settings['checkinoutmode']) {
			switch($checkinoutmode) {
				case 'BAPI.config().checkin.enabled=false; BAPI.config().checkout.enabled=false; BAPI.config().los.enabled=false;': $s = 0; break;
				case 'BAPI.config().checkin.enabled=true; BAPI.config().checkout.enabled=true; BAPI.config().los.enabled=false;': $s = 1; break;
				case 'BAPI.config().checkin.enabled=true; BAPI.config().checkout.enabled=false; BAPI.config().los.enabled=true;': $s = 2;
			}
			$raw['checkinoutmode'] = $s;
		}


		if($minsleeps = $settings['minsleepsearch']) {
			$minsleeps = array_filter(explode(';', $minsleeps));

			$raw['minsleepsearch'] = strpos($minsleeps[1], 'BAPI.config().minsleeps.enabled=true') !== false ? true : false;
		}


		if($minbeds = $settings['minbedsearch']) {
			$minbeds = array_filter(explode(';', $minbeds));

			$raw['minbedsearch'] = strpos($minbeds[1], 'BAPI.config().minbeds.enabled=true') !== false ? true : false;
		}


		$raw['locsearch'] = [];
		if($locsearch = $settings['locsearch']) {
			$raw['locsearch']['city'] = strpos($locsearch, 'BAPI.config().city.enabled=true;') !== false ? true : false;
			$raw['locsearch']['location'] = strpos($locsearch, 'BAPI.config().location.enabled=true;') !== false ? true : false;
			$raw['locsearch']['autocomplete'] = strpos($locsearch, 'BAPI.config().city.autocomplete=true;') !== false ? true : false;
		}


		if($availcal = $settings['propdetail-availcal']) {
			//$raw['propdetail-availcal']['displayavailcalendar'] = strpos($availcal, 'BAPI.config().displayavailcalendar=true;') >= 0 ? true : false;
			$raw['propdetail-availcal'] = intval(explode('=', $availcal)[2]);
		}


		if($poi = $settings['poitypefilter']) {
			$poi = array_filter(explode(';', $poi));

			$raw['poitypefilter'] = strpos($poi[1], 'BAPI.config().haspoitypefilter.enabled=true') !== false ? true : false;
		}


		update_option('bapi_sitesettings_raw', $raw);
		update_option('bapi_settings_ported', true);
	}
}
add_action( 'muplugins_loaded', 'port_site_settings' );


//Delete the insurance page
function delete_insurance() {
	if(!get_option('kigo_travel_insurance_deleted')) {
		$args = array(
		    'meta_query' => array(
		        array(
		            'key' => 'bapi_page_id',
		            'value' => 'bapi_travel_insurance'
		        )
		    ),
		    'post_type' => 'page',
		    'posts_per_page' => -1
		);
		$posts = get_posts($args);

		foreach($posts as $post) {
			wp_delete_post($post->ID, true); //Force delete
		}

		//Set site option
		add_option('kigo_travel_insurance_deleted', true);
	}
}
add_action('init', 'delete_insurance');


/* Remove link to XMLRPC */
add_filter( 'bloginfo_url', function($output, $show) {
	if ( $show == 'pingback_url' ) $output = '';
    return $output;
}, 10, 2 );
remove_action ('wp_head', 'rsd_link');



//Add error logging
if (!function_exists('write_log')) {
    function write_log ( $log )  {
        if ( true === KIGO_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}


//Add easy testing function
if (!function_exists('test')) {
    function test ( $log, $die=false )  {
        if ( true === KIGO_DEBUG ) {
        	echo "<pre>";
            if ( is_array( $log ) || is_object( $log ) ) {
                print_r( $log );
            } else {
                echo $log;
            }
            echo "</pre>";

            if($die) die();
        }
    }
}


//Display WP error message
function display_wp_error($error, $title=false) {
	if ( is_wp_error( $error ) ) {
	   $error_string = $error->get_error_message();
	   echo '<span id="message" style="border-left: 4px solid #dc3232; padding: 1px 12px; background: #fff;">'. (!$title ?: '<b>'.$title.': </b> ') . $error_string . '</span>';
	}
}


//Redirect to property by id
function property_by_uid() { 

	$path = explode( '/', $_SERVER["REQUEST_URI"] );

	if($path[1] == 'property' && $path[2] > 0) {
		$id = $path[2];

		$args = array(
			'post_type'  => 'page',
			'meta_key'   => 'bapikey',
			'orderby'    => 'date',
			'order'      => 'DESC',
			'meta_query' => array(
				array(
					'key'     => 'bapikey',
					'value'   => 'property:'.$id,
					'compare' => '=',
				),
			),
		);
		$results = new WP_Query( $args );

		if($results->posts[0]) {
			wp_redirect( get_permalink( $results->posts[0]->ID ), 301); exit;
		}
	}
}
add_action('init', 'property_by_uid');


//Facebook Sharing
function property_fb_opengraph() {
    global $post;
    if( basename(get_page_template()) == 'property-detail.php') { 
    	$data = json_decode( get_post_meta($post->ID, 'bapi_property_data', true) );
    	$data = !empty($data) ? $data : false; 
    ?>

<meta property="og:site_name" content="<?php echo get_bloginfo(); ?>"/> 
<meta property="og:url" content="<?php echo the_permalink(); ?>"/>
<?php if($title = $data->ContextData->SEO->PageTitle) { ?>
<meta property="og:title" content="<?php echo $title; ?>"/>
<?php } ?>
<?php if($desc = $data->ContextData->SEO->MetaDescrip) { ?>
<meta property="og:description" content="<?php echo $desc; ?>"/>
<?php } ?>
<?php 
	if(!empty($data->Images)) {
		foreach($data->Images as $image) { 
	    	$url = $image->MediumURL;
	    	if  ( $ret = parse_url($url) ) { 
				if ( !isset($ret["scheme"]) ) {
			    	$url = "http://".$ret['host'].$ret['path'];
			    }
			}
	?>
<meta property="og:image" content="<?php echo $url; ?>" />
<?php 

		}
	}
   	?>
 
<?php
	}
}
add_action('wp_head', 'property_fb_opengraph', 5);

//Sitemap crawler
function bapi_crawl_sitemap_pages() {
	if (extension_loaded('newrelic')) {
		newrelic_ignore_transaction();
	}
	global $bapi_all_options;
	$url = get_relative($_SERVER['REQUEST_URI']);
	//echo $url; exit();
	if (strtolower($url) != "/sitemap_crawler.svc")
		return;
	if (strtolower($url) == "/sitemap_crawler.svc"){
	
		$urls = array();  
		$sitemap = get_site_url().'/sitemap.xml';
		$xml= wp_remote_get($sitemap,array('timeout'=>30));
		$xml= $xml['body'];
		$DomDocument = new DOMDocument();
		$DomDocument->preserveWhiteSpace = false;
		$DomDocument->loadXML("$xml"); // $DOMDocument->load('filename.xml');
		$DomNodeList = $DomDocument->getElementsByTagName('loc');
		
		$bapi_solutiondata = json_decode(wp_unslash($bapi_all_options['bapi_solutiondata']),true);
		$secureurl = 'http://'.$bapi_solutiondata['SecureURL'];
		if(!empty($bapi_all_options['bapi_secureurl'])){
			$secureurl = 'http://'.$bapi_all_options['bapi_secureurl'];
		}

		foreach($DomNodeList as $url) {
			$parsed = parse_url($url->nodeValue);
			$url->nodeValue = $secureurl.$parsed['path']; 
			if(wp_remote_get( $url->nodeValue )){
				echo $url->nodeValue.' (OK)<br>';
				flush();
			}else{
				echo $url->nodeValue.' (Fail)<br>';
				flush();
			}
		}
		exit();
	}
}

function cookie_helper() {
	$url = basename( $_SERVER['REQUEST_URI'] );		
	$url = strtolower($url);

	if('cookie.jpg' != $url) return;

	?>
	<script>
		if(Cookies) {
			var searchdata = Cookies.get('searchdata');

			if(searchdata) {
				Cookies.set('searchdata', searchdata);
			}
		}
	</script>
	<?php
	exit();
}
//add_action('init', 'cookie_helper');


function add_cookie_images() {
	global $bapi_all_options;

	$bapi_solutiondata = json_decode(wp_unslash($bapi_all_options['bapi_solutiondata']),true);
	
	$secureurl = $bapi_solutiondata['SecureURL'] ? 'http://'.$bapi_solutiondata['SecureURL'] : '';
	if(!empty($bapi_all_options['bapi_secureurl'])){
		$secureurl = 'http://'.$bapi_all_options['bapi_secureurl'];
	}
	echo '<img src="'.$secureurl.'/cookies.jpg" style="display:none;" />';
}
//add_action('wp_print_footer_scripts', 'add_cookie_images');


//Loading image
add_filter('get_image_from_kigo_plugin', 'kigo_get_image');
function kigo_get_image($img) { 
	if($img) { 
		$path = plugins_url($img, __FILE__);
		if($path) { return $path; }
	}
	
}
