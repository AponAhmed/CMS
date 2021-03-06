<?php defined('ABSPATH') OR exit('No direct script access allowed'); ?>
<?php

/**
 * Attach (or remove) multiple callbacks to an event and trigger those callbacks when that event is called.
 *
 * @param string $event name
 * @param mixed $value the optional value to pass to each callback
 * @param mixed $callback the method or function to call - FALSE to remove all callbacks for event
 */
function titleFilter($string) {
    $text = $string;
    $text = strtolower(remove_accents($text));

    $text = mb_convert_encoding((string) $text, 'UTF-8', mb_list_encodings());
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    //$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // remove unwanted characters
    //$text = preg_replace('~[^-\w]+~', '', $text);
    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);

    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}

function SkipSlugWords($words) {
    $skipwords = array(
        'about' => true,
        'We' => true,
        'after' => true,
        'ago' => true,
        'all' => true,
        'also' => true,
        'an' => true,
        'and' => true,
        'any' => true,
        'are' => true,
        'as' => true,
        'at' => true,
        'be' => true,
        'been' => true,
        'before' => true,
        'both' => true,
        'but' => true,
        'by' => true,
        'can' => true,
        'did' => true,
        'do' => true,
        'does' => true,
        'done' => true,
        'edit' => true,
        'even' => true,
        'every' => true,
        'for' => true,
        'from' => true,
        'had' => true,
        'has' => true,
        'have' => true,
        'he' => true,
        'here' => true,
        'him' => true,
        'his' => true,
        'however' => true,
        'if' => true,
        'in' => true,
        'into' => true,
        'is' => true,
        'it' => true,
        'its' => true,
        'less' => true,
        'many' => true,
        'may' => true,
        'more' => true,
        'most' => true,
        'much' => true,
        'my' => true,
        'no' => true,
        'not' => true,
        'often' => true,
        'quote' => true,
        'of' => true,
        'on' => true,
        'one' => true,
        'only' => true,
        'or' => true,
        'other' => true,
        'our' => true,
        'out' => true,
        're' => true,
        'says' => true,
        'she' => true,
        'so' => true,
        'some' => true,
        'soon' => true,
        'such' => true,
        'than' => true,
        'that' => true,
        'the' => true,
        'their' => true,
        'them' => true,
        'then' => true,
        'there' => true,
        'these' => true,
        'they' => true,
        'this' => true,
        'those' => true,
        'though' => true,
        'through' => true,
        'to' => true,
        'under' => true,
        'use' => true,
        'using' => true,
        've' => true,
        'was' => true,
        'we' => true,
        'were' => true,
        'what' => true,
        'where' => true,
        'when' => true,
        'whether' => true,
        'which' => true,
        'while' => true,
        'who' => true,
        'whom' => true,
        'with' => true,
        'within' => true,
        'you' => true,
        'your' => true,
        'http' => true,
        'www' => true,
        'wp' => true,
        'href' => true,
        'target' => true,
        'blank' => true,
        'image' => true,
        'class' => true,
        'size' => true,
        'src' => true,
        'img' => true,
        'alignleft' => true,
        'title' => true,
        'info' => true,
        'content' => true,
        'uploads' => true,
        'jpg' => true,
        'alt' => true,
        'h3' => true,
        'width' => true,
        'height' => true,
        '150' => true,
        '2010' => true,
        '2009' => true,
        '10' => true,
        '1' => true,
        '2' => true,
        '3' => true,
        '4' => true,
        '5' => true,
        '6' => true,
        '7' => true,
        '8' => true,
        '9' => true,
        '11' => true,
        'com' => true,
        'net' => true,
        'info' => true,
        'map' => true,
        '150x150' => true,
        'thumbnail' => true,
        'param' => true,
        'name' => true,
        'value' => true,
        'will' => true,
        'am' => true,
        '202' => true,
        'retouch' => true,
        '&amp;' => true,
        'amp' => true,
        'like' => true,
        'etc.' => true,
        'nbsp' => true,
        '??' => true,
    );


    $nwArr = array();
    foreach ($words as $w) {
        //if (!array_key_exists($w, $skipwords) && strlen($w) > 3) {
            $nwArr[] = $w;
        //}
    }
    return $nwArr;
}

function titleFilterNexist($string, $id = false) {
    $text = $string;

    $text = preg_split("/\s+/", $text);
    $text = SkipSlugWords($text);
    $text = implode(" ", $text);
    
    $text = strtolower(remove_accents($text));

    $text = mb_convert_encoding((string) $text, 'UTF-8', mb_list_encodings());
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    //$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // remove unwanted characters
    //$text = preg_replace('~[^-\w]+~', '', $text);
    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = strtolower($text);


    if (is_numeric($id)) {
        $Pid = slug2id($text);
        if (!empty($Pid) && $Pid != $id) {
            return array('slug' => $text, 'exist' => 'Already Exist in Post');
        }
    } else {
        $cTid = str_replace("term_", "", $id);
        $Tid = term_slug2Id($text);

        //var_dump($cTid, $Tid);

        if (!empty($Tid)) {
            if ($Tid != $cTid) {
                return array('slug' => $text, 'exist' => 'Already Exist in Term');
            }
        }
    }


    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}

function titleFilter_($str, $options = array()) {
    // Make sure string is in UTF-8 and strip invalid UTF-8 characters
    $str = mb_convert_encoding((string) $str, 'UTF-8', mb_list_encodings());

    $defaults = array(
        'delimiter' => '-',
        'limit' => null,
        'lowercase' => true,
        'replacements' => array(),
        'transliterate' => false,
    );

    // Merge options
    $options = array_merge($defaults, $options);

    $char_map = array(
        // Latin
        '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'AE', '??' => 'C',
        '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'I', '??' => 'I', '??' => 'I', '??' => 'I',
        '??' => 'D', '??' => 'N', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O',
        '??' => 'O', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'Y', '??' => 'TH',
        '??' => 'ss',
        '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'ae', '??' => 'c',
        '??' => 'e', '??' => 'e', '??' => 'e', '??' => 'e', '??' => 'i', '??' => 'i', '??' => 'i', '??' => 'i',
        '??' => 'd', '??' => 'n', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o',
        '??' => 'o', '??' => 'u', '??' => 'u', '??' => 'u', '??' => 'u', '??' => 'u', '??' => 'y', '??' => 'th',
        '??' => 'y',
        // Latin symbols
        '??' => '(c)',
        // Greek
        '??' => 'A', '??' => 'B', '??' => 'G', '??' => 'D', '??' => 'E', '??' => 'Z', '??' => 'H', '??' => '8',
        '??' => 'I', '??' => 'K', '??' => 'L', '??' => 'M', '??' => 'N', '??' => '3', '??' => 'O', '??' => 'P',
        '??' => 'R', '??' => 'S', '??' => 'T', '??' => 'Y', '??' => 'F', '??' => 'X', '??' => 'PS', '??' => 'W',
        '??' => 'A', '??' => 'E', '??' => 'I', '??' => 'O', '??' => 'Y', '??' => 'H', '??' => 'W', '??' => 'I',
        '??' => 'Y',
        '??' => 'a', '??' => 'b', '??' => 'g', '??' => 'd', '??' => 'e', '??' => 'z', '??' => 'h', '??' => '8',
        '??' => 'i', '??' => 'k', '??' => 'l', '??' => 'm', '??' => 'n', '??' => '3', '??' => 'o', '??' => 'p',
        '??' => 'r', '??' => 's', '??' => 't', '??' => 'y', '??' => 'f', '??' => 'x', '??' => 'ps', '??' => 'w',
        '??' => 'a', '??' => 'e', '??' => 'i', '??' => 'o', '??' => 'y', '??' => 'h', '??' => 'w', '??' => 's',
        '??' => 'i', '??' => 'y', '??' => 'y', '??' => 'i',
        // Turkish
        '??' => 'S', '??' => 'I', '??' => 'C', '??' => 'U', '??' => 'O', '??' => 'G',
        '??' => 's', '??' => 'i', '??' => 'c', '??' => 'u', '??' => 'o', '??' => 'g',
        // Russian
        '??' => 'A', '??' => 'B', '??' => 'V', '??' => 'G', '??' => 'D', '??' => 'E', '??' => 'Yo', '??' => 'Zh',
        '??' => 'Z', '??' => 'I', '??' => 'J', '??' => 'K', '??' => 'L', '??' => 'M', '??' => 'N', '??' => 'O',
        '??' => 'P', '??' => 'R', '??' => 'S', '??' => 'T', '??' => 'U', '??' => 'F', '??' => 'H', '??' => 'C',
        '??' => 'Ch', '??' => 'Sh', '??' => 'Sh', '??' => '', '??' => 'Y', '??' => '', '??' => 'E', '??' => 'Yu',
        '??' => 'Ya',
        '??' => 'a', '??' => 'b', '??' => 'v', '??' => 'g', '??' => 'd', '??' => 'e', '??' => 'yo', '??' => 'zh',
        '??' => 'z', '??' => 'i', '??' => 'j', '??' => 'k', '??' => 'l', '??' => 'm', '??' => 'n', '??' => 'o',
        '??' => 'p', '??' => 'r', '??' => 's', '??' => 't', '??' => 'u', '??' => 'f', '??' => 'h', '??' => 'c',
        '??' => 'ch', '??' => 'sh', '??' => 'sh', '??' => '', '??' => 'y', '??' => '', '??' => 'e', '??' => 'yu',
        '??' => 'ya',
        // Ukrainian
        '??' => 'Ye', '??' => 'I', '??' => 'Yi', '??' => 'G',
        '??' => 'ye', '??' => 'i', '??' => 'yi', '??' => 'g',
        // Czech
        '??' => 'C', '??' => 'D', '??' => 'E', '??' => 'N', '??' => 'R', '??' => 'S', '??' => 'T', '??' => 'U',
        '??' => 'Z',
        '??' => 'c', '??' => 'd', '??' => 'e', '??' => 'n', '??' => 'r', '??' => 's', '??' => 't', '??' => 'u',
        '??' => 'z',
        // Polish
        '??' => 'A', '??' => 'C', '??' => 'e', '??' => 'L', '??' => 'N', '??' => 'o', '??' => 'S', '??' => 'Z',
        '??' => 'Z',
        '??' => 'a', '??' => 'c', '??' => 'e', '??' => 'l', '??' => 'n', '??' => 'o', '??' => 's', '??' => 'z',
        '??' => 'z',
        // Latvian
        '??' => 'A', '??' => 'C', '??' => 'E', '??' => 'G', '??' => 'i', '??' => 'k', '??' => 'L', '??' => 'N',
        '??' => 'S', '??' => 'u', '??' => 'Z',
        '??' => 'a', '??' => 'c', '??' => 'e', '??' => 'g', '??' => 'i', '??' => 'k', '??' => 'l', '??' => 'n',
        '??' => 's', '??' => 'u', '??' => 'z'
    );

    // Make custom replacements
    $str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);

    // Transliterate characters to ASCII
    if ($options['transliterate']) {
        $str = str_replace(array_keys($char_map), $char_map, $str);
    }

    // Replace non-alphanumeric characters with our delimiter
    $str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);

    // Remove duplicate delimiters
    $str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);

    // Truncate slug to max. characters
    $str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');

    // Remove delimiter from ends
    $str = trim($str, $options['delimiter']);

    return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
}

function reset_mbstring_encoding() {
    mbstring_binary_safe_encoding(true);
}

function mbstring_binary_safe_encoding($reset = false) {
    static $encodings = array();
    static $overloaded = null;

    if (is_null($overloaded))
        $overloaded = function_exists('mb_internal_encoding') && ( ini_get('mbstring.func_overload') & 2 );

    if (false === $overloaded)
        return;

    if (!$reset) {
        $encoding = mb_internal_encoding();
        array_push($encodings, $encoding);
        mb_internal_encoding('ISO-8859-1');
    }

    if ($reset && $encodings) {
        $encoding = array_pop($encodings);
        mb_internal_encoding($encoding);
    }
}

function seems_utf8($str) {
    mbstring_binary_safe_encoding();
    $length = strlen($str);
    reset_mbstring_encoding();
    for ($i = 0; $i < $length; $i++) {
        $c = ord($str[$i]);
        if ($c < 0x80)
            $n = 0; // 0bbbbbbb
        elseif (($c & 0xE0) == 0xC0)
            $n = 1; // 110bbbbb
        elseif (($c & 0xF0) == 0xE0)
            $n = 2; // 1110bbbb
        elseif (($c & 0xF8) == 0xF0)
            $n = 3; // 11110bbb
        elseif (($c & 0xFC) == 0xF8)
            $n = 4; // 111110bb
        elseif (($c & 0xFE) == 0xFC)
            $n = 5; // 1111110b
        else
            return false; // Does not match any model
        for ($j = 0; $j < $n; $j++) { // n bytes matching 10bbbbbb follow ?
            if (( ++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
                return false;
        }
    }
    return true;
}

function remove_accents($string) {
    if (!preg_match('/[\x80-\xff]/', $string))
        return $string;

    if (seems_utf8($string)) {
        $chars = array(
            // Decompositions for Latin-1 Supplement
            '??' => 'a', '??' => 'o',
            '??' => 'A', '??' => 'A',
            '??' => 'A', '??' => 'A',
            '??' => 'A', '??' => 'A',
            '??' => 'AE', '??' => 'C',
            '??' => 'E', '??' => 'E',
            '??' => 'E', '??' => 'E',
            '??' => 'I', '??' => 'I',
            '??' => 'I', '??' => 'I',
            '??' => 'D', '??' => 'N',
            '??' => 'O', '??' => 'O',
            '??' => 'O', '??' => 'O',
            '??' => 'O', '??' => 'U',
            '??' => 'U', '??' => 'U',
            '??' => 'U', '??' => 'Y',
            '??' => 'TH', '??' => 's',
            '??' => 'a', '??' => 'a',
            '??' => 'a', '??' => 'a',
            '??' => 'a', '??' => 'a',
            '??' => 'ae', '??' => 'c',
            '??' => 'e', '??' => 'e',
            '??' => 'e', '??' => 'e',
            '??' => 'i', '??' => 'i',
            '??' => 'i', '??' => 'i',
            '??' => 'd', '??' => 'n',
            '??' => 'o', '??' => 'o',
            '??' => 'o', '??' => 'o',
            '??' => 'o', '??' => 'o',
            '??' => 'u', '??' => 'u',
            '??' => 'u', '??' => 'u',
            '??' => 'y', '??' => 'th',
            '??' => 'y', '??' => 'O',
            // Decompositions for Latin Extended-A
            '??' => 'A', '??' => 'a',
            '??' => 'A', '??' => 'a',
            '??' => 'A', '??' => 'a',
            '??' => 'C', '??' => 'c',
            '??' => 'C', '??' => 'c',
            '??' => 'C', '??' => 'c',
            '??' => 'C', '??' => 'c',
            '??' => 'D', '??' => 'd',
            '??' => 'D', '??' => 'd',
            '??' => 'E', '??' => 'e',
            '??' => 'E', '??' => 'e',
            '??' => 'E', '??' => 'e',
            '??' => 'E', '??' => 'e',
            '??' => 'E', '??' => 'e',
            '??' => 'G', '??' => 'g',
            '??' => 'G', '??' => 'g',
            '??' => 'G', '??' => 'g',
            '??' => 'G', '??' => 'g',
            '??' => 'H', '??' => 'h',
            '??' => 'H', '??' => 'h',
            '??' => 'I', '??' => 'i',
            '??' => 'I', '??' => 'i',
            '??' => 'I', '??' => 'i',
            '??' => 'I', '??' => 'i',
            '??' => 'I', '??' => 'i',
            '??' => 'IJ', '??' => 'ij',
            '??' => 'J', '??' => 'j',
            '??' => 'K', '??' => 'k',
            '??' => 'k', '??' => 'L',
            '??' => 'l', '??' => 'L',
            '??' => 'l', '??' => 'L',
            '??' => 'l', '??' => 'L',
            '??' => 'l', '??' => 'L',
            '??' => 'l', '??' => 'N',
            '??' => 'n', '??' => 'N',
            '??' => 'n', '??' => 'N',
            '??' => 'n', '??' => 'n',
            '??' => 'N', '??' => 'n',
            '??' => 'O', '??' => 'o',
            '??' => 'O', '??' => 'o',
            '??' => 'O', '??' => 'o',
            '??' => 'OE', '??' => 'oe',
            '??' => 'R', '??' => 'r',
            '??' => 'R', '??' => 'r',
            '??' => 'R', '??' => 'r',
            '??' => 'S', '??' => 's',
            '??' => 'S', '??' => 's',
            '??' => 'S', '??' => 's',
            '??' => 'S', '??' => 's',
            '??' => 'T', '??' => 't',
            '??' => 'T', '??' => 't',
            '??' => 'T', '??' => 't',
            '??' => 'U', '??' => 'u',
            '??' => 'U', '??' => 'u',
            '??' => 'U', '??' => 'u',
            '??' => 'U', '??' => 'u',
            '??' => 'U', '??' => 'u',
            '??' => 'U', '??' => 'u',
            '??' => 'W', '??' => 'w',
            '??' => 'Y', '??' => 'y',
            '??' => 'Y', '??' => 'Z',
            '??' => 'z', '??' => 'Z',
            '??' => 'z', '??' => 'Z',
            '??' => 'z', '??' => 's',
            // Decompositions for Latin Extended-B
            '??' => 'S', '??' => 's',
            '??' => 'T', '??' => 't',
            // Euro Sign
            '???' => 'E',
            // GBP (Pound) Sign
            '??' => '',
            // Vowels with diacritic (Vietnamese)
            // unmarked
            '??' => 'O', '??' => 'o',
            '??' => 'U', '??' => 'u',
            // grave accent
            '???' => 'A', '???' => 'a',
            '???' => 'A', '???' => 'a',
            '???' => 'E', '???' => 'e',
            '???' => 'O', '???' => 'o',
            '???' => 'O', '???' => 'o',
            '???' => 'U', '???' => 'u',
            '???' => 'Y', '???' => 'y',
            // hook
            '???' => 'A', '???' => 'a',
            '???' => 'A', '???' => 'a',
            '???' => 'A', '???' => 'a',
            '???' => 'E', '???' => 'e',
            '???' => 'E', '???' => 'e',
            '???' => 'I', '???' => 'i',
            '???' => 'O', '???' => 'o',
            '???' => 'O', '???' => 'o',
            '???' => 'O', '???' => 'o',
            '???' => 'U', '???' => 'u',
            '???' => 'U', '???' => 'u',
            '???' => 'Y', '???' => 'y',
            // tilde
            '???' => 'A', '???' => 'a',
            '???' => 'A', '???' => 'a',
            '???' => 'E', '???' => 'e',
            '???' => 'E', '???' => 'e',
            '???' => 'O', '???' => 'o',
            '???' => 'O', '???' => 'o',
            '???' => 'U', '???' => 'u',
            '???' => 'Y', '???' => 'y',
            // acute accent
            '???' => 'A', '???' => 'a',
            '???' => 'A', '???' => 'a',
            '???' => 'E', '???' => 'e',
            '???' => 'O', '???' => 'o',
            '???' => 'O', '???' => 'o',
            '???' => 'U', '???' => 'u',
            // dot below
            '???' => 'A', '???' => 'a',
            '???' => 'A', '???' => 'a',
            '???' => 'A', '???' => 'a',
            '???' => 'E', '???' => 'e',
            '???' => 'E', '???' => 'e',
            '???' => 'I', '???' => 'i',
            '???' => 'O', '???' => 'o',
            '???' => 'O', '???' => 'o',
            '???' => 'O', '???' => 'o',
            '???' => 'U', '???' => 'u',
            '???' => 'U', '???' => 'u',
            '???' => 'Y', '???' => 'y',
            // Vowels with diacritic (Chinese, Hanyu Pinyin)
            '??' => 'a',
            // macron
            '??' => 'U', '??' => 'u',
            // acute accent
            '??' => 'U', '??' => 'u',
            // caron
            '??' => 'A', '??' => 'a',
            '??' => 'I', '??' => 'i',
            '??' => 'O', '??' => 'o',
            '??' => 'U', '??' => 'u',
            '??' => 'U', '??' => 'u',
            // grave accent
            '??' => 'U', '??' => 'u',
        );

        $string = strtr($string, $chars);
    } else {
        $chars = array();
        // Assume ISO-8859-1 if not UTF-8
        $chars['in'] = "\x80\x83\x8a\x8e\x9a\x9e"
                . "\x9f\xa2\xa5\xb5\xc0\xc1\xc2"
                . "\xc3\xc4\xc5\xc7\xc8\xc9\xca"
                . "\xcb\xcc\xcd\xce\xcf\xd1\xd2"
                . "\xd3\xd4\xd5\xd6\xd8\xd9\xda"
                . "\xdb\xdc\xdd\xe0\xe1\xe2\xe3"
                . "\xe4\xe5\xe7\xe8\xe9\xea\xeb"
                . "\xec\xed\xee\xef\xf1\xf2\xf3"
                . "\xf4\xf5\xf6\xf8\xf9\xfa\xfb"
                . "\xfc\xfd\xff";

        $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

        $string = strtr($string, $chars['in'], $chars['out']);
        $double_chars = array();
        $double_chars['in'] = array("\x8c", "\x9c", "\xc6", "\xd0", "\xde", "\xdf", "\xe6", "\xf0", "\xfe");
        $double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
        $string = str_replace($double_chars['in'], $double_chars['out'], $string);
    }

    return $string;
}

function event($event, $value = NULL, $callback = NULL) {
    static $events;

    // Adding or removing a callback?
    if ($callback !== NULL) {
        if ($callback) {
            $events[$event][] = $callback;
        } else {
            unset($events[$event]);
        }
    } elseif (isset($events[$event])) { // Fire a callback
        foreach ($events[$event] as $function) {
            $value = call_user_func($function, $value);
        }
        return $value;
    }
}

/**
 * Fetch a config value from a module configuration file
 *
 * @param string $file name of the config
 * @param boolean $clear to clear the config object
 * @return object
 */
function config($file = 'Config', $clear = FALSE) {
    static $configs = array();

    if ($clear) {
        unset($configs[$file]);
        return;
    }

    if (empty($configs[$file])) {
        //$configs[$file] = new \Micro\Config($file);
        require(SP . 'Config/' . $file . EXT);
        $configs[$file] = (object) $config;
        //print dump($configs);
    }

    return $configs[$file];
}

/**
 * Return an HTML safe dump of the given variable(s) surrounded by "pre" tags.
 * You can pass any number of variables (of any type) to this function.
 *
 * @param mixed
 * @return string
 */
function dump() {
    $string = '';
    foreach (func_get_args() as $value) {
        $string .= '<pre>' . h($value === NULL ? 'NULL' : (is_scalar($value) ? $value : print_r($value, TRUE))) . "</pre>\n";
    }
    return $string;
}

/**
 * Safely fetch a $_POST value, defaulting to the value provided if the key is
 * not found.
 *
 * @param string $key name
 * @param mixed $default value if key is not found
 * @param boolean $string TRUE to require string type
 * @return mixed
 */
function post($key, $default = NULL, $string = FALSE) {
    if (isset($_POST[$key])) {
        return $string ? (string) $_POST[$key] : $_POST[$key];
    }
    return $default;
}

/**
 * Safely fetch a $_GET value, defaulting to the value provided if the key is
 * not found.
 *
 * @param string $key name
 * @param mixed $default value if key is not found
 * @param boolean $string TRUE to require string type
 * @return mixed
 */
function get($key, $default = NULL, $string = FALSE) {
    if (isset($_GET[$key])) {
        return $string ? (string) $_GET[$key] : $_GET[$key];
    }
    return $default;
}

/**
 * Safely fetch a $_SESSION value, defaulting to the value provided if the key is
 * not found.
 *
 * @param string $k the post key
 * @param mixed $d the default value if key is not found
 * @return mixed
 */
function session($k, $d = NULL) {
    return isset($_SESSION[$k]) ? $_SESSION[$k] : $d;
}

/**
 * Create a random 32 character MD5 token
 *
 * @return string
 */
function token() {
    return md5(str_shuffle(chr(mt_rand(32, 126)) . uniqid() . microtime(TRUE)));
}

/**
 * Write to the application log file using error_log
 *
 * @param string $message to save
 * @return bool
 */
function log_message($message) {
    $path = SP . 'Storage/Log/' . date('Y-m-d') . '.log';

    // Append date and IP to log message
    return error_log(date('H:i:s ') . getenv('REMOTE_ADDR') . " $message\n", 3, $path);
}

/**
 * Send a HTTP header redirect using "location" or "refresh".
 *
 * @param string $url the URL string
 * @param int $c the HTTP status code
 * @param string $method either location or redirect
 */
function redirect($url = NULL, $code = 302, $method = 'location') {
    if (strpos($url, '://') === FALSE) {
        $url = site_url($url);
    }

    //print dump($url);

    header($method == 'refresh' ? "Refresh:0;url = $url" : "Location: $url", TRUE, $code);
}

/*
 * Return the full URL to a path on this site or another.
 *
 * @param string $uri may contain another sites TLD
 * @return string
 *
  function site_url($uri = NULL)
  {
  return (strpos($uri, '://') === FALSE ? \Micro\URL::get() : '') . ltrim($uri, '/');
  }
 */

/**
 * Return the full URL to a location on this site
 *
 * @param string $path to use or FALSE for current path
 * @param array $params to append to URL
 * @return string
 */
function site_url($path = NULL, array $params = NULL) {
    // In PHP 5.4, http_build_query will support RFC 3986
    return DOMAIN . ($path ? '/' . trim($path, '/') : PATH)
            . ($params ? '?' . str_replace('+', '%20', http_build_query($params, TRUE, '&')) : '');
}

/**
 * Return the current URL with path and query params
 *
 * @return string
 *
  function current_url()
  {
  return DOMAIN . getenv('REQUEST_URI');
  }
 */

/**
 * Convert a string from one encoding to another encoding
 * and remove invalid bytes sequences.
 *
 * @param string $string to convert
 * @param string $to encoding you want the string in
 * @param string $from encoding that string is in
 * @return string
 */
function encode($string, $to = 'UTF-8', $from = 'UTF-8') {
    // ASCII is already valid UTF-8
    if ($to == 'UTF-8' AND is_ascii($string)) {
        return $string;
    }

    // Convert the string
    return @iconv($from, $to . '//TRANSLIT//IGNORE', $string);
}

/**
 * Tests whether a string contains only 7bit ASCII characters.
 *
 * @param string $string to check
 * @return bool
 */
function is_ascii($string) {
    return !preg_match('/[^\x00-\x7F]/S', $string);
}

/**
 * Encode a string so it is safe to pass through the URL
 *
 * @param string $string to encode
 * @return string
 */
function base64_url_encode($string = NULL) {
    return strtr(base64_encode($string), '+/=', '-_~');
}

/**
 * Decode a string passed through the URL
 *
 * @param string $string to decode
 * @return string
 */
function base64_url_decode($string = NULL) {
    return base64_decode(strtr($string, '-_~', '+/='));
}

/**
 * Convert special characters to HTML safe entities.
 *
 * @param string $string to encode
 * @return string
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'utf-8');
}

/**
 * Filter a valid UTF-8 string so that it contains only words, numbers,
 * dashes, underscores, periods, and spaces - all of which are safe
 * characters to use in file names, URI, XML, JSON, and (X)HTML.
 *
 * @param string $string to clean
 * @param bool $spaces TRUE to allow spaces
 * @return string
 */
function sanitize($string, $spaces = TRUE) {
    $search = array(
        '/[^\w\-\. ]+/u', // Remove non safe characters
        '/\s\s+/', // Remove extra whitespace
        '/\.\.+/', '/--+/', '/__+/' // Remove duplicate symbols
    );

    $string = preg_replace($search, array(' ', ' ', '.', '-', '_'), $string);

    if (!$spaces) {
        $string = preg_replace('/--+/', '-', str_replace(' ', '-', $string));
    }

    return trim($string, '-._ ');
}

/**
 * Create a SEO friendly URL string from a valid UTF-8 string.
 *
 * @param string $string to filter
 * @return string
 */
function sanitize_url($string) {
    return urlencode(mb_strtolower(sanitize($string, FALSE)));
}

/**
 * Filter a valid UTF-8 string to be file name safe.
 *
 * @param string $string to filter
 * @return string
 */
function sanitize_filename($string) {
    return sanitize($string, FALSE);
}

/**
 * Return a SQLite/MySQL/PostgreSQL datetime string
 *
 * @param int $timestamp
 */
function sql_date($timestamp = NULL) {
    return date('Y-m-d H:i:s', $timestamp ? : time());
}

/**
 * Make a request to the given URL using cURL.
 *
 * @param string $url to request
 * @param array $options for cURL object
 * @return object
 */
function curl_request($url, array $options = NULL) {
    $ch = curl_init($url);

    $defaults = array(
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_TIMEOUT => 5,
    );

    // Connection options override defaults if given
    curl_setopt_array($ch, (array) $options + $defaults);

    // Create a response object
    $object = new stdClass;

    // Get additional request info
    $object->response = curl_exec($ch);
    $object->error_code = curl_errno($ch);
    $object->error = curl_error($ch);
    $object->info = curl_getinfo($ch);

    curl_close($ch);

    return $object;
}

/**
 * Create a RecursiveDirectoryIterator object
 *
 * @param string $dir the directory to load
 * @param boolean $recursive to include subfolders
 * @return object
 */
function directory($dir, $recursive = TRUE) {
    $i = new \RecursiveDirectoryIterator($dir);

    if (!$recursive)
        return $i;

    return new \RecursiveIteratorIterator($i, \RecursiveIteratorIterator::SELF_FIRST);
}

/**
 * Make sure that a directory exists and is writable by the current PHP process.
 *
 * @param string $dir the directory to load
 * @param string $chmod value as octal
 * @return boolean
 */
function directory_is_writable($dir, $chmod = 0755) {
    // If it doesn't exist, and can't be made
    if (!is_dir($dir) AND !mkdir($dir, $chmod, TRUE))
        return FALSE;

    // If it isn't writable, and can't be made writable
    if (!is_writable($dir) AND !chmod($dir, $chmod))
        return FALSE;

    return TRUE;
}

/**
 * Convert any given variable into a SimpleXML object
 *
 * @param mixed $object variable object to convert
 * @param string $root root element name
 * @param object $xml xml object
 * @param string $unknown element name for numeric keys
 * @param string $doctype XML doctype
 */
function to_xml($object, $root = 'data', $xml = NULL, $unknown = 'element', $doctype = "<?xml version = '1.0' encoding = 'utf-8'?>") {
    if (is_null($xml)) {
        $xml = simplexml_load_string("$doctype<$root/>");
    }

    foreach ((array) $object as $k => $v) {
        if (is_int($k)) {
            $k = $unknown;
        }

        if (is_scalar($v)) {
            $xml->addChild($k, h($v));
        } else {
            $v = (array) $v;
            $node = array_diff_key($v, array_keys(array_keys($v))) ? $xml->addChild($k) : $xml;
            $this->from($v, $k, $node);
        }
    }

    return $xml;
}

/**
 * Return an IntlDateFormatter object using the current system locale
 *
 * @param string $locale string
 * @param integer $datetype IntlDateFormatter constant
 * @param integer $timetype IntlDateFormatter constant
 * @param string $timezone Time zone ID, default is system default
 * @return IntlDateFormatter
 */
function __date($locale = NULL, $datetype = IntlDateFormatter::MEDIUM, $timetype = IntlDateFormatter::SHORT, $timezone = NULL) {
    return new IntlDateFormatter($locale ? : setlocale(LC_ALL, 0), $datetype, $timetype, $timezone);
}

/**
 * Format the given string using the current system locale
 * Basically, it's sprintf on i18n steroids.
 *
 * @param string $string to parse
 * @param array $params to insert
 * @return string
 */
function __($string, array $params = NULL) {
    return msgfmt_format_message(setlocale(LC_ALL, 0), $string, $params);
}

/**
 * Color output text for the CLI
 *
 * @param string $text to color
 * @param string $color of text
 * @param string $background color
 */
function colorize($text, $color, $bold = FALSE) {
    // Standard CLI colors
    $colors = array_flip(array(30 => 'gray', 'red', 'green', 'yellow', 'blue', 'purple', 'cyan', 'white', 'black'));

    // Escape string with color information
    return"\033[" . ($bold ? '1' : '0') . ';' . $colors[$color] . "m$text\033[0m";
}

// End

function normalizePath($path, $separator = '\\/') {
    // Remove any kind of funky unicode whitespace
    $normalized = preg_replace('#\p{C}+|^\./#u', '', $path);

    // Path remove self referring paths ("/./").
    $normalized = preg_replace('#/\.(?=/)|^\./|\./$#', '', $normalized);

    // Regex for resolving relative paths
    $regex = '#\/*[^/\.]+/\.\.#Uu';

    while (preg_match($regex, $normalized)) {
        $normalized = preg_replace($regex, '', $normalized);
    }

    if (preg_match('#/\.{2}|\.{2}/#', $normalized)) {
        throw new LogicException('Path is outside of the defined root, path: [' . $path . '], resolved: [' . $normalized . ']');
    }

    return trim($normalized, $separator);
}

function urlStrSolver($R_URI) {
    //var_dump(ADMIN);
    global $adminDir;
    $re = '@([/]{2})@m';
    preg_match_all($re, $R_URI, $matches, PREG_SET_ORDER, 0);
//  var_dump($matches);
//    if (defined('ADMIN')) {
//        return $R_URI;
//    }
    if (!$matches) {
        if (strpos($R_URI, ".") > 0 || strpos($R_URI, "?") > 0 || strpos($R_URI, $adminDir) !== false) {
            return $R_URI;
        } else {
            if (substr($R_URI, -1) != '/') {
                $redUrlr = trim($R_URI, '/') . "/";
                $redUrlr = preg_replace($re, "/", $redUrlr);
                //var_dump($redUrlr);
                header('Location: /' . SUB_ROOT . $redUrlr); //Solve Url if wrong
                header("HTTP/1.0 301 Moved Permanently");
                exit;
            }
        }
//        if (get_option('html_ext') == 'true') {
//            if (substr($R_URI, -5) != '.html') {
//                $redUrlr = trim($R_URI, '/') . ".html";
//                $redUrlr = preg_replace($re, "/", $redUrlr);
//                //var_dump($redUrlr);
//                header('Location: /' . SUB_ROOT . $redUrlr); //Solve Url if wrong
//                header("HTTP/1.0 301 Moved Permanently");
//                exit;
//            }
//        } else {
//            if (substr($R_URI, -1) != '/') {
//                $redUrlr = trim($R_URI, '/') . "/";
//                $redUrlr = preg_replace($re, "/", $redUrlr);
//                //var_dump($redUrlr);
//                header('Location: /' . SUB_ROOT . $redUrlr); //Solve Url if wrong
//                header("HTTP/1.0 301 Moved Permanently");
//                exit;
//            }
//        }
        $R_URI = trim($R_URI, '/');
    } else {
        if (!webApp()) {
            $redUrlr = trim($R_URI, '/') . "/";
            $redUrlr = preg_replace($re, "/", $redUrlr);
            //var_dump($redUrlr);
            $ur = SUB_ROOT . "/" . $redUrlr;
            $ur = trim_slash($ur);
            header('Location: /' . $ur); //Solve Url if wrong
            header("HTTP/1.0 301 Moved Permanently");
            exit;
        }
    }
    return $R_URI;
}
