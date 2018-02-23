<?php

	require_once('bapi-php/bapi.php');
	require_once('functions.php');

	$bapisync = null;
	class BAPISync {
		const SETTINGS_UPDATE_TIME_OPTION_NAME = 'bapi_wp_settings_update_time';	
		public $soldata = null;
		public $seodata = null;
		public $templates = null;
		
		public function init() {
			$this->soldata = BAPISync::getSolutionData();
			$this->seodata = BAPISync::getSEOData();	
		}
		public function get_templates() {
			if (empty($this->templates)) {
				$this->templates = BAPISync::getTemplates();
			}
			return $this->templates;
		}
		
		public static function getSolutionDataRaw() { 
			global $bapi_all_options; 
			if( empty($bapi_all_options) ) {
				$bapi_all_options = bapi_wp_site_options(); 
			} 
			//test( wp_unslash($bapi_all_options), 1);
			return wp_unslash($bapi_all_options['bapi_solutiondata']); 
		}
		public static function getSolutionDataLastModRaw() { global $bapi_all_options; return (int)$bapi_all_options['bapi_solutiondata_lastmod']; }
		public static function getSolutionData() { return json_decode(BAPISync::getSolutionDataRaw(), TRUE); }
		
		public static function getSEODataRaw() { global $bapi_all_options; return $bapi_all_options['bapi_keywords_array']; }
		public static function getSEODataLastModRaw() { global $bapi_all_options; return (int)$bapi_all_options['bapi_keywords_lastmod']; }
		public static function getSEOData() { return json_decode(BAPISync::getSEODataRaw(), TRUE); }
		
		public static function isMustacheOverriden() { 
			$basefilename = "bapi/bapi.ui.mustache.tmpl";
			// see if there is a custom theme in the theme's folder
			$test = get_stylesheet_directory() . '/' . $basefilename;
			if (file_exists($test)) {
				return true;
			}
			return false;			
		}
		
		public static function getTemplates() {
			$basefilename = "bapi/bapi.ui.mustache.tmpl";

			// Retrieve the custom templates file in the theme's folder
			if(
				!file_exists( $overwritten_tmpl_path = get_stylesheet_directory() . '/' . $basefilename ) ||
				!is_string( $overwritten_tmpl = file_get_contents( $overwritten_tmpl_path ) ) ||
				!preg_match_all( '/<script\s+id=["\']([^\s\'"]*)["\']\s+[^>]*?>.*?<\/script>/s', $overwritten_tmpl, $overwritten_tmpl_parts )
			) {
				$overwritten_tmpl_parts = array();
			}
			
			// Retrieve the default templates file in the plugin folder
			if(
				!file_exists( $default_tmpl_path = realpath( plugin_dir_path( __FILE__ ) . $basefilename ) ) ||
				!is_string( $default_tmpl = file_get_contents( $default_tmpl_path ) )
			) {
				wp_die( "Couldn't retrieve the mustache template.\nPlease contact support." );
			}
			if(
				!count( $overwritten_tmpl_parts ) ||
				!preg_match_all( '/<script\s+id=["\']([^\s\'"]*)["\']\s+[^>]*?>.*?<\/script>/s', $default_tmpl, $default_tmpl_parts )
			) {
				return $default_tmpl;
			}
			
			//All the default templates are overwritten in the custom file (legacy default behaviour)
			if( count( $overwritten_tmpl_parts[0] ) === count( $default_tmpl_parts[0] ) ) {
				return $overwritten_tmpl;
			}
			
			// For every template defined in the custom mustache, overwrite the default template that has the same id (<script id="[..]"><script>)
			foreach( $overwritten_tmpl_parts[1] as $position => $id ) {
				$default_tmpl_parts[0][array_search( $id, $default_tmpl_parts[1] )] = $overwritten_tmpl_parts[0][$position];
			}
			
			return implode( '', $default_tmpl_parts[0] ); 

		}
		
		public static function cleanurl($url) {
			if (empty($url)) { return ""; }
			$url = strtolower(trim($url));
			if (strpos($url, "/") != 0) { $url = "/" . $url; }
			if (substr($url, -1) != "/") { $url = $url . "/"; }
			return $url; exit();
		}
		
		public static function clean_post_name($url) {
			$url = basename($url);			
			if (substr($url, -1) == "/") { $url = substr_replace($url ,"",-1); }
			return $url;
		}
		
		public function getSEOFromUrl($url) { 
			if (empty($url)) { return null; }			
			$url = BAPISync::cleanurl($url); 
			if(!empty($this->seodata)){
				foreach ($this->seodata as $seo) {												
					$turl = BAPISync::cleanurl($seo["DetailURL"]);	
					if ($turl == $url) { return $seo; }								
				}
			}
			return null;
		}
		
		public static function getPageKey($entity, $pkid) { return $entity . ':' . $pkid; }
		public static function getPageTemplate($entity) {
			if($entity=='property') { return 'page-templates/property-detail.php'; }
			if($entity=='development') { return 'page-templates/other-detail-page.php'; }
			if($entity=='specials') { return 'page-templates/other-detail-page.php'; }
			if($entity=='poi') { return 'page-templates/other-detail-page.php'; }
			if($entity=='searches') { return 'page-templates/other-detail-page.php'; }
			if($entity=='marketarea') { return 'page-templates/other-detail-page.php'; }
			return 'page-templates/full-width.php';
		}
		
		public static function getRootPath($entity) {
			if($entity=='property') { $t=BAPISync::getSolutionData(); return $t["Site"]["BasePropertyURL"]; }
			if($entity=='development') { $t=BAPISync::getSolutionData(); return $t["Site"]["BaseDevelopmentURL"]; }
			if($entity=='specials') { $t=BAPISync::getSolutionData(); return $t["Site"]["BaseSpecialURL"]; }
			if($entity=='poi') { $t=BAPISync::getSolutionData(); return $t["Site"]["BasePOIURL"]; }
			if($entity=='searches') { $t=BAPISync::getSolutionData(); return $t["Site"]["BasePropertyFinderURL"]; }
			if($entity=='marketarea') { $t=BAPISync::getSolutionData(); $s=BAPISync::getSEOData(); return ''; }
			return '/rentals/';
		}
		
		public function getMustacheTemplateByEntity($entity, $mustache_loader) {
			switch( $entity ) {
				case "property":
					return $mustache_loader->load( "tmpl-properties-detail" );
				
				case "development":
					return $mustache_loader->load( "tmpl-developments-detail" );
				
				case "specials":
					return $mustache_loader->load( "tmpl-specials-detail" );
				
				case "poi":
					return $mustache_loader->load( "tmpl-attractions-detail" );
				
				case "searches":
					return $mustache_loader->load( "tmpl-searches-detail" );
				
				case "marketarea":
					return $mustache_loader->load( "tmpl-marketarea-detail" );
				
				default:
					return '';
			}
		}

		public static function getEntityOptions($entity) {
			// Set the options for get call
			switch( $entity ) {
				case "property":
					$options = array("seo" => 1, "descrip" => 1, "avail2" => true, "rates" => 1, "reviews" => 1,"poi"=>1);
					break;
				
				case "poi":
					$options = array("nearbyprops" => 1,"seo" => 1);
					break;
				
				default:
					$options = null;
					break;
			}

			return $options;
		}

		// string | false (resource not found)
		public static function getMustache($entity, $pkid, $c=false) {
			$bapi = getBAPIObj();
			if (!$bapi->isvalid()) { return false; }
			$pkid = array(intval($pkid));

			$options = BAPISync::getEntityOptions($entity);

			if(!$c) {
				if(!is_array($c = $bapi->get($entity, $pkid, $options))) {
					if($c === true)
						return false;
					else
						wp_die('This page is temporarily unavailable. Please try again later.');
				}
			}

			// when rendering a template, get() must result in at least one element
			if(
				count( $c['result'] ) < 1 ||
				!is_array( $c['result'][0] ) ||
				(
					$entity === 'property' &&
					isset( $c['result'][0]['AvailableOnline'] ) &&
					!$c['result'][0]['AvailableOnline'] // "Do not show on site" is set
				)
			) {
				return false;
			}

			$c["config"] = BAPISync::getSolutionData();
			$c["config"] = $c["config"]["ConfigObj"];
			/* we get the sitesettings */
			global $bapi_all_options;
			$sitesettings = json_decode($bapi_all_options['bapi_sitesettings'],TRUE);
			if (!empty($sitesettings)) {
				/* we get the review value from the sitesettings*/
				$hasreviews = $sitesettings["propdetail-reviewtab"];
				if (!empty($hasreviews)){
					/* we make an array using = and ; as delimiters */
					$hasreviews = preg_split('[=;]', $hasreviews);
					/* we assign the value to var in the config array - reviews*/
					$hasreviews = $hasreviews[1];
					$c["config"]["hasreviews"] = ($hasreviews === 'true');
				}
				/* the same as review but for the availability calendar */
				$displayavailcalendar = $sitesettings["propdetail-availcal"];
				if (!empty($displayavailcalendar)){
					$displayavailcalendar = preg_split('[=;]', $displayavailcalendar);
					$availcalendarmonths = (int) $displayavailcalendar[3];
					$displayavailcalendar = $displayavailcalendar[1];
					$c["config"]["displayavailcalendar"] = ($displayavailcalendar === 'true');
					$c["config"]["availcalendarmonths"] =  $availcalendarmonths;
				}
				/* the same as review but for the rates and availability tab */
				$hiderateavailtab = $sitesettings["propdetailrateavailtab"];
				if (!empty($hiderateavailtab)){
					$hiderateavailtab = preg_split('[=;]', $hiderateavailtab);
					/* we assign the value to var in the config array */
					$hiderateavailtab = $hiderateavailtab[1];
					$c["config"]["hideratesandavailabilitytab"] = ($hiderateavailtab === 'true');
				}
				/* the same as review but for star reviews */
				$hidestarsreviews = $sitesettings["averagestarsreviews"];
				if (!empty($hidestarsreviews)){
					$hidestarsreviews = preg_split('[=;]', $hidestarsreviews);
					/* we assign the value to var in the config array */
					$hidestarsreviews = $hidestarsreviews[1];
					$c["config"]["hidestarsreviews"] = ($hidestarsreviews === 'true');
				}
				/* the same as review but for the rates table */
				$hideratestable = $sitesettings["propdetailratestable"];
				if (!empty($hideratestable)){
					$hideratestable = preg_split('[=;]', $hideratestable);
					/* we assign the value to var in the config array */
					$hideratestable = $hideratestable[1];
					$c["config"]["hideratestable"] = ($hideratestable === 'true');
				}
			}
			
			$c["textdata"] = kigo_I18n::get_translations( kigo_get_site_language() );
			
			$c["plugin_path"] = get_relative( plugins_url( '', __FILE__ ) );

			
			// Load bapisync 
			global $bapisync;
			if( is_a( $bapisync, 'BAPISync' ) ) {
				$bapisync = new BAPISync();
				$bapisync->init();
			}

			$mustache_loader = new Kigo_Mustache_Loader_By_Name( $bapisync->get_templates() );
			$m = new Mustache_Engine( array( 'partials_loader' => $mustache_loader ) );
			return str_replace( array( "\t", "\n", "\r" ), '', $m->render( $bapisync->getMustacheTemplateByEntity( $entity, $mustache_loader ), $c ) );
		}


		/*
		** Amenities
		*/
		public function do_amenity_sync() {
			$raw = get_transient('kigo_amenity_sync');
			if( empty($raw) || KIGO_DEBUG ) {
				$args = array(
					'fields'	=> 'ids',
				    'meta_query' => array(
				        array(
				            'key' => 'bapikey',
				            'value' => 'property:',
				            'compare' => 'LIKE'
				        )
				    ),
				    'post_type' => 'page',
				    'posts_per_page' => -1
				);
				$posts = get_posts($args);


				if( ! empty($posts) ) {

					$raw = [];
					$amenities = [];
					foreach($posts as $post) {
						$data = get_post_meta($post, 'bapi_property_data');
						$data = json_decode( $data[0], ARRAY_A )['Amenities'];

						if(!empty($data)){
							foreach($data as $key => $value) {
	
								foreach($value['Values'] as $v) {
									$amenities[] = $v['Label'];
									$raw[] = array(
										'amenity'	=> $v['Label'],
										'type'		=> $value['Key']
									);
								}
							}
						}

						
					}

					$counts = array_count_values($amenities);

					$raw = array_unique($raw, SORT_REGULAR);

					foreach($counts as $count => $c) {
						foreach($raw as $key => $value) {
							if($value['amenity'] == $count) {
								$raw[$key]['count'] = $c;
							}
						}
					}  

					//update_option('kigo_amenities', $raw);
					set_transient( 'kigo_amenity_sync', $raw, (defined('KIGO_AMENITY_SYNC') ? KIGO_AMENITY_SYNC : HOUR_IN_SECONDS) );

				}
			}

			return $raw;
		}



		/**
		 * @return bool as returned by WP’s `update_option`
		 */
		public static function updateLastSettingsUpdate()  {
			return update_option( self::SETTINGS_UPDATE_TIME_OPTION_NAME, time() );
		}

		/**
		 * @param $time int the unixtime to compare
		 *
		 * @return bool `true` if the stored value should be re-synced, `false` otherwise
		 */
		public static function obsoletedByLastSettingsUpdate( $time ) {
			return $time < get_option( self::SETTINGS_UPDATE_TIME_OPTION_NAME, 0 );
		}
	}
	function bapi_sync_entity() {
		if(!(strpos($_SERVER['REQUEST_URI'],'wp-admin')===false)||!(strpos($_SERVER['REQUEST_URI'],'wp-login')===false)){
			return false;
		}

		//Make sure rentals exists
		$required_common = array(
			'post_type'		=> 'page',
			'post_status'	=> 'publish',
		);
		$required_pages = array(
			array(
				'post_title'	=> 'Rentals',
				'post_name'		=> 'rentals',
				'page_template'	=> 'page-templates/search-page.php',
				'meta_input'	=> array(
					'bapi_page_id'	=> 'bapi_rentals',
				),
			),
			array(
				'post_title'	=> 'All Rentals',
				'post_name'		=> 'allrentals',
				'page_template'	=> 'page-templates/full-width.php',
				'post_content'	=> '[allproperties]',
				'post_parent'	=> 'rentals',
				'meta_input'	=> array(
					'bapi_page_id'	=> 'bapi_property_grid',
				),
			),
		);
		foreach($required_pages as $required) {
			$page = get_page_by_title( $required['post_title'] );
			
			if( $page == null ) {

				$required = array_merge($required, $required_common);
				
				if($required['post_parent']) {
					$parent = get_page_by_path( $required['post_parent'] );
					if($parent) {
						$required['post_parent'] = $parent->ID;
						wp_insert_post($required);
					}
				} else {
					wp_insert_post($required);
				}

			} elseif( strtolower( $page->post_status ) == 'trash' ) {

				$page->post_status = 'publish';
				wp_update_post( $page );

			}
		}


		if(isset($_SERVER['REDIRECT_URL'])) {
            //getting the page by URL
            $post = get_page_by_path($_SERVER['REDIRECT_URL']);
            //if the URL is empty this is the Home Page
        }else{
            $home_id = get_option('page_on_front');
            $post    = get_post($home_id);
		}
		
		global $bapisync;		
		if (empty($bapisync)) { 
			$bapisync = new BAPISync();
			$bapisync->init();
		}
		
		// locate the SEO data stored in Bookt from the requested URL
		if(
			!is_array( $seo = $bapisync->getSEOFromUrl(str_replace("?".$_SERVER['QUERY_STRING'],'',$_SERVER['REQUEST_URI'])) ) ||
			!isset( $seo[ "entity" ] ) ||
			!strlen( $seo[ "entity" ] ) ||
			
			!isset( $seo[ "pkid" ] ) ||
			(	// Entity 'system' have pkid = 0 but are valid SEO
				empty( $seo["pkid"] ) &&
				'system' !== $seo["entity"]
			)
		){
			// ignore seo info if it doesn't point to a valid entity
			$seo = null;
		}

		// if we didnt find the page using the URL maybe we can search by entity:ID
		// we can only do that if we have the entity and ID from the SEO
		if($post == null && $seo != null){
			//we concat the custom field
			$pageBapiKey = $seo['entity'] .':' . $seo['pkid'];
			//we form the search criteria
			$args = array(              	
				'post_type' => 'page',
				'post_status' => 'publish',
				'meta_query' => array(
					array(
						'key' => 'bapikey',
						'value' => $pageBapiKey
					)
				)
            );
			//we search a post with the entity_ID
			$result = get_posts( $args );
			//it will be null if there is no result		
			$post = $result[0];
		}

		return kigo_sync_entity( $post, $seo );
	}


	function kigo_sync_entity( $post, $seo, $force_sync = false ) {
		global $bapisync;		
		if (empty($bapisync)) { 
			$bapisync = new BAPISync();
			$bapisync->init();
		}

		//$bapisync->do_amenity_sync();

		$t=BAPISync::getSolutionData();

		$maEnabled = $t['BizRules']['Has Market Area Landing Pages'];
		
		// parse out the meta attributes for the current post
		$page_exists_in_wp = !empty($post);				
		$meta = $page_exists_in_wp ? get_post_custom($post->ID) : null;
		$last_update = !empty($meta) ? $meta['bapi_last_update'][0] : null;
		$staticpagekey = !empty($meta) ? $meta['bapi_page_id'][0] : null;
		$pagekey = !empty($meta) && isset($meta['bapikey']) ? $meta['bapikey'][0] : null;
		$meta_keywords = !empty($meta) ? $meta['bapi_meta_keywords'][0] : null;
		$meta_description = !empty($meta) ? $meta['bapi_meta_description'][0] : null;
		$meta_title = !empty($meta) ? $meta['bapi_meta_title'][0] : null;
		
		//Only for properties pages: Retrieve the timestamp of first cron call and the latest successful diff call on this website
		if(
			!is_array( $seo ) ||
			!isset( $seo[ 'entity' ] ) ||
			'property' !== $seo[ 'entity' ] ||
			!is_array( $cron_info = Kigo_Site_Cron::get_cron_info_option( 'property' ) )
		) {
			$last_successful_diff = $first_cron_execution = 0;
		}
		else {
			$last_successful_diff = $cron_info[ 'last_update_timestamp' ];
			$first_cron_execution = $cron_info[ 'first_cron_execution' ];
		}
		
		/*we get the property headline*/
		$page_title = ( !is_array( $seo ) || !is_string( $seo["PageTitle"] ) ) ? '' : $seo["PageTitle"];

		$do_page_update = false;
		$do_meta_update = false;
		$do_market_update = false;
		$changes = "";
		
		if(!empty($seo) && ($seo["entity"]=='property' || $seo["entity"]=='marketarea') && $maEnabled){
			$do_market_update = true;
		}
		
		// Static pages (entity = system ?) are not updated, but their meta data are updated
		if($page_exists_in_wp && !empty($staticpagekey)){
			// update the meta tags		
			if(empty($meta['bapi_last_update'])||((time()-$meta['bapi_last_update'][0]) > KIGO_PROPERTY_SYNC)){			
				does_meta_exist("bapi_last_update", $meta) ? update_post_meta($post->ID, 'bapi_last_update', time()) : add_post_meta($post->ID, 'bapi_last_update', time(), true);
				if(!empty($seo)){
					if ($meta['bapi_meta_description'][0] != $seo["MetaDescrip"]) { update_post_meta($post->ID, 'bapi_meta_description', $seo["MetaDescrip"]); }
					if ($meta['bapi_meta_title'][0] != $seo["PageTitle"]) { update_post_meta($post->ID, 'bapi_meta_title', $seo["PageTitle"]); }
					if ($meta['bapi_meta_keywords'][0] != $seo["MetaKeywords"]) { update_post_meta($post->ID, 'bapi_meta_keywords', $seo["MetaKeywords"]); }
					//does_meta_exist("bapi_meta_description", $meta) ? update_post_meta($post->ID, 'bapi_meta_description', $seo["MetaDescrip"]) : add_post_meta($post->ID, 'bapi_meta_description', $seo["MetaDescrip"], true);
					//does_meta_exist("bapi_meta_keywords", $meta) ? update_post_meta($post->ID, 'bapi_meta_keywords', $seo["MetaKeywords"]) : add_post_meta($post->ID, 'bapi_meta_keywords', $seo["MetaKeywords"], true);
				}
			}
			return true;
		}
		
		//catch bad bapikey
		if ($page_exists_in_wp && !empty($pagekey)){
			$pktest = explode(":",$pagekey);
			//print_r($pktest); exit();
			if((strlen($pktest[0])==0)||(strlen($pktest[1])==0)){
				//To Delete Meta or Page, that is the question.
				//wp_delete_post($post->ID,true);  //Going w/ deleting post for now - I think this will work because if page should exist it will ge recreated.
				//delete_post_meta($post->ID,'bapikey');
			}
			//Check for non-initialized market area page (-1) and set correct bapikey
			if(($pktest[1]==-1)&&$pktest[0]=='marketarea'){
				//print_r($post); exit();
				update_post_meta($post->ID, "bapikey", 'marketarea:'.$seo['pkid']);	
			}
		}
		
		// case 1: page exists in wp and is marked for syncing on wp but, it no longer exists in Bookt		
		if ($page_exists_in_wp && empty($seo) && !empty($pagekey)) {
			//echo $post->ID; exit();
			//print_r("case 1");
			// Action: Set current page to "unpublished"
			// $post->post_status = "unpublish";
			wp_delete_post($post->ID,true); //optional 2nd parameter can be added -> if true then page will be deleted immediately instead of going to trash.
		}
		// case 2: pages exists in wp and in Bookt
		else if ($page_exists_in_wp && !empty($seo)) {
			//Move from trashcan to publish if exists and no published
			if($post->post_status=='trash'){ $post->post_status='publish'; $do_page_update = true; }
			
			$do_page_update = (
				//If the meta is missing, or it has not been update since the first cron execution: update
				empty( $meta['bapi_last_update'] ) ||
				$meta['bapi_last_update'][0] <= $first_cron_execution ||
				
				(	 // If the cron didn't run a diff since 15 minutes (or it's not a property) and the page has not been update since 5 minute: update
					( time() - $last_successful_diff ) > 900 &&
					( time() - $meta['bapi_last_update'][0]) > KIGO_PROPERTY_SYNC
				)
			);
			
			// check for difference in meta description
			if ($meta['bapi_meta_description'][0] != stripslashes($seo["MetaDescrip"])) { $changes = $changes."|meta_description"; $do_meta_update = true; }
			if ($meta['bapi_meta_title'][0] != $seo["PageTitle"]) { $changes = $changes."|meta_title"; $do_meta_update = true; }
			// check for difference in meta keywords
			if ($meta['bapi_meta_keywords'][0] != $seo["MetaKeywords"]) { $changes = $changes."|meta_keywords"; $do_meta_update = true; }
			/*check if this is a bapi defined page*/
			if(!empty($pagekey) || $pagekey != null){
				$dom = new DomDocument();
				   libxml_use_internal_errors(true);
				   $dom->loadHTML("<!DOCTYPE html><html><head></head><body>".$post->post_content."</body></html>");
				   libxml_use_internal_errors(false);
				   $xpath = new DOMXpath($dom);
				   $xpathq = "//h2[@class='title']";
				   $elements = $xpath->query($xpathq);
				   if (!is_null($elements)) {
					$resultarray=array();
					foreach ($elements as $element) {
					 $nodes = $element->childNodes;
					 foreach ($nodes as $node) {
					   $resultarray[] = $node->nodeValue;
					 }
					}
					$entity_title = $resultarray;
					if(strlen($entity_title[0])>0){
					 $page_title = $entity_title[0];
					}
				}
			}
				// check for different in title
			if ($post->post_title != $page_title) { $changes = $changes."|post_title"; $do_page_update = true; }
			
			// check for difference in post name
			if ($post->post_name != BAPISync::clean_post_name($seo["DetailURL"])) { $changes = $changes."|post_name"; $do_page_update = true; }
		}
		// case 3: page exists does not exist in wp and does not exist in Bookt
		else if (!$page_exists_in_wp && empty($seo)) {
			// Action: Do nothing and let wp generate a 404
			//print_r("case 3");
		}
		// case 4: page does not exist in wp but exists in Bookt
		else if (!$page_exists_in_wp && !empty($seo)) {
			//print_r("case 4".$do_market_update);exit();
			// Result-> Need to create the page
			$changes = "create new page";
			$tempPost = new stdClass();
			$post = new WP_Post($tempPost);
			$do_page_update = true;
			$do_meta_update = true;
		}
		//Check if developer is using debugmode and force entity sync
		if (isset($_GET['debugmode'])&&$_GET['debugmode']){
			$do_page_update = true;
		}

		if ($do_page_update || $force_sync) {
			// do page update
			$bapi = getBAPIObj();
			$post->comment_status = "close";		

			$options = BAPISync::getEntityOptions($seo['entity']); 

			$c = $bapi->get($seo["entity"], array(intval($seo["pkid"])), $options);

			//Sort reviews
			$reviews = $c['result'][0]['ContextData']['Reviews'];

			function review_cmp($a, $b)
			{
			    if ($a['ID'] == $b['ID']) {
			        return 0;
			    }
			    return ($a['ID'] > $b['ID']) ? -1 : 1;
			}

			if($reviews) {
				usort($reviews, "review_cmp");
				$c['result'][0]['ContextData']['Reviews'] = $reviews;
			}

			


			if( !is_string( $s2s_success = $bapisync->getMustache( $seo['entity'], $seo['pkid'], $c ) ) ) {
				// by "trash"ing the post, WP will display a nice 404.
				// next time we try to sync and the property shows up, it will be reverted to an active page.
				$post->post_status = 'trash';
			} else {
				$post->post_content = $s2s_success;
				//if we have a bapikey
				if(!empty($pagekey) || $pagekey != null){
				   $dom = new DomDocument();
				   libxml_use_internal_errors(true);
				   $dom->loadHTML("<!DOCTYPE html><html><head></head><body>".$post->post_content."</body></html>");
				   libxml_use_internal_errors(false);
				   $xpath = new DOMXpath($dom);
				   $xpathq = "//h2[@class='title']";
				   $elements = $xpath->query($xpathq);
				   if (!is_null($elements)) {
					$resultarray=array();
					foreach ($elements as $element) {
					 $nodes = $element->childNodes;
					 foreach ($nodes as $node) {
					   $resultarray[] = $node->nodeValue;
					 }
					}
					$entity_title = $resultarray;
					if(strlen($entity_title[0])>0){
					 $page_title = $entity_title[0];
					}
				   }
				}

				$post->post_title = $page_title;
				$post->post_name = BAPISync::clean_post_name($seo["DetailURL"]);
				$post->post_parent = get_page_by_path(BAPISync::getRootPath($seo["entity"]))->ID;
				if($do_market_update){
					$post->post_parent = ensure_ma_landing_pages($seo["DetailURL"]);
				}

				$post->post_type = "page";
				//For later use w/ custom post types
				// if($seo["entity"]=='property'){
					// $post->post_type = "kigo_property";
				// }
				// if($seo["entity"]=='marketarea'){
					// $post->post_type = "kigo_market_area";
				// }
			}

			remove_filter('content_save_pre', 'wp_filter_post_kses');
			if (empty($post->ID)) { 
				$post->ID = wp_insert_post($post, $wp_error);
			} else {
				$post->ID = wp_update_post($post);
			}					
			add_filter('content_save_pre', 'wp_filter_post_kses');

			//$desc = $c['result'][0]['Description'];
			//array_walk_recursive($c['result'][0], function (&$val) { $val = addcslashes(str_replace("\'","'",$val),'"'); });
			
			//$desc = addcslashes($desc, '"');

			//$c['result'][0]['Description'] = $desc;

			//test( $c['result'][0] );

			update_post_meta( $post->ID, 'bapi_property_data', wp_slash(json_encode($c['result'][0], JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT)) );
			update_post_meta( $post->ID, 'property_type', $c['result'][0]['Type']);
			update_post_meta( $post->ID, 'property_headline', $c['result'][0]['Headline']);
		}

		
		if ($do_meta_update || $do_page_update || $force_sync) { 
			// update the meta tags					
			does_meta_exist("bapi_last_update", $meta) ? update_post_meta($post->ID, 'bapi_last_update', time()) : add_post_meta($post->ID, 'bapi_last_update', time(), true);
			does_meta_exist("bapi_meta_description", $meta) ? update_post_meta($post->ID, 'bapi_meta_description', $seo["MetaDescrip"]) : add_post_meta($post->ID, 'bapi_meta_description', $seo["MetaDescrip"], true);
			does_meta_exist("bapi_meta_keywords", $meta) ? update_post_meta($post->ID, 'bapi_meta_keywords', $seo["MetaKeywords"]) : add_post_meta($post->ID, 'bapi_meta_keywords', $seo["MetaKeywords"], true);
			//does_meta_exist("_wp_page_template", $meta) ? update_post_meta($post->ID, "_wp_page_template", BAPISync::getPageTemplate($seo["entity"])) : add_post_meta($post->ID, "_wp_page_template", BAPISync::getPageTemplate($seo["entity"]), true);
			update_post_meta( $post->ID, "_wp_page_template", BAPISync::getPageTemplate($seo["entity"]) );
			does_meta_exist("bapikey", $meta) ? update_post_meta($post->ID, "bapikey", BAPISync::getPageKey($seo["entity"],$seo["pkid"])) : add_post_meta($post->ID, "bapikey", BAPISync::getPageKey($seo["entity"],$seo["pkid"]), true);
			does_meta_exist("bapi_meta_title", $meta) ? update_post_meta($post->ID, 'bapi_meta_title', $seo["PageTitle"]) : add_post_meta($post->ID, 'bapi_meta_title', $seo["PageTitle"], true);
			
		}
		return true;
	}
	
	function does_meta_exist($name, $meta) {
		if (empty($meta)) { return false; }
		if (empty($meta[$name])) { return false; }
		return true;
	}
	
	function bapi_sync_coredata() {
		$syncdebugmode = 0;
		$do_core_update = false;
		//Check if developer is using debugmode and force entity sync
		if (isset($_GET['syncdebugmode'])&&$_GET['syncdebugmode']){
			$do_core_update = true;
			$syncdebugmode = 1;
			echo '<!--synctest-->';
		}
		if(!(strpos($_SERVER['REQUEST_URI'],'wp-admin')===false)||!(strpos($_SERVER['REQUEST_URI'],'wp-login')===false)){
			return false;
		}
		
		// initialize the bapisync object		
		global $bapisync;
		$bapisync = new BAPISync();
		$bapisync->init();
		
		$bapi = getBAPIObj();
		if (!$bapi->isvalid()) { return; }

		delete_option('bapi_sync_error');
		delete_option('bapi_seo_sync_error');
		
		// check if we need to refresh solution data
		$data = BAPISync::getSolutionDataRaw();
		$lastmod = BAPISync::getSolutionDataLastModRaw();
		if(empty($data) || empty($lastmod) || ((time()-$lastmod) > KIGO_SOLUTION_SYNC) || $do_core_update || BAPISync::obsoletedByLastSettingsUpdate( $lastmod ) ) {
			$data = $bapi->getcontext(true,$syncdebugmode);
			
			if (!empty($data) && !is_wp_error($data)) {
				$tagline = $data['SolutionTagline'];
				$solName = $data['SolutionNameInformal'];
				$data = json_encode($data, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); // convert back to text
				update_option('bapi_solutiondata',wp_slash($data));
				update_option('bapi_solutiondata_lastmod',time());
				update_option('blogdescription',$tagline);
				update_option('blogname',$solName);
			} else {
				update_option('bapi_sync_error', $data);
			}	
		}	
		
		// check if we need to refresh seo data
		$data = BAPISync::getSEODataRaw();
		$lastmod = BAPISync::getSEODataLastModRaw();
		if(empty($data) || empty($lastmod) || ((time()-$lastmod) > KIGO_SEO_SYNC) || $do_core_update || BAPISync::obsoletedByLastSettingsUpdate( $lastmod ) ) {
			$data = $bapi->getseodata(true,$syncdebugmode);
			if (!empty($data) && !is_wp_error($data)) {
				$data = $data['result']; // just get the result part
				$data = json_encode($data); // convert back to text
				update_option('bapi_keywords_array',$data);
				update_option('bapi_keywords_lastmod',time());
			} else {
				update_option('bapi_seo_sync_error', $data);
			}					
		}
	}

function get_doc_template($docname,$setting){
	global $bapi_all_options;
	$docmod = $bapi_all_options[$setting.'_lastmod']; //settings must be registered w/ this consistent format.
	$doctext = $bapi_all_options[$setting];
	if(((time()-60)-$docmod)>0){

		// FIXME: WHY NOT USING BAPI object?
		$getopts=array('http'=>array('method'=>"GET",'header'=>"User-Agent: InstaSites Agent\r\nReferer: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "\r\n"));
		$stream = stream_context_create($getopts);
		$url = getbapiurl().'/ws/?method=get&ids=0&entity=doctemplate&docname='.urlencode($docname).'&apikey='.getbapiapikey();
		$d = file_get_contents($url,FALSE,$stream);
		$darr = json_decode($d);
		$doctext = $darr->result[0]->DocText;
		
		/* Temporary Hack For Tag Substitution */
		$siteurl = parse_url($bapi_all_options['bapi_site_cdn_domain'],PHP_URL_HOST);
		$solution = $bapi_all_options['blogname'];
		$doctext = str_replace("#Solution.Solution#", $solution, $doctext);
		$doctext = str_replace("#Site.PrimaryURL#", $siteurl, $doctext);
		/* End Temporary Hack */
		
		update_option($setting,$doctext);
		update_option($setting.'_lastmod',time());
		bapi_wp_site_options();
	}
	return $bapi_all_options[$setting];
}

function ensure_ma_landing_pages($detailurl){
	$perm = explode('/',rtrim(ltrim($detailurl,'/'),'/'));
	$i = 0;
	$req_path = '';
	while($i < count($perm)-1){
		$orig_req = $req_path;
		$req_path .= $perm[$i].'/';
		//echo $req_path;
		$pid = get_page_by_path($req_path)->ID;
		//echo ' '.$pid;
		if(empty($pid)){
			//echo "no-page";
			$tempPost = new stdClass();
			$post = new WP_Post($tempPost);
			$post->comment_status = "close";
			$post->post_content = "";
			$post->post_title = $perm[$i];
			$post->post_name = $perm[$i];
			$post->post_parent = get_page_by_path($orig_req)->ID;
			$post->post_type = "page";
			//print_r($post);
			$pid = wp_insert_post($post, $wp_error);
			//echo $postid;
			// update the meta tags					
			add_post_meta($pid, 'bapi_last_update', 0, true);
			add_post_meta($pid, 'bapi_meta_description', '', true);
			add_post_meta($pid, 'bapi_meta_keywords', '', true);
			add_post_meta($pid, "bapikey", 'marketarea:-1', true);			
		}
		$i++;
		//echo "<br>";
	}
	return $pid;
}

function crawl_sitemap_pages(){
	$urls = array();  
	$xml= wp_remote_get(get_site_url().'/sitemap.xml');
	$xml= $xml['body'];
	$DomDocument = new DOMDocument();
	$DomDocument->preserveWhiteSpace = false;
	$DomDocument->loadXML("$xml"); // $DOMDocument->load('filename.xml');
	$DomNodeList = $DomDocument->getElementsByTagName('loc');

	foreach($DomNodeList as $url) {
		wp_remote_get( $url->nodeValue );
	}
	return true;
}
?>
