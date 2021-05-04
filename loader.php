<?php

//var_dump($_SERVER);exit;
session_start();
//session_destroy();exit;
define('CACHE_PATH', dirname(__FILE__) . "/content/cache/");

function rutime($ru, $rus, $index) {
    return ($ru["ru_$index.tv_sec"] * 1000 + intval($ru["ru_$index.tv_usec"] / 1000)) - ($rus["ru_$index.tv_sec"] * 1000 + intval($rus["ru_$index.tv_usec"] / 1000));
}

define('CDEBUG', false);

//var_dump(CACHE_PATH);
function cacheFile($echo = true) {
    $file = md5(trim($_SERVER['REQUEST_URI']));
    $filename = CACHE_PATH . "$file.html";
    $filename = preg_replace('/([^:])(\/{2,})/', '$1/', $filename);
    if (!defined('ADMIN')) {
        if (file_exists($filename)) {
            echo file_get_contents($filename);
            return true;
        }
    }
    return false;
}

if (function_exists('cacheFile')) {
    if (!isset($_SESSION['login'])) {
        if (cacheFile()) {
            if (CDEBUG) {
                $ru = getrusage();
                echo "<div style='position: fixed;bottom: 0;font-size: 10px;color: #ddd;text-align: center;width: 100%;'>";
                echo "Loaded From Cache, This process used " . rutime($ru, $rustart, "utime") .
                " ms for its computations and \n";
                echo "It spent " . rutime($ru, $rustart, "stime") .
                " ms in system calls\n";
                echo "</div>";
            }
            exit;
        }
    }
}


if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}
define("DOMAIN", "http://" . $_SERVER['SERVER_NAME']);
$adminDir = "admin";
$URI_Element = explode("/", $_SERVER['REQUEST_URI']);
if (in_array($adminDir, $URI_Element)) {
    define("ADMIN", true);
}
//PreLoad Script
// $preloadScriptDir = ABSPATH . 'include/reg_preload';
// if (file_exists($preloadScriptDir)) {
// $Pre_scriptFiles = array_diff(scandir($preloadScriptDir), array('..', '.'));
// if (!empty($Pre_scriptFiles)) {
// foreach ($Pre_scriptFiles as $scriptFile) {
// $scriptFileInfo = pathinfo($scriptFile);
// if ($scriptFileInfo['extension'] == "php") {
// include "$preloadScriptDir/$scriptFile";
// } else {
// unlink("$preloadScriptDir/$scriptFile");
// }
// }
// }
// }
//PreLoad Script
$modes = array();
$head_str_arr = array();
$foot_str_arr = array();
$custom_head = "";
$custom_footer = "";
$GLOBALS = array();
$GLOBALS['blog'] = false;
$GLOBALS['post'] = array();
//$ext = new ReflectionExtension('mysql');
//var_dump($ext->getVersion());	
if (file_exists(ABSPATH . 'config.php')) {
    //Config file included 
    require_once( ABSPATH . 'config.php' );
    if (DEBUG) {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
    } else {
        error_reporting(0);
    }
    require_once( ABSPATH . 'include/common/phpmailer/phpmailerautoload.php');
    //
    //var_dump($_SESSION[SESS_KEY]);
    require_once( ABSPATH . 'include/db_class.php' );
    require_once( ABSPATH . 'include/schedule_class.php' );
    $DB = new db();

    require_once ABSPATH . "$adminDir/classes/error_log.php";
    require_once( ABSPATH . 'include/function.php' );
    $SCHEDULE = new schedule();
    if (isset($_GET['down'])) {
        $fPath = base64_decode($_GET['down']);
        download($fPath);
    }

    if (!webApp() || true) {
        require_once( ABSPATH . 'include/front_functions.php' );
    }
    require_once( ABSPATH . 'include/defination.php');
    require_once( ABSPATH . 'include/helper.php' );

    require_once( ABSPATH . "$adminDir/init.php" );
    require_once( ABSPATH . "$adminDir/parts/terms_class.php" );
    $TERM = new terms();

    require_once( ABSPATH . $adminDir . '/classes/FontManager.php' );
    if (!webApp() || true) {
        require_once( ABSPATH . 'include/re_write.php' );
        $RWR = new re_write();
        $QV = $RWR->match();
        $home_page = get_home();
        $notFound = false;
    }

    //var_dump($GLOBALS['post']);
    //var_dump($RWR->roles);
    //echo "<pre>";
    //var_dump($QV); //exit;
    //$RWR->req_post();//Work Later about it

    if (!defined("ADMIN") && !empty($QV['page'])) {
        $pid = slug2id(@$QV['page']);
        //var_dump($pid,get_home());
        if ($pid == get_home()) {
            header("HTTP/1.0 301");
            header('location:/' . SUB_ROOT);
            //exit;
        }
    }

    if (!webApp()) {
        if (isset($QV['year']) || isset($QV['author'])) {
            //var_dump($QV);
            // red404();
            //header('Location: /' . SUB_ROOT);
            $redPid = get_option('red_not_found');
            if (!empty($redPid)) {
                $redUrl = get_link($redPid);

                // $domain = DOMAIN . "/";
                $domain = domain() . "/";
                $domain = trim(preg_replace('/([^:])(\/{2,})/', '$1/', $domain), "/");
                // var_dump($domain);
                // var_dump($redUrl);
                $redUrlr = str_replace($domain, "", $redUrl);
                $redUrlr = trim(preg_replace('/([^:])(\/{2,})/', '$1/', $redUrlr), "/");
                //  var_dump($redUrlr);
                // exit;
                header('Location: /' . SUB_ROOT . $redUrlr); //404
            } else {
                $notFound = true;
                $TITLE = site_tagline();
                $GLOBALS['template'] = '404.php';
            }
        }

        if (isset($_GET['ID']) || isset($_GET['id'])) {
            //For physical url
            $id = !isset($_GET['id']) ? $_GET['ID'] : $_GET['id'];
            $GLOBALS['post'] = get_post($id);
        } elseif (isset($QV['page']) && !empty($QV['page'])) {
            $enable_type_slug = @array_filter(unserialize(get_option('enable_type_slug')));
            $enable_type_slug = !empty($enable_type_slug) && is_array($enable_type_slug) ? $enable_type_slug : array('post', 'page');


            $Pid = slug2id($QV['page'], $enable_type_slug, true, true); //Change

            $PrID = false;
            if (isset($QV['parent'])) {
                $PrID = slug2id($QV['parent'], 'page', false, true);
            }

            $removeParentSlug = get_option('removeParentSlug');
            if ($removeParentSlug != 'false') {
                $PrID = false;
            }

            if ($PrID !== false && !empty($Pid)) {
                if ($Pid == $home_page) {
                    //header("location"); //Front page redirection
                    header('Location: /' . SUB_ROOT);
                } else {
                    $GLOBALS['post'] = get_post_qv($Pid, "*", $PrID);
                }
                if (empty($GLOBALS['post'])) {
                    red404();
                }
            } elseif (!empty($Pid) && $PrID === false) {

                if ($Pid == $home_page) {
                    //header("location"); //Front page redirection
                    //header('Location: /' . SUB_ROOT);
                } elseif ($PrID === false && !empty($QV['parent'])) {
                    //if parent url is wrong------------than Redirect to 404
                    red404();
                } else {
                    //var_dump($Pid); //exit;
                    $parentID = 0;
                    if ($removeParentSlug != 'false') {
                        $parentID = false;
                    }
                    if (!$RWR->has_term($Pid)) {
                        
                    }
                    $GLOBALS['post'] = get_post_qv($Pid, "*", $parentID);

                    //var_dump($GLOBALS);
                    //exit;
                    if (empty($GLOBALS['post'])) {
                        red404();
                    }
                }
            } else {
                //var_dump($Pid);
                //echo "-->";
                red404();
                //var_dump($TITLE);
            }
        }
    } else {
        if (isset($_GET['ID']) || isset($_GET['id'])) {
            //For physical url
            $id = !isset($_GET['id']) ? $_GET['ID'] : $_GET['id'];
            $GLOBALS['post'] = get_post($id);
        }
    }
    $get = $RWR->get();
    if (isset($get['q'])) {
        $GLOBALS['template'] = "search.php";
        $GLOBALS['title'] = "Search result for - $get[q]";
        $QV['search_string'] = $get['q'];
    }

    //var_dump($GLOBALS);
    $notFound = isset($GLOBALS['notFound']) ? $GLOBALS['notFound'] : '';
    $TITLE = isset($GLOBALS['title']) ? $GLOBALS['title'] : '';
    $template = isset($GLOBALS['template']) ? $GLOBALS['template'] : '';
    $POST = $GLOBALS['post'];

    //        
    if (empty($POST) && !defined('ADMIN') && !defined('LOGIN') && !$GLOBALS['blog']) {
        if (!$notFound) {
            $GLOBALS['post'] = get_post($home_page);
        }
    }


    $POST = $GLOBALS['post'];
    if (empty($TITLE)) {
        $TITLE = isset($POST['post_title']) ? $POST['post_title'] : "";
    }
    require_once( ABSPATH . "$adminDir/functions/admin-functions.php" );

    require_once( ABSPATH . "$adminDir/functions/part_function.php" );

    require_once( ABSPATH . 'include/theme_class.php');
    $THEME = new theme_class();

//    require_once( ABSPATH . "$adminDir/parts/terms_class.php" );
//    $TERM = new terms();

    require_once( ABSPATH . "$adminDir/functions/admin_custom.php" );

    if (!webApp()) {
        require_once( ABSPATH . "$adminDir/parts/menu_class.php" );
        $MENU = new menu_class();
    }

    //require_once 'parts/user.php';
    require_once("$adminDir/parts/user.php");
    require_once("$adminDir/parts/user-permission.php");
    //Plugins---GAP
    require_once( ABSPATH . 'include/plugins.php');
    $plugins = new plugins();
    $plugins->initPlugin();


    if (!empty($GLOBALS['post'])) {
        $postType = $GLOBALS['post']['post_type'];
        $callBack = "CS_QV_Filter_$postType";
        if (function_exists($callBack)) {
            $callBack();
            // var_dump($GLOBALS['post']['post_type']);
        }
    }

    if (!webApp()) {
        //Theme Function GAPrequire_once( ABSPATH . "include/forntend_ini.php");
        $themeFunctionsFile = THEME_DIR . current_theme_dir() . "/functions.php";
        require_once( ABSPATH . "include/forntend_ini.php");
        if (file_exists($themeFunctionsFile)) {
            include "$themeFunctionsFile";
        }


        //Built in plugins  
        $builtIn = ABSPATH . "$adminDir/built-in";
        if (file_exists($builtIn)) {
            $builtInscriptFiles = array_diff(scandir($builtIn), array('..', '.'));
            //var_dump($builtInscriptFiles);
            if (!empty($builtInscriptFiles)) {
                foreach ($builtInscriptFiles as $scriptFile) {
                    $scriptFileInfo = pathinfo($scriptFile);
                    if ($scriptFileInfo['extension'] == "php") {
                        require_once("$builtIn/$scriptFile");
                    }
                }
            }
        }
        //
        //Meta
        global $ROBOT, $initMetaFilter;
        $html = "";
        $html.="<title>" . $TITLE . "</title>\n";
        $html.="<meta name=\"description\" content=\"" . $DESCRIPTION . "\" />\n";
        if (get_option('meta_keyword') != 'false') {
            $html.="<meta name=\"keywords\" content=\"" . $KEYWORD . "\" />\n";
        }
        //var_dump($initMetaFilter);
        foreach ($initMetaFilter as $metaFilter) {
            if (function_exists($metaFilter)) {
                $html = $metaFilter($html);
            }
        }
        add_header($html, 0);

        if (!empty($customCss)) {
            $css = "";
            krsort($customCss);
            foreach ($customCss as $singleCss) {
                $css.=$singleCss;
            }
            $css = "<style>$css</style>";
            //add_header($css, 99); Theme Option CSS now Disabled
        }



        $favIcon = get_option('favicon');
        if (!empty($favIcon)) {
            $idArray = explode(",", $favIcon);
            $iconData = get_post($idArray[0], 'guid');
            $src = get_attachment_src($iconData['guid']);
            if (!empty($src)) {
                if (url_exists($src)) {
                    $info = @getimagesize($src);
                    $favHtml = "<link rel=\"icon\" href=\"$src\" type=\"$info[mime]\" sizes=\"$info[0]x$info[1]\">\n";
                    add_header($favHtml, 2);
                }
            }
        }


        global $POST;
        $CanoLink = get_canonical(); // get_link(@$POST['ID']);
        // var_dump($CanoLink);
        $str = "<link rel=\"canonical\" href=\"" . $CanoLink . "\" />\n";
        //var_dump($GLOBALS);
        if (!empty($POST) || $GLOBALS['blog']) {
            add_header($str, 5);
        }
    }
    //Custom post init
    if (defined('ADMIN')) {
        //echo "custom post init";
        customPost_init();

        if (isset($Pre_scriptFiles)) {
            foreach ($Pre_scriptFiles as $scriptFile) {
                $scInfo = file_info("$preloadScriptDir/$scriptFile");
                if (!isset($scInfo['dependent']) || !function_exists($scInfo['dependent'])) {
                    unlink("$preloadScriptDir/$scriptFile");
                }
            }
        }
    }
    // var_dump($POST);
    //exit;
    //Ajax Request
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        require_once("$adminDir/admin-include.php");
        if (isset($_GET['fancybox']) || isset($_GET['ajx'])) {
            include ABSPATH . "$adminDir/parts/fancybox-ajax.php";
        } else {
            require_once( ABSPATH . "$adminDir/admin-ajax.php");
        }
        exit;
    }

    //var_dump($html);
    add_footer(StatusBar(),1);
    if (!defined('ADMIN') && !defined('LOGIN')) {
        require_once( ABSPATH . 'include/pre_view_functions.php');
        require_once( ABSPATH . 'include/view.php');
    }
} else {
    //Config file not Found, so Create config via install
    require_once( ABSPATH . 'include/install_class.php' );
    require_once( ABSPATH . 'install.php');
}	