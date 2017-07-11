/// <reference path="../../typings/main.d.ts" />
(() => {
    'use strict';
    const app = angular.module('cart.directives', [
            'ngResource',
            'ngMessages',
            'util.services',
            'util.directives',
            'cart.controllers',
            'cart.services'
    ]);

    app.directive('modalAdjuster', ['$window', function($window) {
        return {
            link    : function($scope, elem) {
                $scope.$on('modalAdjust', function() {
                    elem.css({ 'position':'fixed','top':'50%','transform':'translateY(-50%)' });
                });
            }
        };
    }]);

    app.directive('showDetail', ['$window', function($window) {
        return {
            link    : function($scope, elem) {
                $scope.$on('showDetail', function(event, args) {
                    //詳細画面表示
                    $scope.showDetailWindow(args);
                });
            }
        };
    }]);


    app.directive('hideDetailWindow', [
        '$rootScope',
        'ViewStatusSharedService',
        ($rootScope, viewStatus) => {
            return {
                link    : ($scope, element) => {
                    element.on({
                        click   : e => {
                            if (typeof e === 'object') {
                                e.preventDefault();
                            }

                            viewStatus.hide.detailWindow   = true;
                            $scope.$apply()
                        }
                    });
                }
            };
        }]);


    app.directive('initStatusWordBox', [() => {
            return {
                link    : ($scope, element,attr) => {
                    if(attr.initStatusWordBox != '') $(element).show();
                }
            };
        }]);


    app.directive('cartItems', [
        'CartDataSharedService',
        'TrimmingDataSharedService',
        'CartLoader',
        'CartEditor',
        'ArticleItem',
        '$rootScope',
        'ViewStatusSharedService',
        'PremiumPrice',
        'MessageSharedService',
        'pageConst',
        '$timeout',
        function(cartData, trimmingData, loader, editor, article, $rootScope, viewStatus, premiumPrice, message, pageConst,$timeout) {
        return {
            restrict    : 'EA',
            replace     : true,
            controller  : function($scope) {

                $scope.setCaption = function(caption_num){
                    let res = null;
                    _.forEach(pageConst.cart.image_edit, (obj,key) => {
                        if(obj.id == caption_num) res = key
                    });
                    return res;
                };

                //編集
                $scope.editCart = function(cart, subject, key, index = null){
                    let params       = {};
                    params['cartId'] = cart.common_cart_id;
                    params[subject]  = cart[key];

                    //商品情報をダウンロードに変更時
                    if(key == 'article_id' && cart[key] == pageConst.cart.download_article_id){
                        //編集データをリセット
                        let caption_clear = { caption : null , caption_str : null};
                        params = _.extend(params,trimmingData,caption_clear);

                        //編集アコーディオンの状態をクリア cart.jsのイベント
                        let $target = $('.item_list > li').eq(index).find('.edit_btn');
                        if(index != null && $target.hasClass('active')) $target.click();

                        //amountは1固定
                        params['amount'] = 1;
                    }
                    loader.edit(params);
                };


                //編集キャプション
                $scope.editCartCaption = (cart, caption, key, caption_str) => {
                    let caption_id        = (key == null) ? null : pageConst.cart.image_edit[key].id;
                    let params            = {};
                    params['cartId']      = cart.common_cart_id;
                    params['caption_str'] = (key === 'free_caption') ? caption_str : null;
                    params[caption]       = caption_id;
                    loader.edit(params);
                };

                //トリミング解除
                $scope.deleteCartTrim = (cart) => {
                    let params            = {};
                    params['cartId']      = cart.common_cart_id;
                    loader.edit(_.extend(params,trimmingData));
                };

                //詳細画面表示
                $scope.showDetailWindow = (index) => {
                    cartData.currentPhotoDataIndex = $scope.paging.maxList * $scope.paging.currentPage + index;
                    cartData.currentPhotoData = cartData.list[cartData.currentPhotoDataIndex];
                    $timeout(() => { viewStatus.hide.detailWindow = false; });
                    $rootScope.$broadcast('modalAdjust');
                };

                //削除
                $scope.deleteCart = function(cart, e) {
                    e.preventDefault();
                    cart.deleted  = true;
                    editor.delete({cartId:cart.common_cart_id}, function(res) {
                            $rootScope.$broadcast('changeEnd');
                            $rootScope.$broadcast('successToast', message.success.deletingCart);

                            // 削除したカートを除くカート一覧を返す
                            $scope.root.carts.list = $scope.root.carts.list.filter(function (item) {
                                if (item.common_cart_id != cart.common_cart_id) {
                                    return item;
                                }
                            });
                            if($scope.root.carts.list.length == 0) {
                                viewStatus.hide.zeroCartBlock = false;
                                viewStatus.hide.paging        = true;
                            }else{
                                //ページング
                                $scope.paging.setPaging();
                            }

                        },
                        function(error) {
                            cart.deleted  = false;
                            $rootScope.$broadcast('errorToast', message.failed.deletingCart);
                        });
                };

                //コピー
                $scope.copyCart = function(cart, e) {
                    e.preventDefault();
                    let params  = {
                        event_id   : cart.event_id,
                        page_id    : cart.page_id,
                        member_id  : PARAMS['member_id'],
                        amount     : 1,
                        article_id : cart.photo.pricelist_details[0].article_id,
                        photo_id   : cart.photo_id,
                        caption    : (cart.photo.logo_code == 'always') ? 1 : null
                    };
                    loader.copy(params,cart),function(){
                        //ページング
                        $scope.paging.setPaging();
                    };
                };


                $scope.calculatePremiumPrice    = function(articlePrice, rate) {
                    rate        = rate - 0;
                    let price   = articlePrice - 0;
                    return premiumPrice.calculate(price, rate);
                }
            }
        };
    }]);


    //画像サイズ最適化
    app.directive('imgResizer',['$timeout',($timeout)=>{
        return {
            link    : ($scope)=> {
                $scope.$on('resize', (event, message) =>{
                    $timeout(() =>{
                        $('#cart_item_list img').each(function(){
                            let $this = $(this);
                            let img = new Image();
                            img.src = $(this).attr('src');
                            img.onload = function(){
                                let $w = img.width;
                                let $h = img.height;
                                if ($h > $w) {
                                    $this.css({'height': '100%','width': 'auto','left': '-20%','visibility': 'visible'});
                                }else {
                                    $this.css({'width': '100%','height': 'auto','visibility': 'visible'});
                                }
                            };
                        });
                    });
                });
            }
        };
    }]);

    app.directive('listLoaded',['$rootScope','ViewStatusSharedService',($rootScope,viewStatus)=>{
            return (scope) => {
                if (scope.$last){
                    scope.paging.setPaging();
                    $rootScope.$broadcast('resize')
                    viewStatus.hide.paging = false;

                }
            }
        }]);

    app.directive('setAmountModels',['pageConst',(pageConst)=>{
        return (scope) => {
            scope.amounts = [];
            for (let i=1 ; i<=pageConst.cart.models.max_amount ; i++){
                scope.amounts.push({'val' : i ,'label' : i + '点'});
            }
        }
    }]);

    //カートのページング生成
    app.directive('paging', [
        'pageConst',
        'CartDataSharedService',
        function(pageConst,cartData) {
            return {
                restrict    : 'EA',
                replace     : true,
                templateUrl : 'paging.html',
                controller  : function($scope) {

                    $scope.paging = {
                        currentPage     : 0,
                        currentFirstCnt : 0,
                        currentLastCnt  : 0,
                        nextPageNum     : '0',
                        maxList         : pageConst.cart.paging.max_list,
                        view_items_size : 0,
                        numberOfPages   :() => {
                            return Math.ceil( $scope.root.carts.list.length / $scope.paging.maxList);
                        },
                        range : () =>{
                            let arr = [];
                            for (let i=0; i<$scope.paging.numberOfPages(); ++i) arr.push(i);
                            return arr;
                        },
                        setPaging : function(nextPageNum = null) {
                            let paging = $scope.paging;
                            if (!_.isNull(nextPageNum)) paging.currentPage = Number(nextPageNum);
                            paging.currentFirstCnt = (paging.currentPage * paging.maxList) + 1;
                            paging.currentLastCnt  = (Number(paging.currentPage) + 1 == paging.numberOfPages()) ? cartData.list.length : paging.currentFirstCnt + paging.maxList - 1;
                            paging.nextPageNum    = paging.currentPage + '';
                        },
                    };

                    $scope.paging.setPaging();
                }
            };
        }]);

    //トリミングのurl生成
    app.directive('trimmingUrlCompiler', [
        'utils',
        'pageConst',
        function(utils,pageConst) {
        return {
            restrict    : 'EA',
            link  : function($scope,$elm,$attr) {
                let url      =  utils.getUrlPrefix() + pageConst.cart.trimming_path +  $attr.cartid;
                let html     = '<a href="'+ url +'" class="trimming_btn"><span>この写真をトリミングする</span></a>';
                $elm.html(html);
            }
        };
    }]);

})();

