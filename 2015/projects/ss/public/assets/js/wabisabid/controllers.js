/* Controllers */
var controllers = angular.module('wabisabid.controllers', [])


/**
 * Base
 */
controllers.controller('WabiSabidBaseCtrl', ['$scope', '$rootScope','$routeParams',
	function ($scope,$rootScope,$routeParams) {

		//画面切り換え
		$rootScope.display_type   = '';
		$rootScope.message        = '';

		// クライアントコンボボックス定義
		$scope.clientCombobox = {};
		$scope.clientComboboxConfig = {
			focusMediaIds: [1,2],
			registerApi: function(api) {
				$scope.comboboxApi = api;
			}
		};
	}
]);

/**
 * Hagakure
 */
controllers.controller('HagakureCtrl', ['$scope','$rootScope', '$routeParams','wabisabidConst', '$http', 'ssModal', 'wabisabidService', 'filtersService',
	function ($scope,$rootScope,$routeParams, wabisabidConst,$http, ssModal, wabisabidService, filtersService) {

		// クライアント変更検知用
		$scope.pre_client_id  = '';

		$scope.init = function() {
			// 初期化
			$scope.setting        = [];
			$scope.setting_list   = [];
			$scope.result_list    = [];
			$scope.bidding_list   = [];
			$scope.biddetail_list = [];
			$scope.extcv_list     = [];

			// フォームデータ初期化
			$scope.params = {
				wabisabi_id: '',
				wabisabi_name: '',
				media_cost: 20,
				target_budget_mode: '0',
				target_budget: null,
				target_cpa: null,
				new_bid_rate_max: '2.0',
				new_bid_rate_min: '0.2',
				limit_cpc: null,
				limit_mba: null,
				no_bids_flg: '0',
				reference_cost_pattern: '0',
				bid_days: [
							{id: '0', name: '日'},
							{id: '1', name: '月'},
							{id: '2', name: '火'},
							{id: '3', name: '水'},
							{id: '4', name: '木'},
							{id: '5', name: '金'},
							{id: '6', name: '土'},
							{id: '9', name: '祝'}, ],
				checked_bid_days: [false, true, true, true, true, true, false, false],
				no_sum_days: [
							{id: '0', name: '日'},
							{id: '1', name: '月'},
							{id: '2', name: '火'},
							{id: '3', name: '水'},
							{id: '4', name: '木'},
							{id: '5', name: '金'},
							{id: '6', name: '土'},
							{id: '9', name: '祝'}, ],
				checked_no_sum_days: [false, false, false, false, false, false, false, false],
				sum_start_date: '',
				extcv_list: [],
				extcv_exec_hour: 10,
				filters: [],
				filters_is_open: [
					{ filter_item: false, filter_cond: false, filter_text: false }
				],
			};

			$scope.datepicker = {
				minDate : '2014-01-01',
				maxDate : '2020-01-01',
				format : 'yyyy/MM/dd',
				dateOptions : {
					formatYear: 'yyyy',
					startingDay: 1
				},
				opened : false,
				open : function($event) {
					$event.preventDefault();
					$event.stopPropagation();
					this.opened = true;
				}
			};
			/**
			 * pagination
			 */
			$scope.pagination = {
				currentPage:wabisabidConst.pagination["currentPage"],
				numPages   :wabisabidConst.pagination["numPages"],
				limit      :wabisabidConst.pagination["limit"],
				maxSize    :wabisabidConst.pagination["maxSize"],
				offset     :wabisabidConst.pagination["offset"]
			};
			$scope.$watch(function () {
				$scope.pagination.offset = ($scope.pagination.currentPage - 1) * $scope.pagination.limit;
			});

		};

		// 設定一覧取得定義
		$scope.getSettingList = function(client_id, display_type) {
			wabisabidService.getHagakureSetting(client_id).then(function(res) {
				if (!_.isEmpty(res['data'])) {
					$scope.setting_list = res['data'];
					// 設定変更ボタン押下時のみ、フォーム画面遷移
					$rootScope.display_type = !_.isEmpty(display_type) ? display_type : 'setting';
				} else {
					$rootScope.message = '設定が存在しません。';
				}
				$scope.getExtCvList($scope.clientCombobox.client.id, display_type);
			}, function(error) {
				$rootScope.message = '設定の取得に失敗しました';
			});
		};

		// 外部CV一覧取得定義
		$scope.getExtCvList = function(client_id, display_type) {
			wabisabidService.getExtCvList(client_id).then(function(res) {
				$scope.extcv_list = [];
				if (!_.isEmpty(res['data'])) {
					var tmp_extcv_list = [];
					angular.forEach(res['data']['ext_cv'], function (value, key) {
						tmp_extcv_list[key] = { cv_key: value.cv_key, cv_display: value.cv_display };
					});
					$scope.extcv_list = tmp_extcv_list;
					// 選択済みリセット
					if (!_.isEmpty($scope.params.extcv_list) && display_type !== 'form') {
						$scope.params.extcv_list = [];
					}
				}
			}, function(error) {
				$rootScope.message = '外部CVの取得に失敗しました';
			});
		};

		// フィルタリング系定義
		$scope.addFilter = function () {
			filtersService.addFilter($scope);
		};
		$scope.deleteFilter = function (i) {
			filtersService.deleteFilter($scope, i);
		};
		$scope.clearFilter = function (i) {
			filtersService.clear($scope, i);
		};

		// クライアント選択 or 設定変更ボタン押下
		$scope.$on('ss_combobox_client_change', function() {
			if (!_.isEmpty($scope.clientCombobox)) {
				// クライアント変更のない場合、設定変更
				if ($scope.clientCombobox.client.id === $scope.pre_client_id) {
					$scope.getSettingList($scope.clientCombobox.client.id, 'form');
				} else {
					$scope.getSettingList($scope.clientCombobox.client.id);
					$scope.pre_client_id = $scope.clientCombobox.client.id;
					$scope.init();
				}
			}
		});

		// 新規登録ボタン押下
		$scope.regist = function() {
			$scope.init();
			$rootScope.message = '';
			$rootScope.display_type = 'form';
			window.scrollTo(0, 0);
		};

		// ステータス運用ボタン押下
		$scope.status_on = function(wabisabi_id) {
			$rootScope.message = '';
			ssModal.confirm('ステータスを運用中に変更してもよろしいですか？').result.then(function() {
				wabisabidService.updHagakureStatus(wabisabi_id, '0').then(function() {
					$scope.getSettingList($scope.clientCombobox.client.id);
				}, function(error) {
					$rootScope.message = 'ステータスの変更に失敗しました';
				});
			});
		};

		// ステータス停止ボタン押下
		$scope.status_off = function(wabisabi_id) {
			$rootScope.message = '';
			ssModal.confirm('ステータスを停止中に変更してもよろしいですか？').result.then(function() {
				wabisabidService.updHagakureStatus(wabisabi_id, '9').then(function() {
					$scope.getSettingList($scope.clientCombobox.client.id);
				}, function(error) {
					$rootScope.message = 'ステータスの変更に失敗しました';
				});
			});
		};

		// 設定変更ボタン押下
		$scope.edit = function(wabisabi_id) {
			$rootScope.message = '';
			wabisabidService.getHagakureSettingDetail(wabisabi_id).then(function(res) {

				if (!_.isEmpty(res['data'])) {
					$scope.setting = res['data'];

					// 設定内容を適用
					var models = {
						client:   $scope.clientCombobox.client.id,
						accounts: []
					};
					angular.forEach($scope.setting['account'], function (value, key) {
						models.accounts.push({ media_id: parseInt(value.media_id), account_id: value.account_id });
					});
					$scope.comboboxApi.setModels(models);

					$scope.params.wabisabi_id            = wabisabi_id;
					$scope.params.wabisabi_name          = $scope.setting['setting'].wabisabi_name;
					$scope.params.media_cost             = parseFloat($scope.setting['setting'].media_cost);
					$scope.params.target_budget_mode     = $scope.setting['setting'].target_budget_mode;
					$scope.params.target_budget          = parseInt($scope.setting['setting'].target_budget);
					$scope.params.target_cpa             = parseInt($scope.setting['setting'].target_cpa);
					$scope.params.new_bid_rate_max       = $scope.setting['setting'].new_bid_rate_max;
					$scope.params.new_bid_rate_min       = $scope.setting['setting'].new_bid_rate_min;
					$scope.params.limit_cpc              = parseInt($scope.setting['setting'].limit_cpc);
					$scope.params.limit_mba              = parseInt($scope.setting['setting'].limit_mba);
					$scope.params.no_bids_flg            = $scope.setting['setting'].no_bids_flg;
					$scope.params.reference_cost_pattern = $scope.setting['setting'].reference_cost_pattern;
					$scope.params.checked_bid_days       = $scope.setting['setting'].bid_days;
					$scope.params.checked_no_sum_days    = $scope.setting['setting'].no_sum_days;
					$scope.params.sum_start_date         = $scope.setting['setting'].sum_start_date;
					$scope.params.extcv_exec_hour        = parseInt($scope.setting['setting'].extcv_exec_hour);
					$scope.params.filters                = $scope.setting['filter'];

					for (var i=0; i<$scope.extcv_list.length; i++) {
						for (var j=0; j<$scope.setting['cvname'].length; j++) {
							if ($scope.extcv_list[i].cv_key === $scope.setting['cvname'][j].cv_key) {
								$scope.params.extcv_list.push($scope.extcv_list[i]);
								break;
							}
						}
					}

					$rootScope.display_type = 'form';
				} else {
					$rootScope.message = '設定が存在しません。';
				}
			}, function(error) {
				$rootScope.message = '設定の取得に失敗しました';
			});
			window.scrollTo(0, 0);
		};

		// 処理結果ボタン押下
		$scope.result = function(wabisabi_id) {
			$rootScope.message = '';
			wabisabidService.getHagakureResult(wabisabi_id).then(function(res) {
				if (!_.isEmpty(res['data'])) {
					$scope.result_list  = res['data'];
					$rootScope.display_type = 'result';
				} else {
					$rootScope.message = '処理結果が存在しません。処理結果は最大14日間保持されます。';
				}
			}, function(error) {
				$rootScope.message = '処理結果一覧の取得に失敗しました';
			});
		};

		// 入札履歴ボタン押下
		$scope.bidding = function(wabisabi_id) {
			$rootScope.message = '';
			wabisabidService.getHagakureBidding(wabisabi_id).then(function(res) {
				if (!_.isEmpty(res['data'])) {
					$scope.bidding_list = res['data'];
					$rootScope.display_type = 'bidding';
				} else {
					$rootScope.message = '入札履歴が存在しません。入札履歴は最大14日間保持されます。';
				}
			}, function(error) {
				$rootScope.message = '入札履歴一覧の取得に失敗しました';
			});
			window.scrollTo(0, 0);
		};

		// 削除ボタン押下
		$scope.delete = function(wabisabi_id) {
			$rootScope.message = '';
			ssModal.confirm('削除してもよろしいですか？').result.then(function() {
				wabisabidService.delHagakureSetting(wabisabi_id).then(function() {
					$scope.getSettingList($scope.clientCombobox.client.id);
				}, function(error) {
					$rootScope.message = '設定の削除に失敗しました';
				});
			});
		};

		// 入札履歴詳細閲覧ボタン押下
		$scope.biddetail = function(wabisabi_id, target_date) {
			$rootScope.message = '';
			wabisabidService.getHagakureBidDetail(wabisabi_id, target_date).then(function(res) {
				if (!_.isEmpty(res['data'])) {
					for (i in res['data']) {
						var value = res['data'][i];
						if (value.media_id == 1) {
							value.icon = '/sem/new/assets/img/common/media_icon_mini_yahoo.png' ;
						} else if (value.media_id == 2) {
							value.icon =  '/sem/new/assets/img/common/media_icon_mini_google.png' ;
						}
					}
					$scope.biddetail_list = res['data'];
					$rootScope.display_type = 'biddetail';
				} else {
					$rootScope.message = '入札履歴が存在しません。入札履歴は最大14日間保持されます。';
				}
			}, function(error) {
				$rootScope.message = '入札履歴の詳細一覧の取得に失敗しました';
			});
		};

		// 戻る
		$scope.back = function() {
			$rootScope.message = '';
			$scope.getSettingList($scope.clientCombobox.client.id);
		};

		// バリデーション
		$scope.isValidate = function () {
			validateService.isValidate($scope);
		};

		// 設定を保存するボタン押下
		$scope.save = function() {
			$rootScope.message = '';
			// バリデーション
			if (!$scope.isValidate()) {
				return false;
			}

			// フォームデータ
			var data = {
				ssClient:               $scope.clientCombobox.client.id,
				ssAccount:              $scope.clientCombobox.accounts,
				wabisabi_id:            $scope.params.wabisabi_id,
				wabisabi_name:          $scope.params.wabisabi_name,
				media_cost:             $scope.params.media_cost,
				target_budget_mode:     $scope.params.target_budget_mode,
				target_budget:          $scope.params.target_budget,
				target_cpa:             $scope.params.target_cpa,
				new_bid_rate_max:       $scope.params.new_bid_rate_max,
				new_bid_rate_min:       $scope.params.new_bid_rate_min,
				limit_cpc:              $scope.params.limit_cpc,
				limit_mba:              $scope.params.limit_mba,
				no_bids_flg:            $scope.params.no_bids_flg,
				reference_cost_pattern: $scope.params.reference_cost_pattern,
				bid_days:               $scope.params.checked_bid_days,
				no_sum_days:            $scope.params.checked_no_sum_days,
				sum_start_date:         $scope.params.sum_start_date,
				extcv_list:             $scope.params.extcv_list,
				extcv_exec_hour:        $scope.params.extcv_exec_hour,
				filters:                $scope.params.filters,
			};

			ssModal.confirm('設定を保存してもよろしいですか？').result.then(function() {
				wabisabidService.setHagakureSetting(data).then(function() {
					$scope.getSettingList($scope.clientCombobox.client.id);
				}, function(error) {
					$rootScope.message = '設定の保存に失敗しました';
				});
			});
		};
	}
]);

/**
 * MultiDevice
 */
controllers.controller('MultiDeviceCtrl', ['$scope', '$routeParams', '$http', 'wabisabidService',
	function ($scope, $routeParams, $http, wabisabidService) {

	}
]);

/**
 * Separate
 */
controllers.controller('SeparateCtrl', ['$scope', '$routeParams', '$http', 'wabisabidService',
	function ($scope, $routeParams, $http, wabisabidService) {

	}
]);
