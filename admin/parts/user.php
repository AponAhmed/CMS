<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user
 *
 * @author nrb
 */
class user_class {

    var $DB;
    var $userLevel = array('S' => 'Super Admin', 'A' => 'Admin', 'U' => 'User', 'E' => 'Editor');
    var $currentUserID;

    //put your code here
    public function __construct() {
        global $DB;
        $this->DB = $DB;
        $this->currentUserID = isset($_SESSION[SESS_KEY]['login'])?$_SESSION[SESS_KEY]['login']:false;
    }

    public function active_users() {
        return $this->DB->select('user', "*", 'user_status=1');
    }

    public function getUserLabel($id = false) {
        if (!$id) {
            $id = $this->currentUserID;
        }
        //var_dump($id);
        $userMeta = get_metas('user', $id);
        return $userMeta['user_level'];
    }

    public function getUserMeta($id = false) {
        if (!$id) {
            $id = $this->currentUserID;
        }
        //var_dump($id);
        $userMeta = get_metas('user', $id);
        return $userMeta;
    }

    public function storeUserinfo() {
        $data = $_POST['data'];

        if (!empty($_POST['user_pass'])) {
            $data['user_pass'] = md5($_POST['user_pass']);
        }
        $data['user_status'] = 1;
        $meta = isset($_POST['meta']) ? $_POST['meta'] : false;
        if (isset($_POST['id'])) {
            //Update---
            $res = $this->DB->update('user', $data, "ID=$_POST[id]");
            if ($res) {
                if ($meta) {
                    foreach ($meta as $k => $val) {
                        //var_dump($k,$val)
                        $r = add_meta($k, $val, 'user', $_POST[id]);
                        //var_dump($r);
                    }
                }
                $info = array('msg' => "User Data Updated", 'error' => false);
            } else {
                //Update Error
                $info = array('msg' => "User Data Updated error," . $this->DB->error, 'error' => true);
            }
        } else {
            //Insert
            //var_dump($data);
            //exit;
            $insID = $this->DB->insert('user', $data);
            $CominsID = $this->DB->insert_id;
            if ($insID) {
                if ($meta) {
                    foreach ($meta as $k => $val) {
                        //var_dump($k, $val, 'user', $DB->insert_id);
                        add_meta($k, $val, 'user', $CominsID);
                    }
                }
                $info = array('msg' => "User data Inserted", 'error' => false);
            } else {
                //error to insert Data
                $info = array('msg' => "User data not Inserted" . $this->DB->error, 'error' => true);
            }
        }
        echo json_encode($info);
    }

    public function deleteUser($id = false) {
        if ($id == false) {
            $id = $_REQUEST['id'];
        }
        $deleted = $this->DB->delete('user', "id=$id");
        if ($deleted) {
            $metaDeleted = $this->DB->delete('meta', "data_id=$id and meta_table='user'");
            $info = array('msg' => "Data Deleted", 'error' => false);
        } else {
            $info = array('msg' => "Data Not Deleted", 'error' => true);
        }
        echo json_encode($info);
    }

    public function trashuser($id = false) {
        if ($id == false) {
            $id = $_REQUEST['id'];
        }
        $data = array('user_status' => 'trash');
        $trashed = $this->DB->update('user', $data, "id=$id");
        if ($trashed) {
            $info = array('msg' => "Data Trashed", 'error' => false);
        } else {
            $info = array('msg' => "Data Not Trashed", 'error' => true);
        }
        echo json_encode($info);
    }

    public function userRestore($id = false) {
        if ($id == false) {
            $id = $_REQUEST['id'];
        }
        $data = array('user_status' => '1');
        $trashed = $this->DB->update('user', $data, "id=$id");
        if ($trashed) {
            $info = array('msg' => "Data Restored", 'error' => false);
        } else {
            $info = array('msg' => "Data Not Restored", 'error' => true);
        }
        echo json_encode($info);
    }

    public function form() {
        $id = false;
        $editData=array(
            'display_name'=>'',
            'user_email'=>'',
            'user_login'=>''
            );
        if (isset($_REQUEST['id']) && !empty($_REQUEST['id'])) {
            $id = $_GET['id'];
            $editData = $this->DB->select('user', "*", "ID=$id");
            $editData = $editData[0];
            $meta = get_metas('user', $id);
        }
        ?>
        <h4>User</h4><hr>
        <form id="userForm">
            <label>User Full-Name</label>
            <input type="text" name="data[display_name]" value="<?php echo $editData['display_name'] ?>" class="form-control form-control-sm">
            <label>Email Address</label>
            <input type="email" name="data[user_email]" value="<?php echo $editData['user_email'] ?>" class="form-control form-control-sm">
            <label>User ID</label>
            <input type="text" name="data[user_login]" value="<?php echo $editData['user_login'] ?>" class="form-control form-control-sm">
            <label>Password</label>
            <input type="password" name="user_pass" class="form-control form-control-sm" placeholder="<?php echo!empty($id) ? 'Put it blamk for unchange' : '' ?>">
            <label>User Level</label>
            <select name="meta[user_level]" class="custom-select custom-select-sm">
                <option>Select</option>
                <?php
                foreach ($this->userLevel as $k => $label) {
                    $sel = $meta['user_level'] == $k ? "selected" : "";
                    echo "<option value='$k' $sel>$label</option>";
                }
                ?>
            </select>
            <br>
            <?php
            if (!empty($id)) {
                ?> <input type="hidden" name="id" value='<?php echo $id ?>'>  <button type="button" class="btn btn-cms-default" onclick='saveuser(userForm, this)'>Update</button> <?php
            } else {
                ?>   <button type="button" class="btn btn-cms-default" onclick='saveuser(userForm, this)'>Save</button> <?php
            }
            ?>

        </form>
        <script>
            function saveuser(frm, _this) {
                var url = "index.php?c=user&m=storeUserinfo";
                jQuery.ajax({
                    type: "POST",
                    url: url,
                    data: $(frm).serialize(),
                    success: function(res)
                    {
                        //console.log(data);
                        var res = JSON.parse(res);
                        if (res['error']) {
                            if (res['msg'] !== "") {
                                msg(res['msg'], "R");
                            }
                        } else {
                            if (res['msg'] !== "") {
                                msg(res['msg'], "G");
                            }
                            load_list();
                            $.fancybox.close();
                        }
                    }
                })
            }
        </script>
        <?php
    }

    public function userList() {

        if (isset($_REQUEST['st'])) {
            $_SESSION['st'] = $_REQUEST['st'];
        }
        if (isset($_GET['q'])) {
            $_SESSION['q'] = $_REQUEST['q'];
        }
        $status = !isset($_SESSION['st']) ? '1' : $_SESSION['st'];
        $q = isset($_SESSION['q']) ? $_SESSION['q'] : "";
        //$customerField = ;
        unset($_SESSION['st']);
        $fields = array('display_name' => 'Name', 'user_login' => 'ID', 'user_email' => 'Email', 'user_level' => 'User Level');
        ?>
        <div class="row">
            <div class='shortNav col-sm-9'>
                <a class="<?php echo $status == "1" ? "active" : "" ?>" onclick='load_list("st=1")' href="javascript:">Published</a> | 
                <a class="<?php echo $status == "trash" ? "active" : "" ?>" onclick='load_list("st=trash")' href="javascript:">Trashed</a>
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
        <table class="table table-striped table-responsive-lg table-cms">
            <tr>
                <?php
                foreach ($fields as $col) {
                    echo "<th>$col</th>";
                }
                ?>
                <td></td>
            </tr>
            <?php
            $sWh = '';
            if (!empty($q)) {
                $sWh = " and (display_name like '%$q%' or
                             user_login like '%$q%' or
                             user_email like '%$q%'
                             )";
            }
            //var_dump($status);
            $rows = $this->DB->paginate("user", "*", "user_status='$status' $sWh order by ID desc", 15, 'user');

            foreach ($rows as $row) {

                $meta = get_metas('user', $row['ID']);
                $meta = empty($meta) ? array() : $meta;
                $row = array_merge($row, $meta);

                echo "<tr>";
                $c = 0;
                foreach ($fields as $k => $col) {
                    $c++;
                    echo "<td>";
                    $fltrF = $k . "__filter";
                    if (function_exists($fltrF)) {
                        $fltrF($row[$k], $k);
                    } else {
                        echo $row[$k];
                    }

                    echo "</td>";
                }
                ?>
                <td><a class="fBox" w='400' href="index.php?c=user&m=form&id=<?php echo $row['ID'] ?>">Edit</a>&nbsp;|&nbsp;
                    <?php
                    if ($row['user_status'] == 'trash' || $row['user_status'] == '0') {
                        ?>
                        <a onclick="Act('c=user&m=userRestore&id=<?php echo $row['ID'] ?>', true, true)" href="javascript:">Restore</a> | <a onclick="Act('c=user&m=deleteUser&id=<?php echo $row['ID'] ?>', true, true)" href="javascript:">Delete Forever</a></td>
                    <?php
                } else {
                    ?>
                    <a onclick="Act('c=user&m=trashuser&id=<?php echo $row['ID'] ?>', true, true)" href="javascript:">Trash</a></td>
                <?php
            }
            ?>
            <?php
            echo "</tr>";
        }
        ?>
        </table>
        <div id="tnt_pagination"><?php echo $this->DB->renderFullNav(); ?></div>
        <script>
            $(function() {
                fBox();
            })
        </script>
        <?php
    }

}

function user_level__filter($label, $field) {
    $user = new user_class();
    echo $user->userLevel[$label];
}

function UG($check = false) {
    $user = new user_class();
    if (!empty($check)) {
        if ($check == $user->getUserLabel()) {
            return true;
        } else {
            return false;
        }
    }
    return $user->getUserLabel();
}

function UP() {
    //fort pre-development test
    $user = new user_class();
    $meta = $user->getUserMeta();
    $userPermission = unserialize(@$meta['user_permission']);
    if (is_array($userPermission) && count($userPermission > 0)) {
        return $userPermission;
    } else {
        $id = $user->getUserLabel();
        $perms = unserialize(get_option("user_group_permission_$id"));
        return $perms;
    }
    //pre-dev test
    // $userPermission = $customPermission[$user->currentUserID];
    //array(
    //modiul=>array('add','edit',..),
    //)
}
