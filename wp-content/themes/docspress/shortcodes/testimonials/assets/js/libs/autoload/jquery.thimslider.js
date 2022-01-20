/**
 * jQuery Content Slider plugin
 *
 * @author ThimPress
 */
!function (a) {
	a.thimContentSlider = function (b, c) {
		function C(b) {
			var c = [b.heading ? "<h4>" + b.heading + "</h4>" : "", b.content ? "<div>" + b.content + "</div>" : ""].join(""), d = a(' 				<li> 					<div class="slide-content" style="margin: ' + T("itemPadding") + 'px;"> 				' + (c ? '<div class="thumb-content">' + c + "</div>" : "") + '<div class="around-img"><img src="' + b.image + '" /> </div>' + " 							</div> 				</li> 			");
			return d
		}

		function D() {
			var b = typeof d.options.items, c = null;
			"string" == b ? c = a(d.options.items) : "object" == b && (c = a(d.options.items).children()), c && (d.options.items = [], c.each(function () {
				var b = a(this), c = b.find("img" + d.options.imageSelector + ":first"), e = c.parent();
				d.options.items.push({
					image       : c.attr("src"),
					imageHeading: c.attr("data-heading"),
					imageContent: c.attr("data-content"),
					url         : e.is("a") ? e.attr("href") : "",
					content     : b.find(d.options.contentSelector)
				})
			}))
		}

		function E() {
			var b = [' 					<div class="slides-wrapper"> 						<ul class="scrollable"></ul> 					</div> 					<a href="prev" class="control-nav prev"></a> 					<a href="next" class="control-nav next"></a> 				', '<div class="slides-content"></div>'], c = a(("top" == d.options.contentPosition ? b.reverse() : b).join("")), e = d.options.items;
			d.$el.html(c), d.$slidesWrapper = d.$el.find(".slides-wrapper"), d.$scrollable = d.$el.find(".scrollable").css({
				marginTop   : -T("itemPadding"),
				marginBottom: -T("itemPadding")
			}), d.$slideContent = d.$el.find(".slides-content");
			for (var f = 0, g = e.length; g > f; f++) {
				var h = C({
					image  : e[f].image,
					heading: e[f].imageHeading,
					content: e[f].imageContent
				}), i = a('<div class="slide-content" />').append(e[f].content);
				d.$scrollable.append(h), d.$slideContent.append(i)
			}
			d.$items = d.$scrollable.children(), u = T("itemsVisible") <= T("items").length ? T("itemsVisible") : T("items").length, k = Math.floor(u / 2), l = k, p = d.$items.length, o = l, d.$el.on("click", ".control-nav", N).on("click", ".scrollable > li", N), d.options.mouseWheel && d.$el.on("mousewheel", function (a, b, c, d) {
				a.preventDefault(), -1 != b ? Q() : R()
			}), d.options.autoPlay && U(), d.options.pauseOnHover && d.$el.hover(function () {
				V()
			}, function () {
				W()
			}), d.$scrollable.bind(z.start, F).bind(z.move, G).bind(z.end, H), K(), d.$slideContent.children().eq(l).css({opacity: 1}).addClass("current").siblings().removeClass("current")
		}

		function F() {
		}

		function G() {
		}

		function H() {
		}

		function K() {
			var a = d.$el.find(".control-nav");
			d.$el.hover(function () {
				d.$el.addClass("hover")
			}, function () {
				d.$el.removeClass("hover")
			}), d.$nav = a
		}

		function L() {
			var a = d.$nav.height(), b = {top: (t - 2 * T("itemPadding")) / 2, marginTop: -a / 2};
			"behind" == T("controlNav"), "top" == T("contentPosition") && (b.top += d.$slideContent.outerHeight()), d.$nav.css(b)
		}

		function M(a) {
			if (a.hasClass("mid-item"))return 0;
			var b = d.$items.index(a), c = d.$items.index(d.$items.filter(".mid-item")), e = b - c;
			return e
		}

		function N(b) {
			b.preventDefault();
			var c = a(this).attr("href");
			switch (c) {
				case"prev":
					Q();
					break;
				case"next":
					R();
					break;
				default:
					var d = a(b.target);
					d.is("li") || (d = d.closest("li")), P(M(d))
			}
		}

		function O(b, c) {
			"prev" == b ? d.$items.last().remove() : d.$items.first().remove(), S(), d.$items.eq(k).addClass("mid-item").siblings().removeClass("mid-item"), B && d.$slideContent.children().eq(o).stop().show().animate({opacity: 1}).siblings().hide(), d.$items.eq(k).find(".thumb-content").show(), d.$scrollable.height(d.$items.eq(k).height()), n = !1, W(), d.$el.hasClass("hover"), L(), a.isFunction(c) && c.apply(d)
		}

		function P(a) {
			if (0 == a)return A = "", void(B = !0);
			B = 1 == Math.abs(a) ? !0 : !1, A = 250;
			var b = 0 > a ? Q : R;
			b.call(this, function () {
				P(0 > a ? a + 1 : a - 1)
			})
		}

		function Q(a) {
			if (!n) {
				V(), n = !0, d.$slideContent.children().eq(o).stop().animate({opacity: 0}), o--, 0 > o && (o = p - 1);
				var c = (T("itemPadding"), parseInt((t - s) / 2)), e = 0, f = d.$items.length, g = 0, h = function () {
					g++, g == f && O("prev", a)
				}, i = d.$items.last().clone();
				i.insertBefore(d.$items.first()).css({left: parseInt(d.$items.first().css("left")) - s}), S(), d.$el.find(".mid-item").removeClass("mid-item"), d.$items.eq(l + 1).addClass("mid-item");
				for (var j = e; f >= j; j++) {
					var k = {left: v - (l - j) * s, width: s, top: c};
					d.$items.eq(j).find(".thumb-content").hide(), l > j ? k.left -= r : j == l ? (k.left = v, k.top = 0, k.width = t) : j == l + 1 ? (k.left = v + t + r, k.top = c, k.width = s) : k.left += t - s + r, d.$items.eq(j).stop().show().animate(k, A, h)
				}
			}
		}

		function R(a) {
			if (!n) {
				V(), n = !0, d.$slideContent.children().eq(o).stop().animate({opacity: 0}), o++, o >= p && (o = 0);
				var c = (T("itemPadding"), parseInt((t - s) / 2)), e = 0, f = d.$items.length, g = 0, h = function () {
					g++, g == f && O("next", a)
				}, i = d.$items.first().clone();
				i.insertAfter(d.$items.last()).css({left: parseInt(d.$items.last().css("left")) + s}), S(), d.$el.find(".mid-item").removeClass("mid-item"), d.$items.eq(l + 1).addClass("mid-item");
				for (var j = e; f >= j; j++) {
					var k = {left: v - (l - j) * s, width: s, top: c};
					d.$items.eq(j).find(".thumb-content").hide(), l > j ? k.left -= s + r : j == l ? (k.left -= s + r, k.top = c, k.width = s) : j == l + 1 ? (k.left = v, k.top = 0, k.width = t) : k.left = v + t + (j - l - 2) * s + r, d.$items.eq(j).stop().show().animate(k, A, h)
				}
			}
		}

		function S() {
			d.$items = d.$scrollable.children()
		}

		function T(a) {
			return d.options[a]
		}

		function U() {
			j && clearTimeout(j), j = setTimeout(function () {
				U(), R()
			}, T("pauseTime"))
		}

		function V() {
			j && clearTimeout(j)
		}

		function W() {
			T("autoPlay") && U()
		}

		function X(b) {
			if (d.$scrollable.css("width", ""), b = a.extend({
					itemPadding : T("itemPadding"),
					itemMaxWidth: T("itemMaxWidth"),
					itemsVisible: u,
					itemMinWidth: T("itemMinWidth")
				}, b || {}), h = d.$el.width(), t = parseInt(b.itemMaxWidth + 2 * b.itemPadding), s = parseInt(t / q), m = s * (b.itemsVisible - 1) + t + 2 * r, m > h) {
				var c = m - h, e = c / (b.itemsVisible + q - 1);
				if (t - e * q < b.itemMinWidth) {
					if (b.itemsVisible - 2 >= 1)return b.itemsVisible -= 2, void X({itemsVisible: b.itemsVisible})
				} else t -= e * q, s -= e;
				m = h
			} else d.$scrollable.width(m);
			v = parseInt((m - t) / 2)
		}

		function Y() {
			X(), L();
			var a = T("itemPadding");
			d.$scrollable.height(t);
			var b = 0, c = parseInt((t - s) / 2), e = 0, f = d.$items.length - 1;
			d.$items.hide();
			for (var g = e; f >= g; g++)d.$items.eq(g).show(), g == l ? (d.$items.eq(g).css({
				left : parseInt(v),
				width: parseInt(t)
			}).addClass("mid-item").find(".slide-content").css({margin: a}), d.$scrollable.height(d.$items.eq(g).height())) : (b = v - (l - g) * s, g > l ? b += t - s + r : b -= r, d.$items.eq(g).css({
				width: parseInt(s),
				left : parseInt(b),
				top  : parseInt(c)
			}).removeClass("mid-item"))
		}

		function Z(a) {
			a ? Y() : (i && clearTimeout(i), i = setTimeout(function () {
				Y()
			}, 350))
		}

		function $() {
			D(), E(), e.on("resize.thim-content-slider", function () {
				Z()
			}).trigger("resize.thim-content-slider"), Y()
		}

		this.$el = a(b).addClass("thim-content-slider"), this.$items = [], this.options = a.extend({}, a.fn.thimContentSlider.defaults, c);
		var d = this, e = a(window), h = (a(document), a(document.body), 0), i = null, j = null, k = 0, l = 0, m = 0, n = !1, o = 0, p = 0, q = this.options.activeItemRatio || 2.5, r = this.options.activeItemPadding, s = 0, t = 0, u = this.options.itemsVisible || 7, v = 0, y = "ontouchstart" in window || window.navigator.msMaxTouchPoints, z = {
			start: y ? "touchstart" : "mousedown",
			move : y ? "touchmove" : "mousemove",
			end  : y ? "touchend" : "mouseup"
		}, A = "", B = !0;
		this.pause = V, this.restart = W, this.prev = Q, this.next = R, this.update = Y, this.move = P, $()
	}, a.fn.thimContentSlider = function (b) {
		var c = !1, d = [];
		if (arguments.length > 0 && "string" == typeof arguments[0]) {
			c = arguments[0];
			for (var e = 1; e < arguments.length; e++)d[e - 1] = arguments[e]
		}
		return a.each(this, function () {
			var e = a(this), f = e.data("thim-content-slider");
			if (f || (f = new a.thimContentSlider(this, b), e.data("thim-content-slider", f)), c) {
				if (a.isFunction(f[c]))return f[c].apply(f, d);
				throw"Method thimContentSlider." + c + "() does not exists"
			}
			return e
		})
	}, a.fn.thimContentSlider.defaults = {
		items            : [{image: "", url: "", html: ""}],
		itemMaxWidth     : 200,
		itemMinWidth     : 150,
		itemsVisible     : 7,
		itemPadding      : 10,
		activeItemRatio  : 2,
		activeItemPadding: 0,
		mouseWheel       : !0,
		autoPlay         : !0,
		pauseTime        : 3e3,
		pauseOnHover     : !0,
		imageSelector    : "",
		contentSelector  : ".content",
		controlNav       : "behind",
		contentPosition  : ""
	}
}(jQuery);