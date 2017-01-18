// Declare app level module which depends on filters, and services
angular.module('knowledge', [
		//'ngResource',
		'knowledge.services',
		'knowledge.controllers',
		'knowledge.filters',
		'knowledge.directives',
]);

var app = angular.module('knowledge');

app.config(['$routeProvider', '$locationProvider',
	function($routeProvider, $locationProvider) {
			//$locationProvider.html5Mode(true);
			$routeProvider
		.when('/list', {
			templateUrl: function(params) {
				return '/sem/new/knowledge/list/index.html';
			},
			reloadOnSearch: false,
			//controller: 'SearchCtrl'
		})
		.when('/detail/view', {
			templateUrl: function(params) {
				return '/sem/new/knowledge/detail/view/index.html?file_id=' + params.file_id;
			},
			reloadOnSearch: false,
			//controller: 'NewPostCtrl'
		})
		.when('/detail/edit', {
			templateUrl: function(params) {
				return '/sem/new/knowledge/detail/edit/index.html?file_id=' + params.file_id;;
			},
			reloadOnSearch: false,
			//controller: 'NewPostCtrl'
		})
		.otherwise({
			redirectTo: '/list'
		});
	}
]);

/**
 * Loading Bar and spinner Display Option
 */
app.config(['cfpLoadingBarProvider', function(cfpLoadingBarProvider) {
	/* 画面上部のローディングバーを表示 */
	cfpLoadingBarProvider.includeBar     = false;
	/* 画面中央にスピナーを表示 */
	cfpLoadingBarProvider.includeSpinner = false;
}]);

function IsExists(array, value) {
  // 配列の最後までループ
  for (var i =0, len = array.length; i < len; i++) {
    if (value == array[i]) {
      // 存在したらtrueを返す
      return true;
    }
  }
  // 存在しない場合falseを返す
  return false;
}

// 重複を排除しながらpushする関数
function PushArray(array, value) {
	// 存在しない場合、配列にpushする
	if(! IsExists(array, value)) {
		array.push(value);
	}
	return true;
}
