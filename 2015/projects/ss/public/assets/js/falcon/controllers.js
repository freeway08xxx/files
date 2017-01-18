/* Controllers */
var ctrl = angular.module('falcon.controllers', []);

/* 共通 */
ctrl.controller('FalconCommonCtrl',
	['$scope', 'falconSharedStore', 'falconSharedData', 'falconReportData',
	function($scope, falconSharedStore, falconSharedData, falconReportData) {

	var _this = this;

	_this.models = falconSharedStore.models;
	_this.config = falconSharedStore.config;

	_this.clientComboboxConfig = {
		registerApi: function (api) {
			_this.models.comboboxApi = api;
		}
	};

	/**
	 * Common ViewMethod
	 */
	_this.getReportTab = function () {
		return falconReportData.tab.get();
	};

	/**
	 * クライアント別の設定を再取得
	 */
	$scope.$on(_this.config.bs_name.chgClient, function() {
		falconSharedData.config.get('client');
	});

	/**
	 * init
	 */
	console.info("start CommonCtrl");
	$scope.$broadcast(_this.config.bs_name.init);
}]);


/* レポート */
ctrl.controller('FalconReportCtrl',
	['$scope', '$location', 'ssModal', 'falconReportStore', 'falconSharedData', 'falconReportData',
	function($scope, $location, ssModal, falconReportStore, falconSharedData, falconReportData) {

	var _this = this;

	_this.models = falconReportStore.models.report;

	// debug
	_this.allmodels = falconReportStore.models;

	/**
	 * Report Common ViewMethod
	 */
	_this.tab = falconReportData.tab;

	_this.isSetDeviceType = function () {
		return falconReportData.format.isSetDeviceType();
	};

	_this.setAvailableSheets = function (report_type) {
		report_type = report_type || null;
		falconReportData.sheet.clear();
		falconReportData.sheet.device.set();
		falconReportData.sheet.setAvailableSheets(report_type);
	};

	/**
	 * Report ViewMethod
	 */
	_this.create = function () {
		var modalInstance = ssModal.loading('レポートを作成しています...');
		falconReportData.create()
			.then(function () {
				modalInstance.close();
				$location.path("list").search('tab', null);
			}, function (error) {
				modalInstance.close();
				console.warn(error);
			});
	};

	_this.save = function () {
		falconReportData.template.setNameToModal();

		_this.modal_instance = ssModal.custom({
			templateUrl : '/sem/new/assets/template/falcon/template-save.html',
			controller  : 'falconTemplateSaveCtrl as save',
			scope       : $scope
		});
	};

	/**
	 * init
	 */
	falconSharedData.config.get('report').then(function () {
		console.info("start ReportCtrl");

		$scope.$broadcast($scope.common.config.bs_name.initReport);

		// タブ切り替え
		_this.tab.set();

		/** watch Main Params */
		$scope.$watch(function () {
			return {
				accounts: $scope.common.models.client.accounts,
				format: {
					is_form_valid    : $scope.falconReportForm.$valid,
					summary_type     : falconReportStore.models.format.summary_type,
					category_element : falconReportStore.models.format.category_element,
					is_termdate_valid: falconReportStore.models.format.termdate.is_valid
				},
				display: falconReportStore.models.display.col_arr,
				sheet  : falconReportStore.models.sheet.selected
			};
		}, function (newval, oldval) {
			/** 必須項目の入力状況をバリデート */
			console.info('!! validate start');

			falconReportData.validate(newval).then(function () {
				console.info('-> validate success');
				falconReportStore.models.report.is_valid = true;
			}, function () {
				console.info('-> validate failed');
				falconReportStore.models.report.is_valid = false;
			});

			/** 目標設定用データを生成 */
			if (falconReportData.template.isSelected()) falconReportData.aim.getParams();

			/**
			 * toggle KW Tab Disabled
			 */
			if (!_.isEqual(newval.sheet, oldval.sheet)) {
				console.info('toggle KW TAb');
				falconReportData.kw.isActive(newval.sheet);
			}
		}, true);


		// debug setModels
		// $scope.common.models.comboboxApi.setModels({
		// 	client:"4923",
		// 	accounts:[
		// 		{media_id:1,account_id:"504990"},
		// 		{media_id:1,account_id:"31893"},
		// 		{media_id:1,account_id:"43073"},
		// 		{media_id:1,account_id:"4682"},
		// 		{media_id:2,account_id:"856-457-3165"},
		// 		{media_id:2,account_id:"986-451-6904"}
		// 	]
		// });


	});
}]);


/* レポート作成 -> レポート形式 */
ctrl.controller('FalconReportFormatCtrl',
	['$scope', '$timeout', 'falconReportStore', 'falconReportData',
	function($scope, $timeout, falconReportStore, falconReportData) {

	var _this = this;

	_this.models = falconReportStore.models.format;

	/**
	 * viewMethods
	 */
	_this.chgCommonSummary = function (val) {
		falconReportData.format.chgCommonSummary(val);
	};


	/**
	 * init
	 */
	$scope.$on($scope.common.config.bs_name.initReport, function () {
		console.info("start FormatCtrl");

		/**
		 * Wait Termdate Module Directive init
		 */
		$timeout(function () {
			$scope.$watch('format.models.report_type', function (newval) {
				var config = $scope.common.config.report.report_type;

				falconReportStore.models.sheet.active_report_type = config[newval].name;

				falconReportData.template.toggleAimTab();
			});
		});

		$scope.report.setAvailableSheets();
	});
}]);


/* レポート作成 -> 表示設定 */
ctrl.controller('FalconReportDisplayCtrl',
	['$scope', 'falconReportStore', 'falconReportData',
	function($scope, falconReportStore, falconReportData) {

	var _this = this;

	_this.models = falconReportStore.models.display;

	/**
	 * viewMethods
	 */
	_this.isDeviceOutPutEnabled = function (key) {
		return ($scope.report.isSetDeviceType() && key !== 'common');
	};
	_this.edit = function (device_key){
		var device = _.filter($scope.common.config.report.report_device_line, {'key': device_key})[0];
		falconReportData.display.initEditMode(device);
	};


	/**
	 * init
	 */
	$scope.$on($scope.common.config.bs_name.initReport, function () {
		console.info("start DisplayCtrl");

		_this.models.col_arr.common = _.filter($scope.common.config.report.report_elem, 'view_flg');
	});
}]);

/* レポート作成 -> 表示設定 -> 編集 */
ctrl.controller('FalconReportDisplayEditCtrl',
	['$scope', 'ssModal', 'falconReportDisplayEditStore', 'falconReportData',
	function($scope, ssModal, falconReportDisplayEditStore, falconReportData) {

	var _this = this;

	_this.models = falconReportDisplayEditStore.models;

	/**
	 * viewMethods
	 */
	_this.add = function (item, index) {
		falconReportData.display.edit.add(item, index);
	};

	_this.delete = function (i) {
		falconReportData.display.edit.deleteItem(i);
	};

	_this.update = function () {
		falconReportData.display.edit.update();
		falconReportData.display.toggleEditView();
	};
	_this.cancel = function () {
		falconReportData.display.edit.cancel();
		falconReportData.display.toggleEditView(false);
	};

	_this.editFormula = function (i) {
		falconReportData.display.formula.setModel(i);
		_this.openFormulaModal(true);
	};

	_this.openFormulaModal = function (is_edit) {
		is_edit = is_edit || false;
		falconReportData.display.formula.init(is_edit);

		_this.modal_instance = ssModal.custom({
			templateUrl: '/sem/new/assets/template/falcon/edit-display-formula.html',
			controller : 'FalconReportDisplayEditFormulaCtrl as formula',
			scope      : $scope,
			size       : 'lg'
		});
	};

	$scope.$on($scope.common.config.bs_name.chgClient, function() {
		_this.cancel();
	});
}]);

/* レポート作成 -> 表示設定 -> 編集 -> 数式追加 */
ctrl.controller('FalconReportDisplayEditFormulaCtrl',
	['$scope', 'falconReportDisplayEditStore', 'falconReportData',
	function($scope, falconReportDisplayEditStore, falconReportData) {

	var _this = this;

	_this.models = falconReportDisplayEditStore.models.formula;

	/**
	 * viewMethods
	 */
	_this.addElement = function (key) {
		_this.models.formula = _this.models.formula + '[' + key + ']';
	};
	_this.addSymbol = function (symbol) {
		_this.models.formula = _this.models.formula + symbol;
	};

	_this.cancel = function () {
		falconReportData.display.formula.clear();
		$scope.$dismiss('cancel');
	};

	_this.save = function () {
		falconReportData.display.formula.save();
		falconReportData.display.formula.clear();
		$scope.edit.modal_instance.close();
	};
}]);


/* レポート作成 -> シート設定 */
ctrl.controller('FalconReportSheerCtrl',
	['$scope', 'ssModal', 'falconReportStore', 'falconReportData',
	function($scope, ssModal, falconReportStore, falconReportData) {

	var _this = this;

	_this.models = falconReportStore.models.sheet;

	/**
	 * viewMethods
	 */
	_this.preview = function ($event) {
		$event.stopPropagation();
	};
	_this.add = function (i) {
		_this.models.selected[i].is_show  = true;
		_this.models.available[i].is_show = false;
	};
	_this.delete = function (i) {
		_this.models.selected[i].is_show  = false;
		_this.models.available[i].is_show = true;
	};
	_this.clear = function () {
		return falconReportData.sheet.clear();
	};
	_this.addAll = function () {
		return falconReportData.sheet.addAll();
	};
	_this.getReportType = function () {
		return falconReportStore.models.format.report_type;
	};

	_this.openCustomFormatModal = function () {
		_this.modal_instance = ssModal.custom({
			templateUrl: '/sem/new/assets/template/falcon/sheet-custom-format.html',
			controller : 'FalconReportSheetCustomFormatCtrl as customformat',
			scope      : $scope
		});
	};

	/**
	 * init
	 */
	$scope.$on($scope.common.config.bs_name.initReport, function () {
		console.info("start SheetCtrl");
	});
}]);

/* レポート作成 -> シート設定 -> カスタムフォーマット */
ctrl.controller('FalconReportSheetCustomFormatCtrl',
	['$scope', 'falconReportStore', 'falconReportData',
	function($scope, falconReportStore, falconReportData) {

	var _this = this;

	_this.models = falconReportStore.models.sheet.customformat;

	var tmpdata = _.cloneDeep(_this.models);

	/**
	 * viewMethods
	 */
	_this.isRegistered = function () {
		return !_.isEmpty(_this.models.registered);
	};
	_this.getPatternLabal = function () {
		return falconReportData.sheet.customformat.getLabel();
	};

	_this.downloadPattern = function () {
		falconReportData.sheet.customformat.download(true);
	};
	_this.downloadFormat = function () {
		falconReportData.sheet.customformat.download();
	};

	_this.cancel = function () {
		falconReportData.sheet.customformat.cancel(tmpdata);
		$scope.$dismiss('cancel');
	};
	_this.save = function () {
		$scope.sheet.modal_instance.close();
	};
}]);


/* レポート作成 -> 主要キーワード設定 */
ctrl.controller('FalconReportKwCtrl',
	['$scope',  'falconReportStore', 'falconReportData',
	function($scope, falconReportStore, falconReportData) {

	var _this = this;

	_this.models = falconReportStore.models.kw;

	/**
	 * viewMethods
	 */
	_this.save = function () {
		console.log('save start');

		falconReportData.kw.validate().then(function (res) {
			if (res) {
				falconReportData.kw.save().then(function () {
					falconReportData.kw.getModels();
				});
			}
		});
	};

	$scope.$on($scope.common.config.bs_name.chgClient, function () {
		falconReportData.kw.getModels();
	});


	/**
	 * init
	 */
	$scope.$on($scope.common.config.bs_name.initReport, function () {
		console.info("start KWCtrl");
	});
}]);


/* レポート作成 -> キャンペーン設定 */
ctrl.controller('FalconReportCpSettingCtrl',
	['$scope', 'falconReportStore', 'falconReportData',
	function($scope, falconReportStore, falconReportData) {

	var _this = this;

	_this.models = falconReportStore.models.cp;


	/**
	 * viewMethods
	 */
	_this.setType = function (type) {
		// テーブルをre-initialize したいので、選択状態を解除
		_this.models.menu.regist_type = '';

		falconReportData.cp.setType(type);
	};

	_this.isTypeExclude = function () {
		return falconReportData.cp.isTypeExclude();
	};

	_this.isTypeExcludeAndTemplateSelected = function () {
		return _this.isTypeExclude() && falconReportData.template.isSelected();
	};

	_this.isShowRegistBlock = function () {
		if ($scope.report.models.cp_status.exclusion.is_active) {
			return falconReportData.template.isSelected() && !_.isEmpty(_this.models.menu.regist_type);
		}

		return !_.isEmpty(_this.models.menu.regist_type);
	};

	// view table
	_this.getTargetSettingList = function () {
		falconReportData.cp.view.getList();
	};
	_this.setAll = function (type) {
		falconReportData.cp.view.setAll(type);
	};
	_this.addTarget = function (i) {
		falconReportData.cp.view.addTarget(i);
	};
	_this.save = function () {
		console.log(_this.models.list);
		falconReportData.cp.view.save();

		$scope.$broadcast($scope.common.config.bs_name.updateCpSetting);
	};
	_this.closeMsg = function () {
		falconReportStore.models.cp.msg = '';
	};


	// csv
	_this.download = function () {
		falconReportData.cp.csv.download();
	};
	_this.upload = function () {
		falconReportData.cp.csv.upload();
	};


	$scope.$on($scope.common.config.bs_name.applyTemplate, function () {
		console.info('Reset Campaign Setting');
		_this.setType('exclusion');
	});


	/**
	 * init
	 */
	$scope.$on($scope.common.config.bs_name.initReport, function () {
		console.info("start CpSettingCtrl");

		/**
		 * page変更、表示件数変更を監視
		 */
		$scope.$watch(function () {
			_this.models.regist.display.offset =
				(_this.models.regist.display.current - 1) * falconReportStore.models.cp.regist.display.limit;
		});
	});
}]);


/* レポート作成 -> 目標設定 */
ctrl.controller('FalconReportAimCtrl',
	['$scope', 'falconReportStore', 'falconReportData',
	function($scope, falconReportStore, falconReportData) {

	var _this = this;

	_this.models = falconReportStore.models.aim;

	/**
	 * viewMethods
	 */
	_this.isCategory = function () {
		return falconReportData.aim.isCategory();
	};

	/** CategoryAim */
	_this.download = function () {
		falconReportData.aim.category.download();
	};
	_this.clear = function () {
		falconReportData.aim.category.clear();
	};

	$scope.$on($scope.common.config.bs_name.applyTemplate, function () {
		falconReportData.aim.category.getRegisterdStr();
	});

	/**
	 * init
	 */
	$scope.$on($scope.common.config.bs_name.initReport, function () {
		console.info("start AimCtrl");
	});
}]);


/* テンプレート保存 */
ctrl.controller('falconTemplateSaveCtrl',
	['$scope', 'ssModal', 'falconReportStore', 'falconReportData',
	function($scope, ssModal, falconReportStore, falconReportData) {

	var _this = this;

	_this.models = $scope.report.models.modal;

	_this.isUpdate = function () {
		return falconReportData.template.isUpdate(_this.models.template_name);
	};

	_this.insert = function () {
		console.info('insert Template');

		$scope.report.modal_instance.close();
		var processingModal = ssModal.loading('保存しています...');

		var saved_template_id = null;
		falconReportData.save(_this.models)
			.then(function (template_id) {
				saved_template_id = parseInt(template_id);
				return falconReportData.template.get();
			})
			.then(function () {
				falconReportData.template.saveApply(saved_template_id);
				processingModal.close();
			});
	};
}]);


/* 一覧 */
ctrl.controller('FalconListCtrl',
	['$scope', 'falconListStore', 'falconSharedData', 'falconListData',
	function($scope, falconListStore, falconSharedData, falconListData) {

	var _this = this;

	_this.config = falconListStore.config;
	_this.models = falconListStore.models;

	/**
	 * viewMethods
	 */
	_this.download = function (history_id) {
		falconListData.download(history_id);
	};

	/**
	 * service
	 */
	_this.getList = function () {
		falconListData.get().then(function (res) {
			_this.models.data = (_.isEmpty(res)) ? null : res;
		});
	};


	$scope.$on($scope.common.config.bs_name.chgClient, function () {
		_this.models.is_set_client = true;
		_this.getList();
	});

	/**
	 * init
	 */
	falconSharedData.config.get('report').then(function () {
		console.info("start ListCtrl");
		/* CHK isSet Client */
		if (!_.isEmpty($scope.common.models.client.client)) {
			_this.models.is_set_client = true;
			_this.getList();
		}
	});
}]);
