/* Controllers */
var controllers = angular.module('axis.controllers', [])
controllers.controller('BaseCtrl', ['$scope', '$routeParams', function ($scope, $routeParams) {
}]);

/**
 * 「レポート」画面
 */
controllers.controller('ReportCtrl', ['$scope', '$routeParams', 'axisRestApi','extCvShared', 'appConst',function ($scope, $routeParams, axisRestApi,extCvShared,appConst) {

	// クライアントコンボボックス定義
	$scope.clientCombobox = {};
	$scope.clientComboboxConfig = {
// CPN,ADGレポートを移行するまで、D2C&X-Listingは対象外
		focusMediaIds: [1,2,3],
		registerApi : function(api){
			$scope.comboboxApi = api;
		}
	};

	// 画面設定Obj
	$scope.settings = {
		is_report_display: false,
	};
	if (window.location.pathname.indexOf('display') >= 0) {
		$scope.settings.is_report_display = true;
	}

	// 初期化
	$scope.appConst      = appConst;
	$scope.params        = {};
	$scope.message = '';
	$scope.isShowTables  = false;
	$scope.is_processing = false;

	// クライアント変更検知用
	$scope.pre_client_id  = '';

	// テンプレート一覧取得
	$scope.getTemplateList = function (client_id) {
		axisRestApi.getTemplateList(client_id).then(function(res) {
			if (!_.isEmpty(res.data)) {
				$scope.template_list = res.data;
			} else {
				$scope.template_list = null;
			}
		}, function(error) {
			$scope.message = 'テンプレートの取得に失敗しました。';
		});
	};

	// 外部CV一覧取得
	$scope.getExtCvList = function (client_id) {
		axisRestApi.getExtCvList(client_id).then(function(res) {
			if (!_.isEmpty(res.data)) {
				var tmp_extcv_list = [];
				angular.forEach(res.data.ext_cv, function (value, key) {
					tmp_extcv_list[key] = { cv_key: value.cv_key, cv_display: value.cv_display };
				});
				$scope.ext_cv_list = tmp_extcv_list;
				extCvShared.ext_cv_list.set($scope.ext_cv_list);
			} else {
				$scope.ext_cv_list = [];
			}
		}, function(error) {
			$scope.message = '外部CVの取得に失敗しました。';
		});
	};

	// ダウンロード
	$scope.download = function (id) {
		if (id) {
			window.location = '/sem/new/axis/export/download/' + id;
		}
	};
}]);


/**
 * レポート作成画面　フォーム部分
 */

controllers.controller('ReportFormCtrl', ['$scope', '$modal', 'axisRestApi', 'validateService', 'filtersService', 'axisConst','tableFactoryService','paramsFactoryService','$timeout','graphFactoryService','formFactoryService',
	function ($scope, $modal, axisRestApi, validateService,filtersService,axisConst,tableFactoryService,paramsFactoryService,$timeout,graphFactoryService,formFactoryService) {
	var _this = this;
	// 初期値
	$.extend($scope.params, {
		report_format: 'summary',
		device_type: '0',
		media_cost: 20,
		report_type: 'component',
		summary_type: 'account',
		only_has_click: '',
		only_has_conv: '',
		ext_cv_list: [],
		termdate: {
			getReportType: function () {
				return $scope.params.report_format;
			}
		},
		export_type: 'display',
		export_format: 'tsv',
		report_name: '',
		template_name: '',
		template_memo: '',
		send_mail_flg: false,
		filters: [],
		filters_is_open: [
			{ filter_item: false, filter_cond: false, filter_text: false }
		],
		report_filters: [{'filter_item':'','filter_min':'','filter_max':''}],
		report_filters_is_open: [
			{ filter_item: false, filter_max: false, filter_min: false }
		],
	});
	$scope.termdate_config = {
		title: '集計期間',
		datepicker: {
			format: 'yyyy/MM/dd'
		}
	};
	$scope.master = {
		summary     :{},
		daily       :{},
		term_compare:{}
	}

	$scope.async_data = {
		summary     :{},
		daily       :{},
		term_compare:{}
	}

	$scope.report_elem_basic_list = tableFactoryService.objOrder( axisConst.commonValues );


	// サマリ単位切替時にエレメントリストを初期化
	$scope.initElemList = function () {
		$scope.params.elem_list = {}
		_.each((axisConst.commonValues), function(value,key){
			$scope.params.elem_list[key] = true;
		});
	};
	$scope.initElemList();

	// サマリ単位切替時にフィルタリングリセット
	$scope.resetFilter = function () {
		filtersService.resetFilter($scope);
	};
	$scope.resetExtCv = function () {
		// 外部CV選択不可時には選択済みリセット
		if ($scope.params.report_type !== 'component' || $scope.params.summary_type =='ad') {
			formFactoryService.resetExtCvList($scope);
		}
	};

	// Validate
	$scope.isValidForm = function (isSkipAccountCheck) {
		return validateService.isValidForm($scope,isSkipAccountCheck);
	};


	// サーバーへリクエスト送信
	$scope.submit = function (isMovingGranularity,granularityData) {
		var isSkipAccountCheck = false;

		// Validate
		if (!$scope.isValidForm(isSkipAccountCheck)) return false;

		$scope.is_processing = true;
		var data = paramsFactoryService.getParams($scope);

		if(isMovingGranularity) data = paramsFactoryService.getGranularityParams(data,granularityData,$scope);


		if($scope.params.export_type === 'export'){
			axisRestApi.getExportdata(data).then(function(res) {
				console.log(res);

				if(!res.error){
					$scope.message = 'レポートの作成を開始しました。';
				}else{
					$scope.message = res.error;
				}
				$scope.is_processing  = false;
			});
			return false;
		}


		axisRestApi.getReportdata(data).then(function(res) {
			//console.log(res);
			$scope.is_processing  = false;

			if(!res.error){
				$scope.tableActions.deleteTables();
				$scope.async_data = res;
				$scope.setTables();

				if(data.report_format === 'summary') {
					//数値型のものは数値変換 
					$scope.async_data['summary'].device = tableFactoryService.convertNumber($scope.async_data['summary'].device);
					//絞り込みのitemをセット
					$scope.setFilterItems($scope.async_data['summary'].device);
				}
				$scope.master = angular.copy($scope.async_data);
				$scope.isShowTables  = true;
				window.scrollTo(0,0);

				$timeout(function() {
					if(data.report_format ==='summary') { 
						$scope.orderBy('all_devices','cost');
					}else if(data.report_format ==='daily')   {
						var graphElm = graphFactoryService.getInitElm($scope);
						$scope.setGraph(graphElm); 
					}
					$scope.initControlPositionFixed();
				});

			}else{
				$scope.message = res.error;
			}
		});
	};



	// テンプレートとして保存
	$scope.save = function () {
		// 保存用にモデルをコピー
		$scope.ins_param = {
			report_name: $scope.params.report_name,
			send_mail_flg: $scope.params.send_mail_flg
		};

		$modal.open({
			templateUrl: 'tmpl_saveTemplate',
			controller: 'TemplateCtrl',
			scope: $scope
		});
	};
}]);

/**
 *  レポート作成画面　テンプレート一覧　コントローラー
 */
controllers.controller('ReportTemplateCtrl', ['$scope', 'axisRestApi', 'filtersService','extCvTmplShared', function ($scope, axisRestApi, filtersService,extCvTmplShared ) {
	$scope.params.selected_id = null;

	// クライアント選択時にテンプレート一覧取得
	$scope.$on('ss_combobox_client_change', function(event, data) {
		if ($scope.clientCombobox.client.id !== $scope.pre_client_id) {
			var client_id = { 'client_id': $scope.clientCombobox.client.id };
			$scope.getTemplateList(client_id);
		}
		$scope.pre_client_id = $scope.clientCombobox.client.id;
	});

	$scope.applyTemplate = function (i) {
		$scope.params.selected_id = i;
		var t = $scope.template_list[i];

		// アカウントを適用
		var models = {
			client:$scope.clientCombobox.client.id,
			categoryGenre:t.category_genre_id,
			accounts:[]
		};
		angular.forEach(t.account_list, function (value, key) {
			models.accounts.push({ media_id: parseInt(value.media_id), account_id: value.account_id });
		});
		$scope.comboboxApi.setModels(models);

		$scope.params.template_name = t.template_name;
		$scope.params.report_format = t.report_format.value;
		$scope.params.report_type   = t.report_type.value;
		$scope.params.summary_type  = t.summary_type.value;
		$scope.params.device_type   = t.device_type;
		$scope.params.media_cost    = t.media_cost;

		// オプションを適用
		angular.forEach(t.option_list, function (value, key) {
			$scope.params[key] = value;
		});

		//外部CVを適用
		extCvTmplShared.t_ext_cv_list.set(t.ext_cv_list);

		// 期間設定を適用
		$scope.params.termdate.method.applyPresetTerms(t.term_count, t.report_term.value);

		// フィルタを適用
		filtersService.resetFilter($scope);
		angular.forEach(t.filter_list, function (value, key) {
			$scope.params.filters[key] = value;
		});

		if (t.report_name) {
			$scope.params.export_type   = 'export';
			$scope.params.export_format = t.export_format;
			$scope.params.report_name   = t.report_name;
			$scope.params.send_mail_flg = t.send_mail_flg;
		}
	};

	$scope.isSelected = function (i) {
		return $scope.params.selected_id === i;
	};

	// angular-ui で popover 開くときに、$scope.applyTemplate を起動させない
	$scope.info = function ($event) {
		$event.stopPropagation();
	};

	// テンプレート削除
	$scope.delete = function ($event, i) {
		$event.stopPropagation();

		var msg = '※注意※\n削除すると元には戻せませんが、よろしいですか？\n\nテンプレート名：';
		if (window.confirm(msg + $scope.template_list[i].template_name)) {
			var template_id = { 'template_id': $scope.template_list[i].id };
			axisRestApi.delTemplate(template_id).then(function(res) {
				var client_id = { 'client_id': $scope.clientCombobox.client.id };
				$scope.getTemplateList(client_id);
			}, function(error) {
				$scope.message = 'テンプレートの削除に失敗しました。';
			});
			$scope.params.selected_id = null;
		}
	};
}]);

/**
 * テンプレート追加　モーダルウインドウ　コントローラー
 * ※ $modalInstance.close したいのでコントローラー分けてます
 */
controllers.controller('TemplateCtrl', ['$scope', '$modalInstance', 'axisRestApi', 'filtersService','paramsFactoryService',
 function ($scope, $modalInstance, axisRestApi, filtersService,paramsFactoryService) {

	$scope.$watch('params.selected_id', function(newTemp, oldTemp, scope) {
		// テンプレート選択時
		if (newTemp !== null) {
			$scope.is_update = true;
			// テンプレート名をセット
			$scope.params.template_name = $scope.template_list[$scope.params.selected_id].template_name;

			// テンプレート名が変更された場合、新規保存
			$scope.$watch('params.template_name', function(newValue, oldValue, scope) {
				if (newValue !== oldValue) {
					scope.is_update = false;
				}
			});
		} else {
			scope.is_update = false;
		}
	});

	// 入力チェック
	$scope.$watch(function() {
		$scope.is_valid = false;
		if ($scope.clientCombobox.client && $scope.clientCombobox.accounts.length > 0) {
			$scope.is_valid = true;
		}
	});

	// テンプレート新規保存
	$scope.insert = function () {
		console.log('insert');

		// テンプレ保存後、レポート出力するかもなのでメインフォームにセット
		$scope.params.report_name   = $scope.ins_param.report_name;
		$scope.params.send_mail_flg = $scope.ins_param.send_mail_flg;

		var data = paramsFactoryService.getParams($scope);

		$scope.is_processing = true;

		axisRestApi.setTemplate(data).then(function(res) {
			var client_id = { 'client_id': $scope.clientCombobox.client.id };
			$scope.getTemplateList(client_id);
			$scope.is_processing = false;
		}, function(error) {
			$scope.message = 'テンプレートの設定に失敗しました。';
		});
		$modalInstance.close();
	};

	// テンプレート上書き保存
	$scope.update = function () {
		console.log('update');

		// テンプレ保存後、レポート出力するかもなのでメインフォームにセット
		$scope.params.report_name   = $scope.ins_param.report_name;
		$scope.params.send_mail_flg = $scope.ins_param.send_mail_flg;

		var data    = paramsFactoryService.getParams($scope);
			data.id = $scope.template_list[$scope.params.selected_id].id

		$scope.is_processing = true;

		axisRestApi.setTemplate(data).then(function(res) {
			var client_id = { 'client_id': $scope.clientCombobox.client.id };
			$scope.getTemplateList(client_id);
			$scope.is_processing = false;
		}, function(error) {
			$scope.message = 'テンプレートの更新に失敗しました。';
		});
		$modalInstance.close();
	};
}]);

/**
 * レポート作成画面　出力結果部分　コントローラー
 */
controllers.controller('ReportViewCtrl', ['$scope', '$rootScope', function ($scope, $rootScope) {

	// レポート出力画面は、$routeProvider を経由しないので、手動でセット
	$rootScope.mainTab = 'report';

	$scope.back = function () {
		window.history.back();
	};
}]);

/**
 *  レポートフィルタリング選択フォーム コントローラー
 */
controllers.controller('FormReportFilterCtrl', ['$scope', 'reportFiltersService', function ($scope, reportFiltersService) {

	$scope.is_showReportFilter = function (filter_item) {
		return reportFiltersService.isShowFilter($scope, filter_item);
	};

	$scope.addReportFilter = function () {
		reportFiltersService.addFilter($scope);
	};

	$scope.deleteReportFilter = function (i) {
		reportFiltersService.deleteFilter($scope, i);
	};

	$scope.clearReportFilter = function (i) {
		reportFiltersService.clear($scope, i);
	};
}]);



/**
 *  フィルタリング選択フォーム コントローラー
 */
controllers.controller('FormFilterCtrl', ['$scope', 'filtersService', function ($scope, filtersService) {

	var _this = this;

	_this.filters = {
		match_type:{
			is_disabled :[]
		}
	}

	_this.ctrlDisabled = function (i) {
		if($scope.params.filters[i].filter_item.indexOf('_id') >= 0){
			$scope.params.filters[i].filter_cond = 'or_exact';
			_this.filters.match_type.is_disabled[i]  = true;
		}else{
			_this.filters.match_type.is_disabled[i]  = false;
		}
	};

	_this.is_showFilter = function (filter_item) {
		return filtersService.isShowFilter($scope, filter_item);
	};

	_this.addFilter = function () {
		filtersService.addFilter($scope);
	};

	_this.deleteFilter = function (i) {
		filtersService.deleteFilter($scope, i);
	};

	_this.clearFilter = function (i) {
		filtersService.clear($scope, i);
		_this.filters.match_type.is_disabled[i]  = false;
	};
}]);


controllers.controller('ExtCvCtrl',['$scope','extCvShared','extCvTmplShared','$filter','$timeout','formFactoryService',
	function($scope,extCvShared,extCvTmplShared,$filter,$timeout,formFactoryService) {

	// クライアント選択時に外部CV一覧取得
	$scope.$on('ss_combobox_client_change', function(event, data) {
		if ($scope.clientCombobox.client.id !== $scope.pre_client_id) {
			$scope.getExtCvList($scope.clientCombobox.client.id);
		}
		$scope.pre_client_id = $scope.clientCombobox.client.id;
	});

	var _this = this;

	_this.models =  {
		available: [],
		selected : [],
	};

	_this.moveMultiple = function (target) {
		formFactoryService.moveMultipleExtCv(_this,target);
		_this.models.available = formFactoryService.changeObjVal(_this.models.available,'is_search_result',true);
		_this.models.selected  = formFactoryService.changeObjVal(_this.models.selected,'is_search_result',true);
		_this.filterSelected   = '';
		_this.filterAvailable  = '';
		$scope.params.ext_cv_list         = formFactoryService.getExtCvList(_this.models.selected);
	};

	_this.clear = function () {
		_this.models.master               = formFactoryService.toggle(_this.models.master, true);
		_this.models.available            = formFactoryService.toggle(_this.models.available, true);
		_this.models.selected             = formFactoryService.toggle(_this.models.selected, false);
		$scope.params.ext_cv_list         = formFactoryService.getExtCvList(_this.models.selected);
	};

	_this.addItem = function (i) {
		_this.models.available[i].is_show = false;
		_this.models.selected[i].is_show  = true;
		$scope.params.ext_cv_list         = formFactoryService.getExtCvList(_this.models.selected);
	};

	_this.removeItem = function (i) {
		_this.models.available[i].is_show = true;
		_this.models.selected[i].is_show  = false;
		$scope.params.ext_cv_list         = formFactoryService.getExtCvList(_this.models.selected);
	};

	$scope.$on('changedExtCvList', function() {
		_this.filterSelected   = '';
		_this.filterAvailable  = '';
		_this.models.master    = extCvShared.ext_cv_list.get();
		_this.models.available = angular.copy(_this.models.master);
		_this.models.selected  = angular.copy(_this.models.master);
		_this.clear();
	});

	$scope.$on('changedExtCvTmplList', function() {
		var t_ext_cv_list   = extCvTmplShared.t_ext_cv_list.get();
		_this.clear();
		for (var i=0; i<_this.models.master.length; i++) {
			for (var j=0; j<t_ext_cv_list.length; j++) {
				if (_this.models.master[i].cv_key === t_ext_cv_list[j].cv_key) {
					_this.models.available[i].is_show = false;
					_this.models.selected[i].is_show  = true;
					break;
				}
			}
		}
		$scope.params.ext_cv_list = formFactoryService.getExtCvList(_this.models.selected);
		if(t_ext_cv_list.length >= 1) {$('#accordion_ext_cv').collapse('show')};
	});


	_this.selectQuery = function(is_selected) {
		$timeout.cancel($scope.timeid);
		$scope.timeid = $timeout(function() {
			var filterText            = (is_selected) ? _this.filterSelected : _this.filterAvailable;
			var ext_cv_list           = (is_selected) ? 'selected': 'available';
			_this.models[ext_cv_list] = $filter('muitipleFilter')(_this.models[ext_cv_list],filterText,is_selected);
		}, 350);
	};

	_this.clear();

}]);
