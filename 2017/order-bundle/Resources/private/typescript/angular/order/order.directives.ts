/// <reference path="../../typings/main.d.ts" />
(() => {
    'use strict';
    const app = angular.module('order.directives', [
            'ngResource',
            'ngMessages',
            'util.services',
            'util.directives',
            'order.controllers',
            'order.services'
    ]);

    app.directive('nospace', ['$parse', function($parse) {
        return {
            require : 'ngModel',
            link: function(scope, elem, attr, ctrl) {
                ctrl.$parsers.unshift(function(viewValue) {
                    if (typeof viewValue !== 'string') {
                        // 文字列でない場合はチェック対象としない (空白チェックを通す)
                        ctrl.$setValidity('nospace', true);
                        return viewValue;
                    }

                    if (viewValue.match(/[ 　]/g)) {
                        ctrl.$setValidity('nospace', false);
                        return undefined;
                    } else {
                        ctrl.$setValidity('nospace', true);
                        return viewValue;
                    }
                });
            }
        };
    }]);

    app.directive('prefectureList', [() => {
        return {
            restrict    : 'EA',
            require     : '^ngModel',
            replace     : true,
            template    :
            '<select name="ship_prefecture" required ng-model="order.models.addForm.prefecture">'
            +'<option value="">都道府県</option>'
            +'<option ng-repeat="pref in prefectures" value="{[{ pref }]}">{[{ pref }]}</option>'
            +'</select>',
            controller  : $scope => {
                $scope.prefectures = [
                    '北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県',
                    '福島県', '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県',
                    '東京都', '神奈川県', '新潟県', '富山県', '石川県', '福井県',
                    '山梨県', '長野県', '岐阜県', '静岡県', '愛知県', '三重県',
                    '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県',
                    '鳥取県', '島根県', '岡山県', '広島県', '山口県', '徳島県',
                    '香川県', '愛媛県', '高知県', '福岡県', '佐賀県', '長崎県',
                    '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'
                ];
            }
        };
    }]);

    app.directive('creditYearList', ['$compile',($compile) => {
        return {
            link  : ($scope,$elm) => {
                let current     = new Date().getFullYear(),
                    template    =   ''+
                                    '<select credit-listener ng-model="form.credit.creditYear" name="creditYear" ng-init="form.credit.creditYear=' +'\'' + current + '\''+ '">' +
                                    '<option ng-repeat="year in yearList">{[{ year }]}</option>' +
                                    '</select>',
                    createCount = 20,   // 何年分生成するか
                    end         = current + createCount,
                    yearList    = [];

                for (let i=current;i<end;i++) {
                    yearList.push(i);
                }

                $scope.yearList = yearList;
                $elm.append($compile(template)($scope));
            }
        };
    }]);

    //１つのみ選択できかつ、２度押しでチェック削除できるボタン
    app.directive('customCheck', [
        'OrderModelsSharedService',
        'MemberDataSharedService',
        'ViewStatusSharedService',
        (models,memberData,viewStatus) => {
        return {
            link  : ($scope,$element,$attr) => {
                let elem    = angular.element($element[0]);
                elem.bind({
                    change : function(e){
                        e.preventDefault();
                        _(models.ship_addresses).forEach((obj, i) =>{
                            models.ship_addresses[i] = false;
                        });

                        let checked = $(elem).prop('checked');
                        memberData.selectedShipsIndex = (checked) ? $attr.customCheck : null;
                        models.ship_addresses[$attr.customCheck] = checked;
                        viewStatus.hide.errShipAddresses         = checked;
                        models.useAddFormFlg                     = (checked && memberData.member_addresses.ship_addresses.length == memberData.selectedShipsIndex);
                        $scope.$apply();
                    }
                })
            }
        };
    }]);

    //別の住所指定の時はクレジット支払いのみ
    app.directive('paymentClear', [
        'OrderModelsSharedService',
        'ViewStatusSharedService',
        'OrderDataSharedService',
        (models,viewStatus,orderData) => {
            return {
                link  : ($scope,$element) => {
                    let elem    = angular.element($element[0]);
                    elem.bind({
                        change : function(e){
                            e.preventDefault();

                            //ダウンロードが含まれていない
                            if(orderData.common_cart_summary.is_print){
                                viewStatus.show.payment_cod    = !$(elem).prop('checked');
                                viewStatus.show.payment_np_wiz = !$(elem).prop('checked');
                            }

                            if( $(elem).prop('checked') && models.paymentCode != 'credit')  models.paymentCode = null;
                            $scope.$apply();
                        }
                    })
                }
            };
        }]);

    //お客様情報をTwigに出力
    app.directive('memberAddressesCompiler', ['$compile',function($compile) {
        return {
            restrict    : 'EA',
            link  : function($scope,$elm,$attr) {
                let template = '' +
                    '<li ng-repeat="member_address in '+$attr.list+' track by $index" ng-cloak>' +
                        '<p>{[{ member_address.lastname }]} {[{ member_address.firstname }]} （{[{ member_address.lastname_ruby }]} {[{ member_address.firstname_ruby }]}）</p>' +
                        '<p>〒{[{ member_address.zipcode }]}<br>' +
                        '{[{ member_address.address1 }]}{[{ member_address.address2 }]} {[{ member_address.address3 }]}</p>' +
                        '<p>メールアドレス：{[{ member_address.email }]}<br>' +
                        '電話番号：{[{ member_address.tel }]}</p>' +
                        '<p ng-if="member_address.is_default_bill_member_address==false" class="regi_check"><label class="check"><input type="checkbox" value="{[{$index}]}" name="ship_addresses" ng-model="order.models.ship_addresses[$index]" custom-check="{[{$index}]}"/><span>この住所を使う</span></label></p>'+
                    '</li>';
                $elm.append($compile(template)($scope));
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

    //住所登録
    app.directive('memberRegister', [
        'OrderModelsSharedService',
        'FormParamsCreator',
        'MemberDataSharedService',
        'MemberRegister',
        'MessageSharedService',
        '$rootScope',
        function(models, paramsCreator, memberData, memberRegister, message, $rootScope) {
            return {
                link  : function($scope,$element) {
                    let elem    = angular.element($element[0]);
                    elem.bind({
                        click   : function(e) {
                            e.preventDefault();
                            $scope.addForm.$submitted = true;
                            $scope.$apply();

                            if($scope.addForm.$invalid) {
                                _error();
                                return false;
                            }

                            let params = paramsCreator.setAddressParams(models.addForm);

                            memberRegister.save(params, (res) => {
                                res.is_default_bill_member_address = false;
                                memberData.has_ship_address        = true;
                                memberData.member_addresses.ship_addresses.push(res);

                                $rootScope.$broadcast('successToast', message.success.addAddress);
                                $('body,html').animate({scrollTop : $('#member_addresses').offset().top},0);
                            },
                            function(error) {
                                _error();
                            });

                            function _error(){
                                $rootScope.$broadcast('errorToast', message.failed.addAddress);
                                $('body,html').animate({scrollTop : $('form[name="addForm"]').offset().top},1000);
                            }
                        }
                    });
                }
            };
        }]);

    //クレジット情報の変更があったら与信確認
    app.directive('creditListener',[
        'OrderValidateService',
        'CreditParamsCreator',
        'CreditData',
        'ViewStatusSharedService',
        'MessageSharedService',
        '$rootScope',
        (validate,creditParamsCreator,creditData,viewStatus,message,$rootScope) => {
        return {
            link: function(scope, elem) {
                elem.bind('change', () => {
                    let is_validated = validate.credit(scope.form);

                    if(is_validated){
                        $rootScope.$broadcast('successToast', message.success.sendingCredit);
                        let params = creditParamsCreator.build(scope.form.credit);
                        creditData.send(params, (res) => {
                            viewStatus.hide.errCredit = true;
                            creditParamsCreator.setTransactionId(res.transaction_id);
                            $rootScope.$broadcast('successToast', message.success.sendCredit);
                        },
                        function(error) {
                            $rootScope.$broadcast('errorToast', message.failed.sendCredit);
                            viewStatus.hide.errCredit = false;
                        });

                    }
                });
            }
        }
    }]);

    //注文確定ボタンのdisabled制御
    app.directive('switchRegisterDisabled',[
        'ViewStatusSharedService',
        'OrderModelsSharedService',
        'OrderValidateService',
        (viewStatus,models,validate) => {
            return {
                link: function(scope, elem) {
                    elem.bind('change', () => {
                        models.confirm               = $(elem).prop('checked');//safariはchangeからng-modelの値が正しく取れないので代入
                        viewStatus.disabled.register = !validate.registerBtn();
                        scope.$apply();
                    });
                }
            }
        }]);

    //クーポン入力で合計金額を変更
    app.directive('couponDiscounter', [
        'OrderDataSharedService',
        'OrderDataRegister',
        'FormDataSharedService',
        'FormParamsCreator',
        'OrderModelsSharedService',
        'Calculator',
        'pageConst',
        'MessageSharedService',
        'ViewStatusSharedService',
        'OrderValidateService',
        '$timeout',
        '$rootScope',
        'utils',
        function(orderData,dataRegister,formData,formParamsCreator,models,calculator,pageConst,message,viewStatus,validate,$timeout,$rootScope,utils) {
            return {
                link  : function($scope,$element) {
                    let elem    = angular.element($element[0]);
                    let stack   = [];
                    elem.bind({
                        keyup   : e => {
                            e.preventDefault();
                            let invalid_interval = 1000;
                            stack.push(e);

                            $timeout(() => {
                                stack.pop();

                                //入力の度に処理をせず最後のキー入力のみ処理
                                if (stack.length == 0) {
                                    viewStatus.flag.isMinusTotal = false;
                                    let path_params = {
                                        common_cart_ids : _.map(orderData.common_carts,'common_cart_id'),
                                        confirm         : true
                                    };
                                    let form = formParamsCreator.createFormParams(path_params);
                                    form.payment_code  = null;

                                    if(models.coupon_code !== '') $rootScope.$broadcast('successToast', message.success.progressCoupon);
                                    dataRegister.register(form, (res) => {
                                        viewStatus.hide.couponDiscount = false;

                                        //データ構造上クーポン割引の値を取得するのはのfeesのdiscount_nameとres.order_detailsのdisplay_nameを一致させる
                                        let coupon_list  = utils.findByObj(orderData.fees, pageConst.order.coupon_fee_params);
                                        coupon_list      = utils.findByObj(res.order_details, { discount_name : coupon_list[0]['display_name'] });

                                        let shipment_list = utils.findByObj(orderData.fees, pageConst.order.shipment_fee_params);
                                        shipment_list     = utils.findByObj(res.order_details, { fee_name : shipment_list[0]['name'] });

                                        if(coupon_list.length < 1){
                                            orderData.common_cart_summary.coupon_discount = 0;
                                        }else{
                                            $rootScope.$broadcast('successToast', message.success.discountCoupon);
                                            orderData.common_cart_summary.coupon_discount = coupon_list[0].subtotal;
                                            orderData.common_cart_summary.shipment_fee    = shipment_list[0].subtotal;
                                        }
                                        orderData.common_cart_summary = calculator.calculateOrderSummary(orderData.common_cart_summary);
                                        viewStatus.disabled.register  = !validate.registerBtn();
                                    },
                                    function(error) {
                                        //TODO APIのエラーメッセージをそのまま表示 暫定として500エラーはクーポンルールエラーとして判定
                                        let err = (error.status === 500) ? message.failed.addingCoupon : message.failed.callApi;
                                        $rootScope.$broadcast('errorToast', err);
                                        orderData.common_cart_summary.coupon_discount = 0;
                                        orderData.common_cart_summary = calculator.calculateOrderSummary(orderData.common_cart_summary);
                                        viewStatus.disabled.register  = !validate.registerBtn();
                                    });


                                    stack = [];
                                }
                            }, invalid_interval);

                        }
                    });
                }
            };
        }]);

    //注文登録
    app.directive('register', [
        'OrderDataSharedService',
        'OrderValidateService',
        'OrderDataRegister',
        'SubmitService',
        'FormParamsCreator',
        'ViewStatusSharedService',
        'MessageSharedService',
        '$rootScope',
        function(orderData,validate,dataRegister,submitService,formParamsCreator,viewStatus,message,$rootScope) {
        return {
            link  : function($scope,$element) {
                let elem    = angular.element($element[0]);
                elem.bind({
                    click   : function(e) {
                        e.preventDefault();
                        $scope.form.$submitted       = true;
                        viewStatus.disabled.register = true;

                        if(!validate.order($scope)) {
                            _error();
                        }else{
                            $rootScope.$broadcast('successToast', message.success.registeringOrder);
                            let path_params = {
                                common_cart_ids : _.map(orderData.common_carts,'common_cart_id')
                            };

                            let form = formParamsCreator.createFormParams(path_params);
                            if(orderData.common_cart_summary.coupon_discount == 0) form.coupon_code = null;

                            dataRegister.register(form, (res) => {
                                if(!_.isNull(res.num)) submitService.send(res.num);
                            },
                            function(error) {
                                $rootScope.$broadcast('errorToast', message.failed.addingOrder);
                                viewStatus.disabled.register = false;
                            });

                        }

                        function _error(){
                            $('body,html').animate({scrollTop: 0},1000);
                            viewStatus.hide.errForm      = false;
                            viewStatus.disabled.register = false;
                            $scope.$apply();
                        }
                    }
                });
            }
        };
    }]);

})();

