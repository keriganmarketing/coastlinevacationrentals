<?php
/**
 *
 *
 * ATTENTION: When creating widgets Please Use NAMESPACES.
 */
//define('CONCATENATE_SCRIPTS', false);
namespace Kigo\Themes\instaparent;
/**
 * This function prints the contents of the $f var
 * When $d is true it exits the program.
 * @param mixed $f
 * @param bool $d
 */
function dd($f, $d = false) {
    echo "<pre>";
    var_dump($f);
    echo "</pre>";
    if ($d)
        exit;
}



// DEV..........................................................................
define('__DEV__', false);

//Theme dir
define('__PARENT_DIR__', get_template_directory());

//Theme dir url
define('__THEMEDIRURL__', get_stylesheet_directory_uri());

//IS INSTACHILD
define('__IS_CHILD__', false);

if (__IS_CHILD__) {
    define('__PARENT_DIR_URL__', get_template_directory_uri());
}

//TEXTDOMAIN
define('__TEXTDOMAIN__', 'instaparent');


/**
 * returns the relative url of thepassed dirpath
 * @param string $dirnameFullPath is the absolute path to the folder in the server.
 * @return string
 */
function getDirUrl($dirnameFullPath){
    $urlpath = '/wp-content' . '/themes/' . basename(__PARENT_DIR__) . explode(__PARENT_DIR__, $dirnameFullPath)[1];
    return $urlpath;
}



//....Bapi extension.......
// MUST BE LOADED at 'plugins_loaded' hook or this will generate errors and fail to load BAPI/XBAPI EVERY TIME.  Please stop over-writing me.
if (class_exists('BAPI')) {
    require_once('XBAPI.php');

    //Widgets base.............
    require_once('WidgetBase.php');
    //Property finders widget.......
    require_once ('SelectablePropertyFindersWidget/init.php');
    //Special Offers widget.......
    require_once ('SelectableSpecialOffersWidget/init.php');
}

    
