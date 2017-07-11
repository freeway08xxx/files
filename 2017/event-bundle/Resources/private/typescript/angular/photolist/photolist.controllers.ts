/// <reference path="../../typings/main.d.ts" />
(() => {
    'use strict';
    const app = angular.module('photolist.controllers', [
        'ngResource',
        'photolist.services'
    ]);

    app.controller('RootController',
        ['$scope',
            'ViewStatusSharedService',
            'ItemDataSharedService',
            'Initializer',
            'pageData',
            'EventDataSharedService',
            'PremiumPrice',
            function ($scope,viewStatus,itemData,initializer,pageData,eventData,premiumPrice) {
                //console.log(this);
                this.viewStatus     = viewStatus;
                this.items          = itemData;
                this.event          = eventData;
                this.pageData       = pageData.params();
                initializer.start();

                this.calculatePrice = function(price){
                    return premiumPrice.calculate(price, itemData.premiumRate);
                };
                this.isCurrent = function(album_id){
                    return album_id == pageData.params().album_id;
                }

            }
        ]);

        // サムネイルリスト
        app.controller('ListViewController',['FormDataSharedService',
            function(formData) {
                //console.log(this);
                this.formData = formData;
            }
        ]);
})();
