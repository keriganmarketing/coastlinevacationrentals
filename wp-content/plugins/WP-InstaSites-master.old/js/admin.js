/* JavaScript file loaded only on admin pages */

$(function() {

	/* setup-sync page */
	if( $( '#force_full_sync' ).length ) {
		var timeout = null;
		function get_last_cron_execution() {
			$.ajax({
					dataType: "json",
					url: '/wp-admin/admin-ajax.php?action=kigo_get_last_cron_execution'
				})
				.always(function( data ) {
					if(
						!$.isPlainObject( data ) ||
						!data.success ||
						'boolean' !== $.type( data.too_much )
					) {
						$( '#last-exec' ).html( 'n/a' );
					}
					else if( data.too_much ) {
						$( '#last-exec' ).html( '<span class="red">> 1h</span>' );
					}
					else {
						$( '#last-exec' ).html( data.formated );
					}
	
					if(data.success) {
						timeout = setTimeout(function(){ get_last_cron_execution(); }, 60000);
					}
				});
		}
	
		get_last_cron_execution();
	
		$( '#force_full_sync' ).click(
			function()
			{
				$(this).button().button("option", "disabled", true) ;
				var prev_text = $(this).text();
				$(this).text( 'Loading...' );
				$('#force_full_sync_spinner').show();
	
				$.ajax({
						url:  '/wp-admin/admin-ajax.php?action=kigo_site_cron&force_full_sync=1', // the cron runs synchronously
						timeout: 10 * 60 * 1000 // 10 minutes expressed in milliseconds
					})
					.always(function() {
						$( '#force_full_sync' ).text( prev_text );
						$('#force_full_sync_spinner').hide();
						$( '#force_full_sync' ).button("option", "disabled", false);
						if( timeout !== null )
							clearTimeout(timeout);
						get_last_cron_execution();
					});
			}
		);
	}


	/* Translation page */
	if( $( '#translations' ).length ) {

		/* Disable every button until a change is triggered */
		$( 'button.i18n-cancel, a.i18n-save' ).button().button( 'option', 'disabled', true );
		
		/* Cancel buttons listener */
		var cancel_button_enbaled = [];
		$( 'button.i18n-cancel' ).click(
			function() {
				var corresponding_input = null;
				if(
					'string' !== $.type( $(this).attr( 'data-key') ) ||
					'object' !== $.type( corresponding_input = $( "input.overwritten-translation[data-key='" + $(this).attr( 'data-key' ) + "']" ) ) ||
					'string' !== $.type( corresponding_input.attr( 'data-value' ) )
				) {
					// FIXME: Once we use loggly in JS please log this! (https://www.loggly.com/docs/javascript/)
					alert( "Unexpected error occured while retrieving cancel attributes.\nPlease contact support." );
					return false;
				}
				corresponding_input.val( corresponding_input.attr( 'data-value' ) );
				disable_cancel_button( $(this) )
			}
		);
		
		/* Input change listener */
		$( 'input.overwritten-translation' ).bind(
			'input change',
			function() {
				var corresponding_cancel = null;
				if(
					'string' !== $.type( $(this).attr( 'data-key' ) ) ||
					'string' !== $.type( $(this).attr( 'data-value' ) ) ||
					'string' !== $.type( $(this).val() ) ||
					'object' !== $.type( corresponding_cancel = $( "button.i18n-cancel[data-key='" + $(this).attr( 'data-key' ) + "']" ) )
				) {
					// FIXME: Once we use loggly in JS please log this! (https://www.loggly.com/docs/javascript/)
					alert( "Unexpected error occured while retrieving input attributes.\nPlease contact support." );
					return false;
				}
				
				if( $(this).attr( 'data-value' ) !== $(this).val() ) {
					
					// Enable save and the corresponding cancel button
					$( 'a.i18n-save' ).button().button( 'option', 'disabled', false );
					corresponding_cancel.button().button( 'option', 'disabled', false );
					
					// Keep track of the cancel button that are enabled 
					var pos = null;
					if( -1 === ( pos = cancel_button_enbaled.indexOf( $(this).attr( 'data-key' ) ) ) ) {
						cancel_button_enbaled.push( $(this).attr( 'data-key' ) );
					}
					
					// Message preventing to quite without saving
					$( window ).bind( 'beforeunload', function() {
						return 'You have unsaved changes, are you sure you want to leave?';
					});
				}
				else {
					disable_cancel_button( corresponding_cancel );
				}
			}
		);
		
		/* Save listener */
		$( 'a.i18n-save' ).on('click',
			function()
			{ 
				var lang_code = null;
				var translations = null;
				if(
					'string' !== $.type( ( lang_code = $(this).attr( 'data-lang' ) ) ) ||
					'object' !== $.type( translations = get_modified_translations() )
				) {
					// FIXME: Once we use loggly in JS please log this! (https://www.loggly.com/docs/javascript/)
					alert( "Unexpected error occured, unable to retrieve language code.\nPlease contact support." );
					return false;
				}
			
				// Disable all editing/saving
				$( 'button.i18n-cancel, a.i18n-save' ).button().button( 'option', 'disabled', true );
				$( 'input.overwritten-translation' ).attr( 'readonly', true );
				$( '#save-translations-spinner' ).show();
				
				$.ajax({
						method:	'POST',
						url:	'/wp-admin/admin-ajax.php?action=kigo_save_translations',
						data:	{
							'lang_code'	: lang_code,
							'data'		: JSON.stringify( translations )
						}
					})
					.always(function( data ) { 
						if(
							'object' !== $.type( data ) ||
							'boolean' !== $.type( data.success ) ||
							'string' !== $.type( data.msg ) ||
							'object' !== $.type( data.data )
						) {
							// FIXME: Once we use loggly in JS please log this! (https://www.loggly.com/docs/javascript/)
							alert( "Unexpected error occured while saving the translations.\nPlease contact support." );
						}
						else if( !data.success ) {
							// If success false is returned, PHP has already logged into Loggly the error
							alert( "We couldn't save your translation.\nPlease contact support with the following message:\n" + data.msg );
						}
						else {
							// Let's update the data-value attribute with the value saved (returned by the server)
							$( 'input.overwritten-translation').each(
								function() {
									if( 'string' === $.type( data.data[ $(this).attr( 'data-key') ] ) ) { 
										$(this).attr( 'data-value', data.data[ $(this).attr( 'data-key') ] );
									}
									else { 
										$(this).attr( 'data-value', '' );
										$(this).val( '' );
									}
								}
							);
							alert( "Your translations have been correctly saved!" );
						}
						
						$( window ).unbind( 'beforeunload' );
						$( 'input.overwritten-translation' ).attr( 'readonly', false );
						$( '#save-translations-spinner' ).hide();
					});
			}
		);
		
		/* Load Default Translations */
		$( 'a.kigo_update_translation_files' ).on('click',
			function(e)
			{ e.preventDefault();
				
				$( '#save-translations-spinner' ).show();
				
				$.ajax({
						method:	'POST',
						url:	'/wp-admin/admin-ajax.php?action=kigo_update_translation_files',
					})
					.always(function( data ) { 
						$( '#save-translations-spinner' ).hide();
						alert(data);
					});
			}
		);		
		
		// HELPERS
		function disable_cancel_button( cancel_button ) {
			cancel_button.button().button( 'option', 'disabled', true );
			if( -1 !== ( pos = cancel_button_enbaled.indexOf( $(cancel_button).attr( 'data-key' ) ) ) ) {
				cancel_button_enbaled.splice( pos, 1 );
			}
			if( !cancel_button_enbaled.length ) {
				$( 'a.i18n-save' ).button().button( 'option', 'disabled', true );
				$( window ).unbind( 'beforeunload' );
			}
		}
		
		function get_modified_translations() {
			var translations = {};
			$( 'input.overwritten-translation' ).each(
				function() {
					if(
						'string' !== $.type( $(this).attr( 'data-value' ) ) ||
						'string' !== $.type( $(this).attr( 'data-key' ) ) ||
						'string' !== $.type( $(this).val() )
					) {
						translations = null;
						return false;
					}
					
					if( $(this).attr( 'data-value' ) !== $(this).val() ) {
						translations[ $(this).attr( 'data-key') ] = $(this).val();
					}
				}
			);
			return translations;
		}
		
		function unsaved_modification() {
			$( window ).bind( 'beforeunload', function() {
				return 'You have unsaved changes!';
			});
		}
	}


	/* Action for the restore default page content button on edit page */
	$( '#restore-default-content-button' ).click( function() {
		var button = $(this);
		button.prop( 'disabled', true );
		$( '#restore-default-content-spiner' ).show();
		
		var post_name;
		if(
			'string' !== $.type( post_name = $( '#restore-default-content-button' ).attr( 'data-post-name' ) ) ||
			!post_name.length
		) {
			alert( "Sorry we couldn't restore the default content of this page.\n Please try again.\nError code: admin_1" );
		}
		
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,/* This variable is populated by wordpress http://codex.wordpress.org/AJAX_in_Plugins */
			data: {
				'action': 'restore_default_content',
				'post_name' : post_name
			},
			success: function( response ) {
				if(
					!$.isPlainObject( response ) ||
					'boolean' !== $.type( response.success ) ||
					'string' !== $.type( response.error_code )
				) {
					alert( "Sorry we couldn't restore the default content of this page.\n Please try again.\nError code: admin_2" );
				}
				else if( !response.success ) {
					alert( "Sorry we couldn't restore the default content of this page.\n Please try again.\nError code: " + response.error_code );
				}
				else{
					/* Add/Replace the message parameter to the value 4 => this display the "Page updated." message */
					var new_location = location.href;
					if( -1 !== location.href.indexOf( 'message=' ) ) {
						new_location = location.href.replace( /message=\d+/,'message=4')
					}
					else
					{
						new_location += '&message=4';
					}
					location.href = new_location;
				}
				
				button.prop( 'disabled', false );
				$( '#restore-default-content-spiner' ).hide();
			}
		}).fail( function() {
			alert( "Sorry we couldn't restore the default content of this page.\n Please try again.\nError code: admin_3" );
			button.prop( 'disabled', false );
			$( '#restore-default-content-spiner').hide();
		});
	});

	/* Action for the Use new Template for the contact us page on the edit page */
	$('#use-new-version-button').on('click',function (){
	/* confirm */
		var r = confirm("The page content will be removed, any custom change in this page will be lost, be sure to copy or backup your custom content, press OK to proceed.");
		if (r == false) {
			return false;
		} 
		/* try to switch views */
		$("#content-tmce").trigger("click");
		// Sets the HTML contents of the activeEditor editor
		tinyMCE.activeEditor.setContent('');
		/* html view should stay */
		$("#content-html").trigger("click");
		/* select contact us template*/
		if($("#page_template option[value='page-templates/contact-page.php']").length > 0){
			$("#page_template").val("page-templates/contact-page.php");
			/* saving */
			$("#publish").trigger("click");
		}else{
			alert('Contact Page Template not found.');
			return false;
		}				
	});
	
	/* SSL config JS */
	if( $( '#ssl_config').length ) {
		
		// Change image on the fly when script is being updated
		$('#ssl-input').bind('input', function() {
			$('#ssl-preview').attr( 'src', 'data:text/html;charset=utf-8,' + $(this).val() );
		});

		// Trigger change on select
		$('#ssl-select').change( function() {
			change_selected_trigger( $(this).children( 'option:selected' ) );
		});

		// Fill the select
		$.each(
			sslProviders,
			function ( key ) {
			var option  = $('<option />').attr( 'value', key ).text( this.label );
			$('#ssl-select').append( option );
			if( this.selected ) {
				change_selected_trigger( option );
			}
		});


		function change_selected_trigger( option ) {
			option.attr( 'selected', 'selected' );
			$('#ssl-input').prop('disabled', ( 'godaddy' === option.val() ) );
			$('#ssl-input').val( sslProviders[ option.val() ]['content'] ).trigger( 'input' );
		}
	}

});

