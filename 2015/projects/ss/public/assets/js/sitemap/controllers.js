/* Controllers */

/* SitemapBaseCtrl as baseCtrl */
var controller = angular.module('sitemap.controllers.base', []);

controller.controller('SitemapBaseCtrl', ['$scope','$rootScope','ssGlobalNavService',
	function($scope,$rootScope,ssGlobalNavService) {
	var _this = this;

	$rootScope.$watch('nav', function(newVal){
		if(newVal){
			_this.nav = newVal;
		}
	});
}]);






