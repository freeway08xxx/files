/* Services */
var service = angular.module( 'falcon.services', []);

/**
 * 共通 DataStore
 */
service.service('falconSharedStore',
	['falconBsName', 'falconTab', 'falconReportStore',
	function (falconBsName, falconTab, falconReportStore) {

	this.config = {
		bs_name : falconBsName,
		tab    : falconTab,
		formula: {
			'＋': '+',
			'ー': '-',
			'×': '*',
			'÷': '/'
		},
		report : {},
		client : {},
		table : {
			limit: [20, 50, 100]
		},
		cp: {
			target_key: {
				exclude:'exclusion_flg',
				device : 'device_id',
				ad_type: 'ad_type_id'
			}
		}
	};

	this.models = {
		client       : {},
		tab          : '',
		summary_type : falconReportStore.models.format.summary_type
	};
}]);

/**
 * ReportForm DataStore
 */
service.service('falconReportStore', [ function () {

	var _this = this;

	this.models = {
		report: {
			msg: '未設定項目があります',
			is_valid: false,
			tab: {
				active: 'format',
				status: {
					format   : {is_disabled: false, is_show: true},
					display  : {is_disabled: false, is_show: true},
					sheet    : {is_disabled: false, is_show: true},
					kw       : {is_disabled: false, is_show: true},
					cp       : {is_disabled: false, is_show: true},
					aim      : {is_disabled: false, is_show: false}
				},
				tooltip: {
					kw: '出力シートに「主要KW推移」が選択されている場合に有効です。'
				}
			},
			cp_status: {
				exclusion: {is_disabled: true, is_active: true},
				attribute: {is_disabled: false, is_active: false},
			},
			modal: {
				template_name: '',
				template_memo: '',
				is_valid : true
			}
		},
		format: {
			report_type: 'daily',
			summary_type: 'account_id',
			category_element: '',
			device_type: 0,
			termdate: {
				getReportType: function () {
					return _this.models.format.report_type;
				}
			},
			report_name: '',
			is_send_mail: false
		},
		display: {
			col_arr: {
				common: [],
				pc    : [],
				tab   : [],
				sp    : []
			},
			is_edit_mode: false
		},
		sheet: {
			selected: [],
			available: [],
			device: '',
			customformat: {
				file: {},
				registered: '',
				is_delete_format: false
			},
			active_report_type: ''
		},
		kw: {
			list: [],
			msg: ''
		},
		cp: {
			list: [],
			menu: {
				is_collapse_filtertext: true,
				filter: {
					text     : '',
					is_like  : null,
					is_except: null,
					type     : 1
				},
				exclude: {
					status: 2
				},
				attr: {
					is_unset_only: false
				},
				regist_type: ''
			},
			regist: {
				display: {
					target     : {},
					bulk_option: {
						exclude: '',
						device : '',
						ad_type: ''
					},
					limit      : 0,
					offset     : 0,
					current    : 1,
					num_pages  : 1,
					total_items: 0
				},
				csv: {
					file: ''
				}
			},
			msg: {
				text: '',
				attr: 'danger'
			},
			is_collapse_attention: true
		},
		aim: {
			sheet: [],
			cols : {},
			data : {},
			category: {
				file: [],
				registered: ''
			}
		}
	};
}]);


/**
 * ReportForm DisplayEdit DataStore
 */
service.service('falconReportDisplayEditStore', [ function () {

	this.models = {
		device: {},
		col_arr: [],
		available: {
			standard: [],
			media_cv: [],
			ext_cv: [],
			formula: []
		},
		formula: {
			formula: '',
			element: [],
			in_edit_no: null,
			is_edit: false,
			is_valid: false
		},
		selectable_ext_cv_count: 0,
		filter: {
			name: ''
		}
	};
}]);


/**
 * Template DataStore
 */
service.service('falconTemplateStore', [function () {

	this.models = {
		list: {},
		exclude: {},
		selected_index: null
	};
}]);


/**
 * List DataStore
 */
service.service('falconListStore', [function () {

	this.config = {

	};

	this.models = {
		data: null,
		is_set_client: false
	};
}]);


/**
 * Shared DataHandler
 */
service.factory('falconSharedData',
	['falconRestApi', 'falconSharedStore', 'falconUtils',
	function (falconRestApi, falconSharedStore, falconUtils) {

	var deferred = null;

	/**
	 * private Methods
	 */
	function getReportConfig () {
		deferred = falconUtils.createDefer(deferred);

		if (!_.isEmpty(falconSharedStore.config.report)) {
			console.info('shared config exist');
			deferred.resolve();
		} else {
			falconRestApi.get.config.report().then(function (res) {
				console.info('config set success:');
				console.log(res.data);

				falconSharedStore.config.report = res.data;
				deferred.resolve();
			},function (error){
				console.warn('err' + error);
				deferred.reject();
			});
		}

		return deferred.promise;
	}

	function getClientConfig () {
		deferred = falconUtils.createDefer(deferred);

		var client_id = falconSharedStore.models.client.client.id;

		if (!_.isEmpty(falconSharedStore.config.client) &&
		    falconSharedStore.config.client.client_id === client_id) {
			deferred.resolve();
			return deferred.promise;
		}

		falconRestApi.get.config.client(client_id).then(function (res) {
			falconSharedStore.config.client = res.data;
			deferred.resolve();
		},function (error){
			console.warn(error);
			deferred.reject();
		});

		return deferred.promise;
	}

	/**
	 * public API
	 */
	return {
		config: {
			get: function (type) {
				var func = null;

				if (type === 'report') func = getReportConfig;
				if (type === 'client') func = getClientConfig;

				return func();
			}
		}
	};
}]);


/**
 * ReportForm  DataHandler
 */
service.factory('falconReportData',
	['$location', '$routeParams', 'falconReportStore', 'falconReportFormatData', 'falconReportDisplayData',
	'falconReportSheetData', 'falconReportKwData', 'falconReportCpSettingData', 'falconReportAimData',
	'falconReportCreate', 'falconTemplateData',
	function ($location, $routeParams, falconReportStore, falconReportFormatData, falconReportDisplayData,
		falconReportSheetData, falconReportKwData, falconReportCpSettingData, falconReportAimData,
		falconReportCreate, falconTemplateData) {

	/**
	 * private Methods
	 */
	var tab = {
		get: function () {
			return $routeParams.tab ? $routeParams.tab : falconReportStore.models.report.tab.active;
		},
		set: function (tab_id) {
			var t = (tab_id) ? tab_id : tab.get();
			$location.search('tab', t);
			falconReportStore.models.report.tab.active = t;
		},
		isActive: function (tgt) {
			return falconReportStore.models.report.tab.active === tgt;
		}
	};

	/**
	 * public API
	 */
	return {
		tab     : tab,
		format  : falconReportFormatData,
		display : falconReportDisplayData,
		sheet   : falconReportSheetData,
		kw      : falconReportKwData,
		cp      : falconReportCpSettingData,
		aim     : falconReportAimData,
		validate: falconReportCreate.validate,
		create  : falconReportCreate.create,
		save    : falconTemplateData.save,
		template: falconTemplateData
	};
}]);

/**
 * ReportForm Format DataHandler
 */
service.factory('falconReportFormatData',
	['falconSharedStore', 'falconReportStore', 'falconUtils',
	function (falconSharedStore, falconReportStore, falconUtils) {

	/**
	 * private Methods
	 */
	function isSetDeviceType () {
		return falconReportStore.models.format.device_type > 0;
	}

	function chgCommonSummary (val) {
		falconUtils.chgCommonSummary(val);
	}


	/**
	 * public API
	 */
	return {
		isSetDeviceType: isSetDeviceType,
		chgCommonSummary: chgCommonSummary
	};
}]);

/**
 * ReportForm Display DataHandler
 */
service.factory('falconReportDisplayData',
	['falconReportStore', 'falconReportDisplayEditStore', 'falconReportDisplayEditData', 'falconReportDisplayEditFormulaData',
	function (falconReportStore, falconReportDisplayEditStore, falconReportDisplayEditData, falconReportDisplayEditFormulaData) {

	var models = falconReportStore.models.display;

	/**
	 * private Methods
	 */
	function toggleEditView (bool) {
		if (typeof bool === 'undefined') bool = null;

		if (bool === false) {
			models.is_edit_mode = bool;
			return;
		}
		models.is_edit_mode = !models.is_edit_mode;
	}
	function initEditMode (device) {
		falconReportDisplayEditStore.models.device = device;
		falconReportDisplayEditData.init();
		toggleEditView();
	}

	/**
	 * public API
	 */
	return {
		edit           : falconReportDisplayEditData,
		formula        : falconReportDisplayEditFormulaData,
		toggleEditView : toggleEditView,
		initEditMode   : initEditMode
	};
}]);


/**
 * ReportForm Display Edit DataHandler
 */
service.factory('falconReportDisplayEditData',
	['$timeout', 'falconSharedStore', 'falconReportStore', 'falconReportDisplayEditStore',
	function ($timeout, falconSharedStore, falconReportStore, falconReportDisplayEditStore) {

	var models         = falconReportDisplayEditStore.models;
	var display_models = falconReportStore.models.display;

	/**
	 * private Methods
	 */
	function add (item, index) {
		models.col_arr.push(_.clone(item));
		models.available[item.element_type][index].view_flg = 0;
	}
	function deleteItem (i) {
		var item = models.col_arr[i];

		models.col_arr.splice(i, 1);

		if (item.element_type === 'formula') return;

		var t = _.filter(models.available[item.element_type], {'key': item.key})[0];
		t.view_flg = 1;
	}
	function update () {
		display_models.col_arr[models.device.key] = _.sortBy(models.col_arr, 'order');
		models.col_arr = [];
	}
	function cancel () {
		models.col_arr = [];
	}

	function init () {
		// Copy Settings to EditingList
		var target = display_models.col_arr.common;
		if (!_.isEmpty(display_models.col_arr[models.device.key])) {
			target = display_models.col_arr[models.device.key];
		}
		models.col_arr = _.cloneDeep(target);

		// Selectable Items
		var keys = ['standard', 'media_cv', 'formula'];
		_.forEach(keys, function (type) {
			var t = _.filter(falconSharedStore.config.report.report_elem, {'element_type': type});
			models.available[type] = _.transform(_.cloneDeep(t), function (result, val, i) {
				val.view_flg = (val.view_flg > 0) ? 0 : 1;
				result[i] = val;
			});
		});

		// Check Ext_CV exist
		var list = falconSharedStore.config.client.ext_cv_name_list;
		if (list) {
			// 外部CV多いと Rendering 重いので後処理
			$timeout(function () {
				models.available.ext_cv = _.cloneDeep(list);
			});
		}
	}

	/**
	 * public API
	 */
	return {
		add       : add,
		deleteItem: deleteItem,
		update    : update,
		cancel    : cancel,
		init      : init
	};
}]);

/**
 * ReportForm Display EditFormula DataHandler
 */
service.factory('falconReportDisplayEditFormulaData',
	['falconSharedStore', 'falconReportStore', 'falconReportDisplayEditStore',
	function (falconSharedStore, falconReportStore, falconReportDisplayEditStore) {

	var models      = falconReportDisplayEditStore.models.formula;
	var edit_models = falconReportDisplayEditStore.models;

	/**
	 * private Methods
	 */
	function setModel (i) {
		models.formula = _.trimLeft(edit_models.col_arr[i].formula, '=');
		models.in_edit_no = i;
	}

	function clear () {
		models.formula    = '';
		models.element    = [];
		models.in_edit_no = null;
		models.is_edit    = false;
		models.is_valid   = false;
	}

	function save () {
		var addEqual = function (formula) {
			return '=' + formula;
		};

		if (models.is_edit) {
			edit_models.col_arr[models.in_edit_no].formula = addEqual(models.formula);
		} else {
			var data = _.cloneDeep(_.filter(falconSharedStore.config.report.report_elem, {'element_type': 'formula'})[0]);

			data.view_flg = 1;
			data.formula  = addEqual(models.formula);

			edit_models.col_arr.push(data);
		}
	}

	function init (is_edit) {
		models.is_edit = is_edit;
		models.element = _.reject(edit_models.col_arr, {'element_type': 'formula'});
	}

	/**
	 * public API
	 */
	return {
		save    : save,
		clear   : clear,
		setModel: setModel,
		init    : init
	};
}]);


/**
 * ReportForm Sheet DataHandler
 */
service.factory('falconReportSheetData',
	['falconSharedStore', 'falconReportStore', 'falconReportFormatData', 'falconUtils',
	function (falconSharedStore, falconReportStore, falconReportFormatData, falconUtils) {

	var models = falconReportStore.models.sheet;

	/**
	 * private Methods
	 */
	function setDevice () {
		models.device = falconUtils.getDeviceKey(falconReportFormatData.isSetDeviceType());
	}

	function setAvailableSheets (report_type) {
		if (report_type) falconReportStore.models.format.report_type = report_type;
		var sheet = falconSharedStore.config.report.report_sheet[falconReportStore.models.format.report_type][models.device];

		models.available = _.cloneDeep(sheet);
		models.selected  = _.cloneDeep(sheet);
		addAll();
	}

	function getSheet() {
		return falconUtils.getSheetSetting(models.selected);
	}

	function clear () {
		_toggle(models.selected, false);
		_toggle(models.available, true);
	}
	function addAll () {
		_toggle(models.selected, true);
		_toggle(models.available, false);
	}
	function _toggle (sheet, is_show) {
		_.forEach(sheet, function (val) { val.is_show = is_show; });
	}

	/** Custom Format */
	function setFormat (obj) {
		models.customformat = obj;
	}
	function clearFormat () {
		var obj = {
			file: {},
			registered: '',
			is_delete_format: false
		};
		setFormat(obj);
	}
	function getPatternLabel () {
		var report_type = falconReportStore.models.format.report_type;
		var name = falconSharedStore.config.report.report_type[report_type].name;

		var device = (falconReportFormatData.isSetDeviceType()) ? 'デバイス設定あり' : 'デバイス設定無し';

		return name + '(' + device + ')';
	}
	function _getPatternPath () {
		var report_type = falconReportStore.models.format.report_type;
		var i           = (falconReportFormatData.isSetDeviceType()) ? 1 : 0;
		return falconSharedStore.config.report.report_format[report_type][i];
	}
	function downloadFormat (is_pattern) {
		is_pattern = is_pattern || false;
		var path = (is_pattern) ? _getPatternPath() : models.customformat.registered;

		$('form#dlFormat').attr({
			action: '/sem/new/falcon/export/formatdownload/?download_file=' + path,
			method: 'POST'
		})
		.submit();
	}


	/**
	 * public API
	 */
	return {
		device: {
			set: setDevice
		},
		setAvailableSheets: setAvailableSheets,
		getSheet          : getSheet,
		clear             : clear,
		addAll            : addAll,
		customformat: {
			cancel  : setFormat,
			clear   : clearFormat,
			getLabel: getPatternLabel,
			download: downloadFormat
		},
	};
}]);

/**
 * ReportForm KW DataHandler
 */
service.factory('falconReportKwData',
	['$timeout', 'falconRestApi', 'falconSharedStore', 'falconReportStore', 'falconUtils',
	function ($timeout, falconRestApi, falconSharedStore, falconReportStore, falconUtils) {

	var deferred = null;

	var models = falconReportStore.models.kw;

	/**
	 * private Methods
	 */
	function getModels () {
		var client_id = falconSharedStore.models.client.client.id;

		falconRestApi.get.kw(client_id).then(function (res) {
			models.list = res.data;
		});
	}

	function validate() {
		return $timeout(function () {
			var arr = _.transform(models.list, function (res, val, i) {
				if (_.isNull(val)) return res;

				// キーワード空白は無視する
				if (!_.has(val, 'keyword') || _.isEmpty(val.keyword)) {
					res.push(true);
					return res;
				}

				// マッチタイプが選択されていない
				if (val.keyword && (_.isEmpty(val.match_type) || !_.has(val, 'match_type'))) {
					i = i + 1;
					models.msg = 'キーワード ' + i + ' のマッチタイプを指定してください。';

					res.push(false);
					return res;
				}

				res.push(true);
				return res;
			});

			return (arr.indexOf(false) === -1);
		});
	}

	function save () {
		models.msg = '';

		deferred = falconUtils.createDefer();

		var keyword_list = _.transform(models.list, function (res, val) {
			if (_.has(val, 'keyword') && _.has(val, 'match_type')) {
				res.push(val.keyword + '//' + val.match_type);
			}

			return res;
		});

		var data = {
			client_id   : falconSharedStore.models.client.client.id,
			keyword_list: keyword_list
		};

		falconRestApi.post.kw(data).then(function (res) {
			if (res.data) {
				deferred.resolve();
			} else {
				models.msg = 'キーワードの登録に失敗しました。';
				deferred.reject();
			}
		}, function (error) {
			console.warn(error);
			models.msg = 'キーワードの登録に失敗しました。';
			deferred.reject();
		});

		return deferred.promise;
	}

	function isActive (sheet) {
		var is_active = _.chain(sheet)
							.filter({key: 'kw_progress'})
							.pluck('is_show')
							.compact()
							.value();

		falconReportStore.models.report.tab.status.kw.is_disabled = !is_active[0];
	}

	/**
	 * public API
	 */
	return {
		getModels: getModels,
		validate : validate,
		save     : save,
		isActive : isActive
	};
}]);


/**
 * ReportForm CpSetting DataHandler
 */
service.factory('falconReportCpSettingData',
	['$timeout', 'falconRestApi', 'falconSharedStore', 'falconReportStore', 'falconTemplateData', 'falconUtils',
	function ($timeout, falconRestApi, falconSharedStore, falconReportStore, falconTemplateData, falconUtils) {

	var models = falconReportStore.models.cp;

	/**
	 * private Methods
	 */
	function setType (type) {
		var cp_status = falconReportStore.models.report.cp_status;
		cp_status = _.transform(cp_status, function (res, val, key){
			val.is_active = (key === type) ? true : false;
			res[key] = val;
			return res;
		});
	}

	function isTypeExclude () {
		return falconReportStore.models.report.cp_status.exclusion.is_active;
	}

	function _getDataForServer () {
		return $timeout(function () {
			return _.map(falconSharedStore.models.client.accounts, function (val) {
				return val.media_id + '//' + val.account_id;
			});
		}).then(function (account_id_list) {

			var setting_type = _.findKey(falconReportStore.models.report.cp_status, 'is_active');

			var data = {
				template_id          : falconTemplateData.getSelectedId(),
				setting_type         : setting_type,
				client_id            : falconSharedStore.models.client.client.id,
				account_id_list      : account_id_list,
				no_setting_flg       : models.menu.attr.is_unset_only,
				export_exclusion_type: models.menu.exclude.status,

				campaign_search_like  : models.menu.filter.is_like,
				except_campaign_search: models.menu.filter.is_except,
				campaign_search_type  : models.menu.filter.type,
				campaign_search_text  : models.menu.filter.text,
			};

			return data;
		});
	}

	function getTargetSettingList () {
		_getDataForServer().then(function (data) {
			var post_data = falconUtils.convertPostData(data);

			falconRestApi.post.cp.getList(post_data).then(function (res) {
				// 件数上限
				if (res.data[0] === false) {
					_setMsg('リストの件数が' + numeral(res.data[1]).format('0,0') + '件を越えました。CSVで設定を行なってください。', 'error');
					models.menu.regist_type = 'csv';

					return false;
				}

				models.regist.display.total_items = res.data.length;

				models.list = res.data;

				// 一括設定プルダウンをリセット
				models.regist.display.bulk_option = {
					exclude: '',
					device : '',
					ad_type: ''
				};
			});
		});
	}

	function setOffset () {
		models.regist.display.offset = (models.regist.display.current - 1) * models.regist.display.limit;
	}

	function bulkSetTableOptions (type) {
		var target_val = models.regist.display.bulk_option[type];

		models.list = _.map(models.list, function (val) {
			var target_key = falconSharedStore.config.cp.target_key[type];
			val[target_key] = target_val;
			return val;
		});
	}

	function addTarget (i) {
		$timeout(function () {
			var id = models.list[i].account_id + '_' + models.list[i].campaign_id;
			models.regist.display.target[id] = models.list[i];
		});
	}

	function save () {
		_getDataForServer().then(function (data) {
			return $timeout(function () {
				data.update_list = (isTypeExclude) ? models.list : models.regist.display.target;
				models.regist.display.target = {};

				return data;
			});
		})
		.then(function (data) {
			var post_data = falconUtils.convertPostData(data);

			falconRestApi.post.cp.save(post_data).then(function (res) {
				if (!res.data[0]) _setMsg('更新処理に失敗しました', 'error');

				getTargetSettingList();
			}, function (error) {
				console.warn(error);
				_setMsg('更新処理に失敗しました', 'error');
			});
		});
	}


	function downloadCsv () {
		_getDataForServer().then(function (data) {
			$('form#cp_csvform').attr({
				action: '/sem/new/falcon/campaignsetting/campaigndownload/?form=' + JSON.stringify(data),
				method: 'POST'
			})
			.submit();
		});
	}

	function uploadCsv () {
		_getDataForServer().then(function (data) {
			var post_data = falconUtils.convertPostData(data, models.regist.csv.file);

			falconRestApi.post.cp.uploadCsv(post_data)
			.then(function (res) {
				var result_id = res.data;

				_downloadResult(result_id);

				_setMsg('更新処理が完了しました');

			}, function (error) {
				console.warn(error.data);
				_setMsg('更新処理に失敗しました', 'error');
			});
		});
	}
	function _downloadResult (result_id) {
		$('form#cp_csvform').attr({
			action: '/sem/new/falcon/campaignsetting/downloadresult/' + result_id,
			method: 'POST'
		})
		.submit();
	}

	function _setMsg (text, error) {
		error = error || null;
		var attr = (error === 'error') ? 'danger' : 'success';
		models.msg = {text: text, attr: attr};
	}

	/**
	 * public API
	 */
	return {
		setType      : setType,
		isTypeExclude: isTypeExclude,
		view: {
			getList  : getTargetSettingList,
			setAll   : bulkSetTableOptions,
			setOffset: setOffset,
			addTarget: addTarget,
			save     : save
		},
		csv: {
			download: downloadCsv,
			upload  : uploadCsv
		}
	};
}]);


/**
 * ReportForm Aim DataHandler
 */
service.factory('falconReportAimData',
	['$timeout', 'falconRestApi', 'falconSharedStore', 'falconReportStore', 'falconTemplateStore', 'falconTemplateData', 'falconUtils',
	function ($timeout, falconRestApi, falconSharedStore, falconReportStore, falconTemplateStore, falconTemplateData, falconUtils) {

	/**
	 * private Methods
	 */
	function getParams () {
		falconReportStore.models.aim.sheet      = _getTargetSheet();
		falconReportStore.models.aim.cols       = _getCols();
		falconReportStore.models.aim.media_list = falconUtils.getMediaList();
	}

	function isCategory () {
		return falconUtils.isCategory(falconReportStore.models.format.summary_type);
	}

	function _getTargetSheet () {
		var aim_list = falconSharedStore.config.report.report_aim;
		var keys     = _.keys(aim_list);

		var select_sheet_keys = _.chain(falconReportStore.models.sheet.selected)
									.filter('is_show')
									.pluck('key')
									.value();


		var sheet = _.transform(aim_list, function (res, val, key) {
			if (key.indexOf('category_') !== -1) return;

			// デバイス別目標はシート無いので必ず表示
			select_sheet_keys.push('device_daily');

			if (select_sheet_keys.indexOf(val.sheet_key) !== -1) {
				val.key = key;
				res[keys.indexOf(key)] = val;
			}

			return res;
		});

		return _.toArray(sheet);
	}

	/**
	 * 表示項目名のリストを取得
	 * デバイス別推移シート用に、merged, separated col_list を作成
	 */
	function _getCols () {
		var col_arr = falconReportStore.models.display.col_arr;

		var separated_keys = _.transform(col_arr, function (res, val, key) {
			if (key === 'common') return;

			var i  = _.findIndex(falconSharedStore.config.report.report_device_line, {'key': key});
			res[i] = (!_.isEmpty(val)) ? _.pluck(val, 'key') : _.pluck(col_arr.common, 'key');

			return res;
		});

		var merged =  _.chain(col_arr)
						.values()
						.reduce(function (sum, cols) { return _.union(sum, cols); })
						.uniq('key')
						.value();

		return {
			common: col_arr.common,
			device: {
				separated_keys: separated_keys,
				merged        : merged
			}
		};
	}

	/**
	 * CategorySetting
	 */
	function downloadCsv () {
		_getDataForServer().then(function (data) {
			$('form#aim_csvform').attr({
				action: '/sem/new/falcon/aim/getsheet/?form=' + JSON.stringify(data),
				method: 'POST'
			})
			.submit();
		});
	}
	function clear () {
		falconReportStore.models.aim.category.file = [];
	}

	function getRegisterdStr () {
		var obj = _.chain(falconReportStore.models.aim.data)
			.transform(function (res, val, key) {
				if (key.indexOf('category') !== -1) {
					var val_arr = _.pairs(val);
					var id = key.split(':');
					res[0] = id[1] + ':' + val_arr[0];

					return res;
				}
			})
			.toArray()
			.value();

		falconReportStore.models.aim.category.registered = obj;
	}

	function _getDataForServer () {
		return $timeout(function () {
			var template_id   = falconTemplateData.getSelectedId();
			var template_name = falconTemplateData.getTemplateName(template_id);

			return {
				client_id       : falconSharedStore.models.client.client.id,
				template_id     : template_id,
				template_name   : template_name,
				category_id     : falconSharedStore.models.client.categoryGenre.id,
				category_element: falconReportStore.models.format.category_element,
				cols            : falconReportStore.models.aim.cols.common
			};
		});
	}

	/**
	 * public API
	 */
	return {
		getParams : getParams,
		isCategory: isCategory,
		category  : {
			download       : downloadCsv,
			clear          : clear,
			getRegisterdStr: getRegisterdStr
		}
	};
}]);

/**
 * ReportForm Sender
 */
service.factory('falconReportCreate',
	['$location', 'falconRestApi', 'falconReportStore', 'falconTemplateData', 'falconUtils',
	function ($location, falconRestApi, falconReportStore, falconTemplateData, falconUtils) {

	var deferred = null;

	/**
	 * private Methods
	 */
	function validate (val) {
		deferred = falconUtils.createDefer(deferred);

		/** 期間選択 or 媒体費が入力されている && 入力値が正常である */
		if (!val.format.is_form_valid) deferred.reject();

		/** サマリ単位がアカウントの時に、アカウントがひとつ以上選択されている */
		if (!falconUtils.isCategory(val.format.summary_type) && _.isEmpty(val.accounts)) deferred.reject();

		/** サマリ単位がカテゴリの時に、カテゴリ種別が選択されている */
		if (falconUtils.isCategory(val.format.summary_type) && _.isEmpty(val.format.category_element)) deferred.reject();

		/** 表示項目が選択されている */
		var count = _.chain(val.display)
						.transform(function (res, val) {
							res[val.length] = val.length;
							return res;
						})
						.toArray()
						.sum()
						.value();

		if (!count) deferred.reject();

		/** シートがひとつ以上選択されている */
		if (_.isEmpty(_.where(val.sheet, {'is_show': true}))) deferred.reject();

		deferred.resolve();

		return deferred.promise;
	}

	function prepare () {
		var form = falconReportStore.models;

		var data = falconUtils.prepareDataForServer();
		data.export_type = 'export';

		data.template_id = falconTemplateData.getSelectedId();

		// convert Arr
		data.start_date = _.transform(form.format.termdate.term_arr, function (result, val, i) {
			result[i] = moment(val.from).format('YYYY/MM/DD');
		});
		data.end_date = _.transform(form.format.termdate.term_arr, function (result, val, i) {
			result[i] = moment(val.to).format('YYYY/MM/DD');
		});

		data.add_aim_elem = (!_.isEmpty(form.aim.data)) ? falconUtils.getAimSetting(falconReportStore.models.aim.data) : [];

		console.log(data);

		var file = [];
		/** カスタムフォーマットXLS */
		if (!_.isEmpty(falconReportStore.models.sheet.customformat.file)) {
			file.push({
				name: 'customformat',
				data: falconReportStore.models.sheet.customformat.file
			});
		}

		return falconUtils.convertPostData(data, file);
	}

	function create () {
		return falconRestApi.post.createReport(prepare());
	}


	/**
	 * public API
	 */
	return {
		validate: validate,
		prepare : prepare,
		create  : create
	};
}]);

/**
 * Report Template DataHandler
 */
service.factory('falconTemplateData',
	['falconRestApi', 'falconSharedStore', 'falconReportStore', 'falconTemplateStore', 'falconReportSheetData', 'falconTemplateUtil', 'falconUtils',
	function (falconRestApi, falconSharedStore, falconReportStore, falconTemplateStore, falconReportSheetData, falconTemplateUtil, falconUtils) {

	var deferred = null;

	function get () {
		deferred = falconUtils.createDefer(deferred);

		var client_id = falconSharedStore.models.client.client.id;
		falconRestApi.get.template(client_id).then(function (res) {
			falconTemplateStore.models.list    = res.data.template_list;
			// falconTemplateStore.models.exclude = res.data.exclusion_info;

			deferred.resolve();
		});

		return deferred.promise;
	}

	function getSelectedId () {
		return (isSelected()) ?
			falconTemplateStore.models.list[falconTemplateStore.models.selected_index].id : null;
	}

	function getTemplateName (id) {
		return _.chain(falconTemplateStore.models.list)
					.filter({id: id})
					.pluck('template_name')
					.value()
					.toString();
	}

	function setNameToModal () {
		if (!isSelected()) return false;

		var i = falconTemplateStore.models.selected_index;
		falconReportStore.models.report.modal.template_name = falconTemplateStore.models.list[i].template_name;
	}

	function deleteItem (i) {
		deferred = falconUtils.createDefer(deferred);

		var params = {
			client_id   : falconTemplateStore.models.list[i].client_id,
			template_id : falconTemplateStore.models.list[i].id
		};

		falconRestApi.post.template.delete(params).then(function (res) {
			if (res) {
				falconTemplateStore.models.list.splice(i, 1);
				deferred.resolve();
			} else {
				console.warn('delete Error');
				deferred.reject();
			}
		}, function (error){
			console.warn(error);
			deferred.reject();
		});

		return deferred.promise;
	}

	function apply (i) {
		deferred = falconUtils.createDefer(deferred);

		var data = falconTemplateStore.models.list[i];
		falconTemplateStore.models.selected_index = i.toString();

		var params = {
			client_id  : data.client_id,
			template_id: data.id
		};

		/**
		 * テンプレートデータをフォームへ反映
		 */
		falconRestApi.get.templateDetail(params).then(function (res) {
			var detail_data = res.data;

			var is_category = falconUtils.isCategory(data.category_type_id);

			var summary_type = (is_category) ? 'category_id' : data.category_type_id;

			/** ClientCombo 表示切り替え */
			falconUtils.chgCommonSummary(summary_type);

			/** レポート形式 */
			falconReportStore.models.format.summary_type  = summary_type;

			falconReportStore.models.format.report_type   = data.report_type;
			falconReportStore.models.format.device_type   = data.device_type;
			falconReportStore.models.format.report_name   = data.report_name;
			falconReportStore.models.format.send_mail_flg = data.send_mail_flg;
			falconReportStore.models.format.media_cost    = parseInt(data.media_cost);

			falconReportStore.models.format.category_element = (is_category) ? data.category_type_id : null;

			falconReportStore.models.format.termdate.method.applyPresetTerms(data.term_count, data.report_term);

			/** 表示項目 */
			var display_config = {
				device_list: falconSharedStore.config.report.report_device_line,
				elem_name  : falconSharedStore.config.report.report_elem,
				ext_cv_name: falconSharedStore.config.client.ext_cv_name_list
			};
			falconReportStore.models.display.col_arr = falconTemplateUtil.getDisplayModels(detail_data.line_info, display_config);

			/** シート設定 */
			var device_key   = falconUtils.getDeviceKey(parseInt(data.device_type));
			var sheet_master = _.cloneDeep(falconSharedStore.config.report.report_sheet[data.report_type][device_key]);
			var sheet_models = falconTemplateUtil.getSheetModels(detail_data.sheet_info, sheet_master);
			falconReportStore.models.sheet.selected  = sheet_models.selected;
			falconReportStore.models.sheet.available = sheet_models.available;

			/** カスタムフォーマットを適用 */
			falconReportSheetData.customformat.clear();
			falconReportStore.models.sheet.customformat.registered = data.custom_format_file_path;

			/** 目標設定 */
			falconReportStore.models.aim.data = (!_.isEmpty(detail_data.aim_info)) ?
				falconTemplateUtil.getAimModels(detail_data.aim_info, display_config) : {};

			/** アカウント || カテゴリリスト */
			var clientcombo_ids = {
				category_genre_id: data.category_genre_id,
				accounts         : falconTemplateUtil.getAccount(detail_data.account_info)
			};

			deferred.resolve(clientcombo_ids);
		});

		return deferred.promise;
	}

	/**
	 * 目標設定タブの表示切り替え
	 * 日別推移かつテンプレ利用時のみ表示する
	 *
	 * @return void
	 */
	function toggleAimTab () {
		falconReportStore.models.report.tab.status.aim.is_show =  false;

		if (falconTemplateStore.models.selected_index && isDaily()) {
			falconReportStore.models.report.tab.status.aim.is_show = true;
		}
	}

	function isDaily () {
		return falconUtils.isDaily(falconReportStore.models.format.report_type);
	}

	function isUpdate (name) {
		if (_.isEmpty(falconTemplateStore.models.selected_index)) return false;

		return name === falconTemplateStore.models.list[falconTemplateStore.models.selected_index].template_name;
	}

	function isSelected () {
		return !_.isNull(falconTemplateStore.models.selected_index);
	}

	function save (modal) {
		deferred = falconUtils.createDefer(deferred);

		var data = falconUtils.prepareDataForServer();
		data.export_type = 'save';

		/** テンプレート新規保存、別名保存 or 上書き保存の判定 */
		data.new_save = !isUpdate(modal.template_name);

		data.template_id   = getSelectedId();
		data.template_name = modal.template_name;
		data.template_memo = modal.template_memo;

		data.add_aim_elem = (!_.isEmpty(falconReportStore.models.aim.data)) ?
			falconUtils.getAimSetting(falconReportStore.models.aim.data) : [];

		var files = [];
		/** カスタムフォーマットXLS */
		if (!_.isEmpty(falconReportStore.models.sheet.customformat.file)) {
			files.push({
				name: 'customformat',
				data: falconReportStore.models.sheet.customformat.file
			});
		}
		/** カテゴリ別目標設定CSV */
		if (!_.isEmpty(falconReportStore.models.aim.category.file)) {
			files.push({
				name: 'category_aim',
				data: falconReportStore.models.aim.category.file
			});
		}

		var postdata = falconUtils.convertPostData(data, files);

		falconRestApi.post.template.save(postdata).then(function (res) {
			deferred.resolve(res.data);
		});

		return deferred.promise;
	}

	function saveApply (template_id) {
		var i = _.indexOf(_.pluck(falconTemplateStore.models.list, 'id'), template_id.toString());

		return apply(i);
	}

	/**
	 * public API
	 */
	return {
		get            : get,
		getSelectedId  : getSelectedId,
		getTemplateName: getTemplateName,
		apply          : apply,
		save           : save,
		saveApply      : saveApply,
		setNameToModal : setNameToModal,
		isUpdate       : isUpdate,
		isDaily        : isDaily,
		isSelected     : isSelected,
		delete         : deleteItem,
		toggleAimTab   : toggleAimTab,
	};
}]);

/**
 * テンプレートデータ変換
 */
service.service('falconTemplateUtil', [ function () {

	this.getAccount = function (account_info) {
		var accounts = [];
		_.forEach(account_info, function (val, key) {
			var arr = key.split('//');
			accounts.push({media_id: parseInt(arr[0]), account_id: arr[1]});
		});
		return accounts;
	};

	this.getDisplayModels = function (line_info, config) {
		var col_arr = { common: [], pc : [], tab: [], sp : [] };

		_.forEach(line_info, function (val) {
			var model = {
				element_type     : val.element_type,
				formula_cell_type: val.formula_cell_type,
				key              : val.element,
				view_flg         : 1
			};

			if (val.element_type === 'ext_cv') {
				model.name = _.pluck(_.filter(config.ext_cv_name, {ext_key: val.element}), 'name').toString();

				model.key     = model.name;
				model.ext_key = val.element;
			} else {
				model.name = _.pluck(_.filter(config.elem_name, {key: val.element}), 'name').toString();
			}

			col_arr[config.device_list[val.device_id].key].push(model);
		});

		return col_arr;
	};

	this.getSheetModels = function (sheet_info, sheet_master) {
		var sheet = {};

		var keys = _.keys(sheet_info);

		sheet.selected = _.map(sheet_master, function (val) {
			val.is_show = (_.contains(keys, val.key));
			return val;
		});
		sheet.available = _.map(_.cloneDeep(sheet.selected), function (val) {
			val.is_show = !val.is_show;
			return val;
		});

		return sheet;
	};

	this.getAimModels = function (aim_info, config) {
		return _.transform(aim_info, function(res, obj, sheet) {
			res[sheet] = _.transform(obj, function (r, val, key) {
				var k = (key.indexOf('ext') !== -1) ? _.pluck(_.filter(config.ext_cv_name, {ext_key: key}), 'name').toString() : key;
				r[k] = val;
				return r;
			});

			return res;
		});
	};
}]);


/**
 * List Data Access Service
 */
service.factory('falconListData',
	['falconRestApi', 'falconSharedStore', 'falconTemplateData',
	function (falconRestApi, falconSharedStore, falconTemplateData) {

	/**
	 * private Methods
	 */
	function get () {
		var param = {client_id: falconSharedStore.models.client.client.id};

		return falconRestApi.get.list(param)
			.then(function (res) {
				return res.data;
			}, function (error){
				console.warn(error);
			})
			.then(function (list) {
				return falconTemplateData.get().then(function () {
					return _.map(list, function (val) {
						val.template_name = _getTemplateName(val.template_id);
						return val;
					});
				});
			});
	}

	function download (history_id) {
		$('form#dl').attr({
			action: '/sem/new/falcon/export/download/' + history_id,
			method: 'POST'
		})
		.submit();
	}

	function _getTemplateName (id) {
		return (id) ? falconTemplateData.getTemplateName(id) : '--';
	}


	/**
	 * public API
	 */
	return {
		get      : get,
		download : download
	};
}]);

/**
 * Util Functions
 */
service.service('falconUtils',
	['$q', 'falconSharedStore', 'falconReportStore',
	function ($q, falconSharedStore, falconReportStore) {

	var _this = this;

	this.createDefer = function (deferred) {
		deferred = $q.defer();
		return deferred;
	};

	this.getDeviceKey = function (is_set_device_type) {
		return (is_set_device_type) ? 'device' : 'non_device';
	};

	this.getMediaList = function () {
		var media_ids   = _.uniq(_.pluck(falconSharedStore.models.client.accounts, 'media_id'));
		var media_names = _.uniq(_.pluck(falconSharedStore.models.client.accounts, 'media_name'));

		return _.zipObject(media_ids, media_names);
	};

	this.getAccountIdList = function (accounts) {
		var arr = {};
		_.forEach(accounts, function (val) {
			arr[val.account_id] = val.account_id;
		});
		return arr;
	};

	this.getLineSetting = function (col_arr) {
		var result_arr = {};

		_.forEach(col_arr, function (arr, device_id) {
			_.forEach(arr, function (val, i) {
				i = i + 1;
				var device_line_no = device_id + '_' + i;

				var key = _.chain(falconSharedStore.config.report.report_device_line)
							.findIndex({'key': device_id})
							.value()
							.toString();

				/**
				 * 外部CVの場合はキーを入れかえる (for ReportCreate.php)
				 */
				var element = (val.element_type.indexOf('ext') !== -1) ? val.ext_key : val.key;

				result_arr[device_line_no] = {
					line_no           : i.toString(),
					key               : key,
					element           : element,
					formula_cell_type : val.formula_cell_type,
					ranking_flg       : '0'
				};

				result_arr[device_line_no].label   = val.output_name || '';
				result_arr[device_line_no].formula = val.formula || '';
			});
		});

		return result_arr;
	};

	this.getSheetSetting = function (sheet) {
		return { add_sheet: _.pluck(_.filter(sheet, {'is_show': true}), 'key') };
	};

	this.getAimSetting = function (data) {
		var i = 0;
		return _.chain(data)
					.transform(function(result, val_arr, key) {
						_.forEach(val_arr, function (val, col_key) {
							/**
							 * 外部CVの場合はキーを入れかえる (for ReportCreate.php)
							 */
							var ext_cv_key = _.chain(falconSharedStore.config.client.ext_cv_name_list)
												.filter({name: col_key})
												.pluck('ext_key')
												.toString();

							var element = (_.isEmpty(ext_cv_key)) ? col_key : ext_cv_key;

							result[i] = {
								'element': element,
								'target' : key,
								'value'  : val
							};
							i++;
						});

						return result;
					})
					.toArray()
					.value();
	};

	this.chgCommonSummary = function (val) {
		falconSharedStore.models.summary_type = val;
	};

	this.prepareDataForServer = function () {
		var client = falconSharedStore.models.client;
		var form   = falconReportStore.models;

		var data = {
			client_id            : client.client.id,
			report_type          : form.format.report_type,
			device_type          : form.format.device_type,
			term_daily_set       : form.format.termdate.settings.term_set,
			term_compare_set     : form.format.termdate.settings.term_compare_set,
			term_count           : form.format.termdate.term_count.toString(),
			media_cost           : form.format.media_cost.toString(),
			output_file_name     : form.format.report_name,
			send_mail_flg        : form.format.is_send_mail,

			account_id_list: _this.getAccountIdList(client.accounts),
			add_report_elem: _this.getLineSetting(form.display.col_arr),
			report_sheet   : _this.getSheetSetting(form.sheet.selected),

			custom_format_file_path: form.sheet.customformat.registered,
			delete_custom_format   : form.sheet.customformat.is_delete_format
		};

		/** アカウント|| カテゴリ 別でパラメータ調整 */
		data.report_category_type_id = form.format.summary_type;
		if (_this.isCategory(form.format.summary_type)) {
			data.category_genre_id          = client.categoryGenre.id;
			data.report_category_element_id = form.format.category_element;
		}

		return data;
	};

	this.convertPostData = function (data, file) {
		file = file || null;

		/**
		 * FileData はフォームデータ、
		 * FormModelはJSONに変換して、
		 * multi-part でサーバへ送信する
		 */
		var post_data = new FormData();

		post_data.append("form" , angular.toJson(data));

		var appendfile = function (file) {
			if (!_.isEmpty(file)) post_data.append(file.name, file.data);
		};

		_.forEach(file, function (f) {
			appendfile(f);
		});

		return post_data;
	};

	this.isCategory = function (name) {
		return name.indexOf('cat') !== -1;
	};

	this.isDaily = function (report_type) {
		return report_type === 'daily';
	};
}]);

/**
 * ServerData API
 */
service.factory('falconRestApi', ['$http', function ($http) {

	/**
	 * Private Methods
	 */

	/** GET */
	function getReportConfig () {
		return $http({
			url: '/sem/new/api/report/config',
			method: 'GET',
			params: {type: 'falcon'}
		});
	}
	function getClientConfig (client_id) {
		return $http({
			url: '/sem/new/falcon/report/clientConfig',
			method: 'GET',
			params: {client_id: client_id}
		});
	}
	function getKw (client_id) {
		return $http({
			url: '/sem/new/falcon/kwprogress/data',
			method: 'GET',
			params: {client_id: client_id}
		});
	}
	function getList (params) {
		return $http({
			url: '/sem/new/falcon/list/history',
			method: 'GET',
			params: params
		});
	}
	function getTemplate (client_id) {
		return $http({
			url: '/sem/new/falcon/template/list',
			method: 'GET',
			params: {client_id: client_id}
		});
	}
	function getTemplateDetail (params) {
		return $http({
			url: '/sem/new/falcon/template/detail',
			method: 'GET',
			params: params
		});
	}
	function getCategoryList (params) {
		return $http({
			url: '/sem/new/falcon/report/categoryList',
			method: 'GET',
			params: params
		});
	}

	/** POST */
	function saveKw (data) {
		return $http({
			url: '/sem/new/falcon/kwprogress/save/' + data.client_id + '.json',
			method: 'POST',
			data: data,
		});
	}
	function uploadCpSettingSheet (data) {
		return $http({
			url: '/sem/new/falcon/campaignsetting/campaignupload/',
			method: 'POST',
			data: data,
			headers:{'Content-type': undefined, 'enctype': 'multipart/form-data'},
			transformRequest: null
		});
	}
	function getCpSettingList(data) {
		return $http({
			url: '/sem/new/falcon/campaignsetting/data/',
			method: 'POST',
			data: data,
			headers:{'Content-type': undefined, 'enctype': 'multipart/form-data'},
			transformRequest: null
		});
	}
	function saveCpSetting (data) {
		return $http({
			url: '/sem/new/falcon/campaignsetting/update/',
			method: 'POST',
			data: data,
			headers:{'Content-type': undefined, 'enctype': 'multipart/form-data'},
			transformRequest: null
		});
	}
	function createReport (data) {
		return $http({
			url: '/sem/new/falcon/export/start/' + data.client_id,
			method: 'POST',
			data: data,
			headers:{'Content-type': undefined, 'enctype': 'multipart/form-data'},
			transformRequest: null,
			ignoreLoadingBar: true
		});
	}
	function saveTemplate (data) {
		return $http({
			url: '/sem/new/falcon/template/add',
			method: 'POST',
			data: data,
			headers:{'Content-type': undefined, 'enctype': 'multipart/form-data'},
			transformRequest: null,
			ignoreLoadingBar: true
		});
	}
	function deleteTemplate (params) {
		return $http({
			url: '/sem/new/falcon/template/delete',
			method: 'POST',
			params: params
		});
	}

	/**
	 * public API
	 */
	return {
		get: {
			config: {
				report: getReportConfig,
				client: getClientConfig
			},
			kw            : getKw,
			list          : getList,
			template      : getTemplate,
			templateDetail: getTemplateDetail,
			category      : getCategoryList
		},
		post: {
			kw: saveKw,
			cp: {
				getList  : getCpSettingList,
				uploadCsv: uploadCpSettingSheet,
				save     : saveCpSetting
			},
			createReport: createReport,
			template: {
				save  : saveTemplate,
				delete: deleteTemplate
			}
		}
	};
}]);
