var mypageDirective = angular.module('mypage.directives', []);

mypageDirective.directive('isloadedThismonth', function() {
    return {
        link: function (scope) {
            var baseScope = angular.element($('#js-scope_base')).scope().baseCtrl;
            if (scope.$last){
                baseScope.isloaded.thismonth = true;
            }
        }
    };
});

mypageDirective.directive('isloadedLastmonth', function() {
    return {
        link: function (scope) {
            var baseScope = angular.element($('#js-scope_base')).scope().baseCtrl;
            if (scope.$last){
                baseScope.isloaded.lastmonth = true;
            }
        }
    };
});

//データがないときの処理
mypageDirective.directive('checkEmpty', function() {
    return {
        controller:'MypageBaseCtrl',
        link: function (scope) {
            scope.$watch('baseCtrl.models.isEmptyData.thismonth', function(newVal){
                if(newVal){
                    append('.thismonth');
                }
            });
            scope.$watch('baseCtrl.models.isEmptyData.lastmonth', function(newVal){
                if(newVal){
                    append('.lastmonth');
                }
            });
            function append(target){
                var template = '<tr><td class="error_msg" colspan="13">表示するデータがありません</td></tr>';
                $(target).append(template);
            }
        }
    };
});


//ソート処理
mypageDirective.directive('sortItems', function() {
    return {
        restrict: 'A',
        link: function (scope) {
            var baseScope = angular.element($('#js-scope_base')).scope().baseCtrl;
            
            //表示adTypeの切り替え
            scope.typeChoice = function (adType) {
                baseScope.sortItems.adType = adType;
                scope.order            = getOrder(baseScope.sortItems.place);
            };

            scope.orderBy = function (place) {
                scope.order = getOrder(place);
            };

            //ソートの選定
            var getOrder = function(place){
                //現在のscope.sortItems.placeとクリックしたplaceが同じ場合のみtoggle処理
                var str                    = (baseScope.sortItems.isDesc && baseScope.sortItems.place==place) ? '-'  : ''   ;
                baseScope.sortItems.isDesc = (baseScope.sortItems.isDesc && baseScope.sortItems.place==place) ? true : false;
                baseScope.sortItems.place  = place;
                var i            = 0;
                var arr_valid    = [];
                var month        = (baseScope.isLastMonth) ? 'lastmonth':'thismonth';

                if(typeof baseScope.master_data[month] != 'undefined'){
                    var target       = baseScope.master_data[month].results;

                    angular.forEach(target, function(value, key) {
                        //ソートデータに-1が含まれない場合
                        if(target[key][baseScope.sortItems.adType][place] != -1){
                            arr_valid[i] = target[key];
                            i++; 
                        }
                    });
                    baseScope.models.report[month].results = arr_valid;
                }
                return str + baseScope.sortItems.adType + '.' + baseScope.sortItems.place;
            };
            //クラスの付与
            scope.activeClass = function(place){
                if(baseScope.sortItems.place  === place){
                    if(!baseScope.sortItems.isDesc) return {'sort-asc' : true};
                    if(baseScope.sortItems.isDesc)  return {'sort-desc': true};
                }
            };
        }
    };
});


//グラフ
mypageDirective.directive('graphDirective', [function() {
    return {
        restrict: 'A',
        link: function (scope) {
            var baseScope  = angular.element($('#js-scope_base')).scope().baseCtrl;
            var HOVER_TIME = 400;
            var timer      =   0;
            var chart      =  {};
            var removeWord = '一覧';

            /**
             * グラフ全表示 daily:line型 Total:なし
             */
            function showEntryGraph(){
                var month     = (baseScope.isLastMonth) ? 'lastmonth':'thismonth';
                if (typeof baseScope.master_data[month].graph !=  'undefined'){
                    var keys      = getUnloadArr(null);
                    scope.graphCtrl.setting.data.json = baseScope.master_data[month].graph[baseScope.sortItems.adType];
                    scope.graphCtrl.setting.data.keys.value = keys;

                   chart = c3.generate(scope.graphCtrl.setting);
                   $('.c3-axis-y2').css('display','none');
                }
            }

            /**
             * グラフ個別表示 daily:line型 Total:bar型
             */
            function showPickupGraph(name,arr_total,arr_unload){
                chart.unload(arr_unload);
                setTimeout(function() {
                    chart.transform('bar', name);
                }, 1200);
                setTimeout(function() {
                    chart.load({
                        json:arr_total,
                        type: 'line',
                        keys: {value: ['Total',removeWord],},
                        axes: {data1: 'y',data2: 'y2',},
                    });
                    pickupStyles();
                }, 1800);
            }

            /**
             * event listener 
             */
             var $graph_area = $('.graph-area');
             //マウスオーバー
            $graph_area.on('mouseover', '.c3-legend-item:not(".c3-legend-item-'+removeWord+',.c3-legend-item-Total")', function(){
                var name = $(this).children("text").text();
                if(!$graph_area.hasClass('js-pickup-active')){
                    timer = setTimeout(function() {
                        var arr_pickup =  getPickupArr(name);
                        var arr_unload =  getUnloadArr(name);

                        showPickupGraph(name,arr_pickup,arr_unload)
                        $graph_area.addClass('js-pickup-active');
                    }, HOVER_TIME);
                 }   
            });

            //マウスリーブ
            $graph_area.on('mouseleave click', '.c3-legend-item', function(){
                 clearTimeout(timer);
            });

            //クリック
            $('body').on('click', '.js-showEntryGraph,.c3-legend-item-'+removeWord+'', function(){
                $graph_area.removeClass('js-pickup-active');
                showEntryGraph();
            });
            $graph_area.on('click', '.c3-legend-item-event', function(){
                pickupStyles();
            });

            /**
             * Unloadさせる要素の配列を生成する関数 指定key(name ,'x')以外の配列内オブジェクトのkeyを取得し、配列にして返す
             */
            var getUnloadArr = function(name){
                var month      = (baseScope.isLastMonth) ? 'lastmonth':'thismonth';
                var data       = baseScope.master_data[month].graph[baseScope.sortItems.adType];
                var arr_unload = [];
                var keys       = [];
                angular.forEach(data, function(obj) {
                    angular.forEach(obj, function(value, key) {
                        keys.push(key);
                    });
                });
                arr_unload = _.without(_.uniq(keys), name ,'x','Total','totals');
                return arr_unload;
            };
            
            /**
             * pickupグラフの配列を生成する関数 指定データ(name)のTotal数値を入れ,指定データ,日付,total_dataオブジェクト以外は空にして返す
             */
            var getPickupArr = function(name){
                var month    = (baseScope.isLastMonth) ? 'lastmonth':'thismonth';
                var data     = baseScope.master_data[month].graph[baseScope.sortItems.adType];
                var arr_pickup = [];

                angular.forEach(data, function(obj) {
                    var obj_tmp    = {};
                    angular.forEach(obj, function(value, key) {
                        value = (key != name && key != 'x' && key != 'totals') ? '': '¥' + Math.round(value);

                        obj_tmp[key] = value;
                        obj_tmp['Total'] =  obj['totals'][name];
                    });
                    arr_pickup.push(obj_tmp);
                });
                return arr_pickup;
            };

            /**
             * pickupグラフのスタイルを設定
             */
            var pickupStyles = function(){
                //積み上げグラフの今日以降の着地予想は半透明
                if(!baseScope.isLastMonth){
                    var begin = moment().date() -1;
                    var end   = moment().daysInMonth();
                    setTimeout(function() {
                        for (var i = begin; i < end; i++) {
                            $('.c3-bar-'+i+'').css('opacity','0.2');
                        }
                    }, 95);
                }
                $('.c3-legend-item-'+removeWord+' .c3-legend-item-tile').css('display','none');
                $('.c3-axis-y2').css('display','block');
            };

            if (typeof baseScope.master_data.thismonth.graph !=  'undefined'){
                showEntryGraph();
            }
        }
    };
}]);
 
/**
 * keyword登録処理
 */
mypageDirective.directive('keywordTemplate', ['$http','mypageManagerService',function ($http,mypageManagerService) {
    return {
        scope: false,
        restrict: 'AE',
        link: function (scope) {
            //キーワード登録
            scope.register = function() {
                //初期化
                scope.is_show_edit = true;
                scope.is_error     = false;
                scope.models       = scope.keywordCtrl.models;
                to_edit();
            };

            //キャンセル
            scope.close = function () {
                scope.is_show_edit = false;
            }

            //登録キーワードdataを生成して送信
            scope.send = function () {
                var results     = {};
                var obj         = scope.models.google.my_keywords;
                scope.error_msg = "";
                var i           = 1;
                var res         = validate(obj);

                if(!scope.error_msg){
                    if(typeof obj != "undefined"){
                        //client_infoをidとnameに分ける
                        _.each(obj, function(element, index, obj) {
                            obj[index]["client_id"]   =  (element["client_info"]!= null) ? element["client_info"]["id"]:'';
                            obj[index]["client_name"] =  (element["client_info"]!= null) ? element["client_info"]["name"]:'';

                            //空欄を詰める
                            if (!element['keyword'] == "") {
                                 results[i] = element;
                                 i++;
                            }
                        });
                    }
                    post(results)
                }
            };

            //バリデーション
            function validate(models) {
                var obj = _.transform(models, function (res, val, i) {
                    if (_.isNull(val)) return res;

                    // キーワード空白は無視する
                    if (!_.has(val, 'keyword') || _.isEmpty(val.keyword)) {
                        return res;
                    }

                    // クライアント名が選択されていない
                    if (val.keyword && (_.isEmpty(val.client_info) || !_.has(val, 'client_info'))) {
                        validateError(i,'クライアント名');
                        return false;
                    }

                    // マッチタイプが選択されていない
                    if (val.keyword && (_.isEmpty(val.match_type) || !_.has(val, 'match_type'))) {
                        validateError(i,'マッチタイプ');
                        return false;
                    }

                    i = Number(i) + 1;
                });

                 return obj;
            }

            //バリデーションエラー
            function validateError(i,place) {
                scope.error_msg = 'キーワード ' + i + ' の' + place + 'を指定してください。';
            };


            //post Ajax
            function post(results) {
                $http({
                    method : 'POST',
                    url : '/sem/new/mypage/report/set_keyword',
                    data: $.param(results),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }).success(function(data, status, headers, config) {
                    scope.close();
                    scope.keywordCtrl.get_keyword();
                });
            }

            //登録画面へアニメーション
            function to_edit() {
                setTimeout(function() {
                    $("body,html").animate({scrollTop: $('.keyword-form').offset().top}, 500);
                }, 350);
            }
        }
    };
}]);

