// Declare app level module which depends on filters, and services
angular.module("axis", [
		"ss.module.client-combobox",
		"ss.module.termdate",
		"axis.services",
		"axis.controllers",
		"axis.directives"
]);

var app = angular.module("axis");

app.config(["ssClientComboboxConfig",function(ssClientComboboxConfig) {
	ssClientComboboxConfig.categoryGenre.isView = true;
}]);

app.config(["$routeProvider", "$locationProvider",
	function($routeProvider) {
		/**
		 * AngularJSからの ng-view テンプレートリクエストとして、
		 * FuelPHP View::forge を呼ぶ。
		 * サーバデータをテンプレに展開する際はView::forgeにてデータを渡す。
		 */
		$routeProvider
		.when("/report", {
			mainTab : "report",
			templateUrl: function(params) {
				return "/sem/new/axis/report.html";
			},
			reloadOnSearch: false,
			controller: "ReportCtrl",
		})
		.otherwise({
			redirectTo: "/report"
		});
	}
]);

app.run(["$location", "$rootScope", function($location, $rootScope) {
	$rootScope.$on("$routeChangeSuccess", function (event, current, previous) {
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
		currentPage:  1,
		numPages   :  1,
		limit      : 40,
		maxSize    :  5,
		offset     :  1
	},
	summayTableCell:{
		"no"              :"No",
		"media_id"        :"媒体名",
		"account_id"      :"アカウントID",
		"account_name"    :"アカウント名",
		'campaign_deliver':"広告掲載方式",
		'imp'             :"Imp",
		'click'           :"Click",
		'ctr'             :"CTR",
		'cpc'             :"CPC",
		'cost'            :"Cost",
		'rank'            :"Rank",
		'accroasount_id'  :"ROAS",
		'conv'            :"Conv",
		'cvr'             :"CVR",
		'cpa'             :"CPA"
	}
});


var opt_lang = {
	"oPaginate": {
		"sPrevious": "前へ",
		"sNext": "次へ",
	},
	"sInfo": "_START_ ～ _END_件 / 全_TOTAL_件"
};

// jQuery Triggers
$(function () {
	$("a[rel=tooltip], .tooltips").tooltip();

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

	// レポート結果テーブル設定
	/**************************************/
	/* 表示が遅いから考えたほうがいいかも */
	/**************************************/
	if ($("#reportview_table").size() > 0) {
		var wh = $(window).height();
		wh = wh - 400;

		var summary_count = $("#summary_unit_count").val();

		var table = $("#reportview_table").dataTable({
			"bFilter": false,			// フィルタなし
			"bLengthChange": false,		// 表示件数変更なし
			"aaSorting": [0,"asc"],		// デフォルトソート
			"aoColumnDefs": [
				{ "bSortable": false, "aTargets": [0] },
				{ "bSortable": false, "aTargets": [summary_count] },
			],
			"scrollY": wh + "px",
			"scrollX": "100%",
			"scrollCollapse": true,
			"paging": false,
			"oLanguage": opt_lang,
		});
		new $.fn.dataTable.FixedColumns(table, {"leftColumns": summary_count});
	}
});
