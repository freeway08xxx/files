// Declare app level module which depends on filters, and services
angular.module('quickManage', [
		'quickManage.services',
		'quickManage.controllers',
		'quickManage.directives',
		'ss.module.termdate'
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

var opt_lang = {
	"oPaginate": {
		"sPrevious": "前へ",
		"sNext": "次へ",
	},
	"sInfo": "_START_ ～ _END_件 / 全_TOTAL_件"
};

// jQuery Triggers
$(function () {
	$('a[rel=tooltip], .tooltips').tooltip();

	// 作成レポート履歴テーブル設定
	if ($("#history_table").size() > 0) {
		$("#history_table").dataTable({
			"bFilter": false,			// フィルタなし
			"bLengthChange": false,		// 表示件数変更なし
			"aaSorting": [6,"desc"],	// デフォルトソート
			"aLengthMenu" : [25],		// 表示件数メニュー
			"oLanguage": opt_lang,
		});
	}

	// テンプレート一覧テーブル設定
	if ($("#template_table").size() > 0) {
		$("#template_table").dataTable({
			"bFilter": false,			// フィルタなし
			"bLengthChange": false,		// 表示件数変更なし
			"aaSorting": [7,"desc"],	// デフォルトソート
			"aLengthMenu" : [20],		// 表示件数メニュー
			"oLanguage": opt_lang
		});
	}

	// 目標設定一覧テーブル設定
	if ($("#aim_table").size() > 0) {
		$("#aim_table").dataTable({
			"bFilter": false,			// フィルタなし
			"bLengthChange": false,		// 表示件数変更なし
			"aaSorting": [0,"desc"],	// デフォルトソート
			"aLengthMenu" : [20],		// 表示件数メニュー
			"oLanguage": opt_lang
		});
	}

	// 値引設定一覧テーブル設定
	if ($("#discount_table").size() > 0) {
		$("#discount_table").dataTable({
			"bFilter": false,			// フィルタなし
			"bLengthChange": false,		// 表示件数変更なし
			"aaSorting": [0,"desc"],	// デフォルトソート
			"aLengthMenu" : [20],		// 表示件数メニュー
			"oLanguage": opt_lang
		});
	}

	// レポート結果テーブル設定
	if ($("#reportview_table").size() > 0) {
		var wh = $(window).height();
		wh = wh - 400;

		var summary_count = $('#summary_unit_count').val();

		var table = $("#reportview_table").dataTable({
			"bFilter": false,			// フィルタなし
			"bLengthChange": false,		// 表示件数変更なし
			"aaSorting": [0,"desc"],	// デフォルトソート
			"aoColumnDefs": [
				{ "bSortable": false, "aTargets": [0] },
				{ "bSortable": false, "aTargets": [summary_count] },
			],
			"scrollY": wh + 'px',
			"scrollX": "100%",
			"scrollCollapse": true,
			"paging": false,
			"oLanguage": opt_lang,
		});
		new $.fn.dataTable.FixedColumns(table, {"leftColumns": summary_count});
	}
});
