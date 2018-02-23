<?php

/**
 * Class Kigo_Network_Cron
 * 
 * Class containing the static methods needed to launch a sync by cron.
 * 
 * The sync can be triggered by calling <domain>/wp-admin/admin-ajax.php?action=kigo_network_cron
 * WARMING: The sync endpoint can only be called when not logged in !
 * 
 * In case of a multi-sites installation, the sync can be triggered on any domain (or sub domain) 
 * and will do the sync for any website of the installation.
 * 
 * The Sync "lock" the execution of an other sync until the previous one is finished.
 * 
 */
class Kigo_Network_Cron
{

	const CUSTOM_WP_IS_LARGE_NETWORK	= 100000; //default (wp_is_large_network) value 10 000

	const CURL_TIMEOUT					= 300;
	const CURL_PARALLEL_CALLS			= 5;

	const ACTION_CRON					= 'kigo_network_cron';
	const ACTION_SITE_CRON				= 'kigo_site_cron';
	const GET_PARAM_FORCED_SYNC			= 'force_full_sync';
	const GET_PARAM_CRON_SECRET			= 'cron_secret';

	const ADV_LOCK_PROCESSING			= 'KIGO_CRON_LOCK';
	const LOGGLY_TAG					= 'wp_cron_sync';

	const MAX_SOLUTION_DATA_AGE			= 8035200; // If the solution's data from a websites hasn't been updated since 3 months we don't call the diff method. (This is done to prevent executing the cron on legacy sites)
	
	
	static public $wp_die_logs = array();
	static public $sync_error_logs = array();
	
	
	public static function do_sync() {
		global $wpdb;

		$debug_mode = defined( 'KIGO_DEBUG' ) && KIGO_DEBUG;

		// Do not log into New Relic, because this function is slow and we know why
		if( extension_loaded( 'newrelic' ) ) {
			newrelic_ignore_transaction();
		}

		//Check that cron is "enabled" and that the secret is correct
		if(
			!defined( 'KIGO_CRON_SECRET' ) ||
			!isset( $_GET[ self::GET_PARAM_CRON_SECRET ] ) ||
			$_GET[ self::GET_PARAM_CRON_SECRET ] !== KIGO_CRON_SECRET
		) {
			self::log( array(
				'message'	=> 'Missing/Invalid cron secret',
				'info'	=> $_SERVER
			) );
			self::handle_logs( $debug_mode );
			exit;
		}

		// Ensure that no other cron will run concurrently by acquiring an advisory lock (at MySQL database)
		if( ! $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 0)', self::ADV_LOCK_PROCESSING ) ) ) {
			self::log( 'Previous cron execution is not finished, could not acquire cron lock' );
			self::handle_logs( $debug_mode );
			exit;
		}

		$prevTimeTotal = microtime( true );
		if( is_multisite() ) {
			require_once( dirname( __FILE__ ) . '/ext/class-zebra-curl.php' );
			
			// Change the default value of wp_is_large_network necessary if # of sites reach the 10000
			add_filter( 'wp_is_large_network', array( 'Kigo_Network_Cron', 'custom_wp_is_large_network' ), 1, 3 );
			
			// Initialize the list of sites
			$sites = wp_get_sites( array( 'limit' => self::CUSTOM_WP_IS_LARGE_NETWORK, 'deleted' => 0, 'archived' => 0 ) );
			shuffle( $sites );
			
			// Filter the sites, not to trigger a sync for site where the solution data have not been updated since X months
			self::filter_old_sites( $sites );
			
			self::log( array( 'nb_sites' => count( $sites ) ) );
			
			//Do the Zebra cURL call (asynchronous calls)
			$curl = new Zebra_cURL();
			$curl->option( CURLOPT_TIMEOUT, self::CURL_TIMEOUT );
			$curl->threads = self::CURL_PARALLEL_CALLS;
				
			//Prepare URLs to be called
			$urls = array_map( array( 'Kigo_Network_Cron', 'generate_curl_urls' ), $sites );
			$urls = array_filter( $urls, function( $url ) { return is_string( $url ); } );
			
			$curl->get( $urls, array( 'Kigo_Network_Cron', 'zebra_curl_callback' ) );
		}
		else
		{
			set_error_handler( array( 'Kigo_Network_Cron', 'php_error_handler' ) );
			// Add our custom handler for wp_die() because some functions die on error, and we don't want the script to die !
			add_filter( 'wp_die_ajax_handler', array( 'Kigo_Network_Cron', 'kigo_cron_wp_die_handler_filter' ) );
			
			$site_cron = new Kigo_Site_Cron();
			self::log( $site_cron->sync_entities() ? true : $site_cron->_errors );
			
			restore_error_handler();
		}
		

		self::log( array( 'total_execution_time' => ( microtime( true ) - $prevTimeTotal ) ) );

		if( ! $wpdb->query( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', self::ADV_LOCK_PROCESSING ) ) ) {
			self::log( 'Could not release cron lock' );
		}

		// Echo the logs in debug mode or send them by mail
		self::handle_logs( $debug_mode );
		exit;
	}
	
	/**
	 * Network only: Callback called each time a website cron is finished
	 * 
	 * @param $result
	 */
	public static function zebra_curl_callback( $result ) {
		// cURL error
		if( 
			CURLE_OK !== $result->response[1] ||
			200 !== $result->info['http_code']
		) {
			self::log( $result );
			return;
		}
		
		if( true === ( $body = json_decode( $result->body, true ) ) ) {
			return;
		}
		
		if( !is_array( $body ) ) {
			self::log( array( $result->info, $body ) );
			return;
		}
		
		self::log( $body );
	}
	
	/**
	 * Filter to increase the number of sites/blog wp_get_sites will accept to return
	 * 
	 * @param $prevRet	not used
	 * @param $using	not used
	 * @param $count	number of blogs/sites in the network
	 *
	 * @return bool
	 */
	public static function custom_wp_is_large_network( $prevRet, $using, $count ) {
		return $count > self::CUSTOM_WP_IS_LARGE_NETWORK;
	}

	/**
	 * Add a filter returning the function to execute on wp_die()
	 * This is needed because BAPI functions dies on error, and the cron shouldn't stop on error.
	 * 
	 * @return array function to execute on wp_die
	 */
	public static function kigo_cron_wp_die_handler_filter() {
		return array( 'Kigo_Network_Cron', 'kigo_cron_wp_die_handler' );
	}

	/**
	 * Action to execute on wp_die(), overwrite the default behaviour.
	 * This is needed because BAPI functions dies on error, and the cron shouldn't stop on error.
	 * 
	 * @param $message
	 * @param string $title
	 * @param array $args
	 */
	public static function kigo_cron_wp_die_handler( $message, $title = '', $args = array() ) {
		self::$wp_die_logs[] = array(
			'message'	=> $message,
			'title'		=> $title,
			'args'		=> $args
		);
	}

	/**
	 * Do the sync process for a site/blog call remotely only.
	 * 
	 * @return array|bool
	 */
	public static function do_site_sync() {
		// Do not log into New Relic, because this function is slow and we know why
		if( extension_loaded( 'newrelic' ) ) {
			newrelic_ignore_transaction();
		}

		// Do not let the sync happen on the network blog
		if( BLOG_ID_CURRENT_SITE == get_current_blog_id() ) {
			header('Content-type: application/json');
			echo json_encode( false );
			exit;
		}

		set_error_handler( array( 'Kigo_Network_Cron', 'php_error_handler' ) );
		// Add our custom handler for wp_die() because some functions die on error, and we don't want the script to die !
		add_filter( 'wp_die_ajax_handler', array( 'Kigo_Network_Cron', 'kigo_cron_wp_die_handler_filter' ) );
		
		$site_cron = new Kigo_Site_Cron( isset( $_GET[ self::GET_PARAM_FORCED_SYNC ] ) && $_GET[ self::GET_PARAM_FORCED_SYNC ] == 1 );
		$ret = $site_cron->sync_entities() ? true : $site_cron->_errors;
		
		restore_error_handler();
		
		header('Content-type: application/json');
		echo json_encode( $ret );
		exit;
	}
	
	public static function php_error_handler( $code, $msg, $file, $line ) {
		Kigo_Network_Cron::$sync_error_logs[] = array(
			'code'	=> $code,
			'msg'	=> $msg,
			'file'	=> $file,
			'line'	=> $line
		);
	}

	/**
	 * Helper to generate the URL to be called to sync a site. Called only if in a multi-site context.
	 * 
	 * @param $blog
	 *
	 * @return null|string
	 */
	private static function generate_curl_urls( $blog ) {
		// Do not process on network blog
		if(
			BLOG_ID_CURRENT_SITE != $blog[ 'site_id' ] ||
			BLOG_ID_CURRENT_SITE == $blog[ 'blog_id' ] ||
			!is_string( $blog[ 'domain' ] )
		)  {
			return null; // This will be removed by the filter
		}
		
		return $blog[ 'domain' ] . '/wp-admin/admin-ajax.php?' . http_build_query( array( 'action' => self::ACTION_SITE_CRON ) );
	}
	
	/**
	 * Handle the errors log if any
	 * 
	 * @param $debug_mode
	 */
	private static function handle_logs( $echo_logs = false ) {
		if( count( $logs = array_merge( self::$sync_error_logs, self::$wp_die_logs ) ) ) {
			if( $echo_logs ){
				echo '<pre>';
				print_r( $logs );
				echo '</pre>';
			}
			else
				Loggly_logs::log( $logs, array( self::LOGGLY_TAG ) );
		}
	}

	/**
	 * Store the logs in a static array, or print_r and flush to display it in browser
	 * 
	 * @param $logs
	 */
	public static function log( $logs ) {
		if( defined( 'KIGO_DEBUG' ) && KIGO_DEBUG ) {
			echo '<pre>';
			print_r( $logs );
			echo '</pre>';
			ob_flush();
			flush();
		}
		else {
			self::$sync_error_logs[] = $logs;
		}
	}

	/**
	 * Filter the list of sites by checking whether the solution data has been updated since 3 months. 
	 * 
	 * @param $sites
	 */
	private static function filter_old_sites( &$sites ) {
		global $table_prefix;
		global $wpdb;
		
		$sites_considered_as_garbage = array();
		$sites = array_filter(
			$sites,
			function( $blog ) use ( $wpdb, $table_prefix, &$sites_considered_as_garbage ) {
				$ret = true;
				if(
					is_string( $bapi_solutiondata_lastmod = $wpdb->get_var( $wpdb->prepare( 'SELECT option_value FROM ' . $table_prefix . $blog[ 'blog_id' ] . '_options WHERE option_name=%s LIMIT 1', 'bapi_solutiondata_lastmod' ) ) ) &&
					intval( $bapi_solutiondata_lastmod ) < ( time() - Kigo_Network_Cron::MAX_SOLUTION_DATA_AGE )
				) {
					$ret = false;
					$sites_considered_as_garbage[] = $blog[ 'blog_id' ];
				}
				return $ret;
			}
		);
		
		// Log the list of sites considered as garbage
		if( count( $sites_considered_as_garbage ) ) {
			self::log( array( 'code' => 0, 'message' => 'Solution data not updated since more than ' . self::MAX_SOLUTION_DATA_AGE .'s', 'site_id' => $sites_considered_as_garbage ) );
		}
	}
}


/**
 * Class Kigo_Site_Cron
 */
class Kigo_Site_Cron
{
	const KIGO_CRON_DIFF_OPTION			= 'kigo_cron_diff';
	const ACTION_GET_LAST_CRON_EXEC		= 'kigo_get_last_cron_execution';
	
	
	public $_errors = array();
	
	private $_blog_id;
	private $_api_key;
	private $_bapi;
	private $_entity_diff_meth_ids;
	
	// To add an entity to the cron sync please add it bellow
	private $_default_entity_diff_meth_ids = array(
		//'entity'	=> array( 'diff_method_name' => <diff_method_name>, 'diff_id' => <diff_id>, 'last_update_timestamp' = 0 )
		'property'	=> array(
						'diff_method_name'		=> 'detailsdiffid',
						'diff_id'				=> -1,
						'last_update_timestamp'	=> 0,
						'first_cron_execution'	=> null
					)
	);
	
	
	public function __construct( $force_full_sync = false )
	{
		// Reload all option to correspond to the right site/blog
		bapi_wp_site_options();
		$this->_blog_id = get_current_blog_id();
		$this->_api_key = getbapiapikey();

		// Retrieve the correct bapi object
		// In this case all bapi calls (diff, get etc..) during cron execution are going to be called on this endpoint
		if(
			defined( 'BAPI_CRON_ENDPOINT' ) &&
			is_string( BAPI_CRON_ENDPOINT ) &&
			strlen( $cron_host = BAPI_CRON_ENDPOINT )
		) {
			//Check if "http(s)://" is included
			if(
				0 !== strpos( $cron_host, 'http://' ) ||
				0 !== strpos( $cron_host, 'https://' )
			) {
				$cron_host = 'http://' . $cron_host;
			}
			
			$this->_bapi = new BAPI( $this->_api_key, $cron_host );
		}
		else {
			$this->_bapi = getBAPIObj();
		}

		// Get the stored diff ids/methods for each entity and merge it with the default (allow to add entity in future)
		if(
			$force_full_sync ||
			!is_array( $entity_diff_meth_ids = json_decode( get_option( self::KIGO_CRON_DIFF_OPTION ), true ) )
		) {
			$this->_entity_diff_meth_ids = $this->_default_entity_diff_meth_ids;
			
			// Special case: if cron has never been executed and full sync is asked we need to set first_cron_execution = 1 otherwise it wont actually sync
			if( $force_full_sync ) {
				foreach( $this->_default_entity_diff_meth_ids as $entity => $info ) {
					$this->_entity_diff_meth_ids[ $entity ][ 'first_cron_execution' ] = 1;
				}
			}
		}
		else {
			$this->_entity_diff_meth_ids = array_merge( $this->_default_entity_diff_meth_ids, $entity_diff_meth_ids );
		}
	}
	
	/**
	 * Loop on each syncable entities present in $_default_entity_diff_meth_ids, call the diff method associated and do the sync on each entity 
	 * In case of error (return false), error information can be found in $this->_errors array.
	 * 
	 * @return bool
	 */
	public function sync_entities() {
		if(
			!is_string( $this->_api_key ) ||
			!strlen( $this->_api_key )
		) {
			$this->log_error( 0, 'Invalid API key' );
			return false;
		}
		
		$success_log= array();
		foreach( $this->_entity_diff_meth_ids as $entity => $options ) {
			// In case of error propagate, the error, don't update the diff_id and continue with the next entity
			
			// Call the diff method to get the changed entity's ids
			if( !is_array( $ids_to_update = $this->get_entity_diff( $entity, array( $options[ 'diff_method_name' ] => $options[ 'diff_id' ] ), $new_diff_id ) ) ) {
				$this->log_error( 1, 'Unable to process diff method', array( 'entity' => $entity, $options[ 'diff_method_name' ] => $options[ 'diff_id' ], 'url' => $this->url, 'cron_endpoint' => BAPI_CRON_ENDPOINT ) );
				continue;
			}
			
			// First time the cron is executed, we just save the returned diff id and the first execution timestamp, without syncing anything 
			if( null === $this->_entity_diff_meth_ids[ $entity ][ 'first_cron_execution' ] ) {
				$this->_entity_diff_meth_ids[ $entity ][ 'diff_id' ] = $new_diff_id;
				$this->_entity_diff_meth_ids[ $entity ][ 'first_cron_execution' ] = time();
				continue;
			}
			
			if( count( $ids_to_update ) > 0 ) {
				// Initialize the "cache" for get call (this reduce the number of calls by doing bulk calls of ids and caching the result
				$cache_options = array();// Taken from getMustache() function
				if( $entity == "property" ){
					$cache_options = array( "seo" => 1, "descrip" => 1, "avail" => 1, "rates" => 1, "reviews" => 1, "poi" => 1 );
				}
				else if($entity == "poi") {// Taken from getMustache() function
					$cache_options = array( "nearbyprops" => 1, "seo" => 1 );
				}
				
				// Initialize the "cache" of get calls, the return value is not checked because if it didn't worked, then get calls won't use the cache.
				$this->_bapi->init_get_cache( $entity, $ids_to_update, $cache_options );
				
				foreach( $ids_to_update as $id ) {
					
					if( !is_array( $seo = $this->get_seo_from_bapi_cache( $entity, $id ) ) ) {
						$this->log_error( 3, 'Unable to retrieve the SEO', array( 'entity' => $entity, 'entity_id' => $id ) );
						continue 2;
					}
					
					if( !is_a( ( $post = get_page_by_path( BAPISync::cleanurl( $seo[ "DetailURL" ] ) ) ), 'WP_Post' ) ) {
						continue 1;
					}
					
					if( !kigo_sync_entity( $post, $seo, true ) ) {
						$this->log_error( 4, 'Unable to process the sync', array( 'entity' => $entity, 'entity_id' => $id, 'SEO' => $seo ) );
						continue 2;
					}
				}
				$success_log[] = array( 'entity' => $entity, 'nb_of_updates' => count( $ids_to_update ) );
			}
			
			// If this point is reached that means the sync has been done without error, we can update the diff_id and save the timestamp
			$this->_entity_diff_meth_ids[ $entity ][ 'diff_id' ] = $new_diff_id;
			$this->_entity_diff_meth_ids[ $entity ][ 'last_update_timestamp' ] = time();
		}
		
		if( //If there were an error before, we don't want to update the option
			!count( $this->_errors ) &&
			(
				!is_string( $json_entity_diff_meth_ids = json_encode( $this->_entity_diff_meth_ids ) ) ||
				!update_option( self::KIGO_CRON_DIFF_OPTION, $json_entity_diff_meth_ids )
			)
		) {
			$this->log_error( 5, 'Unable to update the option', array( 'entity_diff_meth_ids' => $this->_entity_diff_meth_ids ) );
		}
		
		if( count( $success_log ) ) {
			$this->log_error( 10, 'Correct update', $success_log );
		}
		
		return ( 0 === count( $this->_errors ) );
	}
	
	public static function get_cron_info_option( $entity ) {
		if(
			!is_array( $entity_diff_meth_ids = json_decode( get_option( self::KIGO_CRON_DIFF_OPTION ), true ) ) ||
			
			!isset( $entity_diff_meth_ids[ $entity ] ) ||
			!is_array( $entity_diff_meth_ids[ $entity ] ) ||
			
			!isset( $entity_diff_meth_ids[ $entity ][ 'diff_method_name' ] ) ||
			!isset( $entity_diff_meth_ids[ $entity ][ 'diff_id' ] ) ||
			!isset( $entity_diff_meth_ids[ $entity ][ 'last_update_timestamp' ] ) ||
			!isset( $entity_diff_meth_ids[ $entity ][ 'first_cron_execution' ] )
		) {
			return null;
		}
		
		return $entity_diff_meth_ids[ $entity ];
	}
	
	public static function get_interval_last_update_prop() {

		if( !is_array( $cron_info = Kigo_Site_Cron::get_cron_info_option( 'property' ) ) ) {
			echo json_encode( array( 'success' => false ) );
			exit();
		}
		
		if(
			0 === ( $last_update_timestamp = $cron_info[ 'last_update_timestamp' ] ) ||
			( time() - $last_update_timestamp ) > 3600  //No sync since one hour
		) {
				echo json_encode( array(
					'success'	=> true,
					'too_much'	=> true
				) );
				exit();
		}
		
		$last_update = new DateTime();
		$last_update->setTimestamp( $last_update_timestamp );
		$now = new DateTime();
		$interval = $now->diff($last_update);
		
		echo json_encode( array(
			'success'	=> true,
			'too_much'	=> false,
			'formated'	=> $interval->format( '%i minute(s) %s second(s)' )
		) );
		exit();
	}

	/**
	 * Call the diff method for a given entity.
	 * Return an array containing the ids that need to be synced.
	 * 
	 * @param $entity
	 * @param $options			Array( <dif_method> => <previous_diff_id> )
	 * @param &$new_diff_id		Variable passed by reference: Will contain the new diff ID once the diff method is successfully called
	 *
	 * @return array|null
	 */
	private function get_entity_diff( $entity, $options, &$new_diff_id ) {
		if(
			!$this->_bapi->isvalid() ||
			
			!is_array( $deff_result = $this->_bapi->search( $entity, $options, true ) ) ||
			
			!isset( $deff_result[ 'status' ] ) ||
			1 !== $deff_result[ 'status' ] ||
			
			!isset( $deff_result[ 'result' ] ) ||
			!is_array( $ids_to_update = $deff_result[ 'result' ] ) ||
			
			!isset( $deff_result[ 'retparams' ] ) ||
			!is_numeric( $new_diff_id = $deff_result[ 'retparams' ][ 'diffid' ] )
		) {
			return null;
		}
		return $ids_to_update;
	}

	/**
	 * Return the SEO from the bapi cached data.
	 * Warning: the param seo=1 has to be passed to the call that init the cache.
	 * 
	 * @param $entity
	 * @param $id
	 *
	 * @return array|null
	 */
	private function get_seo_from_bapi_cache( $entity, $id ) {
		if(
			!isset( $this->_bapi->cache_get_call[ $entity ][ $id ] ) ||
			!is_array( $this->_bapi->cache_get_call[ $entity ][ $id ] ) ||
			
			!isset( $this->_bapi->cache_get_call[ $entity ][ $id ][ 'ContextData' ] ) ||
			!is_array( $this->_bapi->cache_get_call[ $entity ][ $id ][ 'ContextData' ] ) ||
			
			!isset( $this->_bapi->cache_get_call[ $entity ][ $id ][ 'ContextData' ][ 'SEO' ] ) ||
			!is_array( $seo = $this->_bapi->cache_get_call[ $entity ][ $id ][ 'ContextData' ][ 'SEO' ] )
		) {
			return null;
		}
		
		// For some weird reason pkid and entity are not set to property when received with the property info
		if( empty( $seo[ 'pkid' ] ) ) {
			$seo[ 'pkid' ] = $id;
		}
		if( empty( $seo[ 'entity' ] ) ) {
			$seo[ 'entity' ] = $entity;
		}
		
		return $seo;
	}

	/**
	 * @param $code
	 * @param string $msg
	 * @param null $blog_id
	 * @param array $options
	 */
	private function log_error( $code, $msg = '', $options = array() ) {
		$this->_errors[] = array(
			'code'		=> $code,
			'message'	=> $msg,
			'blog_id'	=> $this->_blog_id,
			'api_key'	=> $this->_api_key,
			'options'	=> $options
		);
	}
}
