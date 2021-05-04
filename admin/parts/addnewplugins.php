<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<h2>New Plugins
    <!--<button class='btn btn-cms-primary' onclick='fbox(this)' load="c=forms&m=plugins_upload" w='1020' h='500'>Add New</button>-->
    <a class='btn btn-cms-default' href="?l=plugin">Back</a>

</h2>
<br>
<ul class="nav nav-tabs" id="pluginAddTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="downloadFstore-tab" data-toggle="tab" href="#downloadFstore" role="tab" aria-controls="downloadFstore" aria-selected="false">From Store</a>
    </li>
    <li class="nav-item">
        <a class="nav-link " id="uploadPlugin-tab" data-toggle="tab" href="#uploadPlugin" role="tab" aria-controls="uploadPlugin" aria-selected="true">Upload</a>
    </li>
    <li class="searchPlugin">
        <input type="text" id="PluginSearch" class="form-control form-control-sm" placeholder="Search">
        <span class="searchCancel collapse" onclick="plsearchCancel(this)">×</span>
    </li>
</ul>

<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade" id="uploadPlugin" role="tabpanel" aria-labelledby="uploadPlugin-tab">
        <div class="uploaderArea">
            <label id='browseTrig' for="file"><i class="fas fa-upload"></i><br>Upload (.zip) file</label>
            <input id="file" onchange="themeUpload(this, 'plugin')" type="file" class="collapse">
        </div>
        <div class="thm-progress"><div></div></div>
        <div class="afterUpload"></div>
    </div>
    <div class="tab-pane fade show active" id="downloadFstore" role="tabpanel" aria-labelledby="downloadFstore-tab">
        <div id="DataLIst">
            <span class='bodyLoader'></span>
        </div>
    </div>
</div>

<?php // $LIST->pages(); ?>
<script>
    $(document).ready(function() {
        load_list();
        $("#PluginSearch").keyup(function() {
            var q = $(this).val();
            if (q != "") {
                $('.searchCancel').show();
                $(".singlePlugin").each(function() {
                    var str = $(this).attr('data-plugins-info');
                    var r = new RegExp(q, "i");
                    if (str.search(r) < 0) {
                        $(this).parent().addClass('collapse');
                    } else {
                        $(this).parent().removeClass('collapse');
                    }
                });
            } else {
                $(".singlePlugin").parent().removeClass('collapse');
                $('.searchCancel').hide();

            }
        });


    });

    function plsearchCancel(_this) {
        $(_this).hide();
        $('#PluginSearch').val('');
        $(".singlePlugin").parent().removeClass('collapse');
    }

    function load_list(u) {
        if (u) {
            u = u;
        } else {
            u = ""
        }
        var url = "?c=forms&m=plugins_upload" + u;
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
                //$('#DataLIst').html(loaderBig);
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
<script>
    function downloadPlugin(basename, _this, typ) {
        if (typ == 'update') {
            $(_this).html('Updating...');
        } else {
            $(_this).html('Downloading...');
        }
        var data = {ajx_action: "UpdateDownload", basename: basename};
        jQuery.post('index.php', data, function(response) {
            if (response == "1") {
                if (typ == 'update') {
                    msg("Plugin Updateed", 'G');
                    load_list();
                    setTimeout(function() {
                        jQuery(_this).hide('fast');
                    }, 1000);
                } else {
                    msg("Plugin Download", 'G');
                    load_list();
                    setTimeout(function() {
                        jQuery(_this).hide('fast');
                        $(_this).parent().find('.storeActBtn').show('fast');
                    }, 1000);
                }
            } else {
                alert(response);
            }
            //alert(response);
        });

    }
</script>

