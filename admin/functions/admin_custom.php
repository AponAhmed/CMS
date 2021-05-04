<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
add_support('jquery');

add_support('bootstrap');
add_support('fontawesome');
add_support('shortable');
//add_support('jqueryUI');
add_support('fancybox');
add_support('CKE_');


admin_add_script(array('id' => "admin_js", 'src' => ADMIN_JSS . "admin.js", 'order' => 10));
admin_add_style(array('id' => "admin_theme", 'href' => ADMIN_CSS . "admin_colors.css", 'order' => 101));
admin_add_style(array('id' => "admin_pagination", 'href' => ADMIN_CSS . "pagination.css", 'order' => 2));
admin_add_script(array('id' => "ajaxUploader", 'src' => COMMON_SC . "js/ajax_uploader.js", 'order' => 2));
admin_add_script(array('id' => "dropzone", 'src' => COMMON_SC . "js/dropzone.js", 'order' => 1, 'position' => 'head'));

admin_add_style(array('id' => 'CpickerCss', 'href' => COMMON_SC . 'color_picker/jquery.minicolors.css', 'order' => 5));
admin_add_script(array('id' => 'Cpicker', 'src' => COMMON_SC . 'color_picker/jquery.minicolors.js', 'order' => 1, 'position' => 'head'));


//Error Log-==================
//add_metabox(array(
//    'title' => "Error Log",
//    'Description' => "",
//    'position' => "dashboard",
//    'type' => "all",
//    'calback' => '_error_log',
//    'class' => "col-6",
//    'order' => 0
//));
//
//$publicScriptHrml = "<script src=\"" . domain() . "/?public.js\" ></script>";
//add_footer($publicScriptHrml, 200);

$metaBoxOpen[] = 'Page Attributes';
$metaBoxOpen[] = 'Feature Image or icon';

$permaStructure = get_option('permalink');
$vars = explode("/", $permaStructure);
$vars = array_filter($vars);
//var_dump($vars);
$find = array("%category%", "%postname%", '/');
$replace = array('post_category', 'post_slug', ',');


//----------
ob_start();
$TERM->texoSelect(array('category'));
$out = ob_get_clean();
$strRep = str_replace("sel_texo[]", "meta[sel_texo][]", $out);

add_texo_meta(
        array(
            'label' => 'Category',
            'comment' => '',
            'html' => $strRep,
            'texos' => array('tag')
        )
);
//===========


$n = 1;
$str = array();
foreach ($vars as $var) {
    $exp = array();
    for ($i = 0; $i < $n; $i++) {
        $exp[] = "([^/?]+|[^.])";
    }
    $str[] = $var;
    $strExp = implode(',', $str);
    $strExp = str_replace($find, $replace, $strExp);
    $expRegex = implode("/", $exp);
    //var_dump(array("@(" . POST_PATH . ")/$expRegex@", "page,$strExp"));
    add_RW_role(array("@(" . POST_PATH . ")/$expRegex@", "page,$strExp"), 4 + $n);
    $n++;
}

function resetRWR() {
    global $RWR;
    if ($RWR->resetRWR()) {
        $info = array('msg' => 'Re-write role cleaned', 'error' => 0);
    } else {
        $info = array('msg' => 'Re-write role not clean', 'error' => 1);
    }
    echo json_encode($info);
}

function _error_log() {
    ?>
    <div class="mBoxBody ErrorLogMBox">
        <a href="javascript:void(0)" title='Clean all Error' onclick="cleanError(this)" class="float-right"><i class="fas fa-trash-alt"></i></a>
        <a href="javascript:void(0)" title='Refresh' onclick="loadErrorList()" class="float-right" style='margin-right:10px'><i class="fas fa-sync"></i></a>
        <div id='ErrorList'></div> 
    </div>
    <script>

        $(document).ready(function() {
            loadErrorList();
        })



        function loadErrorList() {
            var data = {ajx_action: "loadErrorList"};
            jQuery.post('index.php', data, function(response) {
                $("#ErrorList").html(response);
            });
        }
        function cleanError(_this) {
            $(_this).html(loader);
            var data = {ajx_action: "cleanError"};
            jQuery.post('index.php', data, function(response) {
                $(_this).html('<i class="fas fa-trash-alt"></i>');
                var res = JSON.parse(response);
                if (res['error'] == "") {
                    msg(res['msg'], "G");
                    loadErrorList();
                } else {
                    msg(res['msg'], "R");
                }
            });
        }

        function removeSingle(indx) {
            //alert(indx);
            var data = {ajx_action: "removeSingle", indx: indx};
            jQuery.post('index.php', data, function(response) {
                loadErrorList();
            });
        }
    </script>
    <?php
}

function loadErrorList() {
    $ErrorLog = new error_log();
    $ErrorLog->show_exception();
}

function cleanError() {
    global $DB;
    $ErrorLog = new error_log();
    //var_dump($ErrorLog);
    if ($ErrorLog->clean_exception()) {
        echo json_encode(array('msg' => "All error-log are removed", 'error' => ""));
    } else {
        echo json_encode(array('msg' => "Could not remove erro-log", 'error' => $DB->error));
    }
}

function removeSingle() {
    global $DB;
    $ErrorLog = new error_log();
    //var_dump($ErrorLog);
    if ($ErrorLog->removeSingle($_POST['indx'])) {
        echo json_encode(array('msg' => "error-log removed", 'error' => ""));
    } else {
        echo json_encode(array('msg' => "Could not remove erro-log", 'error' => $DB->error));
    }
}

//===================

$enable_type_slug = @array_filter(@array_unique(@unserialize(get_option('enable_type_slug'))));
$enable_type_slug = !empty($enable_type_slug) ? $enable_type_slug : array('post', 'page');
//var_dump($enable_type_slug);
add_metabox(array(
    'title' => "Page Attributes",
    'Description' => "",
    'position' => "side",
    'type' => implode(",", $enable_type_slug),
    'calback' => 'page_attr',
    'order' => 2
));


add_metabox(array(
    'title' => "Attachment Information",
    'Description' => "",
    'position' => "side",
    'type' => "attachment",
    'calback' => 'attachment_info',
    'order' => 2
));

add_metabox(array(
    'title' => "Media Type",
    'Description' => "",
    'position' => "side",
    'type' => "attachment",
    'calback' => 'mediaTypeSel',
    'order' => 2
));

$AttDownloadArg = array(
    'title' => "",
    'field' => "guid",
    'order' => 10,
    'meta' => false,
    'filter' => 'attachment_download'
);

addListColumn('attachment', $AttDownloadArg);

function filter_attachment_download($url) {
    $fileParam = explode("/", $url);
    $lastIndex = count($fileParam) - 1;
    $fileDir = base64_encode(UPLOAD . $fileParam[($lastIndex - 2)] . "/" . $fileParam[($lastIndex - 1)] . "/" . $fileParam[$lastIndex]);
    echo "<a href='?down=$fileDir'><i class='fas fa-download'></i></a>";
    $info = pathinfo($url);
    if ($info['extension'] == 'pdf') {
        echo "&nbsp;&nbsp;<a target='_blank' href='$url'>View</a>";
    }
}

function mediaTypeSel() {
    global $TERM;
    $TERM->texoSelect(array('type'), "radio");
}

function filter_time($str) {
    $tstamp = strtotime($str);
    return date("Y-m-d", $tstamp);
}

function filter_add_link_title($str, $id) {
    $link = get_link($id);
    return "<a href='$link' target='_blank'>$str</a>";
}

function filter_feature_image_list_filter($id) {
    $ides = explode(",", get_post_meta($id, 'feature_image'));
    $ides = array_filter($ides);
    //var_dump($ides);
    if (!empty($ides)) {
        shuffle($ides);
        $img = get_post($ides[0], "guid");
        $info = pathinfo($img['guid']);

        if (!isset($info['extension'])) {
            echo "Not Found !";
            //Add Eception: Product Image not Found
            return;
        }
        //var_dump($info);
        $icon = findIcon("." . @$info['extension'], "4x");
        $src = get_attachment_src($img['guid'], 100);
        $Img_alt = get_post_meta($ides[0], 'attachment_alter');

        $imgInfo = __getimagesize($src);
        if (isset($imgInfo[0])) {
            $icon = "<div class='featureImgListThumb'><a target='_blank' href='index.php?l=edit&post-type=attachment&ID=$ides[0]'><img width='50' alt='$Img_alt' id='obj_img_" . $ides[0] . "' src='" . $src . "'></a></div>";
        }
    } else {
        $icon = "<div class='featureImgListThumb'></div>";
    }
    echo $icon;
}

function filter_add_link_title_page($fields, $row) {
    //var_dump($row);
    $front = get_option('front_page') == $row['ID'] ? "-Front Page" : "";
    if (empty($front)) {
        // $front = get_option('blog_page') == $row['ID'] ? "-Blog Page" : "";
    }
    $link = get_link($row['ID']);
    if ($row['post_parent'] != 0) {
        $tt = "&raquo;&nbsp;<a href='$link' target='_blank'>$row[post_title]</a>";
    } else {
        $tt = "<a href='$link' target='_blank'>$row[post_title]</a>&nbsp;$front";
    }
    return $tt;
}

function filter_library_file($fields, $row) {
    $info = pathinfo($row[$fields[0]]);
    $icon = findIcon("." . $info['extension'], "4x");
    // $src = get_attachment_src($row[$fields[0]]);
    // if (!@getimagesize($src)) {
    // $src = get_attachment_src($row[$fields[0]]);
    // }
    $src = get_attachment_src($row[$fields[0]]);
    $imgInfo = getimagesize($row[$fields[0]]);
    if (!$imgInfo) {
        $Imgsize = __getimagesize($row[$fields[0]]);
        if (isset($Imgsize[0]) && $Imgsize[0] > 0) {
            $imgInfo = true;
        }
    }
    if ($imgInfo) {
        $icon = "<img width='50' src='" . $src . "'>";
    }

    $showName = strlen($row[$fields[1]]) > 55 ? substr($row[$fields[1]], 0, 55) . "..." : $row[$fields[1]];

    $showSlug = strlen($info['filename']) > 50 ? substr($info['filename'], 0, 50) . "..." : $info['filename'];

    $html = "<div class='filr_list_file'><a href='?l=edit&post-type=$row[post_type]&ID=$row[ID]'>$icon</a></div>";
    $html.= "<div class='filr_list_fileInfo'>
                   <a href='?l=edit&post-type=$row[post_type]&ID=$row[ID]'>" . $showName . "</a>
                  $showSlug.$info[extension]
            </div>";
    return $html;
}

function filter_library_file_grid($fields, $row) {
    $info = pathinfo($row[$fields[0]]);
    $icon = findIcon("." . $info['extension'], "4x");
    // $src = get_attachment_src($row[$fields[0]]);
    // if (!@getimagesize($src)) {
    // $src = get_attachment_src($row[$fields[0]]);
    // }
    $src = get_attachment_src($row[$fields[0]]);
    if (@getimagesize($row[$fields[0]])) {
        $icon = "<img width='140' src='" . $src . "'>";
    }
    //<input class=\"chk itemSel\" value=\"$row[ID]\" name=\"selected[]\" type=\"checkbox\">
    //$html = "<div class='filr_grid_file'><a href='?l=edit&post-type=$row[post_type]&ID=$row[ID]'>$icon</a></div>";
    $htmln = "<div class=\"libraryItem MainMedia\">
        <a href='?l=edit&post-type=$row[post_type]&ID=$row[ID]'>$icon</a>            
    </div>";
    return $htmln;
}

function filter_related_texos($fields, $row) {
    global $DB;
    $post_id = $row[$fields[0]];
    // var_dump($fields[3]);
    $texoName = $fields[3];
    $res = $DB->select("term_relationships as rl left join term_taxonomy as tx on rl.texo_id=tx.taxonomy_id left join terms as tr on tx.term_id=tr.term_id", "name", "object_id=$post_id and tx.taxonomy='$texoName'");
    $txos = array();
    foreach ($res as $tex) {
        $txos[] = $tex['name'];
    }
    echo implode(",", $txos);
}

function filter_item_checkbox($fields, $row) {
    echo "<input class='chk' type='checkbox' value='$row' name='selected[]'>";
}

function StatusBar() {
    global $__statue_bar_object;
    $htm = "";
    if (isset($_SESSION[SESS_KEY]['login']) && (isset($GLOBALS['blog']) || isset($GLOBALS['post']) || isset($GLOBALS['term']))) {
        if (is_array($__statue_bar_object) && !empty($__statue_bar_object)) {
            $htm.= "<style>
            .site-status-bar {
                position: fixed;
                bottom: -1px;
                right: 50px;
                display: flex;
                background: #4f4f4f;
                list-style: none;
                border: 1px solid #000;
                border-radius: 4px 4px 0 0;
                overflow: hidden;
                z-index: 99999999;
                transition: all .5s;
                padding: 0;
                margin: 0;
            }
            .site-status-bar.hid {
                transform: translate(0,100%);
            }
            .site-status-bar li {
                padding: 5px 15px;
                text-align: center;
                align-items: center;
                display: flex;
                align-content: center;
                border-right: 1px solid #2b2a2a;
            }
            .site-status-bar li:last-child {
                border: none;
            }
            .site-status-bar li * {
                color: #fff;
            }
            .site-status-bar li:hover {
                background: #333;
            }
            </style>";
            $htm.= "<ul class='site-status-bar'>";
            $script = "";
            foreach ($__statue_bar_object as $obj) {
                $htm .="<li>$obj[html]</li>";
                if (isset($obj['script'])) {
                    $script.=$obj['script'] . "\n";
                }
            }
            $htm .="</ul>";
            $htm.="<script>
                    $(document).ready(function() {
                        var hideTime;
                        $(window).mousemove(function() {
                            $('.site-status-bar').removeClass('hid');
                            clearTimeout(hideTime);
                            hideTime = setTimeout(function() {
                                $('.site-status-bar').addClass('hid');
                            }, 1000);
                        });
                    });
                    
                    $script</script>";
        }
    }
    return $htm;
}

function EditLink($echo = true, $post = "") {
    global $POST, $QV;
    $post = !empty($post) ? $post : $POST;
    if (isset($_SESSION[SESS_KEY]['login']) && (isset($post['ID']) || isset($QV['term']))) {
        if (isset($QV['term']) && empty($post)) {
            $trm = $GLOBALS['term'];
            $postType=tx2Cp($trm['taxonomy']);
            
            $link = domain() . "/admin/index.php?l=texonomy&tex=$trm[taxonomy]&id=$trm[term_id]&post-type=$postType";
            $link = trim_slash($link);
            if ($echo) {
                echo "<a href=\"$link\">Edit This</a>";
            } else {
                return "<a href=\"$link\">Edit This</a>";
            }
        } else {
            $link = domain() . "/admin/index.php?l=edit&post-type=$post[post_type]&ID=$post[ID]";
            $link = trim_slash($link);
            if ($echo) {
                echo "<a href=\"$link\">Edit This</a>";
            } else {
                return "<a href=\"$link\">Edit This</a>";
            }
        }
    }
}

function postTypeDashboardMetaBox($arg) {
    $pt = $arg['type'];
    //var_dump($postType);
    ?>
    <div class="mBoxBody">
        <a href="#" onclick='load_list()' class="float-right refresh-btn"><i class="fas fa-sync"></i></a>
        <div id="DataLIst"></div>
        <?php // $LIST->pages();      ?>
        <script>
            $(document).ready(function() {
                load_list();
            });

            function load_list(u) {
                if (u) {
                    u = u;
                } else {
                    u = ""
                }
                var typeUrl = "post-type=<?php echo $pt ?>";
                var url = "?loadList=page&" + typeUrl + "&" + u;
                get_list(url);
            }

            function get_list(url, type) {
                var url = "index.php" + url;
                $.ajax({
                    url: url,
                    //method: "GET",
                    //async: true,
                    //processData: true,
                    beforeSend: function() {
                        // $("#wait").hide();
                        // $(".refresh").addClass("fa-spin");
                    },
                    complete: function() {
                        // var delay = 1; //1 second
                        // setTimeout(function() {
                        //    $(".refresh").removeClass("fa-spin");
                        //  }, delay);
                        // $("tbody[jput='entityData'] tr td").fadeIn();
                    },
                    cache: true,
                    success: function(res) {
                        $('#DataLIst').html(res);
                    },
                });
            }
        </script>
    </div>
    <?php
}

add_metabox(array(
    'title' => "Image info. Modifyer",
    'Description' => "",
    'position' => "side",
    'type' => enableSlugCPType(),
    'calback' => 'imgmodifier',
));

function imgmodifier() {
    ?>
    <div id="asd" class = "mBoxBody">
        <button type="button" class="btn btn-cms-primary" onclick="inItBrowse(this)" id="imgInBrowse">Browse</button>
        <button type="button" class="btn btn-cms-default" id="imgBrouser">Change Media</button>
    </div> 
    <script>
        function inItBrowse(_this) {
            $(_this).after(loader);
            var dataStr = CKEDITOR.instances.PostEditor.getData();
            var fd = {ajx_action: 'inItBrowse', dataStr: dataStr};
            jQuery.ajax({
                type: "POST",
                url: "index.php",
                data: fd, // serializes the form's elements.
                success: function(data)
                {
                    $.fancybox.open('<div class="dateModify" style="max-width:800px">' + data + '</div>');
                    $(_this).parent().find(".spinLoader").remove();
                }
            });
            //console.log($(_this.prototype));
        }


        $("#imgBrouser").click(function() {
            $("#asd #imgBrouser").after(loader);
            var fd = {ajx_action: 'imgIDParsser', ID: $("#ID").val()};
            jQuery.ajax({
                type: "POST",
                url: "index.php",
                data: fd, // serializes the form's elements.
                success: function(data)
                {
                    $.fancybox.open('<div class="dateModify">' + data + '</div>');
                    $("#asd .spinLoader").remove();
                    //alert(data);

                    $("#updImgInfo").click(function() {
                        var postImgData = {ajx_action: 'update_img_data', imgData: $("#imgModifier").serialize()};
                        jQuery.ajax({
                            type: "POST",
                            url: "index.php",
                            data: postImgData, // serializes the form's elements.
                            success: function(res)
                            {
                                res = jQuery.parseJSON(res);
                                if (res['error']) {
                                    if (res['msg'] !== "") {
                                        msg(res['msg'], "R");
                                    }
                                } else {
                                    if (res['msg'] !== "") {
                                        msg(res['msg'], "G");
                                    }
                                    //RD(res['rd']);
                                    if (res['rd']) {
                                        // RQST(res['rd']);
                                    }
                                    $.fancybox.close();
                                }
                            }
                        });
                    });
                    $("#clnUpdImgInfo").click(function() {
                        $("#imgModifier textarea").each(function() {
                            remDupl($(this));
                        });
                        var postImgData = {ajx_action: 'update_img_data', imgData: $("#imgModifier").serialize()};
                        jQuery.ajax({
                            type: "POST",
                            url: "index.php",
                            data: postImgData, // serializes the form's elements.
                            success: function(res)
                            {
                                res = jQuery.parseJSON(res);
                                if (res['error']) {
                                    if (res['msg'] !== "") {
                                        msg(res['msg'], "R");
                                    }
                                } else {
                                    if (res['msg'] !== "") {
                                        msg(res['msg'], "G");
                                    }
                                    //RD(res['rd']);
                                    if (res['rd']) {
                                        // RQST(res['rd']);
                                    }
                                    $.fancybox.close();
                                }
                            }
                        });
                    });
                }
            });
        });

        function updateEditor(_this) {
            var dataStr = CKEDITOR.instances.PostEditor.getData();
            $(".imgModifItem").each(function() {
                var oldImg = $(this).find(".originalImg").val();
                // console.log(oldImg);
                var $titleF = $(this).find('.titleNew');
                var $altF = $(this).find('.altNew');

                var newImg = "";
                newImg = oldImg.replace('alt="' + $altF.attr('oldAlt') + '"', 'alt="' + $altF.val() + '"');
                newImg = newImg.replace('title="' + $titleF.attr('oldTitle') + '"', 'title="' + $titleF.val() + '"');
                //console.log(oldImg);
                //dataStr = dataStr.replace($titleF.attr('oldTitle'), $titleF.val());
                // dataStr = dataStr.replace($altF.attr('oldAlt'), $altF.val());
                dataStr = dataStr.replace(oldImg, newImg);

            });
            //console.log(dataStr);
            CKEDITOR.instances.PostEditor.setData(dataStr);
            //console.log(dataStr);
            $.fancybox.close();
        }

        function changeSlug(_this) {
            var tt = $(_this).val();
            var slug = $(_this).parent().parent().find('.attSlug').val();
            tt = tt.replace(/[^[a-zA-Z\d]+/g, "-");
            var ext = fileExt(slug);
            var slug = tt + '.' + ext;
            $(_this).parent().parent().find('.attSlug').val(slug);
        }

        function fileExt(filename) {
            return filename.split('.').pop();
        }
        function remDupl(_this) {
            //counter
            titleStrCount(_this);
            altStrCount(_this);
            slugStrCount(_this)
            //counter


            var phr = $(_this).val();
            var phr = phr.split(',');
            var lnth = phr.length;
            var uniqueNames = [];
            jQuery.each(phr, function(i, el) {
                if (jQuery.inArray(el, uniqueNames) === -1)
                    uniqueNames.push(el);
            });
            // return uniqueNames;
            $(_this).val(uniqueNames);
        }
        function titleStrCount(_this) {
            $(_this).parent().find('.imgTitleCount').html($(_this).val().length);
            if ($(_this).val().length > 100) {
                $(_this).parent().find('.imgTitleCount').css('color', 'red');
            } else {
                $(_this).parent().find('.imgTitleCount').css('color', 'green');
            }
        }
        function altStrCount(_this) {
            $(_this).parent().find('.imgAltCount').html($(_this).val().length);
            if ($(_this).val().length > 100) {
                $(_this).parent().find('.imgAltCount').css('color', 'red');
            } else {
                $(_this).parent().find('.imgAltCount').css('color', 'green');
            }
        }
        function slugStrCount(_this) {
            $(_this).parent().find('.imgSlugCount').html($(_this).val().length);
            if ($(_this).val().length > 100) {
                $(_this).parent().find('.imgSlugCount').css('color', 'red');
            } else {
                $(_this).parent().find('.imgSlugCount').css('color', 'green');
            }
        }


    </script>
    <?php
}

function inItBrowse() {
    $content = $_POST['dataStr'];
    // imgIDParsser($content);
    // return;

    $dom = new DOMDocument();
    @$dom->loadHTML($content);
    # Find all <link> elements
    $Mcontent = $content;
    $imgNodes = $dom->getElementsByTagName('img');

    $html = "<form id='imgModifier'>";
    foreach ($imgNodes as $imgNode) {
        $imgHtml = $dom->saveHTML($imgNode);
        $imgHtml = str_replace(">", " />", $imgHtml);
        //var_dump($imgHtml);
        $attSetStr = "";
        # Find all the Stylesheets
        $src = $imgNode->attributes->getNamedItem("src")->nodeValue;
        $attArray = array();
        if ($imgNode->hasAttributes()) {
            foreach ($imgNode->attributes as $attr) {
                $name = $attr->nodeName;
                $value = $attr->nodeValue;
                //echo "Attribute '$name' :: '$value'<br />"; 
                $attArray[$name] = $value;
            }
//            $properOrderedArray = array_replace(array_flip($AttOrder), $attArray);
//            $properOrderedArray = array_filter(array_merge($AttOrder, $attArray));
//            foreach ($properOrderedArray as $atName => $val) {
//                $attSetStr.="$atName=\"$val\" ";
//            }
        }
        $red = 'readonly';
        //var_dump($attArray);
        //continue;
        $infoPath = pathinfo($attArray['src']);
        //var_dump($infoPath);

        $html.="<div class='imgModifItem'>";
        $html.="<div class='filr_list_file'>";
        $img = str_replace("<img", "<img class='img-fluid' ", $imgHtml);
        $html.= $img;
        $html.="</div>";
        $html.="<div class='imgModifItem_userControl'>";
        $initLength = strlen($infoPath['basename']);
        $nn = $initLength >= 100 ? "<font color='red'>$initLength</font>" : "<font color='green'>$initLength</font>";
        $html.="<div class='row'>
                    <textarea class='collapse originalImg'>$imgHtml</textarea>
                    <div class='col-12'>Name: <span class=\"imgChrCounter\">$nn</span><textarea onkeyup='remDupl(this)' name='' class='form-control form-control-sm imgCaptionFld attSlug' placeholder='Image Slug (Path)' $red>$infoPath[basename]</textarea></div> 
                    <div class='col-6'>Title : <span class=\"imgChrCounter\"><span class=\"imgTitleCount\">" . strlen($attArray['title']) . "</span> / 100 char.</span><textarea onkeyup='remDupl(this)' class='form-control  form-control-sm imgCaptionFld titleNew'  placeholder='Title' oldTitle='$attArray[title]' >$attArray[title]</textarea></div>
                    <div class='col-6'>Alt :<span class=\"imgChrCounter\"><span class=\"imgAltCount\">" . strlen($attArray['alt']) . "</span> / 100 char.</span><textarea onkeyup='remDupl(this)' name='' class='form-control form-control-sm imgCaptionFld altNew' placeholder='Alt Text' oldAlt='$attArray[alt]' >$attArray[alt]</textarea></div>
                </div>
        ";
        $html.="</div>";
        $html.="</div>";
    }
    $html.="<button type='button' style=\"margin-top:10px\" class=\"btn btn-cms-primary\" onclick='updateEditor(this)' id=\"updImgInfoEditor\">Update </button>";
    $html.="</form>";
    echo $html;
}

function imgIDParsser($content = false) {


    $pageUrl = get_link($_POST['ID']);
    $pageData = file_get_contents($pageUrl);
    //echo $pageData;
    // var_dump(realpath ("http://asik/siatex/apon/cms/content/upload/2018/05/viber-image.jpg"));
    if ($content) {
        $pageData = $content;
    }
    preg_match_all('/<img[^>]+>/i', $pageData, $result);
    $result = $result[0];
    //var_dump($result);
    $html = "";
    $html.="<form id='imgModifier'>";
    foreach ($result as $img) {

        $Img_alt = "";
        $Img_caption = "";
        $Img_title = "";
        $red = 'readonly';
        $cls = 'collapse';
        // var_dump($img);
        if (preg_match('/id=(["|\'])obj_img_([\d]+)(["|\'])/', $img, $matches, PREG_OFFSET_CAPTURE)) {
            $img_id = $matches[2][0];
            $Img_alt = get_post_meta($img_id, 'attachment_alter');


            $slug = get_post($img_id, 'guid');
            //var_dump($slug)
            $slug = pathinfo($slug['guid']);
            $slug = $slug['basename'];
            //var_dump($slug);

            $Img_caption = get_post_meta($img_id, 'attachment_caption');
            $Img_title = get_post_title($img_id);
            $red = '';
            $cls = "";
        }
        $html.="<div class='imgModifItem $cls'>";
        $html.="<div class='filr_list_file'>";
        $img = str_replace("<img", "<img class='img-fluid' ", $img);
        $html.= $img;
        $html.="</div>";
        $html.="<div class='imgModifItem_userControl'>";
        $html.="<div class='row'>
                    <input type='hidden' name='imgMod[id_$img_id][id]' value='$img_id'>
                    <div class='col-6'>Main Title: <input type='text' name='imgMod[id_$img_id][post_title]' onkeyup='changeSlug(this)' value=\"$Img_title\" class='form-control form-control-sm attTitle' placeholder='Main Title' $red></div>
                    <div class='col-6'>Title : <span class=\"imgChrCounter\"><span class=\"imgTitleCount\">" . strlen($Img_caption) . "</span> / 100 char.</span><textarea onkeyup='remDupl(this)' name='imgMod[id_$img_id][attachment_caption]' class='form-control  form-control-sm imgCaptionFld'  placeholder='Title' $red>$Img_caption</textarea></div>
                    <div class='col-6'>Slug:<span class=\"imgChrCounter\"><span class=\"imgSlugCount\">" . strlen($slug) . "</span> / 100 char.</span><textarea onkeyup='remDupl(this)' name='imgMod[id_$img_id][slug]' class='form-control form-control-sm imgCaptionFld attSlug' placeholder='Image Slug (Path)' $red>$slug</textarea></div>           
                    <div class='col-6'>Alt:<span class=\"imgChrCounter\"><span class=\"imgAltCount\">" . strlen($Img_alt) . "</span> / 100 char.</span><textarea onkeyup='remDupl(this)' name='imgMod[id_$img_id][attachment_alter]' class='form-control form-control-sm imgCaptionFld' placeholder='Alt Text' $red>$Img_alt</textarea></div>
                </div>
        ";
        $html.="</div>";
        $html.="</div>";
    }
    $html.="<button type='button' style=\"margin-top:10px\" class=\"btn btn-cms-primary\" id=\"updImgInfo\">Update </button>
            <button type='button' style=\"margin-top:10px\" class=\"btn btn-cms-primary\" id=\"clnUpdImgInfo\">Clean & Update </button>
            <button type='button' class='btn btn-cms-default' onclick='$.fancybox.close()' style=\"margin-top:10px\">Close</button>";
    $html.="</form>";
    echo $html;
}

function update_img_data() {
    global $DB, $ATTACH;
    $data = array();
    parse_str($_POST['imgData'], $data);

    $data = $data['imgMod'];
    // $data = cln($data); 
    $ss = 0;
    foreach ($data as $k_str => $iData) {

        if (!empty($iData['id'])) {

            $imgPost = get_post($iData['id'], "guid,post_date_gmt");
            $pathPart = pathinfo($imgPost['guid']);
            $timeStamp = strtotime($imgPost['post_date_gmt']);

            //File
            $rth = UPLOAD . date("Y", $timeStamp) . "/" . date("m", $timeStamp) . "/";

            $oldpath = $rth . $pathPart['basename'];
            $newpath = $rth . $iData['slug'];
            $renamed = attachment_file_rename($oldpath, $newpath);
            //$renamed=true;
            // var_dump($renamed);

            $pstData = array();
            $pstData['post_title'] = $iData['post_title'];
            $newpath = $pathPart['dirname'] . "/" . $iData['slug'];
            if (($pathPart['basename'] != $iData['slug']) && $renamed) {
                $pstData['guid'] = $newpath;

                $sizes = $ATTACH->attachmentSizes;
                foreach ($sizes as $size) {
                    $nInfo = pathinfo($iData['slug']);
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
            $up = $DB->update('post', $pstData, "ID=$iData[id]");
            $r = update_post_meta($iData['id'], 'attachment_alter', $iData['attachment_alter']);
            $r1 = update_post_meta($iData['id'], 'attachment_caption', $iData['attachment_caption']);


            //Replace content By slug
            $detPosts = $DB->select("post", "ID,post_content", "post_content like '%obj_img_$iData[id]%'");
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
                    if ($iData['id'] != $imgID) {
                        continue;
                    }
                    $nwimgPost = get_post($imgID, "guid");
                    $NwSrc = get_attachment_src($nwimgPost['guid']);
                    $alt = get_post_meta($imgID, 'attachment_alter');
                    $title = get_post_meta($imgID, 'attachment_caption');
                    $modiImg = "<img id=\"$idstr\" class=\"$cls\" src=\"$NwSrc\" alt=\"$alt\" title=\"$title\">";

                    $pattern = "/<img[^>]*" . $idstr . "[^>]*>/";
                    $content = preg_replace($pattern, $modiImg, $content);
                }
                $DB->update('post', array('post_content' => $content), "ID=$pst[ID]");
            }
            //========


            if ($up) {
                $ss++;
            }
        }
    }
    if ($ss > 0) {
        echo json_encode(array('msg' => "Information Updated", 'error' => ""));
    }
}

//global $metaBoxes;
//var_dump($metaBoxes);
//Feature Image
add_metabox(
        array(
            'title' => "Feature Image or icon",
            'Description' => "",
            'position' => "side",
            'type' => "all",
            'calback' => 'feature_icon_selector',
            'order' => 10
        )
);

function feature_icon_selector() {
    global $POST, $ATTACH;
    ?>
    <div class="mBoxBody">
        <div class="">
            <div class="FeatureUploader">
                <a href="javascript:void(0)" onclick="$('.resizeOption').slideToggle('fast')"><i class="fas fa-cog"></i></a>
                <div class="resizeOption collapse">
                    <div class="CatPro">
                        <ul>
                            <?php
                            global $TERM;
                            $mediaType = $TERM->texoListRow("type");
                            if (!empty($mediaType)) {

                                $n = 0;
                                foreach ($mediaType as $mTyp) {
                                    $chk = "";
                                    if ($n == 0) {
                                        $chk = "checked";
                                    }
                                    $n++;
                                    echo "<li><input name='mediachose' $chk class='mediaTypeChose' type='radio' value='$mTyp[taxonomy_id]' id='termID_$mTyp[term_id]'>&nbsp;<label for='termID_$mTyp[term_id]'>$mTyp[name]</label></li>";
                                }
                            } else {
                                echo "<li>No media Type found, Create from <a href='index.php?l=texonomy&tex=type&post-type=attachment'>Here</a></li>";
                            }
                            ?>
                        </ul>
                        <?php //$TERM->texoSelect(array('type'), "radio");      ?>
                    </div>
                    <?php
                    $tmpInfo = get_template_info();
                    //var_dump($tmpInfo);
                    ?>
                    <input type="text" id="AttCustomResize" name="meta[imgCustomResize]" value="<?php echo @$tmpInfo['Custom image size'] ? @$tmpInfo['Custom image size'] : get_post_meta(@$POST['ID'], 'imgCustomResize') ?>" class="form-control form-control-sm" placeholder="Custom size should sepparate by (,)">
                    <input type="text" id="imageQuality" name="meta[imageQuality]" value="<?php echo get_post_meta(@$POST['ID'], 'imageQuality') ?>" class="form-control form-control-sm" placeholder="Quality (default 75%)">
                </div>
                <?php $ATTACH->uploader(); ?>
            </div>
            <label onclick="fbox(this)" load="c=forms&m=library&FieldId=feature_image&calback=serviceImgSelect" w="850" h="500" for="browse_pro" class="browse_product"><i class="fas fa-search-plus"></i> Browse</label>
            <ul id='featureSelectedImage' class="sortable">
                <?php
                $c = 0;
                $ids = get_featureImages(@$POST['ID']);
                //var_dump($ids);
                if ($ids) {
                    foreach ($ids as $id) {
                        $c++;
                        $imguid = get_post($id, "guid");
                        if (!empty($imguid)) {
                            $imgSrc = get_attachment_src($imguid['guid']);
                            $info = __getimagesize($imguid['guid']);
                            if (!empty($info[0])) {
                                echo "<li class='featuredImg' id='item_" . $id . "' ><img src='", $imgSrc . "'><a class='featureImgRemove' onclick='removeit(this)' href=\"javascript:\">×</a></li>";
                            } else {
                                $info = pathinfo($imguid['guid']);
                                $icon = findIcon("." . $info['extension'], "4x");
                                $icon = str_replace("<i ", "<i id='post_$id'", $icon);
                                echo "<li class='featuredImg' id='item_" . $id . "' >$icon<a class='featureImgRemove' onclick='removeit(this)' href=\"javascript:\">×</a></li>";
                            }
                        } else {
                            echo "<li class='featuredImg' id='item_" . $id . "' >Not Found<a class='featureImgRemove' onclick='removeit(this)' href=\"javascript:\">×</a></li>";
                        }
                    }
                }
                ?>
            </ul>
            <hr>
            <div style="overflow: hidden;padding: 5px;padding-left: 0;">
                <a href="https://fontawesome.com/icons/" target="_new" title='Open Font-Awesome to Find icon class'><i class="fab fa-font-awesome-flag" style="color: #194c60;padding: 7px 0;float: left;padding-right: 7px;"></i></a>
                <input class="form-control form-control-sm" name="meta[feature_icon]"  value="<?php echo get_post_meta(@$POST['ID'], 'feature_icon') ?>" style="width:calc(100% - 20px);float: left;" placeholder="Font-Awesome icon class" type="text">
            </div>
            <hr>
            <textarea class="form-control form-control-sm" name="meta[feature_icon_svg]" placeholder="SVG"><?php echo get_post_meta(@$POST['ID'], 'feature_icon_svg') ?></textarea>

        </div>
        <?php //var_dump(get_post_meta($POST['ID'], 'feature_image'))                                 ?>
        <input name="meta[feature_image]" type="hidden" value="<?php echo get_post_meta(@$POST['ID'], 'feature_image') ?>" id="feature_image">
        <script>
            var oldContainer;
            $(".sortable").sortable({
                group: 'nested',
                afterMove: function(placeholder, container) {
                    if (oldContainer != container) {
                        if (oldContainer)
                            oldContainer.el.removeClass("active");
                        container.el.addClass("active");

                        oldContainer = container;
                    }
                },
                onDrop: function($item, container, _super) {
                    var ids = [];
                    $("#featureSelectedImage li").each(function() {
                        var itmId = $(this).attr('id');
                                const regex = /(item_)(\d+)/;
                                let
                        m;
                        if ((m = regex.exec(itmId)) !== null) {
                            // The result can be accessed through the `m`-variable.
                            var id = m[2];
                            ids.push(id);
                        }
                    });
                    $("#feature_image").val(ids.join());
                    container.el.removeClass("active");
                    _super($item, container);
                    // mySerialize();
                }
            });


            function serviceImgSelect(obj) {
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


            var added = $("#feature_image").val();
                    var addFlag = true;
                    if (added.indexOf(id) != - 1)
            {
            //alert("found");
            addFlag = false;
            }
            var c = $(".selectedItem_single").length;
                    c = c + 1;
                    if (addFlag) {
            if (iconClass != undefined) {
            $("#featureSelectedImage").append("<li class='featuredImg' id='item_" + id + "' ><i class='" + iconClass + "'></i> <a class='featureImgRemove' onclick='removeit(this)' href=\"javascript:\">×</a></li>");
            } else {
            $("#featureSelectedImage").append("<li class='featuredImg' id='item_" + id + "' ><img src='" + img + "'>  <a class='featureImgRemove' onclick='removeit(this)' href=\"javascript:\">×</a></li>");
            }
            }
            });
                    $(".itemSel").each(function() {
            if ($(this).is(':checked') == false) {
            var Ckid = $(this).val();
                    var remvID = "item_" + Ckid;
                    $("#" + remvID).remove();
            }
            });
            }

            function removeit(_this) {
            $(_this).parent().remove();
                    var itm = $(_this).parent().attr("id");
                    //console.log(itm);
                    var id = itm.replace("item_", "");
                    var str = $("#feature_image").val();
                    str = str.replace(id, "");
                    $("#feature_image").val(str.replace(/(^,)|(,$)/g, ""));
                    $("#" + itm).remove();
            }
        </script>
    </div>
    <?php
}

function FileDateModify() {
    global $DB;
    $time = strtotime($_POST['mDate']);
    $path = ABSPATH . "/content/";
    $res = touchDir($path, $time);
    if ($res) {
        echo json_encode(array('msg' => "Changed files Modifing Date", 'error' => ""));
        if (!add_option('fileLastModify', $time)) {
            update_option('fileLastModify', $time);
        }
        $upData = array('post_modified_gmt' => date('Y-m-d H:i:s', $time));
        $DB->update('post', $upData, "1");
    } else {
        echo json_encode(array('msg' => "Not Change Modifing Date ", 'error' => "1"));
    }
}

function replaceDb() {
    global $DB;
    //var_dump($_POST);
    $fnd = trim($_POST['fnd']);
    $replce = trim($_POST['replce']);

    $tables = array(
        'post' => array('post_content', 'guid'),
        'post-meta' => array('meta_value'),
        'meta' => array('meta_value'),
    );


    $res = array();
    foreach ($tables as $table => $fields) {
        $fldStr = array();
        foreach ($fields as $field) {
            $fldStr[] = "`$field`=REPLACE(`$field`,'$fnd','$replce')";
        }
        $sql = "UPDATE `$table` SET ";
        $sql.=implode(",", $fldStr);
        $res[$table] = $DB->query($sql . " WHERE 1");
    }
    $msg = "";
    $s = 0;
    foreach ($res as $t => $tRes) {
        if ($tRes) {
            $s++;
            $msg.=$t;
            if (count($res) > $s) {
                $msg.=", ";
            }
        }
    }
    if ($s > 0) {
        echo json_encode(array('msg' => "Database Replaced($msg)", 'error' => "0"));
    } else {
        echo json_encode(array('msg' => "Database not Replaced", 'error' => "1"));
    }
}

function saveTag() {
    global $TERM;

    $Pid = $_POST['id'];
    $str = $_POST['str'];
    $texo = $_POST['texo'];
    $arr = explode(",", $str);

    $notSaveLenthF = 0;
    foreach ($arr as $tag) {
        $term = array();
        $term['name'] = $tag;
        $term['slug'] = titleFilter($tag);

        if (strlen($term['slug']) < 3) {
            $notSaveLenthF++;
        } else {
            $id = $TERM->add_term($term['name'], $term['slug'], '');
            if ($id) {
                if ($texo = $TERM->add_texo($id, $texo)) {
                    if (!empty($Pid)) {
                        $TERM->add_term_relation($Pid, $texo);
                    }
                    $info = array("msg" => "$texo Added successfuly !", 'rf' => "", "error" => 0);
                }
            } else {
                $info = array("msg" => "Error..!!  $texo not Save " . $res['error'], "error" => 1);
            }
        }
    }
    if ($notSaveLenthF) {
        $info = array("msg" => "$notSaveLenthF item not saved, Slug length must be more than 2 cherecter. ", "error" => true);
        echo json_encode($info);
    }
}

//add_content_filter('store_content_data',100);
//function store_content_data($content){
//    global $POST;
//    $file=  md5($_SERVER['REQUEST_URI']);
//    $filename=UPLOAD."cache/$file.html";
//    file_put_contents($filename, $content);
//    return $content;
//}

function makeCopy() {
    global $POSTS, $DB, $adminDir;
    $id = $_POST['ID'];
    $metas = get_post_metas($id);
    $post = get_post($id);

    //===========Changes
    $post['post_title'] = "Copy of " . $post['post_title'];
    $post['post_name'] = "copy-of-" . $post['post_name'];
    $post['post_status'] = 'draft';
    unset($post['ID'], $post['guid'], $post['post_date_gmt'], $post['post_modified_gmt']);

    $info = array('red' => false, 'msg' => "Post was not cpopied $DB->error");
    $insId = $POSTS->post_add($post);
    if ($insId) {
        foreach ($metas as $metak => $metaVal) {
            update_post_meta($insId, trim($metak), trim($metaVal));
        }
        $redUrl = domain() . $adminDir . "/index.php?l=edit&post-type=$post[post_type]&ID=$insId";
        $info = array('red' => $redUrl, 'msg' => '');
    }
    echo json_encode($info);
}

function texo_short_filter($texo) {
    global $TERM;
    //====IF Texonomy short
    $customTexoParent = $TERM->texoListRow($texo, true);
    //var_dump($customTexoParent);
    $curentTexo = isset($_SESSION['texo']) ? $_SESSION['texo'] : "";
    $customTexoAll = $TERM->texoListRow($texo);
    $curentTexoT = "";
    foreach ($customTexoAll as $tx) {
        if ($tx['taxonomy_id'] == $curentTexo) {
            // var_dump($tx);
            $curentTexoT = "(<font color='green'>$tx[name]</font>)";
        }
    }

    $texoShort = "<div class=\"dropdown\">
                            <a href='javascript:void(0)' class=\"dropdown-toggle\" id=\"dropdownMenuButton\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                              $texo $curentTexoT
                            </a>
                            <div class=\"dropdown-menu\" aria-labelledby=\"dropdownMenuButton\">";
    $texoShort.="<a class=\"dropdown-item\" onclick=\"load_list('texo=')\" href=\"javascript:void(0)\">All</a>";
    foreach ($customTexoParent as $parentCat) {
        $texoShort.="<a class=\"dropdown-item\" onclick=\"load_list('texo=$parentCat[taxonomy_id]')\" href=\"javascript:void(0)\">$parentCat[name]</a>";
        $customTexoChild = $TERM->texoListRow($texo, $parentCat['taxonomy_id']);
        foreach ($customTexoChild as $ChildCat) {
            $texoShort.="<a class=\"dropdown-item\" onclick=\"load_list('texo=$ChildCat[taxonomy_id]')\" href=\"javascript:void(0)\">» $ChildCat[name]</a>";
        }
    }


    $texoShort.="</div>
                          </div>";

    return $texoShort;
    //=============
}

function SEVisibility() {
    add_option('search_engine_visibility', "");
    //var_dump($_POST);
    $se = $_POST['SE'];
    var_dump($se);
    $disallowStr = "User-agent: *
Disallow: /
";

    $robotXT = @file_get_contents(ABSPATH . 'robots.txt');

    if ($se == 'true') {
        $robotXT = str_replace($disallowStr, "", $robotXT);
        update_option("search_engine_visibility", '0');
    } else {
        $robotXT = $disallowStr . $robotXT;
        update_option("search_engine_visibility", '1');
    }
    file_put_contents(ABSPATH . "robots.txt", $robotXT);
}

function findPageToAddLink() {
    $enable_type_slug = unserialize(get_option('enable_type_slug'));
    $enable_type_slug = !empty($enable_type_slug) ? $enable_type_slug : array('post', 'page');
    $enable_type_slug = array_filter($enable_type_slug);

    $sq = trim($_POST['str']);
    $con = " and (post_title like '%$sq%' or post_name like '%$sq%' or post_content like '%$sq%')";
    //var_dump($con);
    $arg = array(
        'numberposts' => -1,
        'orderby' => 'post_date_gmt',
        'order' => 'DESC',
        'post_type' => $enable_type_slug,
        'post_status' => 'published',
        'selectFields' => "*",
        'condition' => $con
    );
    $posts = get_posts($arg);

    $html = "";
    $protocol = get_protocol();
    foreach ($posts as $post) {
        $lnk = get_link($post['ID']);
        if ($protocol == "https://") {
            $lnk = str_replace("http://", "https://", $lnk);
        }

        $html.="<a href='javascript:void(0)' title='$post[post_title]' class='linkAddClick' onclick='addLink(this)' dataLink='$lnk'>$post[post_title]</a>";
    }
    echo $html;
}

function cannedData() {
    $arg = array(
        'numberposts' => -1,
        'orderby' => 'post_date_gmt',
        'order' => 'DESC',
        'exclude_field' => "ID",
        'post_type' => 'cannedInsert',
        'post_status' => 'published',
    );
    $posts = get_posts($arg);
    $htm = "<table style='width:500px' class='cannedTable table '><tr><th>Title</th><th>Content</th><th></th></tr>";
    foreach ($posts as $canned) {
        $title = str_replace("_canned", "", $canned['post_title']);
        $orgCont = $canned['post_content'];
        $content = substr(strip_tags($canned['post_content']), 0, 50) . '...';

        $htm.="<tr>
                 <td>$title</td>
                 <td>$content</td>
                 <td><button class='canndInsert' onclick='cndIns(this)'>Insert</button><button class='dltCannd' onclick='canndDelete($canned[ID])'>×</button><textarea style='display:none' id='canned_$canned[ID]'>$orgCont</textarea></td>
               </tr>";
    }
    $htm.="<table>";

    echo $htm;
}

function addCanned() {
    global $POSTS, $DB;
    $data = array();
    $data['post_title'] = $_POST['ttl'] . "_canned";
    $data['post_content'] = $_POST['str'];
    $data['post_type'] = 'cannedInsert';
    $data['post_status'] = 'published';
    $inf = array('');
    if ($POSTS->post_add($data)) {
        $inf = array('msg' => 'Canned Inserted', 'error' => 0);
    } else {
        $inf = array('msg' => 'Canned Not Inserted, Please Try again', 'error' => $DB->error);
    }
    echo json_encode($inf);
}

function canndDelete() {
    global $DB;
    $id = $_POST['id'];
    $res = $DB->delete('post', "ID=$id");
    if ($res) {
        $inf = array('msg' => "Canned data Deleted", "error" => 0);
    } else {
        $inf = array('msg' => "Canned data faild to Deleted", "error" => $DB->error);
    }
    echo json_encode($inf);
}

function debugMood() {
    if (isset($_POST['act'])) {
        $dbgPath = ADMIN_ROOT . 'config/';
    }
}

function MovePost() {
    global $DB;
    $selected = array();
    $moveTo = trim($_POST['moveTo']);
    //var_dump($moveTo);
    //exit;
    parse_str($_REQUEST['listdata'], $selected);
    $IDs = implode(",", $selected['selected']);
    $upd = $DB->update("post", array('post_type' => $moveTo), "ID in($IDs)");
    if ($upd) {
        $info = array("msg" => "Date Moved successfuly !", 'rf' => "", "error" => 0);
    } else {
        $info = array("msg" => "Error..!! " . $res['error'], "error" => 1);
    }
    echo json_encode($info);
}
