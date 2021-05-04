/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var conn = true; //connection check 
var loader = "<span class='spinLoader'></span>";
var loaderBig = "<span class='bodyLoader'></span>";
$(document).ready(function() {
//    var tid;
//    $('#pageTitle').keyup(function() {
//        _this = $(this);
//        clearTimeout(tid);
//        tid = setTimeout(function() {
//            $(_this).val(badWordFilter($(_this).val()," "));
//        }, 1000);
//    })


    $(".perLink").after('<button type="button" title="Make Copy" class="makeCopy" onclick="makeCopy(this)"><i class="fas fa-copy"></i></button>');
    //poll();
//    $('.admin-menus li').mouseenter(function() {
//        $(this).find('.admin-subMenu').slideDown('fast');
//    })
//    $('.admin-menus li').mouseleave(function() {
//        $(this).find('.admin-subMenu').slideUp();
//    })
//    //sidebar
//    var windH = $(window).height();
//    var rSideH = $('.admin-inner').height();
//    if (rSideH > windH) {
//        $('.admin-sidebar').height(rSideH + 30);
//    } else {
//        $('.admin-sidebar').height(windH);
//    }
//
//    $('.admin-sidebar').height($('.admin-inner').height() + 30);
    fBox();
    if (jQuery('#slugPrefix').length > 0) {
        slugCounter();
    }
    $("#EditSlug").keyup(function() {
        slugCounter()
    });
    editorFilter();
});
function editorFilter() {
    for (var id in CKEDITOR.instances) {
        CKEDITOR.instances[id].on('focus', function(e) {
            // Fill some global var here
            $('#editorInstance').val(e.editor.name);
            e.editor.filter.addTransformations([
                [
                    'img: sizeToAttribute',
                    'img[width,height]: sizeToAttribute'
                ]
            ]);
        });
    }

}



function currentEditorInstance() {
    return $('#editorInstance').val();
}


function badWordFilter(str, glu) {
    var words = str.split(glu);
    var filtered = words.filter(function(el) {
        return el != "";
    });
    var badWord = ['about', 'We', 'after', 'ago', 'all', 'also', 'an', 'and', 'any', 'are', 'as', 'at', 'be', 'been', 'before', 'both', 'but', 'by', 'can', 'did', 'do', 'does', 'done', 'edit', 'even', 'every', 'for', 'from', 'had', 'has', 'have', 'he', 'here', 'him', 'his', 'however', 'if', 'in', 'into', 'is', 'it', 'its', 'less', 'many', 'may', 'more', 'most', 'much', 'my', 'no', 'not', 'often', 'quote', 'of', 'on', 'one', 'only', 'or', 'other', 'our', 'out', 're', 'says', 'she', 'so', 'some', 'soon', 'such', 'than', 'that', 'the', 'their', 'them', 'then', 'there', 'these', 'they', 'this', 'those', 'though', 'through', 'to', 'under', 'use', 'using', 've', 'was', 'we', 'were', 'what', 'where', 'when', 'whether', 'which', 'while', 'who', 'whom', 'with', 'within', 'you', 'your', 'http', 'www', 'wp', 'href', 'target', 'blank', 'image', 'class', 'size', 'src', 'img', 'alignleft', 'title', 'info', 'content', 'uploads', 'jpg', 'alt', 'h3', 'width', 'height', '150', '2010', '2009', '10', '1', '2', '3', '4', '5', '6', '7', '8', '9', '11', 'com', 'net', 'info', 'map', '150x150', 'thumbnail', 'param', 'name', 'value', 'will', 'am', '202', 'retouch', '&amp;', 'amp', 'like', 'etc.', 'nbsp', 'â'];
    var retStr = "";
    filtered.forEach(function(val) {
        // execute something
        if (badWord.includes(val)) {
            console.log('Word: "' + val + '" Removed');
        } else {
            retStr += val + " ";
        }
    });
    return retStr;
}

function makeCopy(_this) {
    var id = $("#ID").val();
    var data = {ajx_action: "makeCopy", ID: id};
    jQuery.post('index.php', data, function(response) {
        var obj = jQuery.parseJSON(response);
        if (obj['red'] !== "") {
            window.open(obj['red'], '_new');
            //window.location.href = obj['red'];
        } else {
            msg(obj['msg'], "R");
        }
    });
}

function slugCounter() {
    var slugTxt = jQuery('#EditSlug').val();
    var slugPrefix = jQuery('#slugPrefix').val().length;
    $("#SlugCount").html(slugTxt.length + slugPrefix);
    if ((slugTxt.length + slugPrefix) > 100) {
        jQuery('#SlugCount').css('color', 'red');
    } else {
        jQuery('#SlugCount').removeAttr('style');
    }
}

$(window).on("load", function() {
//$('.admin-sidebar').height($('.admin-inner').height() + 30);
});
//$(window).scroll(function() {
//    alert('asd');
//});
//$(window).load(function() {
//    $('.admin-sidebar').height($('.admin-inner').height() + 30);
//    $('.admin-inner').resize(function() {
//        $('.admin-sidebar').height($('.admin-inner').height() + 30);
//    })
//})


function poll() {
    var intVal = setTimeout(function() {
// alert('haa');
        if (typeof HBT === 'function') {
            HBT();
        }
        poll(); //call your function again after successfully calling the first time.
    }, 2000);
//  console.log(intVal);
}


function HBT() {
// alert("Hart bit");
//Act("HBT", false, false);

    var url = "index.php?HBT";
    $.ajax({
        url: url,
        method: "GET",
        async: true,
        processData: true,
        cache: false,
        success: function(res) {
            if (conn == false) {
                // msg("Connection Success.", "G");
                conn = true;
            }
            if (res['redrict']) {
                redirect(res['redrict']);
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            if (xhr.status == 200) {
                // msg(loader + "&nbsp;&nbsp;" + thrownError, "R");
            } else {
                // msg(loader + "&nbsp;&nbsp;Connection lost. " + xhr.status, "R");
            }
            conn = false;
        }
    });
}



//tinymce.init({
//    selector: '#PostEditor',
//    height: 350,
//    menubar: false,
//    plugins: [
//        'advlist autolink lists link image charmap print preview anchor textcolor',
//        'searchreplace visualblocks code fullscreen',
//        'insertdatetime media table contextmenu paste code help wordcount'
//    ],
//    toolbar: 'insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
//    content_css: [
//        '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
//        '//www.tinymce.com/css/codepen.min.css']
//});






$(document).ready(function() {
//$(".drag").draggable();
//menu item toggle option
    mRemInit()
//
//MetaBox collaps ---
//
    $(".meta-box-triger a").click(function() {
        var targetBody = $(this).closest('.metaBox').find(".meta-box-content");
        targetBody.slideToggle('fast', function() {
            if ($(targetBody).is(':hidden')) {
//alert("false");
                $(this).parent().removeClass("in");
            } else {
                $(this).parent().addClass("in");
            }
        });
    })

})


function CaptionChange(_this) {
    var str = $(_this).val();
    if (str == '') {
        $(_this).closest(".item").find(".menuLabel").html($(_this).closest(".item").find(".menuLabel").attr('org-val'));
    } else {
        $(_this).closest(".item").find(".menuLabel").html(str);
    }

}

function clps() {
    $(".has_nest").unbind('click');
    $(".has_nest").click(function() {
//alert('clicked');
        var $targ = $(this).parent().parent().find("> .nest");
        $targ.toggle();
        //console.log($($targ).is(":visible"));
        if ($targ.is(":visible")) {
            $(this).css({"transform": "rotate(180deg)", "transition": "all .2s"});
        } else {
            $(this).css({"transform": "rotate(0deg)", "transition": "all .2s"});
        }

    });
    $(".has_nest").each(function() {
        var targ = $(this).parent().parent().find("> .nest > li").length;
        if (targ > 0) {
            $(this).addClass('on');
        } else {
            $(this).removeClass('on');
        }
    });
}

function mRemInit() {
    if (typeof clps === "function") {
        clps();
    }
    $(".removeMenuItem").click(function() {
        $(this).closest('.item').parent().remove();
        filterItem();
    });
}
function mClps() {
    $('.menuItemOpTg').unbind('click');
    $(".menuItemOpTg").click(function() {
        var targetBody = $(this).closest('.item').find(".itemOption");
        targetBody.slideToggle('fast', function() {
            if ($(targetBody).is(':hidden')) {
//alert("false");
                $(this).parent().removeClass("in");
            } else {
                $(this).parent().addClass("in");
            }
        });
    })

}

function autoSave(_this) {
    setTimeout(function() {
        saveDraft(_this);
    }, 2000);
}

function saveDraft(_this) {
    $(_this).after(loader);
    var postTitle = $("#pageTitle");
    var eDitor = cke.getData();
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
            $(_this).parent().find(".spinLoader").remove();
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
}

function discardPost() {

}

//========================
function Post(frm, url, _this, ret) {
    $(_this).before(loader);
    url = "index.php?" + url;
    var resp;
    $.ajax({
        method: "POST",
        url: url,
        data: $(frm).serialize(),
        async: true,
        processData: true,
        cache: false,
        dataType: 'json',
        success: function(res) {
            if (ret) {
                resp = res;
            } else {
                $(_this).parent().find(".spinLoader").remove();
// alert(res);
                if (res['error']) {
                    if (res['msg'] !== "") {
                        msg(res['msg'] + res['error'], "R");
                    }
                } else {
                    if (res['msg'] !== "") {
                        msg(res['msg'], "G");
                    }
                    if (res['redirect']) {
                        redirect(res['redirect']);
                    }
                }
            }

        },
        error: function(xhr, ajaxOptions, thrownError) {
//alert(xhr.status);
//alert(thrownError);
//alert(ajaxOptions);
        }
    });
    return resp;
}

function msg(txt, type) {
    var _this = $(".msg");
    $(_this).removeAttr("style");
    var icon = "";
    if (txt !== "") {
        var selectedEffect = "pulsate"; //highlight,pulsate
// most effect types need no options passed by default
        var options = {};
// some effects have required parameters
        if (selectedEffect === "scale") {
            options = {percent: 100};
        } else if (selectedEffect === "size") {
            options = {to: {width: 280, height: 185}};
        }
// run the effect
//$(_this).show(selectedEffect, options, 100, callback);
        if (type == "R") {
            $(_this).css("color", "#FF5151");
            icon = "<span class='glyphicon glyphicon-remove'></span>&nbsp;&nbsp;";
        } else if (type == "G") {
            $(_this).css("color", "rgb(82, 188, 82);");
            icon = "<span class='glyphicon glyphicon-saved'></span>&nbsp;&nbsp;";
        }
        $(_this).show("shake", 500);
        $(_this).html("<p>" + txt + "<span class='msgClose' style='float:right;cursor:pointer;color:#fff' onclick=\"$('.msg').hide()\">×</span></p>");
        //}
//callback function to bring a hidden box back
        // function callback() {
        if (type != "R") {
            setTimeout(function() {
//$(_this).slideUp('slow');
                $(_this).fadeOut(); //.fadeOut();explode,shake,bounce,pulsate
            }, 2000);
        }
    }
}

function Act(url, con, retrn) {
    if (con == false) {
        var c = 1;
    } else {
        var c = confirm('Are you sure ?');
    }
    var ret;
    if (c == 1) {
        url = "index.php?" + url
//alert(url);
        $.ajax({
            url: url,
            method: "GET",
            async: true,
            processData: true,
            cache: false,
            dataType: 'json',
            success: function(res) {
                if (retrn === false) {
                    ret = res;
                }
                if (res['redrict']) {
                    redirect(res['redrict']);
                }

                if (retrn == true) {
                    if (res['error']) {
                        msg(res['msg'], "R");
                    } else {
                        if (res['rf']) {
                            msg(res['msg'], "G");
                            load_list(res['rf']);
                        } else {
                            msg(res['msg'], "G");
                            if (typeof load_list === 'function') {
                                load_list();
                            }
                        }
                    }
                }
            },
        });
    }
// console.log(ret);
    return ret;
}

function get_return(url, calback) {
    var url = "index.php?" + url
//alert(url);
    var ret;
    $.ajax({
        url: url,
        method: "GET",
        success: calback,
    });
}

function post_return(url, fData, calback) { //$(frm).serialize()
    url = "index.php?" + url;
    var resp;
    $.ajax({
        method: "POST",
        url: url,
        data: fData,
//        async: true
        processData: false,
//        cache: false,
//dataType: 'json',
        success: calback
    });
}

function multipleDelete(form) {
    multipleAction(form, "", "MDelete", true);
}
function multipleTrash(form) {
    multipleAction(form, "", "MTrash", true);
}
function multiplePublished(form) {
    multipleAction(form, "", "published", true);
}
function multipleDraft(form) {
    multipleAction(form, "", "draft", true);
}

function MltModifieDate(form) {
    var listData = $(form).serialize()
    if (listData == "") {
        msg("At first need to Select a row !!", "R");
    } else {
        $.fancybox.open('<div class="dateModify"><input id="date" type="date" class="form-control form-contron-sm"><button style="margin-top:10px" class="btn btn-cms-primary" id="submitDate">OK</button></div>');
        $("#submitDate").click(function() {
// alert('submitted');
            var data = {
                listdata: listData,
                date: $("#date").val(),
                action: 'dateModify'
            }
            $.ajax({
                method: "POST",
                url: 'index.php',
                data: data,
                async: true,
                processData: true,
                cache: false,
                dataType: 'json',
                success: function(res) {
                    if (res['error']) {
                        if (res['msg'] !== "") {
                            msg(res['msg'], "R");
                        }
                    } else {
                        if (res['msg'] !== "") {
                            msg(res['msg'], "G");
                        }
//RD(res['rd']);
                        if (res['rd']) {
// RQST(res['rd']);
                        }
                        load_list();
                        $.fancybox.close();
                    }
                }
            });
        });
    }
}


function MovePost(form) {
    var listData = $(form).serialize()
    if (listData == "") {
        msg("At first need to Select a row !!", "R");
    } else {
        var PTobj = jQuery.parseJSON($("#PT").val());
        var cur = $("#CPT").val();
        var htm = "<select id='moveTo' class='custom-select custom-select-sm'>";
        $.each(PTobj, function(key, value) {
            if (key != cur) {
                htm += "<option value='" + key + "'>" + value + "</option>"
            }
        });
        htm += "</select>";
        $.fancybox.open('<div class="dateModify" style="width:250px;min-width: inherit;">' + htm + '<button style="margin-top:10px" class="btn btn-cms-primary" id="submitDate">OK</button></div>');
        $("#submitDate").click(function() {
// alert('submitted');
            var data = {
                listdata: listData,
                moveTo: $("#moveTo").val(),
                ajx_action: 'MovePost'
            }
            $.ajax({
                method: "POST",
                url: 'index.php',
                data: data,
                async: true,
                processData: true,
                cache: false,
                dataType: 'json',
                success: function(res) {
                    if (res['error']) {
                        if (res['msg'] !== "") {
                            msg(res['msg'], "R");
                        }
                    } else {
                        if (res['msg'] !== "") {
                            msg(res['msg'], "G");
                        }
//RD(res['rd']);
                        if (res['rd']) {
// RQST(res['rd']);
                        }
                        load_list();
                        $.fancybox.close();
                    }
                }
            });
        });
    }
}


function multipleAction(form, url, action, conf) {
    var listData = $(form).serialize()
    if (listData == "") {
        msg("At first need to Select a row !!", "R");
    } else {
        if (conf == false) {
            var c = 1;
        } else {
            var c = confirm('Are you sure ?');
        }
        if (c == 1) {
            var data = {
                listdata: listData,
                action: action
            }
//encode again encoded url-----
            $.ajax({
                method: "POST",
                url: url,
                data: data,
                async: true,
                processData: true,
                cache: false,
                dataType: 'json',
                success: function(res) {
                    var res = JSON.parse(res);
                    if (res['error']) {
                        if (res['msg'] !== "") {
                            msg(res['msg'], "R");
                        }
                    } else {
                        if (res['msg'] !== "") {
                            msg(res['msg'], "G");
                        }
//RD(res['rd']);
                        if (res['rd']) {
// RQST(res['rd']);
                        }
                        load_list();
                    }
                }
            });
        }

    }
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

            $(".refresh-btn").addClass("fa-spin");
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
            $(".refresh-btn").removeClass("fa-spin");
            $('#DataLIst').html(res);
        },
    });
}


function insMedia(obj) {
//$("#sliderItem").html("");     
    $(obj).each(function() {
        var id = $(this).val();
        var img = $("#img_post_" + id).attr('data-src');
        var altt = $("#img_post_" + id).attr('alt');
        var capt = $("#img_post_" + id).attr('title');
        //var srcSet = $("#img_post_" + id).attr('srcset');
        var sizeSet = $("#img_post_" + id).attr('sizeset');
        var sizesStr = $("#img_post_" + id).attr('sizes');
        var matches = img.match(/(.*)(([\-])([0-9]{3}))([.])([a-zA-Z]{3})/);
        if (matches) {
            img = img.replace(matches[2], "");
        }
// alert(matches[2]);
        var sizesetArr = sizeSet.split(",");
        srcSet = "srcSetG='" + id + "'";
        var added = $("#mdInsSelectedCurrent").val();
        var addFlag = true;
        if (added.indexOf(id) != -1) {
            addFlag = false;
        }

        var instance = $("#editorInstanse").val();
        var editor = CKEDITOR.instances[instance]; //moved from if(addFlag)
        //console.log(instance);
        var imgString = '<img id="obj_img_' + id + '" title="' + capt + '" ' + srcSet + ' sizes="' + sizesStr + '" class="img-fluid" src="' + img + '" alt="' + altt + '" width="' + sizesetArr[0] + '" height="' + sizesetArr[1] + '">'
        //console.log(imgString);


        if ($("#ImgAttOptionEnable").is(":checked")) {
            var colW = (12 / Number($("#clNumber").val()));
            if (addFlag) {
                if ($("#EnableTitleWithImg").is(":checked")) {
                    imgString += "<p>" + capt + "</p>";
                }
                var brdr = "";
                if ($("#EnableBorderWithImg").is(":checked")) {
                    brdr = "borderd";
                }
                editor.insertHtml("<div class='col w" + colW + " col-sm-" + colW + "'><div class='innerColumn " + brdr + "'>" + imgString + "</div></div> ");
                //editor.insertHtml("&nbsp;");
// alert(img);
            }
        } else {
            if (addFlag) {
                editor.insertHtml(formatFactory(imgString));
// alert(img);
            }
        }



    });
}


function formatFactory(html) {
    function parse(html, tab = 0) {
        var tab;
        var html = $.parseHTML(html);
        var formatHtml = new String();
        function setTabs() {
            var tabs = new String();
            for (i = 0; i < tab; i++) {
                tabs += "\t";
            }
            return tabs;
        }

        $.each(html, function(i, el) {
            if (el.nodeName == "#text") {
                if (
                        $(el)
                        .text()
                        .trim().length
                        ) {
                    formatHtml +=
                            setTabs() +
                            $(el)
                            .text()
                            .trim() +
                            "\n";
                }
            } else {
                var innerHTML = $(el)
                        .html()
                        .trim();
                $(el).html(innerHTML.replace("\n", "").replace(/ +(?= )/g, ""));
                if ($(el).children().length) {
                    $(el).html("\n" + parse(innerHTML, tab + 1) + setTabs());
                    var outerHTML = $(el)
                            .prop("outerHTML")
                            .trim();
                    formatHtml += setTabs() + outerHTML + "\n";
                } else {
                    var outerHTML = $(el)
                            .prop("outerHTML")
                            .trim();
                    formatHtml += setTabs() + outerHTML + "\n";
                }
            }
        });
        return formatHtml;
    }

    return parse(html.replace(/(\r\n|\n|\r)/gm, " ").replace(/ +(?= )/g, ""));
}


//formatFactory("asd");

function fbox(_this) {
    var w = $(_this).attr('w');
    var h = $(_this).attr('h');
    var load = $(_this).attr('load');
    var capt = $(_this).attr('title');
    $.fancybox.open({
        src: 'index.php?w=' + w + '&h=' + h + "&" + load, // Source of the content
        type: 'ajax', // Content type: image|inline|ajax|iframe|html (optional)
        opts: {caption: capt}, // Object containing item options (optional)
        afterLoad: function() {
//alert("loaded");
            var content = $(this.opts.$orig[0]).data('content');
            $(".fancybox-div").html(content);
        }

    });
}


function goBack() {
    window.history.back();
}

function redirect(url) {
    if (url == "") {
        window.location.reload();
    } else {
        window.location.href = url;
    }

}


function make_editor(id, h) {
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
    CKEDITOR.config.extraPlugins = 'uploadimage', 'uploadwidget', 'notificationaggregator', 'notification', 'toolbar', 'button', 'filetools', 'dialogui', 'widget', 'widgetselection';
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
//if tools type basic than----
    CKEDITOR.config.extraPlugins = 'acolumn';
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
        {name: 'document', groups: ['mode', 'document', 'doctools', 'insert']}
    ];
    CKEDITOR.config.removeButtons = 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Subscript,Superscript,CopyFormatting,RemoveFormat,NumberedList,Outdent,Indent,CreateDiv,BidiLtr,Language,BidiRtl,Anchor,Flash,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,ShowBlocks,About,Undo,Redo,Strike,Maximize,Table,Styles,Font,link';
    // CKEDITOR.config.toolbar = ['insert','insertMed'];
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
        height: h + 'px',
        // toolbar: ''
    });
    var url = "index.php"

    CKEDITOR.config.imageUploadUrl = 'index.php?ajxurl=' + url;
    CKEDITOR.config.extraPlugins = 'uploadimage', 'uploadwidget', 'notificationaggregator', 'notification', 'toolbar', 'button', 'filetools', 'dialogui', 'widget', 'widgetselection';
    //CKEDITOR.config.height = h + 'px';
    CKEDITOR.config.removePlugins = 'elementspath';
    CKEDITOR.config.allowedContent = {
        $1: {
            // Use the ability to specify elements as an object.
            elements: CKEDITOR.dtd,
            attributes: true,
            styles: true,
            classes: true,
        }
    };
    CKEDITOR.config.disallowedContent = 'script; *[on*]';
    if (type == null || type == "basic") {

//if tools type basic than----
        CKEDITOR.config.extraPlugins = 'acolumn,wordcount,mylink';
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
            {name: 'document', groups: ['mode', 'document', 'doctools', 'insert']}
        ];
        CKEDITOR.config.removeButtons = 'Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Find,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Subscript,Superscript,CopyFormatting,RemoveFormat,NumberedList,Outdent,Indent,CreateDiv,BidiLtr,Language,BidiRtl,Anchor,Flash,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,ShowBlocks,About,Undo,Redo,Strike,Maximize,Table,Styles,Font,Link,Unlink';
        //// CKEDITOR.config.toolbar = ['insert','insertMed'];
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
    } else if (type == "simple") {
        CKEDITOR.editorConfig = function(config) {
            config.toolbarGroups = [
                {name: 'styles', groups: ['styles']},
                {name: 'document', groups: ['mode', 'document', 'doctools']},
                {name: 'clipboard', groups: ['clipboard', 'undo']},
                {name: 'editing', groups: ['find', 'selection', 'spellchecker', 'editing']},
                {name: 'forms', groups: ['forms']},
                '/',
                {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
                {name: 'insert', groups: ['insert']},
                {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']},
                {name: 'links', groups: ['links']},
                '/',
                {name: 'colors', groups: ['colors']},
                {name: 'tools', groups: ['tools']},
                {name: 'others', groups: ['others']},
                {name: 'about', groups: ['about']}
            ];
            config.removeButtons = 'NewPage,ExportPdf,Preview,Print,Templates,Cut,Undo,Redo,Copy,Paste,PasteText,PasteFromWord,Replace,Find,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Bold,Italic,Underline,Strike,Subscript,Superscript,RemoveFormat,CopyFormatting,Outdent,Indent,CreateDiv,Blockquote,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,Language,BidiRtl,BidiLtr,Link,Unlink,Anchor,Flash,Image,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,ShowBlocks,Maximize,TextColor,BGColor,About,Save,NumberedList,BulletedList';
        };
    }
//CKEDITOR.config.extraPlugins = 'imagepaste'; image past
}


function simpleEditor(id) {
    CKEDITOR.config.toolbar_simple =
            [
                ['Bold', 'Italic'],
                ['Styles', 'Format', 'Font', 'FontSize'],
                ['Source']
            ];
    CKEDITOR.replace(id, {
        language: 'en',
        uiColor: '#ffffff',
        customConfig: '',
        resize_enabled: 'false',
        toolbar: 'simple',
        height: 100 + 'px'
    });
}




//Search of post
function searchPost(_this) {
    var sq = $(_this).val();
    sq = sq.trim();
    sq = sq.replace(/^\s+|\s+$|\s+(?=\s)/g, "");
    if (sq === "") {
        load_list("q=");
    } else {
        load_list("q=" + sq);
    }
}
function searchCancel(_this) {
    $(_this).parent().find('.searchIn').val('');
    searchPost($(_this).parent().find('.searchIn'));
}
//Search of post

//function DateModifyAll(_this) {
//    $(_this).append(loader);
//    var mdate = $("#mdate").val();
//    var data = {ajx_action: "FileDateModify", mDate: mdate};
//    jQuery.post('index.php', data, function(response) {
//        var obj = JSON.parse(response);
//        $(".spinLoader").remove();
//        if (obj['error'] == "") {
//            msg(obj['msg'], 'G');
//        } else {
//            msg(obj['msg'], 'R');
//        }
//    });
//}


function fBox() {
    console.log('fBox Initialized.');
    $('.fBox').unbind('click');
    $('.fBox').click(function(e) {
//alert('asd');
        e.preventDefault();
        $(this).append(loader);
        var _this = $(this);
        var w = $(this).attr('w') || 250;
        var h = $(this).attr('h');
        if (h) {
            h = 'height:' + h + 'px';
        } else {
            h = '';
        }
        jQuery.ajax({
            type: "POST",
            url: $(this).attr('href'),
            success: function(data) {
                $(_this).find('.spinLoader').remove();
                $.fancybox.open('<div class="ErpForm" style="width:' + w + 'px;' + h + '">' + data + '</div>');
            }
        });
    });
}

function makeHome(_this) {
    var $inp = $(_this);
    if ($inp.prop('checked')) {
        $(_this).before(loader);
        var data = {ajx_action: "makeHome", ID: $("#ID").val()};
        jQuery.post('index.php', data, function(response) {
            $(".spinLoader").remove();
            if (response == '1') {
                msg("Action Success", "G");
            }
        });
    } else {
        msg("Open another Page and try again", "R");
        $inp.prop('checked', true);
    }

}


function setMenuPrimary(_this, id) {
    var $inp = $(_this);
    if ($inp.prop('checked')) {
        $(_this).before(loader);
        var data = {ajx_action: "setMenuPrimary", ID: id};
        jQuery.post('index.php', data, function(response) {
            $(".spinLoader").remove();
            if (response == '1') {
                $(_this).val(id);
                msg("Action Success", "G");
            }
        });
    } else {
        msg("Open another Menu and try again", "R");
        $inp.prop('checked', true);
    }

}



function addLink(_ths) {
    var hrf = $(_ths).attr('dataLink');
    var title = $(_ths).attr('title');
    if ($("#linkTxt").val() == "") {
        $("#linkTxt").val(title);
    }
    $("#linkTitle").val(title);
    $("#linkUrl").val(hrf);
}

function openTexoChild(_this) {
    $(".childItem").hide();
    $(".texoChildTrig").css({transform: 'rotate(0deg)', transition: 'all .2s'});
    var targetId = $(_this).attr('dataID');
    //console.log(targetId);
    if ($(targetId).attr('childsOn') != 'true') {
        $(targetId).show().attr('childsOn', 'true');
    } else {
        $(targetId).hide();
        $(targetId).removeAttr('childsOn');
    }
    if ($(targetId).is(":visible")) {
        $(_this).css({transform: 'rotate(180deg)', transition: 'all .2s'});
    } else {
        $(_this).css({transform: 'rotate(0deg)', transition: 'all .2s'});
    }
}


function openSubTexo(_this) {
//console.log($(this));
    var targ = $(_this).parent().parent().find('.texoChose-sub');
    $(targ).toggle();
    if ($(targ).is(":visible")) {
        $(_this).css({transform: 'rotate(180deg)', transition: 'all .2s'});
    } else {
        $(_this).css({transform: 'rotate(0deg)', transition: 'all .2s'});
    }

}
