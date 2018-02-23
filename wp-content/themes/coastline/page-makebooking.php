<?php
/**
 * @package coastline_vr
 */

get_header(); 
?>
<div id="support-mast">

</div>

<div id="mid">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12 offset-md-1 col-md-10" >
                            <div class="entry-content">
                                
                                <div class="bapi-bookingform" id="bookingform"></div>
                                <script>
                                $(window).load(function() {
                                    $('.booking-prop-img a img').addClass('img-fluid');
                                });
                                </script>
                            </div><!-- .entry-content -->

                        </div>
                    </div>

                </div>
            </article><!-- #post-## -->
            
		</main><!-- #main -->
	</div><!-- #primary -->
</div>
<?php get_footer();
