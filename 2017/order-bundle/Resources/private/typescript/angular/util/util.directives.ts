/// <reference path="../../typings/main.d.ts" />
(() => {
    'use strict';
    const app = angular.module('util.directives', [
        'ngResource',
    ]);

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




})();
