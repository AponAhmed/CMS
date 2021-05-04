<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//function of Update CMS
function update_cms() {
    $UPD = new Update_Cms();
    ?>
    <h1>Update&nbsp;&nbsp;</h1><hr>
    <div class='row fieldRow'>
        <div class="col-sm-2">
            <label>Last Update</label>
        </div>
        <div class='col-sm-6'>
            <strong><?php echo $UPD->last_update ?></strong>
        </div>
    </div>
    <div class='row fieldRow'>
        <div class="col-sm-2">
            <label>Current Version</label>
        </div>
        <div class='col-sm-6'>
            <strong><?php echo $UPD->version ?></strong>
        </div>
    </div>
    <?php if ($UPD->updateEnable) { ?>
        <div class='row fieldRow'>
            <div class="col-sm-2">
                <label>Available</label>
            </div>
            <div class='col-sm-6'>
                <strong>Version: <?php echo $UPD->up_version ?></strong><br>
                <p>Released Date: <?php echo $UPD->release_date() ?></p>
                <p>Changed Log:</p><hr>
                <p><?php echo $UPD->change_log() ?></p><br>
                <button type="button" class="btn btn-cms-primary" onclick="update(this)">Update Now</button>
            </div>
        </div>
        <script>
            function update(_this) {
                $(_this).html("<i class='fas fa-circle-notch fa-spin'></i> Updating...");
                get_return('update-cms', function(res) {
                    var obj = JSON.parse(res);
                    if (obj['error'] == 0) {
                        //alert(obj['msg']);
                        $(_this).html("<i class='fas fa-check'></i> Updated");
                        msg(obj['msg'], "G");
                        window.location.reload();
                    }
                })
            }
        </script>
        <?php
    } else {
        
    }
    ?>

    <?php
}

//Deshboard
function dashboard() {
    global $metabox;
    ?>
    <h1>Dashboard</h1>
    <hr>
    <div class="row">
        <?php
        $metabox->GetMetaBoxes(array('position' => "dashboard"));
        ?>
    </div>
    <?php
}

//Deshboard
function users() {
    global $DB;
    ?>
    <h1>User Manage &nbsp;&nbsp;<a href="index.php?c=user&m=form" w="350" class="fBox btn btn-cms-primary">Add New</a></h1>
    <hr>
    <div id="DataLIst"></div>
    <script>
        $(document).ready(function() {
            load_list();
        });
        function load_list(u) {
            if (u) {
                u = u;
            } else {
                u = ""
            }
            var url = "?c=user&m=userList&" + u;
            get_list(url);
        }
        function get_list(url, type) {
            var url = "index.php" + url;
            $.ajax({
                url: url,
                //method: "GET",
                //async: true,
                //processData: true,
                beforeSend: function() {
                    // $("#wait").hide();
                    // $(".refresh").addClass("fa-spin");
                },
                complete: function() {
                    // var delay = 1; //1 second
                    // setTimeout(function() {
                    //    $(".refresh").removeClass("fa-spin");
                    //  }, delay);
                    // $("tbody[jput='entityData'] tr td").fadeIn();
                },
                cache: true,
                success: function(res) {
                    $('#DataLIst').html(res);
                },
            });
            fBox();
        }
    </script>
    <?php
}
