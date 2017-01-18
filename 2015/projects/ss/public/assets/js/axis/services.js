/* Services */
var service = angular.module("axis.services", [])

/**
 * ServerData API
 */
service.service('axisRestApi', ['$q', '$http', function($q, $http) {
	return {
		/* レポート取得 */
		getReportdata : function(params) {
			var deferred = $q.defer();
			var postData = {"params" : params };
			$http.post('/sem/new/axis/export/displayasync', postData)
				.success(function(data) {
					deferred.resolve(data);
				}).error(function (data, status, headers, config) {
					if(status == 404){
						alert("エラーが発生しました。");
					}
			});
			return deferred.promise;
		}
	};
}]);


/**
 * tableSetting
 */
service.factory('tableSetting', ['$q', '$http', function($q, $http) {
	return {

		/* レポート取得 */

	};
}]);









service.factory("FiltersService", function () {
		return {
			// サマリ種別によりフィルタ項目の表示を制御
			isShowFilter: function (scope, filter_item) {
				var summary_type = scope.params.summary_type;

				if (filter_item === "match_type") {
					if (summary_type === "keyword" || summary_type === "query") {
						return true;
					}
				} else if (filter_item === "campaign") {
					if (summary_type !== "deliver" && summary_type !== "media" && summary_type !== "account") {
						return true;
					}
				} else if (filter_item === "ad_group") {
					if (summary_type !== "deliver" && summary_type !== "media" && summary_type !== "account" && summary_type !== "campaign") {
						return true;
					}
				} else if (filter_item === "keyword") {
					if (summary_type === "keyword") {
						return true;
					}
				} else if (filter_item === "ad") {
					if (summary_type === "ad") {
						return true;
					}
				} else if (filter_item === "domain") {
					if (summary_type === "domain") {
						return true;
					}
				} else if (filter_item === "url") {
					if (summary_type === "ad" || summary_type === "url") {
						return true;
					}
				} else if (filter_item === "query") {
					if (summary_type === "query") {
						return true;
					}
				} else if (filter_item === "link_url") {
					if (summary_type === "keyword" || summary_type === "ad") {
						return true;
					}
				} else if (filter_item === "category") {
					if (scope.clientCombobox.categoryGenre) {
						return true;
					}
				}

				return false;
			},
			isSettingFilter: function (scope, filter_item) {
				var ret = false;
				var cnt = 0;

				// 選択済みフィルタ項目は１つのみ
				angular.forEach (scope.params.filters, function (obj) {
					if (filter_item === obj["filter_item"]) {
						cnt++;
					}
				});
				if (cnt > 1) { ret = true; }
				return ret;
			},
			addFilter: function (scope) {
				var filters = scope.params.filters;
				var open = scope.params.filters_is_open;
				var ret = true;

				// 未入力の場合、追加させない
				angular.forEach (filters, function (obj) {
					if (obj["filter_item"] === "match_type") {
						if (obj["filter_cond"] === "") {
							ret = false;
						}
					} else {
						angular.forEach (obj, function (value, key) {
							if (value === "") {
								ret = false;
							}
						})
					}
				});
				if (!ret) { return false; }

				filters[filters.length] = {filter_item: "", filter_cond: "", filter_text: ""};
				open[open.length] = {filter_item: false, filter_cond: false, filter_text: false};
			},
			deleteFilter: function (scope, i) {
				scope.params.filters.splice(i, 1);
			},
			resetFilter: function (scope) {
				scope.params.filters.splice(1, scope.params.filters.length);
				scope.params.filters = [];
			},
			clear: function (scope, i) {
				scope.params.filters[i] = {filter_item: "", filter_cond: "", filter_text: ""};
			},
		};
	})

service.factory("ReportFiltersService", function () {
		return {
			isShowFilter: function (scope, filter_item) {

				// 外部CV選択時のみ
				if (filter_item.match("^ext_")) {
					if (scope.params.ext_cv_list.length === 0) {
						return false;
					}
				}

				return true;
			},
			isSettingFilter: function (scope, filter_item) {
				var ret = false;
				var cnt = 0;

				// 選択済みフィルタ項目は１つのみ
				angular.forEach (scope.params.report_filters, function (obj) {
					if (filter_item === obj["filter_item"]) {
						cnt++;
					}
				});
				if (cnt > 1) { ret = true; }
				return ret;
			},
			addFilter: function (scope) {
				var filters = scope.params.report_filters;
				var open = scope.params.report_filters_is_open;
				var ret = true;

				// 未入力の場合、追加させない
				angular.forEach (filters, function (obj) {
					angular.forEach (obj, function (value, key) {
						if (value === "") {
							ret = false;
						}
					})
				});
				if (!ret) { return false; }

				filters[filters.length] = {filter_item: "", filter_min: "", filter_max: ""};
				open[open.length] = {filter_item: false, filter_min: false, filter_max: false};
			},
			deleteFilter: function (scope, i) {
				scope.params.report_filters.splice(i, 1);
			},
			resetFilter: function (scope) {
				scope.params.report_filters.splice(1, scope.params.report_filters.length);
				scope.params.report_filters = [];
			},
			clear: function (scope, i) {
				scope.params.report_filters[i] = {filter_item: "", filter_min: "", filter_max: ""};
			},
		};
	})

service.factory("ExtCvFiltersService", function () {
		return {
			isSettingFilter: function (scope, filter_elem, filter_item) {
				var ret = false;
				var cnt = 0;

				// 選択済みフィルタ項目は１つのみ
				angular.forEach (scope.params.ext_cv_filters, function (obj) {
					if (filter_elem === obj["filter_elem"] && filter_item === obj["filter_item"]) {
						cnt++;
					}
				});
				if (cnt > 1) { ret = true; }
				return ret;
			},
			addFilter: function (scope) {
				var filters = scope.params.ext_cv_filters;
				var open = scope.params.ext_cv_filters_is_open;
				var ret = true;

				// 未入力の場合、追加させない
				angular.forEach (filters, function (obj) {
					angular.forEach (obj, function (value, key) {
						if (value === "") {
							ret = false;
						}
					})
				});
				if (!ret) { return false; }

				filters[filters.length] = {filter_elem: "", filter_item: "", filter_min: "", filter_max: ""};
				open[open.length] = {filter_elem: false, filter_item: false, filter_min: false, filter_max: false};
			},
			deleteFilter: function (scope, i) {
				scope.params.ext_cv_filters.splice(i, 1);
			},
			resetFilter: function (scope) {
				scope.params.ext_cv_filters.splice(1, scope.params.ext_cv_filters.length);
				scope.params.ext_cv_filters = [];
			},
			clear: function (scope, i) {
				scope.params.ext_cv_filters[i] = {filter_elem: "", filter_item: "", filter_min: "", filter_max: ""};
			},
		};
	});
