<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package coastline_vr
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="container">
		<div class="row">
			<div class="col-sm-12 offset-md-1 col-md-10" >
				<div class="entry-content">

					<?php
						the_content();

						wp_link_pages( array(
							'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'coastlinevr' ),
							'after'  => '</div>',
						) );
					?>
				</div><!-- .entry-content -->

			</div>
		</div>

	</div>
</article><!-- #post-## -->
