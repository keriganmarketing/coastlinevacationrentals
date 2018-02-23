<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package coastline_vr
 */

get_header(); ?>
<div id="mast">
	<div class="container-fluid no-gutter">	
		<div class="home-slideshow">
			<?php echo do_shortcode('[getslider]'); ?>
		</div>
		
	</div>
	<div class="container no-gutter stretch-mobile">
		<?php if ( is_active_sidebar('insta-home-qsearch' ) ) : ?>	
		<div class="row">
			<div class="offset-xl-1 col-xl-10" >
				<div class="home-qsearch">

					<?php dynamic_sidebar( 'insta-home-qsearch' ); ?>	
					<script>
					$('.quicksearch-dosearch.btn').html('Start Your Getaway');
					$('#searchcheckin').addClass('input-outline');
					$('#searchcheckout').addClass('input-outline');
					$('.quicksearch-dosearch.btn').addClass('btn-info');
					$('.property-search-button-block.search-button-block.category-block').append('<a  class="btn btn-outline all-rentals-btn" href="/rentals/" >All Rentals</a>');	
                        
					</script>
				</div>
			</div>
		
		<?php endif; ?>
	</div>
</div>
<div id="mid">
	<div class="container">
		<div id="primary" class="content-area">
			<main id="main" class="site-main">

				<?php
				while ( have_posts() ) : the_post(); ?>

					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						
						<div class="row">
							<div class="col-xs-12" >
								<div class="entry-content text-xs-center">

									<?php
										the_content();
									?>
									
								</div>

							</div>
						</div>

					</article>

				<?php endwhile; // End of the loop.
				?>

			</main><!-- #main -->
		</div><!-- #primary -->
	</div>
</div>
<div id="featured-properties" class="text-xs-center">
	<div class="container">
		<div class="row">
            <?php if ( is_active_sidebar('insta-home-qsearch' ) ) : ?>	
			<div class="home-fprop"> 
                <?php 

                //extract($args);
                $feats = new BAPI_Featured_Properties(); 
                $instance = $feats->get_settings();
                $instance = $instance[2];

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
                  
                    function tokenTruncate($string, $your_desired_width) {
                      $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
                      $parts_count = count($parts);

                      $length = 0;
                      $last_part = 0;
                      for (; $last_part < $parts_count; ++$last_part) {
                        $length += strlen($parts[$last_part]);
                        if ($length > $your_desired_width) { break; }
                      }

                      return implode(array_slice($parts, 0, $last_part));
                    }
                  
                    $count = 0;
                    $textcutoff = 85;
                  
                    foreach($featured as $i => $f) {
                        $quote = $f['ContextData']['Quote'];
                        $descParts = strip_tags($f['Headline']);
                        //print_r($descParts[0]);
                        $intro = tokenTruncate($descParts, $textcutoff);
                        if(strlen($intro) < $textcutoff){
                          //$intro .='...';
                        }
                      
                    ?>
 

                    <?php
                        include(locate_template('template-parts/mini-listing.php'));
                    }
                    ?>
                    
                </div>

                <?php } ?>
            </div>
            <?php endif; ?>
		</div>
	</div>
	<a class="btn btn-primary" href="/rentals/" >View All Rentals</a>
</div>
<div id="featured-testimonial">
  <div class="container">
    <div class="row">
      <div class="col-sm-8 col-xl-9" >
        <div class="qcontent">
          <p class="qtext"><?php echo get_field('text',5); ?></p>
          <p class="author">&mdash; <?php echo get_field('author',5); ?></p>
        </div>
      </div>
      <div class="col-sm-4 col-xl-3 text-xs-center" ><a href="/owner-testimonials/" class="btn btn-outline" >More Owner Testimonials</a></div>
    </div>
  </div>  
</div>
<div id="quick-contact">
	<div class="container">
		<div class="row">
			<div class="col-md-10 col-lg-8 col-xl-7">
				<h2>contact us</h2>
				<h3>Vacation Rentals + More</h3>
				<?php include('contactform.php'); ?>
			</div>
		</div>
	</div>
</div>
<div id="areas">
	<h2>Explore the area</h2>
<?php 
	$request = array(
		  'posts_per_page'   => -1,
		  'offset'           => 0,
		  'order'            => 'ASC',
		  'orderby'   		 => 'menu_order',
		  'post_type'        => 'area',
		  'post_status'      => 'publish',		  
	);
	
	$arealist = get_posts( $request );

	$i = 0;
	foreach($arealist as $area){
		$areaid = $area->ID;
		$title = $area->post_title;
		$link = get_permalink($areaid);
		$post_thumbnail_id = get_post_thumbnail_id($areaid);
        $area_thumb_url = wp_get_attachment_url( $post_thumbnail_id, 'area-thumbnail' ); 
		$i++;
		
		echo '<div class="area-tile tile-'.$i.'" style="background-image:url('.$area_thumb_url.');">';
		echo '<div class="overlay"><h3>'.$title.'</h3>';
		echo '<p>more about the area</p><span class="conch"></span><span class="shell"></span><span class="starfish"></span></div>';
		echo '<a href="'.$link.'" class="area-tile-click" ></a>';
		echo '</div>';		
	} 
?>
</div>
<?php get_footer();
