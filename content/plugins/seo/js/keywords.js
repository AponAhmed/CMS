jQuery.fn.highlight = function(pat) {
    function innerHighlight(node, pat) {
        var skip = 0;
        if (node.nodeType == 3) {
            var pos = node.data.toUpperCase().indexOf(pat);
            if (pos >= 0) {
                var spannode = document.createElement('span');
                spannode.className = 'orangeSoda_highlight';
                var middlebit = node.splitText(pos);
                var endbit = middlebit.splitText(pat.length);
                var middleclone = middlebit.cloneNode(true);
                spannode.appendChild(middleclone);
                middlebit.parentNode.replaceChild(spannode, middlebit);
                skip = 1;
            }
        }
        else if (node.nodeType == 1 && node.childNodes && !/(script|style)/i.test(node.tagName)) {
            for (var i = 0; i < node.childNodes.length; ++i) {
                i += innerHighlight(node.childNodes[i], pat);
            }
        }
        jQuery('#replace').html(jQuery(".orangeSoda_highlight").length);
        return skip;
    }
    return this.each(function() {
        innerHighlight(this, pat.toUpperCase());
    });
};

jQuery.fn.removeHighlight = function() {
    return this.find("span.orangeSoda_highlight").each(function() {
        this.parentNode.firstChild.nodeName;
        with (this.parentNode) {
            replaceChild(this.firstChild, this);
            normalize();
        }
    }).end();
};

jQuery.fn.wordCount = function()
{
    //for each keypress function on text areas

    total_words = jQuery('#PostEditor').html().split(/[\s\.\?]+/).length;
    jQuery('#os_word_counter').html(total_words);

};
jQuery(document).ready(function() {
    initKeyDen();
    //============
});

function initKeyDen() {
    // jQuery('.editor_text').text(jQuery('#PostEditor').html());
    total_words = jQuery('#PostEditor').html().split(/[\s\.\?]+/).length;
    jQuery('#os_word_counter').html(total_words);
    //My Way----    
    var str = jQuery('#PostEditor').text();
    var mostRword = nthMostCommon(str, 10);

    //alert(total_words)
    //console.log(mostRword);
    var sortedCities = sortProperties(mostRword);
    //console.log(sortedCities);
    mostRword = sortedCities;




    var wordCount = jQuery('#os_word_counter').html();
    wordCount *= .75;
    var msg = "<h2 style='margin-bottom: 0px; margin-top: 5px;'>Top keywords:</h2><table class='orangesoda_word_table' style='width:100%'><tbody><tr><th>Count</th><th>Density</th><th>Word</th></tr>";
    for (var key in mostRword) {
        if (mostRword.hasOwnProperty(key)) {
            //console.log(mostRword[key][1]);

            var searchNumber = mostRword[key][1]['occurences'];
            var w = mostRword[key][1]['word'];
            var percent = parseFloat((searchNumber / wordCount) * 100).toFixed(2)
            if (percent >= 5) {
                msg += '<tr style="color: green;"><td> ' + searchNumber + '</td><td> ' + percent + '% </td><td>' + w + '</td></tr>';
            } else {
                msg += '<tr><td> ' + searchNumber + '</td><td> ' + percent + '% </td><td>' + w + '</td></tr>';
            }
        }
    }
    msg += '</tbody></table>';
    // alert(msg);
    jQuery('#os_results').html(msg);
    jQuery('#PostEditor').wordCount();
}



// os_keyword_calculate();
function os_keyword_calculate()
{
    jQuery('.editor_text').html(jQuery('#PostEditor').html());
    var count = 5;
    var wordCount = jQuery('#os_word_counter').html();
    wordCount *= .75;

    jQuery.extend(jQuery.wordStats.stopWords, {'retrieved': true, '2007': true});

    jQuery.wordStats.computeTopWords(count, jQuery('.editor_text'));

    var msg = "<h2 style='margin-bottom: 0px; margin-top: 5px;'>Top keywords:</h2><table class='orangesoda_word_table' style='width:100%'><tbody><tr><th>Count</th><th>Density</th><th>Word</th></tr>";
    for (var i = 0, j = jQuery.wordStats.topWords.length; i < j && i <= count; i++) {
        var percent = parseFloat((jQuery.wordStats.topWeights[i] / wordCount) * 100).toFixed(2);
        if (percent >= 5) {
            msg += '<tr style="color: green;"><td> ' + jQuery.wordStats.topWeights[i] + '</td><td> ' + parseFloat((jQuery.wordStats.topWeights[i] / wordCount) * 100).toFixed(2) + '% </td><td>' + jQuery.wordStats.topWords[i].substring(1) + '</td></tr>';
        } else {
            msg += '<tr><td> ' + jQuery.wordStats.topWeights[i] + '</td><td> ' + parseFloat((jQuery.wordStats.topWeights[i] / wordCount) * 100).toFixed(2) + '% </td><td>' + jQuery.wordStats.topWords[i].substring(1) + '</td></tr>';
        }
    }
    msg += '</tbody></table>';
    jQuery('#os_results').html(msg);
    jQuery.wordStats.clear();
}

jQuery('#orangeSoda_search_button').on('click', orangeSoda_click);
function orangeSoda_click(e)
{
    var wordCount = jQuery('#os_word_counter').html();
    //alert(wordCount);
    wordCount *= .75;
    var searchPhrase = jQuery('#orangeSoda_search_phrase').val();
    searchPhrase = searchPhrase.replace(/ +(?= )/g,' ');
    jQuery('.editor_text').removeHighlight().highlight(searchPhrase);
    var searchNumber = jQuery('.orangeSoda_highlight').length;
    var message2 = "<table class='orangesoda_word_table' style='border:none; border-style:none; width:100%'><tbody><tr><th>Count</th><th>Density</th><th>Word</th></tr><tr><td>" + searchNumber + "</td><td>" + parseFloat((searchNumber / wordCount) * 100).toFixed(2) + "%</td><td>" + searchPhrase + "</td></tbody></table>";
    jQuery('#orange_soda_search_density').html(message2);
    jQuery('.editor_text').removeHighlight();
}

var defaultValue = jQuery('#orangeSoda_search_phrase').val();
jQuery('#orangeSoda_search_phrase').click(function() {
    if (this.value == defaultValue) {
        jQuery(this).val("");
        jQuery(this).css('color', 'black');
    }
});
jQuery('#orangeSoda_search_phrase').focusout(function() {
    if (this.value == "") {
        jQuery(this).val("Keyword");
        jQuery(this).css('color', 'gray');
    }
});


function nthMostCommon(string, ammount) {
    var notC = {
        'about': true,
        'after': true,
        'ago': true,
        'all': true,
        'also': true,
        'an': true,
        'and': true,
        'any': true,
        'are': true,
        'as': true,
        'at': true,
        'be': true,
        'been': true,
        'before': true,
        'both': true,
        'but': true,
        'by': true,
        'can': true,
        'did': true,
        'do': true,
        'does': true,
        'done': true,
        'edit': true,
        'even': true,
        'every': true,
        'for': true,
        'from': true,
        'had': true,
        'has': true,
        'have': true,
        'he': true,
        'here': true,
        'him': true,
        'his': true,
        'however': true,
        'if': true,
        'in': true,
        'into': true,
        'is': true,
        'it': true,
        'its': true,
        'less': true,
        'many': true,
        'may': true,
        'more': true,
        'most': true,
        'much': true,
        'my': true,
        'no': true,
        'not': true,
        'often': true,
        'quote': true,
        'of': true,
        'on': true,
        'one': true,
        'only': true,
        'or': true,
        'other': true,
        'our': true,
        'out': true,
        're': true,
        'says': true,
        'she': true,
        'so': true,
        'some': true,
        'soon': true,
        'such': true,
        'than': true,
        'that': true,
        'the': true,
        'their': true,
        'them': true,
        'then': true,
        'there': true,
        'these': true,
        'they': true,
        'this': true,
        'those': true,
        'though': true,
        'through': true,
        'to': true,
        'under': true,
        'use': true,
        'using': true,
        've': true,
        'was': true,
        'we': true,
        'were': true,
        'what': true,
        'where': true,
        'when': true,
        'whether': true,
        'which': true,
        'while': true,
        'who': true,
        'whom': true,
        'with': true,
        'within': true,
        'you': true,
        'your': true,
        'http': true,
        'www': true,
        'wp': true,
        'href': true,
        'target': true,
        'blank': true,
        'image': true,
        'class': true,
        'size': true,
        'src': true,
        'img': true,
        'alignleft': true,
        'title': true,
        'info': true,
        'content': true,
        'uploads': true,
        'jpg': true,
        'alt': true,
        'h3': true,
        'width': true,
        'height': true,
        '150': true,
        '2010': true,
        '2009': true,
        '10': true,
        '1': true,
        '2': true,
        '3': true,
        '4': true,
        '5': true,
        '6': true,
        '7': true,
        '8': true,
        '9': true,
        '11': true,
        'com': true,
        'net': true,
        'info':true,
        'map': true,
        '150x150': true,
        'thumbnail': true,
        'param': true,
        'name': true,
        'value': true,
        'will': true,
        'am': true,
        '202': true,
        'retouch': true
    };
    string = string.replace(/<(.|\n)*?>/g, '');
    string = string.toLowerCase();
    var wordsArray = string.split(/[\s\.\,\?]+/);
    //var wordsArray = string.split(/\s/);
    var wordOccurrences = {}
    for (var i = 0; i < wordsArray.length; i++) {
        wordOccurrences['_' + wordsArray[i]] = (wordOccurrences['_' + wordsArray[i]] || 0) + 1;
    }
    var result = Object.keys(wordOccurrences).reduce(function(acc, currentKey) {
        /* you may want to include a binary search here */
        for (var i = 0; i < ammount; i++) {
            var word = currentKey.slice(1, currentKey.length);
            if (word.length > 3 && !notC[word]) {
                if (!acc[i]) {
                    jQuery('.editor_text').removeHighlight().highlight(currentKey.slice(1, currentKey.length));
                    var searchNumber = jQuery('.orangeSoda_highlight').length;
                    acc[i] = {word: currentKey.slice(1, currentKey.length), occurences: searchNumber};
                    break;
                } else if (acc[i].occurences < wordOccurrences[currentKey]) {
                    jQuery('.editor_text').removeHighlight().highlight(currentKey.slice(1, currentKey.length));
                    var searchNumber = jQuery('.orangeSoda_highlight').length;
                    acc.splice(i, 0, {word: currentKey.slice(1, currentKey.length), occurences: searchNumber});
                    if (acc.length > ammount)
                        acc.pop();
                    break;
                }
            }
        }
        return acc;
    }, []);
    return result;
}
function sortProperties(obj)
{
    // convert object into array
    var sortable = [];
    for (var key in obj)
        if (obj.hasOwnProperty(key))
            sortable.push([key, obj[key]]); // each item is an array in format [key, value]

    // sort items by value
    sortable.sort(function(a, b)
    {
        var x = a[1].occurences,
        y = b[1].occurences;
        // alert(a);
        //console.log(a[1].occurences);
        return x < y ? 1 : x > y ? -1 : 0;
    });
    return sortable; // array in format [ [ key1, val1 ], [ key2, val2 ], ... ]
}