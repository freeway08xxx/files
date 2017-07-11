/// <reference path="../../typings/main.d.ts" />
(() => {
    'use strict';
    const app = angular.module('order.services', [
        'ngResource',
        'util.services',
        'util.directives',
    ]);

    app.factory('MessageSharedService', [()=> {
        return {
            failed  : {
                addingCoupon      : 'ご利用できないクーポンコードです',
                fetchCorrectCoupon: 'クーポンの入力でシステムエラーが発生しました',
                callApi           : '通信エラーが発生しました',
                fetchOrder        : '注文データの取得に失敗しました',
                fetchOrderEmpty   : '注文データがありません',
                addingOrder       : '注文に失敗しました',
                sendCredit        : 'クレジットカード情報を正しく入力してください',
                addAddress        : 'お届け先情報の登録に失敗しました'

            },
            success : {
                progressCoupon    : 'クーポンを適用しています...',
                discountCoupon    : 'クーポン割引が適用されました',
                registeringOrder  : '注文情報を送信しています...',
                sendingCredit     : 'クレジットカード情報を確認しています',
                sendCredit        : 'クレジットカード情報を確認しました',
                addAddress        : 'お届け先情報を登録しました',
            }
        };
    }]);


    // HTML表示制御用サービス
    app.factory('ViewStatusSharedService',[()=> {
        return {
            hide    : {
                errForm            : true,
                errShipAddresses   : true,
                errPaymentCode     : true,
                errCredit          : true,
                couponDiscount     : true,
            },
            show : {
                loading          : true,
                calculatingTotal : true,
                payment_cod      : true,
                payment_np_wiz   : true,
            },
            disabled : {
                register : true
            },
            flag : {
                isMinusTotal : false
            }
        };
    }]);

    //submit
    app.service('SubmitService', ['utils', (utils) => {
        let path = '//' + location.hostname  + utils.getUrlPrefix() + '/order-complete';
        return {
            send : (num) => {
                $('<form/>', {action: path, method: 'post'}).append($('<input/>', {type: 'hidden', name: 'num', value:num })).appendTo(document.body).submit();
            }
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

    //注文送信パラメータ
    app.factory('FormDataSharedService', [()=> {
        return {
            form : {
                bill_address1               : null,
                bill_address2               : null,
                bill_address3               : null,
                bill_first_name             : null,
                bill_first_name_ruby        : null,
                bill_last_name              : null,
                bill_last_name_ruby         : null,
                bill_prefecture             : null,
                bill_tel                    : null,
                bill_zip                    : null,
                coupon_code                 : null,
                email                       : null,
                member_id                   : null,
                notice                      : null,
                payment_code                : null,
                ship_address1               : null,
                ship_address2               : null,
                ship_address3               : null,
                ship_first_name             : null,
                ship_first_name_ruby        : null,
                ship_last_name              : null,
                ship_last_name_ruby         : null,
                ship_prefecture             : null,
                ship_tel                    : null,
                ship_zip                    : null,
                shipping_code               : null,
                store_id                    : null,
                credit_transaction_id       : null,
                different_ship_address_flag : null,
            }
        };
    }]);

    //支払い元とは別の住所に送る場合のデータセット
    app.factory('ShipParamsService', [() =>{
        return {
            ship_address1               : null,
            ship_address2               : null,
            ship_address3               : null,
            ship_first_name             : null,
            ship_first_name_ruby        : null,
            ship_last_name              : null,
            ship_last_name_ruby         : null,
            ship_prefecture             : null,
            ship_tel                    : null,
            ship_zip                    : null,
        };
    }]);

    //ng-modelで取得できる値
    app.factory('OrderModelsSharedService', [()=> {
        return {
                ship_addresses   : {},
                paymentCode      : null,
                notice           : null,
                confirm          : false,
                coupon_code      : '',
                useShipFlag      : false,
                useAddFormFlg    : false,
                addForm : {
                    address1        : null,
                    address2        : null,
                    address3        : null,
                    firstname       : null,
                    firstname_ruby  : null,
                    lastname        : null,
                    lastname_ruby   : null,
                    prefecture      : null,
                    tel             : null,
                    zipcode         : null,
                    mobile          : null,
                    fax             : null,
                },
        };
    }]);


    app.factory('OrderDataSharedService', [() =>{
        return {
            common_carts        : [],
            fees                : [],
            payment_fees        : {},
            api_paths           : {
                confirm  : '',
                register : '',
            },
            common_cart_summary : {
                amount_sum      : 0,
                price_sum       : 0,
                shipment_fee    : 0,
                payment_fee     : 0,
                subtotal_sum    : 0,
                coupon_discount : 0,
                is_download     : false,
                is_print        : false
            }
        }
    }]);

    //会員情報
    app.factory('MemberDataSharedService',[() =>{
        return {
            member_addresses : {
                bill_address     : [],
                ship_addresses   : [],
            },
            has_ship_address   : false,
            selectedShipsIndex : null,
            build : function(list){
                _(list).forEach((obj,i) => {
                    if(obj['is_default_bill_member_address']){
                        this.member_addresses.bill_address.push(obj);
                    }else{
                        this.member_addresses.ship_addresses.push(obj);
                    }
                });
                this.has_ship_address = (this.member_addresses.ship_addresses.length > 0);
            }
        }
    }]);

    app.factory('FeesDataCreator', ['pageConst',(pageConst) =>{
        return {
            buildPaymentFees  : (fees) => {
                let res = {};
                _(fees).forEach((obj) => {
                    if(_.indexOf(pageConst.order.payment_codes, obj['payment_code']) != -1 ) res[obj['payment_code']] = obj;

                });
                return res;
            },
        }
    }]);

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

    app.factory('OrderLoader', [
        'OrderData',
        'OrderDataSharedService',
        'CartDataCreator',
        'ShippingFeesLoader',
        'MemberDataSharedService',
        'FormParamsCreator',
        'pageData',
        'pageConst',
        'utils',
        'FeesDataCreator',
        'MessageSharedService',
        '$rootScope',
        function(orderData,orderDataShared,cartDataCreator,shippingFeesLoader,memberDataShared,formParamsCreator,pageData,pageConst,utils,feesDataCreator,message,$rootScope) {
        return {
            fetch   : function() {
                let params = {
                    member_id       : pageData.params().member_id,
                    not_member_hash : pageData.params().not_member_hash,
                    store_id        : pageData.params().store_id,
                };

                orderData.get(params, function(res) {
                    if (res.common_carts.length < 1) {
                        location.href = utils.getUrlPrefix() + pageConst.order.redirect_cart_url;
                        return false;
                    }
                    
                    orderDataShared.common_carts                      = cartDataCreator.setPhotosetCode(res.common_carts);
                    orderDataShared.common_cart_summary               = _.extend(orderDataShared.common_cart_summary,res.common_cart_summary);
                    orderDataShared.payment_fees                      = feesDataCreator.buildPaymentFees(res.fees);
                    orderDataShared.fees                              = res.fees;
                    orderDataShared.member_addresses                  = memberDataShared.build(res.member_addresses);
                    pageConst.order.shipment_fee_params.shipping_code = formParamsCreator.setShippingCode();
                    formParamsCreator.setBillAddress();
                    shippingFeesLoader.fetch(res.fees);

                },

                function(error) {
                    $rootScope.$broadcast('errorToast', message.failed.fetchOrder);
                });
            }
        };
    }]);

    app.factory('ShippingFeesLoader', [
        'utils',
        'OrderDataRegister',
        'OrderDataSharedService',
        'FormParamsCreator',
        'MessageSharedService',
        'Calculator',
        'ViewStatusSharedService',
        'pageData',
        'pageConst',
        '$rootScope',
        function(utils,dataRegister,orderDataShared,formParamsCreator,message,calculator,viewStatus,pageData,pageConst,$rootScope) {
            return {
                fetch   : function(fees) {
                    let path_params = {
                        common_cart_ids : _.map(orderDataShared.common_carts,'common_cart_id'),
                        member_id       : pageData.params().member_id,
                        confirm         : true
                    };
                    let q = formParamsCreator.createFormParams(path_params);

                    dataRegister.confirm(q, (res) => {
                        let shipment_list = utils.findByObj(fees, pageConst.order.shipment_fee_params);
                        shipment_list     = utils.findByObj(res.order_details, { fee_name : shipment_list[0]['name'] });

                        if(typeof shipment_list[0] != 'undefined'){
                            orderDataShared.payment_fees['shipment']         = shipment_list[0];
                            //暫定 ダウンロードのみ又は5400円以上であれば送料無料
                            orderDataShared.common_cart_summary.shipment_fee = (orderDataShared.common_cart_summary.is_download || orderDataShared.common_cart_summary.subtotal_sum >= pageConst.order.shipment_free_price) ? 0: shipment_list[0]['subtotal'];
                        }

                        orderDataShared.common_cart_summary              = calculator.calculateOrderSummary(orderDataShared.common_cart_summary);
                        viewStatus.show.calculatingTotal                 = false;


                        //ダウンロードが含まれている creditのみ
                        if(!orderDataShared.common_cart_summary.is_print){
                            viewStatus.show.payment_cod    = false;
                            viewStatus.show.payment_np_wiz = false;
                        }

                    },
                    function(error) {
                        $rootScope.$broadcast('errorToast', message.failed.fetchOrder);
                    });
                }
            };
        }]);

    //クレジットカードのパラメータ生成
    app.factory('CreditParamsCreator',[
        'pageData',
        'OrderModelsSharedService',
        'OrderDataSharedService',
        'FormDataSharedService',
        (pageData,models,orderData,formData) =>{
        return {
            creditParams : {
                member_id             : pageData.params().member_id,
                store_id              : pageData.params().store_id,
                transaction_id        : null,
                transaction_id_before : null, //前回のトランザクションID
                status_code           : null,
                amount                : null,
                credit_number         : null,
                credit_name           : null,
                credit_month          : null,
                credit_year           : null,
                credit_security_code  : null
            },
            build : function(form)  {
                this.creditParams.amount                = orderData.common_cart_summary.total;
                this.creditParams.credit_number         = form.creditNumber;
                this.creditParams.credit_name           = form.creditName;
                this.creditParams.credit_month          = form.creditMonth;
                this.creditParams.credit_year           = form.creditYear;
                this.creditParams.credit_security_code  = form.creditSecurityCode;
                this.creditParams.transaction_id_before = (this.hasCreditChanged(this.creditParams,form)) ? null:this.creditParams.transaction_id;
                this.creditParams.transaction_id        = null;
                return this.creditParams;
            },

            hasCreditChanged : function(creditParams,form){
                let has_changed = false;
                _.forEach(form, (value,key) => {
                    if(creditParams[key] != value) has_changed = true;
                });
                return has_changed;
            },
            setTransactionId : function(transaction_id)  {
                this.creditParams.transaction_id   = transaction_id;
                formData.form.credit_transaction_id = transaction_id;
            }
        }
    }]);

    app.factory('FormParamsCreator', [
        'MemberDataSharedService',
        'FormDataSharedService',
        'OrderModelsSharedService',
        'ShipParamsService',
        'OrderDataSharedService',
        'pageData',
        'pageConst',
        function(memberData, formData, models, shipParams, orderData, pageData, pageConst) {
            return {
                setBillAddress : () => {
                    let bill_address          = memberData.member_addresses.bill_address[0];
                    let form                  = formData.form;
                    form.bill_first_name      = bill_address.firstname;
                    form.bill_first_name_ruby = bill_address.firstname_ruby;
                    form.bill_last_name       = bill_address.lastname;
                    form.bill_last_name_ruby  = bill_address.lastname_ruby;
                    form.email                = bill_address.email;
                    form.bill_zip             = bill_address.zipcode;
                    form.bill_prefecture      = bill_address.prefecture;
                    form.bill_address1        = bill_address.address1;
                    form.bill_address2        = bill_address.address2;
                    form.bill_address3        = bill_address.address3;
                    form.bill_tel             = bill_address.tel;
                    form.store_id             = pageData.params().store_id;
                    form.member_id            = pageData.params().member_id;
                    form.shipping_code        = pageConst.order.shipment_fee_params.shipping_code;
                },
                setShipAddress : () => {
                    let ship_addresses =  memberData.member_addresses.ship_addresses;
                    let ship           = (models.useAddFormFlg) ? models.addForm : ship_addresses[memberData.selectedShipsIndex];
                    return  {
                        ship_address1        : ship.address1,
                        ship_address2        : ship.address2,
                        ship_address3        : ship.address3,
                        ship_first_name      : ship.firstname,
                        ship_first_name_ruby : ship.firstname_ruby,
                        ship_last_name       : ship.lastname,
                        ship_last_name_ruby  : ship.lastname_ruby,
                        ship_prefecture      : ship.prefecture,
                        ship_tel             : ship.tel,
                        ship_zip             : ship.zipcode,
                    };
                },
                setModelParams : () => {
                    return {
                        notice        : models.notice,
                        payment_code  : models.paymentCode,
                        coupon_code   : models.coupon_code,
                    };
                },
                createFormParams : function(add_path_params = {}) {
                    formData.form.different_ship_address_flag = (models.useShipFlag) ? 1 : 0;
                    let ship = (models.useShipFlag && !_.isNull(memberData.selectedShipsIndex)) ? this.setShipAddress() : shipParams;
                    if(models.paymentCode != 'credit') formData.form.credit_transaction_id = null;
                    return _.extend(formData.form, ship, this.setModelParams(), add_path_params);
                },

                setAddressParams : (list) => {
                    let res = {};

                    _.forEach(list, (value,key) => {
                        if(value == null || typeof(value) == 'undefined' ||  value == '') return true;
                            res[key] = value;
                    });

                    return res;
                },
                 setShippingCode : function (){
                    let res = '';
                    if(orderData.common_cart_summary.is_download){
                        res = 'download'
                    }else if(pageConst.order.takuhai_min <= orderData.common_cart_summary.subtotal_sum){
                        res = 'takuhai'
                    }else{
                        res = 'mailbin'
                    }

                    return res;
                }
            };
        }]);

    //注文確認画面のカスタムバリデーション
    app.factory('OrderValidateService', [
        'OrderModelsSharedService',
        'ViewStatusSharedService',
        'FormDataSharedService',
        function(models,viewStatus,formDataShared) {
            return {
                order : function($scope){
                    let form        = $scope.form;
                    let addForm     = $scope.addForm;
                    let errCnt      = 0;
                    let isValidated = true;

                    //支払い方法未選択
                    if(models.paymentCode == null){
                        errCnt++;
                        viewStatus.hide.errPaymentCode = false;
                    }

                    //別の住所選択 住所未選択
                    if(models.useShipFlag && _.values(models.ship_addresses).indexOf(true) == -1){
                        viewStatus.hide.errShipAddresses = false;
                        errCnt++;
                    }

                    //新住所登録選択 必須入力情報エラー
                    if(models.useAddFormFlg){
                        if(addForm.ship_lastname.$invalid || addForm.ship_firstname.$invalid || addForm.ship_lastname_ruby.$invalid || addForm.ship_firstname_ruby.$invalid ||
                            addForm.ship_zip.$invalid || addForm.ship_prefecture.$invalid || addForm.ship_address1.$invalid || addForm.ship_address2.$invalid || addForm.ship_tel.$invalid){
                            errCnt++;
                        }
                    }

                    if(models.paymentCode === 'credit'){
                        //クレジット払い選択 入力情報エラー
                        if(form.creditNumber.$invalid || form.creditName.$invalid || form.creditSecurityCode.$invalid){
                            errCnt++;
                        }

                        //与信エラー
                        if(_.isNull(formDataShared.form.transaction_id)){
                            viewStatus.hide.errCredit = false;
                            errCnt++;
                        }
                    }

                    //Form内エラー1つ以上あり
                    if(errCnt > 0){
                        isValidated             = false;
                        viewStatus.hide.errForm = false;
                    }
                    return isValidated;
                },

                credit : (form) =>{
                    let res  = true;
                    if(models.paymentCode !== 'credit' || form.creditNumber.$invalid || form.creditName.$invalid || form.creditSecurityCode.$invalid){
                        res = false;
                    }
                    return res;
                },

                registerBtn : function (){
                    return (models.confirm && !viewStatus.flag.isMinusTotal);
                }
            };
        }]);

})();
