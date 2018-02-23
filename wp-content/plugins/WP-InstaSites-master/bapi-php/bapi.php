<?php

class BAPI
{
	const BAPI_USER_AGENT = 'InstaSites Agent';
	const WWW_FORM_URLENCODED = 'application/x-www-form-urlencoded';
	const MAX_NB_BULK_GET_IDS = 20; // This value can not be higher than 20 by restriction of the app.
	const MAX_CACHED_IDS = 250; // This is to avoid exploding the memory when caching the get calls

	private $apikey;
    private $baseURL;
	private $getOptions;
	
	public  $cache_get_call = array();
	private $use_cache_in_get_calls = array();
	
	public function __construct($apikey, $baseURL) {
		$this->apikey = $apikey;
		$this->baseURL = $baseURL;


		$ssl = array(
			'sslverify'	=> true
		);
		if(defined('BYPASS_SSL_VERIFY') && BYSASS_SSL_VERIFY || (defined('KIGO_DEBUG') && KIGO_DEBUG)) {
 			$ssl['sslverify'] = false;
 			//$ssl['allow_self_signed'] = true;
 		}

		$this->getWPOptions = array(
			'user-agent'	=> 'InstaSites Agent',
			'headers'		=> array(
				'Referer'		=> "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']
			),
			'sslverify'		=> $ssl['sslverify']
		);

		if( defined('BAPI_TIMEOUT') && $timeout = BAPI_TIMEOUT) {
			$this->getWPOptions['timeout'] = $timeout;
		}
	}
	
	public function isvalid() {
		if (empty($this->apikey)) return false;
		if (empty($this->baseURL)) return false;
		return true;
	}

	public function getApikey() {
		return $this->apikey;
	}

	public function getBaseURL() {
		return $this->baseURL;
	}

	public function hasResponse($res) { 
		if(!is_wp_error($res))
			return true;

		if(is_array($res))
			return !empty($res['body']) && (200 >= $res['response']['code']) || ($res['response']['code'] >= 299); 
	}
		
	public function getcontext($jsondecode=true,$debugmode=0) {
		if (!$this->isvalid()) { return null; }

		$url = $this->baseURL . '/js/bapi.context?apikey=' . $this->apikey; 
		//$c = file_get_contents($url,FALSE,$this->getOptions) or wp_die('Error Retrieving Context','Oops!');

		$c = get_transient('kigo_context');
		if( empty($c) || KIGO_DEBUG ) {
			$c = wp_remote_get($url, $this->getWPOptions);

			if($this->hasResponse($c)) {
				set_transient('kigo_context', $c, HOUR_IN_SECONDS);
			}
		} 

		global $getContextURL;
		$getContextURL = $url;
		add_action('wp_head','bapi_add_context_meta',1);	

		if(!is_wp_error($c)) { 
			$res = json_decode($c['body'],TRUE);
			return $res;
		} else {
			write_log( 'Error Retrieving Context: '.$c->get_error_message() );
			return $c;
		}
	}
	
	/**
	 * @Deprecated
	 * 
	 * @param $jsondecode
	 * @param int $debugmode
	 *
	 * @return array|mixed|null|string
	 */
	public function gettextdata($jsondecode=true,$debugmode=0) {
		if (!$this->isvalid()) { return null; }
		$url = $this->baseURL . '/ws/?method=get&entity=textdata&apikey=' . $this->apikey;

		//$c = file_get_contents($url,FALSE,$this->getOptions) or wp_die('Error Retrieving TextData','Oops!');
		$c = get_transient('kigo_textdata');
		if( empty($c) || KIGO_DEBUG ) {
			$c = wp_remote_get($url, $this->getWPOptions);

			if($this->hasResponse($c)) {
				set_transient('kigo_textdata', $c, HOUR_IN_SECONDS);
			}
		} 

		if( is_wp_error($c) ) { 
			write_log( 'Error Retrieving TextData: '.$c->get_error_message() ); 
		}

		
		global $textDataURL;
		$textDataURL = $url;
		add_action('wp_head','bapi_add_textdata_meta',1);	
		
		if ($jsondecode) {return json_decode($c['body'],TRUE); }
		return $c;
	}
	
	public function getseodata($jsondecode=true,$debugmode=0) {
		if (!$this->isvalid()) { return null; }
		$url = $this->baseURL . '/ws/?method=get&entity=seo&apikey=' . $this->apikey;

		//$c = file_get_contents($url,FALSE,$this->getOptions) or wp_die('Error Retrieving Keywords','Oops!');
		$c = get_transient('kigo_seodata');
		if( empty($c) || KIGO_DEBUG ) {
			$c = wp_remote_get($url, $this->getWPOptions);

			if($this->hasResponse($c)) {
				set_transient('kigo_seodata', $c, HOUR_IN_SECONDS);
			}
		} 
		
		global $seoDataURL;
		$seoDataURL = $url;
		add_action('wp_head','bapi_add_seo_meta',1);	

		if(is_wp_error($c))
			return;
		

		if ($jsondecode && !is_wp_error($c)) { return json_decode($c['body'],TRUE); }

		if(is_wp_error($c)) {
			write_log( 'Error Retrieving Keywords: '.$c->get_error_message() );
			return $c;
		}

		return $c;
	}
	
	public function getSolutionConfig($apikey,$debugmode=0){
		$url = $this->baseURL . "/ws/?method=getconfig&apikey=".$apikey;

		//$json = file_get_contents($url,FALSE,$this->getOptions) or wp_die('Error Retrieving Solution Config','Oops!');
		$c = get_transient('kigo_solution_config');
		if( empty($c) || KIGO_DEBUG ) {
			$c = wp_remote_get($url, $this->getWPOptions);

			if($this->hasResponse($c)) {
				set_transient('kigo_solution_config', $c, HOUR_IN_SECONDS);
			}
		} 

		if(is_wp_error($c)) 
			return;

		$res = json_decode($c['body'],TRUE);

		return $res;

	}
	
	public function search($entity,$options=null,$jsondecode=true) {
		if (!$this->isvalid()) { return null; }
		$url = $this->baseURL . "/ws/?method=search&apikey=" . $this->apikey . "&entity=" . $entity;
		if (!empty($options)) { $url = $url . "&" . http_build_query($options); }		

		$c = wp_remote_get($url, $this->getWPOptions);
		if( is_wp_error($c) ) { write_log( 'Error Retrieving Search Results: '.$c->get_error_message() ); return; }

		if (empty($jsondecode) || $jsondecode) { return json_decode($c['body'],TRUE); }
		return $c;
	}
	public function quicksearch($entity,$options=null,$jsondecode=true) {
		if (!$this->isvalid()) { return null; }
		$url = $this->baseURL . "/ws/?method=quicksearch&apikey=" . $this->apikey . "&entity=" . $entity;
		if (!empty($options)) { $url = $url . "&" . http_build_query($options); }		

		$c = wp_remote_get($url, $this->getWPOptions);
		if( is_wp_error($c) ) { 
			write_log( 'Error Retrieving Quick Search Results: '.$c->get_error_message() ); 
		} elseif (empty($jsondecode) || $jsondecode) { 
			return json_decode($c['body'],TRUE); 
		} else { 
			return $c; 
		}

	}

	/**
	 * Generate a bulk call to the function get with an array of ids.
	 * This allow to reduce the number of call to Kigo app
	 * 
	 * @param $entity string
	 * @param $ids array
	 * @param null $options array default value null
	 *
	 * @return bool
	 */
	public function init_get_cache( $entity, $ids, $options = null ) {
		if( count($ids ) > self::MAX_CACHED_IDS ) {
			$this->use_cache_in_get_calls[ $entity ] = false;
			return false;
		}
		$this->cache_get_call[ $entity ] = array();
		// Split the ids into small chunks to avoid errors
		$ids_chunks = array_chunk( $ids, self::MAX_NB_BULK_GET_IDS );
		
		// Set the page size option to receive the correct amount of results
		$options = array_merge( $options, array( 'pagesize' => self::MAX_NB_BULK_GET_IDS ) );
		
		// Process one call by chunks
		foreach( $ids_chunks as $ids_chunk ) {
			if(
				!is_array( $response = $this->get( $entity, $ids_chunk, $options ) ) ||
				
				!isset( $response[ 'status' ] ) ||
				1 !== $response[ 'status' ] ||
				
				!isset( $response[ 'result' ] ) ||
				!is_array( $response[ 'result' ] ) ||
				
				count( $response[ 'result' ] ) !== count( $ids_chunk )
			) {
				return false;
			}
			
			foreach( $response[ 'result' ] as $result ) {
				$this->cache_get_call[ $entity ][ $result[ 'ID' ] ] = $result;
			}
		}
		
		// Set to use the cache only if all the calls have been successful 
		$this->use_cache_in_get_calls[ $entity ] = true;
		
		return true;
	}
	
	public function get($entity,$ids,$options=null) {
		if (!$this->isvalid()) { return null; }

		if(!is_array($ids)) 
			$ids = array($ids);
		
		// In case init_get_cache() has been called before, try to retrieve the values from the local cache
		if(
			isset( $this->use_cache_in_get_calls[ $entity ] ) &&
			$this->use_cache_in_get_calls[ $entity ]
		) {
			$fake_response = array(
				'status'	=> 1,
				'result'	=> array()
			);
			
			$error = false;
			foreach( $ids as $id ) {
				if( !isset( $this->cache_get_call[ $entity ][ $id ] ) ) {
					$error = true;
					break;
				}
				$fake_response[ 'result' ][] = $this->cache_get_call[ $entity ][ $id ];
			}
			
			if( !$error ) { // In case of error, retrieving one or more id from the cache, default to the get call.
				return $fake_response;
			}
		}


		$url = $this->baseURL . "/ws/?method=get&apikey=" . $this->apikey . "&entity=" . $entity . '&ids=' . implode(",", $ids);
		if (!empty($options)) { $url = $url . "&" . http_build_query($options); }

		// adding <meta> on the page, for debugging purposes
		global $entityUpdateURL;
		$entityUpdateURL = $url;
		add_action('wp_head', 'bapi_add_entity_meta', 1);

		$response = wp_remote_get(
			$url,
			array(
				'sslverify'	=>	!(defined('KIGO_DEBUG') && KIGO_DEBUG),
				'timeout'	=>	50,
				'headers'	=>	array( 'User-Agent' => 'InstaSites Agent' )
			)
		);

		if(is_wp_error($response)) {
			Loggly_logs::log( array( 'BAPI get faillure',  $response->get_error_message() ), array( 'wp_bapi' ) );
			return false;
		}

		if(
			is_array($response) &&

			isset($response['response']) &&
			is_array($response['response']) &&

		    isset($response['response']['code']) &&
			$response['response']['code'] == 200 &&

			isset($response['body']) &&
		    is_string($response['body'])
		) {
			// BAPI also returns 200 when there are problems. So if the entity doesn't seem to be correctly retrieved, consider it a resource not found (404)
			if(
				!self::json_decode($decoded, $response['body']) ||

				isset($decoded['error']) ||

				!isset($decoded['status']) ||
					$decoded['status'] != '1' ||

				!isset($decoded['result']) ||
					!is_array($decoded['result'])
			) {
				return true; // "not found"
			}

			return $decoded;
		}

		return false;
	}

	//error testing for send objects to the api
	public function error($response){
		if( is_wp_error( $response ) ) {
   		$error_message = $response->get_error_message();
   		echo "Something went wrong: $error_message";
		echo $response;
		} else {
	   		echo "Something went right";
			echo $response;
 		}
	}
	//saves to our api
	public function save($jsonObj, $apiKey) {
		if (!$this->isvalid()) { return null; }
		$url =$this->baseURL."/ws/?method=save&apikey=".$apiKey."&entity=seo";
		//print_r($url); exit();
		$response = wp_remote_post( $url, array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array('content-type'=>'application/x-www-form-urlencoded','User-Agent'=>'InstaSites Agent'),
			'body' => $jsonObj,
			'cookies' => array(),
			'sslverify' => !( defined( 'KIGO_DEBUG' ) && KIGO_DEBUG ) // in dev mode, allow self-signed certs
		)
		);
		if( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "Something went wrong while saving SEO data: ".$error_message; 
			exit();
			//echo $response;
		} else {
			// print_r($jsonObj);
	   		// print_r($response);
			// exit();
 		}
		
	}

	static private function json_decode(&$decoded, $json, $assoc=true)
	{
		if(!is_string($json) || !strlen($json))
			return false;

		if(($decoded = @json_decode($json, $assoc)) === null)
			return json_last_error() == JSON_ERROR_NONE;

		return true;
	}
}
