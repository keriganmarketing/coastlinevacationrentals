<?php

/**
 * Adds BAPI_Header widget.
 */
class BAPI_Header extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'bapi_header', // Base ID
			'Kigo Header', // Name
			array( 'description' => __( 'Displays the Header', 'text_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		$apikey = getbapiapikey();
		if (!empty($apikey)) {
			$fname = get_stylesheet_directory() . '/insta-default-content/insta-header.php';
			if (!file_exists($fname)) {
				$fname = plugin_dir_path( __FILE__ ) . 'insta-default-content/insta-header.php';				
			}
			if (file_exists($fname)) {
				$t = file_get_contents($fname);					
				$m = new Mustache_Engine();				
				$wrapper = getbapisolutiondata();	
				global $bapi_all_options;
				$display_related_websites = array();

				if($bapi_all_options['bapi_display_related_websites'] && !empty($wrapper['site']['Sites']))
				{
					$display_related_websites = explode(",", $bapi_all_options['bapi_display_related_websites']);
					foreach($wrapper['site']['Sites'] as $key => $site)
					{ 
						if(!in_array( $site['ID'], $display_related_websites) || get_option('bapi_display_related_websites') === false)
						{
							unset($wrapper['site']['Sites'][$key]);
						}
					}
				}

				if(!empty($wrapper['site']['Sites'])){
					$wrapper['site']['Sites'] = array_values($wrapper['site']['Sites']);

					$string = $m->render($t, $wrapper);
					echo $string;
				}
			}
			else{
				echo '<div id="poweredby"><a rel="nofollow" target="_blank" href="http://kigo.net">Vacation Rental Software by Kigo</a></div>';
			}
		}
		else{
			echo '<div id="poweredby"><a rel="nofollow" target="_blank" href="http://kigo.net">Vacation Rental Software by Kigo</a></div>';
		}
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}	

} // class BAPI_Header

/**
 * Adds BAPI_Footer widget.
 */
class BAPI_Footer extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'bapi_footer', // Base ID
			'Kigo Footer', // Name
			array( 'description' => __( 'Displays the Footer', 'text_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		$apikey = getbapiapikey();
		if (!empty($apikey)) {
			$fname = get_stylesheet_directory() . '/insta-default-content/insta-footer.php';
			if (!file_exists($fname)) {
				$fname = plugin_dir_path( __FILE__ ) . 'insta-default-content/insta-footer.php';				
			}
			if (file_exists($fname)) {
				$wrapper = getbapisolutiondata();	
				$t = file_get_contents($fname);					
				$m = new Mustache_Engine();

				//print_r($wrapper);
				$string = $m->render($t, $wrapper);
				echo $string;			
			}
			else{
				echo '<div id="poweredby"><a rel="nofollow" target="_blank" href="http://kigo.net">Vacation Rental Software by Kigo</a></div>';
			}
		}
		else{
			echo '<div id="poweredby"><a rel="nofollow" target="_blank" href="http://kigo.net">Vacation Rental Software by Kigo</a></div>';
		}
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}	

} // class BAPI_Footer


/**
 * Adds BAPI_HP_Slideshow widget.
 */
class BAPI_HP_Slideshow extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'bapi_hp_slideshow', // Base ID
			'Kigo Homepage Slideshow', // Name
			array( 'description' => __( 'Homepage Slideshow', 'text_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		?>
        <div id="bapi-hp-slideshow"></div>		        
        <?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Homepage Slideshow', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

} // class BAPI_HP_Slideshow


/**
 * Adds BAPI_SiteSelector widget.
 */
 class BAPI_SiteSelector extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'bapi_multisites', // Base ID
			'Kigo Site Selector', // Name
			array( 'description' => __( 'Displays Flags for each Site', 'text_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		//echo $before_widget;
		$apikey = getbapiapikey();
		if (!empty($apikey)) {
			$t = '{{#site.HasMultiSites}}<span class="siteselector widget">{{#site.Sites}}&nbsp;<a href="http://{{Url}}"><i class="flag flag-{{Language}}"><span style="display:none">{{RegionInfo.DisplayName}}</span></i></a>{{/site.Sites}}</span>{{/site.HasMultiSites}}';
			$m = new Mustache_Engine();
			$wrapper = getbapisolutiondata(); 		
			
			foreach($wrapper['site']['Sites'] as $key => $site) {
				if(!in_array($site['ID'], explode(",",get_option('bapi_display_related_websites'))) || get_option('bapi_display_related_websites') == false) {
					unset($wrapper['site']['Sites'][$key]);
				}
			}

			$wrapper['site']['Sites'] = array_values($wrapper['site']['Sites']);		

			$string = $m->render($t, $wrapper);
			echo $string;
		}
		//echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}	

} // class BAPI_Header
 

/**
 * Adds BAPI_HP_LogoWithTagline widget.
 */
class BAPI_HP_LogoWithTagline extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'bapi_hp_logowithtagline', // Base ID
			'Kigo Homepage Logo With Tagline', // Name
			array( 'description' => __( 'Homepage Logo With Tagline', 'text_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		$wrapper = getbapisolutiondata();
		$logo = str_replace("http:", "https:", $wrapper["site"]["SolutionLogo"]);
		$tagline = $wrapper["site"]["SolutionTagline"];
		$url = ($_SERVER['SERVER_PORT']==443 ? get_option('bapi_site_cdn_domain') : "/");
		if (empty($url)) { $url = "/"; }
		$logo = set_url_scheme($logo);
		?>
		<a href="<?= $url ?>"><img src="<?= $logo ?>" alt="" /></a>
		<h2><?= $tagline ?></h2>
        <?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Homepage Logo With Tagline', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

} // class BAPI_HP_LogoWithTagline




/**
 * Adds BAPI_HP_Logo widget.
 */
class BAPI_HP_Logo extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'bapi_hp_logo', // Base ID
			'Kigo Homepage Logo', // Name
			array( 'description' => __( 'Homepage Logo', 'text_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		$url = '';
		$wrapper = getbapisolutiondata();
		$logo = get_option('Site_Logo', false) ? : $wrapper["site"]["SolutionLogo"];
		$currdomain = $_SERVER['SERVER_NAME']; //echo $currdomain;
		$cdndomain = parse_url(get_option('bapi_site_cdn_domain'));

		if(($currdomain==$cdndomain['host'])||is_admin()||is_super_admin()){ //Always link to subdomain if logged in as admin [Jacob]
			$url = '/';
			if($_SERVER['SERVER_PORT']==443){
				$url = 'http://'.$currdomain.'/';
			}
		}
		else{
			$url = get_option('bapi_site_cdn_domain');
		}

		$logo = set_url_scheme($logo);
	   ?>
		  <div class="bapi-logo"><a href="<?= $url ?>" ><img src="<?= $logo ?>" alt="" /></a></div>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Homepage Logo', 'text_domain' );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

} // class BAPI_HP_Logo



/**
 * Adds BAPI_HP_Search widget.
 */
class BAPI_HP_Search extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'bapi_hp_search', // Base ID
			'Kigo Search - Sidebar', // Name
			array( 'description' => __( 'Availability Search Widget for sidebars', 'text_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) { 

		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		$textdata = getbapitextdata();

		global $bapi_all_options; 


		//Get Solution Config
		$soldata = json_decode(wp_unslash($bapi_all_options['bapi_solutiondata']), true);
		$solutionConfig = json_decode($soldata['Config'], true)['result'];

		//test($solutionConfig);

		$key = $bapi_all_options['api_key'];

		$config = unserialize( $bapi_all_options['bapi_sitesettings_raw'] );
		
		//Get search page url with bapi_page_id == bapi_search
		$args = array(
			'fields'		=> 'ids',
		    'meta_query' 	=> array(
		        array(
		            'key' => 'bapi_page_id',
		            'value' => 'bapi_search'
		        )
		    ),
		    'post_type' 	=> 'page',
		    'posts_per_page' => 1
		);
		$posts = get_posts($args);

		$action = get_page_link($posts[0]);

		//test($solutionConfig['location']['hierarchicalValues']); 


		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . "<span class='glyphicons search'><i></i>" . $title . "</span>" . $after_title;
		?>

		<div id="bapi-search">
			<div class="property-search-block">
				<form name="property-search" method="get" action="<?php echo $action; ?>">
					<?php if($config['checkinoutmode'] != 0) { 
						$options = array(
							'mindate'	=> $solutionConfig['minbookingdate'],
							'maxdate'	=> $solutionConfig['maxbookingdate'],
							'maxbookingdays'	=> $solutionConfig['maxbookingdays'],

						);

						$options = json_encode($options);
					?>
					<div class="category-block inner-addon right-addon">
						<input id="searchcheckin" name="checkin" data-min-stay="<?php echo $config['deflos']; ?>" type="text" value="" class="span12 txtb quicksearch sessioncheckin datepickercheckin datepicker" data-field="scheckin" data-options='<?php echo $options; ?>' placeholder="<?php echo $textdata['Check-In']; ?>" />
						<span class="halflings calendar cal-icon-trigger"><i></i></span>
					</div>
					<?php } ?>

					<?php if($config['checkinoutmode'] != 0) { ?>
						<?php if($config['checkinoutmode'] == 1) { ?>
						<div class="category-block inner-addon right-addon">
							<input id="searchcheckout" name="checkout" type="text" value="" class="span12 txtb quicksearch sessioncheckout datepickercheckout datepicker" data-field="scheckout" placeholder="<?php echo $textdata['Check-Out']; ?>" />
							<span class="halflings calendar cal-icon-trigger"><i></i></span>
						</div>
						<?php } ?>

						<?php if($config['checkinoutmode'] == 2 && !empty($solutionConfig['los']['values'])) { ?>
						<div class="category-block">
							<select name="los" class="span12 property-search-input quicksearch sessionlos" data-field="los">
								<option value=""><?php if($nights = $textdata['Nights']) { echo $nights; } else { echo $solutionConfig['los']['prompt']; } ?></option>		
								<?php
								foreach($solutionConfig['los']['values'] as $value) {
									echo sprintf('<option value="%s" %s>%s</option>', $value['Data'], $config['deflos'] == $value['Data'] ? 'selected="true"' : '', $value['Label'] );
								} ?>
							</select>
						</div>
						<?php } ?>
					<?php } ?>

					<?php if($config['amenitysearch']) { ?>
					<div class="category-block amenitiesBlock">
						<select name="amenities" class="checkboxes" multiple title="<?php echo $textdata['Amenities']; ?>">
							<?php foreach($solutionConfig['amenity']['values'] as $amenity) {
								echo sprintf('<option value="%s" %s>%s</option>', $amenity['Data'], $_SESSION['amenity'] == $amenity['Data'] ? 'selected="selected"' : '', $amenity['Label']);
							} ?>
						</select>
						<div id="amenitiesDropdownCheckbox"></div>
					</div>
					<?php } ?>

					<?php if($config['adultsearch']) { ?>
					<div class="category-block">
						<select class="span12 property-search-input quicksearch" name="adults[min]" data-field="adults[min]">
							<option value=""><?php echo $solutionConfig['adults']['prompt']; ?></option>
							<?php
							foreach($solutionConfig['adults']['values'] as $value) { ?>
								<option <?php echo isset($_GET['adults']) && $_GET['adults'] == $value['Data'] ? 'selected' : ''; ?> value="<?php echo $value['Data']; ?>"><?php echo $value['Label']; ?></option>
							<?php } ?>
						</select>
					</div>
					<?php } ?>

					<?php if($config['childsearch']) { ?>
					<div class="category-block">
						<select class="span12 property-search-input quicksearch" name="children[min]" data-field="children[min]">
							<option value=""><?php echo $solutionConfig['children']['prompt']; ?></option>
							<?php
							foreach($solutionConfig['children']['values'] as $value) { ?>
								<option <?php echo isset($_GET['children']) && $_GET['children'] == $value['Data'] ? 'selected' : ''; ?> value="<?php echo $value['Data']; ?>"><?php echo $value['Label']; ?></option>
							<?php } ?>
						</select>
					</div>
					<?php } ?>

					<?php 
						$groups = array(
							array(
								'solution'	=> 'category',
								'local'		=> 'categorysearch'
							),
							array(
								'solution'	=> 'dev',
								'local'		=> 'devsearch'
							),
							array(
								'solution'	=> 'sleeps',
								'local'		=> 'sleepsearch'
							),
							array(
								'solution'	=> 'minsleeps',
								'local'		=> 'minsleepsearch'
							),
							array(
								'solution'	=> 'baths',
								'local'		=> 'bathsearch'
							),
							// array(
							// 	'solution'	=> 'amenity',
							// 	'local'		=> 'amenitysearch'
							// ),
						);

						
						//Only show property types that we actually have on this site
						global $wpdb; 
						$types = get_transient('kigo_property_categories');
						if( empty($types) || KIGO_DEBUG ) {
							$types = array_keys( $wpdb->get_results("SELECT DISTINCT meta_value FROM $wpdb->postmeta pm, $wpdb->posts p WHERE meta_key  = 'property_type' and pm.post_id=p.ID  and p.post_type='page' ", "OBJECT_K") );
						
							set_transient('kigo_property_categories', $types, HOUR_IN_SECONDS);
						}

						if( count($types) > 0 && is_array($solutionConfig['category']['values']) ) {
							foreach($solutionConfig['category']['values'] as $key => $value) {
								if(!in_array($value['Label'], $types)) {
									unset($solutionConfig['category']['values'][$key]);
								}
							}
						}


						//test($solutionConfig['dev']);
						//test($config);



						foreach($groups as $group) { 
							if( !empty($config[$group['local']]) && isset($solutionConfig[$group['solution']]) ) { ?>
								<div class="category-block">
									<select class="span12 property-search-input quicksearch" name="<?php echo $group['solution']; ?>" data-field="<?php echo $group['solution']; ?>">
										<option value=""><?php echo $solutionConfig[$group['solution']]['prompt']; ?></option>		
										<?php 
										foreach($solutionConfig[$group['solution']]['values'] as $value) { ?>
											<option <?php echo isset($_GET[$group['solution']]) && $_GET[$group['solution']] == $value['Data'] ? 'selected' : ''; ?> value="<?php echo $value['Data']; ?>"><?php echo $value['Label']; ?></option>
										<?php } ?>
									</select>
								</div>
							<?php }
						}
					?>

					<?php 
					if($config['locsearch'] == 'Market Area Drop Down List' || $config['locsearch'] == 'Market Area Autocomplete') { ?>
						<?php if($config['locsearch'] == 'Market Area Autocomplete') {
							$hierarchy = [];
							$hierarchy[] = array(
								'key'	=> $solutionConfig['location']['hierarchicalValues'][0]['Label'],
								'value'	=> $solutionConfig['location']['hierarchicalValues'][0]['Data']
							);

							if($solutionConfig['location']['hierarchicalValues'][0]['Children']) {
								foreach($solutionConfig['location']['hierarchicalValues'][0]['Children'] as $child) {
									$hierarchy[] = array(
										'key'	=> $child['Label'],
										'value'	=> $child['Data']
									);
								}
							}

						?>
						<script>var hierarchy = <?php echo json_encode($hierarchy); ?>;</script>
						<div class="category-block">
							<input type="text" name="location" class="span12 property-search-input quicksearch sessionlocation bapi-malocationsearch-new" data-field="location" placeholder="<?php echo $solutionConfig['location']['prompt']; ?>" />
						</div>
						<?php } else { ?>
						<div class="category-block">
							<select name="location" class="span12 property-search-input quicksearch sessionlocation" data-field="location">
								<option value=""><?php echo $solutionConfig['location']['prompt']; ?></option>

								<?php if(!empty($solutionConfig['location']['hierarchicalValues'])) { ?>
									<?php foreach($solutionConfig['location']['hierarchicalValues'] as $value) { ?>
										<option value="<?php echo trim($value['Data']); ?>" class="level1"><?php echo $value['Label']; ?>&nbsp;(<?php echo $value['PropertyCount']; ?>)</option>
										<?php if(!empty($value['Children'])) { ?>
											<?php foreach($value['Children'] as $child1) { ?>
											<option value="<?php echo trim($child1['Data']); ?>" class="level2">-&nbsp;<?php echo $child1['Label']; ?>&nbsp;(<?php echo $child1['PropertyCount']; ?>)</option>
												<?php if(!empty($child1['Children'])) { ?>
													<?php foreach($child1['Children'] as $child2) { ?>
														<option value="<?php echo trim($child2['Data']); ?>" class="level3">&nbsp;&nbsp;&nbsp;-&nbsp;<?php echo $child2['Label']; ?>&nbsp;(<?php echo $child2['PropertyCount']; ?>)</option>
														<?php if(!empty($child2['Children'])) { ?>
															<?php foreach($child2['Children'] as $child3) { ?>
																<option value="<?php echo trim($child3['Data']); ?>" class="level4">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;<?php echo $child3['Label']; ?>&nbsp;(<?php echo $child3['PropertyCount']; ?>)</option>
															<?php } ?>
														<?php } ?>
													<?php } ?>
												<?php } ?>
											<?php } ?>
										<?php } ?>
									<?php } ?>
								<?php } ?>
							</select>
						</div>
						<?php } ?>
					<?php } ?>

					<?php
					if($config['locsearch'] == 'City Drop Down List' || $config['locsearch'] == 'City Autocomplete') { ?>
						<?php
						if($config['locsearch'] == 'City Autocomplete') { 
							$cities = $solutionConfig['city']['values'];

							$data = [];
							foreach($cities as $city) {
								$data[] = array(
									'key'	=> $city['Label'],
									'value'	=> $city['Data']
								);
							}
						?>
						<script>var cities = <?php echo json_encode($data); ?>;</script>
						<div class="category-block">
							<input type="text" name="city" class="span12 property-search-input quicksearch sessionlocation bapi-locationsearch-new" data-field="location" placeholder="<?php echo $solutionConfig['location']['prompt']; ?>" />
						</div>
						<?php } else { ?>
						<div class="category-block">
							<select name="city" class="span12 property-search-input quicksearch sessionlocation" data-field="location">
								<option value=""><?php echo $solutionConfig['city']['prompt']; ?></option>		
								<?php 
								foreach($solutionConfig['city']['values'] as $value) {
									echo sprintf('<option value="%s" %s>%s</option>', $value['Data'], ($value['Data'] == $_GET['location'] ? 'selected="selected"' : ''), $value['Label']);
									?>
								<?php } ?>
							</select>
						</div>
						<?php } ?>
					<?php } ?>

					<?php 
					if($config['rate']['enabled']) { ?>
					<div class="category-block">
						<select name="maxrate[max]" class="span12 property-search-input quicksearch sessionmaxratemax" data-field="maxrate[max]">
							<option value=""><?php echo $solutionConfig['rate']['prompt']; ?></option>
							<?php foreach($solutionConfig['rate']['values'] as $value) {
								echo sprintf('<option value="%d" %s>%s</option>', $value['Data'], ($value['Data'] == $_GET['maxrate']['max'] ? 'selected="selected"' : ''), $value['Label']);
								?>
								<option value="<?php echo $value['Data']; ?>"><?php echo $value['Label']; ?></option>
							<?php } ?>
						</select>
					</div>
					<?php } ?>

					<?php 
					if($config['minsleepsearch'] == 'on' && !empty($solutionConfig['sleeps']['minvalues'])) { ?>
					<div class="category-block">
						<select name="sleeps[min]" class="span12 property-search-input quicksearch sessionsleepsmin" data-field="sleeps[min]">
							<option value=""><?php echo $solutionConfig['sleeps']['prompt']; ?></option>
							<?php
							foreach($solutionConfig['sleeps']['minvalues'] as $value) {
								echo sprintf('<option value="%d" %s>%s</option>', $value['Data'], ($value['Data'] == $_GET['rooms']['min'] ? 'selected="selected"' : ''), $value['Label']);
							} ?>
						</select>
					</div>
					<?php } ?>

					<?php
					if($config['bedsearch'] == 'on') { ?>
					<div class="category-block">
						<select name="beds[exactly]" class="span12 property-search-input quicksearch sessionroomsmin" data-field="beds">
							<option value=""><?php echo $solutionConfig['beds']['prompt']; ?></option>
							<?php
							if(!empty($solutionConfig['beds']['values'])){
								foreach($solutionConfig['beds']['values'] as $value) { 
									if($value['Data'] <= $config['maxbedsearch']) {
										echo sprintf('<option value="%d" %s>%s</option>', $value['Data'], ($value['Data'] == $_GET['beds'] ? 'selected="selected"' : ''), $value['Label']);
									} 
								}
							}?>
						</select>
					</div>
					<?php } ?>

					<?php
					if($config['minbedsearch'] == 'on') { ?>
					<div class="category-block">
						<select name="beds[min]" class="span12 property-search-input quicksearch sessionroomsmin" data-field="beds[min]">
							<option value=""><?php echo $solutionConfig['beds']['prompt']; ?></option>
							<?php
							if( !empty($solutionConfig['beds']['minvalues']) ) {
								foreach($solutionConfig['beds']['minvalues'] as $value) { 
									if($value['Data'] <= $config['maxbedsearch']) { 
										echo sprintf('<option value="%d" %s>%s</option>', $value['Data'], ($value['Data'] == $_GET['beds']['min'] ? 'selected="selected"' : ''), $value['Label']);
									} 
								} 
							} ?>
						</select>
					</div>
					<?php } ?>

					<?php 
					if($config['headlinesearch'] == 'on') { 

                        $args = array(
                            'meta_query' => array(
                                array(
                                    'key' => 'property_headline',
                                ),
                                array(
                                    'key' => 'bapikey',
                                    'value' => 'property:',
                                    'compare' => 'LIKE'
                                ),
                            ),
                            'post_type' => 'page',
                            'posts_per_page' => -1
                        );
                        $posts = get_posts($args);

                        $headlines = [];
                        foreach($posts as $post) {
                            if( !empty( $headline = get_post_meta($post->ID, 'property_headline', true)) ) {
                                $headlines[] = array(
                                    'key'	=> $headline,
                                    'value'	=> $headline,
                                );
                            }
                        }

					?>
					<script>var headlines = <?php echo json_encode($headlines); ?>;</script>
					<div class="category-block">
						<input type="text" name="headline" class="span12 property-search-input quicksearch sessionheadline-new" data-field="headline" placeholder="<?php echo $solutionConfig['headline']['prompt']; ?>" autocomplete="off" />
					</div>
					<?php } ?>

					<?php 
					if($config['altid']['enabled']) { ?>
					<div class="category-block">
						<input type="text" name="altid" class="span12 txtb quicksearch sessinoaltid" data-field="altid" />
					</div>
					<?php } ?>

					<div class="property-search-button-block search-button-block category-block">
						<button class="quicksearch-dosearch btn" data-field-selector="quicksearch" type="submit"><?php echo $textdata['Search']; ?></button>&nbsp;
						<button type="reset" class="btn quicksearch-doclear" style="float:right; border: none; background: transparent; box-shadow:none;"><?php echo $textdata['Clear']; ?></button>
						<a class="quicksearch-doadvanced" style="display:none" data-field-selector="quicksearch" href="javascript:void(0)"><?php echo $textdata['Advanced']; ?></a>&nbsp;
					</div>

					<div style="clear:both"></div>

					<?php 
						$order = $config['searchsortorder'] == 'ASC' ? 'sortasc' : 'sortdesc';
					?>

					<input type="hidden" name="sort" class="quicksearch" data-field="sort" value="<?php echo $config['searchsort']; ?>" />
					<input type="hidden" name="<?php echo $order; ?>" class="quicksearch" data-field="<?php echo $order; ?>" value="true" />
					<input type="hidden" name="restrictavail" class="quicksearch" data-field="restrictavail" value="<?php echo $config['showunavailunits'] == 'on' ? 1 : 0; ?>" />
					<input type="hidden" name="search" value="1" />
				</form>
			</div>
		</div>

		<script>
		jQuery(function($) {
			
		});
		</script>

        <?php 
        wp_enqueue_script( 'hp_search', get_relative( plugins_url( '/js/widgets/search.js', __FILE__) ), array('typeahead', 'multiselect') );

		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Search', 'text_domain' );
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}

} // class BAPI_HP_Search

/**
 * Adds BAPI_Search widget.
 */
class BAPI_Search extends BAPI_HP_Search {
	public function __construct() {
		WP_Widget::__construct(
	 		'bapi__search', // Base ID
			'Kigo Search', // Name
			array( 'description' => __( 'Availability Search Widget', 'text_domain' ), ) // Args
		);
	}
} // class BAPI_Search

/**
 * Adds BAPI_Inquiry_Form widget.
 */
class BAPI_Inquiry_Form extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'bapi_inquiry_form', // Base ID
			'Kigo Inquiry Form', // Name
			array( 'description' => __( 'Inquiry Form', 'text_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {


        $translations = getbapitextdata();

		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		
		if(isset( $instance[ 'inquiryModeTitle' ])){$inquiryModeTitle =  $instance['inquiryModeTitle'];}
		else{ $inquiryModeTitle = "Inquire for Booking Details";}
		
		/* Do we show the phone field ? */
		$bShowPhoneField = isset($instance[ 'showPhoneField' ]) ? $instance[ 'showPhoneField' ] : true;

		/* Its the Phone Field Required ? */
		$bPhoneFieldRequired = isset($instance[ 'phoneFieldRequired' ]) ? $instance[ 'phoneFieldRequired' ] : true;

		/* Do we show the date fields ? */
		$bShowDateFields = isset($instance[ 'showDateFields' ]) ? $instance[ 'showDateFields' ] : true;
		
		/* Do we show the number of guests fields ? */
		$bShowNumberGuestsFields = isset($instance[ 'showNumberGuestsFields' ]) ? $instance[ 'showNumberGuestsFields' ] : true;
		
		/* Do we show the how did you hear about us dropdown ? */
		$bShowLeadSourceDropdown = isset($instance[ 'showLeadSourceDropdown' ]) ? $instance[ 'showLeadSourceDropdown' ] : true;

		/* Its the Lead Source Dropdown Required ? */
		$bLeadSourceDropdownRequired = isset($instance[ 'leadSourceDropdownRequired' ]) ? $instance[ 'leadSourceDropdownRequired' ] : false;
		
		/* Do we show the comments field ? */
		$bShowCommentsField = isset($instance[ 'showCommentsField' ]) ? $instance[ 'showCommentsField' ] : true;

		// Show dates fields checkbox
		$bShowDateFields = isset($instance[ 'showDateFields' ]) ? $instance[ 'showDateFields' ] : true;

		$LeadSourceRequired = isset($instance[ 'LeadSourceRequired' ]) ? $instance[ 'LeadSourceRequired' ] : false; // <-- This variable was never being set and the field could not be set as required.

		$bapikey = get_post_meta( get_the_ID(), 'bapikey', true );
		$pid = explode(':',$bapikey)[1];


		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
		if ( ! empty( $inquiryModeTitle ) )
			echo '<div class="inquirymodetitle hide">'. $inquiryModeTitle . '</div>';
		?>

		<?php 
			global $bapi_all_options;	
			$bapi = getBAPIObj(); 

			$config_raw = unserialize( $bapi_all_options['bapi_sitesettings_raw'] ); 
			$soldata = json_decode(wp_unslash($bapi_all_options['bapi_solutiondata']), true);
			$config = json_decode($soldata['Config'], true)['result'];

			$context = $bapi->getcontext();

			//echo "<pre>"; print_r($config); echo "</pre>";
			//  ?method=createevent&checkin=08-16-2016&checkout=08-20-2016&name=wes&email=test%40test.com&homephone=1231231234&adults=1&children=2&&apikey=5ff229df-1b62-4474-81b8-90b5430e92d7&tz=5&tzname=EST&sesspid=-7380&language=en-US&callback=jQuery19109811800924454397_1471269914117&_=1471269914118
		?>

		<form>
			<div id="questions-block">	
				<div id="questions-block-inner">
					<fieldset id="have-question" class="leadrequest">
					<input type="hidden" name="pid" data-field="pid" value="<?php echo $pid; ?>" />
					<input type="hidden" name="apikey" data-field="apikey" value="<?php echo get_option('api_key'); ?>" />
					<input type="hidden" name="language" data-field="language" value="<?php echo $context['Site']['Language']; ?>" />
					<input type="hidden" name="tz" data-field="tz" value="<?php echo $context['TimeZoneID']; ?>" />
					<input type="hidden" name="tzname" data-field="tzname" value="" />
					

					<ul class="unstyled">
					<li id="nameform">
						<input type="text" name="name" id="txtName" class="span12 leadrequestfield required input-text" minlength="2" data-field="name" placeholder="<?php echo $translations['Name']; ?>*" required />
					</li>
					<li id="emailform">
						<input type="text" name="email" id="txtEmail" class="span12 leadrequestfield email required input-text" size="25" data-field="email" type="email" placeholder="<?php echo $translations['Email']; ?>*" data-validity="email" required />
					</li>
					<?php if($bShowPhoneField) { ?>
					<li id="phoneform">
						<input type="text" name="homephone" id="txtPhone" class="span12 leadrequestfield <?php if($bPhoneFieldRequired) echo 'required'; ?> input-text" data-field="homephone" placeholder="<?php echo $translations['Phone']; if($bPhoneFieldRequired) echo '*'; ?>" />
					</li>
					<?php } ?>
					
					<?php if($bShowDateFields) { ?>
					<li id="datesform" class="row-fluid">

						<div class="span12">
							<?php if($config_raw['checkinoutmode'] == 2) { ?>
							<label><?php echo $translations['Check-In']; ?></label>
							<?php } ?>
							<div class="category-block inner-addon right-addon">
								<input type="text" name="checkin" id="txtCheckIn-new" class="span12 leadrequestfield required input-text" data-field="checkin" placeholder="<?php echo $translations['Check-In']; ?>*" required />
								<span class="halflings calendar cal-icon-trigger"><i></i></span>
							</div>
						</div>

					</li>
					<li class="row-fluid">
						<?php if($config_raw['checkinoutmode'] == 2) {
							$los = $config_raw['deflos']; 
							$los = $los >= 1 ? $los : 1;
						?>
						<div class="span12">
							<label><?php echo $translations['Nights']; ?></label>
							<input id="questionsLos" name="los" class="quicksearch span12" type="number" data-field="los" min="<?php echo $los; ?>" value="<?php echo $los; ?>">
						</div>
						<?php } else { ?>
						<div class="span12">
							<div class="category-block inner-addon right-addon">
								<input type="text" name="checkout" id="txtCheckOut-new" class="span12 leadrequestfield required input-text" data-field="checkout" placeholder="<?php echo $translations['Check-Out']; ?>*" required />	
								<span class="halflings calendar cal-icon-trigger"><i></i></span>
							</div>
						</div>		
						<?php } ?>			
					</li>
					<?php } ?>
					
					<?php if($bShowNumberGuestsFields) { ?>
					<li id="numberGuestsform" class="row-fluid">
					<div class="span6">
						<label><?php echo $translations['Adults']; ?></label>
						<input type="text" name="adults" id="txtAdults" class="span12 leadrequestfield required input-text" data-field="adults" placeholder="<?php echo $translations['Adults']; ?>*" required />
					</div>
					<div class="span6">
						<label for=""><?php echo $translations['Children']; ?></label>
						<input type="text" name="children" id="txtChildren" class="span12 leadrequestfield required input-text" data-field="children" placeholder="<?php echo $translations['Children']; ?>*" required />	
					</div>					
					</li>
					<?php } ?>

					<?php if( $bShowLeadSourceDropdown && is_array($config['leadsources']['values']) ) { ?>
					<li id="lsform">
						<select name="ls" class="span12 leadrequestfield <?php if($bLeadSourceDropdownRequired) { echo 'required'; } ?>" data-field="ls" <?php if($bLeadSourceDropdownRequired) { echo 'required'; } ?>>
							<option value=""><?php echo $config['leadsources']['prompt']; ?></option>
							<?php 
								foreach($config['leadsources']['values'] as $lead) {
									echo '<option value="'.$lead['Data'].'">'.$lead['Label'].'</option>';
								}
							?>
						</select>
					</li>
					<?php } ?>

					<?php if($bShowCommentsField) { ?>
					<li id="commentsform">				
						<textarea name="msg" id="txtComments" rows="2" cols="20" class="span12 leadrequestfield" data-field="msg" placeholder="<?php echo $translations['Comments']; ?>" ></textarea>
					</li>
					<?php } ?>

					<li id="specialform" class="specialform hidden">
						<input type="text" name="special" autocomplete="off" id="txtSpecial" class="span12 leadrequestfield input-text" data-field="special" placeholder="<?php echo $translations['Special']; ?>" />
					</li>
					</ul>
					<div class="clearfix"></div>			
					<input type="button" id="btn-leadrequest" class="btn btn-primary pull-right doleadrequest-new" value='<?php echo $translations['Submit']; ?>' data-field-selector="leadrequestfield" />			
					</fieldset>
				</div>
			</div>
		</form>

		<?php /*
		<div id="bapi-inquiryform" class="bapi-inquiryform" data-templatename="tmpl-leadrequestform-propertyinquiry" data-log="0" data-showphonefield="<?= $bShowPhoneField ? 1 : 0; ?>" data-phonefieldrequired="<?= $bPhoneFieldRequired ? 1 : 0; ?>" data-showdatefields="<?= $bShowDateFields ? 1 : 0; ?>" data-shownumberguestsfields="<?= $bShowNumberGuestsFields ? 1 : 0; ?>" data-showleadsourcedropdown="<?= $bShowLeadSourceDropdown ? 1 : 0; ?>" data-leadsourcedropdownrequired="<?= $bLeadSourceDropdownRequired ? 1 : 0; ?>" data-showcommentsfield="<?= $bShowCommentsField ? 1 : 0; ?>" ></div>-->
		*/ ?>

        <?php

        wp_enqueue_script('inquiry', get_relative( plugins_url( '/js/inquiry.js', __FILE__) ), array());
        
        $googleConversionkey = get_option( 'bapi_google_conversion_key');
	$googleConversionlabel = get_option( 'bapi_google_conversion_label');
	$googleConversionCode = '';
	if($googleConversionkey != '' && $googleConversionlabel != ''){
		$googleConversionCode = '<!-- Google Code Conversion -->
<script type="text/javascript">
function googleConversionTrack(){
	var image = new Image(1,1); 
	image.src = "//www.googleadservices.com/pagead/conversion/'.$googleConversionkey.'/?value=0&amp;label='.$googleConversionlabel.'&amp;guid=ON&amp;script=0";
}
</script>';
	}
	
        echo $googleConversionCode;
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['inquiryModeTitle'] = strip_tags( $new_instance['inquiryModeTitle'] );
		/* we sanitize the values, either 1 or nothing */
		$instance['showPhoneField'] =  strip_tags($new_instance['showPhoneField']);
		$instance['phoneFieldRequired'] =  strip_tags($new_instance['phoneFieldRequired']);
		$instance['showDateFields'] =  strip_tags($new_instance['showDateFields']);
		$instance['showNumberGuestsFields'] =  strip_tags($new_instance['showNumberGuestsFields']);
		$instance['showLeadSourceDropdown'] =  strip_tags($new_instance['showLeadSourceDropdown']);
		$instance['leadSourceDropdownRequired'] =  strip_tags($new_instance['leadSourceDropdownRequired']);
		$instance['showCommentsField'] =  strip_tags($new_instance['showCommentsField']);
        

        return $instance;
	}

	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Contact Us', 'text_domain' );
		}
		if ( isset( $instance[ 'inquiryModeTitle' ] ) ) {
			$inquiryModeTitle = $instance[ 'inquiryModeTitle' ];
		}
		else {
			$inquiryModeTitle = __( 'Inquire for Booking Details', 'text_domain' );
		}
		// Show phone field checkbox
		if ( isset( $instance[ 'showPhoneField' ] ) ) { $bShowPhoneField = esc_attr($instance[ 'showPhoneField' ]); }
		else{ $bShowPhoneField = true;}
		
		// phone field required checkbox
		if ( isset( $instance[ 'phoneFieldRequired' ] ) ) { $bPhoneFieldRequired = esc_attr($instance[ 'phoneFieldRequired' ]); }
		else{ $bPhoneFieldRequired = true;}
		
		// Show dates fields checkbox
		if ( isset( $instance[ 'showDateFields' ] ) ) { $bShowDateFields = esc_attr($instance[ 'showDateFields' ]); }
		else{ $bShowDateFields = true;}
		
		// Show number of guests fields checkbox
		if ( isset( $instance[ 'showNumberGuestsFields' ] ) ) { $bShowNumberGuestsFields = esc_attr($instance[ 'showNumberGuestsFields' ]); }
		else{ $bShowNumberGuestsFields = true;}
		
		// Show lead source dropdown checkbox
		if ( isset( $instance[ 'showLeadSourceDropdown' ] ) ) { $bShowLeadSourceDropdown = esc_attr($instance[ 'showLeadSourceDropdown' ]); }
		else{ $bShowLeadSourceDropdown = true;}
		
		// lead source dropdown required checkbox
		if ( isset( $instance[ 'leadSourceDropdownRequired' ] ) ) { $bLeadSourceDropdownRequired = esc_attr($instance[ 'leadSourceDropdownRequired' ]); }
		else{ $bLeadSourceDropdownRequired = false;}
		
		// Show comments field checkbox
		if ( isset( $instance[ 'showCommentsField' ] ) ) { $bShowCommentsField = esc_attr($instance[ 'showCommentsField' ]); }
		else{ $bShowCommentsField = true;}
		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

<p>
<input id="<?php echo $this->get_field_id('showPhoneField'); ?>" name="<?php echo $this->get_field_name('showPhoneField'); ?>" class="checkbox" type="checkbox" value="1" <?php checked( '1', $bShowPhoneField ); ?>/>
<label for="<?php echo $this->get_field_id( 'showPhoneField' ); ?>">
  <?php _e( 'Display Phone Field?' ); ?>
</label>
</p>
<p>
<label for="<?php echo $this->get_field_id( 'inquiryModeTitle' ); ?>"><?php _e( 'Inquire Mode Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'inquiryModeTitle' ); ?>" name="<?php echo $this->get_field_name( 'inquiryModeTitle' ); ?>" type="text" value="<?php echo esc_attr( $inquiryModeTitle ); ?>" />
</p>

<p <?php if(!$bShowPhoneField ){echo 'style="display:none;"';} ?>>&nbsp;&nbsp;&nbsp;&nbsp;<input id="<?php echo $this->get_field_id('phoneFieldRequired'); ?>" name="<?php echo $this->get_field_name('phoneFieldRequired'); ?>" class="checkbox" type="checkbox" value="1" <?php checked( '1', $bPhoneFieldRequired ); ?>/>
<label for="<?php echo $this->get_field_id( 'phoneFieldRequired' ); ?>">
  <?php _e( 'Phone Field Required?' ); ?>
</label>
</p>

<p>
<input id="<?php echo $this->get_field_id('showDateFields'); ?>" name="<?php echo $this->get_field_name('showDateFields'); ?>" class="checkbox" type="checkbox" value="1" <?php checked( '1', $bShowDateFields ); ?>/>
<label for="<?php echo $this->get_field_id( 'showDateFields' ); ?>">
  <?php _e( 'Display Dates Fields?' ); ?>
</label>
</p>

<p>
<input id="<?php echo $this->get_field_id('showNumberGuestsFields'); ?>" name="<?php echo $this->get_field_name('showNumberGuestsFields'); ?>" class="checkbox" type="checkbox" value="1" <?php checked( '1', $bShowNumberGuestsFields ); ?>/>
<label for="<?php echo $this->get_field_id( 'showNumberGuestsFields' ); ?>">
  <?php _e( 'Display Guests Fields?' ); ?>
</label>
</p>

<p>
<input id="<?php echo $this->get_field_id('showLeadSourceDropdown'); ?>" name="<?php echo $this->get_field_name('showLeadSourceDropdown'); ?>" class="checkbox" type="checkbox" value="1" <?php checked( '1', $bShowLeadSourceDropdown ); ?>/>
<label for="<?php echo $this->get_field_id( 'showLeadSourceDropdown' ); ?>">
  <?php _e( 'Display Lead Source Dropdown?' ); ?>
</label>
</p>

<p <?php if(!$bShowLeadSourceDropdown ){echo 'style="display:none;"';} ?>>&nbsp;&nbsp;&nbsp;&nbsp;
<input id="<?php echo $this->get_field_id('leadSourceDropdownRequired'); ?>" name="<?php echo $this->get_field_name('leadSourceDropdownRequired'); ?>" class="checkbox" type="checkbox" value="1" <?php checked( '1', $bLeadSourceDropdownRequired ); ?>/>
<label for="<?php echo $this->get_field_id( 'leadSourceDropdownRequired' ); ?>">
  <?php _e( 'Lead Source Required?' ); ?>
</label>
</p>

<p>
<input id="<?php echo $this->get_field_id('showCommentsField'); ?>" name="<?php echo $this->get_field_name('showCommentsField'); ?>" class="checkbox" type="checkbox" value="1" <?php checked( '1', $bShowCommentsField ); ?>/>
<label for="<?php echo $this->get_field_id( 'showCommentsField' ); ?>">
  <?php _e( 'Display Comments Field?' ); ?>
</label>
</p>

		<?php 
	}

} // class BAPI_Inquiry_Form


/**
 * Adds BAPI_Featured_Properties widget.
 */
class BAPI_Featured_Properties extends WP_Widget {
	const TRANSIENT = 'kigo_featured_properties';

	public function __construct() {
		parent::__construct(
	 		'bapi_featured_properties', // Base ID
			'Kigo Featured Properties', // Name
			array( 'description' => __( 'Kigo Featured Properties', 'text_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) { 
		extract($args);
		$title = apply_filters('widget_title',$instance['title']);
		$idsList = json_decode($instance['idsList'], true);
		$pagesize = intval($instance['text']);
		$rowsize = intval($instance['rowsize']);
		if($rowsize<=0) { $rowsize=1; }

		$columnsize = floor(12/$rowsize);



		$imgSize = $columnsize >= 6 ? 'MediumURL' : 'ThumbnailURL';

		$bapikeys = [];
		if($idsList) {
			foreach($idsList as $prop) {
				$bapikeys[] = 'property:'.$prop['id'];
			}
		}



		//Get Featured Properties
		$args = array(
		    'meta_query' => array(
		        array(
		            'key' 		=> 'bapikey',
		            'value' 	=> $bapikeys,
		            'compare'	=> 'IN'
		        ),
		        array(
		        	'key'		=> 'bapi_property_data',
		        	'value'		=> '"Status":"A"',
		        	'compare'	=> 'LIKE'
		        ),
		    ),
		    'post_type' => 'page',
		    'posts_per_page' => $pagesize
		);



		if(!$idsList) {
			$args['orderby'] = 'rand';
			$args['meta_query'][0]['value'] = 'property:';
			$args['meta_query'][0]['compare'] = 'LIKE';
		}

		$posts = get_posts($args);




		$featured = [];
		foreach($posts as $post) {
			$data = json_decode( get_post_meta($post->ID, 'bapi_property_data')[0], true );

			if($data['Status'] == "A") { 

				if($idsList) {
					foreach($idsList as $key => $value) {
						if($value['id'] == $data['ID']) { 
							$featured[$key] = $data;
						}
					}
				} else {
					$featured[] = $data;
				}

			}
		}

		if($idsList) { ksort($featured); }


		global $bapi_all_options;	
		$textdata = getbapitextdata();		
		$key = $bapi_all_options['api_key'];
		$config = unserialize( $bapi_all_options['bapi_sitesettings_raw'] );


		$seo = json_decode(get_option('bapi_keywords_array'), true);

		if($seo) {

			//Parse and assign SEO data to be refereneced by key
			foreach($seo as $key => $value) {
				$seo[$value['pkid']] = $value;
				unset($seo[$key]);
			}	
			
			echo $before_widget;

			if(!empty($title))
				echo $before_title.$title.$after_title;
			?>

			<div class="row-fluid">
			<?php
			$count = 0;
			foreach($featured as $i => $f) {
				$quote = $f['ContextData']['Quote'];
			?>

				<div class="fp-featured span<?php echo $columnsize; ?>">    
					<div class="fp-image">
						<?php if($seo[$f['ID']]['Keyword']) : ?><a href="<?php echo $seo[$f['ID']]['DetailURL']; ?>"><?php endif; ?>
						<img <?php if($count>=4) { //Get first 4 images, use lazy loading for the rest
							echo 'src="'.plugin_dir_url(__FILE__).'img/placehold-400x300.png" data-';
						} ?>src="<?php echo $img = $f['PrimaryImage'][$imgSize]; ?>" alt="<?php echo $f['Headline']; ?>" placeholder="<?php echo plugin_dir_url(__FILE__).'img/placehold-400x300.png'; ?>" title="<?php echo $f['PrimaryImage']['Caption']; ?>" />
						<?php if($seo[$f['ID']]['Keyword']) : ?></a><?php endif; ?>
					</div>
					<div class="fp-outer"> 
						<div class="fp-title"><?php if($seo[$f['ID']]['Keyword']) : ?><a href='<?php echo $seo[$f['ID']]['DetailURL']; ?>'><?php endif; echo $f['Headline']; if($seo[$f['ID']]['Keyword']) : ?></a><?php endif; ?></div>
								<?php if($config['hidestarsreviews']) : ?>
									<?php if($f['NumReviews'] > 0) : ?>
										<div class="starsreviews"><div id="propstar-<?php echo $f['AvgReview']; ?>"><span class="stars"></span><i class="starsvalue"></i></div></div>
									<?php endif; ?>
								<?php endif; ?>
						<div class="fp-details">
							<b class="hidden-phone"><?php echo $f['Type']; ?></b>
							<?php if($beds = $f['Bedrooms']) : ?>&nbsp;|&nbsp;<b><?php echo $textdata['Beds']; ?>:</b>&nbsp;<?php echo $beds; endif; ?>
							<?php if($baths = $f['Bathrooms']) : ?>&nbsp;|&nbsp;<b><?php echo $textdata['Baths']; ?>:</b>&nbsp;<?php echo $baths; endif; ?>
							<?php if($sleeps = $f['Sleeps']) : ?>&nbsp;|&nbsp;<b><?php echo $textdata['Sleeps']; ?>:</b>&nbsp;<?php echo $sleeps; endif; ?>
						</div>
						<div class="clear"></div>
						<hr/>
						<div class="fp-rates">
							<?php if($quote['QuoteDisplay']['value']) { ?>
								<?php if($prefix = $quote['QuoteDisplay']['prefix']) : ?><span class="prefix"><?php echo $prefix; ?>:</span><?php endif; ?>
									<?php echo $quote['QuoteDisplay']['value']; ?>
								<?php if($suffix = $quote['QuoteDisplay']['suffix']) : ?><span class="suffix">/<?php echo $suffix; ?></span><?php endif; ?>
							<?php } ?>
						</div>
						<?php if($seo[$f['ID']]['Keyword']) : ?><a class="property-link" href="<?php echo $seo[$f['ID']]['DetailURL']; ?>"><?php echo $textdata['Details']; ?>&nbsp;<span>&rarr;</span></a><?php endif; ?>
						<div class="clearfix"></div>
					</div>
				</div>

			<?php	
				$count++;
				if($count % $rowsize == 0) { echo '</div><div class="row-fluid">'; }
			}
			?>
			</div>

	    <?php
			echo $after_widget;
		}
	}

	public function update( $new_instance, $old_instance ) {
		delete_transient(self::TRANSIENT);
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		if ( isset( $new_instance[ 'idsList' ] ) ){
			$countedItems = count(json_decode($new_instance['idsList'],true));
			if($countedItems > 0){
				$instance['text'] = $countedItems;
			}else{
				$instance['text'] = $new_instance['text'];
			}
		}else{
			$instance['text'] = $new_instance['text'];
		}
		
		$instance['rowsize'] = $new_instance['rowsize'];
		$instance['idsList'] = $new_instance['idsList'];

		return $instance;
	}

	public function form( $instance ) {
		$title = isset($instance['title']) ? $instance['title'] : __( 'Featured Properties', 'text_domain' );
		$pagesize = isset($instance['text']) ? $instance['text'] : 4;
		$rowsize = isset($instance['rowsize']) ? $instance['rowsize'] : 2;
		
		if ( isset( $instance[ 'idsList' ] ) ) { $idsListVal =  $instance['idsList']; }
		
		$sourceHeadlines = propertyList_array();
		$addIdsBlock = $this->get_field_id( 'addIdsBlock' );
		$addProperty = $this->get_field_id( 'addProperty' );
		$idsList = $this->get_field_id( 'idsList' );
		$propertyNamesList = $this->get_field_id( 'propertyNamesList' );
		$idsListValArray = json_decode($idsListVal,true);
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	        <input id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" type="hidden" value="<?php echo count($idsListValArray); ?>" />
        </p>
        <p>
        	<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( '# of Properties:' ); ?></label>
	        <select name="<?php echo $this->get_field_name( 'text' ); ?>" id="<?php echo $this->get_field_id( 'text' ); ?>">
			<?php	for($x = 1; $x <= 12; $x++){
				$selct = ($x==$pagesize)? 'selected':'';
				echo '<option value="'.$x.'" '.$selct.' >'.$x.'</option>';
			}?>
			</select>
        </p>
        <p>
	        <label for="<?php echo $this->get_field_id( 'rowsize' ); ?>"><?php _e( 'Row Size:' ); ?></label>
	        <select name="<?php echo $this->get_field_name( 'rowsize' ); ?>" id="<?php echo $this->get_field_id( 'rowsize' ); ?>">
			<?php	for($x = 1; $x <= 6; $x++){
				$selct = ($x==$rowsize)? 'selected':'';
				echo '<option value="'.$x.'" '.$selct.' >'.$x.'</option>';
			}?>
			</select>
		</p>
		<div id="<?php echo $addIdsBlock; ?>" class="addIdsBlock">
		<label for="<?php echo $addProperty; ?>"><?php _e( 'Search Property:' ); ?></label>
        <input id="<?php echo $addProperty; ?>" name="<?php echo $this->get_field_name( 'addProperty' ); ?>" type="text" autocomplete="off" class="addProperty" />
        <script type="text/javascript">
		$( document ).ready(function() {
			
		attachEventsFpWidget("#<?php echo $addProperty; ?>","#<?php echo $propertyNamesList; ?>","#<?php echo $idsList; ?>",<?php echo json_encode($sourceHeadlines); ?>);		
		});
		$(document).on('widget-updated', function(e, widget){
			attachEventsFpWidget("#<?php echo $addProperty; ?>","#<?php echo $propertyNamesList; ?>","#<?php echo $idsList; ?>",<?php echo json_encode($sourceHeadlines); ?>);
		});
		$(document).on('widget-added', function(e, widget){
			$('.widget[id*="bapi_featured_properties"]').each( function( index, element ) {
				var $theEl = $(this);
				var addProperty = $theEl.find(".addProperty").attr("id");
				var propertyNamesList = $theEl.find(".propertyNamesList").attr("id");
				var idsList = $theEl.find(".idsList").attr("id");
				attachEventsFpWidget("#"+addProperty,"#"+propertyNamesList,"#"+idsList,<?php echo json_encode($sourceHeadlines); ?>);
			});
		});
		</script>
        <!--<button type="button" class="button button-small">Select</button><br/>-->
        <input id="<?php echo $idsList; ?>" class="idsList" type="hidden" name="<?php echo $this->get_field_name( 'idsList' ); ?>" value="<?php echo esc_attr( $idsListVal ); ?>">
        
        <ol id="<?php echo $propertyNamesList; ?>" class="propertyNamesList">
        <?php
        $i = 0;
        $len = count($idsListValArray);
        if(!empty($idsListValArray)){
			foreach($idsListValArray as $val) {
				$hideBtn = '';
				if($i==0){$hideBtn = ' style="display: none;" ';}
				$hideBtn2 = '';
				if($i==$len - 1){$hideBtn2 = ' style="display: none;" ';}
				echo "<li data-value='".$val["id"]."' data-headline='".esc_attr($val["headline"])."'><span class='item-wrap'><span class='head-block'>".$val["headline"].'</span><span class="group-btn"><button type="button" class="button remove-btn button-small"><span class="dashicons dashicons-trash"></span></button><button type="button" '.$hideBtn2.' class="button down-btn button-small"><span class="dashicons dashicons-arrow-down-alt"></span></button><button type="button"'.$hideBtn.'class="button up-btn button-small"><span class="dashicons dashicons-arrow-up-alt"></span></button></span></span>'."</li>";
				$i++;
			}
		}
        ?>
        </ol>
        </div>
        
		<?php 
	}

} // class BAPI_Featured_Properties

/**
* Adds BAPI_Developments_Widget widget.
*/
class BAPI_Developments_Widget extends WP_Widget {
	
	public function __construct() {
		parent::__construct(
	 		'bapi_developments_widget', // Base ID
			'Kigo Developments', // Name
			array( 'description' => __( 'Insta Developments', 'text_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title',$instance['title']);
		$pagesize = esc_textarea($instance['text']);
		$rowsize = intval($instance['rowsize']);
		if($rowsize<=0) { $rowsize=1; }
		echo $before_widget;
		if(!empty($title))
			echo $before_title . $title . $after_title;
		?>
        <div id="developments-widget" class="bapi-summary development-widget row-fluid" data-applyfixers="1" data-log="0" data-templatename="tmpl-developments-quickview" data-entity="development" data-searchoptions='{ "pagesize": <?= $pagesize ?>, "sort": "random" }' data-rowfixselector=".development-holder" data-rowfixcount="<?= $rowsize ?>"></div>
        <?php
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['rowsize'] = $new_instance['rowsize'];
		return $instance;
	}

	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) { $title = $instance[ 'title' ]; }
		else { $title = __( 'Developments', 'text_domain' ); }
		if ( isset( $instance[ 'text' ] ) ) { $pagesize =  esc_textarea($instance['text']); }
		else { $pagesize = __( '4', 'text_domain' ); }
		if ( isset( $instance[ 'rowsize' ] ) ) { $rowsize =  $instance['rowsize']; }
		else { $rowsize = '4'; }		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        <label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( '# of Developments:' ); ?></label>
        <input id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" type="text" value="<?php echo esc_attr( $pagesize ); ?>" />
        <label for="<?php echo $this->get_field_id( 'rowsize' ); ?>"><?php _e( 'Row Size:' ); ?></label>
        <input id="<?php echo $this->get_field_id( 'rowsize' ); ?>" name="<?php echo $this->get_field_name( 'rowsize' ); ?>" type="text" value="<?php echo esc_attr( $rowsize ); ?>" />
		</p>
		<?php 
	}

} // class BAPI_Developments_Widget


/**
 * Adds BAPI_Property_Finders widget.
 */
class BAPI_Property_Finders extends WP_Widget {
	const TRANSIENT = 'kigo_property_finders';
	public function __construct() {
		parent::__construct(
	 		'bapi_property_finders', // Base ID
			'Kigo Search Buckets', // Name
			array( 'description' => __( 'Insta Search Buckets', 'text_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title',$instance['title']);
		$pagesize = esc_textarea($instance['text']);
		//if($pagesize > 1) { $pagesize --; }
		$rowsize = intval($instance['rowsize']);
		if($rowsize<=0) { $rowsize=1; }

		$textdata = getbapitextdata();

		global $bapi_all_options;	
		$bapi = getBAPIObj(); 

		$options = array(
			'pagesize'	=> $pagesize,
			'sort'		=> 'random'
		);

		//Get properties locally?

		//Get Properties
		$props = get_transient(self::TRANSIENT);
		if( empty($props) || KIGO_DEBUG) {
			$ids = $bapi->search('searches', $options)['result'];
			if(is_array($ids) && count($ids) > $pagesize) {
				$ids = array_slice($ids, 0, $pagesize);
			}
			
			$props = $bapi->get('searches', $ids, $options)['result'];

			set_transient(self::TRANSIENT, $props, HOUR_IN_SECONDS);
		}

		if(!$props)
			return;

		$cols = floor(12/$rowsize);

		echo $before_widget;
		if(!empty($title))
			echo $before_title.$title.$after_title;		
		?>


		<div class="row-fluid">

		<?php
		$count = 1;
		foreach($props as $prop) { 
			$seo = $prop['ContextData']['SEO'];
		?>

			<div class="pf-featured span<?php echo $cols; ?>">    
				<div class="pf-image">
					<?php if($keyword = $seo['Keyword']) { ?><a href="<?php echo $seo['DetailURL']; ?>"><?php } ?>
						<img src="<?php echo $prop['PrimaryImage']['ThumbnailURL']; ?>" alt="<?php echo $prop['Images']['PrimaryImage']['ThumbnailURL']; ?>" title="<?php echo $prop['Images']['PrimaryImage']['Caption']; ?>" />
					<?php if($keyword = $seo['Keyword']) { ?></a><?php } ?>
				</div>
				<div class="pf-outer">	
					<a class="pf-title" href='<?php if($keyword = $seo['Keyword']) { echo $seo['DetailURL']; } ?>'><?php echo $prop['Name']; ?></a>
					<hr/>	
					<?php if($desc = $prop['Description']) { ?>
					<div class="pf-description bapi-dotdotdot" style="max-height:100px"><?php echo $desc; ?></div>
					<?php } ?>	
				</div>
			</div>

			<?php if($count % $rowsize == 0) { echo '</div><div class="row-fluid">'; }
				$count++; 
			?>
		<?php }

		?>		

		</div>



		<?php
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		delete_transient(self::TRANSIENT);
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		$instance['rowsize'] = $new_instance['rowsize'];
		return $instance;
	}

	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) { $title = $instance[ 'title' ]; }
		else { $title = __( 'Property Finders', 'text_domain' ); }
		if ( isset( $instance[ 'text' ] ) ) { $pagesize =  esc_textarea($instance['text']); }
		else { $pagesize = __( '4', 'text_domain' ); }
		if ( isset( $instance[ 'rowsize' ] ) ) { $rowsize =  $instance['rowsize']; }
		else { $rowsize = '1'; }	
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	        <br />
	        <label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( '# of Properties:' ); ?></label>
	        <select name="<?php echo $this->get_field_name( 'text' ); ?>">
	        	<?php for($i=1; $i<=20; $i++) {
	        		echo sprintf('<option %s>'.$i.'</option>', $pagesize != $i ?: 'selected');
	        	} ?>
	        </select>
	        <br />
	        <label for="<?php echo $this->get_field_id( 'rowsize' ); ?>"><?php _e( 'Row Size:' ); ?></label>
	        <select name="<?php echo $this->get_field_name( 'rowsize' ); ?>">
	        	<?php for($i=1; $i<=6; $i++) {
	        		echo sprintf('<option %s>'.$i.'</option>', $rowsize != $i ?: 'selected');
	        	} ?>
	        </select>
        </p>
		<?php 
	}

} // class BAPI_Property_Finders




/**
 * Adds BAPI_Specials_Widget widget.
 */
class BAPI_Specials_Widget extends WP_Widget {
	const TRANSIENT = 'kigo_specials';

	private $max_items = 20,  
            $max_row_items = 12;

	public function __construct() {
		parent::__construct(
	 		'bapi_specials_widget', // Base ID
			'Kigo Specials', // Name
			array( 'description' => __( 'Insta Specials', 'text_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title',$instance['title']);
		$pagesize = esc_textarea($instance['text']); // Seems that this wasn't being used.
//		var_dump($pagesize);die;
		$rowsize = intval($instance['rowsize']);
		if($rowsize<=0) { $rowsize=1; }
		$cols = floor($this->max_row_items/$rowsize);

		$textdata = getbapitextdata();

		global $bapi_all_options;	
		
		//Get Specials
		$specials = get_transient(self::TRANSIENT);
		if( empty($specials) || KIGO_DEBUG ) {
			$bapi = getBAPIObj(); 

			$options = array(
				'pagesize'	=> $this->max_items, // The pagesize parameter seems that does nothing in the search. I can put 1 and search still returns more than one.
				'seo'		=> true,
				'page'		=> 1
			);


			$ids = (array) $bapi->search('specials', $options)['result'];

			// If the number of ids is higher than $pagesize let's slice it if not, this will return the whole array.
			$sized_ids = array_slice($ids,0, $pagesize);

			$specials = $bapi->get('specials', $sized_ids, $options)['result'];

			set_transient(self::TRANSIENT, $specials, HOUR_IN_SECONDS);
		}

		if(!$specials)
			return;

		echo $before_widget;
		if(!empty($title))
			echo $before_title . "<span class='glyphicons tags'><i></i>" . $title . "</span>" . $after_title;

		echo '<div class="row-fluid">';

		$count = 1;
		foreach($specials as $special) {
			$seo = $special['ContextData']['SEO'];
		?>

		<div class="<?php echo "span".$cols; ?> special-holder">
			<div class="special-image">
				<a href="<?php if($seo['Keyword']) { echo $seo['DetailURL']; } ?>">
					<img alt="<?php echo $special['PrimaryImage']['Caption']; ?>" src="<?php echo $special['PrimaryImage']['ThumbnailURL']; ?>" caption="<?php echo $special['PrimaryImage']['Caption']; ?>" />	
				</a>
			</div>
			<div class="special-description">
				<a href="<?php if($seo['Keyword']) { echo $seo['DetailURL']; } ?>"><h4 class="special-title"><?php echo $seo['PageTitle']; ?></h4></a>
				<div class="special-sum"><?php echo $special['Summary']; ?></div>
			</div>
		</div>

		<?php 
			if($count % $rowsize == 0) { echo '</div><div class="row-fluid">'; }
			$count++;
		} 

		echo "</div>";

		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		delete_transient(self::TRANSIENT);
		$instance = array();

		$instance['title'] = strip_tags( $new_instance['title'] );
		if ( current_user_can('unfiltered_html') )
		{
			$instance['text'] =  ((int)$new_instance['text'] <=  $this->max_items) ?$new_instance['text'] : $this->max_items; // this field can't be higher than  $this->max_items
		}
		else
		{
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
			$instance['text'] =  ((int) $new_instance['text'] <=  $this->max_items) ?$new_instance['text'] : $this->max_items; // this field can't be higher than  $this->max_items
		}

        if ( current_user_can('unfiltered_html') )
        {
            $instance['rowsize'] =  ((int)$new_instance['rowsize'] <=  $this->max_row_items) ?$new_instance['rowsize'] : $this->max_row_items; // this field can't be higher than  $this->max_items
        }
        else
        {
            $instance['rowsize'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['rowsize']) ) ); // wp_filter_post_kses() expects slashed
            $instance['rowsize'] =  ((int) $new_instance['rowsize'] <=  $this->max_row_items) ?$new_instance['rowsize'] : $this->max_row_items; // this field can't be higher than  $this->max_items
        }

		return $instance;
	}

	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) { $title = $instance[ 'title' ]; }
		else { $title = __( 'Special Offers', 'text_domain' ); }
		if ( isset( $instance[ 'text' ] ) ) { $pagesize =  esc_textarea($instance['text']); }
		else { $pagesize = __( '4', 'text_domain' ); }
		if ( isset( $instance[ 'rowsize' ] ) ) { $rowsize =  $instance['rowsize']; }
		else { $rowsize = '1'; }		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        <br />
        <label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( '# of Offers ( '.$this->max_items.' max ):' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>"  type="number" value="<?php echo esc_attr( $pagesize ); ?>" min="1" max="<?php echo $this->max_items; ?>" />
        <br />
        <label for="<?php echo $this->get_field_id( 'rowsize' ); ?>"><?php _e( 'Row Size: ( '.$this->max_row_items.' max )' ); ?></label>
        <input id="<?php echo $this->get_field_id( 'rowsize' ); ?>" name="<?php echo $this->get_field_name( 'rowsize' ); ?>" type="number" value="<?php echo esc_attr( $rowsize ); ?>"  min="1" max="<?php echo $this->max_row_items; ?>" />
		</p>
		<?php 
	}

} // class BAPI_Specials_Widget



/**
 * Adds BAPI_Similar_Properties widget.
 */
class BAPI_Similar_Properties extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'bapi_similar_properties', // Base ID
			'Kigo Similar Properties', // Name
			array( 'description' => __( 'Insta Similar Properties', 'text_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title',$instance['title']);
		$pagesize = esc_textarea($instance['text']);
		if(empty($pagesize)) { $pagesize = 3; }
		$rowsize = intval($instance['rowsize']);
		if($rowsize<=0) { $rowsize=1; }
		$cols = floor(12 / $rowsize);

		$textdata = getbapitextdata();

		global $bapi_all_options;	
		$config = unserialize( $bapi_all_options['bapi_sitesettings_raw'] );

		$meta = get_post_meta( get_the_ID() );
		$id = explode(':', $meta['bapikey'][0])[1]; 

		//Get Similar
		$similars = get_transient('kigo_similar_ids_'.$id);
		if( empty($similars) || KIGO_DEBUG ) {
			$bapi = getBAPIObj();

			$options = array(
				'seo'		=> true,
				'page'		=> 1,
				'similarto'	=> $id
			);

			$ids = $bapi->search('property', $options)['result'];
			shuffle($ids);


			set_transient('kigo_similar_ids_'.$id, $ids, HOUR_IN_SECONDS * 24);
		}

		if(!$ids) return;

		foreach($ids as $key => $value) {
			$ids[$key] = 'property:'.$value;
		}

		$args = array(
			'fields'			=> 'ids',
			'post_type'			=> 'page',
			'posts_per_page'	=> 3,
			'orderby'			=> 'rand',
			'meta_key'			=> 'bapikey',
			'meta_query'		=> array(
				'key' => 'bapikey',
				'value' => $ids,
				'compare' => 'IN',
			)
		);

		$ids = new WP_Query($args);
		$ids = $ids->posts;

		echo $before_widget;
		if(!empty($title))
			echo $before_title.$title.$after_title;
		$i = 0;
		echo '<div class="row-fluid">';
		
		foreach($ids as $id) { 
			$similar = json_decode( get_post_meta($id, 'bapi_property_data', true), true ); 
			$context = $similar['ContextData'];
			$seo = $context['SEO'];
			$quote = $context['Quote']['QuoteDisplay']; ?>

			<div class="fp-featured <?php echo 'span'.$cols; ?>"> 
				<div class="fp-image">
					<?php if($seo['Keyword']) { ?><a href="<?php echo $seo['DetailURL']; ?>"><?php } ?>
					<img src="<?php echo $thumb = $similar['PrimaryImage']['ThumbnailURL']; ?>" alt="<?php echo $thumb; ?>" title="<?php echo $similar['PrimaryImage']['Caption']; ?>" />
					<?php if($seo['Keyword']) { ?></a><?php } ?>
				</div>
				<div class="fp-outer"> 
					<div class="fp-title"><?php if($seo['Keyword']) { ?><a href='<?php echo $seo['DetailURL']; ?>'><?php } echo $similar['Headline']; if($seo['Keyword']) { ?></a><?php } ?></div>
						<?php if(!$config['hidestarsreviews']) { ?>
							<?php if($similar['NumReviews'] > 0) { ?>
							 	<div class="starsreviews"><div id="propstar-<?php echo $similar['AvgReview']; ?>"><span class="stars"></span><i class="starsvalue"></i></div></div>
							<?php } ?>
						<?php } ?>
					<div class="fp-details">
						<b class="hidden-phone"><?php echo $similar['Type']; ?></b>
						<?php if($beds = $similar['Bedrooms']) { ?> |&nbsp;<b><?php echo $textdata['Beds']; ?>:</b>&nbsp;<?php echo $beds; } ?>
						<?php if($baths = $similar['Bathrooms']) { ?> |&nbsp;<b><?php echo $textdata['Baths']; ?>:</b>&nbsp;<?php echo $baths; } ?>
						<?php if($sleeps = $similar['Sleeps']) { ?> |&nbsp;<b><?php echo $textdata['Sleeps']; ?>:</b>&nbsp;<?php echo $sleeps; } ?>
					</div>
					<div class="clear"></div>
					<hr/>
					<div class="fp-rates" style="line-height:1; padding-bottom: .75em;">
					<?php if(!$similar['HidePrice']) { ?>
						<?php if($quote['value']) { ?>
							<?php if($prefix = $quote['prefix']) { ?><span class="prefix"><?php echo $prefix; ?>:</span><br /><?php } ?>
							<?php echo $quote['value']; ?>
							<?php if($suffix = $quote['suffix']) { ?><span class="suffix">/<?php echo $suffix; ?></span><?php } ?>
						<?php } ?>
					<?php } ?>
					</div>
				<?php if($seo['Keyword']) { ?><a class="property-link" href="{{ContextData.SEO.DetailURL}}"><?php echo $textdata['Details']; ?>&nbsp;<span>&rarr;</span></a><?php } ?>
				</div>
			</div>

		<?php
			$i++;	
			if($i % $rowsize == 0) { echo '</div><div class="row-fluid">'; }
		}
		echo '</div>';
		?>

        <!-- <div id="featuredproperties" class="bapi-summary" data-log="0" data-templatename="tmpl-featuredproperties-quickview" data-entity="property" data-searchoptions='{ "pagesize": <?= $pagesize ?>, "sort": "random", "similarto": true }' data-rowfixselector=".fp-featured" data-rowfixcount="<?= $rowsize ?>"></div> -->
		<?php
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['rowsize'] = $new_instance['rowsize'];
		return $instance;
	}

	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) { $title = $instance[ 'title' ]; }
		else { $title = __( 'Similar Properties', 'text_domain' ); }
		if ( isset( $instance[ 'rowsize' ] ) ) { $rowsize =  $instance['rowsize']; }
		else { $rowsize = '1'; }		
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        <label for="<?php echo $this->get_field_id( 'rowsize' ); ?>"><?php _e( 'Row Size:' ); ?></label>
        <select id="<?php echo $this->get_field_id( 'rowsize' ); ?>" name="<?php echo $this->get_field_name( 'rowsize' ); ?>">
        	<?php for($i=1; $i<=6; $i++) {
        		echo sprintf('<option %s>%d</option>', $rowsize == $i ? 'selected' : '', $i);
        	} ?>
        </select>
		</p>
		<?php 
	}

} // class BAPI_Similar_Properties

/**
 * Adds BAPI_Weather widget.
 */
class BAPI_Weather_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'bapi_weather_widget', // Base ID
			'Kigo Weather', // Name
			array( 'description' => __( 'Insta Weather', 'text_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title',$instance['title']);
		$woid = esc_textarea($instance['text']);
		$unit = $instance['unit'];
		if(empty($woid)) return;
		if(empty($unit)){
			$unit = 'f';
		}
		echo $before_widget;
		if(!empty($title))
			echo $before_title . "<span class='glyphicons brightness_increase'><i></i>" . $title . "</span>" . $after_title;
		?>
        <div id="weather-widget"></div>
		<script>
			$(document).ready(function () {
				// weather widget uses code found here: http://www.zazar.net/developers/jquery/zweatherfeed/
				// lookup woid here: http://woeid.rosselliot.co.nz/
				var woid = '<?= $woid ?>';
				var sTemperatureUnit = '<?= $unit ?>';
				if (woid!='') {
					if (sTemperatureUnit == null || sTemperatureUnit == '' && BAPI.defaultOptions.language=="en-US") { sTemperatureUnit = 'f'; }
					BAPI.UI.createWeatherWidget('#weather-widget', ['<?= $woid ?>'], { "link": false, "woeid": true, "unit": sTemperatureUnit });
				}
			});
        </script>
        <?php
		echo $after_widget;
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['unit'] = $new_instance['unit'];
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		return $instance;
	}

	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Weather', 'text_domain' );
		}
		if ( isset( $instance[ 'text' ] ) ) {
			$woid =  esc_textarea($instance['text']);
		}
		else {
			$woid = __( '2450022', 'text_domain' );
		}
		if ( isset( $instance[ 'unit' ] ) ) {
			$unit =  $instance['unit'];
		}
		else {
			$unit = 'f';
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        <label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'WOID:' ); ?></label>
        <input id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" type="text" value="<?php echo esc_attr( $woid ); ?>" />
        <br/>
        <small><a href="//woeid.rosselliot.co.nz/lookup/" target="_blank">Lookup WOID</a></small>
		<div class="clear"></div>
		<label for="<?php echo $this->get_field_id( 'unit' ); ?>">Unit</label>
		<select id="<?php echo $this->get_field_id( 'unit' ); ?>" name="<?php echo $this->get_field_name( 'unit' ); ?>">
			<option value="f" <?php if($unit=='f') echo 'selected'; ?>>Farenheit</option>
			<option value="c" <?php if($unit=='c') echo 'selected'; ?>>Celcius</option>
		</select>
		</p>
		<?php 
	}

} // class BAPI_Weather_Widget

class BAPI_DetailOverview_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
	 		'bapi_detailoverview', // Base ID
			'Kigo Detail Overview', // Name
			array( 'description' => __( 'Displays the overview section of a detail screen', 'text_domain' ), ) // Args
		);
	}

	public function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title',$instance['title']);
		$woid = esc_textarea($instance['text']);
		echo $before_widget;
		if(!empty($title))
			echo $before_title.$title.$after_title;
		?>

		<?php
			global $bapi_all_options;	
			$bapi = getBAPIObj(); 

			$key = $bapi_all_options['api_key'];
			$bapikey = explode( ':', get_post_meta(get_the_ID(), 'bapikey', true) );

			if(empty($bapi_all_options['bapi_solutiondata'])) $bapi_all_options = $bapi_all_options['bapi_solutiondata'];
			$soldata = json_decode(wp_unslash($bapi_all_options['bapi_solutiondata']), true);
			$config = json_decode($soldata['Config'], true)['result'];

			$config_raw = unserialize( $bapi_all_options['bapi_sitesettings_raw'] );

			$secure_url = get_option('bapi_secureurl', '');
			if(!empty($secure_url)){
				$secure_url = 'https://' . $secure_url;
			}

			$context = json_decode(wp_unslash(get_post_meta(get_the_ID(), 'bapi_property_data', true)), true); //$bapi->get($bapikey[0], $bapikey[1])['result'][0];
			//$context = $bapi->get($bapikey[0], $bapikey[1])['result'][0];

			//test($context);

			$availability = isset($context['ContextData']['Availability']) ? $context['ContextData']['Availability'] : false;

			if(is_array($availability) && !empty($availability)){

				$availability = array_values(array_unique($availability, SORT_REGULAR));
				
				foreach($availability as $key => $value) {
					$data = array(
						'from'	=> $value['SCheckIn'],
						'to'	=> $value['SCheckOut'],
					);
					

					unset($availability[$key]);

					$availability[$key] = $data;
				}
			}

			//Only show this if the property is bookable
			//if($context['IsBookable'] != 1) return;

			$contextData = $context['ContextData']; 

			$textdata = getbapitextdata();

			if(!is_array($_SESSION['mylist'])) { $_SESSION['mylist'] = array(); }
		?>

		<!-- Widget here -->
		<form name="getQuote" method="get" action="<?php echo $secure_url; ?>/makebooking" data-id="<?php echo get_the_ID(); ?>" data-propid="<?php echo $bapikey[1]; ?>">
			<script>
			 var availability = '<?php echo json_encode($availability); ?>';
			</script>
			<input type="hidden" name="redir" value="1" />
			<input type="hidden" name="search" value="1" />
			<input type="hidden" name="keyid" value="<?php echo $bapikey[1]; ?>" />
			<div id="book-block" class="module shadow" data-id="<?php echo $bapikey[1]; ?>"> 
				<div class="pd">
					<div class="rate-type">	
						<div class="alert alert-error <?php echo empty($context['Quote']['ValidationMessage']) ? 'hide' : ''; ?>"><?php echo $contextData['Quote']['ValidationMessage']; ?></div>
						
						<div class="quote-display <?php echo empty($contextData['Quote']['QuoteDisplay']['value']) ? 'hide' : ''; ?>">
							<?php if($contextData['Quote']['QuoteDisplay']['prefix']): ?><span class="quote-prefix"><?php echo $contextData['Quote']['QuoteDisplay']['prefix']; ?>:</span><?php endif; ?>
							<div class="clearfix"></div>
							<strong class="rate"><?php echo $contextData['Quote']['QuoteDisplay']['value']; ?></strong>
							<?php if($contextData['Quote']['QuoteDisplay']['suffix']): ?><span class="input-small">/</span><span class="input-small quote-suffix"><?php echo $contextData['Quote']['QuoteDisplay']['suffix']; ?></span><?php endif; ?>
						</div>
					</div>       
					
					<button class="book-btn btn btn-large btn-warning bapi-booknow hide" type="submit">
						<span><?php echo $textdata["Book Now"]; ?></span>
					</button>

					<button class="book-btn btn btn-large btn-warning bapi-inquirenow <?php echo $contextData['Quote']['IsValid'] ? '' : 'hide'; ?>" type="button">
						<span><?php echo $textdata["Inquire Now"]; ?></span>
					</button>

					<hr />  

				<div class="row-fluid">
					<div class="span6">
						<label><?php echo $textdata['Check-In']; ?></label>
						<div class="category-block inner-addon right-addon">
							<input id="rateblockcheckin" name="checkin" class="span12 quicksearch required" type="text" placeholder="<?php echo $textdata['Check-In']; ?>" value="" data-field="scheckin" data-min-stay="<?php echo $los = $context['MinStay']; ?>" required>
							<span class="halflings calendar cal-icon-trigger"><i></i></span>
						</div>
					</div>
					
					<div class="span6">
					<?php if($config_raw['checkinoutmode'] == 2) { 
						$los = $config_raw['deflos']; 
						$los = $los >= 1 ? $los : 1;
					?>
						<label><?php echo $textdata['Nights']; ?></label>
						<input id="quoteLos" name="los" class="quicksearch span12 required" type="number" min="<?php echo $los; ?>" value="<?php echo $los; ?>" data-field="los">
					<?php } else { ?>
						<label><?php echo $textdata['Check-Out']; ?></label>
						<div class="category-block inner-addon right-addon">
							<input id="rateblockcheckout" name="checkout" class="span12 quicksearch required" type="text" placeholder="<?php echo $textdata['Check-Out']; ?>" value="" data-field="scheckout" data-min="<?php echo $los; ?>" required>
							<span class="halflings calendar cal-icon-trigger"><i></i></span>
						</div>
						
					<?php } ?>
					</div>
				</div>

				<div class="row-fluid">

				<div class="span6">
					<label><?php echo $textdata['Adults']; ?></label>
					<input name="adults[min]" class="span12 quicksearch adultsfield" type="number" min="0" placeholder="<?php echo $textdata['Adults']; ?>" value="<?php echo $_SESSION['adults.min'] ? $_SESSION['adults.min'] : 2; ?>" data-field="adults[min]">
				</div>

				<div class="span6">
					<label><?php echo $textdata['Children']; ?></label>
					<input name="children[min]" class="span12 quicksearch childrenfield" type="number" min="0" placeholder="<?php echo $textdata['Children']; ?>" value="<?php echo $_SESSION['children.min'] ? $_SESSION['children.min'] : 0; ?>" data-field="children[min]" >
				</div>

				</div>

					<button class="btn btn-mini bapi-getquote" type="submit" data-id="<?php echo get_the_ID(); ?>"><?php echo $textdata['Update']; ?></button>
					<hr />

					<?php

						$myList = isset($_COOKIE['mylist']) ? json_decode(stripslashes($_COOKIE['mylist'])) : false;
					?>

					<button class="btn add-wishlist bapi-wishlisttracker <?php echo $myList && in_array($bapikey[1], $myList) ? 'active' : ''; ?>" data-pkid="<?php echo $bapikey[1]; ?>" type="button" data-toggle="button">
						<span class="halflings heart-empty">
							<i></i>
							<span>
								<?php 
								if($myList && in_array($bapikey[1], $myList)) {
									echo $textdata['WishListed']; 
								} else {
									echo $textdata['WishList']; 
								} ?>
							</span>
						</span>
					</button>
				</div>
			</div>	
		</form>
		<!-- /End Widget -->

		<script>
		jQuery(function($) {

		});
		</script>

        <!-- <div class="detail-overview-target"></div> -->
		<?php
		echo $after_widget;

		wp_enqueue_script( 'booking', get_relative( plugins_url( '/js/widgets/booking.js', __FILE__) ), array('jquery-min') );
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}	

} // class BAPI_DetailOverview_Widget


?>
