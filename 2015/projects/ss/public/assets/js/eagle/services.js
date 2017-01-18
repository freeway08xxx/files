/* Services */

var app = angular.module( 'eagle.services', [] )

app.service('eagleReserveService', ['$q', '$http', function($q, $http) {
	var deferred = null;
	return {

		/* フォームページ情報取得 */
		save : function(clientId,accounts,isSyncSkip) {
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}
			deferred = $q.defer();
			$http({
				url: '/sem/new/eagle/reserve/save',
				data: {clientId:clientId,accounts:accounts,isSyncSkip:isSyncSkip},
				method: 'POST'
			}).success(function(res) {
				//resolve
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});

			return deferred.promise;
		},

		copy : function(eagleId){
			return $http({
				url: '/sem/new/eagle/reserve/copy', 
				data: {'eagle_id':eagleId},
				method: 'POST'
			});
		}
	}
}]);



app.service('eagleHistoryService', ['$q', '$http', function($q, $http) {
	var deferred = null;
	return {

		/* フォームページ情報取得 */
		getList : function(clientId,accounts) {
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}
			deferred = $q.defer();
			$http({
				url: '/sem/new/eagle/history/list',
				method: 'GET'
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});

			return deferred.promise;
		},

		/* フォームページ情報取得 */
		getDetail : function(eagleId) {
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}
			deferred = $q.defer();
			$http({
				url: '/sem/new/eagle/history/detail/' + eagleId,
				method: 'GET'
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});

			return deferred.promise;
		},
	}
}])


app.service('eagleUpdateService', ['$q', '$http', function($q, $http) {
	var deferred = null;
	return {
		/* フォームページ情報取得 */
		getContent : function(eagleId) {
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}
			deferred = $q.defer();
			$http({
				url: '/sem/new/eagle/update/content/' + eagleId,
				method: 'GET'
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});

			return deferred.promise;
		},


		/* フォームページ情報取得 */
		updateStatus : function(eagleId,component,updateData,activeFlg) {
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}
			deferred = $q.defer();
			
			var data = {
				eagleId: eagleId,
				component: component,
				update_data :updateData,
				activeFlg: activeFlg,	
			};


			$http({
				url: '/sem/new/eagle/update/status/update',
				method: 'POST',
				data:data,
			}).success(function(res) {
				deferred.resolve(res);
			}).error(function(data, status, headers, config) {
				deferred.reject(data);
			});

			return deferred.promise;
		},


		/* CPC変更の送信 */
		updateCpc : function(data) {
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}
			deferred = $q.defer();
			$http({
				url: '/sem/new/eagle/update/cpc/save',
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
}])
