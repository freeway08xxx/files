// Declare app level module which depends on filters, and services
angular.module('accountStructure', [
		'accountStructure.controllers',
		"accountStructure.services",
        'ss.module.client-combobox'
]);

var app = angular.module('accountStructure');

app.config(['$routeProvider', '$locationProvider',
	function($routeProvider) {
		/**
		 * AngularJSからの ng-view テンプレートリクエストとして、
		 * FuelPHP View::forge を呼ぶ。
		 * サーバデータをテンプレに展開する際はView::forgeにてデータを渡す。
		 */
		$routeProvider
		.when('/condition', {
			mainTab : 'condition',
			templateUrl: function(params) {
				return '/sem/new/accountstructure/condition.html';
			},
			reloadOnSearch: false,
			controller: 'AccountStructureConditionCtrl',
		})
		.when('/history', {
			mainTab : 'history',
			templateUrl: function(params) {
				return '/sem/new/accountstructure/history.html';
			},
			reloadOnSearch: false,
			// controller: 'AccountStructureHistoryCtrl',
		})
		.otherwise({
			redirectTo: '/condition'
		});
	}
]);

app.run(['$location', '$rootScope', function($location, $rootScope) {
    $rootScope.$on('$routeChangeSuccess', function (event, current, previous) {
		if(current.$$route){
        	$rootScope.mainTab = current.$$route.mainTab;
		}
    });
}]);

app.config(["ssClientComboboxConfig",function(ssClientComboboxConfig) {
		ssClientComboboxConfig.bcNameClient = 'ss_combobox_client_change';
		ssClientComboboxConfig.bcNameAccount = 'ss_combobox_account_change';
		ssClientComboboxConfig.accountView = true;
	}
]);

