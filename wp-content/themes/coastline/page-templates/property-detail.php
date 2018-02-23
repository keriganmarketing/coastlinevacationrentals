<?php
// Exit if accessed directly
if ( ! defined('ABSPATH')) {
    exit;
}

/**
 * Template Name: Property Detail Page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package kigo-blank
 */

get_header();

$data = get_post_meta(get_the_ID(), 'bapi_property_data', true);

if (isset($_GET['debug']) && $_GET['debug'] == 'raw') {
    echo "Raw data:<pre>";
    print_r($data);
    echo "</pre>";
}

$data    = json_decode($data);
$context = $data->ContextData;

if (isset($_GET['debug'])) {
    echo "Decoded data:<pre>";
    print_r($data);
    echo "</pre>";
}

$translations = getbapitextdata();

global $bapi_all_options;
//$settings = json_decode($bapi_all_options['bapi_sitesettings']);
$settings = get_option('bapi_sitesettings_raw');

if (isset($_GET['debug']) && $_GET['debug'] == 'data') {
    echo "Debug data ...<pre>";
    print_r($settings);
    echo "</pre>";
}

if (isset($_GET['debug']) && $_GET['debug'] == 'session') {
    echo "Session stuff ...<pre>";
    print_r($_SESSION);
    echo "</pre>";
}
?>

    <div id="support-mast">

    </div>

    <div id="mid">
        <div id="primary" class="content-area bapi-entityadvisor" data-pkid="<?php echo $data->ID; ?>"
             data-entity="property">
            <main id="main" class="site-main" role="main">
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <div class="container">
                        <?php
                        if ($data) {
                        //echo '<pre>',print_r($context->Rates),'</pre>';

                        //Custom Vars
                        $propName    = $data->InternalName;
                        $propAddress = str_replace('<br>', ', ', $data->Location);
                        $propCity    = $data->City;
                        $propLat     = $data->Latitude;
                        $propLng     = $data->Longitude;
                        $propSleeps  = $data->Sleeps;
                        $propBeds    = $data->Bedrooms;
                        $propBaths   = $data->Bathrooms;
                        $propType    = $data->Type;
                        $propTopAmen = $data->Amenities[11]->Values;
                        $propLocAmen = $data->Amenities[12]->Values;
                        //$shortAmen = array_merge_recursive( $propTopAmen, $propLocAmen);
                        $propMinStay = $data->MinStay;
                        $topAmen     = array();
                        $amenArray   = array(
                            'View And Location',
                            'Suitability'
                        );
                        for ($i = 0; $i <= count($data->Amenities); $i++) {
                            if (in_array($data->Amenities[$i]->Key, $amenArray)) {
                                if (is_array($data->Amenities[$i]->Values)) {
                                    foreach ($data->Amenities[$i]->Values as $val) {
                                        $topAmen[] = $val->Label;
                                    }
                                }
                            }
                        }

                        $fromRatePre = $data->ContextData->Quote->QuoteDisplay->prefix;
                        $fromRate    = $data->ContextData->Quote->QuoteDisplay->value;
                        $fromRateSuf = $data->ContextData->Quote->QuoteDisplay->suffix;

                        $propAvail = $data->ContextData->Availability;

                        if (is_array($propAvail) and count($propAvail) > 0) {
                            foreach ($propAvail as $pa) {
                                //$pa->CheckOut = date('Y-m-d H:i:s', strtotime($pa->CheckOut . ' + 1 day'));
                                $propAdjusted[] = array(
                                    'CheckIn'  => $pa->CheckIn = date('Y-m-d', strtotime($pa->CheckIn)),
                                    'CheckOut' => $pa->CheckOut = date('Y-m-d', strtotime($pa->CheckOut))
                                );

                                $in              = explode('T', $pa->CheckIn);
                                $out             = explode(' ', $pa->CheckOut);
                                $propCheckins[]  = $in[0];
                                $propCheckouts[] = $out[0];
                            }
                        }


                        $bookingUrl = $data->ContextData->SEO->BookingURL;

                        $placeLat = $data->Latitude;
                        $placeLng = $data->Longitude;
                        ?>

                        <?php //print_r($propCheckins); ?>
                        <?php //print_r($propCheckouts); ?>
                        <?php //print_r($propAdjusted); ?>
                        <div class="entry-content">
                            <h1 class="title"><?php echo $propName; ?></h1>
                            <div class="row headline">
                                <div class="col-md-9">
                                    <p><strong><?php echo $data->Headline; ?></strong></p>
                                </div>
                                <div class="col-md-3">
                                    <div class="book-now">
                                        <a class="btn btn-info btn-lg btn-block" href="<?php echo $bookingUrl; ?>">Book Now</a>
                                    </div>
                                </div>
                            </div>
                            <div class="top-feat-box">
                                <div class="prop-feat-blocks">
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-4 col-md-2">
                                            <div class="feat-block">
                                                <strong>Type</strong>
                                                <?php echo $data->Type; ?>
                                            </div>
                                        </div>

                                        <?php if ($minstay = $data->MinStay) : ?>
                                            <div class="col-xs-6 col-sm-4 col-md-2">
                                                <div class="feat-block">
                                                    <strong>Min Stay</strong>
                                                    <?php echo ($minstay < 7 ? $minstay . ' nights' : 'weekly'); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($beds = $data->Bedrooms) : ?>
                                            <div class="col-xs-6 col-sm-4 col-md-2">
                                                <div class="feat-block">
                                                    <strong>Beds</strong>
                                                    <?php echo $beds; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>


                                        <?php if ($baths = $data->Bathrooms) : ?>
                                            <div class="col-xs-6 col-sm-4 col-md-2">
                                                <div class="feat-block">
                                                    <strong>Baths</strong>
                                                    <?php echo $baths; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($sleeps = $data->Sleeps) : ?>
                                            <div class="col-xs-6 col-sm-4 col-md-2">
                                                <div class="feat-block">
                                                    <strong>Sleeps</strong>
                                                    <?php echo $sleeps; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($sqft = $data->AdjLivingSpace) : ?>
                                            <div class="col-xs-6 col-sm-4 col-md-2">
                                                <div class="feat-block">
                                                    <strong>Sqft</strong>
                                                    <?php echo number_format($sqft); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8 col-lg-9"> <!-- left side -->
                                <div class="property-slider">
                                    <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                                        <div class="carousel-inner" role="listbox">
                                            <?php $imgCount = 1;
                                            foreach ($data->Images as $img) { ?>

                                                <div class="carousel-item <?php if ($imgCount == 1) { ?>active<?php } ?>">
                                                    <img src="<?php echo $img->OriginalURL; ?>"
                                                         alt="<?php echo $img->Caption; ?>">
                                                    <?php if ($img->Caption) { ?>
                                                        <div class="carousel-caption">
                                                            <p><?php echo $img->Caption; ?></p>
                                                        </div>
                                                    <?php } ?>
                                                </div>

                                                <?php $imgCount++;
                                            } ?>
                                        </div>

                                        <a class="left carousel-control" href="#carousel-example-generic" role="button"
                                           data-slide="prev">
                                            <span class="icon-prev" aria-hidden="true"></span>
                                            <span class="sr-only">Previous</span>
                                        </a>
                                        <a class="right carousel-control" href="#carousel-example-generic" role="button"
                                           data-slide="next">
                                            <span class="icon-next" aria-hidden="true"></span>
                                            <span class="sr-only">Next</span>
                                        </a>
                                        <!--<ol class="carousel-indicators">
                                        </ol>-->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-3 text-xs-center text-sm-left"> <!-- right side -->
                                <div class="row">
                                    <div class="col-xs-12 col-sm-6 col-md-12">
                                        <div class="location">
                                            <p class="address"><?php //echo $propAddress; ?></p>
                                        </div>

                                        <div class="big-rate">
                                            <em class="small">Rates <?php echo $fromRatePre; ?></em>
                                            <span class="ratenum"><?php echo $fromRate; ?></span><span
                                                    class="ratesuf">/<?php echo $fromRateSuf; ?></span>
                                        </div>


                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-12">
                                        <div class="details">
                                            <div class="get-quote" >
                                                <h3>Instant Quote</h3>
                                                <?php
                                                global $bapi_all_options;

                                                //Get Solution Config
                                                $soldata        = json_decode(wp_unslash($bapi_all_options['bapi_solutiondata']),
                                                    true);
                                                $solutionConfig = json_decode($soldata['Config'], true)['result'];

                                                //test($solutionConfig);

                                                $key = $bapi_all_options['api_key'];

                                                $config = unserialize($bapi_all_options['bapi_sitesettings_raw']);

                                                //Get search page url with bapi_page_id == bapi_search
                                                $args  = array(
                                                    'fields'         => 'ids',
                                                    'meta_query'     => array(
                                                        array(
                                                            'key'   => 'bapi_page_id',
                                                            'value' => 'bapi_search'
                                                        )
                                                    ),
                                                    'post_type'      => 'page',
                                                    'posts_per_page' => 1
                                                );
                                                $posts = get_posts($args);

                                                $action = get_page_link($posts[0]);


                                                echo $before_widget;
                                                if ( ! empty($title)) {
                                                    echo $before_title . "<span class='glyphicons search'><i></i>" . $title . "</span>" . $after_title;
                                                }
                                                ?>

                                                <?php //QUOTE FORM


                                                    $quote = '';
                                                    if(isset($_GET['quote'])){

                                                        $_SESSION['scheckin'] = $_GET['checkin'];
                                                        $_SESSION['scheckout'] = $_GET['checkout'];
                                                        $checkIn = explode('-',$_GET['checkin']);
                                                        $checkOut = explode('-',$_GET['checkout']);

                                                        $interval = new DateInterval('P1D');
                                                        $begin = new DateTime($checkIn[2] . '-' . $checkIn[0] . '-' . $checkIn[1] );
                                                        $end = new DateTime($checkOut[2] . '-' . $checkOut[0] . '-' . $checkOut[1] );
                                                        $end = $end->modify('-1 day');

                                                        $dateRange = new DatePeriod($begin, $interval ,$end);

                                                        $quotedDays = [];
                                                        foreach ($dateRange as $date) {
                                                            $quotedDays[] = $date->format("Ymd");
                                                        }
                                                        $numDays = count($quotedDays);

                                                        //echo '<pre>',print_r($quotedDays),'</pre>';

                                                        $allRates = [];
                                                        foreach($context->Rates->Values as $rate){
                                                            $rateStart = new DateTime($rate[0]);
                                                            $rateEnd = new DateTime($rate[1]);
                                                            $rateRange = new DatePeriod($rateStart, $interval ,$rateEnd);
                                                            $dailyRate = ($numDays == 6 ? $rate[3] : $rate[2]);
                                                            foreach($rateRange as $date){
                                                                if(in_array($date->format("Ymd"),$quotedDays)){
                                                                    $number = explode('.',preg_replace('/[$,]/','',$dailyRate));
                                                                    $allRates[] = $number[0];
                                                                }
                                                            }
                                                        }

                                                        //echo '<pre>',print_r($allRates),'</pre>';

                                                        $quote = ($numDays == 6 ? $allRates[0] : array_sum($allRates));
                                                        if($quote == 0){
                                                            $quote = 'N/A';
                                                        }else{
                                                            $quote = '$' . number_format($quote);
                                                        }

                                                    }
                                                ?>
                                                <form name="property-search" method="get" action="">
                                                    <?php if ($config['checkinoutmode'] != 0) {
                                                        $options = array(
                                                            'mindate'        => $solutionConfig['minbookingdate'],
                                                            'maxdate'        => $solutionConfig['maxbookingdate'],
                                                            'maxbookingdays' => $solutionConfig['maxbookingdays']
                                                        );

                                                        $options = json_encode($options);
                                                        ?>
                                                        <div class="category-block inner-addon left-addon">
                                                            <input id="rateblockcheckin" name="checkin"
                                                                   data-min-stay="<?php echo $data->MinStay+1; ?>"
                                                                   type="text" value=""
                                                                   class="txtb quicksearch sessioncheckin datepickercheckin makebookingcheckin datepicker form-control"
                                                                   data-field="scheckin"
                                                                   data-options='<?php echo $options; ?>'
                                                                   placeholder="<?php echo $textdata['Check-In']; ?>"/>
                                                            <span class="halflings calendar cal-icon-trigger"><i></i></span>
                                                        </div>
                                                    <?php } ?>

                                                    <?php if ($config['checkinoutmode'] != 0) { ?>
                                                        <?php if ($config['checkinoutmode'] == 1) { ?>
                                                            <div class="category-block inner-addon left-addon">
                                                                <input id="rateblockcheckout" name="checkout" type="text"
                                                                       value=""
                                                                       class="txtb quicksearch sessioncheckout datepickercheckout makebookingcheckout datepicker form-control"
                                                                       data-field="scheckout"
                                                                       placeholder="<?php echo $textdata['Check-Out']; ?>"/>
                                                                <span class="halflings calendar cal-icon-trigger"><i></i></span>
                                                            </div>
                                                        <?php } ?>

                                                        <?php if ($config['checkinoutmode'] == 2 && ! empty($solutionConfig['los']['values'])) { ?>
                                                            <div class="category-block">
                                                                <select name="los"
                                                                        class="span12 property-search-input quicksearch sessionlos"
                                                                        data-field="los">
                                                                    <option value=""><?php if ($nights = $textdata['Nights']) {
                                                                            echo $nights;
                                                                        } else {
                                                                            echo $solutionConfig['los']['prompt'];
                                                                        } ?></option>
                                                                    <?php
                                                                    foreach ($solutionConfig['los']['values'] as $value) {
                                                                        echo sprintf('<option value="%s" %s>%s</option>',
                                                                            $value['Data'],
                                                                            $config['deflos'] == $value['Data'] ? 'selected="true"' : '',
                                                                            $value['Label']);
                                                                    } ?>
                                                                </select>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>

                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <div class="input-group-text">Adults</div>
                                                        </div>
                                                        <input id="adults" name="adults" type="text" value="2"
                                                               class="form-control" placeholder=""/>
                                                    </div>
                                                    <div class="input-group">
                                                        <div class="input-group-addon">
                                                            <div class="input-group-text">Children</div>
                                                        </div>
                                                        <input id="children" name="children" type="text" value="0"
                                                               class="form-control" placeholder=""/>
                                                        <input type="hidden" name="quote" value="true" >
                                                    </div>
                                                    <?php if ( ! isset($_GET['quote'])) { ?>
                                                        <button type="submit" class="btn btn-primary">Get Quote</button>
                                                    <?php } else { ?>

                                                        <div class="your-quote big-rate">
                                                            <em class="small">This Stay</em>
                                                            <span class="ratenum"><?php echo $quote; ?></span> <span class="ratesuf">+ tax & fees</span>
                                                            <span style="display: block;" class="nights"><?php echo $numDays; ?> nights</span>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Update Quote</button>
                                                        <a class="btn btn-info" href="<?php echo $bookingUrl; ?>">Book Now</a>
                                                    <?php } ?>
                                                </form>
                                            </div>
                                        </div>
                                        <?php if ($data->NumReviews > 0) { ?>
                                            <div class="starsreviews">
                                                <h3>Average Rating</h3>
                                                <p><?php echo round($data->AvgReview,
                                                        1, PHP_ROUND_HALF_UP); ?> / 5 <span id="rateYo"></span></p>

                                                <script>
                                                    $(function () {

                                                        $("#rateYo").rateYo({
                                                            rating: <?php echo $data->AvgReview; ?>,
                                                            ratedFill: "#FBDD68",
                                                            normalFill: "#1F91B0",
                                                            readOnly: true,
                                                            starWidth: "25px"
                                                        });

                                                    });
                                                </script>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="property-detail-section">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#description" role="tab" data-toggle="tab">Property
                                        Details</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#rates" role="tab" data-toggle="tab">Rates</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#amenities" role="tab" data-toggle="tab">Amenities</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#photos" role="tab" data-toggle="tab">Photo Gallery</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#reviews" role="tab" data-toggle="tab">Reviews</a>
                                </li>

                            </ul>

                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane fade in active" id="description">
                                    <?php echo preg_replace("/\brn\b/", "", $data->Description); ?>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="rates">
                                    <?php if ($settings['propdetailratestable'] != 'on') { ?>
                                        <?php if ($context->Rates->Values) { ?>
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                <tr>
                                                    <?php foreach ($context->Rates->Keys as $key) {
                                                        echo "<th>" . $key . "</th>";
                                                    } ?>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($context->Rates->Values as $value) { ?>
                                                    <tr>
                                                        <?php foreach ($value as $v) {
                                                            echo "<td>" . $v . "</td>";
                                                        } ?>
                                                    </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                        <?php } else {
                                            echo $translations['No rates available'];
                                        } ?>
                                    <?php } ?>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="amenities">
                                    <?php if ( ! empty($data->Amenities)) { ?>
                                        <?php foreach ($data->Amenities as $amenity) { ?>
                                            <ul class="amenities-list unstyled clearfix">
                                                <li class="category-title"><?php echo $amenity->Key; ?></li>
                                                <?php foreach ($amenity->Values as $value) { ?>
                                                    <li>
                                                        <span class="halflings ok-sign"><i></i><?php echo $value->Label; ?></span>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                            <div class="clearfix"></div>
                                        <?php }
                                    } ?>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="photos">
                                    <div class="property-extra-photos">
                                        <div class="row no-gutter">
                                            <?php $imgCount = 1;
                                            foreach ($data->Images as $img) { ?>
                                                <div class="col-sm-6 col-md-4 col-lg-3 extra-photo-tiles">
                                                    <div class="responsive-embed responsive-embed-4by3">
                                                        <a href="<?php echo $img->OriginalURL; ?>"
                                                           data-gallery="listingGallery" data-toggle="lightbox"
                                                           data-type="image">
                                                            <img src="<?php echo $img->ThumbnailURL; ?>"
                                                                 alt="<?php echo $img->Caption; ?>" class="img-fluid">
                                                        </a>
                                                    </div>
                                                </div>
                                                <?php $imgCount++;
                                            } ?>
                                        </div>

                                    </div>
                                </div>
                                <div role="tabpanel" class="tab-pane fade" id="reviews">
                                    <?php if ($settings['propdetail-reviewtab']) { ?>
                                        <?php if ( ! $data->ContextData->Reviews) { ?>
                                            <p><?php _e('There are no reviews at this time.'); ?></p>
                                        <?php } else { ?>
                                            <div class="clearfix"></div>
                                            <?php foreach ($data->ContextData->Reviews as $review) { ?>
                                                <div class="row-fluid review">
                                                    <div class="span2 left-side">
                                                        <span class="glyphicons chat" href=""><i></i></span>
                                                        <h5 class="username"><?php echo $review->ReviewedBy->FirstName . " " . $review->ReviewedBy->LastName; ?></h5>
                                                    </div>
                                                    <div class="span10">
                                                        <h5 class="title"><?php echo $review->Title; ?></h5>
                                                        <div class="rating"><span
                                                                    class="reviewrating-<?php echo $review->Rating; ?>"></span>
                                                            <span><?php echo $translations['Posted on']; ?>
                                                                : <?php echo $review->SubmittedOn->ShortDate; ?></span>
                                                        </div>
                                                        <div class="comment">
                                                            <?php echo trim(strip_tags($review->Comment)); ?>
                                                        </div>
                                                        <?php
                                                        if ($responses = $review->Response) {
                                                            foreach ((array)$responses as $response) { ?>
                                                                <div class="response-block">
                                                                    <h5 class="response-title"><?php echo $translations['Response']; ?></h5>
                                                                    <div class="response"><?php echo strip_tags($response); ?></div>
                                                                </div>
                                                            <?php }
                                                        } ?>

                                                        <?php
                                                        if ($review->ExternalLink) {
                                                            foreach ($review->ExternalLink as $link) { ?>
                                                                <a class="full-rev-link" href="<?php echo $link; ?>"
                                                                   target="_blank"><?php echo $translations['See full review on']; ?>
                                                                    Flipkey</a><br/>
                                                            <?php }
                                                        } ?>
                                                        <?php if ($review->Source == 'FlipKey') {
                                                            echo '<a class="flipkeyPowered" rel="nofollow" target="_blank" href="//www.flipkey.com"><span></span></a>';
                                                        } ?>
                                                    </div>
                                                </div>
                                                <hr/>

                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                </div>


                            </div>
                        </div>
                        <div id="map" class="contact"></div>
                        <p></p>
                        <div class="availability-section">

                            <?php //if($settings['propdetailrateavailtab'] != 'on') { ?>
                            <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.16.0/moment.min.js"></script>
                            <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.js"></script>
                            <h3 class="avail-headline text-xs-center"><?php echo $translations['Availability']; ?></h3>
                            <p style="text-align:center; margin-top:30px;"><strong>Key:</strong> <span
                                        style="display: inline-block;height:20px;width:20px;margin: 0 0 -3px 10px;background-color: #f96868;"></span>
                                Booked <span
                                        style="display: inline-block;height:20px;width:20px;margin: 0 0 -3px 10px;background-color: #fff;border:1px solid #f96868;"></span>
                                Available <span
                                        style="display: inline-block;height:20px;width:20px;margin: 0 0 -3px 10px;background-color: #d31d1d;background-image: url(/wp-content/themes/coastline/img/changeover.png);background-size: cover;background-repeat: no-repeat;"></span>
                                Changeover</p>

                            <div class="row">
                                <div class="col-xs-12 text-xs-center">
                                    <input type="button" class="btn btn-info" id="myprevbutton"
                                           value="&nbsp;&#9668;&nbsp;"/> <input type="button" class="btn btn-info"
                                                                                id="mynextbutton"
                                                                                value="&nbsp;&#9658;&nbsp;"/>
                                </div>
                                <div class="col-md-6">
                                    <div id="calendar1"></div>
                                </div>
                                <div class="col-md-6">
                                    <div id="calendar2"></div>
                                </div>
                                <div class="col-md-6">
                                    <div id="calendar3"></div>
                                </div>
                                <div class="col-md-6">
                                    <div id="calendar4"></div>
                                </div>
                                <div class="col-md-6">
                                    <div id="calendar5"></div>
                                </div>
                                <div class="col-md-6">
                                    <div id="calendar6"></div>
                                </div>
                            </div>
                            <?php
                            $availability = isset($context->Availability) ? $context->Availability : false;

                            if ($availability) {
                                $availability = array_values(array_unique($availability, SORT_REGULAR));

                                $availability = [
                                    'result' => [
                                        [
                                            'ContextData' => [
                                                'Availability' => $availability
                                            ]
                                        ]
                                    ]
                                ];

                                ?>
                                <div id="avail" class="bapi-availcalendar"
                                     data-availability='<?php echo json_encode($availability); ?>'
                                     data-options='{ "availcalendarmonths": <?php echo isset($settings['propdetail-availcal']) ? $settings['propdetail-availcal'] : 3; ?>, "numinrow": 3 }'
                                     data-pkid="<?php echo $data->ID; ?>" data-rateselector="bapi-ratetable"></div>
                                <?php
                            } else {
                                //echo $translations['There are no more results'];
                            } ?>

                            <?php //} ?>

                            <script>

                                function initMap() {
                                    // Basic options for a simple Google Map
                                    // For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions
                                    var mapOptions = {
                                        // How zoomed in you want the map to start at (always required)
                                        zoom: 12,

                                        // The latitude and longitude to center the map (always required)
                                        center: new google.maps.LatLng(<?php if ($placeLat != '') {
                                            echo $placeLat . ',' . $placeLng;
                                        } ?>),

                                        // How you would like to style the map.
                                        // This is where you would paste any style found on Snazzy Maps.
                                        styles: [{
                                            "featureType": "water",
                                            "elementType": "all",
                                            "stylers": [{"hue": "#7fc8ed"}, {"saturation": 55}, {"lightness": -6}, {"visibility": "on"}]
                                        }, {
                                            "featureType": "water",
                                            "elementType": "labels",
                                            "stylers": [{"hue": "#7fc8ed"}, {"saturation": 55}, {"lightness": -6}, {"visibility": "off"}]
                                        }, {
                                            "featureType": "poi.park",
                                            "elementType": "geometry",
                                            "stylers": [{"hue": "#83cead"}, {"saturation": 1}, {"lightness": -15}, {"visibility": "on"}]
                                        }, {
                                            "featureType": "landscape",
                                            "elementType": "geometry",
                                            "stylers": [{"hue": "#f3f4f4"}, {"saturation": -84}, {"lightness": 59}, {"visibility": "on"}]
                                        }, {
                                            "featureType": "landscape",
                                            "elementType": "labels",
                                            "stylers": [{"hue": "#ffffff"}, {"saturation": -100}, {"lightness": 100}, {"visibility": "off"}]
                                        }, {
                                            "featureType": "road",
                                            "elementType": "geometry",
                                            "stylers": [{"hue": "#ffffff"}, {"saturation": -100}, {"lightness": 100}, {"visibility": "on"}]
                                        }, {
                                            "featureType": "road",
                                            "elementType": "labels",
                                            "stylers": [{"hue": "#bbbbbb"}, {"saturation": -100}, {"lightness": 26}, {"visibility": "on"}]
                                        }, {
                                            "featureType": "road.arterial",
                                            "elementType": "geometry",
                                            "stylers": [{"hue": "#ffcc00"}, {"saturation": 100}, {"lightness": -35}, {"visibility": "simplified"}]
                                        }, {
                                            "featureType": "road.highway",
                                            "elementType": "geometry",
                                            "stylers": [{"hue": "#ffcc00"}, {"saturation": 100}, {"lightness": -22}, {"visibility": "on"}]
                                        }, {
                                            "featureType": "poi.school",
                                            "elementType": "all",
                                            "stylers": [{"hue": "#d7e4e4"}, {"saturation": -60}, {"lightness": 23}, {"visibility": "on"}]
                                        }]
                                    };

                                    // Get the HTML DOM element that will contain your map
                                    // We are using a div with id="map" seen below in the <body>
                                    var mapElement = document.getElementById('map');

                                    // Create the Google Map using our element and options defined above
                                    var map = new google.maps.Map(mapElement, mapOptions);

                                    // Let's also add a marker while we're at it
                                    var marker = new google.maps.Marker({
                                        position: new google.maps.LatLng(<?php if ($placeLat != '') {
                                            echo $placeLat . ',' . $placeLng;
                                        } ?>),
                                        map: map,
                                        icon: '<?php echo get_template_directory_uri() ?>/img/map-location-pin.png'
                                    });

                                }


                                $(document).ready(function () {

                                    var phpArray = <?php echo json_encode($propAdjusted); ?>;
                                    console.info(phpArray);
                                    var events = []; //The array
                                    var checkoutsArray = <?php echo($propCheckouts ? json_encode($propCheckouts) : '[]'); ?>;
                                    var checkinsArray = <?php echo($propCheckins ? json_encode($propCheckins) : '[]'); ?>;
                                    //console.info(checkinsArray);
                                    //console.info(checkoutsArray);

                                    var checkins = [];
                                    for (var i = 0; i < checkinsArray.length; i++) {
                                        var checkin2 = checkinsArray[i];
                                        checkins.push(checkin2);
                                    }

                                    var checkouts = [];
                                    for (var i = 0; i < checkoutsArray.length; i++) {
                                        var checkout2 = checkoutsArray[i];
                                        checkouts.push(checkout2);
                                    }

                                    console.info(checkins);
                                    console.info(checkouts);

                                    if (phpArray != null) {
                                        for (var i = 0; i < phpArray.length; i++) {

                                            var checkin = phpArray[i].CheckIn;
                                            var checkout = phpArray[i].CheckOut;

                                            events.push({
                                                start: checkin,
                                                end: checkout + 'T24:00:00+00:00',
                                                title: 'Booked',
                                                className: 'booked',
                                                allDay: true,
                                                rendering: 'background',
                                                overlap: true,
                                                backgroundColor: '#f96868'
                                            });

                                        }
                                    }

                                    for (var i = 0; i < checkins.length; i++) {

                                        var checkin = checkins[i];
                                        var isnotStart = $.inArray(checkin, checkouts);

                                        if (isnotStart > -1) {

                                            events.push({
                                                start: checkin,
                                                end: checkin + 'T24:00:00+00:00',
                                                title: 'Booked',
                                                className: 'changeover',
                                                allDay: true,
                                                rendering: 'background',
                                                overlap: true,
                                                backgroundColor: '#d31d1d'
                                            });

                                        } else {

                                            events.push({
                                                start: checkin,
                                                end: checkin + 'T24:00:00+00:00',
                                                title: 'Booked',
                                                className: 'check-in',
                                                allDay: true,
                                                rendering: 'background',
                                                overlap: true,
                                                backgroundColor: '#f96868'
                                            });

                                        }
                                    }


                                    for (var i = 0; i < checkouts.length; i++) {

                                        var checkout = checkouts[i];
                                        var isnotEnd = $.inArray(checkout, checkins);

                                        if (isnotEnd > -1) {

                                            events.push({
                                                start: checkout,
                                                end: checkout + 'T24:00:00+00:00',
                                                title: 'Booked',
                                                className: 'changeover',
                                                allDay: true,
                                                rendering: 'background',
                                                overlap: true,
                                                backgroundColor: '#d31d1d'
                                            });

                                        } else {

                                            events.push({
                                                start: checkout,
                                                end: checkout + 'T24:00:00+00:00',
                                                title: 'Booked',
                                                className: 'check-out',
                                                allDay: true,
                                                rendering: 'background',
                                                overlap: true,
                                                backgroundColor: '#f96868'
                                            });

                                        }
                                    }

                                    //console.info(events);

                                    <?php

                                    date_default_timezone_set('America/New_York');
                                    $thismonth = date('Y-m-d');
                                    $nextmonth = strtotime('first day of next month');
                                    $intwomonths = strtotime('first day of second month');
                                    $inthreemonths = strtotime('first day of third month');
                                    $infourmonths = strtotime('first day of fourth month');
                                    $infivemonths = strtotime('first day of fifth month');
                                    $insixmonths = strtotime('first day of sixth month');
                                    ?>

//                                        console.log('<?php //echo $thismonth; ?>//');
//                                        console.log('<?php //echo $nextmonth; ?>//');
//                                        console.log('<?php //echo $intwomonths; ?>//');
//                                        console.log('<?php //echo $inthreemonths; ?>//');
//                                        console.log('<?php //echo $infourmonths; ?>//');
//                                        console.log('<?php //echo $infivemonths; ?>//');
//                                        console.log('<?php //echo $insixmonths; ?>//');

                                    $('#calendar1').fullCalendar({
                                        header: {
                                            left: '',
                                            center: 'title',
                                            right: ''
                                        },
                                        title: '<?php echo date('M'); ?>',
                                        events: events,
                                        aspectRatio: 1
                                    });
                                    $('#calendar2').fullCalendar({
                                        header: {
                                            left: '',
                                            center: 'title',
                                            right: ''
                                        },
                                        defaultDate: '<?php echo date('Y-m-d', $nextmonth); ?>',
                                        events: events,
                                        aspectRatio: 1
                                    });
                                    $('#calendar3').fullCalendar({
                                        header: {
                                            left: '',
                                            center: 'title',
                                            right: ''
                                        },
                                        defaultDate: '<?php echo date('Y-m-d', $intwomonths); ?>',
                                        events: events,
                                        aspectRatio: 1
                                    });
                                    $('#calendar4').fullCalendar({
                                        header: {
                                            left: '',
                                            center: 'title',
                                            right: ''
                                        },
                                        defaultDate: '<?php echo date('Y-m-d', $inthreemonths); ?>',
                                        events: events,
                                        aspectRatio: 1
                                    });
                                    $('#calendar5').fullCalendar({
                                        header: {
                                            left: '',
                                            center: 'title',
                                            right: ''
                                        },
                                        defaultDate: '<?php echo date('Y-m-d', $infourmonths); ?>',
                                        events: events,
                                        aspectRatio: 1
                                    });
                                    $('#calendar6').fullCalendar({
                                        header: {
                                            left: '',
                                            center: 'title',
                                            right: ''
                                        },
                                        defaultDate: '<?php echo date('Y-m-d', $infivemonths); ?>',
                                        events: events,
                                        aspectRatio: 1
                                    });

                                    $('#myprevbutton').click(function () {
                                        $('#calendar6').fullCalendar('prev');
                                        $('#calendar1').fullCalendar('prev');
                                        $('#calendar2').fullCalendar('prev');
                                        $('#calendar3').fullCalendar('prev');
                                        $('#calendar4').fullCalendar('prev');
                                        $('#calendar5').fullCalendar('prev');
                                    });
                                    $('#mynextbutton').click(function () {
                                        $('#calendar6').fullCalendar('next');
                                        $('#calendar1').fullCalendar('next');
                                        $('#calendar2').fullCalendar('next');
                                        $('#calendar3').fullCalendar('next');
                                        $('#calendar4').fullCalendar('next');
                                        $('#calendar5').fullCalendar('next');
                                    });


                                    initMap();
                                });


                            </script>
                        </div>


                    </div><!-- .entry-content -->
                    <?php } ?>
        </div>
        </article><!-- #post-## -->
        </main><!-- #main -->
    </div><!-- #primary -->
    </div>

<?php
get_footer();
