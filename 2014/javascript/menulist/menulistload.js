var menulistload = angular.module("menulistApp", []);


// jsonURL
//サービスリスト
var jsonURL = document.URL;
if (jsonURL.match('service_list')) {
    menulistload.jsonurl = 'http://api.xxx/menulist/getServiceList';
    //メニューリスト
} else if (jsonURL.match('menu_list')) {
    menulistload.jsonurl = 'http://api.xxx/menulist/getMenuList';
    //おすすめリスト		
} else {
    menulistload.jsonurl = 'http://api.xxx/menulist/getRecommendList';
}

// エラー用メッセージ
menulistload.err_str = '<p class="error_text">現在、つながりにくくなっております。<br>しばらくたってから、もう一度お試しください。</p>';
menulistload.more_no = "0";
menulistload.botHide = function() {
    jQuery('#content .btn_more').hide();
};
menulistload.botShow = function() {
    jQuery('#content .btn_more').show();
};

menulistload.controller('addMenulist', function($scope, $http) {
    $scope.list = [];
    var more_no;
    $http({
        method: 'GET',
        url: menulistload.jsonurl + '?cat_no=' + getCatNo() + '&more=' + menulistload.more_no
    })
    .success(function(data) {
        if (data.status !== 'ok') {
            ajxerr();
        }
        $scope.list = data.response.list;

        if ((21) <= data.response.total) {
            menulistload.botShow();
        }
        setTimeout(function() {
            resizeFunc();
        }, 300);


    }).error(function(data) {
        ajxerr();
    });

    //もっとみる	
    $scope.addTodo = function() {
        menulistload.more_no++;
        $http({
                method: 'GET',
                url: menulistload.jsonurl + '?cat_no=' + getCatNo() + '&more=' + menulistload.more_no
            })
            .success(function(data) {
                if (data.status !== 'ok') {
                    ajxerr();
                }

                if ((menulistload.more_no * 20 + 20) >= data.response.total || (menulistload.more_no * 20 + 20) == data.response.total) {
                    menulistload.botHide();
                }

                $scope.list = $scope.list.concat(data.response.list);
            }).error(function(data) {
                ajxerr();
            });
        var menulistblock = jQuery('.menulistblock a');
        var maxHeight = menulistblock.height();
        $scope.setheight = maxHeight + 'px';
        setTimeout(function() {
            resizeFunc();
        }, 100);
    };
}).directive('mldirective', function() {

    return {
        restrict: 'AE',
        compile: function() {
            //初期化	
            activnai();
            jQuery(window).resize(function() {
                setTimeout(function() {
                    resizeFunc();
                }, 200);
            });
            return function(scope, element, attrs) {


            };
        }
    };
});




//カテゴリーNO取得
var getCatNo = function() {
    if (1 < document.location.search.length) {
        var query = document.location.search.substring(1);
        var parameters = query.split('&');
        var result = new Object();
        for (var i = 0; i < parameters.length; i++) {
            var element = parameters[i].split('=');
            var paramName = decodeURIComponent(element[0]);
            var paramValue = decodeURIComponent(element[1]);

            // パラメータ名をキーとして連想配列に追加する
            result[paramName] = decodeURIComponent(paramValue);
        }
        if (!result.cat_no) {
            return '';
        }
        return result.cat_no;
    } else {
        return '';
    }
};

//エラー処理
function ajxerr() {
    if (menulistload.parameter) {
        jQuery('.btn_more').html(menulistload.err_str);
    } else {
        jQuery('#mlapp-block').html(menulistload.err_str);
    }
}

function resizeFunc() {
    var maxHeight = 0;
    var menulistblock = jQuery('.menulistblock a');

    if (jQuery(document).width() > 767) {

        menulistblock.css('height', 'inherit');
        menulistblock.each(function() {
            if (maxHeight < jQuery(this).height()) {
                maxHeight = jQuery(this).height();
            }
        });
        menulistblock.height(maxHeight);
    } else {

        menulistblock.css('height', 'auto');
        menulistblock.height('auto');
    }
}


//左カラムActive
function activnai() {
    var timer = false;
    var dfd = jQuery.Deferred();
    dfd.then(function() {
        jQuery('#navi a[href$="?cat_no=' + getCatNo() + '"]').parent().
        addClass('active').closest('ul').css('display', 'block').closest('ul').prev('.ac').addClass('close');
    }).then(function() {
        jQuery('#navi').show();
    });
    dfd.resolve();
}