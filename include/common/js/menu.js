/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function navPop() {
    $("#navbarSupportedContent").toggleClass("openNav");
    $("body").toggleClass("body-openNav");
    $(".navbar-toggler").toggleClass("taggOpen");
    $(".has_sub_trgg").unbind('click');
    $(".has_sub_trgg").click(function() {

        if ($(this).parent().find(' > .sub-menu').is(":visible")) {
            $(this).css('transform', 'rotate(0deg)').removeClass('cls');
        } else {
            $(this).css('transform', 'rotate(180deg)').addClass('cls');
        }
        $(this).parent().find(' > .sub-menu').toggleClass('open');
        if ($(window).width() < 768) {
            $(this).closest('ul').find('> li').toggleClass('nonopen');
            $(this).parent().toggleClass('nonopen');
        }
    });
}

$(document).ready(function() {
    //$(".nav-item.active").parent().parent().addClass('active-parent');

    var wrap = $(".container");
    var wrapW = wrap.width();
    var pos = wrap.offset();

    $("li.mega-menu").hover(function() {
        var mtopPos = $(this).offset();
        var excLeft = (mtopPos.left - pos.left);
        //console.log(this);
        $(this).find('> .sub-menu').width((wrapW - 2));
        $(this).find('> .sub-menu').css({"transform": "translateX(-" + (excLeft - 15) + "px)"});
    })

//    $('.mega-menu > .sub-menu').width((wrapW));
//    
//    console.log(mtopPos);
//    var excLeft = (mtopPos.left - pos.left);
//    $('.mega-menu > .sub-menu').css({"transform": "translateX(-" + excLeft + "px)"});


});