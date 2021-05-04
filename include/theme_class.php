<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of theme_class
 *
 * @author nrb
 */
class theme_class {

    public $themeDir;
    public $Path;
    public $Dir;
    public $Name;
    public $Version;
    public $templates = array();
    public $themes = array();
    public $updatePath = "http://siatexltd.com/cms_update_path/themes/";

    public function __construct() {
        $css = "style.css";
        $this->Dir = current_theme_dir();
        $this->themeDir = THEME_DIR;
        $this->Path = THEME_DIR . $this->Dir . '/';
        $file = $this->Path . $css;
        $info = $this->extractThemeInfo($file);
        $this->Name = $info['Theme Name'];
        $this->Version = @$info['Version'];
        $this->templats();
    }

    public function extractThemeInfo($file) {
        $info = array();

        $fp = @fopen($file, 'r');

        // move to the 7th byte
        if ($fp) {
            fseek($fp, 2);
            $data = @fread($fp, 150);   // read 8 bytes from byte 7
            @fclose($fp);


            // var_dump($data);
            $lineOfInfo = explode("\n", $data);
            $patt = "/([^:%'\"*$\/\n]+):([^:%'\"*$\/\n]+)/";
            //:([a-zA-Z\d+.\s-]+)
            foreach ($lineOfInfo as $line) {
                if (preg_match($patt, $line, $M)) {
                    //var_dump($M);
                    $info[trim($M[1])] = trim($M[2]);
                }
            }
            return $info;
        }
    }

    public function store() {
        $remoteInfo = $this->updatePath . 'registry.json';
        $regestryJson = file_get_contents_curl($this->updatePath . "registry.json");
        $storeObject = json_decode($regestryJson, true);
        $existsTheme = $this->themes;
        ?>

        <div class="row store">
            <?php
            foreach ($storeObject as $theme) {
                if (array_key_exists($theme['dir'], $existsTheme)) {
                    //continue;
                }
                ?>
                <div class='col-4'>
                    <div class='single-theme' data-theme-info="<?php echo $theme['dir'] . " " . $theme['Theme Name'] ?>" id="<?php echo $theme['dir'] ?>">
                        <?php
                        if (isset($theme['sc'])) {
                            $themeUmg = $this->updatePath . $theme['dir'] . "/screenshot.jpg";
                        } else {
                            $themeUmg = $info['screen'] = ADMIN_IMG . "defauld_theme_screenshot.png";
                        }
                        ?>
                        <img class='themeScreen' src="<?php echo $themeUmg ?>">
                        <div class="theme-title">
                            <label><?php echo $theme['Theme Name'] ?></label>
                            <a href="javascript:" onclick="updateTheme('<?php echo $theme['dir'] ?>', this, 'download')" class="btn btn-cms-primary float-right">Download</a>
                            <br>
                            <label>Author: <span><?php echo $theme['Author'] ?></span></label><br>
                            <?php
                            if ($theme['created']) {
                                ?>
                                <label>Published :<span><?php echo $theme['created'] ?></span></label>
                                <?php
                            }
                            ?>
                        </div>
                    </div> 
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }

    public function templats() {
        foreach (glob($this->Path . "*.php") as $filename) {
            $finfo = pathinfo($filename);
            //var_dump($finfo['basename']);
            $tmpInfo = $this->extractThemeInfo($this->Path . $finfo['basename']);
            if (isset($tmpInfo['Template'])) {
                $this->templates[$finfo['basename']] = $tmpInfo['Template'];
            }
        }
        return $this->templates;
    }

    public function templateSelect($select = false, $name = '', $class = false, $id = false, $label = true) {
        $html = "";
        if (count($this->templates) > 0) {
            if ($label) {
                $html.="<label>Template : </label>";
            }
            $html.="<select name='$name' class='$class' id='$id'>";
            $html.="<option value=''>Default</option>";
            foreach ($this->templates as $file => $name) {
                $sel = !empty($select) && $select == $file ? "selected" : "";
                $html.="<option value='$file' $sel>$name</option>";
            }
            $html.="</select>";
        }
        return $html;
    }

    public function themes() {
        $scanned_directory = array_diff(scandir($this->themeDir), array('..', '.'));
        foreach ($scanned_directory as $folder) {
            //var_dump($folder,is_dir($this->themeDir .$folder));
            if (is_dir($this->themeDir . $folder)) {
                $themeDir = $this->themeDir . $folder;
                $rq_cssFile = $this->themeDir . $folder . "/style.css";
                if (file_exists($rq_cssFile)) {
                    $info = $this->extractThemeInfo($rq_cssFile);
                    if (isset($info['Theme Name'])) {
                        $info['dir'] = $folder;
                        if (file_exists(THEME_DIR . "$folder/screenshot.jpg")) {
                            $info['screen'] = THEMES_PATH . "$folder/screenshot.jpg";
                        } else {
                            $info['screen'] = ADMIN_IMG . "defauld_theme_screenshot.png";
                        }
                        $this->themes[$folder] = $info;
                    }
                }
            }
        }
    }

}

function themeUpdateCkeck() {
    global $THEME;
    //$plugins_names = @$_POST['pluginData'];
    $all_headers = array();

    //$dir = $THEME->pluginDIR;
    $remotePath = $THEME->updatePath;

    $update = array();
    $THEME->themes();
    foreach ($THEME->themes as $file => $indDir) {
        $remoteinfo = $remotePath . $indDir['dir'] . "/style.css";

        $fp = @fopen($remoteinfo, 'r');

        // Pull only the first 8kiB of the file in.
        $file_data = @fread($fp, 99999);

        // PHP will close file handle, but we are good citizens.
        @fclose($fp);
        $file_data = str_replace("\r", "\n", $file_data);
        $default_headers = array(
            'Name' => 'Theme Name',
            'ThemeURI' => 'Theme URI',
            'Version' => 'Version',
            'Description' => 'Description',
            'Author' => 'Author',
            'License' => 'License',
            'Last Change' => 'Last Change'
        );

        foreach ($default_headers as $field => $regex) {
            if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $file_data, $match) && $match[1])
                $all_headers[$field] = $match[1];
            else
                $all_headers[$field] = '';
        }
        if (@$all_headers['Version'] > @$indDir['Version']) {
            $update[] = $indDir['dir'];
        }
    }

    echo json_encode($update);
}

function themeDownload() {
    global $THEME;
    $tempDir = TEMP;
    $ThemeRemoteDir = $THEME->updatePath;
    $themeDir = THEME_DIR;
    if (isset($_POST['basename'])) {
        $folderName = $_POST['basename'];
        //var_dump($_POST['basename']);
        $zipFileFilter = str_replace(' ', '-', $folderName);
        $remoteFile = "$ThemeRemoteDir" . $zipFileFilter . ".zip";
        //var_dump($remoteFile);
        $tempFile = $tempDir . md5($folderName) . ".zip";
        //echo $remoteFile;
        $down = file_put_contents($tempFile, @fopen($remoteFile, 'r'));
        if (!$down) {
            echo "Theme Unable to download";
        }
        $extTo = $themeDir;
        $zip = new ZipArchive;
        $res = $zip->open($tempFile);
        if ($res === TRUE) {
            // extract it to the path we determined above
            $c = $zip->extractTo($extTo);
            $zip->close();
            unlink($tempFile);
            echo 1;
            exit;
        } else {
            echo "Plugin Unable to Extruct (*Zip)";
            exit;
        }
    }
    //echo "Updating...";
}

function themeUpdateinfo() {
    global $THEME;
    $tempDir = TEMP;
    $ThemeRemoteDir = $THEME->updatePath;
    $themeDir = PLUG_DIR;
    if (isset($_POST['basename'])) {
        $folderName = $_POST['basename'];
        //var_dump($_POST['basename']);
        $file = "$ThemeRemoteDir" . "$folderName/readme.txt";

        $fp = @fopen($file, 'r');

        // Pull only the first 8kiB of the file in.
        $file_data = @fread($fp, 99999);

        // PHP will close file handle, but we are good citizens.
        @fclose($fp);

        // Make sure we catch CR-only line endings.
        $file_data = str_replace("\r", "\n", $file_data);
        $default_headers = array(
            'Name' => 'Theme Name',
            'ThemeURI' => 'Theme URI',
            'Version' => 'Version',
            'Description' => 'Description',
            'Author' => 'Author',
            'License' => 'License',
            'Last Change' => 'Last Change'
        );
        foreach ($default_headers as $field => $regex) {
            if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $file_data, $match) && $match[1])
                $all_headers[$field] = $match[1];
            else
                $all_headers[$field] = '';
        }
        $html = "<div class='plUp_info'>";
        foreach ($all_headers as $k => $v) {
            $html.="<strong>$k :</strong> $v <br>";
        }
        $html.= "</div>";
        echo $html;
    }
}
