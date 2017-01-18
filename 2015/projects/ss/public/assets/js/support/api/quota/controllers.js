/* Controllers */
var controllers = angular.module('support_api_quota.controllers', []);


/* Yahoo */
controllers.controller('SupportApiQuotaIndex', ['$scope', 'quotaService',  
	function($scope, quotaService) {
		$scope.$scope = $scope;
		$scope.quotas = {
			data : [],
			loading : true,
			columnDefs: [
				  {displayName:"コマンド", 	field: "command_group_name",	width:'20%', enableColumnMenu:false},
				  {displayName:"説明",		field: "description" , 			width:'25%', enableColumnMenu:false},
				  {displayName:"1日の上限",	field: "daily_quota_amount",	width:'20%', enableColumnMenu:false, cellFilter: 'number'},
				  {displayName:"残クォータ",field: "daily_remain_quota",	width:'20%', enableColumnMenu:false, cellFilter: 'number'},
				  {displayName:"更新日時",	field: "updated_at", 			width:'15%', enableColumnMenu:false, cellFilter: 'moment:"YYYY.M.D HH:mm"'},
				],
			onRegisterApi : function (gridApi) {
				$scope.gridApi = gridApi;
			},
		};
	
		$scope.viewQuotas = function(media){
			if(media == 'yahoo'){
				quotaService.getYahoo().then(function(res){
					console.log(res);
					if(!res || !res.data.quotas){
						return false
					}
	
					$scope.quotas.data = res.data.quotas;
					$scope.quotas.loading = false;
				});
	
			}else if(media == 'ydn'){
				quotaService.getYdn().then(function(res){
					if(!res || !res.data.quotas){
						return false
					}
	
					$scope.quotas.data = res.data.quotas;
					$scope.quotas.loading = false;
				});
			}
		}
	}
]);

/* Yahoo */
controllers.controller('SupportApiQuotaYahoo', ['$scope', 
	function($scope) {
		var media = 'yahoo';
		$scope.quotas.minRowsToShow = "20";
		$scope.viewQuotas(media);
	}
]);

/* YDN */
controllers.controller('SupportApiQuotaYdn', ['$scope', 
	function($scope) {
		var media = 'ydn';
		$scope.quotas.minRowsToShow = "10";
		$scope.viewQuotas(media);
	}
]);
