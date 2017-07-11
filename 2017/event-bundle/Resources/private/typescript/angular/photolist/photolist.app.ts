/// <reference path="../../typings/main.d.ts" />
(() => {
    const app   = angular.module('photolist', [
        'ngResource',
        'ngTouch',
        'photolist.services',
        'photolist.directives',
        'photolist.directives.partial.lazyload',
        'photolist.directives.partial.modal',
        'photolist.directives.partial.slick',
        'photolist.controllers'
    ]);

    app.config(($interpolateProvider) => {
        // Twigとバッティングするので調整
        $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
    });

    app.constant('pageData', {
        params  : () => {

            let base  = {
                member_id         : PARAMS['member_id'],
                not_member_hash   : PARAMS['not_member_hash'],
                page_id           : PARAMS['page_id'],
                event_id          : PARAMS['event_id'],
                code              : PARAMS['code'],
                store_id          : PARAMS['store_id'],
                tag_code          : '',
                tag               : '',
            };

            let getUrlparams  = () =>{
                let urlParams  = {};
                let pair       = location.search.substring(1).split('&');
                for(let i=0; pair[i]; i++) {
                    let keyVal = pair[i].split('=');
                    urlParams[keyVal[0]] = keyVal[1];
                }
                return urlParams;
            };

            return _.extend(base, getUrlparams());
        }
    });

    app.constant('pageConst', {
        without_article_id  : 108,
        cartPostAmount      : 1,
        photosetPostPhotoId : null,
        devPrefix           : '/app_dev.php',
        captionLogoCode     : 1
    });


})();
