/* Services */
var services = angular.module('quickManage.services', []);

services.service('quickManageRestApi', ['$q', '$http', function($q, $http) {
	return {
		getReportdata : function(data) {
			var deferred = $q.defer();
			$http.post('/sem/new/quickmanage/report/display', data).success(function(data) {
				deferred.resolve(data);
			}).error(function (data, status, headers, config) {
				if(status == 404){
					alert("エラーが発生しました。");
				}
			});
			return deferred.promise;
		},
		getExportdata : function(data) {
			var deferred = $q.defer();
			$http.post('/sem/new/quickmanage/report/export', data).success(function(data) {
				deferred.resolve(data);
			}).error(function (data, status, headers, config) {
				if(status == 404){
					alert("エラーが発生しました。");
				}
			});
			return deferred.promise;
		},
	};
}]);

/**
 *  パラメータ制御 
 */
services.factory('paramsFactoryService', [function() {
	var params = {};
	return {
		getParams: function (scope,form,moveParams) {
			if(!moveParams){
				form = decodeURIComponent(form.replace(/\+/g, '%20'))
				params = JSON.parse('{"' + form.replace(/(\r\n)/g, "\\n").replace(/&/g, "\",\"").replace(/=/g,"\":\"") + '"}');

				if(typeof scope.params.filter[scope.params.filter_selected + '_list'] != 'undefined') params[scope.params.filter_selected + '_list'] = scope.params.filter[scope.params.filter_selected + '_list'];
			}else{
				delete params['bureau_id_list'];
				delete params['search_user_name'];

				_.each(moveParams, function(value,key){
					if(!_.isEmpty(value)){
						if(key == 'bureau_id_list') value = [value];
						scope.params[key] = value;
						params[key]       = value;
					}
				});
			}	

			params.start_date = [];
			params.end_date   = [];
			_.each(scope.params.termdate.term_arr, function(obj){
				params['start_date'].push(moment(obj['from']).format('YYYY/MM/DD'));
				params['end_date'].push(moment(obj['to']).format('YYYY/MM/DD'));
			});
			delete params['start_date[]'];
			delete params['end_date[]'];
			return params;
		}
	};
}]);

/**
 *  異なるコントローラ間のテーブルデータ共有
 */
services.service('dataSharedService', ['$rootScope','tableFactoryService', function($rootScope,tableFactoryService) {
	var data   = [];
	return {
		data_list: {
			get: function() { return data; },
			set: function(arr,scope) {
					data = tableFactoryService.arrangeTableData(arr,scope);
					$rootScope.$broadcast('changedData');
				 }
			}
		}
}]);

/**
 *  utile関数
 */
services.factory('utilFactoryService', ['quickManageConst',function(quickManageConst) {
	return {
		/**
		 *  numberに出来る物はnumberにして返す
		 */
		toNumber: function (arr) {
			var res = [];
			var isNumber = function(key){
				if(typeof quickManageConst.tmplSetting[key] != 'undefined' ){
					if (quickManageConst.tmplSetting[key].numberFilter.indexOf('number') >= 0) return true
				}
				return false
			}

			_.each(arr, function(vals,i){
				var obj = {};
				_.each(vals, function(val,key){
					obj[key] = isNumber(key) ? parseFloat(val) : val;
				});
				res[i] = obj
			});


			return res;
		},
	}
}]);

services.factory('tableFactoryService', ['quickManageConst','utilFactoryService', function(quickManageConst,utilFactoryService){
	return {
		/**
		 *  テーブル用にデータを整形
		 */
		 arrangeTableData: function (data,scope) {
			var res = {
				'elm' : {
					'report_elm_list' : (Object.keys(data.elm_list.report_elm_list).length  >= 1) ? data.elm_list.report_elm_list :{},
					'summary_elm_list': (Object.keys(data.elm_list.summary_elm_list).length >= 1) ? data.elm_list.summary_elm_list:{}
				},
				'data': [],
			};
			delete data.elm_list;

			_.each(data, function(obj,i){
				obj['summary'] = utilFactoryService.toNumber(_.values(obj['summary']));
				res.data.push(obj);
			});

			return res;
		},

		/**
		 *  複数のobjectをextend 元のオブジェクトには上書きしない
		 */
		 multiExtend: function (arr) {
			var res = {};
			_.each(arr, function (obj, i) {
				_.extend(res,obj);
			});
			return res;
		},

		/**
		 *  指定した位置(引数elmの前)にobjectを差し込む
		 */
		 addBetweenObj: function (obj,elm,addObj) {
			var res       = {};
			var add_key   = _.keys(addObj);
			var add_value = _.values(addObj);

			_.each(obj, function (value,key) {
				if(key === elm) res[add_key[0]] = add_value[0];
				res[key] = value;
			});
			return res;
		},

		/**
		 *  テーブルエレメントの%,￥を取得
		 */
		getFormat: function (elm) {
			var res = {yen:'',percent:''};
			if(typeof elm === 'undefined') return res;

			for (var key in quickManageConst.tmplSetting) {
				if (elm==key) {
					if (quickManageConst.tmplSetting[key].mark == '%') var name ='percent';
					if (quickManageConst.tmplSetting[key].mark == '¥') var name = 'yen';
					res[name] = quickManageConst.tmplSetting[key].mark;
				}
			}
			return res;
		},
		/**
		 * テーブルエレメントのcss,numberFilter有無を取得
		 */
		getTmplSetting: function (key) {
			var res = {align :'',numberFilter:''};
			if(typeof quickManageConst.tmplSetting[key] == 'undefined') return res;

			res = {
				align       : quickManageConst.tmplSetting[key].align,
				numberFilter: quickManageConst.tmplSetting[key].numberFilter
			};
			return res;
		},
		/**
		 * 日別 日付リスト生成
		 */
		getDateList: function (obj) {
			var res = {};
			_.each(obj.date_list, function (value,key) {
				if(key == 'forecast_list'){
					res[key] = value
				}else{
					value = key + moment(key).format('(dd)');
					res['date_'+ key.replace(/-/g,'_')] = value.replace(/-/g,'/');
				}
			});

			return res;
		},
		/**
		 *  期間比較テーブル用 2番目以降から、差分データを間に挟んで結合させる関数
		 */
		getDateDiffList: function (obj) {
			var dateArr = _.keys(obj.date_list);
			var diffArr = _.keys(obj.diff_list);
			var j = k = 0, arr = [], res = {};
			for (var i = 0; i < dateArr.length + diffArr.length; i++) {
				if(i % 2 != 0 || i < 2){
					arr[i] = dateArr[k]; k++;
				}else{
					arr[i] = diffArr[j]; j++;
				}
			};
			_.each(arr, function (value) { 
				res[value] = (typeof obj.date_list[value] == 'undefined') ? '差分': obj.date_list[value]});

			return res;
		},
		/**
		 * テーブルヘッド
		 * 土,日曜日を<span></span>で囲む
		 * 
		 */
		getDayElm: function (date) {
			_.each(quickManageConst.date_Replace_list, function (value,key) {
				if(date.indexOf(value) >= 0) {
					var arr  = date.split(value);
					date = arr[0] + '<span class="'+key+'">'+value+'</span>)';
				}
			});
			return date;
		}
	};
}]);





