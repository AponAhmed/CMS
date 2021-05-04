<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//global $listFields;
//get_attachment_src($guid)
?>
<h1 class='text-capitalize'><?php echo custom_post_admin_title() . " " . $_GET['tex'] ?>&nbsp;&nbsp;<button onclick="addTexo('<?php echo $_GET['tex'] ?>')" class="btn btn-cms-primary"><i class='fas fa-plus-circle'></i>&nbsp;Add New</button></h1>
<hr>
<!--<div class="col-sm-3">
<?php
/// $form = new forms_class();
// $form->texonomy($_GET['tex'])
?>
</div>-->
<div id="texoEdit"></div>

<div class='row'>
    <div class="col-sm-12">
        <div id="DataLIst"></div>
    </div>
</div>

<?php // $LIST->pages(); ?>
<script>
    $(document).ready(function() {
        load_list();
<?php
if (isset($_GET['id'])) {
    echo "EditTexo('$_GET[tex]',$_GET[id]);";
}
?>

    });

    function load_list(u) {
        if (u) {
            u = u;
        } else {
            u = ""
        }
        var typeUrl = "tex=<?php echo $_GET['tex'] ?>";
        var url = "?loadList=<?php echo $_GET['l'] ?>&" + typeUrl + "&" + u;
        get_Tlist(url, DataLIst);
    }

    function get_Tlist(url, id) {
        var url = "index.php" + url;
        //console.log(url);
        $.ajax({
            url: url,
            //method: "GET",
            async: true,
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
                //console.log(id);
                $(id).html(res);
            },
        });
    }

    function addTexo(texo) {
        var data = {cls: 'forms_class', m: "texonomy_add", texo: texo};
        jQuery.post('index.php', data, function(response) {
            $.fancybox.open(response);
        });
    }
    function EditTexo(texo, id, _this) {
        var loader = "<span class='spinLoader'></span>";
        $(_this).html(loader);
        var data = {cls: 'forms_class', m: "texonomy_edit", texo: texo, id: id};
        jQuery.post('index.php', data, function(response) {
            // $.fancybox.open(response);
            $(_this).html("Edit");
            $("#texoEdit").html(response);
            $(window).scrollTop(0);
            $("#DataLIst").hide();

//New Data====
//var typeUrl = "tex=<?php //echo $_GET['tex']                        ?>";
//var url = "?loadList=<?php // echo $_GET['l']                       ?>&" + typeUrl;
//get_Tlist(url, NewList);
//remove old
//$("#DataLIst").html("");
//Editor======
            Editor(PostEditor, 'basic', 320);
            var timeoutIdF
            CKEDITOR.instances['PostEditor'].on('change', function() {
                CKEDITOR.instances['PostEditor'].updateElement();
                clearTimeout(timeoutIdF);
                timeoutIdF = setTimeout(function() {
                    //Data parse event 
                }, 1000);
            });


        });
    }
//<div class='popBg'><div class='popUp'>
    function SaveTexo(_this) {
        $(_this).append(loader);
        post_return("", $('#termAdd').serialize(), function(res) {
            $(_this).find('.spinLoader').remove();
            res = JSON.parse(res);
            if (res['error'] == "") {

                msg(res['msg'], "G");
                //$('#termAdd')[0].reset();
                //$('#termAdd').trigger("reset");
                load_list();
                //$("#texoEdit").html("");
                //$("#DataLIst").show();
                $.fancybox.close();

            } else {
                msg(res['msg'], "R");
            }
        })
    }
</script>



