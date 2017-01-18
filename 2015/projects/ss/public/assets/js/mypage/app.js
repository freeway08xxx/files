// Declare app level module which depends on filters, and services
angular.module('mypage', [
		'mypage.controllers.base',
		'mypage.controllers.report',
		'mypage.controllers.graph',
		'mypage.controllers.keyword',
		'mypage.services',
		'mypage.filters',
		'mypage.directives',
		'ngAnimate'
]);


var app = angular.module('mypage');

app.config(['$routeProvider', '$locationProvider',
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
				return '/sem/new/mypage/report.html';
			},
			reloadOnSearch: false
		})
		.otherwise({
			redirectTo: '/report'
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

/**
 * Loading Bar and spinner Display Option
 */
app.config(['cfpLoadingBarProvider', function(cfpLoadingBarProvider) {
	/* 画面上部のローディングバーを表示 */
	cfpLoadingBarProvider.includeBar     = true;
	/* 画面中央にスピナーを表示 */
	cfpLoadingBarProvider.includeSpinner = true;
}]);

app.constant('mypageConst', {
	diff : {
		visible: {
			1:true,
			2:false,
		},
	},
	isLastMonth : {
		1:true,
		2:false,
	},
	isloaded : {
		thismonth:{
			1:true,
			2:false,
		},
		lastmonth: {
			1:true,
			2:false,
		},
	},
	sortItems : {
		adType :{
			search:'search',
			display:'display',
		},
		place  :{
			client_name:'client_name',
			conv:'conv',
			cost:'cost',
			conv:'conv',
			cpa:'cpa',
			forecast_conv:'forecast_conv',
			forecast_cost:'forecast_cost',
			forecast_cpa:'forecast_cpa',
			gross_margin:'gross_margin'
		},
		isDesc  :{
			1:true,
			2:false,
		}
	},
	settings : {
		tab:{
			summary:'summary',
			keyword:'keyword'
		}
	},
	myKeywords:{
		max:10
	}
});
