<?php

$bapi = getBAPIObj();
if(!$bapi->isvalid()) {
	echo '<script type="text/javascript">window.location.href="' . menu_page_url('site_settings_general', false) . '"</script>';
	exit();
}

	global $bapi_all_options;	
	// handle if this is a post
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {	
		//bapi_wp_site_options();
		unset($_POST['submit']);		
		//$postSitesettings = $_POST;
		//print_r($postSitesettings);
		
		/*foreach ($postSitesettings as $k => $v) {
			echo "[$k] => $v<br/>";
		}*/

		 

		$post = $_POST;
		unset($post['settingsRaw']);

		$sitesettings = $post;
		$sitesettings_raw = $_POST['settingsRaw'];


		//Length of stay
		$deflos = $sitesettings_raw['deflos']; 
		if($deflos == 'Disabled') { $deflos = 0; } 
		$deflos_int = intval($deflos); 


		if(strpos($deflos, 'Week')) { $deflos_int *= 7; }
		$sitesettings_raw['deflos'] = $deflos_int;

		if($sitesettings_raw['propdetail-availcal'] != 'Hide Availability Calendars') { 
			$sitesettings_raw['propdetail-availcal'] = filter_var($sitesettings_raw['propdetail-availcal'], FILTER_SANITIZE_NUMBER_INT);
		}


		//echo "<pre>"; print_r($sitesettings); echo "</pre>";

		switch($sitesettings['checkinoutmode']) {
			case 'BAPI.config().checkin.enabled=false; BAPI.config().checkout.enabled=false; BAPI.config().los.enabled=false;': $s = 0; break;
			case 'BAPI.config().checkin.enabled=true; BAPI.config().checkout.enabled=true; BAPI.config().los.enabled=false;': $s = 1; break;
			case 'BAPI.config().checkin.enabled=true; BAPI.config().checkout.enabled=false; BAPI.config().los.enabled=true;': $s = 2;
		}
		$sitesettings_raw['checkinoutmode'] = $s;


		switch($sitesettings['searchsort']) {
			case "BAPI.config().sort=\'beds\';": $sort = "beds"; break;
			case "BAPI.config().sort=\'sleeps\';": $sort = "sleeps"; break;
			case "BAPI.config().sort=\'category\';": $sort = "category"; break;
			case "BAPI.config().sort=\'headline\';": $sort = "headline"; break;
			case "BAPI.config().sort=\'location\';": $sort = "location"; break;
			case "BAPI.config().sort=\'minrate\';": $sort = "minrate"; break;
			case "BAPI.config().sort=\'maxrate\';": $sort = "maxrate"; break;
			case "BAPI.config().sort=\'random\';": $sort = "random"; 	
		}
		$sitesettings_raw['searchsort'] = $sort;

		if('Ascending' == $sitesettings_raw['searchsortorder']) {
			$sitesettings_raw['searchsortorder'] = 'ASC';
		}

		if('Decending' == $sitesettings_raw['searchsortorder']) {
			$sitesettings_raw['searchsortorder'] = 'DESC';
		}


		//echo "<pre>"; print_r($sitesettings_raw); echo "</pre>";


		$sitesettings = json_encode($sitesettings);

		update_option('bapi_sitesettings',  $sitesettings);
		update_option('bapi_sitesettings_raw', $sitesettings_raw);
		update_option('bapi_sitesettings_lastmod', time());


		BAPISync::updateLastSettingsUpdate();
		echo '<div id="message" class="updated"><p><strong>Settings saved.</strong></p></div>';		
	}
	else {
		$sitesettings = $bapi_all_options['bapi_sitesettings'];
		$sitesettings_array = json_decode($sitesettings, true);
		$sitesettings_raw = get_option('bapi_sitesettings_raw'); 

		if($_GET['debug']) {
			echo "<pre>"; print_r( json_decode($sitesettings) ); echo "</pre>";
			echo "<pre>"; print_r($sitesettings_raw); echo "</pre>";
		}
	}

	$settings = $sitesettings_raw;

	$bapiSolutionData = BAPISync::getSolutionData();
	$bapiSolutionDataConfig = $bapiSolutionData["ConfigObj"];
	$maxbedsearch = $bapiSolutionDataConfig["beds"]["maxval"];
	$maxbeds = isset($sitesettings_raw['maxbedsearch']) ? $sitesettings_raw['maxbedsearch'] : $maxbedsearch;


/* we get BAPI */
loadscriptjquery();
getconfig();

?>

<div class="wrap sitesettings-wrapper" style="display: none;">
<?php   echo '<h1><img src="' . plugins_url('/img/logo_kigo.png', __FILE__) . '"/></h1>'; ?>
<h2><?php echo 'Property & Search Settings'; ?></h2>
<form method="post">

<h3>Search Result Display Modes</h3>
<table class="form-table">
<tr valign="top">
	<td scope="row">List View:</td>
	<td><input name="settingsRaw[searchmode-listview]" class="searchmode-listview-cbx" type="checkbox" checked="" />
	<input type="hidden" id="searchmode-listview" name="searchmode-listview" data-prevalue="BAPI.config().searchmodes.listview=" value="" />
	</td>	
</tr>
<tr valign="top">
	<td scope="row">Photo View:</td>
	<td><input name="settingsRaw[searchmode-photoview]" class="searchmode-photoview-cbx" type="checkbox" checked="" />
	<input type="hidden" id="searchmode-photoview" name="searchmode-photoview" data-prevalue="BAPI.config().searchmodes.photoview=" value="" />
	</td>	
</tr>
<tr valign="top">
	<td scope="row">Wide Photo View:</td>
	<td><input name="settingsRaw[searchmode-widephotoview]" class="searchmode-widephotoview-cbx" type="checkbox" checked="" />
	<input type="hidden" id="searchmode-widephotoview" name="searchmode-widephotoview" data-prevalue="BAPI.config().searchmodes.widephotoview=" value="" />
	</td>	
</tr>
<tr valign="top">
	<td scope="row">Pagination:</td>
	<td><input name="settingsRaw[searchmode-pagination]" class="searchmode-pagination-cbx" type="checkbox" checked="" />
	<input type="hidden" id="searchmode-pagination" name="searchmode-pagination" data-prevalue="BAPI.config().searchmodes.pagination=" value="" />
	</td>
</tr>
<tr valign="top">
	<td scope="row">Number of Properties per Page:</td>
	<td>
		<input name="settingsRaw[numberproppage]" type="hidden" value="<?php echo $settings['numberproppage']; ?>" data-sync="numberproppage" />
		<select name="numberproppage" id="numberproppage">
		<?php	
			for($x = 1; $x <= 20; $x++){
				echo '<option value="BAPI.config().pagesize='.$x.';" '.($settings['numberproppage'] == 'BAPI.config().pagesize='.$x.';' ? 'selected' : '').'>'.$x.'</option>';
			} 
		?>
		</select>
	</td>
</tr>
<tr valign="top">
	<td scope="row">Map View:</td>
	<td><input name="settingsRaw[searchmode-mapview]" class="searchmode-mapview-cbx" type="checkbox" checked="" />
	<input type="hidden" id="searchmode-mapview" name="searchmode-mapview" data-prevalue="BAPI.config().searchmodes.mapview=" value="" />
	</td>	
</tr>
<tr valign="top">
	<td scope="row">Google Default Map View:</td>
	<td>
		<input type="hidden" name="settingsRaw[mapviewType]" value="<?php echo $mapType = $sitesettings_raw['mapviewType']; ?>" data-sync="mapviewType" />
		<select id="map-settings-select" name="mapviewType">
			<option value="BAPI.config().mapviewType='ROADMAP';" <?php echo $mapType == 'Roadmap' ? 'selected' : ''; ?>>Roadmap</option>
			<option value="BAPI.config().mapviewType='SATELLITE';" <?php echo $mapType == 'Satellite' ? 'selected' : ''; ?>>Satellite</option>
			<option value="BAPI.config().mapviewType='HYBRID';" <?php echo $mapType == 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
			<option value="BAPI.config().mapviewType='TERRAIN';" <?php echo $mapType == 'Terrain' ? 'selected' : ''; ?>>Terrain</option>
		</select>
	</td>
</tr>
<tr valign="top">
 <td scope="row">Avg Review Stars:</td>
 <td><input name="settingsRaw[averagestarsreviews]" class="averagestarsreviews-cbx" type="checkbox" checked="" />
 <input type="hidden" id="averagestarsreviews" name="averagestarsreviews" data-prevalue="BAPI.config().hidestarsreviews=" value="" />
 </td>
</tr>
<!--<tr valign="top">
	<td scope="row">Hotel View:</td>
	<td><input class="searchmode-hotelview-cbx" type="checkbox" checked="" />
	<input type="hidden" id="searchmode-hotelview" name="searchmode-hotelview" data-prevalue="BAPI.config().searchmodes.hotelview=" value="" />
	</td>	
</tr>-->
<tr valign="top">
	<td scope="row">Default Search Result View:</td>
	<td>
		<select name="defaultsearchresultview" id="defaultsearchresultview">
		</select>
	</td>
</tr>
</table>
<div class="clear"></div>

<h3>Search Result Settings</h3>
<table class="form-table">
<tr valign="top">
	<td scope="row">Availability Filtering:</td>
	<td><input name="settingsRaw[showunavailunits]" class="showunavailunits-cbx" type="checkbox" checked="" />
	<input type="hidden" id="showunavailunits" name="showunavailunits" data-prevalue="BAPI.config().restrictavail=" value="" />
	</td>	
</tr>
<!--<tr valign="top">
	<td scope="row">Show Avg. Review Rating in Search Result:</td>
	<td><input id="" type="checkbox" name=""></td>
</tr>-->
<tr valign="top">
	<td scope="row">Default Search Sort Order Option:</td>
	<td>
		<input name="settingsRaw[searchsort]" type="hidden" value="<?php echo $settings['searchsort']; ?>" data-sync='searchsort' />
		<select name="searchsort" id="searchsort">
		<option value="BAPI.config().sort='beds';" <?php echo ($settings['searchsort'] == "BAPI.config().sort='beds';" ? 'selected' : ''); ?> >By Bedrooms</option>
		<option value="BAPI.config().sort='sleeps';" <?php echo ($settings['searchsort'] == "BAPI.config().sort='sleeps';" ? 'selected' : ''); ?> >By Sleeps</option>
		<option value="BAPI.config().sort='category';" <?php echo ($settings['searchsort'] == "BAPI.config().sort='category';" ? 'selected' : ''); ?> >By Category</option>
		<option value="BAPI.config().sort='headline';" <?php echo ($settings['searchsort'] == "BAPI.config().sort='headline';" ? 'selected' : ''); ?> >By Headline</option>
		<option value="BAPI.config().sort='location';" <?php echo ($settings['searchsort'] == "BAPI.config().sort='location';" ? 'selected' : ''); ?> >By City</option>
		<option value="BAPI.config().sort='minrate';" <?php echo ($settings['searchsort'] == "BAPI.config().sort='minrate';" ? 'selected' : ''); ?> >By Minimum Price</option>
		<option value="BAPI.config().sort='maxrate';" <?php echo ($settings['searchsort'] == "BAPI.config().sort='maxrate';" ? 'selected' : ''); ?> >By Maximum Price</option>
		<option value="BAPI.config().sort='random';" <?php echo ($settings['searchsort'] == "BAPI.config().sort='random';" ? 'selected' : ''); ?> >Random</option>
		</select>

		<input name="settingsRaw[searchsortorder]" type="hidden" value="<?php echo $settings['searchsortorder']; ?>" data-sync="searchsortorder" />
		<select name="searchsortorder" id="searchsortorder">
			<option value="BAPI.config().sortdesc=false;" <?php echo ($settings['searchsortorder'] == "BAPI.config().sortdesc=false;" ? 'selected' : ''); ?> >Ascending</option>
			<option value="BAPI.config().sortdesc=true;" <?php echo ($settings['searchsortorder'] == "BAPI.config().sortdesc=true;" ? 'selected' : ''); ?> >Descending</option>		
		</select>
	</td>
</tr>
</table>
<div class="clear"></div>

<h3>Property Search Form Settings</h3>
<table class="form-table">
<tr valign="top">
	<td scope="row">Check-In Check-Out Mode:</td>
	<td>
		<input name="settingsRaw[checkinoutmode]" type="hidden" value="<?php echo $settings['checkinoutmode']; ?>" />
		<select name="checkinoutmode" id="checkinoutmode">
			<option value="BAPI.config().checkin.enabled=false; BAPI.config().checkout.enabled=false; BAPI.config().los.enabled=false;" <?php echo ($settings['checkinoutmode'] == 'BAPI.config().checkin.enabled=false; BAPI.config().checkout.enabled=false; BAPI.config().los.enabled=false;' ? 'selected' : ''); ?> >Disabled</option>
			<option value="BAPI.config().checkin.enabled=true; BAPI.config().checkout.enabled=true; BAPI.config().los.enabled=false;" <?php echo ($settings['checkinoutmode'] == 'BAPI.config().checkin.enabled=true; BAPI.config().checkout.enabled=true; BAPI.config().los.enabled=false;' ? 'selected' : ''); ?> >Check In Date Picker/Check Out DatePicker</option>
			<option value="BAPI.config().checkin.enabled=true; BAPI.config().checkout.enabled=false; BAPI.config().los.enabled=true;" <?php echo ($settings['checkinoutmode'] == 'BAPI.config().checkin.enabled=true; BAPI.config().checkout.enabled=false; BAPI.config().los.enabled=true;' ? 'selected' : ''); ?> >Check In Date Picker/Length of Stay DropDown</option>
	    </select>
	</td>
</tr>
<tr valign="top">
	<td scope="row">Default Nights of Stay:</td>
	<td><?php //test($bapiSolutionDataConfig); ?>
		<input name="settingsRaw[deflos]" type="hidden" value="<?php echo $settings['deflos']; ?>" data-sync="deflos" />
		<select name="deflos" id="deflos">
			<option value="BAPI.config().los.defaultval=0; BAPI.config().los.minval=0;">Disabled</option>
			<?php foreach($bapiSolutionDataConfig['los']['values'] as $los) {
				echo sprintf('<option value="'.$los['Data'].'" %s >'.$los['Label'].'</option>', $los['Data'] == $settings['deflos'] ? 'selected="true"' : '' );
			} ?>
	    </select>
	</td>	
</tr>

<!--<tr valign="top">
	<td scope="row">Default Check-In Date X # of days Out (0 means no default):</td>
	<td><input id="" type="numeric" name=""></td>
</tr>-->
<tr valign="top">
	<td scope="row">Category Search:</td>
	<td>
		<input name="settingsRaw[categorysearch]" class="categorysearch-cbx" type="checkbox" checked="" />
		<input type="hidden" id="categorysearch" name="categorysearch" data-prevalue="BAPI.config().category.enabled=" value="" />
	</td>	
</tr>
<!--<tr valign="top">
	<td scope="row">Sleeps Search (Exactly):</td>
	<td><input class="sleepsearch-cbx" type="checkbox" checked="" />
	<input type="hidden" id="sleepsearch" name="sleepsearch" data-prevalue="BAPI.config().sleeps.enabled=" value="" />
	</td>	
</tr>-->
<tr valign="top">
	<td scope="row">Sleeps Search (Min):</td>
	<td>
		<input name="settingsRaw[minsleepsearch]" class="minsleepsearch-cbx" type="checkbox" checked="" />
		<input type="hidden" id="minsleepsearch" name="minsleepsearch" data-prevalue="BAPI.config().minsleeps={}; BAPI.config().minsleeps.enabled=" value="" />
	</td>	
</tr>
<tr valign="top">
	<td scope="row">Bedroom Search (Exactly):</td>
	<td>
		<input name="settingsRaw[bedsearch]" class="bedsearch-cbx" type="checkbox" checked="" />
		<input type="hidden" id="bedsearch" name="bedsearch" data-prevalue="BAPI.config().beds.enabled=" value="" />
	</td>
</tr>
<tr valign="top">
	<td scope="row">Bedroom Search (Min):</td>
	<td>
		<input name="settingsRaw[minbedsearch]" class="minbedsearch-cbx" type="checkbox" checked="" />
		<input type="hidden" id="minbedsearch" name="minbedsearch" data-prevalue="BAPI.config().minbeds={}; BAPI.config().minbeds.enabled=" value="" />
	</td>	
</tr>
<tr valign="top">
	<td scope="row">Max Bedrooms In List:</td>
	<td>
		<select name="settingsRaw[maxbedsearch]" id="maxbedsearch">
			<?php for($i=1;$i<=$maxbedsearch;$i++) {
				echo "<option ".($i == $maxbeds ? 'selected="selected"' : '').">$i</option>";
			} ?>
		</select>
	</td>
</tr>
<tr valign="top">
	<td scope="row">Amenity Search:</td>
	<td>
		<input name="settingsRaw[amenitysearch]" class="amenitysearch-cbx" type="checkbox" checked="" />
		<input type="hidden" id="amenitysearch" name="amenitysearch" data-prevalue="BAPI.config().amenity.enabled=" value="" />
	</td>
</tr>
<tr valign="top">
	<td scope="row">Development Search:</td>
	<td>
		<input name="settingsRaw[devsearch]" class="devsearch-cbx" type="checkbox" checked="" />
		<input type="hidden" id="devsearch" name="devsearch" data-prevalue="BAPI.config().dev.enabled=" value="" />
	</td>	
</tr>
<tr valign="top">
	<td scope="row">Number of Adults Search:</td>
	<td><input name="settingsRaw[adultsearch]" class="adultsearch-cbx" type="checkbox" checked="" />
	<input type="hidden" id="adultsearch" name="adultsearch" data-prevalue="BAPI.config().adults.enabled=" value="" />
	</td>	
</tr>
<tr valign="top">
	<td scope="row">Number of Children Search:</td>
	<td>
		<input name="settingsRaw[childsearch]" class="childsearch-cbx" type="checkbox" checked="" />
		<input type="hidden" id="childsearch" name="childsearch" data-prevalue="BAPI.config().children.enabled=" value="" />
	</td>	
</tr>
<tr valign="top">
	<td scope="row">Property Headline Search:</td>
	<td>
		<input name="settingsRaw[headlinesearch]" class="headlinesearch-cbx" type="checkbox" checked="" />
		<input type="hidden" id="headlinesearch" name="headlinesearch" data-prevalue="BAPI.config().headline.enabled=" value="" />
	</td>	
</tr>
<tr valign="top" style="display:none;">
	<td scope="row">Max Rate Search:</td>
	<td>
		<input name="settingsRaw[maxratesearch]" class="maxratesearch-cbx" type="checkbox" checked="" />
		<input type="hidden" id="maxratesearch" name="maxratesearch" data-prevalue="BAPI.config().rate.enabled=" value="" />
	</td>
</tr>
<tr valign="top" style="display:none;" >
	<td scope="row">Include # of Rooms/Units Search:</td>
	<td>
		<input name="settingsRaw[roomsearch]" class="roomsearch-cbx" type="checkbox" checked="" />
		<input type="hidden" id="roomsearch" name="roomsearch" data-prevalue="BAPI.config().rooms.enabled=" value="BAPI.config().rooms.enabled=false" />
	</td>
</tr>
<tr valign="top">
	<td style="vertical-align:top;" scope="row">Location Search:</td>
	<td>
		<input name="settingsRaw[locsearch]" type="hidden" value="<?php echo $settings['locsearch']; ?>" data-sync="locsearch" />
		<select name="locsearch" id="locsearch">
		<option value="BAPI.config().city.enabled=false; BAPI.config().location.enabled=false;" <?php echo ($settings['locsearch'] == 'BAPI.config().city.enabled=false; BAPI.config().location.enabled=false;' ? 'selected' : ''); ?> >Disabled</option>
		<option value="BAPI.config().city.enabled=true; BAPI.config().location.enabled=false; BAPI.config().city.autocomplete=false;" <?php echo ($settings['locsearch'] == 'City Drop Down List' ? 'selected' : ''); ?>>City Drop Down List</option>
		<option value="BAPI.config().city.enabled=true; BAPI.config().location.enabled=false; BAPI.config().city.autocomplete=true;" <?php echo ($settings['locsearch'] == 'City Autocomplete' ? 'selected' : ''); ?>>City Autocomplete</option>
		<option value="BAPI.config().city.enabled=false; BAPI.config().location.enabled=true; BAPI.config().location.autocomplete=false;" <?php echo ($settings['locsearch'] == 'Market Area Drop Down List' ? 'selected' : ''); ?>>Market Area Drop Down List</option>
		<option value="BAPI.config().city.enabled=false; BAPI.config().location.enabled=true; BAPI.config().location.autocomplete=true;" <?php echo ($settings['locsearch'] == 'Market Area Autocomplete' ? 'selected' : ''); ?>>Market Area Autocomplete</option>
		</select>
		<p class="description">Market Area search options are for Enterprise solutions.</p>
	</td>
</tr>	
</table>
<div class="clear"></div>

<h3>Property Detail Screen Settings</h3>
<table class="form-table">
<tr valign="top">
	<td scope="row">Hide Rates &amp; Availability Tab:</td>
	<td>
		<input name="settingsRaw[propdetailrateavailtab]" class="propdetailrateavailtab-cbx" type="checkbox" checked="" />
		<input type="hidden" id="propdetailrateavailtab" name="propdetailrateavailtab" data-prevalue="BAPI.config().hideratesandavailabilitytab=" value="" />
	</td>
</tr>
<tr valign="top">
	<td scope="row">Availability Calendar:</td>
	<td>
		<?php $avails = array(
			array(
				'value'	=> 'BAPI.config().displayavailcalendar=false;  BAPI.config().availcalendarmonths=0;',
				'label'	=> 'Hide Availability Calendars'
			),
			array(
				'value'	=> 'BAPI.config().displayavailcalendar=true;  BAPI.config().availcalendarmonths=3;',
				'label'	=> 'Show 3 Months'
			),
			array(
				'value'	=> 'BAPI.config().displayavailcalendar=true;  BAPI.config().availcalendarmonths=6;',
				'label'	=> 'Show 6 Months'
			),
			array(
				'value'	=> 'BAPI.config().displayavailcalendar=true;  BAPI.config().availcalendarmonths=9;',
				'label'	=> 'Show 9 Months'
			),
			array(
				'value'	=> 'BAPI.config().displayavailcalendar=true;  BAPI.config().availcalendarmonths=12;',
				'label'	=> 'Show 12 Months'
			),
			array(
				'value'	=> 'BAPI.config().displayavailcalendar=true;  BAPI.config().availcalendarmonths=15;',
				'label'	=> 'Show 15 Months'
			),
			array(
				'value'	=> 'BAPI.config().displayavailcalendar=true;  BAPI.config().availcalendarmonths=18;',
				'label'	=> 'Show 18 Months'
			)
		); ?>
		<input name="settingsRaw[propdetail-availcal]" type="hidden" value="<?php echo $settings['propdetail-availcal']; ?>" data-sync="propdetail-availcal" />
		<select name="propdetail-availcal" id="propdetail-availcal">
			<?php foreach($avails as $avail) {
				echo sprintf('<option value="%s" %s>%s</option>', $avail['value'], ($settings['propdetail-availcal'] == filter_var($avail['value'], FILTER_SANITIZE_NUMBER_INT) ? 'selected="true"' : ''), $avail['label']);
			} ?>
	    </select>
	</td>	
</tr>
<tr valign="top">
	<td scope="row">Hide Rates Table:</td>
	<td>
		<input name="settingsRaw[propdetailratestable]" class="propdetailratestable-cbx" type="checkbox" checked="" />
		<input type="hidden" id="propdetailratestable" name="propdetailratestable" data-prevalue="BAPI.config().hideratestable=" value="" />
	</td>
</tr>
<tr valign="top" style="display:none">
	<td scope="row">Show Split Days in Availability Calendars:</td>
	<td><input id="" type="checkbox" name=""></td>
</tr>
<tr valign="top">
	<td scope="row">Display Property Review Tab:</td>
	<td>
		<input name="settingsRaw[propdetail-reviewtab]" class="propdetail-reviewtab-cbx" type="checkbox" checked="" />
		<input type="hidden" id="propdetail-reviewtab" name="propdetail-reviewtab" data-prevalue="BAPI.config().hasreviews=" value="" />
	</td>
</tr>

</table>

<h3>Attractions Settings</h3>
<table class="form-table">
<tr valign="top">
	<td scope="row">Attraction Type Filter:</td>
	<td>
		<input name="settingsRaw[poitypefilter]" class="poitypefilter-cbx" type="checkbox" checked="" />
		<input type="hidden" id="poitypefilter" name="poitypefilter" data-prevalue="BAPI.config().haspoitypefilter={}; BAPI.config().haspoitypefilter.enabled=" value="" />
	</td>
</tr>
</table>

<?php submit_button(); ?>
</form>
</div>



<script type="text/javascript" src="<?= get_relative(plugins_url('/js/jquery.ibutton.min.js', __FILE__)) ?>" ></script>
<link type="text/css" href="<?= get_relative(plugins_url('/css/jquery.ibutton.min.css', __FILE__)) ?>" rel="stylesheet" media="all" />
<script type="text/javascript">
<?php
/* the sort options map:
			ByCategory = 0
            ByBedrooms = 1
            ByPriceLoHi = 2
            ByPriceHiLo = 3
            ByLocation = 4
            ByRandom = 5
            ByHeadline = 6
            ByImages = 7*/
            
	if (!empty($sitesettings)){
		echo 'var settings=' . stripslashes($sitesettings).';';
		/* new settings after the initial settings */
		if(strpos($sitesettings,'BAPI.config().haspoitypefilter') == false){
			echo 'settings.poitypefilter = "BAPI.config().haspoitypefilter={}; BAPI.config().haspoitypefilter.enabled=false;";';
		}
		if(strpos($sitesettings,'BAPI.config().hideratesandavailabilitytab') == false){
			//we need to add the new property settings since its totally new only 1 time
			$search = "}";
			$replace = ',"propdetailrateavailtab":"BAPI.config().hideratesandavailabilitytab=false;"}';
			$newSiteSettings = get_option('bapi_sitesettings');
			$pos = strrpos($newSiteSettings, $search);
			if($pos !== false){$newSiteSettings = substr_replace($newSiteSettings, $replace, $pos, strlen($search));}
			update_option('bapi_sitesettings', $newSiteSettings);
			echo 'settings.propdetailrateavailtab = "BAPI.config().hideratesandavailabilitytab=false;";';
		}
		if(strpos($sitesettings,'BAPI.config().hideratestable') == false){
			//we need to add the new property settings since its totally new only 1 time
			$search = "}";
			$replace = ',"propdetailratestable":"BAPI.config().hideratestable=false;"}';
			$newSiteSettings = get_option('bapi_sitesettings');
			$pos = strrpos($newSiteSettings, $search);
			if($pos !== false){$newSiteSettings = substr_replace($newSiteSettings, $replace, $pos, strlen($search));}
			update_option('bapi_sitesettings', $newSiteSettings);
			echo 'settings.propdetailratestable = "BAPI.config().hideratestable=false;";';
		}
		if(strpos($sitesettings,'BAPI.config().amenity.enabled') == false){
			echo 'settings.amenitysearch = "BAPI.config().amenity.enabled=false;";';
		}
		if(strpos($sitesettings,'BAPI.config().sleeps.enabled') == false){
			echo 'settings.sleepsearch = "BAPI.config().sleeps.enabled=false;";';
		}
		if(strpos($sitesettings,'BAPI.config().searchmodes.pagination') == false){
			//we need to add the new property settings since its totally new only 1 time
			$search = "}";
			$replace = ',"searchmode-pagination":"BAPI.config().searchmodes.pagination=false;"}';
			$newSiteSettings = get_option('bapi_sitesettings');
			$pos = strrpos($newSiteSettings, $search);
			if($pos !== false){$newSiteSettings = substr_replace($newSiteSettings, $replace, $pos, strlen($search));}
			update_option('bapi_sitesettings', $newSiteSettings);
			echo 'settings.searchmode-pagination = "BAPI.config().searchmodes.pagination=false;";';
		}
		if(strpos($sitesettings,'BAPI.config().pagesize') == false){
			//we need to add the new property settings since its totally new only 1 time
			$search = "}";
			$replace = ',"numberproppage": "BAPI.config().pagesize=10;"}';
			$newSiteSettings = get_option('bapi_sitesettings');
			$pos = strrpos($newSiteSettings, $search);
			if($pos !== false){$newSiteSettings = substr_replace($newSiteSettings, $replace, $pos, strlen($search));}
			update_option('bapi_sitesettings', $newSiteSettings);
			echo 'settings.numberproppage = "BAPI.config().pagesize=10;";';
		}
	} else {
	/* this is the data from the app, this is in the database, the bizrules */
	$bapiSolutionData = BAPISync::getSolutionData();
	$bapiSolutionDataConfig = $bapiSolutionData["ConfigObj"];
	$maxratesearch = ($bapiSolutionDataConfig["rate"]["enabled"]) ? 'true' : 'false';
	$amenitysearch = ($bapiSolutionDataConfig["amenity"]["enabled"]) ? 'true' : 'false';
	$devsearch = ($bapiSolutionDataConfig["dev"]["enabled"]) ? 'true' : 'false';
	$adultsearch = ($bapiSolutionDataConfig["adults"]["enabled"]) ? 'true' : 'false';
	$childsearch = ($bapiSolutionDataConfig["children"]["enabled"]) ? 'true' : 'false';
	$headlinesearch = ($bapiSolutionDataConfig["headline"]["enabled"]) ? 'true' : 'false';
	$propdetailavailcal = ($bapiSolutionDataConfig["displayavailcalendar"]) ? 'true' : 'false';
	$availcalendarmonths = $bapiSolutionDataConfig["availcalendarmonths"];
	$propdetailreviewtab = ($bapiSolutionDataConfig["hasreviews"]) ? 'true' : 'false';
	$propdetailrateavailtab = ($bapiSolutionDataConfig["hideratesandavailabilitytab"]) ? 'true' : 'false';
	$propdetailratestable = ($bapiSolutionDataConfig["hideratestable"]) ? 'true' : 'false';
	$poitypefilter = ($bapiSolutionDataConfig["haspoitypefilter"]) ? 'true' : 'false';
	$checkin = ($bapiSolutionDataConfig["checkin"]["enabled"]) ? 'true' : 'false';
	$checkout = ($bapiSolutionDataConfig["checkout"]["enabled"]) ? 'true' : 'false';
	$los = ($bapiSolutionDataConfig["los"]["enabled"]) ? 'true' : 'false';
	$losdefaultval = $bapiSolutionDataConfig["los"]["defaultval"];
	$losminval = $bapiSolutionDataConfig["los"]["minval"];
	$categorysearch = ($bapiSolutionDataConfig["category"]["enabled"]) ? 'true' : 'false';
	$sleepexactlysearch = ($bapiSolutionDataConfig["sleeps"]["enabled"]) ? 'true' : 'false';
	$bedexactlysearch = ($bapiSolutionDataConfig["beds"]["enabled"]) ? 'true' : 'false';
	$maxbedsearch = $bapiSolutionDataConfig["beds"]["maxval"];
	$roomsearch = ($bapiSolutionDataConfig["rooms"]["enabled"]) ? 'true' : 'false';
	$city = ($bapiSolutionDataConfig["city"]["enabled"]) ? 'true' : 'false';
	$location = ($bapiSolutionDataConfig["location"]["enabled"]) ? 'true' : 'false';
	$averagestarsreviews = ($bapiSolutionDataConfig["hidestarsreviews"]) ? 'true' : 'false';
	$showunavailunits = ($bapiSolutionData["BizRules"]["Search By Availability"]) ? 'true' : 'false';
	$searchsort = $bapiSolutionData["BizRules"]["Search Sort Order Option"];
	
		echo '
		var locsearch = "BAPI.config().city.enabled=false; BAPI.config().location.enabled=false;";
		if('.$city.' && '.$location.'==false )
		{
			locsearch = "BAPI.config().city.enabled=true; BAPI.config().location.enabled=false; BAPI.config().city.autocomplete=false;";
		}
		if('.$city.'==false && '.$location.')
		{
			locsearch = "BAPI.config().city.enabled=false; BAPI.config().location.enabled=true; BAPI.config().location.autocomplete=false;";
		}
		var thesearchsort = "'.$searchsort.'";
		
		if(thesearchsort==0){
			thesearchsort = "BAPI.config().sort=\'category\';"
		}else{
			if(thesearchsort==1){
				thesearchsort = "BAPI.config().sort=\'beds\';"
			}else{
				if(thesearchsort==2){
					thesearchsort = "BAPI.config().sort=\'minrate\';"
				}else{
					if(thesearchsort==3){
						thesearchsort = "BAPI.config().sort=\'maxrate\';"
					}else{
						if(thesearchsort==4){
							thesearchsort = "BAPI.config().sort=\'location\';"
						}else{
							if(thesearchsort==5){
								thesearchsort = "BAPI.config().sort=\'random\';"
							}else{
								if(thesearchsort==6){
									thesearchsort = "BAPI.config().sort=\'headline\';"
								}else{
									thesearchsort = "BAPI.config().sort=\'random\';"
								}
							}
						}
					}
				}
			}
		}
		
		
		var settings={
			"maxratesearch": "BAPI.config().rate.enabled='.$maxratesearch.';",
			"defaultsearchresultview": "BAPI.config().defaultsearchresultview=\'tmpl-propertysearch-listview\';",
			"searchmode-listview": "BAPI.config().searchmodes.listview=true;",
			"searchmode-photoview": "BAPI.config().searchmodes.photoview=true;",
			"searchmode-widephotoview": "BAPI.config().searchmodes.widephotoview=false;",
			"searchmode-pagination": "BAPI.config().searchmodes.pagination=false;",
			"searchmode-hotelview": "BAPI.config().searchmodes.hotelview=false;",
			"searchmode-mapview": "BAPI.config().searchmodes.mapview=true;",
			"numberproppage": "BAPI.config().pagesize=10;",
			"amenitysearch": "BAPI.config().amenity.enabled=false;",
			"averagestarsreviews": "BAPI.config().hidestarsreviews=false;",
			"devsearch": "BAPI.config().dev.enabled='.$devsearch.';",
			"adultsearch": "BAPI.config().adults.enabled='.$adultsearch.';",
			"childsearch": "BAPI.config().children.enabled='.$childsearch.';",
			"headlinesearch": "BAPI.config().headline.enabled='.$headlinesearch.';",
			"locsearch": locsearch,
			"showunavailunits": "BAPI.config().restrictavail='.$showunavailunits.';",
			"searchsort": thesearchsort,
			"searchsortorder": "BAPI.config().sortdesc=false;",
			"propdetail-availcal": "BAPI.config().displayavailcalendar='.$propdetailavailcal.';  BAPI.config().availcalendarmonths='.$availcalendarmonths.';",
			"propdetail-reviewtab": "BAPI.config().hasreviews='.$propdetailreviewtab.';",
			"propdetailrateavailtab": "BAPI.config().hideratesandavailabilitytab=false;",
			"propdetailratestable": "BAPI.config().hideratestable=false;",
			"poitypefilter": "BAPI.config().haspoitypefilter={}; BAPI.config().haspoitypefilter.enabled='.$poitypefilter.';",
			"checkinoutmode": "BAPI.config().checkin.enabled='.$checkin.'; BAPI.config().checkout.enabled='.$checkout.'; BAPI.config().los.enabled='.$los.';",
			"deflos": "BAPI.config().los.defaultval='.$losdefaultval.'; BAPI.config().los.minval='.$losminval.';",
			"categorysearch": "BAPI.config().category.enabled='.$categorysearch.';",
			"minsleepsearch": "BAPI.config().minsleeps={}; BAPI.config().minsleeps.enabled=false;",
			"sleepsearch": "BAPI.config().sleeps.enabled='.$sleepexactlysearch.';",
			"minbedsearch": "BAPI.config().minbeds={}; BAPI.config().minbeds.enabled=false;",
			"maxbedsearch": "BAPI.config().beds.values=BAPI.config().beds.values.splice(0,'.$maxbedsearch.');",
			"bedsearch": "BAPI.config().beds.enabled='.$bedexactlysearch.';",
			"roomsearch" : "BAPI.config().rooms.enabled='.$roomsearch.';"
		};';
	}	
?>

jQuery(document).ready(function ($) {

	//Keep data in sync
	$('[data-sync]').each(function() {
		var that = $(this),
			name = $(this).data('sync'),
			filter = $(this).data('filter');
		var elem = $('[name='+name+']');

		that.val( elem.find('option:selected').text() );

		elem.on('change', function() {
			that.val( $(this).find('option:selected').text() );
		});
	});


	/* we are not showing this yet */
	// settings["roomsearch"] = "BAPI.config().rooms.enabled=false;";

	// for(var i = 0; i <= +BAPI.config().beds.maxval; i++) {
	// 	$('#maxbedsearch').append(
	// 		'<option value="BAPI.config().beds.values=BAPI.config().beds.values.splice(0,' + (i+1) +
	// 					 ');BAPI.config().beds.minvalues=BAPI.config().beds.minvalues.splice(0,' + (i+1) + ');">' + i + '</option>'
	// 	);
	// }

/* there can be only 1 bedroom setting */
$('.bedsearch-cbx').change(function(){
	if($('.bedsearch-cbx').is(":checked")){$('.minbedsearch-cbx').iButton("toggle", false);}
});
$('.minbedsearch-cbx').change(function(){
	if($('.minbedsearch-cbx').is(":checked")){$('.bedsearch-cbx').iButton("toggle", false);}
});

/* lets populate the dropdown */
	// $.each(BAPI.config().los.values,function(key,value) {
 //        $("#deflos").append('<option value="BAPI.config().los.defaultval='+value.Data+'; BAPI.config().los.minval='+value.Data+';">' + value.Label  + '</option>');
	// });

	/* populating the dropdown and selecting the option that was set */
	function populatedefaultviewddp(showListview,showPhotoView,showMapView){
		var thedefaultsearchresultviewOptions = '';
		if(showListview){
			thedefaultsearchresultviewOptions = thedefaultsearchresultviewOptions + '<option value="BAPI.config().defaultsearchresultview=\'tmpl-propertysearch-listview\';">List View</option>';
		}
		if(showPhotoView){
			thedefaultsearchresultviewOptions = thedefaultsearchresultviewOptions + '<option value="BAPI.config().defaultsearchresultview=\'tmpl-propertysearch-galleryview\';">Photo View</option>';
		}
		if(showMapView){
			thedefaultsearchresultviewOptions = thedefaultsearchresultviewOptions + '<option value="BAPI.config().defaultsearchresultview=\'tmpl-propertysearch-mapview\';">Map View</option>';
		}
		if(thedefaultsearchresultviewOptions != ''){
			$('#defaultsearchresultview').html(thedefaultsearchresultviewOptions);
		}else{
			$('#defaultsearchresultview').html('<option value="">Disabled</option>');
		}
		$('#defaultsearchresultview').val(settings['defaultsearchresultview']);
	}
	populatedefaultviewddp(settings['searchmode-listview'] == 'BAPI.config().searchmodes.listview=true;',settings['searchmode-photoview'] == 'BAPI.config().searchmodes.photoview=true;',settings['searchmode-mapview'] == 'BAPI.config().searchmodes.mapview=true;');
	/* calling the function on change so the dropdown is updated */
	$('.searchmode-listview-cbx,.searchmode-photoview-cbx,.searchmode-mapview-cbx').change(function(){
		populatedefaultviewddp($('.searchmode-listview-cbx').is(":checked"),$('.searchmode-photoview-cbx').is(":checked"),$('.searchmode-mapview-cbx').is(":checked"));
	});

	function setHideRatesAndAvailTab(){
		if($('.propdetailratestable-cbx').is(":checked") && $('#propdetail-availcal').val() == "BAPI.config().displayavailcalendar=false;  BAPI.config().availcalendarmonths=0;"){
			$('.propdetailrateavailtab-cbx').iButton("toggle", true);
		}else{
			$('.propdetailrateavailtab-cbx').iButton("toggle", false);
		}
	}
	
	
	jQuery(window).load(function (){
		
		$('#propdetail-availcal,.propdetailratestable-cbx').change(function(){
			setHideRatesAndAvailTab();
		});
		$('.propdetailrateavailtab-cbx').change(function(){
			if($('.propdetailrateavailtab-cbx').is(":checked")){
				alert("This Setting requires data synchronization, there could be a delay of up to one hour for the changes to appear on all property detail pages.");
			}
		});
		
		
	});
	
	/*$.each(BAPI.config().beds.values,function(key,value) {
        $("#maxbedsearch").append('<option value="BAPI.config().beds.values=BAPI.config().beds.values.splice(0,'+ value.Data +');">' + value.Label  + '</option>');
	});*/
	/* make all checkboxes iphone style */
	jQuery(":checkbox").iButton();
	// update the settings
	jQuery.each(settings, function( key, value ) {
		//console.log(key + '=' + value);
		var theKey = '.'+key+'-cbx';
		//console.log(theKey);
		if (key.indexOf('$')<0) {
			/* we check if the value is valid */
			if (typeof (value) !== "undefined" && value != ''){
				
				/* check if this is a checkbox */
				if(jQuery(theKey).is(':checkbox'))
				{
					jQuery(theKey).change(function(){
					cb = jQuery(this);
					jQuery('#'+key).val(jQuery('#'+key).attr('data-prevalue') + cb.prop('checked') + ";");
					});
					
					var arr = value.split('=');
					var whereIsBool = 1;
					/*settings that create an object first*/
					if(theKey == '.minsleepsearch-cbx' || theKey == '.minbedsearch-cbx' || theKey == '.poitypefilter-cbx')
					{
						whereIsBool = 2;
					}
					var arrBolean = arr[whereIsBool].slice(0,-1);
					if( arrBolean == 'true')
					{
						//console.log("its true");
						jQuery(theKey).prop('checked',true );
						jQuery(theKey).iButton("toggle", true)
					}else{
						jQuery(theKey).prop('checked', false);
						jQuery(theKey).iButton("toggle", false);
					}
				}
				/* this will still populate the hidden inputs */
				jQuery('#'+key).val(value);
			}else{
				if(theKey == '.propdetail-availcal-cbx')
				{
					jQuery('#'+key).val("BAPI.config().displayavailcalendar=true;  BAPI.config().availcalendarmonths=6;");
					
				}else{
					/* values is not valid by default set it to false */
					jQuery('#'+key).val(jQuery('#'+key).attr('data-prevalue') + "false;");
					jQuery(theKey).prop('checked',false );
					jQuery(theKey).iButton("toggle", false);
				}
				
				jQuery(theKey).change(function(){
					cb = jQuery(this);
					jQuery('#'+key).val(jQuery('#'+key).attr('data-prevalue') + cb.prop('checked') + ";");
				});
			}
			
		}
		
/* make all checkboxes iphone style */
  jQuery(":checkbox").iButton();

	});
	/* everything is in place show all */
	jQuery('.sitesettings-wrapper').show();
});

</script>
