<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<h2>Plugins
    <!--<button class='btn btn-cms-primary' onclick='fbox(this)' load="c=forms&m=plugins_upload" w='1020' h='500'>Add New</button>-->
    <a class='btn btn-cms-primary' href="?l=addnewplugins">Add New</a>

</h2>
<br>
<div id="DataLIst">

</div>
<?php // $LIST->pages(); ?>
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
        var url = "?plugins-list&" + u;
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
    }
</script>

