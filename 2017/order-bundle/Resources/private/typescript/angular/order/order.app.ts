/// <reference path="../../typings/main.d.ts" />

(() => {
    const app   = angular.module('order', [
        'ngResource',
        'ngTouch',
        'ngMessages',
        'util.services',
        'util.directives',
        'order.services',
        'order.directives',
        'order.controllers',
    ]);

    app.config(($interpolateProvider) => {
        // Twigとバッティングするので調整
        $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
    });


    app.constant('pageData', {
        params  : () => {
            return PARAMS;
        }
    });

    app.constant('pageConst', {
        devPrefix : '/app_dev.php',
        cart : {
            patch_delete_params : ['cartId'],
        },
        order  : {
            register_delete_post_params : ['common_cart_ids','confirm'],
            payment_codes               : ['credit','np_wiz','cod'],
            redirect_cart_url           : '/order-cart',
            coupon_fee_params           : {
                type_code :'coupon'
            },
            shipment_fee_params         : {
                type_code     : 'shipment',
                payment_code  : null,
                shipping_code : ''
            },
            shipment_free_price : 5400,
            takuhai_min         : 20000
        },
        photoset : {
            sdcd : {
                ids   : ['17','42','43'],
                image : '/img/common/photoset_sdcd.png'
            }
        }
    });
})();
