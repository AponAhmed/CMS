//attach
function sendFileToServer(formData, status, file, url) {
    var uploadURL = "index.php?" + url; //Upload URL
    var extraData = {}; //Extra Data.
    var jqXHR = $.ajax({
        xhr: function() {
            var xhrobj = $.ajaxSettings.xhr();
            if (xhrobj.upload) {
                xhrobj.upload.addEventListener('progress', function(event) {
                    var percent = 0;
                    var position = event.loaded || event.position;
                    var total = event.total;
                    if (event.lengthComputable) {
                        percent = Math.ceil(position / total * 100);
                    }
                    //Set progress
                    status.setProgress(percent);
                }, false);
            }
            return xhrobj;
        },
        url: uploadURL,
        type: "POST",
        contentType: false,
        processData: false,
        cache: false,
        data: formData,
        dataType: "json",
        success: function(data) {
            if (data['error'] == "") {
                status.appendIC(data['icon'])//alert(data['icon']);
                status.setFileNameSize(data['fname'].split(",")[0], file.size);
                status.appendFN(data['fname']);
                status.setProgress(100);
            }
            else {
                //alert(data['error']);
                status.errorMsg();
            }
        }
    });
    status.setAbort(jqXHR);
}
function createStatusbar(obj, url) {
    this.statusbar = $("<div class='singAtt'  id='' data-toggle='tooltip' data-placement='top' title=''></div>");
    this.fileicon = $("<div class='fileicon'></div>").appendTo(this.statusbar);
    this.filename = $("<div class='filename'></div>").appendTo(this.statusbar);
    //this.size = $("<div class='filesize'></div>").appendTo(this.statusbar);
    this.abort = $("<div class='abort pull-right'>&times;</div>").appendTo(this.statusbar);
    this.progressBar = $("<div class='AttprogressBar'><div></div></div>").appendTo(this.statusbar);
    obj.append(this.statusbar);
    this.appendIC = function(icon)
    {
        this.fileicon.append(icon);
    }
    this.setFileNameSize = function(name, size)
    {
        var sizeStr = "";
        var sizeKB = size / 1024;
        if (parseInt(sizeKB) > 1024)
        {
            var sizeMB = sizeKB / 1024;
            sizeStr = sizeMB.toFixed(2) + " MB";
        }
        else
        {
            sizeStr = sizeKB.toFixed(2) + " KB";
        }
        if (name.length > 18) {
            name = name.substr(0, 17) + "...";
        }
        this.filename.html(name);
        this.statusbar.attr("title", sizeStr);
        this.statusbar.attr("sizev", size);
        $("#attach_size").attr("sizev", parseInt($("#attach_size").attr("sizev")) + size);
        this.setTotalSize();
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })
    }
    this.setTotalSize = function()
    {
        // set total size
        //var size = parseInt($("#attach_size").attr("sizev"));
        totalAttachmentSize();
        //removeAttachment();

    }
    this.setProgress = function(progress)
    {
        var progressBarWidth = progress + "%";
        this.progressBar.find('div').animate({width: progressBarWidth}, 10).html(progress + "%");
        if (parseInt(progress) >= 100)
        {
            this.progressBar.hide();
//            if ($.isFunction(tinymce.get))
//                tinymce.get('mail_body').isNotDirty = 0;
            $("#save_status").html("Not saved");

            //this.abort.hide();
        }
        else {
//            if ($.isFunction(tinymce.get))
//                tinymce.get('mail_body').isNotDirty = 1;
            $("#save_status").html("Not saved");
        }
    }
    this.setAbortFD = function()
    {
        var sb = this.statusbar;
        var ts = this;
        this.abort.click(function()
        {
            $.ajax({
                type: "POST",
                url: "index.php?" + url,
                data: "fname=" + sb.children(".filename").children("input").val(),
                //dataType: "json",
                success: function(data) {
                    //tinymce.get('mail_body').isNotDirty = 0;
                    $("#save_status").html("Not saved");
                },
                error: function() {
                    alert('File is not deleted');
                }
            });
            //alert(sb.children(".filename").children("input").val());
            $("#attach_size").attr("sizev", (parseInt($("#attach_size").attr("sizev")) - parseInt(sb.attr("sizev"))));
            // sb.remove();
            // ts.setTotalSize();
            totalAttachmentSize();
            // alert($(this).attr("class"));
        });
    }
    this.setAbort = function(jqxhr)
    {
        var sb = this.statusbar;
        var ts = this;
        this.abort.click(function()
        {
            $(sb).tooltip('hide');
            jqxhr.abort();
            if (sb.children(".AttprogressBar").children("div").html() == "100%") {

                var f = sb.children(".filename").children("input").val();
                var fileName = f.split(',');
                var fN = fileName[1];
                var data = {cls: 'hdex_class', m: "unlinkAttachment", file: fN};
                jQuery.post('index.php', data, function(response) {
                    if (response == '1') {
                        msg('Attachment Deleted', 'G');
                    } else {
                        msg(response, 'R');
                    }
                });
                sb.remove();
                totalAttachmentSize();
            }
            else {
                sb.remove();
                totalAttachmentSize();
            }
        });
    }
    this.appendFN = function(fn)
    {
        this.filename.append("<input type=\"hidden\"  name=\"ticket_attach[]\"  value=\"" + fn + "\"   />");
    }
    this.errorMsg = function()
    {
        var sb = this.statusbar;
        sb.children(".AttprogressBar").children("div").html("File Error");
        sb.children(".AttprogressBar").show();
        sb.children(".filename").children("input").remove()
    }
}

//function insert_attach(input, url) {
//    var files = input.files;
//    $.each(files, function(idx, file) {
//        var fd = new FormData();
//        fd.append('upload', file);
//        var obj = $(".uploaded");
//        var status = new createStatusbar(obj, url); //Using this we can set progress.
//        // var status="";
//        $('.uploaderArea').hide();
//        $('.afterUpload').show();
//        status.setFileNameSize(file.name, file.size);
//        sendFileToServer(fd, status, file, url);
//    });
//    $(files).val("");
//}

//=============Theme Upload =================
function themeUpload(input, url) {
    var files = input.files;
    $.each(files, function(idx, file) {
        var fd = new FormData();
        fd.append('upload', file);
        var obj = $(".uploaded");

        sendFileToServerReturn(fd, function() {
            var xhrobj = $.ajaxSettings.xhr();
            if (xhrobj.upload) {
                xhrobj.upload.addEventListener('progress', function(event) {
                    var percent = 0;
                    var position = event.loaded || event.position;
                    var total = event.total;
                    if (event.lengthComputable) {
                        percent = Math.ceil(position / total * 100);
                    }
                    $(".thm-progress").show();
                    //console.log(percent);
                    $(".thm-progress div").width(percent + "%");
                    if (percent == 100) {
                        $(".uploaderArea").hide();
                        $(".afterUpload ").show();
                        setTimeout(function() {
                            $(".thm-progress").hide();
                        }, 1000);
                        $(".thm-progress div").width(percent + "%");
                    }
                    //status.setProgress(percent);
                }, false);
            }
            return xhrobj;
        }, function(resp) {
            extract(resp['fname'], resp['type'], function(res) {
                $(".afterUpload ").html(res);
            })
        }, file, url);
    });
    $(files).val("");
}

function extract(ress, typ, calback) {
    get_return("action=extract&resource=" + ress + "&type=" + typ, calback);
}

//function()
function sendFileToServerReturn(formData, xhr, succ, file, url) {
    var uploadURL = "index.php?upload=" + url; //Upload URL
    var extraData = {}; //Extra Data.
    var jqXHR = $.ajax({
        xhr: xhr,
        url: uploadURL,
        type: "POST",
        contentType: false,
        processData: false,
        cache: false,
        data: formData,
        dataType: "json",
        //uploadProgress: progg,
        success: succ
    });
}