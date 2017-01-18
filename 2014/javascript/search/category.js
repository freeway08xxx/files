/*========初期値設定======= */
var cate = [];
cate.array = ['web', 'img', 'mov', 'pass'];
cate.logoStyle = ['', 'none', 'none', 'none'];
cate.$kwdElem;
var target = [];
target.classes = ['js-search__form-top', 'js-search__form-bottom'];
target.$forms = $('.'+target.classes.join(', .'));
target.isTop;
var getIp;
$(function () {
    var isSearchTopSubmit = false;
    var id;
    var $categories = $('ul[class^=search__tab]');
    var $tabs = $('li', $categories);
    var active_class = 'search__tab--active';

    function CategoryClick(e) {
        $(e).on('click', function () {
            // 上下判定
            target.isTop = $categories.index($(this).parent())===0?true:false;
            if(target.isTop){
                target.myclass = target.classes[0];
            }else{
                target.myclass = target.classes[1];
            }
            // 選択タブのindex
            var idx = $(this).index();
            // 選択タブのClassToggle
            $categories.find('li').removeClass(active_class).end().each(function(){$(this).find('li').eq(idx).addClass(active_class);});

            target.$forms.find('input[name=service]')
            .remove()
            .end()
            .append('<input type="hidden" name="service" value="'+cate.array[idx]+'">')
            .end()
            .find('.js-sprite-search-logo')
            .css('display', cate.logoStyle[idx]);
            target.$forms.find('input[name=service]').val(cate.array[idx]);

            cate.$kwdElem = $('.'+target.myclass+' [name=q]');
            var kwd = cate.$kwdElem .val();
            if ((kwd == "") || (kwd.match(/^[ 　\r\n\t]*$/))) {
                return false;
            } else {
                getIp(kwd);
            }
            return false;
        });
    }
    CategoryClick($tabs);

    function searchSubmit() {
        var isSearchTopSubmit = false;
        // 上下判定
        target.isTop = target.$forms.index($(this))===0?true:false;
        cate.$kwdElem = $('input[type=search]',$(this));
        var kwd = cate.$kwdElem.val();
        if ((kwd == "") || (kwd.match(/^[ 　\r\n\t]*$/))) {
            kwd = "";
            return false;
        } else if (isSearchTopSubmit) {
            return false;
        }
        isSearchTopSubmit = true;
        getIp(kwd);
    }
    $('.js-search-submit', target.$forms).on('click', function(){$(this).parents('form').submit();return false;});
    target.$forms.on('submit', searchSubmit);

    getIp = function(kwd, refine, sortIn) {
        var uuid = (function () {
            var S4 = function () {
                return (((1 + Math.random()) * 0x10000) | 0).toString(16).substring(1)
            };
            return (S4() + S4() + "-" + S4() + "-" + S4() + "-" + S4() + "-" + S4() + S4() + S4())
        });
        $.ajax({
            type: 'GET',
            url: '/getIpAddress?callback=+"uuid"',
            dataType: 'jsonp',
            async: false,
            timeout: 5000,
            error: function (data) {
                var ipAddress = "";
                SubmitHref(kwd, refine, sortIn, ipAddress);
            },
            success: function (data) {
                var ipAddress = data;
                SubmitHref(kwd, refine, sortIn, ipAddress);
            }
        });
    }

    function SubmitHref(kwd, refine, sortIn, ipAddress) {
        if (sortIn == undefined) {
            sortIn = "";
        }
        if (refine == undefined) {
            refine = "";
        }
        var baseCallback = kwd;
        var encs = {
            'img' : Base64.encodeURI(baseCallback.concat("&images")).rot13(),
            'mov' : Base64.encodeURI(baseCallback.concat("&movies")).rot13(),
            'pass' : Base64.encodeURI(baseCallback.concat("&pass")).rot13(),
            'web' : Base64.encodeURI(baseCallback.concat("&web")).rot13(),
        }

        var ua = navigator.userAgent;
        if ((kwd == "") || (kwd == undefined)) {
            var kwd = encodeURI(baseCallback);
        } else {
            encodeURI(kwd);
        }
        /* for movie_common */
        if(cate.$kwdElem==undefined){
            var movie_kwd = $('#search_form_top [name=q]').val();
            var movie_kwd_b = $('#search_form_bottom [name=q]').val();
            if ((movie_kwd == '') && (movie_kwd_b == '')) {
                $('#noKwd').css('display', 'block');
                return false
            } else if ((movie_kwd.length > 0) && (movie_kwd_b.length > 0)) {
                cate.$kwdElem = $('input[type=search]',$('#search_form_bottom'));
            } else if ((movie_kwd.length > 0) && (movie_kwd_b == "")) {
                cate.$kwdElem = $('input[type=search]',$('#search_form_top'));
            } else {
                cate.$kwdElem = $('input[type=search]',$('#search_form_bottom'));
            }
        }
        var sr = cate.$kwdElem.data('sr'),
        client = cate.$kwdElem.data('client'),
        channel;
        if(/Android/.test(ua)){
            if(/Mobile/.test(ua) || cate.service!='web'){
                channel = 'android';
                sr=cate.service!='web' && cate.$kwdElem.data('sr_android')?cate.$kwdElem.data('sr_android'):sr;
            }else{
                channel = 'android-tablet';
            }
        }else if(/iPad/.test(ua) && cate.service=='web'){
            channel = 'iOS-tablet';
        }else{
            /* 当てはまらない場合はiPhone */
            channel = 'iOS';
            sr=cate.service!='web' && cate.$kwdElem.data('sr_ios')?cate.$kwdElem.data('sr_ios'):sr;
        }

        var URLS = {
        'web':'/web/search?',
        'pass':'/pass/search?',
        'img':'/img/search?page=1&',
        'mov':'/mov//search?page=1'+sortIn+refine+'&',
        };

        var service = $('input[name=service]', $(target.$forms[0|!target.isTop])).val();

        var params = [
            'q=' + encodeURIComponent(kwd).replace(/[!'()]/g, escape).replace(/\*/g, "%2A"),
            'sr=' + sr,
            'client='+client,
            'channel=' + channel,
            'query=' + encs[service],
        ];
        location.href=URLS[service] + params.join('&');
    };

});
$('form').submit(function () {
    return false;
});

String.prototype.rot13 = function () {
    return this.replace(/[a-zA-Z]/g, function (c) {
        return String.fromCharCode((c <= "Z" ? 90 : 122) >= (c = c.charCodeAt(0) + 13) ? c : c - 26)
    });
};
$(window).hashchange(function () {
	var url = document.URL;
    if (url.indexOf('image.search.auone.jp') >= 1) {
        cate.service = 'img';
    } else if (url.indexOf('movie.search.auone.jp') >= 1) {
        cate.service = 'mov';
    } else if (url.indexOf('pass.search.auone.jp') >= 1) {
        cate.service = 'pass';
    } else {
        cate.service = 'web';
    }
    target.$forms.find('input[name=service]').remove().end().append('<input type="hidden" name="service" value="'+cate.service+'">');
});

function hashchange() {}
$(window).hashchange();

