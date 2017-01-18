/* Controllers */
var app = angular.module('Reacquire.controllers', []);

/* Main */
app.controller('ReacquireMainCtrl', ['$scope', 'AjaxService', function($scope, AjaxService) {
	// クライアント
	$scope.clientComboboxConfig = {
		registerApi : function(api){
			$scope.comboboxApi = api;
		}
	}
	$scope.clientCombobox = {};

	// 種別
	$scope.reportTypeElements = [
		{label: 'アカウント', type: 'ACCOUNT'},
		{label: 'キーワード', type: 'KEYWORD'},
		{label: '広告', type: 'AD'	}
	];
	$scope.reportType = $scope.reportTypeElements[0];

	// 期間
	$scope.termdateConfig = {
		title: '期間',
		datepicker: {
			format: 'yyyy/MM/dd'
		}
	};
	$scope.termdate = {
		getReportType: function () {
			return null;
		}
	};

	// 差分フラグ
	$scope.differenceExists = true;

	// 差分データ
	$scope.differenceData = {};

	// 全選択フラグ
	$scope.isChecked = true;

	// 全選択/全解除
	$scope.checkToggleAll = function () {
		angular.forEach($scope.differenceData, function (differenceDatum) {
			differenceDatum.is_checked = !$scope.isChecked;
		});
	}

	// エラーメッセージ
	$scope.errorMessage = null;

	// 差分を表示
	$scope.getDifferenceData = function () {
		$scope.errorMessage = null;

		if ($scope.clientCombobox.accounts.length === 0) {
			return $scope.errorMessage = 'アカウントを選択してください';
		}

		var accountIdList = '';

		angular.forEach($scope.clientCombobox.accounts, function (account) {
			accountIdList = accountIdList + ':' + account.account_id;
		});

		return AjaxService.getDifferenceData(
			accountIdList,
			$scope.reportType.type,
			$scope.termdate.term_arr[0].from.toDateString(),
			$scope.termdate.term_arr[0].to.toDateString(),
			Number($scope.differenceExists)
		).then(function (response) {
			if (response.status === 'success') {
				$scope.differenceData = response.data;
			} else {
				$scope.errorMessage = response.message;
			}
		}, function (response) {
			$scope.errorMessage = 'サーバーとの通信に失敗しました。';
		});
	};

	// ページング処置
	$scope.currentPage = 0;
	$scope.pageSize    = 20;

	$scope.numberOfPages = function () {
		return Math.ceil($scope.differenceData.length / $scope.pageSize);
	}

	// 再取得実行
	$scope.submit = function () {
		$scope.errorMessage = null;

		var accountList = [];

		angular.forEach($scope.differenceData, function (differenceDatum) {
			if (differenceDatum.is_checked) {
				accountList.push(differenceDatum.account_id);
			}
		});

		if (accountList.length) {
			if (confirm('再取得を開始してもよろしいですか?')) {
				var accountIdList = '';

				angular.forEach($scope.differenceData, function (differenceDatum) {
					if (differenceDatum.is_checked) {
						accountIdList = accountIdList + ':' + differenceDatum.account_id;
					}
				});

				AjaxService.postSubmit(
					accountIdList,
					$scope.reportType.type,
					$scope.clientCombobox.client.id,
					$scope.termdate.term_arr[0].from.toDateString(),
					$scope.termdate.term_arr[0].to.toDateString()
				).then(function (response) {
					if (response.status === 'success') {
						var alert_message = '以下の再取得を開始しました。' + "\n";

						angular.forEach($scope.differenceData, function (differenceDatum) {
							if (differenceDatum.is_checked) {
								alert_message = alert_message + '　・' + differenceDatum.account_name + "\n";
							}
						});

						alert(alert_message);
					} else {
						$scope.errorMessage = response.message;
					}
				}, function () {
					$scope.errorMessage = 'サーバーとの通信に失敗しました。';
				});
			}
		} else {
			$scope.errorMessage = 'アカウントを選択してください。';
		}
	}
}]);