<?php defined('ABSPATH') OR exit('No direct script access allowed');  ?>
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of load
 *
 * @author nrb
 */
class Load {

    //put your code here
    public function library() {
        global $ATTACH;
        $ATTACH->attachmentList();
    }
}
