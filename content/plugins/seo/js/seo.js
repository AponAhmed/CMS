/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function() {

    jQuery(".wooSeoTab .nav-tab").click(function() {
        var rq = jQuery(this).attr('dataTarg');
        jQuery('.wooSingleTab').removeClass('in');
        jQuery(rq).addClass('in');
        jQuery(".wooSeoTab .nav-tab").removeClass('nav-tab-active');
        jQuery(this).addClass('nav-tab-active');
    });
    jQuery('.collapsBtn').click(function() {
        var targetObj = jQuery(this).attr('datatarget');
        jQuery(targetObj).slideToggle();
    });


});
String.prototype.replaceArray = function(find, replace) {
    var replaceString = this;
    var regex;
    for (var i = 0; i < find.length; i++) {
        regex = new RegExp(" " + find[i] + " ", "g");
        replaceString = replaceString.replace(regex, replace);
        regex = new RegExp("-" + find[i] + "-", "g");
        replaceString = replaceString.replace(regex, replace);
        regex = new RegExp("-" + find[i] + " ", "g");
        replaceString = replaceString.replace(regex, replace);
        regex = new RegExp(" " + find[i] + "-", "g");
        replaceString = replaceString.replace(regex, replace);
    }
    return replaceString;
};

String.prototype.replaceArrayArray = function(find, replace) {
    var replaceString = this;
    var regex;
    for (var i = 0; i < find.length; i++) {
        regex = new RegExp(" " + find[i] + " ", "g");
        replaceString = replaceString.replace(regex, replace[i]);
        regex = new RegExp("-" + find[i] + "-", "g");
        replaceString = replaceString.replace(regex, replace[i]);
        regex = new RegExp("-" + find[i] + " ", "g");
        replaceString = replaceString.replace(regex, replace[i]);
        regex = new RegExp(" " + find[i] + "-", "g");
        replaceString = replaceString.replace(regex, replace[i]);
    }
    return replaceString;
};

function serp_calc() {

    var badChr = ["a", "about", "above", "after", "again", "against", "all", "am", "an", "any", "are", "as", "at", "be", "because", "been", "before", "being", "below", "between", "both", "but", "by", "could", "did", "do", "does", "doing", "down", "during", "each", "few", "for", "from", "further", "had", "has", "have", "having", "he", "he'd", "he'll", "he's", "her", "here", "here's", "hers", "herself", "him", "himself", "his", "how", "how's", "i", "i'd", "i'll", "i'm", "i've", "if", "in", "into", "is", "it", "it's", "its", "itself", "let's", "me", "more", "most", "my", "myself", "nor", "of", "on", "once", "only", "or", "other", "ought", "our", "ours", "ourselves", "out", "over", "own", "same", "she", "she'd", "she'll", "she's", "should", "so", "some", "such", "than", "that", "that's", "the", "their", "theirs", "them", "themselves", "then", "there", "there's", "these", "they", "they'd", "they'll", "they're", "they've", "this", "those", "through", "to", "too", "under", "until", "up", "very", "was", "we", "we'd", "we'll", "we're", "we've", "were", "what", "what's", "when", "when's", "where", "where's", "which", "while", "who", "who's", "whom", "why", "why's", "with", "would", "you", "you'd", "you'll", "you're", "you've", "your", "yours", "yourself", "yourselves"];
    slug = jQuery('#slug').val();

    var find = ["&", "<", ">", " "];
    var repl = [" &amp; ", " &lt; ", " &gt; ", "-"];

    if (slug) {
        slug = slug.replaceArray(badChr, " ");
        slug = slug.replaceArrayArray(find, repl);
        jQuery('#slug').val(slug);
    }




//    var h1t = jQuery('#h1Txt').val();
//    h1t = h1t.replaceArrayArray(find, repl);
//    h1t = h1t.replaceArray(badChr, " ");
//    //alert(h1t);
//    jQuery('#h1Txt').val(h1t);

//    var ttl = jQuery('#Ctitle').val();
//    ttl = ttl.replaceArrayArray(find, repl);
//    ttl = ttl.replaceArray(badChr, " ");
//    jQuery('#Ctitle').val(ttl);


    var mtK = jQuery('#metaK').val();
    mtK = mtK.replaceArrayArray(find, repl);
    mtK = mtK.replaceArray(badChr, " ");
    jQuery('#metaK').val(mtK);

    var mtD = jQuery('#metaD').val();
    mtD = mtD.replaceArrayArray(find, repl);
    // mtD = mtD.replaceArray(badChr, " ");
    jQuery('#metaD').val(mtD);

    //Filter-- End--

    var ttl = jQuery('#Ctitle').val();
    var slug = jQuery('#slug').val();
    var metaK = jQuery('#metaK').val();
    var metaD = jQuery('#metaD').val();

    var h1Txt = jQuery('#h1Txt').val();
    //=================Count================

    if (h1Txt.length > 70) {
        jQuery('#h1Count').css('color', 'red');
    } else {
        jQuery('#h1Count').removeAttr('style');
    }

    jQuery('#ttlCount').html(ttl.length);
    jQuery('#descCount').html(metaD.length);
    jQuery('#h1Count').html(h1Txt.length);

    if (metaK != '') {
        var k = metaK.split(',');
        jQuery('#keyCount').html(k.length);
        if (k.length > 10) {
            jQuery('#keyCount').css('color', 'red');
        } else {
            jQuery('#keyCount').removeAttr('style');
        }
        //Meta Keyword slice
        if (k.length > 10) {
            metaK = k.slice(0, 10) + "...";
        }
        //
    }
    if (ttl.length > 70) {
        jQuery('#ttlCount').css('color', 'red');
    } else {
        jQuery('#ttlCount').removeAttr('style');
    }


    if (metaD.length > 300) {
        jQuery('#descCount').css('color', '#8aab4a');
    } else if (metaD.length > 270) {
        jQuery('#descCount').css('color', '#3d804a');
    } else {
        jQuery('#descCount').css('color', 'red');
    }


//    if (metaD.length > 300) {
//        jQuery('#descCount').css('color', 'red');
//    } else if (metaD.length > 270) {
//        jQuery('#descCount').css('color', 'green');
//    } else if (metaD.length > 270) {
//        jQuery('#descCount').css('color', 'green');
//    } else {
//        jQuery('#descCount').removeAttr('style');
//    }

    //=================Output==================
    //
    //Title slice---
    if (ttl.length > 70) {
        ttl = ttl.substr(0, 70) + "...";
    }
    //
    //Description slice----
    if (metaD.length > 156) {
        metaD = metaD.substr(0, 156) + "...";
    }
    //

    jQuery('#Otitle').html(ttl);
    //jQuery('#Ourl').html(siteUrl + slug);
    jQuery('#Omk').html(metaK);
    jQuery('#OmD').html(metaD);
    //jQuery('#OmD').html(metaD);


}


function serp_calc_texo() {
    //console.log("asds");
    var badChr = ["a", "about", "above", "after", "again", "against", "all", "am", "an", "any", "are", "as", "at", "be", "because", "been", "before", "being", "below", "between", "both", "but", "by", "could", "did", "do", "does", "doing", "down", "during", "each", "few", "for", "from", "further", "had", "has", "have", "having", "he", "he'd", "he'll", "he's", "her", "here", "here's", "hers", "herself", "him", "himself", "his", "how", "how's", "i", "i'd", "i'll", "i'm", "i've", "if", "in", "into", "is", "it", "it's", "its", "itself", "let's", "me", "more", "most", "my", "myself", "nor", "of", "on", "once", "only", "or", "other", "ought", "our", "ours", "ourselves", "out", "over", "own", "same", "she", "she'd", "she'll", "she's", "should", "so", "some", "such", "than", "that", "that's", "the", "their", "theirs", "them", "themselves", "then", "there", "there's", "these", "they", "they'd", "they'll", "they're", "they've", "this", "those", "through", "to", "too", "under", "until", "up", "very", "was", "we", "we'd", "we'll", "we're", "we've", "were", "what", "what's", "when", "when's", "where", "where's", "which", "while", "who", "who's", "whom", "why", "why's", "with", "would", "you", "you'd", "you'll", "you're", "you've", "your", "yours", "yourself", "yourselves"];
    slug = jQuery('#slug').val();

    var find = ["&", "<", ">", " "];
    var repl = [" &amp; ", " &lt; ", " &gt; ", "-"];

    if (slug) {
        slug = slug.replaceArray(badChr, " ");
        slug = slug.replaceArrayArray(find, repl);
        jQuery('#slug').val(slug);
    }
    var mtK = jQuery('#meta_keyword').val();
    mtK = mtK.replaceArrayArray(find, repl);
    mtK = mtK.replaceArray(badChr, " ");
    jQuery('#meta_keyword').val(mtK);

    var mtD = jQuery('#meta_description').val();
    mtD = mtD.replaceArrayArray(find, repl);
    // mtD = mtD.replaceArray(badChr, " ");
    jQuery('#meta_description').val(mtD);

    //Filter-- End--

    var ttl = jQuery('#customTitle').val();
    var slug = jQuery('#slug').val();
    var metaK = jQuery('#meta_keyword').val();
    var metaD = jQuery('#meta_description').val();

    var h1Txt = jQuery('#meta_h1_text').val();
    //=================Count================

    if (h1Txt.length > 70) {
        jQuery('#h1Count').css('color', 'red');
    } else {
        jQuery('#h1Count').removeAttr('style');
    }

    jQuery('#ttlCount').html(ttl.length);
    jQuery('#descCount').html(metaD.length);
    jQuery('#h1Count').html(h1Txt.length);

    if (metaK != '') {
        var k = metaK.split(',');
        jQuery('#keyCount').html(k.length);
        if (k.length > 10) {
            jQuery('#keyCount').css('color', 'red');
        } else {
            jQuery('#keyCount').removeAttr('style');
        }
        //Meta Keyword slice
        if (k.length > 10) {
            metaK = k.slice(0, 10) + "...";
        }
        //
    }
    if (ttl.length > 70) {
        jQuery('#ttlCount').css('color', 'red');
    } else {
        jQuery('#ttlCount').removeAttr('style');
    }


    if (metaD.length > 300) {
        jQuery('#descCount').css('color', '#8aab4a');
    } else if (metaD.length > 270) {
        jQuery('#descCount').css('color', '#3d804a');
    } else {
        jQuery('#descCount').css('color', 'red');
    }

    if (ttl.length > 70) {
        ttl = ttl.substr(0, 70) + "...";
    }
    //
    //Description slice----
    if (metaD.length > 156) {
        metaD = metaD.substr(0, 156) + "...";
    }
    //
    //console.log(ttl);
    jQuery('#Otitle').html(ttl);
    //jQuery('#Ourl').html(siteUrl + slug);
    jQuery('#Omk').html(metaK);
    jQuery('#OmD').html(metaD);
    //jQuery('#OmD').html(metaD);


}



function cleanKeyDes() {
    jQuery("#massDes").html("");
    jQuery("#massKey").html("");

    var metaK = jQuery('#metaK').val();
    var keys = metaK.split(',');

    var metaD = jQuery('#metaD').val();
    var desPr = metaD.split(',');

    var Htext = jQuery('#h1Txt').val();
    var HtextPhr = Htext.split(',');
//----------------------------------------------
    var desRes = countDupl(desPr);
    var desR = rCount(desPr);
    jQuery('#metaD').val(desRes);
    if (desR) {
        jQuery("#massDes").html(desR + " Phrase removed from Description");
    }
//---------
    var keyRes = countDupl(keys);
    var keyR = rCount(keys);
    jQuery('#metaK').val(keyRes);
    if (keyR) {
        jQuery("#massKey").html(keyR + " Phrase removed from Keyword");
    }
//---------
    var HtextRes = countDupl(HtextPhr);
    var HtextR = rCount(HtextPhr);
    jQuery('#Htext').val(HtextRes);
    if (HtextR) {
        jQuery("#massHtext").html(HtextR + " Phrase removed from Hidden Text");
    }
    serp_calc();
    updateMetaTag();
}

function countDupl(phr) {
    var lnth = phr.length;
    var uniqueNames = [];
    jQuery.each(phr, function(i, el) {
        if (jQuery.inArray(el, uniqueNames) === -1)
            uniqueNames.push(el);
    });
    return uniqueNames;
}
function rCount(phr) {
    var lnth = phr.length;
    var uniqueNames = [];
    jQuery.each(phr, function(i, el) {
        if (jQuery.inArray(el.toLowerCase(), uniqueNames) === -1)
            uniqueNames.push(el.toLowerCase());
    });
    return (lnth - uniqueNames.length);
}

function generateKey() {
    var keys = jQuery('#generatedKey').val();
//    if (jQuery('#incKey').is(":checked"))
//    {
//        var desKey = jQuery('#descKey').val();
//        keys = keys + desKey;
//    }
    jQuery('#metaK').val(keys);
    cleanKeyDes();
    serp_calc();
}

function slugKeylookup(_this) {
    $(_this).append(loader);
    var fd = {ajx_action: 'slugKeylookup', ID: $('#ID').val()};
    jQuery.ajax({
        type: "POST",
        url: 'index.php',
        data: fd,
        success: function(data)
        {
            $(_this).find('.spinLoader').remove();
            $.fancybox.open('<div style="width:500px">' + data + '</div>');
        }
    });

}
function slugKeylookupCustom(_this) {
    $(_this).append(loader);
    var phCheck = false;
    if ($("#phraseCheck").prop('checked') == true) {
        phCheck = true
    }
    var fd = {ajx_action: 'slugKeylookup', ID: $('#ID').val(), cstr: $("#customQueryString").val(), phCheck: phCheck};
    jQuery.ajax({
        type: "POST",
        url: 'index.php',
        data: fd,
        success: function(data)
        {
            $.fancybox.close();
            $(_this).find('.spinLoader').remove();
            $.fancybox.open('<div style="width:500px">' + data + '</div>');
        }
    });

}