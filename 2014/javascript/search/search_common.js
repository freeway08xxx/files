var searchLoad = angular.module("searchApp", ['ngAnimate']);
searchLoad.flg = true;
searchLoad.filter('escapeFilter', function() {
    return function(data) {
        var result = '"' + data + '"';
        return result;
    }
})
searchLoad.filter('movClassFilter', function() {
    return function(data) {
        switch (data) {
            case 'Youtube':
                var result = ('youtube');
                break;
            case 'ニコニコ動画':
                var result = ('niconico');
                break;
            case 'Dailymotion':
                var result = ('dailymotion');
                break;
            default:
                result = ('datanone');
                break;
        }
        return result;
    };
});
searchLoad.filter('minFilter', function() {
    return function(data) {
        var result = "";
        var hour = Math.floor((data / 60) / 60);
        var min = Math.floor((data / 60) % 60);
        var sec = Math.floor(data % 60);
        if (hour > 0) {
            result += hour + ":";
        }
        if (min == 0) {
            result += ('00:' + result);
        } else if (min <= 9) {
            result += ('0' + min + ':');
        } else if (min >= 10) {
            result += min + ":";
        }
        if (sec == 0) {
            result += ('00');
        } else if (sec <= 9) {
            result += ('0' + sec);
        } else if (sec >= 10) {
            result += sec;
        }
        if (result === '00:00') {
            result = '--:--';
        }
        return result;
    };
});
searchLoad.filter('makeTopUrlFilter', function() {
    return function(data) {
        var result = "";
        var start = data.indexOf('/', 8);
        result = data.substring(0, start + 1);
        return result;
    };
});
searchLoad.filter('movNameFilter', function() {
    return function(data) {
        if ((data !== 'Youtube') && (data !== 'ニコニコ動画') && (data !== 'Dailymotion')) {
            var result = '掲載サイト';
        } else {
            result = data;
        }
        return result;
    };
});
searchLoad.controller('addMovie', function($scope, $http) {
    var result = [];
    if (1 < document.location.search.length) {
        var q = document.location.search.substring(1);
        var parameters = q.split('&');
        var result = new Object();
        var i;
        for (i = 0; i < parameters.length; i++) {
            var element = parameters[i].split('=');
            var paramName = (element[0]);
            var paramValue = (element[1]);
            result[paramName] = paramValue;
        }
    }
    var query = decodeURIComponent(result['q']);
    if (query == 'undefined') {
        query = '';
    }
    result['q'] = query;
    InKeyWord(query);
    jQuery('.classSearch [name=q]').val(result['q']);
    if ((result['page'] == undefined) || (result['page'] == 1)) {
        var page;
        page = '';
    } else {
        page = '&page=' + (result['page']);
    }
    var nex = '&page=' + (Number(result['page']) + 1);
    var pre = '&page=' + (Number(result['page']) - 1);
    var filter = '&filter=' + result['filter'];
    if (result['filter'] === undefined) {
        filter = '';
    }
    var sortIn = '&sort=' + result['sort'];
    if (result['sort'] === undefined) {
        sortIn = '';
    }
    var ua = navigator.userAgent;
    var URLbaseLink = 'http://sp-movie.search.auone.jp/search';
    var URLbase = 'http://sp-movie.search.auone.jp/searchBase';
    var paraAllMov = URLbase + ('?useragent=' + encodeURI(ua) + filter + sortIn + page + "&query=" + result['query'] + '&callback=JSON_CALLBACK');
    var paraNex = URLbaseLink + ('?q=' + encodeURIComponent(result['q']) + nex + filter + sortIn + "&query=" + result['query']);
    var paraPre = URLbaseLink + ('?q=' + encodeURIComponent(result['q']) + pre + filter + sortIn + "&query=" + result['query']);
    $scope.listMovie = [];
    $http.jsonp(paraAllMov).success(function(data) {
        if (('503') == data.err) {
            location.href = "http://sp-movie.search.auone.jp/error/busyAPI";
            return false;
        } else if ((('404') == data.err) || (101 <= result['page'])) {
            location.href = "http://sp-movie.search.auone.jp/error/movie/404";
            return false;
        }
        if (0 === data.MOVIE.length) {
            goToErr(result);
        } else {
            makePager(pre, paraNex, paraPre, data);
            $scope.listMovie = data.MOVIE;
            GetSS(data);
            makeDisabled(result);
        }
    }).error(function(data) {
        ajxerr();
    });
}).animation('.last', function() {
    afterModules();
});
searchLoad.controller('addImg', function($scope, $http) {
    var result = [];
    if (1 < document.location.search.length) {
        var q = document.location.search.substring(1);
        var parameters = q.split('&');
        var result = new Object();
        var i;
        for (i = 0; i < parameters.length; i++) {
            var element = parameters[i].split('=');
            var paramName = (element[0]);
            var paramValue = (element[1]);
            result[paramName] = paramValue;
        }
    }
    var query = decodeURIComponent(result['q']);
    if (query == 'undefined') {
        query = '';
    }
    result['q'] = query;
    jQuery('.classSearch [name=q]').val(query);
    InKeyWord(query);
    var hash = location.hash.split('&');
    if ((hash[0]) == undefined) {
        return false;
    } else {
        searchLoad.jsonNum = result['page'];
    }
    if ((result['page'] == undefined) || (result['page'] == 1)) {
        var page;
        page = '';
    } else {
        page = '&page=' + (result['page']);
    }
    var nex = '&page=' + (Number(result['page']) + 1);
    var pre = '&page=' + (Number(result['page']) - 1);
    var ua = navigator.userAgent;
    var URLbaseLink = 'http://sp-image.search.auone.jp/search';
    var URLbase = 'http://sp-image.search.auone.jp/searchBase';
    var paraAllImg = URLbase + ('?useragent=' + encodeURI(ua) + page + "&query=" + result['query'] + '&callback=JSON_CALLBACK');
    var paraNex = URLbaseLink + ('?q=' + encodeURIComponent(result['q']) + "&query=" + result['query'] + nex);
    var paraPre = URLbaseLink + ('?q=' + encodeURIComponent(result['q']) + "&query=" + result['query'] + pre);
    $scope.list = [];
    $http.jsonp(paraAllImg).success(function(data) {
        $scope.list = data.IMG;
        if (('503') == data.err) {
            location.href = "http://sp-image.search.auone.jp/error/busyAPI";
            return false;
        } else if ((('404') == data.err) || (84 <= result['page'])) {
            location.href = "http://sp-image.search.auone.jp/error/image/404";
            return false;
        }
        if ((0) == data.IMG.length) {
            goToErr(result);
        }
        if ((83) == result['page']) {
            data.xt = 1;
        }
        makePager(pre, paraNex, paraPre, data);
        GetSS(data);
        makeBox(data);
    }).error(function(data) {
        ajxerr();
    });
}).animation('.last', function() {
    afterModules();
});

function makeBox(data) {
    searchLoad.dataLength = data.IMG.length;
    searchLoad.dataXt = data.xt;
    searchLoad.dataBoxUrl = [];
    searchLoad.dataBoxWidth = [];
    searchLoad.dataBoxHeight = [];
    searchLoad.dataBoxFilesize = [];
    searchLoad.dataBoxOriginalUrl = [];
    searchLoad.dataBoxRefererurl = [];
    searchLoad.dataBoxHostUrl = [];
    searchLoad.dataBoxThumbnailUrl = [];
    searchLoad.dataBoxAbstract = [];
    searchLoad.dataBoxMidleSizeUrl = [];
    for (var i = 0; i < searchLoad.dataLength; i++) {
        searchLoad.dataBoxUrl.push(data.IMG[i].thumbnail_url);
        searchLoad.dataBoxWidth.push(data.IMG[i].width);
        var midleSizeUrl = 'http://cdn-search-img.auone.jp/small_light(p=msize)/images/';
        var start2 = data.IMG[i].url.indexOf('://') + 3;
        if (data.IMG[i].url.substring(start2).indexOf('newspapers.com') >= 1) {
            searchLoad.dataBoxMidleSizeUrl.push(midleSizeUrl + data.IMG[i].thumbnail_url);
        } else {
            searchLoad.dataBoxMidleSizeUrl.push(midleSizeUrl + data.IMG[i].url.substring(start2));
        }
        searchLoad.dataBoxOriginalUrl.push(data.IMG[i].url);
        searchLoad.dataBoxHeight.push(data.IMG[i].height);
        searchLoad.dataBoxFilesize.push(data.IMG[i].filesize);
        searchLoad.dataBoxRefererurl.push(data.IMG[i].refererclickurl);
        var start = data.IMG[i].refererclickurl.indexOf('/', 8);
        searchLoad.dataBoxHostUrl.push(data.IMG[i].refererclickurl.substring(0, start));
        searchLoad.dataBoxThumbnailUrl.push(data.IMG[i].thumbnail_url);
        searchLoad.dataBoxAbstract.push(data.IMG[i].abstract);
    }
    cacheBoxFirst(data);
}

function cacheBoxFirst(data) {
    for (i = 0; i < searchLoad.dataLength; i++) {
        var j = i + 1;
        $('#ImgList a#' + j).html("<img src=" + searchLoad.dataBoxMidleSizeUrl[i] + " class='hide' />");
    }
    $('#ImgList img').error(function() {
        var errNum = [];
        errNum.push($(this).parent('a').attr('id'));
        for (var i = 0; i < errNum.length; i++) {
            searchLoad.dataBoxMidleSizeUrl.splice(errNum - 1, 1, searchLoad.dataBoxThumbnailUrl[errNum - 1]);
        }
    });
}

function InKeyWord(query) {
    if ((document.URL.indexOf('image.search.auone.jp') >= 0)) {
        document.title = '- ' + query + ' - au画像検索';
        $('meta[property="og:description"]').attr('content', 'au画像検索で' + query + 'を検索しよう。');
        $('meta[name="keywords"]').attr('content', query + ',画像,検索,写真,イメージ,image,photo')
    } else if ((document.URL.indexOf('movie.search.auone.jp') >= 0)) {
        document.title = '- ' + query + ' - au動画検索';
        $('meta[property="og:description"]').attr('content', 'au動画検索で' + query + 'を検索しよう。');
        $('meta[name="keywords"]').attr('content', query + ',動画,検索,ビデオ,video,movie');
    }
}

function ajxerr() {
    jQuery('.ad,#img_result,.pager,.search_footer,#search_option,.search__tab-top,#search_top,#movie_result').remove();
    var ajaxErrMsg = '<div id="error">\<p class="left">ただいま繋がりにくい状態となっております。<br>大変申し訳ございませんが、しばらくお待ちいただいてから再度アクセスをお願いします。</p>\</div>';
    jQuery(ajaxErrMsg).insertAfter('header');
    var error = $('#error'),
        errorText = $('#error_text');
    winResize = function() {
        error.css({
            height: 'auto'
        });
        errorText.css({
            height: 'auto'
        });
        var headerHeight = $('header').height(),
            footerHeight = $('footer').height();
        var h = $(window).height() - 130;
        h -= headerHeight;
        h -= footerHeight;
        if (error.height() < (h - footerHeight) || errorText.height() < (h - footerHeight)) {
            error.height(h);
            errorText.height(h);
        }
    };
    $(window).resize(winResize);
    winResize();
}

function goToErr(result) {
    if ((document.URL.indexOf('image.search.auone.jp') >= 0)) {
        var host = '画像';
    } else {
        var host = '動画';
    }
    var query = $('<div />').text(result['q']).html();
    InKeyWord(result['q']);
    jQuery('.ad,#img_result,#movie_result,.pager,.search_footer,#search_option').remove();
    var errMsg = '<div id="error_text"><p><em>"' + query + '"</em>に該当する' + host + 'が見つかりませんでした。</p>                                       <p>検索のヒント:<br>キーワードに入力誤りが無いか確認してみてください。<br>                                               同じ意味で別のキーワードや一般的なキーワードで検索してみてください。<br>                                                  複数のキーワードを入力されている場合には、キーワードを減らして検索してみてください。</p></div>';
    jQuery(errMsg).insertAfter('#search_top');
    var error = $('#error'),
        errorText = $('#error_text');

    function winResize() {
        error.css({
            height: 'auto'
        });
        errorText.css({
            height: 'auto'
        });
        var headerHeight = $("header").height();
        var footerHeight = $("footer").height();
        var h = $(window).height() - 130;
        h -= headerHeight;
        h -= footerHeight;
        if (error.height() < (h - footerHeight) || errorText.height() < (h - footerHeight)) {
            error.height(h);
            errorText.height(h);
        }
    }
    winResize();
    return false;
}

function GetSS(data) {
    var ssTitle = [],
        ssSiteHost = [],
        ssDescription1 = [],
        ssDescription2 = [],
        ssClickurl = [];
    var loopNum = data.SS.length - 1;
    for (var i = 0; i <= data.SS.length - 1; i++) {
        ssTitle.push(data.SS[i]['title']);
        ssSiteHost.push(data.SS[i]['siteHost']);
        ssDescription1.push(data.SS[i]['description1']);
        ssDescription2.push(data.SS[i]['description2']);
        ssClickurl.push(data.SS[i]['clickurl']);
    }
    if (loopNum == 1) {
        jQuery('#ad_area1 .std').removeClass('border');
    }
    for (var i = 0; i <= loopNum; i++) {
        jQuery('#ad_area' + i + ' .std').html('<a href="' + ssClickurl[i] + '" class="title_link" target="_top">' + ssTitle[i] + '</a>\<a href="http://' + ssSiteHost[i] + '" class="domain_link" target="_top">' + ssSiteHost[i] + '</a>\<p class="desc_text">' + ssDescription1[i] + ssDescription2[i] + '</p>');
        jQuery('#ad_area' + i).css('display', 'block');
    }
}

function makeDisabled(result) {
    switch (result['vid3.sort']) {
        case 'MOST_RECENT':
            $("option[name='1']").attr('selected', 'selected');
            break;
        case 'LEAST_RECENT':
            $("option[name='2']").attr('selected', 'selected');
            break;
        case 'MOST_LONG':
            $("option[name='3']").attr('selected', 'selected');
            break;
        case 'LEAST_LONG':
            $("option[name='4']").attr('selected', 'selected');
            break;
        default:
            break;
    }
}

function afterModules() {
    if (document.body.scrollTop == 0) {
        setTimeout("scrollTo(0,1)", 100);
    }
    $('header a[href=#], .category a[href=#], #search_bottom a[href=#]').on('click', function() {
        return false;
    });
    $('.ad,#movie_result,#ImgList,#img_result,.pager').css('visibility', 'visible').removeClass('hide');
    var wrapper = $('#wrapper'),
        imgResult = '#img_result ul',
        imgClick = $('#img_result ul li a'),
        imgExpand = $('#image_expand'),
        originalSite = $('#originalsite'),
        imgHolder = $('#imageholder'),
        innerImg = $('#imageholder img'),
        pager = $('#imageholder a'),
        pagePre = $('#imageholder a#pre'),
        pageNex = $('#imageholder a#nex'),
        closeBtn = $('#close'),
        size = imgClick.size(),
        hashAddFlag = true;
    $('#imageholder p').bind('touchstart touchmove touchend', touchHandler);
    $('#img_result li').bind('click', function() {
        hashAddFlag = true;
    });

    function touchHandler(e) {
        var id = location.hash.slice(6);
        var hash = location.hash.split('&');
        if (hash[1]) {
            var id = hash[0].slice(6);
        }
        if (searchLoad.jsonNum == undefined) {
            searchLoad.jsonNum = 1;
        }
        var liLength = $('#ImgList li').length;
        e.preventDefault();
        var touch = e.originalEvent.touches[0];
        if (e.type == 'touchstart') {
            searchLoad.startX = touch.pageX;
        } else if (e.type == 'touchmove') {
            searchLoad.diffX = touch.pageX - searchLoad.startX;
        } else if (e.type == 'touchend') {
            if (searchLoad.diffX > 80) {
                if (searchLoad.flg == true) {
                    searchLoad.flg = false;
                    var hash = location.hash.split('&');
                    if ((hash[0] == '#image1') && ((searchLoad.jsonNum <= 1))) {
                        pagePre.addClass('hide');
                        id = 1;
                        searchLoad.jsonNum = 1;
                        searchLoad.flg = true;
                        return false;
                    }
                    if (hash[1]) {
                        searchLoad.jsonNum = hash[1].slice(2);
                        var id = hash[0].slice(6);
                    }
                    if (id <= 1) {
                        id = 12;
                        searchLoad.jsonNum = Number(searchLoad.jsonNum) - 1;
                        pagePre.attr('href', '#image' + id + '&p=' + searchLoad.jsonNum);
                        location.hash = 'image' + id + '&p=' + searchLoad.jsonNum;
                        getAjax(id);
                    } else if (id > 1) {
                        id--;
                        pagePre.attr('href', '#image' + id + '&p=' + searchLoad.jsonNum);
                        location.hash = 'image' + id + '&p=' + searchLoad.jsonNum;
                        makeHtml(id);
                        pagerView(id);
                    }
                }
            } else if (searchLoad.diffX < -80) {
                if (searchLoad.flg == true) {
                    searchLoad.flg = false;
                    if ((searchLoad.dataXt == 1) && (id == liLength)) {
                        pageNex.addClass('hide');
                        searchLoad.flg = true;
                        return false;
                    }
                    var hash = location.hash.split('&');
                    if (hash[1]) {
                        searchLoad.jsonNum = hash[1].slice(2);
                        var id = hash[0].slice(6);
                    }
                    if (id >= 12) {
                        id = 1;
                        searchLoad.jsonNum = Number(searchLoad.jsonNum) + 1;
                        pageNex.attr('href', '#image' + id + '&p=' + searchLoad.jsonNum);
                        location.hash = 'image' + id + '&p=' + searchLoad.jsonNum;
                        getAjax(id)
                    } else {
                        id++;
                        pageNex.attr('href', '#image' + id + '&p=' + searchLoad.jsonNum);
                        location.hash = 'image' + id + '&p=' + searchLoad.jsonNum;
                        makeHtml(id);
                        pagerView(id);
                    }
                }
            }
            searchLoad.diffX = 0;
            location.hash = 'image' + id + '&p=' + searchLoad.jsonNum;
        }
    }
    $(window).hashchange(function() {
        if (searchLoad.jsonNum == undefined) {
            searchLoad.jsonNum = 1;
        }
        var hash = location.hash;
        var liLength = $('#ImgList li').length;
        if (hashAddFlag == true) {
            imgClick.each(function() {
                if ($(this).attr('href') == hash) {
                    var id = parseInt($(this).attr('id'));
                    wrapper.addClass('hide');
                    imgExpand.removeClass('hide');
                    imgView(imgResult, id, liLength);
                    pagePre.on('click', function() {
                        searchLoad.previmgView(id - 1);
                    });
                    pageNex.on('click', function() {
                        searchLoad.nextimgView(id + 1);
                    });
                    location.hash += '&p=' + searchLoad.jsonNum;

                    function imgView(item, cid, liLength) {
                        makeHtml(id);
                        imgResize();
                        pagerView(cid, liLength);
                    }
                    hashAddFlag = false;
                }
            });
        }
        if (hash == '') {
            wrapper.removeClass('hide').removeClass('first');
            imgExpand.addClass('hide');
        } else if (wrapper.hasClass('first')) {
            $('.t,.x,#imgMain').css('visibility', 'hidden');
            wrapper.removeClass('first');
            var hash = location.hash.split('&');
            if (hash[1]) {
                searchLoad.jsonNum = hash[1].slice(2);
                var id = hash[0].slice(6);
                wrapper.addClass('hide');
                imgExpand.removeClass('hide');
                pagePre.on('click', function() {
                    searchLoad.previmgView(id - 1);
                });
                pageNex.on('click', function() {
                    searchLoad.nextimgView(id + 1);
                });
                getAjax(id);
            }
            if ((hash[0] == '#image1') && ((hash[1] == 'p=1'))) {
                pagePre.addClass('hide');
            }
        } else if ((hash[1]) && $('#wrapper').hasClass('hide') && (location.hash.indexOf('#image11') !== 0) && (location.hash.indexOf('#image10') !== 0) && (location.hash.indexOf('#image1') == 0)) {
            if (searchLoad.flg == true) {
                searchLoad.flg = false;
                var hash = location.hash.split('&');
                if (hash[1]) {
                    searchLoad.jsonNum = hash[1].slice(2);
                    var id = hash[0].slice(6);
                }
                getAjax(id);
            }
        } else if ((hash[1]) && $('#wrapper').hasClass('hide')) {
            if (searchLoad.flg == true) {
                searchLoad.flg = false;
                var hash = location.hash.split('&');
                if (hash[1]) {
                    searchLoad.jsonNum = hash[1].slice(2);
                    var id = hash[0].slice(6);
                    makeHtml(id);
                }
            }
        }
    });
    $(window).hashchange();
    searchLoad.previmgView = function(id) {
        pageNex.removeClass('hide');
        if (searchLoad.flg == true) {
            searchLoad.flg = false;
            var hash = location.hash.split('&');
            if (hash[1]) {
                searchLoad.jsonNum = hash[1].slice(2);
                var id = hash[0].slice(6)
            } else {
                id = id + 1
            };
            if (id <= 1) {
                id = 12;
                searchLoad.jsonNum = Number(searchLoad.jsonNum) - 1;
                pagePre.attr('href', '#image' + id + '&p=' + searchLoad.jsonNum);
                location.hash = 'image' + id + '&p=' + searchLoad.jsonNum;
                getAjax(id)
            } else if (id > 1) {
                id--;
                pagePre.attr('href', '#image' + id + '&p=' + searchLoad.jsonNum);
                location.hash = 'image' + id + '&p=' + searchLoad.jsonNum;
                makeHtml(id);
                imgResize();
                pagerView(id)
            }
        }
    };
    searchLoad.nextimgView = function(id) {
        pagePre.removeClass('hide');
        if (searchLoad.flg == true) {
            searchLoad.flg = false;
            var hash = location.hash.split('&');
            if (hash[1]) {
                searchLoad.jsonNum = hash[1].slice(2);
                var id = hash[0].slice(6)
            } else {
                id = id - 1
            };
            if (id >= 12) {
                id = 1;
                searchLoad.jsonNum = Number(searchLoad.jsonNum) + 1;
                pageNex.attr('href', '#image' + id + '&p=' + searchLoad.jsonNum);
                location.hash = 'image' + id + '&p=' + searchLoad.jsonNum;
                getAjax(id)
            } else {
                id++;
                pageNex.attr('href', '#image' + id + '&p=' + searchLoad.jsonNum);
                location.hash = 'image' + id + '&p=' + searchLoad.jsonNum;
                makeHtml(id);
                imgResize();
                pagerView(id)
            }
        }
        return false
    };
    $('#image_menu #close').on('click', function() {
        var result = [];
        if (1 < document.location.search.length) {
            var q = document.location.search.substring(1);
            var parameters = q.split('&');
            var result = new Object();
            var i;
            for (i = 0; i < parameters.length; i++) {
                var element = parameters[i].split('=');
                var paramName = (element[0]);
                var paramValue = (element[1]);
                result[paramName] = paramValue
            }
        }
        var hash = location.hash.split('&');
        if (hash[1]) {
            id = hash[0].slice(6)
        }
        imgExpand.css({
            '-webkit-transform': 'translate3d(2000px,0,0)',
            '-webkit-transition': '-webkit-transform 450ms cubic-bezier(0.25, 0.1, 0.25, 1.0)'
        });
        setTimeout(function() {
            imgExpand.css({
                'height': 'auto',
                '-webkit-transform': '',
                '-webkit-transition': ''
            });
            $('#imgMain,.t,.x').css('visibility', 'hidden');
            location.href = ('?q=' + result['q'] + "&query=" + result['query'] + '&page=' + searchLoad.jsonNum)
        }, 50);
        hashAddFlag = true;
        return false
    });

    function pagerView(id, pagerView) {
        if (id == 1 && searchLoad.jsonNum == 1 && searchLoad.dataXt == 1 && id >= searchLoad.dataLength) {
            pagePre.addClass('hide');
            pageNex.addClass('hide');
        } else if (id == 1 && searchLoad.jsonNum == 1) {
            pagePre.addClass('hide');
            pageNex.removeClass('hide');
        } else if (searchLoad.dataXt == 1 && id >= searchLoad.dataLength) {
            pagePre.removeClass('hide');
            pageNex.addClass('hide');
        } else {
            pagePre.removeClass('hide');
            pageNex.removeClass('hide')
        }
    };

    function makeHtml(id) {
        setTimeout(function() {
            $('#imgMain,.t,.x,#Refererurl').css('visibility', 'visible').removeClass('hide')
        }, 2000);
        setTimeout(function() {
            searchLoad.flg = true
        }, 220);
        id = id - 1;
        $('#originalsite').attr('href', searchLoad.dataBoxOriginalUrl[id]);
        $('#Refererurl').attr('href', searchLoad.dataBoxRefererurl[id]);
        $('.t').html(searchLoad.dataBoxAbstract[id]);
        $('.x').html((searchLoad.dataBoxWidth[id] + (' x ') + searchLoad.dataBoxHeight[id] + (' - ') + searchLoad.dataBoxFilesize[id] + (' - ') + searchLoad.dataBoxHostUrl[id]));
        $('#imgMain img').attr({
            'src': searchLoad.dataBoxMidleSizeUrl[id],
            'alt': searchLoad.dataBoxAbstract[id]
        });
        $('#imgMain,.t,.x,#Refererurl').css('visibility', 'visible').removeClass('hide');
        setTimeout(function() {
            var imgW = $('#imgMain img').width();
            if (imgW <= 1) {
                var hash = location.hash.split('&');
                var id = hash[0].slice(6);
                searchLoad.dataBoxMidleSizeUrl.splice(id - 1, 1, searchLoad.dataBoxThumbnailUrl[id - 1]);
                $('#imgMain').html('<img src =' + searchLoad.dataBoxThumbnailUrl[id - 1] + ' alt=' + searchLoad.dataBoxAbstract[id - 1] + '>')
            }
        }, 200);
        $('#imgMain img').error(function() {
            $(this).attr('src', searchLoad.dataBoxThumbnailUrl[id]);
            $('#originalsite').attr('href', searchLoad.dataBoxThumbnailUrl[id])
        });
        if (searchLoad.dataXt == 1 && id >= searchLoad.dataLength - 1) {
            pageNex.addClass('hide')
        };
        pagerView(id + 1)
    };

    function imgResize() {
        var windowh = $(window).height(),
            pagerh = $('#imageholder span').height(),
            menuh = $('#image_menu').height();
        pagerp = Math.floor((windowh - menuh - pagerh) / 2), imgm = 20;
        imgExpand.css('height', windowh);
        imgHolder.css({
            'height': (windowh - menuh)
        });
        if (innerImg.height() > windowh) {
            innerImg.css({
                'max-height': (windowh - menuh - imgm),
                'max-width': ''
            })
        } else {
            innerImg.css({
                'max-height': (windowh - menuh - imgm),
                'max-width': '270px'
            })
        }
        pager.css({
            'padding-top': pagerp,
            'padding-bottom': pagerp,
            'margin-top': -(pagerp + pagerh / 2);
        })
    };
    $(window).resize(imgResize);
    $(document).ready(imgResize);
    var narrowClick = $('#search_option .narrow button'),
        narrowExpand = $('#narrow_expand')
        searchBtn = '#narrow_expand .j-button__search';
    narrowClick.on('click', function() {
        wrapper.addClass('hide');
        if (narrowExpand.hasClass('hide')) {
            narrowExpand.removeClass('hide');
        }
        $(window).scrollTop(0);
        movbtnClick = function(e) {
            $(e).on('click', function() {
                narrowExpand.css({
                    '-webkit-transform': 'translateY(800px)',
                    '-webkit-transition': '-webkit-transform 1000ms cubic-bezier(0.25,0.1,0.25,1.0)'
                });
                wrapper.removeClass('hide');
                $('#noKwd').css('display', 'none');
                setTimeout(function() {
                    narrowExpand.addClass('hide').css({
                        '-webkit-transform': '',
                        '-webkit-transition': ''
                    });
                    if (e == searchBtn) {
                        kwd = $('#search_form_top [name=q]').val();
                        kwd_b = $('#search_form_bottom [name=q]').val();
                        var formId = "";
                        if ((kwd == '') && (kwd_b == '')) {
                            $('#noKwd').css('display', 'block');
                            return false
                        } else if ((kwd.length > 0) && (kwd_b.length > 0)) {
                            formId = 'search_form_bottom';
                            refineSearch(formId, kwd_b)
                        } else if ((kwd.length > 0) && (kwd_b == "")) {
                            formId = 'search_form_top';
                            refineSearch(formId, kwd)
                        } else {
                            formId = 'search_form_bottom';
                            refineSearch(formId, kwd_b)
                        }
                    }
                }, 400);
            })
        };
        movbtnClick(closeBtn)
        movbtnClick(searchBtn)
    });

    function getAjax(id) {
        var result = [];
        if (1 < document.location.search.length) {
            var q = document.location.search.substring(1);
            var parameters = q.split('&');
            var result = new Object();
            var i;
            for (i = 0; i < parameters.length; i++) {
                var element = parameters[i].split('=');
                var paramName = (element[0]);
                var paramValue = (element[1]);
                result[paramName] = paramValue
            }
            var query = decodeURIComponent(result['q']);
            result['q'] = query;
            var hash = location.hash.split('&');
            if (hash[1]) {
                searchLoad.jsonNum = hash[1].slice(2)
            } else {
                searchLoad.jsonNum = result['page']
            }
            if (id == undefined) {
                id = hash[0].slice(6)
            }
            var ua = navigator.userAgent;
            var URLbase = 'http://sp-image.search.auone.jp/searchBase';
            var paraAllImg = URLbase + ('?useragent=' + encodeURI(ua) + '&page=' + searchLoad.jsonNum + "&query=" + result['query'] + '&callback=JSON_CALLBACK');
            $.ajax({
                type: 'GET',
                url: paraAllImg,
                dataType: 'jsonp',
                async: false,
                jsonpCallback: 'JSON_CALLBACK',
                success: function(data) {
                    makeBox(data);
                    complete: makeHtml(id);
                }
            })
        }
        return false
    }
};

function makePager(pre, paraNex, paraPre, data) {
    var pagerPre = $('.pre'),
        pagerNex = $('.nex');
    $('.pre,.nex').removeClass('non').css('display', 'block');
    if (('&page=0' == pre) && (1 == data.xt)) {
        $('.pre,.nex').css('display', 'none')
    } else if ('&page=0' == pre) {
        pagerPre.css('display', 'none');
        pagerNex.addClass('non')
    } else if (1 == data.xt) {
        pagerNex.css('display', 'none');
        pagerPre.addClass('non')
    }
    $('.pre a').attr('href', paraPre);
    $('.nex a').attr('href', paraNex)
};
window.onunload = function() {};
jQuery(function($) {
    for (var i = 0; i < $.suggest.elms.length; i++) {
        $.suggest.sgt({
            'input': $.suggest.elms[i].input,
            'target': $.suggest.elms[i].suggest,
            'api': 'http://lmes.auone.jp/sgt/',
            'sr': $.suggest.elms[i].sr,
            'form': $.suggest.elms[i].form
        });
        $('#' + $.suggest.elms[i].form).unbind('submit');
        $('#' + $.suggest.elms[i].form).bind('submit', {
            "input": $.suggest.elms[i].input,
            "sr": $.suggest.elms[i].sr,
            "form": $.suggest.elms[i].form
        }, function(param) {
            $.suggest.sbcall(param.data.input, param.data.sr, param.data.form);
            return false
        });
        $('#' + $.suggest.elms[i].remove).bind('click', {
            "target": $.suggest.elms[i].input
        }, function(param) {
            $('#' + param.data.target).val("");
            return false
        })
    }
    $.suggest.clear()
});
(function($) {
    var keycd = 0;
    suggest = {};
    suggest.v = "1";
    suggest.sr = "00f0";
    suggest.sentSb = false;
    suggest.cleared = false;
    suggest.elms = [{
        "sr": "00f0",
        "form": "search_top",
        "input": "textfield-search_top",
        "suggest": "text_suggest_top",
        "button": "btn_search_top",
        "remove": "remove_word"
    }, {
        "sr": "00f0",
        "form": "search_form",
        "input": "textfield-search_bottom",
        "suggest": "text_suggest_bottom",
        "button": "btn_search_bottom",
        "remove": "remove_word_bottom"
    }];
    suggest.clear = function() {
        if (suggest.cleared) {
            return
        }
        suggest.cleared = true;
        for (var i = 0; i < $.suggest.elms.length; i++) {
            $('#' + suggest.elms[i].input).attr('data-tr', '0');
            $('div#' + suggest.elms[i].suggest).hide()
        }
        $('div.inner ul p').remove();
        $('div.inner ul li').remove();
        $.suggest.sgt.index = 0;
        suggest.sgtSelected = "off";
        suggest.sentSb = false;
        return false
    };
    suggest.sbcall = function(input, sr, form) {
        var w = $('#' + input).attr('value');
        $.suggest.sb({
            "input": input,
            "sr": sr,
            "w": w,
            "form": form,
            "search": "./search?"
        })
    };
    suggest.sb = function(ops) {
        if (suggest.sentSb) {
            return false
        }
        suggest.sentSb = true;
        var xOptDefaults = {
            "w": '',
            "input": 'input_elm_id',
            "target": 'suggest_elm_id',
            "charset": 'utf-8',
            "api": 'http://lmes.auone.jp/sb/',
            "timeout": 3000,
            "tr": 0,
            "sr": suggest.sr,
            "cb": "callback",
            "v": suggest.v,
            "search": "./search?",
            "opt": "&",
            "def_opt": "",
            "hwd_flg": true
        };
        ops = $.extend({}, xOptDefaults, ops);
        ops.tr = $('#' + ops.input).attr('data-tr');
        if (ops.w == '') {
            return false
        }
        var notCountOpt = '';
        if (!ops.hwd_flg) {
            notCountOpt = '&t=1';
        }
        $.jsonp({
            "charset": 'utf-8',
            "url": ops.api + '?w=' + encodeURIComponent(ops.w) + '&tr=' + ops.tr + '&sr=' + ops.sr + "&v=" + ops.v + '&cb=' + ops.cb + notCountOpt,
            "callback": ops.cb,
            "timeout": ops.timeout,
            "success": function(d) {
                $('#' + ops.input).attr('data-tr', d.tr);
                if (suggest.sgtSelected != "off") {
                    suggest.jump(ops.form, ops.sr, ops.opt);
                    return false;
                } else {
                    suggest.jump(ops.form, ops.sr, ops.def_opt);
                    return false;
                }
            },
            "error": function(d, msg) {
                if (suggest.sgtSelected != "off") {
                    suggest.jump(ops.form, ops.sr, ops.opt);
                    return false;
                } else {
                    suggest.jump(ops.form, ops.sr, ops.def_opt);
                    return false;
                }
            }
        });
        $.suggest.noBindSubmit()
    };
    suggest.sgt = function(ops) {
        var survey = 500;
        var xOptDefaults = {
            "charset": 'utf-8',
            "api": 'http://lmes.auone.jp/sgt/',
            "timeout": 3000,
            "sr": suggest.sr,
            "cb": "callback",
            "v": suggest.v,
            "limit": 5,
            "tr": 0,
            "input": 'input_elm_id',
            "target": 'suggest_elm_id',
            "survey": 0,
            "index": 1
        };
        ops = $.extend({}, xOptDefaults, ops);
        if ($('#' + ops.input) == undefined || $('#' + ops.input).val() == "") {
            if (ops.target == "text_suggest_bottom") {
                ops.sr = "00f0";
            } else {
                ops.sr = "00f0";
            }
        }
        $('#' + ops.input).focus(function() {
            var v0 = $('#' + ops.input).val();
            ops.survey = survey;
            suggest.sgt.ps = setTimeout(function() {
                if (ops.survey == 0) {
                    return;
                }
                var v1 = $('#' + ops.input).val();
                if (v1 && v1.length < 1) {
                    $('#' + ops.target).hide();
                    $('#' + ops.input).attr('data-tr', 0)
                } else {
                    if (v0 != v1) {
                        suggest.sgtSelected = "off";
                        ops.tr = $('#' + ops.input).attr('data-tr');
                        $.jsonp({
                            "charset": 'utf-8',
                            "url": ops.api + '?w=' + encodeURIComponent(v1) + '&limit=' + ops.limit + '&tr=' + ops.tr + '&sr=' + ops.sr + "&v=" + ops.v + '&cb=' + ops.cb,
                            "callback": ops.cb,
                            "timeout": ops.timeout,
                            "success": function(d) {
                                suggest.cleared = false;
                                $('#' + ops.input).attr('data-tr', d.tr);
                                $('#' + ops.target + ' div.inner ul').children().remove();
                                if (d.items.length == 0) {
                                    $.suggest.clear();
                                    return;
                                }
                                for (var i = 0; i < d.items.length; i++) {
                                    $('#' + ops.target + ' div.inner ul').append('<li><a id="suggest_key_' + (i + 1) + '" href="javascript:void(0)" data-hwd="' + d.items[i] + '"><span class="keyword">' + d.items[i] + '</span><span class="magnifying"></span></a></li>')
                                }
                                $('div.inner ul li .magnifying').bind('mousedown', function() {
                                    $('#' + ops.input).val("");
                                    var that = $(this).prev().text();
                                    $('#' + ops.input).val(that);
                                    return false;
                                });
                                $('div.inner ul li a').bind('mousedown', function() {
                                    $('#' + ops.input).val("");
                                    $('#' + ops.input).val($(this).attr('data-hwd')).trigger("change");
                                    suggest.sgtSelected = "on";
                                    $.suggest.sb({
                                        "input": ops.input,
                                        "sr": ops.sr,
                                        "w": $(this).attr('data-hwd'),
                                        "form": ops.form,
                                        "search": "./?";
                                    });
                                    return false;
                                });
                                $('#' + ops.target).show();
                            },
                            "error": function(d, msg) {
                                $('#' + ops.target).hide();
                                suggest.sgtSelected = "off";
                            }
                        })
                    }
                }
                v0 = $('#' + ops.input).val();
                suggest.sgt.ps = setTimeout(arguments.callee, ops.survey)
            }, ops.survey)
        });
        $('#' + ops.input).blur(function() {
            ops.survey = 0;
            $.suggest.clear();
        })
    };
    suggest.jump = function(form, sr, opt) {
        if (form === 'search_top') {
            searchSubmit('search_form_top');
        } else {
            searchSubmit('search_form_bottom');
        }
    }
    suggest.sgt.ps = '';
    suggest.noBindSubmit = function() {
        for (var i = 0; i < $.suggest.elms.length; i++) {
            $('#' + suggest.elms[i].button).unbind('submit');
            $('#' + suggest.elms[i].button).unbind('click');
        }
        return false;
    }
    $.suggest = suggest;
})(jQuery);