<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package startstrap_v4
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<style type="text/css">
<?php echo file_get_contents(get_template_directory_uri().'/inline.css'); //import page level css ?>
<?php echo file_get_contents('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/css/bootstrap.min.css'); //import page level css ?>
</style>

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> >
<div id="page" class="site">
	<a style="display:none;" class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'ssv4' ); ?></a>
    <div id="top">
        <header id="masthead" class="site-header">
			<div class="navbar-static-top navbar-transparent">
				<div class="container-fluid">
					<div class="navbar-collapse collapse navbar-toggleable-sm inverse" id="navbar-header">
						<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu' ) ); ?>
					</div>
					<button class="navbar-toggler hidden-md-up" type="button" data-toggle="collapse" data-target="#navbar-header">
						<span class="icon-box">
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</span>
					</button>
					<a href="/" class="navbar-brand"><img src="<?php echo get_template_directory_uri(); ?>/img/kerigan-marketing-logo.png" alt="Kerigan Marketing Associates" /></a>

				</div>
			</div>
		</header>

