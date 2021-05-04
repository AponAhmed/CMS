<?php
/*
 * Plugin Name: SEO
 * Plugin URI: http://siatex.com
 * Version: 2.0
 * Author: SiATEX
 * Author URI: http://www.siatex.com
 * Description: Simple SEO, Title,Description,Keyword Editor
 * License: GPL2
 */

if (defined('ADMIN')) {

    add_admin_menu(
            array(
                'slug' => 'seo_edit',
                'menu_title' => "SEO",
                'icon' => "fas fa-chart-line",
                'icon_img' => "",
                'order' => "50",
                'parent_slug' => "tools"
            )
    );

    add_texo_meta(
            array(
                'label' => '',
                'comment' => '',
                'html' => '<div class="seoViewerArea">
                    <a href=\'#\' class=\'seoPrvshow\' style="display:none" onclick=\'$(".SEOpreviewer").show(); $(this).hide()\'>Show</a>
                    <div class="SEOpreviewer">
                        <a href=\'#\' class=\'seoPrvClose\' onclick=\'$(this).parent().hide();
                                    $(".seoPrvshow").show()\'>×</a>
                        <div class="inside serpOUT">
                            <h3 id="Otitle">Title</h3>
                            <p id="Ourl" class="url">
                                <a target="_blank" href="http://">http://</a>
                            </p>
                            <p id="OmD" class=\'metaDes\'></p>
                            <p id="Omk" class=\'metaKey\'></p>
                        </div>
                    </div>
                </div>
                <script>
                $(".admin-inner").scroll(function() {
                    serp_calc_texo();
                });
                $(function(){
                  serp_calc_texo();
                    var hrf=$("#termLink").attr("href");
                    //alert(hrf);
                    $("#Ourl > a").html(hrf).attr("href",hrf);
                    serp_calc_texo();
                });
                </script>',
                'texos' => array('product-group', 'category', 'tag', 'Page category')
            )
    );

    add_texo_meta(
            array(
                'label' => 'Custom Title  <span class="seoCount"><span id="ttlCount"></span> / 60 char.</span>',
                'comment' => 'Custom page title',
                'html' => '<input type="text" name="meta[customTitle]" onkeyup="serp_calc_texo()" onchange="serp_calc_texo()" id="customTitle" class="form-control form-control-sm">',
                'texos' => array('product-group', 'category', 'tag', 'Page category')
            )
    );
    add_texo_meta(
            array(
                'label' => 'H1 Text <span class="seoCount"><span id="h1Count"></span> / 70 char.</span>',
                'comment' => '',
                'html' => '<input  type="text" name="meta[meta_h1_text]"  onkeyup="serp_calc_texo()" onchange="serp_calc_texo()"  id="meta_h1_text" class="form-control form-control-sm">',
                'texos' => array('product-group', 'category', 'tag', 'Page category')
            )
    );
    add_texo_meta(
            array(
                'label' => 'Logo Text',
                'comment' => '',
                'html' => '<input type="text" name="meta[meta_logo_title]" id="meta_logo_title" class="form-control form-control-sm">',
                'texos' => array('product-group', 'category', 'tag', 'Page category')
            )
    );

    add_texo_meta(
            array(
                'label' => 'Meta Description  <span id="massDes" class="comment m0"></span> <span class="seoCount"><span id="descCount"></span> / 155 char.</span>',
                'comment' => '',
                'html' => '<textarea type="text" name="meta[meta_description]" id="meta_description"  onkeyup="serp_calc_texo()" onchange="serp_calc_texo()"  class="form-control form-control-sm"></textarea>',
                'texos' => array('product-group', 'category', 'tag', 'Page category')
            )
    );
    add_texo_meta(
            array(
                'label' => 'Meta Keyword <span id="massKey" class="comment m0"></span> <span class="seoCount"><span id="keyCount"></span> / 10 Phrase</span>',
                'comment' => '',
                'html' => '<textarea type="text" name="meta[meta_keyword]" id="meta_keyword"  onkeyup="serp_calc_texo()" onchange="serp_calc_texo()"  class="form-control form-control-sm"></textarea>',
                'texos' => array('product-group', 'category', 'tag', 'Page category')
            )
    );

    add_texo_meta(
            array(
                'label' => 'Meta Robots ',
                'comment' => '',
                'html' => '&nbsp;<select id="robot" class="custom-select custom-select-sm" name="meta[robot]" style="max-width:120px;">
        <option selected="selected" value="index, all">Index, All</option>	
        <option value="index, follow">Index, follow</option>
        <option value="index, nofollow">Index, no-follow</option>	
        <option value="noindex, follow">No-index, follow</option>
        <option value="noindex, nofollow">No-index, no-follow</option>
        <option value="noodp, noydir">Noodp, Noydir</option>
        <option value="none">None</option>
    </select>',
                'texos' => array('product-group', 'category', 'tag', 'Page category')
            )
    );
}

function termHiddenText($texoSlug) {
    global $DB;
    $term = $DB->select("terms as trm left join term_taxonomy as tx on trm.term_id=tx.term_id", "trm.term_id", "trm.slug='$texoSlug'");
    //var_dump($term);
    $txt = get_term_meta($term[0]['term_id'], 'meta_hidden_text');
    echo content_filter($txt);
}

function seo_edit() {
    ?>
    <h1>SEO Editor</h1>
    <hr>
    <div class="row">
        <div class="col-sm-12">
            <ul class="nav nav-tabs" id="myTab" role="tablist" style="margin-bottom: 15px;">
                <li class="nav-item">
                    <a class="nav-link active" id="seoList-tab" data-toggle="tab" href="#seoList" role="tab" aria-controls="seoList" aria-selected="true">List</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="seoOption-tab" data-toggle="tab" href="#seoOption" role="tab" aria-controls="seoOption" aria-selected="false">Option</a>
                </li>
            </ul>
            <div class="tab-content" id="asdsad">
                <div class="tab-pane fade show active" id="seoList" role="tabpanel" aria-labelledby="seoList-tab">
                    <?php
                    $postTypes = array('page', 'post');
                    $n = 0;
                    foreach ($postTypes as $poostType) {
                        ?><strong style="text-transform: uppercase">::<?php echo $poostType; ?></strong><hr><?php
                        $default = array(
                            'numberposts' => -1,
                            'orderby' => 'post_date_gmt',
                            'order' => 'DESC',
                            'post_type' => "$poostType",
                            'post_status' => 'published',
                            'selectFields' => "ID,post_title,post_name",
                        );
                        if ($n == 0) {
                            echo "<div class='row'>
                    <div class='col-sm-4'>
                       <strong class='tblHead'>Page Title</strong>
                    </div>
                    <div class='col-sm-4'>
                        <strong class='tblHead'>H1</strong>
                    </div> 
                    <div class='col-sm-4'>
                       <strong class='tblHead'>Slug</strong>
                     </div>
                    </div>";
                        }
                        $n++;
                        $posts = get_posts($default);
                        foreach ($posts as $post) {
                            $metas = get_post_metas($post['ID']);
                            ?>
                            <div class="SeoItemArea">
                                <div class="SeoItemAreaR">
                                    <a onclick='loadSeo(<?php echo $post['ID'] ?>, this)' href="javascript:void(0)"><?php echo!empty($metas['meta_title']) ? $metas['meta_title'] : $post['post_title'] ?></a>
                                    <a onclick='loadSeo(<?php echo $post['ID'] ?>, this)' href="javascript:void(0)"><?php echo!empty($metas['meta_h1_text']) ? $metas['meta_h1_text'] : "Not Set" ?></a>

                                    <a target="_blank" href="<?php echo get_link($post['ID']) ?>"><?php echo $post['post_name'] ?></a>

                                </div>
                                <div class="SeoPreview"></div>
                                <a href="javascript:void(0)" class="removeLoadad" onclick="removeSeoPreview(this)">×</a>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
                <div class="tab-pane fade" id="seoOption" role="tabpanel" aria-labelledby="seoOption-tab">
                    <div class="col-sm-12">
                        <form method="post" id="optionsFrm">
                            <div class='row fieldRow'>
                                <div class="col-sm-3">
                                    <label>OG Logo</label>
                                </div>
                                <div class='col-sm-9'>
                                    <label onclick="fbox(this)" load="c=forms&m=library&FieldId=ogLogo&calback=ogLogo" w="850" h="500" for="browse_pro" class="browse_fav"><i class="far fa-folder-open"></i></label>
                                    <input name="options[ogLogo]" type="hidden" value="<?php echo get_option('ogLogo') ?>" id="ogLogo">
                                    <div id='ogLogoSelectedImage'>
                                        <?php
                                        $idS = get_option('ogLogo');
                                        if (!empty($idS)) {
                                            $idArray = explode(",", $idS);
                                            $iconData = get_post($idArray[0], 'guid');
                                            $src = $iconData['guid'];
                                            echo "<div class='favImg' id='item_$idArray[0]' ><img src='$src'>  <a class='ImgRemove' onclick='removeit(this)' href=\"javascript:\">×</a></div>";
                                        }
                                        ?>
                                    </div>
                                    <script>
                                        function ogLogo(obj) {
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
                                                        // alert(img);
                                                        //                         m.forEach((match, groupIndex) => {
                                                        //                            console.log(`Found match, group ${groupIndex}: ${match}`);
                                                        //                         });
                                                    }
                                                }


                                                var added = $("#ogLogo").val();
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
                                                        $("#ogLogoSelectedImage").append("<div class='favImg' id='item_" + id + "' ><i class='" + iconClass + "'></i> <a class='ImgRemove' onclick='removeit(this)' href=\"javascript:\">×</a></div>");
                                                    } else {
                                                        $("#ogLogoSelectedImage").append("<div class='favImg' id='item_" + id + "' ><img src='" + img + "'>  <a class='ImgRemove' onclick='removeit(this)' href=\"javascript:\">×</a></div>");
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
                                            var str = $("#ogLogo").val();
                                            str = str.replace(id, "");
                                            $("#ogLogo").val(str.replace(/(^,)|(,$)/g, ""));
                                            $("#" + itm).remove();
                                        }
                                    </script>
                                </div>
                            </div>
                            <div class="settings_updateArea">
                                <button type="button" class='btn btn-cms-primary updateSettingsBtn' onclick="saveOptions(optionsFrm, this)">Update Settings</button>
                            </div>
                    </div> 
                </div>
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

    </div>
    </div>
    <script>

        function generateKeyConsistency(_this, num) {
            $(_this).append(loader);
            var data = {ajx_action: "generateKeyConsistency", ID: $("#ID").val(), num: num};
            jQuery.post('index.php', data, function(response) {
                $("#consistencyContent").html(response);
                $(_this).find(".spinLoader").remove();
            });
        }

        function loadSeo(id, _this) {
            $(_this).append(loader);
            $('.SeoPreview').html("");
            $(".removeLoadad").hide()
            var data = {ajx_action: "QuickEdit", ID: id};
            jQuery.post('index.php', data, function(response) {
                $(_this).parent().parent().find(".SeoPreview").html(response);
                $(_this).parent().parent().find(".removeLoadad").show()
                $(_this).find(".spinLoader").remove();
                serp_calc();
                generateKeyConsistency($("#generateKeyConsistencyBtn"), 5);
            });
        }
        function removeSeoPreview(_this) {
            $(_this).parent().find('.SeoPreview').html("");
            $(_this).hide();
        }
        function updateMetaTag() {
            var meta_title = $("#Ctitle").val();
            var meta_h1_text = $("#h1Txt").val();
            var meta_description = $("#metaD").val();
            var meta_keyword = $("#metaK").val();
            var articleH = $("#articleH").val();
            var robot = $("#MetaRobot").val();
            var data = {
                ajx_action: 'updateMetaTag',
                ID: $("#ID").val(),
                slug: $("#slug").val(),
                meta: {
                    meta_title: meta_title,
                    meta_h1_text: meta_h1_text,
                    meta_description: meta_description,
                    meta_keyword: meta_keyword,
                    robot: robot,
                    meta_harticle_text: articleH
                }
            }
            jQuery.post('index.php', data, function(response) {
                $(".seoUpdMsg").html(response);
                setTimeout(function() {
                    $(".seoUpdMsg").html("");
                }, 2000);
            });
        }
    </script>   
    <?php
}

function get_seo($echo = true) {
    global $POST, $QV, $TITLE, $KEYWORD, $DESCRIPTION, $PRODUCT, $ROBOT, $TERM;
    //var_dump($POST);
    $metas = get_post_metas(@$POST['ID']);
    $customTitle = isset($metas['meta_title']) ? $metas['meta_title'] : "";
    $DESCRIPTION = isset($metas['meta_description']) ? $metas['meta_description'] : "";
    $KEYWORD = isset($metas['meta_keyword']) ? $metas['meta_keyword'] : "";
    $metaRobot = isset($metas['robot']) ? $metas['robot'] : "";

    $ROBOT = empty($metaRobot) ? "index,all" : $metaRobot;
    if (!empty($customTitle)) {
        $TITLE = $customTitle;
    }

    if (isset($QV['product_category']) && !isset($QV['product_slug'])) {
        $term_id = term_slug2Id($QV['product_category']);
        if ($term_id) {
            $term = $TERM->get_term($term_id);
            //var_dump($term);
            $termTT = @$term['meta']['customTitle'];
            $TITLE = empty($termTT) ? $term['name'] : $termTT;

            $ddDes = substr(strip_tags($term['description']), 0, 155);
            $DESCRIPTION = empty($term['meta']['meta_description']) ? $ddDes : $term['meta']['meta_description'];
            $KEYWORD = empty($term['meta']['meta_keyword']) ? $term['name'] : $term['meta']['meta_keyword'];

            $metaRobot = get_term_meta($term_id, 'robot');
            $ROBOT = empty($metaRobot) ? "index,all" : $metaRobot;
        }
    }
    if (isset($QV['post_category']) && !isset($QV['post_slug'])) {
        $term_id = term_slug2Id($QV['post_category']);
        if ($term_id) {
            $term = $TERM->get_term($term_id);
            //var_dump($term);
            $termTT = @$term['meta']['customTitle'];
            $TITLE = empty($termTT) ? $term['name'] : $termTT;

            $ddDes = substr(strip_tags($term['description']), 0, 155);
            $DESCRIPTION = empty($term['meta']['meta_description']) ? $ddDes : $term['meta']['meta_description'];
            $KEYWORD = empty($term['meta']['meta_keyword']) ? $term['name'] : $term['meta']['meta_keyword'];

            $metaRobot = get_term_meta($term_id, 'robot');
            $ROBOT = empty($metaRobot) ? "index,all" : $metaRobot;
        }
    }



    if (!empty($QV['post_slug'])) {
        $POST_ID = slug2id($QV['post_slug']);
        if ($POST_ID) {
            $TITLE = get_post_meta($POST_ID, 'meta_title');
            $BlogPost = get_post($POST_ID, 'post_title');

            $TITLE = !empty($TITLE) ? $TITLE : $BlogPost['post_title'];
            $DESCRIPTION = get_post_meta($POST_ID, 'meta_description');
            $KEYWORD = get_post_meta($POST_ID, 'meta_keyword');
        } else {
            $term_id = term_slug2Id($QV['post_slug']);
            if ($term_id) {
                $term = $TERM->get_term($term_id);
                //var_dump($term);
                $termTT = $term['meta']['customTitle'];
                $TITLE = empty($termTT) ? $term['name'] : $termTT;
                $ddDes = substr(strip_tags($term['description']), 0, 155);
                $DESCRIPTION = empty($term['meta']['meta_description']) ? $ddDes : $term['meta']['meta_description'];
                $KEYWORD = empty($term['meta']['meta_keyword']) ? $term['name'] : $term['meta']['meta_keyword'];

                $metaRobot = get_term_meta($term_id, 'robot');
                $ROBOT = empty($metaRobot) ? "index,all" : $metaRobot;
            }
        }
    }

    if (isset($QV['catalog_category']) && !isset($QV['catalog_slug'])) {
        $term_id = term_slug2Id($QV['catalog_category']);
        if ($term_id) {
            $term = $TERM->get_term($term_id);
            //var_dump($term);
            $termTT = $term['meta']['customTitle'];
            $TITLE = empty($termTT) ? $term['name'] : $termTT;
            $ddDes = substr(strip_tags($term['description']), 0, 155);
            $DESCRIPTION = empty($term['meta']['meta_description']) ? $ddDes : $term['meta']['meta_description'];
            $KEYWORD = empty($term['meta']['meta_keyword']) ? $term['name'] : $term['meta']['meta_keyword'];

            $metaRobot = get_term_meta($term_id, 'robot');
            $ROBOT = empty($metaRobot) ? "index,all" : $metaRobot;
        }
    }
    if (!empty($QV['catalog_slug'])) {
        $POST_ID = slug2id($QV['catalog_slug']);
        if ($POST_ID) {
            $TITLE = get_post_meta($POST_ID, 'meta_title');
            $BlogPost = get_post($POST_ID, 'post_title');

            $TITLE = !empty($TITLE) ? $TITLE : $BlogPost['post_title'];
            $DESCRIPTION = get_post_meta($POST_ID, 'meta_description');
            $KEYWORD = get_post_meta($POST_ID, 'meta_keyword');
        } else {
            $term_id = term_slug2Id($QV['catalog_slug']);
            if ($term_id) {
                $term = $TERM->get_term($term_id);
                //var_dump($term);
                $termTT = $term['meta']['customTitle'];
                $TITLE = empty($termTT) ? $term['name'] : $termTT;
                $ddDes = substr(strip_tags($term['description']), 0, 155);
                $DESCRIPTION = empty($term['meta']['meta_description']) ? $ddDes : $term['meta']['meta_description'];
                $KEYWORD = empty($term['meta']['meta_keyword']) ? $term['name'] : $term['meta']['meta_keyword'];

                $metaRobot = get_term_meta($term_id, 'robot');
                $ROBOT = empty($metaRobot) ? "index,all" : $metaRobot;
            }
        }
    }

    if (!empty($QV['product_slug'])) {
        $metaRobot = get_post_meta($POST['ID'], 'robot');
        $ROBOT = empty($metaRobot) ? "index,all" : $metaRobot;
    }
    if (!empty($QV['product_slug'])) {
        $POST_ID = slug2id($QV['product_slug']);
        if ($POST_ID) {
            $TITLE = get_post_meta($POST_ID, 'meta_title');
            $BlogPost = get_post($POST_ID, 'post_title');

            $TITLE = !empty($TITLE) ? $TITLE : $BlogPost['post_title'];
            $DESCRIPTION = get_post_meta($POST_ID, 'meta_description');
            $KEYWORD = get_post_meta($POST_ID, 'meta_keyword');
        } else {
            $term_id = term_slug2Id($QV['product_slug']);
            if ($term_id) {
                $term = $TERM->get_term($term_id);
                //var_dump($term);
                $termTT = $term['meta']['customTitle'];
                $TITLE = empty($termTT) ? $term['name'] : $termTT;
                $ddDes = substr(strip_tags($term['description']), 0, 155);
                $DESCRIPTION = empty($term['meta']['meta_description']) ? $ddDes : $term['meta']['meta_description'];
                $KEYWORD = empty($term['meta']['meta_keyword']) ? $term['name'] : $term['meta']['meta_keyword'];

                $metaRobot = get_term_meta($term_id, 'robot');
                $ROBOT = empty($metaRobot) ? "index,all" : $metaRobot;
            }
        }
    }



    $html = "";
    $ROBOT = preg_replace("/[\s]+/", "", $ROBOT);
    if ($ROBOT != "none") {
        $html.= "<meta name=\"robots\" content=\"$ROBOT\" />\n";
        // $html.= "<meta name=\"googlebot\" content=\"$ROBOT\" />\n";
    }
    if (isset($QV['page_no']) && !empty($QV['page_no'])) {
        $TITLE = "Page " . $QV['page_no'] . " - " . $TITLE;
    }

    $TITLE = filterSeoMeta($TITLE);
    $DESCRIPTION = filterSeoMeta($DESCRIPTION);
    $KEYWORD = filterSeoMeta($KEYWORD);

    add_header($html, 10);
}

function filterSeoMeta($str) {
    $str = shortcode_exe($str);
    return $str;
}

function seo_init_filterDescs($html) {
    $html = str_replace('name="description"', 'name="description" lang="EN"', $html);
    return $html;
}

function seo_init_filterTtle($html) {
    $html = str_replace('<title>', '<title lang="EN">', $html);
    return $html;
}

function get_page_title($id) {
    $t = get_post_meta($id, 'meta_title');
    if (empty($t)) {
        $t = get_post_title($id);
    }
    return $t;
}

if (!defined('ADMIN')) {
    get_seo();

    global $TITLE, $DESCRIPTION, $POST;

    $currentUrl = get_link(@$POST['ID']);
    $ogInfo = '
<meta property="og:title" content="' . $TITLE . '" />
<meta property="og:site_name" content="' . site_name() . '" />
<meta property="og:description" content="' . $DESCRIPTION . '" />
<meta property="og:type" content="website" />
<meta property="og:url" content="' . $currentUrl . '" /> ';

    $src = "";
    $idS = get_option('ogLogo');
    if (!empty($idS)) {
        $idArray = explode(",", $idS);
        $iconData = get_post($idArray[0], 'guid');
        $src = $iconData['guid'];
        $ogInfo.="<meta property=\"og:image\" content=\"$src\" />";
    }
//<meta name="twitter:site" content="@aptex">
//<meta name="twitter:creator" content="@aptex">
//    $ogInfo.='
//<!-- Twitter Card data -->
//<meta name="twitter:card" content="summary_large_image" />  
//<meta name="twitter:title" content="' . $TITLE . '"/>
//<meta name="twitter:description" content="' . $DESCRIPTION . '"/>
//<meta name="twitter:image" content="' . $src . '"/>' . "\n";
    // add_header($ogInfo, 1);
} else {
    add_metabox(array(
        'title' => "SEO",
        'Description' => "",
        'position' => "",
        'type' => "post,page,product,services",
        'calback' => 'seo'
    ));


    admin_add_script(array('id' => "SeoAdminJS", 'src' => plugin_path(__FILE__) . "/js/seo.js", 'order' => 5, 'position' => 'head'));
    if (isset($_GET['l']) && (@$_GET['l'] == "new-page" || @$_GET['l'] == "edit")) {
        //admin_add_script(array('id' => "wordstats", 'src' => plugin_path(__FILE__) . "/js/jquery.wordstats.js", 'order' => 0, 'position' => 'head'));
        // admin_add_script(array('id' => "wordstaten", 'src' => plugin_path(__FILE__) . "/js/jquery.wordstat.en.js", 'order' => 3, 'position' => 'head'));
        admin_add_script(array('id' => "keywordsjs", 'src' => plugin_path(__FILE__) . "/js/keywords.js", 'order' => 2, 'position' => 'head'));
    }

    admin_add_style(array('id' => 'seoCustomCss', 'href' => plugin_path(__FILE__) . "/css/admin_style.css", 'order' => 5));
}

//<meta property="fb:admins" content="Facebook numeric ID" />

function seo() {
    global $POST;
    $sseo_meta_tag_robots = get_post_meta(@$POST['ID'], 'robot');
    ?>
    <div class="mBoxBody">

        <ul class="nav nav-tabs" id="myTab" role="tablist"> 
            <li class="nav-item">
                <a class="nav-link active" id="seoEditor-tab" data-toggle="tab" href="#seoEditor" role="tab" aria-controls="seoEditor" aria-selected="false">Editor</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " id="SeoPreview-tab" data-toggle="tab" href="#SeoPreview" role="tab" aria-controls="SeoPreview" aria-selected="true">Preview</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <br>
            <div class="tab-pane fade show active" id="seoEditor" role="tabpanel" aria-labelledby="fileNdata-tab">

                <!--Separator-->
                <div class="keywordConsistency">
                    <button type="button" id="generateKeyConsistencyBtn" class="btn btn-cms-default" onclick='generateKeyConsistency(this, 5)' style='margin-bottom:10px'>Generate Consistency</button>
                    <button type="button" id="" class="btn btn-cms-default" onclick='slugKeylookup(this)' style='margin-bottom:10px'>Lookup slug Key</button>

                    <br>
                    <div id="consistencyContent"></div>
                </div>  
                <hr>
                <div class="seoRobot" style="margin-right:15px;">
                    <label>Meta Robots : </label>
                    <select id="MetaRobot" class="custom-select custom-select-sm" name="meta[robot]" style="max-width:120px;">
                        <option value="none" <?php echo $sseo_meta_tag_robots == "none" ? "selected" : "" ?>>None</option>
                        <option value="index, all" <?php echo $sseo_meta_tag_robots == "index, all" ? "selected" : "" ?>>Index, All</option>	
                        <option value="index, follow" <?php echo $sseo_meta_tag_robots == "index, follow" ? "selected" : "" ?>>Index, follow</option>
                        <option value="index, nofollow" <?php echo $sseo_meta_tag_robots == "index, nofollow" ? "selected" : "" ?>>Index, no-follow</option>	
                        <option value="noindex, follow" <?php echo $sseo_meta_tag_robots == "noindex, follow" ? "selected" : "" ?>>No-index, follow</option>
                        <option value="noindex, nofollow"<?php echo $sseo_meta_tag_robots == "noindex, nofollow" ? "selected" : "" ?>>No-index, no-follow</option>
                        <option value="noodp, noydir"<?php echo $sseo_meta_tag_robots == "noodp, noydir" ? "selected" : "" ?>>Noodp, Noydir</option>
                    </select>
                </div>
                <button type="button" onclick="cleanKeyDes()" class="btn btn-cms-default float-right">Clean & update</button>
                <label class="CusMetaLabel">Title 
                    <div class="seoUpdMsg"></div>
                    <span class="seoCount"><span id="ttlCount"></span> / 70 char.</span></label>
                <input name="meta[meta_title]" id="Ctitle" onkeyup="serp_calc()" onchange="serp_calc()" value="<?php echo get_post_meta(@$POST['ID'], 'meta_title') ?>" type="text" class="form-control form-control-sm">
                <label class="CusMetaLabel">H1 Text <span class="seoCount"><span id="h1Count"></span> / 70 char.</span></label>
                <input id="h1Txt" name="meta[meta_h1_text]" value="<?php echo get_post_meta(@$POST['ID'], 'meta_h1_text') ?>" onkeyup="serp_calc()" onchange="serp_calc()" type="text" class="form-control form-control-sm">
                <label>Article Heading</label>
                <input id="articleH" name="meta[meta_harticle_text]" value="<?php echo get_post_meta(@$POST['ID'], 'meta_harticle_text') ?>" onkeyup="serp_calc()" onchange="serp_calc()" type="text" class="form-control form-control-sm">

                <!--        <label>H2 Text</label>
                    <input name="meta[meta_h2_text]" value="<?php // echo get_post_meta($POST['ID'], 'meta_h2_text')                                                                                                                                                                                                                                                                                                                                                                                                    ?>" type="text" class="form-control form-control-sm">
                    <label>H3 Text</label>
                    <input name="meta[meta_h3_text]" value="<?php // echo get_post_meta($POST['ID'], 'meta_h3_text')                                                                                                                                                                                                                                                                                                                                                                                                    ?>" type="text" class="form-control form-control-sm">
                <hr>-->
                <label>Logo Title</label>
                <input id="meta_logo_title" name="meta[meta_logo_title]" value="<?php echo get_post_meta(@$POST['ID'], 'meta_logo_title') ?>" type="text" class="form-control form-control-sm">

                <label  class="CusMetaLabel">Meta Description <span id="massDes" class="comment m0"></span> <span class="seoCount"><span id="descCount"></span> / 155 char.</span></label>
                <textarea name="meta[meta_description]" id="metaD" onkeyup="serp_calc()" onchange="serp_calc()" class="form-control"><?php echo get_post_meta(@$POST['ID'], 'meta_description') ?></textarea>


                <label  class="CusMetaLabel">Meta Keyword <span id="massKey" class="comment m0"></span> <span class="seoCount"><span id="keyCount"></span> / 10 Phrase</span></label>
                <textarea name="meta[meta_keyword]"  id="metaK" onkeyup="serp_calc()" onchange="serp_calc()" class="form-control"><?php echo get_post_meta(@$POST['ID'], 'meta_keyword') ?></textarea>

            </div>
            <div class="tab-pane fade " id="SeoPreview" role="tabpanel" aria-labelledby="statiSite-tab">
                <div class="seoViewerArea">
                    <a href='#' class='seoPrvshow' style="display:none" onclick='$(".SEOpreviewer").show();
                                $(this).hide()'>Show</a>
                    <div class="SEOpreviewer">
                        <a href='#' class='seoPrvClose' onclick='$(this).parent().hide();
                                    $(".seoPrvshow").show()'>×</a>
                        <div class="inside serpOUT">
                            <h3 id="Otitle">Title</h3>
                            <p id="Ourl" class='url'><a target="_blank" href="<?php echo get_link(@$POST['ID']); ?>"><?php echo get_link(@$POST['ID']); ?></a></p>
                            <p id="OmD" class='metaDes'></p>
                            <p id="Omk" class='metaKey'></p>
                            <a href="https://validator.w3.org/nu/?showsource=yes&doc=<?php echo get_link(@$POST['ID']); ?>" target="_blank" class="btn btn-cms-primary btn-sm">Validator</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            //generateKeyConsistency($("#generateKeyConsistencyBtn"), 5);
            serp_calc();
            $("#Ctitle").on('change', function() {
                updateMetaTag();
            });
            $("#metaD").on('change', function() {
                updateMetaTag();
            });
            $("#metaK").on('change', function() {
                updateMetaTag();
            });
            $("#h1Txt").on('change', function() {
                updateMetaTag();
            });
            $("#meta_logo_title").on('change', function() {
                updateMetaTag();
            });
            $("#MetaRobot").on('change', function() {
                updateMetaTag();
            });
            $("#articleH").on('change', function() {
                updateMetaTag();
            });

            $("#CtitleEs").on('change', function() {
                updateMetaTag();
            });
            $("#metaDEs").on('change', function() {
                updateMetaTag();
            });
        });

        function generateKeyConsistency(_this, num) {
            if (num == 10) {
                $("#moreBtn").attr('onclick', 'generateKeyConsistency(this,5)');
                $("#moreBtn").html('Less');
            }
            $(_this).append(loader);
            var data = {ajx_action: "generateKeyConsistency", ID: $("#ID").val(), num: num};
            jQuery.post('index.php', data, function(response) {
                $("#consistencyContent").html(response);
                $(_this).find(".spinLoader").remove();
            });
        }

        function updateMetaTag() {
            var meta_title = $("#Ctitle").val();
            var meta_titleES = $("#CtitleEs").val();
            var meta_h1_text = $("#h1Txt").val();
            var meta_description = $("#metaD").val();
            var meta_descriptionEs = $("#metaDEs").val();
            var meta_keyword = $("#metaK").val();
            var articleH = $("#articleH").val();
            var robot = $("#MetaRobot").val();
            var meta_logo_title = $("#meta_logo_title").val();
            var data = {
                ajx_action: 'updateMetaTag',
                ID: $("#ID").val(),
                meta: {
                    meta_title: meta_title,
                    meta_h1_text: meta_h1_text,
                    meta_description: meta_description,
                    meta_keyword: meta_keyword,
                    robot: robot,
                    meta_harticle_text: articleH,
                    meta_logo_title: meta_logo_title
                }
            }
            jQuery.post('index.php', data, function(response) {
                $(".seoUpdMsg").html(response);
                setTimeout(function() {
                    $(".seoUpdMsg").html("");
                }, 2000);
            });
        }
    </script>
    <?php
}

function updateMetaTag() {
    global $POSTS;
    if (isset($_POST['meta'])) {
        $metas = $_POST['meta'];
        $nMeta = count($metas);
        $n = 0;
        foreach ($metas as $k => $val) {
            $val = empty($val) ? "" : $val;
            if (is_array($val)) {
                $metaVals = serialize($val);
                $rr = update_post_meta($_POST['ID'], trim($k), trim($metaVals));
                if ($rr) {
                    $n++;
                }
            } else {
                $rr = update_post_meta($_POST['ID'], trim($k), trim($val));
                if ($rr) {
                    $n++;
                }
            }
        }
    }
    if (isset($_POST['slug']) && !empty($_POST['slug'])) {
        $slug = titleFilter($_POST['slug']);
        $data = array('post_name' => $slug);
        $res = $POSTS->post_up($data, "ID=$_POST[ID]");
    }
    if ($nMeta == $n) {
        echo "<p style='color:green'>Meta-Data Updated</p>";
    } else {
        echo "<p style='color:red'>Meta-Data not succesfully Update</p>";
    }

    exit;
}

function HText($hadding = 1) {
    global $POST, $TITLE, $QV, $TERM;
    $str = get_post_meta(@$POST['ID'], "meta_h" . $hadding . "_text");
    if (empty($str)) {
        //$str = $POST['post_title'];
        $str = $TITLE;
    }

    if (isset($QV['term']) && !empty($QV['term']) && empty($POST)) {
        $tt = $TERM->slug2term(trim($QV['term']));
        $str = isset($tt['meta']["meta_h1_text"]) ? $tt['meta']["meta_h1_text"] : $TITLE;
    }
    //var_dump($str);
    $str = filterSeoMeta($str);
    return $str;
}

function logoTitle() {
    global $POST, $TITLE, $QV, $TERM;
    $logoStr = get_option('sitelogo');
    if (!empty($logoStr)) {
        $idArray = explode(",", $logoStr);
        $logoData = get_post($idArray[0], 'guid');
        $imgSrc = get_attachment_src($logoData['guid']);
        $imgInfo = get_post_metas($idArray[0]);
        $Img_alt = $imgInfo['attachment_alter'];
        $Img_caption = $imgInfo['attachment_caption'];
    }
    $str = get_post_meta(@$POST['ID'], 'meta_logo_title');
    if (empty($str)) {
        if (!empty($Img_caption)) {
            $str = $Img_caption;
        } else {
            $str = $TITLE;
        }
    }
    if (isset($QV['term']) && !empty($QV['term'])) {
        $tt = $TERM->slug2term(trim($QV['term']));
        $str = isset($tt['meta']['meta_logo_title']) ? $tt['meta']['meta_logo_title'] : $str;
    }
    $str = filterSeoMeta($str);
    //var_dump($str);
    return $str;
}

add_metabox(array(
    'title' => "Keyword Density",
    'Description' => "",
    'position' => "side",
    'type' => "post,page",
    'calback' => 'key_density',
    'order' => 0
));

function key_density() {
    ?>
    <div class="mBoxBody">
        <input type="text" id="orangeSoda_search_phrase" class="form-control form-control-sm mB10" value="Keyword" style="color: gray" />
        <button class="btn btn-cms-default" id="orangeSoda_search_button" onclick="orangeSoda_click()" type="button">Search</button>
        <div id="orange_soda_search_density"></div>
        <div id="os_results"></div>
        <br />
        <div id="os_word_counter" style="display: none"></div>
        <div class="editor_text" style='display:none' ><?php echo strip_tags(get_content()); ?></div>
        <script>
            function updatedensity() {
                var id = $("#ID").val();
                get_return('ajx_action=stripTagContent&id=' + id, function(res) {
                    $(".editor_text").html(res);
                    //alert(res);
                    initKeyDen();
                });
            }
        </script>
    </div>
    <?php
}

function stripTagContent() {
    $content = get_content($_GET['id']);
    echo strip_tags($content);
}

if (!function_exists('titleLink')) {

    function titleLink() {
        global $TITLE, $POST;
        if (isset($POST) && !empty($POST)) {
            $url = get_link(@$POST['ID']);
            $h1txt = get_post_meta(@$POST['ID'], 'meta_h1_text'); //
            $h1txt = empty($h1txt) ? $TITLE : $h1txt;
            $htm = "<a href='$url' title='$h1txt'>$TITLE</a>";
            return $htm;
        }
    }

}

add_metabox(array(
    'title' => "SEO Audit",
    'Description' => "",
    'position' => "side",
    'type' => "post,page",
    'calback' => 'seo_audit_Mata_box',
    'order' => 1
));

function seo_audit_Mata_box() {
    ?>
    <div class="mBoxBody">
        <input type="text" id="audit_key" class="form-control form-control-sm mB10" placeholder="Keyword">
        <button type="button" class="btn btn-cms-default" onclick="auditAction()" id="auditSubmit">Submit</button>
        <div id='AuditResult'>

        </div>
    </div>
    <script>
        function auditAction() {
            var loader = "<span class='spinLoader'></span>";
            var loaderBig = "<span class='bodyLoader'></span>";

            $("#auditSubmit").after(loader);
            var url = "index.php"; // the script where you handle the form input.
            var fd = {ajx_action: 'ajax_request_callback_audit', key: $("#audit_key").val(), id: $("#ID").val()};
            jQuery.ajax({
                type: "POST",
                url: url,
                data: fd, // serializes the form's elements.
                success: function(data)
                {
                    //var obj = JSON.parse(data);
                    $("#auditSubmit").parent().find('.spinLoader').remove();
                    //$.fancybox.open(data);
                    $("#AuditResult").html(data);
                }

            });
        }
    </script>
    <?php
}

function ajax_request_callback_audit() {
    //global $DB;
    //print_r($_POST); 
    $densityscore = 0;
    $urlscore = 0;
    $titlescore = 0;
    $h1score = 0;
    $h2score = 0;
    $h3score = 0;
    $boldscore = 0;
    $emscore = 0;
    $imgscore = 0;
    $linkscore = 0;
    $wcountscore = 0;
    $descscore = 0;

    $post = get_post($_REQUEST['id']);
    //echo "<pre>";
    //var_dump($post);
    //echo "</pre>";
    $pageUrl = get_link($_REQUEST['id']);
    //$pageData = get_remote_data($pageUrl);

    $pageData = file_get_contents($pageUrl);
    //    var_dump($pageData);
    //    exit;
    //echo strlen($pageData);
    //exit;
    $url = get_option('site_url');
    //$strfilter      = apply_filters('the_content', $post->post_content);	//Content Only
    //==//$strfilter = apply_filters('the_content', $pageData);   //Full Page
    $strfilter = seo_auditFilter($pageData);
    $strfilter = strtolower($strfilter);
    //$titlefilter    = apply_filters('the_title', $post->post_title);
    //$titlefilter = get_post_title($post['ID']);
    $titlefilter = get_page_title($post['ID']);
    $titlefilter = strtolower($titlefilter);
    //var_dump($titlefilter);
    //$vsp_content    = strip_tags($post->post_content);					//Content Only
    $vsp_content = strip_tags($strfilter);       //Full Page								
    $vsp_content = strtolower($vsp_content);
    //$vsp_words = str_word_count(strtolower($vsp_content), 1);
    $vsp_words = str_word_count_utf8($vsp_content);
    $vsp_word_count = count($vsp_words);

    //var_dump($vsp_word_count);
    //var_dump($vsp_content);

    $res = array();
    $kwd = strtolower($_POST['key']);
    $kwd = trim($kwd);
    //$kwd            = strtolower(get_post_meta($post->ID, 'vsp_keyword', true));
    $kwd_count = preg_match_all("/\b$kwd\b/msiU", $vsp_content, $res);

    //var_dump(str_word_count_utf8($kwd));

    $kWc = count(str_word_count_utf8($kwd));
    if ($kWc > 1) {
        //79*(2/2680)*100
        $kwd_density = $kwd_count * ($kWc / $vsp_word_count) * 100;
    } else {
        $kwd_density = ($kwd_count / $vsp_word_count) * 100;
    }
    //var_dump($kwd_count,$vsp_word_count);
    //var_dump($vsp_word_count);
    //$kwd_density = ($kwd_count / $vsp_word_count) * 100;

    $kwdper = number_format($kwd_density, 2);

    // Check keyword density
    if ($kwdper < 1) {
        echo '<p class="vsp-bad">' . "Keyword Density" . ': ' . $kwdper . '% - <strong>' . "is too low!" . '</strong></p>';
    } elseif ($kwdper > 3) {
        echo '<p class="vsp-bad">' . "Keyword Density" . ': ' . $kwdper . '% - <strong>' . "is too high!" . '</strong></p>';
    } else {
        echo '<p class="vsp-ok">' . "Keyword Density" . ': ' . $kwdper . '%</p>';
    }
    // SEO score based on the keyword density
    if ($kwdper >= 1 && $kwdper <= 1.5) {
        $densityscore = 0.25;
    }
    if ($kwdper > 1.5 && $kwdper <= 2) {
        $densityscore = 0.27;
    }
    if ($kwdper > 2 && $kwdper <= 2.5) {
        $densityscore = 0.29;
    }
    if ($kwdper > 2.5 && $kwdper <= 3) {
        $densityscore = 0.31;
    }
    if ($kwdper > 3 && $kwdper <= 3.5) {
        $densityscore = 0.33;
    }
    if ($kwdper > 3.5 && $kwdper <= 4) {
        $densityscore = 0.35;
    }
    if ($kwdper > 4 && $kwdper <= 4.5) {
        $densityscore = 0.37;
    }
    if ($kwdper > 4.5 && $kwdper <= 5) {
        $densityscore = 0.39;
    }
    if ($kwdper > 5 && $kwdper <= 5.5) {
        $densityscore = 0.4;
    }
    if ($kwdper > 5.5 && $kwdper <= 6) {
        $densityscore = 0.1;
    }

    // Check if keyword exist in the URL
    $urlkwd = preg_match_all("/.*$kwd.*/i", $url, $res);
    if ($urlkwd > 0) {
        $urlscore = 0.03;
    }

    // Check if keyword exist in the title
    $customtitle = get_post_meta($post['ID'], 'vsp_title', true);
    //var_dump($customtitle);
    if ($customtitle) {
        $titlekwd = preg_match_all("/.*$kwd.*/i", $customtitle, $res);
    } else {
        $titlekwd = preg_match_all("/.*$kwd.*/i", $titlefilter, $res);
    }
    if ($titlekwd > 0) {
        echo '<p class="vsp-ok">' . "Found title containing main keyword" . '</p>';
        $titlescore = 0.12;
    } else {
        echo '<p class="vsp-bad">' . "Title not containing main keyword" . '</p>';
    }

    // Check if keyword exist in the custom description
    $customdesc = strtolower(get_post_meta($post['ID'], 'meta_description', true));
    //var_dump($customdesc);
    if ($customdesc) {
        $customdesckwd = preg_match_all("/.*$kwd.*/i", $customdesc, $res);
        if ($customdesckwd > 0) {
            echo '<p class="vsp-ok">' . "Description  containing main keyword" . '</p>';
        } else {
            echo '<p class="vsp-bad">' . "Description not containing main keyword" . '</p>';
            $descscore = -0.1;
        }
    }

    // Check if keyword exist in H1 tag
    $h1tags = preg_match_all("/(<h1.*>)(.*$kwd.*)(<\/h1>)/i", $pageData, $res);
    if ($h1tags > 0) {
        echo '<p class="vsp-ok">' . "Found H1 tag containing main keyword" . '</p>';
        $h1score = 0.1;
    } else {
        echo '<p class="vsp-bad">' . "No \"H1\" tag containing main keyword" . '</p>';
    }

    // Check if keyword exist in H2 tag
    $h2tags = preg_match_all("/(<h2.*>)(.*$kwd.*)(<\/h2>)/i", $pageData, $res);
    if ($h2tags > 0) {
        echo '<p class="vsp-ok">' . "Found H2 tag containing main keyword" . '</p>';
        $h2score = 0.08;
    } else {
        echo '<p class="vsp-bad">' . "No \"H2\" tag containing main keyword" . '</p>';
    }

    // Check if keyword exist in H3 tag
    $h3tags = preg_match_all("/(<h3.*>)(.*$kwd.*)(<\/h3>)/i", $pageData, $res);
    if ($h3tags > 0) {
        echo '<p class="vsp-ok">' . "Found H3 tag containing main keyword" . '</p>';
        $h3score = 0.04;
    } else {
        echo '<p class="vsp-bad">' . "No \"H3\" tag containing main keyword" . '</p>';
    }

    // Check if keyword exist in strong tag

    $bolddtags = preg_match_all("/(<strong.*>)(.*$kwd.*)(<\/strong>)/i", $pageData, $res);
    if (@$res[0][0] != "") {
        echo '<p class="vsp-ok">' . "Found \"strong\" <b>(b)</b> tag containing main keyword" . '</p>';
        $boldscore = 0.03;
    } else {
        echo '<p class="vsp-bad">' . "No \"strong\" <b>(b)</b> tag containing main keyword" . '</p>';
    }

    // Check if keyword exist in italic tag
    $emdtags = preg_match_all("/(<em.*>)(.*$kwd.*)(<\/em>)/i", $pageData, $res);
    if ($emdtags > 0) {
        echo '<p class="vsp-ok">' . "Found \"em\" <i>(i)</i> tag containing main keyword" . '</p>';
        $emscore = 0.03;
    } else {
        echo '<p class="vsp-bad">' . "No \"em\" <i>(i)</i> tag containing main keyword" . '</p>';
    }

    // Check if keyword exist in alt image tag
    //$imgtags = preg_match_all("/<img\s[^>]*alt=\"(.*$kwd.*)\"[^>]*>/i", $strfilter, $res);

    $imgtags = preg_match_all("/<img\s[^>]*alt=([\"']).*$kwd.*([\"'])[^>]*>/i", $pageData, $res);
    //var_dump($res);
    if ($imgtags > 0) {
        echo '<p class="vsp-ok">' . "Found \"alt\" image tag containing main keyword" . '</p>';
        $imgscore = 0.05;
    } else {
        echo '<p class="vsp-bad">' . "No \"alt\" image tag containing main keyword" . '</p>';
    }

    // Check if internal link exist
    $int_url = str_replace('http://www.', '', $url);
    $int_url = str_replace('http://', '', $url);
    $int_url = str_replace('/', '\/', $url);
    $intlink = preg_match_all("/<.*href=\".*$int_url.*\"[^>]*>(.*)<\/a>/isxmU", $pageData, $res);
    if ($intlink > 0) {
        echo '<p class="vsp-ok">' . "Found internal link" . '</p>';
        $linkscore = 0.02;
    } else {
        echo '<p class="vsp-bad">' . "No internal link can be found" . '</p>';
    }

    // count words
    if ($vsp_word_count > 199) {
        $wcountscore = 0.1;
    } else {
        echo '<p class="vsp-bad">' . "You should add more words to your content" . '</p>';
    }

    // Calculate the SEO rating score
    $score = ( @$densityscore + @$urlscore + $titlescore + $h1score + $h2score + $h3score + $boldscore + $emscore + $imgscore + $linkscore + $wcountscore + @$descscore ) * 100;

    // If main keyword is set, show the score
    if ($kwd) {
        if ($score < 60) {
            echo '<p><span style="color:red;font-size:14px;font-weight:bold;">' . "Overall Post SEO score" . ': ' . $score . '%</span></p>';
        } else {
            echo '<p><span style="color:green;font-size:14px;font-weight:bold;">' . "Overall Post SEO score" . ': ' . $score . '%</span></p>';
        }
    }
    // write your Php Code
    die;
}

function QuickEdit() {
    $post = array();
    $post['ID'] = isset($_POST['ID']) ? $_POST['ID'] : false;
    $sseo_meta_tag_robots = get_post_meta($post['ID'], 'robot');
    $pInfo = get_post($post['ID'], 'post_name');
    //var_dump($pInfo);
    ?>
    <div class="mBoxBody">
        <ul class="nav nav-tabs" id="myTab" role="tablist"> 
            <li class="nav-item">
                <a class="nav-link active" id="seoEditor-tab" data-toggle="tab" href="#seoEditor" role="tab" aria-controls="seoEditor" aria-selected="false">Editor</a>
            </li>
            <li class="nav-item">
                <a class="nav-link " id="SeoPreview-tab" data-toggle="tab" href="#SeoPreview" role="tab" aria-controls="SeoPreview" aria-selected="true">Preview</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <br>
            <div class="tab-pane fade show active" id="seoEditor" role="tabpanel" aria-labelledby="fileNdata-tab">
                <!--Separator-->
                <div class="keywordConsistency">
                    <button type="button" id="generateKeyConsistencyBtn" class="btn btn-cms-default" onclick='generateKeyConsistency(this, 5)' style='margin-bottom:10px'>Re-Generate</button>
                    <br>
                    <div id="consistencyContent"></div>
                </div>  
                <hr>
                <div class="seoRobot">
                    <label>Meta Robots : </label>
                    <select id="MetaRobot" class="custom-select custom-select-sm" name="meta[robot]" style="max-width:120px;">
                        <option value="none" <?php echo $sseo_meta_tag_robots == "none" ? "selected" : "" ?>>None</option>
                        <option value="index, all" <?php echo $sseo_meta_tag_robots == "index, all" ? "selected" : "" ?>>Index, All</option>	
                        <option value="index, follow" <?php echo $sseo_meta_tag_robots == "index, follow" ? "selected" : "" ?>>Index, follow</option>
                        <option value="index, nofollow" <?php echo $sseo_meta_tag_robots == "index, nofollow" ? "selected" : "" ?>>Index, no-follow</option>	
                        <option value="noindex, follow" <?php echo $sseo_meta_tag_robots == "noindex, follow" ? "selected" : "" ?>>No-index, follow</option>
                        <option value="noindex, nofollow"<?php echo $sseo_meta_tag_robots == "noindex, nofollow" ? "selected" : "" ?>>No-index, no-follow</option>
                        <option value="noodp, noydir"<?php echo $sseo_meta_tag_robots == "noodp, noydir" ? "selected" : "" ?>>Noodp, Noydir</option>
                    </select>
                </div>
                <button type="button" onclick="cleanKeyDes()" class="btn btn-cms-default float-right">Clean & update</button>
                <label class="CusMetaLabel">Title 
                    <div class="seoUpdMsg"></div>
                    <span class="seoCount"><span id="ttlCount"></span> / 60 char.</span></label>
                <input name="meta[meta_title]" id="Ctitle" onkeyup="serp_calc()" onchange="serp_calc()" value="<?php echo get_post_meta($post['ID'], 'meta_title') ?>" type="text" class="form-control form-control-sm">
                <label>Slug</label>
                <input id="slug" name="slug" value="<?php echo $pInfo['post_name']; ?>" onkeyup="serp_calc()" onchange="serp_calc()" type="text" class="form-control form-control-sm">
                <label class="CusMetaLabel">H1 Text <span class="seoCount"><span id="h1Count"></span> / 70 char.</span></label>
                <input id="h1Txt" name="meta[meta_h1_text]" value="<?php echo get_post_meta($post['ID'], 'meta_h1_text') ?>" onkeyup="serp_calc()" onchange="serp_calc()" type="text" class="form-control form-control-sm">
                <label>Article Heading</label>
                <input id="articleH" name="meta[meta_harticle_text]" value="<?php echo get_post_meta($post['ID'], 'meta_harticle_text') ?>" onkeyup="serp_calc()" onchange="serp_calc()" type="text" class="form-control form-control-sm">

                <label>Logo Title</label>
                <input id="meta_logo_title" name="meta[meta_logo_title]" value="<?php echo get_post_meta(@$POST['ID'], 'meta_logo_title') ?>" type="text" class="form-control form-control-sm">

                <!--        <label>H2 Text</label>
                    <input name="meta[meta_h2_text]" value="<?php // echo get_post_meta($POST['ID'], 'meta_h2_text')                                                                                                                                                                                                                                                                                                                                                                                                    ?>" type="text" class="form-control form-control-sm">
                    <label>H3 Text</label>
                    <input name="meta[meta_h3_text]" value="<?php // echo get_post_meta($POST['ID'], 'meta_h3_text')                                                                                                                                                                                                                                                                                                                                                                                                    ?>" type="text" class="form-control form-control-sm">
                <hr>-->
                <label  class="CusMetaLabel">Meta Description <span id="massDes" class="comment m0"></span> <span class="seoCount"><span id="descCount"></span> / 155 char.</span></label>
                <textarea name="meta[meta_description]" id="metaD" onkeyup="serp_calc()" onchange="serp_calc()" class="form-control"><?php echo get_post_meta($post['ID'], 'meta_description') ?></textarea>

                <label  class="CusMetaLabel">Meta Keyword <span id="massKey" class="comment m0"></span> <span class="seoCount"><span id="keyCount"></span> / 10 Phrase</span></label>
                <textarea name="meta[meta_keyword]"  id="metaK" onkeyup="serp_calc()" onchange="serp_calc()" class="form-control"><?php echo get_post_meta($post['ID'], 'meta_keyword') ?></textarea>
                <input id="ID" type="hidden" value="<?php echo $post['ID'] ?>">
            </div>
            <div class="tab-pane fade " id="SeoPreview" role="tabpanel" aria-labelledby="statiSite-tab">
                <div class="seoViewerArea">
                    <a href='#' class='seoPrvshow' style="display:none" onclick='$(".SEOpreviewer").show();
                                $(this).hide()'>Show</a>
                    <div class="SEOpreviewer">
                        <a href='#' class='seoPrvClose' onclick='$(this).parent().hide();
                                    $(".seoPrvshow").show()'>×</a>
                        <div class="inside serpOUT">
                            <h3 id="Otitle">Title</h3>
                            <p id="Ourl" class='url'>
                                <a target="_blank" href="<?php echo get_link($post['ID']); ?>"><?php echo get_link($post['ID']); ?></a>
                            </p>
                            <p id="OmD" class='metaDes'></p>
                            <p id="Omk" class='metaKey'></p>
                            <a href="https://validator.w3.org/nu/?showsource=yes&doc=<?php echo get_link($post['ID']); ?>" target="_blank" class="btn btn-cms-primary btn-sm">Validator</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function generateKeyConsistency() {
    $showItem = isset($_POST['num']) && !empty($_POST['num']) ? $_POST['num'] : 5;
    $link = get_link($_POST['ID']);
    $PageData = strtolower(file_get_contents_curl($link));

    $re = '@(<title>)(.*)(</title>)@m';
    preg_match_all($re, $PageData, $matches, PREG_SET_ORDER, 0);
    $metaTitle = strtolower(@$matches[0][2]);
    $dom = new DOMDocument();
    @$dom->loadHTML($PageData);

    $metaKeword = "";
    $metaDescription = "";
    foreach (@$dom->getElementsByTagName('meta') as $element) {
        if ($element->getAttribute('name') == "description") {
            $metaDescription = $element->attributes->getNamedItem("content")->nodeValue;
        }
        if ($element->getAttribute('name') == "keywords") {
            $metaKeword = $element->attributes->getNamedItem("content")->nodeValue;
        }
    }
    //var_dump($metaTitle,$metaDescription,$metaKeword);
    // $re = '@(<body[^>]*>)((.|\n)*)(</body>)@m';
    // preg_match($re, $PageData, $matches);
    // var_dump($matches);

    $bodyContent = "";
    $body = $dom->getElementsByTagName('body');
    if ($body && 0 < $body->length) {
        $body = $body->item(0);
        $bodyContent = $dom->savehtml($body);
    }


    //=== Remove Script by DOM
    $domRemScript = new DOMDocument();
    @$domRemScript->loadHTML(@$bodyContent);
    $script = $domRemScript->getElementsByTagName('script');
    $remove = array();
    foreach ($script as $item) {
        $remove[] = $item;
    }

    foreach ($remove as $item) {
        $item->parentNode->removeChild($item);
    }
    $bodyContent = $domRemScript->saveHTML();
    //---

    $bodyWhTag = str_replace("&nbsp;", " ", $bodyContent);


    $htagArray = array();
    $re = '@(<h1>)(.*)(</h1>)@m'; //to find H tags
    preg_match_all($re, $bodyWhTag, $matches, PREG_SET_ORDER, 0);
    foreach ($matches as $htag) {
        $htagArray[] = $htag[2];
    }

    //=== Remove <a> by DOM
    $domRem_a = new DOMDocument();
    @$domRem_a->loadHTML($bodyWhTag);
    $aLink = $domRem_a->getElementsByTagName('a');
    $remove = array();
    foreach ($aLink as $link) {
        $remove[] = $link;
    }

    foreach ($remove as $item) {
        $item->parentNode->removeChild($item);
    }
    $bodyWhTag = $domRem_a->saveHTML();
    //---
    //Remove H tags
    $re = '@(<h[\d]{1,6}>)(.*)(</h[\d]{1,6}>)@m'; //to find H tags
    // $re = '@(<h1>)(.*)(</h1>)@m'; //to find H tags
    preg_match_all($re, $bodyWhTag, $matches, PREG_SET_ORDER, 0);
    foreach ($matches as $htag) {
        $bodyWhTag = str_replace($htag, "", $bodyWhTag);
        // $htagArray[] = $htag[2];
    }

    //var_dump($bodyWhTag);
    //var_dump($htagArray);

    $SolidText = strtolower(trim(strip_tags($bodyWhTag)));
    //$SolidText = preg_replace("/(?![.=$'€%-])\p{P}/u", "", $SolidText);
    $unwantedChars = array(',', '!', '?', '.', '+', ';', ':', '`', '"', '\''); // create array with unwanted chars
    $SolidText = str_replace($unwantedChars, '', $SolidText); // remove them
    $SolidText = str_replace(" +", " ", $SolidText);


    //var_dump($SolidText);
    $wordArray = preg_split("/\s+/", $SolidText);
    $wordArray = skipWords($wordArray);
    $counter = array_count_values($wordArray);
    arsort($counter);
    $totalWords = count($wordArray);

    //var_dump($htagArray);

    $htm = "<table class='table  table-responsive-lg table-cms'>
	<tr>
	<th>Keyword</th>
	<th>Content</th>
	<th>Title</th>
	<th>Keywords</th>
	<th>Description</th>
	<th>Heading</th>
	</tr> 
	";


    $n = 0;
    // var_dump($metaTitle);
    foreach ($counter as $w => $t) {
        $n++;

        $htm.="<tr>
		<td>$w</td>
		<td>$t</td>";

        $htm.=" <td>";
        if (strpos($metaTitle, $w) !== false) {
            $htm.=" <span class='vsp-ok'></span>";
        } else {
            $htm.=" <span class='vsp-bad'></span>";
        }
        $htm.=" </td>";

        $htm.=" <td>";
        if (strpos($metaKeword, $w) !== false) {
            $htm.=" <span class='vsp-ok'></span>";
        } else {
            $htm.=" <span class='vsp-bad'></span>";
        }
        $htm.=" </td>";

        $htm.=" <td>";
        if (strpos($metaDescription, $w) !== false) {
            $htm.=" <span class='vsp-ok'></span>";
        } else {
            $htm.=" <span class='vsp-bad'></span>";
        }
        $htm.=" </td>";

        $htm.=" <td>";
        $hExist = false;


        foreach ($htagArray as $h) {
            //var_dump(strpos($h, $w));
            //var_dump($h,$w);
            if (strpos($h, $w) !== false) {
                $hExist = true;
                break;
            }
        }
        if ($hExist) {
            $htm.=" <span class='vsp-ok'></span>";
        } else {
            $htm.=" <span class='vsp-bad'></span>";
        }
        $htm.="</td>";

        $htm.="</tr>";

        if ($n == $showItem) {
            break;
        }
    }
    $htm.="</table>";
    if ($showItem == 10) {
        $htm.=" <button type=\"button\" id='moreBtn' class=\"btn btn-cms-default\" onclick='generateKeyConsistency(this,5)'>Less</button>";
    } else {
        $htm.=" <button type=\"button\" id='moreBtn' class=\"btn btn-cms-default\" onclick='generateKeyConsistency(this,10)'>More</button>";
    }
    echo $htm;
    // var_dump($counter);
}

function skipWords($words) {
    $skipwords = array(
        'about' => true,
        'We' => true,
        'after' => true,
        'ago' => true,
        'all' => true,
        'also' => true,
        'an' => true,
        'and' => true,
        'any' => true,
        'are' => true,
        'as' => true,
        'at' => true,
        'be' => true,
        'been' => true,
        'before' => true,
        'both' => true,
        'but' => true,
        'by' => true,
        'can' => true,
        'did' => true,
        'do' => true,
        'does' => true,
        'done' => true,
        'edit' => true,
        'even' => true,
        'every' => true,
        'for' => true,
        'from' => true,
        'had' => true,
        'has' => true,
        'have' => true,
        'he' => true,
        'here' => true,
        'him' => true,
        'his' => true,
        'however' => true,
        'if' => true,
        'in' => true,
        'into' => true,
        'is' => true,
        'it' => true,
        'its' => true,
        'less' => true,
        'many' => true,
        'may' => true,
        'more' => true,
        'most' => true,
        'much' => true,
        'my' => true,
        'no' => true,
        'not' => true,
        'often' => true,
        'quote' => true,
        'of' => true,
        'on' => true,
        'one' => true,
        'only' => true,
        'or' => true,
        'other' => true,
        'our' => true,
        'out' => true,
        're' => true,
        'says' => true,
        'she' => true,
        'so' => true,
        'some' => true,
        'soon' => true,
        'such' => true,
        'than' => true,
        'that' => true,
        'the' => true,
        'their' => true,
        'them' => true,
        'then' => true,
        'there' => true,
        'these' => true,
        'they' => true,
        'this' => true,
        'those' => true,
        'though' => true,
        'through' => true,
        'to' => true,
        'under' => true,
        'use' => true,
        'using' => true,
        've' => true,
        'was' => true,
        'we' => true,
        'were' => true,
        'what' => true,
        'where' => true,
        'when' => true,
        'whether' => true,
        'which' => true,
        'while' => true,
        'who' => true,
        'whom' => true,
        'with' => true,
        'within' => true,
        'you' => true,
        'your' => true,
        'http' => true,
        'www' => true,
        'wp' => true,
        'href' => true,
        'target' => true,
        'blank' => true,
        'image' => true,
        'class' => true,
        'size' => true,
        'src' => true,
        'img' => true,
        'alignleft' => true,
        'title' => true,
        'info' => true,
        'content' => true,
        'uploads' => true,
        'jpg' => true,
        'alt' => true,
        'h3' => true,
        'width' => true,
        'height' => true,
        '150' => true,
        '2010' => true,
        '2009' => true,
        '10' => true,
        '1' => true,
        '2' => true,
        '3' => true,
        '4' => true,
        '5' => true,
        '6' => true,
        '7' => true,
        '8' => true,
        '9' => true,
        '11' => true,
        'com' => true,
        'net' => true,
        'info' => true,
        'map' => true,
        '150x150' => true,
        'thumbnail' => true,
        'param' => true,
        'name' => true,
        'value' => true,
        'will' => true,
        'am' => true,
        '202' => true,
        'retouch' => true,
        '&amp;' => true,
        'amp' => true,
        'like' => true,
        'etc.' => true,
        'nbsp' => true,
        'â' => true,
    );


    $nwArr = array();
    foreach ($words as $w) {
        if (!array_key_exists($w, $skipwords) && strlen($w) > 3) {
            $nwArr[] = $w;
        }
    }
    return $nwArr;
}

function slugKeylookup() {
    $id = $_POST['ID'];
    $slug = get_post($id, 'post_name');
    $slug = $slug['post_name'];
    $phCheck = isset($_POST['phCheck']) ? $_POST['phCheck'] : '';
    $metatitle = get_post_meta($id, 'meta_title');
    $cstr = "";
    if (isset($_POST['cstr']) && !empty($_POST['cstr'])) {
        $cstr = $_POST['cstr'];
        $metatitle = $cstr;
    }
    $unwantedChars = array('!', '?', '.', '+', ';', ':', '`', '"', '\''); // create array with unwanted spcail chars
    $metatitle = strtolower(str_replace($unwantedChars, '', $metatitle)); // remove them
    $metaDescription = strtolower(get_post_meta($id, 'meta_description'));
    $metaKeyword = strtolower(get_post_meta($id, 'meta_keyword'));

    //var_dump($metaKeyword, $metaDescription, $slug);
    $chkPhr = '';
    $lbl = "Key";
    if ($phCheck == 'true' && $cstr != "") {
        $wordArray = explode(",", $metatitle);
        $chkPhr = "checked";
        $lbl = "Phrase";
    } else {
        $metatitle = str_replace(",", "", $metatitle);
        $wordArray = preg_split("/\s+/", $metatitle); //if not phrase
    }

    $wordArray = array_unique(skipWords($wordArray));

    $html = "<div class=''>
	<input id='customQueryString' type='text' value='$cstr' placeholder='Custom String' class='form-control form-control-sm'> 
	<button class='btn btn-cms-default' onclick='slugKeylookupCustom(this)'>Check</button>&nbsp;&nbsp;<input type='checkbox' id='phraseCheck' $chkPhr value='true'><label for='phraseCheck'>&nbsp;Phrase</label>
	</div><br>";
    $html.= "<div id='ExistingReport'>
	<table class='table  table-responsive-lg table-cms'>
	<tr>
	<th>$lbl</th>
	<th>Url</th>
	<th>Description</th>
	<th>Keyword</th>
	</tr> 
	";
    foreach ($wordArray as $word) {
        $html.="<tr>
		<td>$word</td>
		<td>";
        if (strpos($slug, $word) !== false) {
            $html.=" <span class='vsp-ok'></span>";
        } else {
            $html.=" <span class='vsp-bad'></span>";
        }
        $html.="</td>
		<td>";
        if (strpos($metaDescription, $word) !== false) {
            $html.=" <span class='vsp-ok'></span>";
        } else {
            $html.=" <span class='vsp-bad'></span>";
        }
        $html.="</td>
		<td>";
        if (strpos($metaKeyword, $word) !== false) {
            $html.=" <span class='vsp-ok'></span>";
        } else {
            $html.=" <span class='vsp-bad'></span>";
        }
        $html.=" </td>
		</tr>";
    }
    $html.="</table></div>";
    echo $html;
}

function seo_auditFilter($str) {
    //var_dump($str);
    $str = preg_replace(
            array(
        // Remove invisible content
        '@<head[^>]*? >.*?</head>@siu',
        '@<style[^>]*?>.*?</style>@siu',
        '@<script[^>]*?.*?</script>@siu',
        '@<object[^>]*?.*?</object>@siu',
        '@<embed[^>]*?.*?</embed>@siu',
        '@<applet[^>]*?.*?</applet>@siu',
        '@<noframes[^>]*?.*?</noframes>@siu',
        '@<noscript[^>]*?.*?</noscript>@siu',
        '@<noembed[^>]*?.*?</noembed>@siu',
        // Add line breaks before and after blocks
        '@</?((address)|(blockquote)|(center)|(del))@iu',
        '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
        '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
        '@</?((table)|(th)|(td)|(caption))@iu',
        '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
        '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
        '@</?((frameset)|(frame)|(iframe))@iu',
            ), array(
        ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
        "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
        "\n\$0", "\n\$0",
            ), $str);
    //var_dump($str);
    return $str;
}

function str_word_count_utf8($str) {
    $newArr = array();
    $wrds = preg_split('~[^\p{L}\p{N}\']+~u', $str);
    foreach ($wrds as $w) {
        if (strlen($w) > 1 || $w == 'a') {
            $newArr[] = $w;
        }
    }
    return $newArr;
}

function h1Text() {
    global $QV, $POST, $TERM;

    $termTT = "";
    if (isset($QV['product_slug'])) {
        $productID = slug2id($QV['product_slug']);
        $termTT = get_post_meta($productID, "meta_h1_text");
        if (empty($termTT)) {
            $termTT = get_post_title($productID);
        }
    } elseif (isset($QV['product_category'])) {
        $term_id = term_slug2Id($QV['product_category']);
        if ($term_id) {
            $term = $TERM->get_term($term_id);
            //var_dump($term);
            $termTT = $term['meta']['customTitle'];
        }
    }
    if (empty($termTT)) {
        $termTT = get_post_meta($POST['ID'], "meta_h1_text");
    }
    if (empty($termTT)) {
        $termTT = get_post_title($POST['ID']);
    }

    $termTT = filterSeoMeta($termTT);
    return $termTT;
}
