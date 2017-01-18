/* Controllers */
var controller = angular.module('tskt.controllers.setting', []);

/* TsktSettingCtrl as tsktSetting  */
controller.controller('TsktSettingCtrl', ['$scope', '$rootScope', 'tsktModelProvider', 'tsktCommonService', '$state', '$stateParams', 'tsktSettingService',
	function($scope, $rootScope, tsktModelProvider, tsktCommonService, $state, $stateParams, tsktSettingService) {
		console.log('TsktSettingCtrl');

		var _this = this;
		_this.models = tsktModelProvider.setting.forge();
		_this.models.master.category = $scope.appConst.category;
		_this.models.current.category = _this.models.master.category[1];

		_this.services = {
			
			/**
			 * タスク一覧を表示  
			 */
			viewTask: function() {
				if(_this.models.master.category && $state.is('setting')){
					$state.go('setting.task',{categoryId:_this.models.current.category.id});
				}
			},

			/**
			 * クライアント選択を開く 
			 */
			openClientSelect: function() {
				_this.models.isOpenClientSelect = !_this.models.isOpenClientSelect;
			},

			/**
			 * クライアント選択を解除する 
			 */
			releaseClientSelect: function() {
				_this.models.clientComboboxApi.clearModelsAll();
			}
		}

		$rootScope.$on('$stateChangeSuccess', function (event, current, previous) {
			if($state.is('setting')){
				_this.models.current.category = _this.models.master.category[1];
				_this.services.viewTask();
			}else if($state.is('setting.task')) {
				_this.models.current.task = null;
			}
		});

		//アカウント選択後に飛んでくるメッセージ
		$scope.$on('ss_combobox_client_change', function(event, data) {
			_this.models.isOpenClientSelect = false;
		});

		/**
		 * 選択したカテゴリの取得。通常の遷移とリロードされたことを考慮してwatchする 
		 */
		$scope.$watch(
			function(){
				return $stateParams.categoryId;
			},function(){
				if(_this.models.master.category || $stateParams.categoryId){
					//_this.models.params.categoryId = $stateParams.categoryId;
					for(var k in _this.models.master.category){
						var value = _this.models.master.category[k];
						if(value.id == $stateParams.categoryId){
							_this.models.current.category = value;
						}
					}
				}
		},true);

		_this.services.viewTask();
	}
]);

/* TsktSettingTaskCtrl as tsktSettingTask  */
controller.controller('TsktSettingTaskCtrl', ['$scope', '$stateParams', 'ssModal', 'tsktModelProvider', 'tsktCommonService', 'tsktSettingService', 'tsktSettingApiService',
	function($scope, $stateParams, ssModal, tsktModelProvider, tsktCommonService, tsktSettingService, tsktSettingApiService) {
		console.log('TsktSettingTaskCtrl');
		
		var _this = this;
		_this.models = tsktModelProvider.settingTask.forge();

		_this.taskMasterTable = tsktSettingService.tables.task;
		_this.taskMasterTable.data = null; 

		_this.services = {
			init: function() {
				tsktCommonService.getTaskMaster($stateParams.categoryId).then(function(taskMaster){
					_this.models.master.task = taskMaster;
					_this.taskMasterTable.data = _this.models.master.task;
				});
			},
			addTask: function() {
				ssModal.custom({
					//size:'lg',
					//backdrop:false,
					templateUrl: '/sem/new/tasktracker/setting/add_task.html',
					controller:'TsktSettingAddTaskModalCtrl',
					resolve: {
						categoryId: function () {
							return $stateParams.categoryId;
						},
					}
				})
				.result.then(function(){
					_this.services.init();
				});

			},

			removeTask: function(row) {
				ssModal.confirm('削除してもよろしいですか？').result.then(function(){
					tsktSettingApiService.remove_task(row.id).then(function(){
						_this.services.init();
					});
				});
			}
		}

		_this.services.init();
	}
]);


/* TsktSettingTaskDetailCtrl as tsktSettingTaskDetail  */
controller.controller('TsktSettingTaskDetailCtrl', ['$scope', '$stateParams', 'ssModal', 'tsktModelProvider', 'tsktCommonService', 'tsktSettingService', 'tsktSettingTaskDetailService', 'tsktSettingApiService', 
	function($scope, $stateParams, ssModal, tsktModelProvider, tsktCommonService, tsktSettingService, tsktSettingTaskDetailService, tsktSettingApiService) {
		console.log('TsktSettingTaskDetailCtrl');
		var _this = this;
		_this.models = tsktModelProvider.settingTaskDetail.forge();
		_this.table = {
			process: tsktSettingService.tables.process,
			arujo: tsktSettingService.tables.arujo,
		}
		_this.table.process.data = null;
		_this.table.arujo.data = null;

		_this.services = {
			openAddProcess: function(row) {
				ssModal.custom({
					//size:'lg',
					//backdrop:false,
					templateUrl: '/sem/new/tasktracker/setting/add_process.html',
					controller:'TsktSettingAddProcessModalCtrl',
					resolve: {
						taskMasterId: function () {
							return $stateParams.taskMasterId;
						},
						process: function() {
							if(row) {
								return row;
							}
						}
					}
				})
				.result.then(function(){
					api.getProcessMaster();
				});
			},

			selectMainProcess: function(row) {
				tsktSettingApiService.select_main_process(row.id).then(function(){
					api.getProcessMaster();
				});
			},

			removeProcess: function(row) {
				if(!row) return false;
				/**
				 * ARUJOを削除して元に戻る 
				 */
				ssModal.confirm('削除してもよろしいですか？').result.then(function(){
					tsktSettingApiService.removeProcess(row.id).then(function(){
						api.getProcessMaster();
					});
				});

			},

			openAddArujo: function(row) {
				ssModal.custom({
					//size:'lg',
					//backdrop:false,
					templateUrl: '/sem/new/tasktracker/setting/add_arujo.html',
					controller:'TsktSettingAddArujoModalCtrl',
					resolve: {
						taskMasterId: function () {
							return $stateParams.taskMasterId;
						},
						client: function() {
							if($scope.tsktSetting.models.clientCombobox.client){
								return $scope.tsktSetting.models.clientCombobox.client
							}
						},
						arujo: function() {
							if(row){
								return row;
							}
						},
					}
				})
				.result.then(function(){
					api.getArujoMaster();
				});
			},
			removeArujo: function(row) {
				if(!row) return false;
				/**
				 * ARUJOを削除して元に戻る 
				 */
				ssModal.confirm('削除してもよろしいですか？').result.then(function(){
					tsktSettingTaskDetailService.removeArujo(row).then(function(){
						api.getArujoMaster();
					});
				});
			},
		}

		var api = {
			getArujoMaster : function() {
				var clientId = null;
				if($scope.tsktSetting.models.clientCombobox.client){
					clientId = $scope.tsktSetting.models.clientCombobox.client.id
				}
				tsktCommonService.getArujoMaster($stateParams.taskMasterId, clientId).then(function(arujo){
					_this.models.master.arujo = arujo;
					_this.table.arujo.data = _this.models.master.arujo;
				});
			},
			getProcessMaster : function() {
				tsktCommonService.getProcessMaster($stateParams.taskMasterId).then(function(process){
					_this.models.master.process = process;
					_this.table.process.data = _this.models.master.process;
				});
			},
		}

		/**
		 * 選択したタスクの取得。通常の遷移とリロードされたことを考慮してwatchする 
		 */
		$scope.$watch(function(){
			return [$scope.tsktSettingTask.models.master.task, $stateParams.taskMasterId]
		},function(){
			if($scope.tsktSettingTask.models.master.task && $stateParams.taskMasterId){
				for(k in $scope.tsktSettingTask.models.master.task){
					var value = $scope.tsktSettingTask.models.master.task[k];
					if(value.id == $stateParams.taskMasterId){
						_this.models.task = value;
						$scope.tsktSetting.models.current.task = value;
					}
				}
			}
		},true);

		//アカウント選択後に飛んでくるメッセージ
		$scope.$on('ss_combobox_client_change', function(event, data) {
			api.getArujoMaster();
		});

		$scope.$on('ss_combobox_clear', function(event, data) {
			api.getArujoMaster();
		});

		api.getArujoMaster();
		api.getProcessMaster();
	}
]);

/* TsktSettingArujoCtrl as settingArujo  */
controller.controller('TsktSettingAddTaskModalCtrl', ['categoryId', '$scope', '$modalInstance', 'ssModal', 'tsktSettingApiService', 
	function(categoryId, $scope, $modalInstance, ssModal, tsktSettingApiService) {
		$scope.models = {}
		$scope.close = function () {
			$modalInstance.dismiss();
		};

		$scope.submit = function () {
			ssModal.confirm('保存してもよろしいですか？').result.then(function(){
				tsktSettingApiService.add_task(categoryId, $scope.models.name).then(function(){
					$modalInstance.close();
				});
			});
		}
	}
]);

/* TsktSettingArujoCtrl as settingArujo  */
controller.controller('TsktSettingAddProcessModalCtrl', ['taskMasterId', 'process', '$scope', '$modalInstance', 'ssModal', 'tsktSettingApiService', 
	function(taskMasterId, process, $scope, $modalInstance, ssModal, tsktSettingApiService) {
		$scope.models = {}
		if(process) {
			$scope.models.id = process.id;
			$scope.models.name = process.process_name;
		}

		$scope.close = function () {
			$modalInstance.dismiss();
		};

		$scope.submit = function () {
			ssModal.confirm('保存してもよろしいですか？').result.then(function(){
				tsktSettingApiService.add_process(taskMasterId, $scope.models).then(function(){
					$modalInstance.close();
				});
			});
		}
	}
]);

/* TsktSettingArujoCtrl as settingArujo  */
controller.controller('TsktSettingAddArujoModalCtrl', ['taskMasterId', 'client', 'arujo', '$scope', '$modalInstance', 'ssModal', 'TsktSettingAddArujoModalService', 'appConst', 
	function(taskMasterId ,client,  arujo, $scope, $modalInstance, ssModal, TsktSettingAddArujoModalService, appConst) {
		$scope.appConst = appConst;
		if(!arujo) {
			$scope.models = {
				name: null,
				value: null,
				arujo_description: null
			};
		}else{
			$scope.models  = $.extend(true, {}, arujo);
		}
		$scope.models.client_id = !client? null: client.id; 


		$scope.close = function () {
			$modalInstance.dismiss();
		};

		$scope.submit = function () {
			ssModal.confirm('保存してもよろしいですか？').result.then(function(){
				TsktSettingAddArujoModalService.add_arujo(taskMasterId, $scope.models).then(function(){
					$modalInstance.close();
				});
			});
		}
	}
]);
