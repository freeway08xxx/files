/// <reference path="../../typings/main.d.ts" />
(() => {
    'use strict';
    const app = angular.module('cart.services', [
        'ngResource',
        'util.services',
        'util.directives',
    ]);

    app.factory('MessageSharedService', [()=> {
        return {
            failed  : {
                fetchCart         : 'カートデータの取得に失敗しました',
                editCart          : 'カートの編集に失敗しました',
                addingCart        : 'カートの操作に失敗しました',
                fetchCartEmpty    : 'カートにデータがありません',
                deletingCart      : 'カートからの削除に失敗しました',
                callApi           : '通信エラーが発生しました',
            },
            success : {
                editCart          : 'カートを編集しました',
                deletingCart      : 'カートから削除しました',
                addingCart        : 'カートに追加しました',
            }
        };
    }]);


    // HTML表示制御用サービス
    app.factory('ViewStatusSharedService',[()=> {
        return {
            hide    : {
                paging             : true,
                errForm            : true,
                detailWindow       : true,
                zeroCartBlock      : true,
            },
            show : {
                loading          : true,
                calculatingTotal : true,
            }
        };
    }]);

    app.factory('PremiumPrice', [() =>{
        return {
            calculate   : (originalPrice, premiumRate) => {
                let price   = originalPrice - 0,
                    rate    = premiumRate - 0;
                return (isNaN(rate)) ? price : Math.ceil(price * rate);
            }
        };
    }]);


    app.factory('CartDataSharedService', [function() {
        return {
            origin                : [],
            list                  : [],
            subtotal              : 0,
            currentPhotoData      : null,
            currentPhotoDataIndex : null,
        };
    }]);

    app.factory('TrimmingDataSharedService', [function() {
        return {
            TrimmingWidth    : null,
            TrimmingHeight   : null,
            TrimmingX        : null,
            TrimmingY        : null,
        };
    }]);

    app.factory('Calculator', [
        'ArticleItem',
        'MessageSharedService',
        'ViewStatusSharedService',
        '$rootScope',
        function(article,message,viewStatus,$rootScope) {
        return {
            calculateSubtotal : function(list) {
                let subtotal   = 0;
                _(list).forEach(function(cart) {
                    // 削除済みアイテムは計算対象にない
                    if (cart.deleted) return;

                    let item    = (cart.is_photoset || cart.photo == null) ? cart : article.find(cart);
                    if (typeof item === 'undefined') return;

                    subtotal += cart.price * cart.amount;
                });

                return subtotal;
            },
            calculateOrderSummary   : function(summary) {
                let total_price = summary.subtotal_sum + summary.shipment_fee + summary.payment_fee + summary.coupon_discount;
                if(total_price < 0){
                    total_price                  = null;
                    viewStatus.flag.isMinusTotal = true;
                    $rootScope.$broadcast('errorToast', message.failed.fetchCorrectCoupon);
                }

                return  {
                    is_download     : summary.is_download,
                    is_print        : summary.is_print,
                    amount_sum      : summary.amount_sum,
                    subtotal_sum    : summary.subtotal_sum,
                    shipment_fee    : summary.shipment_fee,
                    payment_fee     : summary.payment_fee,
                    coupon_discount : summary.coupon_discount,
                    total           : total_price,
                };
            }
        };
    }]);

    app.factory('ArticleItem', [() => {
        return {
            find    : (cart) => {

                let article_id  = cart.article_id;
                let target_item = _.find(cart.photo.pricelist_details, (item) => {
                    return (item['article_id'] == article_id);
                });

                return target_item;
            }
        };
    }]);


    // カートデータ呼び出し処理
    app.factory('CartLoader', [
        'CartData',
        'CartEditor',
        'CartDataSharedService',
        'CartDataCreator',
        'Calculator',
        'utils',
        'pageData',
        'pageConst',
        'ViewStatusSharedService',
        'MessageSharedService',
        '$rootScope',
        function(loader, editor, cartShared, cartDataCreator, calculator, utils, pageData, pageConst, viewStatus, message, $rootScope) {
            return {
                fetch   : function(){
                    let _self = this;
                    let params = {
                        member_id       : pageData.params().member_id,
                        not_member_hash : pageData.params().not_member_hash,
                        store_id        : pageData.params().store_id,
                    };

                    loader.query(params,function(res){
                        viewStatus.show.loading = false;
                        if (res.length === 0) {
                            $rootScope.$broadcast('errorToast', message.failed.fetchCartEmpty);
                            viewStatus.hide.zeroCartBlock = false;
                        } else {
                            cartShared.origin   = _self.addOriginalValue(res);
                            cartShared.list     = cartDataCreator.setPhotosetCode(res);
                            cartShared.subtotal = calculator.calculateSubtotal(res);
                        }
                    },
                    function(error) {
                        viewStatus.show.loading = false;
                        viewStatus.hide.zeroCartBlock = false;
                        $rootScope.$broadcast('errorToast', message.failed.fetchCart);
                    });
                },

                edit : function(params) {
                    let _self = this;

                    editor.patch(params, function(res) {
                            _self.overwrite(res,params);
                            $rootScope.$broadcast('successToast', message.success.editCart);
                        },
                        function(error) {
                            $rootScope.$broadcast('errorToast', message.failed.editCart);
                        });

                },
                //画面からカートを操作した後、カート商品内容を適切な情報に上書き
                overwrite : function(edited,params) {
                    let index = null;
                    _(cartShared.list).forEach(function(cart,cnt) {
                        if(cart.common_cart_id == edited.id) {
                            index = cnt;
                            return true;
                        }
                    });
                    let cart = cartShared.list[index];

                    //キャプション変更
                    if(!_.isUndefined(params.caption)) {
                        cart.caption     = edited.caption;
                        cart.caption_str = edited.caption_str;
                        if (cart.caption == pageConst.cart.image_edit.logo.id) {
                            $rootScope.$broadcast('showDetail', index);
                        }
                    }

                    //商品内容変更
                    if(!_.isUndefined(params.articleId)) {
                        let price_list = utils.findByObj(cart.photo.pricelist_details, { article_id : Number(params.articleId) });
                        cart.article_id = params.articleId;
                        cart.price      = price_list[0].price;
                    }

                    //商品数変更
                    if(!_.isUndefined(params.amount)){
                        cart.amount    = params.amount;
                    }

                    //トリミング内容変更 TODO トリミングを解除後、レスポンスのis_trimmingがfalseで帰ってきていない為、書き換えています。
                    if(!_.isUndefined(params.TrimmingWidth) && edited.TrimmingWidth == null){
                        cart.is_trimming = false;
                    }

                    $rootScope.$broadcast('changeEnd');
                },
                
                addOriginalValue : function(list) {
                    let res = [];
                    _(list).forEach(function(cart) {
                        res.push({articleId : cart.article_id, amount : cart.amount});
                    });
                    return res;
                },

                copy  : function(params, original) {
                    loader.save(params, function(res) {
                            let copied            = angular.copy(original);
                            copied.amount         = 1;
                            copied.common_cart_id = res.id;
                            copied.article_id     = res.article_id;
                            copied.caption        = res.caption;
                            copied.caption_str    = res.caption_str;
                            copied.price          = original.photo.pricelist_details[0].price;
                            copied.is_trimming    = false;
                            cartShared.list.push(copied);
                            cartShared.subtotal = calculator.calculateSubtotal(cartShared.list);
                            $rootScope.$broadcast('changeEnd');
                            $rootScope.$broadcast('successToast', message.success.addingCart);
                            $rootScope.$broadcast('resize');
                        },
                        function(error) {
                            $rootScope.$broadcast('changeEnd');
                            $rootScope.$broadcast('errorToast', message.failed.editCart);
                        });
                }
            };
        }
    ]);

    //ページング生成フィルター
    app.filter('paging_filter', () => {
        return function(input, start) {
            start = +start;
            return input.slice(start);
        }
    });

    app.factory('CartDataCreator', ['utils',(utils) =>{
        return {
            setPhotosetCode : (carts)  => {
                let res = [];
                _.forEach(carts, (obj) => {

                    if(obj['is_photoset']){
                        let list    = utils.findByObj(obj.page.photosets, { article_id : obj.article_id });
                        obj['code'] = list[0]['code'];
                    }

                    res.push(obj);
                });
                return res;
            },

        }
    }]);
})();
