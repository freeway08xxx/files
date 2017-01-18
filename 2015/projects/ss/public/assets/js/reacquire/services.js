/* Services */

var ReacquireServices = angular.module('Reacquire.services', [])

ReacquireServices.service('AjaxService', ['$http', '$q', function($http, $q) {
	var deferred = null;

	return {
		getDifferenceData: function (accountIdList, reportType, fromDate, toDate, differenceExists) {
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}

			deferred = $q.defer();

			$http({
				url: '/sem/new/reacquire/index/get_difference_data',
				params: {
					account_id_list:   accountIdList,
					report_type:       reportType,
					from_date:         fromDate,
					to_date:           toDate,
					difference_exists: differenceExists
				},
				method: 'GET'
			}).success(function(response) {
				deferred.resolve(response);
			}).error(function(response) {
				deferred.reject(response);
			});

			return deferred.promise;
		},
		postSubmit: function (accountIdList, reportType, clientId, fromDate, toDate) {
			if (deferred) {
				deferred.resolve();
				deferred = null;
			}

			deferred = $q.defer();

			$http({
				url: '/sem/new/reacquire/index/submit',
				data: {
					account_id_list: accountIdList,
					report_type:     reportType,
					client_id:       clientId,
					from_date:       fromDate,
					to_date:         toDate
				},
				method: 'POST'
			}).success(function(response) {
				deferred.resolve(response);
			}).error(function(response) {
				deferred.reject(response);
			});

			return deferred.promise;
		}
	};
}]);