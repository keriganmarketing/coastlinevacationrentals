<?php
// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
/*
Template Name: Full-width Content Page
*/
?>
<?php get_header(); ?>
<article class="full-width-page">	
<div class="row-fluid">
	<article class="span12">   	
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <?php the_content(); ?>
        <?php endwhile; else: ?>
            <p><?php _e('Sorry, this page does not exist.'); ?></p>
        <?php endif; ?>
    </article>
    <?php 
	/* this is for the booking page */
	if(function_exists('getSSL')) {
    	getSSL();
	} ?>
</div>
</article>
<?php get_footer(); ?>