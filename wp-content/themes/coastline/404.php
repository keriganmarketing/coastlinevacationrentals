<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package coastline_vr
 */

get_header(); ?>

<div id="support-mast">

</div>

<div id="mid">
	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<section class="error-404 not-found">
				
				<div class="container">
					<div class="row">
						<div class="col-xs-12" >
                            <h1 class="page-title text-xs-center"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'coastlinevr' ); ?></h1>
							<div class="page-content text-sm-center">
								<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'coastlinevr' ); ?></p>

								<?php
									get_search_form();

									//the_widget( 'WP_Widget_Recent_Posts' );

								?>
							</div>
						</div>
					</div>
				</div><!-- .page-content -->
			</section><!-- .error-404 -->

		</main><!-- #main -->
	</div><!-- #primary -->
</div>
<?php
get_footer();
