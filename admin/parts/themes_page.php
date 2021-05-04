<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$THEME->themes();
$themes = $THEME->themes;
$curThemeIndex = find_array_with_val($themes, current_theme_dir(), "dir");
//var_dump($curThemeIndex);
?>
<h2>Themes
    <button class='btn btn-cms-primary' onclick='fbox(this)' load="c=forms&m=theme_upload" w='500' h='300' title='Add Theme'>Upload New</button>
    <button type="button" class="float-right btn btn-cms-default" onclick="themeUpdateCheck(this)">Check Update</button>
</h2>
<!--<a class='btn btn-cms-primary' data-options='{"caption" : "My caption", "src" : "index.php?", "type" : "iframe"}' data-fancybox="" data-width="300" data-height="300" data-src="index.php?" data-type="ajax" href="javascript:;">Add New</a>-->
<hr>
<ul class="nav nav-tabs" id="themeTab" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#themes" role="tab" aria-controls="themes" aria-selected="true">Themes</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#themeStore" role="tab" aria-controls="themeStore" aria-selected="false">Store</a>
    </li>
    <li class="searchPlugin">
        <input type="text" id="themesearch" class="form-control form-control-sm" placeholder="Search">
        <span class="searchCancel collapse" onclick="plsearchCancel(this)">×</span>
    </li>

</ul>
<div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active" id="themes" role="tabpanel" aria-labelledby="themes-tab">
        <div class="themesPreviewer row">
            <?php
            foreach ($themes as $k => $theme) {
                if ($k != $curThemeIndex) {
                    continue;
                }
                ?> 
                <div class='col-4'>
                    <div class='single-theme' data-theme-info="<?php echo $theme['dir'] . " " . $theme['Theme Name'] ?>" id="<?php echo $theme['dir'] ?>">
                        <div class="updateBar collapse">
                            <a href="javascript:" onclick="updateTheme('<?php echo $theme['dir'] ?>', this, 'update')" class="updateButton">Update</a>
                            <a href='javascript:' onclick="updateInfo('<?php echo $theme['dir'] ?>')" class="updateButton up_info">Info</a>
                        </div>

                        <img class='themeScreen' src="<?php echo $theme['screen'] ?>">
                        <div class="theme-title">
                            <label>Active : <?php echo $theme['Theme Name'] ?></label>
                            <a href="?l=theme_option" class="btn btn-cms-primary float-right">Customize</a>
                        </div>
                    </div> 
                </div>
            <?php } ?>
            <?php
            foreach ($themes as $k => $theme) {
                if ($k == $curThemeIndex) {
                    continue;
                }
                ?> 
                <div class='col-4'>
                    <div class='single-theme' data-theme-info="<?php echo $theme['dir'] . " " . $theme['Theme Name'] ?>" id="<?php echo $theme['dir'] ?>">
                        <div class="updateBar collapse">
                            <a href="javascript:" onclick="updateTheme('<?php echo $theme['dir'] ?>', this, 'update')" class="updateButton">Update</a>
                            <a href='javascript:' onclick="updateInfo('<?php echo $theme['dir'] ?>')" class="updateButton up_info">Info</a>
                        </div>

                        <img class='themeScreen' src="<?php echo $theme['screen'] ?>">
                        <div class="theme-title">
                            <label><?php echo $theme['Theme Name'] ?></label>
                            <div class='float-right'>
                                <button type="button" onclick="thmDel('<?php echo base64_encode($theme['dir']) ?>')" class="btn text-danger collapse deleteTheme"><i class="fas fa-trash-alt"></i></button>
                                <button type="button" class="btn btn-cms-primary" onclick="themeActive('<?php echo $theme['dir'] ?>', this)">Active</button>
                            </div>
                        </div>
                    </div> 
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="tab-pane fade" id="themeStore" role="tabpanel" aria-labelledby="themeStore-tab">
        <?php $THEME->store(); ?>
    </div>

</div>
<script>
    function thmDel(dir) {
        var c = confirm("Are you sure, You want to delete This Theme ?");
        if (c) {
            $.ajax({
                url: 'index.php?thmDel=' + dir,
                method: "GET",
                async: true,
                processData: true,
                cache: false,
                dataType: 'json',
                success: function(res) {
                    if (res.error == 0) {
                        msg(res.msg, 'G');
                        setTimeout(function() {
                            window.location.reload();
                        }, 500);

                    } else {
                        msg(res.msg, 'R');
                    }
                } //var res = Act('thmDel=' + dir, false, true);
                //console.log(res);
                //window.location.reload();
            });
        }

    }

    function themeActive(T_name, _this) {
        $(_this).append(loader);
        get_return("activeTheme=" + T_name, function(res) {
            if (res == '1') {
                window.location.reload();
            }
        })
    }
//    $(document).ready(function() {
//        //$.fancybox.open('<div class="message"><h2>Hello!</h2><p>You are awesome!</p></div>');
//    })
</script>
<script>
    $(document).ready(function() {
        //themeUpdateCheck();
        $("#themesearch").keyup(function() {
            var q = $(this).val();
            if (q != "") {
                $('.searchCancel').show();
                $(".single-theme").each(function() {
                    var str = $(this).attr('data-theme-info');
                    var r = new RegExp(q, "i");
                    if (str.search(r) < 0) {
                        $(this).parent().addClass('collapse');
                    } else {
                        $(this).parent().removeClass('collapse');
                    }
                });
            } else {
                $(".single-theme").parent().removeClass('collapse');
                $('.searchCancel').hide();

            }
        });

    });

    function plsearchCancel(_this) {
        $(_this).hide();
        $('#themesearch').val('');
        $(".single-theme").parent().removeClass('collapse');
    }

    function themeUpdateCheck(_this) {
        $(_this).html(loader + " Checking...");
        //alert(pdata);
        var url = '';
        var url = "index.php" + url; // the script where you handle the form input.
        var fd = {ajx_action: 'themeUpdateCkeck'};
        jQuery.ajax({
            type: "POST",
            url: url,
            data: fd, // serializes the form's elements.
            success: function(data)
            {
                $(_this).html("Check Update");
                var obj = JSON.parse(data);
                for (var prop in obj) {
                    var id = obj[prop];
                    $("#" + id).find('.updateBar').show();
                }
            }
        });
    }

    function updateTheme(basename, _this, typ) {

        if (typ == 'update') {
            $(_this).html('Updating...');
        } else {
            $(_this).html('Downloading...');
        }


        var data = {ajx_action: "themeDownload", basename: basename};
        jQuery.post('index.php', data, function(response) {
            if (response == "1") {
                if (typ == 'update') {
                    msg("Theme Updateed", 'G');
                    //location.reload();
                    $("#" + basename).find('.updateBar').hide();
                    setTimeout(function() {
                        jQuery(_this).hide('fast');
                    }, 1000);

                } else {
                    msg("Theme Downloaded", 'G');
                    //location.reload();
                    setTimeout(function() {
                        jQuery(_this).closest('.single-theme').parent().hide('fast');
                        window.location.reload();
                    }, 1000);
                }
            } else {
                alert(response);
            }
            //alert(response);
        });

    }

    function updateInfo(basename) {
        var data = {ajx_action: "themeUpdateinfo", basename: basename};
        jQuery.post('index.php', data, function(response) {
            $.fancybox.open(response);
        });
    }
</script>