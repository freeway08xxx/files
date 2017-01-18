// Declare app level module which depends on filters, and services
angular.module('downloadlist', [
		'downloadlist.controllers.base',
		'downloadlist.services',
		'downloadlist.directives',
		'downloadlist.filters',
		'ngAnimate'
]);


var app = angular.module('downloadlist');


/**
 * Loading Bar and spinner Display Option
 */
app.config(['cfpLoadingBarProvider', function(cfpLoadingBarProvider) {
	/* 画面上部のローディングバーを表示 */
	cfpLoadingBarProvider.includeBar     = false;
	/* 画面中央にスピナーを表示 */
	cfpLoadingBarProvider.includeSpinner = true;
}]);



app.constant('downloadlistConst', {
	settings : {
		tab:{
			download:'download',
			report  :'report'
		}
	},
	sort : {
		place  :{
			start_date  :'start_date',
			end_date    :'end_date',
			account_id  :'account_id',
			account_name:'account_name',
			media_id    :'media_id',
			created_user:'created_user',
			status      :'status',
			service     :'service',
			user_id     :'user_id',
			user_name   :'user_name',
		},
		isDesc : [true,false],
	},
	view : {
		displayType  : 'client',
		service      : '',
		is_my_history:[true,false]
	},
	pagination : {
		currentPage:  1,
		numPages   :  1,
		limit      : 40,
		maxSize    :  5,
		offset     :  1
	},
});




