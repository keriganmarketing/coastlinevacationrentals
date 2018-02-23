<?php
/*
Plugin Name: Slider Plugin by KMA
Description: Slider system for use with KMA sites. 
*/
// Add scripts to wp_head()
 
add_action( 'init', 'create_slider_cpt' );
 
function create_slider_cpt(){
	register_post_type( 'slider', array(
	  'labels'             => array(
		  'name' 		         => _x( 'Slider', 'post type general name' ),
		  'singular_name'      => _x( 'Slide', 'post type singular name' ),
		  'menu_name'          => _x( 'Home page Slideshow', 'admin menu' ),
		  'name_admin_bar'     => _x( 'Home page Slideshow', 'add new on admin bar' ),
		  'add_new'            => _x( 'Add New', 'slide' ),
		  'add_new_item'       => __( 'Add New Slide' ),
		  'new_item'           => __( 'NewSlide' ),
		  'edit_item'          => __( 'Edit Slide' ),
		  'view_item'          => __( 'View Slide' ),
		  'all_items'          => __( 'All Slides' ),
		  'search_items'       => __( 'Search Slides' ),
		  'parent_item_colon'  => __( 'Parent Slide:' ),
		  'not_found'          => __( 'No sliders found.' ),
		  'not_found_in_trash' => __( 'No sliders found in Trash.' )
	  ),
	  'public'             => true,
	  'menu_icon' 			=> 'dashicons-images-alt2',
	  'publicly_queryable' => true,
	  'show_ui'            => true,
	  'show_in_menu'       => true,
	  'query_var'          => true,
	  'rewrite'            => array( 'slug' => 'slider', 'with_front' => FALSE ),
	  'capability_type'    => 'post',
	  'has_archive'        => false,
	  'hierarchical'       => false,
	  'menu_position'      => null,
	  'supports'           => array( 'title', 'thumbnail' )
	));
		
	register_taxonomy( 'slide-cat', 'slider', 
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
		'rewrite'           => array( 'slug' => 'slide-cat' ),
	  ) 
	);
}
function slider_meta () {
 
	// - grab data -
	 
	global $post;
	$custom = get_post_custom($post->ID);
	$slidertitle = $custom["slider_title"][0];
	$sliderdesc = $custom["slider_caption"][0]; 
	$sliderdesc = $custom["slider_desc"][0]; 
	$sliderlink = $custom["slider_link"][0]; 
	$sliderlinktext = $custom["slider_linktext"][0]; 

	// - security -
	 
	echo '<input type="hidden" name="slider-nonce" id="slider-nonce" value="' .
	wp_create_nonce( 'slider-nonce' ) . '" />';
	 
	// - output -
	 
	?>
	<div class="slider-meta">
	<table style="width:100%;" >
    <tr>
    <td width="100"><label>Headline</label></td><td><input name="slider_title" class="text" value="<?php echo $slidertitle; ?>" style="width:100%" /></td>
    </tr><tr>
	<td><label>Caption</label></td><td><input name="slider_cap" class="text" value="<?php echo $slidercaption; ?>" style="width:100%" /></td>
	</tr><tr>
	<td><label>Description</label></td><td><textarea name="slider_desc" class="textarea" style="width:100%; height:75px;" /><?php echo $sliderdesc; ?></textarea></td>
	</tr><tr>
	<td><label>Link</label></td><td><input name="slider_link" class="text" value="<?php echo $sliderlink; ?>" style="width:100%" /></td>
	</tr><tr>
	<td><label>Link Text</label></td><td><input name="slider_linktext" class="text" value="<?php echo $sliderlinktext; ?>" style="width:100%" /></td>
	</tr>
    </table>
	</div>
	<?php
}

function slider_create() {
    add_meta_box('slider_meta', 'Slide Info', 'slider_meta', 'slider');
}

function slider_edit_columns($columns){
	$columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"slider_thumb" => "Photo",
		"title" => "Name",
		"slider_title" => "Headline",
		"slider_cap" => "Caption",
		"slider_desc" => "Description",
		"slider_link" => "Link",
		"slider_linktext" => "Link Text",
		"slider_cat" => "Category",
	);
	return $columns;
}

function shortensliderdesc($string,$length=100,$append="&hellip;") {
  $string = trim($string);

  if(strlen($string) > $length) {
    $string = wordwrap($string, $length);
    $string = explode("\n", $string, 2);
    $string = $string[0] . $append;
  }

  return $string;
}
	 
function slider_custom_columns($column){
	global $post;
	$custom = get_post_custom();
	
	switch ($column){
	case "slider_cat":
		// - show taxonomy terms -
		$slidercats = get_the_terms($post->ID, "slide-cat");
		$slidercats_html = array();
		if ($slidercats) {
			foreach ($slidercats as $slidercat)
			array_push($slidercats_html, $slidercat->name);
			echo implode($slidercats_html, ", ");
		} 
	break;
	case "slider_title":
		// - show dates -
		$slidertitle = $custom["slider_title"][0];
		echo $slidertitle;
	break;
	case "slider_cap":
		// - show times -
		$slidercap = $custom["slider_cap"][0];
		echo $slidercap;
	break;
	case "slider_desc":
		// - show times -
		$sliderdesc = $custom["slider_desc"][0];
		echo shortensliderdesc($sliderdesc,50);
	break;
	case "slider_link":
		// - show times -
		$sliderlink = $custom["slider_link"][0];
		echo $sliderlink;
	break;
	case "slider_linktext":
		// - show times -
		$sliderlinktext = $custom["slider_linktext"][0];
		echo $sliderlinktext;
	break;
	case "slider_thumb":
		// - show thumb -
		$post_image_id = get_post_thumbnail_id(get_the_ID());
		if ($post_image_id) {
			$thumbnail = wp_get_attachment_image_src( $post_image_id, 'post-thumbnail', false);
			//$thumbnail = get_field('image', $post->ID);
			if ($thumbnail) (string)$thumbnail = $thumbnail['url'];
			//echo '<div><img src="'.$thumbnail['url'].'" alt="" width="100%" /></div>';
			
		}
	break;
	 
	}
}

function save_slider(){
 
	global $post;
	 
	// - still require nonce
	 
	if ( !wp_verify_nonce( $_POST['slider-nonce'], 'slider-nonce' )) {
		return $post->ID;
	}
	 
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;
	 
	// - convert back to unix & update post
	 
	if(!isset($_POST["slider_title"])):
	return $post;
	endif;
	$updatetitle = $_POST["slider_title"];
	update_post_meta($post->ID, "slider_title", $updatetitle );
	 
	if(!isset($_POST["slider_cap"])):
	return $post;
	endif;
	$updateslidercap = $_POST["slider_cap"];
	update_post_meta($post->ID, "slider_cap", $updateslidercap );
	 
	if(!isset($_POST["slider_desc"])):
	return $post;
	endif;
	$updatesliderdesc = $_POST["slider_desc"];
	update_post_meta($post->ID, "slider_desc", $updatesliderdesc );
	
	if(!isset($_POST["slider_link"])):
	return $post;
	endif;
	$updatesliderlink = $_POST["slider_link"];
	update_post_meta($post->ID, "slider_link", $updatesliderlink );
	
	if(!isset($_POST["slider_linktext"])):
	return $post;
	endif;
	$updatesliderlinktext = $_POST["slider_linktext"];
	update_post_meta($post->ID, "slider_linktext", $updatesliderlinktext );
	
}

function slider_updated_messages( $messages ) {
 
  global $post, $post_ID;
 
  $messages['slider'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('slider member updated. <a href="%s">View item</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('slider member updated.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('slider member restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('slider member published. <a href="%s">View event</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('slider member saved.'),
    8 => sprintf( __('slider member submitted. <a target="_blank" href="%s">Preview event</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('slider member scheduled to post on: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview member</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('slider member draft updated. <a target="_blank" href="%s">Preview member</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );
 
  return $messages;
}

// [getslider category="" ]
function getslider_func( $atts, $content = null ) {
	$debugslider = FALSE;
	
    $a = shortcode_atts( array(
        'category' => '',
		'truncate' => 0,
    ), $atts );
	
	if($debugslider){
		$output = '<p>category = '.$a['category'].'</p>';
	}else{
		$output = '';
	}
	
	$request = array(
		  'posts_per_page'   => -1,
		  'offset'           => 0,
		  'order'            => 'ASC',
		  'orderby'   		 => 'menu_order',
		  'post_type'        => 'slider',
		  'post_status'      => 'publish',		  
	  );
	
	if($a['category']!= ''){
		$categoryarray = array(
			array(
				'taxonomy' => 'slider-cat',
				'field' => 'slug',
				'terms' => $a['category'],
				'include_children' => false,
			),
		);
		$request['tax_query'] = $categoryarray;
	}
	
	if($debugslider){
		print_r($request);
	}
	
	$slidelist = get_posts( $request );

    $dots = '';
    $slides = '';
	$i = 0;
	foreach($slidelist as $slide){
		$slideid = $slide->ID;
		$caption = get_post_meta($slideid, 'slider_cap', true );
		$post_thumbnail_id = get_post_thumbnail_id($slideid);
        $slider_thumb_url = wp_get_attachment_image_url( $post_thumbnail_id, 'slider' ); 

		$slides .= '<div class="carousel-item'; if($i < 1){ $slides .= ' active'; } $slides .= ' slide-'.$i.'">';
		$slides .= '	<img src="'.$slider_thumb_url.'" alt="'.$caption.'" class="img-responsive" />';
		$slides .= '</div>';
                          
		$dots .= '<li data-target="#home-carousel" data-slide-to="'.$i.'" '; 
		if($i < 1){ $dots .= 'class="active"'; } $dots .= '></li>';
                          
		$i++;
	}
	
	
	$output .='    
	<div id="home-carousel" class="carousel slide carousel-fade" data-ride="carousel">
		<div class="slider-control">
		  <a class="left carousel-control" href="#home-carousel" role="button" data-slide="prev">
			<span class="icon-prev" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		  </a>
		  <ol class="carousel-indicators">'.$dots.'</ol>
		  <a class="right carousel-control" href="#home-carousel" role="button" data-slide="next">
			<span class="icon-next" aria-hidden="true"></span>
			<span class="sr-only">Next</span>
		  </a>
		</div>
		
    	<div class="carousel-inner" role="listbox">
		'.$slides.'
		</div>
		
	</div></div>
	';
	
	return $output; 
	
}
add_shortcode( 'getslider', 'getslider_func' );

add_action( 'admin_init', 'slider_create' );
add_filter ('manage_edit-slider_columns', 'slider_edit_columns');
add_action ('manage_posts_custom_column', 'slider_custom_columns');
add_action ('save_post', 'save_slider');
add_filter('post_updated_messages', 'slider_updated_messages');

?>