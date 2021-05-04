<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of forms_class
 *
 * @author nrb
 */
class forms_class {

    //put your code here
    public function __construct() {
//        if (isset($_GET['m'])) {
//            $module = $_GET['m'];
//            if (method_exists($this, $module)) {
//                $this->$module();
//            } else {
//                $this->notFound();
//            }
//        }
    }

    public function theme_upload() {
        ?>
        <div class="uploaderArea ">
            <label id='browseTrig' for="file"><i class="fas fa-upload"></i><br>Upload (.zip) file</label>
            <input id="file" onchange="themeUpload(this, 'theme')" type="file" class="collapse">
        </div>
        <div class="thm-progress"><div></div></div>
        <div class="afterUpload"></div>
        <?php
    }

    public function plugins_upload() {
        pluginsStore();
    }

    public function anotherForm() {
        ?>
        <form action="index.php" method="post" enctype="multipart/form-data">
            <input type="file" name="myfile"><br>
            <input type="submit" value="Upload File to Server">
        </form>
        <i class="far fa-file-zip"></i>
        <div class="progress">
            <div class="bar"></div >
            <div class="percent">0%</div >
        </div>

        <div id="status"></div>
        <?php
    }

    public function texonomy_add() {
        $tex = $_POST['texo'];
        ?>
        <form id="termAdd">
            <h4>Add <?php echo ucfirst($tex) ?></h4><hr>
            <input type='hidden' name="texo[taxonomy]" value="<?php echo $tex ?>">
            <label>Name</label>
            <input type="text" id="name" name="term[name]" class="form-control form-control-sm">
            <label>Parent </label>
            <div id="TermSelect">
                <?php
                global $TERM;
                $texos = $TERM->texoListRow($tex, true);
                $html = "<select class=\"custom-select custom-select-sm texoSelect\" name='term[term_group]'>";
                $html.="<option value=''>Select Parent</option>";
                foreach ($texos as $texo) {
                    $html.="<option value='$texo[taxonomy_id]'>$texo[name]</option>";
                    //var_dump($texo['taxonomy_id']);
                    $chld = $TERM->texoListRow($tex, $texo['term_id']);
                    //var_dump($chld);
                    foreach ($chld as $chldtexo) {
                        $html.="<option value='$chldtexo[taxonomy_id]'>&nbsp;» $chldtexo[name]</option>";
                    }
                }
                $html.='</select>';
                echo $html;
                ?>
            </div>
            <label>Slug</label>
            <input type="text" id="slug" name='term[slug]' class="form-control form-control-sm">
            <span class="comment">The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</span>          
            <button type="button" onclick="SaveTexo()" class="btn btn-cms-primary">Save</button>

        </form>
        <?php
    }

    public function htmlSpcialDecode($str) {
        $str = str_replace("&#039;", "'", $str);
        return htmlspecialchars_decode($str, ENT_COMPAT);
    }

    public function texonomy_edit() {
        global $TERM;
        $tex = $_POST['texo'];
        $termID = $_POST['id'];

        $termData = $TERM->get_term($termID);
        $meta = array();
        if (isset($termData['meta'])) {
            foreach ($termData['meta'] as $k => $v) {
                if (!is_array($v)) {
                    $meta[$k] = $this->htmlSpcialDecode($v);
                } else {
                    $meta[$k] = $v;
                }
            }
        }
        $termData['meta'] = $meta;
        //echo "<pre>";
        //var_dump($termData['meta']);

        $metaJson = json_encode(@$termData['meta']);
        $termSlug = get_term_link($termData);
        //var_dump($termSlug);
        //Update <?php echo ucfirst($tex) \\
        ?>
        <div class="row">
            <div class="col-sm-9">           
                <form id="termAdd" method="post">
                    <h5><button type="button"  class='btn btn-cms-default' onclick="SaveTexo(this)">Update <?php echo $tex ?></button><span class="texoEditClose" onclick="upCancl()">×</span></h5><hr>
                    <input type="hidden" name="meta[sel_texo]" value="">
                    <div class='row'>
                        <div class="col-sm-4">
                            <label>Name</label>
                            <input type="text" value="<?php echo $termData['name'] ?>" id="name" name="term[name]" class="form-control form-control-sm">
                        </div>
                        <div class="col-sm-8">
                            <label>Slug</label>
                            <input type="text" id="slug" value="<?php echo $termData['slug'] ?>" name='term[slug]' class="form-control form-control-sm">
                            <span class="comment">URL-friendly version of the name. It is usually all lowercase and contains only letters and numbers. <a target="_new" id="termLink" href='<?php echo $termSlug ?>'>here</a></span>
                        </div>
                    </div>
                    <div class="Editor texo-editor">
                        <label>Description </label>
                        <textarea id="PostEditor" type="text" name="texo[description]" class="form-control"><?php echo $termData['description'] ?></textarea>
                    </div>
                    <div class="EditorBottomArea">
                        <?php
                        editor_bottom('term');
                        ?>
                    </div>

                    <label>Parent </label>
                    <div id="TermSelect">
                        <?php
                        global $TERM;
                        $texos = $TERM->texoListRow($tex);
                        $html = "<select class=\"custom-select custom-select-sm texoSelect\" name='term[term_group]'>";
                        $html.="<option value=''>Select Parent</option>";
                        foreach ($texos as $texo) {
                            $sel = $termData['term_group'] == $texo['taxonomy_id'] ? "selected" : "";
                            $html.="<option value='$texo[taxonomy_id]' $sel>$texo[name]</option>";
                        }
                        $html.='</select>';
                        echo $html;
                        ?>
                    </div>
                    <?php
                    get_termMeta_field($tex, @$termData['meta']);
                    $enabledTaxos = enableSlugCPTypeArr('t'); //Enabled texonomys by custom post slug controller -> settings 
                    //var_dump($enabledTaxos, $tex);
                    if (in_array($tex, $enabledTaxos)) {
                        ?>
                        <label>Disable Slug</label>
                        <select name="meta[disableSlug]" id="disableSlug" class="custom-select custom-select-sm w80">
                            <option value="false">No</option>
                            <option value="true">Yes</option>
                        </select>
                        <br>
                        <?php
                    } else {
                        echo "<p style='color:#f00;padding:5px;border:1px solid #f00'>This Taxonomy's URL Disabled</p>";
                    }
                    $imgStr = "";
                    if (!empty($termData['meta']['texoThumbni'])) {
                        $texoThambsStr = $termData['meta']['texoThumbni'];
                        $texoThambs = explode(",", $texoThambsStr);
                        //var_dump($texoThambs);
                        shuffle($texoThambs);
                        $imguid = get_post($texoThambs[0], "guid");
                        $meta = get_post_metas(@$id);
                        $ThmbImgSrc = get_attachment_src($imguid['guid'], 300);
                        $imgStr = "<div class=\"texoThumbniImg\"><img src=\"$ThmbImgSrc\"></div>";
                    }
                    ?>
                    <label>Thumbnail </label>
                    <div onclick="fbox(this)" load="c=forms&m=library&FieldId=texoThumbni&calback=texoThumbni" w="850" h="500" id="texoThumbniSelectedImage"  class="TexoThumbnail"><i class="fas fa-camera"></i><?php echo $imgStr ?></div>
                    <input name="meta[texoThumbni]" type="hidden" value="<?php echo @$termData['meta']['texoThumbni'] ?>" id="texoThumbni">




                    <input type='hidden' name="texo[taxonomy]" value="<?php echo $tex ?>">
                    <br>
                    <button type="button"  class='btn btn-cms-primary' onclick="SaveTexo(this)">Update <?php echo $tex ?></button>
                    <button type="button" id="UpCancl" class='btn btn-cms-default' onclick="upCancl()">Cancel</button>
                    <input type="hidden" value="<?php echo $termData['taxonomy_id'] ?>" id="tex_id" name="tex_id">
                    <input type="hidden" value="<?php echo $termData['term_id'] ?>" id="term_id" name="term_id">
                    <br><br>
                </form>
            </div>
            <div class="col-sm-3">
                <ul class='texoListEdit'>
                    <h5><?php echo $tex ?></h5>
                    <?php
                    global $DB;
                    $texonomys = $DB->select("term_taxonomy as tx left join terms as trm on tx.term_id=trm.term_id", "*", "tx.taxonomy='$tex'  and trm.term_group=0");
                    $sub = "";
                    //$texos = $TERM->texoListRow($tex);
                    //var_dump($texos);
                    foreach ($texonomys as $texo) {
                        $Childrows = $DB->select("term_taxonomy as tx left join terms as trm on tx.term_id=trm.term_id", "*", "tx.taxonomy='$tex' and trm.term_group=$texo[taxonomy_id]");
                        $is_Child = count($Childrows) > 0 ? true : false;

                        //var_dump($Childrows);
                        $cht = "";
                        $ch = $texo['term_group'] != '0' ? "» " : "";
                        if ($is_Child) {
                            $cht = $is_Child ? "<span class='has_subTexo' onclick='openSubTexo(this)'></span>" : "";
                            $sub = "<ul class='texoChose-sub'>";
                            foreach ($Childrows as $chld) {
                                $Chcurr = $termID == $chld['term_id'] ? "current" : "";
                                $sub.="<li class='$Chcurr'><div><a href='javascript:' onclick=\"EditTexo('$tex',$chld[term_id])\">» $chld[name]</a></div></li>";
                            }
                            $sub.="</ul>";
                        }
                        $curr = $termID == $texo['term_id'] ? "current" : "";
                        echo "<li class='$curr'><div>$cht<a href='javascript:' onclick=\"EditTexo('$tex',$texo[term_id])\">$texo[name]</a>$sub</div></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
        <script>
            $(function() {
                var res = [<?php echo $metaJson ?>];
                console.log(res);
                if (res) {
                    var data = res[0];
                    $.each(data, function(i, item) {
                        // console.log(item);
                        if (Array.isArray(data[i])) {
                            $.each(data[i], function(k, v) {
                                if ($("#" + i + "_" + v).length) {
                                    $("#" + i + "_" + v).prop("checked", true);
                                    $('.myCheckbox').attr('checked', true);
                                }
                            });
                        } else {
                            if ($('#' + i).is(':checkbox') && data[i] === 'true') {
                                $('#' + i).prop('checked', true);
                                console.log($('#' + i));
                            }

                            if (!$('#' + i).is(':checkbox')) {
                                $("#" + i).val(data[i]);
                            }
                        }
                    });
                }
            });


            function texoThumbni(obj) {
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
                        img = img.replace(m[0], m[2] + m[3]);//Full Version
                    }
                    }
                    var added = $("#texoThumbni").val();
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
                            $("#texoThumbniSelectedImage").append("<div class='texoThumbniImg' id='logo_item_" + id + "' ><i class='" + iconClass + "'></i></div>");
                        } else {
                            $("#texoThumbniSelectedImage").append("<div class='texoThumbniImg' id='logo_item_" + id + "' ><img src='" + img + "'></div>");
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
                        var str = $("#texoThumbni").val();
                        str = str.replace(id, "");
                        $("#texoThumbni").val(str.replace(/(^,)|(,$)/g, ""));
                        $("#" + itm).remove();
                    }
        </script>
        <script>
                    function upCancl() {
                        SaveTexo();
                        $("#texoEdit").html("");
                        $("#DataLIst").show();

                    }

        </script>

        <?php
    }

    public function texonomy($tex) {
        ?>
        <form id="termAdd" method="post">
            <input type="hidden" name="meta[sel_texo]" value="">
            <label>Name</label>
            <input type="text" id="name" name="term[name]" class="form-control form-control-sm">
            <label>Slug</label>
            <input type="text" id="slug" name='term[slug]' class="form-control form-control-sm">
            <span class="comment">The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</span>
            <label>Description </label>
            <textarea id="description" type="text" name="texo[description]" class="form-control"></textarea>
            <label>Parent </label>
            <div id="TermSelect"></div>
            <?php
            get_termMeta_field($tex);
            ?>

            <label>Thumbnail </label>
            <div onclick="fbox(this)" load="c=forms&m=library&FieldId=texoThumbni&calback=texoThumbni" w="850" h="500" id="texoThumbniSelectedImage"  class="TexoThumbnail"><i class="fas fa-camera"></i></div>
            <input name="meta[texoThumbni]" type="hidden" value="" id="texoThumbni">

            <input type='hidden' name="texo[taxonomy]" value="<?php echo $tex ?>">
            <br>
            <button type="button"  class='btn btn-cms-primary' onclick="SaveTexo()"><span id="actionType">Add</span> <?php echo $tex ?></button>
            <button type="button" id="UpCancl" class='btn btn-cms-default collapse' onclick="upCancl()">Cancel</button>
            <input type="hidden" id="tex_id" name="tex_id">
            <input type="hidden" id="term_id" name="term_id">
        </form>
        <script>
                    function texoThumbni(obj) {
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
                                img = img.replace(m[0], m[2] + m[3]);//Full Version
                            }
                        }
                        var added = $("#texoThumbni").val();
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
                                $("#texoThumbniSelectedImage").append("<div class='texoThumbniImg' id='logo_item_" + id + "' ><i class='" + iconClass + "'></i></div>");
                            } else {
                                $("#texoThumbniSelectedImage").append("<div class='texoThumbniImg' id='logo_item_" + id + "' ><img src='" + img + "'></div>");
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
                    })
                }
                function removeit(_this) {
                var itm = $(_this).parent().attr("id");
                var id = itm.replace("item_", "");
                var str = $("#texoThumbni").val();
                str = str.replace(id, "");
                $("#texoThumbni").val(str.replace(/(^,)|(,$)/g, ""));
                $("#" + itm).remove();
            }
        </script>
        <script>
            $(document).ready(function() {
                TermSelect();
            });



            function TermSelect() {
                var data = {ajx_action: "TexoTermSelect", tex: '<?php echo $tex ?>', name: 'term[term_group]'};
                jQuery.post('index.php', data, function(response) {
                    $("#TermSelect").html(response);
                })
            }
            function SaveTexo() {
                var data = {ajx_action: "TexoTermSelect", tex: '<?php echo $tex ?>', name: 'term[term_group]'};
                jQuery.post('index.php', data, function(response) {
                    $("#TermSelect").html(response);
                })
                post_return("", $('#termAdd').serialize(), function(res) {
                    res = JSON.parse(res);
                    if (res['error'] == "") {
                        msg(res['msg'], "G");
                        //$('#termAdd')[0].reset();
                        $('#termAdd').trigger("reset");
                        load_list();
                        TermSelect();
                        $("#UpCancl").hide();
                        $("#actionType").html('Add');
                        $('#termAdd').trigger("reset");
                    } else {
                        msg(res['msg'], "R");
                    }
                })
                // Post(termAdd, "", false, true);

            }
            function getEditData(termID) {
                TermSelect();
                $('#termAdd').trigger("reset");
                get_return("termid=" + termID, function(res) {
                    res = JSON.parse(res);
                    $("#name").val(res['name']);
                    $("#slug").val(res['slug']);
                    $("#description").val(res['description']);
                    $("#actionType").html('Update');
                    $("#tex_id").val(res['taxonomy_id']);
                    $("#term_id").val(res['term_id']);
                    var PVal = res['term_group'];
                    $("#TermSelect").find(".texoSelect option[value='" + PVal + "']").prop('selected', true);
                    $("#UpCancl").show();
                    if (res['meta']) {
                        var data = res['meta'];
                        $.each(data, function(i, item) {
                            if (Array.isArray(data[i])) {
                                $.each(data[i], function(k, v) {
                                    if ($("#" + i + "_" + v).length) {
                                        $("#" + i + "_" + v).prop("checked", true);
                                        $('.myCheckbox').attr('checked', true);
                                    }
                                });
                            } else {
                                $("#" + i).val(data[i]);
                            }
                        });
                    }
                })
            }
            function upCancl() {

                $("#UpCancl").hide();
                $("#actionType").html('Add');
                $('#termAdd').trigger("reset");

            }
        </script>
        <?php
    }

    public function libRef() {
        $texoBrowse = "";
        if (isset($_GET['texo'])) {
            $_SESSION['texoBrowse'] = $_REQUEST['texo'];
        }
        if (isset($_SESSION['texoBrowse'])) {
            $texoBrowse = $_SESSION['texoBrowse'];
        }
        $sq = "";
        if (isset($_REQUEST['q'])) {
            $sq = trim($_REQUEST['q']);
            $sqSlg = str_replace(" ", "-", $sq);
        }

        global $DB;
        $sqQ = "";
        if ($sq != "") {
            $sqQ = "and (post_name like '%$sqSlg%' or post_title like '%$sq%' or guid like '%$sqSlg%') ";
        }
        ?>

        <div class="lb">
            <?php
            if (!empty($texoBrowse)) {
                $rows = $DB->paginate("term_relationships left join post on ID=object_id", "*", "post_type='attachment' $sqQ and post_status <> 'trash'  and texo_id=$texoBrowse order by ID DESC", 32, 'attachment_browse');
            } else {
                $rows = $DB->paginate("post", "ID,post_title,guid", "post_type='attachment' $sqQ and post_status <> 'trash' order by ID DESC", 32, 'attachment_browse');
            }
            //var_dump($rows);
            foreach ($rows as $row) {
                $info = pathinfo($row['guid']);
                $icon = findIcon("." . $info['extension'], "4x");
                $img = false;
                $re = '@-image@u';
                preg_match($re, $icon, $matches, PREG_OFFSET_CAPTURE, 0);
                if ($matches) {
                    $img = true;
                }
                $icon = str_replace("<i ", "<i id='post_$row[ID]'", $icon);
                //echo "<pre>";var_dump($info);echo "</pre>";
                if ($img) {

                    $Img_alt = get_post_meta($row['ID'], 'attachment_alter');
                    $Img_caption = get_post_meta($row['ID'], 'attachment_caption');
                    $src = get_attachment_src($row['guid']);
                    $srcThmb = get_attachment_src($row['guid'], 100);
                    $srcSetStr = '';
                    $sizeStr = "";
                    $srcSet = get_attachment_src_set($row['guid']);
                    if (isset($srcSet['srcset'])) {
                        $srcSetStr = $srcSet['srcset'];
                    }
                    if (isset($srcSet['sizes'])) {
                        $sizeStr = $srcSet['sizes'];
                    }
                    $size = imgSizes($row['guid']);
                    $sizeset = implode(",", $size);
                    //srcset='$srcSetStr'
                    $icon = "<img id='img_post_$row[ID]' width='75' data-src='$src' sizeset='$sizeset'  sizes='$sizeStr' title='$Img_caption'  src='" . $srcThmb . "' alt='$Img_alt'>";
                }
                ?>
                <div class='libraryItem'>
                    <input class="itemSel" value="<?php echo $row['ID'] ?>" type="checkbox">
                    <?php
                    echo $icon;
                    ?>
                </div>
                <?php
            }
            ?>
            <div class="ImgAttOption">
                <span class="ImgAttOptionClose" onclick="$(this).parent().hide()">×</span>
                <div class="row">
                    <div class="col-sm-4">
                        Column
                    </div>
                    <div class="col-sm-8">
                        <select class="custom-select custom-select-sm w80" id="clNumber">
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="2">2</option>
                            <option value="6">6</option>
                        </select>
                    </div>
                    <div class="col-sm-4">

                    </div>
                    <div class="col-sm-8">
                        <input type="checkbox" id="EnableBorderWithImg">&nbsp;&nbsp;<label for="EnableBorderWithImg">Border</label><br><span class='comment'>Add CSS code for border against  ".borderd{ -- }" class</span>

                        <input type="checkbox" id="EnableTitleWithImg">&nbsp;&nbsp;<label for="EnableTitleWithImg">Enable Image Title</label><br>
                        <hr>
                        <input type="checkbox" id="ImgAttOptionEnable">&nbsp;&nbsp;<label for="ImgAttOptionEnable">Enable</label><br>
                    </div>
                </div>

            </div>
            <?php if ($_GET['calback'] == 'insMedia') { ?>
                <a class="AttOptionTrgg" onclick="$('.ImgAttOption').toggle()" href="javascript:void(0)"><i class="fas fa-cog"></i></a>
            <?php } ?>
            <div id="tnt_pagination" ><?php echo $DB->renderFullNav(); ?></div>
        </div>

        <script>
            $("#ImgAttOptionEnable").change(function() {
                if ($(this).is(":checked")) {
                    var instance = $("#editorInstanse").val();
                    var editor = CKEDITOR.instances[instance];//moved from if(addFlag)
                    editor.insertHtml("<div class='row'> </div>");
                } else {
                    //console.log('Not checked');
                }
            });

            var retFieldID = '<?php echo $_GET['FieldId'] ?>';
            //alert(retFieldID);
            var calbac = '<?php echo $_GET['calback'] ?>';
            //Default calbac insMedia()
            var calbac = window[calbac];

            if (retFieldID !== "") {
                var ides = $("#" + retFieldID).val();
                ides = ides.replace(/(^,)|(,$)/g, "");
                ides = ides.split(",");
                if (ides != "") {
                    //alert(ides[1]);
                    for (i = 0; i < ides.length; i++) {
                        var v = ides[i];
                        $(".itemSel:checkbox[value=" + v + "]").prop("checked", "true");
                    }
                }
            }

            $(".libraryItem").change(function() {
                //alert(retFieldID);
                if (typeof calbac === 'function') {
                    calbac('.itemSel:checked');
                }
                var str = "";
                $('.itemSel:checked').each(function() {
                    str += $(this).val() + ",";
                });
                if (retFieldID !== "") {
                    $("#" + retFieldID).val(str.replace(/(^,)|(,$)/g, ""));
                }
            })

            $(".itemSel").change(function() {
                if ($(this).prop('checked') == true) {
                    $(this).parent().addClass('checked');
                } else {
                    $(this).parent().removeClass('checked');
                }
            })


        </script>
        <?php
    }

    public function upl() {
        global $ATTACH;
        ?>
        <div>
            <?php $ATTACH->uploader(); ?>
        </div>
        <?php
    }

    public function library() {
        //var_dump($_GET);
        global $ATTACH, $TERM;
        $calbk = $_GET['calback'];
        $fldID = $_GET['FieldId'];
        $texoBrowse = "";

        if (isset($_SESSION['texoBrowse'])) {
            $texoBrowse = $_SESSION['texoBrowse'];
        }
        ?>
        <div id="library">
            <header>
                <h4>Library</h4>
                <input type="text" id="searchMediaField" class="form-control form-control-sm" onkeyup="searchKeyUp(this)" placeholder="Search">
                <select onchange="load_list('texo=' + this.value)" class="custom-select custom-select-sm attBrowseShort" style="max-width:27%">
                    <option value="">General</option>
                    <?php
                    foreach ($TERM->texoListRow('type') as $texo) {
                        $sel = $texoBrowse == $texo['taxonomy_id'] ? "selected" : "";
                        echo "<option value='$texo[taxonomy_id]' $sel>$texo[name]</option>";
                    }
                    ?>
                </select>
            </header>
            <input type="hidden" id="mdInsSelectedCurrent">
            <input type="hidden" value="<?php echo @$_GET['instanse'] ?>" id="editorInstanse">
        </div>
        <div id="libraryPop"></div>
        <?php // $LIST->pages(); 
        ?>
        <script>
            $(document).ready(function() {
                load_list();
            });

            $("#searchMediaField").keypress(function(e) {
                if (e.which == 13) {
                    searchMedia($("#searchMediaField"));
                }
            })

            function load_list(u) {
                if (u) {
                    u = u;
                } else {
                    u = ""
                }
                var url = "?&c=forms&m=libRef&FieldId=<?php echo $fldID ?>&calback=<?php echo $calbk ?>&" + u,
                        url = "index.php" + url;
                $.ajax({
                    url: url,
                    //method: "GET",
                    //async: true,
                    //processData: true,
                    beforeSend: function() {
                        // $("#wait").hide();
                        $("#libraryPop").append(loaderBig);
                    },
                    complete: function() {
                        $("#libraryPop .bodyLoader").remove();
                    },
                    cache: true,
                    success: function(res) {
                        $('#libraryPop').html(res);
                    },
                });

            }
            var timeOutID;
            function searchKeyUp(_this) {
                clearTimeout(timeOutID);
                timeOutID = setTimeout(function() {
                    searchMedia(_this);
                }, 1500);
            }



            function searchMedia(_this) {
                //console.log('search Starting : ' + $(_this).val());
                load_list('q=' + $(_this).val());
            }
        </script>
        <?php
    }

    public function notFound() {
        echo "Method not Found !";
    }

}

function TexoTermSelect() {
    $tex = $_POST['tex'];
    $name = $_POST['name'];
    global $TERM;
    $texos = $TERM->texoListRow($tex);
    $html = "<select class=\"custom-select custom-select-sm texoSelect\" name='$name'>";
    $html.="<option value=''>Select Parent</option>";
    foreach ($texos as $texo) {
        $html.="<option value='$texo[taxonomy_id]'>$texo[name]</option>";
    }
    $html.='</select>';
    echo $html;
}
