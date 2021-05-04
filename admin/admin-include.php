<?php

defined('ABSPATH') OR exit('No direct script access allowed');
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'parts/metabox_class.php';
$metabox = new metabox_class();
require_once 'parts/load.php';
$LOAD = new Load();
//Store Class
require_once 'parts/posts_class.php';
$POSTS = new Post_class();
require_once 'parts/attachment_class.php';
$ATTACH = new attachment();
require_once 'classes/forms_class.php';
require_once 'classes/update_cms_class.php';
require_once 'parts/list_table_class.php';
require_once 'classes/theme_edit.php';
$LIST = new list_table_class();