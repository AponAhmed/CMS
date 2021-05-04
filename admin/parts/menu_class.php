<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of menu_class
 *
 * @author nrb
 */
class menu_class {

    //put your code here
    private $texonomy = "nav-manu";
    public $regMenus;
    public $linkedMenu = array();

    public function __construct() {
        global $menu_location;
        $this->regMenus = $menu_location;
        $location = get_option('menuLocation');
        if (!empty($location)) {
            $location = json_decode($location, true);
        }
        if (!is_array($location)) {
            $location = array();
        }
        $this->linkedMenu = $location;
    }

    public function getLinkedName($location) {
        if (array_key_exists($location, $this->linkedMenu)) {
            return $this->linkedMenu[$location];
        } else {
            return $location;
        }
    }

    public function menuLocationSet() {
        global $menu_location;
        $location = get_option('menuLocation');
        if (!empty($location)) {
            $location = json_decode($location, true);
        }
        if (!is_array($location)) {
            $location = array();
        }
        //Existing Settings
        $nL = $_POST['location'];
        $LM = $_POST['menu'];
        if ($_POST['patch'] == 'true') {
            $location[$nL] = $LM;
            $ms = "Setup with";
        } else {
            unset($location[$nL]);
            $ms = "Removed From";
        }

        $locaJson = json_encode($location);
        if (update_option('menuLocation', $locaJson)) {
            echo "$LM $ms Location: <strong>" . $menu_location[$nL]['name'] . "</strong>";
        } else {
            echo 0;
        }
    }

    public function menus() {
        global $DB;
        $menus = $DB->select("term_taxonomy as tx left join terms as trm on tx.term_id=trm.term_id", "*", "taxonomy='nav-manu'");
        return $menus;
    }

    function menu_selector($sel = false, $cls = "", $name = "", $id = "menuSelect") {
        $html = "<select name='$name' id='$id' class='custom-select custom-select-sm $cls'>";
        $html.="<option value=''>Select Menu</option>";
        foreach ($this->menus() as $m) {
            $selec = $m['name'] == $sel ? 'checked' : '';
            $html.="<option value='$m[taxonomy_id]' $selec>$m[name]</option>";
        }
        $html.="</select>";
        return $html;
    }

    public function add_menu($name) {
        global $TERM;
        $res = $TERM->add_term($name, titleFilter($name));
        if (is_int($res)) {
            $texo = $TERM->add_texo($res, $this->texonomy, "");
        }
        echo $texo;
    }

    public function currentChild($obj = false) { //Name or menu-slug
        global $DB, $GLOBALS, $QV;
        if ($obj == false) {
            if (!empty($GLOBALS['post'])) {
                $obj = $GLOBALS['post']['ID'];
            } elseif (!empty($GLOBALS['term'])) {
                $obj = "term_" . $GLOBALS['term']['term_id'];
            }
        }
        $texo = get_option("primaryMenu");

        //$ob1stData=$DB->select();


        $items = $DB->select("post AS p
                LEFT JOIN term_relationships AS tr ON tr.object_id = p.ID
                LEFT JOIN term_taxonomy AS tt ON tt.taxonomy_id = tr.texo_id 
                LEFT JOIN `post-meta` AS pm ON pm.meta_key='{$texo}_menu_item_object_id' and pm.post_id=p.ID", "p . *,pm.meta_value as object", "p.post_type = 'nav_menu_item' AND pm.meta_value='$obj' AND tr.texo_id =$texo order by menu_order asc");


        //echo "<pre>";
        //echo "</pre>";
        if (!empty($items[0]['post_parent'])) {
            $parentObj = get_post_meta($items[0]['post_parent'], $texo . "_menu_item_object_id");
            return $this->currentChild($parentObj);
        } else {
            $menuArr = array();
            if (!empty($items)) {
                foreach ($items as $item) {
                    $menuArr[] = $this->pubMenuRec($item, $texo, false);
                }
            }
            return $menuArr;
        }
    }

    public function itemsFtexo($texo) {
        global $DB;
        if (empty($texo)) {
            echo "<em>Please Select a Menu</em>";
            exit;
        }
        $htm = "";
        $items = $DB->select("post AS p
                LEFT JOIN term_relationships AS tr ON tr.object_id = p.ID
                LEFT JOIN term_taxonomy AS tt ON tt.taxonomy_id = tr.texo_id 
                LEFT JOIN `post-meta` AS pm ON pm.meta_key='{$texo}_menu_item_object_id' and pm.post_id=p.ID", "p . *,pm.meta_value as object", "p.post_type = 'nav_menu_item' AND p.post_parent=0 AND tr.texo_id =$texo order by menu_order asc");
        if (!empty($items)) {
            foreach ($items as $item) {
                $htm.=$this->childMenuRec($item, $texo);
            }
        }
        echo $htm;
    }

    private function childMenuRec($item, $texo, $child = false) {
        global $DB, $TERM;
        $Childitems = $DB->select("post AS p
                LEFT JOIN term_relationships AS tr ON tr.object_id = p.ID
                LEFT JOIN term_taxonomy AS tt ON tt.taxonomy_id = tr.texo_id 
                LEFT JOIN `post-meta` AS pm ON pm.meta_key='{$texo}_menu_item_object_id' and pm.post_id=p.ID", "p . *,pm.meta_value as object", "p.post_type = 'nav_menu_item' AND p.post_parent=$item[ID] AND tr.texo_id =$texo order by menu_order asc");
        //var_dump(count($Childitems));
        $phtml = "";
        if (!$child) {
            $phtml.= "<li class=\"ui-state-default\"><div class=\"item\">";
        }
        $phtml.= $this->menuHtmGen($item);
        if (!empty($Childitems)) {
            $chHtm = "";
            foreach ($Childitems as $ch) {
                $chHtm.="<li class=\"ui-state-default\"><div class=\"item\">";
                $chHtm.=$this->childMenuRec($ch, $texo, true);
                $chHtm.="</li>";
            }
            $phtml = $phtml . "<span class='has_nest'></span></div><ul class=\"nest\">" . $chHtm . "</ul>";
        } else {
            $phtml.="<span class='has_nest'></span></div><ul class=\"nest\"></ul>";
        }
        if (!$child) {
            $phtml.="</li>";
        }
        return $phtml;
    }

    private function menuHtmGen($item, $sub = "") {
        global $DB, $TERM;
        $childPageAppend = "";
        $childPageAppend = get_post_meta($item['ID'], 'menu_appendChield');
        $childPageAppend = $childPageAppend == 'true' ? "checked" : "";

        $newWin = "";
        $newWin = get_post_meta($item['ID'], 'menu_openNewWindow');
        $newWin = $newWin == 'true' ? "checked" : "";

        $desUrl = "";
        $desUrl = get_post_meta($item['ID'], 'menu_disableUrl');
        $desUrl = $desUrl == 'true' ? "checked" : "";

        $custTtTag = get_post_meta($item['ID'], 'menu_customTitle');
        $custClass = get_post_meta($item['ID'], 'menu_customClass');
        $shortCode = get_post_meta($item['ID'], 'menu_shortCode');

        $htm = "";
        //$htm = "<li class=\"ui-state-default\"><div class=\"item\">";
        if (is_numeric($item['object'])) {
            $url = get_link($item['object']);
            $customUrl = $item['guid'];
            $orgTitle = get_post_title($item['object']);
            $Title = !empty($item['post_title']) ? $item['post_title'] : $orgTitle;


            $htm.= "<input value=\"$item[object]\" class=\"objID\" name=\"item\" type=\"hidden\">
                        <input value=\"$item[ID]\" class=\"menu_postID\" name=\"item\" type=\"hidden\">
                            <div class=\"itemHeader\">
                                <label class='menuLabel' org-val='$orgTitle'>$Title</label>
                                <a href=\"javascript:\" onclick=\"Act('MenuDel=$item[ID]', true, true)\" class=\"text-danger removeMenuItem\"><i class=\"far fa-trash-alt\"></i></a>
                                <a href=\"javascript:\" class=\"menuItemOpTg\"></a>
                            </div>
                            <div class=\"itemOption\">
                                <label>Caption:</label>
                                <div class='row'>
                                    <div class='col-sm-6'>
                                        <input class=\"form-control form-control-sm menu_item_caption\" value='$item[post_title]' onkeyup=\"CaptionChange(this)\" type=\"text\">
                                    </div>
                                    <div class='col-sm-6'>
                                        <input type='text'  value='$shortCode'  class='form-control form-control-sm shortCode' placeholder='ShortCode'>
                                    </div>
                                 </div>
                                <input type='text' placeholder='$url' value='$customUrl' class='form-control form-control-sm menu_item_url'>    
                                 
                                <div class='row'>
                                    <div class='col-sm-6'>
                                    <input type='text'  value='$custTtTag'  class='form-control form-control-sm customTitle' placeholder='Custom Title'>
                                    </div>
                                    <div class='col-sm-6'>
                                    <input type='text' value='$custClass'  class='form-control form-control-sm customClass' placeholder='Custom Class'>
                                    </div>
                                </div>
                                <input type='checkbox' value='true' $newWin class='openNewWindow'> <span class='checkLabel'>New Window</span>
                                <input type='checkbox' value='true' $desUrl class='disableUrl'> <span class='checkLabel'>Disable URL</span>
                                <input type='checkbox' value='true' $childPageAppend class='appendChield'> <span class='checkLabel'>Append child</span>
                                <button type='button' onclick='addChld(\"" . $item['object'] . "\",this)' class='btn btn-sm btn-cms-default  addChildBtn'>Add Child</button>
                                <br>
                            </div>
                        ";
        } else {
            $TermId = explode('_', $item['object']);
            $TermId = isset($TermId[1]) ? $TermId[1] : "";
            $termInfo = $TERM->get_term($TermId);

            $url = get_term_link($TermId);
            $customUrl = $item['guid'];

            $orgTitle = @$termInfo['name'];
            $Title = !empty($item['post_title']) ? $item['post_title'] : $orgTitle;

            $childPageAppend = "";
            $childPageAppend = get_post_meta($item['ID'], 'menu_appendChield');
            $childPageAppend = $childPageAppend == 'true' ? "checked" : "";

            $newWin = "";
            $newWin = get_post_meta($item['ID'], 'menu_openNewWindow');
            $newWin = $newWin == 'true' ? "checked" : "";

            $desUrl = "";
            $desUrl = get_post_meta($item['ID'], 'menu_disableUrl');
            $desUrl = $desUrl == 'true' ? "checked" : "";



            $custTtTag = get_post_meta($item['ID'], 'menu_customTitle');
            $custClass = get_post_meta($item['ID'], 'menu_customClass');
            $shortCode = get_post_meta($item['ID'], 'menu_shortCode');
            //var_dump($childPageAppend);

            $htm.= "<input value=\"$item[object]\" class=\"objID\" name=\"item\" type=\"hidden\">
                        <input value=\"$item[ID]\" class=\"menu_postID\" name=\"item\" type=\"hidden\">
                            <div class=\"itemHeader item-term\">
                                <label class='menuLabel' org-val='$orgTitle'>$Title</label>
                                <a href=\"javascript:\" onclick=\"Act('MenuDel=$item[ID]', true, true)\" class=\"text-danger removeMenuItem\"><i class=\"far fa-trash-alt\"></i></a>
                                <a href=\"javascript:\" class=\"menuItemOpTg\"></a>
                            </div>
                            <div class=\"itemOption\">
                                <label>Caption:</label>
                                <div class='row'>
                                    <div class='col-sm-6'>
                                        <input class=\"form-control form-control-sm menu_item_caption\" value='$item[post_title]' onkeyup=\"CaptionChange(this)\" type=\"text\">
                                    </div>
                                    <div class='col-sm-6'>
                                        <input type='text'  value='$shortCode'  class='form-control form-control-sm shortCode' placeholder='ShortCode'>
                                    </div>
                                 </div>
                                <input type='text' placeholder='$url' value='$customUrl' class='form-control form-control-sm menu_item_url'> 
                                
                                <div class='row'>
                                    <div class='col-sm-6'>
                                    <input type='text'  value='$custTtTag'  class='form-control form-control-sm customTitle' placeholder='Custom Title'>
                                    </div>
                                    <div class='col-sm-6'>
                                    <input type='text' value='$custClass'  class='form-control form-control-sm customClass' placeholder='Custom Class'>
                                    </div>
                                </div>
                                <input type='checkbox' value='true' $newWin class='openNewWindow'> <span class='checkLabel'>New Window</span>
                                <input type='checkbox' value='true' $desUrl class='disableUrl'> <span class='checkLabel'>Disable URL</span>
                                <input type='checkbox' value='true' $childPageAppend class='appendChield'> <span class='checkLabel'>Append child</span>
                                <button type='button' onclick='addChld(\"" . $item['object'] . "\",this)' class='btn btn-sm btn-cms-default  addChildBtn'>Add Child</button>
                                
                                <br>
                            </div>
                        ";
        }

        return $htm;
    }

    public function itemsFtexo_($texo) { //Disapair----
        global $DB, $TERM;
        $str = "SELECT p .*,pm.meta_value as object
FROM post AS p
LEFT JOIN term_relationships AS tr ON tr.object_id = p.ID
LEFT JOIN term_taxonomy AS tt ON tt.taxonomy_id = tr.texo_id
LEFT JOIN `post-meta` AS pm ON pm.meta_key='_menu_item_object_id' and pm.post_id=p.ID
WHERE p.post_type = 'nav_menu_item'
AND tr.texo_id =2";
        //var_dump($str);
        if (empty($texo)) {
            echo "<em>Please Select a Menu</em>";
            exit;
        }

        $htm = "";
        $items = $DB->select("post AS p
                LEFT JOIN term_relationships AS tr ON tr.object_id = p.ID
                LEFT JOIN term_taxonomy AS tt ON tt.taxonomy_id = tr.texo_id 
                LEFT JOIN `post-meta` AS pm ON pm.meta_key='{$texo}_menu_item_object_id' and pm.post_id=p.ID", "p . *,pm.meta_value as object", "p.post_type = 'nav_menu_item' AND p.post_parent=0 AND tr.texo_id =$texo order by menu_order asc");



        // exit;

        foreach ($items as $item) {
            $childPageAppend = "";
            $childPageAppend = get_post_meta($item['ID'], 'menu_appendChield');
            $childPageAppend = $childPageAppend == 'true' ? "checked" : "";

            $newWin = "";
            $newWin = get_post_meta($item['ID'], 'menu_openNewWindow');
            $newWin = $newWin == 'true' ? "checked" : "";

            $desUrl = "";
            $desUrl = get_post_meta($item['ID'], 'menu_disableUrl');
            $desUrl = $desUrl == 'true' ? "checked" : "";

            $custTtTag = get_post_meta($item['ID'], 'menu_customTitle');

            $htm.= "<li class=\"ui-state-default\"><div class=\"item\">";
            if (is_numeric($item['object'])) {
                $url = get_link($item['object']);
                $customUrl = $item['guid'];
                $orgTitle = get_post_title($item['object']);
                $Title = !empty($item['post_title']) ? $item['post_title'] : $orgTitle;


                $htm.= "<input value=\"$item[object]\" class=\"objID\" name=\"item\" type=\"hidden\">
                        <input value=\"$item[ID]\" class=\"menu_postID\" name=\"item\" type=\"hidden\">
                            <div class=\"itemHeader\">
                                <label class='menuLabel' org-val='$orgTitle'>$Title</label>
                                <a href=\"javascript:\" onclick=\"Act('MenuDel=$item[ID]', true, true)\" class=\"text-danger removeMenuItem\"><i class=\"far fa-trash-alt\"></i></a>
                                <a href=\"javascript:\" class=\"menuItemOpTg\"></a>
                            </div>
                            <div class=\"itemOption\">
                                <label>Caption:</label>
                                <input class=\"form-control form-control-sm menu_item_caption\" value='$item[post_title]' onkeyup=\"CaptionChange(this)\" type=\"text\">
                                <input type='text' placeholder='$url' value='$customUrl' class='form-control form-control-sm menu_item_url'>    
                                 
                                 <input type='text' value='$custTtTag' class='form-control form-control-sm customTitle' placeholder='Custom Title'>  
                               
                                <input type='checkbox' value='true' $newWin class='openNewWindow'> <span class='checkLabel'>New Window</span>
                                <input type='checkbox' value='true' $desUrl class='disableUrl'> <span class='checkLabel'>Disable URL</span>
                                <input type='checkbox' value='true' $childPageAppend class='appendChield'> <span class='checkLabel'>Append child</span>
                                <button type='button' onclick='addChld(\"" . $item['object'] . "\",this)' class='btn btn-sm btn-cms-default  addChildBtn'>Add Child</button>
                                <br>
                            </div>
                        </div>";
            } else {
                $TermId = explode('_', $item['object']);
                $TermId = @$TermId[1];
                $termInfo = $TERM->get_term($TermId);

                $url = get_term_link($TermId);
                $customUrl = $item['guid'];

                $orgTitle = $termInfo['name'];
                $Title = !empty($item['post_title']) ? $item['post_title'] : $orgTitle;

                $childPageAppend = "";
                $childPageAppend = get_post_meta($item['ID'], 'menu_appendChield');
                $childPageAppend = $childPageAppend == 'true' ? "checked" : "";

                $newWin = "";
                $newWin = get_post_meta($item['ID'], 'menu_openNewWindow');
                $newWin = $newWin == 'true' ? "checked" : "";

                $desUrl = "";
                $desUrl = get_post_meta($item['ID'], 'menu_disableUrl');
                $desUrl = $desUrl == 'true' ? "checked" : "";


                $custTtTag = get_post_meta($item['ID'], 'menu_customTitle');
                //var_dump($childPageAppend);

                $htm.= "<input value=\"$item[object]\" class=\"objID\" name=\"item\" type=\"hidden\">
                        <input value=\"$item[ID]\" class=\"menu_postID\" name=\"item\" type=\"hidden\">
                            <div class=\"itemHeader\">
                                <label class='menuLabel' org-val='$orgTitle'>$Title</label>
                                <a href=\"javascript:\" onclick=\"Act('MenuDel=$item[ID]', true, true)\" class=\"text-danger removeMenuItem\"><i class=\"far fa-trash-alt\"></i></a>
                                <a href=\"javascript:\" class=\"menuItemOpTg\"></a>
                            </div>
                            <div class=\"itemOption\">
                                <label>Caption:</label>
                                <input class=\"form-control form-control-sm menu_item_caption\" value='$item[post_title]' onkeyup=\"CaptionChange(this)\" type=\"text\">
                                <input type='text' placeholder='$url' value='$customUrl' class='form-control form-control-sm menu_item_url'> 
                                
                                <input type='text' value='$custTtTag' class='form-control form-control-sm customTitle' placeholder='Custom Title'>  
                                
                                <input type='checkbox' value='true' $newWin class='openNewWindow'> <span class='checkLabel'>New Window</span>
                                <input type='checkbox' value='true' $desUrl class='disableUrl'> <span class='checkLabel'>Disable URL</span>
                                <input type='checkbox' value='true' $childPageAppend class='appendChield'> <span class='checkLabel'>Append child</span>
                                <button type='button' onclick='addChld(\"" . $item['object'] . "\",this)' class='btn btn-sm btn-cms-default  addChildBtn'>Add Child</button>
                                
                                <br>
                            </div>
                        </div>";
            }
            $htm.= "<ul class=\"nest\">";
            //var_dump($item);
            $Childitems = $DB->select("post AS p
                LEFT JOIN term_relationships AS tr ON tr.object_id = p.ID
                LEFT JOIN term_taxonomy AS tt ON tt.taxonomy_id = tr.texo_id 
                LEFT JOIN `post-meta` AS pm ON pm.meta_key='{$texo}_menu_item_object_id' and pm.post_id=p.ID", "p . *,pm.meta_value as object", "p.post_type = 'nav_menu_item' AND p.post_parent=$item[ID] AND tr.texo_id =$texo order by menu_order asc");
            //var_dump($Childitems);
            foreach ($Childitems as $subitem) {
                $SubchildPageAppend = "";

                $htm.= "<li class=\"ui-state-default\"><div class=\"item\">";
                if (is_numeric($subitem['object'])) {

                    $url = get_link($item['object']);
                    $customUrl = $item['guid'];

                    $orgTitle = get_post_title($subitem['object']);
                    $Title = !empty($subitem['post_title']) ? $subitem['post_title'] : $orgTitle;

                    $SubchildPageAppend = get_post_meta($subitem['ID'], 'menu_appendChield');
                    $SubchildPageAppend = $SubchildPageAppend == 'true' ? "checked" : "";

                    $SubnewWin = "";
                    $SubnewWin = get_post_meta($subitem['ID'], 'menu_openNewWindow');
                    $SubnewWin = $SubnewWin == 'true' ? "checked" : "";

                    $SubdesUrl = "";
                    $SubdesUrl = get_post_meta($subitem['ID'], 'menu_disableUrl');
                    $SubdesUrl = $SubdesUrl == 'true' ? "checked" : "";

                    $SubcustTtTag = get_post_meta($subitem['ID'], 'menu_customTitle');

                    $htm.= "<input value=\"$subitem[object]\" class=\"objID\" name=\"item\" type=\"hidden\">
                        <input value=\"$subitem[ID]\" class=\"menu_postID\" name=\"item\" type=\"hidden\">
                            <div class=\"itemHeader\">
                                <label class='menuLabel' org-val='$orgTitle'>$Title</label>
                                <a href=\"javascript:\" onclick=\"Act('MenuDel=$item[ID]', true, true)\" class=\"text-danger removeMenuItem\"><i class=\"far fa-trash-alt\"></i></a>
                                <a href=\"javascript:\" class=\"menuItemOpTg\"></a>
                            </div>
                            <div class=\"itemOption\">
                                <label>Caption:</label>
                                <input class=\"form-control form-control-sm menu_item_caption\" value='$subitem[post_title]' onkeyup=\"CaptionChange(this)\" type=\"text\">
                                <input type='text' placeholder='$url' value='$customUrl' class='form-control form-control-sm menu_item_url'>  
                                    
                                <input type='text' value='$SubcustTtTag' class='form-control form-control-sm customTitle' placeholder='Custom Title'>  
                                <input type='checkbox' value='true' $SubnewWin class='openNewWindow'> <span class='checkLabel'>New Window</span>
                                <input type='checkbox' value='true' $SubdesUrl class='disableUrl'> <span class='checkLabel'>Disable URL</span>
                                <input type='checkbox' value='true' $SubchildPageAppend class='appendChield'> <span class='checkLabel'>Append child</span>
                                <button type='button' onclick='addChld(\"" . $subitem['object'] . "\",this)' class='btn btn-sm btn-cms-default  addChildBtn'>Add Child</button>
                                
                                <br>
                            </div>
                        </div>";
                } else {
                    $SubTermId = explode('_', $subitem['object']);
                    $SubTermId = $SubTermId[1];
                    $subTermInfo = $TERM->get_term($SubTermId);

                    $url = get_term_link($TermId);
                    $customUrl = $item['guid'];

                    $orgTitle = $subTermInfo['name'];
                    $Title = !empty($subitem['post_title']) ? $subitem['post_title'] : $orgTitle;

                    $SubchildPageAppend = get_post_meta($subitem['ID'], 'menu_appendChield');
                    $SubchildPageAppend = $SubchildPageAppend == 'true' ? "checked" : "";
                    //var_dump($SubchildPageAppend);
                    $SubnewWin = "";
                    $SubnewWin = get_post_meta($subitem['ID'], 'menu_openNewWindow');
                    $SubnewWin = $SubnewWin == 'true' ? "checked" : "";

                    $SubdesUrl = "";
                    $SubdesUrl = get_post_meta($subitem['ID'], 'menu_disableUrl');
                    $SubdesUrl = $SubdesUrl == 'true' ? "checked" : "";

                    $SubcustTtTag = get_post_meta($subitem['ID'], 'menu_customTitle');

                    $htm.= "<input value=\"$subitem[object]\" class=\"objID\" name=\"item\" type=\"hidden\">
                        <input value=\"$subitem[ID]\" class=\"menu_postID\" name=\"item\" type=\"hidden\">
                            <div class=\"itemHeader\">
                                <label class='menuLabel' org-val='$orgTitle'>$Title</label>
                                <a href=\"javascript:\" onclick=\"Act('MenuDel=$item[ID]', true, true)\" class=\"text-danger removeMenuItem\"><i class=\"far fa-trash-alt\"></i></a>
                                <a href=\"javascript:\" class=\"menuItemOpTg\"></a>
                            </div>
                            <div class=\"itemOption\">
                                <label>Caption:</label>
                                <input class=\"form-control form-control-sm menu_item_caption\" value='$subitem[post_title]' onkeyup=\"CaptionChange(this)\" type=\"text\">
                                <input type='text' placeholder='$url' value='$customUrl' class='form-control form-control-sm menu_item_url'> 
                                
                                <input type='text' value='$SubcustTtTag' class='form-control form-control-sm customTitle' placeholder='Custom Title'>  
                               
                                <input type='checkbox' value='true' $SubnewWin class='openNewWindow'> <span class='checkLabel'>New Window</span>
                                <input type='checkbox' value='true' $SubdesUrl class='disableUrl'> <span class='checkLabel'>Disable URL</span>
                                <input type='checkbox' value='true' $SubchildPageAppend class='appendChield'> <span class='checkLabel'>Append child</span>
                                <button type='button' onclick='addChld(\"" . $subitem['object'] . "\",this)' class='btn btn-sm btn-cms-default  addChildBtn'>Add Child</button>
                                
                                <br>
                            </div>
                        </div>";
                }
                $htm.= "<ul class=\"nest\">";
                $htm.= "</li>";
            }

            $htm.= " </ul></div>";
            $htm.= "</li>";
        }
        echo $htm;
    }

    public function addChld() {
        global $TERM;
        $id = $_POST['id'];
        $htm = "";
        if (is_numeric($id)) {
            //find Child page of page
            $default = array(
                'orderby' => 'post_date_gmt',
                'order' => 'DESC',
                'exclude_field' => "ID",
                'post_type' => array('page', 'post'),
                'post_status' => 'published',
                'selectFields' => "*",
                'parent' => $id
            );
            $childPages = get_posts($default);

            foreach ($childPages as $cPage) {
                $orgTitle = $cPage['post_title'];
                $url = get_link($cPage['ID']);

                $htm.= "<li class=\"ui-state-default\"><div class=\"item\">";
                $htm.= "<input value=\"$cPage[ID]\" class=\"objID\" name=\"item\" type=\"hidden\">
                        <input value=\"\" class=\"menu_postID\" name=\"item\" type=\"hidden\">
                            <div class=\"itemHeader\">
                                <label class='menuLabel' org-val='$orgTitle'>$orgTitle</label>
                                <a href=\"javascript:\" class=\"menuItemOpTg\"></a>
                            </div>
                            <div class=\"itemOption\">
                                <label>Caption:</label>
                                <input class=\"form-control form-control-sm menu_item_caption\" value='' onkeyup=\"CaptionChange(this)\" type=\"text\">
                                <input type='text' placeholder='$url' value='' class='form-control form-control-sm menu_item_url'> 
                                
                                <input type='text' value='' class='form-control form-control-sm customTitle' placeholder='Custom Title'>  
                               
                                <input type='checkbox' value='true'  class='openNewWindow'> <span class='checkLabel'>New Window</span>
                                <input type='checkbox' value='true'  class='disableUrl'> <span class='checkLabel'>Disable URL</span>
                                <input type='checkbox' value='true'  class='appendChield'> <span class='checkLabel'>Append child</span>
                                <a href=\"javascript:\" class=\"text-danger removeMenuItem\"><i class=\"far fa-trash-alt\"></i></a>
                                <br>
                            </div>
                        </div>";
                $htm.= "</li>";
            }
        } else {
            $trmID = str_replace("term_", "", $id);
            $termData = $TERM->get_term($trmID);

            $default = array(
                'numberposts' => -1,
                'orderby' => 'post_date_gmt',
                'order' => 'DESC',
                'exclude_field' => "ID",
                'post_type' => array('page', 'post'),
                'post_status' => 'published',
                'selectFields' => "*",
                'texonomy' => $termData['slug']
            );
            $childPages = get_posts($default);


            foreach ($childPages as $cPage) {
                $orgTitle = $cPage['post_title'];
                $url = get_link($cPage['ID']);

                $htm.= "<li class=\"ui-state-default\"><div class=\"item\">";
                $htm.= "<input value=\"$cPage[ID]\" class=\"objID\" name=\"item\" type=\"hidden\">
                        <input value=\"\" class=\"menu_postID\" name=\"item\" type=\"hidden\">
                            <div class=\"itemHeader\">
                                <label class='menuLabel' org-val='$orgTitle'>$orgTitle</label>
                                <a href=\"javascript:\" class=\"menuItemOpTg\"></a>
                            </div>
                            <div class=\"itemOption\">
                                <label>Caption:</label>
                                <input class=\"form-control form-control-sm menu_item_caption\" value='' onkeyup=\"CaptionChange(this)\" type=\"text\">
                                <input type='text' placeholder='$url' value='' class='form-control form-control-sm m enu_item_url'> 
                                
                                <input type='text' value='' class='form-control form-control-sm customTitle' placeholder='Custom Title'>  
                               
                                <input type='checkbox' value='true'  class='openNewWindow'> <span class='checkLabel'>New Window</span>
                                <input type='checkbox' value='true'  class='disableUrl'> <span class='checkLabel'>Disable URL</span>
                                <input type='checkbox' value='true'  class='appendChield'> <span class='checkLabel'>Append child</span>
                                <a href=\"javascript:\" class=\"text-danger removeMenuItem\"><i class=\"far fa-trash-alt\"></i></a>
                                <br>
                            </div>
                        </div>";
                $htm.= "</li>";
            }
        }

        echo $htm;
    }

    //==================================
    //==================================  

    public function pub_menu($term, $disable_custom_title) {
        global $DB, $TERM;
        if (is_numeric($term)) {
            $texo = $term;
        } else {
            $tex = $DB->select("term_taxonomy left join terms on term_taxonomy.term_id=terms.term_id", "taxonomy_id", "name='$term'");
            $texo = @$tex[0]['taxonomy_id'];
        }

        $items = $DB->select("post AS p
                LEFT JOIN term_relationships AS tr ON tr.object_id = p.ID
                LEFT JOIN term_taxonomy AS tt ON tt.taxonomy_id = tr.texo_id 
                LEFT JOIN `post-meta` AS pm ON pm.meta_key='{$texo}_menu_item_object_id' and pm.post_id=p.ID", "p . *,pm.meta_value as object", "p.post_type = 'nav_menu_item' AND p.post_parent=0 AND tr.texo_id =$texo order by menu_order asc");
        //echo "<pre>";
        //var_dump($items);
        //echo "</pre>";
        $menuArr = array();
        if (!empty($items)) {
            foreach ($items as $item) {
                $menuArr[] = $this->pubMenuRec($item, $texo, $disable_custom_title);
            }
        }
        return $menuArr;
    }

    private function pubMenuRec($item, $texo, $disable_custom_title) {
        global $DB, $TERM;
        $childMenu = $DB->select("post AS p
                LEFT JOIN term_relationships AS tr ON tr.object_id = p.ID
                LEFT JOIN term_taxonomy AS tt ON tt.taxonomy_id = tr.texo_id 
                LEFT JOIN `post-meta` AS pm ON pm.meta_key='{$texo}_menu_item_object_id' and pm.post_id=p.ID", "p . *,pm.meta_value as object", "p.post_type = 'nav_menu_item' AND p.post_parent=$item[ID] AND tr.texo_id =$texo order by menu_order asc");

        // $childItem = array();
        $PobjArr = $this->pubMenuObj($item, $disable_custom_title);
        if (!empty($childMenu)) {
            $chObArr = array();
            foreach ($childMenu as $ch) {
                $chObArr[] = $this->pubMenuRec($ch, $texo, $disable_custom_title);
            }
            $PobjArr['child'] = $chObArr;
        }
        return $PobjArr;
    }

    private function pubMenuObj($subitem, $disable_custom_title) {
        global $TERM;
        $chI = array();
        // echo "<pre>";
        $orgTitle = "";
        if (is_numeric($subitem['object'])) {
            $orgTitle = get_post_title($subitem['object']);
        } else {
            $terminf = explode('_', $subitem['object']);
            $termID = @$terminf[1];
            $term = $TERM->get_term($termID);
            //var_dump($term);
            if (isset($term['name'])) {
                $orgTitle = $term['name'];
            }
        }
        $Title = !empty($subitem['post_title']) && !$disable_custom_title ? $subitem['post_title'] : $orgTitle;
        // var_dump($Title);
        $chI['id'] = $subitem['ID'];
        $chI['menu_title'] = $Title;
        $chI['bject_id'] = $subitem['object'];
        $chI['menu_order'] = $subitem['menu_order'];
        if (!empty($subitem['guid'])) {
            $chI['customLink'] = $subitem['guid'];
        }
        return $chI;
    }

    //Disapaired ----- 12.05.2019
    //===================================================
    public function pub_menu_($term, $disable_custom_title) {
        $menu_object = array();
        global $DB, $TERM;
        $tex = $DB->select("term_taxonomy left join terms on term_taxonomy.term_id=terms.term_id", "taxonomy_id", "name='$term'");
        $texo = @$tex[0]['taxonomy_id'];
        $items = $DB->select("post AS p
                LEFT JOIN term_relationships AS tr ON tr.object_id = p.ID
                LEFT JOIN term_taxonomy AS tt ON tt.taxonomy_id = tr.texo_id 
                LEFT JOIN `post-meta` AS pm ON pm.meta_key='{$texo}_menu_item_object_id' and pm.post_id=p.ID", "p . *,pm.meta_value as object", "p.post_type = 'nav_menu_item' AND p.post_parent=0 AND tr.texo_id =$texo order by menu_order asc");

        if ($items)
            foreach ($items as $item) {
                $mI = array();


                $orgTitle = get_post_title($item['object']);
                $Title = !empty($item['post_title']) && !$disable_custom_title ? $item['post_title'] : $orgTitle;
                //$Title = !empty($item['post_title']) ? $item['post_title'] : $orgTitle;
                $mI['id'] = $item['ID'];
                $mI['menu_title'] = $Title;
                $mI['bject_id'] = $item['object'];
                $mI['menu_order'] = $item['menu_order'];
                if (!empty($item['guid'])) {
                    $mI['customLink'] = $item['guid'];
                }
                //var_dump($item['ID']);

                $Childitems = $DB->select("post AS p
                LEFT JOIN term_relationships AS tr ON tr.object_id = p.ID
                LEFT JOIN term_taxonomy AS tt ON tt.taxonomy_id = tr.texo_id 
                LEFT JOIN `post-meta` AS pm ON pm.meta_key='{$texo}_menu_item_object_id' and pm.post_id=p.ID", "p . *,pm.meta_value as object", "p.post_type = 'nav_menu_item' AND p.post_parent=$item[ID] AND tr.texo_id =$texo order by menu_order asc");

                if (count($Childitems) > 0) {
                    $childMenu = array();
                    foreach ($Childitems as $subitem) {
                        $chI = array();
                        $orgTitle = get_post_title($subitem['object']);
                        $Title = !empty($subitem['post_title']) ? $subitem['post_title'] : $orgTitle;
                        $chI['id'] = $subitem['ID'];
                        $chI['menu_title'] = $Title;
                        $chI['bject_id'] = $subitem['object'];
                        $chI['menu_order'] = $subitem['menu_order'];
                        if (!empty($subitem['guid'])) {
                            $chI['customLink'] = $subitem['guid'];
                        }

//                    $SubchildPageAppend = get_post_meta($subitem['ID'], 'menu_appendChield');
//                    if($SubchildPageAppend =="true"){
//                        $chI['child']=array();
//                        if(is_numeric($subitem['object'])){
//                            //parent Page
//                            
//                        }else{
//                            //Texonomy 
//                            
//                        }
//                    }

                        $childMenu[] = $chI;
                    }
                    $mI['child'] = $childMenu;
                }
                $menu_object[] = $mI;
            }
        return $menu_object;
    }

    public function del_menu_item($id) {
        global $DB;
        $child = $DB->select("post", "ID", "post_parent=$id");
        // var_dump($id,$child);
        //exit;
        foreach ($child as $cp) {
            $CId = $cp['ID'];
            $DB->delete("`post-meta`", "post_id=$CId");
            $DB->delete("term_relationships", "object_id=$CId");
            $DB->delete("post", "ID=$CId");
        }
        $DB->delete("`post-meta`", "post_id=$id");
        $DB->delete("term_relationships", "object_id=$id");
        $DB->delete("post", "ID=$id");
    }

    //===============Menu Save From Here-------------

    public function saveMenu($items) {
        $texo = $_REQUEST['texo'];
        foreach ($items as $k => $item) {
            $this->saveItemRec($texo, $item, $k);
        }
    }

    private function saveItemRec($texo, $item, $se_id, $parent = false) {
        $parent = $this->saveMenuItem($item, $texo, $se_id, $parent);
        if (!empty($item->child)) {
            foreach ($item->child as $k => $ch) {
                $this->saveItemRec($texo, $ch, $k, $parent);
            }
        }
    }

    private function saveMenuItem($item, $texo, $order, $parent = false) {
        //Insert here 
        global $POSTS, $TERM;
        //var_dump($item);
        //return;

        $data = array();
        $data['post_parent'] = 0;
        $data['post_status'] = 'published';
        $data['post_title'] = @$item->caption;
        $data['menu_order'] = $order;
        $data['guid'] = @$item->customUrl;
        $data['post_type'] = 'nav_menu_item';

        $id = metaval2ID($texo . "_menu_item_object_id", $item->id);

        if ($parent) {
            $data['post_parent'] = $parent;
        }
        if (!empty($item->postID)) {
            $POSTS->post_up($data, "ID=$item->postID"); //Changed to ID=$id
            update_post_meta($item->postID, 'menu_appendChield', $item->appendChield);
            update_post_meta($item->postID, 'menu_openNewWindow', $item->openNewWindow);
            update_post_meta($item->postID, 'menu_disableUrl', $item->disableUrl);
            update_post_meta($item->postID, 'menu_customTitle', $item->customTitle);
            update_post_meta($item->postID, 'menu_customClass', $item->customClass);
            update_post_meta($item->postID, 'menu_shortCode', $item->shortCode);
            return $item->postID;
        } else {
            $id = $POSTS->post_add($data);
            update_post_meta($id, $texo . "_menu_item_object_id", $item->id); //chenged meta postid "$id"  objID
            $TERM->add_term_relation($id, $texo);
            update_post_meta($id, 'menu_appendChield', $item->appendChield);

            update_post_meta($id, 'menu_openNewWindow', $item->openNewWindow);
            update_post_meta($id, 'menu_disableUrl', $item->disableUrl);
            update_post_meta($id, 'menu_customTitle', @$item->customTitle);
            update_post_meta($id, 'menu_customClass', @$item->customClass);
            update_post_meta($id, 'menu_shortCode', @$item->shortCode);
            return $id;
        }
    }

}
