/* Controllers */
angular.module('quickManage.controllers', [])

	/**
	 * 「レポート」画面
	 */
	.controller('QuickManageReportCtrl', ['$scope', '$routeParams', '$http',
		function ($scope, $routeParams, $http) {

		$scope.path = '/sem/new/quickmanage/report/';

		//画面設定Obj
		$scope.settings = {
			tab : $routeParams.tab ? $routeParams.tab : "form",
			is_report_display: false
		};

		if (window.location.pathname.indexOf('display') >= 0) {
			$scope.settings.is_report_display = true;
		}

		$scope.params = {};
		$scope.msg = '';

		$scope.is_processing = false;

		$scope.template_list = null; // expected object, isNull判定のため null

		$scope.getTemplateList = function () {
			$http.get($scope.path + 'templatelist.json')
				.success(function (data) {
					$scope.template_list = data;
				})
				.error(function (data, status) {
					console.log(status);
				});
		};

		$scope.download = function (id) {
			if (id) {
				window.location = '/sem/new/quickmanage/export/download/' + id;
			}
		};
	}])

	/**
	 *  「目標設定」画面
	 */
	.controller("QuickManageAimCtrl", ['$scope', '$routeParams',
		function ($scope, $routeParams) {
		$scope.path = "/sem/new/quickmanage/aim/";

		//画面設定Obj
		$scope.settings = {
			tab : $routeParams.tab ? $routeParams.tab : "list"
		};

		$scope.params = {
			name: 'aim'
		};
		$scope.file = {};
		$scope.msg = '';
	}])

	/**
	 *  「値引設定」画面
	 */
	.controller("QuickManageDiscountCtrl", ['$scope', '$routeParams',
		function ($scope, $routeParams) {
		$scope.path = "/sem/new/quickmanage/discount/";

		//画面設定Obj
		$scope.settings = {
			tab : $routeParams.tab ? $routeParams.tab : "list"
		};

		$scope.params = {
			name: 'discount'
		};

		$scope.file = {};
		$scope.msg = '';
	}])

	/**
	 * レポート作成画面　フォーム部分
	 */
	.controller("ReportFormCtrl", ['$scope', '$modal',
		function ($scope, $modal) {
		$.extend($scope.params, {
			report_type: 'summary',
			position_id: '4',
			summary_type: 'all',
			summary_option: {
				use_customer_class_summary: {value: false, visible: true},
				use_business_type_summary:  {value: false ,visible: true},
				use_media_summary:          {value: false ,visible: true},
				use_product_summary:        {value: false ,visible: true},
				use_device_summary:         {value: false ,visible: true}
			},
			option: {
				use_aim_list:    {value: false ,visible: true},
				use_ext_cv_list: {value: false ,visible: true},
				use_discount:    {value: false ,visible: true},
				show_only_cost:  {value: false ,visible: true},
				use_forecast:    {value: false ,visible: true}
			},
			termdate: {
				getReportType: function () {
					return $scope.params.report_type;
				}
			},
			filter: {},
			filter_selected: 'customer_class',
			export_type: 'display',
			report_name: null,
			send_mail_flg: false
		});
		$scope.termdate_config = {
			title: '集計期間',
			datepicker: {
				format: 'yyyy/MM/dd'
			}
		};

		// 有効オプションの表示切り替え
		$scope.$watch('params.report_type', function(newValue, oldValue, scope) {
			var opt = scope.params.option;

			opt.use_forecast.visible = false;
			opt.use_aim_list.visible = false;

			if (newValue === 'summary') {
				opt.use_aim_list.visible = true;
				opt.use_forecast.value = false;
			}
			if (newValue === 'daily') {
				opt.use_aim_list.visible = true;
				opt.use_forecast.visible = true;
			}
			if (newValue === 'term_compare') {
				opt.use_forecast.value = false;
				opt.use_aim_list.value = false;
			}
		});

		/**
		 * 関連するオプションの値＆表示状態を制御
		 * （外部CVとプロダクト・デバイスの相互チェック状態を監視）
		 */
		$scope.$watch('params.option.use_ext_cv_list.value', function (newValue, oldValue, scope) {
			var sopt = scope.params.summary_option;

			sopt.use_product_summary.visible = true;
			sopt.use_device_summary.visible  = true;
			if (newValue) {
				sopt.use_product_summary = {value: false, visible: false};
				sopt.use_device_summary  = {value: false, visible: false};
			}
		});
		$scope.$watch('params.summary_option', function (newValue, oldValue, scope) {
			scope.params.option.use_ext_cv_list.visible = true;
			if (newValue.use_product_summary.value || newValue.use_device_summary.value) {
				scope.params.option.use_ext_cv_list = {value: false, visible: false};
			}
		}, true);

		$scope.resetFilter = function () {
			$scope.params.filter = {};
		};

		// validate
		$scope.isValidForm = function () {
			var is_valid = false;

			is_valid = $scope.params.termdate.is_valid;
			if (!is_valid) {
				var msg = _.chain($scope.params.termdate.msg)
				           .clone()
				           .compact()
				           .uniq()
				           .value();
				$scope.msg = (msg.length > 0) ? msg.toString().replace(',', '<br>', 'g') : null;
			}

			if ($scope.params.report_type === 'daily' && $scope.params.option.use_forecast.value) {
				if ($scope.isValidForecastTerm()) {
					is_valid = true;
				}
			}

			return is_valid;
		};
		$scope.isValidForecastTerm = function () {
			var from = moment($scope.params.termdate.term_arr[0].from);
			if (from.date() !== 1) {
				$scope.msg = '着地予想表示の場合、開始日を初日に設定してください。';
				return false;
			}
			if (moment($scope.params.termdate.term_arr[0].to).diff(from, "days") < 0) {
				$scope.msg = '着地予想表示の場合、終了日は初日以降に設定してください。';
				return false;
			}

			return true;
		};

		// サーバーへリクエスト送信
		$scope.submit = function () {
			// validate
			if (!$scope.isValidForm()) {
				return false;
			}

			$scope.is_processing = true;

			// サーバ送信用にデータ整形
			var form = $('#report_form');

			form.find('input[name="start_date"]')
				.val(moment($scope.params.termdate.term_arr[0].from).format('YYYY/MM/DD'));
			form.find('input[name="end_date"]')
				.val(moment($scope.params.termdate.term_arr[0].to).format('YYYY/MM/DD'));

			form.attr('action', $scope.path + $scope.params.export_type)
				.submit();
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
				controller: 'TemplateInsertCtrl',
				scope: $scope
			});
		};
	}])

	/**
	 *  レポート作成画面　テンプレート一覧　コントローラー
	 */
	.controller("ReportTemplateCtrl", ['$scope', '$http', '$modal',
		function ($scope, $http, $modal) {
		$scope.selected_id = null;

		$scope.getTemplateList();

		$scope.applyTemplate = function (i) {
			$scope.selected_id = i;

			var t = $scope.template_list[i];
			var p = $scope.params;

			p.report_type  = t.report_type.value;
			p.summary_type = t.summary_type.value;
			p.position_id  = t.position_id;

			// オプションを適用
			angular.forEach(p.summary_option, function (option, key) {
				option.value = (t.summary_option[key]) ? true : false;
			});
			angular.forEach(p.option, function (option, key) {
				option.value = (t.option[key]) ? true : false;
			});

			// フィルタを適用
			p.filter = {};
			angular.forEach(t.filter, function (option, key) {
				p.filter_selected = key;
				p.filter[key] = option;
			});

			// 期間プリセットを適用
			p.termdate.method.applyPresetTerms(t.term_count, t.report_term.value);

			// 出力オプション
			if (t.report_name) {
				p.export_type   = 'export';
				p.report_name   = t.report_name;
				p.send_mail_flg = t.send_mail_flg;
			}
		};

		$scope.isSelected = function (i) {
			return $scope.selected_id === i;
		};

		// angular-ui で popover 開くときに、$scope.applyTemplate を起動させない
		$scope.info = function ($event) {
			$event.stopPropagation();
		};

		$scope.edit = function ($event, i) {
			$event.stopPropagation();

			$scope.editing = angular.copy($scope.template_list[i]);
			$scope.editing.report_type  = $scope.editing.report_type.value;
			$scope.editing.summary_type = $scope.editing.summary_type.value;
			$scope.editing.report_term  = $scope.editing.report_term.value;

			$modal.open({
				templateUrl: 'tmpl_editTemplate',
				controller: 'TemplateEditCtrl',
				scope: $scope
			});
		};

		$scope.delete = function ($event, i) {
			$event.stopPropagation();

			var msg = '※注意※\n削除すると元には戻せませんが、よろしいですか？\n\nテンプレート名：';
			if (window.confirm(msg + $scope.template_list[i].template_name)) {
				var id = {'id': $scope.template_list[i].id};

				$http.post($scope.path + 'deltemplate.json', id)
					.success(function () {
						$scope.getTemplateList();
					})
					.error(function (data, status) {
						console.log(status);
					});

				$scope.selected_id = null;
			}
		};
	}])

	/**
	 * テンプレート追加　モーダルウインドウ　コントローラー
	 * ※ $modalInstance.close したいのでコントローラー分けてます
	 */
	.controller('TemplateInsertCtrl', ['$scope', '$modalInstance', '$http',
		function ($scope, $modalInstance, $http) {
		// 入力チェック
		$scope.$watch(function() {
			$scope.is_valid = false;
			if ($scope.params.template_name && $scope.ins_param.report_name) {
				$scope.is_valid = true;
			}
		});

		$scope.insert = function () {
			// テンプレ保存後、レポート出力するかもなのでメインフォームにセット
			$scope.params.report_name   = $scope.ins_param.report_name;
			$scope.params.send_mail_flg = $scope.ins_param.send_mail_flg;

			var report_term = $scope.params.termdate.settings.term_set;
			if ($scope.params.report_type === 'term_compare') {
				report_term = $scope.params.termdate.settings.term_compare_set;
			}

			var template_memo = ($scope.params.template_memo) ? $scope.params.template_memo : '';

			// option から不要な要素を削除
			var summary_option = {};
			var option = {};
			angular.forEach($scope.params.summary_option, function (opt_val, key) {
				summary_option[key] = opt_val.value;
			});
			angular.forEach($scope.params.option, function (opt_val, key) {
				option[key] = opt_val.value;
			});

			var data = {
				id: '',
				template_name:  $scope.params.template_name,
				template_memo:  template_memo,
				report_type:    $scope.params.report_type,
				summary_type:   $scope.params.summary_type,
				position_id:    $scope.params.position_id,
				summary_option: summary_option,
				option:         option,
				report_term:    report_term,
				term_count:     $scope.params.termdate.term_count,
				filter:         $scope.params.filter,
				report_name:    $scope.params.report_name,
				send_mail_flg:  $scope.params.send_mail_flg
			};

			$scope.is_processing = true;
			$http.post($scope.path + 'instemplate.json', data)
				.success(function () {
					$scope.getTemplateList();
					$scope.is_processing = false;
				})
				.error(function (data, status) {
					console.log(status);
				});

			$modalInstance.close();
		};
	}])

	/**
	 * テンプレート編集　モーダルウインドウ　コントローラー
	 * ※ $modalInstance.close したいのでコントローラー分けてます
	 */
	.controller('TemplateEditCtrl', ['$scope', '$modalInstance', '$http',
		function ($scope, $modalInstance, $http) {

		// オプションの表示制御用にモデルをコピー
		$scope.config = {
			summary_option: angular.copy($scope.params.summary_option),
			option:         angular.copy($scope.params.option)
		};

		// オプションの表示切り替え
		$scope.$watch('editing.report_type', function(newValue, oldValue, scope) {
			var opt = scope.config.option;

			opt.use_forecast.visible = false;
			opt.use_aim_list.visible = false;

			if (newValue === 'summary') {
				opt.use_aim_list.visible = true;
			}
			if (newValue === 'daily') {
				opt.use_aim_list.visible = true;
				opt.use_forecast.visible = true;
			}
		});

		/**
		 * 関連するオプションの値＆表示状態を制御
		 * （外部CVとプロダクト・デバイスの相互チェック状態を監視）
		 */
		$scope.$watch('editing.option.use_ext_cv_list', function (newValue, oldValue, scope) {
			var opt = scope.config.summary_option;

			opt.use_product_summary.visible = true;
			opt.use_device_summary.visible  = true;
			if (newValue) {
				opt.use_product_summary = {value: false, visible: false};
				opt.use_device_summary  = {value: false, visible: false};
			}
		});
		$scope.$watch('editing.summary_option', function (newValue, oldValue, scope) {
			scope.config.option.use_ext_cv_list.visible = true;
			if (newValue.use_product_summary || newValue.use_device_summary) {
				scope.config.option.use_ext_cv_list = {value: false, visible: false};
			}
		}, true);

		$scope.update = function () {
			$scope.is_processing = true;

			$http.post($scope.path + 'instemplate.json', $scope.editing)
				.success(function () {
					$scope.getTemplateList();
					$scope.is_processing = false;
				})
				.error(function (data, status) {
					console.log(status);
				});

			$modalInstance.close();
		};
	}])

	/**
	 * レポート作成画面　出力結果部分　コントローラー
	 */
	.controller("ReportViewCtrl", ['$scope', '$rootScope',
		function ($scope, $rootScope) {

		// レポート出力画面は、$routeProvider を経由しないので、手動でセット
		$rootScope.mainTab = 'report';

		$scope.back = function () {
			window.history.back();
		};
	}])

	/**
	 *  「目標登録」「値引登録」フォームコントローラ
	 */
	.controller("RegistFormCtrl", ['$scope', function ($scope) {
		$scope.csv = {
			name: 'test.csv',
			data: []
		};

		$scope.params.report_type = 'summary';
		$scope.params.termdate = {
			getReportType: function () {
				return $scope.params.report_type;
			}
		};
		$scope.termdate_config = {
			title: '設定期間',
			datepicker: {
				format: 'yyyy/MM'
			}
		};

		$scope.download = function () {
			window.location = $scope.path + 'download';
		};

		// 設定シートアップロード入力チェック
		$scope.validateFile = function () {
			$scope.msg = '';

			if (!$scope.file.dataurl) {
				$scope.msg = 'アップロードファイルを選択してください。';
				return false;
			}

			if ($scope.file.filetype !== 'text/csv' &&
			    $scope.file.filetype.indexOf('ms-excel') === -1) {
				$scope.msg = '設定シートはCSV形式でアップロードしてください。';
				return false;
			}

			return true;
		};

		// 設定シートをサーバーへアップロード
		$scope.submit = function () {
			if ($scope.validateFile()) {
				console.log('valid');

				// データ変換してサーバ送信
				$('#' + $scope.params.name + '_form').find('input[name="file_dataurl"]')
					.val($scope.file.dataurl);
				$('#' + $scope.params.name + '_form')
					.attr('action', $scope.path + 'upload')
					.submit();
			} else {
				console.log('invalid');
			}

			return false;
		};
	}]);