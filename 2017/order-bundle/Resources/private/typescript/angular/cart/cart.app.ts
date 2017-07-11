/// <reference path="../../typings/main.d.ts" />

(() => {
    const app   = angular.module('cart', [
        'ngResource',
        'ngTouch',
        'ngMessages',
        'util.services',
        'util.directives',
        'cart.services',
        'cart.directives',
        'cart.controllers',
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
            trimming_path       : '/order-trimming/common-carts/',
            patch_delete_params : ['cartId'],
            download_article_id : 114,
            paging : {
                max_list : 10
            },
            models : {
                max_amount : 20
            },
            image_edit : {
                logo            : {id : 1},
                default_caption : {id : 2},
                free_caption    : {id : 3}
            }
        },
        order  : {
            register_delete_post_params : ['common_cart_ids','confirm'],
        },
        photoset : {
            sdcd : {
                ids   : ['17','42','43'],
                image : '/img/common/photoset_sdcd.png'
            }
        }

    });
})();
