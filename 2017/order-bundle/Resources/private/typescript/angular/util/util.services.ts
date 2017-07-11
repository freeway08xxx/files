/// <reference path="../../typings/main.d.ts" />
(() => {
    'use strict';
    const app = angular.module('util.services', [
        'ngResource',
    ]);

    app.factory('utils', ['pageConst', function (pageConst) {
        return {
            getUrlPrefix: function () {
                return (window.location.href.indexOf(pageConst.devPrefix) == -1) ? '' : pageConst.devPrefix;
            },
            findByObj   : function(json,matcher) {
                return _.filter(json,_.matches(matcher));
            }
        };
    }]);

    app.factory('CustomRequest', ['pageConst',function(pageConst) {
        return {
            create  : function() {
                return {
                    get      : this.adjust('GET',false),
                    query    : this.adjust('GET',true),
                    save     : this.adjust('POST',false),
                    send     : this.adjust('POST',false),
                    register : this.adjust('POST',false, pageConst.order.register_delete_post_params),
                    confirm  : this.adjust('POST',false, pageConst.order.register_delete_post_params),
                    patch    : this.adjust('PATCH',false, pageConst.cart.patch_delete_params),
                    'delete' : this.adjust('DELETE',false)
                };
            },
            adjust  : function(realMethod,isArray,delete_params = null) {
                return {
                    method   : realMethod,
                    isArray  : isArray,
                    headers  : {'Authorization':'Bearer ' + PARAMS['access_token'],'Content-Type': 'application/x-www-form-urlencoded'},
                    transformRequest : data => {
                        if(typeof data === 'undefined') return data;

                        if(!_.isNull(delete_params)){
                            _.forEach(delete_params, value => { delete data[value] });
                        }
                        return $.param(data);
                    }
                }
            }
        };
    }]);

    // APIリソース定義
    app.factory('CartData', ['$resource', 'CustomRequest', ($resource, customRequest) => {
        let path = PARAMS['api_root']+'/common_carts.json';
        return $resource(path,{},customRequest.create());
    }]);

    app.factory('CartEditor', ['$resource', 'CustomRequest', ($resource, customRequest) => {
        let path = PARAMS['api_root'] + '/common_carts/:cartId.json';
        return $resource(path,{cartId:'@cartId'},customRequest.create());
    }]);

    app.factory('OrderData', ['$resource', 'CustomRequest', ($resource, customRequest) => {
        let path = PARAMS['api_root']+'/order-form.json';
        return $resource(path,{},customRequest.create());
    }]);

    app.factory('OrderDataRegister', ['$resource', 'CustomRequest', ($resource, customRequest) => {
        let path = PARAMS['api_root'] + '/orders.json?member_id=:member_id&common_cart=:common_cart_ids&confirm=:confirm';
        return $resource(path,{member_id:'@member_id',common_cart_ids:'@common_cart_ids',confirm:'@confirm'},customRequest.create());
    }]);

    app.factory('CreditData', ['$resource', 'CustomRequest', ($resource, customRequest) => {
        let path = PARAMS['api_root'] + '/credits.json';
        return $resource(path,{},customRequest.create());
    }]);

    app.factory('MemberRegister', ['$resource', 'CustomRequest', ($resource, customRequest) => {
        let path = PARAMS['api_root'] + '/member-addresses.json';
        return $resource(path,{},customRequest.create());
    }]);




})();
