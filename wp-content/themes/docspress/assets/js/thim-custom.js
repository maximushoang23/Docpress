/**
 * Script custom for theme
 *
 * TABLE OF CONTENT
 *
 * Header: main menu
 * Header: main menu mobile
 * Sidebar: sticky sidebar
 * Feature: Preloading
 * Feature: Back to top
 * Custom select use Dropkickjs.
 */

(function ($) {
	"use strict";

	/**
	 * Check this site in iframe.
	 * @type {boolean}
	 */
	var isInIFrame = (window.location != window.parent.location);

	$(document).ready(function () {
		thim_startertheme.ready();
	});

	$(window).load(function () {
		thim_startertheme.load();
	});

	var thim_startertheme = {

		/**
		 * Call functions when document ready
		 */
		ready: function () {
			// this.header_menu();
			// this.back_to_top();
			// this.feature_preloading();
			  this.sticky_sidebar();
			// if (isInIFrame != true) {
				// this.custom_select();
			// }
			// this.contactform7();
			// this.thim_blog_auto_height();
		},

		/**
		 * Call functions when window load.
		 */
		load: function () {
			// this.header_menu_mobile();
			// this.parallax();
			// this.thim_post_gallery();
		},

		// CUSTOM FUNCTION IN BELOW
		thim_post_gallery: function () {
			$('article.format-gallery .flexslider').imagesLoaded(function () {
				$('.flexslider').flexslider({
					slideshow     : true,
					animation     : 'fade',
					pauseOnHover  : true,
					animationSpeed: 400,
					smoothHeight  : true,
					directionNav  : true,
					controlNav    : false
				});
			});
		},


		/**
		 * Mobile menu
		 */
		header_menu_mobile: function () {
			$(document).on('click', '.menu-mobile-effect', function (e) {
				e.stopPropagation();
				$('.responsive #wrapper-container').toggleClass('mobile-menu-open');
			});

			$(document).on('click', '.mobile-menu-open #main-content', function () {
				$('.responsive #wrapper-container.mobile-menu-open').removeClass('mobile-menu-open');
			});

			$('.navbar>li.menu-item-has-children >a,.navbar-nav>li.menu-item-has-children >span').after('<span class="icon-toggle"><i class="fa fa-angle-down"></i></span>');
			$('.responsive .mobile-menu-container .navbar-nav>li.menu-item-has-children >a').after('<span class="icon-toggle"><i class="fa fa-angle-down"></i></span>');

			$('.responsive .mobile-menu-container .navbar-nav>li.menu-item-has-children .icon-toggle').on('click', function () {
				if ($(this).next('ul.sub-menu').is(':hidden')) {
					$(this).next('ul.sub-menu').slideDown(200, 'linear');
					$(this).html('<i class="fa fa-angle-up"></i>');
				} else {
					$(this).next('ul.sub-menu').slideUp(200, 'linear');
					$(this).html('<i class="fa fa-angle-down"></i>');
				}
			});
		},

		/**
		 * Header menu sticky, scroll, v.v.
		 */
		header_menu: function () {
			var $header = $('#masthead.sticky-header'),
				off_Top = ( $('#wrapper-container').length > 0 ) ? $('#wrapper-container').offset().top : 0,
				$topbar = $('#thim-header-topbar'),
				menuH = $header.outerHeight(),
				latestScroll = 0,
				startFixed = 2;

			if ($topbar.length) {
				if ($topbar.hasClass('style-overlay')) {
					$header.css({
						top: $topbar.outerHeight() + 'px'
					});
				}
				startFixed = $topbar.outerHeight();
			}

			$(window).scroll(function () {
				var current = $(this).scrollTop();
				if (current > startFixed) {
					$header.removeClass('affix-top').addClass('affix');
					$header.css({
						top: off_Top
					});
				} else {
					$header.removeClass('affix').addClass('affix-top');
					if ($topbar.length > 0) {
						if ($topbar.hasClass('style-overlay')) {
							$header.css({
								top: $topbar.outerHeight() + 'px'
							});
						} else {
							$header.css({
								top: 0
							});
						}
					}
				}

				if (current > latestScroll && current > menuH + off_Top) {
					if (!$header.hasClass('menu-hidden')) {
						$header.addClass('menu-hidden');
						$header.css({
							top: off_Top
						});
					}
				} else {
					if ($header.hasClass('menu-hidden')) {
						$header.removeClass('menu-hidden');
						$header.css({
							top: off_Top
						});
					}
				}

				latestScroll = current;
			});

			$('#masthead .navbar > .menu-item-has-children, .navbar > li ul li').hover(
				function () {
					$(this).children('.sub-menu').stop(true, false).slideDown(250);
				},
				function () {
					$(this).children('.sub-menu').stop(true, false).slideUp(250);
				}
			);
		},

		/**
		 * Back to top
		 */
		back_to_top: function () {
			var $element = $('#back-to-top');
			$(window).scroll(function () {
				if ($(this).scrollTop() > 100) {
					$element.addClass('scrolldown').removeClass('scrollup');
				} else {
					$element.addClass('scrollup').removeClass('scrolldown');
				}
			});

			$element.on('click', function () {
				$('html,body').animate({scrollTop: '0px'}, 800);
				return false;
			});
		},

		/**
		 * Sticky sidebar
		 */
		sticky_sidebar: function () {
			var offsetTop = 20;

			if ($("#wpadminbar").length) {
				offsetTop += $("#wpadminbar").outerHeight();
			}
			if ($("#masthead.affix").length) {
				offsetTop += $("#masthead.affix").outerHeight();
			}

			if ($('.sticky-sidebar').length > 0) {
				$(".sticky-sidebar").theiaStickySidebar({
					"containerSelector"     : "",
					"additionalMarginTop"   : offsetTop,
					"additionalMarginBottom": "0",
					"updateSidebarHeight"   : false,
					"minWidth"              : "768",
					"sidebarBehavior"       : "modern"
				});
			}

		},

		/**
		 * Parallax init
		 */
		parallax: function () {
			$(window).stellar({
				horizontalOffset: 0,
				verticalOffset  : 0
			});
		},

		/**
		 * feature: Preloading
		 */
		feature_preloading: function () {
			var $preload = $('#thim-preloading');
			if ($preload.length > 0) {
				$preload.fadeOut(1000, function () {
					$preload.remove();
				});
			}
		},


		/**
		 * Custom select use Dropkickjs
		 */
		custom_select: function () {
			$('select').dropkick({
				mobile: true,
				change: function () {
					var selectedIndex = this.selectedIndex;
					$(this.data.select).find('option').each(function (index, ele) {
						if (index === selectedIndex) {
							$(ele).attr('selected', 'selected');
						} else {
							$(ele).removeAttr('selected');
						}
					});
				}
			});
		},


		/**
		 * Custom effect and UX for contact form.
		 */
		contactform7: function () {
			$(".wpcf7-submit").on('click', function () {
				$(this).css("opacity", 0.2);
				$(this).parents('.wpcf7-form').addClass('processing');
				$('input:not([type=submit]), textarea').attr('style', '');
			});

			$(document).on('spam.wpcf7', function () {
				$(".wpcf7-submit").css("opacity", 1);
				$('.wpcf7-form').removeClass('processing');
			});

			$(document).on('invalid.wpcf7', function () {
				$(".wpcf7-submit").css("opacity", 1);
				$('.wpcf7-form').removeClass('processing');
			});

			$(document).on('mailsent.wpcf7', function () {
				$(".wpcf7-submit").css("opacity", 1);
				$('.wpcf7-form').removeClass('processing');
			});

			$(document).on('mailfailed.wpcf7', function () {
				$(".wpcf7-submit").css("opacity", 1);
				$('.wpcf7-form').removeClass('processing');
			});

			$('body').on('click', 'input:not([type=submit]).wpcf7-not-valid, textarea.wpcf7-not-valid', function () {
				$(this).removeClass('wpcf7-not-valid');
			});
		},


		/**
		 * Blog auto height
		 */
		thim_blog_auto_height: function () {
			var $blog = $('.blog .blog-content article, .archive .blog-content article'),
				maxHeight = 0,
				setH = true;

			// Get max height of all items.
			$blog.each(function () {
				setH = $(this).hasClass('column-1') ? false : true;
				if (maxHeight < $(this).find('.content-inner').height()) {
					maxHeight = $(this).find('.content-inner').height();
				}
			});

			// Set height for all items.
			if (maxHeight > 0 && setH) {
				$blog.each(function () {
					$(this).find('.content-inner').css('height', maxHeight);
				});
			}
		},


	};

})(jQuery);

jQuery(function () {
	jQuery('.sidebar-nav a').bind('click', function (event) {
		var $anchor = jQuery(this);
		jQuery('html, body').stop().animate({
			scrollTop: jQuery($anchor.attr('href')).offset().top - 100
		}, 1500, 'easeInOutExpo');
		event.preventDefault();
	});
});