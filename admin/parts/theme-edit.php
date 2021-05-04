
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.7/themes/default/style.min.css" />
<!--<link rel="stylesheet" href="https://codemirror.net/theme/ambiance.css" />-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.43.0/codemirror.min.css" />



<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.7/jstree.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.43.0/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.43.0/mode/javascript/javascript.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.43.0/mode/css/css.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.43.0/mode/php/php.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.43.0/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.43.0/mode/htmlmixed/htmlmixed.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.43.0/mode/clike/clike.min.js"></script>
<script type="text/javascript">
    var editor;
    function alertBox(message, className) {
        if (className == 'success') {
            className = "G";
        } else {
            className = "R";
        }
        msg(message, className);
    }

    function reloadFiles() {
        $.post("index.php", {cls: 'theme_edit', m: 'action', action: "reload"}, function(data) {
            $("#files > div").jstree("destroy");
            $("#files > div").html(data);
            $("#files > div").jstree();
            $("#files > div a:first").click();
            $("#path").html("");

            window.location.hash = "/";
        });
    }

    $(function() {
        editor = CodeMirror.fromTextArea($("#editor")[0], {
            lineNumbers: true,
            mode: "application/x-httpd-php",
            indentUnit: 4,
            indentWithTabs: true,
            lineWrapping: true,
            //theme: 'ambiance',
            autocorrect: true,
            spellcheck: true,
            autofocus: true,
        });

        $("#files > div").jstree({
            state: {key: "pheditor"},
            plugins: ["state"]
        });

        $("#files").on("click", "a.open-file", function(event) {
            event.preventDefault();

            var file = $(this).attr("data-file"),
                    _this = $(this);

            window.location.hash = file;
            $.post("index.php", {cls: 'theme_edit', m: 'action', action: "open", file: encodeURIComponent(file)}, function(data) {
                if (file.match(/.(jpg|jpeg|png|gif)$/i)) {
                    editor.setValue("");
                    var thmUri = $('#im').attr('thmUri');
                    var src = thmUri + file;
                    $('#im').find('img').attr('src', src);
                    $(".delete, .rename,.close").removeClass("disabled");
                    $(".save").addClass("disabled");
                    $('#im').addClass('imOpen');
                } else {
                    editor.setValue(data);
                    $("#editor").attr("data-file", file);
                    $(".save, .delete, .rename, .reopen, .close").removeClass("disabled");
                    $('#im').removeClass('imOpen');
                }
                $("#path").html(file);

            });
        });

        $("#files").on("click", "a.open-dir", function(event) {
            event.preventDefault();

            var dir = $(this).attr("data-dir"),
                    _this = $(this);
            window.location.hash = dir;

            editor.setValue("");
            $("#path").html(dir);
            $(".save, .reopen, .close").addClass("disabled");
            $(".delete, .rename").removeClass("disabled");
        });

        if (window.location.hash.length > 1) {
            var hash = window.location.hash.substring(1);

            setTimeout(function() {
                $("#files a[data-file=\"" + hash + "\"], #files a[data-dir=\"" + hash + "\"]").click();
            }, 500);
        }

        $(".FileTools .new-file").click(function() {
            var path = $("#path").html();

            if (path.length > 0) {
                var name = prompt("Please enter file name:", "new-file"),
                        end = path.substring(path.length - 1),
                        file = "";

                if (name != null && name.length > 0) {
                    if (end == "/") {
                        file = path + name;
                    } else {
                        file = path.substring(0, path.lastIndexOf("/") + 1) + name;
                    }

                    $.post("index.php", {cls: 'theme_edit', m: 'action', action: "save", file: file, data: ""}, function(data) {
                        data = data.split("|");

                        alertBox(data[1], data[0]);

                        if (data[0] == "success") {
                            reloadFiles();
                        }
                    });
                }
            } else {
                alertBox("Please select a file or directory", "warning");
            }
        });

        $(".FileTools .new-dir").click(function() {
            var path = $("#path").html();

            if (path.length > 0) {
                var name = prompt("Please enter directory name:", "new-dir"),
                        end = path.substring(path.length - 1),
                        dir = "";

                if (name != null && name.length > 0) {
                    if (end == "/") {
                        dir = path + name;
                    } else {
                        dir = path.substring(0, path.lastIndexOf("/") + 1) + name;
                    }

                    $.post("index.php", {cls: 'theme_edit', m: 'action', action: "make-dir", dir: dir}, function(data) {
                        data = data.split("|");

                        alertBox(data[1], data[0]);

                        if (data[0] == "success") {
                            reloadFiles();
                        }
                    });
                }
            } else {
                alertBox("Please select a file or directory", "warning");
            }
        });

        $(".FileTools .save,.save").click(function() {
            var path = $("#path").html(),
                    data = editor.getValue();

            if (path.length > 0) {
                $.post("index.php", {cls: 'theme_edit', m: 'action', action: "save", file: path, data: data}, function(data) {
                    data = data.split("|");
                    alertBox(data[1], data[0]);
                });
            } else {
                alertBox("Please select a file", "warning");
            }
        });

        $(".close").click(function() {
            editor.setValue("");
            $('#im').removeClass('imOpen');
            $("#files > div a:first").click();
            $(".save, .delete, .rename, .reopen, .close").addClass("disabled");
        });

        $(".FileTools .delete").click(function() {
            var path = $("#path").html();

            if (path.length > 0) {
                if (confirm("Are you sure to delete this file?")) {
                    $.post("index.php", {cls: 'theme_edit', m: 'action', action: "delete", path: path}, function(data) {
                        data = data.split("|");

                        alertBox(data[1], data[0]);

                        if (data[0] == "success") {
                            reloadFiles();
                        }
                    });
                }
            } else {
                alertBox("Please select a file or directory", "warning");
            }
        });

        $(".FileTools .rename").click(function() {
            var path = $("#path").html();

            if (path.length > 0) {
                var name = prompt("Please enter new name:", "new-name");

                if (name != null && name.length > 0) {
                    $.post("index.php", {cls: 'theme_edit', m: 'action', action: "rename", path: path, name: name}, function(data) {
                        data = data.split("|");

                        alertBox(data[1], data[0]);

                        if (data[0] == "success") {
                            reloadFiles();
                        }
                    });
                }
            } else {
                alertBox("Please select a file or directory", "warning");
            }
        });

        $(".FileTools .reopen").click(function() {
            var path = $("#path").html();

            if (path.length > 0) {
                $("#files a[data-file=\"" + path + "\"], #files a[data-dir=\"" + path + "\"]").click();
            }
        });

        $(window).resize(function() {
            if (window.innerWidth >= 720) {
                var height = window.innerHeight - $(".CodeMirror")[0].getBoundingClientRect().top - 20;

                $("#files, .CodeMirror").css("height", height + "px");
            } else {
                $("#files > div, .CodeMirror").css("height", "");
            }
        });

        $(window).resize();

        $(".alert").click(function() {
            $(this).fadeOut();
        });

        $(document).bind("keyup keydown", function(event) {
            if ((event.ctrlKey || event.metaKey) && event.shiftKey) {
                if (event.keyCode == 78) {
                    $(".FileTools .new-file").click();
                    event.preventDefault();

                    return false;
                } else if (event.keyCode == 83) {
                    $(".FileTools .save").click();
                    event.preventDefault();

                    return false;
                }
            }
        });

        $(document).bind("keyup", function(event) {
            if (event.keyCode == 27) {
                if (document.activeElement.tagName.toLowerCase() == "textarea") {
                    $(".jstree-clicked").focus();
                } else {
                    editor.focus();
                }
            }
        });
    });
</script>
<?php
$RhemeEditor = new theme_edit();
?>
<div class="row">
    <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
        <h2 style="font-size:20px;">Theme Editor &nbsp;&nbsp; <a class="save disabled btn btn-cms-primary" href="javascript:void(0);">Save Changed</a></h2>
        <span id="path" class="themefilePath"></span>
        <a class="close disabled" href="javascript:void(0);"><i class="fal fa-times"></i></a>
        <div class="card">
            <div class="card-block" id="ed">
                <textarea id="editor" data-file="" class="form-control"></textarea>
            </div>
            <div class="card-block" id="im" thmUri="<?php echo current_theme_path() ?>"><img src=""></div>
            <span class="edHTogg" onclick="$('.EditorHelp').slideToggle()"><i class="fas fa-question-circle"></i></span>
            <span class='EditorHelp'>
                New File Ctrl (CMD) + Shift + N<br>
                Save File Ctrl (CMD) + Shift + S<br>
                Switch between file manager, editor and terminal Esc<br>
                Double press Esc to open file menu<br>
                Double click on file name to view in browser window/tab.<br>
                Find Ctrl (CMD) + F<br>
                Find next Ctrl (CMD) + G<br>
                Find previous Shift + Ctrl (CMD) + G<br>
                Replace Shift + Ctrl + F or CMD + Option + F<br>
                Replace all Shift + Ctrl + R or Shift + CMD + Option + F<br>
                Persistent search Alt + F<br>
                Go to line Alt (Option) + G<br>
                Toggle Terminal Ctrl (CMD) + Shift + L<br>
                Terminal history (Up & Down arrow keys)<br>
            </span>
        </div>
    </div>
    <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
        <div class="FileTools">
            <ul>
                <li>
                    <a class="save disabled" href="javascript:void(0);" title="Save"><i class="fal fa-save"></i></a>
                </li>
                <li>
                    <a class="new-file" href="javascript:void(0);" title="New File"><i class="fal fa-plus"></i></a>
                </li>
                <li>
                    <a class="new-dir" href="javascript:void(0);" title="New Folder"><i class="fal fa-folder-plus"></i></a>
                </li>
                <li>
                    <a class="delete disabled" href="javascript:void(0);" title="Delete"><i class="fal fa-trash-alt"></i></a>
                </li>
                <li>
                    <a class="rename disabled" href="javascript:void(0);" title="Rename"><i class="fas fa-i-cursor"></i></a>
                </li>
            </ul>
        </div>
        <div id="files" class="card">
            <div class="card-block">
                <?php echo $RhemeEditor->files(); ?>
            </div>
        </div>
    </div>
</div>



<!--<div class="row">
    <div class="col-md-9">
        <div class="float-left">
            <div class="dropdown float-left">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="fileMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">File</button>
                <div class="dropdown-menu" aria-labelledby="fileMenu">

                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item delete disabled" href="javascript:void(0);">Delete</a>
                    <a class="dropdown-item rename disabled" href="javascript:void(0);">Rename</a>
                    <a class="dropdown-item reopen disabled" href="javascript:void(0);">Re-open</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item close disabled" href="javascript:void(0);">Close</a>
                </div>
            </div>
           
        </div>
    </div>
</div>-->

