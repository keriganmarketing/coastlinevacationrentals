<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package coastline_vr
 */

?>	<div id="sticky-footer" class="unstuck">
	<div id="bot">
		<div class="container no-gutter">
            <div class="row">
				<div class="col-xs-12">
					<div class="navbar-collapse collapse navbar-toggleable-xl" id="footer-navbar">
						<?php wp_nav_menu( array( 
							'theme_location' => 'footer-exp', 
							'menu_id' => 'footer-exp-menu',
							'menu_class' => 'menu',
							'container' => 'nav',
							'container_class' => 'navbar',
							'echo' => 'true',
							'depth' => 0,
							'items_wrap' => '<ul id="%1$s" class="nav navbar-nav">%3$s</ul>',
						) ); ?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="text-xs-center text-sm-left col-sm-8 col-md-5">
					<div class="mini-footer-navbar" id="mini-footer-navbar">
						<?php wp_nav_menu( array( 
							'theme_location' => 'footer', 
							'menu_id' => 'footer-menu',
							'menu_class' => 'menu',
							'container' => 'nav',
							'container_class' => 'navbar',
							'echo' => 'true',
							'depth' => 0,
							'items_wrap' => '<ul id="%1$s" class="nav navbar-nav">%3$s</ul>',
						) ); ?>
					</div>
				</div>
				<div class="col-sm-1 col-md-2 text-xs-center">
					<button class="footer-toggler" type="button" data-toggle="collapse" data-target="#footer-navbar">
						<span class="footer-expander">
							<span class="expander-bar"></span>
							<span class="expander-bar"></span>
						</span>
					</button>
				</div>
				<div class="text-xs-center text-md-right col-sm-12 col-md-4 pull-md-right">
					<div class="social">
                        <img id="vrmalogo" src="<?php echo get_template_directory_uri(); ?>/img/vrmalogo.png" alt="VRMA Member Company">
					<?php
						//print_r(getSocialLinks());
						$socialLinks = getSocialLinks();
						foreach($socialLinks as $socialId => $socialLink){
							echo '<a class="'.$socialId.'" href="'.$socialLink.'" target="_blank" ></a>';
						}
					?>
					</div>
				</div>
			</div>
			
		</div>
	</div>
	<div id="bot-bot">
		<div class="container no-gutter">
			<div class="row">
				<div class="col-xs-12 col-sm-8">
					<div class="site-info text-xs-center text-md-left">
						<p class="copyright">&copy;<?php echo date('Y'); ?> Coastline Vacation Rentals, All rights reserved.  |  <a href="/termsofuse/">Terms</a>  |  <a href="/privacypolicy/">Privacy</a></p>
					</div>
				</div>
				<div class="col-xs-12 col-sm-4">
					<div class="site-info text-xs-center">
						<p class="siteby pull-sm-right"><img id="kma" src="<?php echo get_template_directory_uri(); ?>/img/kma.svg" alt="Kerigan Marketing Associates"> Site by <a href="https://keriganmarketing.com">KMA</a>.</p>
					</div><!-- .site-info -->
				</div>
			</div>
		</div>
	</div>
	</div>
</div>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css" />
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.2.0/jquery.rateyo.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.0.0/ekko-lightbox.min.js" async ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.2.0/jquery.rateyo.min.js"></script>
<script>    
function setWeatherIcon(condid) {
  var icon = '';
      switch(condid) {
        case '0': icon  = 'wi-tornado';
          break;
        case '1': icon = 'wi-storm-showers';
          break;
        case '2': icon = 'wi-tornado';
          break;
        case '3': icon = 'wi-thunderstorm';
          break;
        case '4': icon = 'wi-thunderstorm';
          break;
        case '5': icon = 'wi-snow';
          break;
        case '6': icon = 'wi-rain-mix';
          break;
        case '7': icon = 'wi-rain-mix';
          break;
        case '8': icon = 'wi-sprinkle';
          break;
        case '9': icon = 'wi-sprinkle';
          break;
        case '10': icon = 'wi-hail';
          break;
        case '11': icon = 'wi-showers';
          break;
        case '12': icon = 'wi-showers';
          break;
        case '13': icon = 'wi-snow';
          break;
        case '14': icon = 'wi-storm-showers';
          break;
        case '15': icon = 'wi-snow';
          break;
        case '16': icon = 'wi-snow';
          break;
        case '17': icon = 'wi-hail';
          break;
        case '18': icon = 'wi-hail';
          break;
        case '19': icon = 'wi-cloudy-gusts';
          break;
        case '20': icon = 'wi-fog';
          break;
        case '21': icon = 'wi-fog';
          break;
        case '22': icon = 'wi-fog';
          break;
        case '23': icon = 'wi-cloudy-gusts';
          break;
        case '24': icon = 'wi-cloudy-windy';
          break;
        case '25': icon = 'wi-thermometer';
          break;
        case '26': icon = 'wi-cloudy';
          break;
        case '27': icon = 'wi-night-cloudy';
          break;
        case '28': icon = 'wi-day-cloudy';
          break;
        case '29': icon = 'wi-night-cloudy';
          break;
        case '30': icon = 'wi-day-cloudy';
          break;
        case '31': icon = 'wi-night-clear';
          break;
        case '32': icon = 'wi-day-sunny';
          break;
        case '33': icon = 'wi-night-clear';
          break;
        case '34': icon = 'wi-day-sunny-overcast';
          break;
        case '35': icon = 'wi-hail';
          break;
        case '36': icon = 'wi-day-sunny';
          break;
        case '37': icon = 'wi-thunderstorm';
          break;
        case '38': icon = 'wi-thunderstorm';
          break;
        case '39': icon = 'wi-thunderstorm';
          break;
        case '40': icon = 'wi-storm-showers';
          break;
        case '41': icon = 'wi-snow';
          break;
        case '42': icon = 'wi-snow';
          break;
        case '43': icon = 'wi-snow';
          break;
        case '44': icon = 'wi-cloudy';
          break;
        case '45': icon = 'wi-lightning';
          break;
        case '46': icon = 'wi-snow';
          break;
        case '47': icon = 'wi-thunderstorm';
          break;
        case '3200': icon = 'wi-cloud';
          break;
        default: icon = 'wi-cloud';
          break;
      }

      return '<i class="wi '+icon+'"></i>';
}
    
function stickFooter(){
    
    var bodyHeight = $('body').height(),
        windowHeight = $(window).height()+1;
    
    if ( bodyHeight < windowHeight ) {
		$('#sticky-footer').addClass("stuck");
		$('#sticky-footer').removeClass("unstuck");
	}else{
		$('#sticky-footer').removeClass("stuck");
		$('#sticky-footer').addClass("unstuck");
	}
    
    //console.log(windowHeight);
    //console.log(bodyHeight);
    
}
    
$(window).scroll(function() {
    
	if ($(this).scrollTop() > 100){  
		$('#top').addClass("smaller");
	}else{
		$('#top').removeClass("smaller");
	}
	 
    stickFooter();
    
});   
    
$(window).load(function() {
    
    stickFooter();
  
    var bodyHeight = $('body').height(),
        windowHeight = $(window).height()+1;
    
    if ( bodyHeight > windowHeight ) {
        $('body').addClass("autoheight");
        $('body').removeClass("fullheight");
	}else{
        $('body').addClass("fullheight");
		$('body').removeClass("autoheight");
	}
    	
	$(document).on('click', '[data-toggle="lightbox"]', function(event) {
		event.preventDefault();
		$(this).ekkoLightbox({
			alwaysShowClose: true,
			showArrows: true
		});
	}); 
    
    <?php
    function getWeather($loc){
        
        $BASE_URL = "http://query.yahooapis.com/v1/public/yql";
        $yql_query = 'select * from weather.forecast where woeid in (select woeid from geo.places(1) where text="'.$loc.'")';
        $yql_query_url = $BASE_URL . "?q=" . urlencode($yql_query) . "&format=json";
        
        if(!isset($_SESSION['weather'])){
            
          // Make call with cURL
          $session = curl_init($yql_query_url);
          curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
          $json = curl_exec($session);

          $_SESSION['weather'] = $json;          
          
        }else{
          
          $json = $_SESSION['weather']; 
          $jsonDecoded = json_decode($json,true);
          $timeSaved = date('Hi',strtotime($jsonDecoded['query']['created']));
          $now = date('Hi');
          $diff = (int)$now - (int)$timeSaved; 
           
          print('console.log(\'weather saved at '.$timeSaved.'\');');
          print('console.log(\'now = '.$now.'\');');
          print('console.log(\'diff = '.$diff.'\');');
          
          if($diff > 30){ //30 minutes have passed
            // Make call with cURL
            $session = curl_init($yql_query_url);
            curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
            $json = curl_exec($session);

            $_SESSION['weather'] = $json; 
          }
          
        }
        
        // Convert JSON to PHP object
        $phpObj =  json_decode($json,true);
        return $phpObj;
    }
    
    $weather = getWeather('Port St Joe, FL');
    $condition = $weather['query']['results']['channel']['item']['condition'];
    $location = $weather['query']['results']['channel']['location'];

    ?>
<?php if(isset($condition)){ ?>   
    $(".w-icon").html( setWeatherIcon("<?php echo $condition['code']; ?>") );
    $(".w-city").text( "Cape San Blas" );
    $(".w-temp").text( "<?php echo $condition['temp'];?>" + String.fromCharCode(176)  );
    $(".w-cond").text( "<?php echo $location['text']; ?>" );
<?php } ?>
    
//phone mask    
$("#txtPhone", "body")

.keydown(function (e) {
    var key = e.charCode || e.keyCode || 0;
    $phone = $(this);

    // Auto-format- do not expose the mask as the user begins to type
    if (key !== 8 && key !== 9) {
        if ($phone.val().length === 4) {
            $phone.val($phone.val() + ')');
        }
        if ($phone.val().length === 5) {
            $phone.val($phone.val() + ' ');
        }			
        if ($phone.val().length === 9) {
            $phone.val($phone.val() + '-');
        }
    }

    // Allow numeric (and tab, backspace, delete) keys only
    return (key == 8 || 
            key == 9 ||
            key == 46 ||
            (key >= 48 && key <= 57) ||
            (key >= 96 && key <= 105));	
})

.bind('focus click', function (e) {
    $phone = $(this);

    if ($phone.val().length === 0) {
        $phone.val('(');
    }
    else {
        var val = $phone.val();
        $phone.val('').val(val); // Ensure cursor remains at the end
    }
})

.blur(function (e) {
    $phone = $(this);

    if ($phone.val() === '(') {
        $phone.val('');
    }
});
    
});
    

</script>

<?php wp_footer(); ?>
</body>
</html>
