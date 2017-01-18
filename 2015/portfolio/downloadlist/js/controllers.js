/**
 * DownloadListBaseCtrl as baseCtrl 
 */
var controller = angular.module('downloadlist.controllers.base', []);

controller.controller('DownloadListBaseCtrl', ['$scope','$rootScope','downloadlistConst',function($scope,$rootScope,downloadlistConst) {
	this.settings = {
		tab         : downloadlistConst.settings.tab['download']
	}
}]);


/**
 * DownloadListReportCtrl as reportCtrl 
 */
controller.controller('DownloadListReportCtrl', ['$scope','downloadListRestApi','downloadlistConst','utilMethods','$filter','$timeout',
	function($scope,downloadListRestApi,downloadlistConst,utilMethods,$filter,$timeout) {
	var _this = this;

	_this.sort = {
		place       : downloadlistConst.sort.place['start_date'],
		isDesc      : downloadlistConst.sort.isDesc[0]
	};

	_this.view = {
		displayType  : downloadlistConst.view['displayType'],
		service      : downloadlistConst.view['service'],
		is_my_history: downloadlistConst.view.is_my_history[1]
	}

	_this.pagination = {
		currentPage:downloadlistConst.pagination["currentPage"],
		numPages   :downloadlistConst.pagination["numPages"],
		limit      :downloadlistConst.pagination["limit"],
		maxSize    :downloadlistConst.pagination["maxSize"],
		offset     :downloadlistConst.pagination["offset"]
	};


	//page変更、表示件数変更を監視
	$scope.$watch(function () {
		_this.pagination.offset = (_this.pagination.currentPage - 1) * _this.pagination.limit;
	});


	//担当クライアント取得
	downloadListRestApi.getMyclients().then(function(res) {
		_this.my_clients   = res.data.my_clients;
		_this.user_id      = res.data.from;
		if(typeof _this.my_clients != "undefined") {
			_this.search       = '';
		}
	});

	downloadListRestApi.getReportHistory().then(function(res) {
		_this.master           = angular.copy(res.data);
		_this.table            = res.data;
		_this.service_details  = utilMethods.getServiceDetails(res.data) 

		_this.setTable = function () {
			$timeout( function(){ 
				_this.table =  $filter('tableFilter')(_this.master,_this.user_id,_this.view);
			});
		};


		$scope.orderBy("start_date");
		$scope.activeClass("start_date");
	});
}]);


