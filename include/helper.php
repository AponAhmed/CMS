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
        'â' => true,
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
        'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
        'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
        'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
        'ß' => 'ss',
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
        'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
        'ÿ' => 'y',
        // Latin symbols
        '©' => '(c)',
        // Greek
        'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
        'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
        'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
        'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
        'Ϋ' => 'Y',
        'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
        'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
        'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
        'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
        'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
        // Turkish
        'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
        'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
        // Russian
        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
        'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
        'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
        'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
        'Я' => 'Ya',
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
        'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
        'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
        'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
        'я' => 'ya',
        // Ukrainian
        'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
        'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
        // Czech
        'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
        'Ž' => 'Z',
        'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
        'ž' => 'z',
        // Polish
        'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
        'Ż' => 'Z',
        'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
        'ż' => 'z',
        // Latvian
        'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
        'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
        'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
        'š' => 's', 'ū' => 'u', 'ž' => 'z'
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
            'ª' => 'a', 'º' => 'o',
            'À' => 'A', 'Á' => 'A',
            'Â' => 'A', 'Ã' => 'A',
            'Ä' => 'A', 'Å' => 'A',
            'Æ' => 'AE', 'Ç' => 'C',
            'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E',
            'Ì' => 'I', 'Í' => 'I',
            'Î' => 'I', 'Ï' => 'I',
            'Ð' => 'D', 'Ñ' => 'N',
            'Ò' => 'O', 'Ó' => 'O',
            'Ô' => 'O', 'Õ' => 'O',
            'Ö' => 'O', 'Ù' => 'U',
            'Ú' => 'U', 'Û' => 'U',
            'Ü' => 'U', 'Ý' => 'Y',
            'Þ' => 'TH', 'ß' => 's',
            'à' => 'a', 'á' => 'a',
            'â' => 'a', 'ã' => 'a',
            'ä' => 'a', 'å' => 'a',
            'æ' => 'ae', 'ç' => 'c',
            'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i',
            'î' => 'i', 'ï' => 'i',
            'ð' => 'd', 'ñ' => 'n',
            'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o',
            'ö' => 'o', 'ø' => 'o',
            'ù' => 'u', 'ú' => 'u',
            'û' => 'u', 'ü' => 'u',
            'ý' => 'y', 'þ' => 'th',
            'ÿ' => 'y', 'Ø' => 'O',
            // Decompositions for Latin Extended-A
            'Ā' => 'A', 'ā' => 'a',
            'Ă' => 'A', 'ă' => 'a',
            'Ą' => 'A', 'ą' => 'a',
            'Ć' => 'C', 'ć' => 'c',
            'Ĉ' => 'C', 'ĉ' => 'c',
            'Ċ' => 'C', 'ċ' => 'c',
            'Č' => 'C', 'č' => 'c',
            'Ď' => 'D', 'ď' => 'd',
            'Đ' => 'D', 'đ' => 'd',
            'Ē' => 'E', 'ē' => 'e',
            'Ĕ' => 'E', 'ĕ' => 'e',
            'Ė' => 'E', 'ė' => 'e',
            'Ę' => 'E', 'ę' => 'e',
            'Ě' => 'E', 'ě' => 'e',
            'Ĝ' => 'G', 'ĝ' => 'g',
            'Ğ' => 'G', 'ğ' => 'g',
            'Ġ' => 'G', 'ġ' => 'g',
            'Ģ' => 'G', 'ģ' => 'g',
            'Ĥ' => 'H', 'ĥ' => 'h',
            'Ħ' => 'H', 'ħ' => 'h',
            'Ĩ' => 'I', 'ĩ' => 'i',
            'Ī' => 'I', 'ī' => 'i',
            'Ĭ' => 'I', 'ĭ' => 'i',
            'Į' => 'I', 'į' => 'i',
            'İ' => 'I', 'ı' => 'i',
            'Ĳ' => 'IJ', 'ĳ' => 'ij',
            'Ĵ' => 'J', 'ĵ' => 'j',
            'Ķ' => 'K', 'ķ' => 'k',
            'ĸ' => 'k', 'Ĺ' => 'L',
            'ĺ' => 'l', 'Ļ' => 'L',
            'ļ' => 'l', 'Ľ' => 'L',
            'ľ' => 'l', 'Ŀ' => 'L',
            'ŀ' => 'l', 'Ł' => 'L',
            'ł' => 'l', 'Ń' => 'N',
            'ń' => 'n', 'Ņ' => 'N',
            'ņ' => 'n', 'Ň' => 'N',
            'ň' => 'n', 'ŉ' => 'n',
            'Ŋ' => 'N', 'ŋ' => 'n',
            'Ō' => 'O', 'ō' => 'o',
            'Ŏ' => 'O', 'ŏ' => 'o',
            'Ő' => 'O', 'ő' => 'o',
            'Œ' => 'OE', 'œ' => 'oe',
            'Ŕ' => 'R', 'ŕ' => 'r',
            'Ŗ' => 'R', 'ŗ' => 'r',
            'Ř' => 'R', 'ř' => 'r',
            'Ś' => 'S', 'ś' => 's',
            'Ŝ' => 'S', 'ŝ' => 's',
            'Ş' => 'S', 'ş' => 's',
            'Š' => 'S', 'š' => 's',
            'Ţ' => 'T', 'ţ' => 't',
            'Ť' => 'T', 'ť' => 't',
            'Ŧ' => 'T', 'ŧ' => 't',
            'Ũ' => 'U', 'ũ' => 'u',
            'Ū' => 'U', 'ū' => 'u',
            'Ŭ' => 'U', 'ŭ' => 'u',
            'Ů' => 'U', 'ů' => 'u',
            'Ű' => 'U', 'ű' => 'u',
            'Ų' => 'U', 'ų' => 'u',
            'Ŵ' => 'W', 'ŵ' => 'w',
            'Ŷ' => 'Y', 'ŷ' => 'y',
            'Ÿ' => 'Y', 'Ź' => 'Z',
            'ź' => 'z', 'Ż' => 'Z',
            'ż' => 'z', 'Ž' => 'Z',
            'ž' => 'z', 'ſ' => 's',
            // Decompositions for Latin Extended-B
            'Ș' => 'S', 'ș' => 's',
            'Ț' => 'T', 'ț' => 't',
            // Euro Sign
            '€' => 'E',
            // GBP (Pound) Sign
            '£' => '',
            // Vowels with diacritic (Vietnamese)
            // unmarked
            'Ơ' => 'O', 'ơ' => 'o',
            'Ư' => 'U', 'ư' => 'u',
            // grave accent
            'Ầ' => 'A', 'ầ' => 'a',
            'Ằ' => 'A', 'ằ' => 'a',
            'Ề' => 'E', 'ề' => 'e',
            'Ồ' => 'O', 'ồ' => 'o',
            'Ờ' => 'O', 'ờ' => 'o',
            'Ừ' => 'U', 'ừ' => 'u',
            'Ỳ' => 'Y', 'ỳ' => 'y',
            // hook
            'Ả' => 'A', 'ả' => 'a',
            'Ẩ' => 'A', 'ẩ' => 'a',
            'Ẳ' => 'A', 'ẳ' => 'a',
            'Ẻ' => 'E', 'ẻ' => 'e',
            'Ể' => 'E', 'ể' => 'e',
            'Ỉ' => 'I', 'ỉ' => 'i',
            'Ỏ' => 'O', 'ỏ' => 'o',
            'Ổ' => 'O', 'ổ' => 'o',
            'Ở' => 'O', 'ở' => 'o',
            'Ủ' => 'U', 'ủ' => 'u',
            'Ử' => 'U', 'ử' => 'u',
            'Ỷ' => 'Y', 'ỷ' => 'y',
            // tilde
            'Ẫ' => 'A', 'ẫ' => 'a',
            'Ẵ' => 'A', 'ẵ' => 'a',
            'Ẽ' => 'E', 'ẽ' => 'e',
            'Ễ' => 'E', 'ễ' => 'e',
            'Ỗ' => 'O', 'ỗ' => 'o',
            'Ỡ' => 'O', 'ỡ' => 'o',
            'Ữ' => 'U', 'ữ' => 'u',
            'Ỹ' => 'Y', 'ỹ' => 'y',
            // acute accent
            'Ấ' => 'A', 'ấ' => 'a',
            'Ắ' => 'A', 'ắ' => 'a',
            'Ế' => 'E', 'ế' => 'e',
            'Ố' => 'O', 'ố' => 'o',
            'Ớ' => 'O', 'ớ' => 'o',
            'Ứ' => 'U', 'ứ' => 'u',
            // dot below
            'Ạ' => 'A', 'ạ' => 'a',
            'Ậ' => 'A', 'ậ' => 'a',
            'Ặ' => 'A', 'ặ' => 'a',
            'Ẹ' => 'E', 'ẹ' => 'e',
            'Ệ' => 'E', 'ệ' => 'e',
            'Ị' => 'I', 'ị' => 'i',
            'Ọ' => 'O', 'ọ' => 'o',
            'Ộ' => 'O', 'ộ' => 'o',
            'Ợ' => 'O', 'ợ' => 'o',
            'Ụ' => 'U', 'ụ' => 'u',
            'Ự' => 'U', 'ự' => 'u',
            'Ỵ' => 'Y', 'ỵ' => 'y',
            // Vowels with diacritic (Chinese, Hanyu Pinyin)
            'ɑ' => 'a',
            // macron
            'Ǖ' => 'U', 'ǖ' => 'u',
            // acute accent
            'Ǘ' => 'U', 'ǘ' => 'u',
            // caron
            'Ǎ' => 'A', 'ǎ' => 'a',
            'Ǐ' => 'I', 'ǐ' => 'i',
            'Ǒ' => 'O', 'ǒ' => 'o',
            'Ǔ' => 'U', 'ǔ' => 'u',
            'Ǚ' => 'U', 'ǚ' => 'u',
            // grave accent
            'Ǜ' => 'U', 'ǜ' => 'u',
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
