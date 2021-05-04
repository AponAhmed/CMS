<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//global $listFields;
//$_SESSION['lng'] = 'EN';
unset($_SESSION['texo']);
global $C_POST_TYPE;
$PTyp = array();
foreach ($C_POST_TYPE as $k => $cp) {
    $PTyp[$k] = $cp['label'];
}
?>
<input type="hidden" id="CPT" value="<?php echo $_GET['post-type'] ?>">
<input type="hidden" id="PT" value='<?php echo json_encode($PTyp) ?>'>
<h1><?php echo custom_post_admin_title(); ?><a href="?l=new-page&post-type=<?php echo post_type() ?>" class='addBtn btn btn-cms-primary'>Add New</a> &nbsp;&nbsp;&nbsp;<a href="#" onclick='load_list()' class="float-right refresh-btn"><i class="fal fa-sync"></i></a></h1>
<hr>
<?php
if (class_exists('siteLanguages')) {
    $lngs = new siteLanguages();
    $lngs->langSwitch();
}
?>
<div id="DataLIst"></div>

<script>
    $(document).ready(function() {
        load_list("st=published");
    });

    function load_list(u) {
        if (u) {
            u = u;
        } else {
            u = ""
        }
        var typeUrl = "post-type=<?php echo $_GET['post-type'] ?>";
        var url = "?loadList=<?php echo $_GET['l'] ?>&" + typeUrl + "&" + u;
        get_list(url);

    }

    function get_list(url, type) {
        var url = "index.php" + url;
        //$(".refresh-btn i").addClass("fa-spin");

        $.ajax({
            url: url,
            //method: "GET",
            //async: true,
            //processData: true,
            beforeSend: function() {
                // $("#wait").hide();
                $(".refresh-btn").addClass("fa-spin");
            },
            complete: function() {
                // var delay = 1; //1 second
                // setTimeout(function() {
                //$(".refresh-btn i").removeClass("fa-spin");
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



