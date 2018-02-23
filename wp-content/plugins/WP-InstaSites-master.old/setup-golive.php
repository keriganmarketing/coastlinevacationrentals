<?php
	$cdn_url = get_option('home');
	if(get_option('bapi_site_cdn_domain')){ $cdn_url = get_option('bapi_site_cdn_domain'); }
	$cdn_url = parse_url($cdn_url);
?>

<style type="text/css">
.bapi_expand{
	-webkit-border-radius: 3px;
	border-radius: 3px;
	border-width: 1px;
	border-style: solid;
	margin: 5px 15px 15px 0;
	padding: 0 .6em;
	background-color: #ddd;
	border-color: #ccc;
	cursor: pointer;
}
.bapi_expand_hidden{
	display: none;
}
</style>

<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#gdsetup').click(function(){
			$('#gdsetup .bapi_expand_hidden').fadeToggle();
		});
		var nip = $('#bapi_wildcard_ip').html();
		$('#bapi_wildcard_ip_inst').html(nip);
		var atesturl = $('#arecordtest').attr('href');
		$('#arecordtest').attr('href',atesturl+nip)
	});	
</script>
<div class="wrap">
	<?php   echo '<h1><img src="' . plugins_url('/img/logo_kigo.png', __FILE__) . '"/></h1>'; ?>
	<h2><?php echo 'Take me Live!'; ?></h2>
	<h3>Instructions</h3>
	<p>
		To go live, you must make changes with your DNS provider.  If you are unable to make these changes, please contact Kigo Support.
	</p>
	<table class="form-table" border="1">
		<tr>
			<th><strong>Record Type</strong></th>
			<th><strong>Setting</strong></th>
			<th><strong>Value</strong></th>
		</tr>
		<tr>
			<td>A Record</td>
			<td>@ (Wildcard)</td>
			<td id="bapi_wildcard_ip">137.117.72.13</td>
		</tr>
		<tr>
			<td>CNAME Record</td>
			<td>www</td>
			<td><?php echo get_option('bapi_cloudfronturl') ?></td>
		</tr>
	</table>
	<p>
		Click here to check DNS propagation for your domain: <a id="arecordtest" class="button" href="http://www.whatsmydns.net/#A/<?= str_replace('www.','',$cdn_url['host']) ?>/" target="_blank">Test <strong>A Record</strong></a>
		<?php if(get_option('bapi_cloudfronturl')&&(get_option('bapi_cloudfronturl')!='')){ ?><a class="button" href="http://www.whatsmydns.net/#CNAME/<?= $cdn_url['host'] ?>/<?= get_option('bapi_cloudfronturl') ?>" target="_blank">Test <strong>CNAME</strong></a><?php } ?>
	</p>
	<div id="gdsetup" class="bapi_expand">
		<h4 title="Click Here to Show Instructions">[+] GoDaddy DNS Setup Instructions</h4>
		<div class="bapi_expand_hidden">
			<em>Please note that these instructions are generated based on the current version of GoDaddy's website and DNS management tools. Please contact support@kigo.net to report any discrepancies.</em>
			<ol>
				<li>Go to <a href="http://dcc.godaddy.com">GoDaddy.com</a> and sign in to your account.</li>
				<li>From the <em>Domains</em> management screen, click on the domain name you wish to update.<br/>
					<img src="<?= plugins_url('/img/dns/godaddy-domains-summary.png', __FILE__) ?>"/></li>
				<li>On the <em>Domain Details</em> screen, click on the <em>DNS Zone File</em> tab and then click <em>Edit</em>.  This will open the <em>Zone File Editor</em> in a new browser tab.<br/>
					<img src="<?= plugins_url('/img/dns/godaddy-domain-detail.png', __FILE__) ?>"/></li>
				<li>Locate the <em>A (Host)</em> record for <em>"@"</em> and set it to <strong id="bapi_wildcard_ip_inst"></strong>. Use the <em>Quick Add</em> button if the record does not already exist.<br/>
					<img src="<?= plugins_url('/img/dns/godaddy-zone-editor1.png', __FILE__) ?>"/></li>
				<li>Locate the <em>CNAME (Alias)</em> record for <em>"www"</em> and set it to <strong><?php if(get_option('bapi_cloudfronturl')==''){echo 'the CloudFront URL you will be given before going live';}else{echo get_option('bapi_cloudfronturl');} ?></strong>. Use the <em>Quick Add</em> button if the record does not already exist. <br/>
					<em>If there is already an A (Host) record for "www", you must delete that first before adding a www CNAME record.</em><br/>
					<img src="<?= plugins_url('/img/dns/godaddy-zone-editor2.png', __FILE__) ?>"/></li>
				<li>Save your zone file changes.<br/>
					<img src="<?= plugins_url('/img/dns/godaddy-zone-editor3.png', __FILE__) ?>"/></li>
			</ol>
			<p>Please note that after saving your changes it will take as long as 48 hours for complete global DNS propagation.  In most cases, your live site URL will begin working within just a few minutes.</p>
		</div>
	</div>
</div>