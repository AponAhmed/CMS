<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$typeOptions = get_post_type_option();
if ($POST['ID'] == get_option("blog_page")) {
    $typeOptions['editor_show'] = false;
}

$style = "<style>";
if (isset($typeOptions['editor_show']) && !$typeOptions['editor_show']) {
    $style.="#cke_PostEditor{display:none}";
}
if ($typeOptions === false) {
    $style.="#pageTitle{display:none}";
}
if (isset($typeOptions['editor_permalink_show']) && !$typeOptions['editor_permalink_show']) {
    $style.=".perLink{display:none}";
}
$style .= "</style>";
echo $style;
?>
<h1>Update <?php echo custom_post_admin_title(); ?><a href="?l=new-page&amp;post-type=<?php echo $_GET['post-type'] ?>" class="addBtn btn btn-cms-primary">Add New</a><a href="javascript:" onclick="goBack()" class='addBtn btn btn-cms-default'>Back</a></h1>
<hr>
<form method="post" id='PostForm'>
    <div class="postCrBody row">
        <div class="postLeft col-sm-8 col-md-9">
            <div class="Editor">
                <input name="data[post_title]" value="<?php echo $POST['post_title'] ?>" id="pageTitle" type="text" class="form-control postTitle" placeholder="Write The Title">

                <div class='perLink'>
                    <div class="PageSlug">
                        Permalink : <?php
                        //echo domain()
                        $permalink = get_link($POST['ID']);
                        $pLinkPart = array_filter(array_map('trim', explode('/', $permalink)));
                        $lastIndex = count($pLinkPart);
                        //var_dump($pLinkPart,$lastIndex);
                        if (is_home() != $POST['ID']) {
                            $permalink = str_replace($pLinkPart[$lastIndex], "", $permalink);
                        }
                        $permalink = trim_slash($permalink);

                        // echo strlen($permalink) > 35 ?substr($permalink,0,10)."...".substr($permalink,-25):$permalink;
                        ?>
                        <a target="_blank" href="<?php echo get_link($POST['ID']); ?>"><?php echo strlen($permalink) > 35 ? substr($permalink, 0, 10) . "..." . substr($permalink, -25) : $permalink; ?></a>
                        <input type="hidden" id="slugPrefix" value="<?php echo $permalink ?>" >
                        <input type="text" name="data[post_name]" value="<?php echo $POST['post_name'] ?>" id="EditSlug" class="form-control-sm postSlug">
                    </div>
                    <div class="mkHome"><input type="checkbox" <?php echo is_home() == $POST['ID'] ? "checked" : "" ?> id="makeDefault" onchange="makeHome(this)"><label for="makeDefault"> &nbsp;Make Home</label></div>
                    <span class="seoCount"><span id="SlugCount"></span> / 100 char.</span>
                </div>

                <?php
                if (post_type() != "attachment") {
                    ?>
                    <textarea style="display:none"  name="data[post_content]" rows="15" id="PostEditor"><?php echo $POST['post_content'] ?></textarea>
                    <?php
                } else {
                    if (url_exists($POST['guid'])) {
                        $imgInfo = getimagesize($POST['guid']);
                        if (!$imgInfo) {
                            $inf = pathinfo($POST['guid']);
                            if ($inf['extension'] == 'webp') {
                                $imgInfo = __getimagesize($POST['guid']);
                            }
                        }
                        if ($imgInfo) {
                            $src = get_attachment_src($POST['guid']);
                            echo "<img src='$src' class='img-fluid'>";
                        } else {
                            $inf = pathinfo($POST['guid']);
                            echo findIcon("." . $inf['extension'], "10x");
                        }
                    }
                }
                ?>
            </div>
            <div class="EditorBottomArea">
                <?php
                editor_bottom('post');
                ?>
            </div>
            <div class='MetaBoxParent'>
                <?php
                $metabox->GetMetaBoxes(array('type' => post_type()));
                ?>
            </div>
        </div>
        <div class="postLeft col-sm-4 col-md-3">
            <div class='MetaBoxParent'>
                <?php
                $metabox->GetMetaBoxes(array('type' => post_type(), 'position' => "side"));
                ?>
            </div>
        </div>
    </div>
    <input type='hidden' id="ID" value="<?php echo $POST['ID'] ?>" name="ID">
    <input type='hidden' value="<?php echo post_type() ?>" name="data[post_type]">
</form>
<script>

    $(document).ready(function() {
        $("#pageTitle").change(function() {
            autoSave_()
        })
        Editor(PostEditor, 'basic', 320);
        var timeoutId
        CKEDITOR.instances['PostEditor'].on('change', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(function() {
                autoSave_();
            }, 1000);
        });
    })
    function autoSave_() {
        CKEDITOR.instances['PostEditor'].updateElement();
        url = "index.php?post_auto_save";
        $.ajax({
            method: "POST",
            url: url,
            data: $(PostForm).serialize(),
            async: true,
            processData: true,
            cache: false,
            dataType: 'json',
            success: function(res) {
                if (res['redrict']) {
                    redirect(res['redrict']);
                }
                if (res['ID'] !== "") {
                    $("#ID").val(res['ID']);
                    $("#EditSlug").val(res['slug']);
                    $("#del").show();
                    $("#preview").show();
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                //alert(xhr.status);
                //alert(thrownError);
                //alert(ajaxOptions);
            }
        });
        if (typeof updatedensity === 'function') {
            updatedensity();
        }
    }


    function Editor(id, type, h) {
        //CK Editor Initialize.
        //@param1-Selecor ID.
        //@param2-tools type(basic,full).
        CKEDITOR.replace(id, {
            language: 'en',
            uiColor: '#ffffff',
            customConfig: '',
            resize_enabled: 'false',
            //toolbar: 'insert,insertMed'
        });


        CKEDITOR.config.imageUploadUrl = 'index.php?inline';
        CKEDITOR.config.extraPlugins = 'wordcount,uploadimage', 'uploadwidget', 'notificationaggregator', 'notification', 'toolbar', 'button', 'filetools', 'dialogui', 'widget', 'widgetselection';
        CKEDITOR.config.height = h + 'px';
        CKEDITOR.config.removePlugins = 'elementspath';
        CKEDITOR.config.allowedContent = {
            $1: {
                // Use the ability to specify elements as an object.
                elements: CKEDITOR.dtd,
                attributes: true,
                styles: true,
                classes: true
            }
        };
        CKEDITOR.config.disallowedContent = 'script; *[on*]';
        if (type == null || type == "basic") {

            //if tools type basic than----
            CKEDITOR.config.extraPlugins = 'acolumn,mylink,wordcount';
            CKEDITOR.config.toolbarGroups = [
                {name: 'clipboard', groups: ['clipboard', 'undo']},
                {name: 'forms', groups: ['forms']},
                {name: 'styles', groups: ['styles']},
                {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
                {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']},
                {name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing']},
                {name: 'insert', groups: ['insert']},
                {name: 'colors', groups: ['colors']},
                {name: 'tools', groups: ['tools']},
                {name: 'others', groups: ['others']},
                {name: 'about', groups: ['about']},
                {name: 'document', groups: ['mode', 'document', 'doctools', 'insert']}
            ];

            CKEDITOR.config.removeButtons = 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Subscript,Superscript,CopyFormatting,RemoveFormat,NumberedList,Outdent,Indent,CreateDiv,BidiLtr,Language,BidiRtl,Anchor,Flash,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,ShowBlocks,About,Undo,Redo,Strike,Maximize,Table,Styles,Font,Link,Unlink';
            // CKEDITOR.config.toolbar = ['insert','insertMed'];
            //BulletedList,Blockquote

        } else if (type == "document") {
            //if tools type Full than----
            CKEDITOR.config.toolbarGroups = [
                {name: 'clipboard', groups: ['clipboard', 'undo']},
                {name: 'forms', groups: ['forms']},
                {name: 'styles', groups: ['styles']},
                {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
                {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']},
                {name: 'links', groups: ['links']},
                {name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing']},
                {name: 'insert', groups: ['insert']},
                {name: 'colors', groups: ['colors']},
                {name: 'tools', groups: ['tools']},
                {name: 'others', groups: ['others']},
                {name: 'about', groups: ['about']},
                {name: 'document', groups: ['mode', 'document', 'doctools']}
            ];
            CKEDITOR.config.removeButtons = 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Subscript,Superscript,CopyFormatting,RemoveFormat,NumberedList,Outdent,Indent,CreateDiv,BidiLtr,Language,BidiRtl,Anchor,Flash,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,ShowBlocks,About,Undo,Redo,Strike,Maximize,Table,Styles,Font,Link,Unlink';
        }
        //CKEDITOR.config.extraPlugins = 'imagepaste'; image past
    }



//    
////****No need beutify for cke initialize script***
//var cke;
//ClassicEditor
//.create(document.querySelector('#PostEditor'), {
//    toolbar: ['headings', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'imageTextAlternative', 'imageStyleFull', ],
//    image: {
//        
//    },
//    extraPlugins: 'easyimage'
//}).then( editor => {
//    //console.log( 'Editor was initialized', editor );
//    cke = editor;
//}).catch( err => {
//    console.error( err.stack );
//});
////****
//No need beutify for cke initialize script***
//
//    $(document).ready(function() {
//         $("#del").hide();
//         $("#preview").hide();
//        //
//    //auto save post 
//    //
//    $("#pageTitle").change(function() {
//        autoSave()
//    })
//    var timeoutId
//    cke.document.on( 'change', () => {
//        //console.log( 'The Document has changed!' );
//        $("#PostEditor").val(cke.getData());
//            clearTimeout(timeoutId);
//            timeoutId = setTimeout(function() {
//             var postTitle = $("#pageTitle");
//             if (postTitle.val() != "") {
//               autoSave();
//           }
//            }, 2000);
//    });
//    
//
//    });


</script>
