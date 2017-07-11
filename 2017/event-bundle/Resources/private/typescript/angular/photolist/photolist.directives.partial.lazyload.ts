/// <reference path="../../typings/main.d.ts" />
(() => {

    const app = angular.module('photolist.directives.partial.lazyload', [
        'ngResource',
        'photolist.services',
        'photolist.directives'
    ]);

    app.directive('lazySrc', ['$window', '$document', function($window, $document){
        var doc = $document[0],
            body = doc.body,
            win = $window,
            $win = angular.element(win),
            uid = 0,
            elements = {};

        function getUid(el){
            var __uid = el.data("__uid");
            if (! __uid) {
                el.data("__uid", (__uid = '' + ++uid));
            }
            return __uid;
        }

        function getWindowOffset(){
            var t,
                pageYOffset = (typeof win.pageYOffset == 'number') ? win.pageYOffset : (((t = doc.documentElement) || (t = body.parentNode)) && typeof t.scrollTop == 'number' ? t : body).scrollTop;
            return {
                offsetY: pageYOffset
            };
        }

        function isVisible(iElement){
            var elem = iElement[0],
                elemRect = elem.getBoundingClientRect(),
                windowOffset = getWindowOffset(),
                winOffsetY = windowOffset.offsetY,
                elemHeight = elemRect.height || elem.height,
                elemOffsetY = elemRect.top + winOffsetY,
                viewHeight = Math.max(doc.documentElement.clientHeight, win.innerHeight || 0),
                yVisible;

            if(elemOffsetY <= winOffsetY){
                if(elemOffsetY + elemHeight >= winOffsetY){
                    yVisible = true;
                }
            }else if(elemOffsetY >= winOffsetY){
                if(elemOffsetY <= winOffsetY + viewHeight){
                    yVisible = true;
                }
            }

            return  yVisible;
        };

        function checkImage(){
            angular.forEach(elements, function(obj, key) {
                var iElement = obj['iElement'],
                    $scope = obj['$scope'];
                if(isVisible(iElement)){
                    iElement.attr('src', $scope.lazySrc);
                }
            });
        }

        $win.bind('scroll', checkImage);
        $win.bind('resize', checkImage);

        function onLoad(){
            var $el = angular.element(this),
                uid = getUid($el);

            $el.css('opacity', 1);

            if(elements.hasOwnProperty(uid)){
                delete elements[uid];
            }
        }

        return {
            restrict: 'A',
            scope: {
                lazySrc: '@',
            },
            link: function($scope, iElement){
                iElement.bind('load', onLoad);

                $scope.$watch('lazySrc', function(){
                    if(isVisible(iElement)){
                        iElement.attr('src', $scope.lazySrc);
                    }else{
                        var uid = getUid(iElement);
                        elements[uid] = {
                            iElement: iElement,
                            $scope: $scope
                        };
                    }
                });

                $scope.$on('$destroy', function(){
                    iElement.unbind('load');
                    var uid = getUid(iElement);
                    if(elements.hasOwnProperty(uid)){
                        delete elements[uid];
                    }
                });
            }
        };
    }]);
})();
