/// <reference path="../../typings/main.d.ts" />
(() => {
    'use strict';

    const app = angular.module('photolist.directives.partial.slick', [
            'ngResource',
            'photolist.services',
            'photolist.directives'
    ]);

    app.directive('showSlickInDetailWindow', [
            '$rootScope',
            'ViewStatusSharedService',
            'ItemDataSharedService',
        function($rootScope, viewStatus, itemData) {
            return {
                link    : function($scope, $elem, attr) {
                    // スワイプ処理の開始
                    let beginSwipe = index =>{
                        $swipeTarget.on({
                            afterChange : function(event, slick, currentSlide) {
                                let current = currentSlide - 0;
                                itemData.currentPhotoData   = itemData.photos[current];
                                $scope.$apply();
                            }
                        });

                        $swipeTarget.slick({
                            infinite        : false,
                            initialSlide    : index
                        });
                    };

                    $elem.on({
                        click   : function(e) {
                            e.preventDefault();

                            let index   = attr.index - 0;
                            itemData.currentPhotoData       = itemData.photos[index];
                            viewStatus.hide.detailWindow    = false;
                            $scope.$apply();

                            let width   = $photoBoxes.width();
                            $photoBoxes.css({ height : width + 'px' });
                            $rootScope.$broadcast('modalAdjust');
                            beginSwipe(index);
                        }
                    });

                    $scope.$on('$destroy', () =>{
                        $swipeTarget.unslick();
                    });

                    let $photoBoxes  = $('.device_sp .cont_left_photo');
                    let $swipeTarget = $(attr.swipeTarget);
                }
            };
        }
    ]);


    app.directive('swipeImage', [()=>{
        return {
            scope   : {
                photo   : '='
            },
            link    : function($scope, $elem, attr) {
                if ($scope.photo.verticalFlag) {
                    $elem.css({ height : '100%' });
                } else {
                    $elem.css({ width : '100%' });
                }
            }
        };
    }]);

    app.directive('hideSlickInDetailWindow', ['ViewStatusSharedService', function(viewStatus) {
        return {
            link    : function($scope, element, attr) {
                element.on({
                    click   : function(e) {
                        if (typeof e === 'object') {
                            e.preventDefault();
                        }

                        viewStatus.hide.detailWindow  = true;
                        $scope.$apply()
                        swipeTarget.slick('unslick');
                    }
                });

                let swipeTarget = $('.cont_left_wrap');
            }
        };
    }]);


    app.directive('slickHideZoomButton', ['ViewStatusSharedService', function(viewStatus) {
        return {
            link    : function(scope, elem, attr) {
                elem.on({
                    click   : function(e) {
                        e.preventDefault();
                        viewStatus.hide.zoomWindow = true;
                        scope.$apply();
                    }
                });
            }
        };
    }]);




    app.directive('slickZoomButton', ['$rootScope', 'ViewStatusSharedService', function($rootScope, viewStatus){
        return {
            scoope  : {
                photo   : '='
            },
            link    : function(scope, elem, attr) {
                elem.on({
                    click   : function(e) {
                        if (typeof e != 'undefined') { e.preventDefault(); }
                        let zoomImage   = (scope.photo['logo_code'] == 'always') ? scope.photo.photo_url.logo_tn : scope.photo.photo_url.tn;
                        $rootScope.$broadcast('setZoomImage', zoomImage);
                        viewStatus.hide.zoomWindow   = false;
                        scope.$apply();
                    }
                });
            }
        };
    }]);


    app.directive('iframeResizer', ['$rootScope', '$window', function($rootScope, $window){
        return {
            link    : function(scope, elem, attr){
                scope.$on('setZoomImage', function(event, imgSrc){
                    if (elem[0].contentWindow) {
                        let iframeImage = elem[0].contentWindow.document.getElementById('detail_image');
                        iframeImage.src = '/view/img/transparent.gif';
                        iframeImage.src = imgSrc;
                    }
                });

                angular.element($window).on({
                    'load resize' : function(e){
                        let html    = angular.element(document).find('html');
                        let width   = html[0].clientWidth;
                        let height  = html[0].clientHeight;

                        let size    = width;
                        if(width > height){
                            size    = height - 80;
                        }

                        elem.width(size);
                        elem.height(size);

                        let leftHeight  = height - size;
                        let leftWidth   = width - size;
                        let topPx       = leftHeight/2;
                        let leftPx      = leftWidth/2;
                        elem.parent().css('top', topPx + 'px');
                        elem.parent().css('left', leftPx + 'px');
                    }
                });
            }
        };
    }]);

    app.directive('lazyLoadArea', ['$rootScope', 'ImageResumeService', 'ViewStatusSharedService', '$window',
        function($rootScope, resumeService, viewStatus, $window){
            return {
                link    : function(scope, elem, attr){
                    angular.element($window).on({
                        'scroll' : function(e){
                            if (!viewStatus.hide.detailWindow) { return; } // 詳細画面が表示されている時は発動させない

                            let bodyHeight  = document.documentElement.scrollHeight;
                            if (bodyHeight < 200) { return; }

                            let clientHeight    = document.documentElement.clientHeight || document.documentElement.offsetHeight;
                            let scroll          = document.documentElement.scrollTop || document.body.scrollTop;
                            let currentPosition = scroll + clientHeight;
                            let limit           = bodyHeight - 800;
                            if (currentPosition >= limit) {
                                resumeService.resume();
                                scope.$apply();
                            }
                        }
                    })
                }
            };
        }
    ]);

    app.directive('switchImageSize', ['ViewStatusSharedService', function(viewStatus) {
        return {
            link    : function($scope, $elem, attr) {
                let size        = attr.size,
                    $tabs       = $('.photo_size_tab a');

                let adjustSize  = function() {
                    var w   = $('.photo_list li').width();
                    $('.photo_list li p').css({ height : w + 'px' });
                };

                let switchSize  = (e) =>{
                    e.preventDefault(e);

                    $tabs.removeClass('on');
                    $elem.addClass('on');

                    if (size == 'big') {
                        viewStatus.hide.squareList  = true;
                        $('.photo_list').removeClass('small');
                        adjustSize();
                    } else {
                        viewStatus.hide.squareList  = false;
                        $('.photo_list').addClass('small');
                        adjustSize();
                    }

                    $scope.$apply();
                };

                $elem.on({ click : switchSize });
                $scope.$on('$destroy', () =>{
                    $elem.off('click', switchSize);
                });
            }
        };
    }]);
})();

