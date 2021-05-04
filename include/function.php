<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php

//Core Function of CMS
//Options
function enableSlugCPType($arg = 'p') {
    global $C_POST_TYPE;
    $slgStr = get_option('enable_type_slug');
    if (!empty($slgStr)) {
        $enableSlug = unserialize(get_option('enable_type_slug'));
    } else {
        $enableSlug = array();
    }
    if (empty($enableSlug)) {
        $enableSlug = 'page,post';
        $texoLabels = 'category,tag';
        if ($arg == 'p') {
            return $postTypeStr;
        } else {
            return $texoLabels;
        }
    }
    $texoLabels = array();
    if (is_array($enableSlug)) {
        $enableSlug = array_filter($enableSlug);
    }
    foreach ($C_POST_TYPE as $k => $typ) {
        if (in_array($k, $enableSlug)) {
            $texoLabels = array_merge($texoLabels, $typ['taxonomies']);
        }
    }
    $postTypeStr = implode(",", $enableSlug);
    if ($arg == 'p') {
        return $postTypeStr;
    } else {
        return $texoLabels;
    }
}

function enableSlugCPTypeArr($arg = 'p') {
    global $C_POST_TYPE;
    $slgStr = get_option('enable_type_slug');
    if (!empty($slgStr)) {
        $enableSlug = unserialize(get_option('enable_type_slug'));
    } else {
        $enableSlug = array();
    }
    if (empty($enableSlug)) {
        $enableSlug = array('page', 'post');
        $texoLabels = 'category,tag';
        if ($arg == 'p') {
            return $postTypeStr;
        } else {
            return $texoLabels;
        }
    }
    $texoLabels = array();
    if (is_array($enableSlug)) {
        $enableSlug = array_filter($enableSlug);
    }
    foreach ($C_POST_TYPE as $k => $typ) {
        if (in_array($k, $enableSlug)) {
            $texoLabels = array_merge($texoLabels, $typ['taxonomies']);
        }
    }
    if ($arg == 'p') {
        return $enableSlug;
    } else {
        return $texoLabels;
    }
}

function reg($c) {
    $_SESSION['reg'][$c] = "";
}

function webApp() {
    if (defined('WEB_APP') && WEB_APP == true) {
        return true;
    } else {
        return false;
    }
}

function convert_to($source, $target_encoding) {
    // detect the character encoding of the incoming file
    $encoding = mb_detect_encoding($source, "auto");

    // escape all of the question marks so we can remove artifacts from
    // the unicode conversion process
    $target = str_replace("?", "[question_mark]", $source);

    // convert the string to the target encoding
    $target = mb_convert_encoding($target, $target_encoding, $encoding);

    // remove any question marks that have been introduced because of illegal characters
    $target = str_replace("?", "", $target);

    // replace the token string "[question_mark]" with the symbol "?"
    $target = str_replace("[question_mark]", "?", $target);

    return $target;
}

function clnContentEnc($string = "", $ch = "UTF-8") {

    $string = convert_to($string, $ch);
    //$string = _convert($string);
    //return mb_convert_encoding($string, $ch, "EUC-JP");
    return $string;
}

function current_mode(&$indx, &$v) {
    global $modes;
    $indx = "";
    $v = array();
    if (isset($_SESSION['mode']) && !empty($_SESSION['mode'])) {
        $indx = $_SESSION['mode'];
        $v = $modes[$indx];
        return true;
    }
    return false;
}

function get_home() {
    global $RWR;
    $var = 'front_page';
    $lng = $RWR->lng;
    if (!empty($lng)) {
        $var.="_" . $lng;
    }
    //var_dump($var);
    return get_option($var);
    //var_dump($lng);
}

function admin_login() {
    return isset($_SESSION[SESS_KEY]['login']) ? $_SESSION[SESS_KEY]['login'] : false;
}

function post_type() {
    return isset($_GET['post-type']) && !empty($_GET['post-type']) ? $_GET['post-type'] : "";
}

function get_post_template($post_id) {
    global $POST;
    if (empty($post_id)) {
        $post_id = $POST['ID'];
    }
    $template = get_post_meta($post_id, 'post_template');
    if (!empty($template)) {
        if (file_exists(THEME_DIR . current_theme_dir() . "/" . $template)) {
            return $template;
        } else {
            return false;
        }
    } else {

        $dfCpostTemplate = $POST['post_type'] . "-single.php";
        if (file_exists(THEME_DIR . current_theme_dir() . "/" . $dfCpostTemplate)) {
            return $dfCpostTemplate;
        } else {
            return false;
        }
    }
}

function get_post_template_cls($post_id) {
    global $POST;
    if (empty($post_id)) {
        $post_id = $POST['ID'];
    }
    $template = get_post_meta($post_id, 'post_template');
    if (!empty($template)) {
        if (file_exists(THEME_DIR . current_theme_dir() . "/" . $template)) {
            return str_replace(".php", "", $template);
        } else {
            return false;
        }
    }
}

function get_meta($meta_key, $meta_table, $id) {
    global $DB;
    $data = $DB->select('meta', "meta_value", "data_id=$id and meta_key='$meta_key' and meta_table='$meta_table'");
    if ($data) {
        return $data;
    } else {
        return false;
    }
}

function get_metas($meta_table, $id) {
    global $DB;
    $datas = $DB->select('meta', "meta_key,meta_value", "data_id=$id and meta_table='$meta_table'");
    $newData = array();
    if (!empty($datas)) {
        foreach ($datas as $data) {
            $newData[$data['meta_key']] = $data['meta_value'];
        }
    }

    if ($datas) {
        return $newData;
    } else {
        return false;
    }
}

function add_meta($meta_key, $meta_value, $meta_table, $id) {
    global $DB;
    $data = array(
        'data_id' => $id,
        'meta_key' => $meta_key,
        'meta_value' => $meta_value,
        'meta_table' => $meta_table
    );
    $res = $DB->insert('meta', $data);
    //var_dump($res);
    if ($res) {
        return $res;
    } else {
        $res = $DB->update('meta', $data, "data_id=$id and meta_key='$meta_key' and meta_table='$meta_table'");
        if ($res) {
            return $res;
        } else {
            return $DB->error;
        }
    }
}

function is_home($id = false) {
    global $POST;
    if (!$id) {
        $id = @$POST['ID'];
    }
    $homeID = get_option('front_page');
    if ($id == $homeID) {
        return true;
    }
    return false;
}

function plugin_path($file) {
    $info = pathinfo($file);
    $path = PLUGIN_PATH . $info['filename'];
    return $path;
}

function get_post_type_option($type = "") {
    global $C_POST_TYPE;
    $type = empty($type) ? $_GET['post-type'] : $type;
    return @$C_POST_TYPE[$type];
}

function add_header($str, $order = 1, $calback = false) {
    global $head_str_arr;
    $head_str_arr[] = array('str' => $str, 'order' => $order, 'calback' => $calback);
    return $head_str_arr;
    //return $custom_head.=$str;
}

function add_RW_role($role, $order = false) {
    global $RWR;
    return $RWR->setRole($role, $order);


    $RW_Role = unserialize(get_option('rewrite_role'));
    //var_dump($RWR);
    if ($order) {
        if (array_key_exists($order, $RW_Role)) {
            $order++;
            add_front_style_filter($role, $order);
        }
        $RW_Role[$order] = $role;
        $RWR->setRole($RW_Role);
        return $RW_Role[$order] = $role;
    } else {
        array_push($RW_Role, $role);
        $RWR->setRole($RW_Role);
        return array_push($RW_Role, $role);
    }
}

function add_custom_css($str, $order = false) {
    global $customCss;
    if ($order) {
        if (array_key_exists($order, $customCss)) {
            $order++;
            add_custom_css($str, $order);
        }
        return $customCss[$order] = $str;
    } else {
        return array_push($customCss, $str);
    }
}

function add_Img_sizes($str) {
    global $sizes;
    return array_push($sizes, $str);
}

function addMode($slug, $arr) {
    global $modes;
    return $modes[$slug] = $arr;
}

function add_custom_font($font = array(), $order = false) {
    //$font=array('title','name','css','cssID',order);//Font structure
    global $customFonts;
    if ($order) {
        if (array_key_exists($order, $customFonts)) {
            $order++;
            add_custom_font($font, $order);
        }
        return $customFonts[md5($order)] = $font;
    } else {
        return array_push($customFonts, $font);
    }
}

function add_front_style_filter($filter_callback, $order = false) {
    global $front_style_filter;
    $front_style_filter = !empty($front_style_filter) ? $front_style_filter : array();
    if ($order) {
        if (array_key_exists($order, $front_style_filter)) {
            $order++;
            add_front_style_filter($filter_callback, $order);
        }
        return $front_style_filter[$order] = $filter_callback;
    } else {
        return array_push($front_style_filter, $filter_callback);
    }
}

function add_front_script_filter($filter_callback, $order = false) {
    global $front_script_filter;
    if ($order) {
        if (array_key_exists($order, $front_script_filter)) {
            $order++;
            add_front_script_filter($filter_callback, $order);
        }
        return $front_script_filter[$order] = $filter_callback;
    } else {
        return array_push($front_script_filter, $filter_callback);
    }
}

function add_menu_location($menu = array()) {
    global $menu_location;
    //unset($menu_location);
    $menu_location[$menu['slug']] = $menu;
}

function AddMenuFilter($filter_callback, $order = false) {
    global $menuInject;
    if ($order) {
        if (array_key_exists($order, $menuInject)) {
            $order++;
            AddMenuFilter($filter_callback, $order);
        }
        return $menuInject[$order] = $filter_callback;
    } else {
        return array_push($menuInject, $filter_callback);
    }
}

function AddAttachment_renameFilter($filter_callback, $order = false, $extEvent = 'after') {
    global $attachment_file_rename_after, $attachment_file_rename_before;
    $attachment_file_rename_before = !empty($attachment_file_rename_before) ? $attachment_file_rename_before : array();
    if ($extEvent == 'after') {
        if ($order) {
            if (array_key_exists($order, $attachment_file_rename_after)) {
                $order++;
                AddAttachment_renameFilter($filter_callback, $order, $extEvent);
            }
            return $attachment_file_rename_after[$order] = $filter_callback;
        } else {
            return array_push($attachment_file_rename_after, $filter_callback);
        }
    } else {
        if ($order) {
            if (@array_key_exists($order, $attachment_file_rename_before)) {
                $order++;
                AddAttachment_renameFilter($filter_callback, $order, $extEvent);
            }
            return $attachment_file_rename_before[$order] = $filter_callback;
        } else {
            return array_push($attachment_file_rename_before, $filter_callback);
        }
    }
}

function add_src_filter($filter_callback, $order = false) {
    global $attachment_src_filter;
    if ($order) {
        if (array_key_exists($order, $attachment_src_filter)) {
            $order++;
            add_src_filter($filter_callback, $order);
        }
        return $attachment_src_filter[$order] = $filter_callback;
    } else {
        return array_push($attachment_src_filter, $filter_callback);
    }
}

function add_public_script($str, $order = false, $event = false) {
    if ($event) {
        return $_SESSION['publicScripts'][$event][$order] = $str;
    } else {
        return $_SESSION['publicScripts'][$order] = $str;
    }
}

function add_content_filter($filter_callback, $order = false) {
    global $contentfilte;
    if ($order) {
        if (array_key_exists($order, $contentfilte)) {
            $order++;
            add_content_filter($filter_callback, $order);
        }
        return $contentfilte[$order] = $filter_callback;
    } else {
        return array_push($contentfilte, $filter_callback);
    }
}

function add_footer($str, $order = 1, $calback = false) {
    global $foot_str_arr;
    $foot_str_arr[] = array('str' => $str, 'order' => $order, 'calback' => $calback);
    return $foot_str_arr;
}

function addListColumn($type, $arg) {
    global $listFields;
    if (!isset($listFields[$type])) {
        $listFields[$type] = array();
    }
    array_push($listFields[$type], $arg);
}

function att_real_dir($url) {
    if (empty($url)) {
        return false;
    }
    $fileParam = explode("/", $url);
    $lastIndex = count($fileParam) - 1;
    $fileDir = UPLOAD . $fileParam[($lastIndex - 2)] . "/" . $fileParam[($lastIndex - 1)] . "/" . $fileParam[$lastIndex];
    return $fileDir;
}

function url_exists($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // don't download content
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($ch);
    curl_close($ch);
    if ($result !== FALSE) {
        return true;
    } else {
        return false;
    }
}

function texoSel($param) {
    global $TERM;
    $TERM->texoSelect($param);
}

function texoChose($param) {
    // var_dump($inType);
    // exit;
    $usTexo = str_replace(" ", "_", $param[0]);
    $usTexo = str_replace("-", "_", $usTexo);
    ?>
    <div id='<?php echo $usTexo ?>TagArr'></div>
    <script>
        $(document).ready(function() {
    <?php echo $usTexo ?>tagList();
        });
                function <?php echo $usTexo ?>tagList(tt) {
                var data = {ajx_action: "tagList", texo: '<?php echo $param[0] ?>', ID: $("#ID").val(), inType: '<?php echo $param['inType'] ?>'};
                        jQuery.post('index.php', data, function(response) {
                        $("#<?php echo $usTexo ?>TagArr").html(response);
                        });
                }
    </script>
    <?php
}

function tagList() {
    global $TERM;
    $param = array();
    //var_dump($_POST);
    $param[] = $_POST['texo'];
    $param['ID'] = $_POST['ID'];
    $inType = $_POST['inType'];
    $TERM->texoChose($param, $inType);
}

function tx2Cp($tex) {
    global $C_POST_TYPE;
    if (!empty($tex)) {
        foreach ($C_POST_TYPE as $k => $cp) {
            $brk = false;
            if (isset($cp['taxonomies'])) {
                foreach ($cp['taxonomies'] as $tx) {
                    if ($tex == $tx) {
                        $pType = $k;
                        $brk = true;
                        break;
                    }
                }
            }
            if ($brk) {
                break;
            }
        }
        return $pType;
    }
    return false;
}

function custom_post_admin_title() {
    $type = post_type();
    global $C_POST_TYPE;
    if (empty($type)) {
        if (isset($_GET['tex'])) {
            $pType = "";
            foreach ($C_POST_TYPE as $k => $cp) {
                $brk = false;
                if (isset($cp['taxonomies'])) {
                    foreach ($cp['taxonomies'] as $tx) {
                        if ($_GET['tex'] == $tx) {
                            $pType = $k;
                            $brk = true;
                            break;
                        }
                    }
                }
                if ($brk) {
                    break;
                }
            }
            $post_typeArray = @$C_POST_TYPE[$pType];
            return $post_typeArray['label'];
        }
    } else {
        $post_typeArray = @$C_POST_TYPE[$type];
        if ($type == "attachment") {
            return "Media";
        }
    }
}

function add_custom_post($type, $arg) {
    //    $arg= array(
    //		'label' => "Pages",
    //		'menu_icon' => 'far fa-newspaper',
    //		'taxonomies' => array(),	
    //		'show_in_menu' => true,
    //		'menu_position' => 5,
    //		'show_in_admin_bar' => true,
    //		'show_in_nav_menus' => true
    //	)
    global $C_POST_TYPE;
    $C_POST_TYPE[$type] = $arg;
}

function add_option($optionName, $optionValue) {
    global $DB;
    $optionValue = clnContentEnc($optionValue);
    $data = array("option_name" => $optionName, "option_value" => $optionValue);
    return $DB->insert("options", $data);
}

function update_option($optionName, $optionValue) {
    global $DB;
    // var_dump($optionName);exit;
    $optionValue = clnContentEnc($optionValue);
    $data = array("option_value" => $optionValue);
    if (add_option($optionName, $optionValue)) {
        return true;
    } else {
        if ($DB->update("options", $data, "`option_name`='$optionName'")) {
            return true;
        } else {
            return false;
        }
    }
}

function get_option($optionName) {
    global $DB;
    $optionValue = $DB->select("options", "option_value", "option_name='$optionName'");
    return isset($optionValue[0]['option_value']) ? $optionValue[0]['option_value'] : false;
}

//term meta 

function get_term_meta($term_id, $meta_key = "") {
    global $DB;
    $val = $DB->select("`termmeta`", "meta_value", "term_id=$term_id and meta_key='$meta_key'");
    return @$val[0]['meta_value'];
}

function update_term_meta($term_id, $meta_key = "", $meta_value = "") {
    $meta_value = is_array($meta_value) ? serialize($meta_value) : $meta_value;
    $meta_value = str_replace("'", "&#039;", $meta_value);
    global $DB;
    if (($DB->rows("`termmeta`", "meta_id", "term_id=$term_id and meta_key='$meta_key'")) > 0) {
        $UpData = array("meta_value" => $meta_value);
        $upd = $DB->update("`termmeta`", $UpData, "term_id=$term_id and meta_key='$meta_key'");
        return true;
    } else {
        $inData = array("term_id" => $term_id, "meta_key" => $meta_key, "meta_value" => $meta_value);
        $ins = $DB->insert("`termmeta`", $inData);
        return true;
    }
}

function get_post_metas($post_id, $keys = false) {
    global $DB;
    $wh = "";
    if ($keys != false) {
        //$wh = "and meta_key='$keys'";
        if (is_array($keys)) {
            $keys = implode("','", $keys);
        }
        $wh = " and meta_key in('$keys')";
    }
    $val = $DB->select("`post-meta`", "meta_key,meta_value", "post_id=$post_id $wh");
    //var_dump($val);
    $arr = array();
    if ($val)
        foreach ($val as $r) {
            $arr[$r['meta_key']] = $r['meta_value'];
        }
    return $arr;
    // return $val;
}

//post Meta
function get_post_meta($post_id, $meta_key = "") {
    global $DB;
    $val = $DB->select("`post-meta`", "meta_value", "post_id=$post_id and meta_key='$meta_key'");
    return isset($val[0]['meta_value']) ? $val[0]['meta_value'] : false;
}

function update_post_meta($post_id, $meta_key = "", $meta_value = "") {
    global $DB;
    //$meta_value = preg_replace('/\s+/', ' ', $meta_value);// Remove White-spch
    $meta_value = str_replace("'", "&#039;", $meta_value);
    //$meta_value=Q2E($meta_value);
    //$meta_value=cln($meta_value);
    $meta_value = clnContentEnc($meta_value);

    if (($DB->rows("`post-meta`", "meta_id", "post_id=$post_id and meta_key='$meta_key'")) > 0) {
        $UpData = array("meta_value" => $meta_value);
        $upd = $DB->update("`post-meta`", $UpData, "post_id=$post_id and meta_key='$meta_key'");
        return true;
    } else {
        $inData = array("post_id" => $post_id, "meta_key" => $meta_key, "meta_value" => $meta_value);
        $ins = $DB->insert("`post-meta`", $inData);
        return true;
    }
}

function get_post_qv($post_id, $fields = "*", $parent = false) {
    global $C_POST_TYPE, $DB;

    // var_dump($C_POST_TYPE);

    $slugType = array(); //default in slug

    foreach ($C_POST_TYPE as $type => $arg) {

        if (isset($arg['in_slug']) && $arg['in_slug'] == true) {
            $slugType[] = $type;
        }
    }

    $enable_type_slug = @array_filter(unserialize(get_option('enable_type_slug')));
    if (!empty($enable_type_slug) && is_array($enable_type_slug)) {
        $slugType = array_unique(array_merge($slugType, $enable_type_slug));
        //var_dump($slugType);
    }

    //var_dump($slugType);

    $wh = "post_status <> 'trash' and ";

    if ($parent !== false) {
        $wh.=" post_parent=$parent and ";
    }

    if (!is_numeric($post_id)) {
        $wh.= " post_name='$post_id'";
    } else {
        $wh.= "ID=$post_id";
    }

    if (!empty($slugType)) {
        $typeStr = implode("','", $slugType);
        $wh.= " and post_type in('$typeStr')";
    }

    $post = $DB->select("post", $fields, $wh);
    return @$post[0];
}

function get_post($post_id, $fields = "*", $parent = false) {
    global $DB;
    $wh = "post_status <> 'trash' and ";

    if ($parent !== false) {
        $wh.=" post_parent=$parent and ";
    }

    if (!is_numeric($post_id)) {
        $wh.= "post_name='$post_id'";
    } else {
        $wh.= "ID=$post_id";
    }

    $post = $DB->select("post", $fields, $wh);
    return @$post[0];
}

function getExcerpt($text, $start = 0, $words = 20) {
    $text = str_replace("&nbsp;", "", $text);
    $words_in_text = str_word_count($text, 1);
    $result = array_slice($words_in_text, $start, $words);
    return implode(" ", $result) . "...";
}

function pagePredictor() {
    $html = "";
    global $QV, $POST, $C_POST_TYPE, $DB;
    $exc = "";
    $qs = "";
    if (isset($POST['ID'])) {
        $excid = $POST['ID'];
        $exc = array($excid);
        $qs = get_post_meta($excid, 'meta_h1_text'); //seo H1
        if (empty($qs)) {
            $qs = get_post_meta($exc, 'meta_title'); //SEO custom meta title
        }
    } elseif (isset($GLOBALS['term'])) {
        $term = $GLOBALS['term'];
        $qs = get_term_meta($term['term_id'], 'meta[meta_h1_text]');
        if (empty($qs)) {
            $qs = get_term_meta($term['term_id'], 'customTitle'); //SEO custom meta title
        }
    }
    $qs = str_replace("-", " ", $qs);
    if (isset($QV['page']) || is_home()) {
        if (empty($qs)) {
            $qs = urldecode($QV['page']);
            $qs = str_replace("-", " ", $qs);
        }

        $wCondition = " and (MATCH(post_content) AGAINST('$qs' IN BOOLEAN MODE) or MATCH(post_title) AGAINST('$qs' IN BOOLEAN MODE))";

        $availablePostType = array();
        $AvailableTexo = array();

        foreach ($C_POST_TYPE as $type => $PT) {
            if (!empty($PT['texo_show_in_menu'])) {
                //var_dump($PT['texo_show_in_menu']);
                foreach ($PT['texo_show_in_menu'] as $txo => $val) {
                    if ($val) {
                        $AvailableTexo[] = $txo;
                    }
                }
            }
        }

        $availablePostType = enableSlugCPTypeArr();

        $default = array(
            'numberposts' => 10,
            'orderby' => 'RAND()',
            'order' => '',
            'exclude_field' => "ID",
            'exclude' => $exc,
            'post_type' => $availablePostType,
            'post_status' => 'published',
            'selectFields' => "ID,post_title,post_content",
            'condition' => $wCondition
        );


        $posts = get_posts($default);
        if (is_array($posts)) {
            foreach ($posts as $element) {
                $hash = $element['post_title'];
                $unique_array[$hash] = $element;
            }

            if (!empty($unique_array)) {
                foreach ($unique_array as $post) {
                    $post['post_content'] = str_replace("[pagePredictor]", "", $post['post_content']);
                    //echo $post['ID'];
                    $html.= postSearchtemplate($post);
                }
            }
        }
    }
    return $html;
}

function short_details($str, $lt = 270) {
    global $TITLE;
    $ttlWord = explode(" ", $TITLE);
    $titleTxt = "<h2>" . $ttlWord[0] . " " . @$ttlWord[1] . "</h2>";
    $titleTxt = "";
    //var_dump($ttlWord[0]);
    $str_sort = trim(substr(strip_tags($str, "<h2>"), 0, $lt)); //Character Count
    //$str_sort = getExcerpt(strip_tags($str, "<h2>"), 0, $word);
    $htm = "<div class='details_Sort'>";
    $htm .= "$titleTxt $str_sort ...<br><a class='moreDetails' href=''>More</a>";
    $htm .= "</div>";
    $htm .= "<div class='details_full collapse'>$titleTxt  $str<a class='lessDetails' href=''>Less</a></div>";
    $htm .= "<script>
    $('.moreDetails').click(function(e){
    e.preventDefault();
    $('.details_Sort').hide();
    $('.details_full').show();
    $(this).hide();
    $('.lessDetails').show()
    });
    $('.lessDetails').click(function(e){
    e.preventDefault();
    $('.details_Sort').show();
    $('.details_full').hide();
    $(this).hide();
    $('.moreDetails').show()
    });
    </script>";
    return $htm;
}

function get_posts($arg = array()) {
    global $DB, $RWR, $TERM;
    $default = array(
        'numberposts' => -1,
        'orderby' => 'post_date_gmt',
        'order' => 'DESC',
        'exclude_field' => "ID",
        'exclude' => array(),
        'meta_key' => '',
        'meta_value' => '',
        'post_type' => 'post',
        'post_status' => 'published',
        'selectFields' => "*",
        'parent' => "",
        'condition' => "",
        'offset' => '',
        'texonomy' => ''
    );
    $arg = array_merge($default, $arg);
    $wh = "";
    if (!empty($arg['exclude'])) {
        $vals = implode(",", $arg['exclude']);
        $wh.=" $arg[exclude_field] NOT IN ($vals) and";
    }
    $wh.="";
    $limit = "";

    if ($arg['numberposts'] > 0) {
        $off = "";
        if ($arg['offset'] != "") {
            $off = "$arg[offset], ";
        }
        $limit = " LIMIT $off $arg[numberposts]";
    }
    if (!empty($arg['parent'])) {
        $wh.=" post_parent=$arg[parent] and";
    }

    if (!is_array($arg['post_type'])) {
        $wh.=" post_type='$arg[post_type]'";
    } else {
        $str = implode("','", $arg['post_type']);
        $wh.=" post_type IN('$str')";
    }

    $tbl = "";
    $sWh = "";
    if (multi_lang()) {
        if (class_exists('siteLanguages')) {
            $deff = get_option('deffLang');
            $lngs = new siteLanguages();

            if (isset($RWR->lng)) {
                $lng = $RWR->lng;
                $tbl = " left join `post-meta` on ID=post_id";
                $sWh.=" and meta_key='lng' and meta_value='$lng' ";
                //var_dump($deff);
            }
        }
    }

    $wh.=" and post_status='$arg[post_status]' $arg[condition] $sWh ORDER BY $arg[orderby] $arg[order] $limit";
    //WHERE 
    //
    //var_dump($wh);
    //var_dump($wh);
    if (!empty($arg['texonomy'])) {
        $txoId = $TERM->slug2texoID($arg['texonomy']);
        $txoCon = "texo_id=$txoId and ";
        $post = $DB->select("term_relationships as tr left join post as p on tr.object_id=p.ID", $arg['selectFields'], $txoCon . $wh);
    } else {
        //$post = $DB->select("post", $arg['selectFields'], $wh);
        $post = $DB->select("post $tbl", $arg['selectFields'], $wh);
    }

    if (is_bool($post)) {
        return $DB->error;
    }
    //return $post[0];
    //var_dump($post);
    return $post;
}

function unique_multidim_array($array, $key) {
    $temp_array = array();
    $i = 0;
    $key_array = array();
    foreach ($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}

function normalize_path($path) {
    $path = str_replace('\\', '/', $path);
    $path = preg_replace('|(?<=.)/+|', '/', $path);
    if (':' === substr($path, 1, 1)) {
        $path = ucfirst($path);
    }
    return $path;
}

function home_url() {
    global $RWR;
    $currentLang = $RWR->lng;
    $domain = domain(true);
    if (class_exists('siteLanguages')) {
        $lan = new siteLanguages();
        if ($currentLang != $lan->defaultLang) {
            $domain.="/" . $currentLang . "/";
        }
    }

    return trim_slash($domain);
}

function domain($site = false) {
    global $POST;
    //var_dump($POST);
    $domain = get_option("site_url");
    if ((!empty($domain) && !defined('ADMIN')) || $site) {
        $re = '/((http:\/\/)|(https:\/\/)).*/';
        preg_match_all($re, $domain, $matches, PREG_SET_ORDER, 0);
        //var_dump($matches);
        if (empty($matches)) {
            $domain = "http://" . $domain;
        }
        $domain = preg_replace('/([^:])(\/{2,})/', '$1/', $domain);
        return $domain;
    }
    $domain = DOMAIN . "/" . SUB_ROOT;
    //var_dump($domain);
    // Print the entire match result
//    if ($POST) {
//        $lng = get_post_meta($POST['ID'], 'lng');
//        $domain.="/" . strtolower($lng);
//    }

    $domain = preg_replace('/([^:])(\/{2,})/', '$1/', $domain);


    return $domain;
}

function trimSlash($str, $url = true) {
    $str = preg_replace('#/+#', '/', $str);
    if ($url == true) {
        $str = str_replace(':/', '://', $str);
    }
    return $str;
}

function trim_slash($str) {
    return preg_replace('/([^:])(\/{2,})/', '$1/', $str);
}

function current_theme_dir($echo = false) {
    $activeTheme = get_option('theme');
    if (empty($activeTheme)) {
        $activeTheme = DEFAULT_THEME;
    }
    //var_dump(THEME_DIR . $activeTheme);
    if (file_exists(THEME_DIR . $activeTheme) && file_exists(THEME_DIR . $activeTheme . "/style.css")) {
        $activeTheme = $activeTheme;
    } else {
        $directory = THEME_DIR;
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));
        foreach ($scanned_directory as $thmDir) {
            //var_dump($thmDir);
            if (is_dir(THEME_DIR . $thmDir) && file_exists(THEME_DIR . $thmDir . "/style.css")) {
                //var_dump($thmDir);
                $themeInfo = file_info(THEME_DIR . $thmDir . "/style.css");
                if (is_array($themeInfo) && isset($themeInfo['Theme Name'])) {
                    $activeTheme = $thmDir;
                    update_option('theme', $activeTheme);
                    break;
                }
            }
        }
    }
    if ($echo) {
        echo $activeTheme;
    } else {
        return $activeTheme;
    }
}

function current_theme_path() {
    return THEMES_PATH . current_theme_dir();
}

function add_support($suportName, $end = "back") {
    if ($end == "back") {
        if ($suportName == "bootstrap") {
            admin_add_script(array(
                'id' => "popper_min",
                'src' => COMMON_SC . "js/popper.min.js",
                'order' => 1
            ));
            admin_add_script(array(
                'id' => "bootstrap",
                'src' => COMMON_SC . "js/bootstrap.min.js",
                //'src'=>'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js',
                'order' => 2
            ));
            admin_add_style(array(
                'id' => "bootstrap-css",
                'href' => COMMON_SC . "css/bootstrap.min.css",
                'order' => 2
            ));
        } elseif ($suportName == "fontawesome") {
            admin_add_style(array(
                'id' => 'fontawesome',
                'href' => "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css",
                //'href' =>COMMON_SC."css/Fw_all.css",
                'order' => 2
            ));
            admin_add_style(array(
                'id' => 'fontawesome-pro',
                'href' => COMMON_SC . "webfonts/font-awesome-pro.css",
                'order' => 2
            ));
        } elseif ($suportName == "jquery") {
            admin_add_script(array(
                'id' => "jquery",
                'src' => COMMON_SC . "js/jquery-3.3.1.min.js",
                //'src'=>"https://code.jquery.com/jquery-3.4.1.min.js",
                'order' => 0,
                'position' => 'head'
            ));
        } elseif ($suportName == "TinyMce") {
            admin_add_script(array(
                'id' => "TinyMce",
                'src' => COMMON_SC . "tinymce/tinymce.min.js",
                'order' => 1
            ));
        } elseif ($suportName == "CKE") {
            admin_add_script(array(
                'id' => "cke",
                'src' => COMMON_SC . "ckeditor5/ckeditor.js",
                //'src' =>"https://cdn.ckeditor.com/ckeditor5/1.0.0-beta.1/classic/ckeditor.js",
                'order' => 1,
                'position' => 'head'
            ));
        } elseif ($suportName == "CKE_") {
            admin_add_script(array(
                'id' => "cke",
                'src' => COMMON_SC . "ckeditor/ckeditor.js",
                //'src' =>"https://cdn.ckeditor.com/ckeditor5/1.0.0-beta.1/classic/ckeditor.js",
                'order' => 1,
                'position' => 'head'
            ));
        } elseif ($suportName == "jqueryUI") {
            admin_add_script(array(
                'id' => "jquery_ui_js",
                'src' => "https://code.jquery.com/ui/1.12.1/jquery-ui.js",
                'order' => 1
            ));
            admin_add_style(array(
                'id' => "jquery_ui_css",
                'href' => "https//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css",
                'order' => 2
            ));
        } elseif ($suportName == "shortable") {
            admin_add_script(array(
                'id' => "shortable",
                'src' => COMMON_SC . "/js/jquery-sortable.js",
                'order' => 1,
                'position' => 'head'
            ));
        } elseif ($suportName == "fancybox") {
            admin_add_script(array(
                'id' => "fancybox-js",
                'src' => COMMON_SC . "fancybox/dist/jquery.fancybox.min.js",
                'order' => 1,
                'position' => 'head'
            ));
            admin_add_style(array(
                'id' => "fancybox-css",
                'href' => COMMON_SC . "fancybox/dist/jquery.fancybox.min.css",
                'order' => 10
            ));
        }
    } else {
        if ($suportName == "bootstrap") {
            add_script(array(
                'id' => "popper_min",
                'src' => COMMON_SC . "js/popper.min.js",
                'order' => 1
            ));
            add_script(array(
                'id' => "bootstrap",
                'src' => COMMON_SC . "js/bootstrap.min.js",
                //'src'=>'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js',
                'order' => 2
            ));
            add_style(array(
                'id' => "bootstrap-css",
                'href' => COMMON_SC . "css/bootstrap.min.css",
                'order' => 2
            ));
        } elseif ($suportName == "fontawesome") {
            //'href' => "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css",
            add_style(array(
                'id' => 'fontawesome',
                'href' => COMMON_SC . "css/Fw_all.css",
                'order' => 2,
                'position' => 'footer'
            ));
            add_style(array(
                'id' => 'fW-pro',
                'href' => COMMON_SC . "webfonts/font-awesome-pro.css",
                'order' => 2
            ));
        } elseif ($suportName == "jquery") {
            add_script(array(
                'id' => "jquery",
                'src' => COMMON_SC . "js/jquery-3.3.1.min.js",
                //'src'=>"https://code.jquery.com/jquery-3.4.1.min.js",
                'order' => 0,
                'position' => 'head'
            ));
        } elseif ($suportName == "TinyMce") {
            add_script(array(
                'id' => "TinyMce",
                'src' => COMMON_SC . "tinymce/tinymce.min.js",
                'order' => 1
            ));
        } elseif ($suportName == "CKE") {
            add_script(array(
                'id' => "cke",
                'src' => COMMON_SC . "ckeditor5/ckeditor.js",
                'order' => 1,
                'position' => 'head'
            ));
        } elseif ($suportName == "jqueryUI") {
            add_script(array(
                'id' => "jquery_ui_js",
                'src' => "https://code.jquery.com/ui/1.12.1/jquery-ui.js",
                'order' => 1
            ));
            add_style(array(
                'id' => "jquery_ui_css",
                'href' => "https//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css",
                'order' => 2
            ));
        } elseif ($suportName == "fancybox") {
            add_script(array(
                'id' => "fancybox-js",
                'src' => COMMON_SC . "fancybox/dist/jquery.fancybox.min.js",
                'order' => 1,
                'position' => 'head'
            ));
            add_style(array(
                'id' => "fancybox-css",
                'href' => COMMON_SC . "fancybox/dist/jquery.fancybox.min.css",
                'order' => 10
            ));
        } elseif ($suportName == "col") {
            add_style(array(
                'id' => "Buil_in_column",
                'href' => COMMON_SC . "css/col.css",
                'order' => 10
            ));
        } elseif ($suportName == "menu-asset") {
            add_style(array(
                'id' => "menu-css",
                'href' => COMMON_SC . "css/menu.css",
                'order' => 10
            ));
            add_script(array(
                'id' => "menu-js",
                'src' => COMMON_SC . "js/menu.js",
                'order' => 15,
                'position' => 'head'
            ));
        }
    }
}

function find_array_with_val($array, $value, $indx) {
    //var_dump($indx);
    foreach ($array as $index => $item) {
        if (isset($item[$indx]) && $item[$indx] == $value)
            return $index;
    }
    return false;
}

function getTbleField($pageSlug = "") {
    global $listFields;
    if ($pageSlug == "") {
        $pageSlug = $_GET['post-type'];
    }
    //var_dump($pageSlug);
    if (isset($listFields[$pageSlug])) {
        $f = $listFields[$pageSlug];
        usort($listFields[$pageSlug], function($a, $b) {
            return $a['order'] - $b['order'];
        });
        return $listFields[$pageSlug];
    } else {
        return array();
    }
}

function addBulkAction($post_Type = "", $action = array()) {
    global $bulkActions;
    return $bulkActions[$post_Type][] = $action;
}

function getBulkAction($pageSlug = "") {
    global $bulkActions;
    if ($pageSlug == "") {
        $pageSlug = @$_GET['post-type'];
    }
    //var_dump($pageSlug);
    if (isset($bulkActions[$pageSlug])) {
        $f = $bulkActions[$pageSlug];
        usort($bulkActions[$pageSlug], function($a, $b) {
            return $a['order'] - $b['order'];
        });
        return $bulkActions[$pageSlug];
    } else {
        return array();
    }
}

function get_texo_link() {
    //
    titleFilter($string);
}

function get_link($post_id) {

    global $C_POST_TYPE;
    $post = get_post($post_id, "ID,post_parent,post_type,post_name");
    $typeInfo = @$C_POST_TYPE[$post['post_type']];

    $lngMeta = '';
    if (class_exists('siteLanguages')) {
        $lngMetaS = strtolower(get_post_meta($post['ID'], 'lng'));
        $lngMeta = !empty($lngMetaS) ? $lngMetaS . "/" : '';
        $lng = new siteLanguages();
        if ($lng->defaultLang == $lngMetaS) {
            $lngMeta = "";
        }
    }

    if (get_home() == $post_id) {
        $hmUrl = trim_slash(domain(true) . "/" . $lngMeta);
        $lstChr = substr($hmUrl, -1);
        if ($lstChr == "/") {
            return substr($hmUrl, 0, -1);
        }
        return $hmUrl;
    }

    if (isset($typeInfo['custom_url'])) {
        if (is_bool($typeInfo['custom_url']) && $typeInfo['custom_url'] == true) {
            $clbk = $post['post_type'] . "_custom_url";
        } else {
            $clbk = $typeInfo['custom_url'];
        }

        if (function_exists($clbk)) {
            $link = $clbk($post);
        } else {
            //var_dump(get_home());
            if (get_home() == $post_id) {
                return domain(true) . "/" . $lngMeta;
            }

            $remParent = get_option('removeParentSlug');
            if (!empty($post['post_parent']) && $remParent == 'false') {
                $link = domain(true) . "/" . $lngMeta . get_slug($post['post_parent']) . "/" . get_slug($post_id);
            } else {
                $link = domain(true) . "/" . $lngMeta . get_slug($post_id);
            }
        }
        //page_custom_url($post);
    } else {
        //var_dump(get_home());
        if (get_home() == $post_id) {
            return domain(true) . "/" . $lngMeta;
        }

        $remParent = get_option('removeParentSlug');
        if (!empty($post['post_parent']) && $remParent == 'false') {
            $link = domain(true) . "/" . $lngMeta . get_slug($post['post_parent']) . "/" . get_slug($post_id);
        } else {
            $link = domain(true) . "/" . $lngMeta . get_slug($post_id);
        }
    }
    $url = preg_replace('/([^:])(\/{2,})/', '$1/', $link . "/");

    if (defined('HTML_EXT') && get_option('html_ext') == 'true') {
        $url = substr($url, 0, strlen($url) - 1) . ".html"; //for .html
    }
    if (function_exists('get_protocol')) {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = str_replace("http://", $protocol, $url);
    }

    return $url;
}

function get_term_link($term) {
    global $TERM;
    if (is_numeric($term)) {
        $term = $TERM->get_term($term);
    }
    $url = domain(true);
    //var_dump($term['term_group']);

    if (isset($term['term_group']) && $term['term_group'] != 0 && false) { //false for disable parent and child link
        $parent = $TERM->get_term($term['term_group']);
        $url.="/$parent[slug]";
    }
    if (isset($term['slug'])) {
        $url.="/$term[slug]/";
    }

    $url = preg_replace('/([^:])(\/{2,})/', '$1/', $url . "/");

    if (defined('HTML_EXT') && get_option('html_ext') == 'true') {
        $url = substr($url, 0, strlen($url) - 1) . ".html"; //for .html
    }

    return $url;
}

function get_slug($post_id) {
    $post = get_post($post_id, "post_name");
    return isset($post['post_name']) ? $post['post_name'] : "";
}

function slug2id($slug, $type = false, $bySlug = false, $strict = false) {
    global $DB;
    global $C_POST_TYPE;
    if ($type == false && $bySlug == true) {
        $type = array();
        foreach ($C_POST_TYPE as $t => $arg) {
            if (isset($arg['in_slug']) && $arg['in_slug'] == true) {
                $type[] = $t;
            }
        }
    }
    $wh = "BINARY post_name='$slug'";
    if ($type !== false) {
        if (is_array($type)) {
            $type = implode("','", $type);
            $wh .=" and post_type in('$type')";
        } else {
            $wh .=" and post_type='$type'";
        }
    }
    if ($strict) {
        // var_dump($wh);
        $wh.=" and post_status='published'";
    }
    //var_dump($wh);

    $post = $DB->select("post", 'ID', $wh);
    //var_dump($post);
    $postID = isset($post[0]['ID']) ? $post[0]['ID'] : false;
    //exit;
    return $postID;
}

function get_attachment_src($guid, $size = false) {
    global $attachment_src_filter, $sizes;
    @ksort($attachment_src_filter);
    $ErrorLog = new error_log();

    $sizes = array_filter(array_unique($sizes));
    asort($sizes);
    //var_dump($sizes);
    //$sizes = array_merge($sizes, $REG_IMG_Sizes);
    //SSL AND WWW Winth Media Url
    $siteUrl = get_option('site_url');
    $ssl = false;
    if (strpos($siteUrl, 'https://') !== false) {
        //var_dump("ssl");
        $ssl = true;
        $guid = strpos($guid, "https://") === false ? str_replace("http://", "https://", $guid) : $guid;
    }

    if (strpos($siteUrl, 'www.') === false) {
        $guid = str_replace("www.", "", $guid);
    } else {
        if (strpos($guid, 'www.') === false) {
            if ($ssl) {
                $guid = str_replace("https://", "https://www.", $guid);
            } else {
                $guid = str_replace("http://", "http://www.", $guid);
            }
        }
    }
    //SSL AND WWW With Media 



    $guidfDir = att_real_dir($guid);
    if (!file_exists($guidfDir)) {
        $ErrorLog->add_exception(array(time(), "Image Not Found in path ($guidfDir)"));
        return $guid;
    }

    $expectedSize = getClosest($size, $sizes);
    if ($size == false || !@getimagesize($guid)) {
        if ($attachment_src_filter) {
            foreach ($attachment_src_filter as $src_c_f) {
                if (function_exists($src_c_f)) {
                    $guid = $src_c_f($guid);
                }
            }
        }
        //var_dump('AS');
        return $guid; // if no size required
    }
    $info = pathinfo($guid);
    $dir = $info['dirname'];
    $fileName = $info['filename'];
    $ext = $info['extension'];
    $expectedUrl = "$dir/$fileName-$expectedSize.$ext";
    $fDir = att_real_dir($expectedUrl);
    if (!file_exists($fDir)) {
        $expectedUrl = "$dir/$fileName.$ext";
    }

    //var_dump($attachment_src_filter);

    if ($attachment_src_filter) {
        foreach ($attachment_src_filter as $src_c_f) {
            if (function_exists($src_c_f)) {
                $expectedUrl = $src_c_f($expectedUrl);
            }
        }
    }
    return $expectedUrl; //end
    //Need to work below=====================
    //var_dump(getimagesize($expectedUrl));
    if (!getimagesize($expectedUrl)) {
        //if not found expected attachment with file size
        //Than find last Smol big size
        for ($i = 4; $i >= 0; $i--) {
            $expectedSizeI = $sizes[$i];

            if ($expectedSizeI > $expectedSize) {
                continue;
            }
            $expectedUrl = "$dir/$fileName-$expectedSizeI.$ext";
            if (getimagesize($expectedUrl)) {
                break;
            } else {
                $expectedUrl = "$dir/$fileName.$ext";
                break;
            }
        }
    }
    return $expectedUrl;
}

//For shortcode SRCSET
function srcset($arg) {
    $attPost = get_post($arg['id'], 'guid');
    if (isset($attPost['guid'])) {
        $arr = get_attachment_src_set($attPost['guid']);
        return $arr['srcset'];
    }
}

function get_attachment_src_set($guid, $sizeStr = "", $maxsize = false) {
    global $attachment_src_filter, $sizes;
    @ksort($attachment_src_filter);
    $ErrorLog = new error_log();

    $sizes = array_filter(array_unique($sizes));
    asort($sizes);
    //var_dump($sizes);
    //$sizes = array_merge($sizes, $REG_IMG_Sizes);
    //SSL AND WWW Winth Media Url
    $siteUrl = get_option('site_url');
    $ssl = false;
    if (strpos($siteUrl, 'https://') !== false) {
        //var_dump("ssl");
        $ssl = true;
        $guid = strpos($guid, "https://") === false ? str_replace("http://", "https://", $guid) : $guid;
    }

    if (strpos($siteUrl, 'www.') === false) {
        $guid = str_replace("www.", "", $guid);
    } else {
        if (strpos($guid, 'www.') === false) {
            if ($ssl) {
                $guid = str_replace("https://", "https://www.", $guid);
            } else {
                $guid = str_replace("http://", "http://www.", $guid);
            }
        }
    }
    //SSL AND WWW With Media 


    $guidfDir = att_real_dir($guid);
    if (!file_exists($guidfDir)) {
        $ErrorLog->add_exception(array(time(), "Image Not Found in path ($guidfDir)"));
        return array('srcset' => "", 'sizes' => "");
    }

    $info = pathinfo($guid);
    $dir = $info['dirname'];
    $fileName = $info['filename'];
    $ext = $info['extension'];
    $orgImg = "$dir/$fileName.$ext";

    $desS = getimagesize($orgImg);
    $orgSize = $desS[0];
    if (!$desS) {
        $imgs = __getimagesize($orgImg);
        if (!$imgs) {
            return array('srcset' => "", 'sizes' => "");
        }
        $orgSize = $imgs[0];
    }
    $sizeArr = array($orgSize => $orgImg);
    $strSetArr = array("$orgImg {$orgSize}w");

    if (is_numeric($maxsize) && $maxsize < $orgSize) {
        $strSetArr = array();
    }
    $skip = false;
    foreach ($sizes as $size) {
        if ($skip) {
            continue;
        }
        if ($orgSize > $size) {
            if (is_numeric($maxsize) && $size > $maxsize) {
                $skip = true;
            }
            $expectedUrl = "$dir/$fileName-$size.$ext";
            $fDir = att_real_dir($expectedUrl);
            if (file_exists($fDir)) {
                $expectedUrl = "$dir/$fileName-$size.$ext";
                //var_dump($attachment_src_filter);
                if ($attachment_src_filter) {
                    foreach ($attachment_src_filter as $src_c_f) {
                        if (function_exists($src_c_f)) {
                            $expectedUrl = $src_c_f($expectedUrl);
                        }
                    }
                }
                $sizeArr[$size] = $expectedUrl;
                $strSetArr[] = "$expectedUrl {$size}w";
            }
        }
    }

    $max = max(array_keys($sizeArr));
    if (is_numeric($maxsize) && $size > $maxsize) {
        $max = $maxsize;
    }
    $min = min(array_keys($sizeArr));
    if ($sizeStr == "") {
        $sizesStr = "(max-width: {$max}px) 100vw, {$max}px";
    }
    return array('srcset' => implode(", ", $strSetArr), 'sizes' => $sizesStr);
}

function imgSizes($src) {
    $fDir = att_real_dir($src);
    if (file_exists($fDir)) {
        $desS = @getimagesize($src);
        if (!$desS) {
            $desS = __getimagesize($src);
        }
        return array($desS[0], $desS[1]);
    } else {
        return array(0, 0);
    }
}

function multi_lang() {
    $res = get_option('multiLangSwtc');
    if ($res == "true") {
        return true;
    } else {
        return false;
    }
}

function del_attachment($postID) {
    global $sizes;
    $post = get_post($postID);
    $guid = $post['guid'];
    $info = pathinfo($guid);
    $dir = $info['dirname'];
    $fileName = $info['filename'];
    $ext = $info['extension'];

    $dirParts = explode("/", $dir);
    $M = $dirParts[count($dirParts) - 1];
    $Y = $dirParts[count($dirParts) - 2];

    $fileDir = UPLOAD . "$Y/$M/$fileName";
    if (file_exists($fileDir . ".$ext")) {
        $res = unlink($fileDir . ".$ext"); //Deleted Main file 
    }
    foreach ($sizes as $size) {
        $fileDir = UPLOAD . "$Y/$M/$fileName-$size.$ext";
        if (file_exists($fileDir)) {
            unlink($fileDir);
        }
    }
    return $res;
}

function getClosest($search, $arr) {
    $closest = null;
    foreach ($arr as $item) {
        if ($closest === null || abs($search - $closest) > abs($item - $search)) {
            $closest = $item;
        }
    }
    return $closest;
}

function do_shortcode($string) {

    $string = clnContentEnc($string);

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

        preg_match_all("/[^\[\]\s]+/", $scode, $split);
        if (isset($split[0][0])) {
            $calF = trim($split[0][0]);


            if (function_exists($calF)) {
                $string = str_replace("<p>[" . $scode . "]</p>", $calF($attribuit), $string);
                $string = str_replace("[" . $scode . "]", $calF($attribuit), $string);
            }
        }
    }
    return $string;



    //Shortcode=========
    $regex = "/\[(.*?)\]/";
    preg_match_all($regex, $string, $matches);

    $attr = preg_split("/[\s,]+/", $matches[1][0]);
    //var_dump($attr);
    $attribuit = array();
    $i = 0;

    foreach ($attr as $at) {
        $i++;
        if ($i == 1) {
            continue;
        }
        $KnV = explode("=", $at);
        $attribuit[trim($KnV[0])] = isset($KnV[1]) ? trim($KnV[1]) : "";
    }//$attr
    //var_dump($attribuit);

    for ($i = 0; $i < count($matches[1]); $i++) {
        $match = $matches[1][$i];
        preg_match_all("/[^\[\]\s]+/", $match, $split);
        $calF = $split[0][0];
        //var_dump($calF);
        if (function_exists($calF)) {

            $string = str_replace($matches[0][$i], $calF($attribuit), $string);
        }
    }

    return $string;
}

//String Function===============================================================
/**
 * Trim Slashes
 *
 * Removes any leading/trailing slashes from a string:
 *
 * /this/that/theother/
 *
 * becomes:
 *
 * this/that/theother
 *
 * This is just an alias for PHP's native trim()
 *
 * @param	string
 * @return	string
 */
function trim_slashes($str) {
    return trim($str, '/');
}

//---------------------------------------------------
/**
 * Underscore
 *
 * Takes multiple words separated by spaces and underscores them
 *
 * @param	string	$str	Input string
 * @return	string
 */
function underscore($str) {
    return preg_replace('/[\s]+/', '_', trim(strtolower($str)));
}

//---------------------------------------------------
/**
 * Humanize
 *
 * Takes multiple words separated by the separator and changes them to spaces
 *
 * @param	string	$str		Input string
 * @param 	string	$separator	Input separator
 * @return	string
 */
function humanize($str, $separator = '_') {
    return ucwords(preg_replace('/[' . preg_quote($separator) . ']+/', ' ', trim(strtolower($str))));
}

//---------------------------------------------------
/**
 * Returns the English ordinal numeral for a given number
 *
 * @param  int    $number
 * @return string
 */
function ordinal_format($number) {
    if (!ctype_digit((string) $number) OR $number < 1) {
        return $number;
    }

    $last_digit = array(
        0 => 'th',
        1 => 'st',
        2 => 'nd',
        3 => 'rd',
        4 => 'th',
        5 => 'th',
        6 => 'th',
        7 => 'th',
        8 => 'th',
        9 => 'th'
    );

    if (($number % 100) >= 11 && ($number % 100) <= 13) {
        return $number . 'th';
    }

    return $number . $last_digit[$number % 10];
}

//---------------------------------------------------
/**
 * Strip Slashes
 *
 * Removes slashes contained in a string or in an array
 *
 * @param	mixed	string or array
 * @return	mixed	string or array
 */
function strip_slashes($str) {
    if (!is_array($str)) {
        return stripslashes($str);
    }

    foreach ($str as $key => $val) {
        $str[$key] = strip_slashes($val);
    }

    return $str;
}

//----------------------------------------------------
/**
 * Strip Quotes
 *
 * Removes single and double quotes from a string
 *
 * @param	string
 * @return	string
 */
function strip_quotes($str) {
    return str_replace(array('"', "'"), '', $str);
}

//----------------------------------------------------
/**
 * Q2E
 *
 * Converts single and double quotes to entities
 *
 * @param	string
 * @return	string
 */
function Q2E($str) {
    return str_replace(array("\'", "\"", "'", '"'), array("&#39;", "&quot;", "&#39;", "&quot;"), $str);
}

//---------------------------------------------------
/**
 * Reduce Double Slashes
 *
 * Converts double slashes in a string to a single slash,
 * except those found in http://
 *
 * http://www.some-site.com//index.php
 *
 * becomes:
 *
 * http://www.some-site.com/index.php
 *
 * @param	string
 * @return	string
 */
function reduce_double_slashes($str) {
    return preg_replace('#(^|[^:])//+#', '\\1/', $str);
}

//-------------------------------------------------------
/**
 * Reduce Multiples
 *
 * Reduces multiple instances of a particular character.  Example:
 *
 * Fred, Bill,, Joe, Jimmy
 *
 * becomes:
 *
 * Fred, Bill, Joe, Jimmy
 *
 * @param	string
 * @param	string	the character you wish to reduce
 * @param	bool	TRUE/FALSE - whether to trim the character from the beginning/end
 * @return	string
 */
function reduce_multiples($str, $character = ',', $trim = FALSE) {
    $str = preg_replace('#' . preg_quote($character, '#') . '{2,}#', $character, $str);
    return ($trim === TRUE) ? trim($str, $character) : $str;
}

//-------------------------------------------------------------
/**
 * Create a Random String
 *
 * Useful for generating passwords or hashes.
 *
 * @param	string	type of random string.  basic, alpha, alnum, numeric, nozero, unique, md5, encrypt and sha1
 * @param	int	number of characters
 * @return	string
 */
function random_string($type = 'alnum', $len = 8) {
    switch ($type) {
        case 'basic':
            return mt_rand();
        case 'alnum':
        case 'numeric':
        case 'nozero':
        case 'alpha':
            switch ($type) {
                case 'alpha':
                    $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    break;
                case 'alnum':
                    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    break;
                case 'numeric':
                    $pool = '0123456789';
                    break;
                case 'nozero':
                    $pool = '123456789';
                    break;
            }
            return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
        case 'unique': // todo: remove in 3.1+
        case 'md5':
            return md5(uniqid(mt_rand()));
        case 'encrypt': // todo: remove in 3.1+
        case 'sha1':
            return sha1(uniqid(mt_rand(), TRUE));
    }
}

//END String Functions=============
//
//
//
//
//
//
//Email=====Helpers====================================================
/**
 * Validate email address
 *
 * @deprecated	3.0.0	Use PHP's filter_var() instead
 * @param	string	$email
 * @return	bool
 */
function valid_email($email) {
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

//End Email helper=================
//
//
//
//File helpers=========================================================
/**
 * Write File
 *
 * Writes data to the file specified in the path.
 * Creates a new file if non-existent.
 *
 * @param	string	$path	File path
 * @param	string	$data	Data to write
 * @param	string	$mode	fopen() mode (default: 'wb')
 * @return	bool
 */
function write_file($path, $data, $mode = 'wb') {
    if (!$fp = @fopen($path, $mode)) {
        return FALSE;
    }

    flock($fp, LOCK_EX);

    for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result) {
        if (($result = fwrite($fp, substr($data, $written))) === FALSE) {
            break;
        }
    }

    flock($fp, LOCK_UN);
    fclose($fp);

    return is_int($result);
}

//------------------------------------------
function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }
    return $bytes;
}

//End File Helper==============
//
//
//
//
//
//
//Text Helpers==================================================================
/**
 * Word Limiter
 *
 * Limits a string to X number of words.
 *
 * @param	string
 * @param	int
 * @param	string	the end character. Usually an ellipsis
 * @return	string
 */
function word_limiter($str, $limit = 100, $end_char = '&#8230;') {
    if (trim($str) === '') {
        return $str;
    }

    preg_match('/^\s*+(?:\S++\s*+){1,' . (int) $limit . '}/', $str, $matches);

    if (strlen($str) === strlen($matches[0])) {
        $end_char = '';
    }

    return rtrim($matches[0]) . $end_char;
}

//--------------------------------------------------------
/**
 * Character Limiter
 *
 * Limits the string based on the character count.  Preserves complete words
 * so the character count may not be exactly as specified.
 *
 * @param	string
 * @param	int
 * @param	string	the end character. Usually an ellipsis
 * @return	string
 */
function character_limiter($str, $n = 500, $end_char = '&#8230;') {
    if (mb_strlen($str) < $n) {
        return $str;
    }

    // a bit complicated, but faster than preg_replace with \s+
    $str = preg_replace('/ {2,}/', ' ', str_replace(array("\r", "\n", "\t", "\x0B", "\x0C"), ' ', $str));

    if (mb_strlen($str) <= $n) {
        return $str;
    }

    $out = '';
    foreach (explode(' ', trim($str)) as $val) {
        $out .= $val . ' ';

        if (mb_strlen($out) >= $n) {
            $out = trim($out);
            return (mb_strlen($out) === mb_strlen($str)) ? $out : $out . $end_char;
        }
    }
}

//------------------------------------------------------
/**
 * High ASCII to Entities
 *
 * Converts high ASCII text and MS Word special characters to character entities
 *
 * @param	string	$str
 * @return	string
 */
function ascii_to_entities($str) {
    $out = '';
    for ($i = 0, $s = strlen($str) - 1, $count = 1, $temp = array(); $i <= $s; $i++) {
        $ordinal = ord($str[$i]);

        if ($ordinal < 128) {
            /*
              If the $temp array has a value but we have moved on, then it seems only
              fair that we output that entity and restart $temp before continuing. -Paul
             */
            if (count($temp) === 1) {
                $out .= '&#' . array_shift($temp) . ';';
                $count = 1;
            }

            $out .= $str[$i];
        } else {
            if (count($temp) === 0) {
                $count = ($ordinal < 224) ? 2 : 3;
            }

            $temp[] = $ordinal;

            if (count($temp) === $count) {
                $number = ($count === 3) ? (($temp[0] % 16) * 4096) + (($temp[1] % 64) * 64) + ($temp[2] % 64) : (($temp[0] % 32) * 64) + ($temp[1] % 64);

                $out .= '&#' . $number . ';';
                $count = 1;
                $temp = array();
            }
            // If this is the last iteration, just output whatever we have
            elseif ($i === $s) {
                $out .= '&#' . implode(';', $temp) . ';';
            }
        }
    }

    return $out;
}

function file_info($file, $type = 'path') {
    $info = array();
    if ($type == 'path') {
        //var_dump($file);
        $fp = fopen($file, 'r');
        // move to the 7th byte
        fseek($fp, 2);
        $data = fread($fp, 1200);   // read 8 bytes from byte 7
        fclose($fp);
    } else {
        $data = $file;
    }
    //var_dump($data);
    $lineOfInfo = explode("\n", $data);
    $patt = "/([^:%'\"*$\/\n]+):([^:%'\"*$\/\n]+)/";
    //:([a-zA-Z\d+.\s-]+)
    foreach ($lineOfInfo as $line) {
        if (preg_match($patt, $line, $M)) {
            //var_dump($M);
            $info[trim($M[1])] = trim($M[2]);
        }
    }
    return $info;
}

//------------------------------------------------------------
/**
 * Entities to ASCII
 *
 * Converts character entities back to ASCII
 *
 * @param	string
 * @param	bool
 * @return	string
 */
function entities_to_ascii($str, $all = TRUE) {
    if (preg_match_all('/\&#(\d+)\;/', $str, $matches)) {
        for ($i = 0, $s = count($matches[0]); $i < $s; $i++) {
            $digits = $matches[1][$i];
            $out = '';

            if ($digits < 128) {
                $out .= chr($digits);
            } elseif ($digits < 2048) {
                $out .= chr(192 + (($digits - ($digits % 64)) / 64)) . chr(128 + ($digits % 64));
            } else {
                $out .= chr(224 + (($digits - ($digits % 4096)) / 4096))
                        . chr(128 + ((($digits % 4096) - ($digits % 64)) / 64))
                        . chr(128 + ($digits % 64));
            }

            $str = str_replace($matches[0][$i], $out, $str);
        }
    }

    if ($all) {
        return str_replace(
                array('&amp;', '&lt;', '&gt;', '&quot;', '&apos;', '&#45;'), array('&', '<', '>', '"', "'", '-'), $str
        );
    }

    return $str;
}

//---------------------------------------------------------
/**
 * Code Highlighter
 *
 * Colorizes code strings
 *
 * @param	string	the text string
 * @return	string
 */
function highlight_code($str) {
    /* The highlight string function encodes and highlights
     * brackets so we need them to start raw.
     *
     * Also replace any existing PHP tags to temporary markers
     * so they don't accidentally break the string out of PHP,
     * and thus, thwart the highlighting.
     */
    $str = str_replace(
            array('&lt;', '&gt;', '<?', '?>', '<%', '%>', '\\', '</script>'), array('<', '>', 'phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose'), $str
    );

    // The highlight_string function requires that the text be surrounded
    // by PHP tags, which we will remove later
    $str = highlight_string('<?php ' . $str . ' ?>', TRUE);

    // Remove our artificially added PHP, and the syntax highlighting that came with it
    $str = preg_replace(
            array(
        '/<span style="color: #([A-Z0-9]+)">&lt;\?php(&nbsp;| )/i',
        '/(<span style="color: #[A-Z0-9]+">.*?)\?&gt;<\/span>\n<\/span>\n<\/code>/is',
        '/<span style="color: #[A-Z0-9]+"\><\/span>/i'
            ), array(
        '<span style="color: #$1">',
        "$1</span>\n</span>\n</code>",
        ''
            ), $str
    );

    // Replace our markers back to PHP tags.
    return str_replace(
            array('phptagopen', 'phptagclose', 'asptagopen', 'asptagclose', 'backslashtmp', 'scriptclose'), array('&lt;?', '?&gt;', '&lt;%', '%&gt;', '\\', '&lt;/script&gt;'), $str
    );
}

//-----------------------------------------------------
/**
 * Word Wrap
 *
 * Wraps text at the specified character. Maintains the integrity of words.
 * Anything placed between {unwrap}{/unwrap} will not be word wrapped, nor
 * will URLs.
 *
 * @param	string	$str		the text string
 * @param	int	$charlim = 76	the number of characters to wrap at
 * @return	string
 */
function word_wrap($str, $charlim = 76) {
    // Set the character limit
    is_numeric($charlim) OR $charlim = 76;

    // Reduce multiple spaces
    $str = preg_replace('| +|', ' ', $str);

    // Standardize newlines
    if (strpos($str, "\r") !== FALSE) {
        $str = str_replace(array("\r\n", "\r"), "\n", $str);
    }

    // If the current word is surrounded by {unwrap} tags we'll
    // strip the entire chunk and replace it with a marker.
    $unwrap = array();
    if (preg_match_all('|\{unwrap\}(.+?)\{/unwrap\}|s', $str, $matches)) {
        for ($i = 0, $c = count($matches[0]); $i < $c; $i++) {
            $unwrap[] = $matches[1][$i];
            $str = str_replace($matches[0][$i], '{{unwrapped' . $i . '}}', $str);
        }
    }

    // Use PHP's native function to do the initial wordwrap.
    // We set the cut flag to FALSE so that any individual words that are
    // too long get left alone. In the next step we'll deal with them.
    $str = wordwrap($str, $charlim, "\n", FALSE);

    // Split the string into individual lines of text and cycle through them
    $output = '';
    foreach (explode("\n", $str) as $line) {
        // Is the line within the allowed character count?
        // If so we'll join it to the output and continue
        if (mb_strlen($line) <= $charlim) {
            $output .= $line . "\n";
            continue;
        }

        $temp = '';
        while (mb_strlen($line) > $charlim) {
            // If the over-length word is a URL we won't wrap it
            if (preg_match('!\[url.+\]|://|www\.!', $line)) {
                break;
            }

            // Trim the word down
            $temp .= mb_substr($line, 0, $charlim - 1);
            $line = mb_substr($line, $charlim - 1);
        }

        // If $temp contains data it means we had to split up an over-length
        // word into smaller chunks so we'll add it back to our current line
        if ($temp !== '') {
            $output .= $temp . "\n" . $line . "\n";
        } else {
            $output .= $line . "\n";
        }
    }

    // Put our markers back
    if (count($unwrap) > 0) {
        foreach ($unwrap as $key => $val) {
            $output = str_replace('{{unwrapped' . $key . '}}', $val, $output);
        }
    }

    return $output;
}

if (!function_exists('mime_content_type')) {

    function mime_content_type($filename) {

        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            'webp' => 'image/webp',
        );

        $Arr = explode('.', $filename);
        $arP = array_pop($Arr);
        $ext = strtolower($arP);
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        } else {
            return 'application/octet-stream';
        }
    }

}

function compress_image($source_url, $destination_url, $quality) {
    $info = getimagesize($source_url);
    $info['mime'] = mime_content_type($source_url);

    if ($info['mime'] == 'image/jpeg')
        $image = imagecreatefromjpeg($source_url);

    elseif ($info['mime'] == 'image/gif')
        $image = imagecreatefromgif($source_url);

    elseif ($info['mime'] == 'image/png')
        $image = imagecreatefrompng($source_url);

    elseif ($info['mime'] == 'image/webp')
        $image = imagecreatefromwebp($source_url);

    if ($info['mime'] == 'image/webp') {
        imagewebp($image, $destination_url, $quality);
    } else {
        imagejpeg($image, $destination_url, $quality);
    }

    return $destination_url;
}

function __getimagesize($source_url) {
    $info = @getimagesize($source_url);
    if (isset($info[0])) {
        return array($info[0], $info[0]);
    }
    $info['mime'] = @mime_content_type($source_url);
    if (!$info['mime']) {
        return array(0, 0);
    }
    if ($info['mime'] == 'image/jpeg')
        $image = imagecreatefromjpeg($source_url);
    elseif ($info['mime'] == 'image/gif')
        $image = imagecreatefromgif($source_url);
    elseif ($info['mime'] == 'image/png')
        $image = imagecreatefrompng($source_url);
    elseif ($info['mime'] == 'image/webp')
        $image = imagecreatefromwebp($source_url);

    return array(@imagesx($image), @imagesy($image));
}

function resize_scal($src, $dst, $dest_imagex = false) {
    $filename = compress_image($src, $dst, 90);
    $info = getimagesize($src);
    $info['mime'] = mime_content_type($src);
    if (($info['mime'] == 'image/webp')) {
        $source_image = imagecreatefromwebp($filename);
    } else {
        $source_image = imagecreatefromjpeg($filename);
    }
    $source_imagex = imagesx($source_image);

    $source_imagey = imagesy($source_image);
    if (!$dest_imagex) {
        $dest_imagex = $source_imagex;
    }

    //$dest_imagex = isset($_POST['width']) ? $_POST['width'] : 750;
    //$dest_imagey = isset($_POST['height'])?$_POST['height']:250;

    $dest_imagey = @floor($source_imagey * ($dest_imagex / $source_imagex));

    $dest_image = imagecreatetruecolor($dest_imagex, $dest_imagey);
    imagecopyresampled($dest_image, $source_image, 0, 0, 0, 0, $dest_imagex, $dest_imagey, $source_imagex, $source_imagey);
    //WaterMark
    $col_transparent = imagecolorallocatealpha($dest_image, 0, 0, 0, 115);
    imagestring($dest_image, 1, 3, ($dest_imagey - 10), 'SiATEX BD LTD', $col_transparent);
    //WaterMark
    $ret = false;
    if (($info['mime'] == 'image/webp')) {
        $ret = imagewebp($dest_image, $dst, 90);
    } else {
        $ret = imagejpeg($dest_image, $dst, 90);
    }
    return $ret;
}

function resize($src, $dest, $desired_width, $quality = 75) {
    /* read the source image */
    $ext = @pathinfo($src, PATHINFO_EXTENSION);
    $ext = @strtolower($ext);
    if ($ext == "jpg")
        $source_image = @imagecreatefromjpeg($src);
    else if ($ext == "png")
        $source_image = @imagecreatefrompng($src);
    else if ($ext == "gif")
        $source_image = @imagecreatefromgif($src);
    else if ($ext == "jpeg")
        $source_image = @imagecreatefromjpeg($src);
    else if ($ext == "webp")
        $source_image = @imagecreatefromwebp($src);

    $width = @imagesx($source_image);
    $height = @imagesy($source_image);
    /* find the "desired height" of this thumbnail, relative to the desired width  */
    $desired_height = @floor($height * ($desired_width / $width));
    /* create a new, "virtual" image */
    $virtual_image = @imagecreatetruecolor($desired_width, $desired_height);
    /* copy source image at a resized size */
    @imagecopyresized($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);
    /* create the physical thumbnail image to its destination */
    if ($ext == 'webp') {
        @imagewebp($virtual_image, $dest, $quality);
    } else {
        @imagejpeg($virtual_image, $dest, $quality);
    }
}

function metaval2ID($key, $val) {
    global $DB;
    $val = $DB->select("`post-meta`", "post_id", "meta_value='$val' and meta_key='$key'");
    if ($val) {
        return $val[0]['post_id'];
    } else {
        return 0;
    }
}

function getClientIP() {
    if (isset($_SERVER)) {
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        if (isset($_SERVER["HTTP_CLIENT_IP"]))
            return $_SERVER["HTTP_CLIENT_IP"];
        return $_SERVER["REMOTE_ADDR"];
    }
    if (getenv('HTTP_X_FORWARDED_FOR'))
        return getenv('HTTP_X_FORWARDED_FOR');

    if (getenv('HTTP_CLIENT_IP'))
        return getenv('HTTP_CLIENT_IP');

    return getenv('REMOTE_ADDR');
}

function getUserIP() {
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = $_SERVER['REMOTE_ADDR'];

    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }

    return $ip;
}

function convertip($ip) {
    global $DB;

    //?fields=country,city,lat,lon
    $url = "http://ip-api.com/json/$ip";
    $content = file_get_contents_curl($url);
    $ob = json_decode($content);
    if (isset($ob->status) && $ob->status == 'success') {
        return $ob->city . "," . $ob->country;
    }


    $sql = 'SELECT 
		c.country 
		FROM 
		ip2nationCountries c,
		ip2nation i 
		WHERE 
		i.ip < INET_ATON("' . $ip . '") 
		AND 
		c.code = i.country 
		ORDER BY 
		i.ip DESC 
		LIMIT 0,1';

    list($countryName) = mysqli_fetch_row(mysqli_query($DB->conn, $sql));
    //$res=$DB->query($sql);
    $countryName = !empty($countryName) ? $countryName : "Unknown";
    //var_dump($countryName);
    return $countryName;


    //Way 1
    //$cont = @file_get_contents("http://api.hostip.info/get_html.php?ip=$ip");
    //return $cont;
    //Another Way
    // $ip = '94.23.27.166';
    $url = get_option('ip2c_api');
    $data = json_decode(@file_get_contents($url . $ip));
    $country = @trim($data->country_name);
    //$country=empty($matches[1])? "Unknown Country": trim(@$matches[1]);
    if (empty($country)) {
        return "Unknown Country";
    } else {
        $str = "";
        //        if (!empty($data->city)) {
        //            $str.=trim($data->city) . "/";
        //        }
        $str.=$country;
    }
    return $str;
}

function site_tagline() {
    return get_option('tag');
}

function site_name() {
    return get_option('site-name');
}

function getBrowser() {
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version = "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }

    // Next get the name of the useragent yes seperately and for good reason
    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif (preg_match('/Firefox/i', $u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif (preg_match('/Chrome/i', $u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif (preg_match('/Safari/i', $u_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif (preg_match('/Opera/i', $u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif (preg_match('/Netscape/i', $u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    }

    // finally get the correct version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }

    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
            $version = $matches['version'][0];
        } else {
            $version = $matches['version'][1];
        }
    } else {
        $version = $matches['version'][0];
    }

    // check if we have a number
    if ($version == null || $version == "") {
        $version = "?";
    }

    return array(
        'userAgent' => $u_agent,
        'name' => $bname,
        'version' => $version,
        'platform' => $platform,
        'pattern' => $pattern
    );
}

function getimgsize($url, $referer = '') {
    $headers = array(
        'Range: bytes=0-32768'
    );

    /* Hint: you could extract the referer from the url */
    if (!empty($referer))
        array_push($headers, 'Referer: ' . $referer);

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    curl_close($curl);

    $image = imagecreatefromstring($data);

    $return = array(imagesx($image), imagesy($image));

    imagedestroy($image);

    return $return;
}

function add__editor_bottom_object($arg) {
    global $__editor_bottom_object;
    array_push($__editor_bottom_object, $arg);
    return $__editor_bottom_object;
}

function add__statue_bar_object($arg) {
    global $__statue_bar_object;
    array_push($__statue_bar_object, $arg);
    return $__statue_bar_object;
}

function add_texo_meta($arg = array()) {
    global $__texo_meta;
    array_push($__texo_meta, $arg);
    return $__texo_meta;
}

function get_termMeta_field($tex, $meta) {
    global $__texo_meta;
    foreach ($__texo_meta as $fieldArr) {
        if (in_array($tex, $fieldArr['texos'])) {
            echo "<div class='termmeta-area'><label>$fieldArr[label]</label>$fieldArr[html] <span class=\"comment\">$fieldArr[comment]</span></div>";
        }
    }
    //var_dump($__texo_meta);
}

function term_slug2Id($slug) {
    global $DB;
    $termID = $DB->select('terms', "term_id", "BINARY slug='$slug'");
    if ($termID) {
        return $termID[0]['term_id'];
    }
}

//Blog-------------

function post_custom_url($post) {
    //var_dump();
    global $DB, $C_POST_TYPE, $TERM;
    $CPostInfo = $C_POST_TYPE[$post['post_type']];

    foreach ($CPostInfo['taxonomies'] as $indx => $texo) {
        if ($texo == 'tag') {
            unset($CPostInfo['taxonomies'][$indx]);
        }
    }

    $texo = $CPostInfo['taxonomies'][0];

    //var_dump($texo);

    $inittrm = $DB->select("term_relationships as tr left join term_taxonomy as tt on tr.texo_id=tt.taxonomy_id left join terms as t on tt.term_id=t.term_id ", "slug,t.term_id", "tr.object_id=$post[ID] and taxonomy='$texo'");

    $texoSlug = @$inittrm[0]['slug'];
    $slugTexo = false;
    if ($inittrm) {
        $termSlug = $inittrm[0]['slug'];
        $trm = $TERM->slug2term($termSlug);
        if (isset($trm['meta']['disableSlug']) && $trm['meta']['disableSlug'] == 'true') {
            $slugTexo = true;
            $texoSlug = "";
        }
    }

    //var_dump($slugTexo);    


    $structure = get_option('permalink');
    if (empty($structure)) {
        $structure = "%postname%";
    }

    $find = array("%category%", "%postname%");
    $replace = array($texoSlug, $post['post_name']);
    $pp = str_replace($find, $replace, $structure);

    $link = domain() . "/$pp";
    //$link = domain() . "/" . POST_PATH . "/$pp";
    //$link = domain() . "/" . POST_PATH . "/$texoSlug/$post[post_name]";
    return $link;
}

function page_custom_url($post) {
    if (!empty($post['post_parent'])) {

        $lngMeta = '';
        if (class_exists('siteLanguages')) {
            $lngMetaS = strtolower(get_post_meta($post['ID'], 'lng'));
            $lngMeta = !empty($lngMetaS) ? $lngMetaS . "/" : '';
            $lng = new siteLanguages();
            if ($lng->defaultLang == $lngMetaS) {
                $lngMeta = "";
            }
        }

        $remParent = get_option('removeParentSlug');
        if (!empty($post['post_parent']) && $remParent == 'false') {
            $link = domain(true) . "/" . $lngMeta . get_slug($post['post_parent']) . "/" . get_slug($post['ID']);
        } else {
            $link = domain(true) . "/" . $lngMeta . get_slug($post['ID']);
        }
        //var_dump($remParent);
        $url = preg_replace('/([^:])(\/{2,})/', '$1/', $link . "/");

        return $url;
    } else {
        //var_dump(get_home(), $post['ID']);
        if (get_home() == $post['ID']) {
            return domain(true) . "/" . $lngMeta;
        }

        return post_custom_url($post);
    }
}

function post_category_menu($arg = array()) {
    //product-group
    global $TERM, $DB;
    $default = array(
        'texo' => 'category',
        'class' => 'pageSideMenu',
        'current' => "",
        'li_class' => "",
        'link_prefx' => '<span class="fas fa-angle-right"></span>',
        'disable_empty' => true
    );

    //$arg = $default;
    $arg = $arg + $default;

    $ides = isset($arg['ides']) ? $arg['ides'] : false;
    //var_dump($arg);
    $groups = $TERM->texoListRow(@$arg['texo'], true, $ides);
    $htm = "<ul class='$arg[class]'>";
    foreach ($groups as $group) {
        $SubGroups = $TERM->texoListRow(@$arg['texo'], $group['taxonomy_id']);
        $subHtml = "";
        if (!empty($SubGroups)) {
            $subHtml.="<ul class='childCategory'>";
            foreach ($SubGroups as $SubGroup) {
                $Scurr = $arg['current'] == $SubGroup['slug'] ? "current" : "";
                //$link = domain() . "//" . POST_PATH . "/$SubGroup[slug]";
                $link = domain() . "//" . "/$SubGroup[slug]";
                $link = preg_replace('/([^:])(\/{2,})/', '$1/', $link . "/");
                if (defined('HTML_EXT') && get_option('html_ext') == 'true') {
                    $link = substr($link, 0, strlen($link) - 1) . ".html"; //for .html
                }
                $subHtml.="<li class='$Scurr $arg[li_class]'><a href=\"$link\">$arg[link_prefx]&nbsp;$SubGroup[name]</a></li>";
            }
            $subHtml.="</ul>";
            $curr = $arg['current'] == $group['slug'] ? "current" : "";
            //$Gllink = domain() . "//" . POST_PATH . "/$group[slug]";
            $Gllink = domain() . "//" . "/$group[slug]";
            $Gllink = preg_replace('/([^:])(\/{2,})/', '$1/', $Gllink . "/");
            $htm.="<li class='$curr $arg[li_class]'><a href=\"$Gllink\" class='parentCategoryName'>$arg[link_prefx]&nbsp;$group[name]<span class='PpArrow'></span></a>$subHtml</li>";
        } else {
            $curr = $arg['current'] == $group['slug'] ? "current" : "";
            $link = domain() . "//" . "/$group[slug]";
            //$link = domain() . "//" . POST_PATH . "/$group[slug]";
            $link = preg_replace('/([^:])(\/{2,})/', '$1/', $link . "/");
            if (defined('HTML_EXT') && get_option('html_ext') == 'true') {
                $link = substr($link, 0, strlen($link) - 1) . ".html"; //for .html
            }
            $htm.="<li class='$curr $arg[li_class]'><a href=\"$link\" class='parentCategoryName'>$arg[link_prefx]&nbsp;$group[name]</a></li>";
        }
    }
    $htm.="</ul>";
    return $htm;
}

function post_menu($menuarg) {
    global $TERM;

    $default = array(
        'texo' => 'category',
        'class' => 'product-cat-menu',
        'current' => "",
        'li_class' => "",
        'link_prefx' => '<span class="fas fa-angle-right"></span>'
    );
    //$arg = $default;
    $menuarg = $menuarg + $default;
    $arg = array(
        'texonomy' => $menuarg['texonomy'],
        'selectFields' => "ID,post_name,post_title"
    );
    $posts = get_row_post($arg, false);

    //var_dump($posts);
    //var_dump($arg);
    $groups = $TERM->texoListRow(@$arg['texo']);
    $htm = "<ul class='$menuarg[class]'>";
    foreach ($posts as $post) {
        $curr = $menuarg['current'] == $post['post_name'] ? "current" : "";
        //$link = domain() . "/" . PPATH . "/$menuarg[texonomy]/$post[post_name]";
        $link = get_link($post['post_name']);
        $link = preg_replace('/([^:])(\/{2,})/', '$1/', $link);
        $htm.="<li class='$curr $menuarg[li_class]'><a href=\"$link\">$menuarg[link_prefx]&nbsp;$post[post_title]</a></li>";
    }
    $htm.="</ul>";
    return $htm;
}

function get_row_post($arg, $pagination = false) {
    global $DB, $TERM;
    $default = array(
        'numberposts' => 10,
        'orderby' => 'post_date_gmt',
        'order' => 'DESC',
        'exclude_field' => "ID",
        'exclude' => array(),
        'post_type' => 'post',
        'post_status' => 'published',
        'selectFields' => "*",
        'parent' => "",
        'texonomy' => "",
        'texonomy2' => '',
        'custCond' => ''
    );
    $arg = array_merge($default, $arg);

    // var_dump($arg);

    $wh = "";
    if (!empty($arg['exclude'])) {
        $vals = implode(",", $arg['exclude']);
        $wh.=" $arg[exclude_field] NOT IN ($vals) and";
    }
    $wh.="";
    $limit = "";
    if ($arg['numberposts'] > 0) {
        $limit = " LIMIT $arg[numberposts]";
    }
    if (!empty($arg['parent'])) {
        $wh.=" post_parent=$arg[parent] and";
    }

    if (!is_array($arg['post_type'])) {
        $wh.=" post_type='$arg[post_type]'";
    } else {
        $str = implode("','", $arg['post_type']);
        $wh.=" post_type IN('$str')";
    }

    $wh.=" and post_status='$arg[post_status]' $arg[custCond] ORDER BY $arg[orderby] $arg[order] $limit";
    //WHERE 
    //
		//var_dump($wh);

    if (!empty($arg['texonomy'])) {
        $txoId = $TERM->slug2texoID($arg['texonomy']);
        $txoCon = "texo_id=$txoId and ";
        $post = $DB->select("term_relationships as tr left join post as p on tr.object_id=p.ID", $arg['selectFields'], $txoCon . $wh);
        if (!empty($arg['texonomy2'])) {
            $newArray = array();
            $txoId2 = $TERM->slug2texoID($arg['texonomy2']);
            $txoCon2 = "texo_id=$txoId2 and ";
            $post2 = $DB->select("term_relationships as tr left join post as p on tr.object_id=p.ID", $arg['selectFields'], $txoCon2 . $wh);

            foreach ($post2 as $p) {
                if (in_array($p, $post)) {
                    $newArray[] = $p;
                }
            }
            $post = $newArray;
        }
    } else {
        $post = $DB->select("post", $arg['selectFields'], $wh);
    }
    return $post;
}

function singlePostShow($id, $readMore = false) {
    ?>
    <article class="post-item <?php echo $readMore ? "short" : "" ?> ">
        <header>
            <h2>
                <?php
                if ($readMore) {
                    echo "<a href='" . get_link($id) . "'>" . get_post_title($id) . "</a>";
                } else {
                    echo get_post_title($id);
                }
                ?>
            </h2>
        </header>
        <p><?php
            if ($readMore) {
                echo substr(get_content($id), 0, 300);
                echo "<a href='" . get_link($id) . "'>..Read more</a>";
            } else {
                echo get_content($id);
            }
            ?>
            <?php EditLink(true, get_post($id, "ID,post_type")) ?>
    </article>
    <?php
}

//Feature images
//Meta field:feature_image;
function get_featureImages($post_id) {
    $idStrings = get_post_meta($post_id, "feature_image");
    if (empty($idStrings)) {
        return false;
    }
    $idArray = explode(",", $idStrings);
    return $idArray;
}

function get_featureIcon($post_id) {
    $idStrings = get_post_meta($post_id, "feature_icon_svg");
    if (!empty($idStrings)) {
        return $idStrings;
    }
    $idStrings = get_post_meta($post_id, "feature_icon");
    return $idStrings;
}

function get_related_post($id = false, $limit = 12, $postType = 'post', $fields = "ID,post_title") {
    global $POST;
    if (!$id) {
        $id = $POST['ID'];
        $postType = $POST['post_type'];
    }

    $terms = get_post_terms($id);
    if (!empty($terms)) {
        $term = $terms[0];
        $term = $term['slug'];
    } else {
        $term = "";
    }
    $arg = array(
        'numberposts' => $limit,
        'orderby' => 'rand()',
        'order' => 'DESC',
        'exclude_field' => "ID",
        'exclude' => array($id),
        'post_status' => 'published',
        'selectFields' => $fields,
        'post_type' => $postType,
        'texonomy' => $term
    );
    $posts = get_posts($arg);
    return $posts;
}

function relatedPostSlider($itemPP = 12, $id = false, $pType = 'post', $label = "Related Post") {
    $relatedPost = get_related_post($id, $itemPP, $pType);
    ?>
    <div class="row related-posts-wrap">
        <div class="col col-12 col-sm-12 w12">
            <label class="h3"><?php echo $label ?></label>
            <div class='relatedSlider-wrap'>
                <a href="javascript:void(0)" onclick="rPrev(this)" class="prev related-nav"><span></span></a>
                <div class="row relatedRow" data-left='0'>
                    <?php
                    $sizeIdea = 200;
                    foreach ($relatedPost as $post) {
                        $img = feature_image($post['ID'], 200);
                        $postTitle = get_post_title($post['ID']);
                        $link = get_link($post['ID']);
                        ?>
                        <div class="col w2 t4 m6 rel-item">
                            <div class="related-post-item">
                                <a href="<?php echo $link ?>"><div class="image-wrap"><?php echo $img ?></div></a>
                                <h3><a href="<?php echo $link ?>"><?php echo $postTitle ?></a></h3>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <a href='javascript:void(0)' onclick="rNext(this)" class="next related-nav"><span></span></a>
            </div>
        </div>
    </div>
    <script>
                let wrp = document.querySelector('.relatedSlider-wrap');
                wrp.addEventListener('touchstart', handleTouchStart, {passive: true});
                wrp.addEventListener('touchmove', handleTouchMove, {passive: true});
                var xDown = null;
                var yDown = null;
                function getTouches(evt) {
                return evt.touches || evt.originalEvent.touches; // browser API || jQuery
                }

        function handleTouchStart(evt) {
        const firstTouch = getTouches(evt)[0];
                xDown = firstTouch.clientX;
                yDown = firstTouch.clientY;
        }

        function handleTouchMove(evt) {
        if (!xDown || !yDown) {
        return;
        }

        var xUp = evt.touches[0].clientX;
                var yUp = evt.touches[0].clientY;
                var xDiff = xDown - xUp;
                var yDiff = yDown - yUp;
                if (Math.abs(xDiff) > Math.abs(yDiff)) {
        /*most significant*/
        if (xDiff > 0) {
        /* left swipe */
        //console.log('left swipe');
        rNext($(".next.related-nav"));
        } else {
        /* right swipe */
        //console.log('right swipe');
        rPrev($(".prev.related-nav"));
        }
        } else {
        if (yDiff > 0) {
        /* up swipe */
        } else {
        /* down swipe */
        }
        }
        /* reset values */
        xDown = null;
                yDown = null;
        }

        function rNext(_this) {
        var wrapW = $('.relatedSlider-wrap').width() + 30;
                col = Math.round(wrapW / ($(".rel-item").width() + 30));
                var wp = $('.relatedRow');
                var Ext = Number(wp.attr('data-left'));
                var t = $('.rel-item').length;
                if (Ext < (t - col)) {
        var aW = wrapW / col;
                let nxtC = col;
                if (t - (Ext + col) <= col) {
        nxtC = t - (Ext + col);
                $(_this).addClass('dsbl');
        }
        nxt = Ext + nxtC;
                wp.attr('data-left', nxt);
                wp.css('transform', 'translateX(-' + Math.round(nxt * aW) + 'px)');
                $(_this).parent().find('.prev').removeClass('dsbl');
        } else {
        $(_this).addClass('dsbl');
        }
        }

        function rPrev(_this) {
        var wrapW = $('.relatedSlider-wrap').width() + 30;
                col = Math.round(wrapW / ($(".rel-item").width() + 30));
                var wp = $('.relatedRow');
                var Ext = Number(wp.attr('data-left'));
                if (Ext > 0) {
        var aW = wrapW / col;
                let nxtC = col;
                if (Ext <= col) {
        nxtC = Ext;
                $(_this).addClass('dsbl');
        }
        nxt = Ext - nxtC;
                wp.attr('data-left', nxt);
                wp.css('transform', 'translateX(-' + Math.round(nxt * aW) + 'px)');
                $(_this).parent().find('.next').removeClass('dsbl');
        } else {
        $(_this).addClass('dsbl');
        }
        }
    </script>
    <?php
}

//Re-Write role Functions
//To add CUstom Re write role
//Not done  
function add_RWrole($regex = "", $placeIdentiti = "") {
    global $RW_Role;
    $new = array($regex, $placeIdentiti);
    array_push($RW_Role, $new);

    //    $CurrentRoles = get_option("rewrite_role");
    //    $CurrentRoles=  unserialize($CurrentRoles);
    //    //var_dump($CurrentRoles);
    //    if (is_array($RW_Role)) {
    //        //var_dump($CurrentRoles);
    //        $CurrentRolesAfterAdd = array_push($RW_Role, $new);
    //        update_option("rewrite_role", serialize($CurrentRolesAfterAdd));
    //    } else {
    //        $CurrentRolesAfterAdd = array($new);
    //        update_option("rewrite_role", serialize($CurrentRolesAfterAdd));
    //    }
}

//var_dump(unserialize(get_option("RWrole")));


function download($fullPath = "", $name = '') {
    if (is_file($fullPath)) {
        if ($fd = @fopen($fullPath, "r")) {
            $fsize = @filesize($fullPath);
            $path_parts = @pathinfo($fullPath);
            $ext = @strtolower($path_parts["extension"]);
            if ($name != "")
                $path_parts["basename"] = $name;
            switch ($ext) {
                case "pdf":
                    header("Content-type: application/pdf"); // add here more headers for diff. extensions
                    header("Content-Disposition: attachment; filename=\"" . $path_parts["basename"] . "\""); // use 'attachment' to force a download
                    break;
                default;
                    header("Content-type: application/octet-stream");
                    header("Content-Disposition: filename=\"" . $path_parts["basename"] . "\"");
            }
            header("Content-length: $fsize");
            header("Cache-control: private"); //use this to open files directly
            while (!feof($fd)) {
                $buffer = @fread($fd, 2048);
                echo $buffer;
            }
        }
        fclose($fd);
        return true;
    } else
        return false;
}

function htaccess_redir_ssl($url) {
    $siteUrl = get_option('site_url');
    if ($url == $siteUrl) {
        return;
    }
    $htLink = $url . "/";
    $htLink = preg_replace('/([^:])(\/{2,})/', '$1/', $htLink);
    $reWriteRole_ = "
			#START_SSLRedir
			RewriteEngine On 
			RewriteCond %{SERVER_PORT} 80 
			RewriteRule ^(.*)$ $htLink$1 [R,L]
			#END_SSLRedir
			";

    $hst = str_replace("www.", '', $_SERVER['HTTP_HOST']);
    $hstBlockPoint = str_replace(".", "\.", $hst);
    $reWriteRole = "
			#START_SSLRedir
			RewriteEngine on
			RewriteCond %{HTTP_HOST} !^www\.$hstBlockPoint$ [NC]
			RewriteRule ^(.*)$ https://www.$hst/$1 [R=301,L]
			#END_SSLRedir
			";

    $htaccContent = file_get_contents(ABSPATH . ".htaccess");

    $re = '~(#START_SSLRedir)(.*)(#END_SSLRedir)~s';
    $htaccContent = preg_replace($re, "", $htaccContent);

    $re = '/((https)|(http)).*/';
    preg_match_all($re, $url, $matches, PREG_SET_ORDER, 0);
    if ($matches) {
        $protocol = $matches[0][1];
        if ($protocol == "https") {
            $htaccContent = $reWriteRole . $htaccContent;
            file_put_contents(ABSPATH . ".htaccess", $htaccContent);
        } else {
            file_put_contents(ABSPATH . ".htaccess", $htaccContent);
        }
    } else {
        file_put_contents(ABSPATH . ".htaccess", $htaccContent);
    }
}

function get_template_info($post_id = false) {
    global $POST, $THEME;
    if ($post_id === false) {
        $post_id = @$POST['ID'];
    }
    $templateFile = THEME_DIR . current_theme_dir() . "/" . get_post_meta($post_id, 'post_template');

    return $THEME->extractThemeInfo($templateFile);
}

function cln($data, $md5 = false) {
    if (!is_array($data)) {
        if (get_magic_quotes_gpc()) {
            if ($md5 == 1)
                return md5(trim($data));
            return trim($data);
        }
        else {
            if ($md5 == 1)
                return md5(mysql_real_escape_string(trim($data)));
            return mysql_real_escape_string(trim($data));
        }
    }
    else {
        $clean_array = array();
        if ($data)
            foreach ($data as $key => $value) {
                $clean_array[$key] = cln($value, $md5);
            }
        return $clean_array;
    }
}

function file_get_contents_curl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

function reg_pre_script($srcFile) {
    //PRELOAD_DIR
    $info = pathinfo($srcFile);
    $baseName = $info['basename'];
    if (file_exists(PRELOAD_DIR . $baseName)) {
        if (filemtime($srcFile) > filemtime(PRELOAD_DIR . $baseName)) {
            copy($srcFile, PRELOAD_DIR . $baseName);
        }
    } else {
        //var_dump($srcFile);
        copy($srcFile, PRELOAD_DIR . $baseName);
    }
}

function file_upload_max_size() {
    static $max_size = -1;

    if ($max_size < 0) {
        // Start with post_max_size.
        $post_max_size = parse_size(ini_get('post_max_size'));
        if ($post_max_size > 0) {
            $max_size = $post_max_size;
        }

        // If upload_max_size is less, then reduce. Except if upload_max_size is
        // zero, which indicates no limit.
        $upload_max = parse_size(ini_get('upload_max_filesize'));
        if ($upload_max > 0 && $upload_max < $max_size) {
            $max_size = $upload_max;
        }
    }
    return $max_size;
}

function parse_size($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
    $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
    if ($unit) {
        // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
        return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
        return round($size);
    }
}

function post_tag_menu($arg = array()) {
    //product-group
    global $TERM, $DB, $QV;
    $default = array(
        'texo' => 'tag',
        'class' => 'tagList',
        'current' => "",
        'li_class' => "",
        'link_prefx' => '<span class="fas fa-angle-right"></span>',
        'disable_empty' => true
    );
    //$arg = $default;
    $arg = $arg + $default;
    //var_dump($arg);
    $groups = $TERM->texoListRow($arg['texo'], true);
    $htm = "<ul class='$arg[class]'>";
    foreach ($groups as $group) {

        $valCat = unserialize(get_term_meta($group['term_id'], 'sel_texo'));
        $catSlug = $QV['post_category'];
        $catID = $TERM->slug2texoID($catSlug);
        if (@!in_array($catID, $valCat)) {
            continue;
        }
        $catSlug = !empty($catSlug) ? $catSlug . "/" : "";

        $SubGroups = $TERM->texoListRow($arg['texo'], $group['taxonomy_id']);
        $subHtml = "";
        if (!empty($SubGroups)) {
            $subHtml.="<ul class='childCategory'>";
            foreach ($SubGroups as $SubGroup) {
                $Scurr = $arg['current'] == $SubGroup['slug'] ? "current" : "";
                $link = domain() . "//" . "/{$catSlug}$SubGroup[slug]";
                //$link = domain() . "//" . POST_PATH . "/{$catSlug}$SubGroup[slug]";

                $link = preg_replace('/([^:])(\/{2,})/', '$1/', $link . "/");
                if (defined('HTML_EXT') && get_option('html_ext') == 'true') {
                    $link = substr($link, 0, strlen($link) - 1) . ".html"; //for .html
                }
                $subHtml.="<li class='$Scurr $arg[li_class]'><a href=\"$link\">$arg[link_prefx]&nbsp;$SubGroup[name]</a></li>";
            }
            $subHtml.="</ul>";
            $curr = $arg['current'] == $group['slug'] ? "current" : "";
            $Gllink = domain() . "//" . "/{$catSlug}$group[slug]";
            //$Gllink = domain() . "//" . POST_PATH . "/{$catSlug}$group[slug]";

            $Gllink = preg_replace('/([^:])(\/{2,})/', '$1/', $Gllink . "/");
            $htm.="<li class='$curr $arg[li_class]'><a href=\"$Gllink\" class='parentCategoryName'>$arg[link_prefx]&nbsp;$group[name]<span class='PpArrow'></span></a>$subHtml</li>";
        } else {
            $curr = $arg['current'] == $group['slug'] ? "current" : "";
            $link = domain() . "//" . "/{$catSlug}$group[slug]";

            //$link = domain() . "//" . POST_PATH . "/{$catSlug}$group[slug]";
            $link = preg_replace('/([^:])(\/{2,})/', '$1/', $link . "/");
            if (defined('HTML_EXT') && get_option('html_ext') == 'true') {
                $link = substr($link, 0, strlen($link) - 1) . ".html"; //for .html
            }
            $htm.="<li class='$curr $arg[li_class]'><a href=\"$link\" class='parentCategoryName'>$arg[link_prefx]&nbsp;$group[name]</a></li>";
        }
    }
    $htm.="</ul>";
    return $htm;
}

function attachment_file_rename($old_path, $new_path) {
    global $attachment_file_rename_after, $attachment_file_rename_before;
    if ($attachment_file_rename_before) {
        foreach ($attachment_file_rename_before as $filterCallback) {
            if (function_exists($filterCallback)) {
                $res = $filterCallback($old_path, $new_path);
                $old_path = $res['old_path'];
                $new_path = $res['new_path'];
            }
        }
    }
    $ret = rename($old_path, $new_path);
    //var_dump($ret,$old_path,$new_path);
    if ($attachment_file_rename_after) {
        foreach ($attachment_file_rename_after as $filterCallback) {
            if (function_exists($filterCallback)) {
                $res = $filterCallback($new_path);
                $new_path = $res;
            }
        }
    }
    return $ret;
}

function DOMinner_HTML(DOMNode $element) {
    $innerHTML = "";
    $children = $element->childNodes;

    foreach ($children as $child) {
        $innerHTML .= $element->ownerDocument->saveHTML($child);
    }

    return $innerHTML;
}

function str_replace_limit($find, $replacement, $subject, $limit = 0) {
    if ($limit == 0)
        return str_replace($find, $replacement, $subject);
    $ptn = '/' . preg_quote($find, '/') . '/';
    return preg_replace($ptn, $replacement, $subject, $limit);
}

//SEARCH====================
function search_box() {
    global $QV;
    $searchString = '';
    if (isset($QV['search_string']) && !empty($QV['search_string']))
        $searchString = $QV['search_string'];
    $htm = "<div class='searchBox'>
                <form method='get'>
                    <input type='text' value='$searchString' name='q' class='searchbox-input'>
                    <button>Search</button>
                </form>
            </div>";
    return $htm;
}

function year() {
    return date('Y');
}

function search_result($qs = false) {
    global $C_POST_TYPE, $DB;
    //var_dump($C_POST_TYPE);
    if (!$qs) {
        global $QV;
        $qs = $QV['search_string'];
    }
    $qs = strtolower(trim($qs));

    $availablePostType = array();
    $AvailableTexo = array();

    foreach ($C_POST_TYPE as $type => $PT) {
        if (!empty($PT['texo_show_in_menu'])) {
            //var_dump($PT['texo_show_in_menu']);
            foreach ($PT['texo_show_in_menu'] as $txo => $val) {
                if ($val) {
                    $AvailableTexo[] = $txo;
                }
            }
        }
    }

    $availablePostType = enableSlugCPTypeArr();

    $condition = " and (post_name like '%$qs%' or
                             post_title like '%$qs%' or
                             post_content like '%$qs%' 
                             )";

    $default = array(
        'numberposts' => -1,
        'orderby' => 'post_date_gmt',
        'order' => 'DESC',
        'post_type' => $availablePostType,
        'post_status' => 'published',
        'selectFields' => "ID,post_title,post_content",
        'condition' => $condition,
    );

    echo search_box();
    $posts = get_posts($default);
    if (!empty($posts)) {
        foreach ($posts as $post) {
            echo postSearchtemplate($post);
        }
    }

    $TexoWh = "1";
    $TexoWh.=" and (tt.description like '%$qs%' or
                             t.slug like '%$qs%' or
                             t.name like '%$qs%' 
                             )";

    $texos = $DB->select("term_taxonomy as tt left join terms as t on tt.term_id=t.term_id", "*", "$TexoWh");
    if (!empty($texos)) {
        foreach ($texos as $texo) {
            echo postSearchtemplate($texo);
        }
    }
    if (empty($posts) && empty($texos)) {
        echo "<strong class='search-result-not-found'>Search result not found for -'$qs'.</strong>";
        echo "<strong class='search-again'>Try another keyword.</strong>";
        //echo search_box();
    }
}

function postSearchtemplate($post, $searchQ = false, $limit = 250) {

    if (isset($post['ID'])) {
        $link = get_link($post['ID']);
        $SearchItemtitle = get_post_meta($post['ID'], 'meta_title');
        if (empty($SearchItemtitle)) {
            $SearchItemtitle = $post['post_title'];
        }

        $content = get_post_meta($post['ID'], 'meta_description');
        if (empty($content)) {
            $content = content_filter($post['post_content']);
            $content = strip_tags($content);
        }

        if ($limit !== false && strlen($content) > $limit) {
            $content = substr($content, 0, $limit) . "<a href='$link'>...more</a>";
        }


        $htm = "<article class='search-item'>
            <header class='search-item-header'>
                <h3><a href='$link'>$SearchItemtitle</a></h3>
            </header>
            <div class='search-item-content'>$content</div>
          </article>";
    } elseif (isset($post['taxonomy_id'])) {
        //var_dump($post);


        $texoTitle = $post['name'];
        $Trmlink = get_term_link($post);
        $content = content_filter($post['description']);
        $content = strip_tags($content);

        if ($limit !== false && strlen($content) > $limit) {
            $content = substr($content, 0, $limit) . "<a href='$Trmlink'>...more</a>";
        }

        $htm = "<article class='search-item'>
            <header class='search-item-header'>
                <h3><a href='$Trmlink'>$texoTitle</a></h3>
            </header>
            <div class='search-item-content'>$content</div>
          </article>";
    }
    if ($searchQ) {
        $htm = str_replace($searchQ, "<strong>$searchQ</strong>", $htm);
    }
    return $htm;
}

function post_term($texo, $id = false) {
    global $POST;
    if ($id === false) {
        $id = $POST['ID'];
    }

    $termByPost = $DB->select("term_relationships tr left join term_taxonomy as tt on tr.texo_id=tt.taxonomy_id left join terms as t on tt.term_id=t.term_id", "t.slug", "tt.taxonomy='$texo' and tr.object_id=$id");
    //var_dump($termByPost);
    return $termByPost[0];
}
