<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of error_log
 *
 * @author Siatex
 */
class error_log {

    //put your code here
    var $dataField = 'error_log';
    var $total_error = 0;
    var $total_error_nt = 0;
    var $total_error_er = 0;
    var $data = array();
    var $desable = true;

    public function __construct() {
        add_option($this->dataField, "");
        
        $rr = @unserialize(get_option($this->dataField));
        //var_dump($rr);
        $this->data = $rr ? $rr : array();
        $this->total_error = count($this->data);
    }

    public function add_exception($array = array()) {
        if ($this->desable) {
            return;
        }
        if (!admin_login()) {
            return;
        }
        if (!empty($array)) {
            array_push($this->data, $array);
            update_option($this->dataField, serialize($this->data));
        } else {
            return;
        }
    }

    public function show_exception() {
        if (count($this->data) > 0) {
            echo "<a href='javascript:void(0)'> All(" . $this->total_error . ")</a> &nbsp;|&nbsp;";
            echo "<a href='javascript:void(0)'> Notice(" . $this->total_error_nt . ")</a> &nbsp;|&nbsp;";
            echo "<a href='javascript:void(0)'> Error(" . $this->total_error_er . ")</a> &nbsp;|&nbsp;";

            echo "<table class='ErrorLog table table-striped table-responsive-sm table-cms'>";
            echo "<tr><th>Time</th><th>Error</th></tr>";
            // ksort($this->data);
            foreach ($this->data as $k => $row) {
                echo "<tr><td>" . date('d/m/y h:i a', $row[0]) . "</td><td class='errArea'>$row[1]<div class='act'><a href='javascript:void(0)' onclick='removeSingle($k)'><i class='fas fa-trash-alt'></i></a></div></td></tr>";
            }
            echo "</table>";
        } else {
            echo "No Error";
        }
    }

    public function clean_exception() {
        return update_option($this->dataField, array());
    }

    public function removeSingle($indx) {
        // var_dump($this->data);
        $data = $this->data;
        unset($data[$indx]);
        $this->data = $data;
        //var_dump($this->data);
        return update_option($this->dataField, $this->data);
    }

}
