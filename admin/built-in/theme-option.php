<?php
add_custom_font(
        array(
    'Roboto',
    "'Roboto', sans-serif",
    'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap',
    'robotoCss'
        ), 1
);
add_custom_font(
        array(
    'Baumans',
    "'Baumans',cursive",
    'https://fonts.googleapis.com/css?family=Baumans&font-display=swap',
    'fontBaumans'
        ), 2
);

function importOption() {
    global $DB;
    $info = array();
    if (isset($_POST['str']) && !empty($_POST['str'])) {
        $json = $DB->encrypt_decrypt('decrypt', $_POST['str']);
        $jsonArray = json_decode($json, true);
        if (is_array($jsonArray)) {
            $CoPt = count($jsonArray);
            $n = 0;
            $notImport = array();
            foreach ($jsonArray as $key => $val) {
                if ($key == 'customFonts') {
                    $val = json_encode($val);
                } else {
                    $val = serialize($val);
                }
                //var_dump($key, $val);
                //continue;
                if (update_option($key, $val)) {
                    $n++;
                } else {
                    $notImport[] = $key;
                }
            }
            if ($n == $CoPt) {
                $info['msg'] = 'Option Imported Sucessfully';
                $info['error'] = 0;
            } else {
                $notImportStr = implode(',', $notImport);
                $info['msg'] = "Option Imported Percially not imported ($notImportStr)";
                $info['error'] = 1;
            }
        } else {
            $info['msg'] = 'Imported String Invalid';
            $info['error'] = 1;
        }
    } else {
        $info['msg'] = 'Imported String Invalid';
        $info['error'] = 1;
    }
    echo json_encode($info);
}

function copyExport() {
    global $DB;
    if (isset($_POST['optName'])) {
        $themeOption = themeOption();
        $fontsStr = get_option('customFonts');
        $fontArray = json_decode($fontsStr, true);
        $fElement = get_option('felement');
        if (!empty($fElement)) {
            $fElement = unserialize($fElement);
        }
        // $ExpArr = array('themeOptions' => $themeOption, 'customFonts' => $fontArray);
        //$jsonData = json_encode($ExpArr);


        $expArrI = array();
        foreach ($_POST['optName'] as $v) {
            $optname = trim($v['name']);
            if ($optname == 'themeOptions') {
                $expArrI['themeOptions'] = $themeOption;
            }
            if ($optname == 'customFonts') {
                $expArrI['customFonts'] = $fontArray;
            }

            if ($optname == 'felement') {
                $expArrI['felement'] = $fElement;
            }
        }
        $jsonData = json_encode($expArrI);
        $expString = $DB->encrypt_decrypt('encrypt', $jsonData);
        echo $expString;
    }
}

function cssGen() {
    global $customFonts;
    $fontManager = new FontManager();

    $elementFonts = $fontManager->rowElemFont();
    //var_dump($elementFonts);
    //Theme Option--------------------------
    $themeOpt = themeOption();
    $LogoFontSize = 32;
    if (isset($themeOpt['logo_font_size']) && $themeOpt['logo_font_size'] > 0) {
        $LogoFontSize = $themeOpt['logo_font_size'];
    }
    $st = "
        .logo h1 {
            font-size: 0px;
            margin: 0;
            padding: 0;
        }
        .site-branding h2, .site-branding h2 a{
            margin-bottom: 0;
            font-size: {$LogoFontSize}px;
        }
        .site-description {
            margin: 0;
            line-height: 1;
        }
        ";
    $ThemeExCSS = "";
    $ThemeExCSS .=minify_css($st);
    $ThemeExCSS .=minify_css(@$themeOpt['customCss']);

    $ThemeExCSS .="html body,html body p{";
    $ThemeExCSS .=!empty($themeOpt['text_color']) ? "color:$themeOpt[text_color];" : "";
    $ThemeExCSS .=!empty($themeOpt['line_height']) ? "line-height:$themeOpt[line_height];" : "";
    if ($elementFonts['global']) {
        $ThemeExCSS .="font-family:$elementFonts[global];";
    }
    $ThemeExCSS .="}";
    //paragraph
    $ThemeExCSS .="html body,html body p{";
    $ThemeExCSS .=!empty($themeOpt['font_size']) ? "font-size:$themeOpt[font_size]px;" : "";
    $ThemeExCSS .=!empty($themeOpt['font_weight']) ? "font-weight:$themeOpt[font_weight];" : "";
    $ThemeExCSS .="}";
    //Container
    $ThemeExCSS .=!empty($themeOpt['container_width']) ? ".container{max-width:$themeOpt[container_width]px !important}" : "";
    //HyperLink
    if (!empty($themeOpt['link_color']) || $elementFonts['hyper_link']) {
        $ThemeExCSS .="html body * a{";
        $ThemeExCSS .=!empty($themeOpt['link_color']) ? "color:$themeOpt[link_color];" : "";
        $ThemeExCSS .=$elementFonts['hyper_link'] ? "font-family:$elementFonts[hyper_link];" : "";
        $ThemeExCSS .=!empty($themeOpt['hyper_link_weight']) ? "font-weight:$themeOpt[hyper_link_weight];" : "";
        $ThemeExCSS .="}";
    }

    //Header
    if (!empty($themeOpt['heading_color']) || $elementFonts['heading']) {
        $ThemeExCSS .="html body h1 *, html body h2 *, html body h3 *, html body h4 *, html body h5 *, html body h6 *, html body h1, html body h2, html body h3, html body h4, html body h5, html body h6{";
        $ThemeExCSS .=!empty($themeOpt['heading_color']) ? "color:$themeOpt[heading_color] !important;" : "";
        $ThemeExCSS .=$elementFonts['heading'] ? "font-family:$elementFonts[heading];" : "";
        $ThemeExCSS .=!empty($themeOpt['heading_weight']) ? "font-weight:$themeOpt[heading_weight] !important;" : "";
        $ThemeExCSS .="}";
    }

    //Site Branding Font
    if ($elementFonts['logo'] && $themeOpt['use_logo_title'] == 'false') {
        $ThemeExCSS .=".site-branding .site-title *,.site-branding .site-title{font-family:$elementFonts[logo];}";
    }
    //Branding TM
    if (@$themeOpt['use_logo_title'] == 'false' && @$themeOpt['t_mark'] == 'true') {
        $ThemeExCSS .=".site-branding .site-title::after{content: 'TM';position: absolute;font-size: 10px;font-weight: 300;}";
    }
    //var_dump($ThemeExCSS);
    //site-logo
    $logoSize = get_option('logo_size');
    if (!empty($logoSize)) {
        $logoSizes = unserialize($logoSize);
    } else {
        $logoSizes = array('w' => 120, 'h' => 120, 'r' => "");
    }
    if (is_array($logoSizes)) {
        $ThemeExCSS.="body .site-logo{";
        if (!empty($logoSizes['r'])) {
            $ThemeExCSS.="border-radius:{$logoSizes['r']};";
        }
        if (!empty($logoSizes['w'])) {
            if ($logoSizes['w'] == 'auto') {
                $ThemeExCSS.="width:{$logoSizes['w']};";
            } else {
                $ThemeExCSS.="max-width:{$logoSizes['w']};";
            }
        }
        if (!empty($logoSizes['h'])) {
            if ($logoSizes['h'] == 'auto') {
                $ThemeExCSS.="height:{$logoSizes['h']};";
            } else {
                $ThemeExCSS.="max-height:{$logoSizes['h']};";
            }
        }
        if (!empty($themeOpt['logo_bg'])) {
            $ThemeExCSS.="background:{$themeOpt['logo_bg']};";
        }

        $ThemeExCSS.="}";
    }

    //$ThemeExCSS.=$fontManager->fontCss();
    //---------
    return $ThemeExCSS;
}

add_custom_css(cssGen(), 1);

function incFontFace() {
    $thisCls = new FontManager();
    $opt = themeOption();
    $n = 0;
    foreach ($opt['font'] as $fontK) {
        $n++;
        if (!empty($fontK)) {
            $fontArr = array();
            if (is_numeric($fontK)) {
                $fontArr = $thisCls->Fonts[$fontK];
            } else {
                $fontArr = $thisCls->regesterted[$fontK];
            }
            if (count($fontArr) > 0) {
                add_style(
                        array(
                            'id' => $fontArr[3],
                            'href' => trim_slash($fontArr[2]),
                            'order' => $n,
                        )
                );
            }
        }
    }
    foreach ($thisCls->regesterted as $k => $rFont) {
        $n++;
        if (isset($rFont[4]) && $rFont[4] == true) {
            //var_dump($rFont,  $rFont[3]);
            add_style(
                    array(
                        'id' => $rFont[3],
                        'href' => trim_slash($rFont[2]),
                        'order' => $n,
                    )
            );
        }
    }
}

incFontFace();

function menuSelector() {
    global $MENU;
    $menuStr = $MENU->menu_selector(false, "m0", "menuSelect");
    echo $menuStr;
}

function company_name() {
    $opt = themeOption();
    if (isset($opt['company_name']) && !empty($opt['company_name'])) {
        return $opt['company_name'];
    } else {
        return site_name();
    }
}

function site_branding() {
    global $TITLE;
    $siteLink = domain();
    $opt = themeOption();

    $h1 = "<h1>$TITLE</h1>";
    $siteTag = site_tagline();
    if ($opt['disable_tag_line'] == 'false') {
        $siteTag = "<p class=\"site-description\">$siteTag</p>";
    } else {
        $siteTag = "";
    }
    $htm = $h1;
    $htm.= "<div class='site-branding'>";
    if ($opt['use_logo_title'] == 'true') {
        $htm.="<a href='$siteLink' title='$TITLE'>" . site_logo() . "</a>";
    } else {
        $companyName = company_name();
        $htm.="<h2 class='site-title'><a href='$siteLink' title='$TITLE'>$companyName</a></h2>";
    }
    $htm.=$siteTag;
    $htm.="</div>"; //End site Branding
    return $htm;
}

add_admin_menu(
        array(
            'slug' => 'theme_option',
            'menu_title' => "Theme Option",
            'icon' => "",
            'icon_img' => "",
            'order' => "3",
            'parent_slug' => "appearence",
        )
);

//add_option('felement', "");

function themeOption() {
    $defaultOpt = array(//default options
        'container_width' => 1080,
        'heading_color' => '',
        'text_color' => '',
        'line_height' => '1.7',
        'link_color' => '', //px
        'font_size' => 14,
        'logo_bg' => '#fff',
        'font' => array('global' => '', 'hyper_link' => '', 'heading' => '', 'logo' => '')
    );
    $opt = @unserialize(get_option('themeOptions'));

    if (!is_array($opt)) {
        $opt = array();
    }
    //echo "<pre>";
    //var_dump($opt);
    //exit;
    $opt = @array_merge($defaultOpt, $opt);
    return $opt;
}

function theme_footer() {
    $strOfData = get_option('felement');
    $FElement = array();
    if (!empty($strOfData)) {
        $FElement = unserialize($strOfData);
    }
    $footerHtm = "";
    if (count($FElement) > 0) {
        $footerHtm.= "<div class='footer-wrap'><div class='container'><div class='row'>";
        foreach ($FElement as $element) {
            //var_dump($element);
            $ccls = 'footerMenu ';
            $ccls.=!empty($element['class']) ? $element['class'] : "";

            $colWidth = $element['col'];
            $footerHtm.="<div class='col-md-$colWidth col w$colWidth t6' >";
            $footerHtm.= "<div class='footer-col-inner $ccls'>";
            $content = $element['content'];
            if (isset($element['htext']) && !empty($element['htext'])) {
                $htag = isset($element['htag']) && !empty($element['htag']) ? $element['htag'] : "3";
                $footerHtm.="<h$htag class='area-header'>$element[htext]</h$htag>";
            }
            if ($element['name'] == 'editor') {
                $footerHtm.="<p>" . shortcode_exe($content) . "</p>";
            } elseif ($element['name'] == 'shortcode') {
                $footerHtm.="<p>" . shortcode_exe($content) . "</p>";
            } elseif ($element['name'] == 'textarea') {
                $dataTextarea = nl2br($content);
                $footerHtm.="<p>" . shortcode_exe($dataTextarea) . "</p>";
            } elseif ($element['name'] == 'menu') {

                $footerHtm.=get_menu($content, "", $ccls, array('prefix' => '<i class="fal fa-angle-right"></i>'), false);
            }
            $footerHtm.= "</div>"; //End of column inner
            $footerHtm.= "</div>"; //End of Column wraper
        }
        $footerHtm.= "</div></div></div>"; //end of row and container
    }
    return $footerHtm;
}

add_footer(theme_footer(), 0);

function theme_option() {
    global $customFonts, $MENU;
    $_CFONT = new FontManager();
    $themeOptions = themeOption(); //var_dump($themeOptions['line_height']);
    $menuArr = array();
    foreach ($MENU->menus() as $m) {
        if (!empty($m['name'])) {
            $menuArr[] = array('name' => $m['name'], 'texoID' => $m['taxonomy_id']);
        }
    }
    ?>
    <h2>Theme Option</h2><hr>
    <form method="post" id="optionsFrm">
        <ul class="nav nav-tabs" id="themeOption" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#MainSettings" role="tab" aria-controls="MainSettings" aria-selected="true">Settings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#footerGenerator" role="tab" aria-controls="footerGenerator" aria-selected="false">Footer</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="fontManager-tab" data-toggle="tab" href="#fontManager" role="tab" aria-controls="fontManager" aria-selected="false">Font Manager</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="expImp-tab" data-toggle="tab" href="#expImp" role="tab" aria-controls="expImp" aria-selected="false">Export/Import</a>
            </li>
        </ul>
        <hr>
        <div class="tab-content" id="themeOptionContent">
            <div class="tab-pane fade show active" id="MainSettings" role="tabpanel" aria-labelledby="MainSettings-tab">
                <!--All Settings here-->
                <button type="button" class='btn btn-cms-primary updateSettingsBtn' onclick="saveOptions(optionsFrm, this)">Update Settings</button>
                <hr>
                <div id="settingsOptions" role="tablist" aria-multiselectable="false">
                    <div class="card">
                        <div class="card-header" role="tab" id="headingsinglePostOption">
                            <h5 class="mb-0">
                                <a data-toggle="collapse" data-parent="#settingsOptions" href="#ThmIdentity" aria-expanded="true" aria-controls="ThmIdentity">
                                    Identity<span href="javascript:" class="trigg"></span>
                                </a>
                            </h5>
                        </div>
                        <div id="ThmIdentity" class="collapse show" role="tabpanel" aria-labelledby="headingThmIdentity">
                            <div class="card-block">
                                <div class='row'>
                                    <div class="col-sm-3">
                                        <label>Company Name</label>
                                    </div>
                                    <div class='col-sm-9'>
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="options[themeOptions][company_name]" value="<?php echo @$themeOptions['company_name'] ?>" class="form-control form-control-sm">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <input type="hidden" name="options[themeOptions][t_mark]" value="false">
                                                    <label style="margin:0;line-height: 0"><input type="checkbox" value="true" name="options[themeOptions][t_mark]" <?php echo isset($themeOptions['t_mark']) && $themeOptions['t_mark'] == 'true' ? 'checked' : '' ?> >&nbsp;&nbsp;TM</label> 
                                                </div>
                                            </div>
                                        </div>
                                        <span class="comment">&lt;?php echo company_name() ?&gt; or [company_name]</span>
                                    </div>
                                </div>
                                <div class='row fieldRow'>
                                    <div class="col-sm-3">
                                        <label>Logo Font</label>
                                    </div>
                                    <div class='col-sm-9'>
                                        <div class="input-group input-group-sm">
                                            <?php
                                            $val = isset($themeOptions['font']['logo']) ? $themeOptions['font']['logo'] : "";
                                            $_CFONT->fontSelect("options[themeOptions][font][logo]", $val);
                                            ?>
                                            <select class="custom-select custom-select-sm" style="max-width:60px" name="options[themeOptions][logo_font_size]">
                                                <?php
                                                for ($i = 15; $i <= 78; $i++) {
                                                    $selected = $themeOptions['logo_font_size'] == $i ? 'selected' : '';
                                                    echo "<option $selected>$i</option>";
                                                }
                                                ?>
                                            </select>
                                            <div class="input-group-append">
                                                <div class="input-group-text " style='margin-bottom:8px;'>
                                                    px
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" name="options[themeOptions][use_logo_title]" value="false">
                                        <label><input name="options[themeOptions][use_logo_title]" value="true" <?php echo isset($themeOptions['use_logo_title']) && $themeOptions['use_logo_title'] == 'true' ? 'checked' : '' ?> type="checkbox">&nbsp; Use Site Logo</label>
                                    </div>
                                </div>
                                <div class='row fieldRow'>
                                    <div class="col-sm-3">
                                        <label>Tagline</label>
                                    </div>
                                    <div class='col-sm-9'>
                                        <input type='text' name="options[tag]" value="<?php echo get_option('tag') ?>" class='form-control form-control-sm'>
                                        <span class="comment">&lt;?php echo site_tagline() ?&gt; or [site_tagline]</span>
                                        <input type="hidden" name="options[themeOptions][disable_tag_line]" value="false">

                                        <label><input name="options[themeOptions][disable_tag_line]" value="true" <?php echo isset($themeOptions['disable_tag_line']) && $themeOptions['disable_tag_line'] == 'true' ? 'checked' : '' ?> type="checkbox">&nbsp; Disable Tag line</label>
                                    </div>
                                </div>

                                <?php
                                $logoSize = get_option('logo_size');
                                if (!empty($logoSize)) {
                                    $logoSizes = unserialize($logoSize);
                                } else {
                                    $logoSizes = array('w' => 120, 'h' => 120, 'r' => '');
                                }
                                ?>
                                <div class='row fieldRow'>
                                    <div class="col-sm-3">
                                        <label>Logo max. Width</label>
                                    </div>
                                    <div class='col-sm-9'>
                                        <input type='text' name="options[logo_size][w]" value="<?php echo $logoSizes['w'] ?>" class='form-control form-control-sm w80'>
                                        <span class="comment">in Pixel (px)</span>
                                    </div>
                                </div>
                                <div class='row fieldRow'>
                                    <div class="col-sm-3">
                                        <label>Logo max. Height</label>
                                    </div>
                                    <div class='col-sm-9'>
                                        <input type='text' name="options[logo_size][h]" value="<?php echo $logoSizes['h'] ?>" class='form-control form-control-sm w80'>
                                        <span class="comment">in Pixel (px)</span>
                                    </div>
                                </div>
                                <div class='row fieldRow'>
                                    <div class="col-sm-3">
                                        <label>Logo Radius</label>
                                    </div>
                                    <div class='col-sm-9'>
                                        <input type='text' name="options[logo_size][r]" value="<?php echo $logoSizes['r'] ?>" class='form-control form-control-sm w80'>
                                        <span class="comment">in Pixel (px) or (%)</span>
                                    </div>
                                </div>
                                <div class='row fieldRow'>
                                    <div class="col-sm-3">
                                        <label>Logo Background</label>
                                    </div>
                                    <div class='col-sm-9'>
                                        <input  data-control="" value="<?php echo $themeOptions['logo_bg'] ?>" name="options[themeOptions][logo_bg]"  type='text' class='dataChange cPicker w80 form-control form-control-sm'>

                                    </div>
                                </div>
                                <span class="comment">Template Function: &lt;?php echo site_branding(); ?&gt;</span>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" role="tab" id="headingsinglePostOption">
                            <h5 class="mb-0">
                                <a data-toggle="collapse" data-parent="#settingsOptions" href="#ThmTayout" aria-expanded="true" aria-controls="ThmTayout">
                                    Layout<span href="javascript:" class="trigg"></span>
                                </a>
                            </h5>
                        </div>
                        <div id="ThmTayout" class="collapse show" role="tabpanel" aria-labelledby="headingThmTayout">
                            <div class="card-block">
                                <div class='row fieldRow'>
                                    <div class="col-sm-3">
                                        <label>Container width </label>
                                    </div>
                                    <div class='col-sm-9'>
                                        <input type="text" name="options[themeOptions][container_width]" value="<?php echo $themeOptions['container_width'] ?>" class="form-control form-control-sm w80">
                                        <span class="comment">in Pixel (px)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" role="tab" id="headingsinglePostOption">
                            <h5 class="mb-0">
                                <a data-toggle="collapse" data-parent="#settingsOptions" href="#Typography" aria-expanded="true" aria-controls="Typography">
                                    Typography <span href="javascript:" class="trigg"></span>
                                </a>
                            </h5>
                        </div>
                        <div id="Typography" class="collapse" role="tabpanel" aria-labelledby="headingTypography">
                            <div class="card-block">
                                <div class='row fieldRow'>
                                    <div class="col-sm-3">
                                        <label>Font</label>
                                    </div>
                                    <div class='col-sm-9'>
                                        <?php
                                        $val = isset($themeOptions['font']['global']) ? $themeOptions['font']['global'] : "";
                                        $_CFONT->fontSelect("options[themeOptions][font][global]", $val);
                                        ?>
                                    </div>
                                </div>
                                <div class='row fieldRow'>
                                    <div class="col-3">
                                        <label>Font Weight</label>
                                    </div>
                                    <div class='col-9'>
                                        <select name="options[themeOptions][font_weight]"  class="custom-select custom-select-sm w80">
                                            <option <?php echo @$themeOptions['font_weight'] == '400' ? 'selected' : '' ?>>400</option>
                                            <option <?php echo @$themeOptions['font_weight'] == '300' ? 'selected' : '' ?>>300</option>
                                            <option <?php echo @$themeOptions['font_weight'] == '500' ? 'selected' : '' ?>>500</option>
                                            <option <?php echo @$themeOptions['font_weight'] == '600' ? 'selected' : '' ?>>600</option>
                                            <option <?php echo @$themeOptions['font_weight'] == '700' ? 'selected' : '' ?>>700</option>
                                        </select>
                                    </div>
                                </div>
                                <div class='row fieldRow'>
                                    <div class="col-3">
                                        <label>Font Size</label>
                                    </div>
                                    <div class='col-9'>
                                        <input  value="<?php echo $themeOptions['font_size'] ?>" name="options[themeOptions][font_size]"  type='text' class='w80 form-control form-control-sm'>
                                        <span class="comment">in Pixel (px)</span>
                                    </div>
                                </div>
                                <div class='row fieldRow'>
                                    <div class="col-3">
                                        <label>Text Color</label>
                                    </div>
                                    <div class='col-9'>
                                        <input  data-control="" value="<?php echo $themeOptions['text_color'] ?>" name="options[themeOptions][text_color]"  type='text' class='dataChange cPicker w80 form-control form-control-sm'>
                                    </div>
                                </div>
                                <div class='row fieldRow'>
                                    <div class="col-3">
                                        <label>Line Height</label>
                                    </div>
                                    <div class='col-9'>
                                        <input value="<?php echo $themeOptions['line_height'] ?>" name="options[themeOptions][line_height]"  type='text'   class=' w80 form-control form-control-sm'>
                                        <span class="comment">Default Unit `em`, also `px` is workable  </span>
                                    </div>
                                </div>
                                <hr>
                                <strong>Hyper Link</strong>
                                <hr>
                                <div class='row fieldRow'>
                                    <div class="col-3">
                                        <label>Link Color</label>
                                    </div>
                                    <div class='col-9'>
                                        <input  data-control="" value="<?php echo $themeOptions['link_color'] ?>" name="options[themeOptions][link_color]"  type='text' class='dataChange cPicker w80 form-control form-control-sm'>
                                    </div>
                                </div>
                                <div class='row fieldRow'>
                                    <div class="col-sm-3">
                                        <label>Font</label>
                                    </div>
                                    <div class='col-sm-9'>
                                        <?php
                                        $val = isset($themeOptions['font']['hyper_link']) ? $themeOptions['font']['hyper_link'] : "";
                                        $_CFONT->fontSelect("options[themeOptions][font][hyper_link]", $val);
                                        ?>
                                    </div>
                                </div>
                                <div class='row fieldRow'>
                                    <div class="col-3">
                                        <label>Font Weight</label>
                                    </div>
                                    <div class='col-9'>
                                        <select name="options[themeOptions][hyper_link_weight]"  class="custom-select custom-select-sm w80">
                                            <option <?php echo @$themeOptions['hyper_link_weight'] == '400' ? 'selected' : '' ?>>400</option>
                                            <option <?php echo @$themeOptions['hyper_link_weight'] == '300' ? 'selected' : '' ?>>300</option>
                                            <option <?php echo @$themeOptions['hyper_link_weight'] == '500' ? 'selected' : '' ?>>500</option>
                                            <option <?php echo @$themeOptions['hyper_link_weight'] == '600' ? 'selected' : '' ?>>600</option>
                                            <option <?php echo @$themeOptions['hyper_link_weight'] == '700' ? 'selected' : '' ?>>700</option>
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <strong>Heading</strong>
                                <hr>
                                <div class='row fieldRow'>
                                    <div class="col-3">
                                        <label>Heading Color</label>
                                    </div>
                                    <div class='col-9'>
                                        <input  data-control="" value="<?php echo $themeOptions['heading_color'] ?>" name="options[themeOptions][heading_color]"  type='text'   class='dataChange cPicker w80 form-control form-control-sm'>
                                    </div>
                                </div>
                                <div class='row fieldRow'>
                                    <div class="col-3">
                                        <label>Font Weight</label>
                                    </div>
                                    <div class='col-9'>
                                        <select name="options[themeOptions][heading_weight]"  class="custom-select custom-select-sm w80">
                                            <option <?php echo @$themeOptions['heading_weight'] == '400' ? 'selected' : '' ?>>400</option>
                                            <option <?php echo @$themeOptions['heading_weight'] == '300' ? 'selected' : '' ?>>300</option>
                                            <option <?php echo @$themeOptions['heading_weight'] == '500' ? 'selected' : '' ?>>500</option>
                                            <option <?php echo @$themeOptions['heading_weight'] == '600' ? 'selected' : '' ?>>600</option>
                                            <option <?php echo @$themeOptions['heading_weight'] == '700' ? 'selected' : '' ?>>700</option>
                                        </select>
                                    </div>
                                </div>
                                <div class='row fieldRow'>
                                    <div class="col-sm-3">
                                        <label>Font</label>
                                    </div>
                                    <div class='col-sm-9'>
                                        <?php
                                        $val = isset($themeOptions['font']['heading']) ? $themeOptions['font']['heading'] : "";
                                        $_CFONT->fontSelect("options[themeOptions][font][heading]", $val);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" role="tab" id="headingsinglePostOption">
                            <h5 class="mb-0">
                                <a data-toggle="collapse" data-parent="#settingsOptions" href="#CustomCss" aria-expanded="true" aria-controls="CustomCss">
                                    Custom CSS <span href="javascript:" class="trigg"></span>
                                </a>
                            </h5>
                        </div>
                        <div id="CustomCss" class="collapse" role="tabpanel" aria-labelledby="headingCustomCss">
                            <div class="card-block">
                                <textarea class="form-control form-control-sm" rows="15"  name="options[themeOptions][customCss]"><?php echo isset($themeOptions['customCss']) ? $themeOptions['customCss'] : "" ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class='row fieldRow'>
                        <div class="col-sm-3">
                        </div>
                        <div class='col-sm-9'>
                            <br>
                            <button type="button" class='btn btn-cms-primary updateSettingsBtn' onclick="saveOptions(optionsFrm, this)">Update Settings</button>
                        </div>
                    </div>  
                </div>

            </div>
            <div class="tab-pane fade" id="footerGenerator" role="tabpanel" aria-labelledby="footerGenerator-tab">
                <button type="button" class='btn btn-cms-primary updateSettingsBtn' onclick="saveOptions(optionsFrm, this)">Update Settings</button>
                <hr>
                <div class="footer-generator-main">
                    <div id="footeWrap" class="row">
                        <?php
                        $strOfData = get_option('felement');
                        $FElement = array();
                        if (!empty($strOfData)) {
                            $FElement = unserialize($strOfData);
                            foreach ($FElement as $k => $el) {
                                $Elid = "fEeditor" . $k;
                                ?>
                                <div class="col-sm-<?php echo $el['col'] ?>">
                                    <div class="element-wrap <?php echo $el['name'] ?>" data-element="<?php echo $k ?>">
                                        <div class="element-header">
                                            <div class="element-name"><?php echo $el['name'] ?>
                                                <input type="hidden" value="<?php echo $el['col'] ?>" name="options[felement][<?php echo $k ?>][col]">
                                                <input type="hidden" value="<?php echo $el['class'] ?>" name="options[felement][<?php echo $k ?>][class]">
                                                <input type="hidden" name="options[felement][<?php echo $k ?>][name]" value="<?php echo $el['name'] ?>">
                                            </div>
                                            <span class="newElClose" onclick="removeElement(this)">×</span>
                                        </div>
                                        <div class="element-content">
                                            <div class='input-group mb5' >
                                                <input type='text' class='form-control form-control-sm' value="<?php echo @$el['htext'] ?>" name='options[felement][<?php echo $k ?>][htext]' placeholder='Heading'>
                                                <div class='input-group-append'>
                                                    <select class='custom-select custom-select-sm mb-0' name='options[felement][<?php echo $k ?>][htag]' id='inputGroupSelectHeadingTag<?php echo $k ?>'>
                                                        <option value='3' <?php echo @$el['htag'] == '3' ? 'selected' : "" ?>>h3</option>
                                                        <option value='1' <?php echo @$el['htag'] == '1' ? 'selected' : "" ?>>h1</option>
                                                        <option value='2' <?php echo @$el['htag'] == '2' ? 'selected' : "" ?>>h2</option>
                                                        <option value='4' <?php echo @$el['htag'] == '4' ? 'selected' : "" ?>>h4</option>
                                                        <option value='5' <?php echo @$el['htag'] == '5' ? 'selected' : "" ?>>h5</option>
                                                        <option value='6' <?php echo @$el['htag'] == '6' ? 'selected' : "" ?>>h6</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php
                                            if ($el['name'] == 'shortcode') {
                                                ?>
                                                <div class='shortcode-area'><input type='text' class='form-control form-control-sm' value="<?php echo $el['content'] ?>" id="<?php echo $Elid ?>" name='options[felement][<?php echo $k ?>][content]'></div>
                                                <?php
                                            } elseif ($el['name'] == 'editor' || $el['name'] == 'textarea') {
                                                ?>
                                                <textarea id="<?php echo $Elid ?>" name="options[felement][<?php echo $k ?>][content]" class="form-control"><?php echo $el['content'] ?></textarea>

                                                <?php
                                            } else {
                                                ?>
                                                <div class='menu-area'>
                                                    <select name='options[felement][<?php echo $k ?>][content]' class='custom-select custom-select-sm'>
                                                        <?php
                                                        foreach ($menuArr as $arr) {
                                                            $selected = $el['content'] == $arr['name'] ? "selected" : "";
                                                            echo "<option $selected>$arr[name]</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                    if ($el['name'] == 'editor') {
                                        ?>
                                        <script>

                                            $(window).on('load', function() {
                                                Editor(<?php echo $Elid ?>, 'basic', 100);
                                                CKEDITOR.instances['<?php echo $Elid ?>'].on('change', function() {
                                                    CKEDITOR.instances['<?php echo $Elid ?>'].updateElement();
                                                    console.log('sfsdaf');
                                                });
                                            });

                                        </script>

                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        <div id="AddNewBefore" class="add-new">
                            <button type="button" class="btn btn-cms-primary" onclick="newElement();"><i class="fal fa-plus"></i></button>
                            <div class="WdaddControl collapse">
                                <div class="wdAddControl-head">New Element <span class="newElClose" onclick="$(this).parent().parent().hide('slow')">×</span></div>
                                <div class="wdAddControl-control">
                                    <select id="colmn" class="custom-select custom-select-sm">
                                        <option value="">Column width</option>
                                        <?php for ($i = 1; $i <= 12; $i ++) { ?>
                                            <option><?php echo $i; ?></option>
                                        <?php }
                                        ?>


                                    </select>
                                    <select id="elmn" class="custom-select custom-select-sm">
                                        <option>Select Element</option>
                                        <option value="editor">Editor</option>
                                        <option value="textarea">Textarea</option>
                                        <option value="shortcode">Shortcode</option>
                                        <option value="menu">Menu</option>
                                    </select>
                                    <input id="ccls" type="text" class="form-control form-control-sm" placeholder="Custom Class">
                                    <hr>

                                    <button type="button" class="btn btn-cms-primary" onclick="addElement()">Add</button> <label><input type="checkbox" id="singleInsert">&nbsp;&nbsp;Single</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <br>
                <button type="button" class='btn btn-cms-primary updateSettingsBtn' onclick="saveOptions(optionsFrm, this)">Update Settings</button>
            </div>
            <div class="tab-pane fade" id="fontManager" role="tabpanel" aria-labelledby="fontManager-tab">
                <?php $_CFONT->manager() ?>
            </div>
            <div class="tab-pane fade" id="expImp" role="tabpanel" aria-labelledby="expImp-tab">
                <div class="row">
                    <div class="col-sm-8">
                        <?php
                        $themeOption = themeOption();
                        $fontsStr = get_option('customFonts');
                        $fontArray = json_decode($fontsStr, true);
                        $fElement = get_option('felement');
                        if (!empty($fElement)) {
                            $fElement = unserialize($fElement);
                        }
                        $ExpArr = array('themeOptions' => $themeOption, 'customFonts' => $fontArray);
                        $jsonData = json_encode($ExpArr);
                        global $DB;
                        $expString = $DB->encrypt_decrypt('encrypt', $jsonData);
                        ?>
                        <div class="expImpBody">
                            <textarea id="importStr" class="form-control" placeholder="Place here Exported String" rows="10"></textarea>
                            <textarea id="expString" style="height: 0px; width: 0px; overflow: hidden;position: absolute;left: -2000px;" class="form-control" placeholder=""><?php echo $expString ?></textarea>
                            <hr>
                            <button type="button" class="btn btn-cms-primary" onclick="importOption(this)">Import</button>
                            <hr>
                            <button type="button" class="btn btn-cms-default" onclick="copyExport(this)">Export & Copy</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <label><input class="optChk" type="checkbox" checked="" value="themeOptions" >&nbsp; Settings </label>&nbsp;&nbsp;
                            <label><input class="optChk" type="checkbox" checked="" value="customFonts" >&nbsp; Font Manager </label>&nbsp;&nbsp;
                            <label><input class="optChk" type="checkbox" value="felement" >&nbsp; Footer </label>&nbsp;&nbsp;
                        </div>


                        <script>
                            function copyExport(_this) {
                                $(_this).html(loader);
                                optName = [];
                                $('.optChk').each(function() {
                                    if ($(this).prop("checked") == true) {
                                        var val = $(this).val();
                                        el = {};
                                        el.name = val
                                        optName.push(el)
                                    }
                                });
                                //console.log(optName);
                                if (optName.length == 0) {
                                    msg('At least Check a option what you want to export', "R");
                                    $(_this).html('Export & Copy');
                                    return;
                                }
                                var data = {ajx_action: "copyExport", optName: optName};
                                jQuery.post('index.php', data, function(response) {
                                    if (response !== "") {
                                        $("#expString").val(response);
                                        var copyText = document.getElementById("expString");
                                        /* Select the text field */
                                        copyText.select();
                                        copyText.setSelectionRange(0, 99999); /* For mobile devices */
                                        /* Copy the text inside the text field */
                                        document.execCommand("copy");
                                        msg("Exported String Copied into Clipboard");
                                        $(_this).html('Export & Copy');
                                    } else {
                                        msg('Somthing Wrong', 'R');
                                    }
                                });

                            }
                            function importOption(_this) {
                                $(_this).html(loader);
                                var jsonStr = $('#importStr').val();
                                var data = {ajx_action: "importOption", str: jsonStr};
                                jQuery.post('index.php', data, function(response) {
                                    var responseObj = JSON.parse(response);
                                    if (responseObj.error == '0') {
                                        $(_this).html('Import');
                                        $('#importStr').val('');
                                        msg(responseObj.msg, 'G');
                                    } else {
                                        $(_this).html('Import');
                                        msg(responseObj.msg, 'R');
                                    }
                                });
                            }
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <?php
        global $MENU;
        $menuArr = array();
        foreach ($MENU->menus() as $m) {
            if (!empty($m['name'])) {
                $menuArr[] = array('name' => $m['name'], 'texoID' => $m['taxonomy_id']);
            }
        }
        ?>
    </form>

    <script>
        var menuObj = <?php echo json_encode($menuArr); ?>;
        jQuery(document).ready(function() {
            jQuery('.cPicker').each(function() {
                //
                // Dear reader, it's actually very easy to initialize MiniColors. For example:
                //
                //  jQuery(selector).minicolors();
                //
                // The way I've done it below is just for the demo, so don't get confused
                // by it. Also, data- attributes aren't supported at this time...they're
                // only used for this demo.
                //
                jQuery(this).minicolors({
                    control: jQuery(this).attr('data-control') || 'hue',
                    defaultValue: jQuery(this).attr('data-defaultValue') || '',
                    format: jQuery(this).attr('data-format') || 'hex',
                    keywords: jQuery(this).attr('data-keywords') || '',
                    inline: jQuery(this).attr('data-inline') === 'true',
                    letterCase: jQuery(this).attr('data-letterCase') || 'lowercase',
                    opacity: jQuery(this).attr('data-opacity'),
                    position: jQuery(this).attr('data-position') || 'bottom left',
                    swatches: jQuery(this).attr('data-swatches') ? jQuery(this).attr('data-swatches').split('|') : [],
                    change: function(value, opacity) {
                        if (!value)
                            return;
                        if (opacity)
                            value += ', ' + opacity;
                        if (typeof console === 'object') {
                            //	console.log(value);
                        }
                    },
                    theme: 'bootstrap'
                });

            });

        });


        function newElement() {
            $('.WdaddControl').show(200);
        }

        function addElement() {
            var col = $('#colmn').val();
            var elm = $('#elmn').val();
            var ccls = $('#ccls').val();

            var nCol = 1;
            var sngle = $("#singleInsert").is(':checked');
            var exist = $(".element-wrap").length; //Already exists Element 
            if (!sngle && exist > 0) {
                //sngle = true;
            }
            if (!sngle) {
                if (12 % col == 0) {
                    nCol = 12 / col;
                } else {
                    sngle = true;
                }
            }
            var c = 1;
            if (exist > 0) {
                c = Number($(".element-wrap").last().attr('data-element')) + 1;
            }
            for (i = 0; i < nCol; i++) {
                //-------------
                var appendStr = "";

                if (!sngle) {
                    c = c + i;
                }
                var elmRow = "";
                //Header Tag

                elmRow += "<div class='input-group mb5' ><input type='text' class='form-control form-control-sm' name='options[felement][" + c + "][htext]' placeholder='Heading'><div class='input-group-append'><select class='custom-select custom-select-sm mb-0' name='options[felement][" + c + "][htag]' id='inputGroupSelectHeadingTag" + c + "'><option value='3'>h3</option><option value='1'>h1</option><option value='2'>h2</option><option value='4'>h4</option><option value='5'>h5</option><option value='6'>h6</option></select></div></div>";

                var elID = "fEeditor" + c;
                if (elm == "textarea" || elm == "editor") {
                    elmRow += "<textarea id='" + elID + "' name='options[felement][" + c + "][content]' class='form-control'></textarea>";
                } else if (elm == "shortcode") {
                    elmRow += "<div class='shortcode-area'><input type='text' class='form-control form-control-sm' id='" + elID + "' name='options[felement][" + c + "][content]'></div>"
                } else {
                    elmRow += "<div class='menu-area'><select name='options[felement][" + c + "][content]' class='custom-select custom-select-sm'>";

                    $.each(menuObj, function(k, v) {
                        if (v != "") {
                            elmRow += "<option>" + v.name + "</option>";
                        }
                    });
                    elmRow += "</select></div>";
                }
                var elementHead = "<div class='element-header'><div class='element-name'>" + elm + "<input type='hidden' value='" + col + "' name='options[felement][" + c + "][col]'><input type='hidden' value='" + ccls + "' name='options[felement][" + c + "][class]'><input type='hidden' name='options[felement][" + c + "][name]' value='" + elm + "'></div><span class='newElClose' onclick='removeElement(this)'>×</span></div>";
                var elementHtm = "<div class='element-content'>" + elmRow + "</div>";
                appendStr += "<div class='col-sm-" + col + "'><div class='element-wrap " + elm + "' data-element='" + c + "'>" + elementHead + elementHtm + "</div></div>";
                // $("#footeWrap").append(appendStr);
                $("#AddNewBefore").before(appendStr);
                if (elm == "editor") {
                    Editor(elID, 'basic', 100);
                    CKEDITOR.instances[elID].on('change', function() {
                        CKEDITOR.instances[elID].updateElement();
                    });
                }
            }
            //-----------
            $('.WdaddControl').hide('fast');
        }

        function removeElement(_this) {
            $(_this).closest(".element-wrap").parent().remove();
        }
    </script>
    <script>
        function saveOptions(frm, _this) {
            $(_this).before(loader);
            post_return('', $(frm).serialize(), function(res) {
                var obj = JSON.parse(res);
                // alert(obj['msg']);
                $(".spinLoader").remove();
                msg(obj['msg'], 'G');
            })
        }

    </script>

    <?php
}
