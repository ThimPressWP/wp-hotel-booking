/**
 * Minified by jsDelivr using Terser v5.3.5.
 * Original file: /npm/lightgallery@2.0.0-beta.3/plugins/thumbnail/lg-thumbnail.umd.js
 *
 * Do NOT use SRI with dynamically generated files! More information: https://www.jsdelivr.com/using-sri-with-dynamic-files
 */
/*!
 * lightgallery | 2.0.0-beta.3 | May 4th 2021
 * http://sachinchoolur.github.io/lightGallery/
 * Copyright (c) 2020 Sachin Neravath;
 * @license GPLv3
 */
!function(t,e){"object"==typeof exports&&"undefined"!=typeof module?module.exports=e():"function"==typeof define&&define.amd?define(e):t.lgThumbnail=e()}(this,(function(){"use strict";
/*! *****************************************************************************
    Copyright (c) Microsoft Corporation.

    Permission to use, copy, modify, and/or distribute this software for any
    purpose with or without fee is hereby granted.

    THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
    REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
    AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
    INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
    LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
    OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
    PERFORMANCE OF THIS SOFTWARE.
    ***************************************************************************** */var t=function(){return(t=Object.assign||function(t){for(var e,i=1,s=arguments.length;i<s;i++)for(var h in e=arguments[i])Object.prototype.hasOwnProperty.call(e,h)&&(t[h]=e[h]);return t}).apply(this,arguments)},e={thumbnail:!0,animateThumb:!0,currentPagerPosition:"middle",alignThumbnails:"middle",thumbWidth:100,thumbHeight:"80px",thumbMargin:5,appendThumbnailsTo:".lg-components",toggleThumb:!1,enableThumbDrag:!0,enableThumbSwipe:!0,swipeThreshold:10,loadYouTubeThumbnail:!0,youTubeThumbSize:1},i="lgContainerResize",s="lgUpdateSlides",h="lgBeforeOpen",n="lgBeforeSlide";return function(){function o(i,s){return this.thumbOuterWidth=0,this.thumbTotalWidth=0,this.translateX=0,this.thumbClickable=!1,this.core=i,this.$LG=s,this.settings=t(t({},e),this.core.settings),this.init(),this}return o.prototype.init=function(){this.thumbOuterWidth=0,this.thumbTotalWidth=this.core.galleryItems.length*(this.settings.thumbWidth+this.settings.thumbMargin),this.translateX=0,this.setAnimateThumbStyles(),this.core.settings.allowMediaOverlap||(this.settings.toggleThumb=!1),this.settings.thumbnail&&this.core.galleryItems.length>1&&(this.build(),this.settings.animateThumb?(this.settings.enableThumbDrag&&this.enableThumbDrag(),this.settings.enableThumbSwipe&&this.enableThumbSwipe(),this.thumbClickable=!1):this.thumbClickable=!0,this.toggleThumbBar(),this.thumbKeyPress())},o.prototype.build=function(){var t=this;this.setThumbMarkup(),this.manageActiveClassOnSlideChange(),this.$lgThumb.first().on("click.lg touchend.lg",(function(e){var i=t.$LG(e.target);i.hasAttribute("data-lg-item-id")&&setTimeout((function(){if(t.thumbClickable&&!t.core.lgBusy){var e=parseInt(i.attr("data-lg-item-id"));t.core.slide(e,!1,!0,!1)}}),50)})),this.core.LGel.on(n+".thumb",(function(e){var i=e.detail.index;t.animateThumb(i)})),this.core.LGel.on(h+".thumb",(function(){t.thumbOuterWidth=t.core.outer.get().offsetWidth})),this.core.LGel.on(s+".thumb",(function(){t.rebuildThumbnails()})),this.core.LGel.on(i+".thumb",(function(){t.core.lgOpened&&setTimeout((function(){t.thumbOuterWidth=t.core.outer.get().offsetWidth,t.animateThumb(t.core.index),t.thumbOuterWidth=t.core.outer.get().offsetWidth}),50)}))},o.prototype.setThumbMarkup=function(){var t="lg-thumb-outer ";this.settings.alignThumbnails&&(t+="lg-thumb-align-"+this.settings.alignThumbnails);var e='<div class="'+t+'">\n        <div class="lg-thumb lg-group">\n        </div>\n        </div>';this.core.outer.addClass("lg-has-thumb"),".lg-components"===this.settings.appendThumbnailsTo?this.core.$lgComponents.append(e):this.core.outer.append(e),this.$thumbOuter=this.core.outer.find(".lg-thumb-outer").first(),this.$lgThumb=this.core.outer.find(".lg-thumb").first(),this.settings.animateThumb&&this.core.outer.find(".lg-thumb").css("transition-duration",this.core.settings.speed+"ms").css("width",this.thumbTotalWidth+"px").css("position","relative"),this.setThumbItemHtml(this.core.galleryItems)},o.prototype.enableThumbDrag=function(){var t=this,e={cords:{startX:0,endX:0},isMoved:!1,newTranslateX:0,startTime:new Date,endTime:new Date,touchMoveTime:0},i=!1;this.$thumbOuter.addClass("lg-grab"),this.core.outer.find(".lg-thumb").first().on("mousedown.lg.thumb",(function(s){t.thumbTotalWidth>t.thumbOuterWidth&&(s.preventDefault(),e.cords.startX=s.pageX,e.startTime=new Date,t.thumbClickable=!1,i=!0,t.core.outer.get().scrollLeft+=1,t.core.outer.get().scrollLeft-=1,t.$thumbOuter.removeClass("lg-grab").addClass("lg-grabbing"))})),this.$LG(window).on("mousemove.lg.thumb.global"+this.core.lgId,(function(s){t.core.lgOpened&&i&&(e.cords.endX=s.pageX,e=t.onThumbTouchMove(e))})),this.$LG(window).on("mouseup.lg.thumb.global"+this.core.lgId,(function(){t.core.lgOpened&&(e.isMoved?e=t.onThumbTouchEnd(e):t.thumbClickable=!0,i&&(i=!1,t.$thumbOuter.removeClass("lg-grabbing").addClass("lg-grab")))}))},o.prototype.enableThumbSwipe=function(){var t=this,e={cords:{startX:0,endX:0},isMoved:!1,newTranslateX:0,startTime:new Date,endTime:new Date,touchMoveTime:0};this.$lgThumb.on("touchstart.lg",(function(i){t.thumbTotalWidth>t.thumbOuterWidth&&(i.preventDefault(),e.cords.startX=i.targetTouches[0].pageX,t.thumbClickable=!1,e.startTime=new Date)})),this.$lgThumb.on("touchmove.lg",(function(i){t.thumbTotalWidth>t.thumbOuterWidth&&(i.preventDefault(),e.cords.endX=i.targetTouches[0].pageX,e=t.onThumbTouchMove(e))})),this.$lgThumb.on("touchend.lg",(function(){e.isMoved?e=t.onThumbTouchEnd(e):t.thumbClickable=!0}))},o.prototype.rebuildThumbnails=function(){var t=this;this.$thumbOuter.addClass("lg-rebuilding-thumbnails"),setTimeout((function(){t.thumbTotalWidth=t.core.galleryItems.length*(t.settings.thumbWidth+t.settings.thumbMargin),t.$lgThumb.css("width",t.thumbTotalWidth+"px"),t.$lgThumb.empty(),t.setThumbItemHtml(t.core.galleryItems),t.animateThumb(t.core.index)}),50),setTimeout((function(){t.$thumbOuter.removeClass("lg-rebuilding-thumbnails")}),200)},o.prototype.setTranslate=function(t){this.$lgThumb.css("transform","translate3d(-"+t+"px, 0px, 0px)")},o.prototype.getPossibleTransformX=function(t){return t>this.thumbTotalWidth-this.thumbOuterWidth&&(t=this.thumbTotalWidth-this.thumbOuterWidth),t<0&&(t=0),t},o.prototype.animateThumb=function(t){if(this.$lgThumb.css("transition-duration",this.core.settings.speed+"ms"),this.settings.animateThumb){var e=0;switch(this.settings.currentPagerPosition){case"left":e=0;break;case"middle":e=this.thumbOuterWidth/2-this.settings.thumbWidth/2;break;case"right":e=this.thumbOuterWidth-this.settings.thumbWidth}this.translateX=(this.settings.thumbWidth+this.settings.thumbMargin)*t-1-e,this.translateX>this.thumbTotalWidth-this.thumbOuterWidth&&(this.translateX=this.thumbTotalWidth-this.thumbOuterWidth),this.translateX<0&&(this.translateX=0),this.setTranslate(this.translateX)}},o.prototype.onThumbTouchMove=function(t){return t.newTranslateX=this.translateX,t.isMoved=!0,t.touchMoveTime=(new Date).valueOf(),t.newTranslateX-=t.cords.endX-t.cords.startX,t.newTranslateX=this.getPossibleTransformX(t.newTranslateX),this.setTranslate(t.newTranslateX),this.$thumbOuter.addClass("lg-dragging"),t},o.prototype.onThumbTouchEnd=function(t){t.isMoved=!1,t.endTime=new Date,this.$thumbOuter.removeClass("lg-dragging");var e=t.endTime.valueOf()-t.startTime.valueOf(),i=t.cords.endX-t.cords.startX,s=Math.abs(i)/e;return s>.15&&t.endTime.valueOf()-t.touchMoveTime<30?((s+=1)>2&&(s+=1),s+=s*(Math.abs(i)/this.thumbOuterWidth),this.$lgThumb.css("transition-duration",Math.min(s-1,2)+"settings"),i*=s,this.translateX=this.getPossibleTransformX(this.translateX-i),this.setTranslate(this.translateX)):this.translateX=t.newTranslateX,Math.abs(t.cords.endX-t.cords.startX)<this.settings.swipeThreshold&&(this.thumbClickable=!0),t},o.prototype.getThumbHtml=function(t,e){var i,s=this.core.galleryItems[e].__slideVideoInfo||{};return i=s.youtube&&this.settings.loadYouTubeThumbnail?"//img.youtube.com/vi/"+s.youtube[1]+"/"+this.settings.youTubeThumbSize+".jpg":t,'<div data-lg-item-id="'+e+'" class="lg-thumb-item '+(e===this.core.index?" active":"")+'" \n        style="width:'+this.settings.thumbWidth+"px; height: "+this.settings.thumbHeight+";\n            margin-right: "+this.settings.thumbMargin+'px;">\n            <img data-lg-item-id="'+e+'" src="'+i+'" />\n        </div>'},o.prototype.getThumbItemHtml=function(t){for(var e="",i=0;i<t.length;i++)e+=this.getThumbHtml(t[i].thumb,i);return e},o.prototype.setThumbItemHtml=function(t){var e=this.getThumbItemHtml(t);this.$lgThumb.html(e)},o.prototype.setAnimateThumbStyles=function(){this.settings.animateThumb&&this.core.outer.addClass("lg-animate-thumb")},o.prototype.manageActiveClassOnSlideChange=function(){var t=this;this.core.LGel.on(n+".thumb",(function(e){var i=t.core.outer.find(".lg-thumb-item"),s=e.detail.index;i.removeClass("active"),i.eq(s).addClass("active")}))},o.prototype.toggleThumbBar=function(){var t=this;this.settings.toggleThumb&&(this.core.outer.addClass("lg-can-toggle"),this.core.$toolbar.append('<button type="button" aria-label="Toggle thumbnails" class="lg-toggle-thumb lg-icon"></button>'),this.core.outer.find(".lg-toggle-thumb").first().on("click.lg",(function(){t.core.outer.toggleClass("lg-components-open")})))},o.prototype.thumbKeyPress=function(){var t=this;this.$LG(window).on("keydown.lg.thumb.global"+this.core.lgId,(function(e){t.core.lgOpened&&t.settings.toggleThumb&&(38===e.keyCode?(e.preventDefault(),t.core.outer.addClass("lg-components-open")):40===e.keyCode&&(e.preventDefault(),t.core.outer.removeClass("lg-components-open")))}))},o.prototype.destroy=function(){this.settings.thumbnail&&this.core.galleryItems.length>1&&(this.$LG(window).off(".lg.thumb.global"+this.core.lgId),this.core.LGel.off(".lg.thumb"),this.core.LGel.off(".thumb"),this.$thumbOuter.remove(),this.core.outer.removeClass("lg-has-thumb"))},o}()}));
//# sourceMappingURL=/sm/8223b5669f1ae2ba3a65419e93d9ab24bbbd56b2d2dec2a320d11900f2dd226d.map
/**
 * Minified by jsDelivr using Terser v5.3.5.
 * Original file: /npm/lightgallery@2.0.0-beta.3/lightgallery.umd.js
 *
 * Do NOT use SRI with dynamically generated files! More information: https://www.jsdelivr.com/using-sri-with-dynamic-files
 */
/*!
 * lightgallery | 2.0.0-beta.3 | May 4th 2021
 * http://sachinchoolur.github.io/lightGallery/
 * Copyright (c) 2020 Sachin Neravath;
 * @license GPLv3
 */
! function (t, e) {
    "object" == typeof exports && "undefined" != typeof module ? module.exports = e() : "function" == typeof define && define.amd ? define(e) : t.lightGallery = e()
}(this, (function () {
    "use strict";
    /*! *****************************************************************************
        Copyright (c) Microsoft Corporation.

        Permission to use, copy, modify, and/or distribute this software for any
        purpose with or without fee is hereby granted.

        THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
        REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
        AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
        INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
        LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
        OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
        PERFORMANCE OF THIS SOFTWARE.
        ***************************************************************************** */
    var t = function () {
        return (t = Object.assign || function (t) {
            for (var e, i = 1, s = arguments.length; i < s; i++)
                for (var o in e = arguments[i]) Object.prototype.hasOwnProperty.call(e, o) && (t[o] = e[o]);
            return t
        }).apply(this, arguments)
    };
    ! function () {
        if ("function" == typeof window.CustomEvent) return !1;
        window.CustomEvent = function (t, e) {
            e = e || {
                bubbles: !1,
                cancelable: !1,
                detail: null
            };
            var i = document.createEvent("CustomEvent");
            return i.initCustomEvent(t, e.bubbles, e.cancelable, e.detail), i
        }
    }(), Element.prototype.matches || (Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector);
    var e = function () {
        function t(t) {
            return this.cssVenderPrefixes = ["TransitionDuration", "TransitionTimingFunction", "Transform", "Transition"], this.selector = this._getSelector(t), this.firstElement = this._getFirstEl(), this
        }
        return t.generateUUID = function () {
            return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(/[xy]/g, (function (t) {
                var e = 16 * Math.random() | 0;
                return ("x" == t ? e : 3 & e | 8).toString(16)
            }))
        }, t.prototype._getSelector = function (t, e) {
            return void 0 === e && (e = document), "string" != typeof t ? t : (e = e || document, "#" === t.substring(0, 1) ? e.querySelector(t) : e.querySelectorAll(t))
        }, t.prototype._each = function (t) {
            return this.selector ? (void 0 !== this.selector.length ? [].forEach.call(this.selector, t) : t(this.selector, 0), this) : this
        }, t.prototype._setCssVendorPrefix = function (t, e, i) {
            var s = e.replace(/-([a-z])/gi, (function (t, e) {
                return e.toUpperCase()
            })); - 1 !== this.cssVenderPrefixes.indexOf(s) ? (t.style[s.charAt(0).toLowerCase() + s.slice(1)] = i, t.style["webkit" + s] = i, t.style["moz" + s] = i, t.style["ms" + s] = i, t.style["o" + s] = i) : t.style[s] = i
        }, t.prototype._getFirstEl = function () {
            return this.selector && void 0 !== this.selector.length ? this.selector[0] : this.selector
        }, t.prototype.isEventMatched = function (t, e) {
            var i = e.split(".");
            return t.split(".").filter((function (t) {
                return t
            })).every((function (t) {
                return -1 !== i.indexOf(t)
            }))
        }, t.prototype.attr = function (t, e) {
            return void 0 === e ? this.firstElement ? this.firstElement.getAttribute(t) : "" : (this._each((function (i) {
                i.setAttribute(t, e)
            })), this)
        }, t.prototype.find = function (t) {
            return i(this._getSelector(t, this.selector))
        }, t.prototype.first = function () {
            return this.selector && void 0 !== this.selector.length ? i(this.selector[0]) : i(this.selector)
        }, t.prototype.eq = function (t) {
            return i(this.selector[t])
        }, t.prototype.parent = function () {
            return i(this.selector.parentElement)
        }, t.prototype.get = function () {
            return this._getFirstEl()
        }, t.prototype.removeAttr = function (t) {
            var e = t.split(" ");
            return this._each((function (t) {
                e.forEach((function (e) {
                    return t.removeAttribute(e)
                }))
            })), this
        }, t.prototype.wrap = function (t) {
            if (!this.firstElement) return this;
            var e = document.createElement("div");
            return e.className = t, this.firstElement.parentNode.insertBefore(e, this.firstElement), this.firstElement.parentNode.removeChild(this.firstElement), e.appendChild(this.firstElement), this
        }, t.prototype.addClass = function (t) {
            return void 0 === t && (t = ""), this._each((function (e) {
                t.split(" ").forEach((function (t) {
                    e.classList.add(t)
                }))
            })), this
        }, t.prototype.removeClass = function (t) {
            return this._each((function (e) {
                t.split(" ").forEach((function (t) {
                    e.classList.remove(t)
                }))
            })), this
        }, t.prototype.hasClass = function (t) {
            return !!this.firstElement && this.firstElement.classList.contains(t)
        }, t.prototype.hasAttribute = function (t) {
            return !!this.firstElement && this.firstElement.hasAttribute(t)
        }, t.prototype.toggleClass = function (t) {
            return this.firstElement ? (this.hasClass(t) ? this.removeClass(t) : this.addClass(t), this) : this
        }, t.prototype.css = function (t, e) {
            var i = this;
            return this._each((function (s) {
                i._setCssVendorPrefix(s, t, e)
            })), this
        }, t.prototype.on = function (e, i) {
            var s = this;
            return this.selector ? (e.split(" ").forEach((function (e) {
                Array.isArray(t.eventListeners[e]) || (t.eventListeners[e] = []), t.eventListeners[e].push(i), s.selector.addEventListener(e.split(".")[0], i)
            })), this) : this
        }, t.prototype.once = function (t, e) {
            var i = this;
            return this.on(t, (function () {
                i.off(t), e(t)
            })), this
        }, t.prototype.off = function (e) {
            var i = this;
            return this.selector ? (Object.keys(t.eventListeners).forEach((function (s) {
                i.isEventMatched(e, s) && (t.eventListeners[s].forEach((function (t) {
                    i.selector.removeEventListener(s.split(".")[0], t)
                })), t.eventListeners[s] = [])
            })), this) : this
        }, t.prototype.trigger = function (t, e) {
            if (!this.firstElement) return this;
            var i = new CustomEvent(t.split(".")[0], {
                detail: e || null
            });
            return this.firstElement.dispatchEvent(i), this
        }, t.prototype.load = function (t) {
            var e = this;
            return fetch(t).then((function (t) {
                e.selector.innerHTML = t
            })), this
        }, t.prototype.html = function (t) {
            return void 0 === t ? this.firstElement ? this.firstElement.innerHTML : "" : (this._each((function (e) {
                e.innerHTML = t
            })), this)
        }, t.prototype.append = function (t) {
            return this._each((function (e) {
                "string" == typeof t ? e.insertAdjacentHTML("beforeend", t) : e.appendChild(t)
            })), this
        }, t.prototype.prepend = function (t) {
            return this._each((function (e) {
                e.insertAdjacentHTML("afterbegin", t)
            })), this
        }, t.prototype.remove = function () {
            return this._each((function (t) {
                t.parentNode.removeChild(t)
            })), this
        }, t.prototype.empty = function () {
            return this._each((function (t) {
                t.innerHTML = ""
            })), this
        }, t.prototype.scrollTop = function (t) {
            return void 0 !== t ? (document.body.scrollTop = t, document.documentElement.scrollTop = t, this) : window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0
        }, t.prototype.scrollLeft = function (t) {
            return void 0 !== t ? (document.body.scrollLeft = t, document.documentElement.scrollLeft = t, this) : window.pageXOffset || document.documentElement.scrollLeft || document.body.scrollLeft || 0
        }, t.prototype.offset = function () {
            if (!this.firstElement) return {
                left: 0,
                top: 0
            };
            var t = this.firstElement.getBoundingClientRect(),
                e = i("body").style().marginLeft;
            return {
                left: t.left - parseFloat(e) + this.scrollLeft(),
                top: t.top + this.scrollTop()
            }
        }, t.prototype.style = function () {
            return this.firstElement ? this.firstElement.currentStyle || window.getComputedStyle(this.firstElement) : {}
        }, t.prototype.width = function () {
            var t = this.style();
            return this.firstElement.clientWidth - parseFloat(t.paddingLeft) - parseFloat(t.paddingRight)
        }, t.prototype.height = function () {
            var t = this.style();
            return this.firstElement.clientHeight - parseFloat(t.paddingTop) - parseFloat(t.paddingBottom)
        }, t.eventListeners = {}, t
    }();

    function i(t) {
        return new e(t)
    }
    var s = ["src", "sources", "subHtml", "subHtmlUrl", "html", "video", "poster", "slideName", "responsive", "srcset", "sizes", "iframe", "downloadUrl", "width", "facebookShareUrl", "tweetText", "iframeTitle", "twitterShareUrl", "pinterestShareUrl", "pinterestText", "fbHtml", "disqusIdentifier", "disqusUrl"];

    function o(t) {
        return "href" === t ? "src" : t = (t = (t = t.replace("data-", "")).charAt(0).toLowerCase() + t.slice(1)).replace(/-([a-z])/g, (function (t) {
            return t[1].toUpperCase()
        }))
    }
    var n = function (t, e, s, o) {
            void 0 === s && (s = 0);
            var n = i(t).attr("data-lg-size") || o;
            if (n) {
                var r = n.split(",");
                if (r[1])
                    for (var l = window.innerWidth, a = 0; a < r.length; a++) {
                        var g = r[a];
                        if (parseInt(g.split("-")[2], 10) > l) {
                            n = g;
                            break
                        }
                        a === r.length - 1 && (n = g)
                    }
                var d = n.split("-"),
                    h = parseInt(d[0], 10),
                    c = parseInt(d[1], 10),
                    m = e.width(),
                    u = e.height() - s,
                    p = Math.min(m, h),
                    f = Math.min(u, c),
                    v = Math.min(p / h, f / c);
                return {
                    width: h * v,
                    height: c * v
                }
            }
        },
        r = function (t, e, s, o, n) {
            if (n) {
                var r = i(t).find("img").first(),
                    l = e.get().getBoundingClientRect(),
                    a = l.width,
                    g = e.height() - (s + o),
                    d = r.width(),
                    h = r.height(),
                    c = r.style(),
                    m = (a - d) / 2 - r.offset().left + (parseFloat(c.paddingLeft) || 0) + (parseFloat(c.borderLeft) || 0) + i(window).scrollLeft() + l.left,
                    u = (g - h) / 2 - r.offset().top + (parseFloat(c.paddingTop) || 0) + (parseFloat(c.borderTop) || 0) + i(window).scrollTop() + s;
                return "translate3d(" + (m *= -1) + "px, " + (u *= -1) + "px, 0) scale3d(" + d / n.width + ", " + h / n.height + ", 1)"
            }
        },
        l = function (t, e, i, s) {
            return '<div class="lg-video-cont lg-has-iframe" style="width:' + e + "; height: " + i + '">\n                    <iframe class="lg-object" frameborder="0" ' + (s ? 'title="' + s + '"' : "") + ' src="' + t + '"  allowfullscreen="true"></iframe>\n                </div>'
        },
        a = function (t, e, i, s, o, n) {
            var r = "<img " + i + " " + (s ? "srcset=" + s : "") + "  " + (o ? "sizes=" + o : "") + ' class="lg-object lg-image" data-index="' + t + '" src="' + e + '" />',
                l = "";
            n && (l = ("string" == typeof n ? JSON.parse(n) : n).map((function (t) {
                var e = "";
                return Object.keys(t).forEach((function (i) {
                    e += " " + i + '="' + t[i] + '"'
                })), "<source " + e + "></source>"
            })));
            return "" + l + r
        },
        g = function (t) {
            for (var e = [], i = [], s = "", o = 0; o < t.length; o++) {
                var n = t[o].split(" ");
                "" === n[0] && n.splice(0, 1), i.push(n[0]), e.push(n[1])
            }
            for (var r = window.innerWidth, l = 0; l < e.length; l++)
                if (parseInt(e[l], 10) > r) {
                    s = i[l];
                    break
                } return s
        },
        d = function (t) {
            return !!t && (!!t.complete && 0 !== t.naturalWidth)
        },
        h = function (t, e, i, s) {
            return '<div class="lg-video-cont ' + (s && s.youtube ? "lg-has-youtube" : s && s.vimeo ? "lg-has-vimeo" : "lg-has-html5") + '" style="' + i + '">\n                <div class="lg-video-play-button">\n                <svg\n                    viewBox="0 0 20 20"\n                    preserveAspectRatio="xMidYMid"\n                    focusable="false"\n                    aria-labelledby="Play video"\n                    role="img"\n                    class="lg-video-play-icon"\n                >\n                    <title>Play video</title>\n                    <polygon class="lg-video-play-icon-inner" points="1,0 20,10 1,20"></polygon>\n                </svg>\n                <svg class="lg-video-play-icon-bg" viewBox="0 0 50 50" focusable="false">\n                    <circle cx="50%" cy="50%" r="20"></circle></svg>\n                <svg class="lg-video-play-icon-circle" viewBox="0 0 50 50" focusable="false">\n                    <circle cx="50%" cy="50%" r="20"></circle>\n                </svg>\n            </div>\n            ' + (e || "") + '\n            <img class="lg-object lg-video-poster" src="' + t + '" />\n        </div>'
        },
        c = function (t, e, n, r) {
            var l = [],
                a = function () {
                    for (var t = 0, e = 0, i = arguments.length; e < i; e++) t += arguments[e].length;
                    var s = Array(t),
                        o = 0;
                    for (e = 0; e < i; e++)
                        for (var n = arguments[e], r = 0, l = n.length; r < l; r++, o++) s[o] = n[r];
                    return s
                }(s, e);
            return [].forEach.call(t, (function (t) {
                for (var e = {}, s = 0; s < t.attributes.length; s++) {
                    var g = t.attributes[s];
                    if (g.specified) {
                        var d = o(g.name),
                            h = "";
                        a.indexOf(d) > -1 && (h = d), h && (e[h] = g.value)
                    }
                }
                var c = i(t),
                    m = c.find("img").first().attr("alt"),
                    u = c.attr("title"),
                    p = r ? c.attr(r) : c.find("img").first().attr("src");
                e.thumb = p, n && !e.subHtml && (e.subHtml = u || m || ""), e.alt = m || u || "", l.push(e)
            })), l
        },
        m = function () {
            var t, e = !1;
            return t = navigator.userAgent || navigator.vendor || window.opera, (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(t) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(t.substr(0, 4))) && (e = !0), e
        },
        u = {
            mode: "lg-slide",
            easing: "ease",
            speed: 400,
            height: "100%",
            width: "100%",
            addClass: "",
            startClass: "lg-start-zoom",
            backdropDuration: 300,
            container: document.body,
            startAnimationDuration: 400,
            zoomFromOrigin: !0,
            hideBarsDelay: 0,
            showBarsAfter: 1e4,
            slideDelay: 0,
            supportLegacyBrowser: !0,
            allowMediaOverlap: !1,
            videoMaxSize: "1280-720",
            defaultCaptionHeight: 0,
            ariaLabelledby: "",
            ariaDescribedby: "",
            closable: !0,
            swipeToClose: !0,
            closeOnTap: !0,
            showCloseIcon: !0,
            showMaximizeIcon: !1,
            loop: !0,
            escKey: !0,
            keyPress: !0,
            controls: !0,
            slideEndAnimation: !0,
            hideControlOnEnd: !1,
            mousewheel: !1,
            getCaptionFromTitleOrAlt: !0,
            appendSubHtmlTo: ".lg-sub-html",
            subHtmlSelectorRelative: !1,
            preload: 2,
            numberOfSlideItemsInDom: 10,
            showAfterLoad: !0,
            selector: "",
            selectWithin: "",
            nextHtml: "",
            prevHtml: "",
            index: 0,
            iframeWidth: "100%",
            iframeHeight: "100%",
            download: !0,
            counter: !0,
            appendCounterTo: ".lg-toolbar",
            swipeThreshold: 50,
            enableSwipe: !0,
            enableDrag: !0,
            dynamic: !1,
            dynamicEl: [],
            extraProps: [],
            galleryId: "1",
            customSlideName: !1,
            exThumbImage: "",
            isMobile: void 0,
            mobileSettings: {
                controls: !1,
                showCloseIcon: !1,
                download: !1
            },
            plugins: []
        },
        p = "lgAfterAppendSlide",
        f = "lgInit",
        v = "lgHasVideo",
        y = "lgContainerResize",
        b = "lgUpdateSlides",
        I = "lgAfterAppendSubHtml",
        C = "lgBeforeOpen",
        w = "lgAfterOpen",
        x = "lgSlideItemLoad",
        S = "lgBeforeSlide",
        T = "lgAfterSlide",
        E = "lgPosterClick",
        O = "lgDragStart",
        z = "lgDragMove",
        k = "lgDragEnd",
        D = "lgBeforeNextSlide",
        L = "lgBeforePrevSlide",
        G = "lgBeforeClose",
        M = "lgAfterClose",
        A = 0,
        B = function () {
            function s(e, s) {
                if (void 0 === s && (s = {}), this.lgOpened = !1, this.index = 0, this.plugins = [], this.lGalleryOn = !1, this.lgBusy = !1, this.currentItemsInDom = [], this.prevScrollTop = 0, this.isDummyImageRemoved = !1, this.mediaContainerPosition = {
                        top: 0,
                        bottom: 0
                    }, A++, this.lgId = A, this.el = e, this.LGel = i(e), this.settings = t(t({}, u), s), this.settings.isMobile && "function" == typeof this.settings.isMobile ? this.settings.isMobile() : m()) {
                    var o = t(t({}, this.settings.mobileSettings), s.mobileSettings);
                    this.settings = t(t({}, this.settings), o)
                }
                if (this.settings.dynamic && void 0 !== this.settings.dynamicEl && !Array.isArray(this.settings.dynamicEl)) throw "When using dynamic mode, you must also define dynamicEl as an Array.";
                return this.settings.slideEndAnimation && (this.settings.hideControlOnEnd = !1), this.settings.closable || (this.settings.swipeToClose = !1), this.zoomFromOrigin = this.settings.zoomFromOrigin, this.galleryItems = this.getItems(), this.settings.dynamic && (this.zoomFromOrigin = !1), this.settings.preload = Math.min(this.settings.preload, this.galleryItems.length), this.init(), this
            }
            return s.prototype.init = function () {
                var t = this;
                if (this.addSlideVideoInfo(this.galleryItems), this.buildFromHash() || this.buildStructure(), this.LGel.trigger(f, {
                        instance: this
                    }), this.settings.keyPress && this.keyPress(), setTimeout((function () {
                        t.enableDrag(), t.enableSwipe()
                    }), 50), this.galleryItems.length > 1 && (this.arrow(), this.settings.mousewheel && this.mousewheel()), !this.settings.dynamic)
                    for (var s = function (s) {
                            var n = o.items[s],
                                r = i(n),
                                l = e.generateUUID();
                            r.attr("data-lg-id", l).on("click.lgcustom-item-" + l, (function (e) {
                                e.preventDefault();
                                var i = t.settings.index || s;
                                t.openGallery(i, n)
                            }))
                        }, o = this, n = 0; n < this.items.length; n++) s(n)
            }, s.prototype.buildModules = function () {
                var t = this,
                    e = 0;
                return this.settings.plugins.forEach((function (s) {
                    e++, setTimeout((function () {
                        t.plugins.push(new s(t, i))
                    }), 10 * e)
                })), 10 * e
            }, s.prototype.getSlideItem = function (t) {
                return i(this.getSlideItemId(t))
            }, s.prototype.getSlideItemId = function (t) {
                return "#lg-item-" + this.lgId + "-" + t
            }, s.prototype.getIdName = function (t) {
                return t + "-" + this.lgId
            }, s.prototype.getElementById = function (t) {
                return i("#" + this.getIdName(t))
            }, s.prototype.buildStructure = function () {
                var t = this;
                if (this.$container && this.$container.get()) return 0;
                var e = "",
                    s = "";
                this.settings.controls && this.galleryItems.length > 1 && (e = '<button type="button" id="' + this.getIdName("lg-prev") + '" aria-label="Previous slide" class="lg-prev lg-icon"> ' + this.settings.prevHtml + ' </button>\n                <button type="button" id="' + this.getIdName("lg-next") + '" aria-label="Next slide" class="lg-next lg-icon"> ' + this.settings.nextHtml + " </button>"), ".lg-sub-html" === this.settings.appendSubHtmlTo && (s = '<div class="lg-sub-html" role="status" aria-live="polite"></div>');
                var o = "";
                this.settings.allowMediaOverlap && (o += "lg-media-overlap ");
                var n = this.settings.ariaLabelledby ? 'aria-labelledby="' + this.settings.ariaLabelledby + '"' : "",
                    r = this.settings.ariaDescribedby ? 'aria-describedby="' + this.settings.ariaDescribedby + '"' : "",
                    l = "lg-container " + this.settings.addClass + " " + (document.body !== this.settings.container ? "lg-inline" : ""),
                    a = this.settings.closable && this.settings.showCloseIcon ? '<button type="button" aria-label="Close gallery" id="' + this.getIdName("lg-close") + '" class="lg-close lg-icon"></button>' : "",
                    g = this.settings.showMaximizeIcon ? '<button type="button" aria-label="Toggle maximize" id="' + this.getIdName("lg-maximize") + '" class="lg-maximize lg-icon"></button>' : "",
                    d = '\n        <div class="' + l + '" id="' + this.getIdName("lg-container") + '" tabindex="-1" aria-modal="true" ' + n + " " + r + ' role="dialog"\n        >\n            <div id="' + this.getIdName("lg-backdrop") + '" class="lg-backdrop"></div>\n\n            <div id="' + this.getIdName("lg-outer") + '" class="lg-outer lg-hide-items ' + o + ' ">\n                    <div id="' + this.getIdName("lg-content") + '" class="lg" style="width: ' + this.settings.width + "; height:" + this.settings.height + '">\n                        <div id="' + this.getIdName("lg-inner") + '" class="lg-inner"></div>\n                        <div id="' + this.getIdName("lg-toolbar") + '" class="lg-toolbar lg-group">\n                        ' + g + "\n                        " + a + "\n                    </div>\n                    " + e + '\n                    <div id="' + this.getIdName("lg-components") + '" class="lg-components">\n                        ' + s + "\n                    </div>\n                </div> \n            </div>\n        </div>\n        ";
                return i(this.settings.container).css("position", "relative").append(d), this.outer = this.getElementById("lg-outer"), this.$lgContent = this.getElementById("lg-content"), this.$lgComponents = this.getElementById("lg-components"), this.$backdrop = this.getElementById("lg-backdrop"), this.$container = this.getElementById("lg-container"), this.$inner = this.getElementById("lg-inner"), this.$toolbar = this.getElementById("lg-toolbar"), this.$backdrop.css("transition-duration", this.settings.backdropDuration + "ms"), this.outer.addClass("lg-use-css3"), this.outer.addClass("lg-css3"), this.outer.addClass(this.settings.mode), this.settings.enableDrag && this.galleryItems.length > 1 && this.outer.addClass("lg-grab"), this.settings.showAfterLoad && this.outer.addClass("lg-show-after-load"), this.$inner.css("transition-timing-function", this.settings.easing), this.$inner.css("transition-duration", this.settings.speed + "ms"), this.settings.download && this.$toolbar.append('<a id="' + this.getIdName("lg-download") + '" target="_blank" aria-label="Download" download class="lg-download lg-icon"></a>'), this.counter(), i(window).on("resize.lg.global" + this.lgId + " orientationchange.lg.global" + this.lgId, (function () {
                    t.refreshOnResize()
                })), this.hideBars(), this.manageCloseGallery(), this.toggleMaximize(), this.buildModules()
            }, s.prototype.refreshOnResize = function () {
                if (this.lgOpened) {
                    var t = this.galleryItems[this.index].__slideVideoInfo,
                        e = this.getMediaContainerPosition(),
                        i = e.top,
                        s = e.bottom;
                    if (this.currentImageSize = n(this.items[this.index], this.$lgContent, i + s, t && this.settings.videoMaxSize), t && this.resizeVideoSlide(this.index, this.currentImageSize), this.zoomFromOrigin && !this.isDummyImageRemoved) {
                        var o = this.getDummyImgStyles(this.currentImageSize);
                        this.outer.find(".lg-current .lg-dummy-img").first().attr("style", o)
                    }
                    this.LGel.trigger(y)
                }
            }, s.prototype.resizeVideoSlide = function (t, e) {
                var i = this.getVideoContStyle(e);
                this.getSlideItem(t).find(".lg-video-cont").attr("style", i)
            }, s.prototype.updateSlides = function (t, e) {
                if (this.index > t.length - 1 && (this.index = t.length - 1), 1 === t.length && (this.index = 0), t.length) {
                    var i = this.galleryItems[e].src;
                    this.addSlideVideoInfo(t), this.galleryItems = t, this.$inner.empty(), this.currentItemsInDom = [];
                    var s = 0;
                    this.galleryItems.some((function (t, e) {
                        return t.src === i && (s = e, !0)
                    })), this.currentItemsInDom = this.organizeSlideItems(s, -1), this.loadContent(s, !0), this.getSlideItem(s).addClass("lg-current"), this.index = s, this.updateCurrentCounter(s), this.updateCounterTotal(), this.LGel.trigger(b)
                } else this.closeGallery()
            }, s.prototype.getItems = function () {
                if (this.items = [], this.settings.dynamic) return this.settings.dynamicEl || [];
                if ("this" === this.settings.selector) this.items.push(this.el);
                else if (this.settings.selector)
                    if (this.settings.selectWithin) {
                        var t = i(this.settings.selectWithin);
                        this.items = t.find(this.settings.selector).get()
                    } else this.items = this.el.querySelectorAll(this.settings.selector);
                else this.items = this.el.children;
                return c(this.items, this.settings.extraProps, this.settings.getCaptionFromTitleOrAlt, this.settings.exThumbImage)
            }, s.prototype.openGallery = function (t, e) {
                var s = this;
                if (void 0 === t && (t = this.settings.index), !this.lgOpened) {
                    this.lgOpened = !0, this.outer.get().focus(), this.outer.removeClass("lg-hide-items"), this.$container.addClass("lg-show");
                    var o = this.getItemsToBeInsertedToDom(t, t);
                    this.currentItemsInDom = o;
                    var l = "";
                    o.forEach((function (t) {
                        l = l + '<div id="' + t + '" class="lg-item"></div>'
                    })), this.$inner.append(l), this.addHtml(t);
                    var a = "";
                    this.mediaContainerPosition = this.getMediaContainerPosition();
                    var g = this.mediaContainerPosition,
                        d = g.top,
                        h = g.bottom;
                    this.settings.allowMediaOverlap || this.setMediaContainerPosition(d, h), this.zoomFromOrigin && e && (this.currentImageSize = n(e, this.$lgContent, d + h, this.galleryItems[this.index].__slideVideoInfo && this.settings.videoMaxSize), a = r(e, this.$lgContent, d, h, this.currentImageSize)), this.zoomFromOrigin && a || (this.outer.addClass(this.settings.startClass), this.getSlideItem(t).removeClass("lg-complete"));
                    var c = this.settings.zoomFromOrigin ? 100 : this.settings.backdropDuration;
                    setTimeout((function () {
                        s.outer.addClass("lg-components-open")
                    }), c), this.LGel.trigger(C), this.getSlideItem(t).addClass("lg-current"), this.lGalleryOn = !1, this.index = t, this.prevScrollTop = i(window).scrollTop(), setTimeout((function () {
                        if (s.zoomFromOrigin && a) {
                            var e = s.getSlideItem(t);
                            e.css("transform", a), setTimeout((function () {
                                e.addClass("lg-start-progress lg-start-end-progress").css("transition-duration", s.settings.startAnimationDuration + "ms"), s.outer.addClass("lg-zoom-from-image")
                            })), setTimeout((function () {
                                e.css("transform", "translate3d(0, 0, 0)")
                            }), 100)
                        }
                        setTimeout((function () {
                            s.$backdrop.addClass("in"), s.$container.addClass("lg-show-in")
                        }), 10), s.zoomFromOrigin && a || setTimeout((function () {
                            s.outer.addClass("lg-visible")
                        }), s.settings.backdropDuration), s.slide(t, !1, !1, !1), s.LGel.trigger(w)
                    })), i(document.body).addClass("lg-on")
                }
            }, s.prototype.getMediaContainerPosition = function () {
                if (this.settings.allowMediaOverlap) return {
                    top: 0,
                    bottom: 0
                };
                var t = this.$toolbar.get().clientHeight || 0,
                    e = this.settings.defaultCaptionHeight || this.outer.find(".lg-sub-html").get().clientHeight,
                    i = this.outer.find(".lg-thumb-outer").get();
                return {
                    top: t,
                    bottom: (i ? i.clientHeight : 0) + e
                }
            }, s.prototype.setMediaContainerPosition = function (t, e) {
                void 0 === t && (t = 0), void 0 === e && (e = 0), this.$inner.css("top", t + "px").css("bottom", e + "px")
            }, s.prototype.buildFromHash = function () {
                var t = this,
                    e = window.location.hash;
                if (e.indexOf("lg=" + this.settings.galleryId) > 0) {
                    i(document.body).addClass("lg-from-hash"), this.zoomFromOrigin = !1;
                    var s = this.getIndexFromUrl(e),
                        o = this.buildStructure();
                    return setTimeout((function () {
                        t.openGallery(s)
                    }), o), !0
                }
            }, s.prototype.hideBars = function () {
                var t = this;
                setTimeout((function () {
                    t.outer.removeClass("lg-hide-items"), t.settings.hideBarsDelay > 0 && (t.outer.on("mousemove.lg click.lg touchstart.lg", (function () {
                        t.outer.removeClass("lg-hide-items"), clearTimeout(t.hideBarTimeout), t.hideBarTimeout = setTimeout((function () {
                            t.outer.addClass("lg-hide-items")
                        }), t.settings.hideBarsDelay)
                    })), t.outer.trigger("mousemove.lg"))
                }), this.settings.showBarsAfter)
            }, s.prototype.initPictureFill = function (t) {
                if (this.settings.supportLegacyBrowser) try {
                    picturefill({
                        elements: [t.get()]
                    })
                } catch (t) {
                    console.warn("lightGallery :- If you want srcset or picture tag to be supported for older browser please include picturefil javascript library in your document.")
                }
            }, s.prototype.counter = function () {
                if (this.settings.counter) {
                    var t = '<div class="lg-counter" role="status" aria-live="polite">\n                <span id="' + this.getIdName("lg-counter-current") + '" class="lg-counter-current">' + (this.index + 1) + ' </span> / \n                <span id="' + this.getIdName("lg-counter-all") + '" class="lg-counter-all">' + this.galleryItems.length + " </span></div>";
                    this.outer.find(this.settings.appendCounterTo).append(t)
                }
            }, s.prototype.addHtml = function (t) {
                var e, s;
                if (this.galleryItems[t].subHtmlUrl ? s = this.galleryItems[t].subHtmlUrl : e = this.galleryItems[t].subHtml, !s)
                    if (e) {
                        var o = e.substring(0, 1);
                        "." !== o && "#" !== o || (e = this.settings.subHtmlSelectorRelative && !this.settings.dynamic ? i(this.items).eq(t).find(e).first().html() : i(e).first().html())
                    } else e = "";
                if (".lg-sub-html" === this.settings.appendSubHtmlTo) s ? this.outer.find(".lg-sub-html").load(s) : this.outer.find(".lg-sub-html").html(e);
                else {
                    var n = i(this.getSlideItemId(t));
                    s ? n.load(s) : n.append('<div class="lg-sub-html">' + e + "</div>")
                }
                null != e && ("" === e ? this.outer.find(this.settings.appendSubHtmlTo).addClass("lg-empty-html") : this.outer.find(this.settings.appendSubHtmlTo).removeClass("lg-empty-html")), this.LGel.trigger(I, {
                    index: t
                })
            }, s.prototype.preload = function (t) {
                for (var e = 1; e <= this.settings.preload && !(e >= this.galleryItems.length - t); e++) this.loadContent(t + e, !1);
                for (var i = 1; i <= this.settings.preload && !(t - i < 0); i++) this.loadContent(t - i, !1)
            }, s.prototype.getDummyImgStyles = function (t) {
                return t ? "width:" + t.width + "px; \n                margin-left: -" + t.width / 2 + "px;\n                margin-top: -" + t.height / 2 + "px; \n                height:" + t.height + "px" : ""
            }, s.prototype.getVideoContStyle = function (t) {
                return t ? "width:" + t.width + "px; \n                height:" + t.height + "px" : ""
            }, s.prototype.getDummyImageContent = function (t, e, s) {
                var o;
                if (this.settings.dynamic || (o = i(this.items).eq(e)), o) {
                    var n = void 0;
                    n = this.settings.exThumbImage ? o.attr(this.settings.exThumbImage) : o.find("img").first().attr("src");
                    var r = "<img " + s + ' style="' + this.getDummyImgStyles(this.currentImageSize) + '" class="lg-dummy-img" src="' + n + '" />';
                    return t.addClass("lg-first-slide"), r
                }
                return ""
            }, s.prototype.setImgMarkup = function (t, e, i) {
                var s = this.galleryItems[i],
                    o = s.alt,
                    n = s.srcset,
                    r = s.sizes,
                    l = s.sources,
                    g = o ? 'alt="' + o + '"' : "",
                    d = '<picture class="lg-img-wrap"> ' + (!this.lGalleryOn && this.zoomFromOrigin && this.currentImageSize ? this.getDummyImageContent(e, i, g) : a(i, t, g, n, r, l)) + "</picture>";
                e.prepend(d)
            }, s.prototype.onLgObjectLoad = function (t, e, i, s, o) {
                var n = this;
                o && this.LGel.trigger(x, {
                    index: e,
                    delay: i || 0
                }), t.find(".lg-object").first().on("load.lg", (function () {
                    n.handleLgObjectLoad(t, e, i, s, o)
                })), setTimeout((function () {
                    t.find(".lg-object").first().on("error.lg", (function () {
                        t.addClass("lg-complete lg-complete_"), t.html('<span class="lg-error-msg">Oops... Failed to load content...</span>')
                    }))
                }), s)
            }, s.prototype.handleLgObjectLoad = function (t, e, i, s, o) {
                var n = this;
                setTimeout((function () {
                    t.addClass("lg-complete lg-complete_"), o || n.LGel.trigger(x, {
                        index: e,
                        delay: i || 0
                    })
                }), s)
            }, s.prototype.isVideo = function (t, e) {
                if (!t) return this.galleryItems[e].video ? {
                    html5: !0
                } : void console.error("lightGallery :- data-src is not provided on slide item " + (e + 1) + ". Please make sure the selector property is properly configured. More info - http://sachinchoolur.github.io/lightGallery/demos/html-markup.html");
                var i = t.match(/\/\/(?:www\.)?youtu(?:\.be|be\.com|be-nocookie\.com)\/(?:watch\?v=|embed\/)?([a-z0-9\-\_\%]+)/i),
                    s = t.match(/\/\/(?:www\.)?(?:player\.)?vimeo.com\/(?:video\/)?([0-9a-z\-_]+)/i),
                    o = t.match(/https?:\/\/(.+)?(wistia\.com|wi\.st)\/(medias|embed)\/([0-9a-z\-_]+)(.*)/);
                return i ? {
                    youtube: i
                } : s ? {
                    vimeo: s
                } : o ? {
                    wistia: o
                } : void 0
            }, s.prototype.addSlideVideoInfo = function (t) {
                var e = this;
                t.forEach((function (t, i) {
                    t.__slideVideoInfo = e.isVideo(t.src, i)
                }))
            }, s.prototype.loadContent = function (t, e) {
                var s = this,
                    o = this.galleryItems[t],
                    r = i(this.getSlideItemId(t)),
                    c = o.poster,
                    m = o.srcset,
                    u = o.sizes,
                    f = o.sources,
                    y = o.src,
                    b = o.video,
                    I = b && "string" == typeof b ? JSON.parse(b) : b;
                if (o.responsive) {
                    var C = o.responsive.split(",");
                    y = g(C) || y
                }
                var w = o.__slideVideoInfo,
                    x = "",
                    S = !!o.iframe;
                if (!r.hasClass("lg-loaded")) {
                    if (w) {
                        var T = this.mediaContainerPosition,
                            E = T.top,
                            O = T.bottom,
                            z = n(this.items[t], this.$lgContent, E + O, w && this.settings.videoMaxSize);
                        x = this.getVideoContStyle(z)
                    }
                    if (S) {
                        var k = l(y, this.settings.iframeWidth, this.settings.iframeHeight, o.iframeTitle);
                        r.prepend(k)
                    } else if (c) {
                        var D = "",
                            L = !this.lGalleryOn,
                            G = !this.lGalleryOn && this.zoomFromOrigin && this.currentImageSize;
                        G && (D = this.getDummyImageContent(r, t, ""));
                        k = h(c, D || "", x, w);
                        r.prepend(k);
                        var M = (G ? this.settings.startAnimationDuration : this.settings.backdropDuration) + 100;
                        setTimeout((function () {
                            s.LGel.trigger(v, {
                                index: t,
                                src: y,
                                html5Video: I,
                                hasPoster: !0,
                                isFirstSlide: L
                            })
                        }), M)
                    } else if (w) {
                        k = '<div class="lg-video-cont " style="' + x + '"></div>';
                        r.prepend(k), this.LGel.trigger(v, {
                            index: t,
                            src: y,
                            html5Video: I,
                            hasPoster: !1
                        })
                    } else if (this.setImgMarkup(y, r, t), m || f) {
                        var A = r.find(".lg-object");
                        this.initPictureFill(A)
                    }
                    this.LGel.trigger(p, {
                        index: t
                    }), this.lGalleryOn && ".lg-sub-html" !== this.settings.appendSubHtmlTo && this.addHtml(t)
                }
                var B = 0,
                    F = 0;
                this.lGalleryOn || (F = this.zoomFromOrigin && this.currentImageSize ? this.settings.startAnimationDuration + 10 : this.settings.backdropDuration + 10), F && !i(document.body).hasClass("lg-from-hash") && (B = F), !this.lGalleryOn && this.zoomFromOrigin && this.currentImageSize && (setTimeout((function () {
                    r.removeClass("lg-start-end-progress lg-start-progress").removeAttr("style")
                }), this.settings.startAnimationDuration + 100), r.hasClass("lg-loaded") || setTimeout((function () {
                    if (r.find(".lg-img-wrap").append(a(t, y, "", m, u, o.sources)), m || f) {
                        var e = r.find(".lg-object");
                        s.initPictureFill(e)
                    }
                    s.onLgObjectLoad(r, t, F, B, !0);
                    var i = r.find(".lg-object").first();
                    d(i.get()) ? s.loadContentOnLoad(t, r, B) : i.on("load.lg error.lg", (function () {
                        s.loadContentOnLoad(t, r, B)
                    }))
                }), this.settings.startAnimationDuration + 100)), r.addClass("lg-loaded"), this.onLgObjectLoad(r, t, F, B, !1), w && w.html5 && !c && r.addClass("lg-complete lg-complete_"), this.zoomFromOrigin && this.currentImageSize || !r.hasClass("lg-complete_") || this.lGalleryOn || setTimeout((function () {
                    r.addClass("lg-complete")
                }), this.settings.backdropDuration), this.lGalleryOn = !0, !0 === e && (r.hasClass("lg-complete_") ? this.preload(t) : r.find(".lg-object").first().on("load.lg error.lg", (function () {
                    s.preload(t)
                })))
            }, s.prototype.loadContentOnLoad = function (t, e, i) {
                var s = this;
                setTimeout((function () {
                    e.find(".lg-dummy-img").remove(), e.removeClass("lg-first-slide"), s.isDummyImageRemoved = !0, s.preload(t)
                }), i + 300)
            }, s.prototype.getItemsToBeInsertedToDom = function (t, e, i) {
                var s = this;
                void 0 === i && (i = 0);
                var o = [],
                    n = Math.max(i, 3);
                n = Math.min(n, this.galleryItems.length);
                var r = "lg-item-" + this.lgId + "-" + e;
                if (this.galleryItems.length <= 3) return this.galleryItems.forEach((function (t, e) {
                    o.push("lg-item-" + s.lgId + "-" + e)
                })), o;
                if (t < (this.galleryItems.length - 1) / 2) {
                    for (var l = t; l > t - n / 2 && l >= 0; l--) o.push("lg-item-" + this.lgId + "-" + l);
                    var a = o.length;
                    for (l = 0; l < n - a; l++) o.push("lg-item-" + this.lgId + "-" + (t + l + 1))
                } else {
                    for (l = t; l <= this.galleryItems.length - 1 && l < t + n / 2; l++) o.push("lg-item-" + this.lgId + "-" + l);
                    for (a = o.length, l = 0; l < n - a; l++) o.push("lg-item-" + this.lgId + "-" + (t - l - 1))
                }
                return this.settings.loop && (t === this.galleryItems.length - 1 ? o.push("lg-item-" + this.lgId + "-0") : 0 === t && o.push("lg-item-" + this.lgId + "-" + (this.galleryItems.length - 1))), -1 === o.indexOf(r) && o.push("lg-item-" + this.lgId + "-" + e), o
            }, s.prototype.organizeSlideItems = function (t, e) {
                var s = this,
                    o = this.getItemsToBeInsertedToDom(t, e, this.settings.numberOfSlideItemsInDom);
                return o.forEach((function (t) {
                    -1 === s.currentItemsInDom.indexOf(t) && s.$inner.append('<div id="' + t + '" class="lg-item"></div>')
                })), this.currentItemsInDom.forEach((function (t) {
                    -1 === o.indexOf(t) && i("#" + t).remove()
                })), o
            }, s.prototype.getPreviousSlideIndex = function () {
                var t = 0;
                try {
                    var e = this.outer.find(".lg-current").first().attr("id");
                    t = parseInt(e.split("-")[3]) || 0
                } catch (e) {
                    t = 0
                }
                return t
            }, s.prototype.setDownloadValue = function (t) {
                if (this.settings.download) {
                    var e = this.galleryItems[t],
                        i = !1 !== e.downloadUrl && (e.downloadUrl || e.src);
                    i && !e.iframe && this.getElementById("lg-download").attr("href", i)
                }
            }, s.prototype.makeSlideAnimation = function (t, e, i) {
                var s = this;
                this.lGalleryOn && i.addClass("lg-slide-progress"), setTimeout((function () {
                    s.outer.addClass("lg-no-trans"), s.outer.find(".lg-item").removeClass("lg-prev-slide lg-next-slide"), "prev" === t ? (e.addClass("lg-prev-slide"), i.addClass("lg-next-slide")) : (e.addClass("lg-next-slide"), i.addClass("lg-prev-slide")), setTimeout((function () {
                        s.outer.find(".lg-item").removeClass("lg-current"), e.addClass("lg-current"), s.outer.removeClass("lg-no-trans")
                    }), 50)
                }), this.settings.slideDelay)
            }, s.prototype.slide = function (t, e, i, s) {
                var o = this,
                    r = this.getPreviousSlideIndex();
                if (this.currentItemsInDom = this.organizeSlideItems(t, r), !this.lGalleryOn || r !== t) {
                    var l = this.galleryItems.length;
                    if (!this.lgBusy) {
                        this.settings.counter && this.updateCurrentCounter(t);
                        var a = this.getSlideItem(t),
                            g = this.getSlideItem(r),
                            d = this.galleryItems[t],
                            h = d.__slideVideoInfo;
                        if (this.outer.attr("data-lg-slide-type", this.getSlideType(d)), this.setDownloadValue(t), h) {
                            var c = this.mediaContainerPosition,
                                m = c.top,
                                u = c.bottom,
                                p = n(this.items[t], this.$lgContent, m + u, h && this.settings.videoMaxSize);
                            this.resizeVideoSlide(t, p)
                        }
                        if (this.LGel.trigger(S, {
                                prevIndex: r,
                                index: t,
                                fromTouch: !!e,
                                fromThumb: !!i
                            }), this.lgBusy = !0, clearTimeout(this.hideBarTimeout), this.arrowDisable(t), s || (t < r ? s = "prev" : t > r && (s = "next")), e) {
                            this.outer.find(".lg-item").removeClass("lg-prev-slide lg-current lg-next-slide");
                            var f = void 0,
                                v = void 0;
                            l > 2 ? (f = t - 1, v = t + 1, (0 === t && r === l - 1 || t === l - 1 && 0 === r) && (v = 0, f = l - 1)) : (f = 0, v = 1), "prev" === s ? this.getSlideItem(v).addClass("lg-next-slide") : this.getSlideItem(f).addClass("lg-prev-slide"), a.addClass("lg-current")
                        } else this.makeSlideAnimation(s, a, g);
                        this.lGalleryOn || this.loadContent(t, !0), setTimeout((function () {
                            o.lGalleryOn && o.loadContent(t, !0), ".lg-sub-html" === o.settings.appendSubHtmlTo && o.addHtml(t)
                        }), (this.lGalleryOn ? this.settings.speed + 50 : 50) + (e ? 0 : this.settings.slideDelay)), setTimeout((function () {
                            o.lgBusy = !1, g.removeClass("lg-slide-progress"), o.LGel.trigger(T, {
                                prevIndex: r,
                                index: t,
                                fromTouch: e,
                                fromThumb: i
                            })
                        }), (this.lGalleryOn ? this.settings.speed + 100 : 100) + (e ? 0 : this.settings.slideDelay))
                    }
                    this.index = t
                }
            }, s.prototype.updateCurrentCounter = function (t) {
                this.getElementById("lg-counter-current").html(t + 1 + "")
            }, s.prototype.updateCounterTotal = function () {
                this.getElementById("lg-counter-all").html(this.galleryItems.length + "")
            }, s.prototype.getSlideType = function (t) {
                return t.__slideVideoInfo ? "video" : t.iframe ? "iframe" : "image"
            }, s.prototype.touchMove = function (t, e) {
                var i = e.pageX - t.pageX,
                    s = e.pageY - t.pageY,
                    o = !1;
                if (this.swipeDirection ? o = !0 : Math.abs(i) > 15 ? (this.swipeDirection = "horizontal", o = !0) : Math.abs(s) > 15 && (this.swipeDirection = "vertical", o = !0), o) {
                    var n = this.getSlideItem(this.index);
                    if ("horizontal" === this.swipeDirection) {
                        this.outer.addClass("lg-dragging"), this.setTranslate(n, i, 0);
                        var r = n.get().offsetWidth,
                            l = 15 * r / 100 - Math.abs(10 * i / 100);
                        this.setTranslate(this.outer.find(".lg-prev-slide").first(), -r + i - l, 0), this.setTranslate(this.outer.find(".lg-next-slide").first(), r + i + l, 0)
                    } else if ("vertical" === this.swipeDirection && this.settings.swipeToClose) {
                        this.$container.addClass("lg-dragging-vertical");
                        var a = 1 - Math.abs(s) / window.innerHeight;
                        this.$backdrop.css("opacity", a);
                        var g = 1 - Math.abs(s) / (2 * window.innerWidth);
                        this.setTranslate(n, 0, s, g, g), Math.abs(s) > 100 && this.outer.addClass("lg-hide-items").removeClass("lg-components-open")
                    }
                }
            }, s.prototype.touchEnd = function (t, e, s) {
                var o, n = this;
                "lg-slide" !== this.settings.mode && this.outer.addClass("lg-slide"), setTimeout((function () {
                    n.$container.removeClass("lg-dragging-vertical"), n.outer.removeClass("lg-dragging lg-hide-items").addClass("lg-components-open");
                    var r = !0;
                    if ("horizontal" === n.swipeDirection) {
                        o = t.pageX - e.pageX;
                        var l = Math.abs(t.pageX - e.pageX);
                        o < 0 && l > n.settings.swipeThreshold ? (n.goToNextSlide(!0), r = !1) : o > 0 && l > n.settings.swipeThreshold && (n.goToPrevSlide(!0), r = !1)
                    } else if ("vertical" === n.swipeDirection) {
                        if (o = Math.abs(t.pageY - e.pageY), n.settings.closable && n.settings.swipeToClose && o > 100) return void n.closeGallery();
                        n.$backdrop.css("opacity", 1)
                    }
                    if (n.outer.find(".lg-item").removeAttr("style"), r && Math.abs(t.pageX - e.pageX) < 5) {
                        var a = i(s.target);
                        n.isPosterElement(a) && n.LGel.trigger(E)
                    }
                    n.swipeDirection = void 0
                })), setTimeout((function () {
                    n.outer.hasClass("lg-dragging") || "lg-slide" === n.settings.mode || n.outer.removeClass("lg-slide")
                }), this.settings.speed + 100)
            }, s.prototype.enableSwipe = function () {
                var t = this,
                    e = {},
                    s = {},
                    o = !1,
                    n = !1;
                this.settings.enableSwipe && (this.$inner.on("touchstart.lg", (function (s) {
                    s.preventDefault();
                    var o = t.getSlideItem(t.index);
                    !i(s.target).hasClass("lg-item") && !o.get().contains(s.target) || t.outer.hasClass("lg-zoomed") || t.lgBusy || 1 !== s.targetTouches.length || (n = !0, t.touchAction = "swipe", t.manageSwipeClass(), e = {
                        pageX: s.targetTouches[0].pageX,
                        pageY: s.targetTouches[0].pageY
                    })
                })), this.$inner.on("touchmove.lg", (function (i) {
                    i.preventDefault(), n && "swipe" === t.touchAction && 1 === i.targetTouches.length && (s = {
                        pageX: i.targetTouches[0].pageX,
                        pageY: i.targetTouches[0].pageY
                    }, t.touchMove(e, s), o = !0)
                })), this.$inner.on("touchend.lg", (function (r) {
                    if ("swipe" === t.touchAction) {
                        if (o) o = !1, t.touchEnd(s, e, r);
                        else if (n) {
                            var l = i(r.target);
                            t.isPosterElement(l) && t.LGel.trigger(E)
                        }
                        t.touchAction = void 0, n = !1
                    }
                })))
            }, s.prototype.enableDrag = function () {
                var t = this,
                    e = {},
                    s = {},
                    o = !1,
                    n = !1;
                this.settings.enableDrag && (this.outer.on("mousedown.lg", (function (s) {
                    var n = t.getSlideItem(t.index);
                    (i(s.target).hasClass("lg-item") || n.get().contains(s.target)) && (t.outer.hasClass("lg-zoomed") || t.lgBusy || (s.preventDefault(), t.lgBusy || (t.manageSwipeClass(), e = {
                        pageX: s.pageX,
                        pageY: s.pageY
                    }, o = !0, t.outer.get().scrollLeft += 1, t.outer.get().scrollLeft -= 1, t.outer.removeClass("lg-grab").addClass("lg-grabbing"), t.LGel.trigger(O))))
                })), i(window).on("mousemove.lg.global" + this.lgId, (function (i) {
                    o && t.lgOpened && (n = !0, s = {
                        pageX: i.pageX,
                        pageY: i.pageY
                    }, t.touchMove(e, s), t.LGel.trigger(z))
                })), i(window).on("mouseup.lg.global" + this.lgId, (function (r) {
                    if (t.lgOpened) {
                        var l = i(r.target);
                        n ? (n = !1, t.touchEnd(s, e, r), t.LGel.trigger(k)) : t.isPosterElement(l) && t.LGel.trigger(E), o && (o = !1, t.outer.removeClass("lg-grabbing").addClass("lg-grab"))
                    }
                })))
            }, s.prototype.manageSwipeClass = function () {
                var t = this.index + 1,
                    e = this.index - 1;
                this.settings.loop && this.galleryItems.length > 2 && (0 === this.index ? e = this.galleryItems.length - 1 : this.index === this.galleryItems.length - 1 && (t = 0)), this.outer.find(".lg-item").removeClass("lg-next-slide lg-prev-slide"), e > -1 && this.getSlideItem(e).addClass("lg-prev-slide"), this.getSlideItem(t).addClass("lg-next-slide")
            }, s.prototype.goToNextSlide = function (t) {
                var e = this,
                    i = this.settings.loop;
                t && this.galleryItems.length < 3 && (i = !1), this.lgBusy || (this.index + 1 < this.galleryItems.length ? (this.index++, this.LGel.trigger(D, {
                    index: this.index
                }), this.slide(this.index, !!t, !1, "next")) : i ? (this.index = 0, this.LGel.trigger(D, {
                    index: this.index
                }), this.slide(this.index, !!t, !1, "next")) : this.settings.slideEndAnimation && !t && (this.outer.addClass("lg-right-end"), setTimeout((function () {
                    e.outer.removeClass("lg-right-end")
                }), 400)))
            }, s.prototype.goToPrevSlide = function (t) {
                var e = this,
                    i = this.settings.loop;
                t && this.galleryItems.length < 3 && (i = !1), this.lgBusy || (this.index > 0 ? (this.index--, this.LGel.trigger(L, {
                    index: this.index,
                    fromTouch: t
                }), this.slide(this.index, !!t, !1, "prev")) : i ? (this.index = this.galleryItems.length - 1, this.LGel.trigger(L, {
                    index: this.index,
                    fromTouch: t
                }), this.slide(this.index, !!t, !1, "prev")) : this.settings.slideEndAnimation && !t && (this.outer.addClass("lg-left-end"), setTimeout((function () {
                    e.outer.removeClass("lg-left-end")
                }), 400)))
            }, s.prototype.keyPress = function () {
                var t = this;
                i(window).on("keydown.lg.global" + this.lgId, (function (e) {
                    t.lgOpened && !0 === t.settings.escKey && 27 === e.keyCode && (e.preventDefault(), t.settings.allowMediaOverlap && t.outer.hasClass("lg-can-toggle") && t.outer.hasClass("lg-components-open") ? t.outer.removeClass("lg-components-open") : t.closeGallery()), t.lgOpened && t.galleryItems.length > 1 && (37 === e.keyCode && (e.preventDefault(), t.goToPrevSlide()), 39 === e.keyCode && (e.preventDefault(), t.goToNextSlide()))
                }))
            }, s.prototype.arrow = function () {
                var t = this;
                this.getElementById("lg-prev").on("click.lg", (function () {
                    t.goToPrevSlide()
                })), this.getElementById("lg-next").on("click.lg", (function () {
                    t.goToNextSlide()
                }))
            }, s.prototype.arrowDisable = function (t) {
                if (!this.settings.loop && this.settings.hideControlOnEnd) {
                    var e = this.getElementById("lg-prev"),
                        i = this.getElementById("lg-next");
                    t + 1 < this.galleryItems.length ? e.removeAttr("disabled").removeClass("disabled") : e.attr("disabled", "disabled").addClass("disabled"), t > 0 ? i.removeAttr("disabled").removeClass("disabled") : i.attr("disabled", "disabled").addClass("disabled")
                }
            }, s.prototype.getIndexFromUrl = function (t) {
                void 0 === t && (t = window.location.hash);
                var e = t.split("&slide=")[1],
                    i = 0;
                if (this.settings.customSlideName)
                    for (var s = 0; s < this.galleryItems.length; s++) {
                        if (this.galleryItems[s].slideName === e) {
                            i = s;
                            break
                        }
                    } else i = parseInt(e, 10);
                return isNaN(i) ? 0 : i
            }, s.prototype.setTranslate = function (t, e, i, s, o) {
                void 0 === s && (s = 1), void 0 === o && (o = 1), t.css("transform", "translate3d(" + e + "px, " + i + "px, 0px) scale3d(" + s + ", " + o + ", 1)")
            }, s.prototype.mousewheel = function () {
                var t = this;
                this.outer.on("mousewheel.lg", (function (e) {
                    e.deltaY && (e.deltaY > 0 ? t.goToPrevSlide() : t.goToNextSlide(), e.preventDefault())
                }))
            }, s.prototype.isSlideElement = function (t) {
                return t.hasClass("lg-outer") || t.hasClass("lg-item") || t.hasClass("lg-img-wrap")
            }, s.prototype.isPosterElement = function (t) {
                var e = this.getSlideItem(this.index).find(".lg-video-play-button").get();
                return t.hasClass("lg-video-poster") || t.hasClass("lg-video-play-button") || e && e.contains(t.get())
            }, s.prototype.toggleMaximize = function () {
                var t = this;
                this.getElementById("lg-maximize").on("click.lg", (function () {
                    t.$container.toggleClass("lg-inline"), t.refreshOnResize()
                }))
            }, s.prototype.manageCloseGallery = function () {
                var t = this;
                if (this.settings.closable) {
                    var e = !1;
                    this.getElementById("lg-close").on("click.lg", (function () {
                        t.closeGallery()
                    })), this.settings.closeOnTap && (this.outer.on("mousedown.lg", (function (s) {
                        var o = i(s.target);
                        e = !!t.isSlideElement(o)
                    })), this.outer.on("mousemove.lg", (function () {
                        e = !1
                    })), this.outer.on("mouseup.lg", (function (s) {
                        var o = i(s.target);
                        t.isSlideElement(o) && e && (t.outer.hasClass("lg-dragging") || t.closeGallery())
                    })))
                }
            }, s.prototype.closeGallery = function (t) {
                var e = this;
                if (!this.lgOpened || !this.settings.closable && !t) return 0;
                this.LGel.trigger(G), i(window).scrollTop(this.prevScrollTop);
                var s, o = this.items[this.index];
                if (this.zoomFromOrigin && o) {
                    var l = this.mediaContainerPosition,
                        a = l.top,
                        g = l.bottom,
                        d = n(o, this.$lgContent, a + g, this.galleryItems[this.index].__slideVideoInfo && this.settings.videoMaxSize);
                    s = r(o, this.$lgContent, a, g, d)
                }
                this.zoomFromOrigin && s ? (this.outer.addClass("lg-closing lg-zoom-from-image"), this.getSlideItem(this.index).addClass("lg-start-end-progress").css("transition-duration", this.settings.startAnimationDuration + "ms").css("transform", s)) : (this.outer.addClass("lg-hide-items"), this.outer.removeClass("lg-zoom-from-image")), this.destroyModules(), this.lGalleryOn = !1, this.isDummyImageRemoved = !1, this.zoomFromOrigin = this.settings.zoomFromOrigin, clearTimeout(this.hideBarTimeout), this.hideBarTimeout = !1, i(document.body).removeClass("lg-on lg-from-hash"), this.outer.removeClass("lg-visible lg-components-open"), this.$backdrop.removeClass("in").css("opacity", 0);
                var h = this.zoomFromOrigin && s ? Math.max(this.settings.startAnimationDuration, this.settings.backdropDuration) : this.settings.backdropDuration;
                return this.$container.removeClass("lg-show-in"), setTimeout((function () {
                    e.zoomFromOrigin && s && e.outer.removeClass("lg-zoom-from-image"), e.$container.removeClass("lg-show"), e.$backdrop.removeAttr("style").css("transition-duration", e.settings.backdropDuration + "ms"), e.outer.removeClass("lg-closing " + e.settings.startClass), e.getSlideItem(e.index).removeClass("lg-start-end-progress"), e.$inner.empty(), e.lgOpened && e.LGel.trigger(M, {
                        instance: e
                    }), e.LGel.get().focus(), e.lgOpened = !1
                }), h + 100), h + 100
            }, s.prototype.destroyModules = function (t) {
                for (var e in this.plugins.forEach((function (e) {
                        try {
                            t ? e.destroy() : e.closeGallery && e.closeGallery()
                        } catch (t) {
                            console.warn("lightGallery:- make sure lightGallery module is properly destroyed")
                        }
                    })), this.plugins)
                    if (this.plugins[e]) try {
                        t ? this.plugins[e].destroy() : this.plugins[e].closeGallery && this.plugins[e].closeGallery()
                    } catch (t) {
                        console.warn("lightGallery:- make sure lightGallery " + e + " module is properly destroyed")
                    }
            }, s.prototype.destroy = function () {
                var t = this,
                    e = this.closeGallery(!0);
                setTimeout((function () {
                    if (t.destroyModules(!0), !t.settings.dynamic)
                        for (var e = 0; e < t.items.length; e++) {
                            var s = i(t.items[e]);
                            s.off("click.lgcustom-item-" + s.attr("data-lg-id"))
                        }
                    i(window).off(".lg.global" + t.lgId), t.LGel.off(".lg"), t.$container.remove()
                }), e)
            }, s
        }();
    return function (t, e) {
        if (t) try {
            return new B(t, e)
        } catch (t) {
            console.error("lightGallery has not initiated properly", t)
        }
    }
}));
//# sourceMappingURL=/sm/c8ef3a4a9692a9954b21a2b48ae53638b6e1f89e3f7dd2558f6acae131967e07.map
/*!
 * lightgallery | 2.0.0-beta.3 | May 4th 2021
 * http://sachinchoolur.github.io/lightGallery/
 * Copyright (c) 2020 Sachin Neravath;
 * @license GPLv3
 */

(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
        typeof define === 'function' && define.amd ? define(factory) :
        (global.lgZoom = factory());
}(this, (function () {
    'use strict';

    /*! *****************************************************************************
    Copyright (c) Microsoft Corporation.

    Permission to use, copy, modify, and/or distribute this software for any
    purpose with or without fee is hereby granted.

    THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
    REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
    AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
    INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
    LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
    OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
    PERFORMANCE OF THIS SOFTWARE.
    ***************************************************************************** */

    var __assign = function () {
        __assign = Object.assign || function __assign(t) {
            for (var s, i = 1, n = arguments.length; i < n; i++) {
                s = arguments[i];
                for (var p in s)
                    if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
            }
            return t;
        };
        return __assign.apply(this, arguments);
    };

    var zoomSettings = {
        scale: 1,
        zoom: true,
        actualSize: true,
        showZoomInOutIcons: false,
        actualSizeIcons: {
            zoomIn: 'lg-zoom-in',
            zoomOut: 'lg-zoom-out',
        },
        enableZoomAfter: 300,
    };

    /**
     * List of lightGallery events
     * All events should be documented here
     * Below interfaces are used to build the website documentations
     * */
    var lGEvents = {
        afterAppendSlide: 'lgAfterAppendSlide',
        init: 'lgInit',
        hasVideo: 'lgHasVideo',
        containerResize: 'lgContainerResize',
        updateSlides: 'lgUpdateSlides',
        afterAppendSubHtml: 'lgAfterAppendSubHtml',
        beforeOpen: 'lgBeforeOpen',
        afterOpen: 'lgAfterOpen',
        slideItemLoad: 'lgSlideItemLoad',
        beforeSlide: 'lgBeforeSlide',
        afterSlide: 'lgAfterSlide',
        posterClick: 'lgPosterClick',
        dragStart: 'lgDragStart',
        dragMove: 'lgDragMove',
        dragEnd: 'lgDragEnd',
        beforeNextSlide: 'lgBeforeNextSlide',
        beforePrevSlide: 'lgBeforePrevSlide',
        beforeClose: 'lgBeforeClose',
        afterClose: 'lgAfterClose',
    };

    var Zoom = /** @class */ (function () {
        function Zoom(instance, $LG) {
            // get lightGallery core plugin instance
            this.core = instance;
            this.$LG = $LG;
            this.settings = __assign(__assign({}, zoomSettings), this.core.settings);
            if (this.settings.zoom) {
                this.init();
                // Store the zoomable timeout value just to clear it while closing
                this.zoomableTimeout = false;
                this.positionChanged = false;
                // Set the initial value center
                this.pageX = this.core.outer.width() / 2;
                this.pageY =
                    this.core.outer.height() / 2 + this.$LG(window).scrollTop();
                this.scale = 1;
            }
            return this;
        }
        // Append Zoom controls. Actual size, Zoom-in, Zoom-out
        Zoom.prototype.buildTemplates = function () {
            var zoomIcons = this.settings.showZoomInOutIcons ?
                "<button id=\"" + this.core.getIdName('lg-zoom-in') + "\" type=\"button\" class=\"lg-zoom-in lg-icon\"></button><button id=\"" + this.core.getIdName('lg-zoom-out') + "\" type=\"button\" class=\"lg-zoom-out lg-icon\"></button>" :
                '';
            if (this.settings.actualSize) {
                zoomIcons += "<button id=\"" + this.core.getIdName('lg-actual-size') + "\" type=\"button\" class=\"" + this.settings.actualSizeIcons.zoomIn + " lg-icon\"></button>";
            }
            this.core.outer.addClass('lg-use-transition-for-zoom');
            this.core.$toolbar.first().append(zoomIcons);
        };
        /**
         * @desc Enable zoom option only once the image is completely loaded
         * If zoomFromOrigin is true, Zoom is enabled once the dummy image has been inserted
         *
         * Zoom styles are defined under lg-zoomable CSS class.
         */
        Zoom.prototype.enableZoom = function (event) {
            var _this = this;
            // delay will be 0 except first time
            var _speed = this.settings.enableZoomAfter + event.detail.delay;
            // set _speed value 0 if gallery opened from direct url and if it is first slide
            if (this.$LG('body').first().hasClass('lg-from-hash') &&
                event.detail.delay) {
                // will execute only once
                _speed = 0;
            } else {
                // Remove lg-from-hash to enable starting animation.
                this.$LG('body').first().removeClass('lg-from-hash');
            }
            this.zoomableTimeout = setTimeout(function () {
                _this.core.getSlideItem(event.detail.index).addClass('lg-zoomable');
            }, _speed + 30);
        };
        Zoom.prototype.enableZoomOnSlideItemLoad = function () {
            // Add zoomable class
            this.core.LGel.on(lGEvents.slideItemLoad + ".zoom", this.enableZoom.bind(this));
        };
        Zoom.prototype.getModifier = function (rotateValue, axis, el) {
            var originalRotate = rotateValue;
            rotateValue = Math.abs(rotateValue);
            var transformValues = this.getCurrentTransform(el);
            if (!transformValues) {
                return 1;
            }
            var modifier = 1;
            if (axis === 'X') {
                var flipHorizontalValue = Math.sign(parseFloat(transformValues[0]));
                if (rotateValue === 0 || rotateValue === 180) {
                    modifier = 1;
                } else if (rotateValue === 90) {
                    if ((originalRotate === -90 && flipHorizontalValue === 1) ||
                        (originalRotate === 90 && flipHorizontalValue === -1)) {
                        modifier = -1;
                    } else {
                        modifier = 1;
                    }
                }
                modifier = modifier * flipHorizontalValue;
            } else {
                var flipVerticalValue = Math.sign(parseFloat(transformValues[3]));
                if (rotateValue === 0 || rotateValue === 180) {
                    modifier = 1;
                } else if (rotateValue === 90) {
                    var sinX = parseFloat(transformValues[1]);
                    var sinMinusX = parseFloat(transformValues[2]);
                    modifier = Math.sign(sinX * sinMinusX * originalRotate * flipVerticalValue);
                }
                modifier = modifier * flipVerticalValue;
            }
            return modifier;
        };
        Zoom.prototype.getImageSize = function ($image, rotateValue, axis) {
            var imageSizes = {
                y: 'offsetHeight',
                x: 'offsetWidth',
            };
            if (rotateValue === 90) {
                // Swap axis
                if (axis === 'x') {
                    axis = 'y';
                } else {
                    axis = 'x';
                }
            }
            return $image[imageSizes[axis]];
        };
        Zoom.prototype.getDragCords = function (e, rotateValue) {
            if (rotateValue === 90) {
                return {
                    x: e.pageY,
                    y: e.pageX,
                };
            } else {
                return {
                    x: e.pageX,
                    y: e.pageY,
                };
            }
        };
        Zoom.prototype.getSwipeCords = function (e, rotateValue) {
            var x = e.targetTouches[0].pageX;
            var y = e.targetTouches[0].pageY;
            if (rotateValue === 90) {
                return {
                    x: y,
                    y: x,
                };
            } else {
                return {
                    x: x,
                    y: y,
                };
            }
        };
        Zoom.prototype.getDragAllowedAxises = function ($image, rotateValue) {
            var $lg = this.core.$lgContent.get();
            var scale = parseFloat($image.attr('data-scale')) || 1;
            var imgEl = $image.get();
            var allowY = this.getImageSize(imgEl, rotateValue, 'y') * scale >
                $lg.clientHeight;
            var allowX = this.getImageSize(imgEl, rotateValue, 'x') * scale >
                $lg.clientWidth;
            if (rotateValue === 90) {
                return {
                    allowX: allowY,
                    allowY: allowX,
                };
            } else {
                return {
                    allowX: allowX,
                    allowY: allowY,
                };
            }
        };
        /**
         *
         * @param {Element} el
         * @return matrix(cos(X), sin(X), -sin(X), cos(X), 0, 0);
         * Get the current transform value
         */
        Zoom.prototype.getCurrentTransform = function (el) {
            if (!el) {
                return;
            }
            var st = window.getComputedStyle(el, null);
            var tm = st.getPropertyValue('-webkit-transform') ||
                st.getPropertyValue('-moz-transform') ||
                st.getPropertyValue('-ms-transform') ||
                st.getPropertyValue('-o-transform') ||
                st.getPropertyValue('transform') ||
                'none';
            if (tm !== 'none') {
                return tm.split('(')[1].split(')')[0].split(',');
            }
            return;
        };
        Zoom.prototype.getCurrentRotation = function (el) {
            if (!el) {
                return 0;
            }
            var values = this.getCurrentTransform(el);
            if (values) {
                return Math.round(Math.atan2(parseFloat(values[1]), parseFloat(values[0])) *
                    (180 / Math.PI));
                // If you want rotate in 360
                //return (angle < 0 ? angle + 360 : angle);
            }
            return 0;
        };
        /**
         * @desc Image zoom
         * Translate the wrap and scale the image to get better user experience
         *
         * @param {String} scale - Zoom decrement/increment value
         */
        Zoom.prototype.zoomImage = function (scale) {
            var $image = this.core
                .getSlideItem(this.core.index)
                .find('.lg-image')
                .first();
            var imageNode = $image.get();
            if (!imageNode)
                return;
            var containerRect = this.core.outer.get().getBoundingClientRect();
            // Find offset manually to avoid issue after zoom
            var offsetX = (containerRect.width - imageNode.offsetWidth) / 2 +
                containerRect.left;
            var offsetY = (containerRect.height - imageNode.offsetHeight) / 2 +
                this.$LG(window).scrollTop() +
                containerRect.top;
            var originalX;
            var originalY;
            if (scale === 1) {
                this.positionChanged = false;
            }
            if (this.positionChanged) {
                originalX =
                    parseFloat($image.parent().attr('data-x')) /
                    (parseFloat($image.attr('data-scale')) - 1);
                originalY =
                    parseFloat($image.parent().attr('data-y')) /
                    (parseFloat($image.attr('data-scale')) - 1);
                this.pageX = originalX + offsetX;
                this.pageY = originalY + offsetY;
                this.positionChanged = false;
            }
            var _x = this.pageX - offsetX;
            var _y = this.pageY - offsetY;
            var x = (scale - 1) * _x;
            var y = (scale - 1) * _y;
            this.setZoomStyles({
                x: x,
                y: y,
                scale: scale,
            });
        };
        /**
         * @desc apply scale3d to image and translate to image wrap
         * @param {style} X,Y and scale
         */
        Zoom.prototype.setZoomStyles = function (style) {
            var $image = this.core
                .getSlideItem(this.core.index)
                .find('.lg-image')
                .first();
            var $dummyImage = this.core.outer
                .find('.lg-current .lg-dummy-img')
                .first();
            var $imageWrap = $image.parent();
            $image
                .attr('data-scale', style.scale + '')
                .css('transform', 'scale3d(' + style.scale + ', ' + style.scale + ', 1)');
            $dummyImage.css('transform', 'scale3d(' + style.scale + ', ' + style.scale + ', 1)');
            var transform = 'translate3d(-' + style.x + 'px, -' + style.y + 'px, 0)';
            $imageWrap.css('transform', transform);
            $imageWrap.attr('data-x', style.x).attr('data-y', style.y);
        };
        /**
         * @param index - Index of the current slide
         * @param event - event will be available only if the function is called on clicking/taping the imags
         */
        Zoom.prototype.setActualSize = function (index, event) {
            var _this = this;
            var currentItem = this.core.galleryItems[this.core.index];
            // Allow zoom only on image
            if (!currentItem.src) {
                return;
            }
            var scale = this.getCurrentImageActualSizeScale();
            if (this.core.outer.hasClass('lg-zoomed')) {
                this.scale = 1;
            } else {
                this.scale = this.getScale(scale);
            }
            this.setPageCords(event);
            this.beginZoom(this.scale);
            this.zoomImage(this.scale);
            setTimeout(function () {
                _this.core.outer.removeClass('lg-grabbing').addClass('lg-grab');
            }, 10);
        };
        Zoom.prototype.getNaturalWidth = function (index) {
            var $image = this.core.getSlideItem(index).find('.lg-image').first();
            var naturalWidth = this.core.galleryItems[index].width;
            return naturalWidth ?
                parseFloat(naturalWidth) :
                $image.get().naturalWidth;
        };
        Zoom.prototype.getActualSizeScale = function (naturalWidth, width) {
            var _scale;
            var scale;
            if (naturalWidth > width) {
                _scale = naturalWidth / width;
                scale = _scale || 2;
            } else {
                scale = 1;
            }
            return scale;
        };
        Zoom.prototype.getCurrentImageActualSizeScale = function () {
            var $image = this.core
                .getSlideItem(this.core.index)
                .find('.lg-image')
                .first();
            var width = $image.get().offsetWidth;
            var naturalWidth = this.getNaturalWidth(this.core.index) || width;
            return this.getActualSizeScale(naturalWidth, width);
        };
        Zoom.prototype.getPageCords = function (event) {
            var cords = {};
            if (event) {
                cords.x = event.pageX || event.targetTouches[0].pageX;
                cords.y = event.pageY || event.targetTouches[0].pageY;
            } else {
                var containerRect = this.core.outer.get().getBoundingClientRect();
                cords.x = containerRect.width / 2 + containerRect.left;
                cords.y =
                    containerRect.height / 2 +
                    this.$LG(window).scrollTop() +
                    containerRect.top;
            }
            return cords;
        };
        Zoom.prototype.setPageCords = function (event) {
            var pageCords = this.getPageCords(event);
            this.pageX = pageCords.x;
            this.pageY = pageCords.y;
        };
        // If true, zoomed - in else zoomed out
        Zoom.prototype.beginZoom = function (scale) {
            this.core.outer.removeClass('lg-zoom-drag-transition lg-zoom-dragging');
            if (scale > 1) {
                this.core.outer.addClass('lg-zoomed');
                var $actualSize = this.core.getElementById('lg-actual-size');
                $actualSize
                    .removeClass(this.settings.actualSizeIcons.zoomIn)
                    .addClass(this.settings.actualSizeIcons.zoomOut);
            } else {
                this.resetZoom();
            }
            return scale > 1;
        };
        Zoom.prototype.getScale = function (scale) {
            var actualSizeScale = this.getCurrentImageActualSizeScale();
            if (scale < 1) {
                scale = 1;
            } else if (scale > actualSizeScale) {
                scale = actualSizeScale;
            }
            return scale;
        };
        Zoom.prototype.init = function () {
            var _this = this;
            this.buildTemplates();
            this.enableZoomOnSlideItemLoad();
            var tapped = null;
            this.core.outer.on('dblclick.lg', function (event) {
                if (!_this.$LG(event.target).hasClass('lg-image')) {
                    return;
                }
                _this.setActualSize(_this.core.index, event);
            });
            this.core.outer.on('touchstart.lg', function (event) {
                var $target = _this.$LG(event.target);
                if (event.targetTouches.length === 1 &&
                    $target.hasClass('lg-image')) {
                    if (!tapped) {
                        tapped = setTimeout(function () {
                            tapped = null;
                        }, 300);
                    } else {
                        clearTimeout(tapped);
                        tapped = null;
                        _this.setActualSize(_this.core.index, event);
                    }
                    event.preventDefault();
                }
            });
            // Update zoom on resize and orientationchange
            this.core.LGel.on(lGEvents.containerResize + ".zoom", function () {
                if (!_this.core.lgOpened)
                    return;
                _this.setPageCords();
                _this.zoomImage(_this.scale);
            });
            this.core.getElementById('lg-zoom-out').on('click.lg', function () {
                if (_this.core.outer.find('.lg-current .lg-image').get()) {
                    _this.scale -= _this.settings.scale;
                    _this.scale = _this.getScale(_this.scale);
                    _this.beginZoom(_this.scale);
                    _this.zoomImage(_this.scale);
                }
            });
            this.core.getElementById('lg-zoom-in').on('click.lg', function () {
                _this.zoomIn();
            });
            this.core.getElementById('lg-actual-size').on('click.lg', function () {
                _this.setActualSize(_this.core.index);
            });
            this.core.LGel.on(lGEvents.beforeOpen + ".zoom", function () {
                _this.core.outer.find('.lg-item').removeClass('lg-zoomable');
            });
            // Reset zoom on slide change
            this.core.LGel.on(lGEvents.afterSlide + ".zoom", function (event) {
                var prevIndex = event.detail.prevIndex;
                _this.scale = 1;
                _this.resetZoom(prevIndex);
            });
            // Drag option after zoom
            this.zoomDrag();
            this.pinchZoom();
            this.zoomSwipe();
        };
        Zoom.prototype.zoomIn = function (scale) {
            var currentItem = this.core.galleryItems[this.core.index];
            // Allow zoom only on image
            if (!currentItem.src) {
                return;
            }
            if (scale) {
                this.scale = scale;
            } else {
                this.scale += this.settings.scale;
            }
            this.scale = this.getScale(this.scale);
            this.beginZoom(this.scale);
            this.zoomImage(this.scale);
        };
        // Reset zoom effect
        Zoom.prototype.resetZoom = function (index) {
            this.core.outer.removeClass('lg-zoomed lg-zoom-drag-transition');
            var $actualSize = this.core.getElementById('lg-actual-size');
            var $item = this.core.getSlideItem(index !== undefined ? index : this.core.index);
            $actualSize
                .removeClass(this.settings.actualSizeIcons.zoomOut)
                .addClass(this.settings.actualSizeIcons.zoomIn);
            $item.find('.lg-img-wrap').first().removeAttr('style data-x data-y');
            $item.find('.lg-image').first().removeAttr('style data-scale');
            // Reset pagx pagy values to center
            this.setPageCords();
        };
        Zoom.prototype.getTouchDistance = function (e) {
            return Math.sqrt((e.targetTouches[0].pageX - e.targetTouches[1].pageX) *
                (e.targetTouches[0].pageX - e.targetTouches[1].pageX) +
                (e.targetTouches[0].pageY - e.targetTouches[1].pageY) *
                (e.targetTouches[0].pageY - e.targetTouches[1].pageY));
        };
        Zoom.prototype.pinchZoom = function () {
            var _this = this;
            var startDist = 0;
            var pinchStarted = false;
            var initScale = 1;
            var $item = this.core.getSlideItem(this.core.index);
            this.core.$inner.on('touchstart.lg', function (e) {
                $item = _this.core.getSlideItem(_this.core.index);
                e.preventDefault();
                if (e.targetTouches.length === 2 &&
                    (_this.$LG(e.target).hasClass('lg-item') ||
                        $item.get().contains(e.target))) {
                    initScale = _this.scale || 1;
                    _this.core.outer.removeClass('lg-zoom-drag-transition lg-zoom-dragging');
                    _this.core.touchAction = 'pinch';
                    startDist = _this.getTouchDistance(e);
                }
            });
            this.core.$inner.on('touchmove.lg', function (e) {
                e.preventDefault();
                if (e.targetTouches.length === 2 &&
                    _this.core.touchAction === 'pinch' &&
                    (_this.$LG(e.target).hasClass('lg-item') ||
                        $item.get().contains(e.target))) {
                    var endDist = _this.getTouchDistance(e);
                    var distance = startDist - endDist;
                    if (!pinchStarted && Math.abs(distance) > 5) {
                        pinchStarted = true;
                    }
                    if (pinchStarted) {
                        _this.scale = Math.max(1, initScale + -distance * 0.008);
                        _this.zoomImage(_this.scale);
                    }
                }
            });
            this.core.$inner.on('touchend.lg', function (e) {
                if (_this.core.touchAction === 'pinch' &&
                    (_this.$LG(e.target).hasClass('lg-item') ||
                        $item.get().contains(e.target))) {
                    pinchStarted = false;
                    startDist = 0;
                    if (_this.scale <= 1) {
                        _this.resetZoom();
                    } else {
                        _this.scale = _this.getScale(_this.scale);
                        _this.zoomImage(_this.scale);
                        _this.core.outer.addClass('lg-zoomed');
                    }
                    _this.core.touchAction = undefined;
                }
            });
        };
        Zoom.prototype.touchendZoom = function (startCoords, endCoords, allowX, allowY, touchDuration, rotateValue) {
            var rotateEl = this.core
                .getSlideItem(this.core.index)
                .find('.lg-img-rotate')
                .first()
                .get();
            var distanceXnew = endCoords.x - startCoords.x;
            var distanceYnew = endCoords.y - startCoords.y;
            var speedX = Math.abs(distanceXnew) / touchDuration + 1;
            var speedY = Math.abs(distanceYnew) / touchDuration + 1;
            if (speedX > 2) {
                speedX += 1;
            }
            if (speedY > 2) {
                speedY += 1;
            }
            distanceXnew = distanceXnew * speedX;
            distanceYnew = distanceYnew * speedY;
            var _LGel = this.core
                .getSlideItem(this.core.index)
                .find('.lg-img-wrap')
                .first();
            var $image = this.core
                .getSlideItem(this.core.index)
                .find('.lg-object')
                .first();
            var dataX = parseFloat(_LGel.attr('data-x')) || 0;
            var dataY = parseFloat(_LGel.attr('data-y')) || 0;
            var distance = {};
            distance.x = -Math.abs(dataX) +
                distanceXnew * this.getModifier(rotateValue, 'X', rotateEl);
            distance.y = -Math.abs(dataY) +
                distanceYnew * this.getModifier(rotateValue, 'Y', rotateEl);
            var possibleSwipeCords = this.getPossibleSwipeDragCords($image, rotateValue);
            if (Math.abs(distanceXnew) > 15 || Math.abs(distanceYnew) > 15) {
                if (allowY) {
                    if (distance.y <= -possibleSwipeCords.maxY) {
                        distance.y = -possibleSwipeCords.maxY;
                    } else if (distance.y >= -possibleSwipeCords.minY) {
                        distance.y = -possibleSwipeCords.minY;
                    }
                }
                if (allowX) {
                    if (distance.x <= -possibleSwipeCords.maxX) {
                        distance.x = -possibleSwipeCords.maxX;
                    } else if (distance.x >= -possibleSwipeCords.minX) {
                        distance.x = -possibleSwipeCords.minX;
                    }
                }
                if (allowY) {
                    _LGel.attr('data-y', Math.abs(distance.y));
                } else {
                    var dataY_1 = parseFloat(_LGel.attr('data-y')) || 0;
                    distance.y = -Math.abs(dataY_1);
                }
                if (allowX) {
                    _LGel.attr('data-x', Math.abs(distance.x));
                } else {
                    var dataX_1 = parseFloat(_LGel.attr('data-x')) || 0;
                    distance.x = -Math.abs(dataX_1);
                }
                this.setZoomSwipeStyles(_LGel, distance);
                this.positionChanged = true;
            }
        };
        Zoom.prototype.getZoomSwipeCords = function (startCoords, endCoords, allowX, allowY, possibleSwipeCords, dataY, dataX, rotateValue, rotateEl) {
            var distance = {};
            if (allowY) {
                distance.y = -Math.abs(dataY) +
                    (endCoords.y - startCoords.y) *
                    this.getModifier(rotateValue, 'Y', rotateEl);
                if (distance.y <= -possibleSwipeCords.maxY) {
                    var diffMaxY = -possibleSwipeCords.maxY - distance.y;
                    distance.y = -possibleSwipeCords.maxY - diffMaxY / 6;
                } else if (distance.y >= -possibleSwipeCords.minY) {
                    var diffMinY = distance.y - -possibleSwipeCords.minY;
                    distance.y = -possibleSwipeCords.minY + diffMinY / 6;
                }
            } else {
                distance.y = -Math.abs(dataY);
            }
            if (allowX) {
                distance.x = -Math.abs(dataX) +
                    (endCoords.x - startCoords.x) *
                    this.getModifier(rotateValue, 'X', rotateEl);
                if (distance.x <= -possibleSwipeCords.maxX) {
                    var diffMaxX = -possibleSwipeCords.maxX - distance.x;
                    distance.x = -possibleSwipeCords.maxX - diffMaxX / 6;
                } else if (distance.x >= -possibleSwipeCords.minX) {
                    var diffMinX = distance.x - -possibleSwipeCords.minX;
                    distance.x = -possibleSwipeCords.minX + diffMinX / 6;
                }
            } else {
                distance.x = -Math.abs(dataX);
            }
            return distance;
        };
        Zoom.prototype.getPossibleSwipeDragCords = function ($image, rotateValue) {
            var $cont = this.core.$lgContent;
            var contHeight = $cont.height();
            var contWidth = $cont.width();
            var imageYSize = this.getImageSize($image.get(), rotateValue, 'y');
            var imageXSize = this.getImageSize($image.get(), rotateValue, 'x');
            var dataY = parseFloat($image.attr('data-scale')) || 1;
            var elDataScale = Math.abs(dataY);
            var minY = (contHeight - imageYSize) / 2;
            var maxY = Math.abs(imageYSize * elDataScale - contHeight + minY);
            var minX = (contWidth - imageXSize) / 2;
            var maxX = Math.abs(imageXSize * elDataScale - contWidth + minX);
            if (rotateValue === 90) {
                return {
                    minY: minX,
                    maxY: maxX,
                    minX: minY,
                    maxX: maxY,
                };
            } else {
                return {
                    minY: minY,
                    maxY: maxY,
                    minX: minX,
                    maxX: maxX,
                };
            }
        };
        Zoom.prototype.setZoomSwipeStyles = function (LGel, distance) {
            LGel.css('transform', 'translate3d(' + distance.x + 'px, ' + distance.y + 'px, 0)');
        };
        Zoom.prototype.zoomSwipe = function () {
            var _this = this;
            var startCoords = {};
            var endCoords = {};
            var isMoved = false;
            // Allow x direction drag
            var allowX = false;
            // Allow Y direction drag
            var allowY = false;
            var startTime = new Date();
            var endTime = new Date();
            var dataX = 0;
            var dataY = 0;
            var possibleSwipeCords;
            var _LGel;
            var rotateEl = null;
            var rotateValue = 0;
            var $item = this.core.getSlideItem(this.core.index);
            this.core.$inner.on('touchstart.lg', function (e) {
                e.preventDefault();
                var currentItem = _this.core.galleryItems[_this.core.index];
                // Allow zoom only on image
                if (!currentItem.src) {
                    return;
                }
                $item = _this.core.getSlideItem(_this.core.index);
                if ((_this.$LG(e.target).hasClass('lg-item') ||
                        $item.get().contains(e.target)) &&
                    e.targetTouches.length === 1 &&
                    _this.core.outer.hasClass('lg-zoomed')) {
                    startTime = new Date();
                    _this.core.touchAction = 'zoomSwipe';
                    var $image = _this.core
                        .getSlideItem(_this.core.index)
                        .find('.lg-object')
                        .first();
                    _LGel = _this.core
                        .getSlideItem(_this.core.index)
                        .find('.lg-img-wrap')
                        .first();
                    rotateEl = _this.core
                        .getSlideItem(_this.core.index)
                        .find('.lg-img-rotate')
                        .first()
                        .get();
                    rotateValue = _this.getCurrentRotation(rotateEl);
                    var dragAllowedAxises = _this.getDragAllowedAxises($image, Math.abs(rotateValue));
                    allowY = dragAllowedAxises.allowY;
                    allowX = dragAllowedAxises.allowX;
                    if (allowX || allowY) {
                        startCoords = _this.getSwipeCords(e, Math.abs(rotateValue));
                    }
                    dataY = parseFloat(_LGel.attr('data-y'));
                    dataX = parseFloat(_LGel.attr('data-x'));
                    possibleSwipeCords = _this.getPossibleSwipeDragCords($image, rotateValue);
                    // reset opacity and transition duration
                    _this.core.outer.addClass('lg-zoom-dragging lg-zoom-drag-transition');
                }
            });
            this.core.$inner.on('touchmove.lg', function (e) {
                e.preventDefault();
                if (e.targetTouches.length === 1 &&
                    _this.core.touchAction === 'zoomSwipe' &&
                    (_this.$LG(e.target).hasClass('lg-item') ||
                        $item.get().contains(e.target))) {
                    _this.core.touchAction = 'zoomSwipe';
                    endCoords = _this.getSwipeCords(e, Math.abs(rotateValue));
                    var distance = _this.getZoomSwipeCords(startCoords, endCoords, allowX, allowY, possibleSwipeCords, dataY, dataX, rotateValue, rotateEl);
                    if (Math.abs(endCoords.x - startCoords.x) > 15 ||
                        Math.abs(endCoords.y - startCoords.y) > 15) {
                        isMoved = true;
                        _this.setZoomSwipeStyles(_LGel, distance);
                    }
                }
            });
            this.core.$inner.on('touchend.lg', function (e) {
                if (_this.core.touchAction === 'zoomSwipe' &&
                    (_this.$LG(e.target).hasClass('lg-item') ||
                        $item.get().contains(e.target))) {
                    _this.core.touchAction = undefined;
                    _this.core.outer.removeClass('lg-zoom-dragging');
                    if (!isMoved) {
                        return;
                    }
                    isMoved = false;
                    endTime = new Date();
                    var touchDuration = endTime.valueOf() - startTime.valueOf();
                    _this.touchendZoom(startCoords, endCoords, allowX, allowY, touchDuration, rotateValue);
                }
            });
        };
        Zoom.prototype.zoomDrag = function () {
            var _this = this;
            var startCoords = {};
            var endCoords = {};
            var isDragging = false;
            var isMoved = false;
            var rotateEl = null;
            var rotateValue = 0;
            // Allow x direction drag
            var allowX = false;
            // Allow Y direction drag
            var allowY = false;
            var startTime;
            var endTime;
            var possibleSwipeCords;
            var dataY;
            var dataX;
            var _LGel;
            this.core.outer.on('mousedown.lg.zoom', function (e) {
                var currentItem = _this.core.galleryItems[_this.core.index];
                // Allow zoom only on image
                if (!currentItem.src) {
                    return;
                }
                var $item = _this.core.getSlideItem(_this.core.index);
                if (_this.$LG(e.target).hasClass('lg-item') ||
                    $item.get().contains(e.target)) {
                    startTime = new Date();
                    // execute only on .lg-object
                    var $image = _this.core
                        .getSlideItem(_this.core.index)
                        .find('.lg-object')
                        .first();
                    _LGel = _this.core
                        .getSlideItem(_this.core.index)
                        .find('.lg-img-wrap')
                        .first();
                    rotateEl = _this.core
                        .getSlideItem(_this.core.index)
                        .find('.lg-img-rotate')
                        .get();
                    rotateValue = _this.getCurrentRotation(rotateEl);
                    var dragAllowedAxises = _this.getDragAllowedAxises($image, Math.abs(rotateValue));
                    allowY = dragAllowedAxises.allowY;
                    allowX = dragAllowedAxises.allowX;
                    if (_this.core.outer.hasClass('lg-zoomed')) {
                        if (_this.$LG(e.target).hasClass('lg-object') &&
                            (allowX || allowY)) {
                            e.preventDefault();
                            startCoords = _this.getDragCords(e, Math.abs(rotateValue));
                            possibleSwipeCords = _this.getPossibleSwipeDragCords($image, rotateValue);
                            isDragging = true;
                            dataY = parseFloat(_LGel.attr('data-y'));
                            dataX = parseFloat(_LGel.attr('data-x'));
                            // ** Fix for webkit cursor issue https://code.google.com/p/chromium/issues/detail?id=26723
                            _this.core.outer.get().scrollLeft += 1;
                            _this.core.outer.get().scrollLeft -= 1;
                            _this.core.outer
                                .removeClass('lg-grab')
                                .addClass('lg-grabbing lg-zoom-drag-transition lg-zoom-dragging');
                            // reset opacity and transition duration
                        }
                    }
                }
            });
            this.$LG(window).on("mousemove.lg.zoom.global" + this.core.lgId, function (e) {
                if (isDragging) {
                    isMoved = true;
                    endCoords = _this.getDragCords(e, Math.abs(rotateValue));
                    var distance = _this.getZoomSwipeCords(startCoords, endCoords, allowX, allowY, possibleSwipeCords, dataY, dataX, rotateValue, rotateEl);
                    _this.setZoomSwipeStyles(_LGel, distance);
                }
            });
            this.$LG(window).on("mouseup.lg.zoom.global" + this.core.lgId, function (e) {
                if (isDragging) {
                    endTime = new Date();
                    isDragging = false;
                    _this.core.outer.removeClass('lg-zoom-dragging');
                    // Fix for chrome mouse move on click
                    if (isMoved &&
                        (startCoords.x !== endCoords.x ||
                            startCoords.y !== endCoords.y)) {
                        endCoords = _this.getDragCords(e, Math.abs(rotateValue));
                        var touchDuration = endTime.valueOf() - startTime.valueOf();
                        _this.touchendZoom(startCoords, endCoords, allowX, allowY, touchDuration, rotateValue);
                    }
                    isMoved = false;
                }
                _this.core.outer.removeClass('lg-grabbing').addClass('lg-grab');
            });
        };
        Zoom.prototype.closeGallery = function () {
            this.resetZoom();
        };
        Zoom.prototype.destroy = function () {
            // Unbind all events added by lightGallery zoom plugin
            this.$LG(window).off(".lg.zoom.global" + this.core.lgId);
            this.core.LGel.off('.lg.zoom');
            this.core.LGel.off('.zoom');
            clearTimeout(this.zoomableTimeout);
            this.zoomableTimeout = false;
        };
        return Zoom;
    }());

    return Zoom;

})));
//# sourceMappingURL=lg-zoom.umd.js.map
/*!
 * lightgallery | 2.0.0-beta.3 | May 4th 2021
 * http://sachinchoolur.github.io/lightGallery/
 * Copyright (c) 2020 Sachin Neravath;
 * @license GPLv3
 */

(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
        typeof define === 'function' && define.amd ? define(factory) :
        (global.lgVideo = factory());
}(this, (function () {
    'use strict';

    /*! *****************************************************************************
    Copyright (c) Microsoft Corporation.

    Permission to use, copy, modify, and/or distribute this software for any
    purpose with or without fee is hereby granted.

    THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
    REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
    AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
    INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
    LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
    OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
    PERFORMANCE OF THIS SOFTWARE.
    ***************************************************************************** */

    var __assign = function () {
        __assign = Object.assign || function __assign(t) {
            for (var s, i = 1, n = arguments.length; i < n; i++) {
                s = arguments[i];
                for (var p in s)
                    if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
            }
            return t;
        };
        return __assign.apply(this, arguments);
    };

    var videoSettings = {
        autoplayFirstVideo: true,
        youTubePlayerParams: false,
        vimeoPlayerParams: false,
        wistiaPlayerParams: false,
        gotoNextSlideOnVideoEnd: true,
        autoplayVideoOnSlide: false,
        videojs: false,
        videojsOptions: {},
    };

    /**
     * List of lightGallery events
     * All events should be documented here
     * Below interfaces are used to build the website documentations
     * */
    var lGEvents = {
        afterAppendSlide: 'lgAfterAppendSlide',
        init: 'lgInit',
        hasVideo: 'lgHasVideo',
        containerResize: 'lgContainerResize',
        updateSlides: 'lgUpdateSlides',
        afterAppendSubHtml: 'lgAfterAppendSubHtml',
        beforeOpen: 'lgBeforeOpen',
        afterOpen: 'lgAfterOpen',
        slideItemLoad: 'lgSlideItemLoad',
        beforeSlide: 'lgBeforeSlide',
        afterSlide: 'lgAfterSlide',
        posterClick: 'lgPosterClick',
        dragStart: 'lgDragStart',
        dragMove: 'lgDragMove',
        dragEnd: 'lgDragEnd',
        beforeNextSlide: 'lgBeforeNextSlide',
        beforePrevSlide: 'lgBeforePrevSlide',
        beforeClose: 'lgBeforeClose',
        afterClose: 'lgAfterClose',
    };

    /**
     * Video module for lightGallery
     * Supports HTML5, YouTube, Vimeo, wistia videos
     *
     *
     * @ref Wistia
     * https://wistia.com/support/integrations/wordpress(How to get url)
     * https://wistia.com/support/developers/embed-options#using-embed-options
     * https://wistia.com/support/developers/player-api
     * https://wistia.com/support/developers/construct-an-embed-code
     * http://jsfiddle.net/xvnm7xLm/
     * https://developer.mozilla.org/en-US/docs/Web/HTML/Element/video
     * https://wistia.com/support/embed-and-share/sharing-videos
     * https://private-sharing.wistia.com/medias/mwhrulrucj
     *
     * @ref Youtube
     * https://developers.google.com/youtube/player_parameters#enablejsapi
     * https://developers.google.com/youtube/iframe_api_reference
     *
     */
    function param(obj) {
        return Object.keys(obj)
            .map(function (k) {
                return encodeURIComponent(k) + '=' + encodeURIComponent(obj[k]);
            })
            .join('&');
    }
    var Video = /** @class */ (function () {
        function Video(instance) {
            // get lightGallery core plugin instance
            this.core = instance;
            this.settings = __assign(__assign({}, videoSettings), this.core.settings);
            this.init();
            return this;
        }
        Video.prototype.init = function () {
            var _this = this;
            /**
             * Event triggered when video url found without poster
             * Append video HTML
             * Play if autoplayFirstVideo is true
             */
            this.core.LGel.on(lGEvents.hasVideo + ".video", this.onHasVideo.bind(this));
            if (this.core.galleryItems.length > 1 &&
                (this.core.settings.enableSwipe || this.core.settings.enableDrag)) {
                this.core.LGel.on(lGEvents.posterClick + ".video", function () {
                    var $el = _this.core.getSlideItem(_this.core.index);
                    _this.loadVideoOnPosterClick($el);
                });
            } else {
                // For IE 9 and bellow
                this.core.outer
                    .find('.lg-item')
                    .first()
                    .on('click.lg', function () {
                        var $el = _this.core.getSlideItem(_this.core.index);
                        _this.loadVideoOnPosterClick($el);
                    });
            }
            // @desc fired immediately before each slide transition.
            this.core.LGel.on(lGEvents.beforeSlide + ".video", this.onBeforeSlide.bind(this));
            // @desc fired immediately after each slide transition.
            this.core.LGel.on(lGEvents.afterSlide + ".video", this.onAfterSlide.bind(this));
        };
        /**
         * @desc Event triggered when video url or poster found
         * Append video HTML is poster is not given
         * Play if autoplayFirstVideo is true
         *
         * @param {Event} event - Javascript Event object.
         */
        Video.prototype.onHasVideo = function (event) {
            var _a = event.detail,
                index = _a.index,
                src = _a.src,
                html5Video = _a.html5Video,
                hasPoster = _a.hasPoster,
                isFirstSlide = _a.isFirstSlide;
            if (!hasPoster) {
                // All functions are called separately if poster exist in loadVideoOnPosterClick function
                this.appendVideos(this.core.getSlideItem(index), {
                    src: src,
                    addClass: 'lg-object',
                    index: index,
                    html5Video: html5Video,
                });
                // Automatically navigate to next slide once video reaches the end.
                this.gotoNextSlideOnVideoEnd(src, index);
            }
            if (this.settings.autoplayFirstVideo && isFirstSlide) {
                if (hasPoster) {
                    var $slide = this.core.getSlideItem(index);
                    this.loadVideoOnPosterClick($slide);
                } else {
                    this.playVideo(index);
                }
            }
        };
        /**
         * @desc fired immediately before each slide transition.
         * Pause the previous video
         * Hide the download button if the slide contains YouTube, Vimeo, or Wistia videos.
         *
         * @param {Event} event - Javascript Event object.
         * @param {number} prevIndex - Previous index of the slide.
         * @param {number} index - Current index of the slide
         */
        Video.prototype.onBeforeSlide = function (event) {
            var _a = event.detail,
                prevIndex = _a.prevIndex,
                index = _a.index;
            this.pauseVideo(prevIndex);
            var _videoInfo = this.core.galleryItems[index].__slideVideoInfo || {};
            if (_videoInfo.youtube || _videoInfo.vimeo || _videoInfo.wistia) {
                this.core.outer.addClass('lg-hide-download');
            }
        };
        /**
         * @desc fired immediately after each slide transition.
         * Play video if autoplayVideoOnSlide option is enabled.
         *
         * @param {Event} event - Javascript Event object.
         * @param {number} prevIndex - Previous index of the slide.
         * @param {number} index - Current index of the slide
         */
        Video.prototype.onAfterSlide = function (event) {
            var _this = this;
            var index = event.detail.index;
            if (this.settings.autoplayVideoOnSlide && this.core.lGalleryOn) {
                setTimeout(function () {
                    var $slide = _this.core.getSlideItem(index);
                    if (!$slide.hasClass('lg-video-loaded')) {
                        _this.loadVideoOnPosterClick($slide);
                    } else {
                        _this.playVideo(index);
                    }
                }, 100);
            }
        };
        /**
         * Play HTML5, Youtube, Vimeo or Wistia videos in a particular slide.
         * @param {number} index - Index of the slide
         */
        Video.prototype.playVideo = function (index) {
            this.controlVideo(index, 'play');
        };
        /**
         * Pause HTML5, Youtube, Vimeo or Wistia videos in a particular slide.
         * @param {number} index - Index of the slide
         */
        Video.prototype.pauseVideo = function (index) {
            this.controlVideo(index, 'pause');
        };
        Video.prototype.getVideoHtml = function (src, addClass, index, html5Video) {
            var video = '';
            var videoInfo = this.core.galleryItems[index]
                .__slideVideoInfo || {};
            var currentGalleryItem = this.core.galleryItems[index];
            var videoTitle = currentGalleryItem.title || currentGalleryItem.alt;
            videoTitle = videoTitle ? 'title="' + videoTitle + '"' : '';
            var commonIframeProps = "allowtransparency=\"true\" \n            frameborder=\"0\" \n            scrolling=\"no\" \n            allowfullscreen \n            mozallowfullscreen \n            webkitallowfullscreen \n            oallowfullscreen \n            msallowfullscreen";
            if (videoInfo.youtube) {
                var videoId = 'lg-youtube' + index;
                var youTubePlayerParams = "?wmode=opaque&autoplay=0&enablejsapi=1";
                var playerParams = youTubePlayerParams +
                    (this.settings.youTubePlayerParams ?
                        '&' + param(this.settings.youTubePlayerParams) :
                        '');
                video = "<iframe allow=\"autoplay\" id=" + videoId + " class=\"lg-video-object lg-youtube " + addClass + "\" " + videoTitle + " src=\"//www.youtube.com/embed/" + (videoInfo.youtube[1] + playerParams) + "\" " + commonIframeProps + "></iframe>";
            } else if (videoInfo.vimeo) {
                var videoId = 'lg-vimeo' + index;
                var playerParams = param(this.settings.vimeoPlayerParams);
                video = "<iframe allow=\"autoplay\" id=" + videoId + " class=\"lg-video-object lg-vimeo " + addClass + "\" " + videoTitle + " src=\"//player.vimeo.com/video/" + (videoInfo.vimeo[1] + playerParams) + "\" " + commonIframeProps + "></iframe>";
            } else if (videoInfo.wistia) {
                var wistiaId = 'lg-wistia' + index;
                var playerParams = param(this.settings.wistiaPlayerParams);
                video = "<iframe allow=\"autoplay\" id=\"" + wistiaId + "\" src=\"//fast.wistia.net/embed/iframe/" + (videoInfo.wistia[4] + playerParams) + "\" " + videoTitle + " class=\"wistia_embed lg-video-object lg-wistia " + addClass + "\" name=\"wistia_embed\" " + commonIframeProps + "></iframe>";
            } else if (videoInfo.html5) {
                var html5VideoMarkup = '';
                for (var i = 0; i < html5Video.source.length; i++) {
                    html5VideoMarkup += "<source src=\"" + html5Video.source[i].src + "\" type=\"" + html5Video.source[i].type + "\">";
                }
                var html5VideoAttrs_1 = '';
                var videoAttributes_1 = html5Video.attributes || {};
                Object.keys(videoAttributes_1 || {}).forEach(function (key) {
                    html5VideoAttrs_1 += key + "=\"" + videoAttributes_1[key] + "\" ";
                });
                video = "<video class=\"lg-video-object lg-html5 " + (this.settings.videojs ? 'video-js' : '') + "\" " + html5VideoAttrs_1 + ">\n                " + html5VideoMarkup + "\n                Your browser does not support HTML5 video.\n            </video>";
            }
            return video;
        };
        /**
         * @desc - Append videos to the slide
         *
         * @param {HTMLElement} el - slide element
         * @param {Object} videoParams - Video parameters, Contains src, class, index, htmlVideo
         */
        Video.prototype.appendVideos = function (el, videoParams) {
            var _a;
            var videoHtml = this.getVideoHtml(videoParams.src, videoParams.addClass, videoParams.index, videoParams.html5Video);
            el.find('.lg-video-cont').append(videoHtml);
            var $videoElement = el.find('.lg-video-object').first();
            if (this.settings.videojs && ((_a = this.core.galleryItems[videoParams.index].__slideVideoInfo) === null || _a === void 0 ? void 0 : _a.html5)) {
                try {
                    return videojs($videoElement.get(), this.settings.videojsOptions);
                } catch (e) {
                    console.error('lightGallery:- Make sure you have included videojs');
                }
            }
        };
        Video.prototype.gotoNextSlideOnVideoEnd = function (src, index) {
            var _this = this;
            var $videoElement = this.core
                .getSlideItem(index)
                .find('.lg-video-object')
                .first();
            var videoInfo = this.core.galleryItems[index].__slideVideoInfo || {};
            if (this.settings.gotoNextSlideOnVideoEnd) {
                if (videoInfo.html5) {
                    $videoElement.on('ended', function () {
                        _this.core.goToNextSlide();
                    });
                } else if (videoInfo.vimeo) {
                    try {
                        // https://github.com/vimeo/player.js/#ended
                        new Vimeo.Player($videoElement.get()).on('ended', function () {
                            _this.core.goToNextSlide();
                        });
                    } catch (e) {
                        console.error('lightGallery:- Make sure you have included //github.com/vimeo/player.js');
                    }
                } else if (videoInfo.wistia) {
                    try {
                        window._wq = window._wq || [];
                        // @todo Event is gettign triggered multiple times
                        window._wq.push({
                            id: $videoElement.attr('id'),
                            onReady: function (video) {
                                video.bind('end', function () {
                                    _this.core.goToNextSlide();
                                });
                            },
                        });
                    } catch (e) {
                        console.error('lightGallery:- Make sure you have included //fast.wistia.com/assets/external/E-v1.js');
                    }
                }
            }
        };
        Video.prototype.controlVideo = function (index, action) {
            var $videoElement = this.core
                .getSlideItem(index)
                .find('.lg-video-object')
                .first();
            var videoInfo = this.core.galleryItems[index].__slideVideoInfo || {};
            if (!$videoElement.get())
                return;
            if (videoInfo.youtube) {
                try {
                    $videoElement.get().contentWindow.postMessage("{\"event\":\"command\",\"func\":\"" + action + "Video\",\"args\":\"\"}", '*');
                } catch (e) {
                    console.error("lightGallery:- " + e);
                }
            } else if (videoInfo.vimeo) {
                try {
                    new Vimeo.Player($videoElement.get())[action]();
                } catch (e) {
                    console.error('lightGallery:- Make sure you have included //github.com/vimeo/player.js');
                }
            } else if (videoInfo.html5) {
                if (this.settings.videojs) {
                    try {
                        videojs($videoElement.get())[action]();
                    } catch (e) {
                        console.error('lightGallery:- Make sure you have included videojs');
                    }
                } else {
                    $videoElement.get()[action]();
                }
            } else if (videoInfo.wistia) {
                try {
                    window._wq = window._wq || [];
                    // @todo Find a way to destroy wistia player instance
                    window._wq.push({
                        id: $videoElement.attr('id'),
                        onReady: function (video) {
                            video[action]();
                        },
                    });
                } catch (e) {
                    console.error('lightGallery:- Make sure you have included //fast.wistia.com/assets/external/E-v1.js');
                }
            }
        };
        Video.prototype.loadVideoOnPosterClick = function ($el) {
            var _this = this;
            // check slide has poster
            if (!$el.hasClass('lg-video-loaded')) {
                // check already video element present
                if (!$el.hasClass('lg-has-video')) {
                    $el.addClass('lg-has-video');
                    var _html = void 0;
                    var _src = this.core.galleryItems[this.core.index].src;
                    var video = this.core.galleryItems[this.core.index].video;
                    if (video) {
                        _html =
                            typeof video === 'string' ? JSON.parse(video) : video;
                    }
                    var videoJsPlayer_1 = this.appendVideos($el, {
                        src: _src,
                        addClass: '',
                        index: this.core.index,
                        html5Video: _html,
                    });
                    this.gotoNextSlideOnVideoEnd(_src, this.core.index);
                    var $tempImg = $el.find('.lg-object').first().get();
                    // @todo make sure it is working
                    $el.find('.lg-video-cont').first().append($tempImg);
                    $el.addClass('lg-video-loading');
                    videoJsPlayer_1 &&
                        videoJsPlayer_1.ready(function () {
                            videoJsPlayer_1.on('loadedmetadata', function () {
                                _this.onVideoLoadAfterPosterClick($el, _this.core.index);
                            });
                        });
                    $el.find('.lg-video-object')
                        .first()
                        .on('load.lg error.lg loadeddata.lg', function () {
                            setTimeout(function () {
                                _this.onVideoLoadAfterPosterClick($el, _this.core.index);
                            }, 50);
                        });
                } else {
                    this.playVideo(this.core.index);
                }
            }
        };
        Video.prototype.onVideoLoadAfterPosterClick = function ($el, index) {
            $el.addClass('lg-video-loaded');
            this.playVideo(index);
        };
        Video.prototype.destroy = function () {
            this.core.LGel.off('.lg.video');
            this.core.LGel.off('.video');
        };
        return Video;
    }());

    return Video;

})));
//# sourceMappingURL=lg-video.umd.js.map
