import jQuery from "jquery";

!(function (e) {
    e.fn.classyNav = function (n) {
        var s = e(".classy-nav-container"),
            a = e(".classynav ul"),
            o = e(".classynav > ul > li"),
            l = e(".classy-navbar-toggler"),
            i = e(".classycloseIcon"),
            t = e(".navbarToggler"),
            d = e(".classy-menu"),
            r = e(window),
            c = e.extend(
                {
                    theme: "light",
                    breakpoint: 991,
                    openCloseSpeed: 350,
                    megaopenCloseSpeed: 700,
                    alwaysHidden: !1,
                    openMobileMenu: "left",
                    dropdownRtl: !1,
                    stickyNav: !1,
                    stickyFooterNav: !1,
                },
                n
            );
        return this.each(function () {
            function n() {
                window.innerWidth <= c.breakpoint
                    ? s.removeClass("breakpoint-off").addClass("breakpoint-on")
                    : s.removeClass("breakpoint-on").addClass("breakpoint-off");
            }
            ("light" !== c.theme && "dark" !== c.theme) || s.addClass(c.theme),
                ("left" !== c.openMobileMenu && "right" !== c.openMobileMenu) ||
                    s.addClass(c.openMobileMenu),
                !0 === c.dropdownRtl && s.addClass("dropdown-rtl"),
                l.on("click", function () {
                    t.toggleClass("active"), d.toggleClass("menu-on");
                }),
                i.on("click", function () {
                    d.removeClass("menu-on"), t.removeClass("active");
                }),
                o.has(".dropdown").addClass("cn-dropdown-item"),
                o.has(".megamenu").addClass("megamenu-item"),
                a.find("li a").each(function () {
                    e(this).next().length > 0 &&
                        e(this)
                            .parent("li")
                            .addClass("has-down")
                            .append('<span class="dd-trigger"></span>');
                }),
                a.find("li .dd-trigger").on("click", function (n) {
                    n.preventDefault(),
                        e(this)
                            .parent("li")
                            .children("ul")
                            .stop(!0, !0)
                            .slideToggle(c.openCloseSpeed),
                        e(this).parent("li").toggleClass("active");
                }),
                e(".megamenu-item").removeClass("has-down"),
                a.find("li .dd-trigger").on("click", function (n) {
                    n.preventDefault(),
                        e(this)
                            .parent("li")
                            .children(".megamenu")
                            .slideToggle(c.megaopenCloseSpeed);
                }),
                n(),
                r.on("resize", function () {
                    n();
                }),
                !0 === c.alwaysHidden &&
                    s.addClass("breakpoint-on").removeClass("breakpoint-off"),
                !0 === c.stickyNav &&
                    r.on("scroll", function () {
                        r.scrollTop() > 0
                            ? s.addClass("classy-sticky")
                            : s.removeClass("classy-sticky");
                    }),
                !0 === c.stickyFooterNav && s.addClass("classy-sticky-footer");
        });
    };
})(jQuery);
