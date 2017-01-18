/* Controllers */
var app = angular.module('eagle.controllers', []);

/* ステータス変更 */
app.controller('EagleReserveCtrl', ['$scope', '$location', '$routeParams', 'ssModal', 'eagleReserveService',
	function($scope, $location, $routeParams, ssModal, eagleReserveService) {

	$scope.isSubmit = false;
	$scope.isSyncSkip = false;
	$scope.clientCombobox = {}; 

	$scope.openModal = {
		loading : function() {
			return ssModal.loading('通信中です...');
		},
		apiSave : function() {
			return ssModal.confirm('登録してもよろしいですか？');
		},
		apiError : function(){
			return ssModal.error('通信エラーが発生しました');
		},
		custom : function(){
			return ssModal.custom({
				templateUrl: '/sem/new/assets/template/modal/custom1.html',
				controller:['$scope', '$modalInstance', function($scope, $modalInstance) {
					$scope.message = '同期予約が完了致しました。<br/>同期が完了致しましたらメールにてお知らせ致します。';
					$scope.title = '同期予約完了';
					$scope.close = function () {
						$modalInstance.dismiss();
					};
				}],
			}).result.then(null,function(){
				$location.url('/history');
			});
		}
	};

	//更新
	$scope.submit = function() {
		if(!$scope.clientCombobox.accounts || $scope.clientCombobox.accounts.length <= 0){
			//TODO 誰も選んでないよエラー
			return false;
		}
		
		var clientId = $scope.clientCombobox.client.id;
		//送信情報作成
		var postAccountData = [];
		for(var k in $scope.clientCombobox.accounts){
			var account = $scope.clientCombobox.accounts[k];
			var postAccount = {
				account_id: account.account_id,	
				media_id : account.media_id
			};
			postAccountData.push(postAccount);
		}

		//確認POPUP
		$scope.openModal.apiSave().result.then(
			function(){
				//登録処理
				$scope.isSubmit = true;
				eagleReserveService.save(clientId, postAccountData, $scope.isSyncSkip).then(function(res){
					//更新が完了した場合は、テーブル一覧を表示
					$scope.openModal.custom();
					$scope.isSubmit = false;
				},function(error){
					ssModal.error(error.message);
					$scope.isSubmit = false;
				});
			},
			function(){}
		);
	}
}]);

/* 更新 */
app.controller('EagleUpdateCtrl', ['$scope', '$location', '$routeParams', 'ssModal', 'eagleUpdateService',
	function($scope, $location, $routeParams, ssModal, eagleUpdateService) {

	$scope.isSubmit = false;
	$scope.settings = { 
		tab : 'status'
	}

	$scope.eagleId = $routeParams.eagle_id;

	eagleUpdateService.getContent($scope.eagleId).then(function(res){

		// エラーがある場合は履歴に飛ばす
		if(res.error){
			var error = ssModal.error(res.error.message);
			error.result.then(null,function(){
				$location.url('/history');
			});
		}

		// Eagle情報追加
		$scope.eagle = res.eagle;
		$scope.eagle_accounts = res.eagle_accounts;
		for(var k in $scope.eagle_accounts){
			var value = $scope.eagle_accounts[k];
			if(value.media_id == 1){
				value.icon = '/sem/new/assets/img/common/media_icon_mini_yahoo.png' ;
			}else if(value.media_id == 2){
				value.icon =  '/sem/new/assets/img/common/media_icon_mini_google.png' ;
			}
		}
		if(res.is_sync_skip){
			$scope.alerts = [
				{ type: 'warning', msg: '掲載内容の同期を行っていません。実際の掲載内容と違う可能性があります。' },
			];
		}
	},function(error){
		ssModal.error(error.message);
	});
}]);

app.controller('EagleUpdateStatusCtrl', ['$scope', '$location', '$routeParams', 'ssModal', 'eagleUpdateService',
	function($scope, $location, $routeParams, ssModal, eagleUpdateService) {

	/**
	 * 初期化 
	 */
	var init = function() {
		$scope.components = [
			{id:'campaign',name:'キャンペーン'},
			{id:'adgroup',name:'広告グループ'},
			{id:'keyword',name:'キーワード'},
			{id:'ad',name:'広告'},
		];
	
		$scope.settings.status = {
			component : $scope.components[0].id,
			updateActive : true		
		};
		
		$scope.filter = {};
		for(var k in $scope.components){
			component = $scope.components[k];
			$scope.filter[component.id] = {
				search : {
					list : null,
					type : '2',
				},
				except : {
					list : null,
					type : '2',
				}
			};
			if(component.id == 'ad'){
				$scope.filter[component.id].pattern = {1:true,2:true,3:true,4:true}
			}else{
				$scope.filter[component.id].isIdOnly = false;
			}
		}

	}

	/**
	 * 変更対象がActiveか判定 
	 */
	$scope.isFilterTypeActive = function(component){
		return {
			'btn-info':$scope.settings.status.component == component,
			'btn-default':$scope.settings.status.component != component,
		}
	}

	/**
	 * 変更対象の絞り込みが開くか判定 
	 */
	$scope.isComponentOpen = function(componentId){
		switch(componentId){
			case 'campaign': 
				if($scope.settings.status.component == 'campaign'){
					return true;	
				}
			case 'adgroup': 
				if($scope.settings.status.component == 'adgroup'){
					return true;	
				}
			case 'keyword': 
				if($scope.settings.status.component == 'keyword'){
					if(componentId != 'ad'){
						return true;
					}
				}
			case 'ad': 
				if($scope.settings.status.component == 'ad'){
					if(componentId != 'keyword'){
						return true;
					}
				}
			default:
				return false;
		}
	}

	/**
	 * Filterファイルのダウンロードを行う 
	 */
	$scope.dlFilterFile = function() {
		var data = {
			eagleId : $scope.eagleId,
			component : $scope.settings.status.component ,
			filter : $scope.filter
		}

		//form作成
		var form = $("<form/>", { 
					action:'/sem/new/eagle/update/status/download_filter_file',
					method:'POST'
				});

		for (var paramName in data) {
			var input = $("<input/>", { 
						type:'hidden',
						name:paramName,
						value:JSON.stringify(data[paramName])
					});
			form.append(input);
		}
		$('body').append(form);
		//送信
		form.submit();
	}

	/**
	 * Filterファイルのダウンロードを行う 
	 */
	$scope.dlConfirmFile = function() {

		var data = {
			eagleId: $scope.eagleId,
			component: $scope.settings.status.component,
			update_data :$scope.updateData,
			activeFlg: $scope.settings.status.updateActive,	
		};

		//form作成
		var form = $("<form/>", { 
					action:'/sem/new/eagle/update/status/download_confirm_save_file',
					method:'POST'
				});

		for (var paramName in data) {
			var input = $("<input/>", { 
						type:'hidden',
						name:paramName,
						value:JSON.stringify(data[paramName])
					});
			form.append(input);
		}
		$('body').append(form);
		//送信
		form.submit();
	}

	/**
	 * 変更内容の更新を行う 
	 */
	$scope.updateStatus = function() {
		var eagleId = $scope.eagleId;
		var component = $scope.settings.status.component;
		var updateData = $scope.updateData;
		var activeFlg = $scope.settings.status.updateActive;

		ssModal.confirm('更新してもよろしいですか？').result.then(
			function(){
				$scope.isSubmit = true;
				eagleUpdateService.updateStatus(eagleId,component,updateData,activeFlg).then(function(res){
					ssModal.custom({
						templateUrl: '/sem/new/assets/template/modal/custom1.html',
						controller:['$scope', '$modalInstance', function($scope, $modalInstance) {
							$scope.message = '更新が反映させるまでしばらくお待ちください';
							$scope.title = '更新完了';
							$scope.close = function () {
								$modalInstance.dismiss();
							};
						}],
					}).result.then(null,function(){
						$location.url('/history');
					});
				},function(error){
					$scope.isSubmit = false;
					ssModal.error(error.message);
				});
			},function(error){
				ssModal.error(error.message);
			});
	}

	//初期化実行
	init();

}]);

app.controller('EagleUpdateCpcCtrl', ['$scope', '$location', '$routeParams', 'ssModal', 'eagleUpdateService',
	function($scope, $location, $routeParams, ssModal, eagleUpdateService) {

	var init = function(){
		$scope.components = [
			{id:'campaign',name:'キャンペーン'},
			{id:'adgroup',name:'広告グループ'},
		];

		$scope.settings.cpc = {
			component : $scope.components[1].id,
			saveType : 1,
			device : {pc:true,sp:true},
			cpcMba : 'CPC'
		}
	
		$scope.filter = {};
		for(k in $scope.components){
			component = $scope.components[k];
			$scope.filter[component.id] = {
				search : {
					list : null,
					type : '2',
					isIdOnly: false,
				},
				except : {
					list : null,
					type : '2',
					isIdOnly: false,
				}
			}
		}
		$scope.cpc = {
			updateData : '',
			bulkValue : '',
		}
	}

	$scope.activeButton = function(active){
		if(active){
			return {
				'btn-info':true,
				'btn-default':false,
			}
		}else{
			return {
				'btn-info':false,
				'btn-default':true,
			}
		}
	}
	$scope.isFilterTypeActive = function(component){
		return $scope.activeButton($scope.settings.cpc.component == component);
	}

	/**
	 * 変更対象の絞り込みが開くか判定 
	 */
	$scope.isComponentOpen = function(componentId){
		switch(componentId){
			case 'campaign': 
				if($scope.settings.cpc.component == 'campaign'){
					return true;	
				}
			case 'adgroup': 
				if($scope.settings.cpc.component == 'adgroup'){
					return true;	
				}
			default:
				return false;
		}
	}

	/**
	 * Filterファイルのダウンロードを行う 
	 */
	$scope.dlFilterFile = function() {

		var data = {
			eagleId : $scope.eagleId,
			component : $scope.settings.cpc.component ,
			filter : $scope.filter
		}

		//form作成
		var form = $("<form/>", { 
					action:'/sem/new/eagle/update/cpc/download_filter_file',
					method:'POST'
				});

		for (var paramName in data) {
			var input = $("<input/>", { 
						type:'hidden',
						name:paramName,
						value:JSON.stringify(data[paramName])
					});
			form.append(input);
		}
		$('body').append(form);
		//送信
		form.submit();
	}


	/**
	 * Filterファイルのダウンロードを行う 
	 */
	$scope.dlConfirmFile = function() {

		var data = {
			eagleId : $scope.eagleId,
			component : $scope.settings.cpc.component,
			update_data : $scope.cpc.updateData,
			bulk_value : $scope.cpc.bulkValue,
			settings : $scope.settings.cpc
		};

		//form作成
		var form = $("<form/>", { 
					action:'/sem/new/eagle/update/cpc/download_confirm_save_file',
					method:'POST'
				});

		for (var paramName in data) {
			var input = $("<input/>", { 
						type:'hidden',
						name:paramName,
						value:JSON.stringify(data[paramName])
					});
			form.append(input);
		}
		$('body').append(form);
		//送信
		form.submit();
	}


	/**
	 * 更新用テキスト内 
	 */
	$scope.getUpdatePlaceholder = function() {
		var placeholder = '更新コード';
		if($scope.settings.cpc.cpcMba == 'CPC'){
			if($scope.settings.cpc.device.pc){
				placeholder += ' CPC(PC)';
			}
			if($scope.settings.cpc.device.sp){
				placeholder += ' CPC(SP)';
			}

		}else{
			placeholder += ' ' + $scope.settings.cpc.cpcMba;
		}

		return placeholder;
	}



	$scope.updateCpc = function() {
		console.log($scope.cpc.updateData);
		var data = {
			eagleId : $scope.eagleId,
			component : $scope.settings.cpc.component,
			update_data : $scope.cpc.updateData,
			bulk_value : $scope.cpc.bulkValue,
			settings : $scope.settings.cpc
		};

		ssModal.confirm('更新してもよろしいですか？').result.then(
			function(){
				$scope.isSubmit = true;
				eagleUpdateService.updateCpc(data).then(function(res){
					ssModal.custom({
						templateUrl: '/sem/new/assets/template/modal/custom1.html',
						controller:['$scope', '$modalInstance', function($scope, $modalInstance) {
							$scope.message = '更新が反映させるまでしばらくお待ちください';
							$scope.title = '更新完了';
							$scope.close = function () {
								$modalInstance.dismiss();
							};
						}],
					}).result.then(null,function(){
						$location.url('/history');
					});
				},function(error){
					$scope.isSubmit = false;
					ssModal.error(error.message);
				});
			},
			function(){
			// モーダルキャンセル
			}
		);
	}

	//初期化実行
	init();

}]);

/* 更新 */
app.controller('EagleHistoryCtrl', ['$scope', '$location', '$routeParams', 'ssModal','eagleHistoryService', 'eagleReserveService', '$compile',
	function($scope, $location, $routeParams, ssModal, eagleHistoryService, eagleReserveService, $compile) {

	$scope.settings = { 
		tab : 'history'
	}

	$scope.$scope = $scope;
	$scope.eagleHistory = {
		minRowsToShow:"10",
		loading : true,
		//rowsPerPage:30,
		rowHeight: 65,
		columnDefs: [
			  {displayName:"ID", 		field: "id",	width:'5%', enableColumnMenu:false},
			  {displayName:"状況",		field: "status" , cellTemplate:'<div compile="row.entity[col.field]"></div>', width:'5%', enableColumnMenu:false},
			  {displayName:"クライアント名",	field: "client_name",	cellTemplate:'<div compile="row.entity[col.field]"></div>', width:'30%', enableColumnMenu:false},
			  {displayName:"更新対象",	field: "exec_name",	width:'10%', enableColumnMenu:false},
			  {displayName:"補足",		field: "detail",	cellTemplate:'<div compile="row.entity[col.field]"></div>', 	width:'15%', enableColumnMenu:false},
			  {displayName:"作成者",	field: "created_user_name",		width:'5%', enableColumnMenu:false},
			  {displayName:"登録日時",  field: "created_at",	width:'10%', enableColumnMenu:false},
			  {displayName:"更新日時",  field: "updated_at", width:'10%', enableColumnMenu:false},
			],
		onRegisterApi : function (gridApi) {
			$scope.gridApi = gridApi;
		},
	};

	/*
	$scope.$watch('eagleHistory.page', function(page, prePage) {
		$scope.gridApi.pagination.seek(page);
	});
	*/


	//既存Eagleのコピーを行う
	$scope.eagleCopy = function(eagleId) {
		eagleReserveService.copy(eagleId).then(function(res){
			//更新が完了した場合は、テーブル一覧を表示
			var new_eagleId = res.data.eagle_id;
			$location.url('/update?eagle_id='+ new_eagleId);
		},function(error){

		});
	}

	eagleHistoryService.getList().then(function(res){
		if(!res || !res.eagleAll){
			return false
		}

		for(k in res.eagleAll){
			//補足情報チェック
			var value = res.eagleAll[k];

			value.client_name = '<a ng-click="getExternalScopes().showDetail(' + value.id + ')">' + value.client_name + '</a>';
			if(value.status){
				switch (value.status){
					case '1':
						value.status = '<span class="blinking label label-info">同期実行中</span>';
						break;
					case '2':
						value.status = '<span class="label label-primary">更新準備完了</span>';
						break;
					case '3':
						value.status = '<span class="blinking label label-warning">更新実行中</span>';
						break;
					case '4':
						value.status = '<span class="label label-success">更新完了</span>';
						break;
					case '97':
						value.status = '<span class="label label-default">時間経過</span>';
						break;
					case '98':
						value.status = '<span class="label label-danger">更新失敗</span>';
						break;
					case '99':
						value.status = '<span class="label label-danger">同期失敗</span>';
						break;
				}
			}

			var detail = '';
			for(k in value.detail){
				if(k === 'component_name'){
					value.exec_name += '(' + value.detail[k] + ')';
				}else{
					dValue = value.detail[k];
					detail += dValue + '<br/>';
				}
			}
			value.detail = detail;

		}
		$scope.eagleHistory.data = res.eagleAll;
		$scope.eagleHistory.loading = false;
	},function(error){
		ssModal.error(error.message);
	});

	$scope.showDetail = function(eagleId){
		eagleHistoryService.getDetail(eagleId).then(function(res){
			if(!res){
				return false
			}

			ssModal.custom({
				size:'lg',
				resolve:{
					eagleDetail : function(){
						return res;
					}
				},
				templateUrl: '/sem/new/assets/template/modal/eagle/detail.html',
				controller:'EagleHistoryDetailCtrl'
			}).result.then(null,function(){});
		});
	}



	
}]);




/* 更新 */
app.controller('EagleHistoryDetailCtrl', ['eagleDetail', '$scope', '$location', 'eagleHistoryService', 'eagleReserveService', '$modalInstance', function(
	eagleDetail, $scope, $location, eagleHistoryService, eagleReserveService, $modalInstance) {
		console.log(eagleDetail);

		$scope.close = function () {
			$modalInstance.dismiss();
		};

		$scope.eagle = eagleDetail.eagle;
		$scope.eagle_accounts = eagleDetail.eagle_accounts;
		for(k in $scope.eagle_accounts){
			var value = $scope.eagle_accounts[k];
			if(value.media_id == 1){
				value.icon = '/sem/new/assets/img/common/media_icon_mini_yahoo.png' ;
			}else if(value.media_id == 2){
				value.icon =  '/sem/new/assets/img/common/media_icon_mini_google.png' ;
			}
		}

		$scope.getStatus = function() {
			statusObj = {
				name : '',
				class : ''
			}
			if($scope.eagle.status){
				switch ($scope.eagle.status){
					case '1':
						statusObj.name = '同期実行中';
						statusObj.class = 'label-info';
						break;
					case '2':
						statusObj.name = '更新準備完了';
						statusObj.class = 'label-primary';
						break;
					case '3':
						statusObj.name = '更新実行中';
						statusObj.class = 'label-warning';
						break;
					case '4':
						statusObj.name = '更新完了';
						statusObj.class = 'label-success';
						break;
					case '97':
						statusObj.name = '時間経過';
						statusObj.class = 'label-default';
						break;
					case '98':
						statusObj.name = '更新失敗';
						statusObj.class = 'label-danger';
						break;
					case '99':
						statusObj.name = '同期失敗';
						statusObj.class = 'label-danger';
						break;
				}
			}

			return statusObj;
		}


		$scope.actionUpdate = function() {
			$modalInstance.dismiss();
			$location.url('update?eagle_id=' + $scope.eagle.id);
		}

		$scope.actionReUpdate = function() {
			$modalInstance.dismiss();
			eagleReserveService.copy($scope.eagle.id).then(function(res){
				//更新が完了した場合は、テーブル一覧を表示
				var new_eagleId = res.data.eagle_id;
				$location.url('/update?eagle_id='+ new_eagleId);
			},function(error){

			});
		}

	}
]);
