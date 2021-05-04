<?php defined('ABSPATH') OR exit('No direct script access allowed');  ?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//var_dump(getTbleField());
?>
<h1>Media Manager &nbsp;&nbsp;<a href="?l=new-media&post-type=attachment" class='btn btn-cms-primary'>Add New</a><a href="#" onclick="load_list()" class="float-right refresh-btn"><i class="fas fa-sync"></i></a></h1>
<hr>
<div id="library"></div>
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
        var url = "?load=<?php echo $_GET['l'] ?>&" + u;

        url = "index.php" + url;
        $.ajax({
            url: url,
            //method: "GET",
            //async: true,
            //processData: true,
            beforeSend: function() {
                // $("#wait").hide();
                $(".admin-inner").append(loaderBig);
            },
            complete: function() {
                $(".admin-inner .bodyLoader").remove();
            },
            cache: true,
            success: function(res) {
                $('#library').html(res);

            },
        });

    }
</script>

