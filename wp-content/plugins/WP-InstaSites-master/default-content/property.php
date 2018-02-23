<?php

function property_detail_content($content){
    if (get_post_meta(get_the_ID(),'property_id',true)!='' && (get_post_meta(get_the_ID(),'bapi_last_update',true) < time()-(3600*60*24))){
		remove_filter('save_post','update_post_bapi');
		wp_update_post(mod_post_builder($propid,get_the_ID()));
		update_post_meta(get_the_ID(), 'bapi_last_update', time());
		wp_set_post_terms(get_the_ID(), wp_create_category(get_option('property_category_name')), 'category');
		add_filter('save_post','update_post_bapi');
	}
	bapi_search_page_body($content);
	return $content;
}

function property_post_list(){
	$propid_array = array();
	$args = array(
		'numberposts' => -1,
		'meta_key' => 'property_id');
	$posts_array = get_posts($args);
	if(count($posts_array)>0){
		foreach($posts_array as $p){
			//print_r($posts_array);exit();
			$propid = get_post_custom_values('property_id',$p->ID);
			if(!empty($propid)){
				$prop_array[$p->ID] = $propid;
			}
		}
	}
	?>
	<script>
		var prop_post_array = new Array();
	<?php
	foreach($prop_array as $po => $pr){
		?>
		prop_post_array[<?= $pr[0] ?>] = <?= $po ?>;
		<?php
	}
	?>
	</script>
    <?php
}

function getproperty(){
	?>
    <?php
	if(is_page($post)&&(get_post_meta(get_the_ID(),'property_id',true)!='')){
		//echo get_the_ID();
		//$apikey = '5309b4a8-6528-4d26-aa2f-ff96a161effc';
		$propid = get_post_meta(get_the_ID(),'property_id',true);
//		$url = "http://connect.bookt.com/ws/?method=get&apikey=".$apikey."&ids=".$propid."&entity=property&rates=1&avail=1&poi=1&seo=1&reviews=1";
//		$json = file_get_contents($url);
//		$data = json_decode($json, TRUE);
		//print_r($data);exit();
		//echo $data['result']['Headline'];exit();
		$apiKey = get_option('api_key');
		?>
		<script type="text/javascript">
			var propid = <?php echo $propid; ?>;
			var prop;
			
			$(document).ready(function () {
				var galleries = $('.ad-gallery').adGallery({
					loader_image: '//booktplatform.s3.amazonaws.com/App_SharedStyles/images/adGallery/loader.gif',
					height: 420, width: 620,
					update_window_hash:false,
					slideshow: {
						enable: true,
						autostart: false,
						speed: 4000,
						start_label: '',
						stop_label: '',
						stop_on_scroll: true, // Should the slideshow stop if the user scrolls the thumb list?
						countdown_prefix: '(', // Wrap around the countdown
						countdown_sufix: ')'
					}    
				});
			});
		</script>
		<?php
	}
	//bapi_search_page_head($content); 
}

function property_summary_content($content){
	if(is_page($post)&&(get_post_meta(get_the_ID(),'bapi_page_id',true)=='bapi_search')){
		?>
        <div id="results"></div>
        <div id="qs"></div>
        <script>
		$(document).ready(function () {
			var searchoptions = { "seo": true, "pagesize": 10 };
			searchoptions = $.extend({}, searchoptions, BAPI.session().searchparams);
			
			BAPI.UI.createSummaryWidget('#results',
				{
					"entity": BAPI.entities.property,
					"template": BAPI.templates.get('tmpl-propertysearch-listview'),
					"searchoptions": searchoptions
				});
	
			BAPI.UI.createSearchWidget('#qs', { "template": BAPI.templates.get('tmpl-search-homepage') }, function () {
				searchoptions = $.extend({}, searchoptions, BAPI.session().searchparams);
				BAPI.UI.createSummaryWidget('#results',
				{
					"entity": BAPI.entities.property,
					"template": BAPI.templates.get('tmpl-propertysearch-listview'),
					"searchoptions": searchoptions
				});
	
			});
		});
        </script>
        <?php
	}
	if(is_page($post)&&(get_post_meta(get_the_ID(),'bapi_page_id',true)=='bapi_property_grid')){
		?>
        <div id="results"></div>
        <script>
		$(document).ready(function () {
			BAPI.UI.createSummaryWidget('#results',
            {
                "entity": BAPI.entities.property,
                "template": BAPI.templates.get('tmpl-allproperties'),
				"log": true
            });
		});
        </script>
        <?php
	}
	if(is_page($post)&&(get_post_meta(get_the_ID(),'bapi_page_id',true)=='bapi_developments')){
		?>
        <div id="results"></div>
        <script>
		$(document).ready(function () {
			BAPI.UI.createSummaryWidget('#results',
            {
                "entity": BAPI.entities.development,
                "template": BAPI.templates.get('tmpl-developments-summary-list'),
				"log": true
            });
		});
        </script>
        <?php
	}
	if(is_page($post)&&(get_post_meta(get_the_ID(),'bapi_page_id',true)=='bapi_property_finders')){
		?>
        <div id="results"></div>
        <script>
		$(document).ready(function () {
			BAPI.UI.createSummaryWidget('#results',
            {
                "entity": BAPI.entities.searches,
                "template": BAPI.templates.get('tmpl-searches-summary'),
				"log": true
            });
		});
        </script>
        <?php
	}
	if(is_page($post)&&(get_post_meta(get_the_ID(),'bapi_page_id',true)=='bapi_attractions')){
		?>
        <div id="results"></div>
        <script>
		$(document).ready(function () {
			BAPI.UI.createSummaryWidget('#results',
            {
                "entity": BAPI.entities.poi,
                "template": BAPI.templates.get('tmpl-attractions-summary-list'),
				"log": true
            });
		});
        </script>
        <?php
	}
	if(is_page($post)&&(get_post_meta(get_the_ID(),'bapi_page_id',true)=='bapi_specials')){
		?>
        <div id="results"></div>
        <script>
		$(document).ready(function () {
			BAPI.UI.createSummaryWidget('#results',
            {
                "entity": BAPI.entities.specials,
                "template": BAPI.templates.get('tmpl-specials-summary'),
				"log": true
            });
		});
        </script>
        <?php
	}
	if(is_page($post)&&(get_post_meta(get_the_ID(),'bapi_page_id',true)=='bapi_debug')){
		$apiKey = get_option('api_key');
		$f = file_get_contents('https://connect.bookt.com/ws/?apikey='.$apiKey.'&method=showclientinfo');
		$j = json_decode($f);
		?>
        	<div id="debug-info">
            <?= $f; ?>
            <br/>
            <?php print_r($j); ?>
            </div>
        <?php
	}
	return $content;
}

?>