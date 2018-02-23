<?php
add_action( 'admin_menu', 'kigo_add_amenities_menu' );
add_action( 'admin_init', 'kigo_amenities_settings_init' );

//TBD: Move this page add to admin.php in the section with all other admin page defs.
function kigo_add_amenities_menu(  ) { 

	add_submenu_page( 'site_settings_general', 'Amenities Settings', 'Amenities', 'administrator', 'kigo_amenities', 'kigo_amenities_options_page' );

}

function kigo_amenities_settings_init(  ) { 

	register_setting( 'amenitiesPage', 'kigo_amenities_settings' );

	add_settings_section(
		'kigo_amenitiesPage_section', 
		__( 'Amenities', 'kigo' ), 
		'kigo_settings_section_callback', 
		'amenitiesPage'
	);

	/*
	** Fields
	*/
	add_settings_field( 
		'kigo_text_field_0', 
		__( 'Settings field description', 'kigo' ), 
		'kigo_text_field_0_render', 
		'amenitiesPage', 
		'kigo_amenitiesPage_section' 
	);

	add_settings_field( 
		'kigo_checkbox_field_1', 
		__( 'Settings field description', 'kigo' ), 
		'kigo_checkbox_field_1_render', 
		'amenitiesPage', 
		'kigo_amenitiesPage_section' 
	);

	add_settings_field( 
		'kigo_select_field_2', 
		__( 'Settings field description', 'kigo' ), 
		'kigo_select_field_2_render', 
		'amenitiesPage', 
		'kigo_amenitiesPage_section' 
	);


}


function kigo_text_field_0_render(  ) { 

	$options = get_option( 'kigo_amenities_settings' );
	?>
	<input type='text' name='kigo_amenities_settings[kigo_text_field_0]' value='<?php echo $options['kigo_text_field_0']; ?>'>
	<?php

}


function kigo_checkbox_field_1_render(  ) { 

	$options = get_option( 'kigo_amenities_settings' );
	?>
	<input type='checkbox' name='kigo_amenities_settings[kigo_checkbox_field_1]' <?php checked( $options['kigo_checkbox_field_1'], 1 ); ?> value='1'>
	<?php

}


function kigo_select_field_2_render(  ) { 

	$options = get_option( 'kigo_amenities_settings' );
	?>
	<select name='kigo_amenities_settings[kigo_select_field_2]'>
		<option value='1' <?php selected( $options['kigo_select_field_2'], 1 ); ?>>Option 1</option>
		<option value='2' <?php selected( $options['kigo_select_field_2'], 2 ); ?>>Option 2</option>
	</select>

<?php

}


function kigo_settings_section_callback(  ) { 

	echo __( 'Amenities are defined here.', 'kigo' );

}


function kigo_amenities_options_page(  ) { 
	if ( ! class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}

	class Customers_List extends WP_List_Table {

		var $example_data = array(
			array(
				'amenity'	=> 'Microwave', 
				'type'		=> 'Interior', 
				'count'		=> 10,
			),
			array(
				'amenity'	=> 'Pool', 
				'type'		=> 'Exterior', 
				'count'		=> 5,
			),
			array(
				'amenity'	=> 'Hot Tub', 
				'type'		=> 'Exterior', 
				'count'		=> 2,
			),
			array(
				'amenity'	=> 'Maid Service', 
				'type'		=> 'Maintenance', 
				'count'		=> 1,
			),
		);

		/** Class constructor */
		public function __construct() {

			parent::__construct( [
				'singular' => __( 'Customer', 'sp' ), //singular name of the listed records
				'plural'   => __( 'Customers', 'sp' ), //plural name of the listed records
				'ajax'     => false //does this table support ajax?
			] );

		}

		function get_data() {
			$bapisync = new BAPISync();
			return $bapisync->do_amenity_sync();
		}

		function get_columns(){
		  $columns = array(
		    'amenity' => 'Amenity',
		    'type'    => 'Type',
		    'count'      => 'Count'
		  );
		  return $columns;
		}

		function prepare_items() {
		  $columns = $this->get_columns();
		  $hidden = array();
		  $sortable = $this->get_sortable_columns();
		  $this->_column_headers = array($columns, $hidden, $sortable);
		  //Sorting
		  usort( $this->get_data(), array( &$this, 'usort_reorder' ) );
		  $this->items = $this->get_data();
		}

		function column_default( $item, $column_name ) {
		  switch( $column_name ) { 
		    case 'amenity':
		    case 'type':
		    case 'count':
		      return $item[ $column_name ];
		    default:
		      return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
		  }
		}

		function get_sortable_columns() {
		  $sortable_columns = array(
		    'amenity'  => array('amenity',true),
		    'type' => array('type',true),
		    'count'   => array('count',false)
		  );
		  return $sortable_columns;
		}

		function usort_reorder( $a, $b ) {
		  // If no sort, default to amenity
		  $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'amenity';
		  // If no order, default to asc
		  $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
		  // Determine sort order
		  if($orderby == 'count') {
		  	$result = ($a[$orderby] < $b[$orderby]) ? -1 : (($a[$orderby] > $b[$orderby]) ? 1 : 0);
		  } else {
		  	$result = strcmp( $a[$orderby], $b[$orderby] );
		  }
		  
		  // Send final sort direction to usort
		  return ( $order === 'asc' ) ? $result : -$result;
		}

	}

	$table = new Customers_List();
	$table->prepare_items();
	$table->display();

	?>
	<form action='options.php' method='post'>

		<?php
		settings_fields( 'amenitiesPage' );
		do_settings_sections( 'amenitiesPage' );
		submit_button();
		?>

	</form>
	<?php

}

?>