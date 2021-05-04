<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of update_cms_class
 *
 * @author nrb
 */
class Update_Cms {

    //put your code here
    var $filename = 'export.zip';
    var $dest;
    var $core_folders = array('admin', 'include');         //Updatable folder
    var $core_files = array('loader', 'install');          //Updatable files
    var $version;                                         //Current Version
    var $up_version;                                      //Remote Version
    var $last_update;
    var $remoteInfo;
    var $updateEnable = false;
    var $dbStructureChangeSql;

    public function __construct() {
        $this->dest = "http://siatexltd.com/cms_update_path/";

        //$info = file_get_contents($this->dest . 'readme.txt');
        $info = file_get_contents_curl($this->dest . 'readme.txt');

        $this->remoteInfo = file_info($info, 'info');
        $current = file_info('../index.php');
        $this->version = $current['Version'];
        if (isset($current['Last Update'])) {
            $this->last_update = $current['Last Update'];
        } else {
            $this->last_update = date("d F Y H:i.", filemtime("../index.php"));
        }
        $this->up_version = $this->remoteInfo['Version'];

        if ($this->version < $this->up_version) {
            $this->notify();
            $this->updateEnable = true;
            $this->dbStructureChangeSql = @file_get_contents($this->dest . 'update.sql');
        }
    }

    public function release_date() {
        if (isset($this->remoteInfo['Released Date'])) {
            return $this->remoteInfo['Released Date'];
        }

        //return filemtime($this->dest . 'readme.txt');
        //return date("F d Y H:i:s.", filemtime($this->dest . 'readme.txt'));

        $curl = curl_init($this->dest . 'readme.txt');

        //don't fetch the actual page, you only want headers
        curl_setopt($curl, CURLOPT_NOBODY, true);

        //stop it from outputting stuff to stdout
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // attempt to retrieve the modification date
        curl_setopt($curl, CURLOPT_FILETIME, true);

        $result = curl_exec($curl);

        if ($result === false) {
            die(curl_error($curl));
        }

        $timestamp = curl_getinfo($curl, CURLINFO_FILETIME);
        if ($timestamp != -1) { //otherwise unknown
            return date("d F Y H:i", $timestamp); //etc
        }
    }

    public function change_log() {
        return $this->remoteInfo['Changed Log'];
    }

    public function notify() {
        global $adminMenu;
        //var_dump($adminMenu);
        $m = find_array_with_val($adminMenu, 'update', 'slug');
        // var_dump($adminMenu[$m]);
        $adminMenu[$m]['menu_title'].=@$adminMenu[$m]['menu_title'] . "<span class='notify'></span>";
        if (isset($adminMenu[$m]['parent_slug'])) {
            $m = find_array_with_val($adminMenu, $adminMenu[$m]['parent_slug'], 'slug');
            $adminMenu[$m]['menu_title'].=$adminMenu[$m]['menu_title'] . "<span class='notify'></span>";
        }
    }

    public function update() {
        $msg = "";
        $tmpDown = TEMP . "up-resource.zip";
        if (copy($this->dest . $this->filename, $tmpDown)) {
            $msg.="Downloaded";
        } else {
            $msg.="Downloaded failed !.";
        }
        flush();
        $zip = new ZipArchive;
        if ($zip->open($tmpDown) === TRUE) {
            if ($_SERVER['HTTP_HOST'] == "asik") {
                $ext = $zip->extractTo('../content/upload/temp/upd');
            } else {
                $ext = $zip->extractTo('../');
            }
            $zip->close();
            $msg.=' and Updated';
            $this->updateDatabase();
            unlink($tmpDown);
        } else {
            $msg.='Updated failed';
        }
        if ($ext) {
            $info = array("msg" => $msg, 'rf' => "", "error" => 0);
            echo json_encode($info);
        } else {
            $info = array("msg" => $msg, 'rf' => "", "error" => 1);
            echo json_encode($info);
        }
    }

    public function updateDatabase() {
        global $DB;
        $DB->query($this->dbStructureChangeSql);
        //var_dump($DB->error);
    }

}
