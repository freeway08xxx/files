var mod = angular.module('ss.module.termdate', []);

mod.config(["datepickerConfig", "datepickerPopupConfig",
	function(datepickerConfig, datepickerPopupConfig) {
		datepickerConfig.showWeeks = false;
		datepickerConfig.yearRange = 10;
		datepickerConfig.dayTitleFormat = "yyyy年 MMMM";
		datepickerPopupConfig.currentText = "本日";
		datepickerPopupConfig.clearText = "クリア";
		datepickerPopupConfig.closeText = "閉じる";
	}
]);

mod.constant('ssTermdateConfig', {
	is_append_hidden_input : true,
	default_termset: 'daily_0',
	title: '集計期間',
	datepicker: {
		formatYear: 'yyyy',
		startingDay: 1,
		format: 'yyyy/MM/dd'
	},
	term_set: null,
	term_compare_set: null
});

mod.constant('ssTermdateMsg', {
	range: '期間の範囲が大きすぎます。1期間あたり最大2ヶ月以内で設定してください。',
	order: '期間の開始日が終了日より後の日付になっています。'
});

mod.directive('ssTermdate',[ function(){
	return {
		scope: true,
		restrict: 'AE',
		replace: true,
		require:'ngModel',
		templateUrl: '/sem/new/assets/template/module/termdate.html',
		template:  '',
		link: function(scope, elem, attrs, ngModel) {
		},
		controller:'ssTermdateCtrl'
	};
}]);

mod.controller('ssTermdateCtrl', ['$rootScope', '$scope', '$attrs', '$filter', 'ssTermdateService', 'ssTermdateConfig',
	function($rootScope, $scope, $attrs, $filter, ssTermdateService, ssTermdateConfig) {

	$scope.moduleModel = $scope.$eval($attrs.ngModel);
	var mdl = $scope.moduleModel;

	mdl.settings    = ssTermdateConfig;
	var eval_config = $scope.$eval($attrs.ssTermdate);
	if (!_.isEmpty(eval_config)) {
		// 設定値拡張
		_.each(eval_config, function (val, key, obj) {
			mdl.settings[key] = _.defaults(obj[key], ssTermdateConfig[key]);
		});
	}
	mdl.settings.term_set = mdl.settings.default_termset;

	$scope.$watch('moduleModel.term_arr.length', function () {
		mdl.term_count = mdl.term_arr.length;
	});

	var appendHiddenInputs = function ()  {
		var name  = 'term_set';
		var value = mdl.settings.term_set;

		if (mdl.getReportType() === 'term_compare') {
			name  = 'term_compare_set';
			value = mdl.settings.term_compare_set;
		}

		var html = '<input type="hidden" name="' + name + '" value="' + value + '">';
		$("#ssTermdateHiddens").append(html);
	};

	var isValid = function () {
		mdl.is_valid = ssTermdateService.validate.isValid(mdl.msg);
	};

	$scope.isCompare = function () {
		return mdl.getReportType() === 'term_compare';
	};

	$scope.add = function () {
		ssTermdateService.operate.add(mdl.term_arr, mdl);
	};
	$scope.clear = function (i) {
		ssTermdateService.operate.clear(mdl.term_arr, i);
		$scope.validate(i);
	};
	$scope.delete = function (i) {
		ssTermdateService.operate.delete(mdl.term_arr, i);
		ssTermdateService.settings.msg.delete(mdl.msg, i);
	};
	$scope.reset = function () {
		ssTermdateService.operate.reset(mdl.term_arr);
		ssTermdateService.settings.msg.reset(mdl.msg);
	};

	$scope.setTerm = function (preset) {
		ssTermdateService.settings.msg.reset(mdl.msg);

		preset = (preset) ? preset : mdl.settings.term_set;
		ssTermdateService.preset.setTerm(mdl.term_arr, preset);
		isValid();
		if (mdl.settings.is_append_hidden_input) {
			appendHiddenInputs();
		}
	};
	$scope.setCompareTerms = function () {
		ssTermdateService.settings.msg.reset(mdl.msg);

		ssTermdateService.preset.setCompareTerms(mdl.term_arr, mdl.settings.term_compare_set);
		isValid();
		if (mdl.settings.is_append_hidden_input) {
			appendHiddenInputs();
		}
	};

	// datepicker action
	$scope.open = function($event, i, key) {
		$event.preventDefault();
		$event.stopPropagation();

		// 一旦全部閉じる
		angular.forEach(mdl.status, function (val, i) {
			mdl.status[i] = {from: false, to: false};
		});
		mdl.status[i][key] = true;
	};

	// viewから操作 = 日付変更したらカスタムプリセット
	$scope.validate = function (i) {
		mdl.settings.term_set = 'custom';
		ssTermdateService.validate.validate(mdl, i);
	};

	// テンプレート設定を適用
	var applyPresetTerms = function (term_count, preset) {
		$scope.reset();

		var is_compare = $scope.isCompare();

		if (!is_compare) {
			mdl.settings.term_set = preset;
			$scope.setTerm();
		}
		if (is_compare) {
			mdl.settings.term_compare_set = preset;
			for (var i = term_count - 1; i > 0; i--) {
				$scope.add();
			}
			$scope.setCompareTerms();
		}
	};

	// 初期化
	var initialize = function() {
		mdl.term_arr = [];
		mdl.status   = [];
		mdl.msg      = [];
		mdl.is_valid = false;

		// 外部操作用にメソッド追加
		mdl.method = {
			add:             $scope.add,
			reset:           $scope.reset,
			setTerm:         $scope.setTerm,
			setCompareTerms: $scope.setCompareTerms,
			isCompare:       $scope.isCompare,
			applyPresetTerms: applyPresetTerms
		};

		// 共通定数取得
		ssTermdateService.settings.config.get().then(function (config) {
			mdl.settings.master = config;

			$scope.add();
			$scope.setTerm();
			isValid();
		});
	};
	initialize();
}]);


// ==========================================================
// 以下 Service
// ==========================================================
mod.service('ssTermdateService', ['ssTermdateValidate', 'ssTermdateOperate', 'ssTermdatePreset', 'ssTermdateSettings',
		function(ssTermdateValidate, ssTermdateOperate, ssTermdatePreset, ssTermdateSettings) {
	return {
		validate: ssTermdateValidate,
		operate:  ssTermdateOperate,
		preset:   ssTermdatePreset,
		settings: ssTermdateSettings
	};
}]);

mod.service('ssTermdateValidate', ['ssTermdateSettings', function(ssTermdateSettings) {
	return {
		// 集計期間が正しい順序である
		isOrdered: function (term) {
			if (moment(term.from).isSame(moment(term.to))) {
				return true;
			}
			return moment(term.from).isBefore(moment(term.to));
		},
		// 各集計期間が2ヶ月以内である
		isRanged: function (term) {
			var date_diff = moment(term.to).diff(moment(term.from), "days");
			return (date_diff < 62);
		},
		isValid : function (msg_arr) {
			return _.isEmpty(_.compact(msg_arr));
		},
		validate: function (model, i) {
			model.msg[i] = null;
			if (!model.term_arr[i].from || !model.term_arr[i].to) {
				return false;
			}

			if (!this.isOrdered(model.term_arr[i])) {
				model.msg[i] = ssTermdateSettings.msg.get('order');
			}
			if (!this.isRanged(model.term_arr[i])) {
				model.msg[i] = ssTermdateSettings.msg.get('range');
			}
			model.is_valid = this.isValid(model.msg);
		}
	};
}]);

mod.service('ssTermdateOperate', [function() {
	return {
		add: function (term_arr, moduleModel) {
			if (term_arr.length >= moduleModel.settings.master.term_limit) {
				return false;
			}
			term_arr.push({from: '', to: ''});
			moduleModel.status.push({from:false, to:false});
		},
		delete: function (term_arr, i) {
			term_arr.splice(i, 1);
		},
		reset: function (term_arr) {
			term_arr.splice(1, term_arr.length);
		},
		clear: function (term_arr, i) {
			term_arr[i] = {from: '', to: ''};
		},
	};
}]);

mod.service('ssTermdatePreset', ['ssTermdateOperate', function(ssTermdateOperate) {
	return {
		getPresetNum: function (preset) {
			return Number(preset.slice(-1));
		},
		/**
		 * 単一期間のプリセット値を取得（期間サマリ、日別推移）
		 * daily_0    : 月初〜昨日
		 * daily_(1-3): 今（前、前前）月 [1日〜末日]
		 *
		 * (preset_num -1)ヶ月前 の [1日〜末日] を取得
		 */
		getTerm: function (preset_num) {
			var to = moment().subtract(1, 'days');
			if (preset_num > 0) {
				to = moment().subtract(preset_num - 1, 'month');
				to.date(to.daysInMonth());
			}
			var from = moment(to).date(1);

			// set Javascript DateObj
			return {from: from.toDate(), to: to.toDate()};
		},
		/**
		 * 週次期間のプリセット値を取得
		 */
		getWeekTerm: function (to, preset_num) {
			var w = (to.weekday() < preset_num) ? (preset_num - 7) : preset_num;
			var from = moment(to).weekday(w);

			// set Javascript DateObj
			return {from: from.toDate(), to: to.toDate()};
		},
		/**
		 * 期間サマリ、日時推移用プリセットを適用
		 */
		setTerm: function (term_arr, preset) {
			if (preset === 'custom') {
				ssTermdateOperate.clear(term_arr, 0);
				return false;
			}
			term_arr[0] = this.getTerm(this.getPresetNum(preset));
		},
		/**
		 * 期間比較用プリセットを適用
		 */
		setCompareTerms: function (term_arr, preset) {
			if (!preset) {
				return false;
			}
			// カスタム
			if (preset === 'custom') {
				for (var i = 0; i < term_arr.length; i++) {
					ssTermdateOperate.clear(term_arr, i);
				}
			}
			// 月次比較(当月含む)
			if (preset.indexOf('monthly') !== -1) {
				for (var j = 0; j < term_arr.length; j++) {
					term_arr[j] = this.getTerm(j + 1);
				}
			}
			// 月次比較
			if (preset.indexOf('monthly_past') !== -1) {
				for (var k = 0; k < term_arr.length; k++) {
					term_arr[k] = this.getTerm(k + 2);
				}
			}
			// 週次比較
			if (preset.indexOf('weekly') !== -1) {
				var to = moment().subtract(1, 'days');

				for (var l = 0; l < term_arr.length; l++) {
					if (l > 0) {
						to = moment(term_arr[l - 1].from).subtract(1, 'days');
					}
					term_arr[l] = this.getWeekTerm(to, this.getPresetNum(preset));
				}
			}
			return term_arr;
		},
	};
}]);

mod.service('ssTermdateSettings', ['$q', '$http', 'ssTermdateMsg',
 function($q, $http, ssTermdateMsg) {
	var deferred = null;
	return {
		config: {
			get: function (type) {
				type = type || 'termdate';
				if (deferred) {
					deferred.resolve();
					deferred = null;
				}
				deferred = $q.defer();
				$http({
					url: '/sem/new/api/report/config',
					method: 'GET',
					params: {type: type}
				}).success(function(res) {
					//resolve
					deferred.resolve(res);
				}).error(function(data, status, headers, config) {
					console.log(status);
					deferred.reject(data);
				});

				return deferred.promise;
			}
		},
		msg: {
			get: function (key) {
				return ssTermdateMsg[key];
			},
			delete: function (msg, i) {
				msg.splice(i, 1);
			},
			reset: function (msg) {
				msg.splice(0, msg.length);
			}
		}
	};
}]);

