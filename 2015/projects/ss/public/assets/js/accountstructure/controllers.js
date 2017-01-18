/* Controllers */
angular.module('accountStructure.controllers', [])
	
	/**
	 * 「アカウント」画面
	 */
	.controller('AccountStructureConditionCtrl', ['$scope', '$http', '$routeParams', function ($scope, $http, $routeParams)  {

		// タブの初期設定値
		$scope.mainTab = 'condition';

		//画面設定Obj
		$scope.settings = {
			tab : $routeParams.tab ? $routeParams.tab : "account",
		};

		$scope.clientCombobox = {};

		$scope.param = {
			// フィルターリスト初期化
			filters : [ {
				filter_item : '',
				filter_cond : '',
				filter_cond_url : '',
				filter_text : '',
				filter_url : '',
				filter_status : '',
				link_url : '',
				custom_url : '',
				t_location : '',
				t_userlist : '',
				t_placement : ''
		} ],
			// 出力フォーマット選択ラジオボタン初期化
			format_type: 'csv',
			// 出力項目選択チェックボックス初期化
			output_kind: {
				cpn: true,
				adgroup: true,
				kw: true,
				negativekw: true,
				td: true,
				target: true,
				target_area: true,
				target_schedule: true,
				target_gender: true,
				target_age: true,
				target_userlist: true,
				target_placement: true
			},
			// EXCEL出力時の設定初期化
			output_type: 'all'
		};

		// サーバーへリクエスト送信
		$scope.submit = function () {

			$scope.alerts = {};

			// ターゲット:falseの場合は画面上非表示となっているため全てのターゲットをfalseに設定
			if(!$scope.param.output_kind.target){
				$scope.param.output_kind.target_area = false;
				$scope.param.output_kind.target_schedule = false;
				$scope.param.output_kind.target_gender = false;
				$scope.param.output_kind.target_age = false;
				$scope.param.output_kind.target_userlist = false;
				$scope.param.output_kind.target_placement = false;
			}

			// アカウント選択チェック
			if($scope.clientCombobox.accounts.length == 0){
				$scope.alerts = [
					{ type: 'danger', msg: 'アカウントを選択してください' }
				];
			}

			// 出力項目選択チェック
			var selected_flg = false;
			for (var key in $scope.param.output_kind) {
				if(key == "target"){
					continue;
				}
				if($scope.param.output_kind[key]){
					selected_flg = true;
					break;
				}
			}
			if(!selected_flg){
				$scope.alerts = [
					{ type: 'danger', msg: '出力項目を1つ以上選択してください' }
				];
			}
			
			// アラートを画面に表示
			if($scope.alerts.length > 0){
				return false;
			}

			/**
			 * サーバ送信用にデータを変換する
			 */
			var form = $('#accountstructure_form');
			
			$scope.path = "/sem/new/accountstructure/export";
			$('#accountstructure_form')
				.attr('action', $scope.path)
				.submit();
		};

		// アラートのクローズ
		$scope.closeAlert = function(index) {
			$scope.alerts.splice(index, 1);
		};
	}])
	/**
	 * フィルタリング選択フォーム コントローラー
	 */
	.controller("FormFilterCtrl",
		[ "$scope", "FiltersService", function($scope, FiltersService) {
			$scope.addFilter = function() {
				FiltersService.addFilter($scope);
			};
			$scope.deleteFilter = function(i) {
				FiltersService.deleteFilter($scope, i);
			};
			$scope.clearFilter = function(i) {
				FiltersService.clearFilter($scope, i);
			};
			$scope.btnFilterGoogle = function() {
				return FiltersService.btnFilterGoogle($scope);
			};
			$scope.btnFilterYdn = function() {
				return FiltersService.btnFilterYdn($scope);
			};
		} ]);

