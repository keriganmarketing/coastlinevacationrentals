<?php
namespace Kigo\Themes\instaparent\Widgets\SpecialOffersWidget;
use Kigo\Themes\instaparent\XBAPI;
use Kigo\Themes\instaparent\WidgetBase;

class SpecialOffersWidget extends WidgetBase{
    /**
      * return void
      */
     function __construct(){

         $args = [
             'base_id'      => 'kigo_sortable_special_offers',
             'visible_name' => 'Kigo Sortable Specials',
             'description'  => 'Use this to select and sort your Specials that you setup in the Kigo App',
             'textdomain'   => __TEXTDOMAIN__,
         ];

         parent::__construct($args);
     }

     /**
     *
     * @param array $args widgets inherited vars
     * @param array  $instance widget fields array
     */
    function widget($args, $ins) {
        extract($args);

        echo $before_widget;

        $reflect = new \ReflectionClass($this);
        if (file_exists(rtrim($reflect->getFileName(), '.php') . '-template.php'))
            include rtrim($reflect->getFileName(), '.php') . '-template.php';
        echo $after_widget;

    }

    function getSpecialOffers(){
        $xbapi = new XBAPI();
         $spoffers = $xbapi->getSpecialOffers();
         $spoffersArr = [];

         foreach($spoffers as $so){
             $spoffersArr[$so['ID']] = $so['Name'];
         }

         return $spoffersArr;
    }
}

function register_SpecialOffersWidget(){
    register_widget( 'Kigo\Themes\instaparent\Widgets\SpecialOffersWidget\SpecialOffersWidget' );
}
add_action('widgets_init','Kigo\Themes\instaparent\Widgets\SpecialOffersWidget\register_SpecialOffersWidget');
