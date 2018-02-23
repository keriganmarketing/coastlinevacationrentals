<?php
// Exit if accessed directly
if ( !defined('ABSPATH')) exit;
/*
Template Name: Content Page
*/
?>
<?php get_header(); ?>
<article class="content-page">
        <?php if ( is_active_sidebar( 'insta-top-wide-content' ) ) : ?>
        <header class="row-fluid">
    		<div class="span12">
            	<?php dynamic_sidebar( 'insta-top-wide-content' ); ?>
            </div>
		</header>
    	<?php endif; ?>
    
    <div class="row-fluid">
    	<?php if ( is_active_sidebar( 'insta-left-sidebar-content' ) ) : ?>
  		<aside class="span3">  				
            <?php dynamic_sidebar( 'insta-left-sidebar-content' ); ?>
		</aside>
        <?php endif; ?>
        
        <?php if ( is_active_sidebar( 'insta-left-sidebar-content' ) && is_active_sidebar( 'insta-right-sidebar-content' ) ) : ?>
        	<section class="span6">
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    <?php the_content(); ?>
                <?php endwhile; else: ?>
                    <p><?php _e('Sorry, this page does not exist.'); ?></p>
                <?php endif; ?>
            </section>
        <?php else: ?>
        <?php if ( !is_active_sidebar( 'insta-left-sidebar-content' ) && !is_active_sidebar( 'insta-right-sidebar-content' ) ) : ?>
        	<section class="span12">
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    <?php the_content(); ?>
                <?php endwhile; else: ?>
                    <p><?php _e('Sorry, this page does not exist.'); ?></p>
                <?php endif; ?>
            </section>
            <?php else: ?>
            <section class="span9">
				<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                    <?php the_content(); ?>
                <?php endwhile; else: ?>
                    <p><?php _e('Sorry, this page does not exist.'); ?></p>
                <?php endif; ?>
            </section>
            <?php endif; ?>
            
        <?php endif; ?>        
			
        
			<?php if ( is_active_sidebar( 'insta-right-sidebar-content' ) ) : ?>
           		<aside class="span3"> 		
            		<?php dynamic_sidebar( 'insta-right-sidebar-content' ); ?>	
        		</aside>        	
            <?php endif; ?>
                
    </div>
    

  		<?php if ( is_active_sidebar( 'insta-bottom-wide-content' ) ) : ?>
        <section class="row-fluid">      
  			<div class="span12">
            	<?php dynamic_sidebar( 'insta-bottom-wide-content' ); ?>
            </div>
		</section>
        <?php endif; ?>

<?php if ( is_active_sidebar( 'insta-bottom-left-content' ) && is_active_sidebar( 'insta-bottom-center-content' ) && is_active_sidebar( 'insta-bottom-right-content' ) ) : ?>
<footer class="row-fluid">      
  <div class="span4">
  		<?php dynamic_sidebar( 'insta-bottom-left-content' ); ?>
  </div>
  <div class="span4">
        <?php dynamic_sidebar( 'insta-bottom-center-content' ); ?>	
  </div>
  <div class="span4">
        <?php dynamic_sidebar( 'insta-bottom-right-content' ); ?>
  </div>
</footer>
<?php else: ?>

<?php if ( is_active_sidebar( 'insta-bottom-left-content' ) ) : ?>
<footer class="row-fluid">
		<?php if ( !is_active_sidebar( 'insta-bottom-center-content' ) && !is_active_sidebar( 'insta-bottom-right-content' ) ) : ?>        
            <div class="span12">        
                <?php dynamic_sidebar( 'insta-bottom-left-content' ); ?>
            </div>        
        <?php else: ?>        
            <div class="span6">        		
                <?php dynamic_sidebar( 'insta-bottom-left-content' ); ?>
            </div>
            <div class="span6">        		
                <?php dynamic_sidebar( 'insta-bottom-center-content' ); ?>
                <?php dynamic_sidebar( 'insta-bottom-right-content' ); ?>        
            </div>               
    	<?php endif; ?>
</footer>
<?php else: ?>

	<?php if ( is_active_sidebar( 'insta-bottom-center-content' ) ) : ?>
    <footer class="row-fluid">
            <?php if ( !is_active_sidebar( 'insta-bottom-left-content' ) && !is_active_sidebar( 'insta-bottom-right-content' ) ) : ?>        
                <div class="span12">        
                    <?php dynamic_sidebar( 'insta-bottom-center-content' ); ?>
                </div>        
            <?php else: ?>
                <?php if ( is_active_sidebar( 'insta-bottom-left-content' ) ) : ?>
                <div class="span6">        		
                    <?php dynamic_sidebar( 'insta-bottom-left-content' ); ?>
                </div>
                <div class="span6">
                    <?php dynamic_sidebar( 'insta-bottom-center-content' ); ?>
                </div>
                <?php else: ?>
                <div class="span6">
                    <?php dynamic_sidebar( 'insta-bottom-center-content' ); ?>
                </div>
                <div class="span6">
                    <?php dynamic_sidebar( 'insta-bottom-right-content' ); ?>        
                </div>
                
                <?php endif; ?>
            <?php endif; ?>
    </footer>
    <?php else: ?>
    
    <?php if ( is_active_sidebar( 'insta-bottom-right-content' ) ) : ?>
    <footer class="row-fluid">
            <?php if ( !is_active_sidebar( 'insta-bottom-left-content' ) && !is_active_sidebar( 'insta-bottom-center-content' ) ) : ?>        
                <div class="span12">        
                    <?php dynamic_sidebar( 'insta-bottom-right-content' ); ?>
                </div>        
            <?php else: ?>        
                <div class="span6">
                	<?php dynamic_sidebar( 'insta-bottom-left-content' ); ?>        		
                    <?php dynamic_sidebar( 'insta-bottom-center-content' ); ?>
                </div>
                <div class="span6">
                    <?php dynamic_sidebar( 'insta-bottom-right-content' ); ?>        
                </div>               
            <?php endif; ?>
    </footer>    
    <?php endif; ?>
    
    <?php endif; ?>

<?php endif; ?>


<?php endif; ?>

</article>
<?php get_footer(); ?>