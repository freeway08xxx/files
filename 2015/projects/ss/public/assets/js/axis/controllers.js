/* Controllers */
var controllers = angular.module("axis.controllers", [])
controllers.controller("BaseCtrl", ["$scope", "$routeParams", "$http", function ($scope, $routeParams, $http) {



}]);

/**
 * 「レポート」画面
 */
controllers.controller("ReportCtrl", ["$scope", "$routeParams", "$http", function ($scope, $routeParams, $http) {

		// レポート出力画面用にセット
		$scope.path = "/sem/new/axis/report/";
		$scope.clientCombobox = {};
		$scope.clientComboboxConfig = {
			registerApi : function(api){
				$scope.comboboxApi = api;
			}
		}

		// 画面設定Obj
		$scope.settings = {
			tab : $routeParams.tab ? $routeParams.tab : "form",
			is_report_display: false
		};

		if (window.location.pathname.indexOf("display") >= 0) {
			$scope.settings.is_report_display = true;
		}

		$scope.params = {};
		$scope.msg = "";

		$scope.is_processing = false;

		// テンプレート一覧取得
		$scope.template_list = null; // expected object, isNull判定のため null

		$scope.getTemplateList = function (client_id) {
			$http.post($scope.path + "templatelist.json", client_id)
				.success(function (data) {
					$scope.template_list = data;
				})
				.error(function (data, status) {
					console.log(status);
				});
		};

		// 外部CV一覧取得
		$scope.ext_cv_list = [];

		$scope.getExtCvList = function (client_id) {
			$http.get("/sem/new/api/extcv/index/" + client_id)
				.success(function (data) {
					var ext_cv_list = [];
					angular.forEach(data["ext_cv"], function (value, key) {
						ext_cv_list[key] = { cv_key: value.cv_key, cv_display: value.cv_display };
					});
					$scope.ext_cv_list = ext_cv_list;
					// 選択済みリセット
					$scope.params.ext_cv_list = [];
				})
				.error(function (data, status) {
					console.log(status);
				});
		};

		$scope.download = function (id) {
			if (id) {
				window.location = "/sem/new/axis/export/download/" + id;
			}
		};
}])

	/**
	 * レポート作成画面　フォーム部分
	 */
controllers.controller("ReportFormCtrl", ["$scope", "$modal", "$http", "FiltersService", "ReportFiltersService", "ExtCvFiltersService","axisRestApi","axisConst","tableSetting", 
		function ($scope, $modal, $http, FiltersService, ReportFiltersService, ExtCvFiltersService,axisRestApi,axisConst,tableSetting) {

		// 初期値
		$.extend($scope.params, {
			report_type: "summary",
			summary_type: "account",
			device_type: "0",
			category_type: "account_id",
			media_cost: 20,
			only_has_imp: "",
			only_has_click: "",
			only_has_conv: "",
			ext_cv_list: [],
			termdate: {
				getReportType: function () {
					return $scope.params.report_type;
				}
			},
			filters: [],
			filters_is_open: [
				{ filter_item: false, filter_cond: false, filter_text: false }
			],
			report_filters: [],
			report_filters_is_open: [
				{ filter_item: false, filter_max: false, filter_min: false }
			],
			ext_cv_filters: [],
			ext_cv_filters_is_open: [
				{ filter_elem: false, filter_item: false, filter_max: false, filter_min: false }
			],
			export_format: "tsv",
			export_type: "display",
			report_name: null,
			send_mail_flg: false
		});



		$scope.termdate_config = {
			title: "集計期間",
			datepicker: {
				format: 'yyyy/MM/dd'
			}
		};



		// サマリ種別切替時にフィルタリングリセット
		$scope.resetFilter = function () {
			FiltersService.resetFilter($scope);
		};
		$scope.resetExtCv = function () {
			// 外部CV選択不可時には選択済みリセット
			if ($scope.params.summary_type === "ad" || $scope.params.summary_type === "domain" || $scope.params.summary_type === "url" || $scope.params.summary_type === "query") {
				$scope.params.ext_cv_list = [];
				ExtCvFiltersService.resetFilter($scope);
			}
		};

		// Validate
		$scope.isValidForm = function () {
			// クライアント選択チェック
			if (!$scope.clientCombobox.client) {
				$scope.msg = "クライアントが選択されていません。";
				return false;
			}
			// アカウント選択チェック
			if ($scope.clientCombobox.accounts.length === 0) {
				$scope.msg = "アカウントが選択されていません。";
				return false;
			}

			// 外部CV選択数チェック
			if ($scope.params.report_type === "summary" && $scope.params.ext_cv_list.length > 30) {
				$scope.msg = "レポート種別が期間サマリの場合、外部CVは30個まで選択できます。";
				return false;
			} else if ($scope.params.report_type !== "summary" && $scope.params.ext_cv_list.length > 10) {
				$scope.msg = "レポート種別が期間サマリ以外の場合、外部CVは10個まで選択できます。";
				return false;
			}

			// フィルタリング項目重複チェック
			angular.forEach ($scope.params.filters, function (obj) {
				settled = FiltersService.isSettingFilter($scope, obj["filter_item"]);
				if (settled) {
					$scope.msg = "フィルタリング設定に重複した項目があります。";
					return false;
				}
			});
			angular.forEach ($scope.params.report_filters, function (obj) {
				settled = ReportFiltersService.isSettingFilter($scope, obj["filter_item"]);
				if (settled) {
					$scope.msg = "レポートフィルタリング設定に重複した項目があります。";
					return false;
				}
			});
			angular.forEach ($scope.params.ext_cv_filters, function (obj) {
				settled = ExtCvFiltersService.isSettingFilter($scope, obj["filter_elem"], obj["filter_item"]);
				if (settled) {
					$scope.msg = "レポートフィルタリング設定(外部CV個別)に重複した項目があります。";
					return false;
				}
			});

			// 期間選択チェック
			var is_valid = $scope.params.termdate.is_valid;
			if (!is_valid) {
				$scope.msg = $scope.params.termdate.msg;
			}

            return is_valid;
		};

		// サーバーへリクエスト送信
		$scope.submit = function () {
			// Validate
			if (!$scope.isValidForm()) {
				return false;
			}
			$scope.is_processing = true;

			/**
			 * 非同期 パラメータ追加
			 *
			*/
			//if($scope.params.report_type === "summary"){
				$scope.params.report_term        = ($scope.params.report_type === 'term_compare') ? $scope.params.termdate.settings.term_compare_set : $scope.params.termdate.settings.term_set;
				$scope.params.category_genre_id  = ($scope.clientCombobox.categoryGenre)          ? $scope.clientCombobox.categoryGenre.id : null;
				$scope.params.start_date         = [moment($scope.params.termdate.term_arr[0].from).format("YYYY/MM/DD")];
				$scope.params.end_date           = [moment($scope.params.termdate.term_arr[0].to).format("YYYY/MM/DD")];
				$scope.params.ssClient           = $scope.clientCombobox.client.id;
				$scope.params.ssAccount          = $scope.clientCombobox.accounts;
				$scope.params.option_list        = { only_has_imp: $scope.params.only_has_imp, only_has_click: $scope.params.only_has_click, only_has_conv: $scope.params.only_has_conv };
				var filters                      = _filters(); 
				$scope.params.filter_list        = filters.filter_list;
				$scope.params.report_filter_list = filters.report_filter_list;
				$scope.params.ext_cv_filter_list = filters.ext_cv_filter_list;
				$scope.params.termdate           = "";

				axisRestApi.getReportdata($scope.params).then(function(res) {
					console.log(res);


					$scope.async_data    = res;
					$scope.setCell();
					$scope.is_processing = false;

				});

			//submit
			//}else{
				/**
				 * サーバ送信用にデータ整形
				 */
				// var form = $("#report_form");
				// form.find("input[name='start_date']").val(moment($scope.params.termdate.term_arr[0].from).format("YYYY/MM/DD"));
				// form.find("input[name='end_date']").val(moment($scope.params.termdate.term_arr[0].to).format("YYYY/MM/DD"));
				// form.attr("action", $scope.path + $scope.params.export_type).submit();
			//}







			//filters
			function _filters(){
				var res         = {
					filter_list:[],
					report_filter_list:[],
					ext_cv_filter_list:[]
				};

				angular.forEach($scope.params.filters, function (value, key) {
					res.filter_list[key] = { filter_item: value.filter_item, filter_cond: value.filter_cond, filter_text: value.filter_text };
				});

				angular.forEach($scope.params.report_filters, function (value, key) {
					res.report_filter_list[key] = { filter_item: value.filter_item, filter_min: value.filter_min, filter_max: value.filter_max };
				});

				angular.forEach($scope.params.ext_cv_filters, function (value, key) {
					res.ext_cv_filter_list[key] = { filter_elem: value.filter_elem, filter_item: value.filter_item, filter_min: value.filter_min, filter_max: value.filter_max };
				});
				return res;
			}
		};


		// テンプレートとして保存
		$scope.save = function () {
			// 保存用にモデルをコピー
			$scope.ins_param = {
				report_name: $scope.params.report_name,
				send_mail_flg: $scope.params.send_mail_flg
			};

			$modal.open({
				templateUrl: "tmpl_saveTemplate",
				controller: "TemplateCtrl",
				scope: $scope
			});
		};
}])



	/**
	 *  レポート作成画面　テンプレート一覧　コントローラー
	 */
controllers.controller("ReportTemplateCtrl", ["$scope", "$http", "$modal", "FiltersService", "ReportFiltersService", "ExtCvFiltersService", function ($scope, $http, $modal, FiltersService, ReportFiltersService, ExtCvFiltersService) {
		$scope.params.selected_id = null;

		// クライアント選択時にテンプレート一覧取得
		$scope.$on("ss_combobox_client_change", function(event, data) {
			var client_id = { "client_id": $scope.clientCombobox.client.id };
			$scope.getTemplateList(client_id);
		});

		$scope.applyTemplate = function (i) {
			$scope.params.selected_id = i;
			var t = $scope.template_list[i];

			// アカウントを適用
			var accounts = [];
			angular.forEach(t.account_list, function (value, key) {
				accounts[key] = { media_id: value.media_id, account_id: value.account_id };
			});
/*
			$scope.comboboxApi.setModels({
				client: $scope.params.client_id,
				accounts: accounts
			});
*/

			$scope.params.template_name = t.template_name;
			$scope.params.report_type   = t.report_type.value;
			$scope.params.summary_type  = t.summary_type.value;
			$scope.params.device_type   = t.device_type;
			$scope.params.category_type = t.category_type;
			$scope.params.media_cost    = t.media_cost;
			$scope.params.ext_cv_list   = t.ext_cv_list;

			// オプションを適用
			angular.forEach(t.option_list, function (value, key) {
				$scope.params[key] = value;
			});

			// 期間設定を適用
			$scope.params.termdate.method.applyPresetTerms(t.term_count, t.report_term.value);

			// フィルタを適用
			FiltersService.resetFilter($scope);
			angular.forEach(t.filter_list, function (value, key) {
				$scope.params.filters[key] = value;
			});
			ReportFiltersService.resetFilter($scope);
			angular.forEach(t.report_filter_list, function (value, key) {
				$scope.params.report_filters[key] = value;
			});
			ExtCvFiltersService.resetFilter($scope);
			angular.forEach(t.ext_cv_filter_list, function (value, key) {
				$scope.params.ext_cv_filters[key] = value;
			});

			if (t.report_name) {
				$scope.params.export_type   = "export";
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

			var msg = "※注意※\n削除すると元には戻せませんが、よろしいですか？\n\nテンプレート名：";
			if (window.confirm(msg + $scope.template_list[i].template_name)) {
				var template_id = { "template_id": $scope.template_list[i].id };

				$http.post($scope.path + "deltemplate.json", template_id)
					.success(function () {
						var client_id = { "client_id": $scope.clientCombobox.client.id };
						$scope.getTemplateList(client_id);
					})
					.error(function (data, status) {
						console.log(status);
					});

				$scope.params.selected_id = null;
			}
		};
}])

	/**
	 * テンプレート追加　モーダルウインドウ　コントローラー
	 * ※ $modalInstance.close したいのでコントローラー分けてます
	 */
controllers.controller("TemplateCtrl", ["$scope", "$modalInstance", "$http", function ($scope, $modalInstance, $http) {

		$scope.$watch("params.selected_id", function(newTemp, oldTemp, scope) {
			// テンプレート選択時
			if (newTemp !== null) {
				$scope.is_update = true;
				// テンプレート名をセット
				$scope.params.template_name = $scope.template_list[$scope.params.selected_id].template_name;

				// テンプレート名が変更された場合、新規保存
				$scope.$watch("params.template_name", function(newValue, oldValue, scope) {
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
			console.log("insert");

			// テンプレ保存後、レポート出力するかもなのでメインフォームにセット
			$scope.params.report_name   = $scope.ins_param.report_name;
			$scope.params.send_mail_flg = $scope.ins_param.send_mail_flg;

			var report_term = $scope.params.termdate.settings.term_set;
			if ($scope.params.report_type === 'term_compare') {
				report_term = $scope.params.termdate.settings.term_compare_set;
			}

			var category_genre_id = ($scope.clientCombobox.categoryGenre) ? $scope.clientCombobox.categoryGenre.id : null;
			var template_memo = ($scope.params.template_memo) ? $scope.params.template_memo : "";

			var filter_list = [];
			angular.forEach($scope.params.filters, function (value, key) {
				filter_list[key] = { filter_item: value.filter_item, filter_cond: value.filter_cond, filter_text: value.filter_text };
			});

			var report_filter_list = [];
			angular.forEach($scope.params.report_filters, function (value, key) {
				report_filter_list[key] = { filter_item: value.filter_item, filter_min: value.filter_min, filter_max: value.filter_max };
			});

			var ext_cv_filter_list = [];
			angular.forEach($scope.params.ext_cv_filters, function (value, key) {
				ext_cv_filter_list[key] = { filter_elem: value.filter_elem, filter_item: value.filter_item, filter_min: value.filter_min, filter_max: value.filter_max };
			});

			var data = {
				id:                 "",
				ssClient:           $scope.clientCombobox.client.id,
				ssAccount:          $scope.clientCombobox.accounts,
				ssCategoryGenre:    category_genre_id,
				template_name:      $scope.params.template_name,
				report_type:        $scope.params.report_type,
				report_term:        report_term,
				term_count:         $scope.params.termdate.term_count,
				category_type:      $scope.params.category_type,
				summary_type:       $scope.params.summary_type,
				device_type:        $scope.params.device_type,
				media_cost:         $scope.params.media_cost,
				option_list:        { only_has_imp: $scope.params.only_has_imp, only_has_click: $scope.params.only_has_click, only_has_conv: $scope.params.only_has_conv },
				ext_cv_list:        $scope.params.ext_cv_list,
				filter_list:        filter_list,
				report_filter_list: report_filter_list,
				ext_cv_filter_list: ext_cv_filter_list,
				export_format:      $scope.params.export_format,
				report_name:        $scope.params.report_name,
				template_memo:      template_memo,
				send_mail_flg:      $scope.params.send_mail_flg
			};

			$scope.is_processing = true;

			$http.post($scope.path + "instemplate.json", data)
				.success(function () {
					var client_id = { "client_id": $scope.clientCombobox.client.id };
					$scope.getTemplateList(client_id);
					$scope.is_processing = false;
				})
				.error(function (data, status) {
					console.log(status);
				});

			$modalInstance.close();
		};

		// テンプレート上書き保存
		$scope.update = function () {
			console.log("update");

			// テンプレ保存後、レポート出力するかもなのでメインフォームにセット
			$scope.params.report_name   = $scope.ins_param.report_name;
			$scope.params.send_mail_flg = $scope.ins_param.send_mail_flg;

			var report_term = $scope.params.termdate.settings.term_set;
			if ($scope.params.report_type === 'term_compare') {
				report_term = $scope.params.termdate.settings.term_compare_set;
			}

			var category_genre_id = ($scope.clientCombobox.categoryGenre) ? $scope.clientCombobox.categoryGenre.id : null;
			var template_memo = ($scope.params.template_memo) ? $scope.params.template_memo : "";

			var filter_list = [];
			angular.forEach($scope.params.filters, function (value, key) {
				filter_list[key] = { filter_item: value.filter_item, filter_cond: value.filter_cond, filter_text: value.filter_text };
			});

			var report_filter_list = [];
			angular.forEach($scope.params.report_filters, function (value, key) {
				report_filter_list[key] = { filter_item: value.filter_item, filter_min: value.filter_min, filter_max: value.filter_max };
			});

			var ext_cv_filter_list = [];
			angular.forEach($scope.params.ext_cv_filters, function (value, key) {
				ext_cv_filter_list[key] = { filter_elem: value.filter_elem, filter_item: value.filter_item, filter_min: value.filter_min, filter_max: value.filter_max };
			});

			var data = {
				id:                 $scope.template_list[$scope.params.selected_id].id,
				ssClient:           $scope.clientCombobox.client.id,
				ssAccount:          $scope.clientCombobox.accounts,
				ssCategoryGenre:    category_genre_id,
				template_name:      $scope.params.template_name,
				report_type:        $scope.params.report_type,
				report_term:        report_term,
				term_count:         $scope.params.termdate.term_count,
				category_type:      $scope.params.category_type,
				summary_type:       $scope.params.summary_type,
				device_type:        $scope.params.device_type,
				media_cost:         $scope.params.media_cost,
				option_list:        { only_has_imp: $scope.params.only_has_imp, only_has_click: $scope.params.only_has_click, only_has_conv: $scope.params.only_has_conv },
				ext_cv_list:        $scope.params.ext_cv_list,
				filter_list:        filter_list,
				report_filter_list: report_filter_list,
				ext_cv_filter_list: ext_cv_filter_list,
				export_format:      $scope.params.export_format,
				report_name:        $scope.params.report_name,
				template_memo:      template_memo,
				send_mail_flg:      $scope.params.send_mail_flg
			};

			$scope.is_processing = true;

			$http.post($scope.path + "instemplate.json", data)
				.success(function () {
					var client_id = { "client_id": $scope.clientCombobox.client.id };
					$scope.getTemplateList(client_id);
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
controllers.controller("ReportViewCtrl", ["$scope", "$rootScope", function ($scope, $rootScope) {

		// レポート出力画面は、$routeProvider を経由しないので、手動でセット
		$rootScope.mainTab = "report";

		$scope.back = function () {
			window.history.back();
		};
}])

	/**
	 *  外部CV選択フォーム コントローラー
	 */
controllers.controller("FormExtCvCtrl", ["$scope", function ($scope) {
		// クライアント選択時に外部CV一覧取得
		$scope.$on("ss_combobox_client_change", function(event, data) {
			var client_id = { "client_id": $scope.clientCombobox.client.id };
			$scope.getExtCvList(client_id);
		});
}])

	/**
	 *  フィルタリング選択フォーム コントローラー
	 */
controllers.controller("FormFilterCtrl", ["$scope", "FiltersService", function ($scope, FiltersService) {

		$scope.is_showFilter = function (filter_item) {
			return FiltersService.isShowFilter($scope, filter_item);
		};

		$scope.addFilter = function () {
			FiltersService.addFilter($scope);
		};

		$scope.deleteFilter = function (i) {
			FiltersService.deleteFilter($scope, i);
		};

		$scope.clearFilter = function (i) {
			FiltersService.clear($scope, i);
		};
}])

	/**
	 *  レポートフィルタリング選択フォーム コントローラー
	 */
controllers.controller("FormReportFilterCtrl", ["$scope", "ReportFiltersService", function ($scope, ReportFiltersService) {

		$scope.is_showReportFilter = function (filter_item) {
			return ReportFiltersService.isShowFilter($scope, filter_item);
		};

		$scope.addReportFilter = function () {
			ReportFiltersService.addFilter($scope);
		};

		$scope.deleteReportFilter = function (i) {
			ReportFiltersService.deleteFilter($scope, i);
		};

		$scope.clearReportFilter = function (i) {
			ReportFiltersService.clear($scope, i);
		};
}])

	/**
	 *  外部CVフィルタリング選択フォーム コントローラー
	 */
controllers.controller("FormExtCvFilterCtrl", ["$scope", "ExtCvFiltersService", function ($scope, ExtCvFiltersService) {

		$scope.addExtCvFilter = function () {
			ExtCvFiltersService.addFilter($scope);
		};

		$scope.deleteExtCvFilter = function (i) {
			ExtCvFiltersService.deleteFilter($scope, i);
		};

		$scope.clearExtCvFilter = function (i) {
			ExtCvFiltersService.clear($scope, i);
		};
}]);
