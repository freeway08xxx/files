// Declare app level module which depends on filters, and services
angular.module('sitemap', [
		'sitemap.controllers.base',
]);


var app = angular.module('sitemap');

app.config(['$routeProvider', '$locationProvider',
	function($routeProvider) {
		/**
		 * AngularJSからの ng-view テンプレートリクエストとして、
		 * FuelPHP View::forge を呼ぶ。
		 * サーバデータをテンプレに展開する際はView::forgeにてデータを渡す。
		 */
		$routeProvider
		.when('/', {
			templateUrl: function(params) {
				return '/sem/new/sitemap/index.html';
			},
			reloadOnSearch: false
		})
	}
]);


/**
 * Loading Bar and spinner Display Option
 */
app.config(['cfpLoadingBarProvider', function(cfpLoadingBarProvider) {
	/* 画面上部のローディングバーを表示 */
	cfpLoadingBarProvider.includeBar     = false;
	/* 画面中央にスピナーを表示 */
	cfpLoadingBarProvider.includeSpinner = true;
}]);
