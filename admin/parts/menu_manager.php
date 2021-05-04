<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<h2>Menus</h2>
<hr>
<div class='menuCreatorSel'>
    <div class="row m0">
        <div class="MenuSelector col-6">
            <div class="row">
                <div id="menuSelectArea" class='col-11'>
                    <?php echo $MENU->menu_selector(false, "m0", "menuSelect"); ?>
                </div>
                <div class='col-1' id="wt">
                </div>
            </div>
        </div>
        <div class="MenuCreator col-6">
            <form id="CreateMenu">
                <div class="row">
                    <div class='col-8'><input class="form-control form-control-sm" name="menu_name" placeholder="Menu name" type="text"></div> 
                    <div class='col-4'><button class="btn btn-cms-primary" type="button" onclick="SaveMenu(CreateMenu)">Save New</button></div>
                </div> 
            </form>
        </div>
    </div>
</div>
<div class="menuEditor">
    <div class="row">
        <div class="col-3">
            <div class='postTypes menuEditorPostType'>
                <div id="menuEditorPostType">
                    <?php
                    global $C_POST_TYPE;
                    $cptC = 0;
                    foreach ($C_POST_TYPE as $type => $arg) {
                        $cptC++;
                        if (!$arg['show_in_nav_menus']) {
                            continue;
                        }
                        ?>
                        <div class="card menuSel">
                            <div class="card-header" id="headingOne">
                                <a href="javascript:" class="" data-toggle="collapse" data-target="#<?php echo "pos_type_" . $type ?>" aria-expanded="true" aria-controls="<?php echo "pos_type_" . $type ?>">
                                    <?php echo $arg['label'] ?>
                                </a>
                            </div>
                            <div id="<?php echo "pos_type_" . $type ?>" class="collapse <?php echo $cptC == 1 ? "show" : "" ?>" aria-labelledby="headingOne" data-parent="#menuEditorPostType">
                                <div class="card-body">
                                    <?php
                                    if (multi_lang()) {
                                        $pTyp = unserialize(get_option('languagePostType'));
                                        if (in_array($type, $pTyp)) {
                                            if (class_exists('siteLanguages')) {
                                                $lng = new siteLanguages();
                                                ?>
                                                <ul class="nav nav-tabs tab-sm">
                                                    <li class="nav-item">
                                                        <a class="langNav nav-link <?php echo (!isset($_GET['lng']) || $_GET['lng'] == 'all') ? "active" : "" ?>" href="index.php?l=menus&lng=all"><?php echo "All" ?></a>
                                                    </li>
                                                    <?php foreach ($lng->languages as $k => $val) { ?>
                                                        <li class="nav-item">
                                                            <a class="langNav nav-link <?php echo $_GET['lng'] == $k ? "active" : "" ?>" href="index.php?l=menus&lng=<?php echo $k ?>"><?php echo $k ?></a>
                                                        </li>
                                                    <?php }
                                                    ?>
                                                </ul>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>
                                    <ul id="<?php echo "type_" . $type ?>" class='MenuChoser'>
                                        <?php
                                        $default = array(
                                            'numberposts' => -1,
                                            'orderby' => 'menu_order',
                                            'order' => 'ASC',
                                            'post_type' => $type,
                                            'post_status' => 'published',
                                            'selectFields' => "ID,post_title,post_name,menu_order",
                                                //'condition' => ' post_parent=0 '
                                        );

                                        $psts = get_posts($default);
                                        foreach ($psts as $pst) {

                                            $langMeta = get_post_meta($pst['ID'], 'lng');
                                            if (isset($_GET['lng']) && $_GET['lng'] != 'all') {
                                                if ($langMeta != $_GET['lng']) {
                                                    continue;
                                                }
                                            }
                                            ?>
                                            <li id="ID<?php echo $pst['ID'] ?>" class="ui-state-default checked">
                                                <input class="menuChoserCheckBox" id='check_<?php echo $pst['ID'] ?>' type='checkbox' value="<?php echo $pst['ID'] ?>">
                                                <label class='menuChoserLabel' for="check_<?php echo $pst['ID'] ?>"><?php echo $pst['post_title'] ?></label>
                                                <input type="hidden" class="pageUrl" value="<?php echo get_link($pst['ID']) ?>">
                                            </li>
                                            <?php
                                        }
                                        //var_dump($psts);
                                        ?>
                                    </ul>
                                </div>
                                <div class="card-footer text-muted">
                                    <input control="#<?php echo "type_" . $type ?>" type="checkbox" id='select_all_<?php echo "type_" . $type ?>' class="selAll">&nbsp;&nbsp;<label for='select_all_<?php echo "type_" . $type ?>'>All</label>
                                    <button class="btn btn-cms-default float-right add_to_menu" control="#<?php echo "type_" . $type ?>" type="button">Add to Menu</button>
                                </div>
                            </div>
                        </div>
                        <?php
                        if (!empty($arg['taxonomies'])) {
                            foreach ($arg['taxonomies'] as $texo) {
                                if (@$arg['texo_show_in_menu'][$texo] == true) {
                                    $usTexo = str_replace(" ", "_", $texo);
                                    ?>
                                    <div class="card menuSel">
                                        <div class="card-header" id="headingOne">
                                            <a href="javascript:" class="" data-toggle="collapse" data-target="#<?php echo "texo_type_{$usTexo}_" . $type ?>" aria-expanded="true" aria-controls="<?php echo "texo_type_{$usTexo}_" . $type ?>">
                                                <?php echo $arg['label'] . " " . $texo ?>
                                            </a>
                                        </div>
                                        <div id="<?php echo "texo_type_{$usTexo}_" . $type ?>" class="collapse" aria-labelledby="headingOne" data-parent="#menuEditorPostType">
                                            <div class="card-body">
                                                <ul id="<?php echo "texo_{$usTexo}_" . $type ?>" class='MenuChoser'>
                                                    <?php
                                                    //echo $texo;
                                                    $terms = $TERM->texoListRow($texo, true);
                                                    // var_dump($terms);

                                                    if (!empty($terms)) {
                                                        foreach ($terms as $term) {
                                                            ?>
                                                            <li id="term_<?php echo $term['term_id'] ?>" class="ui-state-default checked">
                                                                <input class="menuChoserCheckBox" id='check_<?php echo $term['term_id'] ?>' type='checkbox' value="<?php echo 'term_' . $term['term_id'] ?>">
                                                                <label class='menuChoserLabel' for="check_<?php echo $term['term_id'] ?>"><?php echo $term['name'] ?></label>
                                                                <input type="hidden" class="pageUrl" value="<?php echo get_term_link($term) ?>">
                                                                <ul class="subListItem">
                                                                    <?php
                                                                    $childTerms = $TERM->texoListRow($texo, $term['taxonomy_id']);
                                                                    foreach ($childTerms as $childTerm) {
                                                                        ?>
                                                                        <li id="term_<?php echo $childTerm['term_id'] ?>" parent="ID<?php echo $childTerm['term_id'] ?>" class="ui-state-default checked"><span class="Childlnk">-</span>       
                                                                            <input class="menuChoserCheckBox" id='check_<?php echo $childTerm['term_id'] ?>' type='checkbox' value="<?php echo 'term_' . $childTerm['term_id'] ?>">
                                                                            <label class='menuChoserLabel' for="check_<?php echo $childTerm['term_id'] ?>"><?php echo $childTerm['name'] ?></label>
                                                                            <input type="hidden" class="pageUrl" value="<?php echo get_term_link($childTerm) ?>">
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </ul>
                                                            </li>
                                                            <?php
                                                        }
                                                    }
                                                    //var_dump();
                                                    ?>
                                                </ul>
                                            </div>
                                            <div class="card-footer text-muted">
                                                <input control="#<?php echo "texo_{$usTexo}_" . $type ?>" type="checkbox" id='select_all_<?php echo "texo_{$usTexo}_" . $type ?>' class="selAll">&nbsp;&nbsp;<label for='select_all_<?php echo "texo_{$usTexo}_" . $type ?>'>All</label>
                                                <button class="btn btn-cms-default float-right add_to_menu" control="#<?php echo "texo_{$usTexo}_" . $type ?>" type="button">Add to Menu</button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                } // endif menu show
                            } //texo loop
                        }//Texo if
                    }//Post Type end
                    ?>
                </div>
            </div>
        </div>
        <div class="col-9">
            <div class="MenuEditArea">
                <div class="MenuEditHeader">
                    <strong>Menu Name</strong>&nbsp;&nbsp;&nbsp;
                    <button type="button" class='float-right btn btn-cms-primary szS'>Save Menu</button>
                    <button class="btn btn-cms-default " onclick="AddCustomMenu()"  type="button"><i class="fas fa-plus-circle"></i> Custom Menu</button>

                </div>
                <div class="menuEditorMainArea" >
                    <ul id="menuEditItems" class='menuEditItems sortable'>
                        Select a Menu
                    </ul>
                </div>
                <div id="tstPrev"></div>
                <div class="MenuEditFooter">

                    <button type="button" class='float-right btn btn-cms-primary szS'>Save Menu</button>
                    <button type="button" class="btn btn-outline-danger deltMenu" onclick="Act('delTexo=' + menuSelect.value, true, true)"><i class="far fa-trash-alt"></i></button>

                    <div class='primaryMenuSet'>
                        <div class="prim">
                            <input type="checkbox" id="primaryMenu" value="<?php echo get_option('primaryMenu') ?>" onchange="setMenuPrimary(this, menuSelect.value)"> <label for="primaryMenu">Primary</label>
                        </div>
                        <div class='menuLocation collapse'>
                            <strong>Locations :</strong><br><br>
                            <?php
                            global $menu_location;
                            $Ex = get_option('menuLocation');
                            $jsData = json_decode($Ex, true);
                            if (!is_array($jsData)) {
                                $jsData = array();
                            }
                            foreach ($menu_location as $loc) {
                                $data = "";
                                $used = "";
                                if (isset($jsData[$loc['slug']])) {
                                    $data = $jsData[$loc['slug']];
                                    $used = "color:#999;";
                                }
                                echo "<label style='margin-right:10px;$used'  dataselect='$data'><input class='manuLoc' type='checkbox' name='$loc[slug]' value=''>&nbsp;$loc[name]</label>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function AddCustomMenu() {
        var htm = "<li class=\"ui-state-default\">";
        htm += "<input value=\"\" class=\"objID\" name=\"item\" type=\"hidden\">\n\
                        <input value=\"\" class=\"menu_postID\" name=\"item\" type=\"hidden\">\n\
                           <div class=\"item in\">\n\
                            <div class=\"itemHeader\">\n\
                                <label class='menuLabel' org-val=''></label>\n\
                                <a href=\"javascript:\" class=\"menuItemOpTg\"></a>\n\
                            </div>\n\
                            <div class=\"itemOption\" style=\"display: block;\">\n\
                                <label>Caption:</label>\n\
                                <div class='row'>\n\
                                    <div class='col-sm-6'>\n\
                                        <input class=\"form-control form-control-sm menu_item_caption\"  onkeyup=\"CaptionChange(this)\" type=\"text\">\n\
                                    </div>\n\
                                    <div class='col-sm-6'>\n\
                                        <input type='text'  class='form-control form-control-sm shortCode' placeholder='ShortCode'>\n\
                                    </div>\n\
                                 </div>\n\
                                <input type='text' placeholder='URL' class='form-control form-control-sm menu_item_url'>\n\
                                <div class='row'>\n\
                                <div class='col-sm-6'>\n\
                                <input type='text' class='form-control form-control-sm customTitle' placeholder='Custom Title'>\n\
                                </div>\n\
                                <div class='col-sm-6'>\n\
                                <input type='text' class='form-control form-control-sm customClass' placeholder='Custom Class'>\n\
                                </div>\n\
                                </div>\n\
                                <input type='checkbox' value='true' class='openNewWindow'><span class='checkLabel'>New Window</span>\n\
                                <br>\n\
                                <a href=\"javascript:\" onclick='$(this).parent().parent().parent().remove()' class=\"text-danger removeMenuItem\"><i class=\"far fa-trash-alt\"></i></a> \n\
                            </div>\n\
                        </div>";
        htm += "<ul class=\"nest\"></ul>\n</li>";
        $("#menuEditItems").append(htm);
        mClps();
    }

    $(".add_to_menu").click(function() {
        var cont = $(this).attr('control');
        $(cont).find(" > li").each(function() {
            var chk = $(this).find(' > .menuChoserCheckBox');
            if ($(chk).is(":checked"))
            {
                var nst = "";
                if ($(this).find(".subListItem li").length > 0) {
                    $(this).find(".subListItem li").each(function() {

                        var nst_chk = $(this).find('.menuChoserCheckBox');
                        if ($(nst_chk).is(":checked")) {
                            var nst_ID = nst_chk.val();
                            //alert(nst_ID);

                            var nst_label = $(this).find('.menuChoserLabel').html();
                            var nst_pUrl = $(this).find('.pageUrl').val();

                            nst += "<li class=\"ui-state-default\">";
                            nst += "<input value=\"" + nst_ID + "\" class=\"objID\" name=\"item\" type=\"hidden\">\n\
                        <input value=\"\" class=\"menu_postID\" name=\"item\" type=\"hidden\">\n\
                           <div class=\"item\">\n\
                            <div class=\"itemHeader\">\n\
                                <label class='menuLabel' org-val='" + nst_label + "'>" + nst_label + "</label>\n\
                                <a href=\"javascript:\" class=\"menuItemOpTg\"></a>\n\
                            </div>\n\
                            <div class=\"itemOption\">\n\
                                <label>Caption:</label>\n\
                                <div class='row'>\n\
                                    <div class='col-sm-6'>\n\
                                        <input class=\"form-control form-control-sm menu_item_caption\"  onkeyup=\"CaptionChange(this)\" type=\"text\">\n\
                                    </div>\n\
                                    <div class='col-sm-6'>\n\
                                        <input type='text'  class='form-control form-control-sm shortCode' placeholder='ShortCode'>\n\
                                    </div>\n\
                                 </div>\n\
                                <input type='text' placeholder='" + nst_pUrl + "' class='form-control form-control-sm menu_item_url'>\n\
                                <div class='row'>\n\
                                <div class='col-sm-6'>\n\
                                <input type='text' class='form-control form-control-sm customTitle' placeholder='Custom Title'>\n\
                                </div>\n\
                                <div class='col-sm-6'>\n\
                                <input type='text' class='form-control form-control-sm customClass' placeholder='Custom Class'>\n\
                                </div>\n\
                                </div>\n\
                                <input type='checkbox' value='true' class='openNewWindow'><span class='checkLabel'>New Window</span>\n\
                                <input type='checkbox' value='true' class='disableUrl'><span class='checkLabel'>Disable URL</span>\n\
                                <input type='checkbox' value='true' class='appendChield'> <span class='checkLabel'>Append child</span>\n\
                                <button type='button' onclick='addChld(\"" + nst_ID + "\",this)' class='btn btn-sm btn-cms-default  addChildBtn'>Add Child</button>\n\
                                <br>\n\
                                <a href=\"javascript:\" class=\"text-danger removeMenuItem\"><i class=\"far fa-trash-alt\"></i></a> \n\
                            </div><span class='has_nest'></span>\n\
                        </div>";
                            nst += "<ul class=\"nest\"></ul>\n</li>";
                        }
                    });
                }
                var htm = "";
                var ID = chk.val();
                var label = $(this).find('.menuChoserLabel').html();
                var pUrl = $(this).find('.pageUrl').val();
                //console.log(ID);
                //console.log(label);
                htm += "<li class=\"ui-state-default\">";
                htm += "<input value=\"" + ID + "\" class=\"objID\" name=\"item\" type=\"hidden\">\n\
                        <input value=\"\" class=\"menu_postID\" name=\"item\" type=\"hidden\">\n\
                           <div class=\"item\">\n\
                            <div class=\"itemHeader\">\n\
                                <label class='menuLabel' org-val='" + label + "'>" + label + "</label>\n\
                                <a href=\"javascript:\" class=\"menuItemOpTg\"></a>\n\
                            </div>\n\
                            <div class=\"itemOption\">\n\
                                <label>Caption:</label>\n\
                                <div class='row'>\n\
                                    <div class='col-sm-6'>\n\
                                        <input class=\"form-control form-control-sm menu_item_caption\"  onkeyup=\"CaptionChange(this)\" type=\"text\">\n\
                                    </div>\n\
                                    <div class='col-sm-6'>\n\
                                        <input type='text'  class='form-control form-control-sm shortCode' placeholder='ShortCode'>\n\
                                    </div>\n\
                                 </div>\n\
                                <input type='text' placeholder='" + pUrl + "' class='form-control form-control-sm menu_item_url'>\n\
                                <div class='row'>\n\
                                <div class='col-sm-6'>\n\
                                <input type='text' class='form-control form-control-sm customTitle' placeholder='Custom Title'>\n\
                                </div>\n\
                                <div class='col-sm-6'>\n\
                                <input type='text' class='form-control form-control-sm customClass' placeholder='Custom Class'>\n\
                                </div>\n\
                                </div>\n\
                                <input type='checkbox' value='true' class='openNewWindow'><span class='checkLabel'>New Window</span>\n\
                                <input type='checkbox' value='true' class='disableUrl'><span class='checkLabel'>Disable URL</span>\n\
                                <input type='checkbox' value='true' class='appendChield'> <span class='checkLabel'>Append child</span>\n\
                                <button type='button' onclick='addChld(\"" + ID + "\",this)' class='btn btn-sm btn-cms-default  addChildBtn'>Add Child</button>\n\
                                <br>\n\
                                <a href=\"javascript:\" class=\"text-danger removeMenuItem\"><i class=\"far fa-trash-alt\"></i></a> \n\
                            </div><span class='has_nest'></span>\n\
                        </div>";
                htm += "<ul class=\"nest\">" + nst + "</ul>\n</li>";
                $("#menuEditItems").append(htm);
                mClps();
                mRemInit();
            } else {
                var nst = "";
                if ($(this).find(".subListItem li").length > 0) {
                    $(this).find(".subListItem li").each(function() {
                        var nst_chk = $(this).find('.menuChoserCheckBox');
                        if ($(nst_chk).is(":checked")) {
                            var nst_ID = nst_chk.val();
                            //alert(nst_ID);

                            var nst_label = $(this).find('.menuChoserLabel').html();
                            var nst_pUrl = $(this).find('.pageUrl').val();

                            nst += "<li class=\"ui-state-default\">";
                            nst += "<input value=\"" + nst_ID + "\" class=\"objID\" name=\"item\" type=\"hidden\">\n\
                        <input value=\"\" class=\"menu_postID\" name=\"item\" type=\"hidden\">\n\
                           <div class=\"item\">\n\
                            <div class=\"itemHeader\">\n\
                                <label class='menuLabel' org-val='" + nst_label + "'>" + nst_label + "</label>\n\
                                <a href=\"javascript:\" class=\"menuItemOpTg\"></a>\n\
                            </div>\n\
                            <div class=\"itemOption\">\n\
                                <label>Caption:</label>\n\
                                <div class='row'>\n\
                                    <div class='col-sm-6'>\n\
                                        <input class=\"form-control form-control-sm menu_item_caption\"  onkeyup=\"CaptionChange(this)\" type=\"text\">\n\
                                    </div>\n\
                                    <div class='col-sm-6'>\n\
                                        <input type='text'  class='form-control form-control-sm shortCode' placeholder='ShortCode'>\n\
                                    </div>\n\
                                 </div>\n\
                                <input type='text' placeholder='" + nst_pUrl + "' class='form-control form-control-sm menu_item_url'>\n\
                                <div class='row'>\n\
                                <div class='col-sm-6'>\n\
                                <input type='text' class='form-control form-control-sm customTitle' placeholder='Custom Title'>\n\
                                </div>\n\
                                <div class='col-sm-6'>\n\
                                <input type='text' class='form-control form-control-sm customClass' placeholder='Custom Class'>\n\
                                </div>\n\
                                </div>\n\
                                <input type='checkbox' value='true' class='openNewWindow'><span class='checkLabel'>New Window</span>\n\
                                <input type='checkbox' value='true' class='disableUrl'><span class='checkLabel'>Disable URL</span>\n\
                                <input type='checkbox' value='true' class='appendChield'> <span class='checkLabel'>Append child</span>\n\
                                <button type='button' onclick='addChld(\"" + nst_ID + "\",this)' class='btn btn-sm btn-cms-default  addChildBtn'>Add Child</button>\n\
                                <br>\n\
                                <a href=\"javascript:\" class=\"text-danger removeMenuItem\"><i class=\"far fa-trash-alt\"></i></a> \n\
                             </div>\n\
                        </div>";
                            nst += "<ul class=\"nest\"></ul>\n</li>";
                        }
                    });
                }
                $("#menuEditItems").append(nst);
                mClps();
                mRemInit();
            }
        })
        $('.menuChoserCheckBox').prop('checked', false);
        $(".selAll").prop('checked', false);
        filterItem();
    })

    //$(".selAll").change
    $('.selAll').change(function() {
        if ($(this).is(":checked")) {
            var cont = $(this).attr('control');
            $(cont).find("li").each(function() {
                var chk = $(this).find('.menuChoserCheckBox').prop('checked', true);
            });
        } else {
            var cont = $(this).attr('control');
            $(cont).find("li").each(function() {
                var chk = $(this).find('.menuChoserCheckBox').prop('checked', false);
            });
        }
    });

    $(".menuChoserCheckBox").change(function() {
        if ($(this).is(":checked")) {
            $(this).parent().find('.subListItem li').each(function() {
                $(this).find(".menuChoserCheckBox").prop('checked', true);
            });
        }
    });

    var oldContainer;
    $(".sortable").sortable({
        group: 'nested',
        afterMove: function(placeholder, container) {
            if (oldContainer != container) {
                if (oldContainer)
                    oldContainer.el.removeClass("active");
                container.el.addClass("active");

                oldContainer = container;
                clps();
            }
        },
        onDrop: function($item, container, _super) {
            container.el.removeClass("active");
            _super($item, container);
            // mySerialize();
            clps();
        },
        serialize: function($parent, $children, parentIsContainer) {
            var result = $.extend({}, $parent.data());
            if (parentIsContainer)
                return [$children]
            else if ($children[0]) {
                result.children = $children
            }
            //return result;
        }

    });

    $(".szS").click(function() {
        var _this = $(this);
        var str = $(".sortable").sortable('serialize').get();
        var texo = $("#menuSelect").val();
        if (texo == "") {
            msg("Select Menu First", 'R');
            return;
        }
        $(_this).html(loader + "&nbsp;&nbsp; Saving..");
        // alert(array);
        //mySerializeOBJ();
        var str = mySerializeOBJ();
        console.log(str);
        // var form_data = new FormData();
        // form_data.append('fd', str);
//        post_return("save_menu", form_data, function(res) {
//            $("#tstPrev").html(res);
//        })
//        $.ajax({
//            method: "GET",
//            url: "index.php?save_menu=" + str + "&texo=" + texo,
//            dataType: 'json',
//            success: function(res) {
//                loadObject($('#menuSelect').val());
//                msg(res['msg'], "G");
//                $(_this).html("Save Menu");
//            }
//        });


        var data = {save_menu: str};
        jQuery.post('index.php?texo=' + texo, data, function(res) {
            res = JSON.parse(res);
            loadObject($('#menuSelect').val());
            msg(res['msg'], "G");
            $(_this).html("Save Menu");
        });


    });

    function mySerialize() {

//        var a = [];
//        a[0] = [1,2,3]; 
//        a[1] = [4,5,6]; 
//
//        a[1][1] it is 5

        var data = [];
        $("#menuEditItems > li").each(function() {
            var objID = $(this).find(".objID").val();
            var capt = $(this).find(".menu_item_caption").val();
            var customUrl = $(this).find(".menu_item_url").val();

            var child = $(this).find(".nest > li");
            var nstCount = child.length;
            //console.log(nstCount);
            // console.log(capt);
            var ch = [];
            $(child).each(function() {
                var objID = $(this).find(".objID").val();
                var capt = $(this).find(".menu_item_caption").val();
                var customUrl = $(this).find(".menu_item_url").val();
                var child = $(this).find(".nest > li");
                ch.push(objID);
                ch.push(capt);
                var sch = [];
                $(child).each(function() {
                    var objID = $(this).find(".objID").val();
                    var capt = $(this).find(".menu_item_caption").val();
                    sch = [objID, capt, customUrl];
                });
                ch.push(sch);
            });
            data.push([objID, capt, customUrl, ch]);
        })
        var myJsonString = JSON.stringify(data);
        var parsed = JSON.parse(myJsonString);
        return myJsonString;
    }



    function mySerializeOBJ_() {

        var data = [];
        $("#menuEditItems > li").each(function() {
            var Ischild = $(this).find(".nest > li");
            var Cdata = [];
            $(Ischild).each(function() {
                var childItem = {};
                childItem.id = $(this).find(".objID").val();
                childItem.caption = $(this).find(".menu_item_caption").val();
                childItem.postID = $(this).find(".menu_postID").val();
                childItem.customUrl = $(this).find(".menu_item_url").val();
                childItem.customTitle = $(this).find(".customTitle").val();


                if ($(this).find(" > .item").find(".appendChield").is(":checked")) {
                    childItem.appendChield = 'true';
                } else {
                    childItem.appendChield = 'false';
                }
                if ($(this).find(" > .item").find(".disableUrl").is(":checked")) {
                    childItem.disableUrl = 'true';
                } else {
                    childItem.disableUrl = 'false';
                }

                if ($(this).find(" > .item").find(".openNewWindow").is(":checked")) {
                    childItem.openNewWindow = 'true';
                } else {
                    childItem.openNewWindow = 'false';
                }
                var IsSubchild = $(this).find(".nest > li");
                Cdata.push(childItem);
            });
            var item = {};
            item.id = $(this).find(".objID").val();
            item.caption = $(this).find(".menu_item_caption").val();
            item.child = Cdata;
            item.postID = $(this).find(".menu_postID").val();
            item.customUrl = $(this).find(".menu_item_url").val();
            item.customTitle = $(this).find(".customTitle").val();

            if ($(this).find(" > .item").find(".appendChield").is(":checked")) {
                item.appendChield = 'true';
            } else {
                item.appendChield = 'false';
            }

            if ($(this).find(" > .item").find(".disableUrl").is(":checked")) {
                item.disableUrl = 'true';
            } else {
                item.disableUrl = 'false';
            }

            if ($(this).find(" > .item").find(".openNewWindow").is(":checked")) {
                item.openNewWindow = 'true';
            } else {
                item.openNewWindow = 'false';
            }

            // data['item']=item;
            data.push(item);
        });

        var myJsonString = JSON.stringify(data);
        return myJsonString;
        //console.log(myJsonString);
    }


    function mySerializeOBJ() {
        var data = [];
        $("#menuEditItems > li").each(function() {
            var dd = buildObjRec($(this));
            data.push(dd);
            //console.log(dd);
        });
        var myJsonString = JSON.stringify(data);
        return myJsonString;
    }
    function buildObjRec(obj) {
        var $child = $(obj).find('> .nest > li');
        if ($child.length > 0) {
            var itm = mnuHtml2Object(obj);
            var Cdata = [];
            $($child).each(function() {
                Cdata.push(buildObjRec($(this)));
            });
            itm.child = Cdata;
            return itm;
        } else {
            return  mnuHtml2Object(obj);
        }
    }
    function mnuHtml2Object(objHtm) {
        var childItem = {};
        childItem.id = $(objHtm).find(".objID").val();
        childItem.caption = $(objHtm).find(".menu_item_caption").val();
        childItem.postID = $(objHtm).find(".menu_postID").val();
        childItem.customUrl = $(objHtm).find(".menu_item_url").val();
        childItem.customTitle = $(objHtm).find(".customTitle").val();
        childItem.customClass = $(objHtm).find(".customClass").val();
        childItem.shortCode = $(objHtm).find(".shortCode").val();

        if ($(objHtm).find(" > .item").find(".appendChield").is(":checked")) {
            childItem.appendChield = 'true';
        } else {
            childItem.appendChield = 'false';
        }
        if ($(objHtm).find(" > .item").find(".disableUrl").is(":checked")) {
            childItem.disableUrl = 'true';
        } else {
            childItem.disableUrl = 'false';
        }
        if ($(objHtm).find(" > .item").find(".openNewWindow").is(":checked")) {
            childItem.openNewWindow = 'true';
        } else {
            childItem.openNewWindow = 'false';
        }
        return childItem;
    }


    function SaveMenu(frm) {
        Post(frm, "", false, true);
        loadSelect();
        setTimeout(function() {
            window.location.reload();
        }, 1000);
    }

    function loadSelect() {
        get_return("loadSelect", function(res) {
            $("#menuSelectArea").html(res);
        });
    }
    function loadObject(id) {
        $("#wt").append(loader);
        get_return("loadChanger=" + id, function(res) {
            $("#menuEditItems").html(res);
            $("#wt").find(".spinLoader").remove();
            mClps();
            mRemInit();
            filterItem();
            ///clps();
        });
    }

    $('#menuSelect').on('change', function() {
        var primary = $("#primaryMenu").val();
        if (primary == $(this).val()) {
            $("#primaryMenu").prop('checked', true);
        } else {
            $("#primaryMenu").prop('checked', false);
        }
//loocation 
        $('.manuLoc').val($(this).val());
        $('.menuLocation').show();
        loadObject($(this).val());
        $(".manuLoc").each(function() {
            var th = $(this).parent().attr('dataselect');
            var cm = $('#menuSelect option:selected').text();
            // console.log(th, cm);
            if (th == cm) {
                $(this).prop('checked', true);
            } else {
                $(this).prop('checked', false);
            }
        })

    });


    $(".manuLoc").change(function() {
        var loc = $(this).attr('name');
        var menu = $('#menuSelect option:selected').text();
        if ($(this).prop("checked") == true) {
            patch = 'true';
            console.log('checked');
        } else {
            patch = 'false';
            console.log('Unchecked');
        }
        var data = {cls: 'menu_class', m: 'menuLocationSet', location: loc, menu: menu, patch: patch};
        jQuery.post('index.php', data, function(res) {
            if (res != '0') {
                msg(res, 'G');
            }
        });
    });


    function addChld(id, _this) {
        var data = {cls: 'menu_class', m: 'addChld', id: id};
        $(_this).html(loader);
        jQuery.post('index.php', data, function(res) {
            $(_this).parent().parent().parent().find('.nest').html(res);
            $(_this).html('Re-Add');
            mClps();
            mRemInit();
        });
    }


    function filterItem() {
        $(".MenuChoser li").show();
        $(".MenuChoser li").removeClass("added");

        $(".ui-state-default").each(function() {
            var id = $(this).find('.objID').val();
            
            if (id !== "" && id !== undefined) {
                if ($.isNumeric(id)) {
                    var targ = "#ID" + id;
                    $(targ).addClass('added');
                    $(targ).hide();
                } else {
                    var target = "#" + id;
                    if ($(target + " > .subListItem > li").length == 0) {
                        $(target).hide();
                        $(target).addClass('added');
                    }
                }
            }

        });
        //filter2ndStep();
    }
    function filter2ndStep() {
        $(".subListItem").each(function() {
            var el = $(this).children().length;
            var adEl = $(this).find(".added").length;
            if (el == adEl) {
                $(this).parent().hide();
            }
        });
    }

    $(function() {
        filterItem();
    });
</script>

