<?php
/*
Plugin Name: photogallery Plugin by KMA
Description: photogallery plugin for use with KMA sites. 
*/
// Add scripts to wp_head()
 
add_action( 'init', 'create_photogallery_cpt' );
 
function create_photogallery_cpt(){
	register_post_type( 'photogallery', array(
	  'labels'             => array(
		  'name' 		         => _x( 'Photo Gallery', 'post type general name' ),
		  'singular_name'      => _x( 'Photo', 'post type singular name' ),
		  'menu_name'          => _x( 'Photo Gallery', 'admin menu' ),
		  'name_admin_bar'     => _x( 'Photo Gallery', 'add new on admin bar' ),
		  'add_new'            => _x( 'Add New', 'photo' ),
		  'add_new_item'       => __( 'Add New Photo' ),
		  'new_item'           => __( 'New Photo' ),
		  'edit_item'          => __( 'Edit Photo' ),
		  'view_item'          => __( 'View Photo' ),
		  'all_items'          => __( 'All Photos' ),
		  'search_items'       => __( 'Search Photos' ),
		  'parent_item_colon'  => __( 'Parent Photo:' ),
		  'not_found'          => __( 'No photos found.' ),
		  'not_found_in_trash' => __( 'No photos found in Trash.' ),
		  'featured_image' => __( 'Photo' ),
		  'set_featured_image' => __( 'Select Photo' ),
		  'remove_featured_image' => __( 'Remove Photo' )
	  ),
	  'public'             => true,
	  'publicly_queryable' => true,
	  'show_ui'            => true,
	  'show_in_menu'       => true,
	  'menu_icon' 			=> 'dashicons-images-alt2',
	  'query_var'          => true,
	  'rewrite'            => array( 'slug' => 'photogallery', 'with_front' => FALSE ),
	  'capability_type'    => 'post',
	  'has_archive'        => false,
	  'hierarchical'       => false,
	  'menu_position'      => null,
	  'supports'           => array( 'title', 'thumbnail', 'revisions' )
	));
		
	register_taxonomy( 'position', 'photogallery', 
	  array(
		'hierarchical'      => true,
		'labels'            => array(
			'name'                       => _x( 'Categories', 'taxonomy general name' ),
			'singular_name'              => _x( 'Category', 'taxonomy singular name' ),
			'search_items'               => __( 'Search Categories' ),
			'popular_items'              => __( 'Popular Categories' ),
			'all_items'                  => __( 'All Categories' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Category' ),
			'update_item'                => __( 'Update Category' ),
			'add_new_item'               => __( 'Add New Category' ),
			'new_item_name'              => __( 'New Category Name' ),
			'separate_items_with_commas' => __( 'Separate categories with commas' ),
			'add_or_remove_items'        => __( 'Add or remove categories' ),
			'choose_from_most_used'      => __( 'Choose from the most used categories' ),
			'not_found'                  => __( 'No categories found.' ),
			'menu_name'                  => __( 'Categories' ),
		),
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'position' ),
	  ) 
	);
}
function photogallery_meta () {
 
	// - grab data -
	 
	global $post;
	$custom = get_post_custom($post->ID);
	$photogallerytitle = $custom["photogallery_caption"][0];

	// - security -
	 
	echo '<input type="hidden" name="photogallery-nonce" id="photogallery-nonce" value="' .
	wp_create_nonce( 'photogallery-nonce' ) . '" />';
	 
	// - output -
	 
	?>
	<div class="photogallery-meta">
	<table >
    <tr>
    <td width="100"><label>Photo Caption</label></td><td width="300"><input name="photogallery_caption" class="text" value="<?php echo $photogallerytitle; ?>" style="width:100%" /></td>
    </tr>
    </table>
	</div>
	<?php
}

function photogallery_create() {
    add_meta_box('photogallery_meta', 'Member Info', 'photogallery_meta', 'photogallery');
}

function photogallery_edit_columns($columns){
	$columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"photogallery_thumbnail" => "Photo",
		"title" => "Photo Name",
		"photogallery_caption" => "Photo Caption",
		"photogallery_cat" => "Category",
	);
	return $columns;
}
	 
function photogallery_custom_columns($column){
	global $post;
	$custom = get_post_custom();
	
	switch ($column){
	case "photogallery_cat":
		// - show taxonomy terms -
		$photogallerycats = get_the_terms($post->ID, "position");
		$photogallerycats_html = array();
		if ($photogallerycats) {
			foreach ($photogallerycats as $photogallerycat)
			array_push($photogallerycats_html, $photogallerycat->name);
			echo implode($photogallerycats_html, ", ");
		} 
	break;
	case "photogallery_title":
		// - show dates -
		$photogallerytitle = $custom["photogallery_title"][0];
		echo $photogallerytitle;
	break;
	case "photogallery_caption":
		// - show times -
		$photogalleryemail = $custom["photogallery_email"][0];
		echo $photogalleryemail;
	break;
	case "photogallery_thumbnail":
		// - show thumb -
		$post_image_id = get_post_thumbnail_id(get_the_ID());
		if ($post_image_id) {
			$thumbnail = wp_get_attachment_image_src( $post_image_id, 'post-thumbnail', false);
			if ($thumbnail) (string)$thumbnail = $thumbnail[0];
			echo '<div><img src="'.$thumbnail.'" alt="" width="150" /></div>';
		}
	break;
	 
	}
}

function save_photogallery(){
 
	global $post;
	 
	// - still require nonce
	 
	if ( !wp_verify_nonce( $_POST['photogallery-nonce'], 'photogallery-nonce' )) {
		return $post->ID;
	}
	 
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;
	 
	// - convert back to unix & update post
	 
	if(!isset($_POST["photogallery_caption"])):
	return $post;
	endif;
	$updatecaption = $_POST["photogallery_caption"];
	update_post_meta($post->ID, "photogallery_caption", $updatecaption );
	
}

function photogallery_updated_messages( $messages ) {
 
  global $post, $post_ID;
 
  $messages['photogallery'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('photogallery member updated. <a href="%s">View item</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('photogallery member updated.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('photogallery member restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('photogallery member published. <a href="%s">View event</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('photogallery member saved.'),
    8 => sprintf( __('photogallery member submitted. <a target="_blank" href="%s">Preview event</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('photogallery member scheduled to post on: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview member</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('photogallery member draft updated. <a target="_blank" href="%s">Preview member</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );
 
  return $messages;
}

function shortencaption($string,$length=100,$append='&hellip;') {
	$string = trim($string);

	if(strlen($string) > $length) {
		//$string = wordwrap($string, $length);
		//$string = explode('\n', $string, 2);
		$string = substr( $string, 0, strrpos( substr( $string, 0, $length), ' ' ) );
		$string = $string . $append;		
	}
	return $string;
}

function getphotogallery_func( $atts, $content = null ) {
	$debugphotogallery = FALSE;
	
    $a = shortcode_atts( array(
        'category' => '',
		'truncate' => 0,
		'class' => '',
		'format' => 'grid'
    ), $atts );
	
	if($debugphotogallery){
		$output = '<p>category = '.$a['category'].'</p>';
	}else{
		$output = '';
	}
	
	$request = array(
		  'posts_per_page'   => -1,
		  'offset'           => 0,
		  'order'            => 'ASC',
		  'orderby'   		 => 'menu_order',
		  'post_type'        => 'photogallery',
		  'post_status'      => 'publish',		  
	  );
	
	if($a['category']!= ''){
		$categoryarray = array(
			array(
				'taxonomy' => 'photogallery-cat',
				'field' => 'slug',
				'terms' => $a['category'],
				'include_children' => false,
			),
		);
		$request['tax_query'] = $categoryarray;
	}
	
	if($debugphotogallery){
		print_r($request);
	}
	
	$photogallery = get_posts( $request );
	
	$output .='
	<div id="photo-feed" class="photogallery">';
	
	if($a['format']== 'masonry'){
		$output .='
		<script src="'.get_template_directory_uri().'/js/jquery.min.js"></script>
		<script src="'.get_template_directory_uri().'/js/masonry.pkgd.min.js"></script>
		<script>
		$(\'.photogallerys-grid\').masonry({
			// options...
			itemSelector: \'.photogallery-item\',
			percentPosition: true	  
		});
		</script>
		<div class="photogallery-masonry row">
		';
	}elseif($a['format']== 'grid'){
		$output .='
		<div class="photogallery-grid row">
		';
	}elseif($a['format']== 'list'){ 
		$output .='
		<div class="photogallery-list">
		';
	}
	
	$i = 0;
	foreach($photogallery as $photo){
		$photoid = $photo->ID;
		$phototitle = $photo->post_title;
		$caption = get_post_meta($photoid, 'photogallery_caption', true );
		$link = get_permalink($photoid);
		$category = wp_get_post_terms( $photoid, 'photogallery-cat', array("fields" => "names"));
		$photo_id = get_post_thumbnail_id( $photoid );
		$photogallery_thumb_url = wp_get_attachment_url( $photo_id );
		if($photogallery_thumb_url!=''){
			$photosize = getimagesize($photogallery_thumb_url);
			/*if($photosize[0] > 767){
				$medium_array = image_downsize( $photo_id, 'medium' );
				$medium_thumb_url = $medium_array[0];
			}*/
		}
		
		$output .= '<div class="photogallery-item '.$a['class'].'">
					<div class="photogallery-container">';
		if($photogallery_thumb_url){
			$output .= '<div class="photogallery-image"><a href="#" class="thumbnail" data-toggle="modal" data-target="#lightbox"><img src="'.$photogallery_thumb_url.'" alt="'.$phototitle.'" class="img-responsive" /></a></div>';
		}
		$output .= '<div class="photogallery-caption-container">
					<p class="photogallery-caption">';
					
		if($caption){
			if(mb_strlen(strip_tags($caption)) > intval($a['truncate']) && intval($a['truncate']) > 0){ 
				$caption = shortenbio($caption, intval($a['truncate']), '&hellip;'); 
			}
			$output .= $caption;
		}
		
		$output .= '</p></div>';
		$output .= '</div></div>';
                          
		$i++;
	}
	$output .='</div>
	</div>';
	
	return $output; 
		
}
add_shortcode( 'getphotogallery', 'getphotogallery_func' );

function lightbox_to_footer() {
	if( !is_home() && !is_front_page() ) {
		echo '<div id="lightbox" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<button type="button" class="close hidden" data-dismiss="modal" aria-hidden="true">×</button>
				<div class="modal-content">
					<div class="modal-body">
						<img src="" alt="" />
					</div>
				</div>
			</div>
		</div>';
	}
}
add_action( 'wp_footer', 'lightbox_to_footer' );

add_action( 'admin_init', 'photogallery_create' );
add_filter ('manage_edit-photogallery_columns', 'photogallery_edit_columns');
add_action ('manage_posts_custom_column', 'photogallery_custom_columns');
add_action ('save_post', 'save_photogallery');
add_filter('post_updated_messages', 'photogallery_updated_messages');

?>