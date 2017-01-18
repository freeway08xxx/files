// Declare app level module which depends on filters, and services
angular.module('wabisabid', [
	'ss.module.client-combobox',
	'wabisabid.controllers',
	'wabisabid.directives',
	'wabisabid.filters',
	'wabisabid.services'
]);

var app = angular.module('wabisabid');

app.config(['ssClientComboboxConfig', function(ssClientComboboxConfig) {
}]);

app.config(['$routeProvider', '$locationProvider', function($routeProvider) {
	/**
	 * AngularJSからの ng-view テンプレートリクエストとして、
	 * FuelPHP View::forge を呼ぶ。
	 * サーバデータをテンプレに展開する際はView::forgeにてデータを渡す。
	 */
	$routeProvider
	.when('/hagakure', {
		mainTab: 'hagakure',
		templateUrl: function(params) {
			return '/sem/new/wabisabid/hagakure.html';
		},
		reloadOnSearch: false,
		controller: 'HagakureCtrl',
	})
	.when('/multidevice', {
		mainTab: 'multidevice',
		templateUrl: function(params) {
			return '/sem/new/wabisabid/multidevice.html';
		},
		reloadOnSearch: false,
		controller: 'MultiDeviceCtrl',
	})
	.when('/separate', {
		mainTab: 'separate',
		templateUrl: function(params) {
			return '/sem/new/wabisabid/separate.html';
		},
		reloadOnSearch: false,
		controller: 'SeparateCtrl',
	})
	.otherwise({
		redirectTo: 'hagakure'
	});
}]);

app.config(['datepickerConfig', 'datepickerPopupConfig', function(datepickerConfig, datepickerPopupConfig) {
	datepickerConfig.showWeeks = false;
	datepickerConfig.yearRange = 10;
	datepickerConfig.dayTitleFormat = 'yyyy年 MMMM';
	datepickerConfig.formatYear = 'yyyy';
	datepickerPopupConfig.currentText = '本日';
	datepickerPopupConfig.clearText = 'クリア';
	datepickerPopupConfig.closeText = '閉じる';
}]);

app.run(['$location', '$rootScope', function($location, $rootScope) {
	$rootScope.$on('$routeChangeSuccess', function (event, current, previous) {
		if(current.$$route) {
			$rootScope.mainTab = current.$$route.mainTab;
		}
	});
}]);

app.constant('wabisabidConst', {
	pagination : {
		currentPage:  1,
		numPages   :  1,
		limit      :100,
		maxSize    :  5,
		offset     :  1
	},
});
