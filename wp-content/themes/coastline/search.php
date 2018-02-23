<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package coastline_vr
 */

get_header(); ?>
<div id="support-mast">

</div>

<div id="mid">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php
		if ( have_posts() ) : ?>
			

			<div class="container">
                
				<div class="row">
					<div class="offset-lg-1 col-lg-10 " >
                      <h1 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'coastlinevr' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
					<?php
					/* Start the Loop */
					while ( have_posts() ) : the_post();

						if($post->post_type == 'page'){
						get_template_part( 'template-parts/content', 'search' );
                        }
                      
					endwhile;

					the_posts_navigation(); ?>

					</div>
				</div>
			</div>

		<?php else :

			get_template_part( 'template-parts/content', 'none' );

		endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->
</div>
<?php get_footer();
