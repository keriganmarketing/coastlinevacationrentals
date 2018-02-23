<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package startstrap_v4
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php
		if ( is_single() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="boossv4rk">', '</a></h2>' );
		endif; ?>

		<?php if ( 'post' === get_post_type() ) : ?>
			<div class="entry-meta text-xs-center">
				<?php ssv4_posted_on(); ?>
			</div><!-- .entry-meta -->
		<?php endif; ?>

	</header><!-- .entry-header -->

	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-10 col-lg-9" >
				<div class="entry-content">
					<?php
						the_content( sprintf(
							/* translators: %s: Name of current post. */
							wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'ssv4' ), array( 'span' => array( 'class' => array() ) ) ),
							the_title( '<span class="screen-reader-text">"', '"</span>', false )
						) );

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
