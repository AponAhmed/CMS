<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

global $customCss;
$customCssStr = implode(" ", $customCss);
add_header("<style>$customCssStr</style>\n", 999);
