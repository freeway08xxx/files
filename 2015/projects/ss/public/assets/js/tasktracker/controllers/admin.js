/* Controllers */
var controller = angular.module('tskt.controllers.admin', []);

/* TsktAdminCtrl as tsktAdmin  */
controller.controller('TsktAdminCtrl', ['$scope', 'tsktAdminService', 
	function($scope, tsktAdminService) {
		var _this = this;
		_this.models = {
			//subTab: $scope.tsktBase.const.subTab.setting[0]
		};
		_this.services = {
			common: tsktAdminService,
			bindAction: {}
		}
	}
]);
