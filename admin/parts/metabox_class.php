<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of metabox_class
 *
 * @author nrb
 */
class metabox_class {

    //put your code here
    private $boxes;
    private $type;
    private $position;
    private $boxOpen = array('Publish');

    public function __construct() {
        global $metaBoxes, $metaBoxOpen;
        usort($metaBoxes, function($a, $b) {
            return @$a['order'] - @$b['order'];
        });
        if (is_array($metaBoxOpen)) {
            $this->boxOpen = array_merge($this->boxOpen, $metaBoxOpen);
        }
        //var_dump($metaBoxes);
        $this->boxes = $metaBoxes;
    }

    public function GetMetaBoxes($arg = array()) {
        $pos = @$arg['position'];
        $type = @$arg['type'];

        foreach ($this->boxes as $MBox) {
            //$f = find_array_with_val($metaBoxes, 'post', 'type');
            //var_dump($f);
            $types = @explode(",", $MBox['type']);
            //var_dump($MBox);
            if ($pos == 'dashboard' && $MBox['position'] == 'dashboard') {
                //var_dump($MBox);

                if (defined('WEB_APP') && WEB_APP == true) {
                    if (@$MBox['web_app']) {
                        $this->createBox($MBox);
                    }
                } else {
                    $this->createBox($MBox);
                }
            } else {
                if ((@in_array($type, $types) || $types[0] == "all" ) && $MBox['position'] == $pos) {
                    //var_dump($MBox);
                    $this->createBox($MBox);
                }
            }
        }
    }

    public function createBox($array) {
        //echo $this->BoxFrame($array);
        //var_dump($array);
        $dClass = 'mt_' . mt_rand();
        $id = isset($array['id']) ? "id='$array[id]'" : "";
        $style = isset($array['display']) && $array['display'] === false ? "style=\"display:none\"" : "";
        $boxContentStyle = "style='display:none'";
        if (in_array($array['title'], $this->boxOpen) || $array['position'] != 'side' || (isset($array['open']) && $array['open'] === true)) {
            $boxContentStyle = "";
        }
        if (isset($array['open']) && $array['open'] === false) {
            $boxContentStyle = "style='display:none'";
        }
        echo "<div $id class='" . @$array['class'] . " $dClass' $style><div class=\"metaBox $array[position]\">
                    <div class=\"meta-box-header\">
                        <div class='meta-box-title'>$array[title]</div>
                        <div class='meta-box-triger'><a href=\"javascript:\"><span></span></a></div>
                     </div>
                     <div class=\"meta-box-content $array[calback] \" $boxContentStyle> ";
        echo $this->getBoxContent($array);
        echo "</div>
           </div></div>
      ";
        //<script> $(\".$dClass\").sortable();</script>
    }

    private function getBoxContent($arg) {
        $calback = $arg['calback'];
        //var_dump($calback);
        $p = "";
        if (isset($arg['param'])) {
            $p = $arg['param'];
        }
        if (function_exists($calback)) {
            return $calback($p);
        } else {
            return "CallBack Function Not Found !!";
        }
    }

}
