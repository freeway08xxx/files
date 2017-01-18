var services = angular.module('support_api_quota.services', []);

services.service('quotaService', ['$q', '$http', function($q, $http) {
	return {
		getYahoo: function(){
			return $http({
				url: '/sem/new/support/api/quota/yahoo/list',
				method: 'GET'
			});
		},
		getYdn: function(){
			return $http({
				url: '/sem/new/support/api/quota/ydn/list',
				method: 'GET'
			});
		}
	};
}]);
