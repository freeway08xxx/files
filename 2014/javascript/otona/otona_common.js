(function() {
    this.DEVICETYPE = "";
    this.OSVERSION = "";
    var ua = navigator.userAgent;
    if (ua.indexOf('iPhone') > 0) {
        DEVICETYPE = "iphone";
        ua.match(/iPhone OS (\w+){1,3}/g)
    } else if (ua.indexOf('iPad') > 0) {
        DEVICETYPE = "ipad"
    } else if (ua.indexOf('Android') > 0) {
        DEVICETYPE = "android";
        OSVERSION = ua.substr(ua.indexOf('Android') + 8, 3)
    } else {
        DEVICETYPE = "pc"
    }
    if (DEVICETYPE == "iphone" || DEVICETYPE == "ipad") {
        var iosv = (RegExp.$1.replace(/_/g, '') + '00').slice(0, 3);
        if (iosv >= 600) {
            OSVERSION = 6
        } else if (iosv >= 500) {
            OSVERSION = 5
        } else if (iosv >= 400) {
            OSVERSION = 4
        }
    }
}).call(this);
(function($) {
    $(function() {
        var gl = [];
        var allNum = $('.img').length,
            wrapNum = allNum / getNum;
        for (var i = 0; i <= wrapNum; i++) {
            $('.js-wrap').children('.img:lt(' + getNum + ')').wrapAll('<div class="js_moreWrap clearfix" id="b' + i + '"></div>');
            $('#b' + i).after('<a href="#" class="js_more btn_more" id="m' + i + '">もっとみる</a>')
        }
        $('.js_more:last-of-type').remove();

        function attrImg(blockNum) {
            for (var i = 0; i <= allNum; i++) {
                var original = $('#b' + blockNum + ' .img img').eq(i).data('original');
                $('#b' + blockNum + ' .img img').eq(i).attr('src', original)
            }
        }
        attrImg('0');
        gl.moveMore = $('.js_moveMore').offset().top;
        gl.cut = 0;
        var toTop = $('.to_top');
        var ua = navigator.userAgent;
        if (DEVICETYPE == 'ipad' || (DEVICETYPE == 'android' && ua.indexOf('Mobile') == -1) || ua.indexOf('SMT-i9100') > 0 || (DEVICETYPE == 'iphone' && OSVERSION <= 4) || (DEVICETYPE == 'android' && OSVERSION <= 2.1)) {
            $('.js_moveMore').addClass('static')
        };
        topFixed();

        function topFixed() {
            $(document).scroll(function() {
                gl.scrollHeight = $(document).height();
                var offsetTop = $(this).scrollTop();
                gl.scrollPosition = $(window).height() + offsetTop;
                gl.footHeight = 100;
                if ((gl.scrollHeight - gl.scrollPosition <= gl.footHeight) || (offsetTop <= 100)) {
                    toTop.fadeOut()
                } else {
                    toTop.fadeIn()
                }
                if ((autoMoreBtn === true) && gl.cut <= 1) {
                    gl.Bottom = $('.js_moveMore').offset().top;
                    if (gl.scrollPosition > gl.moveMore) {
                        more($('#m' + gl.cut));
                        gl.moveMore = $('.js_moveMore').offset().top
                    }
                }
            })
        }
        toTop.click(function() {
            scrollTo(0, 1);
            return false
        });
        gl.movbtnClick = function(e) {
            $(e).on('click', function() {
                more($(this));
                return false
            })
        };
        var js_more = $('.js_more');
        gl.movbtnClick($('.js_more'));

        function more(that) {
            gl.cut++;
            that.next('.js_moreWrap').fadeIn();
            var id = that.attr('id').slice(1);
            id = Number(id) + 1;
            $('#m' + id).css('display', 'block');
            attrImg(id);
            that.remove()
        }
    })
})(jQuery);