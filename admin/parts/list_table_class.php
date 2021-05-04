<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of list_table_class
 *
 * @author nrb
 */
class list_table_class {

    //put your code here
    public function pages($type) {
        global $DB;
        $parm = $_REQUEST['loadList'];
        $typeField = $_REQUEST['post-type'];

        if (isset($_REQUEST['texo'])) {
            $_SESSION['texo'] = $_REQUEST['texo'];
        }

        //var_dump($_SESSION);

        if (isset($_REQUEST['st'])) {
            $_SESSION['st'] = $_REQUEST['st'];
        }

        if (isset($_REQUEST['ipp'])) {
            $_SESSION['ipp'] = $_REQUEST['ipp'];
        }

        if (isset($_GET['q'])) {
            $_SESSION['q'] = $_REQUEST['q'];
        }
        $status = !isset($_SESSION['st']) ? 'published' : $_SESSION['st'];
        $q = isset($_SESSION['q']) ? $_SESSION['q'] : "";
        $ipp = isset($_SESSION['ipp']) ? $_SESSION['ipp'] : 30;


        $fields = getTbleField('default');
        $fieldsType = getTbleField($typeField);

        $fields = array_merge($fields, $fieldsType);
        usort($fields, function($a, $b) {
            return $a['order'] - $b['order'];
        });
        //$fields=array_unique($fields);
        //var_dump($fields);
        ?>
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
            <table class="table table-striped table-responsive-lg table-cms post-table">
                <tr>
                    <?php
                    foreach ($fields as $col) {
                        if (isset($col['title_filter'])) {
                            $calBack = $col['title_filter'];
                            if (function_exists($calBack)) {
                                echo "<td>" . $calBack($col['title']) . "</td>";
                            } else {
                                echo "<th>$col[title]</th>";
                            }
                        } else {
                            echo "<th>$col[title]</th>";
                        }
                    }
                    ?>
                    <td></td>
                </tr>
                <?php
                $sWh = '';
                if (!empty($q)) {
                    $sWh = " and (post_name like '%$q%' or
                             post_title like '%$q%' or
                             post_content like '%$q%' or    
                             post_date_gmt like '%$q%'
                             )";
                }

                $tbl = "";
                if (multi_lang()) {
                    if (class_exists('siteLanguages')) {
                        $deff = get_option('deffLang');
                        $lngs = new siteLanguages();
                        $curType = post_type();
                        if (in_array($curType, $lngs->postTypes)) {
                            if (isset($_SESSION['lng']) && $_SESSION['lng'] != 'all') {
                                $lng = $_SESSION['lng'];
                                $tbl = " left join `post-meta` on ID=post_id";
                                $sWh.=" and meta_key='lng' and meta_value='$lng' ";
                                //var_dump($deff);
                            }
                        }
                    }
                }

                if (!empty($_SESSION['texo'])) {
                    $txoId = $_SESSION['texo'];
                    $txoCon = "texo_id=$txoId and ";
                    $rows = $DB->paginate("term_relationships as tr left join post as p on tr.object_id=p.ID $tbl", "*", "$txoCon post_type='$type'and post_status='$status' $sWh and post_parent=0 order by post_modified_gmt desc", $ipp, $typeField);
                } else {
                    $rows = $DB->paginate("post $tbl", "*", "post_type='$type'and post_status='$status' $sWh and post_parent=0 order by post_modified_gmt desc", $ipp, $typeField);
                }
                if ($rows) {
                    foreach ($rows as $row) {
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
                                        // echo $filterName($row[$col['field']], $row['ID']);
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
                        <td><a href="?l=edit&post-type=<?php echo $row['post_type'] ?>&ID=<?php echo $row['ID'] ?>">Edit</a>&nbsp;|&nbsp;
                            <?php
                            if ($row['post_status'] == 'trash') {
                                ?>
                                <a onclick="Act('del=<?php echo $row['ID'] ?>', true, true)" href="javascript:">Delete Forever</a></td>
                            <?php
                        } else {
                            ?>
                            <a onclick="Act('trash=<?php echo $row['ID'] ?>', true, true)" href="javascript:">Trash</a></td>
                            <?php
                        }
                        ?>
                        <?php
                        echo "</tr>";

                        //child====
                        if (!empty($_SESSION['texo'])) {
                            $txoId = $_SESSION['texo'];
                            $txoCon = "texo_id=$txoId and ";
                            $Childrows = $DB->select("term_relationships as tr left join post as p on tr.object_id=p.ID $tbl", "*", "$txoCon post_type='$type'and post_status='$status' $sWh and post_parent=$row[ID] order by menu_order asc");
                        } else {
                            $Childrows = $DB->select("post $tbl", "*", "post_type='$type'and post_status='$status' $sWh and post_parent=$row[ID] order by menu_order asc");
                        }
                        foreach ($Childrows as $Childrow) {
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
                                            // echo $filterName($Childrow[$col['field']], $Childrow['ID']);
                                            if (is_array($col['field'])) {
                                                echo $filterName($col['field'], $Childrow);
                                            } else {
                                                echo $filterName($Childrow[$col['field']], $Childrow['ID']);
                                            }
                                        } else {
                                            echo $Childrow[$col['field']] . " (filter function not exist)";
                                        }
                                    } else {
                                        echo $Childrow[$col['field']];
                                    }
                                }
                                echo "</td>";
                            }
                            ?>
                            <td><a href="?l=edit&post-type=<?php echo $Childrow['post_type'] ?>&ID=<?php echo $Childrow['ID'] ?>">Edit</a>&nbsp;|&nbsp;
                                <?php
                                if ($Childrow['post_status'] == 'trash') {
                                    ?>
                                    <a onclick="Act('del=<?php echo $Childrow['ID'] ?>', true, true)" href="javascript:">Delete Forever</a></td>
                                <?php
                            } else {
                                ?>
                                <a onclick="Act('trash=<?php echo $Childrow['ID'] ?>', true, true)" href="javascript:">Trash</a></td>
                                <?php
                            }
                            ?>
                            <?php
                            echo "</tr>";
                        }
                        //Child End
                    }
                } else {
                    echo "No Page Created yet";
                }
                ?>
            </table>
            <div class='row'>
                <div class="bulkControl col-3">
                    <div class="row">
                        <div class="col-9">
                            <?php
                            $defaultAc = getBulkAction('default');
                            $AcType = getBulkAction($typeField);
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
                <div class="col-sm-2 ippControl">Per Page:
                    <div class="dropdown ppi">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php echo $ipp ?>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <?php
                            $NofPage = array(1000, 500, 100, 50, 20);
                            foreach ($NofPage as $nOi) {
                                $sel = $nOi == $ipp ? 'selected' : "";
                                echo "<a class=\"dropdown-item\" onclick=\"load_list('ipp=$nOi')\" href=\"#\">$nOi</a>";
                            }
                            ?>
                            <a class="dropdown-item"><input type="text" value="<?php echo $ipp ?>" onchange="load_list('ipp=' + $(this).val())" class='form-control'></a>
                        </div>
                    </div>
                </div>
                <div id="tnt_pagination" class='col-7'><?php echo $DB->renderFullNav(); ?></div>
            </div>
        </form>
        <?php
    }

}
