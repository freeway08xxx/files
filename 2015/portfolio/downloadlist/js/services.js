/* Services */

var service = angular.module( 'downloadlist.services', []);

/**
 * ServerData API
 */
service.service('downloadListRestApi', ['$q', '$http', function($q, $http) {
	return {
		/* レポート履歴取得 */
		getReportHistory : function() {
			return $http({
				url: '/sem/new/downloadlist/history/get_history', 
				method: 'GET'
			});
		},

		/* 担当クライアント取得 */
		getMyclients : function() {
			return $http({
				url: '/sem/new/downloadlist/history/get_myclients', 
				method: 'GET'
			});
		}
	};
}]);



/**
 *  Util Functions
 */
service.factory('utilMethods',[function () {
	return {
		getServiceDetails: function (data) {
			res = [];
			_.each(data, function(val) {
				if(res.indexOf(val['service']) == -1) res.push(val['service']);
			})
			return res;
		}
	}
}]);







