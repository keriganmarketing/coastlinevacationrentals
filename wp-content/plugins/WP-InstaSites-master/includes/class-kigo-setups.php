<?php

class Kigo_Setups {

	const ADMIN_VIEW_PATH	= 'admin-view';
	const INCLUDE_PATH		= 'includes';


	// Translations managment Section
	public static function translation_gui() {
		require_once( get_kigo_plugin_path( self::INCLUDE_PATH . DIRECTORY_SEPARATOR . 'class-kigo-translations-list-table.php' ) );
		
		$my_table = new Kigo_Translations_List_Table();
		if( !$my_table->prepare_items() ) {
			wp_die( 'Sorry, we were unable to retrieve the translations' );
		}
		
		include( get_kigo_plugin_path( self::ADMIN_VIEW_PATH . DIRECTORY_SEPARATOR . 'setup-translations.php' ) );
	}

	// SSL Section
	public static function ssl_config() {
		$ssl_config = new Kigo_Ssl_Config();
		if(
			isset( $_POST[ 'submit' ] ) &&
			isset( $_POST[ 'ssl_select' ] )
		) {
			if( $ssl_config->save_custom( $_POST[ 'ssl_select' ], $_POST[ 'ssl_custom_script' ] ) ) {
				echo '<div id="message" class="updated"><p><strong>Settings saved.</strong></p></div>';
			}
			else {
				echo '<div id="message" class="error"><p><strong>Settings not saved.</strong></p></div>';
			}
		}
		
		self::ssl_config_html( $ssl_config->get_ssl_options_to_json() );
	}
	
	private static function ssl_config_html( $options ) {
		?>
		<script type="text/javascript">
			var sslProviders = <?php echo $options; ?>;
		</script>
		
		<div class="wrap" id="ssl_config">
			<?php echo self::get_setup_header(); ?>
			<h2>SSL Configuration</h2>
			
			<form method="post">
				<table class="widefat" style="width:auto">
				<tr>
					<td><label for="ssl_select">SSL Support: </label></td>
					<td><select name="ssl_select" id="ssl-select" style="width:30em"></select></td>
				</tr>
				<tr>
					<td colspan="2"><textarea name="ssl_custom_script" id="ssl-input" rows="7" cols="70"></textarea></td>
				</tr>
				<tr>
					<td>Preview: </td>
					<td><iframe id="ssl-preview"></iframe></td>
				</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	// General setup page helpers
	private function get_setup_header() {
            return '<h1><img src="' . get_kigo_plugin_url( '/img/logo_kigo.png' ) . '"/></h1>';
	}

}
