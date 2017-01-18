// Declare app level module which depends on filters, and services
angular.module('tskt', [
		'ui.router',
		'ui.calendar',
		'ngAnimate',
		'tskt.controllers.base',
		'tskt.controllers.task',
		'tskt.controllers.top',
		'tskt.controllers.setting',
		'tskt.controllers.admin',
		'tskt.services.common',
		'tskt.services.task',
		'tskt.services.top',
		'tskt.services.setting',
		'tskt.services.admin',
		'tskt.filters',
		'tskt.directives',
		'ss.module.client-combobox',
	]
);


var app = angular.module('tskt');


app.config (function($stateProvider, $urlRouterProvider) {

	$urlRouterProvider.otherwise("top");

  	$stateProvider
		.state('top', {
			url: '/top',
			mainTab : 'top',
			templateUrl: function() {
				return '/sem/new/tasktracker/top/index.html';
			},
			//reloadOnSearch: false,
		})
		.state('save', {
			url: '/save',
			mainTab : 'save',
			templateUrl: function() {
				return '/sem/new/tasktracker/task/save/index.html';
			},
			//reloadOnSearch: false,
		})

		.state('setting', {
			url: '/setting',
			mainTab : 'setting',
			templateUrl: function() {
				return '/sem/new/tasktracker/setting/index.html';
			},
		})
		.state('setting.task', {
			url: '/:categoryId',
			mainTab : 'setting',
			templateUrl: function() {
				return '/sem/new/tasktracker/setting/task.html';
			},
		})
		.state('setting.task.detail', {
			url: '/:taskMasterId',
			mainTab : 'setting',
			templateUrl: function() {
				return '/sem/new/tasktracker/setting/task_detail.html';
			},
		})
		.state('setting.task.detail.process', {
			url: '/process/:processId',
			mainTab : 'setting',
			templateUrl: function() {
				return '/sem/new/tasktracker/setting/process.html';
			},
		})
		.state('admin', {
			url: '/admin',
			mainTab : 'admin',
			templateUrl: function() {
				return '/sem/new/tasktracker/admin/index.html';
			},
		})
});

app.run(['$location', '$rootScope', function($location, $rootScope) {

	$rootScope.date = new Date();  // デフォルトで現在時刻を表示
    $rootScope.$on('$stateChangeSuccess', function (event, current, previous) {
		if(current.mainTab){
			$rootScope.mainTab = current.mainTab;
		}
    });
}]);

app.config(["datepickerConfig", "datepickerPopupConfig", "timepickerConfig", 
		function(datepickerConfig, datepickerPopupConfig, timepickerConfig) {
			//datepickerConfig.dateType = "string"
			datepickerConfig.showWeeks = false; // 週番号（日本では馴染みが薄い）を非表示にする
			datepickerConfig.dayTitleFormat = "yyyy年 MMMM";
			datepickerPopupConfig.currentText = "本日";
			datepickerPopupConfig.clearText = "消去";
			datepickerPopupConfig.toggleWeeksText = "週番号";
			datepickerPopupConfig.closeText = "閉じる";
			timepickerConfig.showMeridian = false; // 時刻を24時間表示にする（デフォルトでは12時間表示）
}]);

/*
app.config(['$httpProvider', 
		function($httpProvider) {
			$httpProvider.interceptors.push(
				function ($q, $rootScope) {
					return {
						request: function(config) {
							return config;
						},
						response: function(response) {
							return response;
						},
						responseError: function(rejection) {
							if (500 == rejection.status) {
								alert('System Error!');
							}
							return $q.reject(rejection);
						}
					};
				}
			);
}]);
*/

app.config(['cfpLoadingBarProvider', function(cfpLoadingBarProvider) {
	/* 画面上部のローディングバーを表示 */
	cfpLoadingBarProvider.includeBar     = false;
	/* 画面中央にスピナーを表示 */
	cfpLoadingBarProvider.includeSpinner = true;
	/* ローディングを表示する通信時間の閾値 */
	//cfpLoadingBarProvider.latencyThreshold = 100;
}]);

app.constant('broadcast_names', {
	taskDetailView: 'setTaskDetailAction',
	taskRefresh: 'taskRefresh',
	settingSelectClient: 'settingSelectClient',

});
