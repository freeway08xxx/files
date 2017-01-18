/* Controllers */
var ctrl = angular.module('client.mediacost.controllers', []);

/* 共通 */
ctrl.controller('clMediaCostCtrl',
['$scope', 'clMediaCostStore', 'clBaseService', 'clMediaCostService',
function($scope, clMediaCostStore, clBaseService, clMediaCostService) {

	var _this = this;

	_this.models = clMediaCostStore.models;
	_this.config = clMediaCostStore.config;

	_this.methods = clMediaCostService;

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


/* 新規追加 アカウントリスト */
ctrl.controller('clMediaCostAccountListCtrl',
['$scope', 'clMediaCostStore', 'clBaseService', 'clMediaCostService',
function($scope, clMediaCostStore, clBaseService, clMediaCostService) {

	var _this = this;

	_this.clientComboboxConfig = {
		registerApi: function (api) {
			clMediaCostService.comboboxApi = api;
		}
	};

	/**
	 * Controller init
	 */

	// クライアント変更時、新規登録フォームのアカウントリストに反映
	$scope.$watch('base.models.client.client.id', function (newval) {
		console.log('Account List Change');
		clMediaCostService.comboboxApi.setModels({client: newval});
	});
}]);