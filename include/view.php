<?php

if (webApp()) {
    header("location:$adminDir");
}
mb_internal_encoding("iso-8859-1");
mb_http_output("UTF-8");
ob_start("mb_output_handler");

defined('ABSPATH') OR exit('No direct script access allowed');
global $template;

function header404($content) {
    $reDirCode = get_option('redirect_code');
    $reDirCode = empty($reDirCode) ? 404 : $reDirCode;

    header("HTTP/1.0 $reDirCode");
    //var_dump("form Here 404");
    return $content;
}

if (!is_home() && empty($QV)) {
    $GLOBALS['template'] = '404.php';
}

if (!empty($GLOBALS['template'])) {
    $template = $GLOBALS['template'];
    if ($template == '404.php') {
        add_content_filter('header404', 1);
    }
    if (isset($GLOBALS['term']['taxonomy'])) {
        $tempCustom = trim($GLOBALS['term']['taxonomy']) . ".php";
        if (file_exists(THEME_DIR . current_theme_dir() . "/" . $tempCustom)) {
            include THEME_DIR . current_theme_dir() . "/" . $tempCustom;
        } elseif (file_exists(THEME_DIR . current_theme_dir() . "/" . $template)) {
            include THEME_DIR . current_theme_dir() . "/" . $template;
        } else {
            $msg = "$template";
            if ($template != $tempCustom) {
                $msg = "$template or $tempCustom";
            }
            echo "($msg) Template not found";
        }
    } elseif (file_exists(THEME_DIR . current_theme_dir() . "/" . $template)) {
        include THEME_DIR . current_theme_dir() . "/" . $template;
    } else {
        echo "($template) Template not found";
    }
} else {
    //var_dump($template);
    if ($POST['post_type'] == 'post') {
        //var_dump($POST);
        //if ($POST['post_type'] == 'post' || $POST['post_type'] == 'page') {
        $post_category = isset($QV['post_category']) ? $QV['post_category'] : false;
        $single_post = isset($QV['post_slug']) ? $QV['post_slug'] : false;

        $currPostID = isset($QV['post_slug']) ? slug2id($QV['post_slug'], 'post') : $POST['ID'];
        //$template = get_post_meta($POST['ID'], 'post_template');

        $temp = get_post_template($currPostID);
        if ($temp) {
            $template = get_post_template($currPostID);
        }
        //var_dump($template);

        $template_class = str_replace(".php", "", $template);
        if (!empty($template)) {
            if (file_exists(THEME_DIR . current_theme_dir() . "/" . $template)) {
                include THEME_DIR . current_theme_dir() . "/" . $template;
            } else {
                echo "($template) Template not found";
            }
        } else {
            if (file_exists(THEME_DIR . current_theme_dir() . "/post.php")) {
                include THEME_DIR . current_theme_dir() . "/post.php";
            } else {
                include THEME_DIR . current_theme_dir() . "/index.php";
            }
        }
    } else {
        //$template = get_post_meta($POST['ID'], 'post_template');

        if (get_post_template($POST['ID'])) {
            $template = get_post_template($POST['ID']);
        }

        $template_class = str_replace(".php", "", $template);
        if (!empty($template)) {
            if (file_exists(THEME_DIR . current_theme_dir() . "/" . $template)) {
                include THEME_DIR . current_theme_dir() . "/" . $template;
            } else {
                echo "($template) Template not found";
            }
        } else {
            if (empty($template) && is_home()) {
                $template = "home.php";
                if (file_exists(THEME_DIR . current_theme_dir() . "/" . $template)) {
                    include THEME_DIR . current_theme_dir() . "/" . $template;
                } else {
                    include THEME_DIR . current_theme_dir() . "/index.php";
                }
            } else {
                include THEME_DIR . current_theme_dir() . "/index.php";
            }
        }
    }
}
$out = content_filter(ob_get_clean());
//$out = get_content(false, $out);
$pattern = "#<p>(\s|&nbsp;|\?|</?\s?br\s?/?>)*</?p>#";
//$pattern = "/<[^\/>]*>([\s]?)*<\/[^>]*>/";  use this pattern to remove any empty tag
$out = preg_replace($pattern, '', $out);
//====================================
echo $out;
if (DEBUG) {
    $ru = getrusage();
    echo "<div style='position: fixed;bottom: 0;font-size: 10px;color: #ddd;text-align: center;width: 100%;'>";
    echo "This process used " . rutime($ru, $rustart, "utime") .
    " ms for its computations and \n";
    echo "It spent " . rutime($ru, $rustart, "stime") .
    " ms in system calls\n";
    echo "</div>";
}
exit;
//====================================
	//echo  base64_encode($out);
	//echo content_filter($out);
