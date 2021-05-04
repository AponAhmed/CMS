<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//var_dump($_SERVER);
define("PLUG_DIR", ABSPATH . "content/plugins/");
define("THEME_DIR", ABSPATH . "content/themes/");
define("ADMIN_ROOT", ABSPATH . "$adminDir/");
define("UPLOAD", ABSPATH . "content/upload/");
define("PRELOAD_DIR", ABSPATH . "include/reg_preload/");

define("TEMP", ABSPATH . "content/upload/temp/");
define("UPLOAD_URI", domain() . "/content/upload/");
define("ADMIN_CSS", domain() . "/$adminDir/css/");
define("ADMIN_JSS", domain() . "/$adminDir/js/");
define("COMMON_SC", domain() . "/include/common/");

define("CONTENT_URL", ABSPATH . "/content/");
define("COOKIE_DOMAIN", domain());
define("MINIFY_URL", ABSPATH . "content/upload/minify/");
define("PLUGIN_URL", ABSPATH . "content/plugins/");

define("DEFAULT_THEME", "default");
define("THEMES_PATH", domain() . "/content/themes/");
define("PLUGIN_PATH", domain() . "/content/plugins/");
define("ADMIN_IMG", domain() . "/$adminDir/images/");
$regMenus = array(
    "primary" => "Header",
);
//attacgment file sizes----
$sizes = array(100, 150, 200, 300, 768, 1024);

//.html
define('HTML_EXT', true);

//attachment File Icon
$fileformatIcon = array(
    "far fa-file-word" => array('.doc', '.dot', '.wbk', '.docx', '.docm', '.dotx', '.dotm', '.docb'),
    "far fa-file-excel" => array('.xls', '.xlt', '.xlm', '.xlsx', '.xlsm', '.xltx', '.xltm', '.xlsb', '.xla', '.xlam ', '.xll', '.xlw', '.csv'),
    "far fa-file-powerpoint" => array('.ppt', '.pot', '.pps', '.pptx', '.pptm', '.potx', '.potm', '.ppam', '.ppsx', '.ppsm', '.sldx', '.sldm'),
    "fas fa-file-image" => array('.jpeg', '.jpg', '.bmp', '.gif', '.png', '.bpg', ".JPG", ".webp"),
    "far fa-file-pdf" => array('.pdf'),
    "far fa-file-archive" => array('.zip', '.zipx', '.rar'),
    "far fa-file-alt" => array('.txt'),
    "far fa-file-audio" => array('.mp3', '.mpc', '.vox', '.wav', '.wma'),
    "far fa-file-code" => array('.html'),
);

$front_style = array();
$front_script = array();

//Resurved Variable
$PRODUCT = array();
$TITLE = "";
$KEYWORD = "";
$DESCRIPTION = "";
$ROBOT = "";

$blogUrl = get_post(get_option('blog_page'), 'post_name');
$blogUrl = empty($blogUrl['post_name']) ? "blog" : $blogUrl['post_name'];
define("POST_PATH", $blogUrl);

$RW_Role = array(
    4 => array("@([\d+]{4})/([\d+]{1,2})/([\d+]{0,2})@", 'year,month,day'),
	3 => array("@(author)/(.*)@", 'author,author_name'),
	2 => array("@([^/?]+|[^.])/(page)/([\d]+)@", "page,p,page_no"),
	1 => array("@([^/?]+|[^.])/([^/?]+|[^.])@", 'parent,page'),
	0 => array("@([^/?]+|[^.])@", 'page'),
);
//define('CACHE_PATH', CONTENT_URL . "upload/cache/");

