/* Services */
angular.module("accountStructure.services", []).factory("FiltersService",function() {
			return {
				
				addFilter : function(scope) {
					var filters = scope.param.filters;
					var ret = true;
					// 各フィルタリングの必須項目が未入力の場合、追加させない
					angular.forEach(filters, function(obj) {
						if (obj["filter_item"] === "") {
							ret = false;
						}
						if (obj["filter_item"] === "t_location" && !obj["t_location"]) {
							ret = false;
						}
						if (obj["filter_item"] === "t_schedule" && !obj["t_days"]) {
							ret = false;
						}
						if (obj["filter_item"] === "t_gender" && !obj["t_gender"]) {
							ret = false;
						}
						if (obj["filter_item"] === "negative_keyword" && !obj["filter_text"]) {
							ret = false;
						}
						if (obj["filter_item"] === "t_userlist" && !obj["filter_text"]) {
							ret = false;
						}
						if (obj["filter_item"] === "t_placement" && !obj["filter_text"]) {
							ret = false;
						}
					});
					if (!ret) {
						return false;
					}
					// フィルタ追加時初期化
					filters[filters.length] = {
						filter_item : "",
						filter_cond : "",
						filter_cond_url : "",
						filter_text : "",
						filter_url : ""
					};
				},
				deleteFilter : function(scope, i) {
					scope.param.filters.splice(i, 1);
				},
				resetFilter : function(scope) {
					scope.param.filters.splice(1, scope.param.filters.length);
					scope.param.filters = [];
				},
				clearFilter : function(scope, i) {
					scope.param.filters[i] = {
						filter_item : "",
						filter_cond : "",
						filter_text : ""
					};
				},
				btnFilterGoogle : function(scope) {
					var filters = scope.clientCombobox.accounts;
					var ret = false;
					// googleのみ表示させる
					angular.forEach(filters, function(obj) {
						if (obj["media_id"] === 2) {
							ret = true;
						}
					});
					return ret;
				},
				btnFilterYdn : function(scope) {
					var filters = scope.clientCombobox.accounts;
					var ret = false;
					// ydnのみ表示させる
					angular.forEach(filters, function(obj) {
						if (obj["media_id"] === 3) {
							ret = true;
						}
					});
					return ret;
				},
			};
		});