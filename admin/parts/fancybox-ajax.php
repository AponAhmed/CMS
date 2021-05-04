<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
$height = !empty($_GET['h']) ? $_GET['h'] . "px" : "80%";
$width = !empty($_GET['w']) ? $_GET['w'] . "px" : "60%";
$style = "";
$style.="height:$height;width:$width";
?>


<div style="<?php echo $style ?>" class='cms-fancyboxContnt'>    
    <?php
    if (isset($_GET['c'])) {
        $cls = $_GET['c'] . "_class";
        $cls = new $cls();
        if (isset($_GET['m'])) {
            $module = $_GET['m'];
            if (method_exists($cls, $module)) {
                $cls->$module();
            } else {
                $cls->notFound();
            }
        }
    }
    ?>
</div>

