<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of attachment
 *
 * @author nrb
 */
class attachment {

    //put your code here
    var $postType = 'attachment';
    var $dir;
    var $maxSize;
    var $maxInScript;
    var $attachmentSizes;

    public function __construct() {
        global $sizes;
        $this->attachmentSizes = $sizes;
        $this->maxSize = ini_get('post_max_size');
        preg_match("/\d+/", $this->maxSize, $m);
        $this->maxInScript = $m[0];
        $this->dir = UPLOAD;
        //var_dump($this->maxInScript);
    }

    public function uploader() {
        global $TERM;
        ?>
        <div class="dropzone imgUploader"  id="my-awesome-dropzone">
            <div class="fallback">
                <input name="file" type="file" multiple />
            </div>
        </div>
        <script>
            Dropzone.options.myAwesomeDropzone = {
                url: "index.php",
                paramName: "attachment_upload", // The name that will be used to transfer the file
                mamaxFilesize: <?php echo $this->maxInScript ?>, // MB
                thumbnailWidth: 50,
                thumbnailHeight: 50,
                addRemoveLinks: false,
                clickable: true,
                dictDefaultMessage: '(»  Drop files to upload (or click)  «)<br> <span class="limit"><?php echo "Maximum Upload size :" . $this->maxSize ?></span>',
                accept: function(file, done) {
                    if (file.name == "justinbieber.jpg") {
                        done("Naha, you don't.");
                    }
                    else {
                        done();
                    }
                },
                success: function(file, response) {
                    //console.log(response);
                    if ($("#feature_image").length) {
                        var CVal = $("#feature_image").val();
                        CVal = CVal.replace(/(^,)|(,$)/g, "") + "," + response;
                        CVal = CVal.replace(/(^,)|(,$)/g, "");
                        $("#feature_image").val(CVal);
                    }

                },
                uploadprogress: function(res) {
                    // console.log(res['upload']['progress']);
                    var prog = res['upload']['progress'];
                    $(res['previewElement']['childNodes'][5]['childNodes'][0]).width(prog + "%");
                },
                sending: function(file, xhr, formData) {
                    var customResize = "";
                    if ($("#AttCustomResize").length != 0) {
                        //it doesn't exist
                        var AttCustomResize = $("#AttCustomResize").val();
                        formData.append('AttCustomResize', AttCustomResize);
                    }

                    if ($(".sel_texo:checked").length != 0) {
                        //it doesn't exist
                        var texo = $(".sel_texo:checked").val();
                        formData.append('sel_texo', texo);

                    }
                    if ($("#imageQuality").length != 0) {
                        //it doesn't exist
                        var imageQuality = $("#imageQuality").val();
                        formData.append('imageQuality', imageQuality);
                    }

                    if ($(".mediaTypeChose:checked").length > 0) {
                        var Cur = $(".mediaTypeChose:checked").val();
                        formData.append('sel_texo', Cur);
                    }
                    //sel_texo
                }
            };

        </script>
        <?php
    }

    public function save_file() {
        global $attachment_file_save, $TERM;
        global $POSTS;

        //ob_start();

        $customResize = @$_POST['AttCustomResize'];
        $customSizes = explode(',', $customResize);
        $imgQlt = 75;
        if (isset($_POST['imageQuality']) && !empty($_POST['imageQuality'])) {
            $imgQlt = $_POST['imageQuality'];
        }

        $fname = $_FILES['attachment_upload']['name'];
        $pinfo = pathinfo($fname);
        $name = $pinfo['filename'];
        $slug = titleFilter($name);

        $re = '/-([\d]{3,4})$/m';
        $replacement = '';
        $slug = preg_replace($re, $replacement, $slug);

        $ext = $pinfo['extension'];
        //$pinfo['filename']$pinfo['extension']
        $yr = date('Y');
        $mnt = date('m');
        $destDir = UPLOAD . "$yr/$mnt";
        if (!is_dir($destDir)) {
            if (!is_dir(UPLOAD . "$yr")) {
                //MKdir of year
                if (mkdir(UPLOAD . "$yr", 0777, true)) {
                    mkdir(UPLOAD . "$yr/$mnt", 0777, true);
                }
            } else {
                if (!is_dir(UPLOAD . "$yr/$mnt")) {
                    //MKdir of Month
                    mkdir(UPLOAD . "$yr/$mnt", 0777, true);
                }
            }
        }
        $destDir = UPLOAD . "$yr/$mnt";
        $destFileDir = $destDir . "/$slug.$ext";
        $destFileUri = UPLOAD_URI . "$yr/$mnt/$slug.$ext";
        $fr = @copy($_FILES['attachment_upload']['tmp_name'], $destFileDir);
        //$fr = resize_scal($destFileDir, $_FILES['attachment_upload']['tmp_name']);
        //100,150,300,768,1024 Sizes
        $sizes = $this->attachmentSizes;
        $sizes = array_merge($sizes, $customSizes);
        $sizes = array_filter($sizes);
        foreach ($sizes as $size) {
            $desS = getimagesize($destFileUri);
            $wid = $desS[0];
            if (!$desS) {
                if ($ext = "webp") {
                    $img = imagecreatefromwebp($destFileUri);
                    $wid = imagesx($img);
                }
            }
            if ($wid > $size || $size == 100) {
                //resize($destFileDir, "$destDir/$slug-$size.$ext", $size, $imgQlt);
                $rs = resize_scal($destFileDir, "$destDir/$slug-$size.$ext", $size);
            }
        }

        if ($fr) {
            $postData = array();
            $postData['post_type'] = 'attachment';
            $postData['post_title'] = $name;
            $postData['post_status'] = 'published';
            $postData['post_name'] = $slug;
            $postData['guid'] = trimSlash($destFileUri);
            $postData['post_date_gmt'] = time();
            $info = $POSTS->post_add($postData);
            $r = update_post_meta($info, 'attachment_alter', $name);
            $r1 = update_post_meta($info, 'attachment_caption', $name);
            //var_dump($info, $r, $r1);
            if (isset($_POST['sel_texo'])) {
                $texo_id = $_POST['sel_texo'];
                $t = $TERM->add_term_relation($info, $texo_id);
                //var_dump($t);
            }
        }
        foreach ($attachment_file_save as $att_fileSaveCallBack) {
            if (function_exists("$att_fileSaveCallBack")) {
                $att_fileSaveCallBack($info);
            }
        }
        // $error = ob_get_contents();
        // @ob_end_clean();
        // echo json_encode(array("error" => $error, "fname" => $fname, 'icon' => $icn));
        ob_get_clean();
        echo $info;
    }

    public function attachmentList() {
        global $DB, $TERM;
        $fields = getTbleField('attachment');
        $viewType = "list"; //default view type

        $texType = "";
        if (isset($_REQUEST['st'])) {
            $_SESSION['st'] = $_REQUEST['st'];
        }
        if (isset($_GET['q'])) {
            $_SESSION['q'] = $_REQUEST['q'];
        }

        if (isset($_GET['vType'])) {
            $_SESSION['vType'] = $_REQUEST['vType'];
        }
        if (isset($_SESSION['vType'])) {
            $viewType = $_SESSION['vType'];
        }

        if (isset($_GET['texo'])) {
            $_SESSION['texo'] = $_REQUEST['texo'];
        }
        if (isset($_SESSION['texo'])) {
            $texType = $_SESSION['texo'];
        }


        $status = !isset($_SESSION['st']) ? 'published' : $_SESSION['st'];
        $q = isset($_SESSION['q']) ? $_SESSION['q'] : "";
        //$arg = array(
        //   'post_type' => 'attachment'
        // );
        // $attahments = get_posts($arg);
        // var_dump($fields);
        ?>
        <div class="libraryController">
            <div class="row">
                <div class="col-sm-2">
                    <button type="button" onclick="load_list('vType=grid')" class="btn <?php echo $viewType == "grid" ? "btn-cms-primary" : " btn-cms-default" ?>"><i class="fas fa-th"></i></button>
                    <button type="button" onclick="load_list('vType=list')" class="btn <?php echo $viewType == "list" ? "btn-cms-primary" : " btn-cms-default" ?>"><i class="fas fa-list"></i></button>
                </div>
                <div class="col-sm-10">
                    <select onchange="load_list('texo=' + this.value)" class="custom-select custom-select-sm m0 float-right" style="max-width:27%">
                        <option value="">General</option>
                        <?php
                        foreach ($TERM->texoListRow('type') as $texo) {
                            $sel = $texType == $texo['taxonomy_id'] ? "selected" : "";
                            echo "<option value='$texo[taxonomy_id]' $sel>$texo[name]</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

        </div>
        <form id="List" method="post">
            <div class="row">
                <div class='shortNav col-sm-9'>
                    <a class="<?php echo $status == "published" ? "active" : "" ?>" onclick='load_list("st=published")' href="javascript:">Published</a> | 
                    <a class="<?php echo $status == "trash" ? "active" : "" ?>" onclick='load_list("st=trash")' href="javascript:">Trashed</a> | 
                    <a class="<?php echo $status == "draft" ? "active" : "" ?>" onclick='load_list("st=draft")' href="javascript:">Drafts</a>
                </div>
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
            <?php
            if ($viewType == "grid") {
                echo "<div class='gridAtt row'><div class='col-sm-12'>";
                $sWh = '';
                if (!empty($q)) {
                    $sWh = " and (post_name like '%$q%' or
                             post_title like '%$q%' or
                             post_date_gmt like '%$q%'
                             )";
                }

                if (!empty($texType)) {
                    $rows = $DB->paginate("term_relationships left join post on ID=object_id", "*", "post_type='attachment' and post_status='$status' $sWh and texo_id=$texType order by ID DESC", 15, 'attachment');
                } else {
                    $rows = $DB->paginate("post LEFT OUTER JOIN term_relationships on object_id=ID", "*", "post_type='attachment' and post_status='$status' $sWh  order by ID DESC", 15, 'attachment');
                }
                ///var_dump($rows);
                if ($rows) {
                    foreach ($rows as $row) {
                        if (empty($texType) && !empty($row['object_id'])) {
                            continue;
                        }
                        //var_dump($fields);
                        $fileField = $fields[1];
                        echo filter_library_file_grid($fileField['field'], $row);

//                        foreach ($fields as $col) {
//                            if (isset($col['filter'])) {
//                                $filterName = "filter_" . $col['filter'];
//                                if (function_exists($filterName)) {
//                                    if (is_array($col['field'])) {
//                                        echo $filterName($col['field'], $row);
//                                    } else {
//                                        echo $filterName($row[$col['field']], $row['ID']);
//                                    }
//                                } else {
//                                    echo $row[$col['field']] . " (filter function not exist)";
//                                }
//                            }
//                        }
                    }
                }
                echo "</div></div>";
            } else {
                ?>
                <table class="table table-striped table-responsive-lg table-cms">
                    <tr>
                        <?php
                        foreach ($fields as $col) {
                            echo "<th>$col[title]</th>";
                        }
                        ?>
                        <td></td>
                    </tr>
                    <?php
                    $sWh = '';
                    if (!empty($q)) {
                        $sWh = " and (post_name like '%$q%' or
                             post_title like '%$q%' or
                             post_date_gmt like '%$q%'
                             )";
                    }


                    if (!empty($texType)) {
                        $rows = $DB->paginate("term_relationships left join post on ID=object_id", "*", "post_type='attachment' and post_status='$status' $sWh and texo_id=$texType order by ID DESC", 15, 'attachment');
                    } else {
                        $rows = $DB->paginate("post LEFT OUTER JOIN term_relationships on object_id=ID", "*", "post_type='attachment' and post_status='$status' $sWh  order by ID DESC", 15, 'attachment');
                    }

                    if ($rows) {
                        foreach ($rows as $row) {
                            if (empty($texType) && !empty($row['object_id'])) {
                                continue;
                            }
                            echo "<tr>";
                            $c = 0;
                            foreach ($fields as $col) {
                                $c++;
                                echo "<td>";
                                if ($c == 0) {
                                    echo $col['field'];
                                } else {
                                    if (isset($col['filter'])) {
                                        $filterName = "filter_" . $col['filter'];
                                        if (function_exists($filterName)) {
                                            if (is_array($col['field'])) {
                                                echo $filterName($col['field'], $row);
                                            } else {
                                                echo $filterName($row[$col['field']], $row['ID']);
                                            }
                                        } else {
                                            echo $row[$col['field']] . " (filter function not exist)";
                                        }
                                    } else {
                                        echo $row[$col['field']];
                                    }
                                }
                                echo "</td>";
                            }
                            ?>
                            <td><a href="?l=edit&post-type=<?php echo $row['post_type'] ?>&ID=<?php echo $row['ID'] ?>">Edit</a>&nbsp;|&nbsp;<a onclick="Act('delete=<?php echo $row['ID'] ?>', true, true)" href="javascript:">Delete</a></td>
                            <?php
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td>Results Not found</td></tr>";
                    }
                    ?>
                </table>
            <?php } ?>
            <div class='row'>
                <div class="bulkControl col-3">
                    <div class="row">
                        <div class="col-9">
                            <?php
                            $defaultAc = getBulkAction('default');
                            $AcType = getBulkAction(@$typeField);
                            $bulkActions = array_merge($defaultAc, $AcType);
                            usort($fields, function($a, $b) {
                                return $a['order'] - $b['order'];
                            });
                            // var_dump($bulkActions);
                            echo "<select  class=\"custom-select custom-select-sm\" id=\"selectedAction\">";
                            echo "<option value=\"\">Action</option>";
                            foreach ($bulkActions as $action) {
                                if ($status == strtolower($action['label'])) {
                                    continue;
                                }
                                echo "<option value=\"$action[calback]\">$action[label]</option>";
                            }
                            echo "</select>";
                            ?>
                        </div>
                        <div class="col-3">
                            <button type="button" onclick="applyAction(List)" class="btn btn-cms-default">Apply</button>
                        </div>
                        <script>
                            $("#selectALL").click(function() {
                                $('.chk:checkbox').not(this).prop('checked', this.checked);
                            });
                            function applyAction(frm) {
                                var calback = $("#selectedAction").val();
                                if (calback !== "") {
                                    //function multipleAction(form, url, action, conf) {
                                    var calbk = window[calback];
                                    if (typeof calbk === 'function') {
                                        calbk(frm);
                                    } else {
                                        alert(calback + " is not a function");
                                    }
                                } else {
                                    msg("First select a Action", "R");
                                }

                                // fn();

                            }
                        </script>
                    </div>
                </div>
                <div id="tnt_pagination" class='col-9'><?php echo $DB->renderFullNav(); ?></div>
            </div>
        </form>
        <?php
    }

}
