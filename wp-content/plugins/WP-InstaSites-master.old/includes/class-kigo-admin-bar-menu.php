<?php
/**
 * Class Kigo_App_Admin_Bar_Menu
 * Render the Kigo toolbar linking to the App features.
 * 
 * * * ONLY FOR NEW APP CUSTOMERS * * * 
 * 
 */
class Kigo_App_Admin_Bar_Menu {
	
	const APP_URL			= 'https://newapp.kigo.net/';
	
	const PARENT_NODE_ID	= 'kigo_app_parent';
	const PARENT_NODE_TITLE	= 'Kigo app';
	const PARENT_NODE_ICON	= 'img/icon_kigo.ico';
	
	private static $kigo_app_nodes = array(
		'kigo_app_properties'			=>	array(
												'title'	=> 'Manage properties',
												'href'	=> 'marketing/properties/',
												'icon'	=> 'dashicons-admin-home'
											),
		'kigo_app_property_finder'		=>	array(
												'title'	=> 'Set up property finders',
												'href'	=> 'marketing/propertyfinders/',
												'icon'	=> 'dashicons-search'
											),
		'kigo_app_attractions'			=>	array(
												'title'	=> 'Set up attractions',
												'href'	=> 'marketing/attractions/',
												'icon'	=> 'dashicons-location-alt'
											),
		'kigo_app_specials'				=>	array(
												'title'	=> 'Add specials',
												'href'	=> 'booking/mgr/setup/specials/',
												'icon'	=> 'dashicons-awards'
											),
		'kigo_app_optional_services'	=>	array(
												'title'	=> 'Optional services',
												'href'	=> 'marketing/optionalservices/',
												'icon'	=> 'dashicons-plus'
											)
	);
	
	public static function register_admin_bar_node() {
		// Only new app clients have this extra toolbar to link to the app
		if( !is_newapp_website() ) {
			return;
		}
		
		global $wp_admin_bar;
	
		// Create parent node
		$wp_admin_bar->add_node(
			array(
				'id'		=> self::PARENT_NODE_ID,
				'title'		=> '<span class="ab-icon ab-icon-kigo"><img src="' . get_kigo_plugin_url( self::PARENT_NODE_ICON ) . '"></span><span>' . self::PARENT_NODE_TITLE . '</span>',
				'parent'	=> false,
				'href'		=> self::APP_URL,
				'meta'		=> array( 'target' => '_blank' ),
			) 
		);
		
		// Create every child nodes
		foreach ( self::$kigo_app_nodes as $id => $kigo_app_node ) {
			$wp_admin_bar->add_node(
				array(
					'id'		=> $id,
					'title'		=> '<span class="dashicons-before dashicons-kigo ' . $kigo_app_node['icon'] . '"></span><span>' . $kigo_app_node['title'] . '</span>',
					'parent'	=> self::PARENT_NODE_ID,
					'href'		=> self::APP_URL . $kigo_app_node['href'],
					'meta'		=> array( 'target' => '_blank' ),
				)
			);
		}
	}
}
