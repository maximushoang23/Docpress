jQuery(document).ready(function ()
{
    var html = jQuery('.single-post .doc-sec-container').html()
    var doc_search = jQuery('body .documentor-wrap .dcumentor-topicons').html();
    jQuery('body .documentor-wrap .dcumentor-topicons').remove();
    jQuery('.single-post .doc-sec-container').html('')
    jQuery('.single-post .doc-sec-container').prepend('<div class="single-doc-content">')
    jQuery('.single-doc-content').prepend(html)
    jQuery('.single-doc-content').prepend('<div class="dcumentor-topicons doc-noprint">');
    jQuery('.single-doc-content .dcumentor-topicons').prepend(doc_search);


    jQuery('.navbar-toggle').click(function (){
        jQuery('.single-main-content').toggleClass('screen-full-content');
    })

    var height_menu = jQuery('header#masthead').outerHeight();
    var height_adminbar = jQuery('#wpadminbar').outerHeight();
    var height_bottom = jQuery('#colophon').outerHeight();
    jQuery('body .documentor-wrap .doc-menu').css({'top': height_menu + height_adminbar, 'bottom' : height_bottom });
    jQuery('#documentor_seccontainer').css('min-height', '100vh');
    jQuery(window).scroll(function(){
        var st = jQuery(this).scrollTop();
        if (st > height_menu){
                jQuery('body .documentor-wrap .doc-menu').css({'top': '0', 'bottom' : height_bottom});
        } else {
            jQuery('body .documentor-wrap .doc-menu').css({'top': height_menu + height_adminbar, 'bottom' : height_bottom });
        }
    });

})