<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$l = "dash";
if (isset($_GET['l'])) {
    $l = $_GET['l'];
}
//var_dump($l);
$load_Admin_page = get_admin_page($l);
$targetAdminFilePath = ABSPATH . "$adminDir/parts/$load_Admin_page.php";

if (!BodyPermission($l)) {
    echo "You do not have permission to access :(";
    if ($l != 'dash') {
        exit;
    }
}
if (file_exists($targetAdminFilePath)) {
    require_once($targetAdminFilePath);
} else {
    if (function_exists($load_Admin_page)) {
        $load_Admin_page();
    } else {
        echo "Page File or CallBack($load_Admin_page) Function Not Found !!";
    }
}




