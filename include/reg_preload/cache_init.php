<?php

/*
 * dependent:cache_set
 */
//Dependency in most required for regestry pre script 
$bb = "";
$bb = @file_get_contents(ABSPATH . 'include/cacheTmp.tm');
if (!empty($bb)) {
    define('SS3_BUCKET', $bb);
}

if (defined('SS3_BUCKET')) {
    //echo "S3 Enabled";
    $CACHE_PATH = "https://" . SS3_BUCKET . ".s3.amazonaws.com/content/cache/";
    define('CACHE_PATH', $CACHE_PATH);
} else {
    define('CACHE_PATH', ABSPATH . "content/cache/");
}

//var_dump(CACHE_PATH);
function cacheFile($echo = true) {
    //global $CACHE_PATH;
//    $behav = unserialize(get_option('cache_behav')); //showFCacheLogin
//    if (isset($_SESSION[SESS_KEY]['login']) && @$behav['showFCacheLogin'] != 'true') {
//        return false;
//    }

    $file = md5(trim($_SERVER['REQUEST_URI'], '/'));
    //$file =trim($_SERVER['REQUEST_URI'], '/');
   // var_dump($file);
    $filename = CACHE_PATH . "$file.html";
    $filename = preg_replace('/([^:])(\/{2,})/', '$1/', $filename);
    $mdTime = @filemtime($filename);
    $hr = 0;
    //var_dump($filename);

    if (!defined('ADMIN')) {
        if (defined('SS3_BUCKET')) {
            $content = @file_get_contents($filename);
            if ($content) {
                echo $content;
                exit;
            }
        }

        if (file_exists($filename)) {
            $c = file_get_contents($filename);
           // var_dump($c);
            if ($echo !== false) {
                echo $c;
                if ($hr != 0 && ($mdTime + ($hr * 3600)) < time()) {
                    @unlink($filename);
                }
                exit;
            } else {
                return $c;
            }
        }
    }
    return false;
}

if (function_exists('cacheFile')) {
    if (!isset($_SESSION['login'])) {
        cacheFile();
    }
}
