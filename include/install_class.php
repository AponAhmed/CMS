<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php

class InstallCms {

    function __construct($conn) {
        $this->connection = $conn;
        $vv = mysqli_get_server_info($conn);
        preg_match('/[1-9].[0-9].[1-9][0-9]/', $vv, $Vmatch);
        $vv = $Vmatch[0];
        if ($vv > 5.6) {
            //$this->post = str_replace("PRIMARY KEY (`ID`)", $this->postFulltex, $this->post);
        } else {
            // $this->post = str_replace("[%%FULlTEX%%]", "", $this->post);
        }
    }

    private $connection = false;
    private $tables = array("options", "post", "post_meta", "termmeta", "terms", "term_relationships", "term_taxonomy", "user", 'metaTablestr');
    private $mysqli = true;
    private $options = "CREATE TABLE IF NOT EXISTS `options` (
		`option_id` bigint(20) NOT NULL AUTO_INCREMENT,
		`option_name` varchar(191) NOT NULL,
		`option_value` longtext NOT NULL,
		PRIMARY KEY (`option_id`),
		UNIQUE KEY `option_name` (`option_name`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
    private $post = "CREATE TABLE IF NOT EXISTS `post` (
            `ID` bigint(11) NOT NULL AUTO_INCREMENT,
            `post_date_gmt` datetime NOT NULL,
            `post_content` longtext NOT NULL,
            `post_title` varchar(255) NOT NULL,
            `post_status` varchar(20) NOT NULL DEFAULT 'draft',
            `post_name` varchar(200) NOT NULL,
            `post_modified_gmt` datetime NOT NULL,
            `post_parent` int(11) NOT NULL,
            `menu_order` int(11) NOT NULL,
            `guid` varchar(255) NOT NULL,
            `post_type` varchar(50) NOT NULL,
          PRIMARY KEY (`ID`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
    private $post_meta = "CREATE TABLE IF NOT EXISTS `post-meta` (
		`meta_id` int(11) NOT NULL AUTO_INCREMENT,
		`post_id` bigint(20) NOT NULL,
		`meta_key` varchar(255) NOT NULL,
		`meta_value` longtext NOT NULL,
		PRIMARY KEY (`meta_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
    private $termmeta = "CREATE TABLE IF NOT EXISTS `termmeta` (
		`meta_id` bigint(20) NOT NULL AUTO_INCREMENT,
		`term_id` int(11) NOT NULL,
		`meta_key` varchar(255) NOT NULL,
		`meta_value` longtext NOT NULL,
		PRIMARY KEY (`meta_id`),
		UNIQUE KEY `term_id` (`term_id`,`meta_key`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
    private $terms = "CREATE TABLE IF NOT EXISTS `terms` (
		`term_id` bigint(20) NOT NULL AUTO_INCREMENT,
		`name` varchar(200) NOT NULL,
		`slug` varchar(200) NOT NULL,
		`term_group` int(11) NOT NULL,
		PRIMARY KEY (`term_id`),
		UNIQUE KEY `name` (`name`,`slug`),
		UNIQUE KEY `name_2` (`name`,`slug`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
    private $term_relationships = "CREATE TABLE IF NOT EXISTS `term_relationships` (
		`object_id` int(11) NOT NULL,
		`texo_id` int(11) NOT NULL,
		PRIMARY KEY (`object_id`,`texo_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
    private $term_taxonomy = "CREATE TABLE IF NOT EXISTS `term_taxonomy` (
		`taxonomy_id` bigint(20) NOT NULL AUTO_INCREMENT,
		`term_id` bigint(20) NOT NULL,
		`taxonomy` varchar(32) NOT NULL,
		`description` text NOT NULL,
		`test` int(11) NOT NULL,
		PRIMARY KEY (`taxonomy_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
    private $user = "CREATE TABLE IF NOT EXISTS `user` (
		`ID` int(11) NOT NULL AUTO_INCREMENT,
		`user_login` varchar(60) NOT NULL,
		`user_pass` varchar(255) NOT NULL,
		`user_email` varchar(100) NOT NULL,
		`user_status` int(11) NOT NULL,
		`display_name` varchar(250) NOT NULL,
		PRIMARY KEY (`ID`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1";
    private $metaTablestr = "CREATE TABLE IF NOT EXISTS `meta` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `data_id` int(11) NOT NULL,
  `meta_key` varchar(255) NOT NULL,
  `meta_value` longtext NOT NULL,
  `meta_table` varchar(255) NOT NULL,
  PRIMARY KEY (`meta_id`),
  UNIQUE KEY `data_id` (`data_id`,`meta_key`,`meta_table`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
    var $postFulltex = ",
  FULLTEXT KEY `post_title` (`post_title`),
  FULLTEXT KEY `post_content` (`post_content`),
  FULLTEXT KEY `post_name` (`post_name`)";

    public function createTable() {

        foreach ($this->tables as $table) {
            if (!empty($this->$table)) {
                mysqli_query($this->connection, $this->$table);
                //var_dump(mysqli_error($this->connection));
            }
        }
        $this->ip2cData();
    }

    public function initUser($data) {
        $data['site_password'] = md5($data['site_password']);
        $userInitSql = "INSERT INTO `user` ( `user_login`, `user_pass`, `user_email`, `user_status`, `display_name`) 
			VALUES
			('$data[site_user]', '$data[site_password]', '$data[user_email]', 1, '$data[site_user_d_name]');";
        $res = mysqli_query($this->connection, $userInitSql);

        $id = mysqli_insert_id($this->connection);
        $userMetaSql = "INSERT INTO `meta` (`data_id`, `meta_key`, `meta_value`, `meta_table`) VALUES ('$id', 'user_level', 'S', 'user');";
        $res = mysqli_query($this->connection, $userMetaSql);

        $this->demoPost();
        return $res;
        //var_dump($data);
    }

    public function demoPost() {
        $DemoPost = "INSERT INTO `post` (`post_date_gmt`, `post_content`, `post_title`, `post_status`, `post_name`, `post_modified_gmt`, `post_parent`, `menu_order`, `guid`, `post_type`) VALUES ('', 'Demo Page', 'Home', 'published', 'home', '', '', '1', '', 'page');";
        $res = mysqli_query($this->connection, $DemoPost);

        $hID = mysqli_insert_id($this->connection);
        $optionInitSql = "INSERT INTO `options` ( `option_name`, `option_value`) 
			VALUES
			('front_page', '$hID');";
        $res = mysqli_query($this->connection, $optionInitSql);
        return $res;
    }

    public function initOption($data) {
        $optionInitSql = "INSERT INTO `options` ( `option_name`, `option_value`) 
			VALUES
			('site-name', '$data[site_name]');";
        $res = mysqli_query($this->connection, $optionInitSql);
        return $res;
    }

    public function initSiteUrl($url) {
        $optionInitSql = "INSERT INTO `options` ( `option_name`, `option_value`) 
			VALUES
			('site_url', '$url');";
        $res = mysqli_query($this->connection, $optionInitSql);
        return $res;
    }

    public function ip2cData() {
        $dirInfo = pathinfo(__FILE__);
        $file = $dirInfo['dirname'] . "/ip2c.sql";
        $file_data = file($file);
        $output = '';
        $count = 0;
        foreach ($file_data as $row) {
            $start_character = substr(trim($row), 0, 2);
            if ($start_character != '--' || $start_character != '/*' || $start_character != '//' || $row != '') {
                $output = $output . $row;
                $end_character = substr(trim($row), -1, 1);
                if ($end_character == ';') {
                    if (!mysqli_query($this->connection, $output)) {
                        $count++;
                    }
                    $output = '';
                }
            }
        }
    }

}

?>