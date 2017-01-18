var services = angular.module( 'tskt.services.common', [] );


services.service('tsktCommonService', ['$q', '$http', 'ssModal', function($q, $http, ssModal) {
	var _this = this;
	_this.services = {
		/**
		 * タスクマスターを取得する 
		 */
		getCategoryMaster: function() {
			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/common/category_master/',
				method: 'GET'
			}).success(function(res) {
				deferred.resolve(res.category);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});

			return deferred.promise;
		},

		/**
		 * タスクマスターを取得する 
		 */
		getTaskMaster: function(category) {
			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/common/taskmaster/',
				params: {category_id:category},
				method: 'GET'
			}).success(function(res) {
				if(!res.task_masters){
					deferred.resolve([]);
				}else{
					deferred.resolve(res.task_masters);
				}
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});

			return deferred.promise;
		},

		/**
		 * プロセスマスターを取得する 
		 */
		getProcessMaster: function(taskMasterId) {
			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/common/process_master/',
				params: {task_master_id:taskMasterId},
				method: 'GET'
			}).success(function(res) {
				if(!res.process_masters) {
					deferred.resolve([]);
				}else{
					deferred.resolve(res.process_masters);
				}
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
				if(!res.task_master_arujo){
					deferred.resolve([]);
				}else{
					deferred.resolve(res.task_master_arujo);
				}
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});

			return deferred.promise;
		},

		openUserResource: function(userId){
			return ssModal.custom({
				size:'lg',
				//backdrop:false,
				templateUrl: '/sem/new/tasktracker/common/user_resource.html',
				controller:'TsktUserResourceCtrl',
				resolve: {
					userId: function () {
						return userId;
					}
				}
			}).result;
		},

		
	}

	return _this.services;
}]);

/**
 * /Util
 */
services.service('tsktUtil', ['$q', '$http', 'appConst', function($q, $http, appConst) {
	var _this = this;
	_this.services = {
		Factory: {
			/**
			 * タスクDataからタスクオブジェクトの生成 
			 */
			createTaskObject: function(taskData) {
				var taskObject = $.extend(true, [], taskData);

				//種別
				if(taskObject.category){
					taskObject.category_name = appConst.category[taskObject.category].name;
				}

				//種別
				if(taskObject.job_type){
					taskObject.typeName = appConst.job.type[taskObject.job_type];
				}

				//ステータス
				if(taskObject.task_status){
					taskObject.statusName = appConst.status[taskObject.task_status];
				}

				return taskObject;
			},

			/**
			 * プロセスDataからプロセスオブジェクトの生成 
			 */
			createProcessObject: function(processData) {
				processObject = $.extend(true, {}, processData);

				//種別
				if(processObject.job_type){
					processObject.typeName = appConst.job.type[processObject.job_type];
				}

				//ステータス
				if(processObject.process_status){
					processObject.statusName = appConst.status[processObject.process_status];
					processObject.process_status_update = processObject.process_status;
				}

				return processObject;
			},
			/**
			 * Arujoオブジェクトを作成する
			 */
			createArujoObject: function(arujoData) {
				var arujoObject = $.extend(true, {}, arujoData);
				if(!arujoObject.media_id){
					arujoObject.media_id = '0';
				}
				if(!arujoObject.media_name){
					switch(arujoObject.media_id){
						case '1': arujoObject.media_name = 'Yahoo'; break;
						case '2': arujoObject.media_name = 'Google'; break;
						case '3': arujoObject.media_name = 'Ydn'; break;
						default: arujoObject.media_name = '共通'; break;
					}
				}
				if(!arujoObject.name){
					arujoObject.name = null;
				}
				if(!arujoObject.value){
					arujoObject.value = null;
				}
				if(!arujoObject.arujo_description){
					arujoObject.arujo_description = null;
				}


				return arujoObject;
			},

			/**
			 * プロセスマスター情報からオブジェクトを生成 
			 */
			createProcessEditObject: function(processData) {
				processObject = $.extend(true, {}, processData);

				// コストはデフォルト０とする
				if(processObject.forecast_cost == null){
					processObject.forecast_cost = 0;
				}
				if(processObject.process_name == null){
					processObject.process_name = "";
				}
				
				if(processObject.process_start_datetime == null){
					processObject.process_start_datetime = {
						date: new Date(),
						hour: 10,
						minute: '00',
					}
				}else{
					var date = new Date(processObject.process_start_datetime);
					processObject.process_start_datetime = {
						date: date,
						hour: date.getHours(),
						minute: ("0"+date.getMinutes()).slice(-2),
					}
				}

				return processObject;
			},

			convertTaskSaveModels: function(saveModels) {
				var formData = new FormData();
				
				for(var k in saveModels){
					var value = saveModels[k];

					//メインプロセス情報はprocesslistの配列に含める為追加しない
					if(k == 'main_process'){
						continue;
					}

					//FileデータはJson形式にせずそのまま出力
					else if(k == 'file'){
						formData.append('file',value);
					}
					
					else{
						//※プロセス一覧はそれぞれでfile情報を保持しているが
						//そのまま追加するとFileデータとして受け取れない為
						//名前を直接指定して追加する
						//また、紐付けを維持する為にポスト名を元配列に追加
						if(k == 'processList'){
							for(index in value){
								//メインプロセス情報
								value[index]['main_process'] = 0;
								if(value[index] == saveModels.main_process){
									value[index]['main_process'] = 1;
								}
							}
						}
						formData.append(k , angular.toJson(value));
					}
				}

				return formData;
			},
		},

		Date: {
			getUnixTime: function(date){
				if(!date){
					date = new Date();
				}

				return parseInt( date /1000 );
			},
			getDate: function(unixTime){
				if(!unixTime){
					return time();
				}

				return new Date( unixTime * 1000 );
			}
		}
	}

	return _this.services;
}]);


services.service('tsktModelProvider', ['tsktTopModel', 'tsktTopTasksModel', 'tsktTopProcessModel', 'tsktTopTaskDetailModel', 'tsktTaskSaveModel', 'tsktTopTaskEditableModel', 'TsktSettingModel', 'tsktSettingArujoModel', 'TsktSettingTaskModel', 'TsktSettingTaskDetailModel',
	function(tsktTopModel, tsktTopTasksModel, tsktTopProcessModel, tsktTopTaskDetailModel, tsktTaskSaveModel, tsktTopTaskEditableModel, TsktSettingModel, tsktSettingArujoModel, TsktSettingTaskModel, TsktSettingTaskDetailModel) {
		var _this = this;
		_this.models = {};
	
		var provider = function(name, model) {
			return {
				forge: function(obj) {
					_this.models[name]	= new model(obj);
					return _this.models[name];
				},
				get: function() {
					return _this.models[name];
				},
				clear: function() {
					delete _this.models[name];
				}
			}
		}
	
		return  {
			top : provider('top',tsktTopModel),
			topTasks : provider('topTasks',tsktTopTasksModel),
			topProcess : provider('topProcess',tsktTopProcessModel),
			topTaskDetail: provider('topTaskDetail',tsktTopTaskDetailModel),
			topTaskEditable: provider('topTaskEditable',tsktTopTaskEditableModel),
			taskSave: provider('taskSave',tsktTaskSaveModel),
			setting : provider('setting',TsktSettingModel),
			settingTask: provider('settingTask',TsktSettingTaskModel),
			settingTaskDetail: provider('settingTaskDetail',TsktSettingTaskDetailModel),
			settingArujo : provider('settingArujo',tsktSettingArujoModel),
		};
}]);


services.service('TsktUserResourceService', ['$q', '$http', function($q, $http ) {
	return {
		getUserResourceData: function(userId){
			return $http({
				url: '/sem/new/tasktracker/common/user_repource_data/' + userId,
				method: 'GET'
			});
		},
	};

}]);

