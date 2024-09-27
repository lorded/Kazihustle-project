String.prototype.format = String.prototype.f = function() {
    for (var a = this, b = arguments.length; b--; )
        a = a.replace(new RegExp("\\{" + b + "\\}","gm"), arguments[b]);
    return a
}
,
function() {
    $("#div_id_code label").on("click", function() {
        $("#div_id_code .controls").slideToggle()
    }),
    $('[data-toggle="tooltip"]').tooltip()
}(),
$(".client-post-assist").on("click", function(a) {
    var b = $(".client-assist-form")
      , c = $(".client-job-form");
    b.is(":hidden") && (b.show("slow"),
    c.hide("slow")),
    $(".assist-chevron").show(),
    $(".assist-container").slideToggle()
}),
$(".client-post-noassist").on("click", function(a) {
    var b = $(".client-assist-form")
      , c = $(".client-job-form");
    c.is(":hidden") && (c.show("slow"),
    b.hide("slow")),
    $(".assist-chevron").show(),
    $(".assist-container").slideToggle()
}),
$(".assist-chevron a").on("click", function(a) {
    var b = $(".assist-container");
    b.is(":hidden") && ($(".assist-container").slideToggle(),
    $(".client-assist-form").hide(),
    $(".client-job-form").hide())
}),
$(function() {
    function a() {
        $(".freelancer-portfolio-slider").length > 0 && $(".freelancer-portfolio-slider").bxSlider({
            pager: !1,
            minSlides: 2,
            maxSlides: 5,
            moveSlides: 2,
            slideWidth: 140,
            slideMargin: 20
        })
    }
    function b() {
        $(".venobox").length > 0 && ($(".venobox").venobox(),
        $(".venobox_custom").venobox({
            framewidth: "400px",
            frameheight: "300px",
            border: "10px",
            bgcolor: "#5dff5e",
            titleattr: "data-title",
            numeratio: !0,
            infinigall: !0
        }),
        $("#firstlink").venobox().trigger("click"))
    }
    $(document).ready(function() {
        a(),
        b(),
        $("#createJobTabs a, #createBidTabs a, #rateReviewTabs a").click(function(a) {
            a.preventDefault(),
            $(this).tab("show")
        }),
        $(".vScroll").length > 0 && $(".vScroll").mCustomScrollbar({
            theme: "rounded"
        })
    }),
    $(window).scroll(function() {
        $(this).scrollTop() > 1 ? $(".how-it-works #sticky").addClass("sticky-header") : $(".how-it-works #sticky").removeClass("sticky-header")
    })
}),
jQuery(document).ready(function(a) {
    function b() {
        c(a(".cd-headline.letters").find("b")),
        d(a(".cd-headline"))
    }
    function c(b) {
        b.each(function() {
            var b = a(this)
              , c = b.text().split("")
              , d = b.hasClass("is-visible");
            for (i in c)
                b.parents(".rotate-2").length > 0 && (c[i] = "<em>" + c[i] + "</em>"),
                c[i] = d ? '<i class="in">' + c[i] + "</i>" : "<i>" + c[i] + "</i>";
            var e = c.join("");
            b.html(e).css("opacity", 1)
        })
    }
    function d(b) {
        var c = l;
        b.each(function() {
            var b = a(this);
            if (b.hasClass("loading-bar"))
                c = m,
                setTimeout(function() {
                    b.find(".cd-words-wrapper").addClass("is-loading")
                }, n);
            else if (b.hasClass("clip")) {
                var d = b.find(".cd-words-wrapper")
                  , f = d.width() + 10;
                d.css("width", f)
            } else if (!b.hasClass("type")) {
                var g = b.find(".cd-words-wrapper b")
                  , h = 0;
                g.each(function() {
                    var b = a(this).width();
                    b > h && (h = b)
                }),
                b.find(".cd-words-wrapper").css("width", h)
            }
            setTimeout(function() {
                e(b.find(".is-visible").eq(0))
            }, c)
        })
    }
    function e(a) {
        var b = j(a);
        if (a.parents(".cd-headline").hasClass("type")) {
            var c = a.parent(".cd-words-wrapper");
            c.addClass("selected").removeClass("waiting"),
            setTimeout(function() {
                c.removeClass("selected"),
                a.removeClass("is-visible").addClass("is-hidden").children("i").removeClass("in").addClass("out")
            }, q),
            setTimeout(function() {
                f(b, p)
            }, r)
        } else if (a.parents(".cd-headline").hasClass("letters")) {
            var d = a.children("i").length >= b.children("i").length;
            g(a.find("i").eq(0), a, d, o),
            h(b.find("i").eq(0), b, d, o)
        } else
            a.parents(".cd-headline").hasClass("clip") ? a.parents(".cd-words-wrapper").animate({
                width: "2px"
            }, s, function() {
                k(a, b),
                f(b)
            }) : a.parents(".cd-headline").hasClass("loading-bar") ? (a.parents(".cd-words-wrapper").removeClass("is-loading"),
            k(a, b),
            setTimeout(function() {
                e(b)
            }, m),
            setTimeout(function() {
                a.parents(".cd-words-wrapper").addClass("is-loading")
            }, n)) : (k(a, b),
            setTimeout(function() {
                e(b)
            }, l))
    }
    function f(a, b) {
        a.parents(".cd-headline").hasClass("type") ? (h(a.find("i").eq(0), a, !1, b),
        a.addClass("is-visible").removeClass("is-hidden")) : a.parents(".cd-headline").hasClass("clip") && a.parents(".cd-words-wrapper").animate({
            width: a.width() + 10
        }, s, function() {
            setTimeout(function() {
                e(a)
            }, t)
        })
    }
    function g(b, c, d, f) {
        if (b.removeClass("in").addClass("out"),
        b.is(":last-child") ? d && setTimeout(function() {
            e(j(c))
        }, l) : setTimeout(function() {
            g(b.next(), c, d, f)
        }, f),
        b.is(":last-child") && a("html").hasClass("no-csstransitions")) {
            var h = j(c);
            k(c, h)
        }
    }
    function h(a, b, c, d) {
        a.addClass("in").removeClass("out"),
        a.is(":last-child") ? (b.parents(".cd-headline").hasClass("type") && setTimeout(function() {
            b.parents(".cd-words-wrapper").addClass("waiting")
        }, 200),
        c || setTimeout(function() {
            e(b)
        }, l)) : setTimeout(function() {
            h(a.next(), b, c, d)
        }, d)
    }
    function j(a) {
        return a.is(":last-child") ? a.parent().children().eq(0) : a.next()
    }
    function k(a, b) {
        a.removeClass("is-visible").addClass("is-hidden"),
        b.removeClass("is-hidden").addClass("is-visible")
    }
    var l = 2500
      , m = 3800
      , n = m - 3e3
      , o = 50
      , p = 150
      , q = 500
      , r = q + 800
      , s = 600
      , t = 1500;
    b()
});