// Declare app level module which depends on filters, and services
angular.module('support_api_quota', [
		'support_api_quota.services',
		'support_api_quota.controllers',
		'support_api_quota.filters',
		'support_api_quota.directives',
		'ui.grid',
		'ui.grid.pagination',
]);


var app = angular.module('support_api_quota');

app.config(['$routeProvider', '$locationProvider',
	function($routeProvider) {
		/**
		 * AngularJSからの ng-view テンプレートリクエストとして、
		 * FuelPHP View::forge を呼ぶ。
		 * サーバデータをテンプレに展開する際はView::forgeにてデータを渡す。
		 */
		$routeProvider
		.when('/yahoo', {
			mainTab : 'yahoo',
			templateUrl: function(params) {
				return '/sem/new/support/api/quota/yahoo.html';
			},
			reloadOnSearch: false,
			controller: 'SupportApiQuotaYahoo',
		})
		.when('/ydn', {
			mainTab : 'ydn',
			templateUrl: function(params) {
				return '/sem/new/support/api/quota/ydn.html';
			},
			reloadOnSearch: false,
			controller: 'SupportApiQuotaYdn',
		})
		.otherwise({
			redirectTo: '/yahoo'
		});
	}
]);

app.run(['$location', '$rootScope', 'ssModal', function($location, $rootScope, ssModal) {
    $rootScope.$on('$routeChangeStart', function (event, current, previous) {
		//$rootScope.loadingModal = ssModal.loading('通信中です...');
    });
}]);

app.run(['$location', '$rootScope', function($location, $rootScope) {
    $rootScope.$on('$routeChangeSuccess', function (event, current, previous) {
		if(current.$$route){
        	$rootScope.mainTab = current.$$route.mainTab;
		}
    });
}]);

/**
 * Loading Bar and spinner Display Option
 */
app.config(['cfpLoadingBarProvider', function(cfpLoadingBarProvider) {
	/* 画面上部のローディングバーを表示 */
	cfpLoadingBarProvider.includeBar     = false;
	/* 画面中央にスピナーを表示 */
	cfpLoadingBarProvider.includeSpinner = false;
}]);