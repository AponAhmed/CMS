<?php

/*
 * SiATEX CMS
 * Version: 2.4
 * Author: SiATEX
 * Last Update:
 */
ob_start();
$rustart = getrusage();
define('SITE', true);
require_once(dirname(__FILE__) . '/loader.php' );
?>