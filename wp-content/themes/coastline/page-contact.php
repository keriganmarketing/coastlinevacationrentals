<?php
/**
 * @package coastline_vr
 */

get_header(); ?>
<div id="support-mast">

</div>

<div id="mid">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
            
                <div class="container">
                    <h1>Contact Us</h1>
                    <div class="row">
                    <?php while ( have_posts() ) : the_post(); ?>
                        
                    
                        <div class="col-lg-7 col-xl-8">
                            <div class="row">
                                <div class="col-sm-6">
                                    <p>
                                        <?php if(get_field('phone_number')!=''){ ?>
                                        Phone: <?php echo get_field('phone_number'); ?>
                                        <?php } ?>
                                        <?php if(get_field('fax_number')!=''){ ?>
                                        <br>Fax: <?php echo get_field('fax_number'); ?>
                                        <?php } ?>
                                        <?php if(get_field('email_address')!=''){ ?>
                                        <br>Email: <a href="mailto:<?php echo get_field('email_address'); ?>" ><?php echo get_field('email_address'); ?></a>
                                        <?php } ?>
                                    </p>
                                </div>
                                <div class="col-sm-6">
                                    <p><?php echo get_field('address'); ?></p>
                                </div>
                            </div>
                            
                            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                                <div class="entry-content">
                                    <?php the_content(); ?>
                                </div><!-- .entry-content -->
                            </article><!-- #post-## -->

                            <div id="bapi-inquiryform" class="bapi-inquiryform" data-templatename="tmpl-contactus-form" data-log="0" data-shownamefield="1" data-showemailfield="1" data-showphonefield="1" data-showdatefields="0" data-shownumberguestsfields="0" data-showleadsourcedropdown="1" data-showcommentsfield="1"></div>
                            
                        </div>
                        <div class="col-lg-5 col-xl-4">
                            <script type="text/javascript">
                            // When the window has finished loading create our google map below
                            //google.maps.event.addDomListener(window, 'load', initMap);   
                            function initMap() {
                                // Basic options for a simple Google Map
                                // For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions
                                var mapOptions = {
                                    // How zoomed in you want the map to start at (always required)
                                    zoom: 6,

                                    // The latitude and longitude to center the map (always required)
                                    center: new google.maps.LatLng(29.814990, -85.296759),

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
                                    position: new google.maps.LatLng(29.814990, -85.296759),
                                    map: map,
                                    title: 'Area Info',
                                    icon: '<?php echo get_template_directory_uri() ?>/img/map-location-pin.png'
                                });

                                

                            }
                            </script>
				            <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCRXeRhZCIYcKhtc-rfHCejAJsEW9rYtt4&callback=initMap" async ></script>
                            <div id="map" class="contact"></div>
                        </div>
                    
                    <?php endwhile; // End of the loop. ?>
                    
                    </div>
                </div>

		</main><!-- #main -->
	</div><!-- #primary -->
</div>
<?php get_footer();
