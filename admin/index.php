<?php
ob_start();
require_once('../loader.php');
require_once('admin-include.php');

if (isset($_GET['logout'])) {
    unset($_SESSION[SESS_KEY]);
    unset($_SESSION['login']);
    ob_get_clean();
    header('location:login.php');
    exit;
}
//var_dump($_SESSION[SESS_KEY]);
if (!isset($_SESSION[SESS_KEY]['login'])) {
    $_SESSION[SESS_KEY]['redir'] = $_SERVER['REQUEST_URI'];
    ob_get_clean();
    header('location:login.php');
    exit;
} else {
    ?>
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <title><?php adminTitle() ?> - Admin( <?php echo get_option('site-name') ?> )</title>
            <?php admin_styles(true); ?>
            <?php admin_script(true, "head"); ?>
        </head>
        <body>
            <?php admin_bar() ?>
            <div class="admin-body">
                <div class="msg"></div>
                <?php admin_sidebar(); ?> 
                <div class='admin-inner'>
                    <?php //body_top()  ?>
                    <?php
                    admin_body();
                    //var_dump($_SERVER)
                    ?>
                </div>
            </div>
            <?php admin_script(true, 'footer'); ?>
        </body>
    </html> 
    <?php
}
//$out = content_filter(ob_get_clean());
$out = ob_get_clean();
echo $out;
?>