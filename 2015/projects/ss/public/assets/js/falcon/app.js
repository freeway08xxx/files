// Declare app level module which depends on filters, and services
angular.module('falcon', [
		'falcon.services',
		'falcon.controllers',
		'falcon.filters',
		'falcon.directives',
		'ss.module.client-combobox',
		'ss.module.termdate',
		'ngAnimate'
]);

var app = angular.module('falcon');

app.constant('falconBsName', {
	init           : 'init',
	initReport     : 'initReport',
	initEditMode   : 'initEditMode',
	chgClient      : 'chgClient',
	chgReportTab   : 'chgReportTab',
	chgReportType  : 'chgReportType',
	chgSummaryType : 'chgSummaryType',
	chgDeviceType  : 'chgDeviceType',
	applyTemplate  : 'applyTemplate',
	createReport   : 'createReport',
	updateCpSetting: 'updateCpSetting'
});

app.constant('falconTab', [
	{id: 'format',  name: 'レポート形式'},
	{id: 'display', name: '表示項目'},
	{id: 'sheet',   name: 'シート設定'},
	{id: 'kw',      name: '主要キーワード設定'},
	{id: 'cp',      name: 'キャンペーン設定'},
	{id: 'aim',     name: '目標設定'}
]);

app.config(['$routeProvider',
	function($routeProvider) {
		/**
		 * AngularJSからの ng-view テンプレートリクエストとして、
		 * FuelPHP View::forge を呼ぶ。
		 * サーバデータをテンプレに展開する際はView::forgeにてデータを渡す。
		 */
		$routeProvider
		.when('/report', {
			mainTab : 'report',
			templateUrl: function(params) {
				return '/sem/new/falcon/report.html';
			},
			reloadOnSearch: false,
			controller: 'FalconReportCtrl',
			controllerAs: 'report'
		})
		.when('/list', {
			mainTab : 'list',
			templateUrl: function(params) {
				return '/sem/new/falcon/list.html';
			},
			reloadOnSearch: false,
			controller: 'FalconListCtrl',
			controllerAs: 'list'
		})
		.otherwise({
			redirectTo: '/report'
		});
	}
]);

app.config(["ssClientComboboxConfig", "falconBsName", function(ssClientComboboxConfig, falconBsName) {
	ssClientComboboxConfig.account.isView = true;
	ssClientComboboxConfig.categoryGenre.isView = true;
	ssClientComboboxConfig.client.bsName = falconBsName.chgClient;
}]);

/**
 * Loading Bar and spinner Display Option
 */
app.config(['cfpLoadingBarProvider', function(cfpLoadingBarProvider) {
    /* 画面上部のローディングバーを表示 */
	cfpLoadingBarProvider.includeBar = false;
}]);

app.run(['$location', '$rootScope', function($location, $rootScope) {
	$rootScope.$on('$routeChangeSuccess', function (event, current, previous) {
		if(current.$$route){
			$rootScope.mainTab = current.$$route.mainTab;
		}
    });
}]);
