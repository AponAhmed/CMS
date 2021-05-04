<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of store_class
 *
 * @author nrb
 */
class Post_class {

    public function post_add($data) {
        global $DB;
        $data['post_modified_gmt'] = date("Y-m-d H:i:s");
        //post_parent
        //menu_order 
        $data['post_date_gmt'] = date("Y-m-d H:i:s");
        if (empty($data['post_name'])) {
            $data['post_name'] = titleFilter($data['post_title']);
        }
        $data['post_name'] = titleFilter($data['post_name']);
        $res = $DB->insert("post", $data);
        return $id = $DB->insert_id;
    }

    public function post_up($data, $condition) {
        global $DB;
        $data['post_modified_gmt'] = date("Y-m-d H:i:s");
        if (empty($data['post_name'])) {
            $data['post_name'] = titleFilterNexist($data['post_title'], @$_POST['ID']);
        }
        $data['post_name'] = titleFilterNexist($data['post_name'], @$_POST['ID']);

        if (is_array($data['post_name'])) {
            $data['post_name'] = $data['post_name']['slug'];
        } else {
            $data['post_name'] = $data['post_name'];
        }



        $res = $DB->update("post", $data, $condition);
        //var_dump($res);
        $info['info'] = $DB->info;
        if ($res) {
            $info['error'] = "";
        } else {
            $info['error'] = $DB->error;
        }
        return $info;
    }

    public function parent_select($select = false, $name="", $class = "", $id = "") {
        global $post;
        $html = "";
        $arg = array(
            'post_type' => "page",
            'selectFields' => 'ID,post_title',
            'exclude' => array(isset($post['ID'])?$post['ID']:"")
        );
        if (empty($post)) {
            unset($arg['exclude']);
        }

        $posts = get_posts($arg);
        if (count($posts) > 0) {
            $html.="<select name=\"$name\" class='$class' id='$id'>";
            $html.="<option value=''>No Parent</option>";
            foreach ($posts as $post) {
                $sel = !empty($select) && $select == $post['ID'] ? "selected" : "";
                $html.="<option value='$post[ID]' $sel>$post[post_title]</option>";
            }
            $html.="</select>";
        }
        return $html;
    }

    public function post_select($select = false, $name="", $class = "", $id = "", $arg = array()) {
        $html = "";
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
                $sel = !empty($select) && $select == $post['ID'] ? "selected" : "";
                $html.="<option value='$post[ID]' $sel>$post[post_title]</option>";
            }
            $html.="</select>";
        }
        return $html;
    }

    public function move($ID, $dest) {
        global $DB;
        if ($dest == 'trash') {
            if (is_array($ID)) {
                $IDs = implode(",", $ID);
                $upd = $DB->update("post", array('post_status' => 'trash'), "ID in($IDs)");
            } else {
                $upd = $DB->update("post", array('post_status' => 'trash'), "ID=$ID");
            }
        } else if ($dest == 'published') {
            if (is_array($ID)) {
                $IDs = implode(",", $ID);
                $upd = $DB->update("post", array('post_status' => 'published'), "ID in($IDs)");
            } else {
                $upd = $DB->update("post", array('post_status' => 'published'), "ID=$ID");
            }
        } else if ($dest == 'draft') {
            if (is_array($ID)) {
                $IDs = implode(",", $ID);
                $upd = $DB->update("post", array('post_status' => 'draft'), "ID in($IDs)");
            } else {
                $upd = $DB->update("post", array('post_status' => 'draft'), "ID=$ID");
            }
        } else if ($dest == 'dateModify') {
            $dt = strtotime($_POST['date']);
            $dt = date("Y-m-d H:i:s", $dt);
            //exit;
            if (is_array($ID)) {
                $IDs = implode(",", $ID);
                $upd = $DB->update("post", array('post_modified_gmt' => $dt), "ID in($IDs)");
            } else {
                $upd = $DB->update("post", array('post_modified_gmt' => $dt), "ID=$ID");
            }
        }
        $info['info'] = $DB->info;
        if ($upd) {
            $info['error'] = "";
        } else {
            $info['error'] = $DB->error;
        }
        return $info;
    }

    public function delPost($id) {
        global $DB;
        $DB->delete("`post-meta`", "post_id=$id");
        $DB->delete("term_relationships", "object_id=$id");
        $DB->delete("post", "ID=$id");
    }

}
