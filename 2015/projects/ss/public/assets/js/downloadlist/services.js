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
		//テーブル要素を生成
		setTable: function (master,user_id,displayType) {
			var res           = [];
			var myhistory_res = [];

			_.each(master, function(val) {
				if(!_.isNull(val[displayType + '_id'])){ 
					res.push(val); 
				}
			})

			if($("#my_history").prop('checked')){

				_.each(res, function(val) {
					if(val['created_user'] == user_id){
						myhistory_res.push(val);
					}
				})

				return myhistory_res;

			}else{
				return res;
			}
		} 
	}
}]);