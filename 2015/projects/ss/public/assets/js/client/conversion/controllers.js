/* Controllers */
var ctrl = angular.module('client.conversion.controllers', []);

/* 共通 */
ctrl.controller('clCvCtrl',
['$scope', 'clBaseService', 'clCvService',
function($scope, clBaseService, clCvService) {

	var _this = this;

	_this.methods = {
		submit: clCvService.submit
	};

	/**
	 * Controller init
	 */
	clBaseService.setActiveTab($scope.mainTab);

	/**
	 * クライアント変更時、詳細設定画面を再読み込み
	 * ※ Controller init 時にも判定したいので 各 Child Controllers に記述
	 */
	$scope.$watch('base.models.client.client', clBaseService.locateToDetailWhenClientChangeWatched(), true);
}]);
