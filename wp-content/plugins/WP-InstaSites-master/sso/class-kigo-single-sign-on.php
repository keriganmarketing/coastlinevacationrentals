<?php

/**
 * Class Kigo_Single_Sign_On
 *
 * Provides a "SSO"-like feature. Kigo users are automatically signed-on to their Kigo Sites' admin panel.
 * (The "first" administrator of the blog (the one with the lowest user_id) is automatically picked)
 *
 * For this feature to work, you must set const KIGO_SSO_SHARED_KEY @ wp-config.php (Please use the key provided to you by Kigo)
 */
class Kigo_Single_Sign_On {

	// AJAX actions
	const ACTION_CREATE_TOKEN	= 'kigo_sso_create_token';
	const ACTION_LOGIN			= 'kigo_sso_login';

	// TOKEN generator settings
	const TOKEN_POOL			= '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const TOKEN_LENGTH			= 30;
	const TOKEN_TIMEOUT			= 300; // in seconds (5 min)

	// TOKEN table (shared by all blogs of the network)
	const TABLE_NAME			= 'kigo_sso_tokens'; // table that stores the blog's sign on token

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// ACTION HANDLERS

	/**
	 * If the POSTed shared key in $_POST[ 'key' ] matches the const KIGO_SSO_SHARED_KEY,
	 * then this method will respond with a sign-on token URL for the given blog (not for network's default blog).
	 *
	 * The token is stored in a dedicated table, created at plugin activation (or update),
	 * and it gets deleted after one usage or if older than TOKEN_TIMEOUT.
	 *
	 * https://<blog url>/wp-admin/admin-ajax.php?action=kigo_sso_create_token
	 *
	 * @return void
	 *
	 * @header 200 OK
	 * @header 403 Forbidden
	 * @header 500 Application Error
	 *
	 */
	public static function create_token() {

		if( !is_ssl() ) {
			self::http_response( 403, 'Forbidden (SSL REQUIRED)' );
		}

		if( !self::purge_expired_tokens() ) {
			self::http_response( 500, 'Application Error' );
		}

		if(
			!defined( 'KIGO_SSO_SHARED_KEY' ) || // feature not configured?

			// In MultiSite, "blog" is a reference to an individual website. "Site" is a reference to a network of "blogs."
			// In the future, there's the likelihood that WordPress will be able to power multiple networks ("site"s) in addition to its current state of multiple "blog"s.
			// http://wordpress.stackexchange.com/questions/5911/blog-id-current-site-vs-site-id-current-site-in-wordpress-multisite
			(
				is_multisite() &&
				( !defined( 'BLOG_ID_CURRENT_SITE' ) || get_current_blog_id() === BLOG_ID_CURRENT_SITE ) // NO SSO for the "super" network admin!
			) ||

			// key must be given and match the configured one for this installation
			!isset( $_POST[ 'key' ] ) ||
			!is_string( $_POST[ 'key' ] ) ||
			constant( 'KIGO_SSO_SHARED_KEY' ) !== $_POST[ 'key' ]
		) {
			self::http_response( 403, 'Forbidden' );
		}

		if( !is_string( $token = self::generate_and_save_token( self::TOKEN_POOL, self::TOKEN_LENGTH ) ) ) {
			self::http_response( 500, 'Application Error' );
		}

		// generates the sign on token URL used to login to the admin panel of the blog
		echo admin_url( 'admin-ajax.php' ) . '?action=' . urlencode(self::ACTION_LOGIN) . '&' . 'token=' . urlencode($token);

		self::http_response( 200, 'OK' );

	}

	/**
	 * Log in the oldest administrator of the current blog, after verifying the token (and deleting it).
	 *
	 * https://<blog url>/wp-admin/admin-ajax.php?action=kigo_sso_login&token=qIpzsBoHmLlnyKrDSpZxwYrFy124u7
	 *
	 * @return void
	 *
	 * @header 307 Temporary Redirect (+ Location header with url to the admin panel)
	 * @header 403 Invalid request
	 * @header 500 Application Error
	 *
	 */
	public static function login() {
		global $wpdb;

		if( !is_ssl() ) {
			self::http_response( 403, 'Forbidden (SSL REQUIRED)', 'Forbidden (SSL REQUIRED)' );
		}

		if( !self::purge_expired_tokens() ) {
			self::http_response( 500, 'Application Error', 'Application Error' );
		}

		if(
			!isset($_GET[ 'token' ]) ||
			self::TOKEN_LENGTH !== strlen( $_GET[ 'token' ] )
		) {
			self::http_response( 403, 'Forbidden', 'Forbidden' );
		}

		$token = $_GET[ 'token' ];
		$blog_id = get_current_blog_id();
		$table = $wpdb->base_prefix . self::TABLE_NAME;

		$ret = $wpdb->get_var( $wpdb->prepare(
			'SELECT COUNT(*) FROM `' . $table . '` WHERE `token` = %s AND `blog_id` = %d',
			array( $token, $blog_id )
		) );

		if( '1' === $ret ) {

			// remove the used token
			$retDel = $wpdb->delete( $table, array( 'token' => $token, 'blog_id' => $blog_id ) );
			if( 1 !== $retDel )
				self::http_response( 500, 'Application Error', 'Application Error' );

			$query = "
				SELECT
					`meta_value`,
					`user_id`
				FROM
					`" . $wpdb->base_prefix . "usermeta`
				WHERE
					`meta_key`='" . $wpdb->base_prefix . ( is_multisite() ? ( $blog_id . '_' ) : '' ) . "capabilities' AND
					(
						`meta_value` LIKE '%s:13:\"administrator\";s:1:\"1\"%' OR /* serialization hell.. */
						`meta_value` LIKE '%s:13:\"administrator\";b:1;%' OR
						`meta_value` LIKE '%s:13:\"administrator\";i:1;%'
					)
				ORDER BY
					`user_id` ASC
				LIMIT 1
			";

			// verify fetched row
			if(
				!is_array( $row = $wpdb->get_row( $query, ARRAY_A ) ) ||
				!is_array($meta_value = unserialize($row[ 'meta_value' ])) ||
				!isset($meta_value['administrator']) ||
				($meta_value['administrator'] !== '1' && $meta_value['administrator'] !== 1 && $meta_value['administrator'] !== true)
			) {
				self::http_response( 500, 'Application Error', 'Application Error' );
			}

			// sign-out (if already using an account)
			wp_clear_auth_cookie();

			// sign-in
			wp_set_auth_cookie( $row[ 'user_id' ] );

			// redirect the user to the admin panel url
			header( 'Location: ' . get_admin_url() );
			self::http_response( 307, 'Temporary Redirect' );
		}
		elseif( '0' === $ret ) { // This case means the token has already been deleted or it never existed.
			self::http_response( 403, 'Forbidden', 'Forbidden' );
		}
		else {
			self::http_response( 500, 'Application Error', 'Application Error' );
		}
	}


	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// HELPERS

	/**
	 * Plugin activation hook
	 * (re-)create the tokens table
	 * @return bool
	 */
	public static function create_table() {
		global $wpdb;

		$table_name = $wpdb->base_prefix . self::TABLE_NAME;

		if(
			!$wpdb->query( "DROP TABLE IF EXISTS `$table_name`" ) ||

			!$wpdb->query(
				"CREATE TABLE `$table_name` (
					`token` CHAR(30) charset ascii collate ascii_bin NOT NULL, /* ascii_bin for case-sensitive comparison on the token */
					`blog_id` BIGINT NOT NULL,
					`created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
					PRIMARY KEY(`token`)
				)"
			)
		) {
			return false;
		}

		return true;
	}

	/**
	 * Plugin deactivation hook
	 * (re-)create the tokens table
	 * @return bool
	 */
	public static function drop_table() {
		global $wpdb;

		return ( true === $wpdb->query( 'DROP TABLE IF EXISTS `' . $wpdb->base_prefix . self::TABLE_NAME . '`' ) );
	}

	/**
	 * Clean token that have expired.
	 * Return false in case of query failure, true in any other case.
	 *
	 * @return bool
	 */
	private static function purge_expired_tokens() {
		global $wpdb;

		return is_int( $wpdb->query( "DELETE FROM `" . $wpdb->base_prefix . self::TABLE_NAME . "` WHERE `created` < (NOW() - INTERVAL " . self::TOKEN_TIMEOUT . " SECOND)" ) );
	}

	/**
	 * Output the http header for the given code, and stop execution.
	 *
	 * @param int $code The http response code to reply.
	 * @param string $message The http response description to reply.
	 * @param string $body The optional response body to reply.
	 */
	private static function http_response( $code, $description = '', $body = '' ) {

		// not using http_response_code because it requires PHP >= 5.4.0
		header( $_SERVER[ 'SERVER_PROTOCOL' ] . ' ' . $code . ' ' . $description );

		if(
			is_string( $body ) &&
			strlen( $body )
		)
		{
			echo $body;
		}

		exit();
	}

	/**
	 * Generate, save and return a random token (string) with a given length by using characters from a given pool.
	 * Even if the risk of collision is (really) small, it retries to generate and save the token 3 times.
	 * This ensure that the query failure is not resulting from a token collision.
	 *
	 * @param int $length The length of the needed token default to TOKEN_LENGTH
	 * @param int $nbTries The number of reties to save the token before failing
	 *
	 * @return string | null
	 */
	private static function generate_and_save_token( $pool, $length, $nbTries = 3 ) {
		global $wpdb;

		do {
			$token = '';

			for ( $i = 0; $i < $length; $i++ ) {
				$token .= $pool[ mt_rand( 0, strlen( $pool ) - 1 ) ];
			}

			if(
				1 === $wpdb->insert(
					$wpdb->base_prefix . self::TABLE_NAME,
					array( 'blog_id' => get_current_blog_id(), 'token' => $token ),
					array( '%d', '%s' )
				)
			) {
				return $token;
			}

		} while ( --$nbTries );

		return null;
	}

}
