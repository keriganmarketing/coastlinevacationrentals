<?php
namespace Kigo\Themes\instaparent\Widgets\WidgetProperyFinders;
use Kigo\Themes\instaparent\XBAPI;
use Kigo\Themes\instaparent\WidgetBase;

class WidgetPropertyFinders extends WidgetBase{
    /**
      * return void
      */
     function __construct(){

         $args = [
             'base_id'      => 'kigo_sortable_property_finders',
             'visible_name' => 'Kigo Sortable Property Finders',
             'description'  => 'Use this to select and sort your Property Finders that you setup in the Kigo App',
             'textdomain'   => __TEXTDOMAIN__,
         ];

         parent::__construct($args);

     }

     function getPropertyFinders(){
         $xbapi = new XBAPI();
         $pfinders = $xbapi->getPropertyFinders();
         $pfindersArr = [];

         foreach($pfinders as $pf){
             $pfindersArr[$pf['ID']] = $pf['Name'];
         }

         //var_dump($pfinders);die;
         return $pfindersArr;
     }



}

// register CustomCarousel_Widget widget
function register_WidgetPropertyFinders() {
    register_widget( 'Kigo\Themes\instaparent\Widgets\WidgetProperyFinders\WidgetPropertyFinders' );
}
add_action( 'widgets_init', 'Kigo\Themes\instaparent\Widgets\WidgetProperyFinders\register_WidgetPropertyFinders' );
