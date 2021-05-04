<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function get_post_title($post_id = false) {
    global $DB, $POST, $QV, $TERM;
    $tpID = $post_id;
    if ($post_id == false) {
        $post_id = @$POST['ID'];
    }
    $content = $DB->select("post", "post_title", "ID=$post_id");

    if (isset($QV['term']) && empty($content) && $tpID === false) {
        $trm = $TERM->slug2term($QV['term']);
        //var_dump($trm);
        return $trm['name'];
    }
    return isset($content[0]['post_title']) ? $content[0]['post_title'] : "";
}

function get_canonical() {
    $link = domain() . $_SERVER['REQUEST_URI'];
    $link = preg_replace('/([^:])(\/{2,})/', '$1/', $link);
    return $link;
}

function bodyClass() {
    global $TITLE, $template_class;
    $cls = array(titleFilter($TITLE), $template_class);
    if (is_home()) {
        $cls[] = 'home-page';
    }
    if (isset($GLOBALS['term'])) {
        $term = $GLOBALS['term'];
        $cls[] = 'texonomy';
        $cls[] = $term['taxonomy'];
        $cls[] = $term['slug'];
    }
    return implode(" ", $cls);
}

function __conv($text) {
    // map based on:
    // http://konfiguracja.c0.pl/iso02vscp1250en.html
    // http://konfiguracja.c0.pl/webpl/index_en.html#examp
    // http://www.htmlentities.com/html/entities/
    $map = array(
        chr(0x8A) => chr(0xA9),
        chr(0x8C) => chr(0xA6),
        chr(0x8D) => chr(0xAB),
        chr(0x8E) => chr(0xAE),
        chr(0x8F) => chr(0xAC),
        chr(0x9C) => chr(0xB6),
        chr(0x9D) => chr(0xBB),
        chr(0xA1) => chr(0xB7),
        chr(0xA5) => chr(0xA1),
        chr(0xBC) => chr(0xA5),
        chr(0x9F) => chr(0xBC),
        chr(0xB9) => chr(0xB1),
        chr(0x9A) => chr(0xB9),
        chr(0xBE) => chr(0xB5),
        chr(0x9E) => chr(0xBE),
        chr(0x80) => '&euro;',
        chr(0x82) => '&sbquo;',
        chr(0x84) => '&bdquo;',
        chr(0x85) => '&hellip;',
        chr(0x86) => '&dagger;',
        chr(0x87) => '&Dagger;',
        chr(0x89) => '&permil;',
        chr(0x8B) => '&lsaquo;',
        chr(0x91) => '&lsquo;',
        chr(0x92) => '&rsquo;',
        chr(0x93) => '&ldquo;',
        chr(0x94) => '&rdquo;',
        chr(0x95) => '&bull;',
        chr(0x96) => '&ndash;',
        chr(0x97) => '&mdash;',
        chr(0x99) => '&trade;',
        chr(0x9B) => '&rsquo;',
        chr(0xA6) => '&brvbar;',
        chr(0xA9) => '&copy;',
        chr(0xAB) => '&laquo;',
        chr(0xAE) => '&reg;',
        chr(0xB1) => '&plusmn;',
        chr(0xB5) => '&micro;',
        chr(0xB6) => '&para;',
        chr(0xB7) => '&middot;',
        chr(0xBB) => '&raquo;',
    );
    return html_entity_decode(mb_convert_encoding(strtr($text, $map), 'UTF-8', 'ISO-8859-2'), ENT_QUOTES, 'UTF-8');
}

function _convert($content) {
    if (!mb_check_encoding($content, 'UTF-8') OR !($content === mb_convert_encoding(mb_convert_encoding($content, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32'))) {

        $content = mb_convert_encoding($content, 'UTF-8');
        $content = __conv($content);
    }
    return $content;
}

function get_content($post_id = false, $ExString = false) {
    global $DB, $POST, $contentfilte, $QV, $TERM;
    if ($post_id == false) {
        if (!empty($POST)) {
            $post_id = $POST['ID'];
        }
    }
    $content = $DB->select("post", "post_content", "ID=$post_id");
    $string = isset($content[0]['post_content']) ? $content[0]['post_content'] : "";


    if (isset($QV['term']) && empty($string)) {
        $term = $TERM->slug2term($QV['term']);
        $string = $term['description'];
    }

    if ($ExString) {
        $string = $ExString;
    }
    $string = clnContentEnc($string);

    return do_shortcode($string);
    //return $string;
    //Shortcode=========
    $regex = "/\[(.*?)\]/";
    preg_match_all($regex, $string, $matches);

    foreach ($matches[1] as $scode) {
        $Ascode = htmlspecialchars_decode($scode);
        //$attr = preg_split("/[\s]+/", $scode); //desable at February 2021   

        $re = '/([a-z\d_-]+)="(.*?)"/mi';
        preg_match_all($re, $Ascode, $matchesA, PREG_SET_ORDER, 0);


        $attribuit = array();
        foreach ($matchesA as $matchedArr) {
            $attribuit[$matchedArr[1]] = $matchedArr[2];
        }


//desable at February 2021       
//        $i = 0;
//        $attribuit = array();
//        foreach ($attr as $at) {
//            $i++;
//            if ($i == 1) {
//                continue;
//            }
//            $KnV = explode("=", $at);
//            $attribuit[trim($KnV[0])] = isset($KnV[1]) ? trim($KnV[1]) : "";
//        }//$attr
////desable at February 2021 
        //var_dump($attribuit);
        preg_match_all("/[^\[\]\s]+/", $scode, $split);
        if (isset($split[0][0])) {
            $calF = trim($split[0][0]);
            //var_dump($calF);
            //var_dump($scode,$split);
            if (function_exists($calF)) {
                $string = str_replace("<p>[" . $scode . "]</p>", $calF($attribuit), $string);
                $string = str_replace("[" . $scode . "]", $calF($attribuit), $string);
            }
        }
    }
    //Filter content
    //    foreach ($contentfilte as $c_f) {
    //        if (function_exists($c_f)) {
    //            $string = $c_f($string);
    //        } else {
    //            echo "filter Not Found";
    //        }
    //    }
    return $string;
}

function shortcode_exe($string) {
    return do_shortcode($string);
    $string = clnContentEnc($string);

    //Shortcode=========
    $regex = "/\[(.*?)\]/";
    preg_match_all($regex, $string, $matches);

    foreach ($matches[1] as $scode) {
        $Ascode = htmlspecialchars_decode($scode);
        //$attr = preg_split("/[\s]+/", $scode); //desable at February 2021   

        $re = '/([a-z\d_-]+)="(.*?)"/mi';
        preg_match_all($re, $Ascode, $matchesA, PREG_SET_ORDER, 0);


        $attribuit = array();
        foreach ($matchesA as $matchedArr) {
            $attribuit[$matchedArr[1]] = $matchedArr[2];
        }


//desable at February 2021       
//        $i = 0;
//        $attribuit = array();
//        foreach ($attr as $at) {
//            $i++;
//            if ($i == 1) {
//                continue;
//            }
//            $KnV = explode("=", $at);
//            $attribuit[trim($KnV[0])] = isset($KnV[1]) ? trim($KnV[1]) : "";
//        }//$attr
////desable at February 2021 
        //var_dump($attribuit);
        preg_match_all("/[^\[\]\s]+/", $scode, $split);
        if (isset($split[0][0])) {
            $calF = trim($split[0][0]);
            //var_dump($calF);
            //var_dump($scode,$split);
            if (function_exists($calF)) {
                $string = str_replace("<p>[" . $scode . "]</p>", $calF($attribuit), $string);
                $string = str_replace("[" . $scode . "]", $calF($attribuit), $string);
            }
        }
    }
    return $string;
}

function content_filter($string, $filter = false) {
    //return $string;
    global $contentfilte;
    ksort($contentfilte);
    //var_dump($contentfilte);
    if ($filter) {
        if (in_array($filter, $contentfilte) && function_exists($filter)) {
            $string = $filter($string);
        } else {
            echo "$filter Not a valid Filter function";
        }
    } else {
        foreach ($contentfilte as $c_f) {
            if (function_exists($c_f)) {
                $string = $c_f($string);
            } else {
                echo "filter Not Found";
            }
        }
    }
    return $string;
}

function get_header() {
    $headerFile = THEME_DIR . current_theme_dir() . "/header.php";
    if (file_exists($headerFile)) {
        include $headerFile;
    }
}

function get_footer() {
    $footerFile = THEME_DIR . current_theme_dir() . "/footer.php";
    if (file_exists($footerFile)) {
        include $footerFile;
    }
}

function add_script($array) {
    global $front_script;
    $id = $array['id'];
    //var_dump($id);
    $front_script[$id] = $array;
}

function add_style($array) {
    global $front_style;
    $front_style[] = $array;
}

function inc_styles_($echo = false, $position = 'head') {

    global $front_style, $front_style_filter;
    usort($front_style, function($a, $b) {
        return $a['order'] - $b['order'];
    });

    $styleIncPosition = unserialize(get_option('styleIncPosition'));

    //var_dump($styleIncPosition);

    $html = "";
    $filteredFile = unique_multidim_array($front_style, 'id');
    $minify = get_option('css_min');
    $incType = get_option('min_inc_type_css');
    $indi_inc = get_option('indi_incCss');
    foreach ($filteredFile as $styleInfo) {
        $hrf = trimSlash($styleInfo['href']);
        $thisDom = str_replace("/", '\/', domain());
        //var_dump($hrf, $thisDom);
        $cssID = $styleInfo['id'];
        if ($styleIncPosition[$cssID] == 'footer') {
            $styleInfo['position'] = 'footer';
        } else {
            $styleInfo['position'] = 'head';
        }
        $re = '#' . $thisDom . '#s';
        preg_match($re, $hrf, $matches, PREG_OFFSET_CAPTURE, 0);
        if ($matches && $minify) {
            if ($incType == 'ext' && $indi_inc == "true") {
                //$fName = md5($hrf);
                if ($position == $styleInfo['position']) {

                    $fileInfo = pathinfo($hrf);
                    $fileName = $fileInfo['basename'];
                    $pathArr = explode('/', $fileInfo['dirname']);
                    $mx = count($pathArr);
                    $ltCIndex = $mx - 1;
                    $lt2CIndex = $mx - 2;
                    $fileName = $pathArr[$lt2CIndex] . "_" . $pathArr[$ltCIndex] . "_" . $fileName;

                    $minDirFile = domain() . "/content/upload/minify/css/$fileName";
                    $minDirFile = preg_replace('/([^:])(\/{2,})/', '$1/', $minDirFile);
                    $html.="<link rel=\"stylesheet\" href=\"$minDirFile\">\n";
                }
            }
            continue;
        }
        if ($position == $styleInfo['position']) {
            $html.="<link rel=\"stylesheet\" href=\"$hrf\">\n";
        }
    }
    if (($indi_inc != 'true') || $incType == 'int' && $minify) {
        //All Optimize css
        if ($position == 'head') {
            $href = domain() . "/content/upload/minify/header_style.css";
            $href = preg_replace('/([^:])(\/{2,})/', '$1/', $href);
            $styleContent = file_get_contents(ABSPATH . "content/upload/minify/header_style.css");
            $type = get_option('min_inc_type_css');
            if ($type == 'int') {
                $html.="<style>$styleContent</style>";
            } else {
                $html.="<link rel=\"stylesheet\" href=\"$href\">\n";
            }
        }
        if ($position == 'footer') {
            $href = domain() . "/content/upload/minify/footer_style.css";
            $href = preg_replace('/([^:])(\/{2,})/', '$1/', $href);
            $styleContent = file_get_contents(ABSPATH . "content/upload/minify/footer_style.css");
            $type = get_option('min_inc_type_css');
            if ($type == 'int') {
                $html.="<style>$styleContent</style>";
            } else {
                $html.="<link rel=\"stylesheet\" href=\"$href\">\n";
            }
        }
    }

    foreach ($front_style_filter as $filter) {
        if (function_exists($filter)) {
            $html = $filter($html, $position);
        }
    }

    //var_dump($adminStyles);
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

function inc_scripts_($echo = false, $position = "") {
    global $front_script, $front_script_filter;
    // var_dump($front_script);

    $scriptIncAtt = unserialize(get_option('scriptIncAtt'));
    $scriptIncPosition = unserialize(get_option('scriptIncPosition'));

    usort($front_script, function($a, $b) {
        return $a['order'] - $b['order'];
    });
    $html = "";
    $filteredFile = unique_multidim_array($front_script, 'id');
    $minify = get_option('js_min');
    $incType = get_option('min_inc_type');
    $indi_inc = get_option('indi_inc');
    $singleFileAtt = get_option('singleFileAtt');


    foreach ($filteredFile as $styleInfo) {
        $src = trimSlash($styleInfo['src']);
        $scID = $styleInfo['id'];

        if (isset($scriptIncAtt[$scID])) {
            $att = $scriptIncAtt[$scID];
        } else {
            $att = "";
        }

        if ($scriptIncPosition[$scID] == 'footer') {
            $styleInfo['position'] = 'footer';
        }
        //var_dump($styleInfo);

        $thisDom = str_replace("/", '\/', domain());
        //var_dump($hrf, $thisDom);
        $re = '#' . $thisDom . '#s';
        preg_match($re, $src, $matches, PREG_OFFSET_CAPTURE, 0);
        if ($matches && $minify) {
            if ($incType == 'ext' && $indi_inc == "true") {
                if ($position == "head" && (isset($styleInfo['position']) || $styleInfo['position'] == 'head')) {
                    if (isset($styleInfo['position']) && $styleInfo['position'] == $position) {
                        //$fileName = md5($src);

                        $fileInfo = pathinfo($src);
                        $fileName = $fileInfo['basename'];
                        $pathArr = explode('/', $fileInfo['dirname']);
                        $mx = count($pathArr);
                        $ltCIndex = $mx - 1;
                        $lt2CIndex = $mx - 2;
                        $fileName = $pathArr[$lt2CIndex] . "_" . $pathArr[$ltCIndex] . "_" . $fileName;

                        $minDirFile = domain() . "/content/upload/minify/js/$fileName";
                        $minDirFile = preg_replace('/([^:])(\/{2,})/', '$1/', $minDirFile);
                        $html.="<script src=\"$minDirFile\" $att></script>\n";
                    }
                }

                if ($position == "footer" && (!isset($styleInfo['position']) || $styleInfo['position'] == 'footer')) {
                    //var_dump($position);
                    if (!isset($styleInfo['position']) || $styleInfo['position'] == "footer") {
                        $fileInfo = pathinfo($src);
                        $fileName = $fileInfo['basename'];
                        $pathArr = explode('/', $fileInfo['dirname']);
                        $mx = count($pathArr);
                        $ltCIndex = $mx - 1;
                        $lt2CIndex = $mx - 2;
                        $fileName = $pathArr[$lt2CIndex] . "_" . $pathArr[$ltCIndex] . "_" . $fileName;

                        $minDirFile = domain() . "/content/upload/minify/js/$fileName";
                        $minDirFile = preg_replace('/([^:])(\/{2,})/', '$1/', $minDirFile);
                        $html.="<script src=\"$minDirFile\" $att></script>\n";
                    }
                }
            }
            continue;
        }
        if ($position != "" && $position !== "footer") {
            if (isset($styleInfo['position']) && $styleInfo['position'] == $position) {
                $html.="<script src=\"$src\" $att></script>\n";
            }
        } else {
            if (isset($styleInfo['position']) && $styleInfo['position'] != "head") {
                $html.="<script src=\"$src\" $att></script>\n";
            }
        }
    }

    if (($minify && $indi_inc != 'true') || $incType == 'int') {
        $type = get_option('min_inc_type');
        if ($position !== "footer") {
            $headerOptScript = file_get_contents(ABSPATH . "content/upload/minify/optimize_header_js.js");
            if (!empty($headerOptScript)) {
                if ($type == 'int') {
                    $html.="<script>$headerOptScript</script>\n";
                } else {
                    $src = domain() . "/content/upload/minify/optimize_header_js.js";
                    $src = preg_replace('/([^:])(\/{2,})/', '$1/', $src);
                    $html.="<script src='$src' $singleFileAtt></script>\n";
                }
            }
        }
        if ($position !== "head") {
            if ($type == 'int') {
                $footerOptScript = file_get_contents(ABSPATH . "content/upload/minify/optimize_footer_js.js");
                $html.="<script>$footerOptScript</script>\n";
            } else {
                $src = domain() . "/content/upload/minify/optimize_footer_js.js";
                $src = preg_replace('/([^:])(\/{2,})/', '$1/', $src);
                $html.="<script src='$src' $singleFileAtt></script>\n";
            }
        }
    }

    foreach ($front_script_filter as $filter) {
        if (function_exists($filter)) {
            $html = $filter($html, $position);
        }
    }

    //var_dump($adminStyles);
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

function inc_styles($echo = false, $position = false) {
    global $front_style, $front_style_filter;
    ksort($front_style_filter);


    usort($front_style, function($a, $b) {
        return $a['order'] - $b['order'];
    });
    $html = "";
    $filteredFile = unique_multidim_array($front_style, 'id');


    foreach ($filteredFile as $styleInfo) {
        //reset to footer all script wgice is undefined position
        if (!isset($styleInfo['position'])) {
            $styleInfo['position'] = 'head';
        }
        if ($position == $styleInfo['position']) {
            $hrf = trimSlash($styleInfo['href']);
            $hrf = preg_replace('/([^:])(\/{2,})/', '$1/', $hrf);
            $html.="<link rel=\"stylesheet\" href=\"$hrf\">\n";
        }
    }

    //var_dump($front_style_filter);
    foreach ($front_style_filter as $filter) {
        if (function_exists($filter)) {
            $html = $filter($html, $position);
        }
    }


    //var_dump($adminStyles);
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

function inc_scripts($echo = false, $position = "") {
    global $front_script, $front_script_filter;
    ksort($front_script_filter);
    // var_dump($front_script);
    //var_dump($front_script);

    foreach ($front_script as $scID => $scInfo) {
        if (isset($scInfo['dependency'])) {
            $maxOrd = 0;
            foreach ($scInfo['dependency'] as $depend) {
                $depScOrder = @$front_script[$depend]['order'];
                if ($depScOrder > $maxOrd) {
                    $maxOrd = $depScOrder;
                }
            }
            if ($scInfo['order'] <= $maxOrd) {
                $front_script[$scID]['order'] = ($maxOrd + 1);
            }
        }
    }
    //var_dump($front_script);

    usort($front_script, function($a, $b) {
        return $a['order'] - $b['order'];
    });
    //var_dump($front_script);

    $html = "";
    $filteredFile = unique_multidim_array($front_script, 'id');
    foreach ($filteredFile as $styleInfo) {
        //reset to footer all script wgice is undefined position
        if (!isset($styleInfo['position'])) {
            $styleInfo['position'] = 'footer';
        }
        if ($position == $styleInfo['position']) {
            $src = trimSlash($styleInfo['src']);
            $html.="<script src=\"$src\"></script>\n";
        }
    }

    foreach ($front_script_filter as $filter) {
        if (function_exists($filter)) {
            $html = $filter($html, $position);
        }
    }

    //var_dump($adminStyles);
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

function head() {
    global $head_str_arr;

    $head_str_arr[] = array('str' => inc_scripts(false, "head"), 'order' => 2);
    $head_str_arr[] = array('str' => inc_styles(false, "head"), 'order' => 3);
    usort($head_str_arr, function($a, $b) {
        return $a['order'] - $b['order'];
    });

    // var_dump($head_str_arr);

    foreach ($head_str_arr as $strArr) {
        if (isset($strArr['calback']) && $strArr['calback'] !== false) {
            $calBack = $strArr['str'];
            if (function_exists($calBack)) {
                $calBack();
            } else {
                echo $strArr['str'];
            }
        } else {
            echo $strArr['str'];
        }
    }
}

function footer() {
    global $foot_str_arr;
    $foot_str_arr[] = array('str' => inc_styles(false, 'footer'), 'order' => 30);
    $foot_str_arr[] = array('str' => inc_scripts(false, "footer"), 'order' => 50);
    usort($foot_str_arr, function($a, $b) {
        return $a['order'] - $b['order'];
    });

    foreach ($foot_str_arr as $strArr) {
        if (isset($strArr['calback']) && $strArr['calback'] !== false) {
            $calBack = $strArr['str'];
            if (function_exists($calBack)) {
                $calBack();
            } else {
                echo $strArr['str'];
            }
        } else {
            echo $strArr['str'];
        }
    }
}

function menu($arg) {
    //var_dump($arg);
    $MenuStrung = "";
    if (isset($arg['name']) && !empty($arg['name'])) {
        $MenuStrung = get_menu($arg['name'], '', 'sc-menu', array(), false);
    } else {
        $MenuStrung = "Menu Name Attr. Missing !";
    }
    return $MenuStrung;
}

function register_menu($arg = array()) {
    global $regMenus;
    //$dif = array('slug' => 'location');
    array_push($regMenus, $arg);
}

function get_child_menu($cl = false, $id = "", $arg = array(), $echo = true) {
    global $MENU;
    $html = "";
    $id = !empty($id) ? "id='$id'" : "";

    $objid = false;
    if (isset($arg['post_id']) && !empty($arg['post_id'])) {
        $objid = $arg['post_id'];
    }
    $object = $MENU->currentChild($objid);

    //var_dump(ActiveMenuRec($object));

    $titleSub = @$object[0]['menu_title'];
    $object = @$object[0]['child'];


    $html.="<strong class='$cl-title sub-title pcatHead'>$titleSub</strong>";
    $html.= "<ul  $id class='$cl'>";
    if (!empty($object)) {
        foreach ($object as $obj) {
            $html.=getMenuRec($obj, $arg);
        }
    }
    $html.="</ul>";
    if ($echo) {
        echo $html;
    }
    return $html;
}

function get_menu($menu_name = false, $id = "", $cl = false, $arg = array(), $echo = true) {
    global $MENU;

    $menu_name = $MENU->getLinkedName($menu_name);

    $html = "";
    $dfltClass = str_replace(" ", "-", $menu_name);
    $id = !empty($id) ? "id='$id'" : "";
    $html.= "<ul  $id class='$cl $dfltClass'>";
    $object = $MENU->pub_menu($menu_name, isset($arg['disable_custom_title']) ? $arg['disable_custom_title'] : false);
    if (!empty($object)) {
        foreach ($object as $obj) {
            // var_dump(ActiveMenuRec($obj),$obj['child']);
            $html.=getMenuRec($obj, $arg, $menu_name);
        }
    }
    $html.="</ul>";
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

function getMenuRec($item, $arg = array(), $menu_name = false) {
    $cH = "";
    if (isset($item['child']) && !empty($item['child'])) {
        foreach ($item['child'] as $child) {
            $cH.=getMenuRec($child, $arg, $menu_name);
        }
        $pM = getMenuObj2Htm($item, $cH, $arg, $menu_name);
        return $pM;
    } else {
        return getMenuObj2Htm($item, $cH, $arg, $menu_name);
    }
}

function getMenuObj2Htm($item, $childs = "", $arg = array(), $menu_name = false) {
    //echo "<pre>";
    global $MENU, $POST, $menuInject, $RWR, $TERM, $QV;

    $prefix = isset($arg['prefix']) && !empty($arg['prefix']) ? $arg['prefix'] : "";
    if (!empty($prefix)) {
        $prefix = "<span class='menuPrefix'>$prefix</span>";
    }

    //var_dump($menu_name);
    $Smetas = get_post_metas($item['id']);

    //var_dump($Smetas);
    $custTitAtt = !empty($Smetas['menu_customTitle']) ? $Smetas['menu_customTitle'] : $item['menu_title'];
    $custClass = !empty($Smetas['menu_customClass']) ? $Smetas['menu_customClass'] : "";
    $custScode = !empty($Smetas['menu_shortCode']) ? $Smetas['menu_shortCode'] : "";

    $subMenuClass = "";
    $menuInject_F = array();
    foreach ($menuInject as $mf) {
        if (isset($mf['menus'])) {
            if (in_array($menu_name, $mf['menus'])) {
                $menuInject_F[] = $mf;
            }
        } else {
            $menuInject_F[] = $mf;
        }
    }
    $childElemHtml = ""; //Auto Append 
    $chldHtm = "";
    $liClass = "nav-item $custClass";
    $navTitle = "";
    $navLink = "";
    $curr = "";


    if (isset($arg['sub-class'])) {
        $subMenuClass = $arg['sub-class'];
    }


    $current = @$POST['ID'];
    $parent = @$POST['post_parent'];
    if (!empty($childs)) {
        $chldUl = "<ul class='sub-menu $subMenuClass'>$childs</ul>";

        if (!empty($custScode)) {
            $subMenuClass.="menu-has-sc-part ";
            $chldUl = "<div class='sub-menu mega-sub'><div class='menu-sc-part'>" . $custScode . "</div><div class='sub-part'>" . $chldUl . "</div></div>";
        }
        $chldHtm = "<span class=\"has_sub_trgg\"></span>$chldUl";
        $liClass.=" has_sub";
    } else {
        if (!empty($custScode)) {
            $chldUl = "";
            $subMenuClass.="menu-has-sc-part ";
            $chldUl = "<div class='sub-menu mega-sub'><div class='menu-sc-part'>" . $custScode . "</div><div class='sub-part'>" . $chldUl . "</div></div>";

            $chldHtm = "<span class=\"has_sub_trgg\"></span>$chldUl";
            $liClass.=" has_sub";
        }
    }


    if (is_numeric($item['bject_id'])) {
        $navLink = get_link($item['bject_id']);
        $navTitle = $item['menu_title'];

        $navLink = isset($item['customLink']) && !empty($item['customLink']) ? $item['customLink'] : get_link($item['bject_id']);
        $subMenuTitle = $item['menu_title'];
        if ($current == $item['bject_id']) {
            $liClass.= " active";
        }
    } else {
        $terminf = explode('_', $item['bject_id']);
        $termID = @$terminf[1];
        $term = $TERM->get_term($termID);
        $navLink = isset($item['customLink']) && !empty($item['customLink']) ? $item['customLink'] : get_term_link($term);
        $navTitle = empty($item['menu_title']) ? @$term['name'] : @$item['menu_title'];
        if (@$QV['term'] == @$term['slug'] && !empty($item['bject_id'])) {
            $liClass.= " active";
        }

        if ($Smetas['menu_appendChield'] == 'true') {
            $default = array(
                'post_type' => array('post', 'page'),
                'selectFields' => "ID,post_title",
                'texonomy' => $term['slug']
            );
            $childPages = get_posts($default);
            if (!empty($childPages)) {
                $childElemHtml = "<span class=\"has_sub_trgg\"><ul class='sub-menu $subMenuClass appended-auto'>";

                foreach ($childPages as $childElement) {
                    $SubclassAxz = "";
                    $classAxz = "";
                    $childLink = get_link($childElement['ID']);
                    if ($current == $childElement['ID']) {
                        $SubclassAxz.= "active";
                        $classAxz.="current";
                    }
                    $childElemHtml.="<li class='$SubclassAxz $classAxz '><a href='$childLink' title='$childElement[post_title]'>{$prefix}$childElement[post_title]</a></li>";
                }
                $childElemHtml.="</ul>";
                $chldHtm = $childElemHtml;
            }
        }
    }



    if (!empty($menuInject_F)) {
        foreach ($menuInject_F as $menuFilter) {
            $lowStr = strtolower($item['menu_title']);
            $skip = false;

            if (isset($menuFilter['exc']) && !empty($menuFilter['exc'])) {
                //var_dump($lowStr);
                $ssrr = array_map('strtolower', $menuFilter['exc']);
                $skip = in_array($lowStr, $ssrr);
            }

            if ($item['bject_id'] == $menuFilter['obID'] || $menuFilter['obID'] == 'all') {
                $calbackF = $menuFilter['callBack'];
                if (function_exists($calbackF)) {
                    if ($menuFilter['inAct'] == 'subMenu' && !$skip) {
                        $chldHtm = $calbackF($chldHtm, @$item['child'], $item);
                    }
                    if ($menuFilter['inAct'] == 'title' && !$skip) {
                        $navTitle = $calbackF($navTitle);
                    }
                    if ($menuFilter['inAct'] == 'href' && !$skip) {
                        $navLink = $calbackF($navLink);
                    }
                }
            }
        }
    }

    if (ActiveMenuRec($item)) {
        //var_dump($item);
        $curr = "current";
        $chldHtm = str_replace("sub-menu", "sub-menu active-sub", $chldHtm);
    }

    if (isset($Smetas['menu_disableUrl']) && $Smetas['menu_disableUrl'] == 'true') {
        return "<li id='ID_$item[id]' class='$liClass $curr'><span class='noLink'  title='$custTitAtt'>{$prefix}$navTitle</span>$chldHtm</li>";
    } else {
        return "<li id='ID_$item[id]' class='$liClass $curr'><a href='$navLink'  title='$custTitAtt'>{$prefix}$navTitle</a>$chldHtm</li>";
    }
}

function ActiveMenuRec($item) {
    global $POST, $QV, $GLOBALS;


    $term = @$GLOBALS['term'];
    if (!empty($term) && empty($POST)) {
        //var_dump($term);
        $id = "term_" . $term['term_id'];
    } else {
        $id = @$POST['ID'];
    }
    //echo "checked $item[menu_title] <br>";
    //echo "<pre>";
    // var_dump($item['menu_title']);

    if ($item['bject_id'] == $id) {
        return true;
    } else {
        if (!empty($item['child'])) {
            foreach ($item['child'] as $c) {
                // echo "checked $item[menu_title] <br>";
                return ActiveMenuRec($c);
            }
        }
    }
}

function myfunction($products, $field = 'bject_id', $value = '') { //try=====
    foreach ($products as $key => $product) {
        if ($product[$field] === $value)
            return $key;
    }
    return false;
}

function breadcrumb_item() {
    global $POST, $QV, $TERM, $GLOBALS;
    $items = array();
    $items[] = array('title' => 'Home', 'link' => domain(), 'type' => 'root', 'id' => get_option('front_page')); //Home Page
    if (isset($QV['search_string']) && $QV['search_string'] != "") {
        $items[1] = array('title' => "Search - $QV[search_string]", 'link' => "", 'type' => 'search');
    } elseif (empty($POST) && empty($GLOBALS['term'])) {
        $items[1] = array('title' => '404', 'link' => "", 'type' => 'Error');
    } else {
        if (!empty($GLOBALS['term'])) {
            $termName = $GLOBALS['term']['name'];
            $termLink = get_term_link($GLOBALS['term']);
            $term = $GLOBALS['term'];
            $termDisable = get_term_meta($term['term_id'], 'disableSlug');
            if ($termDisable != 'true') {
                $items[1] = array('title' => $termName, 'link' => $termLink, 'type' => 'Term Page %%term%%', 'id' => $term['term_id']);
            }
        } else {
            if (!empty($POST)) {
                $terms = get_post_terms($POST['ID']);
                if (!empty($terms)) {
                    $term = $terms[0];
                    $termName = $term['name'];
                    $termLink = get_term_link($term);
                    $termDisable = get_term_meta($term['term_id'], 'disableSlug');
                    if ($termDisable != 'true') {
                        $items[1] = array('title' => $termName, 'link' => $termLink, 'type' => "Parent Term($term[taxonomy]) %%term%%", 'id' => $term['term_id']);
                    }
                } else {
                    $parentID = !empty($POST['post_parent']) ? $POST['post_parent'] : "";
                    if (!empty($parentID)) {
                        $parent = get_post_title($parentID);
                        $parentPAgeLink = get_link($parentID);
                        $items[1] = array('title' => $parent, 'link' => $parentPAgeLink, 'type' => 'Parent Page %%page%%', 'id' => $parentID);
                    }
                }
                $items[] = array('title' => $POST['post_title'], 'link' => get_link($POST['ID']), 'type' => "Last Node($POST[post_type]) %%page%%", 'id' => $POST['ID']);
            }
        }
    }
    return $items;
}

function breadcrumb($cls = "", $sep = "") {
    $items = breadcrumb_item();
    $c = count($items);
    $html = "<ul class=\"bradC $cls\">";
    $n = 0;
    foreach ($items as $item) {
        $ItemTitle = $item['title'];
        $l = strlen($ItemTitle);
        if ($l > 30) {
            $pref = substr($ItemTitle, 0, 10);
            $suf = substr($ItemTitle, -10);
            $middle = substr($ItemTitle, 10, ($l - 10));
            $ItemTitle = $pref . "<span class='breadHide'>" . $middle . "</span>" . $suf;
        }
        $n++;
        if ($n == $c) {
            $html.= "<li>$ItemTitle</li>";
        } else {
            $html.= "<li><a href='$item[link]'>$ItemTitle</a></li>";
        }
    }
    $html.="</ul>";
    echo $html;
    return;
    global $POST, $QV, $TERM, $GLOBALS;
    //$terms = get_post_terms($POST['ID']);
    //echo "<pre>";
    // var_dump($QV, $terms, $GLOBALS);
    //echo "</pre>";
    //return;

    $html = "<ul class=\"bradC $cls\">";
    $html.="<li><a href=\"" . domain() . "\">Home</a></li>";
    if (empty($POST) && !isset($QV['post_category'])) {
        $html.="<li><a href=\"\">404-Not found</a></li>";
    } else {
        $parentID = !empty($POST['post_parent']) ? $POST['post_parent'] : "";
        if (!empty($parentID)) {
            $parent = get_post_title($parentID);
            $parentPAgeLink = get_link($parentID);
            $html.="<li><a href=\"$parentPAgeLink\">$parent</a></li>";
        }

        if (!is_home() && !isset($QV['post_slug']) && !isset($QV['post_category'])) {
            //$html.="<li><a href=\"" . get_link($POST['ID']) . "\">$POST[post_title]</a></li>";
            $html.="<li>$POST[post_title]</li>";
        }
        //Post=========
        if (isset($QV['post_category'])) {
            $name = $TERM->slug2texoID($QV['post_category'], true);
            //var_dump($name);
            $link = domain() . "/$QV[post_category]";
            $link = preg_replace('/([^:])(\/{2,})/', '$1/', $link . "/");
            if (defined('HTML_EXT') && get_option('html_ext') == 'true') {
                $link = substr($link, 0, strlen($link) - 1) . ".html"; //for .html
            }        //var_dump($QV);

            if (!empty($QV['post_slug'])) {
                $html.="<li><a href=\"" . $link . "\">$name</a></li>";
            } else {
                $html.="<li>$name</li>";
            }
        }

        if (isset($QV['post_slug'])) {
            $PostId = slug2id($QV['post_slug']);
            //var_dump($PostId);
            if ($PostId) {
                $PostName = get_post_title($PostId);
                $link = get_link($PostId);
            } else {
                //tag arc
                $PostName = $TERM->slug2texoID($QV['post_slug'], true);
                $link = domain() . "//$QV[post_category]" . "/$QV[post_slug]";
                $link = preg_replace('/([^:])(\/{2,})/', '$1/', $link . "/");
                if (defined('HTML_EXT') && get_option('html_ext') == 'true') {
                    $link = substr($link, 0, strlen($link) - 1) . ".html"; //for .html
                }
            }
            $PostName = empty($PostName) ? "404" : $PostName;
            //$html.="<li><a href=\"" . $link . "\">$PostName</a></li>";
            $html.="<li>$PostName</li>";
        }


        if (isset($QV['product_category'])) {
            $name = $TERM->slug2texoID($QV['product_category'], true);
            $link = domain() . "//" . PPATH . "/$QV[product_category]";
            $link = preg_replace('/([^:])(\/{2,})/', '$1/', $link . "/");
            if (defined('HTML_EXT') && get_option('html_ext') == 'true') {
                $link = substr($link, 0, strlen($link) - 1) . ".html"; //for .html
            }
            if (!empty($QV['product_slug'])) {
                $html.="<li><a href=\"" . $link . "\">$name</a></li>";
            } else {
                $html.="<li>$name</li>";
            }
        }

        if (isset($QV['product_slug'])) {
            $PostId = slug2id($QV['product_slug']);
            //var_dump($PostId);
            if ($PostId) {
                $PostName = get_post_title($PostId);
                $link = get_link($PostId);
            } else {
                //tag arc
                $PostName = $TERM->slug2texoID($QV['product_slug'], true);
                $link = domain() . "//" . PPATH . "/$QV[product_category]" . "/$QV[product_slug]";
                $link = preg_replace('/([^:])(\/{2,})/', '$1/', $link . "/");
                if (defined('HTML_EXT') && get_option('html_ext') == 'true') {
                    $link = substr($link, 0, strlen($link) - 1) . ".html"; //for .html
                }
            }
            $PostName = empty($PostName) ? "404" : $PostName;
            //$html.="<li><a href=\"" . $link . "\">$PostName</a></li>";
            $html.="<li>$PostName</li>";
        }

        //    $default = array(
        //        'numberposts' => -1,
        //        'orderby' => 'menu_order',
        //        'order' => 'DESC',
        //        'exclude_field' => "ID",
        //        'exclude' => array(),
        //        'post_type' => 'page',
        //        'post_status' => 'published',
        //        'selectFields' => "ID,post_title",
        //        'parent' => $parent
        //    );
        //    $posts = get_posts($default);
    }
    $html.= "</ul>";

    echo $html;
}

function feature_image($post_id = false, $size = 698, $ImageID = false) {
    global $POST;
    if (!$post_id) {
        $post_id = $POST['ID'];
    }
    $html = "";
    $ides = get_featureImages($post_id); //array
    if ($ImageID) {
        $ides = $ImageID;
    }
    if (!empty($ides)) {
        $imgArray = array_filter($ides);
        $imguid = get_post($imgArray[0], "guid");
        $imgSrc = get_attachment_src($imguid['guid'], $size);
        $Img_alt = get_post_meta($imgArray[0], 'attachment_alter');
        $Img_caption = get_post_meta($imgArray[0], 'attachment_caption');
        $srcSet = get_attachment_src_set($imguid['guid'], "", $size);
        $size = imgSizes($imguid['guid']);
        $html = "<img title='$Img_caption' alt='$Img_alt' width='$size[0]' height='$size[1]' srcset='$srcSet[srcset]' sizes='$srcSet[sizes]' src='$imgSrc'  id='obj_img_$imgArray[0]' class='img-fluid'>";
    }
    return $html;
}

function single_post_gallery($multi = false) {
    $FirstImage = feature_image();
    $otherImage = frature_other_images();
    $mlt = "";
    $Mhtml = "";
    if ($multi && is_array($otherImage) && count($otherImage) > 0) {
        $mlt = 'multi';

        $Mhtml = "<div class='gallery-item-wrap'>";
        $n = 0;
        foreach ($otherImage as $img) {
            $n++;
            $ac = "";
            if ($n == 1) {
                $ac = "current";
            }
            $Mhtml .="<div class='gallery-item $ac'>";
            $Mhtml .=$img;
            $Mhtml .= "</div>";
        }
        $Mhtml .= "</div><script>
            $('.gallery-item').click(function(){
                $('.gallery-item').removeClass('current');
                $(this).addClass('current');
                var ob=$(this).find('img');
                $('.single-gallery-big').find('img').attr('srcset',ob.attr('data-orsrcset'));
                $('.single-gallery-big').find('img').attr('sizes',ob.attr('data-orsiz'));
            });
            </script>";
    }
    $html = "<div class='single-gallery $mlt'>";
    $html .= "<div class='single-gallery-big'>";
    $html .=$FirstImage;
    $html .= "</div>";
    $html .= $Mhtml;
    $html .= "</div>";
    return $html;
}

function frature_other_images($post_id = false, $size = 100) {
    global $POST;
    if (!$post_id) {
        $post_id = $POST['ID'];
    }
    $ides = get_featureImages($post_id); //array
    $imgArray = array_filter($ides);
    unset($imgArray[0]);
    $imgSrcArr = array();
    if (!empty($imgArray)) {
        foreach ($imgArray as $imgID) {
            $imguid = get_post($imgID, "guid");
            $imgSrc = get_attachment_src($imguid['guid'], $size);
            $Img_alt = get_post_meta($imgID, 'attachment_alter');
            $Img_caption = get_post_meta($imgID, 'attachment_caption');
            $srcSet = get_attachment_src_set($imguid['guid'], "", 200);
            $orSrcSet = get_attachment_src_set($imguid['guid'], "");
            $imgSrcArr[] = "<img title='$Img_caption' srcset='$srcSet[srcset]' data-orsiz='$orSrcSet[sizes]' data-orsrcset='$orSrcSet[srcset]' sizes='$srcSet[sizes]' alt='$Img_alt'  src='$imgSrc'  id='obj_img_$imgID' class='img-fluid'>";
        }
    }


    return $imgSrcArr;
}

function get_post_terms_link($post_id = false, $texo = false, $rel = "true", $maxp = false, $sep = ',') {
    global $POST, $TERM;
    if (!$post_id) {
        $post_id = @$POST['ID'];
    }

    if ($rel == "false") {
        $terms = $TERM->texoListRow($texo);
    } else {
        $terms = get_post_terms($post_id, $texo);
    }

    $str = "";
    if (!empty($terms)) {
        $str = "<span class='post-term-list'>";
        $max = count($terms);
        if ($maxp) {
            $max = $maxp;
        }
        $n = 0;
        foreach ($terms as $term) {
            $n++;
            $link = get_term_link($term);
            $str.= "<span class='term-link-item'><a href='$link'>$term[name]</a></span>";
            if ($n < $max) {
                $str.=$sep;
            }
            if ($max == $n)
                break;
        }
        $str.="</span>";
    }
    return $str;
}

function tags($arg = array()) {
    $postID = false;
    //var_dump($arg);

    $argDf = array('texo' => 'tag', 'rel' => "true", 'max' => false, 'sep' => ", ");
    $arg = array_merge($argDf, $arg);
    return get_post_terms_link($postID, $arg['texo'], $arg['rel'], $arg['max'], $arg['sep']);
}

function get_post_terms($post_id = false, $texo = false) {
    global $DB, $POST;
    if (!$post_id) {
        $post_id = $POST['ID'];
    }
    $tx = "";
    if ($texo) {
        $tx = "tt.taxonomy='$texo' and ";
    }
    $wh = " $tx tr.object_id=$post_id";
    $cat = $DB->select("term_relationships as tr left join term_taxonomy as tt on tr.texo_id=tt.taxonomy_id left join terms as t on tt.term_id=t.term_id", 't.*,tt.taxonomy,description,taxonomy_id', $wh);

    $cR = array();
    foreach ($cat as $c) {
        if (!empty($c['term_id'])) {
            $cR[] = $c;
        }
    }
    return $cR;
}

function ReadMore_content($string) {
    //By shortcode
    $re = '/(\[more(.*?)\])(.*?)(\[\/more\])/s';
    preg_match_all($re, $string, $matches);

    //echo "<pre>";
    //var_dump($matches);
    // return;
    foreach ($matches[0] as $k => $moreStr) {

        $randID = rand();
        $sc = $matches[1][$k];
        $prmStr = $matches[2][$k];

        $solidContent = trim($matches[3][$k]);

        $paramStrArr = explode(" ", $prmStr);
        $prmArr = array();
        if (!empty($paramStrArr)) {
            $paramStrArr = array_filter($paramStrArr);
            foreach ($paramStrArr as $pp) {
                $expP = explode("=", $pp);
                $prmArr[trim($expP[0])] = trim($expP[1]);
            }
        }

        $showStr = "";
        $solidContentH = $solidContent;
        if (isset($prmArr['limit']) && !empty($prmArr['limit'])) {
            $showStr = substr($solidContent, 0, $prmArr['limit']);
            $solidContentH = substr($solidContent, $prmArr['limit'], strlen($solidContent));
        }

        //<span id='more_$randID' style='display:none' class=\"readMore\">$solidContentH

        $prefix = "$showStr<span class=\"moreTrig\" id=\"morbtn$randID\" onclick='$(\"#more_$randID\").show();$(this).hide();$(\"#lessmore_$randID\").show()'>...More </span>";
        $sufix = "<b class=\"lessTrig\" onclick='$(\"#more_$randID\").hide();$(\"#morbtn$randID\").show();$(this).hide();' style=\"display:none\" id=\"lessmore_$randID\">Less</b>";

        $moreStrModif = str_replace($solidContent, "<div id='more_$randID' style='display:none' class=\"readMore\">$solidContentH</div>", $moreStr);
        //var_dump($moreStrModif);
        // return;
        //$expDecode = $prefix . $sufix;
        $expDecode = str_replace(array("<p>$sc</p>", "<p>[/more]</p>"), array($prefix, $sufix), $moreStrModif);
        $expDecode = str_replace(array("$sc", "[/more]"), array($prefix, $sufix), $expDecode);

        @$dom = new DOMDocument();
        @$dom->preserveWhiteSpace = false;
        @$dom->loadHTML($expDecode, LIBXML_HTML_NOIMPLIED);
        $dom->formatOutput = true;
        $expDecode = @$dom->saveXML($dom->documentElement);

        $expDecode = str_replace(array('</p></p>', '<p><p>'), array('</p>', '<p>'), $expDecode);
        $expDecode = str_replace(array("&#13;", "</p></p>" . "<p> </p>", "<p/>", "<p><p>"), "", $expDecode);
        $string = str_replace($moreStr, $expDecode, $string);
        $string = str_replace(array("&#13;", "</p></p>" . "<p> </p>", "<p/>", "<p><p>"), "", $string);
        $string = preg_replace("/<p[^>]*>[\s|&nbsp;]*<\/p>/", '', $string);
        $string = str_replace(array('</p></p>', '<p><p>'), array('</p>', '<p>'), $string);




        //echo "<pre>";
        //var_dump($string);
        //return;
        //var_dump($sc);
//        $showStr = "";
//        if (isset($prmArr['limit']) && !empty($prmArr['limit'])) {
//            $showStr = substr($solidContent, 0, $prmArr['limit']);
//            $solidContent = substr($solidContent, $prmArr['limit'], strlen($solidContent));
//        }
//
//
//        $prefix = "$showStr<span class='moreTrig' id='morbtn$randID' onclick='$(\"#more_$randID\").show();$(this).hide();$(\"#lessmore_$randID\").show()'>...More </span>";
//        $sufix = "<span id='more_$randID' style='display:none' class=\"readMore\">$solidContent<b class='lessTrig' onclick='$(\"#more_$randID\").hide();$(\"#morbtn$randID\").show();$(this).hide();' style='display:none' id='lessmore_$randID'>Less</b></span>";
//
//
//        $expDecode = $prefix . $sufix;
//        $string = str_replace($moreStr, $expDecode, $string);
    }


    //---------------- On Solid Article 
    $re = '@\[\[(<span\s+class="readMore">(.*)</span>)\]\]@mi';
    preg_match_all($re, $string, $matches, PREG_SET_ORDER, 0);
    // var_dump($matches);
    foreach ($matches as $m) {
        $randID = rand();
        $mainFind = $m[0];
        $str = str_replace("<span", "<span id='more_$randID' style='display:none'", $m[1]);
        $str = str_replace("</span>", " <span class='lessTrig' onclick='$(\"#more_$randID\").hide();$(\"#morbtn$randID\").show();'>Less</span></span>", $str);
        $rep = "<span class='moreTrig' id='morbtn$randID' onclick='$(\"#more_$randID\").show();$(this).hide()'>...More </span>$str";
        $string = str_replace($mainFind, $rep, $string);
    }

    return $string;
}

function expand_shortcode($string) {
    preg_match_all("/(\[expand(.*?)\]).*?(\[\/expand\])/s", $string, $matches);
    // var_dump($matches);

    foreach ($matches[0] as $k => $expnds) {
        $sc = $matches[1][$k];
        $prmStr = $matches[2][$k];
        $paramStrArr = explode(" ", $prmStr);
        $prmArr = array();
        if (!empty($paramStrArr)) {
            $paramStrArr = array_filter($paramStrArr);
            foreach ($paramStrArr as $pp) {
                $expP = explode("=", $pp);
                $prmArr[trim($expP[0])] = trim($expP[1]);
            }
        }

        // var_dump($prmArr);
        $randID = rand();
        $arr = "<a class='clpsTrig' href='javascript:' onclick=\"$('#exp_$randID').slideToggle()\"></a>";
        if (@$prmArr['arrow'] == 'false') {
            $arr = "";
        }
        $prefix = "<div class='expand'>$arr<div id='exp_$randID' class='collapse'>";
        $sufix = "</div></div>";

        $expDecode = str_replace(array("<p>$sc</p>", "<p>[/expand]</p>"), array($prefix, $sufix), $expnds);
        $expDecode = str_replace(array("$sc", "[/expand]"), array($prefix, $sufix), $expDecode);

        $string = str_replace($expnds, $expDecode, $string);
        // print_r($string);
    }
    $string = str_replace("<p><div class='expand'>", "<div class='expand'>", $string);
    $string = str_replace("class='collapse'></p>", "class='collapse'>", $string);
    $string = str_replace("<p></div></div></p>", "</div></div>", $string);

    $string = preg_replace("/<p>([\s]+)<\/p>/", "", $string);

    return $string;
}

function site_logo($w = "", $h = "") {
    global $POST;
    $defaultSize = array();
    $logoSize = get_option('logo_size');
    if (!empty($logoSize)) {
        $defaultSize = unserialize($logoSize);
    } else {
        $defaultSize = array('w' => 120, 'h' => 120);
    }
    if (empty($w)) {
        if (is_array($defaultSize)) {
            if ($defaultSize['w'] != 'auto') {
                $w = $defaultSize['w'];
            }
        }
    }
    if (empty($h)) {
        if (is_array($defaultSize)) {
            if ($defaultSize['h'] != 'auto') {
                $h = $defaultSize['h'];
            }
        }
    }
    $htx = function_exists('HText') ? HText(1) : "";
    $customTitle = function_exists('logoTitle') ? logoTitle() : "";
    $logoStr = get_option('sitelogo');
    if (!empty($logoStr)) {
        $idArray = explode(",", $logoStr);
        $logoData = get_post($idArray[0], 'guid');
        $imgSrc = get_attachment_src($logoData['guid']);
        $imgInfo = get_post_metas($idArray[0]);
        $Img_alt = $imgInfo['attachment_alter'];
        $Img_caption = $imgInfo['attachment_caption'];

        if (!empty($customTitle)) {
            $Img_caption = $customTitle;
        }
        if (empty($Img_caption)) {
            $Img_caption = $htx;
        }
        //$Img_alt = get_post_meta($idArray[0], 'attachment_alter');
        //$Img_caption = get_post_meta($idArray[0], 'attachment_caption');
        //return "<img id='obj_img_$idArray[0]' width='$w' height='$h' class='img-fluid' src='$imgSrc' title='$Img_caption' alt='$Img_alt'>";

        return "<img id='obj_img_$idArray[0]' width='$w' height='$h' class='img-fluid site-logo' src='$imgSrc' alt='$Img_caption'>";
    }
}

function minifyCss($css) {

    // some of the following functions to minimize the css-output are directly taken
    // from the awesome CSS JS Booster: https://github.com/Schepp/CSS-JS-Booster
    // all credits to Christian Schaefer: http://twitter.com/derSchepp
    // remove comments
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    // backup values within single or double quotes
    preg_match_all('/(\'[^\']*?\'|"[^"]*?")/ims', $css, $hit, PREG_PATTERN_ORDER);
    for ($i = 0; $i < count($hit[1]); $i++) {
        $css = str_replace($hit[1][$i], '##########' . $i . '##########', $css);
    }
    // remove traling semicolon of selector's last property
    $css = preg_replace('/;[\s\r\n\t]*?}[\s\r\n\t]*/ims', "}\r\n", $css);
    // remove any whitespace between semicolon and property-name
    $css = preg_replace('/;[\s\r\n\t]*?([\r\n]?[^\s\r\n\t])/ims', ';$1', $css);
    // remove any whitespace surrounding property-colon
    $css = preg_replace('/[\s\r\n\t]*:[\s\r\n\t]*?([^\s\r\n\t])/ims', ':$1', $css);
    // remove any whitespace surrounding selector-comma
    $css = preg_replace('/[\s\r\n\t]*,[\s\r\n\t]*?([^\s\r\n\t])/ims', ',$1', $css);
    // remove any whitespace surrounding opening parenthesis
    $css = preg_replace('/[\s\r\n\t]*{[\s\r\n\t]*?([^\s\r\n\t])/ims', '{$1', $css);
    // remove any whitespace between numbers and units
    $css = preg_replace('/([\d\.]+)[\s\r\n\t]+(px|em|pt|%)/ims', '$1$2', $css);
    // shorten zero-values
    $css = preg_replace('/([^\d\.]0)(px|em|pt|%)/ims', '$1', $css);
    // constrain multiple whitespaces
    $css = preg_replace('/\p{Zs}+/ims', ' ', $css);
    // remove newlines
    $css = str_replace(array("\r\n", "\r", "\n"), '', $css);
    // Restore backupped values within single or double quotes
    for ($i = 0; $i < count($hit[1]); $i++) {
        $css = str_replace('##########' . $i . '##########', $hit[1][$i], $css);
    }
    return $css;
}

function minify_js($input) {
    $javascript = $input;
    require_once ABSPATH . "include/jsmin.php";

    $javascript = JSMin::minify($javascript);
    return $javascript;
}

/**
 * ----------------------------------------------------------------------------------------
 * Based on `https://github.com/mecha-cms/mecha-cms/blob/master/engine/plug/converter.php`
 * ----------------------------------------------------------------------------------------
 */
// Helper function(s) ...

define('X', "\x1A"); // a placeholder character

$SS = '"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'';
$CC = '\/\*[\s\S]*?\*\/';
$CH = '<\!--[\s\S]*?-->';
$TB = '<%1$s(?:>|\s[^<>]*?>)[\s\S]*?<\/%1$s>';

function __minify_x($input) {
    return str_replace(array("\n", "\t", ' '), array(X . '\n', X . '\t', X . '\s'), $input);
}

function __minify_v($input) {
    return str_replace(array(X . '\n', X . '\t', X . '\s'), array("\n", "\t", ' '), $input);
}

/**
 * =======================================================
 *  HTML MINIFIER
 * =======================================================
 * -- CODE: ----------------------------------------------
 *
 *    echo minify_html(file_get_contents('test.html'));
 *
 * -------------------------------------------------------
 */
function _minify_html($input) {
    return preg_replace_callback('#<\s*([^\/\s]+)\s*(?:>|(\s[^<>]+?)\s*>)#', function($m) {
        if (isset($m[2])) {
            // Minify inline CSS declaration(s)
            if (stripos($m[2], ' style=') !== false) {
                $m[2] = preg_replace_callback('#( style=)([\'"]?)(.*?)\2#i', function($m) {
                    return $m[1] . $m[2] . minify_css($m[3]) . $m[2];
                }, $m[2]);
            }
            return '<' . $m[1] . preg_replace(
                            array(
                        // From `defer="defer"`, `defer='defer'`, `defer="true"`, `defer='true'`, `defer=""` and `defer=''` to `defer` [^1]
                        '#\s(checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped)(?:=([\'"]?)(?:true|\1)?\2)#i',
                        // Remove extra white-space(s) between HTML attribute(s) [^2]
                        '#\s*([^\s=]+?)(=(?:\S+|([\'"]?).*?\3)|$)#',
                        // From `<img />` to `<img/>` [^3]
                        '#\s+\/$#'
                            ), array(
                        // [^1]
                        ' $1',
                        // [^2]
                        ' $1$2',
                        // [^3]
                        '/'
                            ), str_replace("\n", ' ', $m[2])) . '>';
        }
        return '<' . $m[1] . '>';
    }, $input);
}

function minify_html($input) {
    if (!$input = trim($input))
        return $input;
    global $CH, $TB;
    // Keep important white-space(s) after self-closing HTML tag(s)
    $input = preg_replace('#(<(?:img|input)(?:\s[^<>]*?)?\s*\/?>)\s+#i', '$1' . X . '\s', $input);
    // Create chunk(s) of HTML tag(s), ignored HTML group(s), HTML comment(s) and text
    $input = preg_split('#(' . $CH . '|' . sprintf($TB, 'pre') . '|' . sprintf($TB, 'code') . '|' . sprintf($TB, 'script') . '|' . sprintf($TB, 'style') . '|' . sprintf($TB, 'textarea') . '|<[^<>]+?>)#i', $input, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $output = "";
    foreach ($input as $v) {
        if ($v !== ' ' && trim($v) === "")
            continue;
        if ($v[0] === '<' && substr($v, -1) === '>') {
            if ($v[1] === '!' && substr($v, 0, 4) === '<!--') { // HTML comment ...
                // Remove if not detected as IE comment(s) ...
                if (substr($v, -12) !== '<![endif]-->')
                    continue;
                $output .= $v;
            } else {
                $output .= __minify_x(_minify_html($v));
            }
        } else {
            // Force line-break with `&#10;` or `&#xa;`
            $v = str_replace(array('&#10;', '&#xA;', '&#xa;'), X . '\n', $v);
            // Force white-space with `&#32;` or `&#x20;`
            $v = str_replace(array('&#32;', '&#x20;'), X . '\s', $v);
            // Replace multiple white-space(s) with a space
            $output .= preg_replace('#\s+#', ' ', $v);
        }
    }
    // Clean up ...
    $output = preg_replace(
            array(
        // Remove two or more white-space(s) between tag [^1]
        '#>([\n\r\t]\s*|\s{2,})<#',
        // Remove white-space(s) before tag-close [^2]
        '#\s+(<\/[^\s]+?>)#'
            ), array(
        // [^1]
        '><',
        // [^2]
        '$1'
            ), $output);
    $output = __minify_v($output);
    // Remove white-space(s) after ignored tag-open and before ignored tag-close (except `<textarea>`)
    return preg_replace('#<(code|pre|script|style)(>|\s[^<>]*?>)\s*([\s\S]*?)\s*<\/\1>#i', '<$1$2$3</$1>', $output);
}

/**
 * =======================================================
 *  CSS MINIFIER
 * =======================================================
 * -- CODE: ----------------------------------------------
 *
 *    echo minify_css(file_get_contents('test.css'));
 *
 * -------------------------------------------------------
 */
function _minify_css($input) {
    // Keep important white-space(s) in `calc()`
    if (stripos($input, 'calc(') !== false) {
        $input = preg_replace_callback('#\b(calc\()\s*(.*?)\s*\)#i', function($m) {
            return $m[1] . preg_replace('#\s+#', X . '\s', $m[2]) . ')';
        }, $input);
    }
    // Minify ...
    return preg_replace(
            array(
        // Fix case for `#foo [bar="baz"]` and `#foo :first-child` [^1]
        '#(?<![,\{\}])\s+(\[|:\w)#',
        // Fix case for `[bar="baz"] .foo` and `url(foo.jpg) no-repeat` [^2]
        '#\]\s+#', '#\)\s+\b#',
        // Minify HEX color code ... [^3]
        '#\#([\da-f])\1([\da-f])\2([\da-f])\3\b#i',
        // Remove white-space(s) around punctuation(s) [^4]
        '#\s*([~!@*\(\)+=\{\}\[\]:;,>\/])\s*#',
        // Replace zero unit(s) with `0` [^5]
        '#\b(?:0\.)?0([a-z]+\b|%)#i',
        // Replace `0.6` with `.6` [^6]
        '#\b0+\.(\d+)#',
        // Replace `:0 0`, `:0 0 0` and `:0 0 0 0` with `:0` [^7]
        '#:(0\s+){0,3}0(?=[!,;\)\}]|$)#',
        // Replace `background(?:-position)?:(0|none)` with `background$1:0 0` [^8]
        '#\b(background(?:-position)?):(0|none)\b#i',
        // Replace `(border(?:-radius)?|outline):none` with `$1:0` [^9]
        '#\b(border(?:-radius)?|outline):none\b#i',
        // Remove empty selector(s) [^10]
        '#(^|[\{\}])(?:[^\s\{\}]+)\{\}#',
        // Remove the last semi-colon and replace multiple semi-colon(s) with a semi-colon [^11]
        '#;+([;\}])#',
        // Replace multiple white-space(s) with a space [^12]
        '#\s+#'
            ), array(
        // [^1]
        X . '\s$1',
        // [^2]
        ']' . X . '\s', ')' . X . '\s',
        // [^3]
        '#$1$2$3',
        // [^4]
        '$1',
        // [^5]
        '0',
        // [^6]
        '.$1',
        // [^7]
        ':0',
        // [^8]
        '$1:0 0',
        // [^9]
        '$1:0',
        // [^10]
        '$1',
        // [^11]
        '$1',
        // [^12]
        ' '
            ), $input);
}

function minify_css($input) {
    if (!$input = trim($input))
        return $input;
    global $SS, $CC;
    // Keep important white-space(s) between comment(s)
    $input = preg_replace('#(' . $CC . ')\s+(' . $CC . ')#', '$1' . X . '\s$2', $input);
    // Create chunk(s) of string(s), comment(s) and text
    $input = preg_split('#(' . $SS . '|' . $CC . ')#', $input, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $output = "";
    foreach ($input as $v) {
        if (trim($v) === "")
            continue;
        if (
                ($v[0] === '"' && substr($v, -1) === '"') ||
                ($v[0] === "'" && substr($v, -1) === "'") ||
                (substr($v, 0, 2) === '/*' && substr($v, -2) === '*/')
        ) {
            // Remove if not detected as important comment ...
            if ($v[0] === '/' && substr($v, 0, 3) !== '/*!')
                continue;
            $output .= $v; // String or comment ...
        } else {
            $output .= _minify_css($v);
        }
    }
    // Remove quote(s) where possible ...
    $output = preg_replace(
            array(
        '#(' . $CC . ')|(?<!\bcontent\:)([\'"])([a-z_][-\w]*?)\2#i',
        '#(' . $CC . ')|\b(url\()([\'"])([^\s]+?)\3(\))#i'
            ), array(
        '$1$3',
        '$1$2$4$5'
            ), $output);
    return __minify_v($output);
}

/**
 * =======================================================
 *  JAVASCRIPT MINIFIER
 * =======================================================
 * -- CODE: ----------------------------------------------
 *
 *    echo minify_js_(file_get_contents('test.js'));
 *
 * -------------------------------------------------------
 */
function _minify_js_($input) {
    return preg_replace(
            array(
        // Remove inline comment(s) [^1]
        '#\s*\/\/.*$#m',
        // Remove white-space(s) around punctuation(s) [^2]
        '#\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#',
        // Remove the last semi-colon and comma [^3]
        '#[;,]([\]\}])#',
        // Replace `true` with `!0` and `false` with `!1` [^4]
        '#\btrue\b#', '#false\b#', '#return\s+#'
            ), array(
        // [^1]
        "",
        // [^2]
        '$1',
        // [^3]
        '$1',
        // [^4]
        '!0', '!1', 'return '
            ), $input);
}

function minify_js_($input) {
    if (!$input = trim($input))
        return $input;
    // Create chunk(s) of string(s), comment(s), regex(es) and 
    global $SS, $CC;
    $input = preg_split('#(' . $SS . '|' . $CC . '|\/[^\n]+?\/(?=[.,;]|[gimuy]|$))#', $input, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    $output = "";
    foreach ($input as $v) {
        if (trim($v) === "")
            continue;
        if (
                ($v[0] === '"' && substr($v, -1) === '"') ||
                ($v[0] === "'" && substr($v, -1) === "'") ||
                ($v[0] === '/' && substr($v, -1) === '/')
        ) {
            // Remove if not detected as important comment ...
            if (substr($v, 0, 2) === '//' || (substr($v, 0, 2) === '/*' && substr($v, 0, 3) !== '/*!' && substr($v, 0, 8) !== '/*@cc_on'))
                continue;
            $output .= $v; // String, comment or regex ...
        } else {
            $output .= _minify_js_($v);
        }
    }
    return preg_replace(
            array(
        // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}` [^1]
        '#(' . $CC . ')|([\{,])([\'])(\d+|[a-z_]\w*)\3(?=:)#i',
        // From `foo['bar']` to `foo.bar` [^2]
        '#([\w\)\]])\[([\'"])([a-z_]\w*)\2\]#i'
            ), array(
        // [^1]
        '$1$2$4',
        // [^2]
        '$1.$3'
            ), $output);
}

// Create the function, so you can use it
function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

function getTemplateName($post_id = false) {
    global $POST;
    if ($post_id == false && isset($POST['ID'])) {
        $post_id = $POST['ID'];
    }
    if (!empty($post_id)) {
        return get_post_meta($post_id, 'post_template');
    } else {
        return false;
    }
}

function red404() {
    global $QV, $adminDir;
    if ($QV['page'] != $adminDir && $QV['page'] != "login.php") {
        if (!$GLOBALS['blog']) {
            if (!preg_match("/index.*/", $_SERVER['REQUEST_URI'])) {
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
                    //var_dump($redUrlr);
                    // exit;
                    //header("HTTP/1.0 404");
                    header('Location: /' . SUB_ROOT . $redUrlr); //404
                } else {
                    //echo "---> ok";
                    $GLOBALS['notFound'] = true;
                    $GLOBALS['title'] = "404-Page Not Found !";
                    $GLOBALS['template'] = '404.php';
                }
            } else {
                $GLOBALS['notFound'] = true;
                $GLOBALS['title'] = "404-Page Not Found !";
                $GLOBALS['template'] = '404.php';
            }
        }
    }
}

function red404_() {
    global $QV, $RWR, $adminDir;
    if ($QV['page'] != $adminDir && $QV['page'] != "login.php") {
        if (!preg_match("/index.*/", $_SERVER['REQUEST_URI'])) {
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
                $TITLE = "404-Page Not Found !";
                $template = '404.php';
            }
        }
    }
}

function same_term_page($order = 'ASC', $type = array('page', 'post')) {
    global $QV, $POST, $DB, $C_POST_TYPE;
    $term = @$QV['term'];
    $postID = @$POST['ID'];
    if (empty($term)) {
        //$term=@$QV['term'];
        $type = $POST['post_type'];
        $txosAr = $C_POST_TYPE[$type]['taxonomies'];
        $txosAr = implode("','", $txosAr);
        $dd = $DB->select("term_relationships AS tr LEFT JOIN term_taxonomy AS tt ON tr.texo_id = tt.taxonomy_id left join terms as tm on tt.term_id=tm.term_id ", "tm.slug", " tr.object_id =$postID AND tt.taxonomy IN ('$txosAr')");
        //var_dump($dd);
        $term = $dd[0]['slug'];
    }

    $default = array(
        'numberposts' => -1,
        'orderby' => 'post_date_gmt',
        'order' => $order,
        'exclude_field' => "ID",
        'post_type' => $type,
        'post_status' => 'published',
        'selectFields' => "*",
        'texonomy' => $term
    );
    // $childPages = get_posts($default);


    $posts = get_posts($default);
    return $posts;
}

function pagination($post_type = 'post', $itemPerPage = 8, $defaultTerm = "") {
    global $QV;
    $rqCate = isset($QV['term']) ? $QV['term'] : $defaultTerm;

    $itemPerPage = !empty($itemPerPage) ? $itemPerPage : 8;

    $argT = array(
        'texonomy' => $rqCate,
        'selectFields' => 'ID',
        'post_type' => $post_type
    );

    $postsT = get_posts($argT);
    $total = @count($postsT);
    //Pageination
    $maxPage = round($total / $itemPerPage);
    //var_dump($maxPage);
    $curPath = domain() . "/$QV[page]/";
    $curPath = trim_slash($curPath);
    //var_dump($curPath);
    $currentPage = isset($QV['page_no']) ? $QV['page_no'] : 1;
    $prevCls = "";
    $nextCls = "";
    if ($currentPage > 1) {
        $prevUrl = trim_slash($curPath . "/page/" . ($currentPage - 1));
    } else {
        $prevUrl = $curPath;
        $prevCls = "disabled";
    }
    if ($currentPage < $maxPage) {
        $nextUrl = trim_slash($curPath . "/page/" . ($currentPage + 1));
    } else {
        $nextUrl = $curPath;
        $nextCls = "disabled";
    }
    $pagiHtml = "<nav class='productPagination' aria-label=\"Page navigation\">
		<ul class=\"pagination\">
		<li class=\"page-item $prevCls\"><a class=\"page-link\" href=\"$prevUrl\">Previous</a></li>";
    for ($i = 1; $i <= $maxPage; $i++) {
        $sel = $i == $currentPage ? "active" : "";
        $url = "$curPath/page/$i";
        $url = trim_slash($url);
        if (defined('HTML_EXT') && get_option('html_ext') == 'true') {
            $url = substr($url, 0, strlen($url)) . ".html"; //for .html
        }
        $pagiHtml.="<li class=\"page-item $sel\"><a class=\"page-link\" href=\"$url\">$i</a></li>";
    }
    $pagiHtml.="<li class=\"page-item $nextCls\"><a class=\"page-link\" href=\"$nextUrl\">Next</a></li>
		</ul>
		</nav>";
    if ($total > $itemPerPage) {
        echo $pagiHtml;
    }
}
