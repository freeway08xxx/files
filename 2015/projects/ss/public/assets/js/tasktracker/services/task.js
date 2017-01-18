var services = angular.module( 'tskt.services.task', [] );



services.service('tsktTaskService', ['$q', '$http', function($q, $http) {
	return {
		getTask: function(taskId){
			return $http({
				url: '/sem/new/tasktracker/task/content/' + taskId,
				method: 'GET'
			});
		},
	};
}]);

services.service('tsktTaskSaveService', ['$q', '$http', 'tsktUtil', 'appConst', function($q, $http, tsktUtil, appConst) {
	var _this = this;
	var deferred = null;
	_this.services = {

		/**
		 * タスクマスターを取得する 
		 */
		getTaskMaster: function(category) {
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}
			deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/common/taskmaster/',
				params: {category_id:category},
				method: 'GET'
			}).success(function(res) {
				if(!res.task_masters) return false;
				deferred.resolve(res.task_masters);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});

			return deferred.promise;
		},



		/**
		 * プロセスマスターを取得する 
		 */
		getProcessMaster: function(taskMasterId) {
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}
			deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/common/process_master/',
				params: {task_master_id:taskMasterId},
				method: 'GET'
			}).success(function(res) {
				if(!res.process_masters) return false;
				var processMasterList = [];
				for(k in res.process_masters){
					processMasterList.push(tsktUtil.Factory.createProcessEditObject(res.process_masters[k]));
				}
				deferred.resolve(processMasterList);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});

			return deferred.promise;
		},


		/**
		 * プロセスユーザー一覧を取得する 
		 */
		getProcessUserlist: function() {
			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/common/process_userlist/',
				//params: {task_master_id:taskMasterId},
				method: 'GET'
			}).success(function(res) {
				if(!res.user_list) return false;
				deferred.resolve(res.user_list);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});

			return deferred.promise;
		},

		/**
		 * ARUJOマスター一覧を取得
		 */
		getArujoMaster: function(taskMasterId, clientId) {
			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/common/arujo_master',
				params: {task_master_id:taskMasterId, client_id:clientId},
				method: 'GET'
			}).success(function(res) {
				if(!res.task_master_arujo) return false;
				deferred.resolve(res.task_master_arujo);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});

			return deferred.promise;
		},

		/**
		 * ArujoMasterテーブル
		 */
		getArujoMasterTable: function() {
			var table = {
				config : {
					columnDefs: [
						{width:'25%',	displayName:"名前",		field: "name",	align:'left' , template: '<span ng-if="$ssRow.isAdd">	<input type="text" class="form-control" name="text_normal" ng-model="$ssRow[$ssColumn.field]" placeholder="ARUJO名を入力"/></span><span ng-if="!$ssRow.isAdd">	{{$ssRow[$ssColumn.field]}}</span>'},
						{width:'20%',	displayName:"メディア",	field: "media_name",	align:'left' , template: '<span ng-if="$ssRow.isAdd">	<select name="select_normal" class="form-control" 				ng-model="$ssRow[\'media_id\']"				ng-options="key as value for (key,value) in getExternalScopes().master.media_list"			><option value="" disabled>-- 未選択 --</option>			</select></span><span ng-if="!$ssRow.isAdd">	{{$ssRow[$ssColumn.field]}}</span>'},
						{width:'25%',	displayName:"値",		field: "value", align:'left', template: '<input type="text" class="form-control" name="text_normal" ng-model="$ssRow[$ssColumn.field]" placeholder="{{$ssColumn.displayName}}"/>'},
						{width:'25%',	displayName:"詳細",		field: "arujo_description", align:'left', template: '<input type="text" class="form-control" name="text_normal" ng-model="$ssRow[$ssColumn.field]" placeholder="{{$ssColumn.displayName}}"/>'},
						{width:'80px',	displayName:"操作",		field: "action",	
							template: '<span ng-if="$ssRow.isAdd"><button class="btn btn-danger btn-sm" ng-click="getExternalScopes().services.bindAction.removeArujo($ssRow)">削除</button></span>', align:'center'},
					],
				}
			}

			return table;
		},

		/**
		 * プロセステーブルを取得する 
		 */
		getProcessListTable: function() {
			input_template = '<input type="text" class="form-control" name="text_normal" ng-model="$ssRow[$ssColumn.field]" placeholder="{{$ssColumn.displayName}}"/>';
			//file_template = '<ss-input-file ng-model="$ssRow[$ssColumn.field]" button-value="\'選択\'">';
			var table = {
				config : {
					columnDefs: [
						{width:'65px',	displayName:"メイン",	field: "main_process",	
							template: '<input ng-value="$ssRow" name="main_process" type="radio" ng-model="getExternalScopes().models.main_process" />', align:'center'},
						{width:'',	displayName:"プロセス名",	field: "process_name",  template: input_template, align:'left'},
						{width:'',	displayName:"想定工数",		field: "forecast_cost",			template: input_template, align:'right'},
						{width:'',	displayName:"開始時刻",	field: "process_start_datetime",
							template: '<div tskt-datepicker ng-model="$ssRow[$ssColumn.field]" option="getExternalScopes().master.datepicker" ></div>', align:'center'},
						{width:'',	displayName:"作業者",		field: "user_id",		
							template: '<div ui-select ng-disabled="disabled" ng-model="$ssRow[$ssColumn.field]" theme="bootstrap" ng-disabled="disabled" style="width:100%;">	<ui-select-match placeholder="ユーザーを選択">{{$select.selected.user_name}}</ui-select-match>	<ui-select-choices repeat="value.id as value in getExternalScopes().master.processUserlist | filter:$select.search">		<div ng-bind-html="value.user_name | highlight: $select.search"></div>	</ui-select-choices></div><div ng-if="$ssRow[$ssColumn.field]"><a ng-click="getExternalScopes().services.bindAction.openUserTask($ssRow[$ssColumn.field])">リソース確認</a></div>',
							align:'left'},
						{width:'80px',	displayName:"操作",		field: "action",	
							template: '<span ng-if="$ssRow.isAdd"><button class="btn btn-danger btn-sm" ng-click="getExternalScopes().services.bindAction.removeProcess($ssRow)">削除</button></span>', align:'center'},
						//{width:'10%',	displayName:"リソース",		field: "file", 			template: file_template, align:'center'},
					],
				}
			}

			return table;
		},

		validModels: function(models) {
			// エラー初期化
			models.errors = [];

			// 設定
			var valids = [
				{
					error_message: '[タスク情報]が正しくありません。',
					valid: function() {
						//カテゴリ
						if(!models.category){
							return false;
						}
						//タスク名とマスタの決定
						if(!models.taskName || models.taskName == "" || !models.taskMaster){
							return false;
						}
						//クライアントとアカウントの件数
						if(!models.client || !models.accounts || models.accounts.length <= 0){
							return false;
						}

						return true;
					},
				},
				{
					error_message: '[タスク設定]が正しくありません。',
					valid: function() {
						if(!models.jobType || !models.limit){
							return false;
						}

						if(models.jobType == "1"){
							if(!models.limit.date) return false;
						}else if(models.jobType == "2"){
							if(!models.routine) return false;

							// 週次選択
							if(models.routine.frequency == "1"){
								var isSelected = false;
								for(var k in models.routine.days){
									if(models.routine.days[k]){
										isSelected = true; 
										break;
									}
								}
								if(!isSelected){
									return false;
								}
							}

							// 月次(日)選択
							if(models.routine.frequency == "2"){
								if(!models.routine.date || !models.routine.dailyUnit){
									return false;
								}
							}

							// 月次(月)選択
							if(models.routine.frequency == "3"){
								var isSelected = false;
								for(var k in models.routine.weekNumber){
									if(models.routine.weekNumber[k]){
										isSelected = true; 
										break;
									}
								}
								if(!isSelected){
									return false;
								}

								var isSelected = false;
								for(var k in models.routine.days){
									if(models.routine.days[k]){
										isSelected = true; 
										break;
									}
								}
								if(!isSelected){
									return false;
								}
							}
						}

						return true;
					},
				},
				{
					error_message: '[ARUJO]が正しくありません。',
					valid: function() {
						if(!models.arujoMaster || models.arujoMaster.length <= 0){
							return false;
						}
						for(var k in models.arujoMaster){
							if(!models.arujoMaster[k].value){
								return false;
							}
							if(models.arujoMaster[k].isAdd){
								if(!models.arujoMaster[k].name){
									return false;
								}
							}
						}
						return true;
					}
				},
				{
					error_message: '[プロセス]が正しくありません。',
					valid: function() {
						if(!models.processList || models.processList.length <= 0){
							return false;
						}
						for(var k in models.processList){
							if(!models.processList[k].process_name || !models.processList[k].forecast_cost || !models.processList[k].user_id){
								return false;
							}
						}
						return true;
					}
				}
			]

			// 実行
			for(var k in valids){
				if(!valids[k].valid()){
					models.errors.push({message: valids[k].error_message})
				}
			}

			//結果
			if(models.errors.length <= 0){
				return true;
			}else{
				return false;
			}
		},

		/**
		 * タスクの登録を行う 
		 */
		saveTask: function(models) {
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}
			deferred = $q.defer();
			var postData = tsktUtil.Factory.convertTaskSaveModels(models);
			$http({
				url: '/sem/new/tasktracker/task/save/save',
				method: 'POST',
				data:postData,
				headers:{"Content-type":undefined,"enctype":'multipart/form-data'},
			    transformRequest: null
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});
			return deferred.promise;

		}

	};

	return _this.services;
}]);

services.factory('tsktTaskSaveModel', [function() {
	return function() {
		this.category = null;
		this.taskMaster = null;
		this.taskName = null;
		this.description = '';
		this.jobType = "1";
		this.routine = {days:{"1":true,"2":true,"3":true,"4":true,"5":true}};
		this.limit = {};
		this.file = {};
	}
}]);
