<?php
/*
Plugin Name: Events Plugin by KMA
Description: Events plugin for use with KMA sites. 
*/
 
add_action( 'init', 'create_events_cpt' );
 
function create_events_cpt(){
	register_post_type( 'events', array(
	  'labels'             => array(
		  'name' 		       => _x( 'Events', 'post type general name' ),
		  'singular_name'      => _x( 'Event', 'post type singular name' ),
		  'menu_name'          => _x( 'Events', 'admin menu' ),
		  'name_admin_bar'     => _x( 'Events', 'add new on admin bar' ),
		  'add_new'            => _x( 'Add New', 'event member' ),
		  'add_new_item'       => __( 'Add New Event' ),
		  'new_item'           => __( 'New Event' ),
		  'edit_item'          => __( 'Edit Event' ),
		  'view_item'          => __( 'View Event' ),
		  'all_items'          => __( 'All Events' ),
		  'search_items'       => __( 'Search Events' ),
		  'parent_item_colon'  => __( 'Parent Event:' ),
		  'not_found'          => __( 'No events found.' ),
		  'not_found_in_trash' => __( 'No events found in Trash.' )
	  ),
	  'public'             => true,
	  'publicly_queryable' => true,
	  'show_ui'            => true,
	  'show_in_menu'       => true,
	  'query_var'          => true,
	  'rewrite'            => array( 
				'slug' 			=> 'upcoming-events',   //string Customize the permalink structure slug. Defaults to the $post_type value. Should be translatable.
				'with_front' 	=> false, 				//bool Should the permalink structure be prepended with the front base. <br>
														//(example: if your permalink structure is /blog/, then your links will be: false-> /news/, true->/blog/news/). Defaults to true
				'feeds' 		=> true, 				//bool Should a feed permalink structure be built for this post type. Defaults to has_archive value
				'pages' 		=> false				//bool Should the permalink structure provide for pagination. Defaults to true
			),
	  'capability_type'    => 'post',
	  'menu_icon'          => 'dashicons-calendar-alt',
	  'has_archive'        => false,
	  'hierarchical'       => false,
	  'menu_position'      => null,
	  'supports'           => array( 'title', 'editor', 'thumbnail' )
	));
		
	register_taxonomy( 'event-type', 'events', 
	  array(
		'hierarchical'      => true,
		'labels'            => array(
			'name'                       => _x( 'Event Categories', 'taxonomy general name' ),
			'singular_name'              => _x( 'Event Category', 'taxonomy singular name' ),
			'search_items'               => __( 'Search Event Categories' ),
			'popular_items'              => __( 'Popular Event Categories' ),
			'all_items'                  => __( 'All Event Categories' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Event Category' ),
			'update_item'                => __( 'Update Event Category' ),
			'add_new_item'               => __( 'Add New Event Category' ),
			'new_item_name'              => __( 'New Event Category Name' ),
			'separate_items_with_commas' => __( 'Separate event categories with commas' ),
			'add_or_remove_items'        => __( 'Add or remove event categories' ),
			'choose_from_most_used'      => __( 'Choose from the most used event categories' ),
			'not_found'                  => __( 'No event categories found.' ),
			'menu_name'                  => __( 'Event Categories' ),
		),
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'event-type' ),
	  ) 
	);
	register_taxonomy( 'event-region', 'events', 
	  array(
		'hierarchical'      => true,
		'labels'            => array(
			'name'                       => _x( 'Event Regions', 'taxonomy general name' ),
			'singular_name'              => _x( 'Event Region', 'taxonomy singular name' ),
			'search_items'               => __( 'Search Event Regions' ),
			'popular_items'              => __( 'Popular Event Regions' ),
			'all_items'                  => __( 'All Event Regions' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit Event Region' ),
			'update_item'                => __( 'Update Event Region' ),
			'add_new_item'               => __( 'Add New Event Region' ),
			'new_item_name'              => __( 'New Event Category Region' ),
			'separate_items_with_commas' => __( 'Separate event regions with commas' ),
			'add_or_remove_items'        => __( 'Add or remove event regions' ),
			'choose_from_most_used'      => __( 'Choose from the most used event regions' ),
			'not_found'                  => __( 'No event regions found.' ),
			'menu_name'                  => __( 'Event Regions' ),
		),
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'event-region' ),
	  ) 
	);
}
function events_meta () {
 
	// - grab data -
	 
	global $post;
	$custom = get_post_custom($post->ID);
	$eventsstartdate = $custom["events_start_date"][0];
	$eventsstarttime = $custom["events_start_time"][0]; 
	$eventsenddate = $custom["events_end_date"][0];
	$eventsendtime = $custom["events_end_time"][0]; 
	$eventsrecur = $custom["events_recur"][0];

	// - security -
	 
	echo '<input type="hidden" name="events-nonce" id="events-nonce" value="' . wp_create_nonce( 'events-nonce' ) . '" />';
	 
	// - output -
	 
	?>
	<div class="events-meta">
	<table >
    <tr>
    <td><label>Start Date (yyyymmdd)</label></td><td width="300"><input name="events_start_date" class="large-text openDatepicker" value="<?php echo $eventsstartdate; ?>" /></td>
    <td width="20">&nbsp;</td>
    <td><label>Start Time (00:00 xm)</label></td><td width="300"><input name="events_start_time" class="large-text" value="<?php echo $eventsstarttime; ?>" /></td>
    </tr>
    <tr>
    <td ><label>End Date (yyyymmdd)</label></td><td ><input name="events_end_date" class="large-text openDatepicker" value="<?php echo $eventsenddate; ?>" /></td>
    <td>&nbsp;</td>
    <td ><label>End Time (00:00 xm)</label></td><td ><input name="events_end_time" class="large-text" value="<?php echo $eventsendtime; ?>" /></td>
    </tr>
    <tr>
    <td><label>Recur Schedule</label></td><td><select name="events_recur" class="select" style="width:100%;" > 
    		<option value="none" <?php if($eventsrecur == 'none' || $eventsrecur == ''){ echo ' selected'; } ?> >None</option>
    		<option value="weekly" <?php if($eventsrecur == 'weekly'){ echo ' selected'; } ?> >Weekly</option>	
            <option value="daily" <?php if($eventsrecur == 'daily'){ echo ' selected'; } ?> >Daily</option>			
    	</select></td><td>&nbsp;</td><td>&nbsp;</td>
    </tr>

    </table>
    <script>
	jQuery(document).ready(function() {
		jQuery('.openDatepicker').datepicker({
			dateFormat : 'yymmdd',
			changeMonth: true,
      		changeYear: true,
			minDate: 0,
			showButtonPanel: true
		});
	});
	</script>
	</div>
	<?php
}

function events_create() {
    add_meta_box('events_meta', 'Event Info', 'events_meta', 'events', 'advanced', 'high');
}

function events_edit_columns($columns){
	$columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"events_thumb" => "Photo",
		"title" => "Name",
		"events_start_date" => "Start Date",
		"events_start_time" => "Start Time",
		"events_end_date" => "End Date",
		"events_end_time" => "End Time",
		"events_recurr" => "Recur Schedule",
		"events_region" => "Region",
		"events_description" => "Description",
		"events_cat" => "Category",
	);
	return $columns;
}
	 
function events_custom_columns($column){
	global $post;
	$custom = get_post_custom();
	
	switch ($column){
	case "events_cat":
		// - show taxonomy terms -
		$eventscats = get_the_terms($post->ID, "event-type");
		$eventscats_html = array();
		if ($eventscats) {
			foreach ($eventscats as $eventscat)
			array_push($eventscats_html, $eventscat->name);
			echo implode($eventscats_html, ", ");
		} 
	break;
	case "events_region":
		// - show taxonomy terms -
		$eventsregs = get_the_terms($post->ID, "event-region");
		$eventsregs_html = array();
		if ($eventsregs) {
			foreach ($eventsregs as $eventsreg)
			array_push($eventsregs_html, $eventsreg->name);
			echo implode($eventsregs_html, ", ");
		} 
	break;
	case "events_start_date":
		// - show dates -
		$eventsstartdate = $custom["events_start_date"][0];
		echo $eventsstartdate;
	break;
	case "events_start_time":
		// - show dates -
		$eventsstarttime = $custom["events_start_time"][0];
		echo $eventsstarttime;
	break;
	case "events_end_date":
		// - show dates -
		$eventsenddate = $custom["events_end_date"][0];
		echo $eventsenddate;
	break;
	case "events_end_time":
		// - show times -
		$eventsendtime = $custom["events_end_time"][0];
		echo $eventsendtime;
	break;
	case "events_recur":
		// - show times -
		$eventsrecur = $custom["events_recur"][0];
		echo $eventsrecur;
	break;
	case "events_thumb":
		// - show thumb -
		$post_image_id = get_post_thumbnail_id(get_the_ID());
		if ($post_image_id) {
			$thumbnail = wp_get_attachment_image_src( $post_image_id, 'post-thumbnail', false);
			if ($thumbnail) (string)$thumbnail = $thumbnail[0];
			echo '<div><img src="'.$thumbnail.'" alt="" width="150" /></div>';
		}
	break;
	case "events_description";
		the_excerpt();
	break;
	 
	}
}

function save_events(){
 
	global $post;
	 
	// - still require nonce
	 
	if ( !wp_verify_nonce( $_POST['events-nonce'], 'events-nonce' )) {
		return $post->ID;
	}
	 
	if ( !current_user_can( 'edit_post', $post->ID ))
		return $post->ID;
	 
	// - convert back to unix & update post
	 
	if(!isset($_POST["events_start_date"])):
	return $post;
	endif;
	$update_start_date = $_POST["events_start_date"];
	update_post_meta($post->ID, "events_start_date", $update_start_date );
	
	if(!isset($_POST["events_start_time"])):
	return $post;
	endif;
	$events_start_time = $_POST["events_start_time"];
	update_post_meta($post->ID, "events_start_time", $events_start_time );
	 
	if(!isset($_POST["events_end_date"])):
	return $post;
	endif;
	$events_end_date = $_POST["events_end_date"];
	update_post_meta($post->ID, "events_end_date", $events_end_date );
	
	if(!isset($_POST["events_end_time"])):
	return $post;
	endif;
	$events_end_time = $_POST["events_end_time"];
	update_post_meta($post->ID, "events_end_time", $events_end_time );
	
	if(!isset($_POST["events_recur"])):
	return $post;
	endif;
	$events_recur = $_POST["events_recur"];
	update_post_meta($post->ID, "events_recur", $events_recur );
}

function events_updated_messages( $messages ) {
 
  global $post, $post_ID;
 
  $messages['events'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('event updated. <a href="%s">View item</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('event updated.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('event restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('event published. <a href="%s">View event</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('event saved.'),
    8 => sprintf( __('event submitted. <a target="_blank" href="%s">Preview event</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('event scheduled to post on: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview member</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('event draft updated. <a target="_blank" href="%s">Preview member</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );
 
  return $messages;
}

function advancedate($start, $interval) {
	$newdate = $start;
	$today = date('Ymd');
	
	//echo $newdate. "\n";
	//echo $today. "\n";
	
	if($newdate < $today){
		while($newdate < $today) {
			$adv = date('Y-m-d', strtotime($start));
			//echo '> '.$adv;
			$d = new DateTime( $adv );
			$d->modify( $interval );
			$newdate = $d->format( 'Ymd' );
			//echo '> '.$newdate. "\n";
		}
	}
	return strtotime($newdate);
}

// [getevents category="" location=""]
function getevents_func( $atts, $content = null ) {
	$debugevents = FALSE;
	
    $a = shortcode_atts( array(
        'category' => '',
    ), $atts );
	
	if($debugevents){
		$output = '<p>category = '.$a['category'].'</p>';
	}else{
		$output = '';
	}
	
	$request = array(
		  'posts_per_page'   => -1,
		  'offset'           => 0,
		  'order'            => 'ASC',
		  'orderby'   		 => 'meta_value',
		  'meta_key' 		 => 'events_end_date',
		  'post_type'        => 'events',
		  'post_status'      => 'publish',
		  'meta_query'	=> array(
			  'relation'		=> 'OR',
			  array(
				'key'	  	=> 'events_start_date',
				'value'	  	=> date('Ymd'),
				'compare' 	=> '>=',
			  ), 
			  array(
				'key'	  	=> 'events_recur',
				'value'	  	=> 'none',
				'compare' 	=> '!=',
			  ), 
		  ),
		  
	  );
	
	if($a['category']!= ''){
		$categoryarray = array(
			array(
				'taxonomy' => 'event-type',
				'field' => 'slug',
				'terms' => $a['category'],
				'include_children' => false,
			),
		);
		$request['tax_query'] = $categoryarray;
	}
	
	if($a['location']!= ''){
		$metaarray = array(
			array(
				'taxonomy' => 'event-region',
				'field' => 'slug',
				'terms' => $a['location'],
				'include_children' => false,
			),
		);
		$request['tax_query'] = $metaarray;
	}
	
	if($debugevents){
		print_r($request);
	}
	
	$eventlist = get_posts( $request );

  $u = 1;
  foreach($eventlist as $event){
	  $id = $event->ID;
	  $title = $event->post_title;
	  $link = get_permalink($id);
	  $start_date = strtotime(get_post_meta( $id, 'events_start_date', true ));
	  $start_time = strtotime(get_post_meta( $id, 'events_start_time', true ));
	  $end_date = strtotime(get_post_meta( $id, 'events_end_date', true ));
	  $end_time = strtotime(get_post_meta( $id, 'events_end_time', true ));
	  $recur = get_post_meta( $id, 'events_recur', true );
	  $category = wp_get_post_terms( $id, 'event-type', array("fields" => "names"));
	  $region = wp_get_post_terms( $id, 'event-region', array("fields" => "names"));

	  
	  $maxchar = 60;
	  
	  if (strlen($title) > $maxchar) {
			$title = substr($title, 0, strrpos(substr($title, 0, $maxchar), ' ')).'...';
	  }
	  
	  if($end_date == ''){ $end_date = $start_date; } //no end date. assume it's the same as start
	  
	  if($start_date != $end_date){ 
			
		  if($recur == 'none'){
			  $fulldate = date('M j, Y', $start_date).' &ndash; '.date('M j, Y', $end_date);
			  if($start_time != ''){ $fulldate .= ', '.date('g:i a', $start_time); }
			  if($end_time != '' && $end_time != $start_time){ $fulldate .= ' &ndash; '.date('g:i a', $end_time); }
		  }
		  if($recur == 'weekly'){
			  $fulldate = date('l', $start_date).'s, '.date('M j, Y', $start_date).' &ndash; '.date('M j, Y', $end_date);
			  if($start_time != ''){ $fulldate .= ', '.date('g:i a', $start_time); }
			  if($end_time != '' && $end_time != $start_time){ $fulldate .= ' &ndash; '.date('g:i a', $end_time); }
			  
			  
			  $new_date = advancedate($start_date,"+1 week");

		  }
		  if($recur == 'daily'){
			  $fulldate = 'Every day, '.date('M j, Y', $start_date).' &ndash; '.date('M j, Y', $end_date);
			  if($start_time != ''){ $fulldate .= ', '.date('g:i a', $start_time); }
			  if($end_time != '' && $end_time != $start_time){ $fulldate .= ' &ndash; '.date('g:i a', $end_time); }
			
			  $new_date = advancedate($start_date,"+1 day");
				  
		  }
		  
	  }else{ //end date same as start date
		  
		  if($recur == 'none'){
			  $fulldate = date('M j, Y', $start_date);
			  if($start_time != ''){ $fulldate .= ', '.date('g:i a', $start_time); }
			  if($end_time != '' && $end_time != $start_time){ $fulldate .= ' &ndash; '.date('g:i a', $end_time); }
		  }
		  if($recur == 'weekly'){
			  $fulldate = date('l', $start_date).'s, '.date('M j, Y', $start_date);
			  if($start_time != ''){ $fulldate .= ', '.date('g:i a', $start_time); }
			  if($end_time != '' && $end_time != $start_time){ $fulldate .= ' &ndash; '.date('g:i a', $end_time); }
			  
			  $new_date = advancedate($start_date,"+1 week");
				  
		  }
		  if($recur == 'daily'){
			  $fulldate = 'Every day, '.date('M j, Y', $start_date);
			  if($start_time != ''){ $fulldate .= ', '.date('g:i a', $start_time); }
			  if($end_time != '' && $end_time != $start_time){ $fulldate .= ' &ndash; '.date('g:i a', $end_time); }
			  
			  $new_date = advancedate($start_date,"+1 day");
				  
		  }
	  }
	  
	  //need conditional to advance recurring date if start date has passed
	  
	  
	  $output .= '<div id="event'.$u.'" >';
	  	
		$output .= '<p><a href="'.$link.'" class="title"><strong>'.$title.'</strong></a><br><span class="fulldate">'.$fulldate.'</span>';
		if($new_date!=''){ $output .= '<br><em><strong>Next occurrence:</strong> <span class="next">'.date('M j, Y',$new_date).'</span></em>'; }
	  $output .= '</p></div>';
	  $u++;
  }

    return $output;
}
add_shortcode( 'getevents', 'getevents_func' );

add_action( 'admin_init', 'events_create' );
add_filter ('manage_edit-events_columns', 'events_edit_columns');
add_action ('manage_posts_custom_column', 'events_custom_columns');
add_action ('save_post', 'save_events');
add_filter('post_updated_messages', 'events_updated_messages');

?>