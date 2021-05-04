<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
global $ATTACH;
?>
<h2>Upload New Media</h2>
<hr><br>

<div class="row">
    <div class="col-sm-9">
        <?php $ATTACH->uploader(); ?>
    </div>
    <div class="col-sm-3">
        <div>
            <div class="metaBox side">
                <div class="meta-box-header">
                    <div class="meta-box-title">Media Type</div>
                    <div class="meta-box-triger"><a href="javascript:"><span></span></a></div>
                </div>
                <div class="meta-box-content"> 
                    <div class="mBoxBody">
                        <?php $TERM->texoSelect(array('type'), "radio"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php



