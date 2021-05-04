<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of plugins
 *
 * @author nrb
 */
class plugins {

    public $pluginDIR = PLUG_DIR;
    public $updatePath = "http://siatexltd.com/cms_update_path/plugins/";

    //put your code here
    function __construct() {
        $plg = get_option('plugins');
        if (empty($plg)) {
            add_option('plugins', '');
        }
    }

    public function AvailablePlugins() {
        $plugins = array_diff(scandir($this->pluginDIR), array('.', '..'));
        $pluginsArray = array();
        foreach ($plugins as $pluginFolder) {
            // var_dump($file_parts);
            if (is_dir($this->pluginDIR . $pluginFolder)) {
                $plFiles = array_diff(scandir($this->pluginDIR . $pluginFolder), array('.', '..'));
                foreach ($plFiles as $file) {
                    $fileinfo = pathinfo($file);
                    if (isset($fileinfo['extension']) && $fileinfo['extension'] == 'php') {
                        $pFile = $this->pluginDIR . $pluginFolder . "/" . $file;
                        if ($this->is_plugin($pFile)) {
                            $pluginsArray[$file] = $this->is_plugin($pFile);
                        }
                    }
                }
            }
        }
        //var_dump($pluginsArray);
        return $pluginsArray;
    }

    public function initPlugin() {
        $PLarr = array();
        foreach ($this->AvailablePlugins() as $fil => $pl) {
            if ($this->is_active($fil)) {
                $incAble = $pl['dir'] . "/" . $fil;
                //$this->to_deactive($fil, 'no-response');
                if (file_exists($incAble)) {
                    if (is_readable($incAble)) {
                        $PLarr[] = $incAble;
                        require_once $incAble;
                    } else {
                        $this->to_deactive($fil, 'no-response');
                    }
                } else {
                    $this->to_deactive($fil, 'no-response');
                }
            }
        }
        return $PLarr;
    }

    public function is_active($pl) {
        $activePl = get_option('plugins');
        if (!empty($activePl)) {
            $activePl_array = unserialize($activePl);
            return in_array($pl, $activePl_array);
        }
    }

    public function is_plugin($file) {
        //return true;
        $fp = @fopen($file, 'r');
        
        // Pull only the first 8kiB of the file in.
        $file_data = @fread($fp, 8192);

        // PHP will close file handle, but we are good citizens.
        @fclose($fp);

        // Make sure we catch CR-only line endings.
        $file_data = str_replace("\r", "\n", $file_data);


        $default_headers = array(
            'Name' => 'Plugin Name',
            'PluginURI' => 'Plugin URI',
            'Version' => 'Version',
            'Description' => 'Description',
            'Author' => 'Author',
            'Type' => 'Type'
        );
        foreach ($default_headers as $field => $regex) {
            if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $file_data, $match) && $match[1])
                $all_headers[$field] = $match[1];
            else
                $all_headers[$field] = '';
        }

        $info = pathinfo($file);
        $all_headers['dir'] = $info['dirname'];

        if (defined('WEB_APP') && WEB_APP == 'true') {
            //var_dump($all_headers);
            if (isset($all_headers['Type']) && trim($all_headers['Type']) == 'Web App') {
                //var_dump($all_headers);
                return $all_headers;
            } else {
                return false;
            }
        }
        return $all_headers;
        //var_dump($all_headers);
    }

    public function to_active($plugin) {
        $activePl = get_option('plugins');
        if (!empty($activePl)) {
            $activePl_array = unserialize($activePl);
        }
        $activePl_array[] = $plugin;
        $activePl = serialize($activePl_array);
        if (update_option('plugins', $activePl)) {
            $functionName = "active_$plugin";
            $functionName = str_replace(".php", "", $functionName);
            if (function_exists($functionName)) {
                $functionName();
                // echo $functionName;
            }
            $info = array("msg" => "Plugin Activated!", 'rf' => "", "error" => 0);
        } else {
            $info = array("msg" => "Plugin not Activated!", 'rf' => "", "error" => 0);
        }
        echo json_encode($info);
    }

    public function to_deactive($plugin, $a = false) {
        $activePl = get_option('plugins');
        if (!empty($activePl)) {
            $activePl_array = unserialize($activePl);
        }

        //var_dump($plugin);
        $deActFn = str_replace(".php", "", $plugin);
        if (function_exists($deActFn . "__deAct")) {
            $deActF = $deActFn . "__deAct";
            $deActF();
        }

        unset($activePl_array[array_search($plugin, $activePl_array)]);
        $activePl = serialize($activePl_array);
        if (update_option('plugins', $activePl)) {
            $functionName = "deactive_$plugin";
            $functionName = str_replace(".php", "", $functionName);
            if (function_exists($functionName)) {
                $functionName();
                echo $functionName;
            }
            $info = array("msg" => "Plugin Deactiveted!", 'rf' => "", "error" => 0);
        } else {
            $info = array("msg" => "Plugin not Deactiveted!", 'rf' => "", "error" => 0);
        }
        if (!$a) {
            echo json_encode($info);
        }
    }

    public function pluginView() {
        ?>
        <?php
        $fields = getTbleField('plugin');
        //var_dump($fields);
        $avPl = $this->AvailablePlugins();
        $activePl = unserialize(get_option('plugins'));
        if (!is_array($activePl)) {
            $activePl = array();
        }
        //var_dump($avPl);
        ?>
        <table class="table table-striped table-responsive-lg table-cms">
            <tr>
                <th></th>
                <th>Plugin</th>
                <th>Description</th>
                <th><button type="button" class="btn btn-cms-default float-right" onclick="updateCheck()">Check Updates</button></th>
            </tr>
            <?php
            foreach ($avPl as $file => $pl) {
                //var_dump($pl);
                //                $updateAv = false;
                //                if ($pl['Version'] < $remote_pluginInfo['Version']) {
                //                    $updateAv = true;
                //                }
                $indPath = str_replace($this->pluginDIR, "", $pl['dir']);
                echo "<tr id='$indPath' class=\"indPlugin\" plugin=\"$indPath\">";
                ?>
                <td></td>
                <td>
                    <strong><?php echo $pl['Name'] ?></strong><br>
                </td>
                <td>
                    <?php echo $pl['Description'] ?><br>
                    <strong>Version : </strong> <?php echo $pl['Version'] ?> &nbsp;&nbsp; <strong>Author : </strong><?php echo $pl['Author'] ?>
                </td>
                <td>
                    <div class='PluginController'>
                        <a href="javascript:" onclick="updatePlugin('<?php echo $indPath ?>', this, 'update')" class="updateButton collapse">Update</a>
                        <a href='javascript:' onclick="updateInfo('<?php echo $indPath ?>')" class="updateButton up_info collapse">Info</a>
                        <?php if (in_array($file, $activePl)) { ?>
                            <a class='text-danger' href="javascript:" onclick="Act('plDact=<?php echo base64_encode($file) ?>', false, true)" >Deactive</a>
                        <?php } else { ?>
                            <a href="javascript:" onclick="Act('plAct=<?php echo base64_encode($file) ?>', false, true)" >Active</a>
                        <?php }
                        ?>
                        &nbsp;|&nbsp;<a onclick="Act('plDel=<?php echo base64_encode($pl['dir']); ?>', true, true)" href="javascript:">Delete</a>
                    </div>
                </td>
                <?php
                echo "</tr>";
            }
            ?>
            <script>
                $(document).ready(function() {
                    //updateCheck();
                });

                function updateCheck() {
                    //alert(pdata);
                    var url = "";
                    msg("<i class=\"fas fa-sync fa-spin\"></i> &nbsp;&nbsp;Update Checking, Hold on Please", "G");
                    var url = "index.php" + url; // the script where you handle the form input.
                    var fd = {ajx_action: 'pluginUpdateCkeck'};
                    jQuery.ajax({
                        type: "POST",
                        url: url,
                        data: fd, // serializes the form's elements.
                        success: function(data)
                        {
                            var obj = JSON.parse(data);
                            for (var prop in obj) {
                                var id = obj[prop];
                                $("#" + id).find('.updateButton').show();
                                $("#" + id).find('.up_info').show();
                            }
                            setTimeout(function() {
                                $(".msg").hide();
                            }, 3000);
                        }
                    });
                }

                function updatePlugin(basename, _this, typ) {

                    if (typ = 'update') {
                        $(_this).html('Updating...');
                    } else {
                        $(_this).html('Downloading...');
                    }
                    var data = {ajx_action: "UpdateDownload", basename: basename};
                    jQuery.post('index.php', data, function(response) {
                        response = response.trim()
                        if (response == "1") {
                            if (typ == 'update') {
                                msg("Plugin Updateed", 'G');
                                //load_list();
                                setTimeout(function() {
                                    jQuery(_this).hide('fast');
                                }, 1000);

                            } else {
                                msg("Plugin Download", 'G');
                                //load_list();
                                setTimeout(function() {
                                    jQuery(_this).hide('fast');
                                }, 1000);
                            }
                        } else {
                            alert(response);
                        }
                        //alert(response);
                    });

                }

                function updateInfo(basename) {
                    var data = {ajx_action: "Updateinfo", basename: basename};
                    jQuery.post('index.php', data, function(response) {
                        $.fancybox.open(response);
                    });
                }
            </script>
        </table>
        <?php
    }

}

function pluginUpdateCkeck() {
    global $plugins;
    $plugins_names = @$_POST['pluginData'];

    $dir = $plugins->pluginDIR;
    $remotePath = $plugins->updatePath;

    $update = array();
    foreach ($plugins->AvailablePlugins() as $file => $indDir) {
        $info = pathinfo($indDir['dir']);
        $rr = $plugins->is_plugin($remotePath . $info['basename'] . "/readme.txt");
        //var_dump($rr);
        //var_dump($rr['Version']);
        if ($rr['Version'] > $indDir['Version']) {
            $update[] = $info['basename'];
        }
    }

    echo json_encode($update);
}

function UpdateDownload() {
    global $plugins;
    $tempDir = TEMP;
    $siatexPluginDir = $plugins->updatePath;
    $pluginInc = PLUG_DIR;
    if (isset($_POST['basename'])) {
        $folderName = $_POST['basename'];
        //var_dump($_POST['basename']);
        $zipFileFilter = str_replace(' ', '-', $folderName);
        $remoteFile = "$siatexPluginDir" . $zipFileFilter . ".zip";
        //var_dump($remoteFile);
        $tempFile = $tempDir . md5($folderName) . ".zip";
        //echo $remoteFile;
        $down = file_put_contents($tempFile, @fopen($remoteFile, 'r'));
        if (!$down) {
            echo "Plugin Unable to download";
        }
        $extTo = $pluginInc;
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

function Updateinfo() {
    global $plugins;
    $tempDir = TEMP;
    $siatexPluginDir = $plugins->updatePath;
    $pluginInc = PLUG_DIR;
    if (isset($_POST['basename'])) {
        $folderName = $_POST['basename'];
        $zipFileFilter = "";
        //var_dump($_POST['basename']);
        $file = "$siatexPluginDir" . $zipFileFilter . "$folderName/readme.txt";

        $fp = fopen($file, 'r');

        // Pull only the first 8kiB of the file in.
        $file_data = fread($fp, 99999);

        // PHP will close file handle, but we are good citizens.
        fclose($fp);

        // Make sure we catch CR-only line endings.
        $file_data = str_replace("\r", "\n", $file_data);
        $default_headers = array(
            'Name' => 'Plugin Name',
            'PluginURI' => 'Plugin URI',
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

function pluginsStore() {

    $showactivPlugin = false;
    global $plugins;
    $regestryJson = file_get_contents_curl($plugins->updatePath . "registry.json");
    $storeObject = json_decode($regestryJson);

    $activePl = unserialize(get_option('plugins'));

    $inSystem = $plugins->AvailablePlugins();
    // var_dump($inSystem,$activePl);
//    echo "<pre>";
//    var_dump($storeObject);
//    echo "</pre>";
    echo "<div class=\"remotePluginContent row\">";
    foreach ($storeObject as $plugin) {

        $file = $plugin->dir . ".php";
        $btn = "";
        $dact = false;
        $get = false;
        $act = false;
        if (isset($inSystem[$file]) && in_array($file, $activePl)) {
            $dact = TRUE;
        } else {
            if (!isset($inSystem[$file])) {
                //to download
                $get = TRUE;
            } else {
                $act = TRUE;
                // echo "Active";
            }
        }

        $enqNAme = base64_encode($file);

        if ($get || $showactivPlugin) {
            echo "<div class=\"col-sm-4 mb-4\">                      
                    <div class=\"singlePlugin\" data-plugins-info=\"$plugin->name $plugin->description\">
                        <div class='singlePlugin-header'>
                            <h4>$plugin->name</h4>
                            <div class='storeItemControl'>";
            echo $act ? "<a href=\"javascript:\" class='storeActBtn btn btn-cms-primary' onclick=\"Act('plAct=$enqNAme', false, true)\" >Active</a>" : "<a href=\"javascript:\" class='storeActBtn btn btn-cms-primary collapse' onclick=\"Act('plAct=$enqNAme', false, true)\" >Active</a>";

            echo $dact ? "<a class='storeDactBtn btn btn-danger'  href=\"javascript:\" onclick=\"Act('plDact=$enqNAme', false, true)\" >Deactive</a>" : "<a class='storeDactBtn btn btn-danger collapse'  href=\"javascript:\" onclick=\"Act('plDact=$enqNAme', false, true)\" >Deactive</a>";

            echo $get ? "<button type=\"button\" onclick=\"downloadPlugin('$plugin->dir',this,'download')\" class=\"btn btn-cms-primary getBtn\"><i class=\"fas fa-plus\"></i>&nbsp;Get</button>" : "<button type=\"button\" onclick=\"downloadPlugin('$plugin->dir',this,'download')\" class=\"btn btn-cms-primary getBtn collapse\"><i class=\"fas fa-plus\"></i>&nbsp;Get</button>";

            echo"</div>
                        </div>
                        <p>
                            <strong>Author :</strong> $plugin->author &nbsp;<br>
                            <strong>Description :</strong>$plugin->description
                        </p>
                    </div>
                </div>";
        }
    }
    echo "</div>";
}
