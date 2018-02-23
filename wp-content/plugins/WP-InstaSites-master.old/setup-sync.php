<?php	
	loadscriptjquery();
	getconfig(); // need this to update detail pages

	$soldata = json_decode(wp_unslash(get_option('bapi_solutiondata')),TRUE);
?> 

<div class="wrap">
<?php   echo '<h1><img src="' . plugins_url('/img/logo_kigo.png', __FILE__) . '"/></h1>'; ?>
<h2><?php echo 'Data Sync Setup'; ?></h2>

<h3>Initial Configuration</h3>
<div style="margin-top:-5px;">			
	<input class="button-primary setuppages" value="Create Default Pages">
</div>	
<div class="clear"></div>				
<small>Note: Permalink Settings should be set to Post name for the menu structure to function correctly.</small>
<div class="clear"></div>
<br>
<div class="clear"></div>

<div style="margin-top:-5px;">			
	<input class="button-primary crawlpages" value="Crawl Sitemap">
</div>	
<div class="clear"></div>	

<br />
<h3>Property pages synchronization information</h3>
<!-- hiding the information as of now we are not using cron 
<table class="widefat" style="width:auto">
	<tr>
		<td>
			Cron synchronization enabled:
			<?php //if( !(defined( 'KIGO_CRON_SECRET' ) ) ) : ?>
			<br /><small>Note: contact support to enable it.</small>
			<?php //endif ?>
		</td>
		<td><span class="<?php //echo ( defined( 'KIGO_CRON_SECRET' ) ) ? 'green' : 'red'; ?>"><?php //echo ( defined( 'KIGO_CRON_SECRET' ) ) ? 'Yes' : 'No'; ?></span></td>
	</tr>
	<tr>
		<td>Time since last successful sync:<br /><small>Note: If last successful sync was more than 1h ago, please contact support.</small></td>
		<td id="last-exec"></td>
	</tr>
</table>

	<br /> -->
	<button type="button" id="force_full_sync" class="button-primary">Force full properties sync</button><span id="force_full_sync_spinner" class="spinner alignleft"></span>
	<br /><small>Note: Use this if changes made on the App don't appear on your website.</small>
	<br /><small>Note: This may take several minutes.</small>

<br /><br />
<h3>Base URLs</h3>
<small>These base urls define where detail pages will get synced.</small>
<table class="form-table">
<tr valign="top">
	<td scope="row">Property:</td>
	<td><?php echo $soldata["Site"]["BasePropertyURL"]; ?></td>
</tr>
<tr valign="top">
	<td scope="row">Developments:</td>
	<td><?php echo $soldata["Site"]["BaseDevelopmentURL"]; ?></td>
</tr>
<tr valign="top">
	<td scope="row">Attractions:</td>
	<td><?php echo $soldata["Site"]["BasePOIURL"]; ?></td>
</tr>
<tr valign="top">
	<td scope="row">Specials:</td>
	<td><?php echo $soldata["Site"]["BaseSpecialURL"]; ?></td>
</tr>
<tr valign="top">
	<td scope="row">Search Buckets:</td>
	<td><?php echo $soldata["Site"]["BasePropertyFinderURL"]; ?></td>
</tr>
<tr valign="top" style="display:none">
	<td scope="row">Market Areas:</td>
	<td><?php echo $soldata["Site"]["BaseMarketAreaURL"]; ?></td>
</tr>
</table>
<small>Note: Base urls need to be modified in the control panel.</small>

</div>

<div id="dlg-result" style="display:none; width:600px">
	<div id="dlg-txtresult" style="padding:10px; height:300px; overflow: auto"></div>
</div>

<script type="text/javascript">
	function getImportParams(entity) {
		if (entity == "property") {
			return { "entity": entity, "template": "tmpl-properties-detail", "parent": "bapi_search" }
		}
		if (entity == 'development') {
			return { "entity": entity, "template": "tmpl-developments-detail", "parent": "bapi_search" }
		}
		if (entity == 'specials') {
			return { "entity": entity, "template": "tmpl-specials-detail", "parent": "bapi_search" }
		}
		if (entity == 'poi') {
			return { "entity": entity, "template": "tmpl-attractions-detail", "parent": "bapi_search" }
		}
		if (entity == 'searches') {
			return { "entity": entity, "template": "tmpl-searches-detail", "parent": "bapi_search" }
		}
	}
	jQuery(document).ready(function($){
	
		$("#tabs").tabs();  			

		$(".setuppages").on("click", function () {			
			if (confirm("Are you sure you want to setup the menu system")) {
				$('#dlg-result').dialog({width:700});
				var txtresult = $('#dlg-txtresult');
				txtresult.html('<h5>Setting up menu system</h5>');
				var url = '/bapi.init?p=1';

				$.post(url, function(res) {
					txtresult.append(res);
					txtresult.append('<b>Done!</b>');
				});
				/*
				$.each(pagedefs, function (i, pagedef) {
					var url = '<?= plugins_url('/init.php', __FILE__) ?>?' + $.param(pagedef);
					$.get(url, function(data) {
						txtresult.append(data);
					});					
				});*/							
			}
		});	

		$(".crawlpages").on("click", function () {			
			if (confirm("Crawling the sitemap will load all pages.  Proceed?")) {
				$('#dlg-result').dialog({width:700});
				var txtresult = $('#dlg-txtresult');
				txtresult.html('<h5>Crawling Sitemap.xml</h5>');
				var url = '/sitemap_crawler.svc';
				jQuery.post(url, {}, function(res) {
					txtresult.append(res);
				});						
			}
		});	
	});
</script>
