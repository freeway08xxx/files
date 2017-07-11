/// <reference path="../../typings/main.d.ts" />
(() => {
    'use strict';
    const app = angular.module('trimming.controllers', [
        'trimming.services'
    ]);

    app.controller('TrimmingController', [
        'ItemDataSharedService',
        'TrimmingDataSharedService',
        'ModelSharedService',
        'ViewStatusSharedService',
        'pageData',
        'Loader',
        function (items, trimmingData, models, viewStatus, pageData, loader) {
            this.pageData     = pageData.params();
            this.trimmingData = trimmingData;
            this.viewStatus   = viewStatus;
            this.items        = items;
            this.models       = models;
            loader.fetch();
            //console.log(this);
        }
     ]);
})();
