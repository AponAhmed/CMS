<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FontManager
 *
 * @author apon
 */
//Font Manager
//Global font array
//add or modify Global array from script //Default 
//another data array of font manage by admin 
//marge array than next 
//array - > font name, Css link (font face), order enque,
//
//font select for indivudual element // a, p, h, logo
//build css


class FontManager {

    //put your code here
    var $Fonts = array();
    var $optField = 'customFonts';
    var $regesterted = array();

    public function __construct() {
        global $customFonts;
        $this->getFonts();
        //Marge Custom and Regestered Fonts
        if (is_array($customFonts)) {
            $this->regesterted = $customFonts;
        }
    }

    public function removeFont($index = "") {
        if (empty($index)) {
            $index = trim($_POST['id']);
        }
        if (isset($this->Fonts[$index])) {
            unset($this->Fonts[$index]);
            if ($this->updateIt()) {
                echo 1;
            } else {
                echo "Font failed to remove";
            }
        } else {
            echo "Font failed to Find";
        }
    }

    public function rowElemFont() {
        $opt = themeOption();
        $fontArray = array();
        if ($opt['font']) {
            foreach ($opt['font'] as $k => $fk) {
                if (empty($fk)) {
                    $fontArray[$k] = false;
                    continue;
                }
                $fontArr = array();
                if (is_numeric($fk)) {
                    $fontArr = $this->Fonts[$fk];
                } else {
                    $fontArr = $this->regesterted[$fk];
                }
                if (empty($fk) || empty($fontArr)) {
                    $fontArray[$k] = false;
                } else {
                    $fontArray[$k] = $fontArr[1];
                }
            }
        }
        return $fontArray;
    }

    public function fontCss() {
        $opt = themeOption();
        if ($opt['font']) {
            $css = "";
            foreach ($opt['font'] as $k => $fk) {
                if (!empty($fk)) {
                    $fontArr = array();
                    if (is_numeric($fk)) {
                        $fontArr = $this->Fonts[$fk];
                    } else {
                        $fontArr = $this->regesterted[$fk];
                    }

                    if ($k == 'global') {
                        //global Font css
                        $css.="body{font-family:$fontArr[1];}";
                    }
                    if ($k == 'heading') {
                        //Heading Font css
                        $css.="html body h1 *,html body h2 *,html body h3 *,html body h4 *,html body h5 *,html body h6 *,html body h1,html body h2,html body h3,html body h4,html body h5,html body h6{font-family:$fontArr[1]}";
                    }
                    if ($k == 'hyper_link') {
                        //Hyper Link Font css
                        $css.="html body a{font-family:$fontArr[1];}";
                    }
                }
            }
        }

        return $css;
    }

    public function addNewFont($array = array()) {
        if (count($array) == 0) {
            $array = array($_POST['name'], $_POST['family'], $_POST['face'], titleFilter($_POST['name']) . time());
        }
        $this->Fonts[time()] = $array;
        if ($this->updateIt()) {
            echo "Font sucessfully Added";
        } else {
            echo 0;
        }
    }

    private function getFonts() {
        $fontsStr = get_option($this->optField);
        //var_dump($fontsStr);
        if (!empty($fontsStr)) {
            $fontArray = json_decode($fontsStr, true);
            $this->Fonts = $fontArray;
        }
    }

    private function updateIt() {
        return update_option($this->optField, json_encode($this->Fonts));
    }

    public function fontSelect($name, $val = "", $class = "") {
        ?>
        <select name="<?php echo $name ?>" class="custom-select custom-select-sm <?php echo $class; ?>">
            <option value="">Select Font</option>
            <optgroup label="Custom Font">
                <?php
                foreach ($this->Fonts as $k => $font) {
                    $sel = $val == $k ? 'selected' : "";
                    echo "<option value='$k' $sel>$font[0]</option>";
                }
                ?>
            </optgroup>
            <optgroup label="Registered Font">
                <?php
                foreach ($this->regesterted as $k => $font) {
                    $sel = $val == $k ? 'selected' : "";
                    echo "<option value='$k' $sel>$font[0]</option>";
                }
                ?>  
            </optgroup>

        </select>
        <?php
    }

    public function dataFont() {
        $htm = "";
        foreach ($this->Fonts as $k => $f) {
            $htm.="<div class='font-m-wrap'><div class='font-title'>$f[0]</div><div class='font-name'>font-family:$f[1];</div><span onclick='deleteFont(\"$k\")'>×</span></div>";
        }
        echo $htm;
    }

    public function manager() {
        ?>
        <a href="javascript:void(0)" onclick="$('.fontAddWrap').toggle('slow');" class="btn btn-cms-default font-addButton"><i class="fal fa-plus"></i></a>&nbsp;&nbsp;
        <a type="button" onclick="loadData()" href="javascript:void(0)"><i class="fal fa-sync"></i></a>
        <div class="fontAddWrap collapse">
            <div class="newFontAdd_header">New Font <span class="newElClose" onclick="$(this).parent().parent().hide('slow')">×</span></div>
            <div class="newFontAdd_form">
                <div class="row">
                    <div class="col-md-6">
                        <label>Name</label>
                        <input type="text" id="name" class="form-control form-control-sm" placeholder="Raleway">
                    </div>
                    <div class="col-md-6">
                        <label>Font Family</label>
                        <input type="text" id="family" class="form-control form-control-sm" placeholder="'Raleway', sans-serif">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label>Font-Face Css</label>
                        <input type="text" id="face" class="form-control form-control-sm" placeholder="https://fonts.googleapis.com/css2?family=Raleway:wght@100&display=swap">
                    </div>
                </div>
                <hr>
                <button type="button" class="btn btn-cms-primary btn-sm" onclick="saveFont()">Save</button>
            </div>
        </div>


        <div id="fontlist">Loading</div>
        <script>
            $(document).ready(function() {
                loadData();
            });
            function loadData() {
                var data = {cls: "FontManager", m: "dataFont"};
                jQuery.post('index.php', data, function(response) {
                    $("#fontlist").html(response);
                });
            }
            function deleteFont(id) {
                var con = confirm('Are you sure ?');
                if (con) {
                    var data = {cls: "FontManager", m: "removeFont", id: id};
                    jQuery.post('index.php', data, function(response) {
                        loadData();
                        if (response != "1") {
                            msg(response, "R");
                        } else {
                            msg("Font Removed", "G");
                        }

                    });
                }
            }
            function saveFont() {
                var data = {cls: "FontManager", m: "addNewFont", name: $('#name').val(), family: $("#family").val(), face: $("#face").val()};
                jQuery.post('index.php', data, function(response) {
                    if (response == "0") {
                        msg("Failed to save Font", "R");
                    } else {
                        msg(response, "G");
                        $('#name').val("");
                        $('#family').val("");
                        $('#face').val("");
                        $("#fontAddWrap").hide();
                        loadData();
                    }

                });
            }
        </script>
        <?php
    }

}
