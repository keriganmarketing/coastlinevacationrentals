<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package startstrap_v4
 */

?>
	<div id="bot">
		<div class="container">
			<div class="col-sm-4">
				<div class="footer-nav-container">
					<?php wp_nav_menu( array( 'theme_location' => 'footer', 'menu_id' => 'footer-menu' ) ); ?>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="blog-feed-conainer">
					<h4><a href="/newsroom/" class="foot-link">News</a></h4>
					<div class="row">

						<?php
						$args = array(
							'numberposts' => 4,
							'offset' => 0,
							'category' => 0,
							'orderby' => 'post_date',
							'order' => 'DESC',
							'include' => '',
							'exclude' => '',
							'meta_key' => '',
							'meta_value' =>'',
							'post_type' => 'post',
							'post_status' => 'publish',
							'suppress_filters' => true
						);

						$recent_posts = wp_get_recent_posts( $args, ARRAY_A );

						foreach($recent_posts as $article){
							$article_id = $article['ID'];
							$thumb_id = get_post_thumbnail_id( $article_id );
							$thumb = wp_get_attachment_image_src( $thumb_id, 'thumbnail');
							$thumb_url = $thumb[0];

						?>

						<!--<div class="col-sm-6 col-xl-3" >
							<div class="article-tile"
							<?php if($thumb_url != ''){ ?>
								 style="background-image:url('<?php echo $thumb_url; ?>');"
							<?php } ?> >
								<div class="button-overlay">
									<p><?php echo $article["post_title"]; ?> <?php the_date( 'F j, Y', '<span class="article-tile-date">', '</span>', true ); ?></p>
									<a href="<?php echo get_permalink($article["ID"]); ?>" class="article-tile-link"></a>
								</div>
							</div>
						</div>-->

						<?php } ?>

					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<h4><a href="/contact/" class="foot-link">Contact</a></h4>
				<div class="contact-container">
				<p>3706 Hwy 98, Suite 103<br>
					Mexico Beach, FL 32456<br>
					850-648-4560 <span class="orange-text"><em>office</em></span><br>
					<a href="mailto:info@kerigan.com">info@kerigan.com</a></p>
					<div class="social">
					<?php
						//print_r(getSocialLinks());
						$socialLinks = getSocialLinks();
						foreach($socialLinks as $socialId => $socialLink){
							echo '<a class="'.$socialId.'" href="'.$socialLink.'" target="_blank" ></a>';
						}
					?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="bot-bot">
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<footer id="colophon" class="site-footer" role="contentinfo">
						<div class="site-info text-xs-center">
							<p class="copyright">&copy;<?php echo date('Y'); ?> Client Name</p>
							<p class="siteby pull-sm-right">Site by <a href="https://keriganmarketing.com">KMA</a>.</p>
						</div><!-- .site-info -->
					</footer><!-- #colophon -->
				</div>
			</div>
		</div>
	</div>

</div>

<?php wp_footer(); ?>

</body>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css" async>
<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.4/js/tether.min.js" crossorigin="anonymous" ></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.3/js/bootstrap.min.js" integrity="sha384-ux8v3A6CPtOTqOzMKiuo3d/DomGaaClxFYdCu2HPMBEkf6x2xiDyJ7gkXU0MWwaD" crossorigin="anonymous" ></script>

</html>
