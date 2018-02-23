<?php
namespace Kigo\Themes\instaparent\Widgets\WidgetSelectablePropertyFinders;
include 'WidgetPropertyFinders.php';

/* include widget style */
add_action('wp_enqueue_scripts', 'Kigo\Themes\instaparent\Widgets\WidgetSelectablePropertyFinders\addStyles');

function addStyles(){

    wp_enqueue_style('kigo-WidgetSelectablePropertyFinders-style', get_template_directory_uri() . '/widgets/SelectablePropertyFindersWidget/css/main.min.css', array(), '1.0.0');
}


/* include admin widget style */
add_action('admin_enqueue_scripts', 'Kigo\Themes\instaparent\Widgets\WidgetSelectablePropertyFinders\addAdminStyles');

function addAdminStyles(){

    wp_enqueue_style('kigo-WidgetSelectablePropertyFindersAdmin-style', get_template_directory_uri() . '/widgets/SelectablePropertyFindersWidget/css/admin.css', array(), '1.0.0');
}
