jQuery(document).ready(function ($) {

    var html = $('.single-post .doc-sec-container').html()
    var doc_search = $('body .documentor-wrap .dcumentor-topicons').html();
    $('body .documentor-wrap .dcumentor-topicons').remove();
    $('.single-post .doc-sec-container').html('')
    $('.single-post .doc-sec-container').prepend('<div class="single-doc-content">')
    $('.single-doc-content').prepend(html)
    $('.single-doc-content').prepend('<div class="dcumentor-topicons doc-noprint">');
    $('.single-doc-content .dcumentor-topicons').prepend(doc_search);

    $('.navbar-toggle').click(function () {
        $('.single-main-content').toggleClass('screen-full-content');
    })
    var height_menu = $('header#masthead').outerHeight();
    var height_adminbar = $('#wpadminbar').outerHeight();
    $('#documentor_seccontainer').css('min-height', '100vh');

    if (height_adminbar) {
        $('body .documentor-wrap .doc-menu').css({'top': height_menu + height_adminbar, 'bottom': 0});
        $('#masthead').css('top', height_adminbar);
        $(window).scroll(function () {
            var st = $(this).scrollTop();
            if (screen.width < 600) {
                if (st > height_adminbar) {
                    $('body .documentor-wrap .doc-menu').css({'top': height_menu, 'bottom': 0});
                    $('#masthead').css('top', 0)
                } else {
                    $('body .documentor-wrap .doc-menu').css({
                        'top': height_menu + height_adminbar,
                        'bottom': 0
                    });
                    $('#masthead').css('top', height_adminbar)
                }
            }
        });
    } else {
        $('body .documentor-wrap .doc-menu').css({'top': height_menu, 'bottom': 0});
        $('#masthead').css('top', 0);
    }

    $('body').on('click', '.doc-actli', function () {
        $('ol.doc-list-front>li').removeClass('parent-current')
        $('.doc-acta').closest('ol.doc-list-front>li').addClass('parent-current');
    });

    $('ol.doc-list-front li').click(function (){
        if($(this).find('>ol').css('display') == 'none') {
            $(this).find('>ol').css('display', 'block')
        }
        if($(this).children('ol').length > 0){

        }
    })
});