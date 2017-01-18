var services = angular.module( 'tskt.services.top', [] );

// =============================================================================
// Service 
// =============================================================================
/**
 * /top サービス  
 */
services.service('tsktTopService', [function() {
	var _this = this;
	return _this;
}]);

/**
 * /top/tasks サービス  
 */
services.service('tsktTopTasksService', ['$q', '$http', 'tskttopTasksTable', function($q, $http, tskttopTasksTable) {
	var _this = this;
	_this.services = {}
	_this.public = {
		/**
		 * 利用するテーブルサービス 
		 */
		table: tskttopTasksTable,
	}

	return _this.public;
}]);


services.service('tskttopTasksTable', ['$q', '$http','tsktUtil', '$filter', function($q, $http, tsktUtil, $filter) {
	var _this = this;	
	_this.tableData = [];
	_this.tableDataFilter = {};
	_this.table = {
		config: {
			registerApi: function(api) {
				_this.table.api = api;
			},
			isScroll: true,
			selection: {},
			columnDefs: [
				{width:'50px', 	displayName:"ID",		field: "task_id" , 		align:'right', isSortEnable:true},
				{width:'25%',	displayName:"タスク名",		field: "task_name",	
					template: '<span><a ng-click="getExternalScopes().services.bindAction.clickTaskView($ssRow)" herf="#">{{$ssRow[$ssColumn.field]}}</a></span>'},
				{width:'35%',	displayName:"クライアント",	field: "client_name", isSortEnable:true	},
				{width:'100px',	displayName:"カテゴリ",		field: "category_name",		align:'center', isSortEnable:true},
				{width:'100px',	displayName:"種別",			field: "typeName",		align:'center', isSortEnable:true},
				{width:'150px',	displayName:"期限",	field: "task_limit_datetime",align:'center',
					template: '<span>{{ $ssRow[$ssColumn.field] | moment:\'YYYY.M.D HH:mm\'}}</span>', isSortEnable:true},
				{width:'150px',	displayName:"完了",	field: "task_end_datetime",	align:'center', isHide:true ,
					template: '<span>{{ $ssRow[$ssColumn.field] | moment:\'YYYY.M.D HH:mm\'}}</span>', isSortEnable:true},
				{width:'50px',	displayName:"遅延",			field: "delay",	
					template: '<span style="color:red;">{{$ssRow[$ssColumn.field]}}</span>', align:'center'},
			],
		},
		services: {

			/**
			 * データをテーブルにセットする 
			 */
			setData : function(data){
				_this.table.data = data;
			},

			/**
			 * 非同期による取得を行う  
			 */
			load : function(status,from,to) {
				clearTimeout(_this.timeid);
				_this.timeid = setTimeout( function(){
					_this.public.getList(status,from,to).then(function(res){
						if(!res || !res.data.tasks){
							return false
						}

						var taskList = [];
						for(var k in res.data.tasks){
							taskList.push(tsktUtil.Factory.createTaskObject(res.data.tasks[k]));
						}

						if(status == "2" && _this.table.api){
							_this.table.api.showField('task_end_datetime');
							_this.table.api.hideField('task_limit_datetime');
						}
						if(status == "1" && _this.table.api){
							_this.table.api.hideField('task_end_datetime');
							_this.table.api.showField('task_limit_datetime');
						}

						_this.tableData = taskList;
						_this.table.services.filter();
					});
				}, 500);
			},

			/**
			 * Filter対象を登録する 
			 */
			registFilter : function(key,value) {
				if(!value) {
					_this.tableDataFilter = {};
				}
				if(value && key == 'client_name'){
					_this.tableDataFilter = {client_name:value};		
				}
			},

			/**
			 * Filterを行う 
			 */
			filter : function() {
				var filteringData = $filter('filter')(_this.tableData, _this.tableDataFilter);
				_this.table.services.setData(filteringData);
			}
		}
	}

	_this.public = {
		getList: function(status,from,to){
			from = tsktUtil.Date.getUnixTime(from);
			to = tsktUtil.Date.getUnixTime(to);
			return $http({
				url: '/sem/new/tasktracker/top/task/list/',
				params: {status: status, dateFrom: from, dateTo: to},
				method: 'GET'
			});
		},
		getTable : function() {
			_this.tableData = [];
			_this.tableDataFilter = {};
			_this.table.data = null;
			return _this.table;
		}
	};

	return _this.public;

}]);

/**
 * /top/process サービス  
 */
services.service('tsktTopProcessService', ['$q', '$http', 'tsktTopProcessTable', function($q, $http, tsktTopProcessTable) {
	var _this = this;
	_this.public = {
		table: tsktTopProcessTable,
	}

	return _this.public;
}]);


/**
 * /top/process のテーブルを扱うサービス
 */
services.service('tsktTopProcessTable', ['$q', '$http', 'tsktUtil', '$filter', function($q, $http, tsktUtil, $filter) {
	var _this = this;
	_this.tableData = [];
	_this.tableDataFilter = {};
	_this.table = {
		config : {
			loading : true,
			registerApi: function(api) {
				_this.table.api = api;
			},
			isScroll: true,
			selection: {},
			columnDefs: [
					{width:'5%', 	displayName:"ID",	field: "process_id" , 			align:'right', isSortEnable:true},
					{width:'15%',	displayName:"プロセス名",	field: "process_name",isSortEnable:true	},
					{width:'15%',	displayName:"クライアント",	field: "client_name", isSortEnable:true},
					{width:'5%',	displayName:"タスクID",	field: "task_id",	align: 'right', isSortEnable:true},
					{width:'10%',	displayName:"タスク名",		field: "task_name",	
						template: '<span><a ng-click="getExternalScopes().services.bindAction.clickTaskView($ssRow)" herf="#">{{$ssRow[$ssColumn.field]}}</a></span>'},
					{width:'5%',	displayName:"工数",		field: "forecast_cost",	align:'right',
						template: '<span>{{$ssRow[$ssColumn.field]}}分</span>'},
					{width:'10%',	displayName:"開始",	field: "process_start_datetime",align:'center',isSortEnable:true, 
						template: '<span>{{ $ssRow[$ssColumn.field] | moment:\'YYYY.M.D HH:mm\'}}</span>'},
					{width:'10%',	displayName:"完了",	field: "process_end_datetime",	align:'center', isHide:true, isSortEnable:true, 
						template: '<span>{{ $ssRow[$ssColumn.field] | moment:\'YYYY.M.D HH:mm\'}}</span>', isSortEnable:true},
					{width:'5%',	displayName:"遅延",			field: "delay",
						template: '<span style="color:red;">{{$ssRow[$ssColumn.field]}}</span>', align:'center'},
					{width:'10%',	displayName:"ファイル",			field: "file", 
						//template: '<div class="form-inline"><span><ss-input-file ng-model="$ssRow[$ssColumn.field]" button-value="\'選択\'" size-type="\'sm\'"></span><button type="button" class="btn btn-primary btn-sm" ng-click="getExternalScopes().services.bindAction.clickCompButton($ssRow)" >完了</button><div>', align: 'center'}
						template: '<div class="form-inline"><ss-input-file ng-change="getExternalScopes().services.bindAction.changeFile($ssRow)" ng-model="$ssRow[$ssColumn.field]" button-value="\'選択\'" size-type="\'sm\'"></div>', align: 'center'},
					{width:'10%',	displayName:"ステータス変更",	field: "process_status_update", 
						template: '<select class="form-control" ng-model="$ssRow[$ssColumn.field]"ng-options="key as value for (key,value) in getExternalScopes().getScope().appConst.status"ng-change="getExternalScopes().services.bindAction.changeStatusUpdate($ssRow)"></select>'	
					},
			],
			/*
			onRegisterApi : function (gridApi) {
				_this.table.api.grid = gridApi;
			},
			*/
		},
		service : {

			/**
			 *  データをGridTableに反映して表示する
			 *
			 * @params array data 表示データ 
			 */
			setData: function(data){
				_this.table.data = data;
			},

			/**
			 * ajax通信を行い、表示する
			 * その際、filterも実行する
			 *
			 * @params int status 表示ステータス
			 */
			load: function(status,from,to) {
				clearTimeout(_this.timeid);
				_this.timeid = setTimeout( function(){
					_this.public.getList(status,from,to).then(function(res){
						if(!res || !res.data.process){
							return false
						}

						var processList = [];
						for(var k in res.data.process){
							processList.push(tsktUtil.Factory.createProcessObject(res.data.process[k]));
						}

						if(status == "2" && _this.table.api){
							_this.table.api.showField('process_end_datetime');
							_this.table.api.hideField('process_start_datetime');
						}
						if(status == "1" && _this.table.api){
							_this.table.api.hideField('process_end_datetime');
							_this.table.api.showField('process_start_datetime');
						}

						_this.tableData = processList;
						_this.table.service.filter();
					});
				}, 500);
			},

			/**
			 * filter情報の登録を行う
			 *
			 * @params string key フィルタ対象カラム
			 * @params string value フィルタ対象値
			 */
			registFilter: function(key,value) {
				if(!value) {
					_this.tableDataFilter = {};
				}
				if(value && key == 'client_name'){
					_this.tableDataFilter = {client_name:value};		
				}
			},

			/**
			 * フィルターを実行する 
			 * 対象はregistFilter()で登録されたfilter値
			 */
			filter: function() {
				var filteringData = $filter('filter')(_this.tableData, _this.tableDataFilter);
				_this.table.service.setData(filteringData);
			}
		}
	};
	_this.public = {

		/**
		 *  プロセス一覧の取得
		 */
		getList: function(status,from,to){
			from = tsktUtil.Date.getUnixTime(from);
			to = tsktUtil.Date.getUnixTime(to);
			return $http({
				url: '/sem/new/tasktracker/top/process/list/',
				params: {status: status, dateFrom: from, dateTo: to},
				method: 'GET'
			});
		},

		/**
		 * プロセステーブルの取得 
		 */
		getTable : function() {
			_this.tableData = [];
			_this.tableDataFilter = {};
			_this.table.data = null;
			return _this.table;
		},

		/**
		 * プロセスの完了更新 
		 */
		saveStatus: function(processId, status){
			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/top/process/update_status/'+processId,
				method: 'POST',
				data:{status: status},
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});

			return deferred.promise;
		},

		saveFile : function(processId, file) {
			var formData = new FormData();
			formData.append('file',file);

			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/top/process/fileup/'+processId,
				method: 'POST',
				data:formData,
				headers:{"Content-type":undefined,"enctype":'multipart/form-data'},
			    transformRequest: null
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});
			return deferred.promise;
		},
	};

	return _this.public;
}]);


/**
 * /top/taskDetail サービス  
 */
services.service('tsktTopTaskDetailService', ['$q', '$http', 'ssModal', 'tsktUtil', 'tsktTopTaskDetailEditService', function($q, $http, ssModal, tsktUtil, tsktTopTaskDetailEditService) {
	var _this = this;
	_this.services = {
		/**
		 * 利用するテーブルサービス 
		 */
		getTaskDetail: function(taskId){
			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/top/detail/content/' + taskId,
				method: 'GET'
			}).success(function(res) {
				if(!res) return false;

				var taskDetail = {
					task : tsktUtil.Factory.createTaskObject(res.task),
					accountList: [],
					processList: [],
					fileList: [],
					arujoList: [],
				};

				if(res.account_list) {
					taskDetail.accountList = res.account_list;
				}
				if(res.process_list) {
					for(k in res.process_list){
						taskDetail.processList.push(tsktUtil.Factory.createProcessObject(res.process_list[k]));
					}
				}
				if(res.file_list) {
					taskDetail.fileList = res.file_list;
				}
				if(res.arujo_list) {
					taskDetail.arujoList = res.arujo_list;
				}

				deferred.resolve(taskDetail);

			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});

			return deferred.promise;
		},


		/**
		 * Bomb登録画面を開く
		 */
		openRegistBomb: function(processId) {
			return ssModal.custom({
				//size:'lg',
				//backdrop:false,
				templateUrl: '/sem/new/tasktracker/task/bomb/index.html',
				controller:'TsktTopTaskDetailBombModalCtrl',
				resolve: {
					processId: function () {
						return processId;
					}
				}
			}).result;
		},

		getAccountListTable: function() {
			var table = {
				config : {
					columnDefs: [
						{width:'',	displayName:"媒体名",	field: "media_name", 	},
						{width:'', 	displayName:"アカウントID",	field: "account_id" , align:'right'},
						{width:'',	displayName:"アカウント名",		field: "account_name",	},
					],
				}
			}

			return table;
		},

		getProcessListTable: function() {
			var table = {
				config : {
					columnDefs: [
						{width:'2%',	displayName:"主",	field: "main_process",	
							template: '<span ng-if="getExternalScopes().models.mainProcess==$ssRow"><i style="color:#337ab7;" class="glyphicon glyphicon-check"></i></span>', align:'center'},
						{width:'', 	displayName:"プロセスID",	field: "id" , 				align:'right'},
						{width:'',	displayName:"プロセス",		field: "process_name",		},
						{width:'',	displayName:"ステータス",	field: "statusName",		align:'center'},
						{width:'',	displayName:"想定工数",		field: "forecast_cost",		
							template: '<span>{{$ssRow[$ssColumn.field]}}分</span>',	align:'right'},
						{width:'',	displayName:"開始時刻",		field: "process_start_datetime",	align:'center',
							template: '<span>{{ $ssRow[$ssColumn.field] | moment:\'YYYY.M.D HH:mm\'}}</span>'},
						{width:'',	displayName:"作業者",		field: "owner_user_name",	align:'center', template: '<a ng-click="getExternalScopes().bindAction.openUserTask($ssRow[\'owner_user_id\'])">{{$ssRow[$ssColumn.field]}}</a>'},
						{width:'',	displayName:"ファイル",			field: "file",	
							template: '<span ng-if="$ssRow[$ssColumn.field]"><a href="{{$ssRow[$ssColumn.field][\'download_link\']}}">{{$ssRow[$ssColumn.field][\'file_name\']}}</a></span>',	align:'center'},
						{width:'',	displayName:"削除/BOMB",			field: "remove",	
							template: '<span ng-if="($ssRow[\'process_status\']==1)">	<button class="btn btn-danger btn-xs" ng-click="getExternalScopes().bindAction.clickRemoveProcess($ssRow)">		削除	</button></span><span ng-if="!($ssRow[\'process_status\']==1)">	<button class="btn btn-warning btn-xs" ng-click="getExternalScopes().bindAction.clickBombSave($ssRow)">		BOMB 				<span ng-if="($ssRow[\'process_bomb_count\'])" class="badge badge-important">{{$ssRow[\'process_bomb_count\']}}</span>	</button></span>', align:'center'},
					],
				}
			}

			return table;
		},
		getProcessEditableTable: function() {
			var table = {
				config : {
					registerApi: function(api) {
						table.api = api;
					},
					columnDefs: [
						{width:'',	displayName:"主",	field: "main_process",	
							template: '<input ng-value="$ssRow" name="main_process" type="radio" ng-model="getExternalScopes().models.editModel.mainProcess" />', align:'center'},
						{width:'',	displayName:"プロセス名",	field: "process_name", 
							template: '<input type="text" class="form-control" name="text_normal" ng-model="$ssRow[$ssColumn.field]" placeholder="{{$ssColumn.displayName}}"/>' , align:'left'},
						{width:'80px',	displayName:"想定工数",		field: "forecast_cost",
							template: '<div class="form-inline"><input type="text" class="form-control" style="width:40px;" name="text_normal" ng-model="$ssRow[$ssColumn.field]" placeholder="{{$ssColumn.displayName}}"/><span> 分</span></div>' , align:'left'},
						{width:'',	displayName:"開始時刻",	field: "process_start_datetime",
							template: '<div tskt-datepicker ng-model="$ssRow[$ssColumn.field]" option="getExternalScopes().models.datepicker"></div>', align:'center'},
						{width:'',	displayName:"作業者",		field: "owner_user_id",		
							template: '<div ui-select ng-disabled="disabled" ng-model="$ssRow[$ssColumn.field]" theme="bootstrap" ng-disabled="disabled" style="width:100%;">	<ui-select-match placeholder="ユーザーを選択">{{$select.selected.user_name}}</ui-select-match>		<ui-select-choices repeat="value.id as value in getExternalScopes().models.userlist | filter:$select.search">				<div ng-bind-html="value.user_name | highlight: $select.search"></div>		</ui-select-choices></div><div ng-if="$ssRow[$ssColumn.field]"><a ng-click="getExternalScopes().bindAction.openUserTask($ssRow[$ssColumn.field])">リソース確認</a></div>',
							align:'left'},
						{width:'80px',	displayName:"操作",		field: "action",	
							template: '<span ng-if="$ssRow.isAdd"><button class="btn btn-danger btn-sm" ng-click="getExternalScopes().bindAction.clickRemoveAddProcess($ssRow)">削除</button></span>', align:'center'},
					],
				}
			}

			return table;
		},

		getFileListTable: function() {
			var table = {
				config : {
					columnDefs: [
						{width:'', 	displayName:"ファイル名",	field: "file_name" },
						{width:'',	displayName:"登録者",		field: "owner_user_name",	align:'center'},
						{width:'',	displayName:"ダウンロード",	field: "download_link",	
							template: '<span ng-if="$ssRow[$ssColumn.field]"><a href="{{$ssRow[$ssColumn.field]}}">ダウンロード</a></span>',	align:'right'},
					],
				}
			}

			return table;
		},

		getArujoTable: function() {
			var table = {
				config: {
					columnDefs: [
						{width:'20%',	displayName:"名前",		field: "name",				align:'left'},
						{width:'20%',	displayName:"メディア",	field: "media_name",		align:'left'},
						{width:'20%',	displayName:"値",		field: "value", 			align:'left'},
						{width:'20%',	displayName:"詳細",		field: "arujo_description", align:'left'},
					],
				}
			}

			return table;
		},


		getArujoEditableTable: function() {
			var table = {
				config: {
					columnDefs: [
						{width:'20%',	displayName:"名前",		field: "name",				align:'left'},
						{width:'20%',	displayName:"メディア",	field: "media_name",		align:'left'},
						{width:'20%',	displayName:"値",		field: "value", 			align:'left', template: '<input type="text" class="form-control" name="text_normal" ng-model="$ssRow[$ssColumn.field]" placeholder="{{$ssColumn.displayName}}"/>'},
						{width:'20%',	displayName:"詳細",		field: "arujo_description", align:'left', template: '<input type="text" class="form-control" name="text_normal" ng-model="$ssRow[$ssColumn.field]" placeholder="{{$ssColumn.displayName}}"/>'},
					],
				}
			}

			return table;
		},

		removeTask: function(taskId) {
			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/task/save/remove/'+taskId,
				method: 'POST',
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});
			
			return deferred.promise;
		},

		removeProcess: function(processId) {
			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/task/save/remove_process/'+processId,
				method: 'POST',
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});
			
			return deferred.promise;
		},


		edit: tsktTopTaskDetailEditService,
	}

	return _this.services;
}]);


/**
 * /top/taskEditサービス 
 */
services.service('tsktTopTaskDetailEditService', ['$q', '$http', 'tsktUtil', 'tsktModelProvider', function($q, $http, tsktUtil, tsktModelProvider) {
	var _this = this;
	_this.api = {
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
	}

	_this.services = {
		setEditModel: function(models) {
			models.editModel = tsktModelProvider.topTaskEditable.forge(models);
			return models;
		},
		convertModel: function(models){
			models = _this.services.setEditModel(models);
			if(!models.task){
				return false;
			}

			/**
			 * 日付設定項目を定例なら時間のみにする 
			 */
			if(models.task.job_type == "2"){
				models.datepicker.isDateHide = true;
			}else{
				models.datepicker.isDateHide = false;
			}

			/**
			 * プロセス編集テーブルにデータを反映させる 
			 */
			if(models.processListTable.data){
				//プロセス編集用テーブル用にユーザー一覧を取得 
				if(!models.userlist){
					_this.api.getProcessUserlist().then(function(userlist){
						models.userlist = userlist;
					});
				}

				var editProcessList = [];
				_.forEach(models.processListTable.data, function(n, key) {
					if(n.process_status == 1){
						var processObject = tsktUtil.Factory.createProcessEditObject(n);
						editProcessList.push(processObject);
						//メインプロセス
						if(models.mainProcess){
							if(models.mainProcess.id == n.id){
								models.editModel.mainProcess = processObject;
							}
						}
					}
				});
				if(!models.editModel.mainProcess || Object.keys(models.editModel.mainProcess).length <= 0){
					models.processEditableTable.api.hideField('main_process');
				}else{
					models.processEditableTable.api.showField('main_process');
				}
				models.processEditableTable.data = editProcessList;
			}

			/**
			 * コンボボックスにアカウントをセット 
			 */
			if(models.accountListTable.data){
				var setAccounts = [];
				_.forEach(models.accountListTable.data, function(n, key) {
					setAccounts.push({
						account_id:n.account_id,
						media_id:parseInt(n.media_id),
					});	
				});
				//コンボボックス初期化
				models.clientComboboxApi.setModels({
					client:models.task.client_id,
					accounts:setAccounts,
				});
			}

			/**
			 * ARUJOテーブルにデータを反映させる 
			 */
			models.arujoEditableTable.data = models.arujoListTable.data;
		},

		updateTask: function(taskId,models) {
			var deferred = $q.defer();
			var postData = tsktUtil.Factory.convertTaskSaveModels({
				file: models.editModel.file,
				main_process:models.editModel.mainProcess,
				processList: models.processEditableTable.data,
				accounts:models.clientCombobox.accounts,
				task: {
					task_name: models.editModel.task_name,
					task_limit_datetime: models.editModel.task_limit_datetime,
					description: models.editModel.task_description,
				},
				arujoList: models.arujoEditableTable.data
			});
			$http({
				url: '/sem/new/tasktracker/task/save/update/'+taskId,
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
		},

		addProcess: function(models,userId) {
			processMasterObject = tsktUtil.Factory.createProcessEditObject({});
			processMasterObject.owner_user_id = userId;
			processMasterObject.isAdd = true;
			models.processEditableTable.data.push(processMasterObject);
		},

		removeProcess: function(models,row) {
			for(k in models.processEditableTable.data){
				var value = models.processEditableTable.data[k];
				if (value == row) {
					models.processEditableTable.data.splice(k, 1);
					break;
				}
			}
		}
	}

	return _this.services;
}]);

/**
 * /top/detail/bomMdal サービス  
 */
services.service('TsktTopTaskDetailBombModalService', ['$q', '$http', 'tsktUtil', function($q, $http, tsktUtil) {
	return {
		get: function(processId) {
			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/task/bomb/get/'+processId,
				method: 'GET',
			}).success(function(res) {
				deferred.resolve(res.bombs);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});

			return deferred.promise;
		},
		save: function(processId, bomb_description) {
			var deferred = $q.defer();
			data = {bomb_description: bomb_description};
			$http({
				url: '/sem/new/tasktracker/task/bomb/save/'+processId,
				method: 'POST',
				data: data,
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});
			
			return deferred.promise;
		},
	}
}]);

/**
 * /top/detail/bomMdal サービス  
 */
services.service('TsktTopTaskDetailRoutineModalService', ['$q', '$http', 'tsktUtil', function($q, $http, tsktUtil) {
	return {
		save: function(taskId, routineEndDate) {
			var deferred = $q.defer();
			routineEndDate = tsktUtil.Date.getUnixTime(routineEndDate);

			data = {routineEndDate: routineEndDate};
			$http({
				url: '/sem/new/tasktracker/task/save/routine_setting/'+taskId,
				method: 'POST',
				data: data,
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});
			
			return deferred.promise;
		},
	}
}]);


// =============================================================================
// Models
// =============================================================================

/**
 * /top Model  
 */
services.factory('tsktTopModel', [function() {
	return function() {
		this.selectTaskId = 0;
	}
}]);

/**
 * /top/tasks Model  
 */
services.service('tsktTopTasksModel', [ function() {
	return function() {
		var toDate = new Date();
		toDate.setHours(0);
		toDate.setMinutes(0);
		toDate.setSeconds(0);
		toDate.setDate(toDate.getDate() + 7 );

		var fromDate = new Date();
		fromDate.setHours(0);
		fromDate.setMinutes(0);
		fromDate.setSeconds(0);
		fromDate.setDate(fromDate.getDate() - 7 );

		this.status = '1',
		this.dateFrom = {
			date:fromDate
		},
		this.dateTo = {
			date:toDate
		}
	}
}]);


/**
 * /top/tasks Model  
 */
services.service('tsktTopProcessModel', [ function() {
	return function() {
		var toDate = new Date();
		toDate.setHours(0);
		toDate.setMinutes(0);
		toDate.setSeconds(0);
		toDate.setDate(toDate.getDate() + 7 );

		var fromDate = new Date();
		fromDate.setHours(0);
		fromDate.setMinutes(0);
		fromDate.setSeconds(0);
		fromDate.setDate(fromDate.getDate() - 7 );

		this.status = '1',
		this.dateFrom = {
			date:fromDate
		},
		this.dateTo = {
			date:toDate
		}
	}
}]);


/**
 * /top/Detail Model  
 */
services.factory('tsktTopTaskDetailModel', [ function() {
	return function() {
		var _this = this;
		_this.mainProcess = {};
		_this.userlist = null;
		_this.isView = false;
		_this.datepicker = {};
		_this.clientCombobox = {};
		_this.clientComboboxApi = {};
		_this.clientComboboxConfig = {
			registerApi : function(api){
				_this.clientComboboxApi = api;
			}
		};
		_this.editModel = null;
	}
}]);

services.factory('tsktTopTaskEditableModel', [ function() {
	return function(obj) {
		var date = new Date(obj.task.task_limit_datetime);

		this.task_name = obj.task.task_name,
		this.task_description = obj.task.task_description;
		this.task_limit_datetime = {
			date: date,
			hour: date.getHours(),
			minute: ("0"+date.getMinutes()).slice(-2),
		},
		this.mainProcess = {};
		this.file = {};
	}
}]);

