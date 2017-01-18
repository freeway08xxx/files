var services = angular.module( 'tskt.services.setting', [] );

services.service('tsktSettingService', ['$q', '$http', 'tsktModelProvider', '$stateParams', function($q, $http, tsktModelProvider, $stateParams) {
	var tables = {
		task : {
			config : {
				isScroll: true,
				columnDefs: [
						{width:'',	displayName:"タスク名",		field: "task_name",	
							template: '<a ui-sref="setting.task.detail({taskMasterId:$ssRow[\'id\']})">{{$ssRow[$ssColumn.field]}}</a>'},
						{width:'65px',	displayName:"操作",		field: "action",align:'center',
							template: '<button class="btn btn-danger btn-xs" ng-click="getExternalScopes().services.removeTask($ssRow)">削除</button>'
						},
				],
			},
		},
		process : {
			config : {
				columnDefs: [
						{width:'65px',	displayName:"メイン",	field: "main_process",align:'center', 
							template: '<span ng-if="$ssRow[$ssColumn.field]==1"><i style="color:#1E94F9;" class="glyphicon glyphicon-check"></i></span><span ng-if="$ssRow[$ssColumn.field]==0"><i style="color:#CBC3C3;" class="glyphicon glyphicon-check action-button" ng-click="getExternalScopes().services.selectMainProcess($ssRow)"></i></span>'},
						{width:'',	displayName:"プロセス名",		field: "process_name"},
						{width:'20%',	displayName:"操作",		field: "action", align:'center'	,
							template: '<button style="margin-right:5px;" class="btn btn-primary btn-xs" ng-click="getExternalScopes().services.openAddProcess($ssRow)">編集</button><button class="btn btn-danger btn-xs" ng-click="getExternalScopes().services.removeProcess($ssRow)">削除</button>'
						},
				],
			},
		},
		arujo : {
			config : {
				columnDefs: [
						{width:'',	displayName:"媒体名",		field: "media_name", 	},
						{width:'',	displayName:"ARUJO名",		field: "name"},
						{width:'',	displayName:"値",		field: "value"},
						{width:'',	displayName:"説明",		field: "arujo_description"},
						{width:'110px',	displayName:"操作",		field: "action",	
							template: '<button style="margin-right:5px;" class="btn btn-primary btn-xs" ng-click="getExternalScopes().services.openAddArujo($ssRow)">編集</button><button ng-if="!$ssRow[\'id\']" class="btn btn-danger btn-xs" ng-click="getExternalScopes().services.removeArujo($ssRow)">削除</button>', align:'center'},
				],
			},
		},
	}
	return {
		tables : tables,
	}
}]);


services.factory('TsktSettingModel', [ function() {
	return function() {
		var _this = this;
		_this.selectClient = false;
		_this.master = {
			category: null,
			task: null,
			process: null,
		};

		_this.current = {}

		_this.clientCombobox = {};
		_this.clientComboboxConfig = {
			isOutInput:false,
			account: {
				isView:false
			},
			registerApi : function(api){
				_this.clientComboboxApi = api;
			}
		};
	}
}]);

services.factory('TsktSettingTaskModel', [ function() {
	return function() {
		var _this = this;
		_this.master = {
			task: null
		}
	}
}]);

services.factory('TsktSettingTaskDetailModel', [ function() {
	return function() {
		var _this = this;
		_this.master = {
			arujo: null,
			process: null,
		}
	}
}]);

services.factory('tsktSettingArujoModel', [ function() {
	return function() {
		var _this = this;
		_this.master = {
			arujoClient: null,
		}
	}
}]);

services.service('tsktSettingApiService', ['$q', '$http', function($q, $http) {
	var api = {
		add_task: function(categoryId, name) {
			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/setting/add_task_save',
				method: 'POST',
				data:{category_id:categoryId, name:name},
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});
			return deferred.promise;
		},
		remove_task: function(taskId) {
			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/setting/remove_task/' + taskId,
				method: 'POST',
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});
			return deferred.promise;
		},
		add_process: function(taskMasterId, process) {
			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/setting/add_process_save/' + taskMasterId,
				method: 'POST',
				data:process,
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});
			return deferred.promise;
		},
		select_main_process: function(processId) {
			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/setting/select_main_process/' + processId,
				method: 'POST',
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});
			return deferred.promise;
		},
		removeProcess: function(id) {
			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/setting/remove_process/' + id,
				method: 'POST',
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});
			return deferred.promise;

		}
	}
	return api;
}]);

services.service('tsktSettingTaskDetailService', ['$q', '$http', function($q, $http) {
	var service = {
		removeArujo: function(data) {
			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/setting/remove_arujo/',
				data:data,
				method: 'POST',
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});
			return deferred.promise;
		},
	}
	return service;
}]);

services.service('TsktSettingAddArujoModalService', ['$q', '$http', function($q, $http) {
	var service = {
		add_arujo: function(taskMasterId, data) {
			var deferred = $q.defer();
			$http({
				url: '/sem/new/tasktracker/setting/add_arujo_save/' + taskMasterId,
				method: 'POST',
				data:data,
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});
			return deferred.promise;
		},
	}
	return service;
}]);
