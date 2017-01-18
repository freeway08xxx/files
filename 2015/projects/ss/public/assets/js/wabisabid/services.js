/* Services */
var services = angular.module('wabisabid.services', []);

services.service('wabisabidService', ['$q', '$http', function($q, $http) {
	return {
		getHagakureSetting: function(client_id) {
			return $http({
				url: '/sem/new/wabisabid/hagakure/setting/' + client_id,
				method: 'GET'
			})
		},
		getExtCvList: function(client_id) {
			return $http({
				url: '/sem/new/api/extcv/index/' + client_id,
				method: 'GET'
			})
		},
		getHagakureSettingDetail: function(wabisabi_id) {
			return $http({
				url: '/sem/new/wabisabid/hagakure/settingdetail/' + wabisabi_id,
				method: 'GET'
			})
		},
		getHagakureResult: function(wabisabi_id) {
			return $http({
				url: '/sem/new/wabisabid/hagakure/result/' + wabisabi_id,
				method: 'GET'
			})
		},
		getHagakureBidding: function(wabisabi_id) {
			return $http({
				url: '/sem/new/wabisabid/hagakure/bidding/' + wabisabi_id,
				method: 'GET'
			})
		},
		getHagakureBidDetail: function(wabisabi_id, target_date) {
			return $http({
				url: '/sem/new/wabisabid/hagakure/biddetail/' + wabisabi_id + '/' + target_date,
				method: 'GET'
			})
		},
		delHagakureSetting: function(wabisabi_id) {
			return $http({
				url: '/sem/new/wabisabid/hagakure/delete/' + wabisabi_id,
				method: 'POST'
			})
		},
		updHagakureStatus: function(wabisabi_id, status) {
			return $http({
				url: '/sem/new/wabisabid/hagakure/updstatus/' + wabisabi_id + '/' + status,
				method: 'POST'
			})
		},
		setHagakureSetting: function(data) {
			return $http({
				url: '/sem/new/wabisabid/hagakure/regist',
				method: 'POST',
				data: data
			})
		},
	};
}]);

services.service('filtersService', function() {
	return {
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
				angular.forEach (obj, function (value, key) {
					if (value === "") {
						ret = false;
					}
				})
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
});

services.service('validateService', function(filtersService) {
	return {
		isValidate: function (scope) {
			if (_.isEmpty(scope.clientCombobox.client)) {
				scope.message = 'クライアントが選択されていません。';
				return false;
			}

			if (_.isEmpty(scope.clientCombobox.accounts)) {
				scope.message = 'アカウントが選択されていません。';
				return false;
			}

			if (!_.isNull(scope.params.media_cost)) {
				if (!_.isNumber(scope.params.media_cost)) {
					scope.message = '媒体費は数値で入力してください。';
					return false;
				}
				if (scope.params.media_cost >= 100 || scope.params.media_cost <= 0) {
					scope.message = '媒体費は0～100%の範囲で入力してください。';
					return false;
				}
			}

			if (!_.isNumber(scope.params.target_budget)) {
				scope.message = 'ターゲット予算は数値で入力してください。';
				return false;
			}

			if (!_.isNumber(scope.params.target_cpa)) {
				scope.message = '目標CPAは数値で入力してください。';
				return false;
			}

			if (!_.isEmpty(scope.params.limit_cpc) && !_.isNumber(scope.params.limit_cpc)) {
				scope.message = '入札額の上限値は数値で入力してください。';
				return false;
			}

			if (!_.isNull(scope.params.limit_mba)) {
				if (!_.isNumber(scope.params.limit_mba)) {
					scope.message = 'モバイル調整率の上限値は数値で入力してください。';
					return false;
				}
				if (scope.params.limit_mba !== -100 && (scope.params.limit_mba > 300 || scope.params.limit_mba < -90)) {
					scope.message = 'モバイル調整率の上限値は-100%、もしくは-90～300%の範囲で入力してください。';
					return false;
				}
			}

			angular.forEach (scope.params.filters, function (obj) {
				settled = filtersService.isSettingFilter(scope, obj["filter_item"]);
				if (settled) {
					scope.message = "フィルタリング設定に重複した項目があります。";
					return false;
				}
			});

			return true;
		}
	};
});
