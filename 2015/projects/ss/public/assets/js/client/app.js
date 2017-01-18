// Declare app level module which depends on filters, and services
angular.module('client', [
		'client.base.services',
		'client.base.controllers',
		'client.conversion.services',
		'client.conversion.controllers',
		'client.conversion.filters',
		'client.conversion.directives',
		'client.mediacost.services',
		'client.mediacost.controllers',
		'client.mediacost.filters',
		'client.mediacost.directives',
		'ss.module.client-combobox'
]);

var app = angular.module('client');

app.constant('clMsg', {
	mediacost: {
		update : '媒体費設定を更新しました。',
		deleted: '媒体費設定を削除しました。',
		error  : '処理中にエラーが発生しました。',
		invalid: '媒体費が数値ではないか、必須項目が未入力です。'
	}
});

app.config(['$routeProvider', '$locationProvider',
	function($routeProvider) {
		/**
		 * AngularJSからの ng-view テンプレートリクエストとして、
		 * FuelPHP View::forge を呼ぶ。
		 * サーバデータをテンプレに展開する際はView::forgeにてデータを渡す。
		 */
		$routeProvider
		.when('/cv/index', {
			mainTab: 'cv',
			templateUrl: function() {
				return '/sem/new/client/conversion/index.html';
			},
			// reloadOnSearch: false,
			controller: 'clCvCtrl',
			controllerAs: 'cv'
		})
		.when('/cv/list', {
			mainTab: 'cv',
			templateUrl: function(params) {
				return '/sem/new/client/conversion/list.html?client_id=' + params.client_id;
			},
			// reloadOnSearch: false,
			controller: 'clCvCtrl',
			controllerAs: 'cv'
		})
		.when('/mediacost/index', {
			mainTab: 'mediacost',
			templateUrl: function() {
				return '/sem/new/client/mediacost/index.html';
			},
			// reloadOnSearch: false,
			controller: 'clMediaCostCtrl',
			controllerAs: 'mediacost'
		})
		.when('/mediacost/detail', {
			mainTab: 'mediacost',
			templateUrl: function(params) {
				return '/sem/new/client/mediacost/detail.html?client_id=' + params.client_id;
			},
			// reloadOnSearch: false,
			controller: 'clMediaCostCtrl',
			controllerAs: 'mediacost'
		})
		.otherwise({
			redirectTo: '/cv/index'
		});
	}
]);

app.config(["ssClientComboboxConfig", function(ssClientComboboxConfig) {
	ssClientComboboxConfig.account.isView = true;
}]);

app.run(['$location', '$rootScope', function($location, $rootScope) {
	$rootScope.$on('$routeChangeSuccess', function (event, current, previous) {
		if(current.$$route){
			$rootScope.mainTab = current.$$route.mainTab;
		}
	});
}]);

