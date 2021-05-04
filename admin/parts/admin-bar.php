<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>

<div class='admin-bar'>
    <div class='cms-logo'><img src="images/logo.png"><input type="hidden" id="editorInstance"></div>
    <div class="admin-bar-menu">
        <ul>
            <li>
                <a target='new' href="<?php echo domain() ?>">
                    <span class='admin-bar-menu-icon'><i class="fal fa-home"></i></span>
                    <span id="siteTitle" class='admin-bar-menu-name'><?php echo get_option('site-name') ?></span>
                </a>
            </li>
            <?php adminBarLink() ?>
        </ul>
    </div>
    <div class="dropdown userLoginControl">
        <button type="button" class="dropdown-toggle" data-toggle="dropdown">
            User
        </button>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="">User</a>
            <a class="dropdown-item" href="?logout">Logout</a>
        </div>
        <a href="http://siatexltd.com/cms_update_path/CMS_installer.zip" class="installerDownload">Download</a>
    </div> 

    <?php
    global $modes;
    if (count($modes) > 0) {
        ?>
        <div class="dropdown userModeControl">
            <button type="button" class="dropdown-toggle" data-toggle="dropdown">
                <?php
                if (current_mode($Mindx, $currentMode)) {
                    echo "<i class='$currentMode[1]'></i>";
                } else {
                    echo "<i class='fas fa-cog'></i>";
                }
                ?>
            </button>
            <div class="dropdown-menu">
                <?php
                echo "<a class=\"dropdown-item\" href='?mode='><i class='fas fa-cog'></i> &nbsp;Cpanel</a>";
                foreach ($modes as $slg => $mode) {
                    echo "<a class=\"dropdown-item\" href='?mode=$slg'><i class='$mode[1]'></i> &nbsp;$mode[0]</a>";
                }
                ?>
            </div>
        </div>

    <?php }
    ?>

</div>