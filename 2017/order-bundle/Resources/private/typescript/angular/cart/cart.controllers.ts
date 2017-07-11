/// <reference path="../../typings/main.d.ts" />
(() => {
    'use strict';
    const app = angular.module('cart.controllers', [
        'ngResource',
        'cart.services'
    ]);

    //カート画面
    app.controller('CartController',
        ['$scope',
            'CartDataSharedService',
            'CartLoader',
            'Calculator',
            'pageData',
            'pageConst',
            'ViewStatusSharedService',
            function ($scope, carts, loader, calculator, pageData, pageConst, viewStatus) {
                //console.log(this);
                loader.fetch();
                this.pageConst   = pageConst.cart;
                this.pageData    = pageData.params();
                this.carts       = carts;
                this.viewStatus  = viewStatus;
                let _self        = this;

                $scope.$on('changeEnd', () => {
                    _self.carts.subtotal = calculator.calculateSubtotal(_self.carts.list);
                });
            }
        ]);

})();
