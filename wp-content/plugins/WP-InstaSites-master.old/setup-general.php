<?php

if ( isset( $_POST['reset-data'] ) ) {
	$ent = $_POST['reset-data'];
	if ( $ent == 'soldata' ) {
		update_option( 'bapi_solutiondata_lastmod', 0 );
	}
	if ( $ent == 'seodata' ) {
		update_option( 'bapi_keywords_lastmod', 0 );
	}
}

$lastmod_soldata = get_option('bapi_solutiondata_lastmod');
$lastmod_seodata = get_option('bapi_keywords_lastmod');
$lastmod_soldata = (!empty($lastmod_soldata) ? date('r',$lastmod_soldata) : "N/A");
$lastmod_seodata = (!empty($lastmod_seodata) ? date('r',$lastmod_seodata) : "N/A");

$soldata = is_super_admin() ? get_option('bapi_solutiondata') : 'N/A';
$seodata = is_super_admin() ? get_option('bapi_keywords_array') : 'N/A';

$solutiondata_error = get_option('bapi_sync_error', false);
$seo_error = get_option('bapi_seo_sync_error', false);
?>

<link rel="stylesheet" type="text/css" href="<?= plugins_url('/css/jquery.ui/jquery-ui-1.10.2.min.css', __FILE__) ?>" />
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#viewraw-soldata').click(function() { $("#dlg-soldata").dialog({ width: 540});});	
		$('#viewraw-seodata').click(function() { $("#dlg-seodata").dialog({ width: 540 });});
		$('#reset-soldata').click(function() { $("#reset-soldata-form").submit(); });	
		$('#reset-seodata').click(function() { $("#reset-seodata-form").submit(); });	
	});
</script>
<div class="wrap">
<?php
echo '<h1><img src="' . plugins_url('/img/logo_kigo.png', __FILE__) . '"/></h1>';

?>
<h2><?php echo 'General'; ?></h2>
<table class="form-table">
<tr valign="top">
	<td scope="row">Site Live:</td>
	<td><?php $st=array(); echo $sitelive; ?></td>
</tr>
<tr valign="top">
	<td scope="row">API Key:</td>
	<td><?php echo get_option('api_key'); ?></td>
</tr>
<tr valign="top">
	<td scope="row">Language:</td>
	<td><?php echo get_option('bapi_language'); ?></td>	
</tr>
<tr valign="top">
	<th scope="row">Solution Data Last Sync<?php echo $solutiondata_error ? ' Attempt' : ''; ?>:</th>
	<td><?php echo $lastmod_soldata; ?>
		<a href="javascript:void(0)" id="viewraw-soldata" style="<?php if(!is_super_admin()){echo 'display:none;'; } ?>">View Raw</a>
		<a href="javascript:void(0)" id="reset-soldata" style="<?php if(!is_super_admin()){echo 'display:none;'; } ?>">Reset</a>
	</td>
</tr>

<?php if($solutiondata_error ) { ?>
<tr valign="top">
	<th scope="row"></th>
	<td><?php display_wp_error($solutiondata_error, "Solution Sync Error"); ?></td>
</tr>
<?php } ?>

<tr valign="top">
	<th scope="row">SEO Last Sync<?php echo $seo_error ? ' Attempt' : ''; ?>:</th>
	<td><?php echo $lastmod_seodata; ?>
		<a href="javascript:void(0)" id="viewraw-seodata" style="<?php if(!is_super_admin()){echo 'display:none;'; } ?>">View Raw</a>
		<a href="javascript:void(0)" id="reset-seodata" style="<?php if(!is_super_admin()){echo 'display:none;'; } ?>">Reset</a>
	</td>
</tr>
<?php if($seo_error ) { ?>
<tr valign="top">
	<th scope="row"></th>
	<td><?php display_wp_error($seo_error, "SEO Sync Error"); ?></td>
</tr>
<?php } ?>
<tr>
	<td colspan="2"><em>If you do not already have an API key for Kigo sites, please contact <a href="mailto:support@kigo.net?subject=API%20Key%20-%20Wordpress%20Plugin">support@kigo.net</a> to obtain an API key.</em></td>
</tr>
</table>
</div>

<div id="dlg-soldata" title="Solution Data" style="display:none">
<textarea style="width:500px;height:300px"><?php echo htmlentities($soldata); ?></textarea>
</div>

<div id="dlg-seodata" title="SEO Data" style="display:none">
<textarea style="width:500px;height:300px"><?php echo htmlentities($seodata); ?></textarea>
</div>

<div id="hidden-reset-forms" style="display:none">
	<form id="reset-soldata-form" method="post">
		<input type="hidden" name="reset-data" value="soldata" />
		<input type="hidden" name="bapi_solutiondata" value="" />
		<input type="hidden" name="bapi_solutiondata_lastmod" value="0" />
	</form>
	<form id="reset-seodata-form" method="post">
		<input type="hidden" name="reset-data" value="seodata" />
		<input type="hidden" name="bapi_keywords_array" value="" />
		<input type="hidden" name="bapi_keywords_lastmod" value="0" />
	</form>
</div>