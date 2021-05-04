<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (isset($_GET['HBT'])) {
    if (!isset($_SESSION[SESS_KEY]['login'])) {
        echo json_encode(array("redrict" => "login.php"));
        exit;
    }
    //scheduler
    $SCHEDULE->init_schedule();
    echo time();
    //flush();
}

if (isset($_REQUEST['ajx_action'])) {
    $func = $_REQUEST['ajx_action'];
    if (function_exists($func)) {
        $params = array();
        if (isset($_POST['data']))
            parse_str($_POST['data'], $params);
        $func($params);
    } else {
        echo "$func function Not found !";
    }
}

if (isset($_REQUEST['cls']) && !empty($_REQUEST['m'])) {
    $clas = trim($_REQUEST['cls']);
    $method = trim($_REQUEST['m']);
    $clsNew = new $clas();
    $clsNew->$method();
}

if (isset($_GET['post_auto_save'])) {
    $data = $_POST['data'];
    $slugIn = titleFilterNexist(empty($data['post_name']) ? $data['post_title'] : $data['post_name'], $_POST['ID']);



    if (is_array($slugIn)) {
        $slug = $slugIn['slug'];
    } else {
        $slug = $slugIn;
    }

    if (empty($_POST['ID'])) {
        global $POSTS, $DB;
        $res = $POSTS->post_add($data);

        if ($res) {
            $info = array();
            $info['ID'] = $res;
            $info['slug'] = $slug;
        } else {
            $info = array();
            $info['msg'] = $DB->error;
            $info['error'] = true;
        }
        echo json_encode($info);
    } else {

        $data['guid'] = domain() . "?id=$_POST[ID]";
        $res = $POSTS->post_up($data, "ID=$_POST[ID]");
        // var_dump($res);

        if (is_array($slugIn)) {
            // $info = array("msg" => "Error..!!, '$slug[slug]' slug $slug[exist]", "error" => true);
            // echo json_encode($info);
            //exit;
            $slug = $_POST['ID'] . "-" . $slug;
        }
        if ($res['error'] == "") {
            $info = array();
            $info['ID'] = $_POST['ID'];
            $info['slug'] = $slug;
        }
        echo json_encode($info);
    }
}

if (isset($_GET['publish'])) {
    $data = $_POST['data'];
    $slugIn = titleFilterNexist(empty($data['post_name']) ? $data['post_title'] : $data['post_name'], $_POST['ID']);


    if (is_array($slugIn)) {
        $slug = $slugIn['slug'];
    } else {
        $slug = $slugIn;
    }
    //var_dump($slug);
    //exit;
    //exit;
    //    if (is_array($slug)) {
    //        $info = array("msg" => "Error..!!, '$slug[slug]' slug $slug[exist]", "error" => true);
    //        echo json_encode($info);
    //        exit;
    //    }

    if (strlen($slug) < 3) {
        $info = array("msg" => "Error..!!, ", "error" => 'Slug length must be more than 2 cherecter ');
        echo json_encode($info);
        exit;
    }



    //var_dump($_POST['calback']);
    if (isset($_POST['calback']) && is_array($_POST['calback'])) {
        foreach ($_POST['calback'] as $calbackFunction) {
            function_exists($calbackFunction) ? $calbackFunction() : "$calbackFunction is not a function !";
        }
    } else {
        if (isset($_POST['calback'])) {
            $calbackFunction = $_POST['calback'];
            function_exists($calbackFunction) ? $calbackFunction() : "$calbackFunction is not a function !";
        }
    }

    if (isset($_POST['meta'])) {
        $metas = $_POST['meta'];
        foreach ($metas as $k => $val) {
            $val = empty($val) ? "" : $val;
            if (is_array($val)) {
                $metaVals = serialize($val);
                update_post_meta($_POST['ID'], trim($k), trim($metaVals));
            } else {
                update_post_meta($_POST['ID'], trim($k), trim($val));
            }
        }
    }
    $DB->delete("term_relationships", "object_id=$_POST[ID]");
    if (isset($_POST['sel_texo'])) {
        $texos = $_POST['sel_texo'];
        foreach ($texos as $texo_id) {
            $TERM->add_term_relation($_POST['ID'], $texo_id);
        }
    }

    if (isset($_POST['post_template'])) {
        update_post_meta($_POST['ID'], 'post_template', $_POST['post_template']);
    }


    $data = $_POST['data'];
    $data['post_status'] = "published";
    $pg = 'page';
    if ($data['post_type'] != 'attachment') {
        $data['guid'] = domain() . "?id=$_POST[ID]";
    } else {
        $pg = 'library';
    }
    $res = $POSTS->post_up($data, "ID=$_POST[ID]");
    if (empty($res['error'])) {
        $info = array("msg" => "Published successfuly", 'redirect' => "index.php?l=$pg&post-type=$data[post_type]", "error" => 0);
    } else {
        $info = array("msg" => "Error..!! " . $res['error'], "error" => 1);
    }

    if (isset($_GET['update'])) {
        unset($info['redirect']);
    }
    //var_dump($res);
    echo json_encode($info);
}


if (isset($_REQUEST['loadList']) && !empty($_REQUEST['loadList'])) {
    //$LIST->$_REQUEST['loadList']();
    if ($_REQUEST['loadList'] == 'texonomy') {
        $texo = $_GET['tex'];
        $TERM->texoList($texo);
    } else {
        $type = 'post'; //Default type
        if (isset($_REQUEST['post-type']) && !empty($_REQUEST['post-type'])) {
            $type = trim($_REQUEST['post-type']);
        }
        $LIST->pages($type);
    }
}

if (isset($_REQUEST['load']) && !empty($_REQUEST['load'])) {
    //$LIST->$_REQUEST['loadList']();
    $ff = $_REQUEST['load'];
    $LOAD->$ff();
}


if (isset($_REQUEST['action']) && $_REQUEST['action'] == "MDelete") {
    $selected = array();
    parse_str($_REQUEST['listdata'], $selected);
    $ides = implode(",", $selected['selected']);
    $re = $DB->delete("post", "ID in($ides)");
    if ($re) {
        $info = array("msg" => "Deleted ", "error" => 0, 'ref' => 1);
    } else {
        $info = array("msg" => "Not Deleted, Somthing not right ! " . $re['error'], "error" => 1, 'ref' => '');
    }
    echo json_encode($info);
}


if (isset($_REQUEST['action']) && $_REQUEST['action'] == "MTrash") {
    $selected = array();
    parse_str($_REQUEST['listdata'], $selected);
    //$ides = implode(",", $selected['selected']);
    $res = $POSTS->move($selected['selected'], 'trash');
    if (empty($res['error'])) {
        $info = array("msg" => "Trashed successfuly !", 'rf' => "", "error" => 0);
    } else {
        $info = array("msg" => "Error..!! " . $res['error'], "error" => 1);
    }
    echo json_encode($info);
}
if (isset($_REQUEST['action']) && $_REQUEST['action'] == "published") {
    $selected = array();
    parse_str($_REQUEST['listdata'], $selected);
    //$ides = implode(",", $selected['selected']);
    $res = $POSTS->move($selected['selected'], 'published');
    if (empty($res['error'])) {
        $info = array("msg" => "Published successfuly !", 'rf' => "", "error" => 0);
    } else {
        $info = array("msg" => "Error..!! " . $res['error'], "error" => 1);
    }
    echo json_encode($info);
}
if (isset($_REQUEST['action']) && $_REQUEST['action'] == "draft") {
    $selected = array();
    parse_str($_REQUEST['listdata'], $selected);
    //$ides = implode(",", $selected['selected']);
    $res = $POSTS->move($selected['selected'], 'draft');
    if (empty($res['error'])) {
        $info = array("msg" => "Drafted successfuly !", 'rf' => "", "error" => 0);
    } else {
        $info = array("msg" => "Error..!! " . $res['error'], "error" => 1);
    }
    echo json_encode($info);
}

if (isset($_REQUEST['action']) && $_REQUEST['action'] == "dateModify") {
    $selected = array();
    parse_str($_REQUEST['listdata'], $selected);
    //$ides = implode(",", $selected['selected']);
    $res = $POSTS->move($selected['selected'], 'dateModify');
    if (empty($res['error'])) {
        $info = array("msg" => "Date Modify successfuly !", 'rf' => "", "error" => 0);
    } else {
        $info = array("msg" => "Error..!! " . $res['error'], "error" => 1);
    }
    echo json_encode($info);
}


if (isset($_REQUEST['trash']) && !empty($_REQUEST['trash'])) {
    //$LIST->$_REQUEST['loadList']();
    $id = $_REQUEST['trash'];
    $res = $POSTS->move($id, 'trash');
    if (empty($res['error'])) {
        $info = array("msg" => "Trashed successfuly !", 'rf' => "", "error" => 0);
    } else {
        $info = array("msg" => "Error..!! " . $res['error'], "error" => 1);
    }
    echo json_encode($info);
}

if (isset($_REQUEST['del']) && !empty($_REQUEST['del'])) {
    //$LIST->$_REQUEST['loadList']();
    $id = $_REQUEST['del'];
    $res = $POSTS->delPost($id);
    if (empty($res['error'])) {
        $info = array("msg" => "Delete successfuly !", 'rf' => "", "error" => 0);
    } else {
        $info = array("msg" => "Error..!! " . $res['error'], "error" => 1);
    }
    echo json_encode($info);
}

if (isset($_REQUEST['MenuDel']) && !empty($_REQUEST['MenuDel'])) {
    //$LIST->$_REQUEST['loadList']();
    $id = $_REQUEST['MenuDel'];
    $res = $MENU->del_menu_item($id);
    if (empty($res['error'])) {
        $info = array("msg" => "Delete successfuly !", 'rf' => "", "error" => 0);
    } else {
        $info = array("msg" => "Error..!! " . $res['error'], "error" => 1);
    }
    echo json_encode($info);
}

if (isset($_POST['term']) && isset($_POST['texo'])) {


    $term = $_POST['term'];
    if (empty($term['slug'])) {
        $term['slug'] = titleFilterNexist($term['name'], 'term_' . @$_POST['term_id']);
    }
    $term['slug'] = titleFilterNexist($term['slug'], 'term_' . @$_POST['term_id']);


    //var_dump($term['slug']);
    //exit;

    if (is_array($term['slug'])) {
        $slug = $term['slug'];
        $info = array("msg" => "Error..!!, '$slug[slug]' slug $slug[exist]", "error" => true);
        echo json_encode($info);
        exit;
    }

    if (strlen($term['slug']) < 3) {
        $info = array("msg" => "Error..!!, Slug length must be more than 2 cherecter. ", "error" => true);
        echo json_encode($info);
        exit;
    }

    $texo = $_POST['texo'];
    $id = @$_POST['term_id'];
    if ((isset($_POST['tex_id']) && !empty($_POST['tex_id'])) && (isset($_POST['term_id']) && !empty($_POST['term_id']))) {

        if ($DB->update("terms", $term, "term_id=$_POST[term_id]")) {
            if ($DB->update("term_taxonomy", $texo, "taxonomy_id=$_POST[tex_id]")) {
                $info = array("msg" => "$texo[taxonomy] Update successfuly !", 'rf' => "", "error" => 0);
            } else {
                $info = array("msg" => "$texo[taxonomy] Not Update! " . $DB->error, 'rf' => "", "error" => 1);
            }
        } else {
            $info = array("msg" => "$texo[taxonomy] Not Update! " . $DB->error, 'rf' => "", "error" => 1);
        }
    } else {
        $id = $TERM->add_term($term['name'], $term['slug'], $term['term_group']);
        if ($id) {
            if ($TERM->add_texo($id, $texo['taxonomy'], @$texo['description'])) {
                $info = array("msg" => "$texo[taxonomy] Added successfuly !", 'rf' => "", "error" => 0);
            }
        } else {
            $info = array("msg" => "Error..!!  $texo[taxonomy] not Save " . $res['error'], "error" => 1);
        }
    }
    if (isset($_POST['meta']) && is_array($_POST['meta'])) {
        foreach ($_POST['meta'] as $termMetaK => $termMetaV) {
            //var_dump($termMetaK,$termMetaV);
            update_term_meta($id, $termMetaK, $termMetaV);
        }
    }
    echo json_encode($info);
}

if (isset($_GET['termid']) && !empty($_GET['termid'])) {
    $termId = $_GET['termid'];
    echo json_encode($TERM->get_term($termId));
    //var_dump($_GET);
}

if (isset($_REQUEST['delTexo']) && !empty($_REQUEST['delTexo'])) {
    //$LIST->$_REQUEST['loadList']();
    $id = $_REQUEST['delTexo'];
    $res = $TERM->delTexo($id);
    if (empty($res['error'])) {
        $info = array("msg" => "Delete successfuly !", 'redrict' => "", "error" => 0);
    } else {
        $info = array("msg" => "Error..!! " . $res['error'], "error" => 1);
    }
    echo json_encode($info);
}


if (isset($_REQUEST['delete']) && !empty($_REQUEST['delete'])) {
    //$LIST->$_REQUEST['loadList']();
    $id = $_REQUEST['delete'];
    $res = del_attachment($id);

    $res = $DB->delete("post", "ID=$id");
    //var_dump($id);

    if (empty($res['error'])) {
        $info = array("msg" => "Deleted successfuly !", 'rf' => "", "error" => 0);
    } else {
        $info = array("msg" => "Error..!! " . $res['error'], "error" => 1);
    }
    echo json_encode($info);
}

if (isset($_POST['menu_name'])) {
    $name = $_POST['menu_name'];
    $MENU->add_menu($name);
}

if (isset($_GET['loadSelect'])) {
    echo $MENU->menu_selector(false, "m0", "menuSelect");
}

if (isset($_REQUEST['save_menu'])) {
    $texo = $_REQUEST['texo'];
    $ItemArr = json_decode($_REQUEST['save_menu']);

    $MENU->saveMenu($ItemArr);
    $info = array("msg" => "Menu Updated", "error" => 0);
    echo json_encode($info);
    exit;
    //Updated on 12 may 2019---^

    foreach ($ItemArr as $parentOrder => $item) {
        var_dump($item);

        $data = array();
        $data['post_parent'] = 0;
        $data['post_status'] = 'published';
        $data['post_title'] = $item->caption;
        $data['menu_order'] = $parentOrder;
        $data['guid'] = $item->customUrl;
        //$data['post_name']=
        $data['post_type'] = 'nav_menu_item';

        $id = metaval2ID($texo . "_menu_item_object_id", $item->id);



        if (!empty($item->postID)) {
            $POSTS->post_up($data, "ID=$item->postID"); //Changed to ID=$id
            update_post_meta($item->postID, 'menu_appendChield', $item->appendChield);
            update_post_meta($item->postID, 'menu_openNewWindow', $item->openNewWindow);
            update_post_meta($item->postID, 'menu_disableUrl', $item->disableUrl);
            update_post_meta($item->postID, 'menu_customTitle', $item->customTitle);
        } else {
            $id = $POSTS->post_add($data);
            update_post_meta($id, $texo . "_menu_item_object_id", $item->id); //chenged meta postid "$id"  objID
            $TERM->add_term_relation($id, $texo);
            update_post_meta($id, 'menu_appendChield', $item->appendChield);

            update_post_meta($id, 'menu_openNewWindow', $item->openNewWindow);
            update_post_meta($id, 'menu_disableUrl', $item->disableUrl);

            update_post_meta($id, 'menu_customTitle', $item->customTitle);
        }
        $pID = $id;


        // var_dump($item->child);
        // continue;

        if (is_array($item->child) && count($item->child) > 0) {
            foreach ($item->child as $childOrder => $childItem) {
                $data['post_title'] = $childItem->caption;
                $data['post_parent'] = $pID;
                $data['menu_order'] = $childOrder;
                $Cid = metaval2ID($texo . "_menu_item_object_id", $childItem->id);
                if (!empty($childItem->postID)) {
                    $POSTS->post_up($data, "ID=$childItem->postID"); //Changed to ID=$Cid
                    update_post_meta($childItem->postID, 'menu_appendChield', $childItem->appendChield);

                    update_post_meta($childItem->postID, 'menu_openNewWindow', $childItem->openNewWindow);
                    update_post_meta($childItem->postID, 'menu_disableUrl', $childItem->disableUrl);
                    update_post_meta($childItem->postID, 'menu_customTitle', $childItem->customTitle);
                } else {
                    $Cid = $POSTS->post_add($data);
                    update_post_meta($Cid, $texo . "_menu_item_object_id", $childItem->id);
                    update_post_meta($Cid, 'menu_appendChield', $childItem->appendChield);

                    update_post_meta($Cid, 'menu_openNewWindow', $childItem->openNewWindow);
                    update_post_meta($Cid, 'menu_disableUrl', $childItem->disableUrl);
                    update_post_meta($Cid, 'menu_customTitle', $childItem->customTitle);

                    $TERM->add_term_relation($Cid, $texo);
                }
            }
        }
    }
    $info = array("msg" => "Menu Updated", "error" => 0);
    echo json_encode($info);
}

if (isset($_GET['activeTheme']) && !empty($_GET['activeTheme'])) {
    $res = add_option("theme", $_GET['activeTheme']);
    if (!$res) {
        $res = update_option("theme", $_GET['activeTheme']);
    }
    echo $res;
}



if (isset($_FILES['upload'])) {
    ///var_dump($_POST);
    //var_dump($_FILES);
    $fname = str_replace(",", "+", $_FILES['upload']['name']);
    $fname = str_replace("'", "", $fname);
    $pinfo = pathinfo($fname);
    $icn = findIcon("." . $pinfo['extension'], '1x');
    $fname .= "," . $ufname = md5(time() . $pinfo['filename']) . "." . $pinfo['extension'];
    $fr = @copy($_FILES['upload']['tmp_name'], TEMP . "/$ufname");
    $error = ob_get_contents();
    @ob_end_clean();
    echo json_encode(array("error" => $error, "fname" => $fname, 'icon' => $icn, 'type' => $_REQUEST['upload']));
}



if (isset($_REQUEST['action'])) {
    if ($_REQUEST['action'] == 'extract') {
        $type = $_REQUEST['type'];
        $resurce = explode(",", $_REQUEST['resource']);
        $relDir = str_replace(".zip", "", $resurce[0]);
        $resPath = TEMP . $resurce[1];
        $destPath = TEMP;
        if ($type == 'theme') {
            $destPath = THEME_DIR;
        } elseif ($type == 'plugin') {
            $destPath = PLUG_DIR;
        }

        $html = "";
        $zip = new ZipArchive;
        if ($zip->open($resPath) === TRUE) {
            $zip->extractTo($destPath);
            $zip->close();
            unlink($resPath);
            if ($type == 'theme') {
                $ThemeCSS = $destPath . $relDir . "/style.css";
                $info = $THEME->extractThemeInfo($ThemeCSS);

                if (file_exists(THEME_DIR . "$relDir/screenshot.jpg")) {
                    $sc = THEMES_PATH . "$relDir/screenshot.jpg";
                } else {
                    $sc = ADMIN_IMG . "defauld_theme_screenshot.png";
                }
                $html.="<div id=\"themeInfo\">  
	<div class='themeScren'>
	<img src=\"$sc\">
	</div> 
	<div class=\"inf\">
	<strong>Theme Name : </strong>" . $info['Theme Name'] . "<br>
	<strong>Version : </strong>" . $info['Version'] . "<br>
	<strong>Author : </strong>" . $info['Author'] . "<br>
	<br>
	<button type=\"button\" class=\"btn btn-cms-primary\" onclick=\"themeActive('$relDir')\">Active</button>
	</div>
	</div>";
                echo $html;
            } elseif ($type == 'plugin') {
                $pluginMainFile = $destPath . $relDir . "/$relDir.php";
                $info = $THEME->extractThemeInfo($pluginMainFile);

                $html.="<div id=\"PluginInfo\">  
	<div class='pluginScreen'>
	<i class=\"fas fa-puzzle-piece fa-4x\"></i>
	</div> 
	<div class=\"inf\">
	<strong>Plugin Name : </strong>" . $info['Plugin Name'] . "<br>
	<strong>Description : </strong>" . $info['Description'] . "<br>  
	<strong>Version : </strong>" . $info['Version'] . "<br>
	<strong>Author : </strong>" . $info['Author'] . "<br>
	<br>
	<button type=\"button\" class=\"btn btn-cms-primary\" onclick=\"pluginActive('$relDir')\">Active</button>
	</div>
	</div>";
                echo $html;
            }
        } else {
            echo 'Some thing not right';
        }
        //var_dump($resPath, $destPath);
    }
}



if (isset($_GET['plugins-list'])) {
    global $plugins;
    $plugins->pluginView();
}

if (isset($_GET['thmDel'])) {
    $path = THEME_DIR . base64_decode($_GET['thmDel']);
    // echo $path;
    $rrD = rrmdir($path);
    //$rrD = true;
    if ($rrD) {
        $info = array("msg" => "Theme Deleted!", 'rf' => "", "error" => 0);
    } else {
        $info = array("msg" => "Theme not Delete!", 'rf' => "", "error" => 1);
    }
    echo json_encode($info);
}

if (isset($_GET['plDel'])) {
    $path = base64_decode($_GET['plDel']);
    if (rrmdir($path)) {
        $info = array("msg" => "Plugin Deleted!", 'rf' => "", "error" => 0);
    } else {
        $info = array("msg" => "Plugin not Delete!", 'rf' => "", "error" => 1);
    }
    echo json_encode($info);
}
if (isset($_GET['plAct'])) {
    $dir = base64_decode($_GET['plAct']);
    $plugins->to_active($dir);
}
if (isset($_GET['plDact'])) {
    $dir = base64_decode($_GET['plDact']);
    $plugins->to_deactive($dir);
}

if (isset($_FILES['attachment_upload'])) {
    //var_dump($_POST,$_FILES,$_GET);
    //exit;
    $ATTACH->save_file();
}

if (isset($_POST['options'])) {
    $options = $_POST['options'];
    foreach ($options as $optionName => $optionValue) {
        if ($optionName == "site_url") {
            htaccess_redir_ssl($optionValue);
        }

        if (is_array($optionValue)) {
            $optionValue = serialize($optionValue);
        }
        $as = get_option($optionName);
        //var_dump($as);
        if ($as !== false) {
            $res = update_option($optionName, $optionValue);
        } else {
            $res = add_option($optionName, $optionValue);
        }
    }

    $info = array("msg" => "Settings Updated !", 'rf' => "", "error" => 0);
    echo json_encode($info);
}

if (isset($_GET['update-cms'])) {
    $UPD = new Update_Cms();
    $UPD->update();
}

if (isset($_GET['loadChanger'])) {
    $MENU->itemsFtexo($_GET['loadChanger']);
    exit;
    //sleep(1);
    var_dump($_GET);
    ?>
    <li class="ui-state-default">
        <input type="hidden" value="1" class='objID' name="item">
        <div class="item">
            <div class='itemHeader'>
                <label class='menuLabel' org-val='Item 1'>Item 1</label>
                <a href="javascript:" class="menuItemOpTg"></a>
            </div>
            <div class="itemOption">
                <label>Caption:</label>
                <input type="text" class="form-control form-control-sm menu_item_caption" onkeyup="CaptionChange(this)">
                <br>
                <a href="javascript:" class="text-danger removeMenuItem"><i class="far fa-trash-alt"></i></a> 
            </div>
        </div>
        <ul class="nest"></ul>
    </li>


    <?php
}

if (isset($_GET['c'])) {
    $cls = $_GET['c'] . "_class";
    $cls = new $cls();
    if (isset($_GET['m'])) {
        $module = $_GET['m'];
        if (method_exists($cls, $module)) {
            $cls->$module();
        } else {
            $cls->notFound();
        }
    }
} 	