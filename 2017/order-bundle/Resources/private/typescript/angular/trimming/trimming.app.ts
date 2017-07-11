/// <reference path="../../typings/main.d.ts" />

(() => {
    const app   = angular.module('trimming', [
        'ngResource',
        'util.services',
        'util.directives',
        'trimming.services',
        'trimming.directives',
        'trimming.controllers',
    ]);

    app.config(($interpolateProvider) => {
        // Twigとバッティングするので調整
        $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
    });

    app.constant('pageData', {
        params  : () => {
            let arr  = window.location.href.split('/');
            let i    = arr.indexOf('common-carts') + 1;
            PARAMS['common_cart_id'] = arr[i];
            return PARAMS;
        }
    });

    app.constant('pageConst', {
        devPrefix : '/app_dev.php',
        order  : {
            register_delete_post_params : ['common_cart_ids', 'confirm'],
        },
        cart : {
            patch_delete_params : ['cartId'],
            path                : '/order-cart'
        },
        cropper: {
            dl_article_id      : 114,
            max_container_size : 600,
            viewMode           : 2,
            autoCropArea       : 0.95,
            move_crop_box : {
                align : {
                    top    : {x: 0,   y: -16},
                    bottom : {x: 0,   y: 16},
                    left   : {x: -16, y: 0},
                    right  : {x: 16,  y: 0},
                },
                keycode : {
                    '37': 'left',
                    '38': 'top',
                    '39': 'right',
                    '40': 'bottom',
                }
            }
        }
    });


})();
