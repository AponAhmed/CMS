<?php

//session_start();
//session_destroy();exit;
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(dirname(__FILE__)) . '/');
}
define("DOMAIN", "http://" . $_SERVER['SERVER_NAME']);
$adminDir = "admin";
$URI_Element = explode("/", $_SERVER['REQUEST_URI']);
if (in_array($adminDir, $URI_Element)) {
    define("ADMIN", true);
}


$head_str_arr = array();
$foot_str_arr = array();
$custom_head = "";
$custom_footer = "";
$GLOBALS = array();
$GLOBALS['post'] = array();
//$ext = new ReflectionExtension('mysql');
//var_dump($ext->getVersion());	
//Config file included 
require_once( ABSPATH . 'config.php' );

error_reporting(0);

require_once( ABSPATH . 'include/common/phpmailer/phpmailerautoload.php');
//
//var_dump($_SESSION[SESS_KEY]);
require_once( ABSPATH . 'include/db_class.php' );
require_once( ABSPATH . 'include/schedule_class.php' );
$DB = new db();

require_once( ABSPATH . 'include/function.php' );
$SCHEDULE = new schedule();

require_once( ABSPATH . 'include/front_functions.php' );
require_once( ABSPATH . 'include/defination.php');


require_once( ABSPATH . 'include/helper.php' );
//Hooking

$home_page = get_option('front_page');


if (isset($_GET['clean'])) {
    $directory = UPLOAD . "cache/";
    $scanned_directory = array_diff(scandir($directory), array('..', '.'));
    //  var_dump($scanned_directory);
    foreach ($scanned_directory as $file) { // iterate files
        if (is_file($directory . $file))
            unlink($directory . $file); // delete file
    }
}

$POST = $GLOBALS['post'];
//var_dump($POST);
//        
if (empty($POST) && !defined('ADMIN') && !defined('LOGIN')) {
    //$front = get_slug($home_page);
    //header("location:$front");
    //$_GET['ID']=$home_page;
    $GLOBALS['post'] = get_post($home_page);
}
$POST = $GLOBALS['post'];


require_once( ABSPATH . "$adminDir/init.php" );

require_once( ABSPATH . "$adminDir/functions/admin-functions.php" );
require_once( ABSPATH . "$adminDir/functions/admin_custom.php" );
require_once( ABSPATH . "$adminDir/functions/part_function.php" );

require_once( ABSPATH . 'include/theme_class.php');
$THEME = new theme_class();
require_once( ABSPATH . "$adminDir/parts/terms_class.php" );
$TERM = new terms();
require_once( ABSPATH . "$adminDir/parts/menu_class.php" );
$MENU = new menu_class();

//Plugins---GAP

require_once( ABSPATH . 'include/plugins.php');
$plugins = new plugins();
$plugins->initPlugin();
//Theme Function GAPrequire_once( ABSPATH . "include/forntend_ini.php");
$themeFunctionsFile = THEME_DIR . current_theme_dir() . "/functions.php";
require_once( ABSPATH . "include/forntend_ini.php");

if (file_exists($themeFunctionsFile)) {
    include "$themeFunctionsFile";
}



global $POST;

//Custom post init
if (defined('ADMIN')) {
    //echo "custom post init";
    customPost_init();
}
require_once("admin-include.php");


//===============INIT=========================
if (isset($_GET['HBT'])) {
    if (!isset($_SESSION[SESS_KEY]['login'])) {
        echo json_encode(array("redrict" => "login.php"));
        exit;
    }
    //scheduler
    $SCHEDULE->init_schedule();
    //var_dump($SCHEDULE);
    echo time();
}

if (isset($_REQUEST['ajx_action'])) {
    $func = $_REQUEST['ajx_action'];
    var_dump($func);
    if (function_exists($func)) {
        $params = array();
        parse_str($_POST['data'], $params);
        $func($params);
    } else {
        echo "$func function Not found !";
    }
}