/// <reference path="../../typings/main.d.ts" />
(() => {
    'use strict';
    const app = angular.module('photolist.services', [
        'ngResource'
    ]);

    app.factory('utils',['pageConst',(pageConst)=>{
        return {
            getUrlPrefix: function () {
                return (window.location.href.indexOf(pageConst.devPrefix) == -1) ? '' : pageConst.devPrefix;
            },
            findByObj   : function(json,matcher) {
                return _.filter(json,_.matches(matcher));
            }
        }
    }]);

    app.factory('CustomRequest', [function() {
        return {
            create  : function() {
                return {
                    get     : this.adjust('GET',  false),
                    query   : this.adjust('GET',  true),
                    save    : this.adjust('POST', false),
                    'delete': this.adjust('DELETE',false)
                };
            },
            adjust  : function(realMethod, isArray) {
                return {
                    method   : realMethod,
                    isArray  : isArray,
                    headers  : {'Authorization':'Bearer ' + PARAMS['access_token'],'Content-Type': 'application/x-www-form-urlencoded'},
                    transformRequest    : function(data){
                        if(typeof data === 'undefined'){
                            return data;
                        }
                        return $.param(data);
                    }
                }
            }
        };
    }]);

    // APIリソース定義
    app.factory('PhotoData', ['$resource','CustomRequest', ($resource,customRequest) => {
        let path = PARAMS['domain'] + '/' + PARAMS['version'] + PARAMS['path'];
        return $resource(path ,{},customRequest.create());
    }]);

    app.factory('CartData', ['$resource', 'CustomRequest', ($resource, customRequest) => {
        let path    = PARAMS['domain'] + '/' + PARAMS['version'] + '/common_carts.json?';
        return $resource(path,{},customRequest.create());
    }]);

    app.factory('CartRemover', ['$resource', 'CustomRequest', ($resource, customRequest) => {
        let path    = PARAMS['domain'] + '/' + PARAMS['version'] + '/common_carts/:cartId.json?';
        return $resource(path,{cartId:'@cartId'},customRequest.create());
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

    app.factory('MessageSharedService', [()=> {
        return {
            addSamePhotoToCart  : 'この写真はすでに買い物かごに入っています。\n買い物かごにいれますか？',
            disableAddingToCart : 'この写真をカートに入れられません。カートを空にしてから追加してください。',
            notSale             : 'この写真をカートに入れられません。非販売写真です。',
            confirm             : '同じ写真が複数枚カートにあります。全て削除してよろしいですか？',
            disableRightClick   : '右クリックはご遠慮いただいております',
            failed  : {
                photo             : '写真データの取得に失敗しました',
                fetchPrice        : '価格データの取得に失敗しました',
                fetchCart         : 'カートデータの取得に失敗しました',
                addingCart        : 'カートの操作に失敗しました',
                deletingCart      : 'カートからの削除に失敗しました',
                eventList         : 'イベントデータの取得に失敗しました',
                errMin            : '%minつ以上選択してください',
                errMax            : '選択できるのは%max枚です。(現在の選択数 : %total枚)'
            },
            success : {
                addingCart      : 'カートに商品が追加されました',
                deletingCart    : 'カートから商品が削除されました'
            },
            progress    : {
                addingCart      : 'カートに追加しています...',
                deletingCart    : 'カートから削除しています...'
            },
            string  : {
                beforeLimit : '（%dateまで）',
                tillToday   : '今日まで',
                limitOver   : '再掲載中'
            }
        };
    }]);

    // HTML表示制御用サービス
    app.factory('ViewStatusSharedService',[()=> {
        return {
            hide    : {
                albumList       : true,
                detailWindow    : true,
                cartWindow      : true,
                zoomWindow      : true,
                completeWindow  : true,
                loadingList     : false,
                loadingDetail   : false,
                navi            : true,
                zeroComment     : true,
                orderLimit      : true,
                maxPhotoCount   : true,
                searchResult    : true,
                photoset        : true,
                photosetOverlay : true,
                photosetTitle   : true,
            },
            show    : {
                loadingPhotoList : true,
            },
        };
    }]);


    // 各種データ格納用
    app.factory('ItemDataSharedService', [()=> {
        return {
            photoCount               : 0,    // 現在のページの最大枚数
            maxPhotoCount            : 0,    // アルバムの最大写真枚数
            photos                   : [],   // ページに表示させる写真データ
            carts                    : [],   // カートに入っている写真データ
            currentPhotoData         : {},   // 詳細画面で表示中の写真データ
            currentPhotoDataIndex    : 0,    // 詳細画面で表示中の写真データインデックス
            cartMapping              : {},   // カート内容と画面表示中の写真マッピング
            premiumRate              : 1,
            photosets                : [],   // セット販売写真データ
            current_photoset         : {},   // セット販売画面で表示中の情報
            photoset_selecting       : '',   // セット販売画面で選択中の項目
            donePhotosetSelectFlgs   : { data : true, print : true }, // セット販売画面で選択する必要がある項目
        };
    }]);

    // セット商品 表示名を日本語に変換
    app.filter('display_name', () => {
        return function(code_name) {
            let map = {
                print    : 'プリント',
                data     : 'ダウンロード'
            };
            return map[code_name];
        }
    });

    // 各種データ加工用
    app.factory('ItemDataCreator', [
        'ItemDataSharedService',
        'pageConst',
        'pageData',
        'utils',
        (itemData,pageConst,pageData,utils) => {
            return {
                buildPhotoset : (responseData) => {
                    let photosets = [];
                    _(responseData).forEach((obj) => {
                        if(obj['article_id'] != pageConst['without_article_id']) photosets.push(obj);
                        if(pageData.params().code === obj['code']) {
                            itemData.current_photoset = obj;
                        }
                    });
                    itemData.photosets = photosets;

                    //DLデータを選択する必要がある
                    if(itemData.current_photoset.data_min > 0)  {
                        itemData.photoset_selecting              = 'data';
                        itemData.donePhotosetSelectFlgs['data']  = false;
                    }

                    //プリントを選択する必要がある
                    if(itemData.current_photoset.print_min > 0) {
                        itemData.donePhotosetSelectFlgs['print'] = false;

                        //初回がプリントの場合
                        if(itemData.donePhotosetSelectFlgs['data']) {
                            itemData.photoset_selecting = 'print';
                        }

                    }
                },

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
                }
            };
        }]);




    app.service('FormDataSharedService', [
        'ViewStatusSharedService',
        'ItemDataSharedService',
        'FormValidateSharedService',
        function(viewStatus,itemData,validate) {
        return {
            complete        : false,
            model           : {},
            selected_ids    : {
                data  : [],
                print : []
            },
            setModels   : function()  {
                let _self         = this;
                let model         = {};
                let init_val      = false;
                _self.complete    = false;

                _.each(_.map(itemData['photos'], 'id'),function(id : string) {

                    if(itemData.current_photoset[itemData.photoset_selecting + '_max'] == null)  init_val  = true;

                    model[id]      = init_val;
                    _self.complete = init_val;
                });

                let type  = itemData.photoset_selecting;
                _self.model              = model;
                _self.selected_ids[type] = validate.setCheckboxData(model);

            },
        };
    }]);


    // イベント詳細データ格納用
    app.factory('EventDataSharedService', [
        'MessageSharedService',
        'ViewStatusSharedService',
        'ItemDataSharedService',
        'utils',
        'pageData',
        '$filter',
        (message,viewStatus,itemData,utils,pageData,$filter)=>  {
        return {
            albumList    : [],
            albumCount   : 0,    // 子区分の数
            left         : '',   // 期日文言
            leftCount    : 0,    // 残り日数
            event_name   : '',   // イベント名
            album_title  : '',   // 表示中の区分名
            zekken       : '',
            campaign_code: '',   // キャンペーンコード
            build        : function(responseData) {
                if(_.isUndefined(pageData.params().album_id) ){
                    viewStatus.hide.searchResult = false;
                    this.search_title = pageData.params().tag;
                }else{
                    this.albumCount  = responseData.albums.length;
                    this.albumList   = this.convertDataStructure(responseData.albums);
                    this.event_name  = responseData.event.event_name;
                    let current_list = utils.findByObj(responseData.albums,{id:Number(pageData.params().album_id)});
                    this.album_title = current_list[0].name;
                    this.campaign_code = responseData.event.campaign_code;
                }
                this.buildDateData(responseData.event);
            },
            buildDateData : function(response) {
                let orderEnd = $filter('date')(response.order_end_date,'yyyy年MM月dd日');
                let leftDays = response.order_end_remaining_days.split('days')[0];
                let leftString  = '';
                if (leftDays > 0) {
                    leftString  = message.string.beforeLimit.replace('%date', orderEnd);
                // 今日まで
                } else if (leftDays === 0) {
                    leftString  = message.string.tillToday;
                // 再掲載中
                } else {
                    leftString  = message.string.limitOver;
                }
                this.left       = leftString;
                this.leftCount  = leftDays;
            },
            convertDataStructure : function(albums){
                let res = [];
                let parent;
                let i = 0 ;
              
                _.forEach(albums, (obj) => {
                    if(obj.directory_level == 2){
                        parent = i;
                        res[i] = obj;
                        res[i]['directory_level3'] = [];
                        i++;
                    }else{
                        if(!_.isUndefined(res[parent])) res[parent]['directory_level3'].push(obj)
                    }
                });
                return res;
            }
        }
    }]);

    // ItemDataSharedService.cartMapping を操作するためのモジュール
    // { photoId : カートに入っている枚数 } といったハッシュを作成/編集する
    app.factory('CartMapping', ['ItemDataSharedService', 'PhotoFinder', function(itemData, finder) {
        return {
            buildList   : function(photoList) {
                _(photoList).forEach(function(item) {
                    let key = 'p' + item.id;
                    itemData.cartMapping[key]   = 0;
                });
            },
            addCartInfo : function(cartList) {
                _(cartList).forEach(function(item) {
                    let photoId = item.photo_id;
                    let key     = 'p' + photoId;
                    if (typeof itemData.cartMapping[key] == 'number') {
                        itemData.cartMapping[key]++;
                        let photo       = finder.find(photoId);
                        if (typeof photo == 'object') {
                            photo.inCart    = true;
                        }
                    }
                });
            },
            getCount    : function(photoId) {
                let key = 'p' + photoId;
                return itemData.cartMapping[key];
            },
            add : function(photoId) {
                let key = 'p' + photoId;
                itemData.cartMapping[key]++
            },
            remove  : function(photoId) {
                let key = 'p' + photoId;
                if (!itemData.cartMapping[key]) {
                    return;
                }

                itemData.cartMapping[key]--;
            }
        };
    }]);

    // ItemDataSharedService.carts へ新規追加するためのカートオブジェクトを生成する
    app.factory('CartCreator', [()=> {
        return {
            build   : function(photoData, cartId) {
                let cart:any;
                cart = {};
                cart.photo_id       = photoData.id;
                cart.common_cart_id = cartId;
                cart.photo      = {
                    photo_url   : {
                        w2  : photoData.photo_url.w2
                    }
                };
                return cart;
            }
        };
    }]);

    // カート登録に関するパラメータを生成
    app.factory('CartParams', [
        'pageConst',
        'pageData',
        'ItemDataSharedService',
        'PhotoFinder',
        (pageConst,pageData,itemData,photoFinder) => {
        return {
            build   : function(photo_id, add_params) {
                let article_id = (_.has(add_params, 'alldata_str')) ? itemData.current_photoset.article_id : photoFinder.find(photo_id).pricelist_details[0].article_id;

                let base = {
                    page_id     : pageData.params().page_id,
                    event_id    : pageData.params().event_id,
                    member_id   : pageData.params().member_id,
                    amount      : pageConst.cartPostAmount,
                    article_id  : article_id,
                    photo_id    : photo_id,
                };

                return  _.extend(base, add_params);
            }
        };
    }]);

    // 初期化処理
    app.factory('Initializer', [
        'DataLoader',
        (loader) => {
            return {
                start   : () => {
                    loader.fetch();
                }
            };
        }]);

    // 写真データ呼び出し処理
    app.factory('DataLoader', [
        'PhotoData',
        'pageData',
        'EventDataSharedService',
        'ViewStatusSharedService',
        'ItemDataSharedService',
        'ItemDataCreator',
        'CartMapping',
        'CartLoader',
        'MessageSharedService',
        '$rootScope',
        (photoData, pageData,eventData,viewStatus,itemData,itemDataCreator,mapping,cartLoader,message,$rootScope) => {
            return {
                fetch   : () => {
                    photoData.get((res) => {
                        if (res.photos.length == 0) {
                            viewStatus.hide.zeroComment      = false;
                            viewStatus.show.loadingPhotoList = false;
                        } else{
                            let photoData        = res.photos;
                            itemData.photoCount  = photoData.length;
                            itemData.params      = pageData.params();
                            itemData.premiumRate = Number(res.event['premiumRate']);
                            itemDataCreator.buildPhotoset(res.event.photosets);
                            eventData.build(res, pageData);
                            mapping.buildList(photoData);

                            if(_.isNull(pageData.params().code)) {
                                cartLoader.fetch(photoData);
                            }else{
                                itemData.photos = photoData;
                                viewStatus.show.loadingPhotoList = false;
                            }
                            viewStatus.hide.orderLimit    = false;
                            viewStatus.hide.maxPhotoCount = false;
                        }
                    },
                    (error) => {
                        $rootScope.$broadcast('errorToast', message.failed.photo);
                        viewStatus.hide.orderLimit       = false;
                        viewStatus.hide.maxPhotoCount    = false;
                        viewStatus.show.loadingPhotoList = false;
                    });
                }
            };
        }
    ]);

    // カートデータ呼び出し処理
    app.factory('CartLoader', [
        'CartData',
        'pageData',
        'ItemDataSharedService',
        'ItemDataCreator',
        'ViewStatusSharedService',
        'CartMapping',
        'MessageSharedService',
        '$rootScope',
        function(cartData,pageData,itemData, itemDataCreator, viewStatus, mapping, message, $rootScope) {
            return {
                fetch   : (photoData) => {
                    let params = {
                        member_id       : pageData.params().member_id,
                        not_member_hash : pageData.params().not_member_hash,
                        store_id        : pageData.params().store_id
                    };

                    cartData.query(params, function(data) {
                        // カートデータの呼び出しが完了してから写真一覧を描画したいためこのタイミングで代入する
                        itemData.photos = photoData;
                        itemData.carts  = itemDataCreator.setPhotosetCode(data);
                        mapping.addCartInfo(data);
                        viewStatus.show.loadingPhotoList = false;
                    },
                    function(error) {
                        itemData.photos = photoData;
                        viewStatus.show.loadingPhotoList = false;
                        $rootScope.$broadcast('errorToast', message.failed.fetchCart);
                    });
                }
            };
        }
    ]);

    // ItemDataSharedService.cartsから対象となるカートデータを取得
    app.factory('CartFinder', ['ItemDataSharedService', function(itemData) {
        return {
            findByCartId    : function(cartId) {
                return _.find(itemData.carts, function(item) {
                    if(item['common_cart_id'] == cartId) {
                        return true;
                    }
                    return false;
                });
            },
            findByPhotoId   : function(photoId) {
                let matcher = {
                    photo_id    : photoId
                };
                return _.filter(itemData.carts, _.matches(matcher));
            }
        };
    }]);

    // ItemDataSharedService.photosから対象となる写真データを取得
    app.factory('PhotoFinder', ['ItemDataSharedService', function(itemData) {
        return {
            find    : function(photoId) {
                return _.find(itemData.photos, function(item) {
                    return item['id'] == photoId;
                });
            }
        };
    }]);


    // カートからの削除処理
    app.factory('CartDataRemover', [
        'CartRemover',
        'ItemDataSharedService',
        'CartMapping',
        'PhotoFinder',
        'CartFinder',
        '$rootScope',
        'MessageSharedService',
        function(cartRemover, itemData, mapping, photoFinder, cartFinder, $rootScope, message) {
            return {
                removeByCartId  : function(cartId, photoId) {
                    let cartParams = {cartId : cartId};
                    mapping.remove(photoId);
                    if (mapping.getCount(photoId) < 1) {
                        let photo       = photoFinder.find(photoId);
                        photo.inCart    = false;
                    }
                    cartRemover.delete(cartParams, function(res) {
                            _.remove(itemData.carts, function(cartItem) {
                                if (cartItem['common_cart_id'] == cartId) {
                                    return true;
                                }
                                return false;
                            });
                            $rootScope.$broadcast('successToast', message.success.deletingCart);
                        },
                        function(error) {
                            let cartItem    = cartFinder.findByCartId(cartId);
                            if (cartItem) {
                                cartItem.deleted = false;
                            }

                            let photoItem   = photoFinder.find(photoId);
                            if (photoItem) {
                                photoItem.inCart = true;
                            }

                            mapping.add(photoId);
                            $rootScope.$broadcast('errorToast', message.failed.deletingCart);
                        });
                },
                removeByPhotoId  : function(photoId) {
                    let cartItems   = cartFinder.findByPhotoId(photoId);
                    if (!cartItems.length) {
                        return;
                    }

                    let _self   = this;
                    _(cartItems).forEach(function(item) {
                        _self.removeByCartId(item.common_cart_id, item.photo_id);
                    })
                }
            };
        }
    ]);

    app.factory('FormValidateSharedService', [
        'MessageSharedService',
        'ItemDataSharedService',
        '$rootScope',
        function(message, itemData,$rootScope) {
            return {
                checkBox  : function(photo_ids) {
                    let complete = false;
                    let type     = itemData.photoset_selecting;
                    let min      = itemData.current_photoset[type + '_min'];
                    let max      = itemData.current_photoset[type + '_max'];
                    let total    = _.without(photo_ids, false).length;

                    if(max == null){
                        if(total < min){
                            $rootScope.$broadcast('errorToast', message.failed.errMin.replace('%min',min));
                        }else{
                            complete = true;
                        }
                    }else{
                        if(total < min){
                            $rootScope.$broadcast('errorToast', message.failed.errMin.replace('%min',min));

                        }else if(total > max){
                            $rootScope.$broadcast('errorToast', message.failed.errMax.replace('%max',max).replace('%total',total));

                        }else if(total == max){
                            complete = true;

                        }
                    }

                    return complete;
                },
                setCheckboxData : function(model) {
                    let res = [];
                    angular.forEach(model, (isChecked,id) => { if(isChecked) res.push(id); });
                    return res;
                },
            };
        }
    ]);

})();
