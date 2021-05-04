<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
global $DB;
?>
<h1>General Settings&nbsp;&nbsp;</h1>
<hr>
<form method="post" id="optionsFrm">
    <div id="settingsOptions" role="tablist" aria-multiselectable="false">
        <div class="card">
            <div class="card-header" role="tab" id="headingOne">
                <h5 class="mb-0">
                    <a data-toggle="collapse" data-parent="#settingsOptions" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Site Info<span href="javascript:" class="trigg"></span>
                    </a>
                </h5>
            </div>
            <div id="collapseOne" class="collapse show" role="tabpanel" aria-labelledby="headingOne">
                <div class="card-block">
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Site Name</label>
                        </div>
                        <div class='col-sm-9'>
                            <input onkeyup="$('#siteTitle').html(this.value)" type='text' name="options[site-name]" value="<?php echo get_option('site-name') ?>" class='form-control form-control-sm'>
                        </div>
                    </div>
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Tagline</label>
                        </div>
                        <div class='col-sm-9'>
                            <input type='text' name="options[tag]" value="<?php echo get_option('tag') ?>" class='form-control form-control-sm'>
                        </div>
                    </div>
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Site Url</label>
                        </div>
                        <div class='col-sm-9'>
                            <input type='text' name="options[site_url]" value="<?php echo get_option('site_url') ?>" class='form-control form-control-sm'>
                        </div>
                    </div>

                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Favicon</label>
                        </div>
                        <div class='col-sm-9'>
                            <label onclick="fbox(this)" load="c=forms&m=library&FieldId=favicon&calback=favicon" w="850" h="500" for="browse_pro" class="browse_fav"><i class="far fa-folder-open"></i></label>
                            <input name="options[favicon]" type="hidden" value="<?php echo get_option('favicon') ?>" id="favicon">
                            <div id='faviconSelectedImage'>
                                <?php
                                $idS = get_option('favicon');
                                if (!empty($idS)) {
                                    $idArray = explode(",", $idS);
                                    $iconData = get_post($idArray[0], 'guid');
                                    $src = $iconData['guid'];
                                    echo "<div class='favImg' id='item_$idArray[0]' ><img src='$src'>  <a class='ImgRemove' onclick='removeit(this)' href=\"javascript:\">×</a></div>";
                                }
                                ?>
                            </div>
                            <script>
                                function favicon(obj) {
                                    //$("#featureSelectedImage").html("");            
                                    $(obj).each(function() {
                                        var id = $(this).val();
                                        var img = $("#img_post_" + id).attr('src');
                                        if (img == undefined) {
                                        var iconClass = $("#post_" + id).attr('class');
                                        } else {
                                        var regex = /-([\d+]{3})([.])([a-z]{2,4})/;
                                                var str = img;
                                                let m;
                                                if ((m = regex.exec(str)) !== null) {
                                            // img = img.replace(m[1], "300");
                                            img = img.replace(m[0], m[2] + m[3]); //Full Version
                                            // alert(img);
                                            //                         m.forEach((match, groupIndex) => {
                                            //                            console.log(`Found match, group ${groupIndex}: ${match}`);
                                            //                         });
                                        }
                                    }


                                    var added = $("#favicon").val();
                                            var addFlag = true;
                                    if (added.indexOf(id) != -1)
                                    {
                                        //alert("found");
                                        addFlag = false;
                                    }
                                    var c = $(".selectedItem_single").length;
                                    c = c + 1;
                                    if (addFlag) {
                                        if (iconClass != undefined) {
                                            $("#faviconSelectedImage").append("<div class='favImg' id='item_" + id + "' ><i class='" + iconClass + "'></i> <a class='ImgRemove' onclick='removeit(this)' href=\"javascript:\">×</a></div>");
                                        } else {
                                            $("#faviconSelectedImage").append("<div class='favImg' id='item_" + id + "' ><img src='" + img + "'>  <a class='ImgRemove' onclick='removeit(this)' href=\"javascript:\">×</a></div>");
                                        }
                                    }
                                    }
                                    );
                                            $(".itemSel").each(function() {
                                        if ($(this).is(':checked') == false) {
                                            var Ckid = $(this).val();
                                            var remvID = "item_" + Ckid;
                                            $("#" + remvID).remove();
                                        }
                                    });
                                    }
                                    function removeit(_this) {
                                        var itm = $(_this).parent().attr("id");
                                        var id = itm.replace("item_", "");
                                        var str = $("#favicon").val();
                                        str = str.replace(id, "");
                                        $("#favicon").val(str.replace(/(^,)|(,$)/g, ""));
                                        $("#" + itm).remove();
                                    }
                            </script>
                        </div>
                    </div>
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Logo</label>
                        </div>
                        <div class='col-sm-9'>
                            <label onclick="fbox(this)" load="c=forms&m=library&FieldId=sitelogo&calback=sitelogo" w="850" h="500" for="browse_pro" class="browse_fav"><i class="far fa-folder-open"></i></label>
                            <input name="options[sitelogo]" type="hidden" value="<?php echo get_option('sitelogo') ?>" id="sitelogo">
                            <div id='sitelogoSelectedImage'>
                                <?php
                                $idS = get_option('sitelogo');
                                if (!empty($idS)) {
                                    $idArray = explode(",", $idS);
                                    $iconData = get_post($idArray[0], 'guid');
                                    $src = $iconData['guid'];
                                    echo "<div class='sitelogoImg' id='logo_item_$idArray[0]' ><img src='$src'>  <a class='ImgRemove' onclick='removeit(this)' href=\"javascript:\">×</a></div>";
                                }
                                ?>
                            </div>
                            <script>
                                    function sitelogo(obj) {
                                        //$("#featureSelectedImage").html("");            
                                        $(obj).each(function() {
                                            var id = $(this).val();
                                            var img = $("#img_post_" + id).attr('src');
                                            if (img == undefined) {
                                            var iconClass = $("#post_" + id).attr('class');
                                            } else {
                                            var regex = /-([\d+]{3})([.])([a-z]{2,4})/;
                                                    var str = img;
                                                    let m;
                                                    if ((m = regex.exec(str)) !== null) {
                                                // img = img.replace(m[1], "300");
                                                img = img.replace(m[0], m[2] + m[3]); //Full Version
                                                // alert(img);
                                                //                         m.forEach((match, groupIndex) => {
                                                //                            console.log(`Found match, group ${groupIndex}: ${match}`);
                                                //                         });
                                            }
                                        }


                                        var added = $("#sitelogo").val();
                                                var addFlag = true;
                                        if (added.indexOf(id) != -1)
                                        {
                                            //alert("found");
                                            addFlag = false;
                                        }
                                        var c = $(".selectedItem_single").length;
                                        c = c + 1;
                                        if (addFlag) {
                                            if (iconClass != undefined) {
                                                $("#sitelogoSelectedImage").append("<div class='sitelogoImg' id='logo_item_" + id + "' ><i class='" + iconClass + "'></i> <a class='ImgRemove' onclick='removeit(this)' href=\"javascript:\">×</a></div>");
                                            } else {
                                                $("#sitelogoSelectedImage").append("<div class='sitelogoImg' id='logo_item_" + id + "' ><img src='" + img + "'>  <a class='ImgRemove' onclick='removeit(this)' href=\"javascript:\">×</a></div>");
                                            }
                                        }
                                    }
                                    );
                                            $(".itemSel").each(function() {
                                        if ($(this).is(':checked') == false) {
                                            var Ckid = $(this).val();
                                            var remvID = "item_" + Ckid;
                                            $("#" + remvID).remove();
                                        }
                                    });
                                }
                                function removeit(_this) {
                                    var itm = $(_this).parent().attr("id");
                                    var id = itm.replace("item_", "");
                                    var str = $("#sitelogo").val();
                                    str = str.replace(id, "");
                                    $("#sitelogo").val(str.replace(/(^,)|(,$)/g, ""));
                                    $("#" + itm).remove();
                                }
                            </script>
                        </div>
                    </div>
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Meta Keyword</label>
                        </div>
                        <div class='col-sm-9'>
                            <input type="hidden" name="options[meta_keyword]" value="false">
                            <input value="true" <?php echo get_option('meta_keyword') == 'true' ? "checked" : "" ?> type="checkbox" name="options[meta_keyword]" id="metaKewEnbl"><label for="metaKewEnbl">&nbsp;Enable</label>
                        </div>
                    </div>
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Multi Language</label>
                        </div>
                        <div class='col-sm-9'>                           
                            <select id="multiLangSwtc" name="options[multiLangSwtc]" class="custom-select custom-select-sm w80">
                                <option value="false" <?php echo get_option('multiLangSwtc') == 'false' ? 'selected' : "" ?>>Disable</option>
                                <option value="true" <?php echo get_option('multiLangSwtc') == 'true' ? 'selected' : "" ?>>Enable</option>
                            </select>
                            <script>
                                $(function() {
                                    $("#multiLangSwtc").change(function() {
                                        var swtcVal = $(this).val();
                                        if (swtcVal == 'true') {
                                            $("#multiLang").show();
                                        } else {
                                            $("#multiLang").hide();
                                        }
                                    });
                                    var swtchVal = $("#multiLangSwtc").val();
                                    if (swtchVal == 'true') {
                                        $("#multiLang").show();
                                    } else {
                                        $("#multiLang").hide();
                                    }
                                });</script>
                        </div>
                    </div>
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Debug Mode</label>
                        </div>
                        <div class='col-sm-9'>
                            <input type="checkbox" id="DebugMode"  value="1" ><label for="DebugMode">&nbsp;&nbsp;Enable Debug mode for find bugs</label>
                        </div>
                        <script>
                            $("#DebugMode").change(function() {
                                if ($(this).is(":checked")) {
                                    debugMood('true')
                                } else {
                                    debugMood('false')
                                }
                            });
                            function debugMood(act) {
                                var data = {ajx_action: "debugMood", act: act};
                                jQuery.post('index.php', data, function(response) {

                                });
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" role="tab" id="headingOne">
                <h5 class="mb-0">
                    <a data-toggle="collapse" data-parent="#settingsOptions" href="#collapsePermalink" aria-expanded="true" aria-controls="collapseOne">
                        Permalink <span href="javascript:" class="trigg"></span>
                    </a>
                </h5>
            </div>
            <div id="collapsePermalink" class="collapse show" role="tabpanel" aria-labelledby="headingcollapsePermalink">
                <div class="card-block">
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Add (.html) Ext</label>
                        </div>
                        <div class='col-sm-9'>
                            <select name="options[html_ext]" class="custom-select custom-select-sm w80">
                                <option value="false" <?php echo get_option('html_ext') == "false" ? "selected" : "" ?>>No</option>
                                <option value="true" <?php echo get_option('html_ext') == "true" ? "selected" : "" ?>>Yes</option>
                            </select>
                        </div>
                    </div>
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Remove Parent slug</label>
                        </div>
                        <div class='col-sm-9'>
                            <select name="options[removeParentSlug]" class="custom-select custom-select-sm w80">
                                <option value="true" <?php echo get_option('removeParentSlug') == "true" ? "selected" : "" ?>>Yes</option>
                                <option value="false" <?php echo get_option('removeParentSlug') == "false" ? "selected" : "" ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Enable to slug</label>
                        </div>
                        <div class='col-sm-9'>
                            <input type="hidden" value="" name="options[enable_type_slug][]">
                            <?php
                            global $C_POST_TYPE;
                            $enable_type_slug = unserialize(get_option('enable_type_slug'));
                            foreach ($C_POST_TYPE as $type => $tOption) {
                                //|| (isset($tOption['in_slug']) && $tOption['in_slug'] == true) 
                                $chked = @in_array($type, $enable_type_slug) ? "checked" : "";
                                echo "<input $chked type=\"checkbox\" name=\"options[enable_type_slug][]\" value=\"$type\" id='ctype_$type'>&nbsp;<label for=\"ctype_$type\">$tOption[label]</label>&nbsp;&nbsp;";
                            }
                            ?>
                        </div>
                    </div>
                    <div class='row fieldRow'>
                        <div class="col-sm-5">
                            <label><?php echo domain(); ?></label>
                        </div>
                        <div class='col-sm-7'>
                            <input type="text"  name="options[permalink]" value=" <?php echo get_option('permalink') ?>" class="form-control form-control-sm">
                            <span class="comment">%category%,%postname%,</span>
                        </div>
                    </div>
                    <button onclick='$("#reWrDev").slideToggle()' class="btn btn-cms-default" type="button">Developer Preview</button>
                    <div id='reWrDev'  class="devPreview collapse">
                        <button type="button" class='btn btn-cms-default float-right' onclick='resetRWR(this)'>Reset</button>
                        <table class="table table-responsive table-cms table-striped">
                            <tr> 
                                <th>Priority</th>
                                <th>Expression</th>
                                <th>Vars</th>
                            </tr>
                            <?php
                            global $RWR;
                            foreach ($RWR->roles as $priority => $rr) {
                                ?>
                                <tr>
                                    <td><?php echo $priority ?></td>
                                    <td><?php echo $rr[0] ?></td>
                                    <td><?php echo $rr[1] ?></td>
                                </tr>
                                <?php
                            }
                            ?> 
                        </table>

                    </div>
                </div>
                <script>
                    function resetRWR(_this) {
                        $(_this).append(loader);
                        var data = {ajx_action: "resetRWR"};
                        jQuery.post('index.php', data, function(response) {
                            response = JSON.parse(response);
                            $(".spinLoader").remove();
                            if (response['error'] == "0") {
                                msg(response['msg'], 'G');
                            } else {
                                msg(response['msg'], 'R');
                            }
                        });
                        //console.log('called');
                    }
                </script>
            </div>
        </div>

        <div class="card">
            <div class="card-header" role="tab" id="headingCatPageSetup">
                <h5 class="mb-0">
                    <a data-toggle="collapse" data-parent="#settingsOptions" href="#CatPageSetup" aria-expanded="true" aria-controls="CatPageSetup">
                        Category Page Setup<span href="javascript:" class="trigg"></span>
                    </a>
                </h5>
            </div>
            <div id="CatPageSetup" class="collapse" role="tabpanel" aria-labelledby="headingCatPageSetup">
                <?php
                $getCategoryPageOption = get_option('categoryPage');
                if ($getCategoryPageOption != "") {
                    $getCategoryPageOption = unserialize($getCategoryPageOption);
                } else {
                    $getCategoryPageOption = array('post_per_page' => 12, 'order' => 'DESC', 'column' => 4, 'tcol' => 3, 'mcol' => 2, 'orderby' => 'ID', 'link' => 'true', 'get-price' => 'no');
                }
                ?>
                <div class="card-block">
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Column :</label>
                        </div>
                        <div class='col-sm-4'>
                            <select name='options[categoryPage][column]'  class="custom-select custom-select-sm">
                                <option value="1" <?php echo $getCategoryPageOption['column'] == 1 ? 'selected' : '' ?>>1</option>
                                <option value="3" <?php echo $getCategoryPageOption['column'] == 3 ? 'selected' : '' ?>>3</option>
                                <option value="2" <?php echo $getCategoryPageOption['column'] == 2 ? 'selected' : '' ?>>2</option>
                                <option value="4" <?php echo $getCategoryPageOption['column'] == 4 ? 'selected' : '' ?>>4</option>
                                <option value="6" <?php echo $getCategoryPageOption['column'] == 6 ? 'selected' : '' ?>>6</option>
                            </select>
                        </div>
                        <div class='col-sm-3'>
                            <select name='options[categoryPage][tcol]'  class="custom-select custom-select-sm">
                                <option value="">Default</option>
                                <option value="1" <?php echo $getCategoryPageOption['tcol'] == 1 ? 'selected' : '' ?>>1</option>
                                <option value="3" <?php echo $getCategoryPageOption['tcol'] == 3 ? 'selected' : '' ?>>3</option>
                                <option value="2" <?php echo $getCategoryPageOption['tcol'] == 2 ? 'selected' : '' ?>>2</option>
                                <option value="4" <?php echo $getCategoryPageOption['tcol'] == 4 ? 'selected' : '' ?>>4</option>
                                <option value="6" <?php echo $getCategoryPageOption['tcol'] == 6 ? 'selected' : '' ?>>6</option>
                            </select>
                        </div>
                        <div class='col-sm-2'>
                            <select name='options[categoryPage][mcol]' class="custom-select custom-select-sm">

                                <option value="">Default</option>
                                <option value="1" <?php echo $getCategoryPageOption['mcol'] == 1 ? 'selected' : '' ?>>1</option>
                                <option value="3" <?php echo $getCategoryPageOption['mcol'] == 3 ? 'selected' : '' ?>>3</option>
                                <option value="2" <?php echo $getCategoryPageOption['mcol'] == 2 ? 'selected' : '' ?>>2</option>
                                <option value="4" <?php echo $getCategoryPageOption['mcol'] == 4 ? 'selected' : '' ?>>4</option>
                                <option value="6" <?php echo $getCategoryPageOption['mcol'] == 6 ? 'selected' : '' ?>>6</option>
                            </select>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-sm-3">
                            <label>Pagination</label> 
                        </div>
                        <div class="col-sm-9">
                            <select  name='options[categoryPage][pagination]' class="custom-select custom-select-sm w80 pagin">
                                <option value="yes" <?php echo @$getCategoryPageOption['pagination'] == 'yes' ? 'selected' : '' ?>>Yes</option>
                                <option value="no" <?php echo @$getCategoryPageOption['pagination'] == 'no' ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Items Per Page :</label>
                        </div>
                        <div class='col-sm-9'>
                            <input class="form-control form-control-sm w80" name='options[categoryPage][post_per_page]' type="text" value="<?php echo $getCategoryPageOption['post_per_page'] ?>">
                        </div>
                    </div>

                    <div class='row'>
                        <div class="col-sm-3">
                            <label>Order By:</label>
                        </div>
                        <div class='col-sm-9'>
                            <select name='options[categoryPage][orderby]' class="custom-select custom-select-sm w80 ">
                                <option value="ID" <?php echo $getCategoryPageOption['orderby'] == 'ID' ? 'selected' : '' ?>>ID</option>
                                <option value="post_date_gmt" <?php echo $getCategoryPageOption['orderby'] == 'post_date_gmt' ? 'selected' : '' ?>>Date</option>
                                <option value="post_title" <?php echo $getCategoryPageOption['orderby'] == 'post_title' ? 'selected' : '' ?>>Title</option>
                            </select>
                        </div>
                    </div>
                    <div class='row'>
                        <div class="col-sm-3">
                            <label>Order :</label>
                        </div>
                        <div class='col-sm-9'>
                            <select name='options[categoryPage][order]' class="custom-select custom-select-sm w80 ">
                                <option value="DESC" <?php echo $getCategoryPageOption['order'] == 'DESC' ? 'selected' : '' ?>>DESC</option>
                                <option value="ASC" <?php echo $getCategoryPageOption['order'] == 'ASC' ? 'selected' : '' ?>>ASC</option>
                                <option value="rand" <?php echo $getCategoryPageOption['order'] == 'rand' ? 'selected' : '' ?>>Random</option>
                            </select>
                        </div>
                    </div>
                    <div class='row  fieldRow'>
                        <div class="col-sm-3">
                            <label>Excerpt :</label>
                        </div>
                        <div class='col-sm-9'>
                            <input type="text" name='options[categoryPage][excerpt]' class="form-control form-control-sm w80 excp" value="<?php echo empty($getCategoryPageOption['excerpt']) ? 'no' : $getCategoryPageOption['excerpt'] ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <label>Image</label> 
                        </div>
                        <div class="col-sm-9">
                            <select name='options[categoryPage][img]' class="custom-select custom-select-sm w80">
                                <option value="yes" <?php echo @$getCategoryPageOption['img'] == 'yes' ? 'selected' : '' ?>>Yes</option>
                                <option value="no"  <?php echo @$getCategoryPageOption['img'] == 'no' ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class='row'>
                        <div class="col-sm-3">
                            <label>Enable Link :</label>
                        </div>
                        <div class='col-sm-9'>
                            <select name='options[categoryPage][link]' class="custom-select custom-select-sm w80 ">
                                <option value="true" <?php echo @$getCategoryPageOption['link'] == 'true' ? 'selected' : '' ?>>Yes</option>
                                <option value="false" <?php echo @$getCategoryPageOption['link'] == 'false' ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                    </div>
                    <div class='row'>
                        <div class="col-sm-3">
                            <label>Get Quote :</label>
                        </div>
                        <div class='col-sm-9'>
                            <select name='options[categoryPage][get-price]' class="custom-select custom-select-sm w80">
                                <option value="no" <?php echo @$getCategoryPageOption['get-price'] == 'no' ? 'selected' : '' ?>>No</option>
                                <option value="yes" <?php echo @$getCategoryPageOption['get-price'] == 'yse' ? 'selected' : '' ?>>Yes</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" role="tab" id="headingReading">
                <h5 class="mb-0">
                    <a data-toggle="collapse" data-parent="#settingsOptions" href="#Reading" aria-expanded="true" aria-controls="Reading">
                        Reading<span href="javascript:" class="trigg"></span>
                    </a>
                </h5>
            </div>
            <div id="Reading" class="collapse " role="tabpanel" aria-labelledby="headingReading">
                <div class="card-block">
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Front Page</label>
                        </div>
                        <div class='col-sm-9'>
                            <?php
                            echo $POSTS->post_select(
                                    get_option('front_page'), "options[front_page]", "custom-select custom-select-sm", "", array(
                                'post_type' => "page",
                                'selectFields' => 'ID,post_title'
                                    )
                            )
                            ?>
                        </div>
                    </div>
                    <div id="multiLang" class="collapse">
                        <?php
                        if (class_exists('siteLanguages')) {

                            $lngs = new siteLanguages();
                            foreach ($lngs->languages as $k => $val) {
                                $html = "";
                                // var_dump($lngs->defaultLang);
                                if ($lngs->defaultLang == strtolower($k)) {
                                    continue;
                                }
                                ?>
                                <div class='row fieldRow'>
                                    <div class="col-sm-3">
                                        <label>Front Page (<?php echo $val[0] ?>)</label>
                                    </div>
                                    <div class='col-sm-9'>
                                        <?php
                                        $select = get_option("front_page_$k");
                                        $name = "options[front_page_$k]";
                                        $class = "custom-select custom-select-sm";
                                        $id = "";
                                        $arg = array(
                                            'post_type' => "page",
                                            'selectFields' => 'ID,post_title'
                                        );
                                        $default = array(
                                            'numberposts' => -1,
                                            'post_type' => "page",
                                            'selectFields' => 'ID,post_title',
                                        );
                                        $arg = array_merge($default, $arg);
                                        $posts = get_posts($arg);
                                        if (count($posts) > 0) {
                                            $html.="<select name=\"$name\" class='$class' id='$id'>";
                                            $html.="<option value=''>Select</option>";
                                            foreach ($posts as $post) {
                                                $lng = get_post_meta($post['ID'], 'lng');
                                                if ($lng != $k) {
                                                    continue;
                                                }
                                                $sel = !empty($select) && $select == $post[ID] ? "selected" : "";
                                                $html.="<option value='$post[ID]' $sel>$post[post_title]</option>";
                                            }
                                            $html.="</select>";
                                        }
                                        echo $html;
                                        ?>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <!--               <div class='row fieldRow'>
                                            <div class="col-sm-3">
                                                <label>Blog Page</label>
                                            </div>
                                            <div class='col-sm-9'>
                    <?php
//                            echo $POSTS->post_select(
//                                    get_option('blog_page'), "options[blog_page]", "custom-select custom-select-sm", "", array(
//                                'post_type' => array("page"),
//                                'selectFields' => 'ID,post_title'
//                                    )
//                            )
                    ?>
                                            <span class="comment">Attention !, if change the blog page than make sure Post url re-write role change by slug  </span>
                                        </div>
                                    </div>-->
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>404 to Redirect</label>
                        </div>
                        <div class='col-sm-9'>
                            <?php
                            echo $POSTS->post_select(
                                    get_option('red_not_found'), "options[red_not_found]", "custom-select custom-select-sm", "", array(
                                'post_type' => array("page", "post"),
                                'selectFields' => 'ID,post_title'
                                    )
                            )
                            ?>
                        </div>
                    </div>

                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>404 Error Code</label>
                        </div>
                        <div class='col-sm-9'>
                            <select name="options[redirect_code]" class='custom-select custom-select-sm w80'>
                                <?php $reDirCode = get_option('redirect_code'); ?>
                                <option value="404" <?php echo $reDirCode == '404' ? "selected" : "" ?>>404</option>
                                <option value="301" <?php echo $reDirCode == '301' ? "selected" : "" ?>>301</option>
                                <option value="302" <?php echo $reDirCode == '302' ? "selected" : "" ?>>302</option>
                            </select>
                        </div>
                    </div>

                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Search Engine Visibility</label>
                        </div>
                        <div class='col-sm-9'>
                            <input type="checkbox" id="sEv" name="options[search_engine_visibility]" value="1" <?php echo get_option('search_engine_visibility') == '1' ? 'checked' : '' ?> ><label for="sEv">&nbsp;&nbsp;Discourage search engines from indexing this site</label>
                        </div>
                    </div>


                    <script>
                        $("#sEv").change(function() {
                            var se = true;
                            if ($(this).prop('checked')) {
                                var se = false;
                            }
                            var data = {ajx_action: "SEVisibility", SE: se};
                            jQuery.post('index.php', data, function(response) {

                            });
                        });</script>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" role="tab" id="headingDbUp">
                <h5 class="mb-0">
                    <a data-toggle="collapse" data-parent="#settingsOptions" href="#DbUp" aria-expanded="true" aria-controls="DbUp">
                        Modify Database<span href="javascript:" class="trigg"></span>
                    </a>
                </h5>
            </div>
            <div id="DbUp" class="collapse" role="tabpanel" aria-labelledby="headingDbUp">
                <div class="card-block">
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Find:</label>
                        </div>
                        <div class='col-sm-9'>
                            <input class="form-control form-control-sm" name='' type="text" value="" id="dbReplaceFind">
                        </div>
                    </div>
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Replace:</label>
                        </div>
                        <div class='col-sm-9'>
                            <input class="form-control form-control-sm" name='' type="text" value="" id="dbReplace">
                        </div>
                    </div>
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                        </div>
                        <div class='col-sm-9'>
                            <button type='button' class="btn btn-cms-default" onclick="replaceDb(this)">Replace</button>
                        </div>
                    </div>

                </div>
            </div>
            <script>
                function replaceDb(_this) {
                    $(_this).append(loader);
                    var find = $("#dbReplaceFind").val();
                    var replce = $("#dbReplace").val();
                    var data = {ajx_action: "replaceDb", fnd: find, replce: replce};
                    jQuery.post('index.php', data, function(response) {
                        response = JSON.parse(response);
                        $(".spinLoader").remove();
                        if (response['error'] == "0") {
                            msg(response['msg'], 'G');
                        } else {
                            msg(response['msg'], 'R');
                        }
                    });
                    //console.log('called');
                }
            </script>
        </div>
        <div class="card">
            <div class="card-header" role="tab" id="headingMedia">
                <h5 class="mb-0">
                    <a data-toggle="collapse" data-parent="#settingsOptions" href="#Media" aria-expanded="true" aria-controls="Media">
                        File Settings<span href="javascript:" class="trigg"></span>
                    </a>
                </h5>
            </div>
            <div id="Media" class="collapse" role="tabpanel" aria-labelledby="headingMedia">
                <div class="card-block">
                    <p class="comment">Resize Uploaded images with size below:</p>
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Thumbnail Width (Max)</label>
                        </div>
                        <div class='col-sm-9'>
                            <input class="form-control w80 form-control-sm" name='options[upload-size][]' type="number" value="100" id="example-number-input">
                        </div>
                    </div>
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Small Width (Max)</label>
                        </div>
                        <div class='col-sm-9'>
                            <input class="form-control w80 form-control-sm" name='options[upload-size][]' type="number" value="300" id="example-number-input">
                        </div>
                    </div>
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Medium Width (Max)</label>
                        </div>
                        <div class='col-sm-9'>
                            <input class="form-control w80 form-control-sm" name='options[upload-size][]' type="number" value="768" id="example-number-input">
                        </div>
                    </div>
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            <label>Large Width (Max)</label>
                        </div>
                        <div class='col-sm-9'>
                            <input class="form-control w80 form-control-sm" name='options[upload-size][]' type="number" value="1024" id="example-number-input">
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <input id="mdate" type="date" value="<?php echo date('Y-m-d') ?>" class="form-control form-control-sm float-left" style="max-width:135px;margin:5px 5px"><button type='button' class="btn btn-cms-default float-left" style="margin:5px 0" onclick="DateModifyAll(this)">Date Modify</button>
                        </div>
                        <div class="col-sm-12">
                            <span class="comment">To change modify-date of all files in content directory. Last modified on <font color='green'><?php echo date('d/m/Y', get_option('fileLastModify')); ?></font></span>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header" role="tab" id="headingUpdate">
                <h5 class="mb-0">
                    <a data-toggle="collapse" data-parent="#settingsOptions" href="#Update" aria-expanded="true" aria-controls="Update">
                        Update Setting<span href="javascript:" class="trigg"></span>
                    </a>
                </h5>
            </div>
            <div id="Update" class="collapse" role="tabpanel" aria-labelledby="headingUpdate">
                <div class="card-block">
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            Automatic Update
                        </div>
                        <div class='col-sm-9'>
                            <select name="options[auto_update]" class="custom-select custom-select-sm w80">
                                <option value="desable">Disable</option>
                                <option value="enable">Enable</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" role="tab" id="headingMedia">
                <h5 class="mb-0">
                    <a data-toggle="collapse" data-parent="#settingsOptions" href="#SerVerInfo" aria-expanded="true" aria-controls="SerVerInfo">
                        Info <span href="javascript:" class="trigg"></span>
                    </a>
                </h5>
            </div>
            <div id="SerVerInfo" class="collapse" role="tabpanel" aria-labelledby="headingSerVerInfo">
                <div class="card-block">
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            MySql Version
                        </div>
                        <div class='col-sm-9'>
                            <?php
                            echo $DB->version
                            ?>
                        </div>
                    </div>
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                            PHP Version
                        </div>
                        <div class='col-sm-9'>
                            <?php
                            echo phpversion();
                            ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="settings_updateArea">
            <button type="button" class='btn btn-cms-primary updateSettingsBtn float-right' onclick="saveOptions(optionsFrm, this)">Update Settings</button>
        </div>
    </div>  
</form>
<script>
    function saveOptions(frm, _this) {
        $(_this).before(loader);
        post_return('', $(frm).serialize(), function(res) {
            var obj = JSON.parse(res);
            // alert(obj['msg']);
            $(".spinLoader").remove();
            msg(obj['msg'], 'G');
        })
    }

</script>
