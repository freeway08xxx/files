/* Controllers */

/* DownloadListBaseCtrl as baseCtrl */
var controller = angular.module('downloadlist.controllers.base', []);

controller.controller('DownloadListBaseCtrl', ['$scope','$rootScope','downloadlistConst',function($scope,$rootScope,downloadlistConst) {
	//base settings
	this.settings = {
		tab         : downloadlistConst.settings.tab['download']
	}
}]);

/* DownloadListReportCtrl as reportCtrl */
controller.controller('DownloadListReportCtrl', ['$scope','downloadListRestApi','downloadlistConst','utilMethods',
	function($scope,downloadListRestApi,downloadlistConst,utilMethods) {
	var _this = this;

	_this.sort = {
		place       : downloadlistConst.sort.place['created_at'],
		isDesc      : downloadlistConst.sort.isDesc[1]
	};

	_this.view = {
		isDesc      : downloadlistConst.sort.isDesc[1],
		displayType : downloadlistConst.sort['displayType']
	}

	_this.pagination = {
		currentPage:downloadlistConst.pagination["currentPage"],
		numPages   :downloadlistConst.pagination["numPages"],
		limit      :downloadlistConst.pagination["limit"],
		maxSize    :downloadlistConst.pagination["maxSize"],
		offset     :downloadlistConst.pagination["offset"]
	};

	/**
	 * page変更、表示件数変更を監視
	 */
	$scope.$watch(function () {
		_this.pagination.offset = (_this.pagination.currentPage - 1) * _this.pagination.limit;
	});


	//担当クライアント取得
	downloadListRestApi.getMyclients().then(function(res) {
		_this.my_clients   = res.data.my_clients;
		_this.user_id      = res.data.from;
		if(typeof _this.my_clients != "undefined") {
			_this.search       = _this.my_clients[0]["id"];
		}

	});

	downloadListRestApi.getReportHistory().then(function(res) {
		//console.log(res.data)

		_this.master = angular.copy(res.data);
		_this.table  = res.data;

		_this.setTable = function () {
			_this.table = utilMethods.setTable(_this.master,_this.user_id,_this.view.displayType )
		};

		$scope.orderBy("updated_at");
	});
}]);


