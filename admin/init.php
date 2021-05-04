<?php

defined('ABSPATH') OR exit('No direct script access allowed');

//&& !empty($_GET['mode'])
if (isset($_GET['mode'])) {
    $_SESSION['mode'] = $_GET['mode'];
}

$slugLng = array(
    'dash' => 'dashboard',
    'page' => 'pages',
    'library' => 'media-exp',
    'new-media' => 'upload',
    'plugin' => 'plugin-list',
    'themes' => 'themes_page',
    'menus' => 'menu_manager',
    'update' => 'update_cms',
    'texonomy' => 'texonomy_edit',
);

//$_SESSION['publicScripts'] = array();

$contentfilte = array('expand_shortcode', 'ReadMore_content');
$attachment_file_save = array();
$attachment_src_filter = array();
$menu_location = array();
$front_style_filter = array();
$front_script_filter = array();
$Custom_texonomys = array(
    'category' => array('label' => 'Category', 'calback' => ''),
);

$customFonts = array();
$customCss = array(); //array string all strings make a css
$initMetaFilter = array();

$__statue_bar_object = array(); //Frontend Status bar 
$__editor_bottom_object = array(); //array('order','html','both/term/post')
$attachment_file_rename_after = array();

$menuInject = array();
//array(
// 'obID' => 68,
// 'inAct' => 'subMenu', //title/href
// 'callBack' => 'subMenuCallback'
// );
//
//

$adminBarLink = array();

$C_POST_TYPE = array(
    'page' => array(
        'label' => "Pages",
        'menu_icon' => 'fal fa-newspaper',
        'taxonomies' => array(),
        'show_in_menu' => true,
        'taxonomies' => array('Page category'),
        'texo_callback' => array('Page category' => 'texoChose'),
        'texo_show_in_menu' => array('Page category' => true),
        'texo_input_type' => array('Page category' => 'radio'),
        'menu_position' => 4,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'editor_permalink_show' => true,
        'editor_show' => true,
        'show_in_dash' => true,
        'custom_url' => true,
        'in_slug' => true
    ),
    'post' => array(
        'label' => "Post",
        'menu_icon' => 'fal fa-images',
        'taxonomies' => array('category', 'tag'),
        'texo_callback' => array('tag' => 'texoChose', 'category' => 'texoChose'),
        'show_in_menu' => true,
        'texo_show_in_menu' => array('tag' => false, 'category' => true),
        'menu_position' => 2,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'editor_permalink_show' => true,
        'editor_show' => true,
        'show_in_dash' => false,
        'custom_url' => true,
        'in_slug' => true
    ),
//    'attachment' => array(
//        'label' => "Media",
//        'menu_icon' => 'far fa-images',
//        'taxonomies' => array(),
//        'show_in_menu' => true,
//        'menu_position' => 6,
//        'show_in_admin_bar' => true,
//        'show_in_nav_menus' => false
//    )
);

$__texo_meta = array();

$adminMenu = array(
    array(
        'slug' => '',
        'menu_title' => "Dashboard",
        'icon' => "fal fa-chart-line",
        'icon_img' => "",
        'order' => 1,
        'parent_slug' => "",
        'privilege_label' => 'all',
        'web_app' => true,
    ),
//    array(
//        'slug' => 'pages',
//        'menu_title' => "Pages",
//        'icon' => "fal fa-newspaper",
//        'icon_img' => "",
//        'order' => "2",
//        'parent_slug' => ""
//    ),
    array(
        'slug' => 'tools',
        'menu_title' => "Tools",
        'icon' => "fal fa-wrench",
        'icon_img' => "",
        'order' => 8,
        'parent_slug' => "",
        'post_type' => "",
        'web_app' => false
    ),
    array(
        'slug' => 'media',
        'menu_title' => "Media",
        'icon' => "fal fa-images",
        'icon_img' => "",
        'order' => 5,
        'parent_slug' => "",
        'post_type' => "attachment",
        'web_app' => true,
    ),
    array(
        'slug' => 'texonomy&tex=type',
        'menu_title' => "Category",
        'icon' => "",
        'icon_img' => "",
        'order' => 5,
        'parent_slug' => "media",
        'post_type' => "attachment",
        'web_app' => true,
    ),
    array(
        'slug' => 'library',
        'menu_title' => "Library",
        'icon' => "",
        'icon_img' => "",
        'order' => 1,
        'parent_slug' => "media",
        'post_type' => "attachment",
        'web_app' => true,
    ),
    array(
        'slug' => 'new-media',
        'menu_title' => "Add New",
        'icon' => "",
        'icon_img' => "",
        'order' => 2,
        'parent_slug' => "media",
        'post_type' => "attachment",
        'web_app' => true,
    ),
    array(
        'slug' => 'plugins_manager',
        'menu_title' => "Plugins",
        'icon' => "fal fa-plug",
        'icon_img' => "",
        'order' => 6,
        'parent_slug' => "",
        'privilege_label' => array('S', 'A'),
        'web_app' => true,
    ),
    array(
        'slug' => 'plugin',
        'menu_title' => "Installed plugin",
        'icon' => "fal fa-plug",
        'icon_img' => "",
        'order' => 1,
        'parent_slug' => "plugins_manager",
        'privilege_label' => array('S', 'A'),
        'web_app' => true,
    ),
    array(
        'slug' => 'addnewplugins',
        'menu_title' => "New Plugin",
        'icon' => "fal fa-plug",
        'icon_img' => "",
        'order' => 2,
        'parent_slug' => "plugins_manager",
        'privilege_label' => array('S', 'A'),
        'web_app' => true,
    ),
    array(
        'slug' => 'appearence',
        'menu_title' => "Appearence",
        'icon' => "fal fa-paint-brush",
        'icon_img' => "",
        'order' => 6,
        'parent_slug' => "",
        'web_app' => false
    ),
    array(
        'slug' => 'theme-edit',
        'menu_title' => "Theme Editor",
        'icon_img' => "",
        'order' => 6,
        'parent_slug' => "appearence",
        'web_app' => false
    ),
    array(
        'slug' => 'themes',
        'menu_title' => "Themes",
        'icon' => "",
        'icon_img' => "",
        'order' => 1,
        'parent_slug' => "appearence",
        'web_app' => false
    ),
    array(
        'slug' => 'menus',
        'menu_title' => "Menus",
        'icon' => "",
        'icon_img' => "",
        'order' => 2,
        'parent_slug' => "appearence",
        'web_app' => false,
    ),
    //Options
    array(
        'slug' => 'options',
        'menu_title' => "Settings",
        'icon' => "fal fa-cogs",
        'icon_img' => "",
        'order' => 15,
        'parent_slug' => "",
        'privilege_label' => array('S', 'A'),
        'web_app' => true,
    ),
    array(
        'slug' => 'options-general',
        'menu_title' => "General",
        'icon' => "",
        'icon_img' => "",
        'order' => 1,
        'parent_slug' => "options",
        'privilege_label' => array('S', 'A'),
        'web_app' => true,
    ),
    array(
        'slug' => 'update',
        'menu_title' => "Update",
        'icon' => "",
        'icon_img' => "",
        'order' => 2,
        'parent_slug' => "options",
        'privilege_label' => array('S', 'A'),
        'web_app' => true,
    ),
    array(
        'slug' => 'users',
        'menu_title' => "User",
        'icon' => "",
        'icon_img' => "",
        'order' => 4,
        'parent_slug' => "options",
        'privilege_label' => array('S'),
        'web_app' => true,
    ),
);
//Meta Box
$metaBoxes = array(
    array(
        'title' => "Publish",
        'Description' => "Publish or Un-publish post",
        'position' => "side",
        'type' => "all",
        'excpet' => 'dashboard',
        'calback' => 'publishBox',
        'order' => -1
    )
);


//Admin Enqueue  Script

$adminScripts = array(
);
//Admin Enqueue  Style
$adminStyles = array(
    array(
        'id' => "col",
        'href' => COMMON_SC . "css/col.css",
        'order' => 1
    ),
    array(
        'id' => "admin",
        'href' => ADMIN_CSS . "admin.css",
        'order' => 100
    ),
);

$loginScript = array(
    array(
        'id' => "login-css",
        'href' => ADMIN_CSS . "login.css"
    ),
);

$bulkActions = array(
    'default' => array(
        //Label,javascript Function, 
        array('label' => 'Delete Forever', 'calback' => 'multipleDelete', 'order' => 1),
        array('label' => 'Trash', 'calback' => 'multipleTrash', 'order' => 1),
        array('label' => 'Publish', 'calback' => 'multiplePublished', 'order' => 1),
        array('label' => 'Draft', 'calback' => 'multipleDraft', 'order' => 1),
        array('label' => 'Move', 'calback' => 'MovePost', 'order' => 2),
    ),
    'page' => array(
        array('label' => 'Date modifie', 'calback' => 'MltModifieDate', 'order' => 2),
    ),
    'post' => array(
        array('label' => 'Date modifie', 'calback' => 'MltModifieDate', 'order' => 2),
    )
);


$listFields = array(
    'default' => array(
        array(
            'title' => "<input type='checkbox' id='selectALL'>",
            'field' => "ID",
            'order' => 1,
            'meta' => false,
            'filter' => 'item_checkbox'
        ),
        array(
            'title' => "#",
            'field' => "ID",
            'order' => 2
        ),
        array(
            'title' => "Feature Image",
            'field' => 'ID',
            'order' => 100,
            'meta' => false,
            'filter' => 'feature_image_list_filter'
        ),
        array(
            'title' => "Title",
            'field' => array('guid', 'post_title', 'post_name'),
            'order' => 2,
            'meta' => false,
            'filter' => 'add_link_title_page'
        ),
        array(
            'title' => "Last Update",
            'field' => 'post_modified_gmt',
            'order' => 3,
            'meta' => false,
            'filter' => 'time'
        )
    ),
    'page' => array(
    ),
    'attachment' => array(
        array(
            'title' => "<input type='checkbox' id='selectALL'>",
            'field' => "ID",
            'order' => 1,
            'meta' => false,
            'filter' => 'item_checkbox'
        ),
        array(
            'title' => "File",
            'field' => array('guid', 'post_title', 'post_name'),
            'order' => 2,
            'meta' => false,
            'filter' => 'library_file'
        ),
        array(
            'title' => "Date",
            'field' => 'post_date_gmt',
            'order' => 3,
            'meta' => false,
            'filter' => 'time'
        )
    ),
);

$CPdata = get_option('customPostTypes');
$CPdata = json_decode($CPdata, true);
if (is_array($CPdata)) {
    foreach ($CPdata as $k => $type) {
        if ($type['status'] == 'active') {
            $txClbackArr = array();
            $txShowMenu = array();
            $texoStr = "";
            if (isset($type['texonomy']['name'])) {
                foreach ($type['texonomy']['name'] as $i => $tx) {
                    $txClbackArr[$tx] = 'texoChose';
                    $txShowMenu[$tx] = $type['texonomy']['menu_status'][$i] == "true" ? true : false;
                }
                $texoStr = $type['texonomy']['name'];
            }

            $arg = array(
                'label' => $type['label'],
                'menu_icon' => $type['icon'],
                'taxonomies' => $texoStr,
                'texo_callback' => $txClbackArr,
                'texo_show_in_menu' => $txShowMenu,
                'show_in_menu' => true,
                'menu_position' => 10,
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'content' => false,
                'editor_permalink_show' => @$type['enable_slug'] == "true" ? true : false,
                'editor_show' => @$type['editor'] == "true" ? true : false,
                'custom_url' => @$type['customUrl'] != "" ? "CPcustomSlug" : "",
                'in_slug' => @$type['enable_slug'] == "true" ? true : false,
            );
            add_custom_post($type['slug'], $arg);
        }
    }
}

function customPost_init() {
    global $C_POST_TYPE, $adminMenu, $TERM, $listFields;

    foreach ($C_POST_TYPE as $slug => $CP) {
        if (isset($CP['show_in_dash']) && $CP['show_in_dash'] === true) {
            add_metabox(array(
                'title' => "$CP[label]",
                'Description' => "",
                'position' => "dashboard",
                'type' => "$slug",
                'calback' => 'postTypeDashboardMetaBox',
                'param' => array('type' => $slug),
                'class' => 'col-6',
            ));
        }


        //Menu Init
        if ($CP['show_in_menu']) {
            $webApp = isset($CP['web_app']) ? $CP['web_app'] : false;
            // var_dump($webApp, $CP['label']);
            $erpDesh = isset($CP['AppDashboard']) ? $CP['AppDashboard'] : false;

            $adminMenu[] = array(
                'slug' => $slug,
                'menu_title' => $CP['label'],
                'icon' => $CP['menu_icon'],
                'icon_img' => "",
                'order' => $CP['menu_position'],
                'parent_slug' => "",
                'post_type' => $slug,
                'web_app' => $webApp,
                'AppDashboard' => $erpDesh
            );
            $adminMenu[] = array(
                'slug' => "page",
                'menu_title' => "All $CP[label]",
                'icon' => "",
                'icon_img' => "",
                'order' => 1,
                'parent_slug' => $slug,
                'post_type' => $slug
            );
            $adminMenu[] = array(
                'slug' => "new-page",
                'menu_title' => "New",
                'icon' => "",
                'icon_img' => "",
                'order' => 2,
                'parent_slug' => $slug,
                'post_type' => $slug
            );

            if (isset($CP['taxonomies']) && count($CP['taxonomies']) > 0) {
                $ord = 1;
                foreach ($CP['taxonomies'] as $texo) {
                    $ord++;
                    $adminMenu[] = array(
                        'slug' => "texonomy&tex=" . $texo,
                        'menu_title' => $texo,
                        'icon' => "",
                        'icon_img' => "",
                        'order' => $ord,
                        'parent_slug' => $slug,
                        'post_type' => $slug
                    );

                    $texoCallback = 'texoSel';
                    if (isset($CP['texo_callback'][$texo])) {
                        $texoCallback = $CP['texo_callback'][$texo];
                    }

                    $inType = 'checkbox';
                    if (isset($CP['texo_input_type'][$texo])) {
                        $inType = $CP['texo_input_type'][$texo];
                    }

                    //var_dump($inType);

                    add_metabox(array(
                        'title' => "$texo Select",
                        'Description' => "",
                        'position' => "side",
                        'type' => "$slug",
                        'calback' => $texoCallback,
                        'param' => array($texo, 'inType' => $inType)
                    ));

                    if (!isset($listFields["$slug"])) {
                        $listFields["$slug"] = array();
                    }

                    array_push($listFields["$slug"], array(
                        'title' => "$texo",
                        'title_filter' => 'texo_short_filter',
                        'field' => array('ID', 'post_title', 'post_name', $texo),
                        'order' => 15,
                        'meta' => false,
                        'filter' => "related_texos"
                    ));
                }
            }
        }
    }
    return $adminMenu;
}

//add_header($title);
