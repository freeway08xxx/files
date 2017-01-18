// Declare app level module which depends on filters, and services
angular.module('quickManage', [
		'quickManage.controllers',
		'quickManage.services',
		'quickManage.directives',
		'quickManage.filters',
		'ss.module.termdate',
		'ngAnimate'
]);

var app = angular.module('quickManage');

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
				return '/sem/new/quickmanage/report.html';
			},
			reloadOnSearch: false,
			controller: 'QuickManageReportCtrl',
		})
		.when('/aim', {
			mainTab : 'aim',
			templateUrl: function(params) {
				return '/sem/new/quickmanage/aim.html';
			},
			reloadOnSearch: false,
			controller: 'QuickManageAimCtrl',
		})
		.when('/discount', {
			mainTab : 'discount',
			templateUrl: function(params) {
				return '/sem/new/quickmanage/discount.html';
			},
			reloadOnSearch: false,
			controller: 'QuickManageDiscountCtrl',
		})
		.otherwise({
			redirectTo: '/report'
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

app.constant('quickManageConst', {
	sortItems:{
		place  :{
			cost:'cost',
		},
		isDesc  :[false,true],
	},
	pagination : {
		limit       : 50,
		maxSize     : 5,
		currentPage : 1,
		offset      : 1
	},
	tmplSetting:{
		'imp'                   : {mark:'' , align :'text-right' , numberFilter :'| number'  },
		'click'                 : {mark:'' , align :'text-right' , numberFilter :'| number'  },
		'ctr'                   : {mark:'%', align :'text-right' , numberFilter :'| number:2'},
		'cpc'                   : {mark:'¥', align :'text-right' , numberFilter :'| number:0'},
		'cost'                  : {mark:'¥', align :'text-right' , numberFilter :'| number:0'},
		'rank'                  : {mark:'' , align :'text-right' , numberFilter :'| number:2'},
		'conv'                  : {mark:'' , align :'text-right' , numberFilter :'| number'  },
		'cvr'                   : {mark:'%', align :'text-right' , numberFilter :'| number:2'},
		'cpa'                   : {mark:'¥', align :'text-right' , numberFilter :'| number:0'},
		'ext_cv'                : {mark:'' , align :'text-right' , numberFilter :'| number:0'},
		'ext_cvr'               : {mark:'%', align :'text-right' , numberFilter :'| number:2'},
		'ext_cpa'               : {mark:'¥', align :'text-right' , numberFilter :'| number:0'},
		'media_name'            : {mark:'' , align :'text-center', numberFilter :''          },
		'cost_discount'         : {mark:'¥', align :'text-right' , numberFilter :'| number:0'},
		'gross_margin_discount' : {mark:'¥', align :'text-right' , numberFilter :'| number:0'},
		'discount_rate'         : {mark:'%', align :'text-right' , numberFilter :'| number:1'},
		'discount_value'        : {mark:'¥', align :'text-right' , numberFilter :'| number:0'},
		'budget'                : {mark:'¥', align :'text-right' , numberFilter :'| number:0'},
		'bureau_name'           : {mark:'',  align :'text-left' , numberFilter :''},
		'company_name'          : {mark:'',  align :'text-left' , numberFilter :''},
		'user_name'             : {mark:'',  align :'text-left' , numberFilter :''},
		'account_name'          : {mark:'',  align :'text-left' , numberFilter :''},
		'client_name'           : {mark:'',  align :'text-left' , numberFilter :''},
		'media_name'            : {mark:'',  align :'text-center' , numberFilter :''},
		'business_type_name'    : {mark:'',  align :'text-left' , numberFilter :''},
		'forecast'              : {mark:'¥', align :'text-right', numberFilter :''},
		'cl_aim_budget'         : {mark:'¥', align :'text-right', numberFilter :''},
		'customer_class_name'   : {mark:'',  align :'text-left' , numberFilter :''},
		'business_type_name '   : {mark:'',  align :'text-left' , numberFilter :''},
		'product_name'          : {mark:'',  align :'text-left' , numberFilter :''},
		'device_name'           : {mark:'',  align :'text-left' , numberFilter :''}
	},
	date_Replace_list: {
		saturday : '土',
		sunday   : '日'
	}
});




