<?php

class Kigo_Ssl_Config {

	const SSL_OPTION_NAME	= 'kigo_ssl_script';

	private $ssl_options = array(
		'custom' => array(
			'label'		=> 'Custom',
			'selected'	=> false,
			'content'	=> ''
		),
		'godaddy' => array(
			'label'		=> 'GoDaddy',
			'selected'	=> false,
			'content'	=> '<span id="siteseal"><script type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=kZi7XeoWWG7MnbEGNzusNQHN3AJeOFLGefXuNPmCospvEyTB0Spn6r"></script></span>'
		)
	);
	//Any loaded seal has to be wrapped into a div with the id SSLcontent
	private $ssl_wrapper = array(
		'open'	=> '<div id="SSLcontent">',
		'close'	=> '</div>'
	);

	public function __construct() {
		if( is_string( $ssl_support_script = get_option( self::SSL_OPTION_NAME, null ) ) ) {
			$this->ssl_options[ 'custom' ][ 'content' ] = stripslashes( $ssl_support_script );
			$this->ssl_options[ 'custom' ][ 'selected' ] = true;
		}
		else {
			$this->ssl_options[ 'godaddy' ][ 'selected' ] = true;
		}
	}
	
	public function get_ssl_seal() {
		if( $this->ssl_options[ 'custom' ][ 'selected' ] ) {
			return $this->ssl_wrapper[ 'open' ] . $this->ssl_options[ 'custom' ][ 'content' ] . $this->ssl_wrapper[ 'close' ];
		}

		return $this->ssl_wrapper[ 'open' ] . $this->ssl_options[ 'godaddy' ][ 'content' ] . $this->ssl_wrapper[ 'close' ];
	}
	 
	public function get_ssl_options_to_json() {
		return json_encode( $this->ssl_options );
	}
	
	public function save_custom( $selected_value, $custom_script = null ) {
		if( !is_string( $selected_value ) ) {
			return false;
		}
		
		if( 'godaddy' === $selected_value ) {
			$this->switch_selected( $selected_value );
			if( null === get_option( self::SSL_OPTION_NAME, null ) ) {
				return true;
			}
			return delete_option( self::SSL_OPTION_NAME );
		}
		
		if(
			'custom' === $selected_value ||
			!is_string( $custom_script )
		) {
			if( $this->ssl_options[ 'custom' ][ 'content' ] === stripslashes( $custom_script ) ) {
				return true;
			}
			$this->ssl_options[ 'custom' ][ 'content' ] = stripslashes( $custom_script );
			$this->switch_selected( $selected_value );
			return update_option( self::SSL_OPTION_NAME, $custom_script );
		}
		
		return false;
	}
	
	private function switch_selected( $selected_key ) {
		foreach ( $this->ssl_options as $key => $option ) {
			$this->ssl_options[ $key ][ 'selected' ] = ( $key === $selected_key );
		}
	}
}
