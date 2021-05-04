<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php

//last Update Thu 2nd Aug 2018
//$_SESSION['query_count'] = 0;

class DB {

    //
    private $dbHost = DB_HOST;
    private $dbUser = DB_USER;
    private $dbPass = DB_PASS;
    private $db = DB;
    //Connection Init ...
    public $conn;
    //Enable Mysqli 
    public $mySqli = true;
    //Custom Mysql Error
    public $error;
    public $info;
    public $eff;
    public $insert_id;
    var $query_count = 0;
    var $query_array = array();
    var $report = false;
    var $version_str;
    var $version;

    //----------------------------------------

    function __construct() {
        if ($this->mySqli) {
            //Mysqli Connect
            $conn = mysqli_connect($this->dbHost, $this->dbUser, $this->dbPass, $this->db);
            if (!$conn) {
                echo "Connection Error " . mysqli_connect_error();
                $this->error = "Connect Error (" . mysqli_connect_errno() . ") " . mysqli_connect_error();
                exit;
            } else {
                $this->version_str = mysqli_get_server_info($conn);
            }
        } else {
            //Mysql Connect
            $conn = mysql_connect($this->dbHost, $this->dbUser, $this->dbPass);
            mysql_select_db($this->db);

            if (!$conn) {
                echo "Connection Error " . mysql_connect_error();
                ;
                $this->error = "Connect Error (" . mysql_connect_errno() . ") " . mysql_connect_error();
                exit;
            } else {
                $this->version_str = mysql_get_server_info($conn);
            }
        }
        $this->conn = $conn;

        if ($this->mySqli) {
            $this->info = mysqli_info($this->conn);
        } else {
            $this->info = mysql_info($this->conn);
        }

        preg_match('/[1-9].[0-9].[1-9][0-9]/', $this->version_str, $Vmatch);
        $this->version = $Vmatch[0];
    }

    public function query($sql) {
        //var_dump($sql);
        //exit;
        //return;
        if (empty($sql)) {
            return false;
        }

        if ($this->mySqli) {
            $exe = mysqli_query($this->conn, $sql);
            if ($this->report) {
                $this->query_array[] = array('time' => time(), 'sql' => $sql, 'status' => $exe ? true : false);
                $this->query_count ++;
            }
            if (!$exe) {
                $this->error = "Query Error ( " . mysqli_error($this->conn) . " ) ";
            } else {
                $this->info = mysqli_info($this->conn);
                $this->eff = mysqli_affected_rows($this->conn);
                //var_dump($exe);
                //$this->error = "No error !";
                $this->insert_id = mysqli_insert_id($this->conn);
                return $exe;
            }
        } else {
            $exe = mysql_query($sql, $this->conn);
            if ($this->report) {
                $this->query_array[] = array('time' => time(), 'sql' => $sql, 'status' => $exe ? true : false);
                $this->query_count ++;
            }
            if (!$exe) {
                $this->error = "Query Error ( " . mysql_error($this->conn) . " ) ";
            } else {
                $this->info = mysql_info($this->conn);
                $this->eff = mysql_affected_rows($this->conn);
                $this->insert_id = mysql_insert_id($this->conn);
                return $exe;
            }
        }
    }

    public function select($table, $fields = "*", $condition = "1", $returnSQL = false) {
        if (!empty($table)) {
            $rows = array();
            $sql = "SELECT $fields FROM $table WHERE $condition";
            //	var_dump($sql);
            if ($returnSQL == true) {
                return $sql;
            }
            $res = $this->query($sql);
            if ($res) {
                if ($this->mySqli) {
                    //mySqli--
                    if (mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            $rows[] = $row;
                        }
                    }
                } else {
                    //mySql--
                    if (mysql_num_rows($res) > 0) {
                        while ($row = mysql_fetch_assoc($res)) {
                            $rows[] = $row;
                        }
                    }
                }
                //var_dump($row);
                return $rows;
            } else {
                return false;
            }
        }
    }

    public function insert($table = "", $formData = array()) {

        $sql = "";
        $fields = array();
        $values = array();
        if (!empty($formData)) {
            $formData = $this->cln($formData);
            foreach ($formData as $field => $value) {
                $fields[] = "" . $field . "";
                $values[] = "'" . $value . "'";
            }
            $fields = implode(",", $fields);
            $values = implode(",", $values);
            $sql = "INSERT INTO $table($fields)VALUES($values);";
        }
        //var_dump($sql);
        //return $sql;
        return $this->query($sql);

        if ($this->query($sql)) {
            return mysqli_insert_id($this->conn);
        } else {
            return $this->error;
        }
    }

    public function delete($table, $condition) {
        if (!empty($table) && !empty($condition)) {
            $sql = "DELETE FROM $table WHERE $condition";
            return $this->query($sql);
        }
    }

    public function update_($table = "", $formfield = array(), $condition = "1") {
        if (!empty($table)) {
            $sql = "UPDATE $table SET ";
            $fieldsArray = array();
            foreach ($formfield as $field => $value) {
                $fieldsArray[] = "$field='$value'";
            }
            $fieldsArray = implode(",", $fieldsArray);
            $sql.="$fieldsArray WHERE $condition";
            $sql;

            //var_dump($sql);
            //return $sql;
            return $this->query($sql);
        }
    }

    function update($table_name, $form_data, $where_clause = '') {
        $form_data = $this->cln($form_data);
        // check for optional where clause
        $whereSQL = '';
        if (!empty($where_clause)) {
            // check to see if the 'where' keyword exists
            if (substr(strtoupper(trim($where_clause)), 0, 5) != 'WHERE') {
                // not found, add key word
                $whereSQL = " WHERE " . $where_clause;
            } else {
                $whereSQL = " " . trim($where_clause);
            }
        }
        // start the actual SQL statement
        $sql = "UPDATE " . $table_name . " SET ";
        // loop and build the column /
        $sets = array();
        foreach ($form_data as $column => $value) {
            $sets[] = "" . $column . " = '" . $value . "'";
        }
        $sql .= implode(', ', $sets);
        // append the where statement
        $sql .= $whereSQL;
        //var_dump($sql);
        return $this->query($sql);
    }

    public function rows($table, $fields = "*", $condition = "1") {
        $sql = "SELECT $fields FROM $table WHERE $condition;";
        $res = $this->query($sql);
        if ($res) {
            if ($this->mySqli) {
                //mySqli--
                return mysqli_num_rows($res);
            } else {
                //mySql--
                return mysql_num_rows($res);
            }
        }
    }

    function escape($value) {
        $return = '';
        for ($i = 0; $i < strlen($value); ++$i) {
            $char = $value[$i];
            var_dump($char);
            $ord = ord($char);
            if ($char !== "'" && $char !== "\"" && $char !== '\\' && $ord >= 32 && $ord <= 126)
                $return .= $char;
            else
                $return .= '\\x' . dechex($ord);
        }
        return $return;
    }

    public function cln($data, $md5 = false) {
        if (!is_array($data)) {
            //return $this->escape($data);
            if ($this->mySqli) {
                if ($md5 == 1)
                    return md5(mysqli_real_escape_string($this->conn, trim($data)));
                return mysqli_real_escape_string($this->conn, trim($data));
            } else {
                if ($md5 == 1)
                    return md5(mysql_real_escape_string(trim($data)));
                return mysql_real_escape_string(trim($data));
            }
        }
        else {
            $clean_array = array();
            if ($data)
                foreach ($data as $key => $value) {
                    $clean_array[$key] = $this->cln($value, $md5);
                }
            return $clean_array;
        }
    }

    public function info($cnn) {
        
    }

//  
//  
//pagination---------------------------------------------------------------------------------------------------
//   
//
    var $total;
    var $php_self;
    var $rows_per_page = 10; //Number of records to display per page
    var $total_rows = 0; //Total number of rows returned by the query
    var $links_per_page = 5; //Number of links to display per page
    var $append = ""; //Paremeters to append to pagination links
    var $sql = "";
    var $debug = true;
    var $page = 1;
    var $max_pages = 0;
    var $offset = 0;

    public function paginate($table, $field = "*", $condition = "1", $rows_per_page = false, $unique = false) {
        if ($rows_per_page) {
            $this->rows_per_page = $rows_per_page;
        }
        $this->total_rows = $this->rows($table, $field, $condition);
        if (!isset($_GET['page'])) {
            $this->page = @$_SESSION['current_page' . $unique];
        } else {
            $this->page = trim($_GET['page']);
            $_SESSION['current_page' . $unique] = $this->page;
        }

        $this->page = @$_SESSION['current_page' . $unique];



        //Max number of pages
        $this->max_pages = ceil($this->total_rows / $this->rows_per_page);
        if ($this->links_per_page > $this->max_pages) {
            $this->links_per_page = $this->max_pages;
        }

        //Check the page value just in case someone is trying to input an aribitrary value
        if ($this->page > $this->max_pages || $this->page <= 0) {
            $this->page = 1;
        }

        //Calculate Offset
        $this->offset = $this->rows_per_page * ($this->page - 1);

        $limitSql = " LIMIT {$this->offset}, {$this->rows_per_page}";
        $condition = $condition . $limitSql;
        //var_dump($table,$field,$condition);
        $rows = $this->select($table, $field, $condition);
        return $rows;
    }

    function renderFirst($tag = 'First') {
        if ($this->total_rows == 0)
            return FALSE;

        if ($this->page == 1) {
            return "<span class=disabled_tnt_pagination>First </span>";
        } else {
            //return '<a href="' . $this->php_self . '?page=1' . $this->append . '">' . $tag . '</a> ';
            return ' <a href="javascript:void(0)" onclick="load_list(\'page=1\')">' . $tag . '</a>';
        }
    }

    function renderLast($tag = 'Last') {
        if ($this->total_rows == 0)
            return FALSE;

        if ($this->page == $this->max_pages) {
            return "<span class=disabled_tnt_pagination>Last </span>";
        } else {
            //return ' <a href="' . $this->php_self . '?page=' . $this->max_pages . $this->append . '">' . $tag . '</a>';
            return ' <a href="javascript:void(0)" onclick="load_list(\'page=' . $this->max_pages . '\')">' . $tag . '</a>';
        }
    }

    function renderNext($tag = 'Next') {
        if ($this->total_rows == 0)
            return FALSE;

        if ($this->page < $this->max_pages) {
            //return '<a href="' . $this->php_self . '?page=' . ($this->page + 1) .  $this->append . '">' . $tag . '</a>';
            return '<a href="javascript:void(0)" onclick="load_list(\'page=' . ($this->page + 1) . '\')">' . $tag . '</a>';
        } else {
            return "<span class=disabled_tnt_pagination>Next </span>";
        }
    }

    function renderPrev($tag = 'Previous') {
        if ($this->total_rows == 0)
            return FALSE;

        if ($this->page > 1) {
            //return ' <a href="' . $this->php_self . '?page=' . ($this->page - 1) .  $this->append . '">' . $tag . '</a>';
            return ' <a href="javascript:void(0)" onclick="load_list(\'page=' . ($this->page - 1) . '\')">' . $tag . '</a>';
        } else {
            return "<span class=disabled_tnt_pagination>Previous</span>";
        }
    }

    function renderNav($prefix = '<span class="page_link">', $suffix = '</span>') {
        if ($this->total_rows == 0)
            return FALSE;

        $batch = ceil($this->page / $this->links_per_page);
        $end = $batch * $this->links_per_page;
        if ($end == $this->page) {
            //$end = $end + $this->links_per_page - 1;
            //$end = $end + ceil($this->links_per_page/2);
        }
        if ($end > $this->max_pages) {
            $end = $this->max_pages;
        }
        $start = $end - $this->links_per_page + 1;
        $links = '';

        for ($i = $start; $i <= $end; $i ++) {
            if ($i == $this->page) {
                $links .= $prefix . "<span class=disabled_tnt_pagination> $i </span>  " . $suffix;
            } else {
                //$links .= ' ' . $prefix . '<a href="' . $this->php_self . '?page=' . $i .  $this->append . '">' . $i . '</a>' . $suffix . ' ';
                $links .= ' ' . $prefix . '<a href="javascript:void(0)" onclick="load_list(\'page=' . $i . '\')">' . $i . '</a>' . $suffix . ' ';
            }
        }

        return $links;
    }

    function renderTotal() {
        $from = ($this->page - 1) * $this->rows_per_page + 1;
        $to = ($from + $this->rows_per_page) - 1 > $this->total_rows ? $this->total_rows : ($from + $this->rows_per_page) - 1;
        return $from . '-' . $to . '&nbsp;of&nbsp;' . $this->total_rows . '&nbsp;';
    }

    /**
     * Display full pagination navigation
     *
     * @access public
     * @return string
     */
    function renderFullNav() {
        return $this->renderFirst() . '&nbsp;' . $this->renderPrev() . '&nbsp;' . $this->renderNav() . '&nbsp;' . $this->renderNext() . '&nbsp;' . $this->renderLast();
    }

    //DB Information


    public function table_list() {
        //SQL
        //SELECT table_name FROM information_schema.tables where table_schema='<your_database_name>';
        $tables = $this->select("information_schema.tables", "table_name", "table_schema='$this->db'");
        $tableList = array();
        foreach ($tables as $table) {
            $tableList[] = $table['table_name'];
        }
        return $tableList;
    }

    public function dump_json($dir, $filter = array()) {
        $exclude = array('ip2nation', 'ip2nationCountries');
        $dataArr = array();
        foreach ($this->table_list() as $table) {
            // var_dump($table);
            if (in_array($table, $exclude)) {
                continue;
            }
            $res = "";
            $rows = array();
            //$data=$this->select($table);
            $res = $this->query("SELECT * FROM `$table`");
            // var_dump("SELECT * FROM `$table`");
            if ($res) {
                if ($this->mySqli) {
                    //mySqli--
                    if (mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            if (!empty($filter)) {
                                $row = $this->dump_filter($row, $filter);
                            }
                            $rows[] = $row;
                        }
                    }
                } else {
                    //mySql--
                    if (mysql_num_rows($res) > 0) {
                        while ($row = mysql_fetch_assoc($res)) {
                            if (!empty($filter)) {
                                $row = $this->dump_filter($row, $filter);
                            }
                            $rows[] = $row;
                        }
                    }
                }
                //var_dump($row);
                // return $rows;
            }

            //var_dump($rows);
            $tableTitle = "\n\n//---Table Name : $table---\n\n";
            $tableDataJSON = json_encode($rows);
            //$dataString.=$tableTitle;
            $dataArr[$table] = $rows;
        }
// var_dump($dataArr);
// exit;
        $dataString = json_encode($dataArr);
        $myfile = fopen($dir, "w") or die("Unable to open file!");
        $created = fwrite($myfile, $this->encrypt_decrypt("encrypt", $dataString));
        fclose($myfile);
        return $created;
    }

    public function dump_filter($row, $filter) {
        $find = array_keys($filter);
        $replace = array_values($filter);
        $newRow = array();
        foreach ($row as $k => $v) {
            $newRow[$k] = str_ireplace($find, $replace, $v);
        }
        //$new_string = str_ireplace($find, $replace, $string);
        return $newRow;
    }

    public function encrypt_decrypt($action, $string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'CMS Data key';
        $secret_iv = 'CMS Data iv';
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

}
