//scrollTo function
jQuery.docuScrollTo = jQuery.fn.docuScrollTo = function(x, y, options){
    if (!(this instanceof jQuery)) return jQuery.fn.docuScrollTo.apply(jQuery('html,body'), arguments);

    options = jQuery.extend({}, {
        gap: {
            x: 0,
            y: 0
        },
        animation: {
            easing: 'swing',
            duration: 1000,
            complete: jQuery.noop,
            step: jQuery.noop
        }
    }, options);

    return this.each(function(){
        var elem = jQuery(this);
		var menuTop = !isNaN(Number(options.menuTop)) ? ( Number(options.menuTop) + 12 ) : 12;
	elem.stop().animate({
            scrollLeft: !isNaN(Number(x)) ? x : jQuery(y).offset().left + options.gap.x,
            scrollTop: (!isNaN(Number(y)) ? y : jQuery(y).offset().top + options.gap.y) - menuTop 
	}, options.animation);
    });
};
//function to get current url parameter
function getUrlParameter(sParam)
{
    var sPageURL = window.location.search.substring(1);
    sPageURL = decodeURI(sPageURL);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) 
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam) 
        {
            return sParameterName[1];
        }
    }
} 
//social share functions to get share count
// Facebook Shares Count
function docfacebookShares($URL) {
	if ( jQuery('#doc_fb_share').hasClass('doc-fb-share') ) {
		jQuery.getJSON('https://graph.facebook.com/?id=' + $URL, function (fbdata) {
			jQuery('#doc-fb-count').text( ReplaceNumberWithCommas(fbdata.shares || 0) );
		});
	} 
}
// Twitter Shares Count
function doctwitterShares($URL) {
	if ( jQuery('#doc_twitter_share').hasClass('doc-twitter-share') ) {
		jQuery.getJSON('https://cdn.api.twitter.com/1/urls/count.json?url=' + $URL + '&callback=?', function (twitdata) {
			jQuery('#doc-twitter-count').text( ReplaceNumberWithCommas(twitdata.count) );
		});
	} 
}
// Pinterest Shares Count
function docpinterestShares($URL) {
	if ( jQuery('#doc_pin_share').hasClass('doc-pin-share') ) {
		jQuery.getJSON('https://api.pinterest.com/v1/urls/count.json?url=' + $URL + '&callback=?', function (pindata) {
			jQuery('#doc-pin-count').text( ReplaceNumberWithCommas(pindata.count) );
		});
	} 
}
function ReplaceNumberWithCommas(shareNumber) {
	 if (shareNumber >= 1000000000) {
		return (shareNumber / 1000000000).toFixed(1).replace(/\.0$/, '') + 'G';
	 }
	 if (shareNumber >= 1000000) {
		return (shareNumber / 1000000).toFixed(1).replace(/\.0$/, '') + 'M';
	 }
	 if (shareNumber >= 1000) {
		return (shareNumber / 1000).toFixed(1).replace(/\.0$/, '') + 'K';
	 }
	 return shareNumber;
}
;(function($){
	jQuery.fn.documentor=function(args){
		var defaults= {
			documentid	: '1',
			docid		: '1',
			animation	: '',
			indexformat	: '1',
			pformat         : 'decimal',
			cformat		: 'decimal',			
			secstyle	: '',
			actnavbg_default: '0',
			actnavbg_color	: '#f3b869',
			enable_ajax	: '0',
			scrolling	: "1",
			skin		: "default",
			scrollBarSize	: "3",
			scrollBarColor	: "#F45349",
			scrollBarOpacity: "0.4",
			windowprint	: '0',
			menuTop: '0'
		}		
		var options=jQuery.extend({},defaults,args);		
		var documentHandle = options.docid;
		if(options.animation.length > 0 ) {
			wow = new WOW({
				boxClass:     "wow",      
				animateClass: "documentor-animated", 
				offset:       0,          
				mobile:       true,       
				live:         true        
			});
			wow.init();
		}
		if(options.enable_ajax == '1'){
			options.pformat=options.cformat='decimal';
		}
		if(options.indexformat == '1') {
			var countercss = '';			
			countercss = "#"+documentHandle+" .doc-menu ol.doc-list-front > li:before {content: counter(item,"+options.pformat+") \".\";counter-increment: item; "+options.secstyle+";}.doc-menu ol ol li:before {content: counter(item,"+options.pformat+")\".\"counters(childitem, \".\", "+options.cformat+") \".\";counter-increment: childitem;"+options.secstyle;
			if( options.skin == 'bar' ) {				
				countercss = "#"+documentHandle+" .doc-menu ol.doc-list-front > li:before {content: counter(item,"+options.pformat+") \".\";counter-increment: item;"+options.secstyle+";}.doc-menu ol ol li:before {content: counter(item,"+options.pformat+")\".\"counters(childitem, \".\", "+options.cformat+") \".\";counter-increment: childitem;"+options.secstyle;
				
			}
			if( options.skin == 'broad') {
				countercss = "#"+documentHandle+" .doc-menu ol.doc-list-front > li:before {content: counter(item,"+options.pformat+") \".\";counter-increment: item;"+options.secstyle;
			}
			jQuery("head").append("<style type=\"text/css\"> #"+documentHandle+" .doc-menu ol.doc-list-front {counter-reset: item ;}.doc-menu ol ol {counter-reset: childitem;}#"+documentHandle+" ol.doc-menu {margin-top: 20px;}#"+documentHandle+" .doc-menu ol li {display: block;}"+countercss+"}</style>");
		} else {
			jQuery("head").append("<style type=\"text/css\">#"+documentHandle+" .doc-menu ol {list-style: none;}#"+documentHandle+" .doc-menu li {list-style: none;}</style>");
		}
		if(options.actnavbg_default != '1' && options.actnavbg_color.length > 0 && options.skin != 'broad') {
			jQuery("head").append("<style type=\"text/css\">#"+documentHandle+" .doc-menu ol > li.doc-acta{background-color: "+options.actnavbg_color+"}</style>");
		}
		if(options.actnavbg_default != '1' && options.actnavbg_color.length > 0 && options.skin == 'broad') {
			jQuery("head").append("<style type=\"text/css\">#"+documentHandle+" .doc-menu li.activeli > a, #"+documentHandle+" .doc-menu .documentor-relatedtitle{background-color: "+options.actnavbg_color+"; color: #fff !important;}</style>");
		}
		if( options.enable_ajax == 0 && options.fixmenu == 1 ) {
			var docEnd = jQuery("#"+documentHandle+"-end").position(); //cache the position
			jQuery.lockfixed("#"+documentHandle+" .doc-menu",{offset: {top: options.menuTop, bottom: (document.body.clientHeight - docEnd.top)}});
		}
		if( options.skin == 'broad' ) {
			jQuery("#"+documentHandle+" ol.doc-list-front li:first").addClass('activeli');
		}
		//js
		/* if in url section is present - start */
		if( options.enable_ajax == '1' ) {
			if( typeof getUrlParameter('section') === "undefined" ) {
				jQuery(this).find(".doc-menu a.documentor-menu:first, .doc-menu li.doc-actli:first").addClass('doc-acta');
			} else {
				var secid = getUrlParameter('section');
				if( jQuery(this).find(".doc-menu a[data-href='#"+secid+"']").length > 0 ) {
					jQuery(this).find(".doc-menu a[data-href='#"+secid+"']").addClass('doc-acta');
					var seccnt = jQuery(this).find(".doc-menu a[data-href='#"+secid+"']").data('sec-counter');
					jQuery("#"+secid).find(".doc-sec-count").html(seccnt+'.');
				} else {
					jQuery(this).find(".doc-menu a.documentor-menu:first, .doc-menu li.doc-actli:first").addClass('doc-acta');
				}
			}	
		} else {
			jQuery(this).find(".doc-menu a.documentor-menu:first, .doc-menu li.doc-actli:first").addClass('doc-acta');
		}
		//bar skin specific
		if( options.skin == 'bar' ) {
			jQuery("#"+documentHandle+" .doc-menu li > ol:not(:has(.doc-acta))").css('display', 'none');
			jQuery("#"+documentHandle+" .doc-menu li > ol:has('.doc-acta')").css('display', 'block');
			jQuery("#"+documentHandle+" .doc-menu li > ol:has('.doc-acta')").parents('.doc-actli').last().find('a.documentor-menu:first').addClass('doc-activea');
		}
		/* if in url section is present - end */
			
		/* Call bindbehaviours on load */
		var cnxt=jQuery(this);
		bindsectionBehaviour(cnxt, options);
		
		/* Search in document */
		jQuery("#"+documentHandle+" .search-document").autocomplete({
			source: function(req, response){
				jQuery("#"+documentHandle+" .doc-search").addClass('search-loading');
				req['docid'] = options.documentid;
				jQuery.getJSON(DocAjax.docajaxurl+'?callback=?&action=doc_search_results', req, response);
			},
			select: function(event, ui) {
				var thref = ui.item.slug;
				if( thref ) {
					jQuery("#"+documentHandle+" a[data-href='#"+thref+"']")[0].click();
				}

			},
			delay: 200,
			minLength: 3,
			response: function (event, ui) {
				jQuery("#"+documentHandle+" .doc-search").removeClass('search-loading');
			}
		}).autocomplete( "widget" ).addClass( "doc-sautocomplete" );
		
		/**
		 * This part causes smooth scrolling using scrollto function
		*/
		if( jQuery("#"+documentHandle+" .doc-firstnext").length > 0 ) {
			var activea = jQuery("#"+documentHandle+" .doc-acta");
			var nextsecid = activea.nextAll().find('a:first').data("href");
			var nextsecname = 'Next';
			nextsecname = activea.nextAll().find('a:first').html();
			if( typeof nextsecid === 'undefined' ) {
				nextsecid = activea.parents('.doc-actli:last').nextAll().find('a:first').data("href");
				nextsecname = activea.parents('.doc-actli:last').nextAll().find('a:first').html();
			}
			if( typeof nextsecid === 'undefined' ) {
				nextsecid = '0';
			}
			jQuery("#"+documentHandle+" .doc-firstnext").attr('data-href',nextsecid);
			jQuery("#"+documentHandle+" .doc-firstnext").html(nextsecname+' &raquo;');
		}
		jQuery(this).find(".doc-menu a").not('.documentor-menu').click(function(evn) {
			evn.preventDefault();
			if( jQuery(this).attr('target') == "_blank" ) {
				window.open(jQuery(this).attr('href'), '_blank');
			} else {
				window.location = jQuery(this).attr('href');
			}
		});
		jQuery(this).find(".doc-menu a.documentor-menu").click(function(evn){
			if( typeof documentorPreview === 'undefined' && options.scrolling == 1 ){
				evn.preventDefault();
			}
			jQuery(this).parents('.doc-menu:first').find('a.documentor-menu, li.doc-actli').removeClass('doc-acta');
			jQuery(this).addClass('doc-acta');
			jQuery(this).parents('li.doc-actli:first').addClass('doc-acta');
			//for broad skin
			if( options.skin == 'broad' ) {
				if( options.togglechild == 1 ) {
					jQuery('.doc-menu li ol:not(:has(.doc-acta))').hide();
					jQuery(this).parents('.doc-actli:last').find('ol').show();
				}
				jQuery("#"+documentHandle+" .doc-menu li").removeClass('activeli');
				jQuery( "#"+documentHandle+" a.doc-acta" ).parents("li:last").addClass('activeli');
				var mwrapcnt = jQuery( this ).data('mwrapcnt');
				if( typeof mwrapcnt === 'undefined' ) {
					mwrapcnt = jQuery(this).parents("li.doc-actli:last").find("a").data('mwrapcnt');
				}
				if( typeof mwrapcnt !== 'undefined' ) {
					jQuery("#"+documentHandle+" .doc-sectionwrap").hide();
					jQuery("#"+documentHandle+" .doc-sectionwrap[data-wrapcnt="+mwrapcnt+"]").fadeIn( 400 );
				}
			//	if( options.enable_ajax == '1' ) { //menu highlighlighting issue resolved due to commenting this code 1.4
					var visiblemheight = jQuery("#"+documentHandle+" .doc-menu ol.doc-list-front").height();
					if( jQuery("#"+documentHandle+" .documentor-related").length > 0 ) {
						visiblemheight = visiblemheight + jQuery("#"+documentHandle+" .documentor-related").height()+40;
					}
					jQuery("#"+documentHandle+" .doc-sec-container").css('min-height',visiblemheight+'px');
			//	}
			}
			/* If bar skin then display child menus on click of parent menu */
			if( options.skin == 'bar' ) {
				jQuery("#"+documentHandle+" .doc-menu li > ol:not(:has(.doc-acta))").css('display', 'none');
				jQuery("#"+documentHandle+" .doc-menu li > ol:has('.doc-acta')").css('display', 'block');
				if( jQuery(this).parent('.doc-actli').find('ol').length > 0 ) {
					jQuery(this).parent('.doc-actli').find('ol').show();
				}
				jQuery("#"+documentHandle).find('.doc-actli a.documentor-menu').removeClass('doc-activea');
				jQuery(this).parents('.doc-actli').last().find('a.documentor-menu:first').addClass('doc-activea');
			}
			/* Do not apply animation effect if click on menu item */
			jQuery("#"+documentHandle).find(".documentor-section").css({"visibility":"visible","-webkit-animation":"none"});
			/**/
			if( options.enable_ajax == '1' ) {
				var secid = jQuery( this ).attr("data-section-id");
				var currSec = jQuery( this ).parent( '.doc-actli' );
				var currSecIdx = jQuery("#"+documentHandle+" .doc-menurelated .doc-actli").index( currSec );
				var totalSecCount = jQuery("#"+documentHandle+" .doc-menurelated .doc-actli").length;
				var nextsecid=currSecIdx+1;
				var nextsechref,nextsecname;
				if(nextsecid==totalSecCount)nextsechref=0;
				else {
					nextsechref = jQuery("#"+documentHandle+" .doc-menurelated .doc-actli").eq(parseInt(nextsecid)).find( 'a' ).data( 'href' );
					nextsecname = jQuery("#"+documentHandle+" .doc-menurelated .doc-actli").eq(parseInt(nextsecid)).find( 'a' ).html();
				}
				var prevsecid=currSecIdx-1;
				var prevsechref, prevsecname;
				if(prevsecid<0)prevsechref=0;
				else {
					prevsechref = jQuery("#"+documentHandle+" .doc-menurelated .doc-actli").eq(parseInt(prevsecid)).find( 'a' ).data( 'href' );
					prevsecname = jQuery("#"+documentHandle+" .doc-menurelated .doc-actli").eq(parseInt(prevsecid)).find( 'a' ).html();
				}
				var docid = jQuery( this ).parents(".documentor-wrap:first").data('docid');
				var sec_cnt = jQuery( this ).attr("data-sec-counter");
				var currenturl = encodeURI(window.location.href);
				var data = {
					'action': 'doc_get_ajaxcontent',
					'secid': secid,
					'docid': docid,
					'sec_cnt': sec_cnt,
					'currenturl': currenturl,
					'nextsecid' : nextsechref,
					'prevsecid' : prevsechref,
					'nextsecname' : nextsecname,
					'prevsecname' : prevsecname
				};
				//display loader
				jQuery("#"+documentHandle).find(".doc-sec-container").empty();
				jQuery("#"+documentHandle).find(".doc-sec-container").append('<div class="doc-loader"></div>');
				jQuery.post(DocAjax.docajaxurl, data, function(response) {
					if( response != "0" ) {
						jQuery("#"+documentHandle).find('.doc-sec-container').html(response);
						if( options.indexformat == 1 ) {
							jQuery("#"+documentHandle).find('.section-'+data['secid']).find('.doc-sec-count').html(data['sec_cnt']+'.');
						}
						if( options.scrolling == 1 ) {
							var targetid = jQuery("#"+documentHandle).find('.section-'+data['secid']).attr('id');
							targetid = '#'+targetid; 
							var dstopts = {
								'menuTop': options.menuTop
							};
							//Add link to address bar
							window.history.pushState( null , null , targetid);
							jQuery('html,body').docuScrollTo( targetid, targetid, dstopts ); 
						}
						/* call social share count functions */
						if( options.socialshare == 1 && options.sharecount == 1 ) {
							var sharelink = jQuery("#"+documentHandle+" .doc-sharelink").data('sharelink');
							if( sharelink != '' ) {
								sharelink = decodeURIComponent(sharelink);
								if( options.fbshare == 1 ) {
									docfacebookShares( sharelink );
								}
								if( options.twittershare == 1 ) {
									doctwitterShares( sharelink );
								}
								if( options.gplusshare == 1 ) {
									if ( jQuery('#doc_gplus_share').hasClass('doc-gplus-share') ) {
										// Google Plus Shares Count
										var googleplusShares = jQuery('#doc-gplus-count').data('gpluscnt');
										jQuery('#doc-gplus-count').text( ReplaceNumberWithCommas(googleplusShares) )
									}
								}
								if( options.pinshare == 1 ) {
									docpinterestShares( sharelink );
								}
							}
						}
					}
				}).always( function() { 
					var cnxt=jQuery("#"+documentHandle).find('.section-'+data['secid']);
				   	bindsectionBehaviour(cnxt, options);
				 });
				 return false;
			 }
			if( typeof documentorPreview === 'undefined' && jQuery(this.hash).length > 0 && options.scrolling == 1 ) {
				var dstopts = {
					'menuTop': options.menuTop
				};

				//Add link to address bar
				window.history.pushState( null , null , this.hash);
			 	jQuery('html,body').docuScrollTo( this.hash, this.hash, dstopts ); 
			}
		
		});
			
		/* For broad skin - if link with hash value of section is opened in window */
		if( location.hash != "" && options.skin == 'broad' ) {
			var hashval = location.hash;
			jQuery("a.documentor-menu[data-href='"+hashval+"']").trigger("click");
		}     
			           
		/**
		 * This part handles the highlighting functionality.
		 */
		var aChildren = jQuery(this).find(".doc-menu li.doc-actli").children('a.documentor-menu'); // find the a children of the list items
		var aArray = []; // create the empty aArray
		for (var i=0; i < aChildren.length; i++) {    
			var aChild = aChildren[i];
			var ahref = jQuery(aChild).data('href');
			aArray.push(ahref);
		} // this for loop fills the aArray with attribute href values
		if( options.enable_ajax != '1' ) {
			jQuery(window).scroll(function(){
				var window_top = jQuery(window).scrollTop() + 12; // the "12" should equal the margin-top value for nav.stick
				var windowPos = jQuery(window).scrollTop(); // get the offset of the window from the top of page
				var windowHeight = jQuery(window).height(); // get the height of the window
				var docHeight = jQuery(document).height();
		
				if(windowPos + windowHeight == docHeight) {
					if (!jQuery("#"+documentHandle+" .doc-menu li:last-child a").hasClass("doc-acta")) {
					    var navActiveCurrent = jQuery("#"+documentHandle+" .doc-acta").data("href");
						//Add link to address bar
						//console.log(navActiveCurrent);
						window.history.pushState( null , null , navActiveCurrent);
					    jQuery("#"+documentHandle+" a[data-href='" + navActiveCurrent + "']").removeClass("doc-acta");
					    jQuery("#"+documentHandle+" .doc-menu li:last-child a").addClass("doc-acta");
					}
				}
				
				clearTimeout(jQuery.data(this, 'scrollTimer'));
				jQuery.data(this, 'scrollTimer', setTimeout(function() {
					// do something
					for (var i=0; i < aArray.length; i++) {
						if( jQuery(aArray[i]).length > 0 ) {
							var theID = aArray[i];
							var divPos = jQuery(theID).offset().top - (windowHeight*0.20); // get the offset of the div from the top of page
							var divHeight = jQuery(theID).outerHeight(true); // get the height of the div in question
							if (windowPos >= divPos && windowPos < (divPos + divHeight)) {
								var temp=jQuery("#"+documentHandle+" a[data-href='" + theID + "']").addClass("doc-acta");
								//console.log(theID);
								window.history.pushState( null , null , theID);
								/* If bar skin then display child menus on scroll to parent menu */
								if( options.skin == 'bar' ) {
									jQuery("#"+documentHandle+" .doc-menu li > ol:not(:has(.doc-acta))").css('display', 'none');
									jQuery("#"+documentHandle+" .doc-menu li > ol:has('.doc-acta')").css('display', 'block');
									if(jQuery("#"+documentHandle+" a[data-href='" + theID + "']").parent('.doc-actli').find('ol').length > 0 ) {
										jQuery("#"+documentHandle+" a[data-href='" + theID + "']").parent('.doc-actli').find('ol').show();
										
									}
									jQuery("#"+documentHandle).find('.doc-actli a.documentor-menu').removeClass('doc-activea');
									jQuery("#"+documentHandle+" a[data-href='" + theID + "']").parents('.doc-actli').last().find('a.documentor-menu:first').addClass('doc-activea');
								}
							} else {
								jQuery("#"+documentHandle+" a[data-href='" + theID + "']").removeClass("doc-acta");
							}
						}
					}
					//commented one line v1.1
					/*if(jQuery("#"+documentHandle+" a.doc-acta").length<=0) {
						jQuery("#"+documentHandle+" .doc-menu a.documentor-menu:first").addClass("doc-acta");
					}*/
					jQuery("#"+documentHandle+" .doc-menu a.documentor-menu.doc-acta").parent('li').addClass("doc-acta");
					jQuery("#"+documentHandle+" .doc-menu a:not(.doc-acta)").parent('li').removeClass("doc-acta");
					}, 200));
					//right positioned menu
					jQuery(window).scroll(function(){
						if( jQuery("#"+documentHandle+" .doc-menuright.doc-menufixed").length > 0 ) {
							var mleft = jQuery("#"+documentHandle).outerWidth()-jQuery("#"+documentHandle+" .doc-menuright.doc-menufixed").outerWidth();
							jQuery("#"+documentHandle+" .doc-menuright.doc-menufixed").css('margin-left',mleft+'px');
						} else {
							jQuery("#"+documentHandle+" .doc-menuright").css('margin-left','0px');
						}
					});
			});
		}
		/* Expand / collapse menus */
		jQuery("#"+documentHandle+" .doc-menu.toggle .doc-mtoggle").on('click', function() {
			jQuery(this).toggleClass('expand');
			jQuery(this).parent('.doc-actli').find('ol:first').slideToggle('slow');
		});
		//scroll bar js
		 jQuery("#"+documentHandle+" .doc-menurelated").slimScroll({
			  size: options.scrollBarSize+'px', 
			  height: '100%', 
			  color: options.scrollBarColor, 
			  opacity: options.scrollBarOpacity,
		});
		/*scrolltop*/
		jQuery(".scrollup").on('click', function() {
			var doctop = jQuery("#"+documentHandle).offset().top-50;
			jQuery("html, body").animate({scrollTop:doctop}, 600);
		});
		/*show scrolltop button*/
		jQuery("body").hover(function(){
			jQuery("#"+documentHandle+" .scrollup").stop(true,true).animate({'opacity':'0.8'},1000);
		},function() {
			jQuery("#"+documentHandle+" .scrollup").stop(true,true).animate({'opacity':'0'},1000);
		});	
		//print document
		jQuery("#"+documentHandle+" .dcumentor-topicons .doc-print").on('click', function(e) {
			if( options.windowprint == '1' ) {
				e.preventDefault();
				var printCSS=jQuery('<link rel="stylesheet" href="'+jQuery(this).data('printspath')+'" media="print" />').prependTo("head");
				printCSS.on('load', function(){
					jQuery("#"+documentHandle+" .documentor-section").each(function(i, elm){
						var st = jQuery(this).attr("style");	
						if( st !== undefined ) {					
							st=st.replace("hidden","visible");
							jQuery(this).attr("style", st);
						}
					});
					window.print();
					printCSS.remove();
				});
				return false;
			} else {
				jQuery("#"+documentHandle).find('iframe[src*="youtube.com"]').each(function() {
					var url = jQuery(this).attr("src");
					if( jQuery(this).parent().find('.doc-isrc').length <= 0 ) {
						jQuery(this).after( '<div class="doc-isrc">'+url+'</div>' );
					}
				}); 
				jQuery("#"+documentHandle).find('iframe[src*="youtube.com"]').addClass('doc-noprint');
				jQuery("#"+documentHandle).find('iframe[src*="vimeo.com"]').each(function() {
					var url = jQuery(this).attr("src");
					if( jQuery(this).parent().find('.doc-isrc').length <= 0 ) {
						jQuery(this).after( '<div class="doc-isrc">'+url+'</div>' );
					}
				});
				jQuery("#"+documentHandle).find('iframe[src*="vimeo.com"]').addClass('doc-noprint'); 
				jQuery("#"+documentHandle).find("object, embed, video, .wp-video").addClass('doc-noprint');
				jQuery("#"+documentHandle+" .documentor-section").each(function(i, elm){
					var st = jQuery(this).attr("style");
					if( st !== undefined ) {
						st=st.replace("hidden","visible");
						jQuery(this).attr("style", st);
					}
				});
				jQuery("#"+documentHandle).print({
					noPrintSelector : ".doc-noprint",
					wrapClass : ""
				});
			}
		});
		/* Call social sharing count functtions on load */
		if( options.socialshare == 1 && options.sharecount == 1 ) {
			var sharelink = jQuery("#"+documentHandle+" .doc-sharelink").data('sharelink');
			if( sharelink != '' ) {
				if( options.fbshare == 1 ) {
					docfacebookShares( sharelink );
				}
				if( options.twittershare == 1 ) {
					doctwitterShares( sharelink );
				}
				if( options.gplusshare == 1 ) {
					if ( jQuery('#doc_gplus_share').hasClass('doc-gplus-share') ) {
						// Google Plus Shares Count
						var googleplusShares = jQuery('#doc-gplus-count').data('gpluscnt');
						jQuery('#doc-gplus-count').text( ReplaceNumberWithCommas(googleplusShares) )
					}
				}
				if( options.pinshare == 1 ) {
					docpinterestShares( sharelink );
				}
			}
		}
	}
	/*bind behaviours at front end*/
	var bindsectionBehaviour = function(scope, options) {
		var documentHandle = options.docid;
		//apply leanmodal popup
		jQuery(".documentor-wrap").find("a[rel*=leanModal]").leanModal({ top : 200, overlay : 0.4, closeButton: ".modal_close" });
		if( options.iconscroll == 1 ) {
			jQuery(".documentor-section").hover(function(){
				jQuery(this).find(".documentor-social").stop(true, true).slideDown("slow");
			},function() {});  
		} else {
			jQuery("#"+documentHandle).find(".documentor-social").css('display','block');	
		}
		//print document section
		jQuery(".documentor-social .doc-print", scope).on('click', function(e) {
			if( options.windowprint == '1' ) {
				e.preventDefault();
				var printCSS=jQuery('<link rel="stylesheet" href="'+jQuery(this).data('printspath')+'" media="print" />').prependTo("head");
				printCSS.on('load', function(){
					window.print();
					printCSS.remove();
				});
				return false;
			} else {
				var docsection = jQuery(this).parents('.documentor-section:first');
				docsection.find('iframe[src*="youtube.com"]').each(function() {
					var url = jQuery(this).attr("src");
					if( jQuery(this).parent().find('.doc-isrc').length <= 0 ) {
						jQuery(this).after( '<div class="doc-isrc">'+url+'</div>' );
					}
				}); 
				docsection.find('iframe[src*="youtube.com"]').addClass('doc-noprint');
				docsection.find('iframe[src*="vimeo.com"]').each(function() {
					var url = jQuery(this).attr("src");
					if( jQuery(this).parent().find('.doc-isrc').length <= 0 ) {
						jQuery(this).after( '<div class="doc-isrc">'+url+'</div>' );
					}
				});
				docsection.find('iframe[src*="vimeo.com"]').addClass('doc-noprint'); 
				docsection.find("object, embed, video, .wp-video").addClass('doc-noprint');
				docsection.print({
					noPrintSelector : ".doc-noprint",
					wrapClass	: "documentor-"+options.skin
				});
			}
		});
		/*next section*/
		jQuery('.doc-next, .doc-firstnext', scope).on('click', function(e) {
			var ahref = jQuery(this).data('href');
			jQuery(this).parents('.documentor-wrap:first').find('.doc-menu a[data-href="'+ahref+'"]').trigger('click');
		});
		/*previous section*/
		jQuery('.doc-prev', scope).on('click', function(e) {
			var ahref = jQuery(this).data('href');
			jQuery(this).parents('.documentor-wrap:first').find('.doc-menu a[data-href="'+ahref+'"]').trigger('click');
		});
		/*positive feedback*/
		jQuery( ".positive-feedback", scope ).click(function(e) {
			e.preventDefault();
			var secid = jQuery( this ).parents(".documentor-section:first").data('section-id');
			var docid = jQuery( this ).parents(".documentor-wrap:first").data('docid');
			var data = {
					'action': 'doc_positive_feedback',
					'secid': secid,
					'docid': docid
				};
			jQuery.post(DocAjax.docajaxurl, data, function(response) {
				response = response.replace(/^\s*[\r\n]/gm, "");
    				response = response.match(/!!START!!(.*[\s\S]*)!!END!!/)[1];
				var res = JSON.parse(response);
				if( res['success'] == 1 ) {
					jQuery('.section-'+data['secid']).find(".feedback-msg").html("<div class='doc-success-msg'>"+res['msg']+"</div>");
					//if votecount present then increment total and positive vote count
					if( jQuery('.section-'+data['secid']).find('.doc-feedbackcnt .upvote').length > 0 ) {
						var upvotespan = jQuery('.section-'+data['secid']).find('.doc-feedbackcnt .upvote');
						var totalvotespan = jQuery('.section-'+data['secid']).find('.doc-feedbackcnt .totalvote');
						var upvotecnt = parseInt(upvotespan.html());
						var totalvotecnt = parseInt(totalvotespan.html());
						jQuery(upvotespan).html(upvotecnt+1);
						jQuery(totalvotespan).html(totalvotecnt+1);
					}
				} else {
					jQuery('.section-'+data['secid']).find(".feedback-msg").html("<div class='doc-error-msg'>"+res['msg']+"</div>");
				}
			});
		});
		/*negative feedback*/
		jQuery( ".negative-feedback", scope ).click(function(e) {
				e.preventDefault();
				var docid = jQuery( this ).parents(".documentor-wrap:first").data('docid');
				var secid = jQuery( this ).parents(".documentor-section:first").data('section-id');
				var data = {
					'action': 'doc_get_feedback_form',
					'docid': docid,
					'secid': secid
				};
				jQuery.post(DocAjax.docajaxurl, data, function(response) {
					response = response.replace(/^\s*[\r\n]/gm, "");
  					response = response.match(/!!START!!(.*[\s\S]*)!!END!!/)[1];
					//check if message is returned
					var res = JSON.parse(response);
					if( res['msgflag'] == 1 ) {
						jQuery('.section-'+data['secid']).find(".feedback-msg").html("<div class='doc-error-msg'>"+res['text']+"</div>");
						
					} else {
						jQuery('.section-'+data['secid']).find('.negative-feedbackform').html(res['text']).slideToggle("slow");
					}
				});
			});
		/*negative feedback form submit*/
		jQuery('.documentor-help', scope).on('click', '.docsubmit-nfeedback', function() {
			var submitbtn = jQuery( this ).attr('class');
			var data = {
					'action': 'doc_negative_feedback',
					'submitbtn': submitbtn
				};
				jQuery(this).parents('.documentor-nfeedback').serializeArray().map(function(item) {
						data[item.name] = item.value;
				});
			jQuery.post(DocAjax.docajaxurl, data, function(response) {
				jQuery('.section-'+data['secid']).find('.negative-feedbackform').slideUp("slow");
				response = response.replace(/^\s*[\r\n]/gm, "");
    				response = response.match(/!!START!!(.*[\s\S]*)!!END!!/)[1];
				var res = JSON.parse(response);
				if( res['success'] == 1 ) {
					jQuery('.section-'+data['secid']).find(".feedback-msg").html("<div class='doc-success-msg'>"+res['msg']+"</div>");
					//if votecount present then increment total vote count
					if( jQuery('.section-'+data['secid']).find('.doc-feedbackcnt .upvote').length > 0 ) {
						var totalvotespan = jQuery('.section-'+data['secid']).find('.doc-feedbackcnt .totalvote');
						var totalvotecnt = parseInt(totalvotespan.html());
						jQuery(totalvotespan).html(totalvotecnt+1);
					}	
				} else {
					jQuery('.section-'+data['secid']).find(".feedback-msg").html("<div class='doc-error-msg'>"+res['msg']+"</div>");
				}
			});
			return false;
		});
		/* set values in suggest edit popup form */
		jQuery(".spopupopen", scope).on("click", function() {
			var docid = jQuery( this ).parents(".documentor-wrap:first").data('docid');
			var secid = jQuery( this ).parents(".documentor-section:first").data('section-id');
			jQuery(".spopupopen").leanModal();
			jQuery( this ).parents(".documentor-wrap:first").find("#sugestedit_popup"+docid+" .sedit-secid").val(secid);
			var data = {
					'action': 'doc_suggest_editsecdata',
					'secid': secid,
					'docid': docid
				};
			jQuery.post(DocAjax.docajaxurl, data, function(response) {
				var res = JSON.parse(response);
				//set section title hidden field
				jQuery('#sugestedit_popup'+docid+' .sedit-sectitle').val(res[0]);
				//set pot id hidden field
				jQuery('#sugestedit_popup'+docid+' .sedit-postid').val(res[2]);
				//set captcha field
				if( jQuery('#sugestedit_popup'+docid+' .doc-sedit-captcha').length > 0 ) {
					jQuery('#sugestedit_popup'+docid+' .doc-sedit-captcha').html(res[1]);
				}
				if( jQuery( '#sugestedit_popup'+docid+' .textareainput' ).length > 0 ) {
					jQuery( '#sugestedit_popup'+docid+' .textareainput' ).val('');
				}
			});
		
		});
		/* suggest edit form submit */
		jQuery('.docsubmit-suggestform', scope).on('click', function() {
			var submitbtn = jQuery( this ).attr('class');
			var data = {
					'action': 'doc_suggest_edit',
					'submitbtn': submitbtn
				};
				jQuery(this).parent('.documentor-suggestform').serializeArray().map(function(item) {
						data[item.name] = item.value;
				});
			jQuery.post(DocAjax.docajaxurl, data, function(response) {
				jQuery("#lean_overlay").trigger("click");
				response = response.replace(/^\s*[\r\n]/gm, "");
    				response = response.match(/!!START!!(.*[\s\S]*)!!END!!/)[1];
				var res = JSON.parse(response);
				if( res['success'] == 1 ) {
					jQuery('.section-'+data['secid']).find(".documentor-help .feedback-msg").html("<div class='doc-success-msg'>"+res['msg']+"</div>");
				} else {
					jQuery('.section-'+data['secid']).find(".documentor-help .feedback-msg").html("<div class='doc-error-msg'>"+res['msg']+"</div>");
				}
			});
			return false;
		});
		/*Save PDF*/
		//document pdf 		
		jQuery(".save_secpdf", scope).on("click", function() {
			jQuery( this ).parent(".save_docpdf").submit();
		});
		jQuery(".save_secpdf_inline", scope).on("click", function() {
			jQuery( this ).parent(".save_docpdf").submit();
		});
	}
})(jQuery);

/*! Copyright (c) 2011 Piotr Rochala (http://rocha.la)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * Version: 1.3.0
 *
 */
(function(f) {
    jQuery.fn.extend({
        slimScroll: function(h) {
            var a = f.extend({
                width: "auto",
                height: "250px",
                size: "7px",
                color: "#000",
                position: "right",
                distance: "0px",
                start: "top",
                opacity: 0.4,
                alwaysVisible: !1,
                disableFadeOut: !1,
                railVisible: !1,
                railColor: "#333",
                railOpacity: 0.2,
                railDraggable: !0,
                railClass: "slimScrollRail",
                barClass: "slimScrollBar",
                wrapperClass: "slimScrollDiv",
                allowPageScroll: !1,
                wheelStep: 20,
                touchScrollStep: 200,
                borderRadius: "7px",
                railBorderRadius: "7px"
            }, h);
            this.each(function() {
                function r(d) {
                    if (s) {
                        d = d ||
                            window.event;
                        var c = 0;
                        d.wheelDelta && (c = -d.wheelDelta / 120);
                        d.detail && (c = d.detail / 3);
                        f(d.target || d.srcTarget || d.srcElement).closest("." + a.wrapperClass).is(b.parent()) && m(c, !0);
                        d.preventDefault && !k && d.preventDefault();
                        k || (d.returnValue = !1)
                    }
                }

                function m(d, f, h) {
                    k = !1;
                    var e = d,
                        g = b.outerHeight() - c.outerHeight();
                    f && (e = parseInt(c.css("top")) + d * parseInt(a.wheelStep) / 100 * c.outerHeight(), e = Math.min(Math.max(e, 0), g), e = 0 < d ? Math.ceil(e) : Math.floor(e), c.css({
                        top: e + "px"
                    }));
                    l = parseInt(c.css("top")) / (b.outerHeight() - c.outerHeight());
                    e = l * (b[0].scrollHeight - b.outerHeight());
                    h && (e = d, d = e / b[0].scrollHeight * b.outerHeight(), d = Math.min(Math.max(d, 0), g), c.css({
                        top: d + "px"
                    }));
                    b.scrollTop(e);
                    b.trigger("slimscrolling", ~~e);
                    v();
                    p()
                }

                function C() {
                    window.addEventListener ? (this.addEventListener("DOMMouseScroll", r, !1), this.addEventListener("mousewheel", r, !1), this.addEventListener("MozMousePixelScroll", r, !1)) : document.attachEvent("onmousewheel", r)
                }

                function w() {
                    u = Math.max(b.outerHeight() / b[0].scrollHeight * b.outerHeight(), D);
                    c.css({
                        height: "20%"
                    });
                    var a = u == b.outerHeight() ? "none" : "block";
                    c.css({
                        display: a
                    })
                }

                function v() {
                    w();
                    clearTimeout(A);
                    l == ~~l ? (k = a.allowPageScroll, B != l && b.trigger("slimscroll", 0 == ~~l ? "top" : "bottom")) : k = !1;
                    B = l;
                    u >= b.outerHeight() ? k = !0 : (c.stop(!0, !0).fadeIn("fast"), a.railVisible && g.stop(!0, !0).fadeIn("fast"))
                }

                function p() {
                    a.alwaysVisible || (A = setTimeout(function() {
                        a.disableFadeOut && s || (x || y) || (c.fadeOut("slow"), g.fadeOut("slow"))
                    }, 1E3))
                }
                var s, x, y, A, z, u, l, B, D = 30,
                    k = !1,
                    b = f(this);
                if (b.parent().hasClass(a.wrapperClass)) {
                    var n = b.scrollTop(),
                        c = b.parent().find("." + a.barClass),
                        g = b.parent().find("." + a.railClass);
                    w();
                    if (f.isPlainObject(h)) {
                        if ("height" in h && "auto" == h.height) {
                            b.parent().css("height", "auto");
                            b.css("height", "auto");
                            var q = b.parent().parent().height();
                            b.parent().css("height", q);
                            b.css("height", q)
                        }
                        if ("scrollTo" in h) n = parseInt(a.scrollTo);
                        else if ("scrollBy" in h) n += parseInt(a.scrollBy);
                        else if ("destroy" in h) {
                            c.remove();
                            g.remove();
                            b.unwrap();
                            return
                        }
                        m(n, !1, !0)
                    }
                } else {
                    a.height = "auto" == a.height ? b.parent().height() : a.height;
                    n = f("<div></div>").addClass(a.wrapperClass).css({
                        position: "relative",
                        overflow: "hidden",
                        width: a.width,
                        height: a.height
                    });
                    b.css({
                        overflow: "hidden",
                        width: a.width,
                        height: a.height
                    });
                    var g = f("<div></div>").addClass(a.railClass).css({
                            width: a.size,
                            height: "100%",
                            position: "absolute",
                            top: 0,
                            display: a.alwaysVisible && a.railVisible ? "block" : "none",
                            "border-radius": a.railBorderRadius,
                            background: a.railColor,
                            opacity: a.railOpacity,
                            zIndex: 90
                        }),
                        c = f("<div></div>").addClass(a.barClass).css({
                            background: a.color,
                            width: a.size,
                            position: "absolute",
                            top: 0,
                            opacity: a.opacity,
                            display: a.alwaysVisible ?
                                "block" : "none",
                            "border-radius": a.borderRadius,
                            BorderRadius: a.borderRadius,
                            MozBorderRadius: a.borderRadius,
                            WebkitBorderRadius: a.borderRadius,
                            zIndex: 99
                        }),
                        q = "right" == a.position ? {
                            right: a.distance
                        } : {
                            left: a.distance
                        };
                    g.css(q);
                    c.css(q);
                    b.wrap(n);
                    b.parent().append(c);
                    b.parent().append(g);
                    a.railDraggable && c.bind("mousedown", function(a) {
                        var b = f(document);
                        y = !0;
                        t = parseFloat(c.css("top"));
                        pageY = a.pageY;
                        b.bind("mousemove.slimscroll", function(a) {
                            currTop = t + a.pageY - pageY;
                            c.css("top", currTop);
                            m(0, c.position().top, !1)
                        });
                        b.bind("mouseup.slimscroll", function(a) {
                            y = !1;
                            p();
                            b.unbind(".slimscroll")
                        });
                        return !1
                    }).bind("selectstart.slimscroll", function(a) {
                        a.stopPropagation();
                        a.preventDefault();
                        return !1
                    });
                    g.hover(function() {
                        v()
                    }, function() {
                        p()
                    });
                    c.hover(function() {
                        x = !0
                    }, function() {
                        x = !1
                    });
                    b.hover(function() {
                        s = !0;
                        v();
                        p()
                    }, function() {
                        s = !1;
                        p()
                    });
                    b.bind("touchstart", function(a, b) {
                        a.originalEvent.touches.length && (z = a.originalEvent.touches[0].pageY)
                    });
                    b.bind("touchmove", function(b) {
                        k || b.originalEvent.preventDefault();
                        b.originalEvent.touches.length &&
                            (m((z - b.originalEvent.touches[0].pageY) / a.touchScrollStep, !0), z = b.originalEvent.touches[0].pageY)
                    });
                    w();
                    "bottom" === a.start ? (c.css({
                        top: b.outerHeight() - c.outerHeight()
                    }), m(0, !0)) : "top" !== a.start && (m(f(a.start).position().top, null, !0), a.alwaysVisible || c.hide());
                    C()
                }
            });
            return this
        }
    });
    jQuery.fn.extend({
        slimscroll: jQuery.fn.slimScroll
    })
})(jQuery);
