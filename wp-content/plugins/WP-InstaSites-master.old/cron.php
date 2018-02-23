<?php
//CRON.php is to be used for scheduled tasks such as entity syncing and sitemap crawling
//wp_schedule_single_event(time()+3600,'bapi_sitemap_crawler_cron_event');
add_action('bapi_sitemap_crawler_cron_event','bapi_sitemap_crawler_cron');
function bapi_sitemap_crawler_cron() {
	if (extension_loaded('newrelic')) {
		newrelic_ignore_transaction();
	}
	$urls = array();  
	$sitemap = get_site_url().'/sitemap_crawler.svc';
	$xml= wp_remote_get($sitemap,array('timeout'=>300));
}

wp_clear_scheduled_hook('bapi_sitemap_crawler_cron_event');