<?php				
	// handle if this is a post
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {		
		update_option('bapi_global_header', stripslashes($_POST['bapi_global_header']));
		update_option('bapi_google_conversion_key', stripslashes($_POST['bapi_google_conversion_key']));
		update_option('bapi_google_conversion_label', stripslashes($_POST['bapi_google_conversion_label']));
		update_option('bapi_google_webmaster_htmltag', sanitize_text_field(stripslashes($_POST['bapi_google_webmaster_htmltag'])));
		update_option('bapi_display_related_websites', @implode(",",$_POST['bapi_display_related_websites']));
		bapi_wp_site_options();
		BAPISync::updateLastSettingsUpdate();
		echo '<div id="message" class="updated"><p><strong>Settings saved.</strong></p></div>';
	}
global $bapi_all_options;
$solution_data 				= json_decode(wp_unslash($bapi_all_options['bapi_solutiondata']));
$display_related_websites 	= array();
if($bapi_all_options['bapi_display_related_websites'])
{
	$display_related_websites 	= explode(",", $bapi_all_options['bapi_display_related_websites']);
}
?> 
<link rel="stylesheet" type="text/css" href="<?= plugins_url('/css/jquery.ui/jquery-ui-1.10.2.min.css', __FILE__) ?>" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#bapi_google_webmaster_htmltag').click(function() { $("#bapi_google_webmaster_htmltag-large").dialog({ width: $(window).width()-60, height: $(window).height()-60 });});
	});
</script>
<div class="wrap">
<?php
    echo '<h1><img src="' . plugins_url('/img/logo_kigo.png', __FILE__) . '"/></h1>';

?>
<h2><?php echo 'Advanced Options'; ?></h2>
<form method="post">
<table class="form-table">
<tr valign="top">
	<td scope="row">Display Related Websites <br/><em><small> (Language Flags) </small></em></td>
	<td>
	<?php	
		foreach($solution_data->Sites as $site)
		{	
			$checked = false;
			if( in_array($site->ID, $display_related_websites) || get_option('bapi_display_related_websites') === false)
			{
				$checked= "checked";
			}
			else { 
				$checked = false;
			}
		?>
		<label title="<?= $site->Language ?>">
		<input type="checkbox" name="bapi_display_related_websites[]" <?=$checked?> value="<?= $site->ID ?>"> 
		<?php echo $site->Url." &nbsp;(".$site->Language.")"; ?> 
		</label><br>
	<?php } ?>
	</td>
</tr>
<tr valign="top">
	<td scope="row">Google AdWords Conversion Key<br/><em><small>The google_conversion_id value</small></em></td>
	<td><input type="text" name="bapi_google_conversion_key" id="bapi_google_conversion_key" size="80" value="<?= esc_attr($bapi_all_options['bapi_google_conversion_key']) ?>" /></td>
</tr>
<tr valign="top">
	<td scope="row">Google AdWords Conversion Label<br/><em><small>The google_conversion_label value no quotes</small></em></td>
	<td><input type="text" name="bapi_google_conversion_label" id="bapi_google_conversion_label" size="80" value="<?= esc_attr($bapi_all_options['bapi_google_conversion_label']) ?>" /></td>
</tr>
<tr valign="top">
	<td scope="row">Global Header Scripts<br/><em><small>JavaScript must be contained within &lt;script&gt; tags.</small></em></td>
	<td><textarea name="bapi_global_header" id="bapi_global_header" cols="80" rows="8"><?=  $bapi_all_options['bapi_global_header'] ?></textarea></td>
</tr>
<tr valign="top">
	<td scope="row">Google Webmaster Verification <em>(HTML Tag Method)</em><br/><em><small><img id="bapi_google_webmaster_htmltag" src="<?= plugins_url('/img/gw_html_tag_verification.png', __FILE__) ?>" height="60" title="Click Here to See Sample Verification Code"/><br/><a href="https://support.google.com/webmasters/answer/35659?hl=en" target="_blank" >More information from Google Help</a></small></em></td>
	<td><input type="text" name="bapi_google_webmaster_htmltag" id="bapi_google_webmaster_htmltag" size="80" value="<?= esc_attr($bapi_all_options['bapi_google_webmaster_htmltag']) ?>" /></td>
</tr>
</table>
<div class="clear"></div>
<?php submit_button(); ?>
</form>
<div id="bapi_google_webmaster_htmltag-large" title="Google Webmaster HTML Tag Verification Example" style="display:none">
<img src="<?= plugins_url('/img/gw_html_tag_verification.png', __FILE__) ?>" width="100%" />
</div>
</div>
