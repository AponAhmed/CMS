<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php

function findIcon($ftype, $size = false) {
    global $fileformatIcon;
    //vd($fileformatIcon);
    foreach ($fileformatIcon as $icon => $formets) {
        if (in_array($ftype, $formets)) {
            $i = "<i class='$icon fa-$size'></i>";
        }
    }
    if (empty($i)) {
        $i = "<i class='far fa-file fa-$size'></i>";
    }
    return $i;
}

//Permission level==================

function webAppMenu($item) {
    if (defined('WEB_APP') && WEB_APP == true) {
        if (@$item['web_app'] == true) {
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
}

function menuPermission($item) {

    if (UG('S') || $item['privilege_label'] == 'all') {
        return true;
    } else {
        if (in_array(UG(), $item['privilege_label']) || array_key_exists($item['slug'], UP())) {
            return true;
        } else {
            return false;
        }
    }
}

function BodyPermission($slug) {
    global $adminMenu;
    $up = UP();
    if ($slug == 'dash') {
        return true;
    }
    $key = find_menu_with_slug($adminMenu, $slug);

    if (isset($_GET['post-type'])) {//For Custom post Permission
        $key = find_array_with_val($adminMenu, $_GET['post-type'], 'post_type');
    }

    $parentSlug = $adminMenu[$key]['parent_slug'];
    //var_dump($parentSlug);
    if (UG('S') || $adminMenu[$key]['privilege_label'] == 'all') {
        return true;
    } else {
        if (array_key_exists($slug, $up)) {
            return true;
        } elseif (array_key_exists($parentSlug, $up)) {
            return true;
        } else {
            return false;
        }
    }
    // var_dump($menuItem, $adminMenu);
}

//==================================

function editor_bottom($p) {
    global $__editor_bottom_object;
    $order = array_column($__editor_bottom_object, 'order');
    array_multisort($order, SORT_DESC, $__editor_bottom_object);

    $htm = "";
    foreach ($__editor_bottom_object as $obj) {
        if ($obj['append'] == 'both') {
            $htm.= $obj['html'];
        } elseif ($p == $obj['append']) {
            $htm.= $obj['html'];
        }
    }
    echo $htm;
}

function admin_bar() {
    global $adminDir, $metabox;
    require_once( ABSPATH . "$adminDir/parts/admin-bar.php");
}

function add_adminBarLink($array) {
    global $adminBarLink;
    array_push($adminBarLink, $array);
}

function adminBarLink() {
    global $adminBarLink;
    usort($adminBarLink, function($a, $b) {
        return $a['order'] - $b['order'];
    });
    $html = "";
    foreach ($adminBarLink as $link) {
        $html.="<li>$link[link]</li>";
    }
    echo $html;
}

function admin_sidebar() {
    global $adminDir, $metabox;
    require_once( ABSPATH . "$adminDir/parts/admin-sidebar.php");
}

function have_adminSubMenu($slug) {
    global $adminMenu;
    return find_array_with_val($adminMenu, $slug, "parent_slug");
}

function first_adminSubMenu($slug) {
    global $adminMenu;
    //    usort($adminMenu, function($a, $b) {
    //        return $a['order'] - $b['order'];
    //    });
    foreach ($adminMenu as $item) {
        if (!empty($item['parent_slug']) && $item['parent_slug'] == $slug) {
            return $item;
        }
    }
}

function adminTitle($echo = true) {
    global $adminMenu;
    $indx = find_array_with_val($adminMenu, @$_GET['l'], 'slug');
    $type = "";
    if (isset($_GET['post-type'])) {
        $type = $_GET['post-type'];
    }
    $Admin_title = "";
    $Admin_title.=ucfirst($type);
    $Admin_title.=isset($adminMenu[$indx]['menu_title']) ? " " . $adminMenu[$indx]['menu_title'] : "";

    if ($echo) {
        echo $Admin_title;
    } else {
        return $Admin_title;
    }
}

function admin_body() {
    global $adminDir, $metabox, $DB, $POSTS, $THEME, $LIST, $listFields, $POST, $POST_TYPE, $MENU, $TERM, $ATTACH, $LOAD;
    require_once( ABSPATH . "$adminDir/parts/admin-body.php");
}

function get_admin_page($l) {
    global $slugLng;
    if (array_key_exists($l, $slugLng)) {
        return $slugLng[$l];
    } else {
        return $l;
    }
}

function add_admin_menu($items = array()) {
    global $adminMenu;
    array_push($adminMenu, $items);
}

function add_metabox($array = array()) {
    //var_dump($array);
    global $metaBoxes, $metabox;
    array_push($metaBoxes, $array);
}

function admin_add_script($script = array()) {
    global $adminScripts;
    $adminScripts[] = $script;
}

function admin_add_style($style = array()) {
    global $adminStyles;
    $adminStyles[] = $style;
}

function admin_styles($echo = false) {
    global $adminStyles;
    usort($adminStyles, function($a, $b) {
        return $a['order'] - $b['order'];
    });
    $html = "";
    $filteredFile = unique_multidim_array($adminStyles, 'id');
    foreach ($filteredFile as $styleInfo) {
        $href = trimSlash($styleInfo['href']);
        $html.="<link id=\"$styleInfo[id]\" rel=\"stylesheet\" href=\"$href\">\n";
    }

    $html = admin_ssl_fixer($html);
    //var_dump($adminStyles);
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

function get_protocol() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    return $protocol;
}

function admin_ssl_fixer($content) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $re = '/((https)|(http)).*/';
    preg_match_all($re, $protocol, $matches, PREG_SET_ORDER, 0);
    if ($matches && $matches[0][1] == "https") {
        $re = '/(http:\/\/)/m';
        $content = preg_replace($re, "https://", $content, -1, $c);
        return $content;
    }
    return $content;
}

function rrmdir($src) {
    $dir = opendir($src);
    while (false !== ( $file = readdir($dir))) {
        if (( $file != '.' ) && ( $file != '..' )) {
            $full = $src . '/' . $file;
            if (is_dir($full)) {
                rrmdir($full);
            } else {
                unlink($full);
            }
        }
    }
    closedir($dir);
    return rmdir($src);
}

function touchDir($src, $time = false) {
    if ($time == false) {
        $time = time();
    }
    $dir = opendir($src);
    while (false !== ( $file = readdir($dir))) {
        if (( $file != '.' ) && ( $file != '..' )) {
            $full = $src . '/' . $file;
            if (is_dir($full)) {
                touchDir($full, $time);
            } else {
                touch($full, $time);
            }
        }
    }
    closedir($dir);
    return touch($src, $time);
}

function admin_script($echo = false, $position = "") {
    global $adminScripts;
    usort($adminScripts, function($a, $b) {
        return $a['order'] - $b['order'];
    });
    $html = "";
    $filteredFile = unique_multidim_array($adminScripts, 'id');
    foreach ($filteredFile as $styleInfo) {
        if ($position != "" && $position !== "footer") {
            if (isset($styleInfo['position']) && $styleInfo['position'] == $position) {
                $src = trimSlash($styleInfo['src']);
                $html.="<script src=\"$src\"></script>\n";
            }
        } else {
            if (@$styleInfo['position'] != "head") {
                $src = trimSlash($styleInfo['src']);
                $html.="<script src=\"$src\"></script>\n";
            }
        }
    }
    $html = admin_ssl_fixer($html);
    //var_dump($adminStyles);
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

function login_script($echo) {
    global $loginScript;
    $html = "";
    $filteredFile = unique_multidim_array($loginScript, 'id');
    foreach ($filteredFile as $styleInfo) {
        $html.="<link id=\"$styleInfo[id]\" rel=\"stylesheet\" href=\"$styleInfo[href]\">\n";
    }

    $html = admin_ssl_fixer($html);
    //var_dump($adminStyles);
    if ($echo) {
        echo $html;
    } else {
        return $html;
    }
}

function publishBox() {
    global $POST;
    ?>
    <div class="mBoxBody">
        <?php
        if (post_type() == "attachment") {
            if (!url_exists($POST['guid'])) {
                echo "Image File Not Found !";
                return;
            }
            $infoName = pathinfo($POST['guid']);
            $infoFile = getimagesize($POST['guid']);
            //$size = filesize($POST['guid']);
            $Y = date("Y", strtotime($POST['post_date_gmt']));
            $M = date("m", strtotime($POST['post_date_gmt']));
            $size = formatSizeUnits(filesize(UPLOAD . "$Y/$M/$infoName[basename]"));
            $FilePathIngo = pathinfo($POST['guid']);
            $initLength = strlen($FilePathIngo['filename']);
            ?>
            <div class="singleInfoFile">
                <label>File URL</label> 
                <input type="text" readonly="" class="form-control form-control-sm" value="<?php echo get_attachment_src($POST['guid']) ?>">
            </div>
            <div class="singleInfoFile">
                <label>Length of File Name :</label> 
                <?php echo $initLength >= 100 ? "<font color='red'>$initLength</font>" : "<font color='green'>$initLength</font>"; ?>
            </div>
            <div class="singleInfoFile">
                <label>File Name :</label><strong><?php echo $infoName['basename'] ?></strong>   
            </div>

            <div class="singleInfoFile">
                <label>File Type :</label><strong><?php echo $infoName['extension'] ?></strong>
            </div>
            <div class="singleInfoFile">
                <label>File Size :</label><strong><?php echo $size ?></strong>
            </div>
            <?php
            if ($infoFile) {
                ?>
                <div class="singleInfoFile"> 
                    <label>Dimensions :<?php echo $infoFile[0] . " × " . $infoFile[1] ?> </label>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="draftPrev">
                <button type="button" class="btn btn-cms-default" onclick="saveDraft(this)">Save to Draft</button>
                <button id="preview" class="btn btn-cms-default float-right">Show Preview</button>
            </div>
            <?php
        }
        ?>
    </div>
    <div class='mBoxFooter'>
        <a id="del" onclick="Act('del=' + $('#ID').val(), true, true)" href="javascript:"><i class="far fa-trash-alt"></i></a>
        <button class="btn btn-cms-primary float-right" name="publish" value="pp" onclick="Post(PostForm, 'publish', this)" type="button">Publish</button>
        <button class="btn btn-cms-default float-right" name="publish" value="pp" onclick="Post(PostForm, 'publish&update', this)" type="button" style="margin-right:10px;">Update</button>
    </div>
    <?php
}

function page_attr() {
    global $THEME, $POSTS, $POST;
    $parent = !empty($POST) ? $POST['post_parent'] : false;
    $template = get_post_meta(@$POST['ID'], 'post_template');
    //var_dump($template);
    ?>
    <div class="mBoxBody ">
        <label>Parent : </label>
        <?php echo $POSTS->parent_select($parent, "data[post_parent]", "custom-select custom-select-sm") ?>
        <?php echo $THEME->templateSelect($template, "post_template", "custom-select custom-select-sm", 'themeTemplate') ?>
        <label>Menu Order :</label>
        <input name="data[menu_order]" value="<?php echo @$POST['menu_order'] ?>" type="text" class="form-control form-control-sm">
    </div>
    <?php
}

function attachment_info() {
    global $POST;
    //    var_dump($POST);
    ?>
    <div class="mBoxBody">
        <input type="hidden" name="calback[]" value="attachmentSaveCallback">
        <label>Caption</label>
        <textarea name="meta[attachment_caption]" class="form-control form-control-sm"><?php echo get_post_meta($POST['ID'], 'attachment_caption') ?></textarea>
        <?php
        if (url_exists($POST['guid'])) {
            $info = __getimagesize($POST['guid']);
            if (!empty($info[0])) {
                ?>
                <label>Alternative Text</label>
                <input name="meta[attachment_alter]" value="<?php echo get_post_meta($POST['ID'], 'attachment_alter') ?>" type="text" class="form-control form-control-sm">
                <?php
            }
        }
        ?>
    </div>
    <?php
}

function attachmentSaveCallback() {
    global $DB, $ATTACH;
    //var_dump($_POST);
    $data = $_POST['data'];
    //exit;
    $id = $_POST['ID'];


    //var_dump($inputSlug);exit;

    $imgPost = get_post($id, "guid,post_date_gmt");
    $pathPart = pathinfo($imgPost['guid']);
    $timeStamp = strtotime($imgPost['post_date_gmt']);

    $inputSlug = titleFilter($data['post_title']);
    $inputSlug.="." . $pathPart['extension'];
    //File
    $rth = UPLOAD . date("Y", $timeStamp) . "/" . date("m", $timeStamp) . "/";

    $oldpath = $rth . $pathPart['basename'];
    $newpath = $rth . $inputSlug;
    $renamed = attachment_file_rename($oldpath, $newpath);
    //$renamed=true;
    //var_dump($renamed);
    //exit;
    $pstData = array();
    $pstData['post_title'] = $data['post_title'];
    $newpath = $pathPart['dirname'] . "/" . $inputSlug;
    if (($pathPart['basename'] != $inputSlug) && $renamed) {
        $pstData['guid'] = $newpath;
        $sizes = $ATTACH->attachmentSizes;
        foreach ($sizes as $size) {
            $nInfo = pathinfo($inputSlug);
            $oInfo = pathinfo($pathPart['basename']);
            //var_dump($nInfo,$oInfo);
            attachment_file_rename($rth . $oInfo['filename'] . "-$size.$oInfo[extension]", $rth . $nInfo['filename'] . "-$size.$nInfo[extension]");
        }
    }
    //===DB
    // var_dump($pstData);
    //exit;
    //$pstData = array('post_title' => $iData['post_title'], 'guid' => $newpath);
    //rename("/tmp/tmp_file.txt", "/home/user/login/docs/my_file.txt"); 
    $up = $DB->update('post', $pstData, "ID=$id");

    //Replace content By slug
    $detPosts = $DB->select("post", "ID,post_content", "post_content like '%obj_img_$id%'");
    foreach ($detPosts as $pst) {
        $content = $pst['post_content'];
        $dom = new DOMDocument();
        @$dom->loadHTML($content);

        # Find all <img> elements
        $imgTags = $dom->getElementsByTagName('img');
        foreach ($imgTags as $img) {
            //DOM 
            $src = $img->attributes->getNamedItem("src")->nodeValue;
            $cls = $img->attributes->getNamedItem("class")->nodeValue;
            $idstr = $img->attributes->getNamedItem("id")->nodeValue;
            $imgID = str_replace("obj_img_", "", $idstr);
            if ($id != $imgID) {
                continue;
            }
            $nwimgPost = get_post($imgID, "guid");
            $NwSrc = get_attachment_src($nwimgPost['guid']);

            $title = $_POST['meta']['attachment_caption'];
            $alt = $_POST['meta']['attachment_alter'];

            $modiImg = "<img id=\"$idstr\" class=\"$cls\" src=\"$NwSrc\" alt=\"$alt\" title=\"$title\">";

            $pattern = "/<img[^>]*" . $idstr . "[^>]*>/";
            $content = preg_replace($pattern, $modiImg, $content);
        }
        $DB->update('post', array('post_content' => $content), "ID=$pst[ID]");
    }
}

function singleCrul() {
    // var_dump($_REQUEST);
    $link = $_REQUEST['ping_lnk'];
    if (!empty($link)) {
        $url = $link;
        $ch = @curl_init($url);
        @curl_setopt($ch, CURLOPT_NOBODY, true);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        @curl_exec($ch);
        $retcode = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
        //var_dump($retcode);
        @curl_close($ch);
        if (200 == $retcode) {
            $info = array('msg' => "Sitemap Uploaded", 'error' => "");
        } else {
            $info = array('msg' => "Sitemap not Uploaded, Status:$retcode", 'error' => "1");
        }
    }
    echo json_encode($info);
}

function sitemapCurl() {
    genarate_sitemap();
    $links = array();
    $links[] = get_option('sitemaplink');
    $links[] = get_option('sitemaplink1');
    $links[] = get_option('sitemaplink2');
    $s = 0;
    $ns = 0;
    // vd($n);exit;
    if (!empty($links)) {
        foreach ($links as $link) {
            if (!empty($link)) {
                $url = $link;
                $ch = @curl_init($url);
                @curl_setopt($ch, CURLOPT_NOBODY, true);
                @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                @curl_exec($ch);
                $retcode = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
                //var_dump($retcode);
                @curl_close($ch);
                if (200 == $retcode) {
                    $s++;
                } else {
                    $ns++;
                }
            }
        }

        if ($s) {
            $info = array('msg' => "Sitemap Uploaded", 'error' => "");
        } else {
            $info = array('msg' => "Sitemap not Uploaded", 'error' => "1");
        }
        echo json_encode($info);
    }
}

function aksort(&$array, $valrev = false, $keyrev = false) {
    if ($valrev) {
        arsort($array);
    } else {
        asort($array);
    }
    $vals = array_count_values($array);
    $i = 0;
    foreach ($vals AS $val => $num) {
        $first = array_splice($array, 0, $i);
        $tmp = array_splice($array, 0, $num);
        if ($keyrev) {
            krsort($tmp);
        } else {
            ksort($tmp);
        }
        $array = array_merge($first, $tmp, $array);
        unset($tmp);
        $i = $num;
    }
}

function makeHome() {
    if (update_option('front_page', $_POST['ID'])) {
        echo true;
    } else {
        echo false;
    }
}

function setMenuPrimary() {
    add_option('primaryMenu', $_POST['ID']);
    if (update_option('primaryMenu', $_POST['ID'])) {
        echo true;
    } else {
        echo false;
    }
}

//function generate_optimized_script_css() {
//    global $front_script, $front_style;
//    usort($front_style, function($a, $b) {
//        return $a['order'] - $b['order'];
//    });
//    usort($front_script, function($a, $b) {
//        return $a['order'] - $b['order'];
//    });
//    $filteredFileCss = unique_multidim_array($front_style, 'id');
//    $filteredFileJs = unique_multidim_array($front_script, 'id');
//
////    0 => 
////    array (size=3)
////      'id' => string 'fancybox-css' (length=12)
////      'href' => string 'http://asik/siatex/apon/cms//include/common/css/col.css' (length=55)
////      'order' => int 10
////      
////    0 => 
////       array (size=4)
////         'id' => string 'elevatZoomjs' (length=12)
////         'src' => string 'http://asik/siatex/apon/cms//content/plugins/product/js/jquery.elevatezoom.js' (length=77)
////         'order' => int 1
////         'position' => string 'head' (length=4
//    //var_dump($front_script);
//    foreach ($filteredFileCss as $styleInfo) {
//        $hrf = trimSlash($styleInfo['href']);
//        $thisDom = str_replace("/", '\/', domain());
//        //var_dump($hrf, $thisDom);
//        $re = '#' . $thisDom . '#s';
//        preg_match($re, $hrf, $matches, PREG_OFFSET_CAPTURE, 0);
//        if (empty($matches)) {
//            // $html.="<link rel=\"stylesheet\" href=\"$hrf\" id=\"$styleInfo[id]\">\n";
//            continue;
//        }
//
//        $singleFileContent = file_get_contents($hrf);
//        $singleFileContent = minifyCss($singleFileContent);
//        $minifyCss.=$singleFileContent;
//    }
//
//
//    $headScript = "";
//    $footScript = "";
//    foreach ($filteredFileJs as $styleInfo) {
//        $src = trimSlash($styleInfo['src']);
//        $thisDom = str_replace("/", '\/', domain());
//        $re = '#' . $thisDom . '#s';
//        preg_match($re, $src, $matches, PREG_OFFSET_CAPTURE, 0);
//        //var_dump($matches);
//        if (empty($matches)) {
//            $html.="<script src=\"$src\"></script>\n";
//            continue;
//        }
//        if (isset($styleInfo['position']) && $styleInfo['position'] == "head") {
//            $headScript.= file_get_contents($src) . "\n\n";
//        } else {
//            $footScript.= file_get_contents($src) . "\n\n";
//        }
//    }
//    //css Create
//    $msg = "";
//    if (file_put_contents(ABSPATH . "content/upload/style.css", $minifyCss)) {
//        $msg.="CSS ";
//    }
//    //Js Create
//    if (file_put_contents(ABSPATH . "content/upload/optimize_header_js.js", minify_js($headScript))) {
//        $msg.="& Header JS";
//    }//header
//
//    if (file_put_contents(ABSPATH . "content/upload/optimize_footer_js.js", minify_js($footScript))) {
//        $msg.="& Footer JS";
//    } //footer
//
//    if (!empty($msg)) {
//        echo json_encode(array('msg' => "Successfully generated $msg"));
//    } else {
//        echo json_encode(array('msg' => 'Something not right', 'error' => 1));
//    }
//}
