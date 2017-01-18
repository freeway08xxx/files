// Declare app level module which depends on filters, and services
angular.module('eagle', [
		'eagle.services',
		'eagle.controllers',
		'eagle.filters',
		'eagle.directives',
		'checklist-model',
		'ss.module.client-combobox',
		'ui.grid',
		'ui.grid.pagination',
]);


var app = angular.module('eagle');

app.config(['$routeProvider', '$locationProvider',
	function($routeProvider) {
		/**
		 * AngularJSからの ng-view テンプレートリクエストとして、
		 * FuelPHP View::forge を呼ぶ。
		 * サーバデータをテンプレに展開する際はView::forgeにてデータを渡す。
		 */
		$routeProvider
		.when('/reserve', {
			mainTab : 'reserve',
			templateUrl: function(params) {
				return '/sem/new/eagle/reserve.html';
			},
			reloadOnSearch: false,
			controller: 'EagleReserveCtrl',
		})
		.when('/history', {
			mainTab : 'history',
			templateUrl: function(params) {
				return '/sem/new/eagle/history.html';
			},
			reloadOnSearch: false,
			controller: 'EagleHistoryCtrl',
		})
		.when('/update', {
			mainTab : 'update',
			templateUrl: function(params) {
				return '/sem/new/eagle/update.html';
			},
			reloadOnSearch: false,
			controller: 'EagleUpdateCtrl',
		})

		/*
		.when('/status', {
			mainTab : 'status',
			templateUrl: function(params) {
				return '/sem/new/eagle/status/index.html';
			},
			reloadOnSearch: false,
			controller: 'EagleStatusCtrl',
		})
		.when('/status/filter', {
			mainTab : 'status',
			templateUrl: function(params) {
				return '/sem/new/eagle/status/filter.html';
			},
			reloadOnSearch: false,
			controller: 'EagleStatusCtrl',
		})
		.when('/status/upload', {
			mainTab : 'status',
			templateUrl: function(params) {
				return '/sem/new/eagle/status/upload.html';
			},
			reloadOnSearch: false,
			controller: 'EagleStatusCtrl',
		})
		.when('/cpc', {
			mainTab : 'cpc',
			templateUrl: function(params) {
				return '/sem/new/eagle/cpc/index.html';
			},
			reloadOnSearch: false,
			controller: 'EagleCpcCtrl',
		})
		.when('/cpc/upload', {
			mainTab : 'cpc',
			templateUrl: function(params) {
				return '/sem/new/eagle/cpc/upload.html';
			},
			reloadOnSearch: false,
			controller: 'EagleCpcCtrl',
		})
		.when('/updating', {
			mainTab : 'status',
			templateUrl: function(params) {
				return '/sem/new/assets/template/eagle/updating.html';
			},
			reloadOnSearch: false,
			// controller: 'EagleCpcCtrl',
		})
		*/

		.otherwise({
			redirectTo: '/reserve'
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
		//$rootScope.loadingModal.close();
    });
}]);
