<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package coastline_vr
 */

 $areaContent = get_post_meta($id);
 $post_thumbnail_id = get_post_thumbnail_id($id);
 $area_thumb_url = wp_get_attachment_url( $post_thumbnail_id, 'area-thumbnail' ); 
 
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="container">  
        <h1><?php echo get_the_title($id); ?></h1>
		<div class="row">
			<div class="col-md-8 col-lg-9" >
				<div class="entry-content">
					
					<?php the_content(); ?>	
					
					<?php //print_r($areaContent); ?>
					
					<?php //echo '<br>map_info_display_map = ' . $areaContent['map_info_display_map'][0]; ?>
					<?php //echo '<br>map_info_latitude,_longitutde = ' . $areaContent['map_info_latitude,_longitutde'][0]; ?>
					<?php //echo '<br>map_info_map_zoom = ' . $areaContent['map_info_map_zoom'][0]; ?>
					<?php //echo '<br>map_info_show_photos = ' . $areaContent['map_info_show_photos'][0]; ?>
					<?php //echo '<br>map_info_show_info_window = ' . $areaContent['map_info_show_info_window'][0]; ?>
					<?php //echo '<br>map_info_info_window_content = ' . $areaContent['map_info_info_window_content'][0]; ?>
					<?php //echo '<br>tripadvisor_module_display_tripadvisor = ' . $areaContent[0]['tripadvisor_module_display_tripadvisor']; ?>
					<?php //echo '<br>tripadvisor_module_tripadvisor = ' . $areaContent[0]['tripadvisor_module_tripadvisor']; ?>
					
				</div><!-- .entry-content -->
				
				<?php //if($areaContent['map_info_display_map'][0]=='on' && $areaContent['map_info_latitude,_longitutde'][0]!=''){ ?>
				
				<div class="row no-gutter">
					
				<?php
				//Get Place Info
				$apiKey = 'AIzaSyBi9_eWSaQ7AqrQM95SGdlChW6XXNGJEno';
					
				$placeRequest = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/place/textsearch/json?query='.urlencode(get_the_title()).'&key='.$apiKey), true);		
				$placeInfo = $placeRequest['results'][0];																																
				$placeLat = $placeInfo['geometry']['location']['lat'];
				$placeLng = $placeInfo['geometry']['location']['lng']; 
				$placePhotos = $placeInfo['photos'][0]['photo_reference']; 	
				$placeId = $placeInfo['place_id']; 
				$placeReference = $placeInfo['reference'];
                    
                if($placeRequest['status'] != 'OK'){
                    //echo $placeRequest['error_message'];
                }
				/*echo $placeLat.'<br>'; 				
				echo $placeLng.'<br>';
				echo $placePhotos.'<br>';	
				echo $placeId.'<br>'; 
				echo $placeReference.'<br>';*/
																								
				//Show Details based on Place info
				//$detailRequest = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/place/details/json?placeid='.urlencode($placeId).'&key='.$apiKey), true);
				//$detailInfo = $detailRequest['result'];
				//$detailPhotos = $detailInfo['photos'];
				//print_r($detailPhotos);																																
				?>																											
																				
				
				<script type="text/javascript">
					// When the window has finished loading create our google map below
					//google.maps.event.addDomListener(window, 'load', initMap);   
                    
                    function addMarker(location,contentString,type) {
                        var infowindow = new google.maps.InfoWindow({
                            content: contentString,
                            maxWidth: 300
                        });
                        var marker = new google.maps.Marker({
                            position: location,
                            setMap : map
                            //icon: '<?php echo get_template_directory_uri() ?>/img/map-'+type+'-pin.png'
                        });
                        marker.addListener('click', function(){
                            infowindow.close(); // Close previously opened infowindow
                            infowindow.open(map, marker);
                        });
                        
                        //bounds.extend(location);
                        //map.fitBounds(bounds);
                    }
                    
					function initMap() {
						// Basic options for a simple Google Map
						// For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions
						var mapOptions = {
							// How zoomed in you want the map to start at (always required)
							zoom: <?php if($areaContent['map_info_map_zoom'][0]==''){ echo '11'; } else { echo $areaContent['map_info_map_zoom'][0]; } ?>,

							// The latitude and longitude to center the map (always required)
							center: new google.maps.LatLng(<?php if($placeLat!=''){ echo $placeLat.','.$placeLng; }else{ echo $areaContent['map_info_latitude,_longitutde'][0]; } ?>),

							// How you would like to style the map.
							// This is where you would paste any style found on Snazzy Maps.
							styles: [{"featureType":"water","elementType":"all","stylers":[{"hue":"#7fc8ed"},{"saturation":55},{"lightness":-6},{"visibility":"on"}]},{"featureType":"water","elementType":"labels","stylers":[{"hue":"#7fc8ed"},{"saturation":55},{"lightness":-6},{"visibility":"off"}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"hue":"#83cead"},{"saturation":1},{"lightness":-15},{"visibility":"on"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"hue":"#f3f4f4"},{"saturation":-84},{"lightness":59},{"visibility":"on"}]},{"featureType":"landscape","elementType":"labels","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"hue":"#ffffff"},{"saturation":-100},{"lightness":100},{"visibility":"on"}]},{"featureType":"road","elementType":"labels","stylers":[{"hue":"#bbbbbb"},{"saturation":-100},{"lightness":26},{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"hue":"#ffcc00"},{"saturation":100},{"lightness":-35},{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"hue":"#ffcc00"},{"saturation":100},{"lightness":-22},{"visibility":"on"}]},{"featureType":"poi.school","elementType":"all","stylers":[{"hue":"#d7e4e4"},{"saturation":-60},{"lightness":23},{"visibility":"on"}]}]
						};

						// Get the HTML DOM element that will contain your map
						// We are using a div with id="map" seen below in the <body>
						var mapElement = document.getElementById('map');

						// Create the Google Map using our element and options defined above
						var map = new google.maps.Map(mapElement, mapOptions);

						// Let's also add a marker while we're at it
						var marker = new google.maps.Marker({
							position: new google.maps.LatLng(<?php if($placeLat!=''){ echo $placeLat.','.$placeLng; }else{ echo $areaContent['map_info_latitude,_longitutde'][0]; } ?>),
							map: map,
							title: 'Area Info',
                            icon: '<?php echo get_template_directory_uri() ?>/img/map-location-pin.png'
						});
                    
                    <?php if($areaContent['map_info_show_info_window'][0]=='on'){ ?>

						var infowindow = new google.maps.InfoWindow({
                            maxWidth: 300
                        });
						marker.addListener('click', function() {
							infowindow.open(map, marker);
						});

						infowindow.setContent('<?php echo str_replace("'",'', preg_replace( "/\r|\n/", "", $areaContent['map_info_info_window_content'][0] ) ); ?>');
						//infowindow.open(map, marker); //open by default

					<?php } ?>
                    
                    <?php
                        
                            $request = array(
                                  'posts_per_page'   => -1,
                                  'offset'           => 0,
                                  'order'            => 'ASC',
                                  'orderby'   		 => 'menu_order',
                                  'post_type'        => 'attraction',
                                  'post_status'      => 'publish',		  
                            );

                            $attrlist = get_posts( $request );
                            $attrTypes = array();
                            //$areaAttrs = array();
                            $j = 0;
                            if(is_array($attrlist)){
                                foreach($attrlist as $attr){
                                    $attrid = $attr->ID;
                                    $title = $attr->post_title;
                                    $content = $attr->post_content;
                                    $attrMeta = get_post_meta($attrid);
                                    $areaLink = get_field('area',$attrid);
                                    
                                    $terms = wp_get_post_terms( $attrid, 'type' );
                                    $latlng = $attrMeta['attraction_info_latitude,_longitutde'][0];
                                    
                                    $j++;
                                    
                                    $attrInfo = '';
                                    if($attrMeta['attraction_info_phone_number'][0]!=''){ 
                                        $attrInfo .= '<p><strong>Phone:</strong> '.$attrMeta['attraction_info_phone_number'][0].'</p>';
                                    }
                                    if($attrMeta['attraction_info_address'][0]!=''){ 
                                        $attrInfo .= '<p>'.$attrMeta['attraction_info_address'][0].'</p>';
                                    }
                                    if($attrMeta['attraction_info_website'][0]!=''){ 
                                        $attrInfo .= '<p><a href="'.$attrMeta['attraction_info_website'][0].'" target="_blank" >'.$attrMeta['attraction_info_website'][0].'</a></p>';
                                    }
                                        

                                    if($latlng == '' ){
                                        $placeRequest = json_decode(file_get_contents('https://maps.googleapis.com/maps/api/place/textsearch/json?query='.urlencode($title).'&key='.$apiKey), true);		 
                                        $placeInfo = $placeRequest['results'][0];													//print_r($placeInfo);																			
                                        $placeLat = $placeInfo['geometry']['location']['lat'];
                                        $placeLng = $placeInfo['geometry']['location']['lng']; 
                                        
                                        if($placeLat!='' && $placeLng != ''){
                                            
                                            $latlng = $placeLat.','.$placeLng;
                                            update_post_meta($attrid, 'attraction_info_latitude,_longitutde', $latlng);
                                            
                                        }
                                    }
                                  
                                     if($areaLink[0]->post_name == $post->post_name ){
                                      
                                        $key = array_search($terms[0]->name,$attrTypes);
                                        if($key === false){
                                          $attrTypes[$terms[0]->slug] = $terms[0]->name;
                                          //$areaAttrs[] = $post;
                                        }
                                       
                                     }
                                        
                                    if($areaLink[0]->post_name == $post->post_name && $latlng != ''){

                                    ?>
                    
                                    var marker<?php echo $j; ?> = new google.maps.Marker({
                                        position: new google.maps.LatLng(<?php echo $latlng; ?>),
                                        map: map,
                                        title: '<?php echo str_replace("'",'', $title); ?>',
                                        icon: '<?php echo get_template_directory_uri() ?>/img/map-<?php echo $terms[0]->slug; ?>-pin.png'
                                    });

                                    var infowindow<?php echo $j; ?> = new google.maps.InfoWindow({
                                        maxWidth: 300
                                    });
                                    marker<?php echo $j; ?>.addListener('click', function() {
                                        infowindow<?php echo $j; ?>.open(map, marker<?php echo $j; ?> );
                                    });

                                    infowindow<?php echo $j; ?>.setContent('<?php echo str_replace("'",'', preg_replace( "/\r|\n/", "", '<h3>'.$title.'</h3><p>'.$content.'</p>'.$attrInfo ) ); ?>');
                                    //infowindow.open(map, marker); //open by default

                                    <?php 
                                    }
                                } 
                            }
                        ?>
					}
                    
                    
                    
				</script>
                <div class="buttons">
                  <p><?php foreach($attrTypes as $attrType => $attrName){ echo '<a class="btn btn-info btn-sm" href="#'.$attrType.'" >'.$attrName.'</a> '; } ?></p>
                </div>
				<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRXeRhZCIYcKhtc-rfHCejAJsEW9rYtt4&callback=initMap" async ></script>
				<div id="map" class="support-area" ></div>
              </div>
                  <div class="attr-tiles">
                    
                    <?php 
                    
                    //print_r($attrTypes);

                    foreach($attrTypes as $attrType => $attrName){
                      echo '<a name="'.$attrType.'" class="pad-anchor" ></a>
                      <a href="#top" class="btn btn-sm btn-info pull-xs-right" >Top</a><h2 >'.$attrName.'</h2><hr>
                      <div class="row tile-container">';
                      //echo $attrType;
                      
                      $attrs = array(
                          'numberposts'	     => -1,
                          'offset'           => 0,
                          'order'            => 'ASC',
                          'orderby'   		 => 'menu_order',
                          'post_type'        => 'attraction',
                          'post_status'      => 'publish',		  
                          'tax_query'       => array(
                            array(
                              'taxonomy'    => 'type',
                              'field'       => 'slug',
                              'terms'       => $attrType,
                            )
                          ), 
                          /*'meta_query'	=> array(
                            'relation'		=> 'AND',
                            array(
                                'key'	 	=> 'area',
                                'value'	  	=> $post,
                                'compare' 	=> 'IN',
                            )
                          ),*/
                      );

                      $attrTiles = get_posts( $attrs );
                      foreach($attrTiles as $attrTile){
                        $attrid = $attrTile->ID;
                        $title = $attrTile->post_title;
                        $content = $attrTile->post_content;
                        $attrMeta = get_post_meta($attrid);
                        $areaLink = get_field('area',$attrid);

                        //print_r($attrTile);
                        
                                    

                        if($areaLink[0]->post_name == $post->post_name ){
                        ?>
                        <div class="col-sm-4 fp-featured">
                          <div class="attr-container fp-outer">
                            <div class="fp-title">
                              <h3><?php echo $title; ?></h3>
                            </div>
                            <div class="fp-desc">
                              <p class="description"><?php echo $content; ?></p>
                            </div>
                            <div class="fp-details">
                              <?php 
                                if($attrMeta['attraction_info_phone_number'][0]!=''){ 
                                  echo '<p class="phone"><strong>'.$attrMeta['attraction_info_phone_number'][0].'</strong></p>';
                                }
                                if($attrMeta['attraction_info_address'][0]!=''){ 
                                  echo '<p class="address"><strong>'.$attrMeta['attraction_info_address'][0].'</strong></p>';
                                }
                                if($attrMeta['attraction_info_website'][0]!=''){ 
                                  echo '<p class="website"><strong><a class="btn btn-sm btn-info" href="'.$attrMeta['attraction_info_website'][0].'" target="_blank" >VISIT WEBSITE</a></strong></p>';
                                }
                              ?>
                            </div>
                          </div>
                        </div>
                        <?php
                        }
                      }
                      
                      echo '</div>';

                    }

                    ?>
                    
                  </div>
                  
                
                <div class="row no-gutter">
                    <?php

                    //print_r($GLOBALS['wp_scripts']);

                    //Request Photos tied to Google Place
                    if(is_array($detailPhotos)){
                        foreach($detailPhotos as $photoRef){
                            $refNum = $photoRef['photo_reference'];
                            $reqUrl = 'https://maps.googleapis.com/maps/api/place/photo?photoreference='.urlencode($refNum).'&maxwidth=1600&key='.$apiKey;
                            //echo $reqUrl.'<br>';
                            echo '<div class="col-md-6 col-lg-4 col-xl-3">
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <a href="'.$reqUrl.'" data-toggle="lightbox" data-gallery="area-gallery" data-type="image" data-width="800" >
                                        <img src="'.$reqUrl.'" class="img-fluid" alt="Photo by '.strip_tags($photoRef['html_attributions'][0]).'" >
                                        </a>
                                    </div>
                                </div>';
                            //$photoRequest = json_decode(file_get_contents($reqUrl), true);

                        }
                    }

                    ?>
                </div>
				
			</div>
			<div class="col-md-4 col-lg-3">
				<?php if($area_thumb_url!=''){ ?>
				<div class="area-photo">
					<img src="<?php echo $area_thumb_url; ?>" class="img-fluid" >
				</div>
				<?php } ?>
				<?php if($areaContent['tripadvisor_module_tripadvisor'][0]!='' && $areaContent['tripadvisor_module_display_tripadvisor'][0] == 'on'){ ?>
				<div class="tripadvisor">
					<?php echo $areaContent['tripadvisor_module_tripadvisor'][0]; ?>
				</div>
				<style>
				div#CDSWIDSSP {
					width: 100% !important;
				}
				</style>
				<?php } ?>
			</div>
			
		</div>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'coastlinevr' ),
				'after'  => '</div>',
			) );
		?>
	</div>
</article><!-- #post-## -->
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script>
  $(window).load(function() {
    $('.tile-container').masonry({
      // options
      itemSelector: '.fp-featured',
    });
  });
</script>
