/// <reference path="../../typings/main.d.ts" />
(() => {
    'use strict';
    const app = angular.module('trimming.directives', [
            'util.services',
            'util.directives',
            'trimming.controllers',
            'trimming.services'
    ]);

    app.directive('imageLoaded',[
        'TrimmingDataSharedService',
        'ItemDataSharedService',
        'ModelSharedService',
        '$window',
        'ContainerSize',
        'pageConst',
        'ViewStatusSharedService',
        'utils',
        'MessageSharedService',
        '$rootScope',
        function(trimmingData, items, models, $window, containerSize, pageConst, viewStatus, utils, message, $rootScope) {
        return {
            restrict: 'A',
            link: function(scope, element) {
                $window.Cropper.DEFAULTS['checkCrossOrigin'] = false;

                //article_idの変更
                scope.setNewArticle = function(){
                    let cropper_image     = document.querySelector('.cropper-image');
                    let cropper           = scope.cropper;
                    let data              = cropper.getData();
                    let current           = utils.findByObj(items.common_cart.photo.pricelist_details, {article_id : Number(models.article_id)});
                    if(current[0].article_id != pageConst.cropper.dl_article_id) viewStatus.disabled.register = false;
                    items.current_article = current[0];
                    cropper.options['minCropBoxWidth']   = items.current_article.article.minimum_pixel;
                    cropper.options['minCropBoxHeight']  = items.current_article.article.minimum_pixel;
                    cropper.options['aspectRatio']       = items.current_article.article.aspect;
                    cropper.destroy();
                    scope.cropper = new $window.Cropper(cropper_image,cropper.options);
                    scope.cropper.setData(data);
                };


                //クロップボックスの移動
                scope.moveCropBox = function(align = null){
                    if(align == null) return false;
                    let map = pageConst.cropper.move_crop_box;
                    let values = {x : 0, y : 0};

                    align   = map.align[align];
                    values.x = (viewStatus.show.small_range) ? align.x / 2: align.x;
                    values.y = (viewStatus.show.small_range) ? align.y / 2: align.y;
                    let cropper = scope.cropper;
                    let data    = cropper.getData();
                    data.x     += values.x;
                    data.y     += values.y;
                    cropper.setData(data);
                };


                //画像が表示されたらcropperを起動
                element.bind('load', function() {
                    let img = new Image();
                    img.src = $('.cropper-image').attr('src');
                    $('.cropper-image-container').css(containerSize(img));

                    let cropper_image  = document.querySelector('.cropper-image');
                    scope.cropper  = new $window.Cropper(cropper_image, {
                        background       : false,
                        zoomable         : false,
                        scalable         : false,
                        movable          : false,
                        viewMode         : pageConst.cropper.viewMode,
                        autoCropArea     : pageConst.cropper.autoCropArea,
                        minCropBoxWidth  : items.current_article.article.minimum_pixel,
                        minCropBoxHeight : items.current_article.article.minimum_pixel,
                        aspectRatio      : items.current_article.article.aspect,
                        crop : (data) => {
                            trimmingData.TrimmingWidth  = Math.round(data['width']);
                            trimmingData.TrimmingHeight = Math.round(data['height']);
                            let trim_data = adjustCoordinate(data);
                            trimmingData.TrimmingX      = Math.round(trim_data['x']);
                            trimmingData.TrimmingY      = Math.round(trim_data['y']);
                            scope.$apply();
                        }
                    });

                    //ライブラリデータは左端が基準なのでAPI送信用に中央基準に最適化
                    function adjustCoordinate(data){
                        let res             = {x:0,y:0};
                        let x_coordinate    = scope.cropper.getImageData().naturalWidth / 2;
                        let y_coordinate    = scope.cropper.getImageData().naturalHeight / 2;
                        let half_crop_box_x = data['width'] / 2;
                        let half_crop_box_y = data['height'] / 2;
                        res.x               = (data['x'] + half_crop_box_x) - x_coordinate;
                        res.y               = (data['y'] + half_crop_box_y) - y_coordinate;

                        return res;
                    }


                    if(items.current_article.article.aspect == null){
                        scope.cropper.clear();
                        $rootScope.$broadcast('errorToast', message.failed.cropImage);
                        return false
                    }

                });
            }
        };
    }]);

    //大小ボタン
    app.directive('zoomCropBox', [
        'ViewStatusSharedService',
        function(viewStatus) {
            return {
                scope : {
                    cropper: '=',
                },
                link : (scope, element, attrs) =>{
                    element.on({
                        click   : e => {
                            e.preventDefault();
                            let zoom = JSON.parse(attrs.zoomCropBox);

                            if(viewStatus.show.small_range) {
                                zoom.w = zoom.w / 2;
                                zoom.h = zoom.h / 2;
                            }

                            let data        = scope.$parent.cropper.getData();
                            data['width']  += Number(zoom.w);
                            data['height'] += Number(zoom.h);
                            data['x']      -= Number(zoom.w / 2);
                            data['y']      -= Number(zoom.h / 2) / scope.$parent.cropper.getImageData().aspectRatio;

                            scope.$parent.cropper.setData(data);

                        }
                    });
                }
            };
        }]);


    //回転ボタン
    app.directive('reverseCropBox',[
        'ItemDataSharedService',
        'pageConst',
        '$window',
        function(items, pageConst, $window) {
            return {
                scope : {
                    cropper: '=',
                },
                link : (scope, element) =>{
                    element.on({
                        click   : e => {
                            e.preventDefault();
                            let cropper_image  = document.querySelector('.cropper-image');
                            let cropper        = scope.$parent.cropper;
                            let data           = cropper.getData();
                            let long           = items.current_article.article.aspect_long;
                            let short          = items.current_article.article.aspect_short;

                            cropper.options["aspectRatio"] = (data.width > data.height) ? short/ long : long / short;
                            cropper.destroy();

                            scope.$parent.cropper = new $window.Cropper(cropper_image,cropper.options);
                            scope.$parent.cropper.setData(data);
                        }
                    });
                }
            };
        }]);

    //カーソル操作移動
    app.directive('moveCropBox',[
        function() {
            return {
                scope : {
                    cropper: '=',
                },
                link : (scope, element, attrs) =>{

                    element.on({
                        click   : e => {
                            e.preventDefault();
                            scope.$parent.moveCropBox(attrs.moveCropBox);
                        }
                    });
                }
            };
        }]);

    //key操作移動
    app.directive('moveCropboxByKeypress',[
        'pageConst',
        function(pageConst) {
            return {
                scope : {
                    cropper: '=',
                },
                link : (scope) =>{
                    $('body').on('keydown', function (e) {
                        e.preventDefault();
                        let keycode = (e.which).toString();
                        scope.$parent.moveCropBox(pageConst.cropper.move_crop_box.keycode[keycode]);
                    });
                }
            };
        }]);


    app.directive('trimmingRegister',[
        'TrimmingDataSharedService',
        'ModelSharedService',
        'pageConst',
        'utils',
        'CartEditor',
        'MessageSharedService',
        '$rootScope',
        function(trimmingData, models, pageConst, utils, editor, message, $rootScope) {
            return {
                scope : {
                    cropper: '=',
                },
                link : (scope, element) =>{

                    element.on({
                        click   : e => {
                            e.preventDefault();
                            trimmingData['article_id'] = models.article_id;
                            editor.patch(trimmingData, function(res) {
                                    //console.log(res);
                                    location.href = utils.getUrlPrefix() + pageConst.cart.path
                            },
                            function(error) {
                               $rootScope.$broadcast('errorToast', message.failed.register);
                            });

                        }
                    });
                }
            };
        }]);

})();

