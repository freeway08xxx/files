/* Controllers */
var app = angular.module('ss.controllers', []);

/* アプリ管理 */
app.controller('SsCtrl', ['$scope', '$rootScope', 'ssConst', function($scope, $rootScope, ssConst) {
	$scope.user = ssConst.user;
}]);

/* ヘッダー管理 */
app.controller('SsHeaderCtrl',
	['$scope','$rootScope', 'ssGlobalNavService', function($scope,$rootScope, ssGlobalNavService) {
	$scope.getGlobalNav = function () {
		ssGlobalNavService.getNav("gnavi").then(function(res) {
			$rootScope.nav = res;
		});
	};

	$scope.checkEmpty = function (obj) {
		var array  = _.pull(_.pluck(obj, 'path'), "");
		return res = (array.length == 0) ? false:true;
	};
}]);

