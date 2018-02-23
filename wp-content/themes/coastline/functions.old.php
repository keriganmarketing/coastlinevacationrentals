<?php
/**
 * Coastline VR functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * WordPress
 * Theme: coastline_vr
 * Version: coastline_vr 1.0 
 * Date: 10-27-2016
 * @package coastline_vr
 */

class Custom_Post_Type {
	
    public $post_type_name;
    public $post_type_args;
    public $post_type_labels;

    public function __construct( $name, $args = array(), $labels = array() ){
		
        // Set some important variables
		$this->post_type_name	= strtolower( str_replace( ' ', '_', $name ) );
		$this->post_type_args 	= $args;
		$this->post_type_labels	= $labels;

		// Add action to register the post type, if the post type does not already exist
		if( ! post_type_exists( $this->post_type_name ) ){
			add_action( 'init', array( &$this, 'register_post_type' ) );
		}

		// Listen for the save post hook
		$this->save();
		
    }
	
	public function __get($varName){

		return $this->queryvar[$varName];
		
	}

   	public function __set($varName,$value){
		
	    $this->queryvar[$varName] = $value;
		
   	}
	
	/* Method which registers the post type */
    public function register_post_type(){
		
         //Capitilize the words and make it plural
		$name       = ucwords( str_replace( '_', ' ', $this->post_type_name ) );
		$plural     = $name . 's';

		// We set the default labels based on the post type name and plural. We overwrite them with the given labels.
		$labels = array_merge(

			// Default
			array(
				'name'                  => _x( $plural, 'post type general name' ),
				'singular_name'         => _x( $name, 'post type singular name' ),
				'add_new'               => _x( 'Add New', strtolower( $name ) ),
				'add_new_item'          => __( 'Add New ' . $name ),
				'edit_item'             => __( 'Edit ' . $name ),
				'new_item'              => __( 'New ' . $name ),
				'all_items'             => __( 'All ' . $plural ),
				'view_item'             => __( 'View ' . $name ),
				'search_items'          => __( 'Search ' . $plural ),
				'not_found'             => __( 'No ' . strtolower( $plural ) . ' found'),
				'not_found_in_trash'    => __( 'No ' . strtolower( $plural ) . ' found in Trash'), 
				'parent_item_colon'     => '',
				'menu_name'             => $plural
			),

			// Given labels
			$this->post_type_labels

		);

		// Same principle as the labels. We set some defaults and overwrite them with the given arguments.
		$args = array_merge(

			// Default
			array(
				'label'                 => $plural,
				'labels'                => $labels,
				'public'                => true,
				'show_ui'               => true,
				'supports'              => array( 'title', 'editor', 'thumbnail', 'revisions' ),
				'show_in_nav_menus'     => true,
				'_builtin'              => false,
			),

			// Given args
			$this->post_type_args

		);

		// Register the post type
		register_post_type( $this->post_type_name, $args );
    }
     
    /* Method to attach the taxonomy to the post type */
    public function add_taxonomy( $name, $args = array(), $labels = array() ){
    	if( ! empty( $name ) ){
			// We need to know the post type name, so the new taxonomy can be attached to it.
			$post_type_name = $this->post_type_name;

			// Taxonomy properties
			$taxonomy_name      = strtolower( str_replace( ' ', '_', $name ) );
			$taxonomy_labels    = $labels;
			$taxonomy_args      = $args;

			if( ! taxonomy_exists( $taxonomy_name ) ){
				/* Create taxonomy and attach it to the object type (post type) */
				
				//Capitilize the words and make it plural
				$name       = ucwords( str_replace( '_', ' ', $name ) );
				$plural     = $name . 's';

				// Default labels, overwrite them with the given labels.
				$labels = array_merge(

					// Default
					array(
						'name'                  => _x( $plural, 'taxonomy general name' ),
						'singular_name'         => _x( $name, 'taxonomy singular name' ),
						'search_items'          => __( 'Search ' . $plural ),
						'all_items'             => __( 'All ' . $plural ),
						'parent_item'           => __( 'Parent ' . $name ),
						'parent_item_colon'     => __( 'Parent ' . $name . ':' ),
						'edit_item'             => __( 'Edit ' . $name ),
						'update_item'           => __( 'Update ' . $name ),
						'add_new_item'          => __( 'Add New ' . $name ),
						'new_item_name'         => __( 'New ' . $name . ' Name' ),
						'menu_name'             => __( $name ),
					),

					// Given labels
					$taxonomy_labels

				);

				// Default arguments, overwritten with the given arguments
				$args = array_merge(

					// Default
					array(
						'label'                 => $plural,
						'labels'                => $labels,
						'public'                => true,
						'show_ui'               => true,
						'show_in_nav_menus'     => true,
						'_builtin'              => false,
					),

					// Given
					$taxonomy_args

				);

				// Add the taxonomy to the post type
				add_action( 'init',
					function() use( $taxonomy_name, $post_type_name, $args )
					{
						register_taxonomy( $taxonomy_name, $post_type_name, $args );
					}
				);
			}else{
				/* The taxonomy already exists. We are going to attach the existing taxonomy to the object type (post type) */
				
				add_action( 'init',
					function() use( $taxonomy_name, $post_type_name ){
						register_taxonomy_for_object_type( $taxonomy_name, $post_type_name );
					}
				);
			}
		}
    }
     
    /* Attaches meta boxes to the post type */
    public function add_meta_box($title, $fields = array(), $context = 'normal', $priority = 'default' ){
         if( ! empty( $title ) ){
			// We need to know the Post Type name again
			$post_type_name = $this->post_type_name;

			// Meta variables
			$box_id         = strtolower( str_replace( ' ', '_', $title ) );
			$box_title      = ucwords( str_replace( '_', ' ', $title ) );
			$box_context    = $context;
			$box_priority   = $priority;

			// Make the fields global
			global $custom_fields;
			$custom_fields[$title] = $fields;

			add_action( 'admin_init',
					   
				function() use( $box_id, $box_title, $post_type_name, $box_context, $box_priority, $fields ){
					add_meta_box(
						$box_id,
						$box_title,
						function( $post, $data ){
							global $post;

							// Nonce field for some validation
							wp_nonce_field( plugin_basename( __FILE__ ), 'custom_post_type' );

							// Get all inputs from $data
							$custom_fields = $data['args'][0];

							// Get the saved values
							$meta = get_post_custom( $post->ID );

							// Check the array and loop through it
							if( ! empty( $custom_fields ) ){
								/* Loop through $custom_fields */
								echo '<table width="100%">';
								foreach( $custom_fields as $label => $type ){
                                    $field_id_name  = strtolower( str_replace( ' ', '_', $data['id'] ) ) . '_' . strtolower( str_replace( ' ', '_', $label ) );
                                    if($type == 'text'){
								       echo '<tr>
									   <td width="20%" align="right" valign="top"><label for="' . $field_id_name . '">' . $label . '</label></td><td width="80%" valign="top"><input class="form-control" type="text" name="custom_meta[' . $field_id_name . ']" id="' . $field_id_name . '" value="' . $meta[$field_id_name][0] . '" /></td></tr>';
                                    }elseif($type == 'editor'){
                                        //todo tinymce editor
                                    }elseif($type == 'boolean'){
                                        echo '<tr>
									    <td width="20%" align="right" valign="top"><input type="checkbox" class="form-control" name="custom_meta[' . $field_id_name . ']" id="' . $field_id_name . '"';
                                        if($meta[$field_id_name][0]==TRUE){ echo ' checked '; }
                                        echo '></td><td width="80%" valign="top">' . $label . '</td></tr>';
                                    }elseif($type == 'longtext'){
                                        echo '<tr>
									   <td width="20%" align="right" valign="top" ><label for="' . $field_id_name . '">' . $label . '</label></td><td width="80%" valign="top" ><textarea rows="4" class="form-control" name="custom_meta[' . $field_id_name . ']" id="' . $field_id_name . '" style="width:100%;" >' . $meta[$field_id_name][0] . '</textarea></td></tr>';
                                    }elseif($type == 'embed'){
                                        echo '<tr>
									   <td width="20%" align="right" valign="top" ><label for="' . $field_id_name . '">' . $label . '</label></td><td width="50%" valign="top"><textarea rows="4" class="form-control" name="custom_meta[' . $field_id_name . ']" id="' . $field_id_name . '" style="width:100%;" ';
                                        if($meta[$field_id_name][0]!=''){ echo ' readonly '; }
                                        echo '>' . $meta[$field_id_name][0].'</textarea>';
                                        if($meta[$field_id_name][0]!=''){ 
                                            echo ' <a style="display:inline-block; padding:5px 10px; text-decoration:none; cursor:pointer; background-color:#eaeaea; border-radius:5px; border:1px solid #ddd;" onclick="document.getElementById(\'' . $field_id_name . '\').readOnly=false" >Edit embed code</a> '; 
                                        }
                                        echo '</td><td width="30%" valign="top">' . $meta[$field_id_name][0].'</td></tr>';
                                    }
								}
								echo '</table>';
							}

						},
						$post_type_name,
						$box_context,
						$box_priority,
						array( $fields )
					);
				}
			);
		}
    }
     
    /* Listens for when the post type being saved */ 
    public function save(){
         // Need the post type name again
		$post_type_name = $this->post_type_name;

		add_action( 'save_post',
			function() use( $post_type_name ){
				
				// Deny the WordPress autosave function
				if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

				if ( ! wp_verify_nonce( $_POST['custom_post_type'], plugin_basename(__FILE__) ) ) return;

				global $post;

				if( isset( $_POST ) && isset( $post->ID ) && get_post_type( $post->ID ) == $post_type_name ){
					global $custom_fields;

					// Loop through each meta box
					foreach( $custom_fields as $title => $fields ){
						// Loop through all fields
						foreach( $fields as $label => $type ){
							$field_id_name  = strtolower( str_replace( ' ', '_', $title ) ) . '_' . strtolower( str_replace( ' ', '_', $label ) );

							update_post_meta( $post->ID, $field_id_name, $_POST['custom_meta'][$field_id_name] );
						}

					}
				}
			}
		);
    }
	
	public static function beautify( $string ){
		return ucwords( str_replace( '_', ' ', $string ) );
	}

	public static function uglify( $string ){
		return strtolower( str_replace( ' ', '_', $string ) );
	}
	
	public static function pluralize( $string ){
		$last = $string[strlen( $string ) - 1];

		if( $last == 'y' ){
			$cut = substr( $string, 0, -1 );
			//convert y to ies
			$plural = $cut . 'ies';
		}else{
			// just attach an s
			$plural = $string . 's';
		}

		return $plural;
	}

}

$theOptionIsString = is_string(get_option('bapi_solutiondata'));
if ($theOptionIsString) {
    $bapi_solutiondata = json_decode(get_option('bapi_solutiondata'), true);
    $siteIsLive = $bapi_solutiondata['Site']['IsLive'];
    $sitePrimaryURL = $bapi_solutiondata['PrimaryURL'];
    $siteSecureURL = $bapi_solutiondata['SecureURL'];
    $siteUniquePrefix = $bapi_solutiondata['UniquePrefix'];
}
if (function_exists('getTextDataArray')) {
    /* we get the array of textdata */
    $textDataArray = getTextDataArray();
}

/* lets get the name of the theme folder to see which theme it is */
$currentThemeName = substr(strrchr(get_stylesheet_directory(), "/"), 1);
$itsInstaTheme = strpos($currentThemeName, "ct-") !== FALSE ? FALSE : TRUE;

if ( ! function_exists( 'coastlinevr_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function coastlinevr_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Seriously Creative, use a find and replace
	 * to change 'coastlinevr' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'coastlinevr', get_template_directory() . '/languages' );
    
    $areas = new Custom_Post_Type( 'Area' );

    //'menu_icon'  => 'dashicons-location',
    $areas->add_meta_box( 
        'Map Info', 
        array(
            'Display Map' => 'boolean',
            'Latitude, Longitutde' => 'text',
            'Map Zoom' => 'text',
            'Show Photos' => 'boolean',
            'Show Info Window' => 'boolean',
            'Info Window Content' => 'longtext' 
        )
    );
    
    $areas->add_meta_box( 
        'TripAdvisor Module', 
        array(
            'Display TripAdvisor' => 'boolean',
            'TripAdvisor' => 'embed' 
        )
    );
    
    $attractions = new Custom_Post_Type( 'Attraction' );

    //'menu_icon'  => 'dashicons-location',
    $attractions->add_meta_box( 
        'Attraction Info', 
        array(
            'Phone Number' => 'text',
            'Address' => 'text',
            'Website' => 'text',
            'Latitude, Longitutde' => 'text',
        )
    );
    
    $attractions->add_taxonomy(
        'Type',
       array(
            'hierarchical'          => true,
			'show_ui'               => true,
			'show_admin_column'     => true,
            'public'                => true,
            'show_ui'               => true,
            'show_in_nav_menus'     => true,
            '_builtin'              => false,
        ));

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	//add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );
	
	add_image_size( 'slider', 1920, 845, true );
	add_image_size( 'area-thumbnail', 400, 400, true );
	
	add_filter( 'image_size_names_choose', 'my_custom_sizes' );
 
	function my_custom_sizes( $sizes ) {
		return array_merge( $sizes, array(
			'slider' => __( 'Home Page Slide' ),
		) );
	}

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary-left' => esc_html__( 'Primary Left', 'coastlinevr' ),
		'primary-right' => esc_html__( 'Primary Right', 'coastlinevr' ),
		'primary-mobile' => esc_html__( 'Mobile-Only', 'coastlinevr' ),
		'footer' => esc_html__( 'Footer', 'coastlinevr' ),
        'footer-exp' => esc_html__( 'Expanded Footer', 'coastlinevr' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'coastlinevr_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
    
}
endif;
add_action( 'after_setup_theme', 'coastlinevr_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function coastlinevr_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'coastlinevr_content_width', 640 );
}
add_action( 'after_setup_theme', 'coastlinevr_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function coastlinevr_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'coastlinevr' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'coastlinevr' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name' => __( 'Home Featured Properties', 'coastlinevr' ),
		'id' => 'insta-home-featured-properties',
		'description' => __( 'Insta Home Featured Properties', 'instaparent' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s featured-properties">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	register_sidebar( array(
		'name' => __( 'Home Search Bar', 'coastlinevr' ),
		'id' => 'insta-home-qsearch',
		'description' => __( 'The Quick Seach into home slideshow', 'instaparent' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="title">',
		'after_title' => '</h3>',
	) );
	/*register_sidebar( array(
		'name' => __( 'Footer Social widget', 'instaparent' ),
		'id' => 'insta-bottom-social',
		'description' => __( 'The footer social widget', 'instaparent' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s social-btm-widget">',
		'after_widget' => '</div>',
		'before_title' => '<h3 class="title">',
		'after_title' => '</h3>',
	) );*/
}
add_action( 'widgets_init', 'coastlinevr_widgets_init' );


	
/**
 * Enqueue scripts and styles.
 */
function coastlinevr_scripts() {
    //wp_enqueue_script( 'jquery', 'https://code.jquery.com/jquery-1.8.3.min.js', array(), false ); 
    
	if ( !is_admin() ) {
		
        //wp_deregister_script( 'google-maps' );
        //wp_deregister_script( 'markermanager' ); 
        //wp_deregister_script( 'styled-markers' );
        //wp_deregister_script( 'jmapping' );
        //wp_deregister_script( 'flexslider' );
        //wp_deregister_script( 'multiselect' ); 
        //wp_deregister_script( 'jquery-min' );
        //wp_deregister_script( 'jquery-migrate-min' );
        //wp_deregister_script( 'jquery-ui-min' );
        //wp_deregister_script( 'jquery-ui-i18n-min' );
        //wp_deregister_script( 'jquery-ui-datepicker' );
        
	}
    
    //wp_enqueue_script( 'bapijs', getbapijsurl($apiKey), array('scripts') );
    //wp_enqueue_script( 'bapi-combined', '/bapi.combined.min.js?ver='.md5(urlHandler_bapi_js_combined_helper()), array('bapijs', 'typeahead', 'google-maps') );
    //wp_enqueue_script( 'custom-scripts', '/custom-scripts.js', array('bapi-combined') );
    
    //wp_deregister_script( 'jquery' );
    //wp_deregister_script( 'loadscriptjquery' );
	
	//wp_enqueue_style( 'coastlinevr-style', get_stylesheet_uri() );
	//wp_enqueue_script( 'coastlinevr-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20160907', true );
	//wp_enqueue_script( 'coastlinevr-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20160907', true );
    
    //wp_enqueue_script( 'pickadate', get_template_directory_uri().'/insta-common/js/bapi.ui.pickadate.translate.js');

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		//wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'coastlinevr_scripts' );

//DISABLE WP CORE CRAP
//remove_action('wp_head', 'rsd_link'); // Removes the Really Simple Discovery link
//remove_action('wp_head', 'wlwmanifest_link'); // Removes the Windows Live Writer link
//remove_action('wp_head', 'wp_generator'); // Removes the WordPress version
//remove_action('wp_head', 'start_post_rel_link'); // Removes the random post link
//remove_action('wp_head', 'index_rel_link'); // Removes the index page link
//remove_action('wp_head', 'adjacent_posts_rel_link'); // Removes the next and previous post links
//remove_action('wp_head', 'parent_post_rel_link', 10, 0); // remove parent post link
//remove_action('wp_head', 'feed_links', 2); // remove rss feed links *** RSS ***
//remove_action('wp_head', 'feed_links_extra', 3); // removes all rss feed links
//remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0 );
//remove_action('wp_head', 'loadscriptjquery');

function disable_wp_emojicons() {
	
  // all actions related to emojis
  //remove_action( 'admin_print_styles', 'print_emoji_styles' );
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  //remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

  // filter to remove TinyMCE emojis
  //add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
}
add_action( 'init', 'disable_wp_emojicons' );

function disable_emojicons_tinymce( $plugins ) {
  if ( is_array( $plugins ) ) {
    return array_diff( $plugins, array( 'wpemoji' ) );
  } else {
    return array();
  }
}

function sendEmail(
	$sendadmin = array(
		'to'		=> 'support@kerigan.com',
		'from'		=> 'Website <noreply@kerigan.com>',
		'subject'	=> 'Email from website'
	), 
	$templatetop = '<!doctype html><html><head><meta charset="utf-8"></head><body bgcolor="#E8EDE9" style="background-color:#E8EDE9;"><table cellpadding="0" cellspacing="0" border="0" align="center" style="width:650px; background:#FFF;" bgcolor="#FFF" ><tbody><tr><td>', 
	$emaildata = array(
		'headline'	=> 'This is an email from the website!', 
		'introcopy'	=> 'If we weren\'t testing, there would be stuff here.',
		'filedata' => '',
		'fileinfo' => ''
	), 
	$templatebot = '</td></tr></tbody></table>'){
	
		$eol = "\r\n";
		
		//build headers
		$headers = 'From: ' . $sendadmin['from'] . $eol;
		if($sendadmin['cc'] != ''){ $headers .= 'Cc: ' . $sendadmin['cc'] . $eol; }
		if($sendadmin['bcc'] != ''){ $headers .= 'Bcc: ' . $sendadmin['bcc'] . $eol; }
		
		$headers .= 'MIME-Version: 1.0' . $eol;
		
		
		//start building the email (if attachment)
		if($emaildata['fileinfo']!='' && $emaildata['filedata']!=''){
			
				
			//file info
			$mime_boundary = md5(time());
			$name = $emaildata['fileinfo']['filename'];
			$type = $emaildata['fileinfo']['filetype'];
			$data = $emaildata['filedata'];

			//mixed content type
        	$headers .= "Content-Type: multipart/mixed;boundary=\"" . $mime_boundary . "\"". $eol;
			
			//add attachment	 
			$emailcontent  = "--".$mime_boundary . $eol;
			$emailcontent .= "Content-Type: ".$type."; name=\"".$name."\"" . $eol;
			$emailcontent .= "Content-Transfer-Encoding: base64".$eol;
			$emailcontent .= "Content-Disposition: attachment".$eol.$eol;
			$emailcontent .= $data . $eol;
			$emailcontent .= "--".$mime_boundary . $eol; //transition to new content type
			
			//add html email content type
			$emailcontent .= "Content-Type: text/html; charset=\"utf-8\"" . $eol;
			$emailcontent .= "Content-Transfer-Encoding: 8bit" . $eol . $eol;
			
			// fancy html part
			$emailcontent .= $templatetop . $eol . $eol;
			$emailcontent .= '<h2>'.$emaildata['headline'].'</h2>';
			$emailcontent .= '<p>'.$emaildata['introcopy'].'</p>'; 
			$emailcontent .= $templatebot . $eol . $eol;
			
			$emailcontent .= "--".$mime_boundary."--" . $eol . $eol; //close text/html part
			
			
		}else{ //no attachment
			$headers .= 'Content-type: text/html; charset=utf-8' . $eol;
			$emailcontent  = $templatetop . $eol . $eol;
			$emailcontent .= '<h2>'.$emaildata['headline'].'</h2>';
			$emailcontent .= '<p>'.$emaildata['introcopy'].'</p>';
			$emailcontent .= $templatebot . $eol . $eol;
		}
				
		mail( $sendadmin['to'], $sendadmin['subject'], $emailcontent, $headers );
	
}

function wpa54064_inspect_scripts() {
    global $wp_scripts;
    foreach( $wp_scripts->queue as $handle ) :
        echo $handle . ' | ';
    endforeach;
}

function wpa54065_inspect_sstyles() {
    global $wp_styles ;
    foreach( $wp_styles ->queue as $handle ) :
        echo $handle . ' | ';
    endforeach;
}

//add_action( 'wp_print_scripts', 'wpa54064_inspect_scripts' );
//add_action( 'wp_print_scripts', 'wpa54065_inspect_sstyles' );

/**
 * Implement the Custom Header feature.
 */ 
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

//remove_action( 'wp_head','loadscriptjquery',99 );