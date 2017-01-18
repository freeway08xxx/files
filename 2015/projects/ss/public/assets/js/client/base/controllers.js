/* Controllers */
var ctrl = angular.module('client.base.controllers', []);

/* 共通 */
ctrl.controller('clBaseCtrl',
['$scope', '$route', '$timeout', 'clBaseStore', 'clBaseService', 'ssUtils',
function($scope, $route, $timeout, clBaseStore, clBaseService, ssUtils) {

	var _this = this;

	_this.clientComboboxConfig = {
		registerApi: function (api) {
			clBaseService.comboboxApi = api;
		}
	};

	_this.models = clBaseStore.models;

	_this.methods = {
		chgLocation: clBaseService.chgLocation,
		reload     : clBaseService.reload,
	};


	$scope.$on('$routeChangeSuccess', function () {
		console.log('on route change');

		/**
		 * URLのクライアントIDをコンボボックスに反映する
		 */
		var params = ssUtils.getLocationParams();

		if (_.isEmpty(params.client_id)) return false;

		if (!_.isEmpty(_this.models.client.client)) {
			if (params.client_id === _this.models.client.client.id) return false;
		}

		console.log('setmodel');

		if (_.isEmpty(clBaseService.comboboxApi)) {
			$timeout(function () {
				clBaseService.comboboxApi.setModels({client: params.client_id});
			}, 1000);
		} else {
			clBaseService.comboboxApi.setModels({client: params.client_id});
		}
	});
}]);