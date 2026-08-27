/* Polyfill service v3.111.0
 * Disable minification (remove `.min` from URL path) for more info */

(function(self, undefined) {
    !function(t, e) {
        "use strict";
        function n(t) {
            this.time = t.time,
            this.target = t.target,
            this.rootBounds = t.rootBounds,
            this.boundingClientRect = t.boundingClientRect,
            this.intersectionRect = t.intersectionRect || u();
            try {
                this.isIntersecting = !!t.intersectionRect
            } catch (r) {}
            var e = this.boundingClientRect
              , n = e.width * e.height
              , o = this.intersectionRect
              , i = o.width * o.height;
            this.intersectionRatio = n ? Number((i / n).toFixed(4)) : this.isIntersecting ? 1 : 0
        }
        function o(t, e) {
            var n = e || {};
            if ("function" != typeof t)
                throw new Error("callback must be a function");
            if (n.root && 1 != n.root.nodeType)
                throw new Error("root must be an Element");
            this._checkForIntersections = r(this._checkForIntersections.bind(this), this.THROTTLE_TIMEOUT),
            this._callback = t,
            this._observationTargets = [],
            this._queuedEntries = [],
            this._rootMarginValues = this._parseRootMargin(n.rootMargin),
            this.thresholds = this._initThresholds(n.threshold),
            this.root = n.root || null,
            this.rootMargin = this._rootMarginValues.map(function(t) {
                return t.value + t.unit
            }).join(" ")
        }
        function i() {
            return t.performance && performance.now && performance.now()
        }
        function r(t, e) {
            var n = null;
            return function() {
                n || (n = setTimeout(function() {
                    t(),
                    n = null
                }, e))
            }
        }
        function s(t, e, n, o) {
            "function" == typeof t.addEventListener ? t.addEventListener(e, n, o || !1) : "function" == typeof t.attachEvent && t.attachEvent("on" + e, n)
        }
        function h(t, e, n, o) {
            "function" == typeof t.removeEventListener ? t.removeEventListener(e, n, o || !1) : "function" == typeof t.detatchEvent && t.detatchEvent("on" + e, n)
        }
        function c(t, e) {
            var n = Math.max(t.top, e.top)
              , o = Math.min(t.bottom, e.bottom)
              , i = Math.max(t.left, e.left)
              , r = Math.min(t.right, e.right)
              , s = r - i
              , h = o - n;
            return s >= 0 && h >= 0 && {
                top: n,
                bottom: o,
                left: i,
                right: r,
                width: s,
                height: h
            }
        }
        function a(t) {
            var e;
            try {
                e = t.getBoundingClientRect()
            } catch (n) {}
            return e ? (e.width && e.height || (e = {
                top: e.top,
                right: e.right,
                bottom: e.bottom,
                left: e.left,
                width: e.right - e.left,
                height: e.bottom - e.top
            }),
            e) : u()
        }
        function u() {
            return {
                top: 0,
                bottom: 0,
                left: 0,
                right: 0,
                width: 0,
                height: 0
            }
        }
        function l(t, e) {
            for (var n = e; n; ) {
                if (n == t)
                    return !0;
                n = p(n)
            }
            return !1
        }
        function p(t) {
            var e = t.parentNode;
            return e && 11 == e.nodeType && e.host ? e.host : e && e.assignedSlot ? e.assignedSlot.parentNode : e
        }
        if (!("IntersectionObserver"in t && "IntersectionObserverEntry"in t && "intersectionRatio"in t.IntersectionObserverEntry.prototype)) {
            var f = [];
            o.prototype.THROTTLE_TIMEOUT = 100,
            o.prototype.POLL_INTERVAL = null,
            o.prototype.USE_MUTATION_OBSERVER = !0,
            o.prototype.observe = function(t) {
                if (!this._observationTargets.some(function(e) {
                    return e.element == t
                })) {
                    if (!t || 1 != t.nodeType)
                        throw new Error("target must be an Element");
                    this._registerInstance(),
                    this._observationTargets.push({
                        element: t,
                        entry: null
                    }),
                    this._monitorIntersections(),
                    this._checkForIntersections()
                }
            }
            ,
            o.prototype.unobserve = function(t) {
                this._observationTargets = this._observationTargets.filter(function(e) {
                    return e.element != t
                }),
                this._observationTargets.length || (this._unmonitorIntersections(),
                this._unregisterInstance())
            }
            ,
            o.prototype.disconnect = function() {
                this._observationTargets = [],
                this._unmonitorIntersections(),
                this._unregisterInstance()
            }
            ,
            o.prototype.takeRecords = function() {
                var t = this._queuedEntries.slice();
                return this._queuedEntries = [],
                t
            }
            ,
            o.prototype._initThresholds = function(t) {
                var e = t || [0];
                return Array.isArray(e) || (e = [e]),
                e.sort().filter(function(t, e, n) {
                    if ("number" != typeof t || isNaN(t) || t < 0 || t > 1)
                        throw new Error("threshold must be a number between 0 and 1 inclusively");
                    return t !== n[e - 1]
                })
            }
            ,
            o.prototype._parseRootMargin = function(t) {
                var e = t || "0px"
                  , n = e.split(/\s+/).map(function(t) {
                    var e = /^(-?\d*\.?\d+)(px|%)$/.exec(t);
                    if (!e)
                        throw new Error("rootMargin must be specified in pixels or percent");
                    return {
                        value: parseFloat(e[1]),
                        unit: e[2]
                    }
                });
                return n[1] = n[1] || n[0],
                n[2] = n[2] || n[0],
                n[3] = n[3] || n[1],
                n
            }
            ,
            o.prototype._monitorIntersections = function() {
                this._monitoringIntersections || (this._monitoringIntersections = !0,
                this.POLL_INTERVAL ? this._monitoringInterval = setInterval(this._checkForIntersections, this.POLL_INTERVAL) : (s(t, "resize", this._checkForIntersections, !0),
                s(e, "scroll", this._checkForIntersections, !0),
                this.USE_MUTATION_OBSERVER && "MutationObserver"in t && (this._domObserver = new MutationObserver(this._checkForIntersections),
                this._domObserver.observe(e, {
                    attributes: !0,
                    childList: !0,
                    characterData: !0,
                    subtree: !0
                }))))
            }
            ,
            o.prototype._unmonitorIntersections = function() {
                this._monitoringIntersections && (this._monitoringIntersections = !1,
                clearInterval(this._monitoringInterval),
                this._monitoringInterval = null,
                h(t, "resize", this._checkForIntersections, !0),
                h(e, "scroll", this._checkForIntersections, !0),
                this._domObserver && (this._domObserver.disconnect(),
                this._domObserver = null))
            }
            ,
            o.prototype._checkForIntersections = function() {
                var t = this._rootIsInDom()
                  , e = t ? this._getRootRect() : u();
                this._observationTargets.forEach(function(o) {
                    var r = o.element
                      , s = a(r)
                      , h = this._rootContainsTarget(r)
                      , c = o.entry
                      , u = t && h && this._computeTargetAndRootIntersection(r, e)
                      , l = o.entry = new n({
                        time: i(),
                        target: r,
                        boundingClientRect: s,
                        rootBounds: e,
                        intersectionRect: u
                    });
                    c ? t && h ? this._hasCrossedThreshold(c, l) && this._queuedEntries.push(l) : c && c.isIntersecting && this._queuedEntries.push(l) : this._queuedEntries.push(l)
                }, this),
                this._queuedEntries.length && this._callback(this.takeRecords(), this)
            }
            ,
            o.prototype._computeTargetAndRootIntersection = function(n, o) {
                if ("none" != t.getComputedStyle(n).display) {
                    for (var i = a(n), r = i, s = p(n), h = !1; !h; ) {
                        var u = null
                          , l = 1 == s.nodeType ? t.getComputedStyle(s) : {};
                        if ("none" == l.display)
                            return;
                        if (s == this.root || s == e ? (h = !0,
                        u = o) : s != e.body && s != e.documentElement && "visible" != l.overflow && (u = a(s)),
                        u && !(r = c(u, r)))
                            break;
                        s = p(s)
                    }
                    return r
                }
            }
            ,
            o.prototype._getRootRect = function() {
                var t;
                if (this.root)
                    t = a(this.root);
                else {
                    var n = e.documentElement
                      , o = e.body;
                    t = {
                        top: 0,
                        left: 0,
                        right: n.clientWidth || o.clientWidth,
                        width: n.clientWidth || o.clientWidth,
                        bottom: n.clientHeight || o.clientHeight,
                        height: n.clientHeight || o.clientHeight
                    }
                }
                return this._expandRectByRootMargin(t)
            }
            ,
            o.prototype._expandRectByRootMargin = function(t) {
                var e = this._rootMarginValues.map(function(e, n) {
                    return "px" == e.unit ? e.value : e.value * (n % 2 ? t.width : t.height) / 100
                })
                  , n = {
                    top: t.top - e[0],
                    right: t.right + e[1],
                    bottom: t.bottom + e[2],
                    left: t.left - e[3]
                };
                return n.width = n.right - n.left,
                n.height = n.bottom - n.top,
                n
            }
            ,
            o.prototype._hasCrossedThreshold = function(t, e) {
                var n = t && t.isIntersecting ? t.intersectionRatio || 0 : -1
                  , o = e.isIntersecting ? e.intersectionRatio || 0 : -1;
                if (n !== o)
                    for (var i = 0; i < this.thresholds.length; i++) {
                        var r = this.thresholds[i];
                        if (r == n || r == o || r < n != r < o)
                            return !0
                    }
            }
            ,
            o.prototype._rootIsInDom = function() {
                return !this.root || l(e, this.root)
            }
            ,
            o.prototype._rootContainsTarget = function(t) {
                return l(this.root || e, t)
            }
            ,
            o.prototype._registerInstance = function() {
                f.indexOf(this) < 0 && f.push(this)
            }
            ,
            o.prototype._unregisterInstance = function() {
                var t = f.indexOf(this);
                -1 != t && f.splice(t, 1)
            }
            ,
            t.IntersectionObserver = o,
            t.IntersectionObserverEntry = n
        }
    }(window, document);
    !function(e, t) {
        "object" == typeof exports && "undefined" != typeof module ? t(exports) : "function" == typeof define && define.amd ? define(["exports"], t) : (e = "undefined" != typeof globalThis ? globalThis : e || self,
        t(e.ResizeObserver = {}))
    }(this, function(e) {
        "use strict";
        var t, n = [], r = function() {
            return n.some(function(e) {
                return e.activeTargets.length > 0
            })
        }, i = function() {
            return n.some(function(e) {
                return e.skippedTargets.length > 0
            })
        }, o = "ResizeObserver loop completed with undelivered notifications.", s = function() {
            var e;
            "function" == typeof ErrorEvent ? e = new ErrorEvent("error",{
                message: o
            }) : (e = document.createEvent("Event"),
            e.initEvent("error", !1, !1),
            e.message = o),
            window.dispatchEvent(e)
        };
        !function(e) {
            e.BORDER_BOX = "border-box",
            e.CONTENT_BOX = "content-box",
            e.DEVICE_PIXEL_CONTENT_BOX = "device-pixel-content-box"
        }(t || (t = {}));
        var c, a = function(e) {
            return Object.freeze(e)
        }, u = function() {
            function e(e, t) {
                this.inlineSize = e,
                this.blockSize = t,
                a(this)
            }
            return e
        }(), f = function() {
            function e(e, t, n, r) {
                return this.x = e,
                this.y = t,
                this.width = n,
                this.height = r,
                this.top = this.y,
                this.left = this.x,
                this.bottom = this.top + this.height,
                this.right = this.left + this.width,
                a(this)
            }
            return e.prototype.toJSON = function() {
                var e = this;
                return {
                    x: e.x,
                    y: e.y,
                    top: e.top,
                    right: e.right,
                    bottom: e.bottom,
                    left: e.left,
                    width: e.width,
                    height: e.height
                }
            }
            ,
            e.fromRect = function(t) {
                return new e(t.x,t.y,t.width,t.height)
            }
            ,
            e
        }(), h = function(e) {
            return e instanceof SVGElement && "getBBox"in e
        }, d = function(e) {
            if (h(e)) {
                var t = e.getBBox()
                  , n = t.width
                  , r = t.height;
                return !n && !r
            }
            var i = e
              , o = i.offsetWidth
              , s = i.offsetHeight;
            return !(o || s || e.getClientRects().length)
        }, v = function(e) {
            var t, n;
            if (e instanceof Element)
                return !0;
            var r = null === (n = null === (t = e) || void 0 === t ? void 0 : t.ownerDocument) || void 0 === n ? void 0 : n.defaultView;
            return !!(r && e instanceof r.Element)
        }, l = function(e) {
            switch (e.tagName) {
            case "INPUT":
                if ("image" !== e.type)
                    break;
            case "VIDEO":
            case "AUDIO":
            case "EMBED":
            case "OBJECT":
            case "CANVAS":
            case "IFRAME":
            case "IMG":
                return !0
            }
            return !1
        }, p = "undefined" != typeof window ? window : {}, b = new WeakMap, g = /auto|scroll/, w = /^tb|vertical/, E = /msie|trident/i.test(p.navigator && p.navigator.userAgent), x = function(e) {
            return parseFloat(e || "0")
        }, y = function(e, t, n) {
            return void 0 === e && (e = 0),
            void 0 === t && (t = 0),
            void 0 === n && (n = !1),
            new u((n ? t : e) || 0,(n ? e : t) || 0)
        }, z = a({
            devicePixelContentBoxSize: y(),
            borderBoxSize: y(),
            contentBoxSize: y(),
            contentRect: new f(0,0,0,0)
        }), T = function(e, t) {
            if (void 0 === t && (t = !1),
            b.has(e) && !t)
                return b.get(e);
            if (d(e))
                return b.set(e, z),
                z;
            var n = getComputedStyle(e)
              , r = h(e) && e.ownerSVGElement && e.getBBox()
              , i = !E && "border-box" === n.boxSizing
              , o = w.test(n.writingMode || "")
              , s = !r && g.test(n.overflowY || "")
              , c = !r && g.test(n.overflowX || "")
              , u = r ? 0 : x(n.paddingTop)
              , v = r ? 0 : x(n.paddingRight)
              , l = r ? 0 : x(n.paddingBottom)
              , p = r ? 0 : x(n.paddingLeft)
              , T = r ? 0 : x(n.borderTopWidth)
              , m = r ? 0 : x(n.borderRightWidth)
              , O = r ? 0 : x(n.borderBottomWidth)
              , R = r ? 0 : x(n.borderLeftWidth)
              , S = p + v
              , B = u + l
              , C = R + m
              , k = T + O
              , N = c ? e.offsetHeight - k - e.clientHeight : 0
              , D = s ? e.offsetWidth - C - e.clientWidth : 0
              , M = i ? S + C : 0
              , _ = i ? B + k : 0
              , I = r ? r.width : x(n.width) - M - D
              , P = r ? r.height : x(n.height) - _ - N
              , F = I + S + D + C
              , L = P + B + N + k
              , W = a({
                devicePixelContentBoxSize: y(Math.round(I * devicePixelRatio), Math.round(P * devicePixelRatio), o),
                borderBoxSize: y(F, L, o),
                contentBoxSize: y(I, P, o),
                contentRect: new f(p,u,I,P)
            });
            return b.set(e, W),
            W
        }, m = function(e, n, r) {
            var i = T(e, r)
              , o = i.borderBoxSize
              , s = i.contentBoxSize
              , c = i.devicePixelContentBoxSize;
            switch (n) {
            case t.DEVICE_PIXEL_CONTENT_BOX:
                return c;
            case t.BORDER_BOX:
                return o;
            default:
                return s
            }
        }, O = function() {
            function e(e) {
                var t = T(e);
                this.target = e,
                this.contentRect = t.contentRect,
                this.borderBoxSize = a([t.borderBoxSize]),
                this.contentBoxSize = a([t.contentBoxSize]),
                this.devicePixelContentBoxSize = a([t.devicePixelContentBoxSize])
            }
            return e
        }(), R = function(e) {
            if (d(e))
                return Infinity;
            for (var t = 0, n = e.parentNode; n; )
                t += 1,
                n = n.parentNode;
            return t
        }, S = function() {
            var e = Infinity
              , t = [];
            n.forEach(function o(n) {
                if (0 !== n.activeTargets.length) {
                    var r = [];
                    n.activeTargets.forEach(function i(t) {
                        var n = new O(t.target)
                          , i = R(t.target);
                        r.push(n),
                        t.lastReportedSize = m(t.target, t.observedBox),
                        i < e && (e = i)
                    }),
                    t.push(function o() {
                        n.callback.call(n.observer, r, n.observer)
                    }),
                    n.activeTargets.splice(0, n.activeTargets.length)
                }
            });
            for (var r = 0, i = t; r < i.length; r++) {
                (0,
                i[r])()
            }
            return e
        }, B = function(e) {
            n.forEach(function t(n) {
                n.activeTargets.splice(0, n.activeTargets.length),
                n.skippedTargets.splice(0, n.skippedTargets.length),
                n.observationTargets.forEach(function r(t) {
                    t.isActive() && (R(t.target) > e ? n.activeTargets.push(t) : n.skippedTargets.push(t))
                })
            })
        }, C = function() {
            var e = 0;
            for (B(e); r(); )
                e = S(),
                B(e);
            return i() && s(),
            e > 0
        }, k = [], N = function() {
            return k.splice(0).forEach(function(e) {
                return e()
            })
        }, D = function(e) {
            if (!c) {
                var t = 0
                  , n = document.createTextNode("")
                  , r = {
                    characterData: !0
                };
                new MutationObserver(function() {
                    return N()
                }
                ).observe(n, r),
                c = function() {
                    n.textContent = "" + (t ? t-- : t++)
                }
            }
            k.push(e),
            c()
        }, M = function(e) {
            D(function t() {
                requestAnimationFrame(e)
            })
        }, _ = 0, I = function() {
            return !!_
        }, P = {
            attributes: !0,
            characterData: !0,
            childList: !0,
            subtree: !0
        }, F = ["resize", "load", "transitionend", "animationend", "animationstart", "animationiteration", "keyup", "keydown", "mouseup", "mousedown", "mouseover", "mouseout", "blur", "focus"], L = function(e) {
            return void 0 === e && (e = 0),
            Date.now() + e
        }, W = !1, X = function() {
            function e() {
                var e = this;
                this.stopped = !0,
                this.listener = function() {
                    return e.schedule()
                }
            }
            return e.prototype.run = function(e) {
                var t = this;
                if (void 0 === e && (e = 250),
                !W) {
                    W = !0;
                    var n = L(e);
                    M(function() {
                        var r = !1;
                        try {
                            r = C()
                        } finally {
                            if (W = !1,
                            e = n - L(),
                            !I())
                                return;
                            r ? t.run(1e3) : e > 0 ? t.run(e) : t.start()
                        }
                    })
                }
            }
            ,
            e.prototype.schedule = function() {
                this.stop(),
                this.run()
            }
            ,
            e.prototype.observe = function() {
                var e = this
                  , t = function() {
                    return e.observer && e.observer.observe(document.body, P)
                };
                document.body ? t() : p.addEventListener("DOMContentLoaded", t)
            }
            ,
            e.prototype.start = function() {
                var e = this;
                this.stopped && (this.stopped = !1,
                this.observer = new MutationObserver(this.listener),
                this.observe(),
                F.forEach(function(t) {
                    return p.addEventListener(t, e.listener, !0)
                }))
            }
            ,
            e.prototype.stop = function() {
                var e = this;
                this.stopped || (this.observer && this.observer.disconnect(),
                F.forEach(function(t) {
                    return p.removeEventListener(t, e.listener, !0)
                }),
                this.stopped = !0)
            }
            ,
            e
        }(), A = new X, V = function(e) {
            !_ && e > 0 && A.start(),
            !(_ += e) && A.stop()
        }, q = function(e) {
            return !h(e) && !l(e) && "inline" === getComputedStyle(e).display
        }, j = function() {
            function e(e, n) {
                this.target = e,
                this.observedBox = n || t.CONTENT_BOX,
                this.lastReportedSize = {
                    inlineSize: 0,
                    blockSize: 0
                }
            }
            return e.prototype.isActive = function() {
                var e = m(this.target, this.observedBox, !0);
                return q(this.target) && (this.lastReportedSize = e),
                this.lastReportedSize.inlineSize !== e.inlineSize || this.lastReportedSize.blockSize !== e.blockSize
            }
            ,
            e
        }(), G = function() {
            function e(e, t) {
                this.activeTargets = [],
                this.skippedTargets = [],
                this.observationTargets = [],
                this.observer = e,
                this.callback = t
            }
            return e
        }(), H = new WeakMap, J = function(e, t) {
            for (var n = 0; n < e.length; n += 1)
                if (e[n].target === t)
                    return n;
            return -1
        }, U = function() {
            function e() {}
            return e.connect = function(e, t) {
                var n = new G(e,t);
                H.set(e, n)
            }
            ,
            e.observe = function(e, t, r) {
                var i = H.get(e)
                  , o = 0 === i.observationTargets.length;
                J(i.observationTargets, t) < 0 && (o && n.push(i),
                i.observationTargets.push(new j(t,r && r.box)),
                V(1),
                A.schedule())
            }
            ,
            e.unobserve = function(e, t) {
                var r = H.get(e)
                  , i = J(r.observationTargets, t)
                  , o = 1 === r.observationTargets.length;
                i >= 0 && (o && n.splice(n.indexOf(r), 1),
                r.observationTargets.splice(i, 1),
                V(-1))
            }
            ,
            e.disconnect = function(e) {
                var t = this
                  , n = H.get(e);
                n.observationTargets.slice().forEach(function(n) {
                    return t.unobserve(e, n.target)
                }),
                n.activeTargets.splice(0, n.activeTargets.length)
            }
            ,
            e
        }(), Y = function() {
            function e(e) {
                if (0 === arguments.length)
                    throw new TypeError("Failed to construct 'ResizeObserver': 1 argument required, but only 0 present.");
                if ("function" != typeof e)
                    throw new TypeError("Failed to construct 'ResizeObserver': The callback provided as parameter 1 is not a function.");
                U.connect(this, e)
            }
            return e.prototype.observe = function(e, t) {
                if (0 === arguments.length)
                    throw new TypeError("Failed to execute 'observe' on 'ResizeObserver': 1 argument required, but only 0 present.");
                if (!v(e))
                    throw new TypeError("Failed to execute 'observe' on 'ResizeObserver': parameter 1 is not of type 'Element");
                U.observe(this, e, t)
            }
            ,
            e.prototype.unobserve = function(e) {
                if (0 === arguments.length)
                    throw new TypeError("Failed to execute 'unobserve' on 'ResizeObserver': 1 argument required, but only 0 present.");
                if (!v(e))
                    throw new TypeError("Failed to execute 'unobserve' on 'ResizeObserver': parameter 1 is not of type 'Element");
                U.unobserve(this, e)
            }
            ,
            e.prototype.disconnect = function() {
                U.disconnect(this)
            }
            ,
            e.toString = function() {
                return "function ResizeObserver () { [polyfill code] }"
            }
            ,
            e
        }();
        e.ResizeObserver = Y,
        e.ResizeObserverEntry = O,
        e.ResizeObserverSize = u,
        Object.defineProperty(e, "__esModule", {
            value: !0
        })
    }),
    self.ResizeObserverEntry = ResizeObserver.ResizeObserverEntry,
    self.ResizeObserver = ResizeObserver.ResizeObserver;
    !function() {
        var t = {}
          , e = {};
        !function(t, e) {
            function n(t) {
                if ("number" == typeof t)
                    return t;
                var e = {};
                for (var n in t)
                    e[n] = t[n];
                return e
            }
            function r() {
                this._delay = 0,
                this._endDelay = 0,
                this._fill = "none",
                this._iterationStart = 0,
                this._iterations = 1,
                this._duration = 0,
                this._playbackRate = 1,
                this._direction = "normal",
                this._easing = "linear",
                this._easingFunction = w
            }
            function i() {
                return t.isDeprecated("Invalid timing inputs", "2016-03-02", "TypeError exceptions will be thrown instead.", !0)
            }
            function o(e, n, i) {
                var o = new r;
                return n && (o.fill = "both",
                o.duration = "auto"),
                "number" != typeof e || isNaN(e) ? void 0 !== e && Object.getOwnPropertyNames(e).forEach(function(n) {
                    if ("auto" != e[n]) {
                        if (("number" == typeof o[n] || "duration" == n) && ("number" != typeof e[n] || isNaN(e[n])))
                            return;
                        if ("fill" == n && -1 == T.indexOf(e[n]))
                            return;
                        if ("direction" == n && -1 == x.indexOf(e[n]))
                            return;
                        if ("playbackRate" == n && 1 !== e[n] && t.isDeprecated("AnimationEffectTiming.playbackRate", "2014-11-28", "Use Animation.playbackRate instead."))
                            return;
                        o[n] = e[n]
                    }
                }) : o.duration = e,
                o
            }
            function a(t) {
                return "number" == typeof t && (t = isNaN(t) ? {
                    duration: 0
                } : {
                    duration: t
                }),
                t
            }
            function s(e, n) {
                return e = t.numericTimingToObject(e),
                o(e, n)
            }
            function u(t, e, n, r) {
                return t < 0 || t > 1 || n < 0 || n > 1 ? w : function(i) {
                    function o(t, e, n) {
                        return 3 * t * (1 - n) * (1 - n) * n + 3 * e * (1 - n) * n * n + n * n * n
                    }
                    if (i <= 0) {
                        var a = 0;
                        return t > 0 ? a = e / t : !e && n > 0 && (a = r / n),
                        a * i
                    }
                    if (i >= 1) {
                        var s = 0;
                        return n < 1 ? s = (r - 1) / (n - 1) : 1 == n && t < 1 && (s = (e - 1) / (t - 1)),
                        1 + s * (i - 1)
                    }
                    for (var u = 0, c = 1; u < c; ) {
                        var l = (u + c) / 2
                          , f = o(t, n, l);
                        if (Math.abs(i - f) < 1e-5)
                            return o(e, r, l);
                        f < i ? u = l : c = l
                    }
                    return o(e, r, l)
                }
            }
            function c(t, e) {
                return function(n) {
                    if (n >= 1)
                        return 1;
                    var r = 1 / t;
                    return (n += e * r) - n % r
                }
            }
            function l(t) {
                R || (R = document.createElement("div").style),
                R.animationTimingFunction = "",
                R.animationTimingFunction = t;
                var e = R.animationTimingFunction;
                if ("" == e && i())
                    throw new TypeError(t + " is not a valid value for easing");
                return e
            }
            function f(t) {
                if ("linear" == t)
                    return w;
                var e = E.exec(t);
                if (e)
                    return u.apply(this, e.slice(1).map(Number));
                var n = O.exec(t);
                if (n)
                    return c(Number(n[1]), S);
                var r = j.exec(t);
                return r ? c(Number(r[1]), {
                    start: N,
                    middle: k,
                    end: S
                }[r[2]]) : P[t] || w
            }
            function d(t) {
                return Math.abs(h(t) / t.playbackRate)
            }
            function h(t) {
                return 0 === t.duration || 0 === t.iterations ? 0 : t.duration * t.iterations
            }
            function p(t, e, n) {
                if (null == e)
                    return M;
                var r = n.delay + t + n.endDelay;
                return e < Math.min(n.delay, r) ? D : e >= Math.min(n.delay + t, r) ? L : C
            }
            function m(t, e, n, r, i) {
                switch (r) {
                case D:
                    return "backwards" == e || "both" == e ? 0 : null;
                case C:
                    return n - i;
                case L:
                    return "forwards" == e || "both" == e ? t : null;
                case M:
                    return null
                }
            }
            function g(t, e, n, r, i) {
                var o = i;
                return 0 === t ? e !== D && (o += n) : o += r / t,
                o
            }
            function v(t, e, n, r, i, o) {
                var a = t === 1 / 0 ? e % 1 : t % 1;
                return 0 !== a || n !== L || 0 === r || 0 === i && 0 !== o || (a = 1),
                a
            }
            function b(t, e, n, r) {
                return t === L && e === 1 / 0 ? 1 / 0 : 1 === n ? Math.floor(r) - 1 : Math.floor(r)
            }
            function _(t, e, n) {
                var r = t;
                if ("normal" !== t && "reverse" !== t) {
                    var i = e;
                    "alternate-reverse" === t && (i += 1),
                    r = "normal",
                    i !== 1 / 0 && i % 2 != 0 && (r = "reverse")
                }
                return "normal" === r ? n : 1 - n
            }
            function y(t, e, n) {
                var r = p(t, e, n)
                  , i = m(t, n.fill, e, r, n.delay);
                if (null === i)
                    return null;
                var o = g(n.duration, r, n.iterations, i, n.iterationStart)
                  , a = v(o, n.iterationStart, r, n.iterations, i, n.duration)
                  , s = b(r, n.iterations, a, o)
                  , u = _(n.direction, s, a);
                return n._easingFunction(u)
            }
            var T = "backwards|forwards|both|none".split("|")
              , x = "reverse|alternate|alternate-reverse".split("|")
              , w = function(t) {
                return t
            };
            r.prototype = {
                _setMember: function(e, n) {
                    this["_" + e] = n,
                    this._effect && (this._effect._timingInput[e] = n,
                    this._effect._timing = t.normalizeTimingInput(this._effect._timingInput),
                    this._effect.activeDuration = t.calculateActiveDuration(this._effect._timing),
                    this._effect._animation && this._effect._animation._rebuildUnderlyingAnimation())
                },
                get playbackRate() {
                    return this._playbackRate
                },
                set delay(t) {
                    this._setMember("delay", t)
                },
                get delay() {
                    return this._delay
                },
                set endDelay(t) {
                    this._setMember("endDelay", t)
                },
                get endDelay() {
                    return this._endDelay
                },
                set fill(t) {
                    this._setMember("fill", t)
                },
                get fill() {
                    return this._fill
                },
                set iterationStart(t) {
                    if ((isNaN(t) || t < 0) && i())
                        throw new TypeError("iterationStart must be a non-negative number, received: " + t);
                    this._setMember("iterationStart", t)
                },
                get iterationStart() {
                    return this._iterationStart
                },
                set duration(t) {
                    if ("auto" != t && (isNaN(t) || t < 0) && i())
                        throw new TypeError("duration must be non-negative or auto, received: " + t);
                    this._setMember("duration", t)
                },
                get duration() {
                    return this._duration
                },
                set direction(t) {
                    this._setMember("direction", t)
                },
                get direction() {
                    return this._direction
                },
                set easing(t) {
                    this._easingFunction = f(l(t)),
                    this._setMember("easing", t)
                },
                get easing() {
                    return this._easing
                },
                set iterations(t) {
                    if ((isNaN(t) || t < 0) && i())
                        throw new TypeError("iterations must be non-negative, received: " + t);
                    this._setMember("iterations", t)
                },
                get iterations() {
                    return this._iterations
                }
            };
            var N = 1
              , k = .5
              , S = 0
              , P = {
                ease: u(.25, .1, .25, 1),
                "ease-in": u(.42, 0, 1, 1),
                "ease-out": u(0, 0, .58, 1),
                "ease-in-out": u(.42, 0, .58, 1),
                "step-start": c(1, N),
                "step-middle": c(1, k),
                "step-end": c(1, S)
            }
              , R = null
              , A = "\\s*(-?\\d+\\.?\\d*|-?\\.\\d+)\\s*"
              , E = new RegExp("cubic-bezier\\(" + A + "," + A + "," + A + "," + A + "\\)")
              , O = /steps\(\s*(\d+)\s*\)/
              , j = /steps\(\s*(\d+)\s*,\s*(start|middle|end)\s*\)/
              , M = 0
              , D = 1
              , L = 2
              , C = 3;
            t.cloneTimingInput = n,
            t.makeTiming = o,
            t.numericTimingToObject = a,
            t.normalizeTimingInput = s,
            t.calculateActiveDuration = d,
            t.calculateIterationProgress = y,
            t.calculatePhase = p,
            t.normalizeEasing = l,
            t.parseEasingFunction = f
        }(t),
        function(t, e) {
            function n(t, e) {
                return t in l ? l[t][e] || e : e
            }
            function r(t) {
                return "display" === t || 0 === t.lastIndexOf("animation", 0) || 0 === t.lastIndexOf("transition", 0)
            }
            function i(t, e, i) {
                if (!r(t)) {
                    var o = s[t];
                    if (o) {
                        u.style[t] = e;
                        for (var a in o) {
                            var c = o[a]
                              , l = u.style[c];
                            i[c] = n(c, l)
                        }
                    } else
                        i[t] = n(t, e)
                }
            }
            function o(t) {
                var e = [];
                for (var n in t)
                    if (!(n in ["easing", "offset", "composite"])) {
                        var r = t[n];
                        Array.isArray(r) || (r = [r]);
                        for (var i, o = r.length, a = 0; a < o; a++)
                            i = {},
                            i.offset = "offset"in t ? t.offset : 1 == o ? 1 : a / (o - 1),
                            "easing"in t && (i.easing = t.easing),
                            "composite"in t && (i.composite = t.composite),
                            i[n] = r[a],
                            e.push(i)
                    }
                return e.sort(function(t, e) {
                    return t.offset - e.offset
                }),
                e
            }
            function a(e) {
                if (null == e)
                    return [];
                window.Symbol && Symbol.iterator && Array.prototype.from && e[Symbol.iterator] && (e = Array.from(e)),
                Array.isArray(e) || (e = o(e));
                for (var n = e.map(function(e) {
                    var n = {};
                    for (var r in e) {
                        var o = e[r];
                        if ("offset" == r) {
                            if (null != o) {
                                if (o = Number(o),
                                !isFinite(o))
                                    throw new TypeError("Keyframe offsets must be numbers.");
                                if (o < 0 || o > 1)
                                    throw new TypeError("Keyframe offsets must be between 0 and 1.")
                            }
                        } else if ("composite" == r) {
                            if ("add" == o || "accumulate" == o)
                                throw {
                                    type: DOMException.NOT_SUPPORTED_ERR,
                                    name: "NotSupportedError",
                                    message: "add compositing is not supported"
                                };
                            if ("replace" != o)
                                throw new TypeError("Invalid composite mode " + o + ".")
                        } else
                            o = "easing" == r ? t.normalizeEasing(o) : "" + o;
                        i(r, o, n)
                    }
                    return void 0 == n.offset && (n.offset = null),
                    void 0 == n.easing && (n.easing = "linear"),
                    n
                }), r = !0, a = -1 / 0, s = 0; s < n.length; s++) {
                    var u = n[s].offset;
                    if (null != u) {
                        if (u < a)
                            throw new TypeError("Keyframes are not loosely sorted by offset. Sort or specify offsets.");
                        a = u
                    } else
                        r = !1
                }
                return n = n.filter(function(t) {
                    return t.offset >= 0 && t.offset <= 1
                }),
                r || function c() {
                    var t = n.length;
                    null == n[t - 1].offset && (n[t - 1].offset = 1),
                    t > 1 && null == n[0].offset && (n[0].offset = 0);
                    for (var e = 0, r = n[0].offset, i = 1; i < t; i++) {
                        var o = n[i].offset;
                        if (null != o) {
                            for (var a = 1; a < i - e; a++)
                                n[e + a].offset = r + (o - r) * a / (i - e);
                            e = i,
                            r = o
                        }
                    }
                }(),
                n
            }
            var s = {
                background: ["backgroundImage", "backgroundPosition", "backgroundSize", "backgroundRepeat", "backgroundAttachment", "backgroundOrigin", "backgroundClip", "backgroundColor"],
                border: ["borderTopColor", "borderTopStyle", "borderTopWidth", "borderRightColor", "borderRightStyle", "borderRightWidth", "borderBottomColor", "borderBottomStyle", "borderBottomWidth", "borderLeftColor", "borderLeftStyle", "borderLeftWidth"],
                borderBottom: ["borderBottomWidth", "borderBottomStyle", "borderBottomColor"],
                borderColor: ["borderTopColor", "borderRightColor", "borderBottomColor", "borderLeftColor"],
                borderLeft: ["borderLeftWidth", "borderLeftStyle", "borderLeftColor"],
                borderRadius: ["borderTopLeftRadius", "borderTopRightRadius", "borderBottomRightRadius", "borderBottomLeftRadius"],
                borderRight: ["borderRightWidth", "borderRightStyle", "borderRightColor"],
                borderTop: ["borderTopWidth", "borderTopStyle", "borderTopColor"],
                borderWidth: ["borderTopWidth", "borderRightWidth", "borderBottomWidth", "borderLeftWidth"],
                flex: ["flexGrow", "flexShrink", "flexBasis"],
                font: ["fontFamily", "fontSize", "fontStyle", "fontVariant", "fontWeight", "lineHeight"],
                margin: ["marginTop", "marginRight", "marginBottom", "marginLeft"],
                outline: ["outlineColor", "outlineStyle", "outlineWidth"],
                padding: ["paddingTop", "paddingRight", "paddingBottom", "paddingLeft"]
            }
              , u = document.createElementNS("http://www.w3.org/1999/xhtml", "div")
              , c = {
                thin: "1px",
                medium: "3px",
                thick: "5px"
            }
              , l = {
                borderBottomWidth: c,
                borderLeftWidth: c,
                borderRightWidth: c,
                borderTopWidth: c,
                fontSize: {
                    "xx-small": "60%",
                    "x-small": "75%",
                    small: "89%",
                    medium: "100%",
                    large: "120%",
                    "x-large": "150%",
                    "xx-large": "200%"
                },
                fontWeight: {
                    normal: "400",
                    bold: "700"
                },
                outlineWidth: c,
                textShadow: {
                    none: "0px 0px 0px transparent"
                },
                boxShadow: {
                    none: "0px 0px 0px 0px transparent"
                }
            };
            t.convertToArrayForm = o,
            t.normalizeKeyframes = a
        }(t),
        function(t) {
            var e = {};
            t.isDeprecated = function(t, n, r, i) {
                var o = i ? "are" : "is"
                  , a = new Date
                  , s = new Date(n);
                return s.setMonth(s.getMonth() + 3),
                !(a < s && (t in e || console.warn("Web Animations: " + t + " " + o + " deprecated and will stop working on " + s.toDateString() + ". " + r),
                e[t] = !0,
                1))
            }
            ,
            t.deprecated = function(e, n, r, i) {
                var o = i ? "are" : "is";
                if (t.isDeprecated(e, n, r, i))
                    throw new Error(e + " " + o + " no longer supported. " + r)
            }
        }(t),
        function() {
            if (document.documentElement.animate) {
                var n = document.documentElement.animate([], 0)
                  , r = !0;
                if (n && (r = !1,
                "play|currentTime|pause|reverse|playbackRate|cancel|finish|startTime|playState".split("|").forEach(function(t) {
                    void 0 === n[t] && (r = !0)
                })),
                !r)
                    return
            }
            !function(t, e, n) {
                function r(t) {
                    for (var e = {}, n = 0; n < t.length; n++)
                        for (var r in t[n])
                            if ("offset" != r && "easing" != r && "composite" != r) {
                                var i = {
                                    offset: t[n].offset,
                                    easing: t[n].easing,
                                    value: t[n][r]
                                };
                                e[r] = e[r] || [],
                                e[r].push(i)
                            }
                    for (var o in e) {
                        var a = e[o];
                        if (0 != a[0].offset || 1 != a[a.length - 1].offset)
                            throw {
                                type: DOMException.NOT_SUPPORTED_ERR,
                                name: "NotSupportedError",
                                message: "Partial keyframes are not supported"
                            }
                    }
                    return e
                }
                function i(n) {
                    var r = [];
                    for (var i in n)
                        for (var o = n[i], a = 0; a < o.length - 1; a++) {
                            var s = a
                              , u = a + 1
                              , c = o[s].offset
                              , l = o[u].offset
                              , f = c
                              , d = l;
                            0 == a && (f = -1 / 0,
                            0 == l && (u = s)),
                            a == o.length - 2 && (d = 1 / 0,
                            1 == c && (s = u)),
                            r.push({
                                applyFrom: f,
                                applyTo: d,
                                startOffset: o[s].offset,
                                endOffset: o[u].offset,
                                easingFunction: t.parseEasingFunction(o[s].easing),
                                property: i,
                                interpolation: e.propertyInterpolation(i, o[s].value, o[u].value)
                            })
                        }
                    return r.sort(function(t, e) {
                        return t.startOffset - e.startOffset
                    }),
                    r
                }
                e.convertEffectInput = function(n) {
                    var o = t.normalizeKeyframes(n)
                      , a = r(o)
                      , s = i(a);
                    return function(t, n) {
                        if (null != n)
                            s.filter(function(t) {
                                return n >= t.applyFrom && n < t.applyTo
                            }).forEach(function(r) {
                                var i = n - r.startOffset
                                  , o = r.endOffset - r.startOffset
                                  , a = 0 == o ? 0 : r.easingFunction(i / o);
                                e.apply(t, r.property, r.interpolation(a))
                            });
                        else
                            for (var r in a)
                                "offset" != r && "easing" != r && "composite" != r && e.clear(t, r)
                    }
                }
            }(t, e),
            function(t, e, n) {
                function r(t) {
                    return t.replace(/-(.)/g, function(t, e) {
                        return e.toUpperCase()
                    })
                }
                function i(t, e, n) {
                    s[n] = s[n] || [],
                    s[n].push([t, e])
                }
                function o(t, e, n) {
                    for (var o = 0; o < n.length; o++)
                        i(t, e, r(n[o]))
                }
                function a(n, i, o) {
                    var a = n;
                    /-/.test(n) && !t.isDeprecated("Hyphenated property names", "2016-03-22", "Use camelCase instead.", !0) && (a = r(n)),
                    "initial" != i && "initial" != o || ("initial" == i && (i = u[a]),
                    "initial" == o && (o = u[a]));
                    for (var c = i == o ? [] : s[a], l = 0; c && l < c.length; l++) {
                        var f = c[l][0](i)
                          , d = c[l][0](o);
                        if (void 0 !== f && void 0 !== d) {
                            var h = c[l][1](f, d);
                            if (h) {
                                var p = e.Interpolation.apply(null, h);
                                return function(t) {
                                    return 0 == t ? i : 1 == t ? o : p(t)
                                }
                            }
                        }
                    }
                    return e.Interpolation(!1, !0, function(t) {
                        return t ? o : i
                    })
                }
                var s = {};
                e.addPropertiesHandler = o;
                var u = {
                    backgroundColor: "transparent",
                    backgroundPosition: "0% 0%",
                    borderBottomColor: "currentColor",
                    borderBottomLeftRadius: "0px",
                    borderBottomRightRadius: "0px",
                    borderBottomWidth: "3px",
                    borderLeftColor: "currentColor",
                    borderLeftWidth: "3px",
                    borderRightColor: "currentColor",
                    borderRightWidth: "3px",
                    borderSpacing: "2px",
                    borderTopColor: "currentColor",
                    borderTopLeftRadius: "0px",
                    borderTopRightRadius: "0px",
                    borderTopWidth: "3px",
                    bottom: "auto",
                    clip: "rect(0px, 0px, 0px, 0px)",
                    color: "black",
                    fontSize: "100%",
                    fontWeight: "400",
                    height: "auto",
                    left: "auto",
                    letterSpacing: "normal",
                    lineHeight: "120%",
                    marginBottom: "0px",
                    marginLeft: "0px",
                    marginRight: "0px",
                    marginTop: "0px",
                    maxHeight: "none",
                    maxWidth: "none",
                    minHeight: "0px",
                    minWidth: "0px",
                    opacity: "1.0",
                    outlineColor: "invert",
                    outlineOffset: "0px",
                    outlineWidth: "3px",
                    paddingBottom: "0px",
                    paddingLeft: "0px",
                    paddingRight: "0px",
                    paddingTop: "0px",
                    right: "auto",
                    strokeDasharray: "none",
                    strokeDashoffset: "0px",
                    textIndent: "0px",
                    textShadow: "0px 0px 0px transparent",
                    top: "auto",
                    transform: "",
                    verticalAlign: "0px",
                    visibility: "visible",
                    width: "auto",
                    wordSpacing: "normal",
                    zIndex: "auto"
                };
                e.propertyInterpolation = a
            }(t, e),
            function(t, e, n) {
                function r(e) {
                    var n = t.calculateActiveDuration(e)
                      , r = function(r) {
                        return t.calculateIterationProgress(n, r, e)
                    };
                    return r._totalDuration = e.delay + n + e.endDelay,
                    r
                }
                e.KeyframeEffect = function(n, i, o, a) {
                    var s, u = r(t.normalizeTimingInput(o)), c = e.convertEffectInput(i), l = function() {
                        c(n, s)
                    };
                    return l._update = function(t) {
                        return null !== (s = u(t))
                    }
                    ,
                    l._clear = function() {
                        c(n, null)
                    }
                    ,
                    l._hasSameTarget = function(t) {
                        return n === t
                    }
                    ,
                    l._target = n,
                    l._totalDuration = u._totalDuration,
                    l._id = a,
                    l
                }
            }(t, e),
            function(t, e) {
                function n(t, e) {
                    return !(!e.namespaceURI || -1 == e.namespaceURI.indexOf("/svg")) && (a in t || (t[a] = /Trident|MSIE|IEMobile|Edge|Android 4/i.test(t.navigator.userAgent)),
                    t[a])
                }
                function r(t, e, n) {
                    n.enumerable = !0,
                    n.configurable = !0,
                    Object.defineProperty(t, e, n)
                }
                function i(t) {
                    this._element = t,
                    this._surrogateStyle = document.createElementNS("http://www.w3.org/1999/xhtml", "div").style,
                    this._style = t.style,
                    this._length = 0,
                    this._isAnimatedProperty = {},
                    this._updateSvgTransformAttr = n(window, t),
                    this._savedTransformAttr = null;
                    for (var e = 0; e < this._style.length; e++) {
                        var r = this._style[e];
                        this._surrogateStyle[r] = this._style[r]
                    }
                    this._updateIndices()
                }
                function o(t) {
                    if (!t._webAnimationsPatchedStyle) {
                        var e = new i(t);
                        try {
                            r(t, "style", {
                                get: function() {
                                    return e
                                }
                            })
                        } catch (e) {
                            t.style._set = function(e, n) {
                                t.style[e] = n
                            }
                            ,
                            t.style._clear = function(e) {
                                t.style[e] = ""
                            }
                        }
                        t._webAnimationsPatchedStyle = t.style
                    }
                }
                var a = "_webAnimationsUpdateSvgTransformAttr"
                  , s = {
                    cssText: 1,
                    length: 1,
                    parentRule: 1
                }
                  , u = {
                    getPropertyCSSValue: 1,
                    getPropertyPriority: 1,
                    getPropertyValue: 1,
                    item: 1,
                    removeProperty: 1,
                    setProperty: 1
                }
                  , c = {
                    removeProperty: 1,
                    setProperty: 1
                };
                i.prototype = {
                    get cssText() {
                        return this._surrogateStyle.cssText
                    },
                    set cssText(t) {
                        for (var e = {}, n = 0; n < this._surrogateStyle.length; n++)
                            e[this._surrogateStyle[n]] = !0;
                        this._surrogateStyle.cssText = t,
                        this._updateIndices();
                        for (var n = 0; n < this._surrogateStyle.length; n++)
                            e[this._surrogateStyle[n]] = !0;
                        for (var r in e)
                            this._isAnimatedProperty[r] || this._style.setProperty(r, this._surrogateStyle.getPropertyValue(r))
                    },
                    get length() {
                        return this._surrogateStyle.length
                    },
                    get parentRule() {
                        return this._style.parentRule
                    },
                    _updateIndices: function() {
                        for (; this._length < this._surrogateStyle.length; )
                            Object.defineProperty(this, this._length, {
                                configurable: !0,
                                enumerable: !1,
                                get: function(t) {
                                    return function() {
                                        return this._surrogateStyle[t]
                                    }
                                }(this._length)
                            }),
                            this._length++;
                        for (; this._length > this._surrogateStyle.length; )
                            this._length--,
                            Object.defineProperty(this, this._length, {
                                configurable: !0,
                                enumerable: !1,
                                value: void 0
                            })
                    },
                    _set: function(e, n) {
                        this._style[e] = n,
                        this._isAnimatedProperty[e] = !0,
                        this._updateSvgTransformAttr && "transform" == t.unprefixedPropertyName(e) && (null == this._savedTransformAttr && (this._savedTransformAttr = this._element.getAttribute("transform")),
                        this._element.setAttribute("transform", t.transformToSvgMatrix(n)))
                    },
                    _clear: function(e) {
                        this._style[e] = this._surrogateStyle[e],
                        this._updateSvgTransformAttr && "transform" == t.unprefixedPropertyName(e) && (this._savedTransformAttr ? this._element.setAttribute("transform", this._savedTransformAttr) : this._element.removeAttribute("transform"),
                        this._savedTransformAttr = null),
                        delete this._isAnimatedProperty[e]
                    }
                };
                for (var l in u)
                    i.prototype[l] = function(t, e) {
                        return function() {
                            var n = this._surrogateStyle[t].apply(this._surrogateStyle, arguments);
                            return e && (this._isAnimatedProperty[arguments[0]] || this._style[t].apply(this._style, arguments),
                            this._updateIndices()),
                            n
                        }
                    }(l, l in c);
                for (var f in document.documentElement.style)
                    f in s || f in u || function(t) {
                        r(i.prototype, t, {
                            get: function() {
                                return this._surrogateStyle[t]
                            },
                            set: function(e) {
                                this._surrogateStyle[t] = e,
                                this._updateIndices(),
                                this._isAnimatedProperty[t] || (this._style[t] = e)
                            }
                        })
                    }(f);
                t.apply = function(e, n, r) {
                    o(e),
                    e.style._set(t.propertyName(n), r)
                }
                ,
                t.clear = function(e, n) {
                    e._webAnimationsPatchedStyle && e.style._clear(t.propertyName(n))
                }
            }(e),
            function(t) {
                window.Element.prototype.animate = function(e, n) {
                    var r = "";
                    return n && n.id && (r = n.id),
                    t.timeline._play(t.KeyframeEffect(this, e, n, r))
                }
            }(e),
            function(t, e) {
                function n(t, e, r) {
                    if ("number" == typeof t && "number" == typeof e)
                        return t * (1 - r) + e * r;
                    if ("boolean" == typeof t && "boolean" == typeof e)
                        return r < .5 ? t : e;
                    if (t.length == e.length) {
                        for (var i = [], o = 0; o < t.length; o++)
                            i.push(n(t[o], e[o], r));
                        return i
                    }
                    throw "Mismatched interpolation arguments " + t + ":" + e
                }
                t.Interpolation = function(t, e, r) {
                    return function(i) {
                        return r(n(t, e, i))
                    }
                }
            }(e),
            function(t, e) {
                function n(t, e, n) {
                    return Math.max(Math.min(t, n), e)
                }
                function r(e, r, i) {
                    var o = t.dot(e, r);
                    o = n(o, -1, 1);
                    var a = [];
                    if (1 === o)
                        a = e;
                    else
                        for (var s = Math.acos(o), u = 1 * Math.sin(i * s) / Math.sqrt(1 - o * o), c = 0; c < 4; c++)
                            a.push(e[c] * (Math.cos(i * s) - o * u) + r[c] * u);
                    return a
                }
                var i = function() {
                    function t(t, e) {
                        for (var n = [[0, 0, 0, 0], [0, 0, 0, 0], [0, 0, 0, 0], [0, 0, 0, 0]], r = 0; r < 4; r++)
                            for (var i = 0; i < 4; i++)
                                for (var o = 0; o < 4; o++)
                                    n[r][i] += e[r][o] * t[o][i];
                        return n
                    }
                    function e(t) {
                        return 0 == t[0][2] && 0 == t[0][3] && 0 == t[1][2] && 0 == t[1][3] && 0 == t[2][0] && 0 == t[2][1] && 1 == t[2][2] && 0 == t[2][3] && 0 == t[3][2] && 1 == t[3][3]
                    }
                    function n(n, r, i, o, a) {
                        for (var s = [[1, 0, 0, 0], [0, 1, 0, 0], [0, 0, 1, 0], [0, 0, 0, 1]], u = 0; u < 4; u++)
                            s[u][3] = a[u];
                        for (var u = 0; u < 3; u++)
                            for (var c = 0; c < 3; c++)
                                s[3][u] += n[c] * s[c][u];
                        var l = o[0]
                          , f = o[1]
                          , d = o[2]
                          , h = o[3]
                          , p = [[1, 0, 0, 0], [0, 1, 0, 0], [0, 0, 1, 0], [0, 0, 0, 1]];
                        p[0][0] = 1 - 2 * (f * f + d * d),
                        p[0][1] = 2 * (l * f - d * h),
                        p[0][2] = 2 * (l * d + f * h),
                        p[1][0] = 2 * (l * f + d * h),
                        p[1][1] = 1 - 2 * (l * l + d * d),
                        p[1][2] = 2 * (f * d - l * h),
                        p[2][0] = 2 * (l * d - f * h),
                        p[2][1] = 2 * (f * d + l * h),
                        p[2][2] = 1 - 2 * (l * l + f * f),
                        s = t(s, p);
                        var m = [[1, 0, 0, 0], [0, 1, 0, 0], [0, 0, 1, 0], [0, 0, 0, 1]];
                        i[2] && (m[2][1] = i[2],
                        s = t(s, m)),
                        i[1] && (m[2][1] = 0,
                        m[2][0] = i[0],
                        s = t(s, m)),
                        i[0] && (m[2][0] = 0,
                        m[1][0] = i[0],
                        s = t(s, m));
                        for (var u = 0; u < 3; u++)
                            for (var c = 0; c < 3; c++)
                                s[u][c] *= r[u];
                        return e(s) ? [s[0][0], s[0][1], s[1][0], s[1][1], s[3][0], s[3][1]] : s[0].concat(s[1], s[2], s[3])
                    }
                    return n
                }();
                t.composeMatrix = i,
                t.quat = r
            }(e),
            function(t, e, n) {
                t.sequenceNumber = 0;
                var r = function(t, e, n) {
                    this.target = t,
                    this.currentTime = e,
                    this.timelineTime = n,
                    this.type = "finish",
                    this.bubbles = !1,
                    this.cancelable = !1,
                    this.currentTarget = t,
                    this.defaultPrevented = !1,
                    this.eventPhase = Event.AT_TARGET,
                    this.timeStamp = Date.now()
                };
                e.Animation = function(e) {
                    this.id = "",
                    e && e._id && (this.id = e._id),
                    this._sequenceNumber = t.sequenceNumber++,
                    this._currentTime = 0,
                    this._startTime = null,
                    this._paused = !1,
                    this._playbackRate = 1,
                    this._inTimeline = !0,
                    this._finishedFlag = !0,
                    this.onfinish = null,
                    this._finishHandlers = [],
                    this._effect = e,
                    this._inEffect = this._effect._update(0),
                    this._idle = !0,
                    this._currentTimePending = !1
                }
                ,
                e.Animation.prototype = {
                    _ensureAlive: function() {
                        this.playbackRate < 0 && 0 === this.currentTime ? this._inEffect = this._effect._update(-1) : this._inEffect = this._effect._update(this.currentTime),
                        this._inTimeline || !this._inEffect && this._finishedFlag || (this._inTimeline = !0,
                        e.timeline._animations.push(this))
                    },
                    _tickCurrentTime: function(t, e) {
                        t != this._currentTime && (this._currentTime = t,
                        this._isFinished && !e && (this._currentTime = this._playbackRate > 0 ? this._totalDuration : 0),
                        this._ensureAlive())
                    },
                    get currentTime() {
                        return this._idle || this._currentTimePending ? null : this._currentTime
                    },
                    set currentTime(t) {
                        t = +t,
                        isNaN(t) || (e.restart(),
                        this._paused || null == this._startTime || (this._startTime = this._timeline.currentTime - t / this._playbackRate),
                        this._currentTimePending = !1,
                        this._currentTime != t && (this._idle && (this._idle = !1,
                        this._paused = !0),
                        this._tickCurrentTime(t, !0),
                        e.applyDirtiedAnimation(this)))
                    },
                    get startTime() {
                        return this._startTime
                    },
                    set startTime(t) {
                        t = +t,
                        isNaN(t) || this._paused || this._idle || (this._startTime = t,
                        this._tickCurrentTime((this._timeline.currentTime - this._startTime) * this.playbackRate),
                        e.applyDirtiedAnimation(this))
                    },
                    get playbackRate() {
                        return this._playbackRate
                    },
                    set playbackRate(t) {
                        if (t != this._playbackRate) {
                            var n = this.currentTime;
                            this._playbackRate = t,
                            this._startTime = null,
                            "paused" != this.playState && "idle" != this.playState && (this._finishedFlag = !1,
                            this._idle = !1,
                            this._ensureAlive(),
                            e.applyDirtiedAnimation(this)),
                            null != n && (this.currentTime = n)
                        }
                    },
                    get _isFinished() {
                        return !this._idle && (this._playbackRate > 0 && this._currentTime >= this._totalDuration || this._playbackRate < 0 && this._currentTime <= 0)
                    },
                    get _totalDuration() {
                        return this._effect._totalDuration
                    },
                    get playState() {
                        return this._idle ? "idle" : null == this._startTime && !this._paused && 0 != this.playbackRate || this._currentTimePending ? "pending" : this._paused ? "paused" : this._isFinished ? "finished" : "running"
                    },
                    _rewind: function() {
                        if (this._playbackRate >= 0)
                            this._currentTime = 0;
                        else {
                            if (!(this._totalDuration < 1 / 0))
                                throw new DOMException("Unable to rewind negative playback rate animation with infinite duration","InvalidStateError");
                            this._currentTime = this._totalDuration
                        }
                    },
                    play: function() {
                        this._paused = !1,
                        (this._isFinished || this._idle) && (this._rewind(),
                        this._startTime = null),
                        this._finishedFlag = !1,
                        this._idle = !1,
                        this._ensureAlive(),
                        e.applyDirtiedAnimation(this)
                    },
                    pause: function() {
                        this._isFinished || this._paused || this._idle ? this._idle && (this._rewind(),
                        this._idle = !1) : this._currentTimePending = !0,
                        this._startTime = null,
                        this._paused = !0
                    },
                    finish: function() {
                        this._idle || (this.currentTime = this._playbackRate > 0 ? this._totalDuration : 0,
                        this._startTime = this._totalDuration - this.currentTime,
                        this._currentTimePending = !1,
                        e.applyDirtiedAnimation(this))
                    },
                    cancel: function() {
                        this._inEffect && (this._inEffect = !1,
                        this._idle = !0,
                        this._paused = !1,
                        this._finishedFlag = !0,
                        this._currentTime = 0,
                        this._startTime = null,
                        this._effect._update(null),
                        e.applyDirtiedAnimation(this))
                    },
                    reverse: function() {
                        this.playbackRate *= -1,
                        this.play()
                    },
                    addEventListener: function(t, e) {
                        "function" == typeof e && "finish" == t && this._finishHandlers.push(e)
                    },
                    removeEventListener: function(t, e) {
                        if ("finish" == t) {
                            var n = this._finishHandlers.indexOf(e);
                            n >= 0 && this._finishHandlers.splice(n, 1)
                        }
                    },
                    _fireEvents: function(t) {
                        if (this._isFinished) {
                            if (!this._finishedFlag) {
                                var e = new r(this,this._currentTime,t)
                                  , n = this._finishHandlers.concat(this.onfinish ? [this.onfinish] : []);
                                setTimeout(function() {
                                    n.forEach(function(t) {
                                        t.call(e.target, e)
                                    })
                                }, 0),
                                this._finishedFlag = !0
                            }
                        } else
                            this._finishedFlag = !1
                    },
                    _tick: function(t, e) {
                        this._idle || this._paused || (null == this._startTime ? e && (this.startTime = t - this._currentTime / this.playbackRate) : this._isFinished || this._tickCurrentTime((t - this._startTime) * this.playbackRate)),
                        e && (this._currentTimePending = !1,
                        this._fireEvents(t))
                    },
                    get _needsTick() {
                        return this.playState in {
                            pending: 1,
                            running: 1
                        } || !this._finishedFlag
                    },
                    _targetAnimations: function() {
                        var t = this._effect._target;
                        return t._activeAnimations || (t._activeAnimations = []),
                        t._activeAnimations
                    },
                    _markTarget: function() {
                        var t = this._targetAnimations();
                        -1 === t.indexOf(this) && t.push(this)
                    },
                    _unmarkTarget: function() {
                        var t = this._targetAnimations()
                          , e = t.indexOf(this);
                        -1 !== e && t.splice(e, 1)
                    }
                }
            }(t, e),
            function(t, e, n) {
                function r(t) {
                    var e = c;
                    c = [],
                    t < g.currentTime && (t = g.currentTime),
                    g._animations.sort(i),
                    g._animations = s(t, !0, g._animations)[0],
                    e.forEach(function(e) {
                        e[1](t)
                    }),
                    a(),
                    f = void 0
                }
                function i(t, e) {
                    return t._sequenceNumber - e._sequenceNumber
                }
                function o() {
                    this._animations = [],
                    this.currentTime = window.performance && performance.now ? performance.now() : 0
                }
                function a() {
                    p.forEach(function(t) {
                        t()
                    }),
                    p.length = 0
                }
                function s(t, n, r) {
                    m = !0,
                    h = !1,
                    e.timeline.currentTime = t,
                    d = !1;
                    var i = []
                      , o = []
                      , a = []
                      , s = [];
                    return r.forEach(function(e) {
                        e._tick(t, n),
                        e._inEffect ? (o.push(e._effect),
                        e._markTarget()) : (i.push(e._effect),
                        e._unmarkTarget()),
                        e._needsTick && (d = !0);
                        var r = e._inEffect || e._needsTick;
                        e._inTimeline = r,
                        r ? a.push(e) : s.push(e)
                    }),
                    p.push.apply(p, i),
                    p.push.apply(p, o),
                    d && requestAnimationFrame(function() {}),
                    m = !1,
                    [a, s]
                }
                var u = window.requestAnimationFrame
                  , c = []
                  , l = 0;
                window.requestAnimationFrame = function(t) {
                    var e = l++;
                    return 0 == c.length && u(r),
                    c.push([e, t]),
                    e
                }
                ,
                window.cancelAnimationFrame = function(t) {
                    c.forEach(function(e) {
                        e[0] == t && (e[1] = function() {}
                        )
                    })
                }
                ,
                o.prototype = {
                    _play: function(n) {
                        n._timing = t.normalizeTimingInput(n.timing);
                        var r = new e.Animation(n);
                        return r._idle = !1,
                        r._timeline = this,
                        this._animations.push(r),
                        e.restart(),
                        e.applyDirtiedAnimation(r),
                        r
                    }
                };
                var f = void 0
                  , d = !1
                  , h = !1;
                e.restart = function() {
                    return d || (d = !0,
                    requestAnimationFrame(function() {}),
                    h = !0),
                    h
                }
                ,
                e.applyDirtiedAnimation = function(t) {
                    if (!m) {
                        t._markTarget();
                        var n = t._targetAnimations();
                        n.sort(i),
                        s(e.timeline.currentTime, !1, n.slice())[1].forEach(function(t) {
                            var e = g._animations.indexOf(t);
                            -1 !== e && g._animations.splice(e, 1)
                        }),
                        a()
                    }
                }
                ;
                var p = []
                  , m = !1
                  , g = new o;
                e.timeline = g
            }(t, e),
            function(t, e) {
                function n(t, e) {
                    for (var n = 0, r = 0; r < t.length; r++)
                        n += t[r] * e[r];
                    return n
                }
                function r(t, e) {
                    return [t[0] * e[0] + t[4] * e[1] + t[8] * e[2] + t[12] * e[3], t[1] * e[0] + t[5] * e[1] + t[9] * e[2] + t[13] * e[3], t[2] * e[0] + t[6] * e[1] + t[10] * e[2] + t[14] * e[3], t[3] * e[0] + t[7] * e[1] + t[11] * e[2] + t[15] * e[3], t[0] * e[4] + t[4] * e[5] + t[8] * e[6] + t[12] * e[7], t[1] * e[4] + t[5] * e[5] + t[9] * e[6] + t[13] * e[7], t[2] * e[4] + t[6] * e[5] + t[10] * e[6] + t[14] * e[7], t[3] * e[4] + t[7] * e[5] + t[11] * e[6] + t[15] * e[7], t[0] * e[8] + t[4] * e[9] + t[8] * e[10] + t[12] * e[11], t[1] * e[8] + t[5] * e[9] + t[9] * e[10] + t[13] * e[11], t[2] * e[8] + t[6] * e[9] + t[10] * e[10] + t[14] * e[11], t[3] * e[8] + t[7] * e[9] + t[11] * e[10] + t[15] * e[11], t[0] * e[12] + t[4] * e[13] + t[8] * e[14] + t[12] * e[15], t[1] * e[12] + t[5] * e[13] + t[9] * e[14] + t[13] * e[15], t[2] * e[12] + t[6] * e[13] + t[10] * e[14] + t[14] * e[15], t[3] * e[12] + t[7] * e[13] + t[11] * e[14] + t[15] * e[15]]
                }
                function i(t) {
                    var e = t.rad || 0;
                    return ((t.deg || 0) / 360 + (t.grad || 0) / 400 + (t.turn || 0)) * (2 * Math.PI) + e
                }
                function o(t) {
                    switch (t.t) {
                    case "rotatex":
                        var e = i(t.d[0]);
                        return [1, 0, 0, 0, 0, Math.cos(e), Math.sin(e), 0, 0, -Math.sin(e), Math.cos(e), 0, 0, 0, 0, 1];
                    case "rotatey":
                        var e = i(t.d[0]);
                        return [Math.cos(e), 0, -Math.sin(e), 0, 0, 1, 0, 0, Math.sin(e), 0, Math.cos(e), 0, 0, 0, 0, 1];
                    case "rotate":
                    case "rotatez":
                        var e = i(t.d[0]);
                        return [Math.cos(e), Math.sin(e), 0, 0, -Math.sin(e), Math.cos(e), 0, 0, 0, 0, 1, 0, 0, 0, 0, 1];
                    case "rotate3d":
                        var n = t.d[0]
                          , r = t.d[1]
                          , o = t.d[2]
                          , e = i(t.d[3])
                          , a = n * n + r * r + o * o;
                        if (0 === a)
                            n = 1,
                            r = 0,
                            o = 0;
                        else if (1 !== a) {
                            var s = Math.sqrt(a);
                            n /= s,
                            r /= s,
                            o /= s
                        }
                        var u = Math.sin(e / 2)
                          , c = u * Math.cos(e / 2)
                          , l = u * u;
                        return [1 - 2 * (r * r + o * o) * l, 2 * (n * r * l + o * c), 2 * (n * o * l - r * c), 0, 2 * (n * r * l - o * c), 1 - 2 * (n * n + o * o) * l, 2 * (r * o * l + n * c), 0, 2 * (n * o * l + r * c), 2 * (r * o * l - n * c), 1 - 2 * (n * n + r * r) * l, 0, 0, 0, 0, 1];
                    case "scale":
                        return [t.d[0], 0, 0, 0, 0, t.d[1], 0, 0, 0, 0, 1, 0, 0, 0, 0, 1];
                    case "scalex":
                        return [t.d[0], 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1];
                    case "scaley":
                        return [1, 0, 0, 0, 0, t.d[0], 0, 0, 0, 0, 1, 0, 0, 0, 0, 1];
                    case "scalez":
                        return [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, t.d[0], 0, 0, 0, 0, 1];
                    case "scale3d":
                        return [t.d[0], 0, 0, 0, 0, t.d[1], 0, 0, 0, 0, t.d[2], 0, 0, 0, 0, 1];
                    case "skew":
                        var f = i(t.d[0])
                          , d = i(t.d[1]);
                        return [1, Math.tan(d), 0, 0, Math.tan(f), 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1];
                    case "skewx":
                        var e = i(t.d[0]);
                        return [1, 0, 0, 0, Math.tan(e), 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1];
                    case "skewy":
                        var e = i(t.d[0]);
                        return [1, Math.tan(e), 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1];
                    case "translate":
                        var n = t.d[0].px || 0
                          , r = t.d[1].px || 0;
                        return [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, n, r, 0, 1];
                    case "translatex":
                        var n = t.d[0].px || 0;
                        return [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, n, 0, 0, 1];
                    case "translatey":
                        var r = t.d[0].px || 0;
                        return [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, r, 0, 1];
                    case "translatez":
                        var o = t.d[0].px || 0;
                        return [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, o, 1];
                    case "translate3d":
                        var n = t.d[0].px || 0
                          , r = t.d[1].px || 0
                          , o = t.d[2].px || 0;
                        return [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, n, r, o, 1];
                    case "perspective":
                        return [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, t.d[0].px ? -1 / t.d[0].px : 0, 0, 0, 0, 1];
                    case "matrix":
                        return [t.d[0], t.d[1], 0, 0, t.d[2], t.d[3], 0, 0, 0, 0, 1, 0, t.d[4], t.d[5], 0, 1];
                    case "matrix3d":
                        return t.d
                    }
                }
                function a(t) {
                    return 0 === t.length ? [1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1] : t.map(o).reduce(r)
                }
                function s(t) {
                    return [u(a(t))]
                }
                var u = function() {
                    function t(t) {
                        return t[0][0] * t[1][1] * t[2][2] + t[1][0] * t[2][1] * t[0][2] + t[2][0] * t[0][1] * t[1][2] - t[0][2] * t[1][1] * t[2][0] - t[1][2] * t[2][1] * t[0][0] - t[2][2] * t[0][1] * t[1][0]
                    }
                    function e(e) {
                        for (var n = 1 / t(e), r = e[0][0], i = e[0][1], o = e[0][2], a = e[1][0], s = e[1][1], u = e[1][2], c = e[2][0], l = e[2][1], f = e[2][2], d = [[(s * f - u * l) * n, (o * l - i * f) * n, (i * u - o * s) * n, 0], [(u * c - a * f) * n, (r * f - o * c) * n, (o * a - r * u) * n, 0], [(a * l - s * c) * n, (c * i - r * l) * n, (r * s - i * a) * n, 0]], h = [], p = 0; p < 3; p++) {
                            for (var m = 0, g = 0; g < 3; g++)
                                m += e[3][g] * d[g][p];
                            h.push(m)
                        }
                        return h.push(1),
                        d.push(h),
                        d
                    }
                    function r(t) {
                        return [[t[0][0], t[1][0], t[2][0], t[3][0]], [t[0][1], t[1][1], t[2][1], t[3][1]], [t[0][2], t[1][2], t[2][2], t[3][2]], [t[0][3], t[1][3], t[2][3], t[3][3]]]
                    }
                    function i(t, e) {
                        for (var n = [], r = 0; r < 4; r++) {
                            for (var i = 0, o = 0; o < 4; o++)
                                i += t[o] * e[o][r];
                            n.push(i)
                        }
                        return n
                    }
                    function o(t) {
                        var e = a(t);
                        return [t[0] / e, t[1] / e, t[2] / e]
                    }
                    function a(t) {
                        return Math.sqrt(t[0] * t[0] + t[1] * t[1] + t[2] * t[2])
                    }
                    function s(t, e, n, r) {
                        return [n * t[0] + r * e[0], n * t[1] + r * e[1], n * t[2] + r * e[2]]
                    }
                    function u(t, e) {
                        return [t[1] * e[2] - t[2] * e[1], t[2] * e[0] - t[0] * e[2], t[0] * e[1] - t[1] * e[0]]
                    }
                    function c(c) {
                        var l = [c.slice(0, 4), c.slice(4, 8), c.slice(8, 12), c.slice(12, 16)];
                        if (1 !== l[3][3])
                            return null;
                        for (var f = [], d = 0; d < 4; d++)
                            f.push(l[d].slice());
                        for (var d = 0; d < 3; d++)
                            f[d][3] = 0;
                        if (0 === t(f))
                            return null;
                        var h, p = [];
                        l[0][3] || l[1][3] || l[2][3] ? (p.push(l[0][3]),
                        p.push(l[1][3]),
                        p.push(l[2][3]),
                        p.push(l[3][3]),
                        h = i(p, r(e(f)))) : h = [0, 0, 0, 1];
                        var m = l[3].slice(0, 3)
                          , g = [];
                        g.push(l[0].slice(0, 3));
                        var v = [];
                        v.push(a(g[0])),
                        g[0] = o(g[0]);
                        var b = [];
                        g.push(l[1].slice(0, 3)),
                        b.push(n(g[0], g[1])),
                        g[1] = s(g[1], g[0], 1, -b[0]),
                        v.push(a(g[1])),
                        g[1] = o(g[1]),
                        b[0] /= v[1],
                        g.push(l[2].slice(0, 3)),
                        b.push(n(g[0], g[2])),
                        g[2] = s(g[2], g[0], 1, -b[1]),
                        b.push(n(g[1], g[2])),
                        g[2] = s(g[2], g[1], 1, -b[2]),
                        v.push(a(g[2])),
                        g[2] = o(g[2]),
                        b[1] /= v[2],
                        b[2] /= v[2];
                        var _ = u(g[1], g[2]);
                        if (n(g[0], _) < 0)
                            for (var d = 0; d < 3; d++)
                                v[d] *= -1,
                                g[d][0] *= -1,
                                g[d][1] *= -1,
                                g[d][2] *= -1;
                        var y, T, x = g[0][0] + g[1][1] + g[2][2] + 1;
                        return x > 1e-4 ? (y = .5 / Math.sqrt(x),
                        T = [(g[2][1] - g[1][2]) * y, (g[0][2] - g[2][0]) * y, (g[1][0] - g[0][1]) * y, .25 / y]) : g[0][0] > g[1][1] && g[0][0] > g[2][2] ? (y = 2 * Math.sqrt(1 + g[0][0] - g[1][1] - g[2][2]),
                        T = [.25 * y, (g[0][1] + g[1][0]) / y, (g[0][2] + g[2][0]) / y, (g[2][1] - g[1][2]) / y]) : g[1][1] > g[2][2] ? (y = 2 * Math.sqrt(1 + g[1][1] - g[0][0] - g[2][2]),
                        T = [(g[0][1] + g[1][0]) / y, .25 * y, (g[1][2] + g[2][1]) / y, (g[0][2] - g[2][0]) / y]) : (y = 2 * Math.sqrt(1 + g[2][2] - g[0][0] - g[1][1]),
                        T = [(g[0][2] + g[2][0]) / y, (g[1][2] + g[2][1]) / y, .25 * y, (g[1][0] - g[0][1]) / y]),
                        [m, v, b, T, h]
                    }
                    return c
                }();
                t.dot = n,
                t.makeMatrixDecomposition = s,
                t.transformListToMatrix = a
            }(e),
            function(t) {
                function e(t, e) {
                    var n = t.exec(e);
                    if (n)
                        return n = t.ignoreCase ? n[0].toLowerCase() : n[0],
                        [n, e.substr(n.length)]
                }
                function n(t, e) {
                    e = e.replace(/^\s*/, "");
                    var n = t(e);
                    if (n)
                        return [n[0], n[1].replace(/^\s*/, "")]
                }
                function r(t, r, i) {
                    t = n.bind(null, t);
                    for (var o = []; ; ) {
                        var a = t(i);
                        if (!a)
                            return [o, i];
                        if (o.push(a[0]),
                        i = a[1],
                        !(a = e(r, i)) || "" == a[1])
                            return [o, i];
                        i = a[1]
                    }
                }
                function i(t, e) {
                    for (var n = 0, r = 0; r < e.length && (!/\s|,/.test(e[r]) || 0 != n); r++)
                        if ("(" == e[r])
                            n++;
                        else if (")" == e[r] && (n--,
                        0 == n && r++,
                        n <= 0))
                            break;
                    var i = t(e.substr(0, r));
                    return void 0 == i ? void 0 : [i, e.substr(r)]
                }
                function o(t, e) {
                    for (var n = t, r = e; n && r; )
                        n > r ? n %= r : r %= n;
                    return n = t * e / (n + r)
                }
                function a(t) {
                    return function(e) {
                        var n = t(e);
                        return n && (n[0] = void 0),
                        n
                    }
                }
                function s(t, e) {
                    return function(n) {
                        return t(n) || [e, n]
                    }
                }
                function u(e, n) {
                    for (var r = [], i = 0; i < e.length; i++) {
                        var o = t.consumeTrimmed(e[i], n);
                        if (!o || "" == o[0])
                            return;
                        void 0 !== o[0] && r.push(o[0]),
                        n = o[1]
                    }
                    if ("" == n)
                        return r
                }
                function c(t, e, n, r, i) {
                    for (var a = [], s = [], u = [], c = o(r.length, i.length), l = 0; l < c; l++) {
                        var f = e(r[l % r.length], i[l % i.length]);
                        if (!f)
                            return;
                        a.push(f[0]),
                        s.push(f[1]),
                        u.push(f[2])
                    }
                    return [a, s, function(e) {
                        var r = e.map(function(t, e) {
                            return u[e](t)
                        }).join(n);
                        return t ? t(r) : r
                    }
                    ]
                }
                function l(t, e, n) {
                    for (var r = [], i = [], o = [], a = 0, s = 0; s < n.length; s++)
                        if ("function" == typeof n[s]) {
                            var u = n[s](t[a], e[a++]);
                            r.push(u[0]),
                            i.push(u[1]),
                            o.push(u[2])
                        } else
                            !function(t) {
                                r.push(!1),
                                i.push(!1),
                                o.push(function() {
                                    return n[t]
                                })
                            }(s);
                    return [r, i, function(t) {
                        for (var e = "", n = 0; n < t.length; n++)
                            e += o[n](t[n]);
                        return e
                    }
                    ]
                }
                t.consumeToken = e,
                t.consumeTrimmed = n,
                t.consumeRepeated = r,
                t.consumeParenthesised = i,
                t.ignore = a,
                t.optional = s,
                t.consumeList = u,
                t.mergeNestedRepeated = c.bind(null, null),
                t.mergeWrappedNestedRepeated = c,
                t.mergeList = l
            }(e),
            function(t) {
                function e(e) {
                    function n(e) {
                        var n = t.consumeToken(/^inset/i, e);
                        return n ? (r.inset = !0,
                        n) : (n = t.consumeLengthOrPercent(e)) ? (r.lengths.push(n[0]),
                        n) : (n = t.consumeColor(e),
                        n ? (r.color = n[0],
                        n) : void 0)
                    }
                    var r = {
                        inset: !1,
                        lengths: [],
                        color: null
                    }
                      , i = t.consumeRepeated(n, /^/, e);
                    if (i && i[0].length)
                        return [r, i[1]]
                }
                function n(n) {
                    var r = t.consumeRepeated(e, /^,/, n);
                    if (r && "" == r[1])
                        return r[0]
                }
                function r(e, n) {
                    for (; e.lengths.length < Math.max(e.lengths.length, n.lengths.length); )
                        e.lengths.push({
                            px: 0
                        });
                    for (; n.lengths.length < Math.max(e.lengths.length, n.lengths.length); )
                        n.lengths.push({
                            px: 0
                        });
                    if (e.inset == n.inset && !!e.color == !!n.color) {
                        for (var r, i = [], o = [[], 0], a = [[], 0], s = 0; s < e.lengths.length; s++) {
                            var u = t.mergeDimensions(e.lengths[s], n.lengths[s], 2 == s);
                            o[0].push(u[0]),
                            a[0].push(u[1]),
                            i.push(u[2])
                        }
                        if (e.color && n.color) {
                            var c = t.mergeColors(e.color, n.color);
                            o[1] = c[0],
                            a[1] = c[1],
                            r = c[2]
                        }
                        return [o, a, function(t) {
                            for (var n = e.inset ? "inset " : " ", o = 0; o < i.length; o++)
                                n += i[o](t[0][o]) + " ";
                            return r && (n += r(t[1])),
                            n
                        }
                        ]
                    }
                }
                function i(e, n, r, i) {
                    function o(t) {
                        return {
                            inset: t,
                            color: [0, 0, 0, 0],
                            lengths: [{
                                px: 0
                            }, {
                                px: 0
                            }, {
                                px: 0
                            }, {
                                px: 0
                            }]
                        }
                    }
                    for (var a = [], s = [], u = 0; u < r.length || u < i.length; u++) {
                        var c = r[u] || o(i[u].inset)
                          , l = i[u] || o(r[u].inset);
                        a.push(c),
                        s.push(l)
                    }
                    return t.mergeNestedRepeated(e, n, a, s)
                }
                var o = i.bind(null, r, ", ");
                t.addPropertiesHandler(n, o, ["box-shadow", "text-shadow"])
            }(e),
            function(t, e) {
                function n(t) {
                    return t.toFixed(3).replace(/0+$/, "").replace(/\.$/, "")
                }
                function r(t, e, n) {
                    return Math.min(e, Math.max(t, n))
                }
                function i(t) {
                    if (/^\s*[-+]?(\d*\.)?\d+\s*$/.test(t))
                        return Number(t)
                }
                function o(t, e) {
                    return [t, e, n]
                }
                function a(t, e) {
                    if (0 != t)
                        return u(0, 1 / 0)(t, e)
                }
                function s(t, e) {
                    return [t, e, function(t) {
                        return Math.round(r(1, 1 / 0, t))
                    }
                    ]
                }
                function u(t, e) {
                    return function(i, o) {
                        return [i, o, function(i) {
                            return n(r(t, e, i))
                        }
                        ]
                    }
                }
                function c(t) {
                    var e = t.trim().split(/\s*[\s,]\s*/);
                    if (0 !== e.length) {
                        for (var n = [], r = 0; r < e.length; r++) {
                            var o = i(e[r]);
                            if (void 0 === o)
                                return;
                            n.push(o)
                        }
                        return n
                    }
                }
                function l(t, e) {
                    if (t.length == e.length)
                        return [t, e, function(t) {
                            return t.map(n).join(" ")
                        }
                        ]
                }
                function f(t, e) {
                    return [t, e, Math.round]
                }
                t.clamp = r,
                t.addPropertiesHandler(c, l, ["stroke-dasharray"]),
                t.addPropertiesHandler(i, u(0, 1 / 0), ["border-image-width", "line-height"]),
                t.addPropertiesHandler(i, u(0, 1), ["opacity", "shape-image-threshold"]),
                t.addPropertiesHandler(i, a, ["flex-grow", "flex-shrink"]),
                t.addPropertiesHandler(i, s, ["orphans", "widows"]),
                t.addPropertiesHandler(i, f, ["z-index"]),
                t.parseNumber = i,
                t.parseNumberList = c,
                t.mergeNumbers = o,
                t.numberToString = n
            }(e),
            function(t, e) {
                function n(t, e) {
                    if ("visible" == t || "visible" == e)
                        return [0, 1, function(n) {
                            return n <= 0 ? t : n >= 1 ? e : "visible"
                        }
                        ]
                }
                t.addPropertiesHandler(String, n, ["visibility"])
            }(e),
            function(t, e) {
                function n(t) {
                    t = t.trim(),
                    o.fillStyle = "#000",
                    o.fillStyle = t;
                    var e = o.fillStyle;
                    if (o.fillStyle = "#fff",
                    o.fillStyle = t,
                    e == o.fillStyle) {
                        o.fillRect(0, 0, 1, 1);
                        var n = o.getImageData(0, 0, 1, 1).data;
                        o.clearRect(0, 0, 1, 1);
                        var r = n[3] / 255;
                        return [n[0] * r, n[1] * r, n[2] * r, r]
                    }
                }
                function r(e, n) {
                    return [e, n, function(e) {
                        if (e[3])
                            for (var n = 0; n < 3; n++)
                                e[n] = Math.round(function r(t) {
                                    return Math.max(0, Math.min(255, t))
                                }(e[n] / e[3]));
                        return e[3] = t.numberToString(t.clamp(0, 1, e[3])),
                        "rgba(" + e.join(",") + ")"
                    }
                    ]
                }
                var i = document.createElementNS("http://www.w3.org/1999/xhtml", "canvas");
                i.width = i.height = 1;
                var o = i.getContext("2d");
                t.addPropertiesHandler(n, r, ["background-color", "border-bottom-color", "border-left-color", "border-right-color", "border-top-color", "color", "fill", "flood-color", "lighting-color", "outline-color", "stop-color", "stroke", "text-decoration-color"]),
                t.consumeColor = t.consumeParenthesised.bind(null, n),
                t.mergeColors = r
            }(e),
            function(t, e) {
                function n(t) {
                    function e() {
                        var e = s.exec(t);
                        a = e ? e[0] : void 0
                    }
                    function n() {
                        var t = Number(a);
                        return e(),
                        t
                    }
                    function r() {
                        if ("(" !== a)
                            return n();
                        e();
                        var t = o();
                        return ")" !== a ? NaN : (e(),
                        t)
                    }
                    function i() {
                        for (var t = r(); "*" === a || "/" === a; ) {
                            var n = a;
                            e();
                            var i = r();
                            "*" === n ? t *= i : t /= i
                        }
                        return t
                    }
                    function o() {
                        for (var t = i(); "+" === a || "-" === a; ) {
                            var n = a;
                            e();
                            var r = i();
                            "+" === n ? t += r : t -= r
                        }
                        return t
                    }
                    var a, s = /([\+\-\w\.]+|[\(\)\*\/])/g;
                    return e(),
                    o()
                }
                function r(t, e) {
                    if ("0" == (e = e.trim().toLowerCase()) && "px".search(t) >= 0)
                        return {
                            px: 0
                        };
                    if (/^[^(]*$|^calc/.test(e)) {
                        e = e.replace(/calc\(/g, "(");
                        var r = {};
                        e = e.replace(t, function(t) {
                            return r[t] = null,
                            "U" + t
                        });
                        for (var i = "U(" + t.source + ")", o = e.replace(/[-+]?(\d*\.)?\d+([Ee][-+]?\d+)?/g, "N").replace(new RegExp("N" + i,"g"), "D").replace(/\s[+-]\s/g, "O").replace(/\s/g, ""), a = [/N\*(D)/g, /(N|D)[*\/]N/g, /(N|D)O\1/g, /\((N|D)\)/g], s = 0; s < a.length; )
                            a[s].test(o) ? (o = o.replace(a[s], "$1"),
                            s = 0) : s++;
                        if ("D" == o) {
                            for (var u in r) {
                                var c = n(e.replace(new RegExp("U" + u,"g"), "").replace(new RegExp(i,"g"), "*0"));
                                if (!isFinite(c))
                                    return;
                                r[u] = c
                            }
                            return r
                        }
                    }
                }
                function i(t, e) {
                    return o(t, e, !0)
                }
                function o(e, n, r) {
                    var i, o = [];
                    for (i in e)
                        o.push(i);
                    for (i in n)
                        o.indexOf(i) < 0 && o.push(i);
                    return e = o.map(function(t) {
                        return e[t] || 0
                    }),
                    n = o.map(function(t) {
                        return n[t] || 0
                    }),
                    [e, n, function(e) {
                        var n = e.map(function(n, i) {
                            return 1 == e.length && r && (n = Math.max(n, 0)),
                            t.numberToString(n) + o[i]
                        }).join(" + ");
                        return e.length > 1 ? "calc(" + n + ")" : n
                    }
                    ]
                }
                var a = "px|em|ex|ch|rem|vw|vh|vmin|vmax|cm|mm|in|pt|pc"
                  , s = r.bind(null, new RegExp(a,"g"))
                  , u = r.bind(null, new RegExp(a + "|%","g"))
                  , c = r.bind(null, /deg|rad|grad|turn/g);
                t.parseLength = s,
                t.parseLengthOrPercent = u,
                t.consumeLengthOrPercent = t.consumeParenthesised.bind(null, u),
                t.parseAngle = c,
                t.mergeDimensions = o;
                var l = t.consumeParenthesised.bind(null, s)
                  , f = t.consumeRepeated.bind(void 0, l, /^/)
                  , d = t.consumeRepeated.bind(void 0, f, /^,/);
                t.consumeSizePairList = d;
                var h = function(t) {
                    var e = d(t);
                    if (e && "" == e[1])
                        return e[0]
                }
                  , p = t.mergeNestedRepeated.bind(void 0, i, " ")
                  , m = t.mergeNestedRepeated.bind(void 0, p, ",");
                t.mergeNonNegativeSizePair = p,
                t.addPropertiesHandler(h, m, ["background-size"]),
                t.addPropertiesHandler(u, i, ["border-bottom-width", "border-image-width", "border-left-width", "border-right-width", "border-top-width", "flex-basis", "font-size", "height", "line-height", "max-height", "max-width", "outline-width", "width"]),
                t.addPropertiesHandler(u, o, ["border-bottom-left-radius", "border-bottom-right-radius", "border-top-left-radius", "border-top-right-radius", "bottom", "left", "letter-spacing", "margin-bottom", "margin-left", "margin-right", "margin-top", "min-height", "min-width", "outline-offset", "padding-bottom", "padding-left", "padding-right", "padding-top", "perspective", "right", "shape-margin", "stroke-dashoffset", "text-indent", "top", "vertical-align", "word-spacing"])
            }(e),
            function(t, e) {
                function n(e) {
                    return t.consumeLengthOrPercent(e) || t.consumeToken(/^auto/, e)
                }
                function r(e) {
                    var r = t.consumeList([t.ignore(t.consumeToken.bind(null, /^rect/)), t.ignore(t.consumeToken.bind(null, /^\(/)), t.consumeRepeated.bind(null, n, /^,/), t.ignore(t.consumeToken.bind(null, /^\)/))], e);
                    if (r && 4 == r[0].length)
                        return r[0]
                }
                function i(e, n) {
                    return "auto" == e || "auto" == n ? [!0, !1, function(r) {
                        var i = r ? e : n;
                        if ("auto" == i)
                            return "auto";
                        var o = t.mergeDimensions(i, i);
                        return o[2](o[0])
                    }
                    ] : t.mergeDimensions(e, n)
                }
                function o(t) {
                    return "rect(" + t + ")"
                }
                var a = t.mergeWrappedNestedRepeated.bind(null, o, i, ", ");
                t.parseBox = r,
                t.mergeBoxes = a,
                t.addPropertiesHandler(r, a, ["clip"])
            }(e),
            function(t, e) {
                function n(t) {
                    return function(e) {
                        var n = 0;
                        return t.map(function(t) {
                            return t === l ? e[n++] : t
                        })
                    }
                }
                function r(t) {
                    return t
                }
                function i(e) {
                    if ("none" == (e = e.toLowerCase().trim()))
                        return [];
                    for (var n, r = /\s*(\w+)\(([^)]*)\)/g, i = [], o = 0; n = r.exec(e); ) {
                        if (n.index != o)
                            return;
                        o = n.index + n[0].length;
                        var a = n[1]
                          , s = h[a];
                        if (!s)
                            return;
                        var u = n[2].split(",")
                          , c = s[0];
                        if (c.length < u.length)
                            return;
                        for (var l = [], p = 0; p < c.length; p++) {
                            var m, g = u[p], v = c[p];
                            if (void 0 === (m = g ? {
                                A: function(e) {
                                    return "0" == e.trim() ? d : t.parseAngle(e)
                                },
                                N: t.parseNumber,
                                T: t.parseLengthOrPercent,
                                L: t.parseLength
                            }[v.toUpperCase()](g) : {
                                a: d,
                                n: l[0],
                                t: f
                            }[v]))
                                return;
                            l.push(m)
                        }
                        if (i.push({
                            t: a,
                            d: l
                        }),
                        r.lastIndex == e.length)
                            return i
                    }
                }
                function o(t) {
                    return t.toFixed(6).replace(".000000", "")
                }
                function a(e, n) {
                    if (e.decompositionPair !== n) {
                        e.decompositionPair = n;
                        var r = t.makeMatrixDecomposition(e)
                    }
                    if (n.decompositionPair !== e) {
                        n.decompositionPair = e;
                        var i = t.makeMatrixDecomposition(n)
                    }
                    return null == r[0] || null == i[0] ? [[!1], [!0], function(t) {
                        return t ? n[0].d : e[0].d
                    }
                    ] : (r[0].push(0),
                    i[0].push(1),
                    [r, i, function(e) {
                        var n = t.quat(r[0][3], i[0][3], e[5]);
                        return t.composeMatrix(e[0], e[1], e[2], n, e[4]).map(o).join(",")
                    }
                    ])
                }
                function s(t) {
                    return t.replace(/[xy]/, "")
                }
                function u(t) {
                    return t.replace(/(x|y|z|3d)?$/, "3d")
                }
                function c(e, n) {
                    var r = t.makeMatrixDecomposition && !0
                      , i = !1;
                    if (!e.length || !n.length) {
                        e.length || (i = !0,
                        e = n,
                        n = []);
                        for (var o = 0; o < e.length; o++) {
                            var c = e[o].t
                              , l = e[o].d
                              , f = "scale" == c.substr(0, 5) ? 1 : 0;
                            n.push({
                                t: c,
                                d: l.map(function(t) {
                                    if ("number" == typeof t)
                                        return f;
                                    var e = {};
                                    for (var n in t)
                                        e[n] = f;
                                    return e
                                })
                            })
                        }
                    }
                    var d = []
                      , p = []
                      , m = [];
                    if (e.length != n.length) {
                        if (!r)
                            return;
                        var g = a(e, n);
                        d = [g[0]],
                        p = [g[1]],
                        m = [["matrix", [g[2]]]]
                    } else
                        for (var o = 0; o < e.length; o++) {
                            var c, v = e[o].t, b = n[o].t, _ = e[o].d, y = n[o].d, T = h[v], x = h[b];
                            if (function(t, e) {
                                return "perspective" == t && "perspective" == e || ("matrix" == t || "matrix3d" == t) && ("matrix" == e || "matrix3d" == e)
                            }(v, b)) {
                                if (!r)
                                    return;
                                var g = a([e[o]], [n[o]]);
                                d.push(g[0]),
                                p.push(g[1]),
                                m.push(["matrix", [g[2]]])
                            } else {
                                if (v == b)
                                    c = v;
                                else if (T[2] && x[2] && s(v) == s(b))
                                    c = s(v),
                                    _ = T[2](_),
                                    y = x[2](y);
                                else {
                                    if (!T[1] || !x[1] || u(v) != u(b)) {
                                        if (!r)
                                            return;
                                        var g = a(e, n);
                                        d = [g[0]],
                                        p = [g[1]],
                                        m = [["matrix", [g[2]]]];
                                        break
                                    }
                                    c = u(v),
                                    _ = T[1](_),
                                    y = x[1](y)
                                }
                                for (var w = [], N = [], k = [], S = 0; S < _.length; S++) {
                                    var P = "number" == typeof _[S] ? t.mergeNumbers : t.mergeDimensions
                                      , g = P(_[S], y[S]);
                                    w[S] = g[0],
                                    N[S] = g[1],
                                    k.push(g[2])
                                }
                                d.push(w),
                                p.push(N),
                                m.push([c, k])
                            }
                        }
                    if (i) {
                        var R = d;
                        d = p,
                        p = R
                    }
                    return [d, p, function(t) {
                        return t.map(function(t, e) {
                            var n = t.map(function(t, n) {
                                return m[e][1][n](t)
                            }).join(",");
                            return "matrix" == m[e][0] && 16 == n.split(",").length && (m[e][0] = "matrix3d"),
                            m[e][0] + "(" + n + ")"
                        }).join(" ")
                    }
                    ]
                }
                var l = null
                  , f = {
                    px: 0
                }
                  , d = {
                    deg: 0
                }
                  , h = {
                    matrix: ["NNNNNN", [l, l, 0, 0, l, l, 0, 0, 0, 0, 1, 0, l, l, 0, 1], r],
                    matrix3d: ["NNNNNNNNNNNNNNNN", r],
                    rotate: ["A"],
                    rotatex: ["A"],
                    rotatey: ["A"],
                    rotatez: ["A"],
                    rotate3d: ["NNNA"],
                    perspective: ["L"],
                    scale: ["Nn", n([l, l, 1]), r],
                    scalex: ["N", n([l, 1, 1]), n([l, 1])],
                    scaley: ["N", n([1, l, 1]), n([1, l])],
                    scalez: ["N", n([1, 1, l])],
                    scale3d: ["NNN", r],
                    skew: ["Aa", null, r],
                    skewx: ["A", null, n([l, d])],
                    skewy: ["A", null, n([d, l])],
                    translate: ["Tt", n([l, l, f]), r],
                    translatex: ["T", n([l, f, f]), n([l, f])],
                    translatey: ["T", n([f, l, f]), n([f, l])],
                    translatez: ["L", n([f, f, l])],
                    translate3d: ["TTL", r]
                };
                t.addPropertiesHandler(i, c, ["transform"]),
                t.transformToSvgMatrix = function(e) {
                    var n = t.transformListToMatrix(i(e));
                    return "matrix(" + o(n[0]) + " " + o(n[1]) + " " + o(n[4]) + " " + o(n[5]) + " " + o(n[12]) + " " + o(n[13]) + ")"
                }
            }(e),
            function(t) {
                function e(t) {
                    var e = Number(t);
                    if (!(isNaN(e) || e < 100 || e > 900 || e % 100 != 0))
                        return e
                }
                function n(e) {
                    return e = 100 * Math.round(e / 100),
                    e = t.clamp(100, 900, e),
                    400 === e ? "normal" : 700 === e ? "bold" : String(e)
                }
                function r(t, e) {
                    return [t, e, n]
                }
                t.addPropertiesHandler(e, r, ["font-weight"])
            }(e),
            function(t) {
                function e(t) {
                    var e = {};
                    for (var n in t)
                        e[n] = -t[n];
                    return e
                }
                function n(e) {
                    return t.consumeToken(/^(left|center|right|top|bottom)\b/i, e) || t.consumeLengthOrPercent(e)
                }
                function r(e, r) {
                    var i = t.consumeRepeated(n, /^/, r);
                    if (i && "" == i[1]) {
                        var o = i[0];
                        if (o[0] = o[0] || "center",
                        o[1] = o[1] || "center",
                        3 == e && (o[2] = o[2] || {
                            px: 0
                        }),
                        o.length == e) {
                            if (/top|bottom/.test(o[0]) || /left|right/.test(o[1])) {
                                var s = o[0];
                                o[0] = o[1],
                                o[1] = s
                            }
                            if (/left|right|center|Object/.test(o[0]) && /top|bottom|center|Object/.test(o[1]))
                                return o.map(function(t) {
                                    return "object" == typeof t ? t : a[t]
                                })
                        }
                    }
                }
                function i(r) {
                    var i = t.consumeRepeated(n, /^/, r);
                    if (i) {
                        for (var o = i[0], s = [{
                            "%": 50
                        }, {
                            "%": 50
                        }], u = 0, c = !1, l = 0; l < o.length; l++) {
                            var f = o[l];
                            "string" == typeof f ? (c = /bottom|right/.test(f),
                            u = {
                                left: 0,
                                right: 0,
                                center: u,
                                top: 1,
                                bottom: 1
                            }[f],
                            s[u] = a[f],
                            "center" == f && u++) : (c && (f = e(f),
                            f["%"] = (f["%"] || 0) + 100),
                            s[u] = f,
                            u++,
                            c = !1)
                        }
                        return [s, i[1]]
                    }
                }
                function o(e) {
                    var n = t.consumeRepeated(i, /^,/, e);
                    if (n && "" == n[1])
                        return n[0]
                }
                var a = {
                    left: {
                        "%": 0
                    },
                    center: {
                        "%": 50
                    },
                    right: {
                        "%": 100
                    },
                    top: {
                        "%": 0
                    },
                    bottom: {
                        "%": 100
                    }
                }
                  , s = t.mergeNestedRepeated.bind(null, t.mergeDimensions, " ");
                t.addPropertiesHandler(r.bind(null, 3), s, ["transform-origin"]),
                t.addPropertiesHandler(r.bind(null, 2), s, ["perspective-origin"]),
                t.consumePosition = i,
                t.mergeOffsetList = s;
                var u = t.mergeNestedRepeated.bind(null, s, ", ");
                t.addPropertiesHandler(o, u, ["background-position", "object-position"])
            }(e),
            function(t) {
                function e(e) {
                    var n = t.consumeToken(/^circle/, e);
                    if (n && n[0])
                        return ["circle"].concat(t.consumeList([t.ignore(t.consumeToken.bind(void 0, /^\(/)), r, t.ignore(t.consumeToken.bind(void 0, /^at/)), t.consumePosition, t.ignore(t.consumeToken.bind(void 0, /^\)/))], n[1]));
                    var o = t.consumeToken(/^ellipse/, e);
                    if (o && o[0])
                        return ["ellipse"].concat(t.consumeList([t.ignore(t.consumeToken.bind(void 0, /^\(/)), i, t.ignore(t.consumeToken.bind(void 0, /^at/)), t.consumePosition, t.ignore(t.consumeToken.bind(void 0, /^\)/))], o[1]));
                    var a = t.consumeToken(/^polygon/, e);
                    return a && a[0] ? ["polygon"].concat(t.consumeList([t.ignore(t.consumeToken.bind(void 0, /^\(/)), t.optional(t.consumeToken.bind(void 0, /^nonzero\s*,|^evenodd\s*,/), "nonzero,"), t.consumeSizePairList, t.ignore(t.consumeToken.bind(void 0, /^\)/))], a[1])) : void 0
                }
                function n(e, n) {
                    if (e[0] === n[0])
                        return "circle" == e[0] ? t.mergeList(e.slice(1), n.slice(1), ["circle(", t.mergeDimensions, " at ", t.mergeOffsetList, ")"]) : "ellipse" == e[0] ? t.mergeList(e.slice(1), n.slice(1), ["ellipse(", t.mergeNonNegativeSizePair, " at ", t.mergeOffsetList, ")"]) : "polygon" == e[0] && e[1] == n[1] ? t.mergeList(e.slice(2), n.slice(2), ["polygon(", e[1], a, ")"]) : void 0
                }
                var r = t.consumeParenthesised.bind(null, t.parseLengthOrPercent)
                  , i = t.consumeRepeated.bind(void 0, r, /^/)
                  , o = t.mergeNestedRepeated.bind(void 0, t.mergeDimensions, " ")
                  , a = t.mergeNestedRepeated.bind(void 0, o, ",");
                t.addPropertiesHandler(e, n, ["shape-outside"])
            }(e),
            function(t, e) {
                function n(t, e) {
                    e.concat([t]).forEach(function(e) {
                        e in document.documentElement.style && (r[t] = e),
                        i[e] = t
                    })
                }
                var r = {}
                  , i = {};
                n("transform", ["webkitTransform", "msTransform"]),
                n("transformOrigin", ["webkitTransformOrigin"]),
                n("perspective", ["webkitPerspective"]),
                n("perspectiveOrigin", ["webkitPerspectiveOrigin"]),
                t.propertyName = function(t) {
                    return r[t] || t
                }
                ,
                t.unprefixedPropertyName = function(t) {
                    return i[t] || t
                }
            }(e)
        }(),
        function() {
            if (void 0 === document.createElement("div").animate([]).oncancel) {
                var t;
                if (window.performance && performance.now)
                    var t = function() {
                        return performance.now()
                    };
                else
                    var t = function() {
                        return Date.now()
                    };
                var e = function(t, e, n) {
                    this.target = t,
                    this.currentTime = e,
                    this.timelineTime = n,
                    this.type = "cancel",
                    this.bubbles = !1,
                    this.cancelable = !1,
                    this.currentTarget = t,
                    this.defaultPrevented = !1,
                    this.eventPhase = Event.AT_TARGET,
                    this.timeStamp = Date.now()
                }
                  , n = window.Element.prototype.animate;
                window.Element.prototype.animate = function(r, i) {
                    var o = n.call(this, r, i);
                    o._cancelHandlers = [],
                    o.oncancel = null;
                    var a = o.cancel;
                    o.cancel = function() {
                        a.call(this);
                        var n = new e(this,null,t())
                          , r = this._cancelHandlers.concat(this.oncancel ? [this.oncancel] : []);
                        setTimeout(function() {
                            r.forEach(function(t) {
                                t.call(n.target, n)
                            })
                        }, 0)
                    }
                    ;
                    var s = o.addEventListener;
                    o.addEventListener = function(t, e) {
                        "function" == typeof e && "cancel" == t ? this._cancelHandlers.push(e) : s.call(this, t, e)
                    }
                    ;
                    var u = o.removeEventListener;
                    return o.removeEventListener = function(t, e) {
                        if ("cancel" == t) {
                            var n = this._cancelHandlers.indexOf(e);
                            n >= 0 && this._cancelHandlers.splice(n, 1)
                        } else
                            u.call(this, t, e)
                    }
                    ,
                    o
                }
            }
        }(),
        function(t) {
            var e = document.documentElement
              , n = null
              , r = !1;
            try {
                var i = getComputedStyle(e).getPropertyValue("opacity")
                  , o = "0" == i ? "1" : "0";
                n = e.animate({
                    opacity: [o, o]
                }, {
                    duration: 1
                }),
                n.currentTime = 0,
                r = getComputedStyle(e).getPropertyValue("opacity") == o
            } catch (t) {} finally {
                n && n.cancel()
            }
            if (!r) {
                var a = window.Element.prototype.animate;
                window.Element.prototype.animate = function(e, n) {
                    return window.Symbol && Symbol.iterator && Array.prototype.from && e[Symbol.iterator] && (e = Array.from(e)),
                    Array.isArray(e) || null === e || (e = t.convertToArrayForm(e)),
                    a.call(this, e, n)
                }
            }
        }(t)
    }();
    function Call(t, l) {
        var n = arguments.length > 2 ? arguments[2] : [];
        if (!1 === IsCallable(t))
            throw new TypeError(Object.prototype.toString.call(t) + "is not a function.");
        return t.apply(l, n)
    }
    function CreateMethodProperty(e, r, t) {
        var a = {
            value: t,
            writable: !0,
            enumerable: !1,
            configurable: !0
        };
        Object.defineProperty(e, r, a)
    }
    function Get(n, t) {
        return n[t]
    }
    function IsCallable(n) {
        return "function" == typeof n
    }
    function ToNumber(r) {
        return Number(r)
    }
    function ToIntegerOrInfinity(n) {
        var i = ToNumber(n);
        if (isNaN(i) || 0 === i || 1 / i == -Infinity)
            return 0;
        if (i === Infinity)
            return Infinity;
        if (i === -Infinity)
            return -Infinity;
        var r = Math.floor(Math.abs(i));
        return i < 0 && (r = -r),
        r
    }
    function ToObject(e) {
        if (null === e || e === undefined)
            throw TypeError();
        return Object(e)
    }
    function GetV(t, e) {
        return ToObject(t)[e]
    }
    function GetMethod(e, n) {
        var r = GetV(e, n);
        if (null === r || r === undefined)
            return undefined;
        if (!1 === IsCallable(r))
            throw new TypeError("Method not callable: " + n);
        return r
    }
    function Type(e) {
        switch (typeof e) {
        case "undefined":
            return "undefined";
        case "boolean":
            return "boolean";
        case "number":
            return "number";
        case "string":
            return "string";
        case "symbol":
            return "symbol";
        default:
            return null === e ? "null" : "Symbol"in self && (e instanceof self.Symbol || e.constructor === self.Symbol) ? "symbol" : "object"
        }
    }
    function OrdinaryToPrimitive(r, t) {
        if ("string" === t)
            var e = ["toString", "valueOf"];
        else
            e = ["valueOf", "toString"];
        for (var i = 0; i < e.length; ++i) {
            var n = e[i]
              , a = Get(r, n);
            if (IsCallable(a)) {
                var o = Call(a, r);
                if ("object" !== Type(o))
                    return o
            }
        }
        throw new TypeError("Cannot convert to primitive.")
    }
    function ToInteger(n) {
        if ("symbol" === Type(n))
            throw new TypeError("Cannot convert a Symbol value to a number");
        var t = Number(n);
        return isNaN(t) ? 0 : 1 / t === Infinity || 1 / t == -Infinity || t === Infinity || t === -Infinity ? t : (t < 0 ? -1 : 1) * Math.floor(Math.abs(t))
    }
    function ToLength(n) {
        var t = ToInteger(n);
        return t <= 0 ? 0 : Math.min(t, Math.pow(2, 53) - 1)
    }
    function LengthOfArrayLike(e) {
        return ToLength(Get(e, "length"))
    }
    function ToPrimitive(e) {
        var t = arguments.length > 1 ? arguments[1] : undefined;
        if ("object" === Type(e)) {
            if (arguments.length < 2)
                var i = "default";
            else
                t === String ? i = "string" : t === Number && (i = "number");
            var r = "function" == typeof self.Symbol && "symbol" == typeof self.Symbol.toPrimitive ? GetMethod(e, self.Symbol.toPrimitive) : undefined;
            if (r !== undefined) {
                var n = Call(r, e, [i]);
                if ("object" !== Type(n))
                    return n;
                throw new TypeError("Cannot convert exotic object to primitive.")
            }
            return "default" === i && (i = "number"),
            OrdinaryToPrimitive(e, i)
        }
        return e
    }
    function ToString(t) {
        switch (Type(t)) {
        case "symbol":
            throw new TypeError("Cannot convert a Symbol value to a string");
        case "object":
            return ToString(ToPrimitive(t, String));
        default:
            return String(t)
        }
    }
    CreateMethodProperty(Array.prototype, "at", function t(e) {
        var r = ToObject(this)
          , n = LengthOfArrayLike(r)
          , o = ToIntegerOrInfinity(e)
          , i = o >= 0 ? o : n + o;
        return i < 0 || i >= n ? undefined : Get(r, ToString(i))
    });
}
)('object' === typeof window && window || 'object' === typeof self && self || 'object' === typeof global && global || {});
