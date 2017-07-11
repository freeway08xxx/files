/// <reference path="../../typings/main.d.ts" />
(() => {
    'use strict';
    const app = angular.module('order.controllers', [
        'ngResource',
        'order.services'
    ]);

    //注文画面
    app.controller('OrderController', [
        '$scope',
        'OrderDataSharedService',
        'FormDataSharedService',
        'MemberDataSharedService',
        'OrderModelsSharedService',
        'ViewStatusSharedService',
        'OrderLoader',
        'Calculator',
        'pageData',
        function($scope,orderData,formData,memberData,models,viewStatus,orderLoader,calculator,pageData) {
            //console.log(this);
            this.pageData       = pageData.params();
            this.form           = formData.form;
            this.orderData      = orderData;
            this.memberData     = memberData;
            this.models         = models;
            this.viewStatus     = viewStatus;
            orderLoader.fetch();

            this.calculateOrderTotal = () => {
                if(!_.isNull(models.paymentCode)) {
                    viewStatus.hide.errPaymentCode            = true;
                    orderData.common_cart_summary.payment_fee = orderData.payment_fees[models.paymentCode]['price'];
                    orderData.common_cart_summary             = calculator.calculateOrderSummary(orderData.common_cart_summary);
                }
            };
        }
    ]);
})();
