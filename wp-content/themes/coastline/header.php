<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package coastline_vr
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<title>
<?php 
  $bapi_meta_title = get_post_meta($post->ID,'bapi_meta_title', true); 
  if($bapi_meta_title != ''){ 
    echo $bapi_meta_title;
  }else{
    wp_title('');
  }
?>
</title>  
<?php $themeUrl = get_template_directory_uri(); ?>
    
<?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
<!--[if lt IE 9]>
<script src="<?php echo $themeUrl; ?>/insta-common/js/html5.js" type="text/javascript"></script>
<![endif]-->
    
<?php wp_head(); ?>

<style type="text/css">
<?php echo file_get_contents(wp_normalize_path(get_template_directory().'/fonts.css')); //BOOTSTRAP CSS ?>
</style>
<style type="text/css">
<?php echo file_get_contents('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/css/bootstrap.min.css'); //BOOTSTRAP CSS ?>
</style>
<style type="text/css">
<?php echo file_get_contents(wp_normalize_path(get_template_directory().'/css/weather-icons.min.css')); //WEATHER ICONS CSS ?>
</style>
<style type="text/css">
<?php echo file_get_contents('https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.0.0/ekko-lightbox.min.css'); //LIGHTBOX ?>
</style>
<style>
<?php echo file_get_contents(wp_normalize_path(get_template_directory().'/inline.css')); //CUSTOM ABOVE THE FOLD ?>
</style>
<style type="text/css">
<?php echo file_get_contents('https://fonts.googleapis.com/css?family=Abel'); //CUSTOM ABOVE THE FOLD ?>
</style>

<script>
<?php echo file_get_contents('https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.4/js/tether.min.js'); //TETHER JS ?>
</script>
<script>
<?php echo file_get_contents('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.3/js/bootstrap.min.js'); //BOOTSTRAP JS ?>
</script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/turbolinks/5.0.0/turbolinks.min.js"></script>-->
</head>
    

    
<body <?php body_class(); ?> >
<div id="page" class="site">
	<a style="display:none;" class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'coastlinevr' ); ?></a>
	<div id="top-top">
		<div class="container">
			<div class="row">
				
                <div class="col-md-3 pull-sm-right text-xs-right">
                  <div class="topsearch" >
                    <form id="searchform" method="get" action="/" class="form form-inline">
                      <label class="sr-only" for="s">Search our site</label>
                      <div class="form-group">
                      <input name="s" id="s" class="form-control form-control-sm" placeholder="Search our site" style="width:75%;" >
                      <input value="Go" class="btn btn-info btn-sm xs-hidden" type="submit" style="width:20%;" >
                      </div>
                    </form>
                  </div>
                </div>
              
                <div class="col-xs-6 col-md-3">
					<div class="social social-top text-xs-left">
					<?php
						//print_r(getSocialLinks());
						$socialLinks = getSocialLinks();
						foreach($socialLinks as $socialId => $socialLink){
							echo '<a class="'.$socialId.'" href="'.$socialLink.'" target="_blank" ></a>';
						}
					?>
					</div>
				</div>
              
				<div class="col-md-3 hidden-xs-down">
					<div class="weather">                        
                        <span class="w-city"></span>
						<span class="w-icon"></span>
						<span class="w-temp"></span>
						<span class="w-cond"></span>
                    </div>
				</div>

				<div class="col-xs-6 col-md-3 text-xs-right text-sm-right">
                    <span class="top-phone"><a href="tel:850-227-3330">850-227-3330</a></span>
				</div>

			</div>
		</div>
	</div>
    <div id="top">
        <header id="masthead" class="site-header">
			
				<div class="container no-gutter">
					<div class="hidden-lg-up">
					
						<div class="navbar-collapse collapse navbar-toggleable-md" id="mobile-navbar">
							<?php wp_nav_menu( array( 
								'theme_location' => 'primary-mobile', 
								'menu_id' => 'mobile-menu',
								'menu_class' => 'menu',
								'container' => 'nav',
								'container_class' => 'navbar',
								'echo' => 'true',
								'depth' => 0,
								'items_wrap' => '<ul id="%1$s" class="nav navbar-nav">%3$s</ul>',
							) ); ?>
						</div>
						<button class="navbar-toggler hidden-lg-up" type="button" data-toggle="collapse" data-target="#mobile-navbar">
							<span class="icon-box">
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</span>
						</button>
						<div class="mobile-logo text-xs-center">
							<a href="/" class="navbar-brand"><img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="Coastline Vacation Rentals" class="img-fluid center-block" ><?php //echo file_get_contents(get_template_directory_uri().'/img/logo.svg'); ?></a>
						</div>
						
					</div>
					<div class="hidden-md-down">
						
						<div class="row">
							<div class="col-sm-4">
								<div class="navbar-left pull-md-left" >
									<?php wp_nav_menu( array( 
										'theme_location' => 'primary-left', 
										'menu_id' => 'primary-menu-left',
										'menu_class' => 'menu',
										'container' => 'nav',
										'container_class' => 'navbar',
										'echo' => 'true',
										'depth' => 0,
										'items_wrap' => '<ul id="%1$s" class="nav navbar-nav">%3$s</ul>',
									) ); ?>
								</div>
							</div>
							<div class="col-sm-4 text-xs-center">
								<a href="/" class="navbar-brand"><img src="<?php echo get_template_directory_uri(); ?>/img/logo.png" alt="Coastline Vacation Rentals" class="img-fluid center-block" ><?php //echo file_get_contents(get_template_directory_uri().'/img/logo.svg'); ?></a>
							</div>
							<div class="col-sm-4">
								<div class="navbar-right pull-md-right" >
									<?php wp_nav_menu( array( 
										'theme_location' => 'primary-right', 
										'menu_id' => 'primary-menu-right',
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
						
					</div>
				</div>

		</header>
	</div>
