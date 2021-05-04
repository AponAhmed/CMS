<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//Initial 
//JSON
//
//if (get_option('html_min')) {
//    add_content_filter('minify_html_filter',50);
//}
//
//function minify_html_filter($html) {
//    //return preg_replace('/\s+/',' ', $html);
//    //return $html;
//    return minify_html($html);
//}
//Plugin 

add_content_filter('shortcodeFinalExc', 290);  //Before Cache generate


add__statue_bar_object(array('html' => EditLink(false)));

function shortcodeFinalExc($str) {
    $re = '/srcsetg=\"(\d+)\"/m';
    preg_match_all($re, $str, $matches, PREG_SET_ORDER, 0);
// Print the entire match result
    foreach ($matches as $srcsetStrAtr) {
        //var_dump($srcsetStrAtr);
        $srcset = srcset(array('id' => $srcsetStrAtr[1]));
        $str = str_replace($srcsetStrAtr[0], "srcset=\"$srcset\"", $str);
    }
    $str = do_shortcode($str);
    return $str;
}
