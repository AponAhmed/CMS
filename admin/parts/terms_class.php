<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of terms
 *
 * @author nrb
 */
class terms {

    //put your code here
    var $currentTerms;
    var $data;
    public $currPage = 1;

    public function posts($arg = array()) {
        $tempArg = $arg;
        global $DB, $QV;
        if (isset($QV['page_no']))
            $this->currPage = $QV['page_no'];
        $this->data = $DB;
        $cat = '';
        if (isset($GLOBALS['term'])) {
            $this->currentTerms = $GLOBALS['term'];
        }

        $getCategoryPageOption = get_option('categoryPage');
        if ($getCategoryPageOption != "") {
            $arg = unserialize($getCategoryPageOption);
            if (is_array($arg)) {
                $arg = array_filter($arg);
            }
        }
        $arg = array_merge($tempArg, $arg);

        if (!empty($this->currentTerms) || (isset($arg['cat']) && !empty($arg['cat']))) {
            if (isset($arg['cat']) && !empty($arg['cat'])) {
                $cat = $arg['cat'];
            } else {
                $cat = $this->currentTerms['term_id'];
            }
            $atts = array(
                'cat' => $cat,
                'column' => '4',
                'post_per_page' => '12',
                'img' => 'yes',
                'excerpt' => '20',
                'pagination' => 'yes',
                'orderby' => 'ID',
                'order' => 'DESC',
                'link' => 'true',
                'icon' => '',
                'c-class' => '',
                'get-price' => 'no',
                'tcol' => '3',
                'mcol' => '2'
            );

            $arg = array_merge($atts, $arg);

            $wh = " post_status='published'";
            $limitQry = "";
            if ($arg['post_per_page'] > 1) {
                $offSet = ($this->currPage - 1) * $arg['post_per_page'];
                $limitQry = "limit $offSet , $arg[post_per_page]";
            }
            $arg = array_merge($atts, $arg);

            $order = $arg['order'];
            if ($order == 'rand') {
                $order = "RAND()";
                $arg['orderby'] = "";
            }

            $wh = " post_status='published'";
            $limitQry = "";
            if ($arg['post_per_page'] > 1) {
                $offSet = ($this->currPage - 1) * $arg['post_per_page'];
                $limitQry = "limit $offSet , $arg[post_per_page]";
            }

            $html = "";
            if (!empty($arg['cat'])) {
                $texoID = trim($arg['cat']);
                $txoCon = "texo_id=$texoID and ";
                //$sql=""           
                $posts = $this->data->select("term_relationships as tr left join post as p on tr.object_id=p.ID", 'ID,post_content,post_title', $txoCon . $wh . " ORDER by $arg[orderby] $order $limitQry");

                //get_posts();
                $html.="<div class=\"row posts-wrap\">";
                $clm = 12 / $arg['column'];
                $colClass = " col w$clm";
                if ($clm == 12) {
                    $colClass.= " singleCol";
                }

                if (!empty($arg['c-class'])) {
                    $colClass.=" " . $arg['c-class'];
                }

                if (!empty($arg['tcol'])) {
                    $tcol = 12 / $arg['tcol'];
                    $colClass.=" t" . $tcol;
                }
                if (!empty($arg['mcol'])) {
                    $mcol = 12 / $arg['mcol'];
                    $colClass.=" m" . $mcol;
                }

                foreach ($posts as $post) {
                    //$html.=$post['ID'];
                    $html.=$this->postView($post, "post-single $colClass", $arg);
                }

                $html.="</div>";

                $c = $this->data->select("term_relationships as tr left join post as p on tr.object_id=p.ID", 'COUNT(ID) as totalPost', $txoCon . $wh . " ORDER by $arg[orderby] $order");
                $html.=$this->pagination($arg, $c);
                return $html;
            }
        }
    }

    private function postView($post, $class, $att) {
        ob_start();
        if (is_numeric($att['excerpt'])) {
            if ($att['excerpt'] > 0) {
                $strContent = strip_tags(shortcode_exe($post['post_content']));
                $content = getExcerpt($strContent, 0, $att['excerpt']);
                $otContent = getExcerpt($strContent, $att['excerpt'], (str_word_count($strContent) - $att['excerpt']));
                $content.="<span class='collapse'>$otContent</span>";
            } elseif ($att['excerpt'] == 0) {
                $strContent = strip_tags(shortcode_exe($post['post_content']));
                $content = "<div class='collapse'>$strContent</div>";
            }
        } elseif ($att['excerpt'] == 'no') {
            $content = shortcode_exe($post['post_content']);
        }

        if ($att['icon'] != "") {
            $att['img'] = 'no';
            $icon = "<div class='icon-wrap'><i class='" . get_featureIcon($post['ID']) . "'></i></div>";
            $class.=" has-icon $att[icon]";
        }

        $link = get_link($post['ID']);

        $thumb = "";
        if ($att['img'] == 'yes') {
            $ides = explode(",", get_post_meta($post['ID'], 'feature_image'));
            $imgArray = array_filter($ides);
            if (empty($imgArray)) {
                $thumb = "<span class='post-imageBg'></span>";
            } else {
                if ($att['column'] == 1 && count($imgArray) > 1) {
                    $thumb.="<div class='seoSinglePostSliderWrap'><div class='seoSinglePostThumb'>";
                    $imgN = 0;
                    foreach ($imgArray as $imgID) {
                        $imgN++;
                        $imguid = get_post($imgID, 'guid');
                        $imgSrc = get_attachment_src($imguid['guid'], 698);
                        $Img_alt = get_post_meta($imgArray[0], 'attachment_alter');
                        $Img_caption = get_post_meta($imgArray[0], 'attachment_caption');
                        $Imginfo = get_attachment_src_set($imguid['guid']); //Src ser and sizes 
                        $size = imgSizes($imguid['guid']);
                        $first = "";
                        if ($imgN == 1) {
                            $first = 'onView';
                        }
                        if ($att['link'] == 'true') {
                            $thumb.= "<div class='singlePost-singleImage $first'><a href='$link'><img title='$Img_caption' width='$size[0]' sizes='$Imginfo[sizes]' height='$size[1]' srcset='$Imginfo[srcset]' alt='$Img_alt' src='$imgSrc'  id='obj_img_$imgArray[0]' class='img-fluid'></a></div> ";
                        } else {
                            $thumb.= "<div class='singlePost-singleImage $first'><img title='$Img_caption' width='$size[0]' sizes='$Imginfo[sizes]' height='$size[1]' srcset='$Imginfo[srcset]' alt='$Img_alt' src='$imgSrc'  id='obj_img_$imgArray[0]' class='img-fluid'></div> ";
                        }
                    }
                    $thumb.="</div></div>";
                    $att['link'] = 'false';
                } else {
                    shuffle($imgArray);
                    $imguid = get_post($imgArray[0], "guid");
                    $imgSrc = get_attachment_src($imguid['guid'], 698);
                    $Img_alt = get_post_meta($imgArray[0], 'attachment_alter');
                    $Img_caption = get_post_meta($imgArray[0], 'attachment_caption');
                    $Imginfo = get_attachment_src_set($imguid['guid']); //Src ser and sizes 
                    $size = imgSizes($imguid['guid']);
                    $thumb = "<img title='$Img_caption' width='$size[0]' sizes='$Imginfo[sizes]' height='$size[1]' srcset='$Imginfo[srcset]' alt='$Img_alt' src='$imgSrc'  id='obj_img_$imgArray[0]' class='img-fluid'>";
                }
            }
        }

        $class.="";
        ?>
        <article class="post-wrap <?php echo $class; ?>">
            <?php
            if ($att['icon'] == 'top-center' || $att['icon'] == 'top-left' || $att['icon'] == 'top-right' || $att['icon'] == 'left') {
                echo $icon;
            }

            if ($att['img'] == 'yes') {
                ?>
                <div class="post-thumbnail">
                    <?php if ($att['link'] == 'true') { ?>
                        <a href="<?php echo $link ?>" class="post-image"><?php echo $thumb ?></a>
                        <?php
                    } else {
                        echo $thumb;
                    }
                    ?>
                </div>
            <?php } ?>

            <div class="post-content">
                <header class="post-header">
                    <?php
                    if ($att['icon'] == 'center-title') {
                        echo $icon;
                    }
                    ?>
                    <h3 class="post-title">

                        <?php if ($att['link'] == 'true') { ?>
                            <a href="<?php echo $link ?>"><?php echo $post['post_title'] ?></a>
                            <?php
                        } else {
                            echo $post['post_title'];
                        }
                        ?>
                    </h3>
                </header>
                <p><?php echo $content; ?></p>
                <?php
                if ($att['get-price'] == 'yes') {
                    ?> <div class='plink' data-plink="<?php echo $link ?>"> <button class="btn btn-default getPrice" onclick="add2Cart(<?php echo $post['ID'] ?>, this)" title="Add To get Quote list" >Get Quote</button></div><?php
                }
                ?>
            </div>
        </article>
        <?php
        return ob_get_clean();
    }

    public function pagination($arg, $c) {
        $link = get_term_link($this->currentTerms);
        $numberOfpage = ceil($c[0]['totalPost'] / $arg['post_per_page']);
        if ($arg['pagination'] == "yes") {
            $paginationHtml = "<div class='pagination-wrap'>";
            $paginationHtml.="<ul>";
            if ($this->currPage > 1) {
                $prev = $this->currPage - 1;
                $paginationHtml.="<li><a href='{$link}page/$prev/'>Prev</a></li>";
            }
            for ($i = 1; $i <= $numberOfpage; $i++) {
                $act = "";
                if ($this->currPage == $i)
                    $act = "current-link";
                if ($i == 1) {
                    $paginationHtml.="<li class='$act'><a href='$link'>$i</a></li>";
                } else {
                    $paginationHtml.="<li class='$act'><a href='{$link}page/$i/'>$i</a></li>";
                }
            }
            if ($this->currPage < $numberOfpage) {
                $next = $this->currPage + 1;
                $paginationHtml.="<li><a href='{$link}page/$next/'>Next</a></li>";
            }



            $paginationHtml.="</ul>";
            $paginationHtml.="</div>";
            return $paginationHtml;
        }
    }

    public function add_term_relation($obj, $texo) {
        global $DB;
        $data = array();
        $data['object_id'] = $obj;
        $data['texo_id'] = $texo;
        $res = $DB->insert("term_relationships", $data);
        return $res;
    }

    public function get_relations($post_id) {
        global $DB;
        $ides = array();
        $res = $DB->select("term_relationships", "texo_id", "object_id=$post_id");
        if ($res) {
            foreach ($res as $texID) {
                $ides[] = $texID['texo_id'];
            }
        }
        return $ides;
    }

    public function slug2texoID($slug, $name = false) {
        global $DB;
        $term = $DB->select("terms as trm left join term_taxonomy as tx on trm.term_id=tx.term_id", "tx.taxonomy_id,trm.name", "trm.slug='$slug'");
        if (!empty($term)) {
            //var_dump($term);
            if ($name) {
                //var_dump($term);
                return $term[0]['name'];
            }
            return $term[0]['taxonomy_id'];
        }
    }

    public function slug2term($slug, $name = false) {
        global $DB;
        $term = $DB->select("terms as trm left join term_taxonomy as tx on trm.term_id=tx.term_id", "trm.term_id", "BINARY trm.slug='$slug'");
        $term = $term[0];
        return $this->get_term($term['term_id']);
    }

    public function get_term($termId, $texoID = false) {
        //var_dump($termId);
        global $DB;
        $txCon = "trm.term_id=$termId";
        if ($texoID) {
            $txCon = "tx.taxonomy_id=$termId";
        }
        $term = $DB->select("term_taxonomy as tx left join terms as trm on tx.term_id=trm.term_id", "*", "$txCon");
//$term = $DB->select("SELECT p . * , GROUP_CONCAT( pm.meta_key
//ORDER BY pm.meta_key DESC
//SEPARATOR '||') AS meta_keys, GROUP_CONCAT(pm.meta_value
//ORDER BY pm.meta_key DESC
//SEPARATOR '||') AS meta_values
//FROM term_taxonomy p
//LEFT JOIN termmeta pm ON pm.term_id = p.term_id
//WHERE p.term_id=$termId");
        $termMeta = $DB->select('termmeta', 'meta_key,meta_value', "term_id=$termId");
        $term = isset($term[0]) ? $term[0] : array();
        $custom = array();
        if (!empty($termMeta)) {
            foreach ($termMeta as $csMeta) {
                if (@unserialize($csMeta['meta_value'])) {
                    $csMeta['meta_value'] = unserialize($csMeta['meta_value']);
                }
                $custom[$csMeta['meta_key']] = $csMeta['meta_value'];
            }
            $term['meta'] = $custom;
        }
        //var_dump($term);
        return $term;
    }

    public function add_term($name, $slug, $group = "") {
        global $DB;
        $data = array();
        $data['name'] = $name;
        $data['slug'] = $slug;
        $data['term_group'] = $group;
        $res = $DB->insert("terms", $data);
        if (!$res) {
            return $DB->error;
        } else {
            return $DB->insert_id;
        }
        return $res;
    }

    public function add_texo($trmID, $texo, $description = "") {
        global $DB;
        $data = array();
        $data['taxonomy'] = $texo;
        $data['term_id'] = $trmID;
        $data['description'] = $description;
        $res = $DB->insert("term_taxonomy", $data);
        if (!$res) {
            return $DB->error;
        } else {
            return $DB->insert_id;
        }
        return $res;
    }

    public function delTexo($texoID) {
        global $DB;
        $termID = $DB->select("term_taxonomy", "term_id", "taxonomy_id=$texoID");
        $termID = $termID[0]['term_id'];
        $DB->delete("terms", "term_id=$termID");
        $DB->delete("term_taxonomy", "taxonomy_id=$texoID");
        $DB->delete("termmeta", "term_id=$termID");

        $TRel = $DB->select("term_relationships", "object_id", "texo_id=$texoID");
        foreach ($TRel as $p) {
            $DB->delete("post", "ID=$p[object_id]");
            $DB->delete("`post-meta`", "post_id=$p[object_id]");
        }
        $DB->delete("term_relationships", "texo_id=$texoID");
    }

    public function delTerm($termID) {
        global $DB;
    }

    public function texoListRow($texo, $parent = false, $ides = false) {
        global $DB;
        $idC = "";
        if ($ides) {
            $ides = implode("','", $ides);
            $idC = " and taxonomy_id in('$ides')";
        }
        //var_dump($parent);
        if ($parent) {
            if ($parent !== true) {
                $rows = $DB->select("term_taxonomy as tx left join terms as trm on tx.term_id=trm.term_id", "*", "tx.taxonomy='$texo' and trm.term_group=$parent $idC");
            } else {
                $rows = $DB->select("term_taxonomy as tx left join terms as trm on tx.term_id=trm.term_id", "*", "tx.taxonomy='$texo' and trm.term_group=0 $idC");
            }
        } else {
            $rows = $DB->select("term_taxonomy as tx left join terms as trm on tx.term_id=trm.term_id", "*", "tx.taxonomy='$texo' $idC");
        }
        return $rows;
    }

    public function texoList($texo) {
        //echo "List Table class not found for $texo";
        global $DB;
        if (isset($_REQUEST['term_ipp'])) {
            $_SESSION['term_ipp'] = $_REQUEST['term_ipp'];
        }
        $ipp = isset($_SESSION['term_ipp']) ? $_SESSION['term_ipp'] : 15;
        ?>
        <div class="row">
            <div class='shortNav col-sm-9'>&nbsp;
        <!--                <a class="<?php // echo $status == "published" ? "active" : ""                                                                                                                                                            ?>" onclick='load_list("st=published")' href="javascript:">Published</a> | 
                <a class="<?php //echo $status == "trash" ? "active" : ""                                                                                                                                                            ?>" onclick='load_list("st=trash")' href="javascript:">Trashed</a> | 
                <a class="<?php //echo $status == "draft" ? "active" : ""                                                                                                                                                            ?>" onclick='load_list("st=draft")' href="javascript:">Drafts</a>
                --> </div>
            <div class="col-sm-3 float-right">
                <div class="searchBox">
                    <input  placeholder='Search' onchange="searchPost(this);" value="<?php echo!empty($q) ? $q : '' ?>" class="searchIn form-control form-control-sm" type="text">
                    <?php
                    if (!empty($q)) {
                        ?><span class="searchCancel" onclick="searchCancel(this)">×</span><?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <table class="table table-striped table-responsive-lg table-cms">
            <tr>
                <th width='50'><input type="checkbox" class=""></th>
                <th>#</th>
                <th >Name</th>
                <th>Description</th>
                <th>Slug</th> 
                <th></th>
            </tr>
            <?php
            $rows = $DB->paginate("term_taxonomy as tx left join terms as trm on tx.term_id=trm.term_id", "*", "tx.taxonomy='$texo'  and trm.term_group=0  ORDER BY tx.taxonomy_id DESC ", $ipp);
            foreach ($rows as $row) {
                //var_dump($row);
                //Child==========
                $Childrows = $DB->select("term_taxonomy as tx left join terms as trm on tx.term_id=trm.term_id", "*", "tx.taxonomy='$texo' and trm.term_group=$row[taxonomy_id] ORDER BY tx.taxonomy_id DESC");
                $is_Child = count($Childrows) > 0 ? true : false;
                ?>
                <tr class='<?php echo $is_Child ? "has_child" : "" ?>'>
                    <td>
                        <input type="checkbox">
                        <?php
                        if ($is_Child) {
                            echo "<button dataID='#childs_$row[term_id]' type='button' class='texoChildTrig' onclick='openTexoChild(this)'><span></span></button>";
                        }
                        ?>
                    </td>
                    <td><?php
                        echo $row['term_id']
                        ?></td>
                    <td width='200'><?php
                        echo $row['name']
                        ?></td>
                    <td><?php echo $this->descriptionInList($row['description']) ?></td>
                    <td  width='250'><?php
                        echo $this->slugInList($row['slug']);
                        ?>
                    </td> 
                    <td  width='100'><a onclick="EditTexo('<?php echo $texo ?>',<?php echo $row['term_id'] ?>, this)" href="javascript:">Edit</a><!--<a onclick="getEditData('<?php // echo $row['term_id']                                                                                                                                                                                             ?>')" href="javascript:">Edit</a>-->&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="Act('delTexo=<?php echo $row['taxonomy_id'] ?>', true, true)" href="javascript:">Delete</a></td>
                </tr>
                <tr class='childItem' id="childs_<?php echo $row['term_id'] ?>">
                    <td colspan="5" style='padding:0;'>
                        <table class="table table-striped table-responsive-lg table-cms" style="margin:0;">
                            <?php
                            foreach ($Childrows as $Childrow) {
                                ?>
                                <tr>
                                    <td width='50'><input type="checkbox"></td>
                                    <td width='200'><?php echo $Childrow['term_group'] != '0' ? "» " : "" ?><?php echo $Childrow['name'] ?></td>
                                    <td><?php echo $this->descriptionInList($Childrow['description']) ?></td>
                                    <td  width='250'>
                                        <?php
                                        echo $this->slugInList($Childrow['slug']);
                                        ?>
                                    </td> 
                                    <td width='100'><a onclick="EditTexo('<?php echo $texo ?>',<?php echo $Childrow['term_id'] ?>, this)" href="javascript:">Edit</a><!--<a onclick="getEditData('<?php // echo $Childrow['term_id']                                                                                                                                                                                           ?>')" href="javascript:">Edit</a>-->&nbsp;&nbsp;|&nbsp;&nbsp;<a onclick="Act('delTexo=<?php echo $Childrow['taxonomy_id'] ?>', true, true)" href="javascript:">Delete</a></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </table>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
        <div class="row">
            <div class="col-sm-2 ippControl">Per Page:
                <div class="dropdown ppi">
                    <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo $ipp ?>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <?php
                        $NofPage = array(500, 100, 50, 20);
                        foreach ($NofPage as $nOi) {
                            $sel = $nOi == $ipp ? 'selected' : "";
                            echo "<a class=\"dropdown-item\" onclick=\"load_list('term_ipp=$nOi')\" href=\"javascript:void(0)\">$nOi</a>";
                        }
                        ?>
                        <a class="dropdown-item"><input type="text" value="<?php echo $ipp ?>" onchange="load_list('term_ipp=' + $(this).val())" class='form-control'></a>
                    </div>
                </div>
            </div>
            <div class="col-sm-10">
                <div id="tnt_pagination" class=''><?php echo $DB->renderFullNav(); ?></div>
            </div>
        </div>

        <?php
    }

    public function descriptionInList($str) {
        $str = strip_tags($str);
        if (strlen($str) > 50) {
            return substr($str, 0, 50) . "...";
        }
        return $str;
    }

    public function slugInList($str) {
        if (strlen($str) > 30) {
            return substr($str, 0, 30) . "...";
        }
        return $str;
    }

    public function texoSelect($name = "", $input = 'checkbox') {
        //var_dump($name);
        global $DB, $POST;
        $ides = array();
        if (isset($POST['ID']))
            $ides = $this->get_relations($POST['ID']);
        //var_dump($ides);
        $texo = $name[0];
        //var_dump($name);
        $texonomys = $DB->select("term_taxonomy as tx left join terms as trm on tx.term_id=trm.term_id", "name,taxonomy_id", "tx.taxonomy='$texo'");
        // var_dump($texonomys);
        echo "<div class=\"mBoxBody\">";
        foreach ($texonomys as $texo) {
            if ($input == 'checkbox') {
                ?>
                <span class='term-item-select'><input class='sel_texo' id='sel_texo_<?php echo $texo['taxonomy_id'] ?>' name="sel_texo[]" <?php echo in_array($texo['taxonomy_id'], $ides) ? "checked" : "" ?> type='checkbox'  value="<?php echo $texo['taxonomy_id'] ?>">&nbsp;&nbsp;<label for="sel_texo_<?php echo $texo['taxonomy_id'] ?>"><?php echo $texo['name'] ?></label></span>
                <?php
            } else {
                ?>
                <span class='term-item-select'><input class='sel_texo' id='sel_texo_<?php echo $texo['taxonomy_id'] ?>' name="sel_texo[]" <?php echo in_array($texo['taxonomy_id'], $ides) ? "checked" : "" ?> type='radio'  value="<?php echo $texo['taxonomy_id'] ?>">&nbsp;&nbsp;<label for="sel_texo_<?php echo $texo['taxonomy_id'] ?>"><?php echo $texo['name'] ?></label></span>
                <?php
            }
        }

        echo "</div>";
    }

    public function texoChose($name = "", $type = 'checkbox') {
        //var_dump($name);
        global $DB;
        $ides = $this->get_relations($name['ID']);
        $tex = $name[0];
        $usTexo = str_replace(" ", "_", $tex);
        $usTexo = str_replace("-", "_", $usTexo);
        // var_dump($name);
        $texonomys = $DB->select("term_taxonomy as tx left join terms as trm on tx.term_id=trm.term_id", "*", "tx.taxonomy='$tex'  and trm.term_group=0 order by tx.term_id DESC");
        // var_dump($texonomys);
        echo "<div class=\"mBoxBody\">";
        echo "<div class='tagArea'>";
        echo "<ul class='texoChose'>";
        echo "<li><div class='tag'><input type='$type' value='' checked name=\"sel_texo[]\" id='noneTexo'>&nbsp;&nbsp;<label for='noneTexo'>None</label></div></li>";
        foreach ($texonomys as $texo) {
            $Childrows = $DB->select("term_taxonomy as tx left join terms as trm on tx.term_id=trm.term_id", "*", "tx.taxonomy='$tex' and trm.term_group=$texo[taxonomy_id]");
            $is_Child = count($Childrows) > 0 ? true : false;
            ?>
            <li><div class='tag'><?php echo $is_Child ? "<span class='has_subTexo' onclick='openSubTexo(this)'></span>" : "" ?><input name="sel_texo[]" <?php echo in_array($texo['taxonomy_id'], $ides) ? "checked" : "" ?> type='<?php echo $type ?>' id="texo_<?php echo $texo['taxonomy_id'] ?>" value="<?php echo $texo['taxonomy_id'] ?>">&nbsp;&nbsp;<label for="texo_<?php echo $texo['taxonomy_id'] ?>"><?php echo $texo['name'] ?></label></div>
                <?php
                if ($is_Child) {
                    echo "<ul class='texoChose-sub'>";
                    foreach ($Childrows as $chld) {
                        ?><li><div class='tag'><input name="sel_texo[]" <?php echo in_array($chld['taxonomy_id'], $ides) ? "checked" : "" ?> type='<?php echo $type ?>' id="texo_<?php echo $chld['taxonomy_id'] ?>" value="<?php echo $chld['taxonomy_id'] ?>">&nbsp;&nbsp;<label for="texo_<?php echo $chld['taxonomy_id'] ?>"><?php echo $chld['name'] ?></label></div></li><?php
                        }
                        echo "</ul>";
                    }
                    ?>
            </li>
            <?php
        }
        echo "</ul>";
        ?>
        <hr>
        <div class="AddTexo">
            <div class="input-group mb-3">
                <input id="<?php echo $usTexo ?>tagString" type="text" class="form-control" title='Separat by comma(,) to bulk add' placeholder="Add New" aria-label="Recipient's username" aria-describedby="basic-addon2" >
                <div class="input-group-append">
                    <button class="btn btn-cms-default" onclick="<?php echo $usTexo ?>saveTag(this)" type="button">Save</button>
                </div>
            </div>
            <input id='<?php echo $usTexo ?>texo' type='hidden' value="<?php echo $name[0] ?>">
        </div>
        <script>
            function <?php echo $usTexo ?>saveTag(_this) {
                var str = $("#<?php echo $usTexo ?>tagString").val();
                var id = $("#ID").val();
                $(_this).html(loader);
                var data = {ajx_action: "saveTag", str: str, id: id, texo: $("#<?php echo $usTexo ?>texo").val()};
                jQuery.post('index.php', data, function(response) {
                    //console.log(response);
        <?php echo $usTexo ?>tagList();
                    if (response) {
                        var obj = jQuery.parseJSON(response);
                        if (obj['msg'] !== '') {
                            if (obj['error'] !== '') {
                                msg(obj['msg'], 'R');
                            } else {
                                msg(obj['msg'], 'G');
                            }
                        }
                    }

                    $(_this).html("Save");
                });
            }

        </script>
        <?php
        echo "</div>";
    }

}

//saveTag();