<?php

class kigo_I18n {

	const I18N_FOLDER_NAME			= 'language';
	const I18N_FILE_EXTENSION		= 'tsv';
	const I18N_FILE_SEPARATOR		= "\t";
	const I18N_FILE_LINE_SEPARATOR	= "\n";

	const NETWORK_I18N_OPTION		= 'kigo_default_translations';
	const SITE_I18N_OPTION			= 'kigo_overwritten_translations';

	const DEFAULT_LANGUAGE_CODE		= 'en';

	const LOGGLY_LOG_TAG_FATAL		= 'wp_i18n_fatal';
	const LOGGLY_LOG_TAG_WARN		= 'wp_i18n_warn';

	const ACTION_UPDATE_TRANSLATION_FILES	= 'kigo_update_translation_files';
	const ACTION_SAVE_CUSTOM_TRANSLATION	= 'kigo_save_translations';


	/**
	 * This list is coming from google translate API; please uncomment and run file creation to add support for a new language
	 * 
	 * IMPORTANT: Google translate API doesn't support culture codes (ie. be-BE) but, translation files for specific cultures can be added manual and enabled in this list.
	 */
	private static $enabled_language = array(
		//'af'	=> 'Afrikaans',
		//'sq'	=> 'Albanian',
		'ar'	=> 'Arabic',
		//'hy'	=> 'Armenian',
		//'az'	=> 'Azerbaijani',
		//'eu'	=> 'Basque',
		//'be'	=> 'Belarusian',
		//'bn'	=> 'Bengali',
		//'bs'	=> 'Bosnian',
		'bg'	=> 'Bulgarian',
		'ca'	=> 'Catalan',
		//'ceb'	=> 'Cebuano',
		//'ny'	=> 'Chichewa',
		'zh'	=> 'Chinese (Simplified)',
		'zh-TW'	=> 'Chinese (Traditional)',
		'hr'	=> 'Croatian',
		'cs'	=> 'Czech',
		'da'	=> 'Danish',
		'nl'	=> 'Dutch',
		'en'	=> 'English',
		//'eo'	=> 'Esperanto',
		//'et'	=> 'Estonian',
		//'tl'	=> 'Filipino',
		//'fi'	=> 'Finnish',
		'fr'	=> 'French',
		'gl'	=> 'Galician',
		//'ka'	=> 'Georgian',
		'de'	=> 'German',
		'el'	=> 'Greek',
		//'gu'	=> 'Gujarati',
		//'ht'	=> 'Haitian Creole',
		//'ha'	=> 'Hausa',
		//'iw'	=> 'Hebrew',
		//'hi'	=> 'Hindi',
		//'hmn'	=> 'Hmong',
		'hu'	=> 'Hungarian',
		'is'	=> 'Icelandic',
		//'ig'	=> 'Igbo',
		'id'	=> 'Indonesian',
		//'ga'	=> 'Irish',
		'it'	=> 'Italian',
		'ja'	=> 'Japanese',
		//'jw'	=> 'Javanese',
		//'kn'	=> 'Kannada',
		//'kk'	=> 'Kazakh',
		//'km'	=> 'Khmer',
		//'ko'	=> 'Korean',
		//'lo'	=> 'Lao',
		//'la'	=> 'Latin',
		//'lv'	=> 'Latvian',
		//'lt'	=> 'Lithuanian',
		//'mk'	=> 'Macedonian',
		//'mg'	=> 'Malagasy',
		//'ms'	=> 'Malay',
		//'ml'	=> 'Malayalam',
		//'mt'	=> 'Maltese',
		//'mi'	=> 'Maori',
		//'mr'	=> 'Marathi',
		//'mn'	=> 'Mongolian',
		//'my'	=> 'Myanmar (Burmese)',
		//'ne'	=> 'Nepali',
		'no'	=> 'Norwegian',
		//'fa'	=> 'Persian',
		//'pl'	=> 'Polish',
		'pt'	=> 'Portuguese',
		'pt-BR'	=> 'Portuguese Brazil',
		//'pa'	=> 'Punjabi',
		'ro'	=> 'Romanian',
		'ru'	=> 'Russian',
		//'sr'	=> 'Serbian',
		//'st'	=> 'Sesotho',
		//'si'	=> 'Sinhala',
		//'sk'	=> 'Slovak',
		//'sl'	=> 'Slovenian',
		//'so'	=> 'Somali',
		'es'	=> 'Spanish',
		//'su'	=> 'Sundanese',
		//'sw'	=> 'Swahili',
		'sv'	=> 'Swedish',
		//'tg'	=> 'Tajik',
		//'ta'	=> 'Tamil',
		//'te'	=> 'Telugu',
		'th'	=> 'Thai',
		'tr'	=> 'Turkish',
		//'uk'	=> 'Ukrainian',
		//'ur'	=> 'Urdu',
		//'uz'	=> 'Uzbek',
		'vi'	=> 'Vietnamese',
		//'cy'	=> 'Welsh',
		//'yi'	=> 'Yiddish',
		//'yo'	=> 'Yoruba',
		//'zu'	=> 'Zulu'
	);

	private static $array_i18n_default = null;
	private static $array_i18n_overwritten = null;


	/**
	 * Returns the text data for a specified language code (en, en-US, fr, fr-FR etc..)
	 * 
	 * The function first retrieve the default translations (network wide) for the "parent" language (ie. fr).
	 * Then if a culture code (ie. fr-BE) is given it search for any default translation (network wide) for the specific culture (ie. fr-BE) and merge them with the parent once.
	 * (A specific culture may have only a subset of keys, which differ from the parent language)
	 * Finally it checks any overwritten (site wide) translated sentence for that specific culture.
	 * 
	 * IMPORTANT: If for any reason the lang_code and its parents is not found a log is made in Loggly and the default language is returned
	 * 
	 * @param $lang_code
	 *
	 * @return array|null
	 */
	public static function get_translations( $lang_code = 'en' ) {
		if(
			!is_string( $lang_code ) ||
			!strlen( $lang_code ) ||
			
			!is_array( $array_i18n_default = self::get_default_translations( $lang_code ) )
		) {
			Loggly_logs::log( array( 'msg' => ( is_array( $array_i18n_default ) ? 'Invalide language code' : 'Unable to retrieve default translations' ), 'lang_code' => $lang_code ) , array( self::LOGGLY_LOG_TAG_FATAL ) );
			return null;
		}
		
		if( !is_array( $array_i18n_overwritten = self::get_i18n_site_array( $lang_code ) ) ) {
			$array_i18n_overwritten[ $lang_code ] = array();
		}
		
		return array_merge( $array_i18n_default, $array_i18n_overwritten );
	}

	/**
	 * Returns the text data for with a specific output for the GUI (including default + overwritten value)
	 * 
	 * @param $lang_code
	 *
	 * @return array|null
	 */
	public static function get_translations_for_edit( $lang_code ) { 
		if(
			!is_string( $lang_code ) ||
			!strlen( $lang_code ) ||
			
			!is_array( $array_i18n_default = self::get_default_translations( $lang_code ) )
		) {
			return null;
		}
		
		if( !is_array( $array_i18n_overwritten = self::get_i18n_site_array( $lang_code ) ) ) {
			$array_i18n_overwritten = array();
		}
		
		array_walk(
			$array_i18n_default,
			function( &$item, $key ) use ( $array_i18n_overwritten ) {
				$item = array(
					'key'			=> $key,
					'default_value'	=> $item,
					'value'			=> isset( $array_i18n_overwritten[ $key ] ) ? $array_i18n_overwritten[ $key ] : ''
				);
			}
		);
		

		return $array_i18n_default;
	}

	/**
	 * Generate/Update the network wide option containing all the translation in all the supported languages, based on the translation files.
	 * 
	 * IMPORTANT: THis functions should be called by kigo_on_plugin_update, every time a change is done in any translation files.
	 * 
	 * @return bool
	 */
	public static function update_i18n_network_option() {
		// List all the files existing in the language folder
		if(
			!is_string( $folder_name = get_kigo_plugin_path( self::I18N_FOLDER_NAME ) ) ||
			!is_dir( $folder_name ) ||
			!is_array( $file_list = scandir( $folder_name ) )
		) {
			return false;
		}

		$i18n_array = array();
		foreach ( $file_list as $filename ) {
			if(
				// Ensure the file in a translation file and the language is enabled  
				false === ( $pos = strpos( $filename, '.' . self::I18N_FILE_EXTENSION ) ) ||
				!is_string( $lang = substr( $filename, 0, $pos) ) ||
				!in_array( $lang, array_keys( self::$enabled_language ) ) ||
				
				!is_string( $file_content = file_get_contents( get_kigo_plugin_path( self::I18N_FOLDER_NAME . DIRECTORY_SEPARATOR . $filename ) ) ) ||
				!is_array( $i18n_array[ $lang ] = self::parse_i18n_file( $file_content ) )
			) {
				continue;
			}
		}
		
		if( !is_string( $i18n_json = json_encode( $i18n_array ) ) ) {
			return false;
		}
		
		if(
			!is_string( $old_value = get_site_option( self::NETWORK_I18N_OPTION, null ) ) ||
			$old_value !== $i18n_json
		) {
			return update_site_option( self::NETWORK_I18N_OPTION, $i18n_json );
		}
		
		return true;
	}
	
	public static function save_custom_translations() {
		if(
			!isset( $_POST['data'] ) ||
			!is_string( $_POST['data'] ) ||
			!strlen( $_POST['data'] ) ||
			
			!isset( $_POST['lang_code'] ) ||
			!is_string( $_POST['lang_code'] ) ||
			!strlen( $_POST['lang_code'] )
		) {
			self::echo_json_response( false, 'Missing POST parameters (data or lang_code)', $_POST );
		}

		if( !is_array( $data = json_decode( stripslashes( $_POST['data'] ), true ) ) ) {
			self::echo_json_response( false, 'Unable to json_decode the post data', array( 'data' => $_POST['data'] ) );
		}

		if(
			!is_array( $array_i18n_default = self::get_default_translations( $_POST['lang_code'] ) ) ||
			!is_array( $array_i18n_overwritten = self::get_i18n_site_array( $_POST['lang_code'] ) )
		) {
			self::echo_json_response( false, 'Unable to retrieve the default or overwritten values', array( 'lang_code' => $_POST['lang_code'] ) );
		}

		foreach ( $data as $en_key => $overwritten_value ) { 
			// If the key is not a key of the default translation, skip it
			if( !in_array( $en_key, array_keys( $array_i18n_default ) ) ) { 
				continue;
			}
			
			// If an empty array is passed or the overwritten value is exactly the same that the default one, this means that we should stop overwriting the value
			if(
				strlen( trim( $overwritten_value ) ) === 0 ||
				strcmp( trim( $overwritten_value ), $array_i18n_default[ $en_key ] ) === 0
			) { 
				unset( $array_i18n_overwritten[ $en_key ] );
			}
			else { 
				$array_i18n_overwritten[ $en_key ] = trim( $overwritten_value );
			}
		}

		// Loggly_logs::log( array('$array_i18n_default' => $array_i18n_default) );
		// Loggly_logs::log( array('$array_i18n_overwritten' => $array_i18n_overwritten) );

		if( !self::update_i18n_customized_translation( $array_i18n_overwritten ) ) {  
			self::echo_json_response( false, 'Unable to save the custom translations', array( $_POST['lang_code'] => $array_i18n_overwritten ) );
		}

		delete_transient('kigo_textdata');
		self::echo_json_response( true, '', $array_i18n_overwritten );
	}



	// Private Helpers

	/**
	 * Return the default translations for a language.
	 * 
	 * LOGIC: The function first retrieve the default translations for the "parent" language (ie. fr).
	 * Then if a culture code (ie. fr-BE) is given it search for any default translation for the specific culture (ie. fr-BE) and merge them with the parent once.
	 * (A specific culture may have only a subset of keys, which differ from the parent language)
	 * 
	 * If the culture is not found it default to english, and logs an error in Loggly.
	 * 
	 * @param $lang_code
	 *
	 * @return array|null
	 */
	private static function get_default_translations( $lang_code ) { 
		if(
			!is_string( $lang_code ) ||
			!strlen( $lang_code ) ||
			
			!is_string( $parent_lang_code = self::parent_lang_code( $lang_code ) ) ||
			!strlen( $parent_lang_code ) ||
			
			!in_array( $parent_lang_code, self::get_avalaible_language_codes() )
		) {
			Loggly_logs::log( array( 'msg' => 'Invalide language code default fall back to ' . self::DEFAULT_LANGUAGE_CODE, 'lang_code' => $lang_code, 'parent_lang_code' => $parent_lang_code ) , array( self::LOGGLY_LOG_TAG_WARN ) );
			$parent_lang_code = self::DEFAULT_LANGUAGE_CODE;
		}
		
		// Check if the "parent" language (ie.fr for fr-FR and fr-BE etc..) exists
		if(
			!is_array( $array_i18n_default = self::get_i18n_network_array() ) ||
			
			!isset( $array_i18n_default[ $parent_lang_code ] ) ||
			!is_array( $array_i18n_default[ $parent_lang_code ] )
		) {
			return null;
		}

		// Check if some translations exists for the specific "culture" (ie. fr-BE)
		if(
			$lang_code === $parent_lang_code ||
			!isset( $array_i18n_default[ $lang_code ] ) ||
			!is_array( $array_i18n_default[ $lang_code ] )
		) {
			return $array_i18n_default[ $parent_lang_code ];
		} 

		return array_merge( $array_i18n_default[ $parent_lang_code ], $array_i18n_default[ $lang_code ] );
	}

	/**
	 * This function returns the parent language code.
	 * If the function can't retrieve the parent language code it returns the default language.
	 * 
	 * @param $lang_code
	 *
	 * @return string
	 */
	private static function parent_lang_code( $lang_code ) {
		if(
			!preg_match( '/^([a-z]{2,3})-?.*/', $lang_code, $matches ) ||
			count( $matches ) !== 2 ||
			!is_string( $matches[1] ) ||
			!strlen( $matches[1] ) ||
			!in_array( $matches[1], array_keys( self::$enabled_language ) )
		) {
			return null;
		}
		
		return $matches[1];
	}

	/**
	 * Returns the list of language codes that are available at the network level.
	 * 
	 * @return array
	 */
	private static function get_avalaible_language_codes() {
		if( !is_array( $array_i18n_default = self::get_i18n_network_array() ) ) {
			return array();
		}
		
		return array_keys( $array_i18n_default );
	}

	/**
	 * Return the network saved array of languages and translations.
	 * Use a "cache" ($array_i18n_default) not to call get_site_option every time.
	 * 
	 * @return array|null
	 */
	private static function get_i18n_network_array() {
		if( is_array( self::$array_i18n_default ) ) {
			return self::$array_i18n_default;
		}
		
		if(
			!is_string( $json_i18n = get_site_option( self::NETWORK_I18N_OPTION, null ) ) ||
			!is_array( self::$array_i18n_default = json_decode( $json_i18n, true ) )
		) {
			return null;
		}
		
		return self::$array_i18n_default;
	}

	/**
	 * Return the site overwritten array of translations.
	 * Use a "cache" ($array_i18n_overwritten) not to call get_option every time.
	 *
	 * @param $lang_code
	 * 
	 * @return array|null
	 */
	private static function get_i18n_site_array( $lang_code ) { 
		if( is_array( self::$array_i18n_overwritten ) ) { 
			return self::$array_i18n_overwritten;
		}

		if(
			!is_string( $json_i18n = get_option( self::SITE_I18N_OPTION, null ) ) ||
			!is_array( self::$array_i18n_overwritten = json_decode( $json_i18n, true ) ) ||
			!is_array( self::$array_i18n_overwritten )  //Use lang code here for multiple.  Right now we are using a flat array
		) { 

			if(
				!is_array( $old_customized_translations = kigo_I18n::get_old_customized_translations( $lang_code ) ) ||
				!is_array( $default_translations = self::get_default_translations( $lang_code ) ) ||
				!is_array( self::$array_i18n_overwritten = array_diff_assoc( $old_customized_translations, $default_translations ) ) ||
				!self::update_i18n_customized_translation( self::$array_i18n_overwritten )
			) { 

				// If something goes wrong in retrieving the old custom translations, let's consider there are none.
				// This is done not to trigger error when get entity textdata will be deprecated or on newly created sites.
				self::update_i18n_customized_translation( array( /* $lang_code => array() */ ) );
				return array();
			}

			return self::$array_i18n_overwritten;
		}

		return self::$array_i18n_overwritten;
	}

	/**
	 * Retrieves the translations (ex textdata) saved in bapi for a specific bapi key and compare them to the default ones arriving from the plugin.
	 * This will only allows customers not to lose their specific translations while moving to this new system.
	 * 
	 * @return array|null
	 */
	private static function get_old_customized_translations( $lang_code ) {
		bapi_wp_site_options();
		$bapi = getBAPIObj();
		if (
			!$bapi->isvalid() ||
			!is_array( $ret = $bapi->gettextdata( true ) ) ||
			
			!isset( $ret['result'] ) ||
			!is_array( $old_textdata = $ret['result'] ) ||
			
			!isset( $ret['retparams'] ) ||
			!is_array( $ret['retparams'] ) ||
			
			!isset( $ret['retparams']['language'] ) ||
			!is_string( $old_lang_code = $ret['retparams']['language'] )
		) {
			Loggly_logs::log( array( 'msg' => 'Unable to get BAPI textdata', 'blog_id' => get_current_blog_id() ) , array( self::LOGGLY_LOG_TAG_FATAL ) );
			return null;
		}
		
		if( $lang_code !== $old_lang_code ) {
			return array();
		}
		
		return $old_textdata;
	}

	/**
	 * Update the site option containing the overwritten specific translations from this website.
	 * 
	 * @param $custom_translations
	 *
	 * @return bool
	 */
	private static function update_i18n_customized_translation( $custom_translations ) { 
		if(
			!is_array( $custom_translations ) ||
			!is_string( $json_custom_translation = json_encode( $custom_translations ) )
		) {
			return false;
		}
		
		if(
			!is_string( $old_value = get_option( self::SITE_I18N_OPTION, null ) ) ||
			$old_value !== $json_custom_translation
		) { 
			return update_option( self::SITE_I18N_OPTION, $json_custom_translation );
		}
		
		return true;
	}

	/**
	 * Echo the json response and exist
	 * 
	 * @param boolean $success
	 * @param string $msg
	 * @param array $data
	 */
	private static function echo_json_response( $success, $msg = '', $data = array() ) {
		header('Content-Type: application/json');
		if(
			!is_bool( $success ) ||
			!is_string( $msg ) ||
			!is_array( $data )
		) {
			$data = array(
				'success'	=> $success,
				'msg'		=> $msg,
				'data'		=> $data
			);
			Loggly_logs::log( $data, array( self::LOGGLY_LOG_TAG_FATAL ) );
			echo json_encode(
				array(
					'success'	=> false,
					'msg'		=> 'Incorect parameters passed to method: ' . __METHOD__,
					'data'		=> $data
				)
			);
			exit();
		}
		
		echo json_encode(
			array(
				'success'	=> $success,
				'msg'		=> $msg,
				'data'		=> $data
			)
		);
		exit();
	}




	/**
	 * The following set of function is reserved to generate the translation files.
	 * You shouldn't use this set of functions
	 */

	/**
	 * Function to be called to update the static translations files
	 * http://imbookingsecure.com/wp-admin/admin-ajax.php?action=kigo_update_translation_files
	 * 
	 * Do be able to use this function you need to define KIGO_PRIVATE_GOOGLE_TRANSLATE_KEY with your google translate API key
	 * 
	 * IMPORTANT: Google translate API doesn't support culture codes (ie. be-BE)
	 * ( Translation files for specific cultures can be added manual )
	 * 
	 */
	public static function update_default_translations() {
		if(
			!defined( 'KIGO_PRIVATE_GOOGLE_TRANSLATE_KEY' ) ||
			!strlen( KIGO_PRIVATE_GOOGLE_TRANSLATE_KEY ) ||
			
			!is_array( $supported_lang_codes = self::get_google_supported_languages_codes() ) 
/*			||
			
			!is_string( $translations_string = file_get_contents( get_kigo_plugin_path( self::I18N_FOLDER_NAME . DIRECTORY_SEPARATOR . self::DEFAULT_LANGUAGE_CODE . '.' . self::I18N_FILE_EXTENSION ) ) ) ||
			
			!is_array( $default_lang_translations = self::parse_i18n_file( $translations_string ) ) ||
			!count( $default_lang_translations ) 
*/
		) {
			
			self::update_i18n_network_option();
			echo "Translations loaded from local files.";
			exit();
		}
		
		foreach( array_keys( self::$enabled_language ) as $lang_code ) {
			
			if( !in_array( $lang_code, $supported_lang_codes ) ) {
				echo 'Unsuported language code: $lang_code<br>';
				continue;
			}
			
			if( self::DEFAULT_LANGUAGE_CODE !=  $lang_code ) {
				$filename = $lang_code . '.' . self::I18N_FILE_EXTENSION;
				echo ( self::update_lang_file( $lang_code, $default_lang_translations ) ? 'Fille ' . $filename . ' has been correctly created/updated' : 'Error creating/updating the file ' . $filename ) . '<br>' ;
			}
			
		}
		
		exit();
	}

	/**
	 * Parse the file content and return an array, where keys are the english sentences, and value the translated onces
	 * 
	 * @param $file_content
	 *
	 * @return array|null
	 */
	private static function parse_i18n_file( $file_content ) {
		if(
			!is_string( $file_content ) ||
			!strlen( $file_content = str_replace( "\r", '', $file_content ) ) ||
			
			!is_array( $lines = explode( self::I18N_FILE_LINE_SEPARATOR, $file_content ) )
		) {
			return null;
		}
		
		$trad = array();
		foreach ( $lines as $line ) {
			if(
				!is_array( $key_val = explode( self::I18N_FILE_SEPARATOR, $line ) ) ||
				count( $key_val ) !== 2 ||
				
				!is_string( $key_val[0] ) ||
				!strlen( $key_val[0] ) ||
				
				!is_string( $key_val[1] ) ||
				!strlen( $key_val[1] )
			) {
				continue;
			}
			$trad[ $key_val[0] ] = $key_val[1];

		}
		
		return $trad;
	}

	/**
	 * Update (generate if not exist) the language file by google translating the missing keys in comparison to the english version
	 *
	 * @param string $lang_code
	 * @param array $translations_keys
	 *
	 * @return bool
	 */
	private static function update_lang_file( $lang_code, $default_lang_translations ) {
		$file_path = get_kigo_plugin_path( self::I18N_FOLDER_NAME . DIRECTORY_SEPARATOR . $lang_code . '.' . self::I18N_FILE_EXTENSION );
		if(
			!is_array( $default_lang_translations ) ||
			!is_string( $lang_code )
		) {
			return false;
		}

		if(
			!file_exists( $file_path )  ||
			//!is_string( $file_content = fread( $resource, $file_size ) ) ||
			!is_string( $file_content = file_get_contents( $file_path ) ) ||
			!strlen( $file_content ) ||
			!is_array( $existing_trad = self::parse_i18n_file( $file_content ) )
		) {
			$existing_trad = array();
		}
		
		// Retrieve the list of needed translations, we use the english values (not key) to translate
		$needed_sentences = array_diff_key( $default_lang_translations, $existing_trad );

		// Check if there are no translations in the file that has been deleted
		$content_to_writte = array_intersect_key( $existing_trad, $default_lang_translations );

		// Check if there is any reason to cleanup and rewrite the file
		if(
			!count( $needed_sentences ) &&
			count( $content_to_writte ) === count( $existing_trad )
		) {
			return true;
		}

		// Do the call to google translate by chunks of 50 sentences, because google has an undocumented limit.
		$needed_sentences_chunks = array_chunk( $needed_sentences, 50, true );
		foreach ( $needed_sentences_chunks as $needed_sentences_chunk ) {
			// Retrieve translations needed from google translate API
			if(
				!is_string( $translations_json = file_get_contents( self::build_google_translate_url( $lang_code, array_values( $needed_sentences_chunk ) ) ) ) ||
				!is_array( $translations = json_decode( $translations_json, true ) ) ||

				!isset( $translations[ 'data' ] ) ||
				!isset( $translations[ 'data' ][ 'translations' ] ) ||
				!is_array( $translations[ 'data' ][ 'translations' ] ) ||

				count( $translations[ 'data' ][ 'translations' ] ) !== count( $needed_sentences_chunk )
			) {
				return false;
			}

			// Build the translation array
			$needed_sentences_chunk_keys = array_keys( $needed_sentences_chunk );
			foreach ( $translations[ 'data' ][ 'translations' ] as $idx => $translated_sentence ) {
				if(
					!isset( $needed_sentences_chunk_keys[ $idx ] ) ||
					!strlen( $needed_sentences_chunk_keys[ $idx ] ) ||

					!isset( $translated_sentence[ 'translatedText' ] ) ||
					!strlen( $translated_sentence[ 'translatedText' ] )
				) {
					continue;
				}

				$content_to_writte[ $needed_sentences_chunk_keys[ $idx ] ] = $translated_sentence[ 'translatedText' ];
			}
		}

		if(
			!count( $content_to_writte ) ||
			!array_walk( $content_to_writte, function( &$value, $key ){ $value = $key . kigo_I18n::I18N_FILE_SEPARATOR . $value; } ) ||
			!is_resource ( $resource = fopen( $file_path, 'w' ) ) ||
			!is_int( fwrite( $resource, implode( self::I18N_FILE_LINE_SEPARATOR, $content_to_writte ) ) )
		) {
			fclose( $resource );
			return false;
		}

		return fclose( $resource );
	}

	/**
	 * Build the http url to be called to translate the sentenced given in $needed_translations to $lang_code
	 * 
	 * @param string $lang_code
	 * @param array $needed_translations
	 *
	 * @return string|null
	 */
	private static function build_google_translate_url( $lang_code, $needed_translations ) {
		if(
			!is_string( $lang_code ) ||
			!is_array( $needed_translations ) ||
			
			!defined( 'KIGO_PRIVATE_GOOGLE_TRANSLATE_KEY' ) ||
			!strlen( KIGO_PRIVATE_GOOGLE_TRANSLATE_KEY )
		) {
			return null;
		}
		
		$url = 'https://www.googleapis.com/language/translate/v2?key=' . KIGO_PRIVATE_GOOGLE_TRANSLATE_KEY;
		$url .= '&format=text'; // By default google return html special chars
		$url .= '&source=' . self::DEFAULT_LANGUAGE_CODE;
		$url .= '&target=' . $lang_code;
		
		foreach( $needed_translations as $sentence ) {
			$url .= '&q=' . urlencode( $sentence );
		}
		
		return $url;
	}

	/**
	 * Call google translate API to retrieve the list of available languages
	 * 
	 * @return array|null
	 */
	private static function get_google_supported_languages_codes() {
		if(
			!defined( 'KIGO_PRIVATE_GOOGLE_TRANSLATE_KEY' ) ||
			!strlen( KIGO_PRIVATE_GOOGLE_TRANSLATE_KEY ) ||
			
			!is_string( $supported_lang_json = file_get_contents( 'https://www.googleapis.com/language/translate/v2/languages?target=en&key=' . KIGO_PRIVATE_GOOGLE_TRANSLATE_KEY ) ) ||
			!strlen( $supported_lang_json ) ||
			
			!is_array( $supported_lang = json_decode( $supported_lang_json, true ) ) ||
			
			!isset( $supported_lang[ 'data' ] ) ||
			!isset( $supported_lang[ 'data' ][ 'languages' ] ) ||
			!is_array( $supported_lang[ 'data' ][ 'languages' ] )
		) {
			return null;
		}
		
		$codes = array();
		foreach ( $supported_lang[ 'data' ][ 'languages' ] as $language ) {
			$codes[] = $language[ 'language' ];
		}

		return $codes;
	}
}
