/// <reference path="../../typings/main.d.ts" />
(() => {
    'use strict';
    const app = angular.module('photolist.directives.partial.modal', [
            'ngResource',
            'photolist.services',
            'photolist.directives'
    ]);

    app.directive('showDetailWindow', [
        '$rootScope',
        '$timeout',
        'ViewStatusSharedService',
        'ItemDataSharedService',
        ($rootScope,$timeout, viewStatus, itemData) => {
            return {
                link    : ($scope, $elem, attr) => {
                    let zoomTarget = $('#zoom_image');
                    $elem.on({
                        click   : e => {
                            e.preventDefault();
                            zoomTarget.trigger('zoom.destroy');
                            itemData.currentPhotoDataIndex = attr.index - 0;
                            itemData.currentPhotoData   = itemData.photos[itemData.currentPhotoDataIndex];

                            let zoomElm = (itemData.currentPhotoData['logo_code'] == 'always') ? itemData.currentPhotoData.photo_url.logo_tn : itemData.currentPhotoData.photo_url.tn;
                            zoomTarget.zoom({ url : zoomElm });

                            $rootScope.$broadcast('modalAdjust');
                            $timeout(() =>{ viewStatus.hide.detailWindow = false; });
                            $scope.$apply();
                        }
                    });
                }
            };
        }
    ]);


    app.directive('hideDetailWindow', [
        '$rootScope',
        'ViewStatusSharedService',
        'ItemDataSharedService',
        ($rootScope, viewStatus,itemData) => {
        return {
            link    : ($scope, element, attr) => {
                element.on({
                    click   : e => {
                        if (typeof e === 'object') {
                            e.preventDefault();
                        }

                        viewStatus.hide.detailWindow   = true;
                        $scope.$apply()
                    }
                });
            }
        };
    }]);


    app.directive('showNext', ['ItemDataSharedService', '$window', (itemData, $window) => {
        return {
            link    : function($scope, $elem) {
                let zoomTarget  = $('#zoom_image');
                let endClass    = 'end';
                let showNext:any = e =>{
                    if (typeof e !== 'undefined') {
                        e.preventDefault();
                    }

                    let currentIndex = itemData.currentPhotoDataIndex;
                    let nextIndex = currentIndex + 1;
                    if (nextIndex >= itemData.photoCount) {
                        return;
                    }

                    $elem.removeClass(endClass);
                    zoomTarget.trigger('zoom.destroy');
                    itemData.currentPhotoData   = itemData.photos[nextIndex];
                    itemData.currentPhotoDataIndex++;

                    let zoomElm = (itemData.currentPhotoData['logo_code'] == 'always') ? itemData.currentPhotoData.photo_url.logo_tn : itemData.currentPhotoData.photo_url.tn;
                    zoomTarget.zoom({ url : zoomElm });

                    $scope.$apply();
                };

                let addKeyEvent = e =>{
                    if (e.keyCode != 39) {  // KeyCode 39 ... カーソルキー(右)
                        return;
                    }
                    if ($scope.root.viewStatus.hide.detailWindow) { // 詳細モーダルが非表示のときは何もしない
                        return;
                    }
                    showNext();
                };
                angular.element($window).on('keyup', addKeyEvent);
                $elem.on({
                    click   : showNext
                });

                $scope.$on('$destroy', () =>{
                    $elem.off('click', showNext);
                    angular.element($window).off('keyup', addKeyEvent);
                    zoomTarget.trigger('zoom.destroy');
                });

            }
        };
    }]);

    app.directive('showPrev', ['ItemDataSharedService', '$window', (itemData, $window) =>{
        return {
            link    : function($scope, $elem) {
                let zoomTarget  = $('#zoom_image');
                let endClass    = 'end';
                let showPrev:any= (e) =>{
                    if (typeof e !== 'undefined') {
                        e.preventDefault();
                    }

                    let currentIndex    = itemData.currentPhotoDataIndex;
                    if (currentIndex == 0) {
                        return;
                    }

                    let nextIndex = currentIndex - 1;
                    zoomTarget.trigger('zoom.destroy');
                    itemData.currentPhotoData   = itemData.photos[nextIndex];
                    itemData.currentPhotoDataIndex--;

                    let zoomElm = (itemData.currentPhotoData['logo_code'] == 'always') ? itemData.currentPhotoData.photo_url.logo_tn : itemData.currentPhotoData.photo_url.tn;
                    zoomTarget.zoom({ url : zoomElm });


                    $elem.removeClass(endClass);
                    $scope.$apply();
                };

                let addKeyEvent = (e) =>{
                    if (e.keyCode != 37) {  // KeyCode 37 ... カーソルキー(左)
                        return;
                    }

                    if ($scope.root.viewStatus.hide.detailWindow) { // 詳細モーダルが非表示のときは何もしない
                        return;
                    }

                    showPrev();
                };

                angular.element($window).on('keyup', addKeyEvent);
                $elem.on({
                    click   : showPrev
                });

                $scope.$on('$destroy', () =>{
                    $elem.off('click',showPrev);
                    angular.element($window).off('keyup', addKeyEvent);
                    zoomTarget.trigger('zoom.destroy');
                });
            }
        };
    }]);
})();

