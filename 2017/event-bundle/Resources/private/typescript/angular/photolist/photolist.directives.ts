/// <reference path="../../typings/main.d.ts" />
(() => {
    'use strict';
    const app = angular.module('photolist.directives', [
            'ngResource',
            'photolist.controllers',
            'photolist.services'
    ]);

    app.directive('cartButton', ['ViewStatusSharedService', (viewStatus)=> {
        return {
            restrict    : 'EA',
            replace     : true,
            templateUrl : 'cart-button.html',
            controller  : $scope => {
                $scope.showCartWindow = e =>{
                    e.preventDefault();
                    viewStatus.hide.cartWindow  = false;
                };
            }
        };
    }]);

    app.directive('photoList', [()=>{
        return {
            restrict    : 'EA',
            replace     : true,
            templateUrl : 'photo-list.html',
        };
    }]);

    app.directive('photosetList', [()=> {
        return {
            restrict    : 'EA',
            replace     : true,
            templateUrl : 'photoset-list.html',
        };
    }]);

    app.directive('zoomWindow', [()=>{
        return {
            restrict    : 'EA',
            replace     : true,
            templateUrl : 'zoom-window.html'
        };
    }]);

    app.directive('initDom', [()=>{
        return {
            compile    : function() {
                $(".btn_cart_tab, .modal_overlay, .cart_modal, #photo_detail_modal,.complete_modal").show();
            }
        };
    }]);

    app.directive('deleteFromCart', [
            'CartDataRemover',
            'ItemDataSharedService',
            'MessageSharedService',
        (remover, itemData, message) => {
            return {
                scope   : {
                    cart    : '='
                },
                link    : function($scope, element, attr) {
                    let elem    = angular.element(element[0]);
                    elem.bind({
                        click   : function(e) {
                            e.preventDefault();
                            $scope.cart.deleted = true;
                            remover.removeByCartId($scope.cart.common_cart_id, $scope.cart.photo_id);
                            $scope.$emit('progressToast', message.progress.deletingCart);
                            $scope.$apply();
                        }
                    });
                }
            };
        }
    ]);

    // 写真サムネイル一覧のstyle調整
    app.directive('resizeListImage', ['$window', ($window) => {
        return {
            link    : function($scope, elem, attr) {
                let reSize:any  = e =>{
                    if (typeof elem !== 'object' || !elem) { return; }

                    let width   = elem.width();
                    p.css({ height : width + "px"});
                };

                let cleanUp = () =>{
                    elem    = null;
                    angular.element($window).off('resize', reSize());
                };

                if ($scope.$last) {
                    // 一覧を並べ終わった時に実行
                    let width   = elem.width();
                    $( ".photo_list li p").css({ height : width + 'px' });
                }

                let p   = elem.find('p');
                angular.element($window).on('resize', reSize());
                $scope.$on('$destroy', cleanUp());
            }
        };
    }]);

    app.directive('cartDataSwitcher', [
            'CartData',
            'ItemDataSharedService',
            'ViewStatusSharedService',
            'CartParams',
            'CartCreator',
            'CartMapping',
            'CartDataRemover',
            'PhotoFinder',
            '$window',
            'MessageSharedService',
            'pageConst',
        (cartData, itemData, viewStatus,params, creator, mapping, remover, photoFinder, $window, message, pageConst)=> {
            return {
                scope   : {
                    item    : '='
                },
                link    : ($scope, element, attr)=> {
                    let elem    = angular.element(element[0]);
                    elem.bind({
                        click   : e =>{
                            if (typeof e !== 'undefined') {
                                e.preventDefault();
                            }
                            let photo   = $scope.item;

                            if(photo.is_processing) return false;
                            photo.is_processing = true;

                            if (photo.inCart) {
                                if (mapping.getCount(photo.id) > 1) {
                                    if (!$window.confirm(message.confirm)) {
                                        return;
                                    }
                                }
                                photo.inCart = false;
                                photo.is_processing = false;
                                remover.removeByPhotoId(photo.id);
                                $scope.$emit('progressToast', message.progress.deletingCart);
                            } else {
                                photo.inCart  = true;
                                $scope.$emit('progressToast', message.progress.addingCart);

                                let add_params = (photo.logo_code == 'always') ? {caption : pageConst.captionLogoCode} : null;
                                let cartParams = params.build(photo.id, add_params);

                                cartData.save(cartParams, function(res) {
                                    photo.is_processing = false;
                                    let newCartItem = creator.build(photo, res.id);
                                    itemData.carts.push(newCartItem);
                                    mapping.add(photo.id);
                                    $scope.$emit('successToast', message.success.addingCart);
                                },
                                function(error) {
                                    photo.inCart  = false;
                                    photo.is_processing = false;
                                    $scope.$emit('errorToast', message.failed.addingCart);
                                });

                            }
                        }
                    });
                }
            };
        }
    ]);

    app.directive('closeCartWindow', ['ViewStatusSharedService', (viewStatus) =>{
        return {
            link    : ($scope, element, attr)=> {
                element.on({
                    click   : e => {
                        if (typeof e === 'object') {
                            e.preventDefault();
                        }

                        viewStatus.hide.cartWindow  = true;
                        $scope.$apply()
                    }
                });
            }
        };
    }]);

    app.directive('modalAdjuster', ['$window', ($window) =>{
        return {
            link    : function($scope, elem, attr) {
                $scope.$on('modalAdjust', (event, args) =>{
                    let h   = $window.pageYOffset;
                    elem.css('top', h+'px');
                });
            }
        };
    }]);

    app.directive('timeFormat', ['$window', ($window) =>{
        return {
            link    : function($scope, elem, attr) {
                $scope.$on('modalAdjust', (event, args) =>{
                    let h   = $window.pageYOffset;
                    elem.css('top', h+'px');
                });
            }
        };
    }]);

    app.directive('toastMessage', ['$timeout', ($timeout)=> {
        return {
            link    : ($scope, $elem)=> {
                $scope.$on('successToast', (event, message) =>{
                    if ($elem.hasClass('toast-red')) {
                        $elem.removeClass('toast-red');
                    }

                    $elem.addClass('toast-green');
                    $elem.text(message);
                    $elem.show();
                    $timeout(function() {
                        $elem.fadeOut();
                    }, 3000);
                });

                $scope.$on('errorToast', (event, message) =>{
                    if ($elem.hasClass('toast-green')) {
                        $elem.removeClass('toast-green');
                    }

                    $elem.addClass('toast-red');
                    $elem.text(message);
                    $elem.show();
                    $timeout(()  =>{
                        $elem.fadeOut();
                    }, 3000);
                });

                $scope.$on('progressToast', (event, message) =>{
                    if ($elem.hasClass('toast-red')) {
                        $elem.removeClass('toast-red');
                    }

                    $elem.addClass('toast-green');
                    $elem.text(message);
                    $elem.show();
                });
            }
        };
    }]);

    app.directive('setPhotosetsUrl', [
        'pageData',
        'utils',
        (pageData,utils) => {
            return {
                replace   : true,
                link   : (scope,element, attr)=> {
                    let devMode     = utils.getUrlPrefix();
                    let pageData    = scope.root.pageData;
                    let event_id    = pageData.event_id;
                    let page_id     = pageData.page_id;
                    let tag_code    = (pageData.tag_code == '') ? 'zekken'          : pageData.tag_code;
                    let tag         = (pageData.tag      == '') ? pageData.album_id : pageData.tag;
                    let link = '//' + location.hostname  + devMode + '/photo-list/' + 'events/' + event_id + '/pages/' + page_id + '/photosets/' + attr.code + '?' + 'tag_code=' + tag_code + '&tag=' + tag;
                    let html = '<a href="'+ link +'">選択する</a>';
                    element.html(html);
                }
            };
        }]);

    app.directive('setSponsoredUrl', [
        'pageData',
        'utils',
        (pageData,utils) => {
            return {
                replace   : true,
                link   : (scope,element, attr)=> {
                    let devMode     = utils.getUrlPrefix();
                    let pageData    = scope.root.pageData;
                    let event_id    = pageData.event_id;
                    let page_id     = pageData.page_id;
                    let link = '//' + location.hostname  + devMode + '/sponsored-print/' + 'events/' + event_id + '/pages/' + page_id + '/photos/';
                    scope.sponsoredlink  = link;
                }
            };
        }]);


    app.directive('setAlbumsUrl', [() => {
            return {
                link   : (scope,element, attr)=> {
                    let url     = '//' + location.host + location.pathname + '?album_id=' + attr.albumId;
                    let current = scope.root.isCurrent(attr.albumId) ? 'current': '';
                    let html    = '<a class="'+ current +'" href="'+ url +'">' + attr.albumName +'</a>';
                    element.html(html);
                }
            };
        }]);

    app.directive('validateCheckbox', [
        'FormValidateSharedService',
        'ItemDataSharedService',
        'FormDataSharedService',
        (validate,itemData,formData) => {
            return {
                link    : (scope, elem)=> {

                    elem.on({
                        change   : e => {
                            let checked  = $(elem).prop('checked');
                            let photo_id = $(elem).prop('value');
                            formData.model[photo_id] = checked;

                            let type                    = itemData.photoset_selecting;
                            formData.complete           = validate.checkBox(_.values(formData.model));
                            formData.selected_ids[type] = validate.setCheckboxData(formData.model);
                            scope.$apply();
                        }
                    });
                }
            };
        }]);

    app.directive('loaded',[
        'ViewStatusSharedService',
        'FormDataSharedService',
        (viewStatus,formData)=>{
        return (scope) => {
            if (scope.$last){
                formData.setModels();
                viewStatus.hide.photosetTitle = false;
            }
        }
    }]);


    app.directive('postPhotosetForm', [
        'CartData',
        'MessageSharedService',
        'pageConst',
        'CartParams',
        'ViewStatusSharedService',
        'ItemDataSharedService',
        'FormDataSharedService',
        'FormValidateSharedService',
        'pageData',
        (cartData,message,pageConst,params,viewStatus,itemData,formData,validate,pageData) => {
            return {
                link    : (scope, elem) => {
                    elem.on({
                        click   : e => {
                            itemData.donePhotosetSelectFlgs[itemData.photoset_selecting] = true;

                            //プリント選択が残っている場合 modelを初期化して選択画面へ
                            if(_.values(itemData.donePhotosetSelectFlgs).indexOf(false) > 0){
                                $('html,body').scrollTop(0);
                                itemData.photoset_selecting  = 'print';
                                formData.setModels();
                                scope.$apply();
                                return false;
                            }

                            //パラメータ生成
                            let data        = formData.selected_ids.data;
                            let prtint      = formData.selected_ids.print;
                            let print_data  = (prtint == '') ? '' : ':' + prtint.join(',');
                            let tag         = pageData.params().tag;
                            let alldata_str = tag + ':'+ data.join(',') + print_data;
                            let cartParams  = params.build(pageConst.photosetPostPhotoId, {alldata_str : alldata_str});

                            if(typeof tag == 'undefined' || tag == '') {
                                _error();
                                return false;
                            }

                            cartData.save(cartParams, (res) => {
                                viewStatus.hide.completeWindow = false;
                                return false;
                            },
                            function(error) {
                                _error()
                            });

                            function _error(){
                                scope.$emit('errorToast', message.failed.addingCart);
                            }
                        }
                    });
                }
            };
        }]);

    app.directive('showPhotosetModalButton', [
        'ViewStatusSharedService',
        (viewStatus) => {
        return {
            link    : function(scope, elem, attr) {
                elem.on({
                    click   : function(e) {
                        e.preventDefault();

                        let scrollHeight    = $(window).scrollTop();
                        let top  = 50 + scrollHeight;
                        $("div.set_product_window").css('top', top +'px');

                        viewStatus.hide.photoset        = false;
                        viewStatus.hide.photosetOverlay = false;
                        scope.$apply();
                    }
                });
            }
        };
    }]);

    app.directive('hidePhotosetModalButton', ['ViewStatusSharedService', (viewStatus) =>{
        return {
            link    : ($scope, element, attr)=> {
                element.on({
                    click   : e => {
                        e.preventDefault();
                        viewStatus.hide.completeWindow = true;
                        $scope.$apply()
                    }
                });
            }
        };
    }]);



    app.directive('showAlbumButton', ['ViewStatusSharedService', (viewStatus) =>{
        return {
            link    : ($scope, element, attr)=> {
                element.on({
                    click   : e => {
                        viewStatus.hide.cartWindow  = true;
                        $scope.$apply()
                    }
                });
            }
        };
    }]);

    app.directive('hideSdcdButton', ['ViewStatusSharedService', (ViewStatusSharedService) =>{
        return {
            link    : function(scope, elem, attr) {
                elem.on({
                    click   : function(e) {
                        e.preventDefault();
                        ViewStatusSharedService.hide.photoset    = true;
                        ViewStatusSharedService.hide.photosetOverlay = true;
                        scope.$apply();
                    }
                });
            }
        };
    }]);

    // 右クリック禁止処理
    app.directive('disableRightClick', ['MessageSharedService', '$window', (message, $window)=> {
        return {
            link : ($scope, $elem, attr) =>{
                let showDisableMessage  = () =>{
                    alert(message.disableRightClick);
                };
                angular.element($window).on('contextmenu', () =>{
                    showDisableMessage();
                    return false;
                });
                $scope.$on('$destroy', ()=> {
                    angular.element($window).off('contextmenu', showDisableMessage);
                });
            }
        };
    }]);

    //画像ドラッグ禁止処理
    app.directive('disableDrag', [() =>{
        return  {
            link : ($scope, $elem, attr) =>{
                $elem.on({
                    dragstart :() =>{
                        return false
                    }
                });
            }
        };
    }]);

    app.directive('historyBack', ['$window', ($window) =>{
        return {
            link    : (scope, $elem, attr) =>{
                $elem.on({
                    click   : (e) =>{
                        if (!_.isEmpty(e)) {
                            e.preventDefault();
                        }

                        $window.history.back();
                    }
                });
            }
        };
    }]);
})();

