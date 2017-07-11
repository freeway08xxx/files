/// <reference path="../../typings/main.d.ts" />
(() => {
    'use strict';
    const app = angular.module('trimming.services', [
        'util.services',
        'util.directives',
        'trimming.controllers',
    ]);

    // HTML表示制御用サービス
    app.factory('ViewStatusSharedService',[()=> {
        return {
            show    : {
                small_range : false,
            },
            disabled : {
                register : true
            }
        };
    }]);


    app.factory('MessageSharedService', [()=> {
        return {
            failed  : {
                register   : 'トリミングデータの編集に失敗しました',
                fetchCart  : 'トリミングデータの取得に失敗しました',
                emptyCart  : 'トリミングデータがカートに存在しません',
                cropImage : 'この画像はトリミングできません。商品種類を変更して下さい。'
            },
        };
    }]);


    // トリミングコンテナのサイズ
    app.factory('ContainerSize',['pageConst',(pageConst)=> {
        return function (img){
            let ration = img.width / img.height;
            let max    = pageConst.cropper.max_container_size;
            let max_w,max_h;

            if( ration >= 1){
                max_w = max;
                max_h = max * ration;
            }else{
                max_w = max * ration;
                max_h = max;
            }

            return {'max-width' : max_w ,'max-height' : max_h };
        };
    }]);


    app.factory('TrimmingDataSharedService', [() =>{
        return {
            cartId          : null,
            photo_id        : null,
            article_id      : null,
            TrimmingWidth   : null,
            TrimmingHeight  : null,
            TrimmingX       : null,
            TrimmingY       : null,
        }
    }]);

    app.factory('ItemDataSharedService', [() =>{
        return {
            common_cart     : null,
            current_article : {},
        }
    }]);

    app.factory('ModelSharedService', [() =>{
        return {
            article_id : '',
        }
    }]);

    app.factory('Loader', [
        'ItemDataSharedService',
        'TrimmingDataSharedService',
        'ModelSharedService',
        'CartEditor',
        'ViewStatusSharedService',
        'pageData',
        'pageConst',
        'utils',
        'MessageSharedService',
        '$rootScope',
        function(items, trimmingData, models, cartEditor, viewStatus, pageData, pageConst, utils, message, $rootScope) {
            return {
                fetch   : function()  {
                    let params = {
                        member_id       : pageData.params().member_id,
                        not_member_hash : pageData.params().not_member_hash,
                        store_id        : pageData.params().store_id,
                        cartId          : pageData.params().common_cart_id,
                    };

                    cartEditor.get(params, (res) =>{
                            if(typeof res.photo == 'undefined'){
                                $rootScope.$broadcast('errorToast', message.failed.emptyCart);
                            }else{
                                let current                  = utils.findByObj(res.photo.pricelist_details, {article_id : res.article_id});
                                items.current_article        = current[0];
                                items.common_cart            = res;
                                models.article_id            = items.current_article.article_id.toString();
                                trimmingData.photo_id        = res.photo_id;
                                trimmingData.cartId          = pageData.params().common_cart_id;
                                if(items.current_article.article_id != pageConst.cropper.dl_article_id) viewStatus.disabled.register = false;
                            }

                        },
                        function(error) {
                            $rootScope.$broadcast('errorToast', message.failed.fetchCart);
                        });
                }
            };
        }]);
})();
