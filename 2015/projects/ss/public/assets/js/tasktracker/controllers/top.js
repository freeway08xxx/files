/* Controllers */
var controller = angular.module('tskt.controllers.top', []);

/* TsktTopCtrl as tsktTop */
controller.controller('TsktTopCtrl', ['$scope', 'tsktTopService', 'tsktModelProvider',
	function($scope, tsktTopService, tsktModelProvider) {
		var _this = this;
		_this.models 	= tsktModelProvider.top.forge();

		// $scopeを返す(broadcastなどで利用)
		_this.getScope = function() {
			return $scope;
		}
	}
]);

/* TsktTopTaskCtrl as tsktTopTask */
controller.controller('TsktTopTaskCtrl', ['$scope', 'tsktTopTasksService', 'tsktModelProvider',  
	function($scope, tsktTopTasksService, tsktModelProvider) {
		var _this = this;
		_this.models = tsktModelProvider.topTasks.forge();

		_this.services = {

			/**
			 * viewからのアクションに対応するサービス 
			 */
			bindAction: {
				// ステータス変更イベント
				changeStatus: function() {
					//詳細を消す
					$scope.tsktTop.getScope().$broadcast($scope.appConst.broadcast_names.taskDetailView,null);
					_this.services.refreshTableData();
				},

				// クライアント名入力イベント
				changeClient: function() {
					_this.taskTable.services.registFilter('client_name',_this.models.client_name);
					_this.taskTable.services.filter();
				},

				// 詳細表示
				clickTaskView: function(row) {
					//詳細表示
					_this.taskTable.api.selection.setActiveRow(row);
				//	$scope.tsktTop.models.selectTaskId = row.task_id;
					$scope.tsktTop.getScope().$broadcast($scope.appConst.broadcast_names.taskDetailView,row.task_id);

					// 詳細までスクロールする
					setTimeout(function() {
						$("body,html").animate({scrollTop: $('#tskt-taskDetail').offset().top}, "slow");
					}, 350);
				}
			},
			refreshTableData: function() {
				_this.taskTable.services.load(_this.models.status, _this.models.dateFrom.date, _this.models.dateTo.date);
			}
		}

		// datepicker設定
		_this.datepickerConfig = {
			isTimeHide: true,
			changeDate: function() {
				_this.services.refreshTableData();
			}
		}

		//テーブルオブジェクト取得
		_this.taskTable = tsktTopTasksService.table.getTable();
		//初回表示
		_this.services.refreshTableData();

		$scope.$on($scope.appConst.broadcast_names.taskRefresh, function(event, data) {
			_this.services.refreshTableData();
		});
	}
]);

/* TsktTopProcessCtrl as tsktTopProcess */
controller.controller('TsktTopProcessCtrl', ['$scope', 'tsktTopProcessService', 'tsktModelProvider', 'ssModal', 
	function($scope, tsktTopProcessService, tsktModelProvider, ssModal) {
		var _this = this;
		_this.getScope = function() {
			return $scope;
		}

		_this.models = tsktModelProvider.topProcess.forge();

		_this.services 	= {

			/**
			 * viewからのアクションに対応するサービス 
			 */
			bindAction: {
				// ステータス変更イベント
				changeStatus : function() {
					_this.services.refreshTableData();
					//詳細を消す
					$scope.tsktTop.getScope().$broadcast($scope.appConst.broadcast_names.taskDetailView,null);
				},

				// クライアント名入力イベント
				changeClient : function() {
					_this.processTable.service.registFilter('client_name',_this.models.client_name);
					_this.processTable.service.filter();
				},

				// 詳細表示
				clickTaskView: function(row) {
					_this.processTable.api.selection.setActiveRow(row);
					$scope.tsktTop.getScope().$broadcast($scope.appConst.broadcast_names.taskDetailView,row.task_id);

					// 詳細までスクロールする
					setTimeout(function() {
						$("body,html").animate({scrollTop: $('#tskt-taskDetail').offset().top}, "slow");
					}, 350);
				},

				changeStatusUpdate: function(row){
					var message = 'プロセスを完了にします。よろしいですか？';
					if(row.process_status_update == 1){
						message = 'プロセスを未完了に戻します。よろしいですか？';
					}

					ssModal.confirm(message).result.then(function(){
						tsktTopProcessService.table.saveStatus(row.process_id, row.process_status_update).then(function(res){
							$scope.tsktTop.getScope().$broadcast($scope.appConst.broadcast_names.taskRefresh);
							$scope.tsktTop.getScope().$broadcast($scope.appConst.broadcast_names.taskDetailView,row.task_id);
						});
					},function(){
						row.process_status_update = row.process_status;
					});
				},

				changeFile: function(row) {
					var message = 'ファイルをUPします。よろしいですか？(すでにある場合は上書きされます。)';
					ssModal.confirm(message).result.then(function(){
						tsktTopProcessService.table.saveFile(row.process_id, row.file).then(function(res){
							$scope.tsktTop.getScope().$broadcast($scope.appConst.broadcast_names.taskRefresh);
							$scope.tsktTop.getScope().$broadcast($scope.appConst.broadcast_names.taskDetailView,row.task_id);
						});
					});
				},
			},

			/**
			 * テーブル情報リフレッシュ 
			 */
			refreshTableData: function() {
				_this.processTable.service.load(_this.models.status, _this.models.dateFrom.date, _this.models.dateTo.date);
			}

		};

		// datepicker設定
		_this.datepickerConfig = {
			isTimeHide: true,
			changeDate: function() {
				_this.services.refreshTableData();
			}
		}

		//テーブルオブジェクト取得
		_this.processTable = tsktTopProcessService.table.getTable();
		//初回表示
		_this.services.refreshTableData();

		//リフレッシュ通知
		$scope.$on($scope.appConst.broadcast_names.taskRefresh, function(event, data) {
			_this.services.refreshTableData();
		});
	}
]);


/* TsktTopTaskDetailCtrl as tsktTopTaskDetail */
controller.controller('TsktTopTaskDetailCtrl', ['$scope', '$location', 'tsktTopTaskDetailService', 'tsktCommonService', 'tsktModelProvider', 'tsktUtil', 'ssModal', 
	function($scope, $location, tsktTopTaskDetailService, tsktCommonService, tsktModelProvider, tsktUtil, ssModal) {
		var _this = this;


		//初期Model情報
		_this.models = tsktModelProvider.topTaskDetail.forge();

		//表示テーブル
		_this.models.accountListTable 	= tsktTopTaskDetailService.getAccountListTable();
		_this.models.processListTable 	= tsktTopTaskDetailService.getProcessListTable();
		_this.models.fileListTable 		= tsktTopTaskDetailService.getFileListTable();
		_this.models.arujoListTable 	= tsktTopTaskDetailService.getArujoTable();
		_this.models.processEditableTable = tsktTopTaskDetailService.getProcessEditableTable();
		_this.models.arujoEditableTable = tsktTopTaskDetailService.getArujoEditableTable();


		_this.bindAction = {
			/**
			 * 編集ボタン 
			 */
			clickEditButton : function() {
				//編集要にmodel書き換え
				tsktTopTaskDetailService.edit.convertModel(_this.models);
				_this.models.isEditable = true;
			},

			/**
			 * 編集後登録ボタン 
			 */
			clickSaveButton : function() {
				ssModal.confirm('更新してもよろしいですか？').result.then(function(){
					tsktTopTaskDetailService.edit.updateTask(_this.models.task.id,_this.models).then(function(taskDetail){
						_this.allRefreshData();
						_this.showView(_this.models.task.id);
					});
				});
			},

			/**
			 * 編集後キャンセルボタン 
			 */
			clickCancelButton: function() {
				//編集モード終了
				_this.models.isEditable = false;
			},

			/**
			 * 編集後追加プロセスボタン 
			 */
			clickAddProcess: function() {
				//プロセスの追加
				//_this.allRefreshData();
				tsktTopTaskDetailService.edit.addProcess(_this.models,$scope.user.id);
			},
			/**
			 * 編集後のプロセスの削除 
			 */
			clickRemoveAddProcess: function(row) {
				tsktTopTaskDetailService.edit.removeProcess(_this.models, row);
			},

			/**
			 * タスク削除ボタン 
			 */
			clickRemoveTask: function() {
				ssModal.confirm('削除してもよろしいですか？').result.then(function(){
					tsktTopTaskDetailService.removeTask(_this.models.task.id).then(function(){
						_this.allRefreshData();
						_this.hideView();
					});
				});
			},

			/**
			 * プロセス削除ボタン 
			 */
			clickRemoveProcess: function(row) {
				ssModal.confirm('削除してもよろしいですか？').result.then(function(){
					tsktTopTaskDetailService.removeProcess(row.id).then(function(){
						_this.allRefreshData();
						_this.showView(_this.models.task.id);
					});
				});
			},

			/**
			 * Bomb登録ボタン 
			 */
			clickBombSave: function(row) {
				tsktTopTaskDetailService.openRegistBomb(row.id).then(function(){
					_this.showView(_this.models.task.id);
				});
			},

			/**
			 * ユーザーリソースを開く 
			 */
			openRoutineSetting: function() {
				ssModal.custom({
					//size:'lg',
					//backdrop:false,
					templateUrl: '/sem/new/tasktracker/common/routine_setting.html',
					controller:'TsktTopTaskDetailRoutineModalCtrl',
					resolve: {
						task: function () {
							return _this.models.task;
						}
					}
				})
				.result.then(function(){
					_this.allRefreshData();
				});
			},

			/**
			 * ユーザーリソースを開く 
			 */
			openUserTask: function(userId) {
				tsktCommonService.openUserResource(userId);

			}
		}

		_this.allRefreshData = function() {
			$scope.tsktTop.getScope().$broadcast($scope.appConst.broadcast_names.taskRefresh);
		}

		_this.hideView = function() {
			_this.models.isView = false;
		}
		_this.showView = function(taskId) {
			//_this.models.isView = false;
					
			tsktTopTaskDetailService.getTaskDetail(taskId).then(function(taskDetail){
				if(taskDetail){
					_this.models.task = taskDetail.task;
					_this.models.accountListTable.data 	= taskDetail.accountList;
					_this.models.fileListTable.data 	= taskDetail.fileList;
					for(k in taskDetail.arujoList){
						taskDetail.arujoList[k] = tsktUtil.Factory.createArujoObject(taskDetail.arujoList[k]);
					}
					_this.models.arujoListTable.data 	= taskDetail.arujoList;

					for(k in taskDetail.processList){
						//メインプロセス
						if(taskDetail.processList[k].main_process == "1"){
							_this.models.mainProcess = taskDetail.processList[k];
						}
					}
					_this.models.processListTable.data = taskDetail.processList;
					_this.models.isView = true;
				}
			});

			_this.models.isEditable = false;
			_this.models.editModel = {};
		}


		// rowクリックイベント
		$scope.$on($scope.appConst.broadcast_names.taskDetailView, function(event, task) {
			if(!task){
				_this.hideView();
			}else{
				//var taskId = task.task_id;
				var taskId = task; 
				_this.showView(taskId);
			}
		});
	}
]);



/* BOMB */
app.controller('TsktTopTaskDetailBombModalCtrl', ['processId' ,'$scope', '$location','$modalInstance', 'TsktTopTaskDetailBombModalService', 'ssModal', function(
	processId, $scope, $location, $modalInstance, TsktTopTaskDetailBombModalService, ssModal) {

		TsktTopTaskDetailBombModalService.get(processId).then(function(bombs){
			$scope.bombs = bombs;
		});
		
		$scope.close = function () {
			$modalInstance.dismiss();
		};

		$scope.save = function() {
			ssModal.confirm('登録してもよろしいですか？').result.then(function(){
				TsktTopTaskDetailBombModalService.save(processId,$scope.bomb_description).then(function(){
					$modalInstance.close();
				});
			});
		}
	}
]);

/* 定例設定 */
app.controller('TsktTopTaskDetailRoutineModalCtrl', ['task' ,'$scope', '$location','$modalInstance', 'TsktTopTaskDetailRoutineModalService', 'ssModal', function(
	task, $scope, $location, $modalInstance, TsktTopTaskDetailRoutineModalService, ssModal) {
		var endDate = new Date();
		endDate.setHours(0);
		endDate.setMinutes(0);
		endDate.setSeconds(0);
		endDate.setDate(endDate.getDate() + 7 );

		$scope.models = {
			datepicker: {
				date: endDate
			},
			datepickerConfig:  {
				isTimeHide: true,
			}
		};

		$scope.close = function () {
			$modalInstance.dismiss();
		};

		$scope.clickSubmit = function () {
			ssModal.confirm('登録してもよろしいですか？').result.then(function(){
				TsktTopTaskDetailRoutineModalService.save(task.id, $scope.models.datepicker.date).then(function(){
					$modalInstance.close();
				});
			});
		}
	}
]);
