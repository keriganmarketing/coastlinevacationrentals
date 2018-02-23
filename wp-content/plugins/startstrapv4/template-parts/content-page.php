<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package startstrap_v4
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<h1><?php echo get_the_title(); ?></h1>
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-10 col-lg-9" >
				<div class="entry-content">

					<?php
						the_content();

						wp_link_pages( array(
							'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'ssv4' ),
							'after'  => '</div>',
						) );
					?>
				</div><!-- .entry-content -->

			</div>
		</div>

	</div>
</article><!-- #post-## -->
