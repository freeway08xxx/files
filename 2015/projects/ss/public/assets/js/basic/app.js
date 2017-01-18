// Declare app level module which depends on filters, and services
angular.module('ssTemplate', [
		'ssTemplate.services',
		'ssTemplate.controllers',
		'ssTemplate.filters',
		'ssTemplate.directives',
		'checklist-model',
		'ss.module.client-combobox',
		'ss.module.graph',
		'ss.module.table',
		'ngAnimate'
]);


var app = angular.module('ssTemplate');

app.config(['$routeProvider', '$locationProvider',
	function($routeProvider) {
		/**
		 * AngularJSからの ng-view テンプレートリクエストとして、
		 * FuelPHP View::forge を呼ぶ。
		 * サーバデータをテンプレに展開する際はView::forgeにてデータを渡す。
		 */
		$routeProvider
		.when('/form', {
			mainTab : 'form',
			templateUrl: function(params) {
				return '/sem/new/basic/form.html';
			},
			reloadOnSearch: false,
			controller: 'SsTemplateFormCtrl',
		})
		.when('/table', {
			mainTab : 'table',
			templateUrl: function(params) {
				return '/sem/new/basic/table.html';
			},
			reloadOnSearch: false,
			controller: 'SsTemplateTableCtrl',
			controllerAs: 'SsTemplateTable'
		})
		.otherwise({
			redirectTo: '/form'
		});
	}
]);

app.config(["datepickerConfig", "datepickerPopupConfig",
	function(datepickerConfig, datepickerPopupConfig) {
		datepickerConfig.showWeeks = false;
		datepickerConfig.yearRange = 10;
		datepickerConfig.dayTitleFormat = "yyyy年 MMMM";
		datepickerConfig.formatYear = "yyyy";
		datepickerPopupConfig.currentText = "本日";
		datepickerPopupConfig.clearText = "クリア";
		datepickerPopupConfig.closeText = "閉じる";
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
