<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package startstrap_v4
 */

get_header(); ?>
<div id="mid">
	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php
		if ( have_posts() ) : ?>
			<h1 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'ssv4' ), '<span>' . get_search_query() . '</span>' ); ?></h1>

			<div class="container">
				<div class="row">
					<div class="col-sm-12 offset-md-1 col-md-10 offset-xl-2 col-lg-8" >

					<?php
					/* Start the Loop */
					while ( have_posts() ) : the_post();

						/**
						 * Run the loop for the search to output the results.
						 * If you want to overload this in a child theme then include a file
						 * called content-search.php and that will be used instead.
						 */
						get_template_part( 'template-parts/content', 'search' );

					endwhile;

					the_posts_navigation(); ?>

					</div>
				</div>
			</div>

		<?php else :

			get_template_part( 'template-parts/content', 'none' );

		endif; ?>

		</main><!-- #main -->
	</section><!-- #primary -->
</div>
<?php get_footer();
