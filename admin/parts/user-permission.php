<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class user_permission {

    var $DB;
    var $current_user_pemission_str;
    var $user;

    public function __construct() {
        global $DB;
        $this->DB = $DB;
        $this->user = new user_class();
    }

    public function selectUserNlabel() {
        $labels = $this->user->userLevel;
        $users = $this->user->active_users();
        //var_dump($users);
        $htm = "<select class='custom-select custom-select-sm userLabelSelect'>";
        $htm.="<optgroup label=\"User Group\">";
        foreach ($labels as $k => $lbl) {
            if ($k == 'S')
                continue;
            $htm.="<option value='$k'>$lbl</option>";
        }
        $htm.="</optgroup>";
        $htm.="<optgroup label=\"User\">";
        foreach ($users as $user) {
            if ($this->user->getUserLabel($user['ID']) !== 'S') {
                $htm.="<option value='$user[ID]'>$user[display_name]</option>";
            }
        }
        $htm.="</optgroup>";

        $htm.="</select>";
        return $htm;
    }

    public function permissionWrap($id = false) {
        global $adminMenu;
        $id = $id == false ? $_POST['id'] : $id;
        usort($adminMenu, function($a, $b) {
            return $a['order'] - $b['order'];
        });

        $perms = array();
        if (is_numeric($id)) {
            // echo 'user';
            $perms = get_meta('user_permission', 'user', $id);
            $perms = unserialize($perms[0]['meta_value']);
        } else {
            $perms = unserialize(get_option("user_group_permission_$id"));
        }
        // var_dump($perms);
        $htm = "<button type='button' class='btn btn-cms-primary' onclick='updatePermission()'>Update</button><hr>";

        $htm .= "<form id='permission'>
                <input type='hidden' name='id' value='$id'>
                <table class='table table table-striped table-responsive-lg table-cms permissionTable'>";
        $htm .= "<tr>
                    <th>Module</th>
                    <td>Read</td>
                    <td>Write</td>
                    <td>Delete</td>
                </tr>";
        foreach ($adminMenu as $k => $item) {
            if (!webAppMenu($item)) {
                continue;
            }

            if (empty($item['parent_slug']) && $item['slug'] != "" && $item['slug'] != "options") {
                //Parent
                // var_dump($item['slug']);
                // echo "<input type='checkbox' id='$item[slug]'>&nbsp;&nbsp;<label for='$item[slug]'>$item[menu_title]</label><br>";
                $slug = $item['slug'];

                $Mread = isset($perms[$slug]['read']) ? "checked" : "";
                $Mwrite = isset($perms[$slug]['write']) ? "checked" : "";
                $Mdelete = isset($perms[$slug]['delete']) ? "checked" : "";

                $htm .= "<tr>
                             <th>$item[menu_title]</th>
                             <td><input name='prm[$slug][read]' type='checkbox' $Mread value='1'></td>
                             <td><input name='prm[$slug][write]' type='checkbox' $Mwrite value='1'></td>
                             <td><input name='prm[$slug][delete]' type='checkbox' $Mdelete value='1'></td>
                         </tr>";
                //var_dump($item);

                foreach ($adminMenu as $sub) {
                    if (!empty($sub['parent_slug']) && $item['slug'] == $sub['parent_slug'] && $sub['slug'] != "" && $sub['slug'] != "permission") {
                        //Sub
                        $slug = $sub['slug'];
                        $Sread = isset($perms[$slug]['read']) ? "checked" : "";
                        $Swrite = isset($perms[$slug]['write']) ? "checked" : "";
                        $Sdelete = isset($perms[$slug]['delete']) ? "checked" : "";
                        $htm .= "<tr>
                             <th>&raquo;&nbsp;$sub[menu_title]</th>
                             <td><input name='prm[$slug][read]' type='checkbox' $Sread value='1'></td>
                             <td><input name='prm[$slug][write]' type='checkbox' $Swrite value='1'></td>
                             <td><input name='prm[$slug][delete]' type='checkbox' $Sdelete value='1'></td>
                         </tr>";
                        //var_dump($sub['slug']);
                        //echo "&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' id='$sub[slug]'>&nbsp;&nbsp;<label for='$sub[slug]'>$sub[menu_title]</label><br>";
                        //var_dump($sub);
                    }
                }
            }
        }
        $htm .= "</form></table>";

        echo $htm;
    }

    public function updatePermission() {
        $params = array();
        parse_str($_POST['data'], $params);

        $id = $params['id'];
        $prm = $params['prm'];
        if (is_numeric($id)) {
            echo 'user';
            $perms = add_meta('user_permission', serialize($prm), 'user', $id);
        } else {
            echo 'group';
            add_option("user_group_permission_$id", serialize($prm));
            update_option("user_group_permission_$id", serialize($prm));
        }
    }

    public function permission_window() {
        echo "<h4>User Permission</h4>" . $this->selectUserNlabel() . "<hr>";
        ?>
        <div id="permissionWrap"> </div>
        <script>

            function updatePermission() {
                console.log();
                var data = {cls: 'user_permission', m: "updatePermission", data: $("#permission").serialize()};
                jQuery.post('index.php', data, function(response) {
                    // console.log(response);
                    //$("#permissionWrap").html(response);
                    msg('Permission Updated');
                });
            }

            $(function() {
                $(".userLabelSelect").on('change', function() {
                    var data = {cls: 'user_permission', m: "permissionWrap", id: $(this).val()};
                    jQuery.post('index.php', data, function(response) {
                        // console.log(response);
                        $("#permissionWrap").html(response);
                    });


                });
            });
        </script>
        <?php
    }

}

add_admin_menu(array(
    'slug' => 'permission',
    'menu_title' => "Permission",
    'icon' => "",
    'icon_img' => "",
    'order' => "4",
    'parent_slug' => "options",
    'privilege_label' => array('S'),
));

function permission() {
    $USER_PERMISSION = new user_permission();
    $USER_PERMISSION->permission_window();
}
