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
			created_at  :'created_at',
			account_id  :'account_id',
			created_user:'created_user',
			status      :'status'
		},
		isDesc : [true,false],
		displayType :'client'
	},
	pagination : {
		currentPage:  1,
		numPages   :  1,
		limit      : 40,
		maxSize    :  5,
		offset     :  1
	},
	media_id:{
		MEDIA_ID_YAHOO :'1',
		MEDIA_ID_GOOGLE:'2',
		MEDIA_ID_YDN   :'3',
		MEDIA_ID_D2C   :'17'
	}
});




