// Declare app level module which depends on filters, and services
angular.module('axis', [
		'ss.module.client-combobox',
		'ss.module.termdate',
		'axis.controllers',
		'axis.directives',
		'axis.services',
		'axis.filters',
		'ngAnimate'
]);

var app = angular.module('axis');

app.config(['ssClientComboboxConfig',function(ssClientComboboxConfig) {
	ssClientComboboxConfig.categoryGenre.isView = true;
}]);

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
				return '/sem/new/axis/report.html';
			},
			reloadOnSearch: false,
			controller: 'ReportCtrl',
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


app.constant('axisConst', {
	sortItems:{
		place  :{
			account_id:'account_id',
		},
		isDesc  :[true,false],
	},
	pagination : {
		limit      : 75,
		maxSize    : 5,
		device     : {
			all_devices: {currentPage: 1,offset: 1,},
			pc         : {currentPage: 1,offset: 1,},
			tab        : {currentPage: 1,offset: 1,},
			pc_tab     : {currentPage: 1,offset: 1,},
			tab_sp     : {currentPage: 1,offset: 1,},
		}
	},
	depth_indicate_cell:{
		'indicate'               :'指標',
		'all'                    :'合計',
	},
	unsorted:{
		'account_name'           :'合計'
	},
	extCvSumDisplay:{
		'ext_all'                :'外部CV合計'
	},
	markList:{
		'cpc_max'                : '¥',
		'bid_modifier'           : '%',
		'ctr'                    : '%',
		'cpc'                    : '¥',
		'cost'                   : '¥',
		'cvr'                    : '%',
		'cpa'                    : '¥',
		'cpc_min'                : '¥',
		'campaign_daily_budget'  : '¥',
		'zero_cost_rate'                         : '%',
		'campaign_search_impr_share'             : '%',
		'campaign_search_rank_lost_impr_share'   : '%',
		'campaign_search_exact_match_impr_share' : '%',
		'campaign_content_impr_share'            : '%',
		'campaign_content_rank_lost_impr_share'  : '%',
		'ad_group_search_impr_share'             : '%',
		'ad_group_search_rank_lost_impr_share'   : '%',
		'ad_group_search_exact_match_impr_share' : '%',
		'ad_group_content_impr_share'            : '%',
		'ad_group_content_rank_lost_impr_share'  : '%',
	},
	granularity_order:['category','media','account','campaign','ad_group','keyword','ad'],
	number_format:['imp','click','ctr','cpc','cost','rank','conv','cvr','cpa','cpc_min','cpc_max','campaign_daily_budget'],

	commonValues:{
		'imp'                    :'Imp',
		'click'                  :'Click',
		'ctr'                    :'CTR',
		'cpc'                    :'CPC',
		'cost'                   :'Cost',
		'rank'                   :'Rank',
		'conv'                   :'CVs',
		'cvr'                    :'CVR',
		'cpa'                    :'CPA'
	},
	graphColorCode:{
		'imp'  :'#ff7a7a',
		'click':'#ff9b38',
		'ctr'  :'#729ed7',
		'cpc'  :'#00ced1',
		'cost' :'#00a1e9',
		'rank' :'#ff99cc',
		'conv' :'#D62728',
		'cvr'  :'#98df8a',
		'cpa'  :'#2CA02C',
		'campaign_daily_budget'  : '#ba55d3',
		'campaign_search_impr_share'             : '#ba55d3',
		'campaign_search_rank_lost_impr_share'   : '#6b8e23',
		'campaign_search_exact_match_impr_share' : '#48d1cc',
		'campaign_content_impr_share'            : '#9400d3',
		'campaign_content_rank_lost_impr_share'  : '#32cd32',
		'ad_group_search_impr_share'             : '#ba55d3',
		'ad_group_search_rank_lost_impr_share'   : '#6b8e23',
		'ad_group_search_exact_match_impr_share' : '#48d1cc',
		'ad_group_content_impr_share'            : '#9400d3',
		'ad_group_content_rank_lost_impr_share'  : '#32cd32',
	}
});


