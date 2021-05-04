<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of theme-edit
 *
 * @author apon
 */
define('DS', DIRECTORY_SEPARATOR);
$CurThemeDir = THEME_DIR . current_theme_dir();
define('MAIN_DIR', $CurThemeDir);
define('EDITABLE_FORMATS', 'txt,php,htm,html,js,css,tpl,xml,md,json,jpg,png,gif'); // empty means all types
define('LOG_FILE', MAIN_DIR . DS . '.phedlog');
define('SHOW_PHP_SELF', false);
define('SHOW_HIDDEN_FILES', false);
define('HISTORY_PATH', MAIN_DIR . DS . '.phedhistory');
define('MAX_HISTORY_FILES', 5);
define('WORD_WRAP', true);

function file_to_history($file) {
    if (is_numeric(MAX_HISTORY_FILES) && MAX_HISTORY_FILES > 0) {
        $file_dir = dirname($file);
        $file_name = basename($file);
        $file_history_dir = HISTORY_PATH . DS . str_replace(MAIN_DIR, '', $file_dir);

        foreach ([HISTORY_PATH, $file_history_dir] as $dir) {
            if (file_exists($dir) === false || is_dir($dir) === false) {
                mkdir($dir);
            }
        }

        $history_files = scandir($file_history_dir);

        foreach ($history_files as $key => $history_file) {
            if (in_array($history_file, ['.', '..', '.DS_Store'])) {
                unset($history_files[$key]);
            }
        }

        $history_files = array_values($history_files);

        if (count($history_files) >= MAX_HISTORY_FILES) {
            foreach ($history_files as $key => $history_file) {
                if ($key < 1) {
                    unlink($file_history_dir . DS . $history_file);
                    unset($history_files[$key]);
                } else {
                    rename($file_history_dir . DS . $history_file, $file_history_dir . DS . $file_name . '.' . ($key - 1));
                }
            }
        }

        copy($file, $file_history_dir . DS . $file_name . '.' . count($history_files));
    }
}

class theme_edit {

//put your code here

    public function action() {
        if (isset($_POST['action'])) {
            if (isset($_POST['file']) && empty($_POST['file']) === false) {
                $formats = explode(',', EDITABLE_FORMATS);

                if (($position = strrpos($_POST['file'], '.')) !== false) {
                    $extension = substr($_POST['file'], $position + 1);
                } else {
                    $extension = null;
                }

                if (empty(EDITABLE_FORMATS) === false && empty($extension) === false && in_array(strtolower($extension), $formats) !== true) {
                    die('INVALID_EDITABLE_FORMAT');
                }

                if (strpos($_POST['file'], '../') !== false || strpos($_POST['file'], '..\'') !== false) {
                    die('INVALID_FILE_PATH');
                }
            }

            switch ($_POST['action']) {
                case 'open':
                    $_POST['file'] = urldecode($_POST['file']);
                    $fineNAme = MAIN_DIR . $_POST['file'];
                    $fineNAme = str_replace('\\', '/', $fineNAme);
                    if (isset($_POST['file']) && file_exists($fineNAme)) {
                        echo file_get_contents($fineNAme);
                    }
                    break;

                case 'save':
                    $file = MAIN_DIR . $_POST['file'];

                    if (isset($_POST['file']) && isset($_POST['data']) && (file_exists($file) === false || is_writable($file))) {
                        if (file_exists($file) === false) {
                            file_put_contents($file, $_POST['data']);

                            echo 'success|File saved successfully';
                        } else if (is_writable($file) === false) {
                            echo 'danger|File is not writable';
                        } else {
                            if (file_exists($_POST['file'])) {
                                file_to_history($file);
                            }

                            file_put_contents($file, $_POST['data']);

                            echo 'success|File saved successfully';
                        }
                    }
                    break;

                case 'make-dir':
                    $dir = MAIN_DIR . $_POST['dir'];

                    if (file_exists($dir) === false) {
                        mkdir($dir);

                        echo 'success|Directory created successfully';
                    } else {
                        echo 'warning|Directory already exists';
                    }
                    break;

                case 'reload':
                    echo $this->files();
                    break;

//        case 'password':
//            if (isset($_POST['password']) && empty($_POST['password']) === false) {
//                $contents = file(__FILE__);
//
//                foreach ($contents as $key => $line) {
//                    if (strpos($line, 'define(\'PASSWORD\'') !== false) {
//                        $contents[$key] = "define('PASSWORD', '" . hash('sha512', $_POST['password']) . "');\n";
//
//                        break;
//                    }
//                }
//
//                file_put_contents(__FILE__, implode($contents));
//
//                echo 'Password changed successfully.';
//            }
//            break;

                case 'delete':
                    if (isset($_POST['path']) && file_exists(MAIN_DIR . $_POST['path'])) {
                        $path = MAIN_DIR . $_POST['path'];

                        if ($_POST['path'] == '/') {
                            echo 'danger|Unable to delete main directory';
                        } else if (is_dir($path)) {
                            if (count(scandir($path)) !== 2) {
                                echo 'danger|Directory is not empty';
                            } else if (is_writable($path) === false) {
                                echo 'danger|Unable to delete directory';
                            } else {
                                rmdir($path);

                                echo 'success|Directory deleted successfully';
                            }
                        } else {
                            file_to_history($path);

                            if (is_writable($path)) {
                                unlink($path);

                                echo 'success|File deleted successfully';
                            } else {
                                echo 'danger|Unable to delete file';
                            }
                        }
                    }
                    break;

                case 'rename':
                    if (isset($_POST['path']) && file_exists(MAIN_DIR . $_POST['path']) && isset($_POST['name']) && empty($_POST['name']) === false) {
                        $path = MAIN_DIR . $_POST['path'];
                        $new_path = str_replace(basename($path), '', dirname($path)) . DS . $_POST['name'];

                        if ($_POST['path'] == '/') {
                            echo 'danger|Unable to rename main directory';
                        } else if (is_dir($path)) {
                            if (is_writable($path) === false) {
                                echo 'danger|Unable to rename directory';
                            } else {
                                rename($path, $new_path);

                                echo 'success|Directory renamed successfully';
                            }
                        } else {
                            file_to_history($path);

                            if (is_writable($path)) {
                                rename($path, $new_path);

                                echo 'success|File renamed successfully';
                            } else {
                                echo 'danger|Unable to rename file';
                            }
                        }
                    }
                    break;
            }

            exit;
        }
    }

    function files($dir = false, $first = true) {
        $data = '';

        if (!$dir) {
            $dir = MAIN_DIR;
        }
        if ($first === true) {
            $data .= '<ul><li data-jstree=\'{ "opened" : true }\'><a href="javascript:void(0);" class="open-dir" data-dir="/">' . basename($dir) . '</a>';
        }

        $formats = explode(',', EDITABLE_FORMATS);
        $data .= '<ul class="files">';
        $files = array_slice(scandir($dir), 2);

        asort($files);

        foreach ($files as $key => $file) {
            if ((SHOW_PHP_SELF === false && $dir . DS . $file == __FILE__) || (SHOW_HIDDEN_FILES === false && substr($file, 0, 1) === '.')) {
                continue;
            }

            if (is_dir($dir . DS . $file)) {
                $dir_path = str_replace(MAIN_DIR . DS, '', $dir . DS . $file);
                $data .= '<li class="dir"><a href="javascript:void(0);" class="open-dir" data-dir="/' . $dir_path . '/">' . $file . '</a>' . $this->files($dir . DS . $file, false) . '</li>';
            } else {

                $is_editable = strpos($file, '.') === false || in_array(substr($file, strrpos($file, '.') + 1), $formats);

                $data .= '<li class="file ' . ($is_editable ? 'editable' : null) . '" data-jstree=\'{ "icon" : "jstree-file" }\'>';
                if ($is_editable === true) {
                    $file_path = str_replace(MAIN_DIR . DS, '', $dir . DS . $file);
                    $fInfo = pathinfo($file_path);
                    $ext = $fInfo['extension'];
                    $data .= '<a href="javascript:void(0);" class="open-file ' . $ext . ' " data-file="/' . $file_path . '">';
                }

                $data .= $file;

                if ($is_editable) {
                    $data .= '</a>';
                }

                $data .= '</li>';
            }
        }

        $data .= '</ul>';

        if ($first === true) {
            $data .= '</li></ul>';
        }

        return $data;
    }

}
