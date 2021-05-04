<?php
/*
 * Plugin Name: Google Map 
 * Plugin URI: http://siatex.com
 * Version: 1.2
 * Author: SiATEX
 * Author URI: http://www.siatex.com
 * Description: Simple google map
 * License: GPL2
 */

class gmap {

    var $Latitude = 23.77324343492922;
    var $Longitude = 90.40915695623085;
    var $height = "300px";
    var $name = "SiATEX BD";
    var $address = "House#8, Road#6, Niketon, \n Gulshan-1, Dhaka.";

    public function __construct() {
        $mapOpt = unserialize(get_option('map_option'));
        $this->Latitude = $mapOpt['latitude'];
        $this->Longitude = $mapOpt['longitude'];
        $this->name = $mapOpt['name'];
        $this->address = $mapOpt['address'];
        $this->height = $mapOpt['height'];
    }

    public function mapinit() {
        ob_start();
        ?>
        <div id="gmap">
            <iframe 
                src="https://maps.google.com/maps?q=<?php echo $this->Latitude; ?>,<?php echo $this->Longitude; ?>&hl=en;z=14&amp;output=embed"
                width="100%"
                frameborder="0"
                title="Our Location in google map"
                style="height:<?php echo $this->height; ?>; width:100%;  padding:0 !important;"
                allowfullscreen=""
                >
            </iframe>
            <div class="mapinfo">
                <strong class='mapTitle'><?php echo $this->name ?></strong>
                <p class='mapAddress'><?php echo nl2br($this->address) ?></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

}

function gmap() {
    $gmap = new gmap();
    return $gmap->mapinit();
}

add_style(array('id' => 'styleMap', 'href' => plugin_path(__FILE__) . "/style.css", 'order' => 5));
add_admin_menu(
        array(
            'slug' => 'gmaps',
            'menu_title' => "GMap",
            'icon' => "fas fa-map-marker-alt",
            'icon_img' => "",
            'order' => "50",
            'parent_slug' => ""
        )
);

function gmaps() {
    $mapOpt = unserialize(get_option('map_option'));
    if (!is_array($mapOpt)) {
        $mapOpt = array(
            'name' => 'SiATEX BD',
            'address' => "House#8, Road#6, Niketon, \n Gulshan-1, Dhaka.",
            'latitude' => 23.77324343492922,
            'longitude' => 90.40915695623085,
            'height' => "300px"
        );
    }
    ?>
    <h2>Google Map Setup</h2><hr>
    <br><br>
    <form method="post" id="optionsFrm" style='width:500px'>
        <div class="row fieldRow">
            <div class="col-sm-3">
                <label>Pin Name</label>
            </div>
            <div class="col-sm-9">
                <input type='text' name="options[map_option][name]" value="<?php echo $mapOpt['name'] ?>" class='form-control form-control-sm'>
            </div>
        </div>
        <div class="row fieldRow">
            <div class="col-sm-3">
                <label>Address</label>
            </div>
            <div class="col-sm-9">
                <textarea type='text' name="options[map_option][address]" class='form-control form-control-sm'><?php echo $mapOpt['address'] ?></textarea>
            </div>
        </div>
        <div class="row fieldRow">
            <div class="col-sm-3">
                <label>Latitude</label>
            </div>
            <div class="col-sm-9">
                <input type='text' name="options[map_option][latitude]" value="<?php echo $mapOpt['latitude'] ?>" class='form-control form-control-sm'>
                <span class="comment">Latitude Find from <a href='https://www.latlong.net/' target="_blank">Here</a></span>
            </div>
        </div>
        <div class="row fieldRow">
            <div class="col-sm-3">
                <label>Longitude</label>
            </div>
            <div class="col-sm-9">
                <input type='text' name="options[map_option][longitude]" value="<?php echo $mapOpt['longitude'] ?>" class='form-control form-control-sm'>
                <span class="comment">Longitude Find from <a href='https://www.latlong.net/' target="_blank">Here</a></span>
            </div>
        </div>
        <div class="row fieldRow">
            <div class="col-sm-3">
                <label>Height</label>
            </div>
            <div class="col-sm-9">
                <input type='text' name="options[map_option][height]" value="<?php echo $mapOpt['height'] ?>" class='form-control form-control-sm'>
            </div>
        </div>
        <div class="settings_updateArea">
            <button type="button" class='btn btn-cms-primary updateSettingsBtn float-right' onclick="saveOptions(optionsFrm, this)">Update Settings</button>
        </div>
    </form>
    <hr>
    <strong>Short-Code :</strong> [gmap]<br>
    <strong>Function call  :</strong><span style='border:1px solid #ddd;background: #eee;padding:2px  5px;'>&lt;?php echo function_exists("gmap")?gmap():""; ?&gt;</span>
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
