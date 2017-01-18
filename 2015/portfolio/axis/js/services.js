/* Services */
var services = angular.module('axis.services', []);

services.service('axisRestApi', ['$q', '$http', function($q, $http) {
	return {
		getTemplateList: function(client_id) {
			return $http({
				url: '/sem/new/axis/report/templatelist.json',
				method: 'POST',
				data: client_id,
			})
		},
		getExtCvList: function(client_id) {
			return $http({
				url: '/sem/new/api/extcv/index/' + client_id,
				method: 'GET',
			})
		},
		delTemplate: function(template_id) {
			return $http({
				url: '/sem/new/axis/report/deltemplate.json',
				method: 'POST',
				data: template_id,
			})
		},
		setTemplate: function(data) {
			return $http({
				url: '/sem/new/axis/report/instemplate.json',
				method: 'POST',
				data: data,
			})
		},
		getReportdata : function(data) {
			var deferred = $q.defer();
			$http.post('/sem/new/axis/export/display', data).success(function(data) {
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
			$http.post('/sem/new/axis/report/export', data).success(function(data) {
				deferred.resolve(data);
			}).error(function (data, status, headers, config) {
				if(status == 404){
					alert("エラーが発生しました。");
				}
			});
			return deferred.promise;
		},

		getReportElem : function(data) {
			var deferred = $q.defer();
			$http.post('/sem/new/axis/export/reportelem', data,{ignoreLoadingBar: true}).success(function(data) {
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

services.service('validateService', function(filtersService) {
	return {
		isValidForm: function (scope,isSkipAccountCheck) {

			if(!isSkipAccountCheck){
				// クライアント選択チェック
				if (_.isEmpty(scope.clientCombobox.client)) {
					scope.message = 'クライアントが選択されていません。';
					return false;
				}
				// アカウント選択チェック
				if (_.isEmpty(scope.clientCombobox.accounts)) {
					scope.message = 'アカウントが選択されていません。';
					return false;
				}

				// カテゴリ選択チェック
				if (scope.params.summary_type==='category_big'||scope.params.summary_type==='category_middle'||scope.params.summary_type==='category') {
					if(_.isEmpty(scope.clientCombobox.categoryGenre)){
						scope.message = 'カテゴリが選択されていません。';
						return false;
					}
				}
			}

			// レポート種別選択チェック
			if (_.isEmpty(scope.params.report_type)) {
				return false;
			}

			// サマリ単位選択チェック
			if (_.isEmpty(scope.params.summary_type)) {
				return false;
			}

			// 指標選択チェック
			if( _.values(scope.params.elem_list).indexOf(true) === -1 ){
				scope.message = '指標を選択してください。';
				return false;
			}

			// 外部CV選択数チェック
			if (scope.params.ext_cv_list.length > 0) {
				if (scope.params.report_format === 'summary' && scope.params.ext_cv_list.length > 30) {
					scope.message = 'レポート形式が期間サマリの場合、外部CVは30個まで選択できます。';
					return false;
				} else if (scope.params.report_format !== 'summary' && scope.params.ext_cv_list.length > 10) {
					scope.message = 'レポート形式が期間サマリ以外の場合、外部CVは10個まで選択できます。';
					return false;
				}
			}
			// フィルタリング項目重複チェック
			angular.forEach (scope.params.filters, function (obj) {
				settled = filtersService.isSettingFilter(scope, obj['filter_item']);
				if (settled) {
					scope.message = 'フィルタリング設定に重複した項目があります。';
					return false;
				}
			});

			// 期間選択チェック
			var is_valid = scope.params.termdate.is_valid;
			if (!is_valid) {
				scope.message = scope.params.termdate.msg;
			}

			return is_valid;
		}
	};
});

services.factory('filtersService', function () {
	return {
		// サマリ単位によりフィルタ項目の表示を制御
		isShowFilter: function (scope, filter_item) {
			var summary_type = scope.params.summary_type;

			if (filter_item === 'campaign_id' || filter_item === 'campaign_name') {
				if (summary_type !== 'media' && summary_type !== 'account' && summary_type !== 'category_big' && summary_type !== 'category_middle' && summary_type !== 'category') {
					return true;
				}
			} else if (filter_item === 'ad_group_id' || filter_item === 'ad_group_name') {
				if (summary_type !== 'media' && summary_type !== 'account' && summary_type !== 'campaign' && summary_type !== 'category_big' && summary_type !== 'category_middle' && summary_type !== 'category') {
					return true;
				}
			} else if (filter_item === 'keyword_id' || filter_item === 'keyword') {
				if (summary_type === 'keyword') {
					return true;
				}
			} else if (filter_item === 'match_type') {
				if (summary_type === 'keyword' || summary_type === 'query') {
					return true;
				}
			} else if (filter_item === 'ad_id' || filter_item === 'title' || filter_item === 'description') {
				if (summary_type === 'ad') {
					return true;
				}
			} else if (filter_item === 'link_url') {
				if (summary_type === 'keyword' || summary_type === 'ad') {
					return true;
				}
			} else if (filter_item === 'domain') {
				if (summary_type === 'domain' || summary_type === 'url') {
					return true;
				}
			} else if (filter_item === 'url') {
				if (summary_type === 'url') {
					return true;
				}
			} else if (filter_item === 'query') {
				if (summary_type === 'query') {
					return true;
				}
			} else if (filter_item === 'category_big_name') {
				if (summary_type === 'category_big' || summary_type === 'category_middle' || summary_type === 'category') {
					return true;
				}
			} else if (filter_item === 'category_middle_name') {
				if (summary_type === 'category_middle' || summary_type === 'category') {
					return true;
				}
			} else if (filter_item === 'category_name') {
				if (summary_type === 'category') {
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
				if (filter_item === obj['filter_item']) {
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
				if (obj['filter_item'] === 'match_type') {
					if (obj['filter_cond'] === '') {
						ret = false;
					}
				} else {
					angular.forEach (obj, function (value, key) {
						if (value === '') {
							ret = false;
						}
					})
				}
			});
			if (!ret) { return false; }

			filters[filters.length] = {filter_item: '', filter_cond: '', filter_text: ''};
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
			(i == 'all') ? scope.params.filters = [] :scope.params.filters[i] = {filter_item: '', filter_cond: '', filter_text: ''};
		},
		toJson: function (filters) {
			var filter_list = [];
			angular.forEach(filters, function(value, key) {
				filter_list[key] = { filter_item: value.filter_item, filter_cond: value.filter_cond, filter_text: value.filter_text };
			});
			return filter_list;
		},
	};
});


services.factory('reportFiltersService', function () {
	return {
		isShowFilter: function (scope, filter_item) {

			// 外部CV選択時のみ
			if (filter_item.match('^ext_')) {
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
				if (filter_item === obj['filter_item']) {
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
					if (obj['filter_max'] === '' && obj['filter_min'] === '') {

						ret = false;

					}
				})
			});
			if (!ret) { return false; }

			filters[filters.length] = {filter_item: '', filter_min: '', filter_max: ''};
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
			scope.params.report_filters[i] = {filter_item: '', filter_min: '', filter_max: ''};
		}
	};
});


services.factory('paramsFactoryService', ['filtersService', function(filtersService) {
	return {
		getParams: function (scope) {
			var client_id  = _.isEmpty(scope.clientCombobox.client) ? null : scope.clientCombobox.client.id;
			var start_date = [];
			var end_date   = [];

			_.each(scope.params.termdate.term_arr, function(obj,i){
				start_date.push(moment(obj['from']).format("YYYY/MM/DD"))
				end_date.push(moment(obj['to']).format("YYYY/MM/DD"))
			});


			var params = {
				ssClient:           client_id, 
				ssAccount:          scope.clientCombobox.accounts,
				ssCategoryGenre:    (scope.clientCombobox.categoryGenre && scope.params.summary_type.indexOf('category') >= 0) ? scope.clientCombobox.categoryGenre.id : null,
				report_format:      scope.params.report_format,
				term_set:           scope.params.termdate.settings.term_set,
				term_compare_set:   scope.params.termdate.settings.term_compare_set,
				device_type:        scope.params.device_type,
				media_cost:         scope.params.media_cost,
				report_type:        scope.params.report_type,
				summary_type:       scope.params.summary_type,
				option_list:        { only_has_click: scope.params.only_has_click, only_has_conv: scope.params.only_has_conv },
				ext_cv_list:        scope.params.ext_cv_list,
				term_count:         scope.params.termdate.term_count,
				start_date:         start_date,
				end_date:           end_date,
				export_type:        scope.params.export_type,
				export_format:      scope.params.export_format,
				report_name:        scope.params.report_name,
				filter_list:        scope.params.filters,
				elem_list:          scope.params.elem_list,
				id:                 '',
				template_name:      scope.params.template_name,
				report_term:        scope.params.report_format === 'term_compare' ? scope.params.termdate.settings.term_compare_set : scope.params.termdate.settings.term_set,
				template_memo:      scope.params.template_memo ? scope.params.template_memo : '',
				send_mail_flg:      scope.params.send_mail_flg
			};
			return params;
		},

		/**
		 * 粒度移動のparamsを生成して返す
		 * 
		 */
		getGranularityParams: function (data,granularityData,scope) {
			scope.params.filters = [{filter_cond : '',filter_item : '',filter_text : ''}];

			switch (granularityData.name){
				case 'return_all_selected_category':
					data.summary_type            = 'category_big';
					scope.params.summary_type    = 'category_big';
				break;

				case 'return_all_selected_account':
					data.summary_type            = 'account';
					scope.params.summary_type    = 'account';
				break;

				case 'category_big_name':
					data.summary_type            = 'category_middle';
					scope.params.summary_type    = 'category_middle';
					scope.params.filters = [{
						filter_cond : "or_exact",
						filter_item : "category_big_name",
						filter_text : granularityData.category_big_text
					}];

				break;

				case 'category_middle_name':
					data.summary_type            = 'category';
					scope.params.summary_type    = 'category';
					scope.params.filters[0] = {
						filter_cond : "or_exact",
						filter_item : "category_big_name",
						filter_text : granularityData.category_big_text
					};

					scope.params.filters[1] = {
						filter_cond : "or_exact",
						filter_item : "category_middle_name",
						filter_text : granularityData.category_middle_text
					};
				break;

				case 'account_name':
					data.ssAccount = _replaceSsAccount(granularityData);
					data.summary_type            = 'campaign';
					scope.params.summary_type    = 'campaign';
					_replaceSsAccount(granularityData);
				break;

				case 'campaign_name':
					data.ssAccount = _replaceSsAccount(granularityData);
					data.summary_type             = 'ad_group';
					scope.params.summary_type     = 'ad_group';
					scope.params.filters = [{
						filter_cond : "or_exact",
						filter_item : "campaign_id",
						filter_text : granularityData.info.campaign
					}];

				break;

				case 'ad_group_name':
					data.ssAccount = _replaceSsAccount(granularityData);
					data.summary_type            = 'keyword';
					scope.params.summary_type    = 'keyword';

					scope.params.filters[0] = {
						filter_cond : "or_exact",
						filter_item : "campaign_id",
						filter_text : granularityData.info.campaign
					};

					scope.params.filters[1] = {
						filter_cond : "or_exact",
						filter_item : "ad_group_id",
						filter_text : granularityData.info.ad_group
					};

				break;
				default:;break;
			}
			data.filter_list = scope.params.filters
			return data;

			function _replaceSsAccount(granularityData) { 
				var res  = [{
					account_id : granularityData.info.account,
					media_id   : granularityData.info.media
				}];
				return res;
			}
		}
	};
}]);


services.factory('formFactoryService', ['filtersService','extCvShared', function(filtersService,extCvShared) {
	return {
		/**
		 *  外部CV is_showがtrueのオブジェクトだけ返す
		 */
		getExtCvList: function (arr) {
			ext_cv_list = []
			_.each(arr, function(obj,i){
				if(obj['is_show']===true) ext_cv_list.push(obj)
			});
			return ext_cv_list;
		},
		resetExtCvList: function (scope) {
			scope.params.ext_cv_list = [];
			extCvShared.ext_cv_list.set(scope.ext_cv_list);
		},
		moveMultipleExtCv : function (_this, target) {
			_.each(_this.models[target], function(obj,i){
				if(obj['is_search_result'] === true) {
					if(target === 'available') _this.addItem(i);
					if(target === 'selected')  _this.removeItem(i);
				}
			});
		},
		/**
		 *  配列内オブジェクトの指定keyを全て引数valに変更して返す
		 */
		changeObjVal : function (arr, key ,val) {
			var res = []
			_.each(arr, function(obj,i){
				obj[key] = val;
				res[i] = obj
			});
			return res;
		},
		/**
		 *  外部CV オブジェクトのis_showを引数boolにis_search_resultをtrueにして返す
		 */
		toggle :function (arr, bool) {
			var res = []
			_.forEach(arr, function (val,i) { 
				val.is_show           = bool;
				val.is_search_result  = true;
				res[i] = val;
			});
			return res;
		}
	};
}]);


services.factory('tableFactoryService', ['axisConst','utilFactoryService',function(axisConst,utilFactoryService){
	return {
		/**
		 *  日付のformat変換
		 */
		 getFormat: function (format,data) {
			if(format=='term_compare') {
				if(data.indexOf('diff')!=-1){
					data = '差分'
				}else{
					data = data.substr(5).replace('__','-').replace(/_/g,'/');
				}
			}

			if(format=='daily') {
				data  = data.substr(5).replace(/_/g,'-');
				data += moment(data).format('(dd)');
				data  = data.replace(/-/g,'/');
			}
			return data;
		},

		/**
		 *  colspan,rowspanの選定
		 */
		 getCell: function (format,key) {
			if(format !== 'summary'){
				return {colspan:'1',rowspan:'1'};

			}else if(key.indexOf('ext') > -1){
				return {colspan:'3',rowspan:'1'};

			}else{
				return {colspan:'1',rowspan:'2'};

			}
		},

		/**
		 *  2番目以降から、差分データを間に挟んで結合させる関数
		 */
		 arrUnion: function (masterArr,diffArr) {
			var j = k = 0, arr = [];
			for (var i = 0; i < masterArr.length + diffArr.length; i++) {
				if(i % 2 != 0 || i < 2){
					arr[i] = masterArr[k]; k++;
				}else{
					arr[i] = diffArr[j]; j++;
				}
			};
			return arr;
		},

		/**
		 *  objectのng-repeatは表示順番が変わってします為、配列に変換
		 */
		 objOrder: function (object) {
			var array = [];
			angular.forEach(object, function (value, key) {
				array.push({key: key, value: value});
			});
			return _.uniq(array);
		},

		/**
		 * 配列の中にあるobjectの指定valueと引数idが一致したらそのobjectを返す
		 */
		getHasObject: function (arr,id,str) {
			var res = {};
			_.each(arr, function(value){
				if(value[str] == id) {
					res = value;
				}
			});
			return res;
		},
		/**
		 * 引数のstrの文字列を含むオブジェクトは除外
		 */
		withoutKeyByStr: function (obj,str) {
			var res = {};
			_.each(obj, function(value,key){
				if(key.indexOf(str) == -1) res[key] = value;
			});
			return res;
		},
		/**
		 * サマリーテーブルヘッドのセル名 
		 * 外部CVにはindexを付与
		 */
		getCellName: function (format,cellName,arr) {
			if(format == 'summary') {
				var i = (cellName === 'ext_all') ? 'all' : $.inArray(cellName, arr);
				cellName = cellName.indexOf('ext') > -1 ? 'parent_excv_cell_'+i : cellName;
			}
			return cellName;
		},

		/**
		 * サマリーテーブル 外部CVshow,hideによる親セルのcolspan制御
		 * 
		 */
		getExtCvColspan: function (scope,ext_elm) {
			var colspan = 0;
			_.each(scope.isShow.basic, function(value,key){
				if(key.indexOf(ext_elm[0]) != -1  && key.indexOf(ext_elm[2]) != -1  && value == true){
					colspan++
				}
			});
			return colspan;
		},
		/**
		 * テーブルヘッドの表示名
		 * replace_listにある場合は置換
		 * 土,日曜日を<span></span>で囲む
		 */
		getViewName: function (format,viewName,scope) {

			viewName = _.has(scope.replace_list, viewName) ? _.pick(scope.replace_list,viewName)[viewName] : viewName;
			if(format == 'daily'){

				if(viewName.indexOf('(土)') >= 1){
					var arr  = viewName.split('(土)')
					viewName = arr[0] + '(<span class="saturday">土</span>)';

				}else if(viewName.indexOf('(日)') >= 1){
					var arr  = viewName.split('(日)')
					viewName = arr[0] + '(<span class="sunday">日</span>)';
				}
			}
			return viewName;
		},
		/**
		 * 引数に画像ファイルが含まれているか
		 * 
		 */
		hasImg: function (data) {
			var hasImg = false;
			if(!_.isUndefined(data) && !_.isNull(data)) {
				_.each(['.gif','.png','.jpeg','.jpg'], function(value){
					if(data.toLowerCase().indexOf(value) >= 0) hasImg = true;
				});
			}
			return hasImg;
		},
		/**
		 * 数値であるべき値は数値に変換
		 * 
		 */
		convertNumber: function (target) {

			_.each(target, function(json,device){
				_.each(json, function(obj,i){
					_.each(obj, function(val,key){
						target[device][i][key] = ( utilFactoryService.isNumber(val) ) ? parseFloat(val): val;
					});
				});
			});

			return target;
		},
		/**
		 * 元データから引数keyの全てのvalueをオブジェクトにして返す
		 * 
		 */
		getValueByKey: function (data,key) {
		 	var res = {};
			_.each(data, function(json,device){
				_.each(json, function(obj,i){
					if(!_.isUndefined(obj[key])) res[obj[key]] = obj[key]
				});
			});
			return res;
		}
	};
}]);


services.factory('extCvShared', ['$rootScope', function($rootScope) {
	var ext_cv_list   = [];
	return {
		ext_cv_list: {
			get: function() { return ext_cv_list; },
			set: function(arr) {
					ext_cv_list = arr;
					$rootScope.$broadcast('changedExtCvList');
				 }
			}
		}
}]);

services.factory('extCvTmplShared', ['$rootScope', function($rootScope) {
	var t_ext_cv_list   = [];
	return {
		t_ext_cv_list: {
			get: function() { return t_ext_cv_list; },
			set: function(arr) {
					t_ext_cv_list = arr;
					$rootScope.$broadcast('changedExtCvTmplList');
				 }
			}
		}
}]);


services.factory('graphFactoryService', [function() {
	return {
		getInitElm: function (scope) {
			var res =  {};
			res.y   = (_.has(scope.async_data.report_elem_list,'imp'))  ? 'imp' : scope.table.views.report_elem_list[0]['key'];
			if(!_.isEmpty(scope.table.views.report_elem_list[1])) res.y2 =  (_.has(scope.async_data.report_elem_list,'cost')) ? 'cost' : scope.table.views.report_elem_list[1]['key'];

			return res;
		}
	}
}]);


services.factory('utilFactoryService', ['axisConst',function(axisConst){
	return {
		getFormat: function (elm) {
			var res = {
				yen    :'',
				percent:''
			};
			if(typeof elm === 'undefined') return res;

			for (var key in axisConst.markList) {
				if (elm==key || (elm.indexOf('ext') >= 0 && elm.indexOf(key) >= 0) ) {
					if (axisConst.markList[key] == '%') var name ='percent';
					if (axisConst.markList[key] == '¥') var name = 'yen';
					res[name] = axisConst.markList[key];
				}
			}

			return res;
		},
		/**
		 *  number判定 
		 */
		isNumber : function (x) {
			if( typeof(x) != 'number' && typeof(x) != 'string' ){
				return false;
			}else{ 
				return (x == parseFloat(x) && isFinite(x));
			}
		},
	}
}]);
