<?php

class Kigo_Translations_List_Table extends WP_List_Table {

	const DEFAULT_ORDER_BY		= 'site_id';
	const DEFAULT_ORDER			= 'asc';
	const TRANSLATIONS_PER_PAGE	= 100;
	const LEGEND_CLASS			= 'legend';
	const BUTTON_CLASS			= 'button button-primary';


	public $_column_headers;
	public static $columns = array(
		'key'			=> 'Default English translation',
		'default_value'	=> 'Default translation',
		'value'			=> 'Your translation'
	);
	private static $sortable_colums = array(
		'key'			=> array( 'key', false ),
		'default_value'	=> array( 'default_value', false ),
		'value'			=> array( 'value', false )
	);


	public function __construct( $args = array( 'plural' => 'translations' ) ) {
		parent::__construct( $args );
		
		self::$columns['default_value'] .= ' (' .  kigo_get_site_language() . ')';
		$this->_column_headers = array( self::$columns, array(), self::$sortable_colums );
	}

	/**
	 * Prepare the list of items to be rendered
	 * 
	 * @return bool
	 */
	public function prepare_items() {
		$nb_items = null;
		if(
			!is_array( $this->items = $this->_prepare_items( $nb_items ) ) ||
			!is_int( $nb_items )
		) {
			return null;
		}

		$this->set_pagination_args( array(
			'total_items'	=> $nb_items,
			'per_page'		=> self::TRANSLATIONS_PER_PAGE,
		) );

		return true;
	}
        
        /**
         * The parent function is private now so we need our own version here.
         * @return array
         */
        function get_columns() {
            $columns = array(
                    'name' => 'Name',
                    'subject' => 'Subject'
                    );
            return $columns;
        }

	/**
	 * Specific rendering for the column value
	 * 
	 * @param $item
	 *
	 * @return string
	 */
	public function column_value( $item ) {
		return '<input type="text" data-value="'.$item['value'].'" data-key="'.$item['key'].'" value="'.$item['value'].'" class="overwritten-translation">' .
		'<button type="cancel" data-key="'.$item['key'].'" class="i18n-cancel button button-small">Cancel</button>';
	}

	/**
	 * Default rendering for columns
	 * 
	 * @param $item
	 * @param $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Render the save button and the search box
	 * 
	 * @param string $which
	 */
	public  function extra_tablenav( $which ) {
		if( 'top' === $which ) {
			?><div style="float:left">
				<form method="get">
					<span id="save-translations-spinner" class="spinner" style="visibility:visible; display: none;"></span>
					<a class="button-primary i18n-save" data-lang="<?php echo kigo_get_site_language(); ?>">Save changes</a>
					<input type="hidden" name="page" value="<?php echo esc_attr( $_REQUEST['page'] ); ?>" />
					<?php $this->search_box( __('Search') , 'translation_search' ) ?>
				</form>
			</div><?php
		}
	}


	// Retrieve all items
	/**
	 * Retrieve all the default and overwritten translations, and apply search and sort on it.
	 * 
	 * @param $nb_items
	 *
	 * @return array|null
	 */
	private function _prepare_items( &$nb_items ) {
		if(
			!is_string( $lang_code = kigo_get_site_language() ) ||
			!is_array( $translations = kigo_I18n::get_translations_for_edit( $lang_code ) ) 
		) {
			Loggly_logs::log( array( 'msg' => 'Unable to retrieve translation for edit', 'lang_code' => $lang_code ) , array( kigo_I18n::LOGGLY_LOG_TAG_FATAL ) );
			return null;
		}
		
		// Handle the input search
		if(
			isset( $_GET[ 's' ] ) &&
			strlen( $search = wp_unslash( trim( $_GET[ 's' ] ) ) )
		) {
			$translations = array_filter(
				$translations,
				function ( $item ) use ( $search )  {
					return (
						false !== stripos( $item['key'], $search ) ||
						false !== stripos( $item['default_value'], $search ) ||
						false !== stripos( $item['value'], $search )
					);
				}
			);
		}
		
		$nb_items = count( $translations );
		
		if(
			!isset( $_REQUEST['orderby'] ) ||
			!strlen( $orderby = $_REQUEST['orderby'] ) ||
			!in_array( $orderby, array_keys( self::$sortable_colums ) )
		) {
			$orderby = 'key';
		}
		
		if(
			!isset( $_REQUEST['order'] ) ||
			!strlen( $order = $_REQUEST['order'] ) ||
			!in_array( $orderby, array( 'asc', 'desc' ) )
		) {
			$order = 'asc';
		}
		
		uasort(
			$translations,
			function( $item_a, $item_b ) use ( $orderby, $order ) {
				return ( 'desc' === $order ? -1 : 1) * strnatcasecmp( $item_a[ $orderby ], $item_b[ $orderby ] );
			}
		);
		
		return array_slice( $translations, ( ( $this->get_pagenum() - 1 ) * Kigo_Translations_List_Table::TRANSLATIONS_PER_PAGE ), Kigo_Translations_List_Table::TRANSLATIONS_PER_PAGE, true );
	}
}
