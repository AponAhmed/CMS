<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of re_write
 *
 * @author nrb
 */
class re_write {

    var $roles;
    var $requestURI;
    var $structure;
    var $uri;
    var $getUri;
    var $htmlExtension;
    var $adminDir;
    var $lng;

    public function __construct() {
        global $RW_Role, $DB, $adminDir;
        //var_dump($RW_Role);
        krsort($RW_Role);
        $enqReData = serialize($RW_Role);
        $CurrentR = get_option('rewrite_role');
        if (strlen($enqReData) > strlen($CurrentR)) {
            $rr = add_option('rewrite_role', $enqReData);
            if (!$rr) {
                update_option('rewrite_role', $enqReData);
            }
        }
        $this->adminDir = $adminDir;
        $RW_Role = unserialize(get_option('rewrite_role'));

        //var_dump($RW_Role);
        $R_URI = $_SERVER['REQUEST_URI'];
        $R_URI = str_replace(SUB_ROOT, "", $R_URI);

        //Additional   $R_URI = trim($R_URI, '/');
        //if()
        $R_URI = urlStrSolver($R_URI);
        //Additional 
        //var_dump($R_URI);

        $this->htmlExtension = get_option('html_ext') == 'true' ? true : false;
        if ($this->htmlExtension) {
            if (strpos($R_URI, '.html')) {
                $R_URI = str_replace_limit(".html", "", $R_URI, 1); //For .html  
            }
        }

        //krsort($RW_Role);
        //arsort($RW_Role);       
        //var_dump(add_option("RWrole","as"));
        //var_dump($RW_Role);
        //var_dump(unserialize(get_option("RWrole")));
        //var_dump($customRole);
        //shorting reWrite---
        //aadd from External file or plugin

        $this->roles = $RW_Role;
        $this->requestURI = $R_URI;
        $structure = get_option('permalink');
        $this->structure = $structure;
        $prtUri = explode('?', $this->requestURI);
        $this->uri = $prtUri[0] . "/";
        if (isset($prtUri[1])) {
            $this->getUri = $prtUri[1];
        }



        //var_dump($this->uri);
        $re = '@/([a-z]{0,2})/(.*)@';
        preg_match_all($re, "/" . $this->uri, $matches, PREG_SET_ORDER, 0);
        if (!empty($matches)) {
            //var_dump($matches);
            $this->lng = $matches[0][1];
            //$this->uri = str_replace($this->lng, "", $this->uri);
            $this->uri = reduce_double_slashes($matches[0][2]);
        }
    }

    public function setRole($role, $ord) {
        $RW_Role = unserialize(get_option('rewrite_role'));
        $RW_Role[$ord] = $role;
        krsort($RW_Role);
        //var_dump($RW_Role);
        //$role=unique_multidim_array($role,1);
        //var_dump($role);
        $enqReData = serialize($RW_Role);
        $CurrentR = get_option('rewrite_role');
        if ($enqReData != $CurrentR) {
            $rr = add_option('rewrite_role', $enqReData);
            if (!$rr) {
                update_option('rewrite_role', $enqReData);
            }
        }
    }

    public function resetRWR() {
        return update_option('rewrite_role', "");
    }

    public function match() {
        $vars = array();
        //var_dump($this->roles);
        foreach ($this->roles as $r => $val) {
            $match = preg_match($val[0], $this->uri, $matches);
            //var_dump($matches);
            unset($matches[0]);
            if ($match) {
                $params = explode(",", $val[1]);
                $c = 0;
                // var_dump($params);
                foreach ($matches as $m) {
                    // var_dump($m);
                    $vars[$params[$c]] = $m;
                    $c++;
                }
                //var_dump($val[0]);
                // var_dump($vars);
                $vars = $this->customRewrite($vars);
                //var_dump($vars);
                return $vars;
                break;
                //break;
            }
            // var_dump($this->uri);
        }
        //return $vars;
        //var_dump($vars);
    }

    public function customRewrite($QV) {
        //var_dump($inSlugTerms);

        global $TERM;

        $RemoveParent = false;
        $removeParentSlug = get_option('removeParentSlug');
        if ($removeParentSlug != 'false') {
            $RemoveParent = true;
        }
        $BlogPageUrl = get_post(get_option('blog_page'), "post_name");
        if (is_array($BlogPageUrl)) {
            $BlogPageUrl = $BlogPageUrl['post_name'];
        }

        //var_dump($BlogPageUrl);
        $NQV = $QV;
        if (!isset($QV['parent'])) {
            //not parent link isset only (1);
            $enable_type_slug = @array_filter(unserialize(get_option('enable_type_slug')));
            $enable_type_slug = !empty($enable_type_slug) && is_array($enable_type_slug) ? $enable_type_slug : array('post', 'page');
            //var_dump($enable_type_slug);
            if ($pgid = slug2id(@$QV['page'], $enable_type_slug, true, true)) { /* -to work-- */ //type w'll be when 'custom_url'=false and 'show_in_nav_menus' =true
                //page
                // var_dump(get_post($pgid));
                //exit;
                $NQV['page'] = $QV['page'];
                if ($this->has_term($pgid) && !isset($QV['PostCustomPath'])) {
                    $GLOBALS['template'] = "404.php";
                    $GLOBALS['title'] = "404-Page Not Found !";
                } else {
                    //$GLOBALS['post'] = get_post($pgid);
                }
            } elseif ($pgid = slug2id(@$QV['page'], 'post', true, true)) {
                //post
                $NQV['post_slug'] = $QV['page'];
                //$NQV['page'] = $BlogPageUrl;
            } elseif (term_slug2Id(@$QV['page'])) {
                //texonomy
                if ($this->isTermSlug($QV['page'])) {
                    $NQV['post_category'] = $QV['page'];
                    $NQV['term'] = $QV['page']; //===============
                    $GLOBALS['template'] = "category.php";
                    $GLOBALS['blog'] = true;
                    $GLOBALS['term'] = $TERM->slug2term($NQV['term']);
                } else {
                    $GLOBALS['template'] = "404.php";
                    $GLOBALS['title'] = "404-Page Not Found !";
                }


                //var_dump($GLOBALS['term']);
                //$NQV['page'] = $BlogPageUrl;
            } else {
                
            }
        } else {
            //Both is present (1),(2)
            if (!slug2id($QV['parent'], 'page', true)) {//2-page
                $structurePart = explode("/", $this->structure);
                $structurePart = array_unique(array_filter(array_map('trim', $structurePart)));
                //var_dump($structurePart);
                if (count($structurePart) > 1) {
                    if (term_slug2Id($QV['parent']) && $this->isTermSlug($QV['parent'])) {
                        //$GLOBALS['blog'] = true;
                        //var_dump(term_slug2Id($QV['parent']));
                        $NQV['post_category'] = $QV['parent'];
                        $NQV['post_slug'] = $QV['page'];
                        $NQV['term'] = $QV['parent']; //====================
                        $GLOBALS['term'] = $TERM->slug2term($NQV['term']);
                        if (!slug2id($NQV['post_slug'], array('post', 'page'), true)) {//2-post
                            //echo "not found";
                            // var_dump(slug2id($NQV['post_slug'], array('post', 'page'), true));
                            $GLOBALS['template'] = "404.php";
                            $GLOBALS['blog'] = true;
                            $GLOBALS['title'] = "404-Page Not Found !";
                            //var_dump()
                            unset($GLOBALS['term'], $NQV['term']);
                            //unset($NQV['page']);
                        }
                        $GLOBALS['post'] = get_post($QV['page'], 'post');
                        // $NQV['page'] = $BlogPageUrl;
                        unset($NQV['parent']);
                    }
                }
            }
        }

        //var_dump($NQV);
        $get = $this->get();
        if (isset($get['q'])) {
            $GLOBALS['template'] = "search.php";
            $GLOBALS['title'] = "Search result for - $get[q]";
            $NQV['search_string'] = $get['q'];
        }


        return $NQV;
        //parent,page,post_category,post_slug
    }

    public function req_post() {
        $vars = $this->match();
        //var_dump($vars);
    }

    public function reg() {
        global $RW_Role, $DB;
        $enqReData = serialize($RW_Role);
        $CurrentR = get_option('rewrite_role');
        if ($enqReData != $CurrentR) {
            $rr = add_option('rewrite_role', $enqReData);
            if (!$rr) {
                update_option('rewrite_role', $enqReData);
            }
        }
        //$this->roles = $RW_Role; 
    }

    function has_term($id) {
        global $DB, $TERM;
        global $C_POST_TYPE;
        //var_dump($C_POST_TYPE);
        $structure = get_option('permalink');

        //var_dump($structure);

        if (strpos($structure, '%category%') === false) {
            return false;
        }

        $inSlugTerms = array();
        foreach ($C_POST_TYPE as $cp) {
            //var_dump($cp);
            if (isset($cp['texo_show_in_menu'])) {
                foreach ($cp['texo_show_in_menu'] as $menuTexo => $yes) {
                    if ($yes) {
                        $inSlugTerms[] = $menuTexo;
                    }
                }
            }
        }

        $enbleTexo = enableSlugCPTypeArr('t');
        $inSlugTerms = array_intersect($enbleTexo, $inSlugTerms);
        //var_dump($inSlugTerms);
        $inSlugTerms = implode("','", $inSlugTerms);

        //var_dump($inSlugTerms);
        // exit;

        $dd = $DB->select("term_relationships as tr left join term_taxonomy as tt on tr.texo_id=tt.taxonomy_id", "tt.taxonomy,tt.term_id", "tr.object_id=$id and tt.taxonomy IN('$inSlugTerms')");
        if (!empty($dd)) {
            $trm = $TERM->get_term($dd[0]['term_id']);
            if (isset($trm['meta']['disableSlug']) && $trm['meta']['disableSlug'] == 'true') {
                return false;
            }
            return $dd[0]['taxonomy'];
        } else {
            return false;
        }
    }

    public function isTermSlug($slug) {
        global $TERM;
        $term = $TERM->slug2term($slug);

        $texo = $term['taxonomy'];
        $enabledTexonomys = enableSlugCPTypeArr('t');
        $enabledTexonomys[] = 'product-group';
        //var_dump($enabledTexonomys);

        if (!in_array($texo, $enabledTexonomys)) {
            return false;
        }
        if (isset($term['meta']['disableSlug']) && $term['meta']['disableSlug'] == 'true') {
            return false;
        } else {
            return true;
        }
    }

    public function get() {
        //$QV=$this->match();
//        
        $get = array();
//			if(isset($QV['get'])){
//				$getStrArr=explode("&",$QV['get']);
//				foreach($getStrArr as $str){
//					$singleG=explode("=",$str);
//					$get[$singleG[0]]=isset($singleG[1])?$singleG[1]:"";	
//				}
//			}
        if (!empty($this->getUri)) {
            $getStrArr = explode("&", $this->getUri);
            foreach ($getStrArr as $str) {
                $singleG = explode("=", $str);
                $get[$singleG[0]] = urldecode(isset($singleG[1]) ? $singleG[1] : "");
            }
        }
        //var_dump($get);
        return $get;
    }

}
