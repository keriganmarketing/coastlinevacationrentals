<?php
// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
/*
Template Name: Search Page
*/

get_header(); ?>


<div id="support-mast">

</div>

<div id="mid">
<div id="primary" class="content-area bapi-entityadvisor" data-pkid="<?php echo $data->ID; ?>" data-entity="property">
    <main id="main" class="site-main" role="main">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <div class="home-qsearch search-page">
                <script>
                    <?php //echo file_get_contents(get_template_directory_uri().'/js/bootstrap-datepicker.js'); //DATEPICKER ?>
                </script>
                <?php dynamic_sidebar( 'insta-home-qsearch' ); ?>
                <script>
                    $('.quicksearch-dosearch.btn').html('Start Your Getaway');
                    $('#searchcheckin').addClass('input-outline');
                    $('#searchcheckout').addClass('input-outline');
                    $('.quicksearch-dosearch.btn').addClass('btn-info');
                    $('.property-search-button-block.search-button-block.category-block').append('<a  class="btn btn-outline all-rentals-btn" href="/rentals/" >All Rentals</a>');
                </script>
            </div>

            <div class="container">
                <div class="row">
                    <div class="rentals">
                        
                        <?php //Get All Properties
                        
                        //Define Get Vars
                        $AREA = (isset($_GET['city']) ? $_GET['city'] : null);
                        $CHECKIN = (isset($_GET['checkin']) ? $_GET['checkin'] : null);
                        $CHECKOUT = (isset($_GET['checkout']) ? $_GET['checkout'] : null);
                        
                        //echo 'search: '.$AREA.', '.$CHECKIN.', '.$CHECKOUT;
                        
                        if($CHECKIN != ''){ $_SESSION['checkin'] = $CHECKIN; }
                        if($CHECKOUT != ''){ $_SESSION['checkout'] = $CHECKOUT; }
                      
                        $checkInParts = explode('-',$CHECKIN);
                        $CHECKINDATE = $checkInParts[2].$checkInParts[1].$checkInParts[0];
                      
                        $checkOutParts = explode('-',$CHECKOUT);
                        $CHECKOUTDATE = $checkOutParts[2].$checkOutParts[1].$checkOutParts[0];
                      
                       // echo $CHECKINDATE.', '.$CHECKOUTDATE;
                    
                        $imgSize = 'ThumbnailURL';
                        $args = array(
                            'meta_query' => array(
                                array(
                                    'key' 		=> 'bapikey',
                                    'value' 	=> 'property',
                                    'compare'	=> 'LIKE'
                                ),
                            ),
                            'post_type' => 'page',
                            'posts_per_page' => -1,
                            'orderby' => 'menu_order',

                        );

            
                    $posts = get_posts($args);
                        
                    function check_in_range($start_date, $end_date, $bookeddates) {
                        if(is_array($bookeddates)){
                          
                            foreach($bookeddates as $bookeddate){
                                //echo '<!--CHECKED: '.$start_date.' >> '.$end_date.'-->';
                              
                                $bookedCheckin = explode('T',$bookeddate['CheckIn']);
                                $bookedCheckin = explode('-',$bookedCheckin[0]);
                                $bookedCheckin = $bookedCheckin[0].$bookedCheckin[2].$bookedCheckin[1];
                                $bookedCheckOut = explode('T',$bookeddate['CheckOut']);
                                $bookedCheckOut = explode('-',$bookedCheckOut[0]);
                                $bookedCheckOut = $bookedCheckOut[0].$bookedCheckOut[2].$bookedCheckOut[1];
                              
                                //echo '<!--BOOKED: '.$bookedCheckin.' >> '.$bookedCheckOut.'-->';
                              
                                if( $start_date >= $bookedCheckin && $start_date <= $bookedCheckOut ) {
                                    //echo '<!--BOOKED IN: '.$bookedCheckin.' => CHECKED IN: '.$start_date.'-->';
                                    return FALSE;
                                }elseif( $end_date >= $bookedCheckin && $end_date <= $bookedCheckOut ) {
                                    //echo '<!--BOOKED OUT: '.$bookedCheckOut.' => CHECKED OUT: '.$end_date.'-->';
                                    return FALSE;
                                }else{
                                    //echo '<!--IN: '.$start_date.' => OUT: '.$end_date.' IS CLEAR-->';
                                    //echo '<!--BOOKED IN: '.$bookedCheckin.' => BOOKED OUT: '.$bookedCheckOut.'-->';
                                    return TRUE;
                                }
                            }
                        }
                    }

                    $results = [];
                    foreach($posts as $post) {
                        $data = json_decode( get_post_meta($post->ID, 'bapi_property_data')[0], true );
                        
                        $BOOKED = $data['ContextData']['Availability'];

                        if($data['City'] == $AREA || $AREA == '') { 
                            if(check_in_range($CHECKINDATE,$CHECKOUTDATE,$BOOKED) || ($CHECKINDATE == '' && $CHECKOUTDATE == '')){
                                $results[] = $data;
                            }
                        }
                      
                        
                    }

                    global $bapi_all_options;	
                    $textdata = getbapitextdata();		
                    $key = $bapi_all_options['api_key'];
                    $config = unserialize( $bapi_all_options['bapi_sitesettings_raw'] );
                    $seo = json_decode(get_option('bapi_keywords_array'), true);

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
                            if(count($results)>0){
                                foreach($results as $i => $f) {
                                    include(locate_template('template-parts/listing-tile.php'));
                                }
                            }else{  ?>
                                <div class="col-xs-12">
                                    <p>There are currently no rentals available for the search criteria you entered. Please try another search or view <a href="/rentals/">all rentals</a>. </p>
                                </div>
                            <?php } ?>
                        </div>
                        
                        
                    </div>
                </div>
                
                </div>
            
        </article><!-- #post-## -->
    </main><!-- #main -->
</div><!-- #primary -->
</div>

<?php get_footer(); ?>