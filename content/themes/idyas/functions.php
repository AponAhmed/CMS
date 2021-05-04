<?php
/*
 * Theme function 
 */

//add_support("bootstrap", "front");
add_support("jquery", "front");
add_support("menu-asset", "front");
add_custom_font(
        array(
    'Ropa Sans',
    "'Ropa Sans', sans-serif",
    'https://fonts.googleapis.com/css2?family=Ropa+Sans&display=swap',
    'ropaSans',
    true
        ), 3
);


add_menu_location(array('name' => 'Main Menu', 'slug' => 'header-main-menu'));
add_menu_location(array('name' => 'Other Menu', 'slug' => 'other-menu'));
add_menu_location(array('name' => 'Product Menu', 'slug' => 'product-menu'));

add_style(array('id' => "theme-main", 'href' => current_theme_path() . "/style.css", 'order' => 50));
add_script(array('id' => "idyas-script", 'src' => current_theme_path() . "/assets/scripts.js", 'order' => 10, 'position' => 'footer'));


if (is_home() || getTemplateName() == 'template_home.php') {
    add_metabox(
            array(
                'title' => "Slider Area Content",
                'Description' => "",
                'position' => "",
                'type' => "page",
                'calback' => 'idyasSliderAreaContent'
            )
    );
}

function idyasSlider() {
    global $POST;
    if (!empty($POST))
        $metas = get_post_metas($POST['ID'], array('sliderShortcode', 'sliderWidth', 'contentOfSlider', 'sliderBg'));
    if (isset($metas['sliderShortcode']) && !empty($metas['sliderShortcode'])) {
        $sliderHtml = slider(array('id' => $metas['sliderShortcode']));

        if (@$metas['sliderWidth'] == 'fullViewPort') {
            echo "<div style='background:$metas[sliderBg]'>" . $sliderHtml . "</div>";
        } elseif (@$metas['sliderWidth'] == 'fullContainer') {
            echo "<div class='container'><div style='background:$metas[sliderBg]'>" . $sliderHtml . "</div></div>";
        } else {
            $sliderDefaultHtml = "<div style='background:$metas[sliderBg]'>";
            $sliderDefaultHtml.= "<div class='default-slider-wrap'>";
            $sliderDefaultHtml.= "<div class='defaultSliderArea'>";
            $sliderDefaultHtml.=$sliderHtml;
            $sliderDefaultHtml.="</div>"; //slider area end

            $sliderDefaultHtml .= "<div class='defaultSlidercontentArea'>";
            $sliderDefaultHtml.=$metas['contentOfSlider'];
            $sliderDefaultHtml.="</div>"; //slider content area end 

            $sliderDefaultHtml.="</div>";
            $sliderDefaultHtml.="</div>";
            echo "<div class='container'>" . $sliderDefaultHtml . "</div>";
        }
    }
    //var_dump($metas);
}

function idyasSliderAreaContent() {
    global $POST;
    $metas = get_post_metas($POST['ID']);
    ?>
    <div class="mBoxBody">
        <div class="row">
            <div class="col-sm-7">
                <label>Slider Select</label>
                <select name="meta[sliderShortcode]" class="custom-select custom-select-sm">
                    <?php
                    $default = array(
                        'numberposts' => -1,
                        'orderby' => 'post_date_gmt',
                        'order' => 'DESC',
                        'post_type' => 'slider-gallery',
                        'post_status' => 'published',
                        'selectFields' => "ID,post_title",
                    );
                    $sliders = get_posts($default);
                    $selected = @$metas['sliderShortcode'];
                    foreach ($sliders as $slider) {
                        $selectedID = $slider['ID'] == $selected ? 'selected' : '';
                        echo "<option value='$slider[ID]' $selectedID>$slider[post_title]</option>";
                    }
                    ?>
                </select>
                <hr>
                <label>Slider Width: </label>
                <label><input type='radio' name="meta[sliderWidth]" value='default' <?php echo @$metas['sliderWidth'] == 'default' ? 'checked' : '' ?>>&nbsp;Default</label>
                <label><input type='radio'  name="meta[sliderWidth]" value='fullContainer' <?php echo @$metas['sliderWidth'] == 'fullContainer' ? 'checked' : '' ?>>&nbsp;Full Container</label> 
                <label><input type='radio' name="meta[sliderWidth]" value='fullViewPort' <?php echo @$metas['sliderWidth'] == 'fullViewPort' ? 'checked' : '' ?>>&nbsp;Full View Port</label>
                <hr>
                <div class='row fieldRow'>
                    <div class="col-sm-3">
                        <label>Background</label>
                    </div>
                    <div class='col-sm-9'>
                        <input  data-control="" value="<?php echo @$metas['sliderBg'] ?>" name="meta[sliderBg]"  type='text' class='dataChange cPicker w80 form-control form-control-sm'>

                    </div>
                </div>
            </div>
            <div class="col-sm-5">
                <label>Default Slider Right Text</label>
                <textarea id='contentOfSlider' class='form-control' name='meta[contentOfSlider]'>
					<?php echo @$metas['contentOfSlider']; ?>
				</textarea>		
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            //Editor(contentOfSlider, 'simple', 100);
            simpleEditor(contentOfSlider);
            var timeoutId
            CKEDITOR.instances['contentOfSlider'].on('change', function() {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(function() {
                    CKEDITOR.instances['contentOfSlider'].updateElement();
                }, 1000);
            });
        });
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

    </script>
    <?php
}

function postByTerm() {
    global $TERM;
    echo $TERM->posts(array('get-price' => 'yes', 'excerpt' => '10', 'post_per_page' => 4));
}

function sideBar() {
    $html = "<h3 class='bg'>Other Links</h3>";
    $html.= get_menu('other-menu', "", "sidebar-menu", array('prefix' => '<i class="fal fa-angle-right"></i>'), false);
    return $html;
}

function ProductMenu() {
    $html = "<h3 class='bg'>Our Products</h3>";
    $html.= get_menu('product-menu', "", "sidebar-menu", array('prefix' => '<i class="fal fa-angle-right"></i>'), false);
    return $html;
}

function idys_banner() {
    return feature_image();
}
