<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php
define("ADMN_MENU_DEF_IC", ""); //Menu Default icon
global $adminMenu;
usort($adminMenu, function($a, $b) {
    return $a['order'] - $b['order'];
});

function find_menu_with_slug($menu, $currentSlug) {
    foreach ($menu as $index => $item) {
        if ($item['slug'] == $currentSlug)
            return $index;
    }
    return FALSE;
}

function AchildMenu($item) {
    global $adminMenu;
    ob_start();

    $i = 0;
    $postTypeUrl = "";
    if (!empty($item['post_type'])) {
        $postTypeUrl = "&post-type=" . $item['post_type'];
    }
    foreach ($adminMenu as $sub) {
        $Subactive = "";
        if (!empty($sub['parent_slug']) && $item['slug'] == $sub['parent_slug']) {
            $i++;
            $url = "index.php";
            if (!empty($sub['slug'])) {
//                                    if ($i == 1) {
//                                       $url.="?l=$item[slug]";
//                                    } else {
//                                        $url.="?l=$sub[slug]";
//                                    }
                $url.="?l=$sub[slug]";
            }
            //var_dump($sub['slug']);
            if ((!empty($_GET['l']) && $sub['slug'] == $_GET['l']) || (isset($_GET['tex']) && $sub['slug'] == "texonomy&tex=" . $_GET['tex'])) {
                //var_dump($sub['slug']);
                if (isset($item['post_type']) && !empty($item['post_type'])) {
                    if (isset($_GET['post-type']) && $item['post_type'] == $_GET['post-type']) {
                        $Subactive = "active";
                    }
                } else {
                    $Subactive = "active";
                }
            } else {
                $Subactive = "";
            }
            ?>
            <li class="<?php echo $Subactive ?>">
                <a href="<?php echo $url . $postTypeUrl ?>">
                    <span class='admin-menu-icon'>
                    </span>
                    <span class='admin-menu-name'><?php echo $sub['menu_title'] ?></span>
                </a>
            </li>
            <?php
        } else {
            continue;
        }
    }
    return ob_get_clean();
}

//var_dump($adminMenu);
?>
<div class='admin-sidebar'>
    <div class="sidebar">
        <ul class='admin-menus'>
            <?php
            foreach ($adminMenu as $k => $item) {
                // var_dump($item);
                if (current_mode($mode, $mode_val)) {
                    if (@$item['mode'] !== $mode) {
                        continue;
                    }
                } else {
                    if (isset($item['mode']) && $item['mode'] != "") {
                        continue;
                    }
                }

                if (!webAppMenu($item)) {
                    continue;
                }
                if (!menuPermission($item)) {
                    continue;
                }
                if (empty($item['parent_slug'])) {
                    $active = "";
                    $ActiveChild = "";
                    $hasChild = "";
                    $url = "index.php";
                    if (!empty($item['slug'])) {
                        $url.="?l=$item[slug]";
                    }
                    //if (have_adminSubMenu($item['slug'])) {
                    if (first_adminSubMenu($item['slug'])) {
                        $firstSuburl = first_adminSubMenu($item['slug']);
                        $url = "index.php?l=$firstSuburl[slug]";
                    }
                    if (isset($_GET['l'])) {
                        $key = find_menu_with_slug($adminMenu, $_GET['l']);
                        $hasChild = $adminMenu[$key]['parent_slug'];
                        if ((!empty($_GET['l']) && $item['slug'] == $_GET['l']) || !empty($hasChild) && $hasChild == $item['slug']) {
                            if (!isset($_GET['tex']) && $_GET['l'] !== 'edit') {
                                $active = "active";
                            }
                        } else {
                            $active = "";
                        }
                    } elseif ($url == "index.php") {
                        $active = "active";
                    }

                    //var_dump($hasChild);
                    if (!empty($hasChild)) {
                        $ActiveChild = "activeChild";
                    }
                    $postTypeUrl = "";
                    if (!empty($item['post_type'])) {
                        $postTypeUrl = "&post-type=" . $item['post_type'];
                        if (isset($_GET['post-type']) && $item['post_type'] == $_GET['post-type']) {
                            $active = "active";
                        } else {
                            $active = "";
                        }
                    }
                    $childHtml = AchildMenu($item);
                    $hasChildC = "";
                    if ($childHtml != "") {
                        $hasChildC = "hasChild";
                    }
                    //var_dump($childHtml);
                    ?>
                    <li class='<?php echo $active . " " . $ActiveChild . " " . $hasChildC ?>'>
                        <a href="<?php echo $url . $postTypeUrl ?>">
                            <span class='admin-menu-icon'>
                                <?php
                                if (!empty($item['icon'])) {
                                    echo "<i class=\"$item[icon]\"></i>";
                                } else {
                                    echo "<i class=\"" . ADMN_MENU_DEF_IC . "\"></i>";
                                }
                                ?>
                            </span>
                            <span class='admin-menu-name'><?php echo $item['menu_title'] ?></span>
                        </a>

                        <?php
                        if (!empty($childHtml)) {
                            ?>
                            <span class="childMenuTrig"></span>
                            <ul class='admin-subMenu'><?php echo $childHtml ?></ul>
                        <?php } ?>
                    </li>
                    <?php
                } else {
                    continue;
                }
            }
            ?>

        </ul>
    </div>
    <script>
        $('.hasChild > a').click(function(e) {
            e.preventDefault();
            $(this).parent().toggleClass('open').find('.admin-subMenu').slideToggle('fast');
        });
    </script>
    <?php

    //array_push($adminMenu,array(),array());

    function abc() {
        return array(
            'slug' => '',
            'menu_title' => "asdsd",
            'icon' => "",
            'order' => "",
            'parent_slug' => ""
        );
    }

    function echo_this_in_header() {
        echo 'this came from a hooked function';
    }

//    global $hooks;
//    $hooks->add_action('header_action', 'echo_this_in_header');
//    $hooks->do_action('header_action');
//
//    $hooks->add_action('header_action', 'echo_this_in_header');
//
//    function echois_in_header() {
//        echo 'this came from a hooked function';
//    }
//    $hooks->add_action('header_action', 'echois_in_header',1,100);

    global $plugins;
    $plugins->AvailablePlugins();
    ?>

</div>