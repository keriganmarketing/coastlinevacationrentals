<?php	
	function curPageURL() {
		$pageURL = 'http';
		if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}
	
	function urlHandler_securepages() {
		global $bapi_all_options;
		$url = get_relative($_SERVER['REQUEST_URI']);
		//echo $url; exit();
		/*if (((strpos($url,'makepayment') !== false)||(strpos($url,'makebooking') !== false))&&(strpos($_SERVER['HTTP_HOST'],'lodgingcloud.com') == false)&&(strpos($_SERVER['HTTP_HOST'],'localhost') == false)&&(strpos($_SERVER['HTTP_HOST'],'localdomain') == false)) { //Do not force the redirect on lodgingcloud - helps bobby debug connect.
			$purl = parse_url(curPageURL());
			if($purl['scheme'] == 'http'){
				$nurl = "https://".$purl['host'].$purl['path'];
				if(!empty($purl['query'])){
					$nurl .= "?".$purl['query'];
				}
				//echo $nurl;
				header("Location: $nurl");
				exit();
			}
		}*/
		/* if (((strpos($url,'makepayment') === false)&&(strpos($url,'makebooking') === false)&&(strpos($url,'bapi') === false))){
			$purl = parse_url(curPageURL());
			if($purl['scheme'] == 'https'){
				$nurl = $bapi_all_options['bapi_site_cdn_domain'].$purl['path'];
				if(!empty($purl['query'])){
					$nurl .= "?".$purl['query'];
				}
				//echo $nurl;
				header("Location: $nurl");
				exit();
			}
		} 
		else{
			return;
		}*/
	}

	function get_default_pages_def( $url = null ) {
		$default_page_defs = array(
			array(
				"addtomenu" => false,
				"content"   => "/default-content/home.php",
				"intid"     => "bapi_home",
				"order"     => 1,
				"parent"    => "",
				"template"  => "page-templates/front-page.php",
				"title"     => "Home",
				"url"       => ""
			),
			array(
				"addtomenu" => true,
				"content"   => "",
				"intid"     => "bapi_rentals",
				"order"     => 2,
				"parent"    => "",
				"template"  => "page-templates/search-page.php",
				"title"     => "Rentals",
				"url"       => "rentals"
			),
			array(
				"addtomenu" => true,
				"content"   => "/default-content/rentalsearch.php",
				"intid"     => "bapi_search",
				"order"     => 1,
				"parent"    => "rentals",
				"template"  => "page-templates/search-page.php",
				"title"     => "Search",
				"url"       => "rentalsearch"
			),
			array(
				"addtomenu" => true,
				"content"   => "/default-content/allrentals.php",
				"intid"     => "bapi_property_grid",
				"order"     => 2,
				"parent"    => "rentals",
				"template"  => "page-templates/full-width.php",
				"title"     => "All Rentals",
				"url"       => "allrentals"
			),
			array(
				"addtomenu" => true,
				"content"   => "/default-content/propertyfinders.php",
				"intid"     => "bapi_search_buckets",
				"order"     => 3,
				"parent"    => "rentals",
				"template"  => "page-templates/full-width.php",
				"title"     => "Search Buckets",
				"url"       => "searchbuckets"
			),
			array(
				"addtomenu" => true,
				"content"   => "/default-content/developments.php",
				"intid"     => "bapi_developments",
				"order"     => 4,
				"parent"    => "rentals",
				"template"  => "page-templates/search-page.php",
				"title"     => "Developments",
				"url"       => "developments"
			),
			array(
				"addtomenu" => false,
				"content"   => "/default-content/mylist.php",
				"intid"     => "bapi_mylist",
				"order"     => 5,
				"parent"    => "rentals",
				"template"  => "page-templates/search-page.php",
				"title"     => "My List",
				"url"       => "mylist"
			),
			array(
				"addtomenu" => true,
				"content"   => "/default-content/specials.php",
				"intid"     => "bapi_specials",
				"order"     => 3,
				"parent"    => "",
				"template"  => "page-templates/full-width.php",
				"title"     => "Specials",
				"url"       => "specials"
			),
			array(
				"addtomenu" => true,
				"content"   => "/default-content/attractions.php",
				"intid"     => "bapi_attractions",
				"order"     => 4,
				"parent"    => "",
				"template"  => "page-templates/full-width.php",
				"title"     => "Attractions",
				"url"       => "attractions"
			),
			array(
				"addtomenu" => true,
				"content"   => "",
				"intid"     => "bapi_company",
				"order"     => 5,
				"parent"    => "",
				"template"  => "page-templates/full-width.php",
				"title"     => "Company",
				"url"       => "company"
			),
			array(
				"addtomenu" => true,
				"content"   => "/default-content/services.php",
				"intid"     => "bapi_services",
				"order"     => 1,
				"parent"    => "company",
				"template"  => "page-templates/full-width.php",
				"title"     => "Services",
				"url"       => "services"
			),
			array(
				"addtomenu" => true,
				"content"   => "/default-content/aboutus.php",
				"intid"     => "bapi_about_us",
				"order"     => 2,
				"parent"    => "company",
				"template"  => "page-templates/full-width.php",
				"title"     => "About Us",
				"url"       => "aboutus"
			),
			array(
				"addtomenu" => true,
				"content"   => "/default-content/infoforowners.php",
				"intid"     => "bapi_company_owner",
				"order"     => 3,
				"parent"    => "company",
				"template"  => "page-templates/full-width.php",
				"title"     => "Owner Information",
				"url"       => "companyowner"
			),
			array(
				"addtomenu" => true,
				"content"   => "/default-content/infoforguests.php",
				"intid"     => "bapi_company_guest",
				"order"     => 4,
				"parent"    => "company",
				"template"  => "page-templates/full-width.php",
				"title"     => "Guest Information",
				"url"       => "companyguest"
			),
			array(
				"addtomenu" => true,
				"content"   => "/default-content/contactus.php",
				"intid"     => "bapi_contact",
				"order"     => 6,
				"parent"    => "company",
				"template"  => "page-templates/contact-page.php",
				"title"     => "Contact Us",
				"url"       => "contact"
			),
			array(
				"addtomenu" => true,
				"content"   => "",
				"intid"     => "bapi_blog",
				"order"     => 7,
				"parent"    => "company",
				"template"  => "",
				"title"     => "Blog",
				"url"       => "blog"
			),
			array(
				"addtomenu" => false,
				"content"   => "/default-content/makebooking.php",
				"intid"     => "bapi_makebooking",
				"order"     => 9,
				"parent"    => "",
				"template"  => "page-templates/full-width.php",
				"title"     => "Make Booking",
				"url"       => "makebooking"
			),
			array(
				"addtomenu" => false,
				"content"   => "/default-content/makepayment.php",
				"intid"     => "bapi_makepayment",
				"order"     => 10,
				"parent"    => "",
				"template"  => "page-templates/full-width.php",
				"title"     => "Make a Payment",
				"url"       => "makepayment"
			),
			array(
				"addtomenu" => false,
				"content"   => "/default-content/bookingconfirmation.php",
				"intid"     => "bapi_booking_confirm",
				"order"     => 11,
				"parent"    => "",
				"template"  => "page-templates/full-width.php",
				"title"     => "Booking Confirmation",
				"url"       => "bookingconfirmation"
			),
			array(
				"addtomenu" => false,
				"content"   => "/default-content/rentalpolicy.php",
				"intid"     => "bapi_rental_policy",
				"order"     => 12,
				"parent"    => "",
				"template"  => "page-templates/full-width.php",
				"title"     => "Rental Policy",
				"url"       => "rentalpolicy"
			),
			array(
				"addtomenu" => false,
				"content"   => "/default-content/privacypolicy.php",
				"intid"     => "bapi_privacy_policy",
				"order"     => 13,
				"parent"    => "",
				"template"  => "page-templates/full-width.php",
				"title"     => "Privacy Policy",
				"url"       => "privacypolicy"
			),
			array(
				"addtomenu" => false,
				"content"   => "/default-content/termsofuse.php",
				"intid"     => "bapi_tos",
				"order"     => 14,
				"parent"    => "",
				"template"  => "page-templates/full-width.php",
				"title"     => "Terms of Use",
				"url"       => "termsofuse"
			)
		);
		
		if( !is_string( $url ) ) {
			return $default_page_defs;
		}
		
		foreach( $default_page_defs as $default_page_def ) {
			if( $url === $default_page_def[ 'url' ] ) {
				return $default_page_def;
			}
		}
		
		return null;
	}

	function urlHandler_bapidefaultpages() { 
		header('Access-Control-Allow-Origin: *');
		$url = get_relative($_SERVER['REQUEST_URI']);
		//echo $_SERVER['REQUEST_URI']; exit();
		if (strtolower($url) != "/bapi.init")
			return;
		
		header("Cache-Control: no-cache, must-revalidate");
		$menuname = "Main Navigation Menu";
		$menu_id = initmenu($menuname);
		
		$change_logs = array();
		foreach (get_default_pages_def() as $pagedef) {
			$change_logs[] = addpage($pagedef, $menu_id);
		}
		
		$qs = $_SERVER['QUERY_STRING'];
		if(strtolower($qs) == 'mode=initial-setup'){

			if( defined('KIGO_SELF_HOSTED') && !KIGO_SELF_HOSTED ) {
				switch_theme(WP_DEFAULT_THEME);
			}
			else {
				switch_theme('instatheme01');
				$toptions = get_option('instaparent_theme_options');
				$toptions['presetStyle'] = 'style01';
				update_option('instaparent_theme_options', $toptions);
				setSlideshowImages();
			}
			bapi_wp_site_options();
			$blog_url = get_site_url();
			update_option( 'bapi_first_look', 0 );
			header("HTTP/1.1 307 Temporary Redirect");
			header("Location: $blog_url");
			exit();
		}
		
		foreach( $change_logs as $log ) {
			if( is_array( $log['add_to_nav'] ) ) {
				echo "PageID=" . $log['add_to_nav']['page_id'] . ", Parent=" . $log['add_to_nav']['parent'] . ", navParentID=" . $log['add_to_nav']['nav_parent_id'] . "<br/>";
			}
			echo '<div>' . $log['action'] . ' menu item <b>' . $log['post_title'] . '</b> post_id=' . $log['post_id'] . ', miid=' . $log['miid'] . ', menu_id=' . $log['menu_id'] . '</div>';
		}
		exit();
	}
	
	
	function addpage($pagedef, $menu_id) {
		$ret = null;
		$parent = $pagedef['parent'];	
		$parentid = 0;
		$test = get_page_by_path($parent);
		if (!empty($test)) {
			$parentid = $test->ID;
		}
		
		// try to find if this page already exists
		$pid = getPageID($parent, $pagedef['url'], $pagedef['title']);
		
		// create the post
		$post = array();
		$post['ID'] = $pid;
		$post['menu_order'] = $pagedef['order'];
		$post['post_name'] = $pagedef['url'];	
		if (empty($post['post_name'])) {
			$post['post_name'] = null;
		}
		$post['post_title'] = $pagedef['title'];
		$post['post_status'] = 'publish';
		$post['post_parent'] = $parentid;
		$post['comment_status'] = 'closed';		
		
		// set the default content
		$content = $pagedef['content'];	
		if($content!=''){
			/* we check if the content is pointing to a local file */
			if(strpos($content, '/') === 0)
			{			
			$cpath = get_local(plugins_url($content,__FILE__));
			$t = file_get_contents($cpath);
			$m = new Mustache_Engine();
			/* we need to call this function so the $bapi_all_options gets populated */
			bapi_wp_site_options();
			$wrapper = getbapisolutiondata();
			$string = $m->render($t, $wrapper);
			}else{
				/* if not is pointing to a json object */				
				$jsonContent = file_get_contents($content);
				if($jsonContent != FALSE)
				{
				$jsonObjContent = json_decode($jsonContent);
				$string = $jsonObjContent->result[0]->DocText;
				}else{$string = '';}
			}
			$string = str_replace("\t", '', $string); // remove tabs
			$string = str_replace("\n", '', $string); // remove new lines
			$string = str_replace("\r", '', $string); // remove carriage returns			
			$post['post_content'] = $string; //utf8_encode($string);				
		}
		else {
			$post['post_content'] = '';
		}
		$post['post_type'] = 'page';			
						
		$action = "Added";
		if ($pid == 0) {			
			$pid = wp_insert_post($post, $error);			
		}
		else {
			$action = "Edited";
			wp_update_post($post);
		}
		add_post_meta($pid, 'bapi_page_id', $pagedef['intid'], true);
		update_post_meta($pid, "_wp_page_template", $pagedef['template']);					
			
		$miid = 0;
		$addtomenu = ($pagedef['addtomenu'] == 'true');
		if($addtomenu && !doesNavMenuExist($pid)) {				
			$miid = addtonav($pid, $menu_id, $post, $parent, $ret);
		}
		
		if($post['post_title']=='Home'){
			update_option( 'page_on_front', $pid);
			update_option( 'show_on_front', 'page');
		}
		if($post['post_title']=='Blog'){
			update_option( 'page_for_posts', $pid);
		}
		return array(
			'action'		=> $action,
			'post_title'	=> $post['post_title'],
			'post_id'		=> $pid,
			'miid'			=> $miid,
			'menu_id'		=> $menu_id,
			'add_to_nav'	=> $ret
		);	
	}	

	function addtonav($pid, $menu_id, $post, $parent, &$ret) {
		global $navmap;
		$navParentID = 0;
		if (!empty($navmap[$parent])&&!empty($parent)) {
			$navParentID = $navmap[$parent]; //getNavMenuID($parent); //$menu_id;
		}
		$ret = array(
			'page_id'		=> $pid,
			'parent'		=> $parent,
			'nav_parent_id'	=> $navParentID
		);
		$miid = wp_update_nav_menu_item($menu_id, 0, array(
								'menu-item-title' => $post['post_title'],
								'menu-item-object' => 'page',
								'menu-item-object-id' => $pid,
								'menu-item-type' => 'post_type',
								'menu-item-status' => 'publish',
								'menu-item-parent-id' => $navParentID,
								'menu-item-position' => $post['menu_order']));
		$url = $post['post_name'];
		$navmap[$url] = $miid;		
		return $miid;
	}
	
	function initmenu($menuname) {		
		$bpmenulocation = 'primary'; //Needs to be customized to InstaThemes when ready
		// Does the menu exist already?
		$menu_exists = wp_get_nav_menu_object( $menuname );
		
		// If it doesn't exist, let's create it.
		if( !$menu_exists){			
			$menu_id = wp_create_nav_menu($menuname);
			//print_r("<div>Menu does not exist.  Created menu with menuid=" . $menu_id . ".</div>");
		}
		else {
			$menu_id = getMenuID($bpmenulocation);
			//print_r("<div>Menu already exists with menuid=" . $menu_id . ".</div>");
		}
		
		if( !has_nav_menu( $bpmenulocation ) ){
			$locations = get_theme_mod('nav_menu_locations');
			$locations[$bpmenulocation] = $menu_id;
			set_theme_mod( 'nav_menu_locations', $locations );
		}
		return $menu_id;
	}	
	
	/* Helper Functions */
	function getPageID($parent, $url, $title) {
		$testurl = $parent . '/' . $url;
		$existing_page = get_page_by_path($testurl);
		if (!empty($existing_page)) {
			return $existing_page->ID;
		}
		$existing_page = get_page_by_title($title);
		if (!empty($existing_page)) {
			return $existing_page->ID;
		}
		return 0;
	}
	
	function getMenuID($menuname) {
		$locations = get_nav_menu_locations();
		if (isset($locations[$menuname])) {
			return $locations[$menuname];
		}
	}
	
	function doesNavMenuExist($pid) {		
		$locations = get_nav_menu_locations();		
		$menu = wp_get_nav_menu_object( $locations['primary'] );
		$menu_items = wp_get_nav_menu_items($menu->term_id);		
		foreach ( (array) $menu_items as $key => $menu_item ) {
			if ($menu_item->object_id == $pid) {				
				return true;
			}			
		}
		return false;		
	}
	
	function getNavMenuID($url) {
		$locations = get_nav_menu_locations();		
		$menu = wp_get_nav_menu_object( $locations['primary'] );
		$menu_items = wp_get_nav_menu_items($menu->term_id);		
				
		foreach ( (array) $menu_items as $key => $menu_item ) {
			$turl = parse_url($menu_item->url);
			$purl = $turl['path'];
			if ($purl == $url || $purl == '/' . $url || $purl == $url . '/' || $purl == '/' . $url . '/') {
				return $menu_item->ID;
			}			
		}
		return 0;
	}
?>
